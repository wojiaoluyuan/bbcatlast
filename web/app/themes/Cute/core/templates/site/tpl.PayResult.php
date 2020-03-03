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

if(!is_user_logged_in() && strpos(trim($_GET['oid']),'U') !== 0){
    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
}

if(!isset($_GET['oid']) || !isset($_GET['spm'])){
    wp_die(__('The required parameters for check result of order payment  are missing', 'tt'), __('Invalid Query Parameters', 'tt'), 403);
}

if(!wp_verify_nonce(htmlspecialchars($_GET['spm']), 'pay_result')){
    wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 403);
}

$order_id = htmlspecialchars($_GET['oid']);
$order = tt_get_order($order_id);
if(!$order){
    wp_die(__('The order with order id you specified is not existed', 'tt'), __('Invalid Order', 'tt'), 403);
}

if(!in_array($order->order_status, array(2, 3, 4))){
    wp_die(__('The order with order id you specified has not been payed', 'tt'), __('Invalid Order', 'tt'), 403);
}

$currency = $order->order_currency;

//$sub_orders = array();
//if($order->parent_id == -1){
//    $sub_orders = tt_get_sub_orders($order->id);
//}

$current_user = wp_get_current_user();
?>
<?php tt_get_header('simple'); ?>
<body class="is-loadingApp site-page payresult">
<?php load_template(THEME_MOD . '/mod.LogoHeader.php'); ?>
<div id="content" class="wrapper container no-aside">
    <div class="main inner-wrap">
        <section class="processor">
            <ol>
                <li class="done size1of2">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">1</i>
                        <h4><?php _e('Confirm Order', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="done size1of2 no_extra">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">2</i>
                        <h4><?php _e('Accomplish Payment', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="current size1of2 last">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">3</i>
                        <h4><?php _e('Confirm Delivery', 'tt'); ?></h4>
                    </div>
                </li>
            </ol>
        </section>
        <section class="result">
            <div class="result-wrapper">
                <h1><?php echo sprintf(__('Payment for Order ID %s has finished successfully', 'tt'), $order_id); ?></h1>
                <?php if($order->order_currency == 'credit'){ ?>
                <p class="order-status"><?php echo sprintf(__('You use site credit to accomplish this payment, cost %d credits, currently your credit balance is: %d', 'tt'), intval($order->order_total_price), tt_get_user_credit($current_user->ID)); ?></p>
                <?php }else{ ?>
                <p class="order-status"><?php echo sprintf(__('Currently the order status is: %s', 'tt'), tt_get_order_status_text($order->order_status)); ?></p>
                <?php } ?>
                <?php if($order->product_id == '-9') { ?>
                <h2>你的注册邀请码为：<span style="color: #17a8e3;"><?php setcookie('invite_order',$order->order_id,time()-3600,'/');echo $order->trade_no; ?></span></h2>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('signup').'?invite='.$order->trade_no; ?>" target="_blank">立刻注册</a></p>
                <?php }elseif($order->product_id == '-8'){ ?>
                <?php if(is_user_logged_in()){update_user_meta($current_user->ID, 'donate_order', tt_encrypt($order_id, tt_get_option('tt_private_token')));}else{ ?>
                <?php $active_time = tt_get_option('tt_k_donate_active_time');setcookie('donate_order',tt_encrypt($order_id, tt_get_option('tt_private_token')),time()+3600*$active_time,'/'); ?>
                <?php } ?>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo home_url(); ?>" target="_blank">尽情下载</a></p>
                <?php }else{ ?>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('my_order', $order->id); ?>" target="_blank"><?php _e('Check Order Detail', 'tt'); ?></a></p>
                <?php } ?>
            </div>
        </section>
    </div>
</div>
</body>
<?php tt_get_footer(); ?>