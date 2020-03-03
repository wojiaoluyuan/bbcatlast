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
        <?php $vm = DateArchivePostsVM::getInstance($paged); ?>
        <?php if($vm->isCache && $vm->cacheTime) { ?>
            <!-- Date archive posts cached <?php echo $vm->cacheTime; ?> -->
        <?php } ?>
        <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $period = $data->period; $date_posts = $data->date_posts; ?>
      <?php if (tt_get_option('tt_enable_k_fbsbt', true)) { ?>
            <!-- 日期归档信息 -->
            <section class="billboard date-archive-header">
                <div class="container text-center">
                    <h1><?php echo $period['str']; ?></h1>
                    <p><?php echo $period['des']; ?></p>
                </div>
            </section>
      <?php } ?>
            <!-- 归档文章 -->
            <section class="container archive-posts date-posts">
                <div class="row loop-grid posts-loop-grid mt20 mb20 clearfix">
                    <?php foreach ($date_posts as $date_post) { ?>
                        <div class="col-md-3">
                            <article id="<?php echo 'post-' . $date_post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $date_post['format']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <a href="<?php echo $date_post['permalink']; ?>"><img width="250" height="170" src="<?php echo $date_post['thumb']; ?>" class="thumb-medium wp-post-image fadeIn" alt="<?php echo $date_post['title']; ?>"></a>
                                    <?php echo $date_post['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title h4"><a href="<?php echo $date_post['permalink']; ?>" rel="bookmark"><?php echo $date_post['title']; ?></a></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="author vcard"><a class="url" href="<?php echo $date_post['author_url']; ?>"><?php echo $date_post['author']; ?></a></span>
                                            <span class="entry-date text-muted"><time class="entry-date" datetime="<?php echo $date_post['datetime']; ?>" title="<?php echo $date_post['datetime']; ?>"><?php echo $date_post['timediff']; ?></time></span>
                                            <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $date_post['permalink'] . '#respond'; ?>"><?php echo $date_post['comment_count']; ?></a></span>
                                            <span class="likes-link text-muted pull-right mr10"><i class="tico tico-favorite"></i><a href="javascript:void(0)"><?php echo $date_post['star_count']; ?></a></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $date_post['excerpt']; ?></div>
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