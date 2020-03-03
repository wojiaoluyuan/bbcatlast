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
<div id="postlist-main" class="main cms-main primary col-md-8" role="main">
    <?php $paged = get_query_var('paged'); if(!$paged || $paged===1) { load_mod('cms/stickies'); } ?>
<?php $vm = HomeCMSCatsVM::getInstance(''); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- CMS posts cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php if(!$paged || $paged===1) { ?>
    <div id="cms-cats" class="block6">
        <?php if($data = $vm->modelData) { ?>
        <?php $cms = $data->cms;
            global $cat_data;
            foreach ($cms as $cms_cat) { $cat_data = $cms_cat; ?>
                <?php if($cms_cat->full){ ?>
                    <section class="block-wrapper wow bounceInUp clearfix">
                        <section class="cat-<?php echo $cms_cat->cat_id;?> cat-col cat-col-full">
                            <div class="cat-container clearfix">
                                <h2 class="home-heading clearfix">
                                <span class="heading-text">
                                    <?php echo $cms_cat->cat_name;?>
                                </span>
                                    <a href="<?php echo $cms_cat->cat_link;?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php _e('更多<i class="tico tico-angle-right"></i>', 'tt'); ?></a>
                                </h2>
                                <?php include(THEME_DIR . '/core/modules/cms/cat.' . $cms_cat->tp . '.php'); ?>
                            </div>
                        </section>
                    </section>
                    <!-- AD -->
<!--                    --><?php //if($cms_cat->index == 1){ ?>
<!--                        <div id="loopad" class="container banner">-->
<!--                            --><?php //echo tt_get_option('cmswithsidebar_loop_ad'); ?>
<!--                        </div>-->
<!--                    --><?php //}?>
                <?php } else { ?>
                <?php if($cms_cat->start_wrap) { ?>
                <section class="block-wrapper clearfix">
                <?php } ?>
                    <section class="cat-<?php echo $cms_cat->cat_id;?> cat-col cat-col-1_2">
                        <div class="cat-container clearfix">
                            <h2 class="home-heading clearfix">
                                <span class="heading-text">
                                    <?php echo $cms_cat->cat_name;?>
                                </span>
                                <a href="<?php echo $cms_cat->cat_link;?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php _e('更多<i class="tico tico-angle-right"></i>', 'tt'); ?></a>
                            </h2>
                            <?php include(THEME_DIR . '/core/modules/cms/cat.' . $cms_cat->tp . '.php'); ?>
                        </div>
                    </section>
                <?php if($cms_cat->end_wrap) { ?>
                </section>
                <?php } ?>
                <?php }?>
            <?php } ?>
        <?php } ?>
    </div>
	<?php } ?>
<?php if (tt_get_option('tt_enable_home_latestpost', true)) { include(THEME_DIR . '/core/modules/cms/cat.LatestPost.php');}; ?>
</div>