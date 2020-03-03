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
<?php $paged = get_query_var('paged') ? : 1; ?>
<div id="content" class="wrapper right-aside">
    <?php $vm = CategoryPostsVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Category posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $category = $data->category; $category_posts = $data->category_posts; ?>
     <?php if (tt_get_option('tt_enable_k_fbsbt', true)) { ?>
    <!-- 分类名及介绍信息 -->
    <section class="billboard category-header">
        <div class="container text-center">
            <h1><?php echo $category['cat_name']; ?></h1>
            <?php if($category['description'] != ''){ ?><p><?php echo $category['description']; ?></p><?php } ?>
        </div>
    </section>
    <?php } ?>
    <?php if (tt_get_option('tt_enable_k_postlistnews', true)) { ?>
    <section id="mod-insideContent" class="main-wrap container content-section clearfix">
        <!-- 分类文章列表 -->
        <div id="main" class="main primary col-md-8" role="main">
                <!-- 分类文章 -->
                <section class="category-posts loop-rows posts-loop-rows">
                    <?php foreach ($category_posts as $category_post) { ?>
                        <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="post type-post status-publish wow bounceInUp <?php echo 'format-' . $category_post['format'] . ' ' . $category_post['sticky_class']; ?>">
                            <div class="entry-thumb hover-scale">
                                <a href="<?php echo $category_post['permalink']; ?>"><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $category_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $category_post['title']; ?>"></a>
                              </div>
                            <div class="entry-detail">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php echo $category_post['permalink']; ?>" rel="bookmark"><?php echo $category_post['title']; ?></a>
                                    </h2>
                                </header>
                                <div class="entry-excerpt">
                                    <div class="post-excerpt"><?php echo $category_post['excerpt']; ?></div>
                                </div>
                              <div class="entry-tags"><?php echo $category_post['tags']; ?></div>
                            </div>
                          <div class="entry-meta entry-meta-1">
                                        <span class="author vcard"><i class="tico tico-user"></i><a class="url" href="<?php echo $category_post['author_url']; ?>"><?php echo $category_post['author']; ?></a></span>
                                        <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $category_post['datetime']; ?>" title="<?php echo $category_post['datetime']; ?>"><?php echo $category_post['time']; ?></time></span>
                                        <span class="views-count text-muted"><i class="tico tico-folder-open-o"></i><?php echo $category_post['category']; ?></span>
                                        <span class="views-count text-muted"><i class="tico tico-eye"></i><?php echo $category_post['views']; ?></span>
                                        <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $category_post['permalink'] . '#respond'; ?>"><?php echo $category_post['comment_count']; ?></a></span>
                                        <span class="read-more"><a href="<?php echo $category_post['permalink']; ?>">阅读全文<i class="tico tico-sign-in"></i></a></span>
                                    </div>
                        </article>
                    <?php } ?>
                </section>
                <?php }else{ ?>
                <section id="mod-insideContent" class="main-wrap container content-section clearfix">
        <!-- 分类文章列表 -->
        <div id="main" class="main primary col-md-8" role="main">
                <!-- 分类文章 -->
                <section class="category-posts loop-rows posts-loop-rows">
                    <?php foreach ($category_posts as $category_post) { ?>
                        <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $category_post['format'] . ' ' . $category_post['sticky_class']; ?>">
                            <div class="entry-thumb hover-scale">
                                <a href="<?php echo $category_post['permalink']; ?>"><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $category_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $category_post['title']; ?>" style="max-height: 175px;"></a>
                                <?php echo $category_post['category']; ?>
                            </div>
                            <div class="entry-detail">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php echo $category_post['permalink']; ?>" rel="bookmark"><?php echo $category_post['title']; ?></a>
                                    </h2>
                                    <div class="entry-meta entry-meta-1">
                                        <span class="author vcard"><a class="url" href="<?php echo $category_post['author_url']; ?>"><?php echo $category_post['author']; ?></a></span>
                                        <span class="entry-date text-muted"><time class="entry-date" datetime="<?php echo $category_post['datetime']; ?>" title="<?php echo $category_post['datetime']; ?>"><?php echo $category_post['time']; ?></time></span>
                                        <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $category_post['permalink'] . '#respond'; ?>"><?php echo $category_post['comment_count']; ?></a></span>
                                    </div>
                                </header>
                                <div class="entry-excerpt">
                                    <div class="post-excerpt"><?php echo $category_post['excerpt']; ?></div>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                </section>
                <?php } ?>
                <?php if($pagination_args['max_num_pages'] > 1) { ?>
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                <?php } ?>
        </div>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
    </section>
    <?php } ?>
</div>