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
<!-- SideBar Begin -->
<aside class="sidebar secondary col-md-4" id="sidebar">
    <?php if(is_single() && tt_get_option('tt_enable_k_author_widget')) the_widget('AuthorWidget'); ?>
    <?php $post_embed_down_info = maybe_unserialize(get_post_meta(get_queried_object_id(), 'tt_embed_down_info', true));$option = $post_embed_down_info[0];if(is_single() && $option=='1') the_widget('DownWidget'); ?>
    <?php dynamic_sidebar(tt_dynamic_sidebar()); ?>
</aside>