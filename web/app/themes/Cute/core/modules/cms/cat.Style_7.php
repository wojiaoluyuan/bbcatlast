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
<div class="cms-cat cms-cat-s7">
    <?php
    global $cat_data;
    $posts = $cat_data->posts;
    $i = 0;
    foreach ($posts as $post) {
        $r = fmod($i, 3);
        $i++;
        if ($i <= 9) {
            ?>
            <div class="<?php printf('col-md-4 col-%s', $r == 0 ? 'left' : 'right'); ?>">
               <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
                            <div class="entry-thumb hover-scale">
                                 <a href="<?php echo $post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $post['title']; ?>"></a>
                                 <?php echo $post['category']; ?>
                            </div>
                            <div class="entry-detail">
                                <header class="entry-header">
                                    <h2 class="entry-title h4"><a href="<?php echo $post['permalink']; ?>" rel="bookmark"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $post['title']; ?></a></h2>
                                    <div class="entry-meta entry-meta-1">
                                        <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $post['datetime']; ?>" title="<?php echo $post['datetime']; ?>"><?php echo $post['datetime']; ?></time></span>
                                        <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $post['permalink'] . '#respond'; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $post['comment_count']; ?></a></span>
                                        <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $post['views']; ?></span>
                                        <span class="likes-link text-muted pull-right mr10"><i class="tico tico-favorite"></i><a href="javascript:void(0)"><?php echo $post['star_count']; ?></a></span>
                                    </div>
                                </header>
								<div class="entry-excerpt">
                                    <div class="post-excerpt"><?php echo $post['excerpt']; ?></div>
                                </div>
                            </div>
                        </article>
            </div>
            <?php
        }
    }
    ?>
</div>