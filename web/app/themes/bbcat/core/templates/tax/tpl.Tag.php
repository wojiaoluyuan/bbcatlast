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
<?php $paged = get_query_var('paged') ? : 1; ?>
    <div id="content" class="wrapper">
        <?php $vm = TagPostsVM::getInstance($paged); ?>
        <?php if($vm->isCache && $vm->cacheTime) { ?>
            <!-- Tag posts cached <?php echo $vm->cacheTime; ?> -->
        <?php } ?>
        <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $tag = $data->tag; $tag_posts = $data->tag_posts; ?>
         <?php if (tt_get_option('tt_enable_k_fbsbt', true)) { ?>
            <!-- 标签名及介绍信息 -->
            <section class="billboard tag-header">
                <div class="container text-center">
                    <h1><i class="tico tico-price-tag"></i><?php echo $tag['name']; ?></h1>
                    <?php if($tag['description'] != ''){ ?><p><?php echo $tag['description']; ?></p><?php } ?>
                </div>
            </section>
      <?php } ?>
            <!-- 标签文章 -->
            <section class="container archive-posts category-posts">
                <div class="row loop-grid posts-loop-grid mt20 mb20 clearfix">
                    <?php foreach ($tag_posts as $tag_post) { ?>
                        <div class="col-md-3">
                            <article id="<?php echo 'post-' . $tag_post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $tag_post['format']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <a href="<?php echo $tag_post['permalink']; ?>"><img width="250" height="170" src="<?php echo $tag_post['thumb']; ?>" class="thumb-medium wp-post-image fadeIn" alt="<?php echo $tag_post['title']; ?>"></a>
                                    <?php echo $tag_post['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title h4"><a href="<?php echo $tag_post['permalink']; ?>" rel="bookmark"><?php echo $tag_post['title']; ?></a></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="author vcard"><a class="url" href="<?php echo $tag_post['author_url']; ?>"><?php echo $tag_post['author']; ?></a></span>
                                            <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $tag_post['datetime']; ?>" title="<?php echo $tag_post['datetime']; ?>"><?php echo $tag_post['timediff']; ?></time></span>
                                            <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $tag_post['permalink'] . '#respond'; ?>"><?php echo $tag_post['comment_count']; ?></a></span>
                                            <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $tag_post['views']; ?></span>
                                            <span class="likes-link text-muted pull-right mr10"><i class="tico tico-favorite"></i><a href="javascript:void(0)"><?php echo $tag_post['star_count']; ?></a></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $tag_post['excerpt']; ?></div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                </div>

                <?php if($pagination_args['max_num_pages'] > $paged) { ?>
                    <!--        <div class="row pagination-wrap clearfix">-->
                    <!--            <nav aria-label="Page navigation">-->
                    <!--                <ul class="pagination">-->
                    <!--                    --><?php //$pagination = paginate_links(array(
//                        'base' => $pagination_args['base'],
//                        'format' => '?paged=%#%',
//                        'current' => $pagination_args['current_page'],
//                        'total' => $pagination_args['max_num_pages'],
//                        'type' => 'array',
//                        'prev_next' => true,
//                        'prev_text' => '<i class="tico tico-angle-left"></i>',
//                        'next_text' => '<i class="tico tico-angle-right"></i>'
//                    )); ?>
                    <!--                    --><?php //foreach ($pagination as $page_item) {
//                        echo '<li class="page-item">' . $page_item . '</li>';
//                    } ?>
                    <!--                </ul>-->
                    <!--            </nav>-->
                    <!--        </div>-->
                <?php } ?>
                <?php if($pagination_args['max_num_pages'] > 1) { ?>
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                <?php } ?>
            </section>
        <?php } ?>
    </div>
<?php tt_get_footer(); ?>