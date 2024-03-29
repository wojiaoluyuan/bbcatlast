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
<?php tt_get_header(); ?>
<div id="content" class="wrapper">
        <?php load_mod('uc/uc.TopPane'); ?>
        <!-- 主要内容区 -->
        <section class="container author-area">
            <div class="inner">
            <?php load_mod('uc/uc.NavTabs'); ?>
            <?php load_mod('uc/uc.Tab.Chat'); ?>
            </div>
        </section>
</div>
<?php tt_get_footer(); ?>