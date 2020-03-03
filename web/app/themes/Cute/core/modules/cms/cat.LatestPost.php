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
<?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>
    <?php $vm = HomeLatestVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Latest posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
<div id="latest-posts" class="block5">
        <aside class="block5-widget">
            <?php if(!$paged || $paged===1) { ?>
            <h2 class="widget-title cat-container wow bounceInUp"><?php _e('Latest Posts', 'tt'); ?></h2>
            <?php } ?>
            <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $latest_posts = $data->latest_posts; ?>
            <div class="block5_widget_content block5_list loop-rows posts-loop-rows">
              <?php foreach ($latest_posts as $latest_post) { ?>
                <?php if (tt_get_option('tt_enable_k_postlistnews', true)) { ?>
                <article id="<?php echo 'post-' . $latest_post['ID']; ?>" class="post type-post status-publish wow bounceInUp <?php echo 'format-' . $latest_post['format'] . ' ' . $latest_post['sticky_class']; ?>">
                    <div class="entry-thumb hover-scale">
                        <a href="<?php echo $latest_post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $latest_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $latest_post['title']; ?>"></a>
<!--                        <span class="shadow"></span>-->
                        <!--a class="entry-category" href="">XXX</a-->
                        </div>
                    <div class="entry-detail">
                        <header class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php echo $latest_post['permalink']; ?>" rel="bookmark"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['title']; ?></a>
                                <?php if($latest_post['sticky_class'] == 'sticky') { ?>
                                <img class="sticky-ico" src="<?php echo THEME_ASSET . '/img/sticky.png'; ?>" title="<?php _e('Sticky Post', 'tt'); ?>" >
                                <?php } ?>
                            </h2>
                        </header>
                        <div class="entry-excerpt">
                            <div class="post-excerpt"><?php echo $latest_post['excerpt']; ?></div>
                        </div>
                        <div class="entry-tags"><?php echo $latest_post['tags']; ?></div>
                    </div>
                  <div class="entry-meta entry-meta-1">
                                <span class="author vcard"><i class="tico tico-user"></i><a class="url" href="<?php echo $latest_post['author_url']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['author']; ?></a></span>
                                <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $latest_post['datetime']; ?>" title="<?php echo $latest_post['datetime']; ?>"><?php echo $latest_post['time']; ?></time></span>
                                <span class="views-count text-muted"><i class="tico tico-folder-open-o"></i><?php echo $latest_post['category']; ?></span>
                                <span class="views-count text-muted"><i class="tico tico-eye"></i><?php echo $latest_post['views']; ?></span>
                                <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $latest_post['permalink'] . '#respond'; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['comment_count']; ?></a></span>
                                <span class="read-more"><a href="<?php echo $latest_post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>阅读全文<i class="tico tico-sign-in"></i></a></span>
                            </div>
                </article>
                <?php }else{ ?>
                <article id="<?php echo 'post-' . $latest_post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $latest_post['format'] . ' ' . $latest_post['sticky_class']; ?>">
                    <div class="entry-thumb hover-scale">
                        <a href="<?php echo $latest_post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $latest_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $latest_post['title']; ?>" style="max-height: 175px;"></a>
<!--                        <span class="shadow"></span>-->
                        <!--a class="entry-category" href="">XXX</a-->
                        <?php echo $latest_post['category']; ?>
                    </div>
                    <div class="entry-detail">
                        <header class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php echo $latest_post['permalink']; ?>" rel="bookmark"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['title']; ?></a>
                                <?php if($latest_post['sticky_class'] == 'sticky') { ?>
                                <img class="sticky-ico" src="<?php echo THEME_ASSET . '/img/sticky.png'; ?>" title="<?php _e('Sticky Post', 'tt'); ?>" >
                                <?php } ?>
                            </h2>
                            <div class="entry-meta entry-meta-1">
                                <span class="author vcard"><a class="url" href="<?php echo $latest_post['author_url']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['author']; ?></a></span>
                                <span class="entry-date text-muted"><time class="entry-date" datetime="<?php echo $latest_post['datetime']; ?>" title="<?php echo $latest_post['datetime']; ?>"><?php echo $latest_post['time']; ?></time></span>
                                <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $latest_post['permalink'] . '#respond'; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $latest_post['comment_count']; ?></a></span>
                            </div>
                        </header>
                        <div class="entry-excerpt">
                            <div class="post-excerpt"><?php echo $latest_post['excerpt']; ?></div>
                        </div>
                        <div class="entry-tags"><?php echo $latest_post['tags']; ?></div>
                    </div>
                </article>
                <?php } ?>
                <?php } ?>
            </div>

            <?php if($pagination_args['max_num_pages'] > 1) { ?>
            <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
            <?php } ?>
            <?php } ?>
        </aside>
    </div>