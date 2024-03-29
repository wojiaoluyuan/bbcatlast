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
 * Class Product
 *
 * 定义order的product_id enum
 */
final class Product {
    const MONTHLY_VIP = -1;

    const MONTHLY_VIP_NAME = '月费会员';

    const ANNUAL_VIP = -2;

    const ANNUAL_VIP_NAME = '年费会员';

    const PERMANENT_VIP = -3;

    const PERMANENT_VIP_NAME = '永久会员';

    const CREDIT_CHARGE = -4;

    const CREDIT_CHARGE_NAME = '站内积分';
}