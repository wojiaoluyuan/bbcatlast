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
<?php $this->layout('base', array('blogName' => get_bloginfo('name'), 'logo' => tt_get_option('tt_logo'), 'home' => home_url(), 'shopHome' => tt_url_for('shop_archive'))) ?>

<p>您的站点「<?php echo get_bloginfo('name'); ?>」有新用户注册:</p>
<div style="background-color:#fefcc9; padding:10px 15px; border:1px solid #f7dfa4; font-size: 12px;line-height:160%;">
    用户名: <?=$this->e($loginName)?>
    <br>注册邮箱: <?=$this->e($email)?>
    <br>注册时间: <?php echo date("Y-m-d H:i:s"); ?>
    <br>注册IP: <?=$this->e($ip)?><?php echo ' [' . tt_query_ip_addr($ip) . ']'; ?>
</div>