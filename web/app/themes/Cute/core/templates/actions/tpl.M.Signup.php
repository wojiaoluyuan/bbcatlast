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

wp_no_robots();

if ( !get_option('users_can_register') ) {
	wp_safe_redirect( add_query_arg('registration', 'disabled', tt_url_for('signin')));
	exit();
}

// 引入头部
tt_get_header('simple');
$invite_option = $invite_option = tt_get_option('tt_enable_k_invite', false);
?>
<body class="is-loadingApp action-page signup" style="background: url(<?php echo tt_get_option('tt_signup_bg'); ?>) top/cover no-repeat fixed">
    <?php load_template(THEME_MOD . '/mod.LogoHeader.php'); ?>
    <a href="<?php echo home_url(); ?>" class="top-go-home" style="z-index: 999">返回首页</a>
    <div id="content" class="wrapper container no-aside">
        <div class="main inner-wrap">
            <form class="form-signup">
                <h2 class="title signup-title mb30"><?php _e('Create Account', 'tt'); ?></h2>
<!--                <div class="msg"></div>-->
                <p id="default-tip"><?php _e('We will send you an email including a activation link to help to complete the registration steps.', 'tt'); ?></p>
                <?php
                    $open_weibo = tt_get_option('tt_enable_weibo_login');
                    $open_qq = tt_get_option('tt_enable_qq_login');
                    $open_weixin = tt_get_option('tt_enable_weixin_login');
                    $has_open_login = $open_weibo || $open_qq || $open_weixin;
                ?>
                <div class="local-signup">
                    <div class="input-container clearfix">
                        <input autofocus="" name="user_login" type="text" class="input text-input form-control" id="user_login-input" title="" placeholder="<?php _e('Account', 'tt'); ?>" required="required">
                    </div>
                    <div class="input-container clearfix mt10">
                        <input autofocus="" name="email" type="email" class="input email-input form-control" id="email-input" title="" placeholder="<?php _e('Email', 'tt'); ?>" required="required">
                    </div>
                    <div class="input-container clearfix mt10">
                        <input autocomplete="new-password" name="password" type="password" class="input password-input form-control" id="password-input" title="" placeholder="<?php _e('Password', 'tt'); ?>" required="required">
                    </div>
                    <?php if($invite_option) { ?>
                    <div class="input-container clearfix mt10">
                        <input autofocus="" name="invite" type="text" class="input invite-input form-control" id="invite-input" title="" placeholder="邀请码" required="required" <?php if (isset($_GET['invite'])){echo 'value="'.$_GET['invite'].'" readonly="readonly"';};?>>
                    <span>没有邀请码？<a href="/site/payinvite" style="color: #e74c3c;font-weight: 600;">点击购买</a></span>
                    </div>
                    <?php } ?>
                    <?php if(tt_get_option('tt_tencent_captcha', false)){ ?>
                    <div class="input-container clearfix mt10">
                     <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
                     <input type="hidden" name="tcaptcha_007" value="1" />
                     <input type="hidden" name="tcaptcha_ticket" id="tcaptcha_ticket" value="" />
                     <input type="hidden" name="tcaptcha_randstr" id="tcaptcha_randstr" value="" />
                     <input type="button" class="button button-primary" id="TencentCaptcha"  data-appid="<?php echo tt_get_option('tt_tencent_captcha_id'); ?>"  data-cbfn="tcaptcha_callback" value="点我验证" style="float:none;width:100%" />
                     <script>
                          window.tcaptcha_callback = function(res){
                              if(res.ret === 0){
                                  document.getElementById("tcaptcha_ticket").value= res.ticket;
                                  document.getElementById("tcaptcha_randstr").value=res.randstr;
                                  document.getElementById("TencentCaptcha").value="您已通过验证~";
                              }else{
                                  alert("您已取消验证");
                              }
                          }
                      </script>
                    </div>
                    <?php } ?>
                    <input name="nonce" type="hidden" value="<?php echo wp_create_nonce('page-signup'); ?>">
                    <input name="step" type="hidden" value=1>
                    <a href="<?php echo home_url(); ?>" class="btn btn-primary mt20 mb20" style="width:auto;position: relative;height: 42px;letter-spacing: 5px;">返回首页</a>
                    <button class="btn btn-primary mt20 mb20" id="signup-btn" style="margin-left: 65px;width:auto;" disabled><!--span class="indicator spinner tico tico-spinner3"></span-->立刻注册</button>
                </div>
                <?php if($has_open_login) { ?>
                    <!-- Open Login -->
                    <div class="open-login clearfix mt10 mb10">
                        <p class="text-white mt10 mr10 pull-left hidden-xs"><?php _e('Quick SignIn', 'tt'); ?></p>
                        <?php if($open_weibo) { ?>
                            <a href="<?php echo tt_add_redirect(tt_url_for('oauth_weibo')); ?>" class="btn btn-lg btn-sn-weibo pull-left anchor-noborder">
                                <span class="tico tico-weibo"></span>
                            </a>
                        <?php } ?>
                        <?php if($open_qq) { ?>
                            <a href="<?php echo tt_add_redirect(tt_url_for('oauth_qq')); ?>" class="btn btn-lg btn-sn-qq pull-left anchor-noborder">
                                <span class="tico tico-qq"></span>
                            </a>
                        <?php } ?>
                        <?php if($open_weixin) { ?>
                            <a href="<?php echo tt_add_redirect(tt_url_for('oauth_weixin')); ?>" class="btn btn-lg btn-sn-weixin pull-left anchor-noborder">
                                <span class="tico tico-weixin"></span>
                            </a>
                        <?php } ?>
                    </div>
                    <!-- End Open Login -->
                <?php } ?>
                <div class="note">
                    <p class="login-note"><?php _e('Already have an account? ', 'tt'); ?><a class="login-link" id="go-login" href="<?php echo tt_add_redirect(tt_url_for('signin')); ?>" rel="link"><?php _e('Sign In', 'tt'); ?></a></p>
                    <!-- Terms -->
                    <p class="terms-note"><?php echo sprintf(__('* By signing up, you agree to our <a href="%s" target="_blank"><strong>「Terms of Use, Privacy Policy」</strong></a> and to receive emails, newsletters &amp; updates.', 'tt'), tt_url_for('privacy')); ?></p>
                </div>
            </form>
        </div>
    </div>
<?php

// 引入页脚
tt_get_footer('simple');