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
global $productdata;
//TODO 判断是否购买用户,方可评论
?>
<div class="submit-box comment-form clearfix" id="comment-form">
    <?php //comment_id_fields(); ?>
    <input type="hidden" name="comment_post_ID" value="<?php echo $productdata->ID; ?>" id="comment_post_ID">
    <input type="hidden" name="comment_parent" id="comment_parent" value="0">
    <input type="hidden" name="tt_comment_nonce" id="comment_nonce" value="<?php echo wp_create_nonce('tt_comment_nonce'); ?>">
    <?php do_action('comment_form', $productdata->ID); ?>
    <div class="rating-radios">
        <span><?php _e('RATING','tt'); ?></span>
        <label class="radio rating-radio">
            <input type="radio" name="product_star_rating" value="1">
            <span class="tico-star one-star"></span>
        </label>
        <label class="radio rating-radio">
            <input type="radio" name="product_star_rating" value="2">
            <span class="tico-star two-star"></span>
        </label>
        <label class="radio rating-radio">
            <input type="radio" name="product_star_rating" value="3">
            <span class="tico-star three-star"></span>
        </label>
        <label class="radio rating-radio">
            <input type="radio" name="product_star_rating" value="4">
            <span class="tico-star four-star"></span>
        </label>
        <label class="radio rating-radio">
            <input type="radio" name="product_star_rating" value="5" checked>
            <span class="tico-star five-star"></span>
        </label>
    </div>

    <div class="text">
        <?php if(is_user_logged_in()) { ?>
            <textarea name="comment" placeholder="<?php _e('Leave some words...', 'tt'); ?>" id="comment-text" required></textarea>
        <?php }else{ ?>
            <textarea name="comment" placeholder="<?php _e('Signin and Leave some words...', 'tt'); ?>" id="comment-text" required></textarea>
        <?php } ?>
    </div>
    <?php if(is_user_logged_in()) { ?>
        <button class="btn btn-info comment-submit" id="submit" type="submit" title="<?php _e('Submit', 'tt'); ?>"><?php _e('Submit', 'tt'); ?></button>
        <div class="err text-danger"></div>
    <?php }else{ ?>
        <button class="btn btn-success comment-submit" id="submit" type="submit" title="<?php _e('Submit', 'tt'); ?>" disabled><?php _e('Submit', 'tt'); ?></button>
    <?php } ?>
</div>