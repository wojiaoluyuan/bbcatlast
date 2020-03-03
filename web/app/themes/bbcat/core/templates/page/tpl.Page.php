<?php
/**
 * Default Page Template
 *
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