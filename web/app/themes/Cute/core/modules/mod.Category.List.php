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
<?php $paged = get_query_var('paged') ? : 1; ?>
    <?php $vm = CategoryPostsVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Category posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
<?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $category = $data->category; $category_posts = $data->category_posts; ?>
<!-- 分类名及介绍信息 -->
<section id="hero">
    <div class="hero hero--animate" style="height: 450px"> 
        <div class="hero__image" style="background-image: url('<?php echo $category['thumbnail'] ? $category['thumbnail'] : tt_get_option('tt_shop_hero_bg');?>');"> 
            <div class="hero__image__overlay"></div>
        </div>
        <div class="hero__inner"> 
            <div class="hero__content"> 
                <h1><?php echo $category['cat_name'];?></h1> 
                <h2><?php echo $category['description'];?></h2>
            </div>
        </div>
    </div> 
</section>
<!-- 分类导航 -->
<section id="cat-nav" class="wrapper container">
    <div class="secondary-navbar">
        <div class="secondary-navbar-inner clearfix">
            <ul class="secondary-navbar_list-items secondary-navbar_list-items--left clearfix">
                <!-- Categories -->
                <?php $categories = $data->categories; ?>
                <li class="secondary-navbar_list-item secondary-navbar_list-item--filter category-filter">
                    <a href="javascript:;">分类栏目</a>
                    <ul>
                        <?php foreach ($categories as $category) { ?>
                            <li><a href="<?php echo $category['permalink']; ?>"><strong><?php echo $category['name']; ?></strong> (<?php echo $category['count']; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <!-- Tags -->
                <?php $tags = $data->tags; ?>
                <li class="secondary-navbar_list-item secondary-navbar_list-item--filter tag-filter">
                    <a href="javascript:;">栏目标签</a>
                    <ul>
                        <?php foreach ($tags as $tag) { ?>
                            <li><a href="<?php echo $tag['permalink']; ?>"><strong><?php echo $tag['name']; ?></strong> (<?php echo $tag['count']; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="serach-form">
                    <div class="secondary-navbar_list-item header-search">
                        <form method="get" action="/">
                            <input autocomplete="off" class="header_search-input" placeholder="搜索点什么..." spellcheck="false" name="s" type="text" value="">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>
<div id="content" class="wrapper right-aside">
    <!-- 判断布局 -->
    <?php 
    if(tt_get_option('post_template_cats_is_sidebar', true)) { 
        $col_mod_num = 'col-md-8';
        $card_col_mod_num = 'col-md-4 col-sm-6 col-xs-6';
    }else{
        $col_mod_num = 'col-md-12';
        $card_col_mod_num = 'col-md-3 col-sm-4 col-xs-6';
    }
    ?>
        <section id="mod-insideContent" class="main-wrap container content-section clearfix">
        <!-- 分类文章列表 -->
           <div id="postlist-main" class="main primary <?php echo $col_mod_num; ?>" role="main">
                <!-- 分类文章 -->
                <section class="category-posts loop-rows posts-loop-rows">
                    <?php foreach ($category_posts as $category_post) { ?>
                        <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="post type-post status-publish wow bounceInUp <?php echo 'format-' . $category_post['format'] . ' ' . $category_post['sticky_class']; ?>">
                            <div class="entry-thumb hover-scale">
                                <a href="<?php echo $category_post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $category_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $category_post['title']; ?>"></a>
                              </div>
                            <div class="entry-detail">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php echo $category_post['permalink']; ?>" rel="bookmark"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $category_post['title']; ?></a>
                                    </h2>
                                </header>
                                <div class="entry-excerpt">
                                    <div class="post-excerpt"><?php echo $category_post['excerpt']; ?></div>
                                </div>
                              <div class="entry-tags"><?php echo $category_post['tags']; ?></div>
                            </div>
                          <div class="entry-meta entry-meta-1">
                                        <span class="author vcard"><i class="tico tico-user"></i><a class="url" href="<?php echo $category_post['author_url']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $category_post['author']; ?></a></span>
                                        <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $category_post['datetime']; ?>" title="<?php echo $category_post['datetime']; ?>"><?php echo $category_post['time']; ?></time></span>
                                        <span class="views-count text-muted"><i class="tico tico-folder-open-o"></i><?php echo $category_post['category']; ?></span>
                                        <span class="views-count text-muted"><i class="tico tico-eye"></i><?php echo $category_post['views']; ?></span>
                                        <span class="comments-link text-muted"><i class="tico tico-comments-o"></i><a href="<?php echo $category_post['permalink'] . '#respond'; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>><?php echo $category_post['comment_count']; ?></a></span>
                                        <span class="read-more"><a href="<?php echo $category_post['permalink']; ?>"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>阅读全文<i class="tico tico-sign-in"></i></a></span>
                                    </div>
                        </article>
                    <?php } ?>
                </section>
                <?php if($pagination_args['max_num_pages'] > 1) { ?>
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                <?php } ?>
        </div>
        <!-- is_sidebar -->
        <?php if (tt_get_option('post_template_cats_is_sidebar', true)) { load_mod('mod.Sidebar'); } ?>
    </section>
    <?php } ?>
</div>