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
defined('YOUZAN_DEBUG') || define('YOUZAN_DEBUG', false);
// Debug //
function tt_debug_log($text)
{
    if (!YOUZAN_DEBUG) {
        return;
    }
    $file = THEME_DIR.'/youzan.log';
    file_put_contents($file, $text.PHP_EOL, FILE_APPEND);
}
tt_debug_log('ping....');

if ($_POST && isset($_POST['tradeNo'])) {
    //////////////////////////////////////////////////////////////////

    // TODO
    // tt_get_option('tt_pay_channel', 'alipay')=='apsv' ?
    //[orderData.time.toString(), orderData.tradeNo.toString(), orderData.status.toString(), this.secret.toString()].join('|');

    if (!isset($_POST['sign'])) {
        //echo 'fail';
        tt_debug_log('no sign');
        wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 404); // 防止直接GET访问
        exit();
    }

    $secret = tt_get_option('tt_youzan_self_secret');

    $order_data = array(
        'time' => isset($_POST['time']) ? htmlspecialchars($_POST['time']) : '',
        'tradeNo' => isset($_POST['tradeNo']) ? htmlspecialchars($_POST['tradeNo']) : '',
        'orderId' => isset($_POST['orderId']) ? htmlspecialchars($_POST['orderId']) : '',
        'payment' => isset($_POST['payment']) ? htmlspecialchars($_POST['payment']) : 0,
        'status' => isset($_POST['status']) ? htmlspecialchars($_POST['status']) : '',
        'secret' => $secret,
    );

    if (md5(implode('|', $order_data)) != trim(htmlspecialchars($_POST['sign']))) {
        echo 'fail(wrong-token)';
        tt_debug_log('fail(wrong-token)');
        exit();
    }

    // 所有验证通过, 开始业务逻辑

    $order_id = intval(trim($order_data['orderId']));

    $order = tt_get_order($order_id);

    if (!$order) {
        echo 'fail(no-order)';
        tt_debug_log('fail(no-order');
        exit();
    }

    // 验证金额
    $amount = $order_data['payment'];
    if ($order->order_total_price * 100 - $amount > 10) { // 少了1毛钱就不干 // 0.01?
        echo 'fail(insufficient-pay)'; //TODO email notify 未足额支付
        tt_debug_log('fail(insufficient-pay) - PAY:'.($amount / 100).' NEED:'.$order->order_total_price);
        exit();
    }

    $order_status = trim($order_data['status']);

    if ($order_status == 'TRADE_SUCCESS') { // 转账支付只会有`交易成功`这个状态
        $success_time = current_time('mysql');
        $trade_no = trim($order_data['tradeNo']);
        if ($order->order_status <= 3) {
            tt_update_order($order->order_id, array(
                'order_status' => 4,
                'order_success_time' => $success_time,
                'trade_no' => $trade_no,
            ), array('%d', '%s', '%s'));
        }
        tt_debug_log('success');
        echo 'success';		//请不要修改或删除
        exit();
    } elseif ($order_status == 'TRADE_CLOSED') {
        $trade_no = trim($order_data['tradeNo']);
        if ($order->order_status <= 3) {
            tt_update_order($order->order_id, array(
                'order_status' => 9,
                'trade_no' => $trade_no,
            ), array('%d', '%s'));
        }
        tt_debug_log('close');
        echo 'success';		//请不要修改或删除
        exit();
    } else {
        echo 'fail(wrong-order-status)';
        tt_debug_log('fail(wrong-order-status)');
        exit();
    }
} else {
    $client_id = tt_get_option('tt_youzan_client_id', ''); //应用的 client_id
    $client_secret = tt_get_option('tt_youzan_client_secret', ''); //应用的 client_secret
    $kdt_id = tt_get_option('tt_youzan_kdt_id', ''); // 有赞微小店ID

    $success_result = array('code' => 0, 'msg' => 'success');

    $json = file_get_contents('php://input');

    if (!$json) {
        tt_debug_log('get visit');
        wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 404); // 防止直接GET访问
        exit();
    }

    $data = json_decode($json, true);

    /**
     * 判断消息是否合法，若合法则返回成功标识.
     */
    $msg = $data['msg'];
    $sign_string = $client_id.''.$msg.''.$client_secret;
    $sign = md5($sign_string);
    if ($sign != $data['sign']) {
        exit();
    } else {
        var_dump($success_result);
    }

    /**
     * msg内容经过 urlencode 编码，需进行解码
     */
    $msg = json_decode(urldecode($msg), true);

    tt_debug_log('got msg:'.json_encode($msg));

    /*
     * 根据 type 来识别消息事件类型，具体的 type 值以文档为准，此处仅是示例
     */
    if ($data['type'] != 'trade_TradeClose' && $data['type'] != 'trade_TradeSuccess') {
        tt_debug_log('ignore type '.$data['type']);
        exit();
    }

    $tid = $msg['tid'];

    // 获取订单详情
    require_once THEME_LIB.'/youzan/YZGetTokenClient.php';
    require_once THEME_LIB.'/youzan/YZTokenClient.php';
    $token_client = new YZGetTokenClient($client_id, $client_secret);

    $type = 'self';
    $keys = array(
        'grant_type' => 'silent',
        'kdt_id' => intval($kdt_id),
    );
    $token = $token_client->get_token($type, $keys);

    tt_debug_log('got token: '.json_encode($token));

    $client = new YZTokenClient($token['access_token']);

    $method = 'youzan.trade.get'; //要调用的api名称
    $api_version = '3.0.0'; //要调用的api版本号
    $params = array(
        'tid' => $tid,
    );

    $resp = $client->post($method, $api_version, $params);
    $trade = $resp['response']['trade'];
    $qr_id = $trade['qr_id'];
    $payment = $trade['payment'];
    $status = $data['status'];
    $trade_no = $tid;

    tt_debug_log('got response: '.json_encode($resp));

    tt_debug_log('got trade detail: '.json_encode($trade));

    $order_qr_id = tt_get_order_by_trade_no($qr_id);
    $order_title = tt_get_order_by_name($trade['title']);
    if($order_qr_id){
      $order= $order_qr_id;
      tt_debug_log('this order with qr_id '.$qr_id);
    }else{
      $order= $order_title;
      tt_debug_log('this order with order_title '.$order->order_id);
    }

    if (!$order || $order->order_status >= OrderStatus::TRADE_SUCCESS) {
        tt_debug_log('no this order with qr_id '.$qr_id);
        exit();
    }

    // 验证金额
    if ($order->order_total_price * 100 - $payment * 100 > 10) { // 少了1毛钱就不干 // 0.01?
        echo 'fail(insufficient-pay)'; //TODO email notify 未足额支付
        tt_debug_log('fail(insufficient-pay) - PAY:'.($amount / 100).' NEED:'.$order->order_total_price);
        exit();
    }

    if ($status == 'TRADE_SUCCESS') { // 转账支付只会有`交易成功`这个状态
        $success_time = current_time('mysql');
        if ($order->status <= 3) {
            tt_update_order($order->order_id, array(
                'order_status' => 4,
                'order_success_time' => $success_time,
                'trade_no' => $trade_no,
            ), array('%d', '%s', '%s'));
        }
        tt_debug_log('success');
        exit();
    } elseif ($status == 'TRADE_CLOSED') {
        if ($order->order_status <= 3) {
            tt_update_order($order->order_id, array(
                'order_status' => 9,
                'trade_no' => $trade_no,
            ), array('%d', '%s'));
        }
        tt_debug_log('close');
        exit();
    } else {
        echo 'fail(wrong-order-status)';
        tt_debug_log('fail(wrong-order-status)');
        exit();
    }
}
