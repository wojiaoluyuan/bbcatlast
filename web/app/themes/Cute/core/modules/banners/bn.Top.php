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
<?php if(tt_get_option('tt_enable_nav_banner', false)) { ?>
<section class="ttgg" id="ttgg-1">
    <div class="tg-inner">
        <?php echo tt_get_option('tt_nav_banner'); ?>
    </div>
</section>
<?php } ?>