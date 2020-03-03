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
    <!-- 主要内容区 -->
    <section class="container user-area">
        <div class="inner row">
            <?php load_mod('me/me.NavMenu'); ?>
            <?php load_mod('me/me.Tab.Order'); ?>
        </div>
    </section>
</div>
<?php tt_get_footer(); ?>