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
<?php if(!tt_get_option('tt_enable_homepage_bulletins', true)) return; ?>
<!-- 顶部公告 -->
<?php $vm = HomeBulletinsVM::getInstance(); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- Bulletins cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $data = $vm->modelData; $count = $data->count; $bulletins = $data->bulletins; ?>
<?php if($count > 0 && $bulletins) { ?>
<?php $fullSlide = tt_get_option('tt_k_custom_slide');$paged = get_query_var('paged'); ?>
<section id="home-information" <?php if($fullSlide != 'max_big') { echo ' class="container information-big"';}?>>
  <div class="information-bar<?php if($fullSlide != 'max_big') { echo ' bar-big';}?>">
    <div class="slide-container">
      <ul class="js-slide-list">
        <?php foreach ($bulletins as $bulletin) { ?>
        <li class="information-bar__inner bulletin">
          <div class="information-bar__text">
            <span class="information-baricon"><a href="<?php echo $bulletin['permalink']; ?>" target="_blank" rel="nofollow"><i class="tico tico-bullhorn2"></i><span>[<?php echo $bulletin['modified']; ?>]</span><?php echo $bulletin['title']; ?></a></span> 
            </div>
          </li>
      <?php } ?>
      </ul> </div> </div></section>
      <script type="text/javascript">var doscroll = function() { var $parent = $('.js-slide-list'); var $first = $parent.find('li:first'); var height = $first.height(); $first.animate({ marginTop: -height + 'px' }, 500, function() { $first.css('marginTop', 0).appendTo($parent); });};setInterval(function() { doscroll()}, 10000);</script>
<?php } ?>