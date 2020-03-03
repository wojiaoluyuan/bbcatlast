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
<?php global $tt_vars; $data = $tt_vars['data']; ?>
<div id="main" class="main primary col-md-8 search-results" role="main">
    <?php if($data->count > 0) { $search_results = $data->results; $max_pages = $data->max_pages; ?>
    <div class="loop-rows posts-loop-row clearfix">
        <?php foreach ($search_results as $search_result) { ?>
            <article id="<?php echo 'post-' . $search_result['ID']; ?>" class="post type-post status-publish wow bounceInUp <?php echo 'format-' . $search_result['format']; ?>">
                <div class="entry-thumb hover-scale">
                    <a href="<?php echo $search_result['permalink']; ?>"><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $search_result['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $search_result['title']; ?>"></a>
                 </div>
                <div class="entry-detail">
                    <header class="entry-header">
                        <h2 class="entry-title"><a href="<?php echo $search_result['permalink']; ?>" rel="bookmark"><?php echo $search_result['title']; ?></a></h2>
                    </header>
                    <div class="entry-excerpt">
                        <div class="post-excerpt"><?php echo $search_result['excerpt']; ?></div>
                    </div>
                  <div class="entry-tags"><?php echo $search_result['tags']; ?></div>
                </div>
              <div class="entry-meta entry-meta-1">
                            <span class="author vcard"><i class="tico tico-user"></i><a class="url" href="<?php echo $search_result['author_url']; ?>"><?php echo $search_result['author']; ?></a></span>
                            <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $search_result['datetime']; ?>" title="<?php echo $search_result['datetime']; ?>"><?php echo $search_result['time']; ?></time></span>
                            <span class="views-count text-muted"><i class="tico tico-folder-open-o"></i><?php echo $search_result['category']; ?></span>
                            <span class="views-count text-muted"><i class="tico tico-eye"></i><?php echo $search_result['views']; ?></span>
                            <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $search_result['permalink'] . '#respond'; ?>"><?php echo $search_result['comment_count']; ?></a></span>
                           <span class="read-more"><a href="<?php echo $search_result['permalink']; ?>">阅读全文<i class="tico tico-sign-in"></i></a></span>
                        </div>
            </article>
        <?php } ?>
    </div>
    <?php if($max_pages > 1) { ?>
        <div class="pagination-mini clearfix">
            <?php if($tt_vars['page'] == 1) { ?>
                <div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
            <?php }else{ ?>
                <div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
            <?php } ?>
            <div class="col-md-6 page-nums">
                <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_vars['page']); ?></span>
                <span class="separator">/</span>
                <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
            </div>
            <?php if($tt_vars['page'] != $data->max_pages) { ?>
                <div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
            <?php }else{ ?>
                <div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php }else{ ?>
    <div class="empty-results">
        <span class="tico tico-dropbox"></span>
        <p><?php _e('No results matched your search words', 'tt'); ?></p>
    </div>
    <?php } ?>
</div>