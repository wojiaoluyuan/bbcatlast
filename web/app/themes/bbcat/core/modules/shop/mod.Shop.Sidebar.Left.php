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
<?php global $productdata; $catIDs = $productdata->catIDs; $rand_products = $productdata->rands; ?>
<?php $tool_vm = ShopHeaderSubNavVM::getInstance(); $data = $tool_vm->modelData; $all_categories = $data->categories; $all_tags = $data->tags;?>
<!-- Product category -->
<aside class="commerce-widget widget_product_categories">
    <h3 class="widget-title"><?php _e('Categories', 'tt'); ?></h3>
    <ul class="widget-content category-list">
      <li class="tico-angle cat-item"> <a class="product-cat cat-link" href="<?php echo tt_url_for('shop_archive'); ?>" title="">全部商品</a> </li>
        <?php foreach ($all_categories as $category) { ?>
            <li class="<?php if(in_array($category['ID'], $catIDs)){echo 'tico-angle cat-item active';}else{echo 'tico-angle cat-item';}; ?>">
                <a class="product-cat cat-link" href="<?php echo $category['permalink']; ?>" title=""><?php echo $category['name']; ?></a>
            </li>
        <?php } ?>
    </ul>
</aside>
<!-- Product list -->
<aside class="commerce-widget widget_products">
    <h3 class="widget-title"><?php _e('Products', 'tt'); ?></h3>
    <ul class="widget-content product-list">
        <?php foreach ($rand_products as $rand_product) { ?>
            <li>
                <a href="<?php echo $rand_product['permalink']; ?>" title="<?php echo $rand_product['title']; ?>">
                    <img class="lazy" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $rand_product['thumb']; ?>">
                    <span class="product-title"><?php echo $rand_product['title']; ?></span>
                </a>
                <?php if(!($rand_product['price'] > 0)) { ?>
                    <div class="price price-free"><?php _e('FREE', 'tt'); ?></div>
                <?php }elseif(!isset($rand_product['discount'][0]) || $rand_product['min_price'] >= $rand_product['price']){ ?>
                    <div class="price"><?php echo $rand_product['price_icon']; ?><?php echo $rand_product['price']; ?></div>
                <?php }else{ ?>
                    <div class="price">
                    <del><span class="price original-price"><?php echo $rand_product['price_icon']; ?><?php echo $rand_product['price']; ?></span></del>
                    <?php echo $rand_product['price_icon']; ?><ins><span class="price discount-price"><?php echo $rand_product['min_price']; ?></span></ins>
                    </div>
               <?php } ?>
            </li>
        <?php } ?>
    </ul>
</aside>
<!-- Product tags -->
<aside class="commerce-widget widget_product_tag_cloud">
    <h3 class="widget-title"><?php _e('Product Tags', 'tt'); ?></h3>
    <div class="widget-content tagcloud">
        <?php foreach ($all_tags as $tag) { ?>
            <a class="product-tag tag-link" href="<?php echo $tag['permalink']; ?>" title=""><?php echo $tag['name']; ?></a>
        <?php } ?>
    </div>
</aside>