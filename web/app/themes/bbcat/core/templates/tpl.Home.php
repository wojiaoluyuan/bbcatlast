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
<?php if (tt_get_option('tt_enable_tinection_home', false) && (!isset($_GET['mod']) || $_GET['mod'] != 'blog')): ?>
    <?php load_tpl('tpl.CmsHome'); ?>
<?php else: ?>
    <?php load_tpl('tpl.NewHome'); ?>
<?php endif; ?>