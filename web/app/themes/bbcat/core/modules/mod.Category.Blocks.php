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
<div id="content" class="wrapper">
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
        <!-- 分类文章 -->
        <section class="container archive-posts category-posts">
            <div class="row loop-grid posts-loop-grid mt20 mb20 clearfix">
                <?php foreach ($category_posts as $category_post) { ?>
                    <div class="col-md-3">
                        <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="post type-post status-publish wow bounceInUp <?php echo 'format-' . $category_post['format']; ?>">
                            <div class="entry-thumb hover-scale">
                                <a href="<?php echo $category_post['permalink']; ?>"><img width="250" height="170" src="<?php echo $category_post['thumb']; ?>" class="thumb-medium wp-post-image fadeIn" alt="<?php echo $category_post['title']; ?>"></a>
                                <!--                        <span class="shadow"></span>-->
                                <!--a class="entry-category" href="">XXX</a-->
                                <?php echo $category_post['category']; ?>
                            </div>
                            <div class="entry-detail">
                                <header class="entry-header">
                                    <h2 class="entry-title h4"><a href="<?php echo $category_post['permalink']; ?>" rel="bookmark"><?php echo $category_post['title']; ?></a></h2>
                                    <div class="entry-meta entry-meta-1">
                                        <span class="author vcard"><a class="url" href="<?php echo $category_post['author_url']; ?>"><?php echo $category_post['author']; ?></a></span>
                                        <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $category_post['datetime']; ?>" title="<?php echo $category_post['datetime']; ?>"><?php echo $category_post['timediff']; ?></time></span>
                                        <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $category_post['permalink'] . '#respond'; ?>"><?php echo $category_post['comment_count']; ?></a></span>
                                        <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $category_post['views']; ?></span>
                                        <span class="likes-link text-muted pull-right mr10"><i class="tico tico-favorite"></i><a href="javascript:void(0)"><?php echo $category_post['star_count']; ?></a></span>
                                    </div>
                                </header>
                                <div class="entry-excerpt">
                                    <div class="post-excerpt"><?php echo $category_post['excerpt']; ?></div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php } ?>
            </div>

            <?php if($pagination_args['max_num_pages'] > 1) { ?>
                <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
            <?php } ?>
            <!--        --><?php //if($pagination_args['max_num_pages'] > $paged) { ?>
            <!--            <div class="row load-next clearfix mt30 mb30 text-center">-->
            <!--                <a class="btn btn-danger btn-wide btn-next" title="--><?php //_e('LOAD NEXT', 'tt'); ?><!--" data-component="loadNext" data-next-page="--><?php //echo $paged + 1; ?><!--" data-next-page-url="--><?php //echo $pagination_args['next']; ?><!--"><i class="tico tico-angle-down"></i></a>-->
            <!--            </div>-->
            <!--        --><?php //} ?>
        </section>
    <?php } ?>
</div>