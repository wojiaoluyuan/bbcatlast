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
 * Class SinglePageVM
 */
class SinglePageVM extends BaseVM {
    /**
     * @var int 页面ID
     */
    private $_pageId = 0;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $page_id   页面ID
     * @return  static
     */
    public static function getInstance($page_id = 1) {
        $instance = new static();
        // 登录与回复可见短代码缓存问题解决
        global $post;
        $the_post = $post ? : get_post($page_id);
        $user_id = get_current_user_id();
        $key_suffix = '_u' . $user_id; // 区别登录用户与未登录用户缓存
        $reviewed = '_not_reviewed'; // 区别评论与未评论用户
        if( $user_id == $the_post->post_author || user_can($user_id,'edit_others_posts') ){
            $reviewed = '_reviewed';
        } else {
            $comments = get_comments( array('status' => 'approve', 'user_id' => $user_id, 'post_id' => $the_post->ID, 'count' => true ) );
            if($comments) {
                $reviewed = '_reviewed';
            }
        }
        $sale_content = '_not_sale';
        if(tt_check_bought_post_resources2($the_post->ID, 0)){
          $sale_content = '_sale';
        }
        $star_users_cache = array_unique(get_post_meta( $the_post->ID, 'tt_post_star_users', false)); //TODO 最多显示10个，最新的靠前(待确认)
        $star_cache = '_not_star';
        if(in_array($user_id,$star_users_cache)){
          $star_cache = '_star';
        }
        $key_suffix .= $reviewed;
        $key_suffix .= $sale_content;
        $key_suffix .= $star_cache;

        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_page' . $page_id . $key_suffix;
        $instance->_pageId = absint($page_id);
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $the_page = null;
        while(have_posts()) : the_post();
            global $post;
            $the_page = $post ? : get_post($this->_pageId);
        endwhile;

        // 基本信息
        $info = array();
        $info['ID'] = $the_page->ID;
        $info['title'] = get_the_title($the_page);
        $info['permalink'] = get_permalink($the_page);
        $info['comment_count'] = $the_page->comment_count;
        $info['comment_status'] = !($the_page->comment_status != 'open');
        $info['excerpt'] = get_the_excerpt($the_page);
        $content = get_the_content();
        $content = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $content ) );
        $info['content'] =  wp_image_to_weibo_content_img_replace($content); //$the_post->post_content;
        $info['category'] = get_the_category_list(' ', '', $the_page->ID);
        $info['tags'] = get_the_tag_list(' ', ' ', '', $the_page->ID);
        $info['author'] = get_the_author();
        $info['author_url'] = home_url('/@' . $info['author']); //TODO the link
        $info['time'] = get_post_time('Y-m-d', false, $the_page, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
        $info['datetime'] = get_the_time(DATE_W3C, $the_page);
        $info['modified'] = get_post_modified_time(DATE_W3C, false, $the_page);
        $info['timediff'] = Utils::getTimeDiffString($info['datetime']);
        $info['modifieddiff'] = Utils::getTimeDiffString($info['modified']);
        $info['thumb'] = tt_get_thumb($the_page, array('width' => 720, 'height' => 400, 'str' => 'medium'));
        $info['format'] = get_post_format($the_page) ? : 'standard';

        // 浏览数
        $views = absint(get_post_meta( $the_page->ID, 'views', true ));

        // 点赞
        //$stars = intval(get_post_meta( $the_post->ID, 'tt_post_stars', true )); // 可以直接count $star_users替代

        $star_user_ids = array_unique(get_post_meta( $the_page->ID, 'tt_post_star_users', false)); //TODO 最多显示10个，最新的靠前(待确认)
        $stars = count($star_user_ids);
        $star_users = array();
        $limit = min(count($star_user_ids), 10);
        for ($i = 0; $i < $limit; $i++) {
            $uid = $star_user_ids[$i];
            $star_users[] = (object)array(
                'uid' => $uid,
                'name' => tt_get_privacy_mail(get_userdata($uid)->display_name),
                'avatar' => tt_get_avatar($uid, 'small')
            );
        }

        return (object)array_merge(
            $info,
            array(
                'views'        => $views,
                'stars'        => $stars,
                'star_users'   => $star_users,
                'likes'        => $stars,
                'star_uids'    => $star_user_ids
            )
        );
    }
}