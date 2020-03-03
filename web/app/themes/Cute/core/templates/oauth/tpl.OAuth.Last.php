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

tt_get_header('simple');

$open_type = strtolower(get_query_var('oauth'));
$invite_option = $invite_option = tt_get_option('tt_enable_k_invite', false);
?>
<body class="is-loadingApp oauth-page oauth-last">
    <?php load_template(THEME_MOD . '/mod.LogoHeader.php'); ?>
    <div id="content" class="wrapper container no-aside">
        <div class="main inner-wrap">
            <div class="form-account">
                <h2 class="form-account-heading"><?php _e('The Last Step, Complete Basic Account Info', 'tt'); ?></h2>
                <p>提示：连接已有账号无需填写邀请码</p>
                <input type="hidden" id="oauthType" value="<?php echo $open_type; ?>">
                <label for="inputUsername" class="sr-only"><?php _e('Email', 'tt'); ?></label>
                <input type="text" id="inputUsername" class="form-control" placeholder="<?php _e('Email', 'tt'); ?>" required="required">
                <label for="inputPassword" class="sr-only"><?php _e('Repeat Password', 'tt'); ?></label>
                <input type="password" id="inputPassword" class="form-control" placeholder="<?php _e('Password', 'tt'); ?>" required="required">
                <?php if($invite_option) { ?>
                <label for="inputInvite" class="sr-only">邀请码</label>
                <input type="text" class="form-control" id="inputInvite" placeholder="邀请码" required="required">
                <span>没有邀请码？<a href="/site/payinvite" style="color: #e74c3c;font-weight: 600;">立刻获取</a></span>
                <?php } ?>
                <button class="btn btn-lg btn-primary btn-block" id="bind-account" type="submit"><?php _e('Bind', 'tt'); ?></button>
            </div>
        </div>
    </div>

<?php

tt_get_footer('simple');