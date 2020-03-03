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
<div class="embed-product">
    <img src="<?=$this->e($thumb)?>">
    <div class="product-info">
        <h4><a href="<?=$this->e($link)?>"><?=$this->e($name)?></a></h4>
        <div class="price">
            <?php if(!($price > 0)) { ?>
                <span><?php echo __('FREE', 'tt'); ?></span>
            <?php }elseif(!isset($discount) || $min_price >= $price) { ?>
                <?php echo $price_icon; ?>
                <span><?php echo $price; ?></span>
            <?php }else{ ?>
                <del><span class="price original-price"><?php echo $price_icon; ?><?php echo $price; ?></span></del>
                <?php echo $price_icon; ?><ins><span class="price discount-price"><?php echo $min_price; ?></span></ins>
            <?php } ?>
        </div>
        <div class="product-rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
            <div class="star-rating tico-star-o" title="<?php printf(__('Rated %0.1f out of 5', 'tt'), $rating_value); ?>">
                                <span class="tico-star" style="<?php echo sprintf('width:%d', $rating_percent) . '%;'; ?>">
                                    <?php printf(__('<strong itemprop="ratingValue" class="rating">%0.1f</strong> out of <span itemprop="bestRating">5</span>based on <span itemprop="ratingCount" class="rating">%d</span> customer ratings', 'tt'), $rating_value, $rating_count); ?>
                                </span>
            </div>
        <div class="entry-meta">
                            <i class="tico tico-eye"></i> 阅读(<?php echo $product_views; ?>)次
                            <i class="tico tico-truck"></i>累计销售(<?php if($product_sales > 0) : echo $product_sales; else : echo '0'; endif; ?>)件
                        </div>
        </div>
        <a class="btn btn-success btn-buy" href="<?=$this->e($link)?>"><i class="tico tico-shopping-cart"></i><?php _e('Buy Now', 'tt'); ?></a>
    </div>
</div>