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
<div id="content" class="wrapper container full-page">
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 页面内容 -->
        <div id="main" class="main primary post-box" role="main">
            <div class="page">
                <div class="single-header">
                    <div class="header-wrap">
                        <h1 class="h2"><?php _e('Privacy Policies and Terms', 'tt'); ?></h1>
                        <div class="header-meta">
                            <span class="meta-date"><?php _e('Post on: ', 'tt'); ?><time class="entry-date"><?php echo tt_get_option('tt_site_open_date'); ?></time></span>
                            <span class="separator" role="separator"> · </span>
                            <span class="meta-date"><?php _e('Modified on: ', 'tt'); ?><time class="entry-date"><?php echo tt_get_option('tt_site_open_date'); ?></time></span>
                        </div>
                    </div>
                </div>
                <div class="single-body">
                    <article class="single-article">
                        <?php load_mod('mod.Privacy'); ?>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>
<?php tt_get_footer(); ?>