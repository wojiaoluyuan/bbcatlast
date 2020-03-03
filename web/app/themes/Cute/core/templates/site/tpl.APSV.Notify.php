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
defined('APSV_DEBUG') || define('APSV_DEBUG', true);

// Debug //
function tt_debug_log($text)
{
    if (!APSV_DEBUG) return;
    $file = THEME_DIR.'/apsv.log';
    file_put_contents($file, $text . PHP_EOL, FILE_APPEND);
}
tt_debug_log('ping....');

//////////////////////////////////////////////////////////////////

// TODO
// tt_get_option('tt_pay_channel', 'alipay')=='apsv' ?
//[orderData.time.toString(), orderData.tradeNo.toString(), orderData.status.toString(), this.secret.toString()].join('|');

ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$codepay_key="NxL3FtFsR1XLawls12u098TjbaY8Ruw"; //这是您的密钥
$sign = '';//初始化
foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign') continue; //跳过这些不签名
    if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $sign .= "$key=$val"; //拼接为url参数形式
}
if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
    tt_debug_log('fail');
    exit('fail');  //返回失败 继续补单
} else { //合法的数据
$secret = tt_get_option('tt_codepay_secret');
if($_POST['param'] != $secret){
    tt_debug_log('fail');
    exit('secret_fail');  //返回失败 继续补单
}

// 所有验证通过, 开始业务逻辑
$order = tt_get_order($_POST['pay_id']);

if (!$order) {
    echo 'fail(no-order)';
    tt_debug_log('fail(no-order');
    exit();
}

// 验证金额
$amount = isset($_POST['money']) ? (float)$_POST['money'] : 0.0;
if ($order->order_total_price - $amount > 0.1) { // 少了1毛钱就不干 // 0.01?
    echo 'fail(insufficient-pay)'; //TODO email notify 未足额支付
    tt_debug_log('fail(insufficient-pay) - PAY:' . $amount . ' NEED:' . $order->order_total_price);
    exit();
}

$order_status = isset($_POST['pay_no']) ? '交易成功' : '';

if ($order_status == '交易成功') { // 转账支付只会有`交易成功`这个状态
    $success_time = isset($_POST['pay_time']) ? date('Y-m-d H:i:s', $_POST['pay_time']) : current_time('mysql');
    $trade_no = isset($_POST['pay_no']) ? trim($_POST['pay_no']) : '';
    $username = '码支付';
    if ($order->order_status <= 3) {
        tt_update_order($order->order_id, array(
            'order_status' => 4,
            'order_success_time' => $success_time,
            'trade_no' => $trade_no,
            'user_alipay' => $username
        ), array('%d', '%s', '%s', '%s'));
    }
    tt_debug_log('success');
    echo "success";		//请不要修改或删除
    exit();
}
else {
    echo 'fail(wrong-order-status)';
    tt_debug_log('fail(wrong-order-status');
    exit();
}
  }