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
 * Class TagPostsVM
 */
class TagPostsVM extends BaseVM {

    /**
     * @var int 分页号
     */
    private $_page = 1;

    /**
     * @var int 标签ID
     */
    private $_tagID;

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
        $tag_ID = absint(get_queried_object_id());
        $instance = new static(); // 因为不同分页不同标签共用该模型，不采用单例模式
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_tag' . $tag_ID . '_page' . $page.'_user_'.get_current_user_id();
        $instance->_page = max(1, $page);
        $instance->_tagID = $tag_ID;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $tag_ID = $this->_tagID;
        $tag = get_tag($tag_ID);
        $tag->thumbnail = z_taxonomy_image_url($tag_ID);
        $tag_link = get_tag_link($tag_ID);
        $tag->tag_link = $tag_link;
        $user_id = get_current_user_id();
        //$category_description = $category->description;//category_description();

//        $args = array(
//            'post_type' => 'post',
//            'post_status' => 'publish',
//            'posts_per_page' => get_option('posts_per_page', 10),
//            'paged' => $this->_page,
//            'tag_in' => $tag_ID,
//            'has_password' => false,
//            'ignore_sticky_posts' => true,
//            'orderby' => 'date', // modified - 如果按最新编辑时间排序
//            'order' => 'DESC'
//        );

        //$query = new WP_Query($args); // 如果需要自定义循环，如改变排序行为，可取消该注释
        global $wp_query;
        $query = $wp_query;
        //$GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query)

        $tag_posts = array();

        $big_page_link = get_pagenum_link(999999999);
        $pagination = array(
            'max_num_pages' => $query->max_num_pages,
            'current_page' => $this->_page,
            'base' => str_replace('999999999', '%#%', $big_page_link),
            'next' => str_replace('999999999', $this->_page+1, $big_page_link)
        );

        while ($query->have_posts()) : $query->the_post();
            $tag_post = array();
            global $post;
            $tag_post['ID'] = $post->ID;
            $tag_post['title'] = get_the_title($post);
            $tag_post['permalink'] = get_permalink($post);
            $tag_post['comment_count'] = $post->comment_count;
            $tag_post['excerpt'] = get_the_excerpt($post);
            $tag_post['category'] = get_the_category_list(' ', '', $post->ID);
            $tag_post['author'] = get_the_author();
            $tag_post['author_url'] = home_url('/u/' . get_the_author_meta("ID"));
            $tag_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $tag_post['timediff'] = Utils::getTimeDiffString(get_post_time('Y-m-d G:i:s', true));
            $tag_post['datetime'] = get_the_time(DATE_W3C, $post);
            $tag_post['thumb'] = tt_get_thumb($post, 'medium');
            $tag_post['video'] = get_post_meta($post->ID, 'ashu_video', true);
            $tag_post['format'] = get_post_format($post) ? : 'standard';
            $tag_post['views'] = absint(get_post_meta( $post->ID, 'views', true ));
            $star_user_ids = array_unique(get_post_meta( $post->ID, 'tt_post_star_users', false));
            $stars = count($star_user_ids);
            $tag_post['star_count'] = $stars;
            $sale_dls = tt_get_post_sale_resources($post->ID);
            $free_dls = trim(get_post_meta($post->ID, 'tt_free_dl', true));
            $vip_price = tt_get_specified_user_post_price($sale_dls[0]['price'], $sale_dls[0]['currency']);
            $currency = $sale_dls[0]['currency'] == 'cash' ? '元' : '积分';
            $member = new Member($user_id);
            if(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && $vip_price == 0 && !empty($sale_dls)){
              $tag_post['price_text'] = '会员免费';
            }elseif(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && !empty($sale_dls)){
              $tag_post['price_text'] = '会员价 '.$vip_price.' '.$currency;
            }elseif($vip_price != 0){
              $tag_post['price_text'] = $vip_price.' '.$currency;
            }elseif(!empty($free_dls)){
              $tag_post['price_text'] = '免费资源';
            }else{
              $tag_post['price_text'] = '';
            }

            $tag_posts[] = $tag_post;
        endwhile;

        wp_reset_postdata();

        return (object)array(
            'tag' => (array)$tag,
            'pagination' => $pagination,
            'tag_posts' => $tag_posts
        );
    }
}
