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
 * Class CreditIntro.
 */
class CreditIntro extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(false, __('TT-Credit Intro', 'tt'), array('description' => __('TT-Show credit intro', 'tt'), 'classname' => 'widget_credit-intro'));
    }

    public function widget($args, $instance)
    {
        ?>
        <?php echo $args['before_widget']; ?>
        <?php if ($instance['title']) {
            echo $args['before_title'].$instance['title'].$args['after_title'];
        } ?>
        <div class="widget-content">
            <div id="creditintrowidget"></div>
        </div>
        <!-- <script src="http://localhost:8080/assets/app.js"></script> -->
        <script src="<?php echo THEME_CDN_ASSET.'/vue/creditwidget2018512.js'; ?>"></script>
        <?php echo $args['after_widget']; ?>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        // TODO 清除小工具缓存

        return $new_instance;
    }

    public function form($instance)
    {
        $title = esc_attr(isset($instance['title']) ? $instance['title'] : __('CREDITS INTRO', 'tt')); ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title：', 'tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php
    }
}

/* 注册小工具 */
if (!function_exists('tt_register_widget_credit_intro')) {
    function tt_register_widget_credit_intro()
    {
        register_widget('CreditIntro');
    }
}
add_action('widgets_init', 'tt_register_widget_credit_intro');
