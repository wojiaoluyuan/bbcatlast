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

/*** 请填写以下配置信息 ***/
$appid = tt_get_option('tt_alipay_appid');
$returnUrl = tt_url_for('alipayreturn');     //付款成功后的同步回调地址
$notifyUrl = tt_url_for('alipaynotify');     //付款成功后的异步回调地址
$outTradeNo = $order->order_id;     //你自己的商品订单号，不能重复
$payAmount = $order->order_total_price;          //付款金额，单位:元
$orderName = $product_name;    //订单标题
$orderBody = $product_des;     //订单描述
$signType = 'RSA2';			//签名算法类型，支持RSA2和RSA，推荐使用RSA2
$rsaPrivateKey = tt_get_option('tt_alipay_private_key');
/*** 配置结束 ***/
$aliPay = new AliQrPayService();
$aliPay->setAppid($appid);
$aliPay->setNotifyUrl($notifyUrl);
$aliPay->setRsaPrivateKey($rsaPrivateKey);
$aliPay->setTotalFee($payAmount);
$aliPay->setOutTradeNo($outTradeNo);
$aliPay->setOrderName($orderName);
$aliPay->setOrderBody($orderBody);
$result = $aliPay->doPay();
$result = $result['alipay_trade_precreate_response'];
if($result['code'] && $result['code']=='10000'){
    //生成二维码
    $url = home_url('/').'site/qr?key=bigger&text='.$result['qr_code'];
    $qr_url = $result['qr_code'];
}else{
    wp_die(__('The required parameters for retrieve a order  are missing', 'tt'), __('Invalid Query Parameters', 'tt'), 403);
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
                <p class="introduction"><?php _e('请使用支付宝扫描下方二维码进行支付, 如果二维码无法显示请刷新页面', 'tt'); ?></p>
                <h4 style="font-weight: 800;color: #f64540;">提示：付款后请勿关闭网页，付款成功将自动跳转</h4>
                <div class="pay-qr-images row">
                <div class="qrcode yz-qrcode">
                        <div id="yzQrcode"><a href="<?php echo $qr_url; ?>" target="_blank">
                        <img class="lazy" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $url; ?>" /></a></div>
                        <?php if( wp_is_mobile() ) { ?>
                        <a class="btn btn-alipay" href="javascript:;" id="openApp" style="-webkit-background-size: 200% 200%;background-size: 200% 200%;background-position: 50%;color: #fff;background-color: #45b6f7;">启动支付宝APP</a>
                        <script type="text/javascript">
    document.getElementById('openApp').onclick = function(e){
        
        if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i))
           {
            window.location.href = "alipays://platformapi/startapp?saId=10000007&qrcode=<?php echo urlencode($qr_url); ?>";
            window.setTimeout(function() {
                window.location.href = "https://itunes.apple.com/cn/app/id333206289";
            }, 2000)
           }
        if(navigator.userAgent.match(/android/i))
        {
            window.location.href = "alipays://platformapi/startapp?saId=10000007&qrcode=<?php echo urlencode($qr_url); ?>";
            window.setTimeout(function() {
                window.location.href = "https://ds.alipay.com/";
            }, 2000)    
        }
    };
</script>
                        <?php } ?>
                    </div>
</div>
                <div class="actions"><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('my_order', $order->id); ?>" target="_blank"><?php _e('Check Order Detail', 'tt'); ?></a></div>
            </div>
        </section>
    </div>
</div>
</body>
<?php tt_get_footer(); ?>