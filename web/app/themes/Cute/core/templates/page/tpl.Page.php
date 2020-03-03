<?php
/**
 * Default Page Template
 *
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
    <div id="content" class="wrapper container right-aside">
        <section id="mod-insideContent" class="main-wrap content-section clearfix">
            <!-- 页面 -->
            <?php load_mod('mod.SinglePage'); ?>
            <!-- 边栏 -->
            <?php load_mod('mod.Sidebar'); ?>
        </section>
    </div>
<?php tt_get_footer(); ?>