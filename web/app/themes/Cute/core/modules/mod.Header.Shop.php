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
<?php load_mod('mod.Head'); ?>
<body <?php body_class('is-loadingApp'); ?>>
<!-- 页面开始 -->
<div class="page__wrapper">
<div class="loading-line"></div>
<?php if (tt_get_option('tt_is_loading_css', true)) { ?>
<div id="loading"> <div id="loading-center"> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> </div></div>
<?php } ?>
<!-- /.header -->
<header class="header">
    <div class="header sps">
        <div class="header__inner">
            <div class="container header__content">
                <a class="header__logo" href="<?php echo home_url(); ?>"><img src="<?php echo tt_get_option('tt_logo'); ?>" alt="<?php echo get_bloginfo('name'); ?>" class="dark"><img src="<?php echo tt_get_option('tt_logo_light'); ?>" alt="<?php echo get_bloginfo('name'); ?>" class="light"></a>
                <div class="menu-primary-container">
                    <?php wp_nav_menu( array( 'theme_location' => 'header-menu', 'container' => '', 'menu_id'=> 'menu-primary', 'menu_class' => 'header__nav header__nav--left', 'depth' => '3', 'fallback_cb' => false  ) ); ?>

                </div>
                
                <ul class="header__nav header__nav--right">

                    <?php $user = wp_get_current_user(); ?>
                    <?php if($user && $user->ID) { ?>
                    <?php $unread = tt_count_pm_cached($user->ID, 0, MsgReadStatus::UNREAD); ?>
                        <li class="header__user nav-user dropdown">
                    <a href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if($unread) { ?><i class="badge"></i><?php } ?>
                        <img src="<?php echo tt_get_avatar($user->ID, 'small'); ?>" class="avatar">
                        <span class="username"><?php echo $user->display_name; ?></span>
                    </a>
                     <ul class="nav-user-menu dropdown-menu">
                            <?php if(current_user_can('edit_users')) { ?>
                            <li><a href="<?php echo get_dashboard_url(); ?>"><span class="tico tico-meter"></span><?php _e('Go Dashboard', 'tt'); ?></a></li>
                            <li><a href="<?php echo tt_url_for('manage_home'); ?>"><span class="tico tico-list-small-thumbnails"></span><?php _e('Site Management', 'tt'); ?></a></li>
                            <?php }elseif(current_user_can('publish_posts')){ ?>
                            <li><a href="<?php echo get_dashboard_url(); ?>"><span class="tico tico-meter"></span><?php _e('Go Dashboard', 'tt'); ?></a></li>
                            <?php } ?>
                            <li><a href="<?php echo tt_url_for('new_post'); ?>"><span class="tico tico-quill"></span><?php _e('New Post', 'tt'); ?></a></li>
                            <li><a href="<?php echo tt_url_for('uc_latest', $user); ?>"><span class="tico tico-stack-overflow"></span><?php _e('My Posts', 'tt'); ?></a></li>
                            <li><a href="<?php echo tt_url_for('my_all_orders', $user); ?>"><span class="tico tico-exchange"></span><?php _e('My Orders', 'tt'); ?></a></li>
                            <li><a href="<?php echo tt_url_for('my_credits', $user); ?>"><span class="tico tico-diamond"></span><?php _e('My Credits', 'tt'); ?></a></li>
                            <li><a href="<?php echo tt_url_for('in_msg'); ?>"><span class="tico tico-envelope"></span><?php _e('My Messages', 'tt'); ?><?php if($unread){printf('<i style="position: absolute;margin-left: 5px;">(%d)</i>', intval($unread));} ?></a></li>
                            <li><a href="<?php echo tt_url_for('my_settings'); ?>"><span class="tico tico-equalizer"></span><?php _e('My Settings', 'tt'); ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo tt_add_redirect(tt_url_for('signout'), tt_get_current_url()); ?>"><span class="tico tico-sign-out"></span><?php _e('Sign Out', 'tt'); ?></a></li>
                        </ul>
                    </li>
                        

                    <?php }else{ ?>
                        <?php if (tt_get_option('tt_is_modloginform', true)) { ?>
                        <li class="header__login login-actions"><a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>" id="go-signin" class="login-link bind-redirect">登录</a></li>
                        <?php } else { ?>
                           <li class="header__login"><a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>" id="go-signin">登录</a></li>
                        <?php } ?>
                        <li class="header__nav__btn header__nav__btn--primary header__register"><a href="<?php echo tt_add_redirect(tt_url_for('signup'), tt_get_current_url()); ?>" id="go-register">注册</a></li>
                <?php } ?>
                <?php if( wp_is_mobile() ) { ?>
                <li class="header__nav__btn"><a href="javascript:void(0)" data-toggle="modal" data-target="#globalSearch" data-backdrop="1"><span class="tico tico-search"></span></a></li>
                <?php } else { ?>
                <li class="header__nav__btn header__nav__btn--search">
                <div class="link-wrapper">
                    <i class="tico tico-search"></i>
                    <form method="get" action="<?php echo home_url(); ?>" role="search">
                        <input name="s" type="text" value="" placeholder="输入关键词回车">
                    </form>
                </div>
                </li>
                <?php } ?>
                    <li class="header__nav__btn sidenav-trigger">
                        <a href="javascript: void(0)">
                            <span class="sidenav-trigger__open"><i class="tico tico-list-small-thumbnails"></i></span>
                            <span class="sidenav-trigger__close"><i class="tico tico-close delete"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header> 


