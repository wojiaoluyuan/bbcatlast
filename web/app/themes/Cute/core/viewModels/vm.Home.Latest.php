<?php
/**
 * Copyright (c) 2014-2018, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since LTS-181021
 * @package BBCat
 * @author 哔哔猫
 * @date 2018/10/21 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

/**
 * Class HomeLatestVM
 */
class HomeLatestVM extends BaseVM {

    /**
     * @var int 分页号
     */
    private $_page = 1;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $page   分页号
     * @return  static
     */
    public static function getInstance($page = 1) {
        $instance = new static(); // 因为不同分页共用该模型，不采用单例模式
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . get_called_class() . '_page' . $page.'_user_'.get_current_user_id();
        $instance->_page = max(1, $page);
        //$instance->_enableCache = false; // TODO Debug use
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        //$featured_catIds = array(tt_get_option('tt_home_featured_category_one'), tt_get_option('tt_home_featured_category_two'), tt_get_option('tt_home_featured_category_three'));
        $uncat = tt_filter_of_multicheck_option(tt_get_option('tt_home_undisplay_cats', array()));
        $user_id = get_current_user_id();
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => get_option('posts_per_page', 10),
            'paged' => $this->_page,
            'category__not_in' => $uncat, // TODO: 第二页置顶分类隐藏了会仍然不显示这些分类的文章
            'has_password' => false,
            'ignore_sticky_posts' => true,
            'post__not_in' => get_option('sticky_posts'),
            'orderby' => 'modified', // date // modified - 如果按最新编辑时间排序
            'order' => 'DESC'
        );

        $query = new WP_Query($args);
        $GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query) //TODO 缓存时无效

        $latest_posts = array();
        $pagination = array(
            'max_num_pages' => $query->max_num_pages,
            'current_page' => $this->_page,
            'base' => str_replace('999999999', '%#%', get_pagenum_link(999999999))
        );

        while ($query->have_posts()) : $query->the_post();
            $latest_post = array();
            global $post;
            $latest_post['ID'] = $post->ID;
            $latest_post['title'] = get_the_title($post);
            $latest_post['permalink'] = get_permalink($post);
            $latest_post['comment_count'] = $post->comment_count;
            $latest_post['excerpt'] = get_the_excerpt($post);
            $latest_post['category'] = get_the_category_list(' ', '', $post->ID);
            $latest_post['author'] = get_the_author();
            $latest_post['author_url'] = get_author_posts_url($post->post_author);
            $latest_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $latest_post['datetime'] = get_the_time(DATE_W3C, $post);
            $latest_post['thumb'] = tt_get_thumb($post, 'medium');
            $latest_post['video'] = get_post_meta($post->ID, 'ashu_video', true);
            $latest_post['format'] = get_post_format($post) ? : 'standard';
            $latest_post['sticky_class'] = '';
            $latest_post['tags'] = tt_get_custom_post_tags($post->ID, 10);
            $latest_post['views'] = absint(get_post_meta( $post->ID, 'views', true ));
            $sale_dls = tt_get_post_sale_resources($post->ID);
            $free_dls = trim(get_post_meta($post->ID, 'tt_free_dl', true));
            $vip_price = tt_get_specified_user_post_price($sale_dls[0]['price'], $sale_dls[0]['currency']);
            $currency = $sale_dls[0]['currency'] == 'cash' ? '元' : '积分';
            $member = new Member($user_id);
            if(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && $vip_price == 0 && !empty($sale_dls)){
              $latest_post['price_text'] = '会员免费';
            }elseif(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && !empty($sale_dls)){
              $latest_post['price_text'] = '会员价 '.$vip_price.' '.$currency;
            }elseif($vip_price != 0){
              $latest_post['price_text'] = $vip_price.' '.$currency;
            }elseif(!empty($free_dls)){
              $latest_post['price_text'] = '免费资源';
            }else{
              $latest_post['price_text'] = '';
            }

            $latest_posts[] = $latest_post;
        endwhile;

        wp_reset_postdata();

        return (object)array(
            'pagination' => $pagination,
            'latest_posts' => $latest_posts
        );
    }
}