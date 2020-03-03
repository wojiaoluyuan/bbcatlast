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
 * Class StickysVM
 */
class StickysVM extends BaseVM {

    /**
     * @var int 限制文章数量, 0即为不限制
     */
    private $_limit = 0;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $limit   限制文章数量
     * @return  static
     */
    public static function getInstance($limit = 0) {
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . get_called_class() .'_user_'.get_current_user_id().'_limit' . $limit;
        $instance->_limit = max(0, $limit);
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $stickys = get_option('sticky_posts');
        $user_id = get_current_user_id();
        $stickys_num = count($stickys);

        if($stickys_num < 1) {
            return (object)array(
                'count' => 0,
                'sticky_posts' => array()
            );
        }

        $args = array(
            'post__in' => $stickys,
            'post_status' => 'publish',
            'has_password' => false,
            'orderby' => 'date', // modified - 如果按最新编辑时间排序
            'order' => 'DESC'
        );
        if($this->_limit > 0) {
            $args['showposts'] = $this->_limit;
        }

        $query = new WP_Query($args);
        $GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query) //TODO 缓存时无效

        $sticky_posts = array();

        while ($query->have_posts()) : $query->the_post();
            $sticky_post = array();
            global $post;
            $sticky_post['ID'] = $post->ID;
            $sticky_post['title'] = get_the_title($post);
            $sticky_post['permalink'] = get_permalink($post);
            $sticky_post['comment_count'] = $post->comment_count;
            $sticky_post['excerpt'] = get_the_excerpt($post);
            $sticky_post['category'] = get_the_category_list(' ', '', $post->ID);
            $sticky_post['tags'] = tt_get_custom_post_tags($post->ID, 10);
            $sticky_post['author'] = get_the_author();
            $sticky_post['author_url'] = get_author_posts_url($post->post_author);
            $sticky_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $sticky_post['datetime'] = get_the_time(DATE_W3C, $post);
            $sticky_post['thumb'] = tt_get_thumb($post, 'medium');
            $sticky_post['video'] = get_post_meta($post->ID, 'ashu_video', true);
            $sticky_post['format'] = get_post_format($post) ? : 'standard';
            $sticky_post['sticky_class'] = 'sticky';
            $sticky_post['views'] = absint(get_post_meta( $post->ID, 'views', true ));
            $sale_dls = tt_get_post_sale_resources($post->ID);
            $free_dls = trim(get_post_meta($post->ID, 'tt_free_dl', true));
            $vip_price = tt_get_specified_user_post_price($sale_dls[0]['price'], $sale_dls[0]['currency']);
            $currency = $sale_dls[0]['currency'] == 'cash' ? '元' : '积分';
            $member = new Member($user_id);
            if(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && $vip_price == 0 && !empty($sale_dls)){
              $sticky_post['price_text'] = '会员免费';
            }elseif(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && !empty($sale_dls)){
              $sticky_post['price_text'] = '会员价 '.$vip_price.' '.$currency;
            }elseif($vip_price != 0){
              $sticky_post['price_text'] = $vip_price.' '.$currency;
            }elseif(!empty($free_dls)){
              $sticky_post['price_text'] = '免费资源';
            }else{
              $sticky_post['price_text'] = '';
            }
         
            $sticky_posts[] = $sticky_post;
        endwhile;

        wp_reset_postdata();

        return (object)array(
            'count' => count($sticky_posts),
            'sticky_posts' => $sticky_posts
        );
    }
}