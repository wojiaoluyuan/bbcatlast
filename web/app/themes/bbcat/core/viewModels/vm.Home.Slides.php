<?php
/**
 * Copyright (c) 2019-2025, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since 2.5.0
 * @package BBcat-K
 * @author 洛茛艺术影视在线
 * @date 2019-04-03 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

/**
 * Class SlideVM
 */
class SlideVM extends BaseVM {
    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24;
    }

    protected function getRealData() {
        $slide_postIds = explode(',', tt_get_option('tt_home_slides'));

        if(!count($slide_postIds)) {
            return null;
        }

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post__in' => $slide_postIds,
            'has_password' => false,
            'ignore_sticky_posts' => true,
            'orderby' => 'none'
        );

        $query = new WP_Query($args);

        $slide_posts = array();

        while ($query->have_posts()) : $query->the_post();
            $slide_post = array();
            global $post;
            $slide_post['title'] = get_the_title($post);
            $slide_post['permalink'] = get_permalink($post);
            $slide_post['comment_count'] = $post->comment_count;
            $slide_post['category'] = get_the_category_list(' · ', '', $post->ID);
            $slide_post['author'] = get_the_author();
            $slide_post['author_url'] = home_url('/u/' . get_the_author_meta("ID"));
            $slide_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $slide_post['datetime'] = get_the_time(DATE_W3C, $post);
            $slide_post['thumb'] = tt_get_thumb($post, array(
                'width' => 750,
                'height' => 375,
                'str' => 'large'
            ));

            $slide_posts[strval($post->ID)] = $slide_post;
        endwhile;

        wp_reset_postdata();

        $ordered_posts = array();

        foreach ($slide_postIds as $value) {
            $ordered_posts[] = $slide_posts[$value];
        }

        return $ordered_posts;
    }
}