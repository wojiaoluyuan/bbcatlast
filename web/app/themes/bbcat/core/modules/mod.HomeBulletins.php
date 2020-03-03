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
<?php if(!tt_get_option('tt_enable_homepage_bulletins', true)) return; ?>
<?php $now_stamp = time(); $close_time = isset($_COOKIE['tt_close_bulletins']) ? intval($_COOKIE['tt_close_bulletins']) : 0; if($now_stamp - $close_time < 3600*24) return; ?>
<?php $vm = HomeBulletinsVM::getInstance(); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- Bulletins cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $data = $vm->modelData; $count = $data->count; $bulletins = $data->bulletins; ?>
<?php if($count > 0 && $bulletins) { ?>
<section class="top-bulletins wow bounceInDown" id="topBulletins">
    <div class="container inner">
        <i class="tico tico-bullhorn2"></i>
        <div id="bulletins-scroll-zone">
            <ul>
            <?php foreach ($bulletins as $bulletin) { ?>
                <li class="bulletin">
                    <a href="<?php echo $bulletin['permalink']; ?>" target="_blank"><?php printf('<span>[%1$s]</span> %2$s', $bulletin['modified'], $bulletin['title']); ?></a>
                </li>
            <?php } ?>
            </ul>
        </div>
    </div>
    <span class="act_close" data-toggle="close" data-target="#topBulletins"><i></i><i></i></span>
</section>
<?php } ?>