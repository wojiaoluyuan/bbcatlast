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

// 要求noindex
wp_no_robots();

$open_type = strtolower(get_query_var('oauth'));

if(!in_array($open_type, (array)json_decode(ALLOWED_OAUTH_TYPES))){
    $response_code = is_user_logged_in() ? '403' : '401';
    wp_die(__('The request you have done is illegal, please retry', 'tt'), __('Illegal Request', 'tt'), array('response' => $response_code, 'back_link' => true));
}

switch($open_type) {
    case 'qq':
        $open = new OpenQQ(wp_get_current_user());
        break;
    case 'weibo':
        $open = new OpenWeibo(wp_get_current_user());
        break;
    case 'weixin':
        $open = new OpenWeiXin(wp_get_current_user());
        break;
}

$try = $open->openHandle(); // 成功会跳转，无需再执行处理

if(!$try){
    $error = $open->getError();
    wp_die($error->message, $error->title, array('back_link' => true));
}

// 对于其他任意情况，一律如下处理
$response_code = is_user_logged_in() ? '403' : '401';
wp_die(__('The request you have done is illegal, please retry', 'tt'), __('Illegal Request', 'tt'), array('response' => $response_code, 'back_link' => true));
