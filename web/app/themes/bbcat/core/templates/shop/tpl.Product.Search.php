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
<?php $search = get_search_query(); ?>
<?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>
<?php tt_get_header('shop'); ?>
<!-- Left Menu -->
<div class="menu_wrapper" style="margin-top: 55px;">
    <div class="menu">
        <?php wp_nav_menu( array( 'theme_location' => 'shop-menu', 'container' => '', 'menu_id'=> 'shop-menu-items', 'menu_class' => 'menu-items', 'depth' => '1', 'fallback_cb' => false  ) ); ?>
    </div>
    <div class="icons">
        <a href="javascript:;" data-toggle="modal" data-target="#siteQrcodes" data-trigger="click"><span class="tico tico-qrcode"></span></a>
        <a href="<?php echo 'mailto:' . get_option('admin_email'); ?>"><span class="tico tico-envelope"></span></a>
        <a href="<?php bloginfo('rss2_url'); ?>"><span class="tico tico-rss"></span></a>
    </div>
</div>
<div class="wrapper">
    <div class="content text-center">
        <div class="billboard" style="background-image: url(<?php echo THEME_ASSET . '/img/shop-banner.jpg'; ?>)">
            <div class="billboard-text">
                <h1><i class="tico tico-search"></i><?php printf(__('Searching: %s'), $search); ?></h1>
                <p></p>
            </div>
        </div>
    </div>
    <?php $vm = ShopSearchVM::getInstance($paged, $search); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Products search cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <div class="content">
        <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $products = $data->products; ?>
            <div class="row loop-grid products-loop-grid mb20 clearfix">
                <?php foreach ($products as $product) { ?>
                    <div class="col col-md-4 col-sm-4 col-xs-12">
                        <article class="product" id="<?php echo 'product-' . $product['ID']; ?>">
                            <div class="entry-thumb">
                                <a href="<?php echo $product['permalink']; ?>">
                                    <img class="thumb-medium wp-post-image fadeIn" src="<?php echo $product['thumb']; ?>">
                                    <span class="product-stats clearfix">
                            <span class="product-stat"><i class="tico tico-eye"></i><?php echo $product['views']; ?></span>
                            <span class="product-stat"><i class="tico tico-comments"></i><?php echo $product['comment_count']; ?></span>
                            <span class="product-stat"><i class="tico tico-truck"></i><?php echo $product['sales']; ?></span>
                        </span>
                                </a>
                            </div>
                            <div class="entry-detail">
                                <div class="pull-right entry-meta">
                            <span class="pull-left price">
                                    <?php if(!($product['price'] > 0)) { ?>
                                        <span class="price line-height"><?php echo __('FREE', 'tt'); ?></span>
                                    <?php }elseif(!isset($product['discount'][0]) || $product['min_price'] >= $product['price']){ ?>
                                        <?php echo $product['price_icon']; ?>
                                        <span class="price line-height"><?php echo $product['price']; ?></span>
                                    <?php }else{ ?>
                                        <del><span class="price original-price"><?php echo $product['price_icon']; ?><?php echo $product['price']; ?></span></del>
                                        <div><?php echo $product['price_icon']; ?><ins><span class="price discount-price"><?php echo $product['min_price']; ?></span></ins></div>
                                    <?php } ?>
                                    </span>
                             <span class="meta-price">
                                <a href="<?php echo $product['permalink']; ?>" title="立即购买">立即购买</a>
                            </span>
                            </div>
                                <h2 class="entry-title"><a href="<?php echo $product['permalink']; ?>" rel="bookmark"><?php echo $product['title']; ?></a></h2>
                            </div>
                        </article>
                    </div>
                <?php } ?>
            </div>
            <?php if($pagination_args['max_num_pages'] > 1) { ?>
                <div class="pagination-wrap">
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php tt_get_footer(); ?>