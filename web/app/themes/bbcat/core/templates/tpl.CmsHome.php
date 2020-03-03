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
<?php $fullSlide = tt_get_option('tt_enable_home_full_width_slides', false); ?>
<div id="content" class="wrapper container right-aside">
    <?php load_mod(('banners/bn.Top')); ?>
    <?php if(tt_get_option('tt_enable_home_slides', false)) : ?>
        <!-- 顶部Slides + Popular -->
        <section id="mod-show" class="content-section clearfix <?php if ($fullSlide) echo 'full'; ?>">
            <?php load_mod('mod.HomeSlide'); ?>
            <?php if ($fullSlide == false) { load_mod('mod.HomePopular'); } ?>
        </section>
        <?php load_mod(('banners/bn.Slide.Bottom')); ?>
    <?php endif; ?>
    <!-- 分类模块与边栏 -->
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 分类模块列表 -->
        <?php load_mod('mod.HomeCMSCats'); ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
    </section>
    <?php if(tt_get_option('tt_home_products_recommendation', false)) { ?>
        <!-- 商品展示 -->
        <section id="mod-sales" class="content-section clearfix">
            <?php load_mod('mod.ProductGallery', true); ?>
        </section>
    <?php } ?>
    <?php load_mod(('banners/bn.Bottom')); ?>
</div>
<?php tt_get_footer(); ?>