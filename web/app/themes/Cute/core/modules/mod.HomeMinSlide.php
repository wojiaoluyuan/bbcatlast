<?php
/**
 * Copyright (c) 2014-2018, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since 2.5.0
 * @package Tint-K
 * @author 哔哔猫
 * @date 2018/02/14 10:00
 * @link https://bbcatga.herokuapp.com/18494.html
 */
?>
<div id="content" class="wrapper container right-aside" style="padding-bottom: 0px !important;">
<section id="mod-show" class="content-section clearfix">
<section class="nt-slider slider-min owl-carousel">
<!-- 幻灯-->
<?php $vm = SlideVM::getInstance(); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
<!-- Slide cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php if($data = $vm->modelData) { ?>
<?php foreach ($data as $seq=>$slide) { ?>
<?php if(!empty($slide['thumb'])) { ?>
            <div class="item" style="background-image:url('<?php echo $slide['thumb']; ?>')"> 
	        <div class="content">
	            <h2 class="title"><?php echo $slide['title']; ?></h2>
	          </div>
	        <a class="permalink" href="<?php echo $slide['permalink']; ?>" title="<?php echo $slide['title']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>></a>
	    </div><?php } ?><?php } ?><?php } ?>
<?php $custom_slider_contents = tt_get_option('tt_k_custom_slider_content');
      $custom_slider_contents = explode(PHP_EOL, $custom_slider_contents); ?>
<?php foreach ($custom_slider_contents as $custom_slider_content) { 
      $custom_slider_content = explode('|', $custom_slider_content); $slide_custom_thumb = $custom_slider_content[0];
      $slide_custom_permalink = $custom_slider_content[1];
      $slide_custom_title = $custom_slider_content[2];?>
    <?php if(!empty($custom_slider_content[0])) { ?>
            <div class="item" style="background-image:url('<?php echo $slide_custom_thumb; ?>')"> 
	        <div class="content">
	            <h2 class="title"><?php echo $slide_custom_title; ?></h2>
	          </div>
	        <a class="permalink" href="<?php echo $slide_custom_permalink; ?>" title="<?php echo $slide_custom_title; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>></a>
	    </div><?php } ?><?php } ?>
</section>
<?php load_mod('mod.HomePopular'); ?>
</section>
</div>