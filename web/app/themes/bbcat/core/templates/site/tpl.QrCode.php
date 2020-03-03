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

if(!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
    wp_die(__('The request is not allowed', 'tt'), __('Illegal request', 'tt'));
}

if(!isset($_REQUEST['text'])) {
    wp_die(__('The text parameter is missing', 'tt'), __('Missing argument', 'tt'));
}

$text = trim($_REQUEST['text']);

load_class('class.QRcode');

QRcode::png($text);