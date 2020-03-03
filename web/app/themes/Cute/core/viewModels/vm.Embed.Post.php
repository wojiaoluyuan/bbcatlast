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
 * Class EmbedPostVM
 */
class EmbedPostVM extends BaseVM {
    /**
     * @var int 文章ID
     */
    private $_postId = 0;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $product_id   商品ID
     * @return  static
     */
    public static function getInstance($post_id = 0) {
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_product' . $post_id;
        $instance->_postId = absint($post_id);
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $post = get_post($this->_postId);
        $author = get_userdata($post->post_author);
        $post_content = strip_tags($post->post_content);
        $excerpt = wp_trim_words($post_content,100,'...');
        $data = array();
        // 基本信息
        if($post && $post->post_status == 'publish'){
            $data = array(
                'post_id' => $post->ID,
                'post_title' => $post->post_title,
                'comment_count'=> $post->comment_count,
                'category' => get_the_category_list(' ', '', $post->ID),
                'author'  => $author->display_name,
                'author_url' => get_author_posts_url($post->post_author),
                'time' => get_post_time('Y-m-d', false, $post, false),
                'datetime' => get_the_time(DATE_W3C, $post),
                'description' => $excerpt,
                'post_link' => get_permalink($post),
                'thumb' => tt_get_thumb($post, array('width' => 200, 'height' => 150, 'str' => 'thumbnail')),
                'views' => absint(get_post_meta( $post->ID, 'views', true ))
            );
        }

        return (object)$data;
    }
}