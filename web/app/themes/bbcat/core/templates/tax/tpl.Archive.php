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
        <?php $vm = TermPostsVM::getInstance($paged); ?>
        <?php if($vm->isCache && $vm->cacheTime) { ?>
            <!-- Archive posts cached <?php echo $vm->cacheTime; ?> -->
        <?php } ?>
        <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $term = $data->term; $term_posts = $data->term_posts; ?>
          <?php if (tt_get_option('tt_enable_k_fbsbt', true)) { ?>
            <!-- 归档名及介绍信息(自定义分类等Term) -->
            <section class="billboard term-header">
                <div class="container text-center">
                    <h1><i class="tico tico-price-term"></i><?php echo $term['name']; ?></h1>
                    <?php if($term['description'] != ''){ ?><p><?php echo $term['description']; ?></p><?php } ?>
                </div>
            </section>
      <?php } ?>
            <!-- 归档文章 -->
            <section class="container archive-posts category-posts">
                <div class="row loop-grid posts-loop-grid mt20 mb20 clearfix">
                    <?php foreach ($term_posts as $term_post) { ?>
                        <div class="col-md-3">
                            <article id="<?php echo 'post-' . $term_post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $term_post['format']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <a href="<?php echo $term_post['permalink']; ?>"><img width="250" height="170" src="<?php echo $term_post['thumb']; ?>" class="thumb-medium wp-post-image fadeIn" alt="<?php echo $term_post['title']; ?>"></a>
                                    <?php echo $term_post['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title h4"><a href="<?php echo $term_post['permalink']; ?>" rel="bookmark"><?php echo $term_post['title']; ?></a></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="author vcard"><a class="url" href="<?php echo $term_post['author_url']; ?>"><?php echo $term_post['author']; ?></a></span>
                                            <span class="entry-date text-muted"><time class="entry-date" datetime="<?php echo $term_post['datetime']; ?>" title="<?php echo $term_post['datetime']; ?>"><?php echo $term_post['timediff']; ?></time></span>
                                            <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $term_post['permalink'] . '#respond'; ?>"><?php echo $term_post['comment_count']; ?></a></span>
                                            <span class="likes-link text-muted pull-right mr10"><i class="tico tico-favorite"></i><a href="javascript:void(0)"><?php echo $term_post['star_count']; ?></a></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $term_post['excerpt']; ?></div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                </div>
                <?php if($pagination_args['max_num_pages'] > 1) { ?>
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                <?php } ?>
            </section>
        <?php } ?>
    </div>
<?php tt_get_footer(); ?>