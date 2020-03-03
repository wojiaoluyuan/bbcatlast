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
<?php if (tt_get_option('tt_enable_k_footernews', true)) { ?>
<footer class="footer">
  <div class="footer-wrap">
    <div class="footer-nav footer-custom">
  <div class="footer-nav-links">
                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'menu_class' => 'footer-menu', 'depth' => '1', 'fallback_cb' => 'header-menu'  ) ); ?>
            </div>
	<div id="footer-menu">
		<div class="container">
			<div class="pull-left">
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">关于我们</li>
					<li>
						<a href="/about">关于我们</a></li>
					<li>
						<a href="/guestbook">联系留言</a></li>
					<li>
						<a href="/site/privacy-policies-and-terms">版权声明</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">常见问题</li>
					<li>
						<a href="/gmlc">购买流程</a></li>
					<li>
						<a href="/zffs">支付方式</a></li>
					<li>
						<a href="/shfw">售后服务</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">合作伙伴</li>
					<li>
						<a href="/tgyj">建议提交</a></li>
					<li>
						<a href="/business">广告合作</a></li>
					<li>
						<a href="/links">友情链接</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">解决方案</li>
					<li>
						<a href="/ztxg">代理服务</a></li>
					<li>
						<a href="/azts">安装调试</a></li>
					<li>
						<a href="/hjdj">环境搭建</a></li>
				</ul>
				<ul class="pull-left ml20 mr20">
					<li class="fs16" style="padding: 0px;">官方微信</li>
					<li>
						<a>
							<img class="kuangimg" alt="洛茛艺术影视在线" src="<?php echo THEME_ASSET . '/img/qr/weixin.png'; ?>"></a>
					</li>
				</ul>
				<ul class="pull-left ml20 mr20">
					<li class="fs16" style="padding: 0px;">官方支付宝</li>
					<li>
						<a>
							<img class="kuangimg" alt="洛茛艺术影视在线" src="<?php echo THEME_ASSET . '/img/qr/alipay.png'; ?>"></a>
					</li>
				</ul>
			</div>
			<div class="col-contact">
				<p class="phone"><?php echo get_bloginfo('name'); ?></p>
				<p>
					<span class="J_serviceTime-normal">周一至周日 10:00-24:00</span>
					<br>（其他时间勿扰）</p>
                <?php $qq = tt_get_option('tt_site_qq')  ?>
				<a rel="nofollow" href="<?php echo 'https://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">在线咨询</a></div>
		</div>
	</div>
	<div id="footer-copy">
		<div class="container">
			<div class="copyright">
				<!-- 页脚菜单/版权信息 IDC No. -->
				<div class="footer-shares">
                <?php if($facebook = tt_get_option('tt_site_facebook')) { ?>
                <a class="fts-facebook" href="<?php echo 'https://www.facebook.com/' . $facebook; ?>" target="_blank">
                    <i class="tico tico-facebook"></i>
                  </a>
                <?php } ?>
                <?php if($twitter = tt_get_option('tt_site_twitter')) { ?>
                    <a class="fts-twitter" href="<?php echo 'https://www.twitter.com/' . $twitter; ?>" target="_blank">
                    <i class="tico tico-twitter"></i>
                    </a>
                <?php } ?>
                <?php if($qq = tt_get_option('tt_site_qq')) { ?>
                    <a class="fts-qq" href="<?php echo 'https://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">
                    <i class="tico tico-qq"></i>
                    </a>
                <?php } ?>
                <?php if($qq_group = tt_get_option('tt_site_qq_group')) { ?>
                    <a class="fts-qq" href="<?php echo 'https://shang.qq.com/wpa/qunwpa?idkey=' . $qq_group; ?>" target="_blank">
                    <i class="tico tico-users2"></i>
                    </a>
                <?php } ?>
                <?php if($weibo = tt_get_option('tt_site_weibo')) { ?>
                    <a class="fts-twitter" href="<?php echo 'https://www.weibo.com/' . $weibo; ?>" target="_blank">
                    <i class="tico tico-weibo"></i>
                    </a>
                <?php } ?>
                <?php if($weixin = tt_get_option('tt_site_weixin')) { ?>
                    <a class="fts-weixin" href="javascript:void(0)" rel="weixin-qr" target="_blank">
                    <i class="tico tico-weixin"></i>
                    </a>
                <?php } ?>
                <?php if($qq_mailme = tt_get_option('tt_mailme_id')) { ?>
                    <a class="fts-email" href="<?php echo 'https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=' . $qq_mailme; ?>" target="_blank">
                    <i class="tico tico-envelope"></i>
                    </a>
                <?php } ?>
                <a class="fts-rss" href="<?php bloginfo('rss2_url'); ?>" target="_blank">
                    <i class="tico tico-rss"></i>
                </a>
                </div>
              <div class="footer-copy">
				&copy;&nbsp;<?php echo tt_copyright_year(); ?>&nbsp;&nbsp;<?php echo ' ' . get_bloginfo('name') . ' All Right Reserved '; ?>
					<?php if($beian = tt_get_option('tt_beian')){
                    echo '·&nbsp;<a href="https://www.miitbeian.gov.cn/" rel="link" target="_blank">' . $beian . '</a>';
                } ?>
					 <?php echo '·&nbsp;<b style="color: #ff4425;">♥</b>&nbsp;Theme By <a href="https://bbcatga.herokuapp.com" title="BBcat-K" rel="link" target="_blank">BBcat-K</a> & Design By <a href="https://bbcatga.herokuapp.com/" title="洛茛艺术" rel="link" target="_blank">洛茛艺术.</a>'; ?>
                <?php if(tt_get_option('tt_show_queries_num', false)) printf(__(' ·&nbsp;%1$s queries in %2$ss', 'tt'), get_num_queries(), timer_stop(0)); ?>
			</div>
            </div>
		</div>
	</div>
 </div>
</footer>
<?php }else{ ?>
<footer class="footer">
    <!--div class="footer-before"><img src="<?php echo THEME_ASSET . '/img/colorful-line.png'; ?>" ></div-->
    <div class="footer-wrap">
        <!-- 页脚小工具区 -->
        <div class="footer-widgets">

        </div>
        <!-- 页脚菜单/版权信息 IDC No. -->
        <div class="footer-nav" style="padding: 20px 0!important;text-align: center!important;">
            <div class="footer-nav-links" style="display: block;">
                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'menu_class' => 'footer-menu', 'depth' => '1', 'fallback_cb' => 'header-menu'  ) ); ?>
            </div>
            <div class="footer-shares">
                <?php if($facebook = tt_get_option('tt_site_facebook')) { ?>
                <a class="fts-facebook" href="<?php echo 'https://www.facebook.com/' . $facebook; ?>" target="_blank">
                    <span class="tico tico-facebook">
                      <span class="se-icon tico tico-facebook"></span>
                    </span>
                </a>
                <?php } ?>
                <?php if($twitter = tt_get_option('tt_site_twitter')) { ?>
                    <a class="fts-twitter" href="<?php echo 'https://www.twitter.com/' . $twitter; ?>" target="_blank">
                    <span class="tico tico-twitter">
                      <span class="se-icon tico tico-twitter"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($qq = tt_get_option('tt_site_qq')) { ?>
                    <a class="fts-qq" href="<?php echo 'https://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">
                    <span class="tico tico-qq">
                      <span class="se-icon tico tico-qq"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($qq_group = tt_get_option('tt_site_qq_group')) { ?>
                    <a class="fts-qq" href="<?php echo 'https://shang.qq.com/wpa/qunwpa?idkey=' . $qq_group; ?>" target="_blank">
                    <span class="tico tico-users2">
                      <span class="se-icon tico tico-users2"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($weibo = tt_get_option('tt_site_weibo')) { ?>
                    <a class="fts-twitter" href="<?php echo 'https://www.weibo.com/' . $weibo; ?>" target="_blank">
                    <span class="tico tico-weibo">
                      <span class="se-icon tico tico-weibo"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($weixin = tt_get_option('tt_site_weixin')) { ?>
                    <a class="fts-weixin" href="javascript:void(0)" rel="weixin-qr" target="_blank">
                    <span class="tico tico-weixin">
                      <span class="se-icon tico tico-weixin"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($qq_mailme = tt_get_option('tt_mailme_id')) { ?>
                    <a class="fts-email" href="<?php echo 'https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=' . $qq_mailme; ?>" target="_blank">
                    <span class="tico tico-envelope">
                      <span class="se-icon tico tico-envelope"></span>
                    </span>
                    </a>
                <?php } ?>
                <a class="fts-rss" href="<?php bloginfo('rss2_url'); ?>" target="_blank">
                    <span class="tico tico-rss">
                      <span class="se-icon tico tico-rss"></span>
                    </span>
                </a>

            </div>
            <div class="footer-copy">
                &copy;&nbsp;<?php echo tt_copyright_year(); ?><?php echo ' ' . get_bloginfo('name') . ' All Right Reserved '; ?>
                <?php if($beian = tt_get_option('tt_beian')){
                    echo '·&nbsp;<a href="https://www.miitbeian.gov.cn/" rel="link" target="_blank">' . $beian . '</a>';
                } ?>
                <?php echo '·&nbsp;<b style="color: #ff4425;">♥</b>&nbsp;Theme By <a href="https://bbcatga.herokuapp.com" title="BBcat-K" rel="link" target="_blank">BBcat-K</a> & Design By <a href="https://bbcatga.herokuapp.com/" title="洛茛艺术" rel="link" target="_blank">洛茛艺术.</a>'; ?>
                <?php if(tt_get_option('tt_show_queries_num', false)) printf(__(' ·&nbsp;%1$s queries in %2$ss', 'tt'), get_num_queries(), timer_stop(0)); ?>
            </div>
        </div>
    </div>
</footer>
<?php } ?>
<?php load_mod('mod.FixedControls'); ?>
<?php load_mod('mod.ModalSearch'); ?>
<?php if(is_author() && current_user_can('edit_users'))load_mod('mod.ModalBanBox'); ?>
<?php if(is_home() || is_single() || is_author()){
    load_mod('mod.ModalPmBox');
    do_action('tt_ref'); // 推广检查的钩子
} ?>
<?php if(!is_user_logged_in()) load_mod('mod.ModalLoginForm'); ?>
<!-- 页脚自定义代码 -->
<?php if (tt_get_option('tt_enable_k_bkpfdh', true)) { ?>
<!-- 版块动画特效JS -->
<?php if( wp_is_mobile() ) { ?>
<script type="text/javascript" src="<?php echo THEME_ASSET.'/js/custom-m.js'; ?>"></script>
<?php }else{ ?>
<script type="text/javascript" src="<?php echo THEME_ASSET.'/js/custom.js'; ?>"></script>
<?php } ?>
<script>POWERMODE.colorful = true;POWERMODE.shake = false;document.body.addEventListener('input', POWERMODE);</script>
<?php } ?>
<?php if(tt_get_option('tt_foot_code')) { echo tt_get_option('tt_foot_code'); } ?>
<?php wp_footer(); ?>
<!--<?php echo get_num_queries();?> queries in <?php timer_stop(1); ?> seconds.-->
</body>
</html>