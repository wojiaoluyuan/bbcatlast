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

if(!isset($_POST['trade_status'])) {
    wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 404); // 防止直接GET访问
}
//支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
$alipayPublicKey = tt_get_option('tt_alipay_public_key');
$aliPay = new AlipayNotify($alipayPublicKey);
//验证签名
if(strpos($_POST['fund_bill_list'], "\\") > 0){
$_POST['fund_bill_list'] = stripslashes($_POST['fund_bill_list']);
}
$result = $aliPay->rsaCheck($_POST);
if($result===true){//验证成功
    //商户订单号
    $out_trade_no = htmlspecialchars(trim($_POST['out_trade_no']));
    //支付宝交易号
    $trade_no = trim($_POST['trade_no']);
    //交易状态
    $trade_status = trim($_POST['trade_status']);
  
    $buyer_alipay = trim($_POST['buyer_logon_id']);

    if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
        //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款

        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序

        echo "fail";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
        //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货

        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
        //该判断表示卖家已经发了货，但买家还没有做确认收货的操作

        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序
        $order = tt_get_order($out_trade_no);
        if($order && $order->order_status <= 2){
            tt_update_order($out_trade_no, array(
                'order_status' => 3,
                'trade_no' => $trade_no,
                'user_alipay' => $buyer_alipay
            ), array('%d', '%s', '%s', '%s'));
        }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }else if($_POST['trade_status'] == 'TRADE_FINISHED'||$_POST['trade_status'] == 'TRADE_SUCCESS') {
        //该判断表示买家已经确认收货，这笔交易完成

        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序
        $order = tt_get_order($out_trade_no);
        if($order && $order->order_status <= 3){
            tt_update_order($out_trade_no, array(
                'order_status' => 4,
                'order_success_time' => current_time('mysql'),
                'trade_no' => $trade_no,
                'user_alipay' => $buyer_alipay
            ), array('%d', '%s', '%s', '%s'));
        }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }elseif($_POST['trade_status'] == 'TRADE_CLOSED'){
        $order = tt_get_order($out_trade_no);
        if($order && $order->order_status <= 3){
            tt_update_order($out_trade_no, array(
                'order_status' => 9,
                'order_success_time' => current_time('mysql'), //关闭的交易success_time字段实际为交易关闭时间
                'trade_no' => $trade_no,
                'user_alipay' => $buyer_alipay
            ), array('%d', '%s', '%s', '%s'));
        }
        echo "success";
    }else {
        //其他状态判断
        echo "success";

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
