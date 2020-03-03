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

if(!isset($_GET['post_id'])){
    wp_die(__('The required resource id is missing', 'tt'), __('Invalid Resource ID', 'tt'), 404);
}

if ($_GET['post_id']) {//重置功能
            global $wpdb;
            $post_id = $_GET['post_id'];
            $prefix = $wpdb->prefix;
            $table_name = $prefix.'tt_weibo_image';
            if($post_id == 'all'){
            $rs = $wpdb->get_results("SELECT post_id, src, pid FROM $table_name");
            }else{
            $rs = $wpdb->get_results("SELECT post_id, src, pid FROM $table_name where post_id=$post_id");
            }
            if (!$rs || count($rs) == 0) {
                echo '<div id="message" class="updated below-h2"><p>' . __('没有可重置文章.', 'wp-image-to-weibo') . '</p></div>';
            } else {
                $map = array();//按文章 id 分
                foreach ($rs as $row) {
                    $inner = $map[$row->post_id];
                    if (!$inner) {
                        $inner = array();
                    }
                    array_push($inner, $row);
                    $map[$row->post_id] = $inner;
                }
                reset_url($map);
            }
        }
//还原 URL
function reset_url($post_id_to_row_map){
    remove_filter("wp_insert_post_data", "process_post_when_save", 99);
    $success = array();
    foreach ($post_id_to_row_map as $post_id => $row_arr) {
        $post = get_post($post_id);
        $post_content = $post->post_content;
        foreach ($row_arr as $row) {
            $link = '/(https?:)?\/\/(ws|wx)(\d).sinaimg.cn\/(large|mw690|mw1024|mw2048|bmiddle)\/'.$row->pid.'.(jpg|jpeg|png|gif|bmp)/i';
            $post->post_content = preg_replace($link, $row->src, $post->post_content);
        }
        if($post_content != $post->post_content){
        $ret = wp_update_post($post);
        }else{
         continue;
        }
        if ($ret == 0) {
            echo '<div id="error" class="updated below-h2"><p>' . sprintf(__('Error: %1$s', 'wp-image-to-weibo'), $post->post_title) . '</p></div>';
        } else {
            array_push($success, $post);
        }
    }
    if (count($success) > 0) {
        echo '<div id="message" class="updated below-h2">';
        printf('还原了 %1$s 篇文章:<hr>' . PHP_EOL, count($success));
        foreach ($success as $p) {
            echo '<a href="' . get_permalink($p) . '" target="_blank">' . $p->post_title . '</a><br>';
        }
        echo '</div>';
    }else{
      echo '<div id="message" class="updated below-h2"><p>' . __('没有可重置文章.', 'wp-image-to-weibo') . '</p></div>';
    }
}