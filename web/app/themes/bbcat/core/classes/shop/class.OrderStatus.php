<?php
/**
 * Copyright (c) 2019-2025, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since 2.5.0
 * @package BBcat-K
 * @author 洛茛艺术影视在线
 * @date 2019-04-03 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

/**
 * Class OrderStatus
 *
 * 定义order的status enum
 */
final class OrderStatus {

    const DEFAULT_STATUS = 0;

    const WAIT_PAYMENT = 1;

    const PAYED_AND_WAIT_DELIVERY = 2;

    const DELIVERED_AND_WAIT_CONFIRM = 3;

    const TRADE_SUCCESS = 4;

    const TRADE_CLOSED = 9;
}