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
<?php if(tt_get_option('tt_enable_dl_top_banner', false)) { ?>
    <section class="ttgg row" id="ttgg-10">
        <div class="tg-inner col-md-6">
            <?php echo tt_get_option('tt_dl_top_banner_1'); ?>
        </div>
        <div class="tg-inner col-md-6">
            <?php echo tt_get_option('tt_dl_top_banner_2'); ?>
        </div>
    </section>
<?php } ?>