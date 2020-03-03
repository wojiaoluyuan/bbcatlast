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
<!-- 登录弹窗 -->
<div class="form-signin modal fadeScale" id="modalSignBox">
    <h2><?php _e('Sign In', 'tt'); ?></h2>
    <form class="local-signin">
        <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
        <input style="display:none" type="text" name="fakeusernameremembered"/>
        <input style="display:none" type="password" name="fakepasswordremembered"/>
        <div class="form-group input-container clearfix">
            <input autocomplete="off" name="user_login" type="text" class="form-control input text-input" id="user_login-input" title="" required="required" placeholder="<?php _e('Email/Username', 'tt'); ?>">
            <span class="tip"></span>
        </div>
        <div class="form-group input-container clearfix">
            <input autocomplete="new-password" name="password" type="password" class="form-control input password-input" id="password-input" title="" required="required" placeholder="<?php _e('Password', 'tt'); ?>">
            <span class="tip"></span>
        </div>
        <?php if(tt_get_option('tt_tencent_captcha', false)){ ?>
        <div class="form-group input-container clearfix">
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
        <input name="nonce" type="hidden" value="<?php echo wp_create_nonce('page-signin'); ?>">
        <button class="btn btn-info btn-block submit" type="submit"><?php _e('Submit', 'tt'); ?></button>
        <div class="text-center mt20 login-help">
            <a href="<?php echo tt_add_redirect(tt_url_for('signup'), tt_get_current_url()); ?>" id="go-register" class="mr20 register-link" rel="link"><?php _e('Register Now', 'tt'); ?></a>
            <span class="dot-separator" role="separator"></span>
            <a href="<?php echo tt_add_redirect(tt_url_for('findpass'), tt_get_current_url()); ?>" id="go-findpass" class="ml20 findpass-link" rel="link"><?php _e('Forgot your password?', 'tt'); ?></a>
        </div>
    </form>
    <!-- Open Login -->
    <?php
    $open_weibo = tt_get_option('tt_enable_weibo_login');
    $open_qq = tt_get_option('tt_enable_qq_login');
    $open_weixin = tt_get_option('tt_enable_weixin_login');
    $has_open_login = $open_weibo || $open_qq || $open_weixin;
    ?>
    <?php if($has_open_login) { ?>
        <div class="open-login clearfix">
            <p class="mb20 hidden-xs"><?php _e('SignIn with Social Account', 'tt'); ?></p>
            <div class="social-items">
            <?php if($open_weibo) { ?>
                <a href="<?php echo tt_add_redirect(tt_url_for('oauth_weibo'), tt_get_current_url()); ?>" class="btn btn-sn-weibo">
                    <span class="tico tico-weibo"></span>
                </a>
            <?php } ?>
            <?php if($open_qq) { ?>
                <a href="<?php echo tt_add_redirect(tt_url_for('oauth_qq'), tt_get_current_url()); ?>" class="btn btn-sn-qq">
                    <span class="tico tico-qq"></span>
                </a>
            <?php } ?>
            <?php if($open_weixin) { ?>
                <a href="<?php echo tt_add_redirect(tt_url_for('oauth_weixin'), tt_get_current_url()); ?>" class="btn btn-sn-weixin">
                    <span class="tico tico-weixin"></span>
                </a>
            <?php } ?>
            </div>
        </div>
    <?php } ?>
    <!-- End Open Login -->
</div>