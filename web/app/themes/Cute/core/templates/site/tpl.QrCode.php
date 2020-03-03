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

if((!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) && (!isset($_REQUEST['key']) || $_REQUEST['key'] != 'bigger')) {
    wp_die(__('The request is not allowed', 'tt'), __('Illegal request', 'tt'));
}

if(!isset($_REQUEST['text'])) {
    wp_die(__('The text parameter is missing', 'tt'), __('Missing argument', 'tt'));
}

$text = trim($_REQUEST['text']);

load_class('class.QRcode');

QRcode::png($text);