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