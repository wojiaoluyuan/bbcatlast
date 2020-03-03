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
<?php $product = get_queried_object(); ?>
<?php tt_get_header('shop'); ?>
    
    <div class="shop-wrapper">
        

         <!-- main   container  -->
        <main id="main" class="wrapper container" role="main">
        <?php $vm = ShopProductVM::getInstance($product->ID); ?>
        <?php global $productdata; $productdata = $vm->modelData; $categories = $productdata->cats; $tags = $productdata->tags; ?>
        
        <div class="row">
            <div class="content-title__inner">
                <!-- Breadcrumb -->
                <nav class="commerce-breadcrumb">
                    <a href="<?php echo home_url(); ?>"><?php _e('HOME', 'tt'); ?></a>
                    <span class="breadcrumb-delimeter">&nbsp;/&nbsp;</span>
                    <a href="<?php echo tt_url_for('shop_archive'); ?>"><?php _e('SHOP', 'tt'); ?></a>
                    <span class="breadcrumb-delimeter">&nbsp;/&nbsp;</span>
                    <?php $cat_breads = array(); foreach($categories as $category) { ?>
                    <?php $category = (array)$category; $cat_breads[] = '<a href="' . $category['permalink'] . '">' . $category['name'] . '</a>'; ?>
                    <?php } ?>
                    <?php echo implode(', ', $cat_breads); ?>
                    <span class="breadcrumb-delimeter">&nbsp;/&nbsp;</span>
                    <?php echo $productdata->title; ?>
                </nav>
            </div>

            <!-- content in middle -->
            <div class="content col-md-push-2 col-sm-12 col-md-10" id="primary">
                <div class="shop-post"> <!-- shop-post -->
                    <div class="shop-single-body"><!-- shop-single-body -->
                        <!-- Product -->
                        <div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $productdata->ID; ?>" class="product type-product">
                            <?php if($productdata->sale_text){ ?><span class="onsale"><span><?php echo $productdata->sale_text; ?></span></span><?php } ?>
                            <div class="single-body-head row">
                            <!-- Images -->
                                <section class="entry-images">
                                    <a href="<?php echo $productdata->thumb; ?>" itemprop="image" class="lightbox-gallery commerce-main-image" data-fancybox="images"><img src="<?php echo $productdata->thumb; ?>"></a>
                                    
                                </section>
                                <!-- Summary -->
                                <section class="summary entry-summary">
                                    <h1 itemprop="name" class="product_title entry-title"><?php echo $productdata->title; ?></h1>
                                    <!-- Description -->
                                    <div class="commerce-product-description" itemprop="description"><p><?php echo $productdata->excerpt; ?></p></div>
                                    <!-- Price -->
                                    <div class="price">
                                    <?php if(!($productdata->price > 0)) { ?>
                                        <span class="price price-free">免费</span>
                                    <?php }elseif(!isset($productdata->discount[0]) || $productdata->min_price >= $productdata->price){ ?>
                                        <span class="price"><?php echo $productdata->price_icon; ?><?php echo $productdata->price; ?></span>
                                    <?php }else{ ?>
                                        <span style="font-size:18px;"><del style="color: #666;"><?php echo $productdata->price_icon; ?><?php echo $productdata->price; ?></del>
                                        <ins>   <?php echo $productdata->price_icon; ?><?php echo $productdata->min_price; ?></ins></span>
                                    <?php } ?>
                                    <?php if($productdata->vip1_price < $productdata->price || $productdata->vip2_price < $productdata->price || $productdata->vip3_price < $productdata->price) { ?>
                                    <div style="font-size:12px;">月费会员价： <ins> <?php echo $productdata->price_icon; ?><?php echo $productdata->vip1_price; ?></ins></div>
                                    <div style="font-size:12px;">年费会员价： <ins> <?php echo $productdata->price_icon; ?><?php echo $productdata->vip2_price; ?></ins></div>
                                    <div style="font-size:12px;">永久会员价： <ins> <?php echo $productdata->price_icon; ?><?php echo $productdata->vip3_price; ?></ins></div>
                                    <?php } ?>
                                    </div>
                                    <!-- Quantity and Action button -->
                                    <div class="variations_form cart" data-product-id="<?php echo $productdata->ID; ?>">
                                        <div class="single_variation_wrap">
                                            <div class="variations_button">
                                                <?php if($productdata->amount < 1) { ?>
                                                <a href="javascript:;" class="btn btn-info btn-buy" data-buy-action="contact" data-msg-title="<?php _e('SOLD OUT', 'tt'); ?>" data-msg-text="<?php echo __('Please contact the site manager via: ', 'tt') . get_option('admin_email'); ?>"><?php _e('SOLD OUT', 'tt'); ?></a>
                                                <div class="quantity">
                                                    <input type="number" step="1" min="1" name="quantity" value="0" title="<?php _e('Qty', 'tt'); ?>" class="input-text qty text" size="4">
                                                </div>
                                                <?php }elseif($productdata->channel == 'taobao') { ?><!-- Link to Taobao -->
                                                <a href="<?php echo $productdata->taobao; ?>" class="btn btn-info btn-buy" data-channel="taobao" target="_blank"><?php _e('Purchase in Taobao', 'tt'); ?></a>
                                                <?php }else{ ?>
                                                <a href="javascript:;" class="btn btn-success btn-buy" data-buy-action="checkout"><?php _e('CHECK OUT', 'tt'); ?></a>
                                                <?php if($productdata->currency=='cash') { ?><a href="javascript:;" class="btn btn-danger btn-buy" data-buy-action="addcart"><?php _e('ADD TO CART', 'tt'); ?></a><?php } ?>
                                                <div class="quantity">
                                                    <input type="number" step="1" min="1" name="quantity" value="1" title="<?php _e('Qty', 'tt'); ?>" class="input-text qty text" size="4">
                                                </div>
                                                <?php } ?>
                                                <input type="hidden" name="product_id" value="<?php echo $productdata->ID; ?>">
                                                <input type="hidden" name="product_amount" value="<?php echo $productdata->amount; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div id="tab-paycontent" class="shop-paycontent"><!-- shop-paycontent -->
                                <div class="paycontent-wrapper">
                                    <?php echo tt_get_product_pay_content($productdata->ID); ?>
                                </div>
                            </div>
                            <article class="single-article"><?php echo $productdata->content; ?></article>
                        </div><!-- Product End -->
                    </div>
                    <div id="tab-reviews" class="shop-comment"><!-- shop-comment -->
                       <div id="reviews">
                            <!--h2><?php printf(__('%d reviews for the product', 'tt'), $productdata->comment_count); ?></h2-->
                            <?php load_mod('shop/mod.Shop.ReplyForm'); ?>
                            <!-- Comments list -->
                            <?php load_mod('shop/mod.Shop.Comments'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- left aside -->
            <div class="widget-area col-sm-12 col-md-2 col-md-pull-10 " id="secondary" role="complementary">
                <div id="shop-post-aside" class="product-info">
                    <?php load_mod('shop/mod.Shop.Sidebar.Left'); ?>
                </div>
            </div>

        </div>
    </main>
</div>  
<?php tt_get_footer(); ?>