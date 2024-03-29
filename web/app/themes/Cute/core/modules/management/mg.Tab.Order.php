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
<?php global $tt_me_vars; $tt_user_id = $tt_me_vars['tt_user_id']; $tt_order_seq = get_query_var('manage_grandchild_route'); ?>
<div class="col col-right order">
    <?php $vm = MgOrderVM::getInstance($tt_order_seq); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Order detail cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php $data = $vm->modelData; $order = $data->order; $order_status_text = $data->order_status_text; $pay_method = $data->pay_method; $pay_amount = $data->pay_amount; $pay_content = $data->pay_content; $is_combined = $data->is_combined; $sub_orders = $data->sub_orders; $address = $data->address ?>
    <div class="mg-tab-box order-tab">
        <div class="tab-content">
            <?php if($order) { ?>
                <!-- 订单信息 -->
                <section class="mg-order clearfix">
                    <header><h2><?php _e('Order Detail', 'tt'); ?></h2></header>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Order ID', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order->order_id; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Trade NO', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order->trade_no ? $order->trade_no : 'N/A'; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Order Content', 'tt'); ?></label>
                            <div class="col-md-9">
                                <p class="form-control-static"><?php if($order->product_id > 0) { ?><a href="<?php echo get_permalink($order->product_id); ?>" target="_blank"><?php echo $order->product_name; ?></a><?php }else{ echo $order->product_name; } ?></p>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Order Create Time', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order->order_time; ?></p></div>
                        </div>
                        <?php if($order->order_status == OrderStatus::TRADE_SUCCESS) { ?>
                            <div class="row clearfix">
                                <label class="col-md-3 control-label"><?php _e('Order Success Time', 'tt'); ?></label>
                                <div class="col-md-9"><p class="form-control-static"><?php echo $order->order_success_time; ?></p></div>
                            </div>
                        <?php } ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Payment Status', 'tt'); ?></label>
                            <div class="col-md-9">
                                <p class="form-control-static">
                                    <?php echo $order_status_text; ?>
                                </p>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Payment Method', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $pay_method; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Payment Amount', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $pay_amount; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Buyer Info', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo get_user_meta($order->user_id, 'nickname', true) . ' <strong>(ID: ' . $order->user_id . ')</strong>'; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Buyer Message', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order->user_message; ?></p></div>
                        </div>
                        <?php if($address) { ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('Delivery Info', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php printf('%1$s<br>%2$s<br>%3$s %4$s %5$s', $address->user_name, $address->user_email, $address->user_address, $address->user_zip, $address->user_cellphone); ?></p></div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
                <!-- 价格管理 -->
                <?php if($order->parent_id < 1 && in_array($order->order_status, array(OrderStatus::WAIT_PAYMENT, OrderStatus::PAYED_AND_WAIT_DELIVERY, OrderStatus::DELIVERED_AND_WAIT_CONFIRM))) { ?>
                <section class="mg-price clearfix">
                    <header><h2>价格管理</h2></header>
                    <div class="info-group clearfix" style="margin-left: 20px;">
                        <div class="form-group add-cash-form">
                            <div class="form-inline">
                                <div class="form-group">
                                    <div class="input-group active">
                                        <div class="input-group-addon"><?php if($data->pay_currency == 'cash'){echo '现金(单位: 元)';}else{echo '积分';}; ?></div>
                                        <input class="form-control" type="number" min="1" name="price-num" id="price-num" value="<?php echo $data->pay_price; ?>" aria-required="true" required="">
                                    </div>
                                </div>
                                <button class="btn btn-inverse" type="submit" id="update_price" data-order-seq="<?php echo $order->id; ?>" data-order-id="<?php echo $order->order_id; ?>">修改价格</button>
                            </div>
                        </div>
                    </div>
                </section>
                <?php } ?>
                <!-- 状态管理 -->
                <?php if($order->parent_id < 1 && in_array($order->order_status, array(OrderStatus::WAIT_PAYMENT, OrderStatus::PAYED_AND_WAIT_DELIVERY, OrderStatus::DELIVERED_AND_WAIT_CONFIRM))) { ?>
                <section class="mg-status clearfix">
                    <header><h2><?php _e('Manage Order Status', 'tt'); ?></h2></header>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <a class="btn btn-success btn-wide order-status-act" data-order-seq="<?php echo $order->id; ?>" data-order-id="<?php echo $order->order_id; ?>" data-act-value="<?php echo OrderStatus::TRADE_SUCCESS; ?>"><?php _e('FINISH ORDER', 'tt'); ?></a>
                            <?php if($order->parent_id < 1 && in_array($order->order_status, array(OrderStatus::DEFAULT_STATUS, OrderStatus::WAIT_PAYMENT))) { ?>
                            <a class="btn btn-danger btn-wide order-status-act" data-order-seq="<?php echo $order->id; ?>" data-order-id="<?php echo $order->order_id; ?>" data-act-value="<?php echo OrderStatus::TRADE_CLOSED; ?>"><?php _e('CLOSE ORDER', 'tt'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <?php } ?>
            <?php }else{ ?>
            <section class="mg-order clearfix">
                <header><h2><?php _e('Order Detail', 'tt'); ?></h2></header>
                <div class="empty-content">
                    <span class="tico tico-cart"></span>
                    <p><?php _e('No this order', 'tt'); ?></p>
                </div>
                <?php } ?>
        </div>
    </div>
</div>