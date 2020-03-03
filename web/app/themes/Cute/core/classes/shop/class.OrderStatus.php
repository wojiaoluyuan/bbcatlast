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