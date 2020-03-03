<?php
/**
 * Copyright (c) 2014-2018, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since LTS
 * @package BBCAT
 * @author 哔哔猫
 * @date 2019/10/25 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php


if (!defined('ABSPATH')){
    wp_die(__('Lack of WordPress environment', 'tt'), __('WordPress internal error', 'tt'), array('response'=>403));
}
/* 引入加载器 */
require_once (get_template_directory() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'func.Loader.php');

/* 请在下方添加你的自定义函数和功能 */
///////////////////////////////////////////////////
/*
==================================================
熊掌号获取文章三张图片（不需要可删除）
==================================================
*/
function fanly_post_imgs(){
	global $post;
	$src = '';
	$content = $post->post_content;  
	preg_match_all('/<img .*?src=[\"|\'](.+?)[\"|\'].*?>/', $content, $strResult, PREG_PATTERN_ORDER);  
	$n = count($strResult[1]);  
	if($n >= 3){
		$src = $strResult[1][0].'","'.$strResult[1][1].'","'.$strResult[1][2];
	}elseif($n >= 1){
		$src = $strResult[1][0];
	}
	return $src;
}
/*
==================================================
压缩网站源码（如使用OSS插件上传异常请删除）
==================================================
*/
function wp_compress_html(){
    function wp_compress_html_main ($buffer){
        $initial=strlen($buffer);
        $buffer=explode("<!--wp-compress-html-->", $buffer);
        $count=count ($buffer);
        for ($i = 0; $i <= $count; $i++){
            if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                $buffer[$i]=(str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
            } else {
                $buffer[$i]=(str_replace("\t", " ", $buffer[$i]));
                $buffer[$i]=(str_replace("\n\n", "\n", $buffer[$i]));
                $buffer[$i]=(str_replace("\n", "", $buffer[$i]));
                $buffer[$i]=(str_replace("\r", "", $buffer[$i]));
                while (stristr($buffer[$i], '  ')) {
                    $buffer[$i]=(str_replace("  ", " ", $buffer[$i]));
                }
            }
            $buffer_out.=$buffer[$i];
        }
        $final=strlen($buffer_out);   
        $savings=($initial-$final)/$initial*100;   
        $savings=round($savings, 2);   
        $buffer_out.="\n<!--压缩前的大小: $initial bytes; 压缩后的大小: $final bytes; 节约：$savings% -->";   
    return $buffer_out;
}
ob_start("wp_compress_html_main");
}
add_action('get_header', 'wp_compress_html');
/*
==================================================
代码高亮不启用压缩（不需要可删除）
==================================================
*/
function unCompress($content) {
    if(preg_match_all('/(crayon-|<\/pre>)/i', $content, $matches)) {
        $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->'.$content;
        $content.= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
    }
    return $content;
}
add_filter( "the_content", "unCompress");
/*
==================================================
去除分类标志（不需要可删除）
==================================================
*/
add_action( 'load-themes.php',  'no_category_base_refresh_rules');
add_action('created_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
function no_category_base_refresh_rules() {
    global $wp_rewrite;
    $wp_rewrite -> flush_rules();
}
// register_deactivation_hook(__FILE__, 'no_category_base_deactivate');
// function no_category_base_deactivate() {
//  remove_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
//  // We don't want to insert our custom rules again
//  no_category_base_refresh_rules();
// }
// Remove category base
add_action('init', 'no_category_base_permastruct');
function no_category_base_permastruct() {
    global $wp_rewrite, $wp_version;
    if (version_compare($wp_version, '3.4', '<')) {
        // For pre-3.4 support
        $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
    } else {
        $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
    }
}
// Add our custom category rewrite rules
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
function no_category_base_rewrite_rules($category_rewrite) {
    //var_dump($category_rewrite); // For Debugging
    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));
    foreach ($categories as $category) {
        $category_nicename = $category -> slug;
        if ($category -> parent == $category -> cat_ID)// recursive recursion
            $category -> parent = 0;
        elseif ($category -> parent != 0)
            $category_nicename = get_category_parents($category -> parent, false, '/', true) . $category_nicename;
        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    // Redirect support from Old Category Base
    global $wp_rewrite;
    $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
    $old_category_base = trim($old_category_base, '/');
    $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
    //var_dump($category_rewrite); // For Debugging
    return $category_rewrite;
}
// Add 'category_redirect' query variable
add_filter('query_vars', 'no_category_base_query_vars');
function no_category_base_query_vars($public_query_vars) {
    $public_query_vars[] = 'category_redirect';
    return $public_query_vars;
}
// Redirect if 'category_redirect' is set
add_filter('request', 'no_category_base_request');
function no_category_base_request($query_vars) {
    //print_r($query_vars); // For Debugging
    if (isset($query_vars['category_redirect'])) {
        $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
        status_header(301);
        header("Location: $catlink");
        exit();
    }
    return $query_vars;
}
/*
==================================================
禁止全英日俄韩阿泰语评论
==================================================
*/
function ssdax_comment_all_post( $incoming_comment ) {
$enpattern = '/[一-龥]/u';
$jpattern ='/[ぁ-ん]+|[ァ-ヴ]+/u';
$ruattern ='/[А-я]+/u';
$krattern ='/[갂-줎]+|[줐-쥯]+|[쥱-짛]+|[짞-쪧]+|[쪨-쬊]+|[쬋-쭬]+|[쵡-힝]+/u';
$arattern ='/[؟-ض]+|[ط-ل]+|[م-م]+/u';
$thattern ='/[ก-๛]+/u';
if(!preg_match($enpattern, $incoming_comment['comment_content'])) {
wp_die( "写点汉字吧，博主外语很捉急！ Please write some chinese words！" );
}
if(preg_match($jpattern, $incoming_comment['comment_content'])){
wp_die( "日文滚粗！Japanese Get out！日本語出て行け！" );
}
if(preg_match($ruattern, $incoming_comment['comment_content'])){
wp_die( "北方野人讲的话我们不欢迎！Russians, get away！Savage выйти из Русского Севера!" );
}
if(preg_match($krattern, $incoming_comment['comment_content'])){
wp_die( "思密达的世界你永远不懂！Please do not use Korean！하시기 바랍니다 한국 / 한국어 사용하지 마십시오！" );
}
if(preg_match($arattern, $incoming_comment['comment_content'])){
wp_die( "禁止使用阿拉伯语！Please do not use Arabic！！من فضلك لا تستخدم اللغة العربية" );
}
if(preg_match($thattern, $incoming_comment['comment_content'])){
wp_die( "人妖你好，人妖再见！Please do not use Thai！กรุณาอย่าใช้ภาษาไทย！" );
}
if(preg_match("/http/i", $incoming_comment['comment_author'])){
        wp_die(__('用户名不能含有链接', 'tt'), __('温馨提示', 'tt'), 403);
    }
return( $incoming_comment );
}
add_filter('preprocess_comment', 'ssdax_comment_all_post');
