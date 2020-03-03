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

$product_id = '-9';
$invite_active_time = tt_get_option('tt_k_invite_active_time');
$product_name = '注册邀请码（有效期'.$invite_active_time.'天）';
$order_quantity = '1';
$checkout_nonce = wp_create_nonce('checkout');
if(isset($_COOKIE['invite_order'])){
  $checkout_url = add_query_arg(array('oid' => $_COOKIE['invite_order'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
}else{
  $create = tt_create_invite_order($product_id, $product_name, intval($order_quantity));
  $checkout_url = add_query_arg(array('oid' => $create['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
  setcookie('invite_order',$create['order_id'],time()+3600*24*7,'/');
}
header("Location: $checkout_url");