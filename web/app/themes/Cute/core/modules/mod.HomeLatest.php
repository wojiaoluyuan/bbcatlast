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
<?php if(tt_get_option('tt_home_postlist_is')): ?>
<?php $paged = get_query_var('paged'); if((!$paged || $paged===1) ) : ?>
    <div class="section-info">
        <h2 class="postmodettitle"><?php echo tt_get_option('tt_home_postlist_title');?></h2>
        <div class="postmode-description"><?php echo tt_get_option('tt_home_postlist_desc');?></div>
    </div>
    <?php endif; ?> 
<?php if(tt_get_option('post_item_style', style_0) == 'style_0') { ?>

    <?php load_mod('mod.HomeLatestList'); ?>

<?php } else { ?>

    <?php load_mod('mod.HomeLatestCard'); ?>

<?php } ?>
<?php endif; ?> 