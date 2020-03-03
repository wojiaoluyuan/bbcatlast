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
<!-- 返回顶部等固定按钮 -->
<div id="fix-controls" class="wow bounceInRight">
    <a class="scroll-to scroll-top" href="javascript:" data-tooltip="<?php _e('Scroll to top', 'tt'); ?>"><i class="tico tico-arrow-up2"></i></a>
    <?php if (tt_get_option('tt_enable_k_fdgj', true)) { ?>
    <!-- 右边浮窗扩展按钮注释开始 -->
    <?php if($qq = tt_get_option('tt_site_qq')) { ?>
    <a class="scroll-to scroll-comment" href="<?php echo 'https://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" data-tooltip="QQ在线" target="_blank"><i class="tico tico-qq"></i></a>
    <?php } ?>
    <a class="scroll-to scroll-comment" href="/shop" data-tooltip="在线商城"><i class="tico tico-shopping-cart"></i></a>
    <a class="scroll-to scroll-search" href="javascript:void(0)" data-toggle="modal" data-target="#globalSearch" data-backdrop="1" data-tooltip="搜索"><i class="tico tico-search"></i></a>
    <!-- 右边浮窗扩展按钮注释结束 -->
    <?php } ?>
    <a class="scroll-to scroll-bottom" href="javascript:" data-tooltip="<?php _e('Scroll to bottom', 'tt'); ?>"><i class="tico tico-arrow-down2"></i></a>
</div>