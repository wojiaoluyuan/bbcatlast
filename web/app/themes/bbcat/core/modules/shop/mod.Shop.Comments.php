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
<?php
global $productdata;
$vm = ProductCommentsVM::getInstance($productdata->ID);
if($vm->isCache && $vm->cacheTime) { ?>
    <!-- Comments cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $comment_list = $vm->modelData->list_html; ?>
<div id="comments-wrap">
    <ul class="comments-list">
        <input type="hidden" id="comment_star_nonce" name="tt_comment_star_nonce" value="<?php echo wp_create_nonce('tt_comment_star_nonce'); ?>">
        <?php echo $comment_list; ?>
        <div class="pages"><?php //paginate_comments_links('prev_text=«&next_text=»&type=list'); ?></div>
    </ul>
    <?php if($vm->modelData->list_count > 0){ ?>
        <div class="load-more"><button class="btn btn-primary btn-wide btn-more"><?php _e('Load More Comments', 'tt'); ?></button></div>
    <?php } ?>
    <div class="err text-primary text-center h3"></div>
</div>