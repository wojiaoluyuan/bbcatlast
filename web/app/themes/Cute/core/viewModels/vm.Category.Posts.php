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
 * Class CategoryPostsVM
 */
class CategoryPostsVM extends BaseVM {

    /**
     * @var int 分页号
     */
    private $_page = 1;

    /**
     * @var int 分类ID
     */
    private $_catID;

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
        $cat_ID = absint(get_queried_object_id());
        $instance = new static(); // 因为不同分页不同分类共用该模型，不采用单例模式
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_cat' . $cat_ID . '_page' . $page.'_user_'.get_current_user_id();
        $instance->_page = max(1, $page);
        $instance->_catID = $cat_ID;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $category_ID = $this->_catID;
        $category = get_category($category_ID);
        
        $category->thumbnail = get_term_meta($category_ID,'thumbnail',true);
        $category_link = get_category_link($category_ID);
        $category->category_link = $category_link;
        $user_id = get_current_user_id();
        $category_terms = get_categories(array(
            'taxonomy'  =>  'category',
            'child_of' => $category_ID
        ));

        $categories = array();
        foreach ($category_terms as $category_term) {
            $category = array();
            $category['ID'] = $category_term->term_id;
            $category['slug'] = $category_term->slug;
            $category['name'] = $category_term->name;
            $category['description'] = $category_term->description;
            $category['parent'] = $category_term->parent;
            $category['count'] = $category_term->count;
            $category['permalink'] = get_term_link($category_term, 'category');

            $categories[] = $category;
        }
        $args = array( 'categories' => $category_ID);
        $tag_terms = tt_get_category_tags($args);
        $tags = array();
        foreach ($tag_terms as $tag_term) {
            $tag = array();
            $tag['ID'] = $tag_term->term_id;
            $tag['slug'] = $tag_term->slug;
            $tag['name'] = $tag_term->name;
            $tag['description'] = $tag_term->description;
            $tag['parent'] = $tag_term->parent;
            $tag['count'] = $tag_term->count;
            $tag['permalink'] = get_term_link($tag_term, 'product_tag');

            $tags[] = $tag;
        }
        //$category_description = $category->description;//category_description();

//        $args = array(
//            'post_type' => 'post',
//            'post_status' => 'publish',
//            'posts_per_page' => get_option('posts_per_page', 10),
//            'paged' => $this->_page,
//            'category_in' => $category_ID,
//            'has_password' => false,
//            'ignore_sticky_posts' => true,
//            'orderby' => 'date', // modified - 如果按最新编辑时间排序
//            'order' => 'DESC'
//        );

        //$query = new WP_Query($args); // 如果需要自定义循环，如改变排序行为，可取消该注释
        global $wp_query;
        $query = $wp_query;
        //$GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query)

        $category_posts = array();

        $big_page_link = get_pagenum_link(999999999);
        $pagination = array(
            'max_num_pages' => $query->max_num_pages,
            'current_page' => $this->_page,
            'base' => str_replace('999999999', '%#%', $big_page_link),
            'next' => str_replace('999999999', $this->_page+1, $big_page_link)
        );

        while ($query->have_posts()) : $query->the_post();
            $category_post = array();
            global $post;
            $category_post['ID'] = $post->ID;
            $category_post['title'] = get_the_title($post);
            $category_post['permalink'] = get_permalink($post);
            $category_post['comment_count'] = $post->comment_count;
            $category_post['excerpt'] = get_the_excerpt($post);
            $category_post['category'] = sprintf('<a class="category" href="%1$s" rel="bookmark">%2$s</a>', $category_link, $category->cat_name);
            $category_post['author'] = get_the_author();
            $category_post['author_url'] = home_url('/u/' . get_the_author_meta("ID"));
            $category_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $category_post['timediff'] = Utils::getTimeDiffString(get_post_time('Y-m-d G:i:s', true));
            $category_post['datetime'] = get_the_time(DATE_W3C, $post);
            $category_post['thumb'] = tt_get_thumb($post, 'medium');
            $category_post['format'] = get_post_format($post) ? : 'standard';
            $category_post['video'] = get_post_meta($post->ID, 'ashu_video', true);
            $category_post['tags'] = tt_get_custom_post_tags($post->ID, 10);
            $category_post['views'] = absint(get_post_meta( $post->ID, 'views', true ));
            $star_user_ids = array_unique(get_post_meta( $post->ID, 'tt_post_star_users', false)); //TODO 最多显示10个，最新的靠前(待确认)
            $stars = count($star_user_ids);
            $category_post['star_count'] = $stars;
            $sale_dls = tt_get_post_sale_resources($post->ID);
            $free_dls = trim(get_post_meta($post->ID, 'tt_free_dl', true));
            $vip_price = tt_get_specified_user_post_price($sale_dls[0]['price'], $sale_dls[0]['currency']);
            $currency = $sale_dls[0]['currency'] == 'cash' ? '元' : '积分';
            $member = new Member($user_id);
            if(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && $vip_price == 0 && !empty($sale_dls)){
              $category_post['price_text'] = '会员免费';
            }elseif(($member->vip_type == 1 || $member->vip_type == 2 || $member->vip_type == 3) && !empty($sale_dls)){
              $category_post['price_text'] = '会员价 '.$vip_price.' '.$currency;
            }elseif($vip_price != 0){
              $category_post['price_text'] = $vip_price.' '.$currency;
            }elseif(!empty($free_dls)){
              $category_post['price_text'] = '免费资源';
            }else{
              $category_post['price_text'] = '';
            }

            $category_posts[] = $category_post;
        endwhile;

        wp_reset_postdata();

        return (object)array(
            'category' => (array)$category,
            'categories' => (array)$categories,
            'tags' => (array)$tags,
            'pagination' => $pagination,
            'category_posts' => $category_posts
        );
    }
}
