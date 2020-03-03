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

$product_id = '-8';
$active_time = tt_get_option('tt_k_donate_active_time');
$product_name = '捐赠获取下载权限'.$active_time.'小时';
$order_quantity = '1';
$checkout_nonce = wp_create_nonce('checkout');
$create = tt_create_donate_order($product_id, $product_name, intval($order_quantity));
$checkout_url = add_query_arg(array('oid' => $create['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));

header("Location: $checkout_url");