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
if(!is_user_logged_in()){
    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
}

if(!current_user_can('edit_users')) {
    wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
}

if(!isset($_GET['post_id']) || empty($_GET['post_id'])){
    wp_die(__('The required resource id is missing', 'tt'), __('Invalid Resource ID', 'tt'), 404);
}

if ($_GET['post_id']) {
            $post_id = $_GET['post_id'];
            if($post_id == 'all'){
            tt_daily_check_weibo_image();
            }else{
            tt_daily_check_weibo_image($post_id);
            }
}