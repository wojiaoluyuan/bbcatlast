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
<?php

/**
 * Class AwardCouponWidget
 */
class AwardCouponWidget extends WP_Widget {
    function __construct() {
        parent::__construct(false, __('TT-Coupon Award', 'tt'), array( 'description' => __('TT-Random show coupon award', 'tt') ,'classname' => 'widget_award wow bounceInRight'));
    }

    function select_coupon($coupon_str) {
        if (!is_string($coupon_str) || empty($coupon_str)) {
            return '';
        }
        $coupons = explode(',', $coupon_str);
        $sel_index = rand(0, count($coupons) - 1);
        return $coupons[$sel_index];
    }

    function widget($args, $instance) {
        // parent::widget($args, $instance); // TODO: Change the autogenerated stub
        // extract($args);
        echo $args['before_widget'];
        if($instance['title']) { echo $args['before_title'] . $instance['title'] . $args['after_title']; } ?>
        <div class="widget-content">
            <style>
                .award-des {
                    text-align: center;
                    font-style: italic;
                    margin-top: 20px;
                }
                .award-coupon {
                    text-align: center;
                    color: #eee;
                    font-size: 10px;
                    margin: 40px auto;
                    font-style: italic;
                }
                .coupon-des {
                    text-align: center;
                    text-decoration: underline;
                    margin-bottom: 25px;
                }
            </style>
            <div class="row">
                <p class="award-des">你努力的刮开墙壁，发现这里有一行小字</p>
                <?php
                    $rand = rand(1, 1000);
                    if ($rand === 888) {
                        $coupon = array(
                            'type' => 'cd5',
                            'text' => __('这是一个5折优惠码', 'tt'),
                            'coupon' => $this->select_coupon($instance['cd5'])
                        );
                    } elseif (in_array($rand, array(233, 666, 999))) {
                        $coupon = array(
                            'type' => 'cd6',
                            'text' => __('这是一个6折优惠码', 'tt'),
                            'coupon' => $this->select_coupon($instance['cd6'])
                        );
                    } elseif (in_array($rand, array(121, 208, 312, 425, 509, 808))) {
                        $coupon = array(
                            'type' => 'cd7',
                            'text' => __('这是一个7折优惠码', 'tt'),
                            'coupon' => $this->select_coupon($instance['cd7'])
                        );
                    } elseif ($rand >= 257 && $rand <= 266) {
                        $coupon = array(
                            'type' => 'cd8',
                            'text' => __('这是一个8折优惠码', 'tt'),
                            'coupon' => $this->select_coupon($instance['cd8'])
                        );
                    } elseif ($rand >= 606 && $rand <=625) {
                        $coupon = array(
                            'type' => 'cd9',
                            'text' => __('这是一个9折优惠码', 'tt'),
                            'coupon' => $this->select_coupon($instance['cd9'])
                        );
                    } else {
                        $coupon = array(
                            'type' => 'cd10',
                            'text' => __('这里什么都没有, 不要气馁，明天再来', 'tt'),
                            'coupon' => ''
                        );
                    }
                ?>
                <?php if (!isset($_COOKIE['tt_coupon_award_engage']) && !empty($coupon['coupon'])) { ?>
                    <p class="award-coupon"><?php echo $coupon['coupon']; ?></p>
                    <p class="coupon-des"><?php echo $coupon['text']; ?></p>
                <?php } else { ?>
                    <p class="award-coupon"><?php echo '谢谢来访'; ?></p>
                    <p class="coupon-des"><?php _e('这里什么都没有, 不要气馁，明天再来', 'tt'); ?></p>
                <?php } ?>
            </div>
        </div>
        <?php echo $args['after_widget']; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        $title = esc_attr(isset($instance['title']) ? $instance['title'] : __('COUPON AWARD', 'tt'));
        $cd5 = esc_attr(isset($instance['cd5'])) ? $instance['cd5'] : ''; // 0.1% 概率
        $cd6 = esc_attr(isset($instance['cd6'])) ? $instance['cd6'] : ''; // 0.3% 概率
        $cd7 = esc_attr(isset($instance['cd7'])) ? $instance['cd7'] : ''; // 0.6% 概率
        $cd8 = esc_attr(isset($instance['cd8'])) ? $instance['cd8'] : ''; // 1.0% 概率
        $cd9 = esc_attr(isset($instance['cd9'])) ? $instance['cd9'] : ''; // 2.0% 概率
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('cd5'); ?>"><?php _e('Discount 50% Coupons：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('cd5'); ?>" name="<?php echo $this->get_field_name('cd5'); ?>" type="text" value="<?php echo $cd5; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('cd6'); ?>"><?php _e('Discount 60% Coupons：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('cd6'); ?>" name="<?php echo $this->get_field_name('cd6'); ?>" type="text" value="<?php echo $cd6; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('cd7'); ?>"><?php _e('Discount 70% Coupons：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('cd7'); ?>" name="<?php echo $this->get_field_name('cd7'); ?>" type="text" value="<?php echo $cd7; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('cd8'); ?>"><?php _e('Discount 80% Coupons：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('cd8'); ?>" name="<?php echo $this->get_field_name('cd8'); ?>" type="text" value="<?php echo $cd8; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('cd9'); ?>"><?php _e('Discount 90% Coupons：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('cd9'); ?>" name="<?php echo $this->get_field_name('cd9'); ?>" type="text" value="<?php echo $cd9; ?>" /></label></p>
        <?php
    }
}

/* 注册小工具 */
if ( ! function_exists( 'tt_register_widget_award' ) ) {
    function tt_register_widget_award() {
        register_widget( 'AwardCouponWidget' );
    }
}
add_action( 'widgets_init', 'tt_register_widget_award' );
