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
<div class="cms-cat cms-cat-s0">
<?php
    global $cat_data;
    $posts = $cat_data->posts;
    $i = 0;
    foreach ($posts as $post) {
        $r = fmod($i, 3)+1;
        $i++;
        if ($i == 1) {
            ?>
    <div class="row-big">
        <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
            <div class="entry-thumb hover-scale">
                <a href="<?php echo $post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $post['title']; ?>" style="max-height: 175px;"></a>
            </div>
            <div class="entry-detail">
                <h3 class="entry-title">
                    <a href="<?php echo $post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $post['title']; ?></a>
                </h3>
                <p class="entry-excerpt"><?php echo $post['excerpt']; ?></p>
            </div>
        </article>
    </div>
            <?php
        } elseif ($i <= 9) {
    ?>
    <div class="row-small">
        <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
            <div class="entry-detail">
                <h3 class="entry-title">
                    <i class=""></i>
                    <a href="<?php echo $post['permalink']; ?>" title="<?php echo $post['title']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $post['title']; ?></a>
                </h3>
            </div>
        </article>
    </div>
    <?php
        }
    }
?>
</div>