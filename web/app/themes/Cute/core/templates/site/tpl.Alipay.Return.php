<?php
/**
 * Copyright (c) 2014-2018, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since LTS-190127
 * @package BBCat
 * @author 哔哔猫
 * @date 2019/01/27 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

if(!is_user_logged_in() && strpos(trim($_GET['out_trade_no']),'U') !== 0){
    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
}
$current_user = wp_get_current_user();
//支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
$alipayPublicKey = tt_get_option('tt_alipay_public_key');
$aliPay = new AlipayNotify($alipayPublicKey);
//验证签名
$result = $aliPay->rsaCheck($_GET);
if($result===true){//验证成功
    //商户订单号
    $out_trade_no = htmlspecialchars(trim($_GET['out_trade_no']));
    //支付宝交易号
    $trade_no = trim($_GET['trade_no']);
    // 付款额
    $total_fee = sprintf('%0.2f', trim($_GET['total_amount']));

        //判断该笔订单是否在商户网站中已经做过处理
        $order = tt_get_order($out_trade_no);
        $product_id = $order->product_id;
        if($order && $order->order_status <= 3){
            tt_update_order($out_trade_no, array(
                'order_status' => 4,
                'order_success_time' => current_time('mysql'),
                'trade_no' => $trade_no,
                'user_alipay' => ''
            ), array('%d', '%s', '%s', '%s'));
        }
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序

} else {
    //验证失败
    wp_die(__('Verify return result failed, please contact the site administrator if you have finished your payment', 'tt'), __('Verify Failed', 'tt'));
    exit;
}

$updated_order = tt_get_order($out_trade_no);
?>
<?php tt_get_header('simple'); ?>
<body class="is-loadingApp site-page payresult alipay-return">
<?php load_template(THEME_MOD . '/mod.LogoHeader.php'); ?>
<div id="content" class="wrapper container no-aside">
    <div class="main inner-wrap">
        <section class="processor">
            <ol>
                <li class="size1of2">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">1</i>
                        <h4><?php _e('Confirm Order', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="size1of2 no_extra">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">2</i>
                        <h4><?php _e('Payment Accomplish', 'tt'); ?></h4>
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
                <h1><?php echo sprintf(__('Payment for Order ID %s has finished successfully', 'tt'), $updated_order->order_id); ?></h1>
                <p class="order-status"><?php echo sprintf(__('You have payed %f yuan, Currently the order status is: %s', 'tt'), $total_fee, tt_get_order_status_text($updated_order->order_status)); if($updated_order->order_status < 4) _e('<br>You need to go to visit your alipay account and confirm delivery.', 'tt'); ?></p>
                <?php if($product_id == '-9') { ?>
                <h2>你的注册邀请码为：<span style="color: #17a8e3;"><?php setcookie('invite_order',$updated_order->order_id,time()-3600,'/');echo $updated_order->trade_no; ?></span></h2>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('signup').'?invite='.$updated_order->trade_no; ?>" target="_blank">立刻注册</a></p>
                <?php }elseif($product_id == '-8'){ ?>
                <?php if(is_user_logged_in()){update_user_meta($current_user->ID, 'donate_order', tt_encrypt($updated_order->order_id, tt_get_option('tt_private_token')));}else{ ?>
                <?php $active_time = tt_get_option('tt_k_donate_active_time');setcookie('donate_order',tt_encrypt($updated_order->order_id, tt_get_option('tt_private_token')),time()+3600*$active_time,'/'); ?>
                <?php } ?>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo home_url(); ?>" target="_blank">尽情下载</a></p>
                <?php }else{ ?>
                <p><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('my_order', $updated_order->id); ?>" target="_blank"><?php _e('Check Order Detail', 'tt'); ?></a></p>
                <?php } ?>
            </div>
        </section>
    </div>
</div>
</body>
<?php tt_get_footer(); ?>
