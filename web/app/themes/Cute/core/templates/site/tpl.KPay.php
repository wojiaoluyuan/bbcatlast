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
if (!isset($_GET['oid'])) {
    wp_die(__('The required parameters for retrieve a order  are missing', 'tt'), __('Invalid Query Parameters', 'tt'), 403);
}

$order_id = htmlspecialchars($_GET['oid']);
$order = tt_get_order($order_id);
if (!$order) {
    wp_die(__('The order with order id you specified is not existed', 'tt'), __('Invalid Order', 'tt'), 403);
}

if (in_array($order->order_status, array(2, 3, 4))) {
    wp_die(__('The order with order id you specified has been payed', 'tt'), __('Invalid Order', 'tt'), 403);
}

$currency = $order->order_currency;

if ($currency != 'cash') {
    wp_die(__('The order does not support cash payment', 'tt'), __('Unsuitable Payment Method', 'tt'), 403);
}

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
$channel = $_GET['channel'] ? str_replace('kpay_','',trim($_GET['channel'])) : 'alipay';
if($channel == 'wxpay'){
  $channel_text = '微信';
}elseif($channel == 'qqpay'){
  $channel_text = '手机QQ';
}else{
  $channel_text = '支付宝';
}
require_once THEME_LIB.'/bbcatpay/kpay.config.php';
require_once THEME_LIB.'/bbcatpay/lib/kpay_submit.class.php';
//构造要请求的参数数组，无需改动
$parameter = array(
		"pid" => trim($alipay_config['partner']),
		"type" => $channel,
		"notify_url"	=> tt_url_for('kpaynotify'),
		"return_url"	=> tt_url_for('kpaynotify'),
		"out_trade_no"	=> $order->order_id,
		"name"	=> $product_name,
		"money"	=> $order->order_total_price,
		"sitename"	=> get_bloginfo('name')
);
//建立请求
$alipaySubmit = new KpaySubmit($alipay_config);
$result = $alipaySubmit->buildRequestForm($parameter);
if($result['code']=='success'){
    //生成二维码
    $url = home_url('/').'site/qr?key=bigger&text='.$result['pay_url'];
    $qr_url = $result['pay_url'];
}else{
    wp_die('获取二维码失败', __('错误', 'tt'), 403);
}
?>
<?php tt_get_header('simple'); ?>
<body class="is-loadingApp site-page qrpay yz-qrpay">
<?php load_template(THEME_MOD.'/mod.LogoHeader.php'); ?>
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
                <li class="current size1of2 no_extra">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">2</i>
                        <h4><?php _e('Accomplish Payment', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="size1of2 last">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">3</i>
                        <h4><?php _e('Confirm Delivery', 'tt'); ?></h4>
                    </div>
                </li>
            </ol>
        </section>
        <section class="payment">
            <div class="payment-wrapper">
                <h1><?php echo sprintf(__('Payment Amount for Order ID %s is %0.2f', 'tt'), $order_id, $order->order_total_price); ?></h1>
                <p class="introduction">请使用<?php echo $channel_text; ?>扫描下方二维码进行支付, 如果二维码无法显示请刷新页面</p>
                <h4 style="font-weight: 800;color: #f64540;">提示：付款后请勿关闭网页，付款成功将自动跳转</h4>
                <div class="pay-qr-images row">
                <div class="qrcode yz-qrcode">
                        <div id="yzQrcode"><a href="<?php echo $qr_url; ?>" target="_blank">
                        <img class="lazy" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $url; ?>" /></a></div>
                    </div>
</div>
                <div class="actions"><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('my_order', $order->id); ?>" target="_blank"><?php _e('Check Order Detail', 'tt'); ?></a></div>
            </div>
        </section>
    </div>
</div>
</body>
<?php tt_get_footer(); ?>