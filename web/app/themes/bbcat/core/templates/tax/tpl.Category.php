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
<?php tt_get_header(); ?>
<?php
    $cat_id = get_queried_object_id();
    $alt_tpl_cats = tt_get_option('tt_alt_template_cats', array());
    if (isset($alt_tpl_cats[$cat_id]) && $alt_tpl_cats[$cat_id]) {
        load_mod('mod.Category.Blocks');
    } else {
        load_mod('mod.Category.Normal');
    }
?>
<?php tt_get_footer(); ?>