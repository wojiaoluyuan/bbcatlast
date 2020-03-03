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
<?php if(tt_get_option('tt_enable_post_content_top_banner', false)) { ?>
    <section class="ttgg" id="ttgg-6">
        <div class="tg-inner">
            <?php echo tt_get_option('tt_post_content_top_banner'); ?>
        </div>
    </section>
<?php } ?>