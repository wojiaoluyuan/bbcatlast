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
<?php tt_get_header(); ?>
    <div id="content" class="wrapper">
        <!-- 主要内容区 -->
        <section class="container main-area">
            <div class="inner row">
                <?php load_mod('management/mg.NavMenu'); ?>
                <?php load_mod('management/mg.Tab.Coupons'); ?>
            </div>
        </section>
    </div>
<?php tt_get_footer(); ?>