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
<?php tt_get_header(); ?>
<div id="content" class="wrapper">
    <?php load_mod('uc/uc.TopPane'); ?>
    <!-- 主要内容区 -->
    <section class="container author-area">
        <div class="inner">
            <?php load_mod('uc/uc.NavTabs'); ?>
            <?php load_mod('uc/uc.Tab.Comments'); ?>
        </div>
    </section>
</div>
<?php tt_get_footer(); ?>