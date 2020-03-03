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

if(!is_user_logged_in() && strpos($_GET['oid'],'U') !== 0){
    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
}

if(!isset($_GET['oid']) || !isset($_GET['spm'])){
    wp_die(__('The required parameters for payment are missing', 'tt'), __('Invalid Payment Parameters', 'tt'), 403);
}

if(!wp_verify_nonce(htmlspecialchars($_GET['spm']), 'pay_gateway')){
    wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 403);
}

$order_id = htmlspecialchars($_GET['oid']);
$order = tt_get_order($order_id);
if(!$order){
    wp_die(__('The order with order id you specified is not existed', 'tt'), __('Invalid Order', 'tt'), 403);
}

if(in_array($order->order_status, array(2, 3, 4))){
    wp_die(__('The order with order id you specified has been payed', 'tt'), __('Invalid Order', 'tt'), 403);
}

//获取参数

$product_id = $order->product_id;
$product_name = '';
$product_des = '';

if($product_id>=0){
    $product_name = '【'.get_bloginfo('name').'】'.$order->product_name;
    $product_des = get_post_field('post_excerpt', $product_id);
}elseif($product_id==Product::MONTHLY_VIP){
    $product_name = '【'.get_bloginfo('name').'】'.__('VIP Membership(Monthly)', 'tt');
    $product_des = __('Subscribe VIP Membership(Monthly)', 'tt');
}elseif($product_id==Product::ANNUAL_VIP){
    $product_name = '【'.get_bloginfo('name').'】'.__('VIP Membership(Annual)', 'tt');
    $product_des = __('Subscribe VIP Membership(Annual)', 'tt');
}elseif($product_id==Product::PERMANENT_VIP){
    $product_name = '【'.get_bloginfo('name').'】'.__('VIP Membership(Permanent)', 'tt');
    $product_des = __('Subscribe VIP Membership(Permanent)', 'tt');
}elseif($product_id==Product::CREDIT_CHARGE){
    $product_name = '【'.get_bloginfo('name').'】'.__('Credits Charge', 'tt');
    $product_des=$order->product_name;
}elseif($product_id=='-9'){
    $invite_active_time = tt_get_option('tt_k_invite_active_time');
    $product_name = '【'.get_bloginfo('name').'】注册邀请码（有效期'.$invite_active_time.'天）';
    $product_des=$order->product_name;
}elseif($product_id=='-8'){
    $active_time = tt_get_option('tt_k_donate_active_time');
    $product_name = '【'.get_bloginfo('name').'】 捐赠获取下载权限'.$active_time.'小时';
    $product_des=$order->product_name;
}else{
    // TODO more
}
$product_url = ($product_id>0) ? get_permalink($product_id) : tt_url_for('my_settings');
$order_id = $_POST['order_id'];

/*** 请填写以下配置信息 ***/
$appid = tt_get_option('tt_alipay_appid');
$returnUrl = tt_url_for('alipayreturn');
$notifyUrl = tt_url_for('alipaynotify');
$outTradeNo = $order->order_id;
$payAmount = $order->order_total_price;
$orderName = $product_name;
$orderBody = $product_des;
$signType = 'RSA2';
$rsaPrivateKey = tt_get_option('tt_alipay_private_key');
/*** 配置结束 ***/
$aliPay = new AlipayService();
$aliPay->setAppid($appid);
$aliPay->setReturnUrl($returnUrl);
$aliPay->setNotifyUrl($notifyUrl);
$aliPay->setRsaPrivateKey($rsaPrivateKey);
$aliPay->setTotalFee($payAmount);
$aliPay->setOutTradeNo($outTradeNo);
$aliPay->setOrderName($orderName);
$aliPay->setOrderBody($orderBody);
$html_text = $aliPay->doPay();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="noindex,follow">
    <title><?php echo __('Payment Gateway', 'tt') . ' - ' . get_bloginfo('name'); ?></title>
</head>
<body>
    <p><?php _e('Redirecting to alipay...', 'tt'); ?></p>
    <div style="display:none">
        <?php echo $html_text; ?>
    </div>
</body>
</html>