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
<?php $fullSlide = tt_get_option('tt_k_custom_slide');$paged = get_query_var('paged'); ?>
<?php if (tt_get_option('tt_enable_home_slides', false) && (!$paged || $paged===1)) { ?>
<?php if($fullSlide == 'min') { ?>
<?php load_mod('mod.HomeMinSlide'); ?>
<?php }else{ ?>
 <section class="nt-slider <?php if($fullSlide != 'max_big') { echo 'slider-big';}?> owl-carousel">
     <?php load_mod('mod.HomeSlide'); ?>
     </section>
<?php } ?>
<?php load_mod(('banners/bn.Slide.Bottom')); ?>
<?php } ?>
<?php if(is_home() || (is_single() && in_array(get_post_type(), array('post', 'product', 'page')))) { ?>
<!-- 顶部公告 -->
<?php load_mod('mod.HomeBulletins'); ?>
<?php } ?>
<div id="content" class="wrapper container right-aside">
    <?php load_mod(('banners/bn.Top')); ?>
    <!-- 分类模块与边栏 -->
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 分类模块列表 -->
        <?php load_mod('mod.HomeCMSCats'); ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
    </section>
    <?php load_mod(('banners/bn.Bottom')); ?>
</div>
<?php tt_get_footer(); ?>