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
<!-- 返回顶部等固定按钮 -->
<div id="fix-controls" class="wow bounceInRight">
    <a class="scroll-to scroll-top" href="javascript:" data-tooltip="<?php _e('Scroll to top', 'tt'); ?>"><i class="tico tico-arrow-up2"></i></a>
	<?php if(is_single() && get_post_type()=='product' ){?>
    <a id="scroll-shop-pay" href="javascript:" data-tooltip="付费内容"><i class="tico tico-paypal"></i></a>
    <a id="scroll-shop-comment" href="javascript:" data-tooltip="评论"><i class="tico tico-comments-o"></i></a>
	<?php }elseif(is_single() && get_post_type()=='post' ){ ?>
    <a id="scroll-comment" href="javascript:" data-tooltip="评论"><i class="tico tico-comments-o"></i></a>
    <?php } ?>
    <?php if($qq = tt_get_option('tt_site_qq')) { ?>
    <a class="scroll-to scroll-comment" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" data-tooltip="QQ在线" target="_blank"><i class="tico tico-qq"></i></a>
    <?php } ?>
    <a class="scroll-to scroll-bottom" href="javascript:" data-tooltip="<?php _e('Scroll to bottom', 'tt'); ?>"><i class="tico tico-arrow-down2"></i></a>
</div>

<script type="text/javascript">
$('#scroll-comment').click(function(){
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $('#respond').offset().top-80}, 400);
    return false;
});
  
$('#scroll-shop-pay').click(function(){
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $('#tab-paycontent').offset().top-80}, 400);
    return false;
});

$('#scroll-shop-comment').click(function(){
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $('#tab-reviews').offset().top-80}, 400);
    return false;
});

    
</script>