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
if(!isset($_POST['trade_status'])) {
    wp_die(__('You are acting an illegal visit', 'tt'), __('Illegal Visit', 'tt'), 404); // 防止直接GET访问
}
require_once THEME_LIB.'/bbcatpay/kpay.config.php';
require_once THEME_LIB.'/bbcatpay/lib/kpay_notify.class.php';

$alipayNotify = new KpayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result) {
    //商户订单号
    $out_trade_no = $_POST['out_trade_no'];
    //微信交易号
    $trade_no = $_POST['trade_no'];
    //交易状态
    $trade_status = $_POST['trade_status'];
  
    $buyer_wxid = $_POST['type'];

    if($trade_status == 'TRADE_SUCCESS') {
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
                'user_alipay' => $buyer_wxid
            ), array('%d', '%s', '%s', '%s'));
        }
        echo "success";		//请不要修改或删除

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
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
