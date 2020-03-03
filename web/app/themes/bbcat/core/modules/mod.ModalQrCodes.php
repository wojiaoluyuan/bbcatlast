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
<!-- 模态二维码框 -->
<div id="siteQrcodes" class="js-qrcode qrcode-modal fadeZoomIn" role="dialog" aria-hidden="true">
    <div class="qr-wrap row">
        <div class="qrcode col-md-6 col-sm-6 col-xs-12">
            <?php if(tt_get_option('tt_site_weixin_qr')) { ?>
                <div class="wx-qr"><img src="<?php echo tt_get_option('tt_site_weixin_qr'); ?>" title="<?php _e('Scan the qrcode image and contact with me', 'tt'); ?>"></div>
            <?php } ?>
        </div>
        <div class="qrcode col-md-6 col-sm-6 col-xs-12">
            <?php if(tt_get_option('tt_site_alipay_qr')) { ?>
                <div class="ali-qr"><img src="<?php echo tt_get_option('tt_site_alipay_qr'); ?>" title="<?php _e('Scan the qrcode image and contact with me', 'tt'); ?>"></div>
            <?php } ?>
        </div>
    </div>
</div>