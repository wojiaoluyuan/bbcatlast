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
<?php
    $cat_id = get_queried_object_id();
    $alt_tpl_cats = tt_get_option('tt_alt_template_cats', array());
    if (isset($alt_tpl_cats[$cat_id]) && $alt_tpl_cats[$cat_id]) {
        load_mod('mod.Category.List');
    } else {
        load_mod('mod.Category.Card');
    }
?>
<?php tt_get_footer(); ?>