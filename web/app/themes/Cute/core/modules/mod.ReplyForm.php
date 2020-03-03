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
global $postdata;
foreach($_COOKIE as $value => $key){
  if(strpos( $value,'comment_author_') !== false){
    if(strpos( $value,'comment_author_email_') !== false){
    $author_email=$value;
    }elseif(strpos( $value,'comment_author_url_') !== false){
    $author_url=$value;
    }else{
    $author=$value;
    }
  }  
};
?>
<?php if (tt_get_option('tt_enable_k_ykpl', true)) { ?>
<form action="<?php echo home_url('/wp-comments-post.php'); ?>" method="post" id="commentform">
	<?php if(!is_user_logged_in()) { ?>
		<div id="comment-author-info" class="clearfix">
          <div class="comment-md-3 input-group"><div class="input-group-addon"><i class="tico tico-qq"></i></div>
              <input id="qqinfo" maxlength="12" name="u" type="text" placeholder="输入QQ号码可以快速填写" onblur="bbcat_qq()" value="" tabindex="1"></div>
          <div class="comment-md-3 input-group comment-form-url"><div class="input-group-addon"><i class="tico tico-user"></i></div>
				<input type="text" name="author" id="author" class="comment-md-9" value="<?php if(!empty($_COOKIE[$author])){ echo $_COOKIE[$author];}; ?>" size="22" placeholder="昵称(必填)" tabindex="2"></div>
			<div class="comment-md-3 input-group"><div class="input-group-addon"><i class="tico tico-envelope-o"></i></div>
				<input type="email" name="email" id="email" class="comment-md-9" value="<?php if(!empty($_COOKIE[$author_email])){ echo $_COOKIE[$author_email];}; ?>" size="22" placeholder="邮箱(必填)" tabindex="3"></div>
			<div class="comment-md-3 input-group comment-form-url"><div class="input-group-addon"><i class="tico tico-link"></i></div>
				<input type="text" name="url" id="url" class="comment-md-9" value="<?php if(!empty($_COOKIE[$author_url])){ echo $_COOKIE[$author_url];}; ?>" size="22" placeholder="网址(选填)" tabindex="4"></div>
          </div>
		<?php } ?>
			<div class="comment-from-main submit-box clearfix">
                <div class="comment-tip">
				</div>
				<div class="comment-form-textarea">
					<div class="comment-textarea-box">
						<textarea class="comment-textarea" name="comment" id="comment" placeholder="说点什么吧...（评论内容最少要有一个汉字）" tabindex="4"></textarea>
					</div>
				</div>
				<div class="form-submit">
					<input class="btn-comment pull-right" name="submit" type="submit" id="submit" tabindex="5" title="发表评论" value="发表评论">
					<input type="hidden" name="comment_post_ID" value="<?php echo $postdata->ID; ?>" id="comment_post_ID">
					<input type="hidden" name="comment_parent" id="comment_parent" value="0">
					<input type="hidden" name="tt_comment_nonce" id="comment_nonce" value="<?php echo wp_create_nonce('tt_comment_nonce'); ?>">
					<?php do_action( 'comment_form', $postdata->ID); ?></div></div>
</form>
<script>$("#comment").emoji({ addlocation: '.submit-box',showTab: true, animation: 'fade', basePath: '/wp-content/themes/BBCat/assets/img/emoji', icons: emojiLists});</script>
<?php }else{ ?>
<div class="comment-tip"></div>
<div class="submit-box comment-form clearfix" id="comment-form">
    <?php //comment_id_fields(); ?>
    <input type="hidden" name="comment_post_ID" value="<?php echo $postdata->ID; ?>" id="comment_post_ID">
    <input type="hidden" name="comment_parent" id="comment_parent" value="0">
    <input type="hidden" name="tt_comment_nonce" id="comment_nonce" value="<?php echo wp_create_nonce('tt_comment_nonce'); ?>">
    <?php do_action('comment_form', $postdata->ID); ?>
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
    <script>$("#comment-text").emoji({ addlocation: '.submit-box', showTab: true, animation: 'fade', basePath: '/wp-content/themes/BBCat/assets/img/emoji', icons: emojiLists});</script>
    <style type="text/css">.emoji_btn,.emoji_container{top: 10px!important;}</style>
</div>
<?php } ?>