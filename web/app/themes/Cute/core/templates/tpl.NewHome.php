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
    if(tt_get_option('post_item_is_sidebar', true)) { 
        $col_mod_num = 'col-md-8';
        $card_col_mod_num = 'col-md-4 col-sm-6 col-xs-6';
    }else{
        $col_mod_num = 'col-md-12';
        $card_col_mod_num = 'col-md-3 col-sm-4 col-xs-6';
    }
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
<!--shop List item./-->
<?php $paged = get_query_var('paged'); if((!$paged || $paged===1) ) { ?>
<?php if(tt_get_option('tt_home_products_recommendation', false)) { ?>
<section id="home-postmode" class="wrapper container">
    <div class="section-info">
        <h2 class="postmodettitle"><?php echo tt_get_option('tt_home_products_title');?></h2>
        <div class="postmode-description"><?php echo tt_get_option('tt_home_products_desc');?></div>
    </div>
    
    <?php load_mod('mod.ProductGallery', true); ?>

</section>
<?php } ?>
<?php } ?>
<!-- home features./ -->
<?php if(tt_get_option('tt_home_features', false) && (!$paged || $paged===1)) { ?>
<section id="home-features" class="wrapper widget_woothemes_features"> 
    <div class="features container">
        <div class="feature first">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_1');?>" title="主题定制"><?php echo tt_get_option('feature_title_1');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_1');?></div>
        </div>
        <div class="feature">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_2');?>" title="主题定制"><?php echo tt_get_option('feature_title_2');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_2');?></div>
        </div>
        <div class="feature last">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_3');?>" title="主题定制"><?php echo tt_get_option('feature_title_3');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_3');?></div>
        </div>
        <div class="fix"></div>
        <?php if(tt_get_option('tt_home_features_num') > 3) { ?>
        <div class="feature first">
           <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_4');?>" title="主题定制"><?php echo tt_get_option('feature_title_4');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_4');?></div>
        </div>
        <div class="feature">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_5');?>" title="主题定制"><?php echo tt_get_option('feature_title_5');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_5');?></div>
        </div>
        <div class="feature last">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_6');?>" title="主题定制"><?php echo tt_get_option('feature_title_6');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_6');?></div>
        </div>
        <div class="fix"></div>
        <?php } ?>
        <?php if(tt_get_option('tt_home_features_num') > 6) { ?>
        <div class="feature first">
           <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_7');?>" title="主题定制"><?php echo tt_get_option('feature_title_7');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_7');?></div>
        </div>
        <div class="feature">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_8');?>" title="主题定制"><?php echo tt_get_option('feature_title_8');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_8');?></div>
        </div>
        <div class="feature last">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_9');?>" title="主题定制"><?php echo tt_get_option('feature_title_9');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_9');?></div>
        </div>
        <div class="fix"></div>
        <?php } ?>
    </div>
</section>
<?php } ?>
<div id="content" class="wrapper container right-aside">
    <?php load_mod(('banners/bn.Top')); ?>
    <!-- 推荐商品 -->
    <!-- 分类模块与边栏 -->
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
      <div class="<?php echo $col_mod_num; ?>">
        <div id="postcard-main" class="main primary" role="main">
          <?php $paged = get_query_var('paged'); if((!$paged || $paged===1)) { ?>
            <!-- 置顶文章 -->
          <?php if(tt_get_option('tt_enable_sticky_cats')) { ?>
          <?php $vm = StickysVM::getInstance(4); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- CMS stickies cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $data = $vm->modelData; ?>
<?php if(is_array($data->sticky_posts) && count($data->sticky_posts) > 0) { ?>
<?php $stickies = $data->sticky_posts; ?>
  <div class="cms-stickies block5 wow bounceInUp clearfix">
  <div class="section-info"> <h2 class="postmodettitle"><?php echo tt_get_option('tt_sticky_cats_title');?></h2> <div class="postmode-description"><?php echo tt_get_option('tt_sticky_cats_description');?></div> </div>
  <div class="block5_list loop-rows posts-loop-rows row">
                <?php foreach ($stickies as $sticky) { ?>
                <div class="<?php echo $card_col_mod_num; ?>">
                  <article id="<?php echo 'post-' . $sticky['ID']; ?>" class="post type-post status-publish wow bounceInUp">
                    <div class="entry-thumb hover-scale">
                      <a href="<?php echo $sticky['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>
                        <img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $sticky['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $sticky['title']; ?>" style="display: block;"> </a>
                      <?php echo $sticky['category']; ?>
                      <?php if($sticky['price_text'] == '免费资源'){ ?>
                          <span class="post-free"><i class="fa fa-ticket"></i> <?php echo $sticky['price_text']; ?></span>
                           <?php }elseif(!empty($sticky['price_text'])){ ?>
                           <span class="post-price"><i class="fa fa-ticket"></i> <?php echo $sticky['price_text']; ?></span>
                    <?php } ?>
                    </div> <div class="entry-detail"> <header class="entry-header"> <h2 class="entry-title h4"> <a href="<?php echo $sticky['permalink']; ?>" rel="bookmark" target="_blank" title="<?php echo $sticky['title']; ?>">
                    <?php echo $sticky['title']; ?></a> </h2> <div class="entry-meta entry-meta-1">
                    <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $sticky['datetime']; ?>"><?php echo $sticky['time']; ?></time>
                    </span> <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $sticky['permalink']; ?>#respond" target="_blank"><?php echo $sticky['comment_count']; ?></a></span> 
                    <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $sticky['views']; ?></span> </div> </header> </div> </article> </div>

                <?php } ?>
</div>
    </div>
<?php } ?>
          <?php } ?>

      <!-- 分类模块列表 -->
      <?php $tt_cms_home_show_cats_num = tt_get_option('tt_cms_home_show_cats_num'); ?>
      <?php $vm = HomeCMSCatsVM::getInstance($tt_cms_home_show_cats_num); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- CMS posts cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php if(!$paged || $paged===1) { ?>
        <?php if($data = $vm->modelData) { ?>
        <?php $cms = $data->cms; 
            global $cat_data;
            foreach ($cms as $cms_cat) { $cat_data = $cms_cat; ?>
                    <div class="cms-stickies block5 wow bounceInUp clearfix">
                      <div class="section-info"> <h2 class="postmodettitle"><?php echo $cms_cat->cat_name;?></h2> <div class="postmode-description"><?php echo $cms_cat->description; ?><a href="<?php echo $cms_cat->cat_link;?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?> class="more"><?php _e('更多<i class="tico tico-angle-right"></i>', 'tt'); ?></a></div> </div>
                        <div class="block5_list loop-rows posts-loop-rows row">
                          <?php $posts = $cat_data->posts;
    foreach ($posts as $post) {
            ?><div class="<?php echo $card_col_mod_num; ?>"> <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish wow bounceInUp"> <div class="entry-thumb hover-scale"> <a href="<?php echo $post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>> <img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $post['title']; ?>" style="display: block;"> </a> <?php echo $post['category']; ?><?php if($post['price_text'] == '免费资源'){ ?>
                          <span class="post-free"><i class="fa fa-ticket"></i> <?php echo $post['price_text']; ?></span>
                           <?php }elseif(!empty($post['price_text'])){ ?>
                           <span class="post-price"><i class="fa fa-ticket"></i> <?php echo $post['price_text']; ?></span>
                    <?php } ?></div> <div class="entry-detail"> <header class="entry-header"> <h2 class="entry-title h4"> <a href="<?php echo $post['permalink']; ?>" rel="bookmark" target="_blank" title="<?php echo $post['title']; ?>"><?php echo $post['title']; ?></a> </h2> <div class="entry-meta entry-meta-1"> <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $post['datetime']; ?>"><?php echo $post['datetime']; ?></time></span> <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $post['permalink'] . '#respond'; ?>#respond" target="_blank"><?php echo $post['comment_count']; ?></a></span> <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $post['views']; ?></span> </div> </header> </div> </article> </div>
<?php
    }
    ?>
                          </div>
                    </div>
                    <!-- AD -->
<!--                    --><?php //if($cms_cat->index == 1){ ?>
<!--                        <div id="loopad" class="container banner">-->
<!--                            --><?php //echo tt_get_option('cmswithsidebar_loop_ad'); ?>
<!--                        </div>-->
<!--                    --><?php //}?>
      
            <?php } ?>
        <?php } ?>
	<?php } ?>
          <?php } ?>
  </div>
          
      <?php load_mod('mod.HomeLatest'); ?>
        </div>
<?php if(tt_get_option('post_item_is_sidebar', true)) { ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
        <?php } ?>
    </section>
    <?php load_mod(('banners/bn.Bottom')); ?>
</div>
<?php tt_get_footer(); ?>