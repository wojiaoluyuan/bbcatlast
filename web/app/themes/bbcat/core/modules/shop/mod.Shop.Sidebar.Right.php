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
<?php global $productdata; ?>
<?php $rating_vm = ShopLatestRatedVM::getInstance(); $rated_products = $rating_vm->modelData; ?>
<?php global $cart_items; if(!$cart_items)$cart_items = tt_get_cart(); ?>
<!-- Shopping cart -->
<?php $display_cart = $cart_items && count($cart_items) > 0; ?>
<aside class="commerce-widget shopcart widget_shopping_cart <?php if($display_cart) echo 'active'; ?>">
    <h3 class="widget-title"><?php _e('CART', 'tt'); ?></h3>
    <ul class="widget-content widget_shopping_cart-list">
        <?php $total = 0; foreach ($cart_items as $cart_item) { $cart_item_min_price = tt_get_specified_user_product_price($cart_item['id'], get_current_user_id()); $total += $cart_item_min_price * $cart_item['quantity'] ?>
            <li class="cart-item" data-product-id="<?php echo $cart_item['id']; ?>">
                <a href="<?php echo $cart_item['permalink']; ?>" title="<?php echo $cart_item['name']; ?>">
                    <img class="thumbnail" src="<?php echo $cart_item['thumb']; ?>">
                    <span class="product-title"><?php echo $cart_item['name']; ?></span>
                </a>
                <div class="price"><i class="tico tico-cny"></i><?php echo $cart_item_min_price . ' x ' . $cart_item['quantity']; ?></div>
                <i class="tico tico-close delete"></i>
            </li>
        <?php } ?>
        <div class="cart-amount"><?php _e('TOTAL: ', 'tt'); ?><i class="tico tico-cny"></i><span><?php echo $total; ?></span></div>
    </ul>
    <div class="cart-actions">
        <a class="btn btn-border-success cart-act check-act" href="javascript:;"><?php _e('Check Out Now', 'tt'); ?></a>
        <a class="btn btn-border-danger cart-act clear-act" href="javascript:;"><?php _e('Clear All', 'tt'); ?></a>
    </div>
</aside>
<!-- Latest rated products -->
<?php if(count($rated_products)) { ?>
<aside class="commerce-widget widget_rated_products">
    <h3 class="widget-title"><?php _e('Recent Rated', 'tt'); ?></h3>
    <ul class="widget-content rated_product-list">
        <?php foreach ($rated_products as $rated_product) { ?>
            <?php $rating = $rated_product['rating']; ?>
            <li>
                <a href="<?php echo $rated_product['permalink']; ?>" title="<?php echo $rated_product['title']; ?>">
                    <img class="lazy" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $rated_product['thumb']; ?>">
                    <span class="product-title"><?php echo $rated_product['title']; ?></span>
                    <div class="star-rating tico-star-o" title="<?php printf(__('Rated %0.1f out of 5', 'tt'), $rating['value']); ?>">
                        <span class="tico-star" style="<?php echo sprintf('width:%d', $rating['percent']) . '%;'; ?>">
                            <?php printf(__('<strong itemprop="ratingValue" class="rating">%0.1f</strong> out of <span itemprop="bestRating">5</span>based on <span itemprop="ratingCount" class="rating">%d</span> customer ratings', 'tt'), $rating['value'], $rating['count']); ?>
                        </span>
                    </div>
                </a>
            </li>
        <?php } ?>
    </ul>
</aside>
<?php } ?>
<!-- User view history -->
<?php $view_vm = ShopViewedHistoryVM::getInstance(get_current_user_id()); $view_products = $view_vm->modelData; ?>
<?php if(count($view_products)) { ?>
<aside class="commerce-widget widget_product_view_history">
    <h3 class="widget-title"><?php _e('Recent Viewed Products', 'tt'); ?></h3>
    <ul class="widget-content view_product-list">
        <?php foreach ($view_products as $view_product) { ?>
            <li>
                <a href="<?php echo $view_product['permalink']; ?>" title="<?php echo $view_product['title']; ?>">
                    <img class="lazy" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $view_product['thumb']; ?>">
                    <span class="product-title"><?php echo $view_product['title']; ?></span>
                </a>
                <?php if(!($view_product['price'] > 0)) { ?>
                    <div class="price price-free"><?php _e('FREE', 'tt'); ?></div>
                <?php }elseif(!isset($view_product['discount'][0]) || $view_product['min_price'] >= $view_product['price']){ ?>
                    <div class="price"><?php echo $view_product['price_icon']; ?><?php echo $view_product['price']; ?></div>
                <?php }else{ ?>
                    <div class="price">
                    <del><span class="price original-price"><?php echo $view_product['price_icon']; ?><?php echo $view_product['price']; ?></span></del>
                    <?php echo $view_product['price_icon']; ?><ins><span class="price discount-price"><?php echo $view_product['min_price']; ?></span></ins>
                    </div>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</aside>
<?php } ?>