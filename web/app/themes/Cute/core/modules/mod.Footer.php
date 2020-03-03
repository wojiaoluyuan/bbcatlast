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
<?php if(tt_get_option('home_footer_btn_is')) { ?>
<section id="home-features" class="wrapper">
<div class="cta-large shift-bottom">
    <div class="cta-large__inner container">
        <div class="cta-large__text">
            <img src="<?php echo tt_get_option('home_footer_img', THEME_ASSET . '/img/logo-dark.png'); ?>" alt="">
            <h1><?php echo tt_get_option('home_footer_title');?></h1>
            <h2><?php echo tt_get_option('home_footer_desc');?></h2>
        </div>
        <a class="cta-large__button" href="<?php echo tt_get_option('home_footer_btn_href');?>"><?php echo tt_get_option('home_footer_btn_name');?></a>
    </div>
</div>
</section>
<?php } ?>
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
						<a href="/about"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>关于我们</a></li>
					<li>
						<a href="/guestbook"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>联系留言</a></li>
					<li>
						<a href="/site/privacy-policies-and-terms"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>版权声明</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">常见问题</li>
					<li>
						<a href="/gmlc"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>购买流程</a></li>
					<li>
						<a href="/zffs"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>支付方式</a></li>
					<li>
						<a href="/shfw"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>售后服务</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">合作伙伴</li>
					<li>
						<a href="/tgyj"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>投稿有奖</a></li>
					<li>
						<a href="/business"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>广告合作</a></li>
					<li>
						<a href="/links"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>友情链接</a></li>
				</ul>
				<ul class="pull-left mr95">
					<li class="fs16" style="padding: 0px;">解决方案</li>
					<li>
						<a href="/ztxg"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>主题修改</a></li>
					<li>
						<a href="/azts"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>安装调试</a></li>
					<li>
						<a href="/hjdj"<?php echo tt_get_option('tt_enable_k_blank', false) ?  ' target="_blank"' : ''; ?>>环境搭建</a></li>
				</ul>
				<ul class="pull-left ml20 mr20">
					<li class="fs16" style="padding: 0px;">官方微信</li>
					<li>
						<a>
							<img class="kuangimg" alt="哔哔猫" src="<?php echo tt_get_option('tt_custom_footer_weixin'); ?>"></a>
					</li>
				</ul>
				<ul class="pull-left ml20 mr20">
					<li class="fs16" style="padding: 0px;">官方支付宝</li>
					<li>
						<a>
							<img class="kuangimg" alt="哔哔猫" src="<?php echo tt_get_option('tt_custom_footer_alipay'); ?>"></a>
					</li>
				</ul>
			</div>
			<div class="col-contact">
				<p class="phone"><?php echo get_bloginfo('name'); ?></p>
				<p>
					<span class="J_serviceTime-normal">周一至周日 10:00-24:00</span>
					<br>（其他时间勿扰）</p>
                <?php $qq = tt_get_option('tt_site_qq')  ?>
				<a rel="nofollow" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">在线咨询</a></div>
		</div>
	</div>
    <div class="footer-nav friendlink container">
                                <ul>
                                <?php $bookmarks = get_bookmarks(array(
                                        'orderby' => 'rating',
                                        'order' => 'asc'
                                    ));
                                foreach ($bookmarks as $bookmark) {
                                ?>
                                    <li><a href="<?php echo $bookmark->link_url; ?>"><?php echo $bookmark->link_name; ?></a></li>
                                <?php }
                                ?>
                                </ul>
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
                    <a class="fts-qq" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">
                    <i class="tico tico-qq"></i>
                    </a>
                <?php } ?>
                <?php if($qq_group = tt_get_option('tt_site_qq_group')) { ?>
                    <a class="fts-qq" href="<?php echo 'http://shang.qq.com/wpa/qunwpa?idkey=' . $qq_group; ?>" target="_blank">
                    <i class="tico tico-users2"></i>
                    </a>
                <?php } ?>
                <?php if($weibo = tt_get_option('tt_site_weibo')) { ?>
                    <a class="fts-twitter" href="<?php echo 'http://www.weibo.com/' . $weibo; ?>" target="_blank">
                    <i class="tico tico-weibo"></i>
                    </a>
                <?php } ?>
                <?php if($weixin = tt_get_option('tt_site_weixin')) { ?>
                    <a class="fts-weixin" href="javascript:void(0)" rel="weixin-qr" target="_blank">
                    <i class="tico tico-weixin"></i>
                    </a>
                <?php } ?>
                <?php if($qq_mailme = tt_get_option('tt_mailme_id')) { ?>
                    <a class="fts-email" href="<?php echo 'http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=' . $qq_mailme; ?>" target="_blank">
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
                    echo '·&nbsp;<a href="http://www.miitbeian.gov.cn/" rel="link" target="_blank">' . $beian . '</a>';
                } ?>
					 <?php echo '·&nbsp;<b style="color: #ff4425;">♥</b>&nbsp;Theme By <a href="https://bbcatga.herokuapp.com" title="BBCat" rel="link" target="_blank">BBCat</a> & Design By <a href="https://bbcatga.herokuapp.com/" title="哔哔猫" rel="link" target="_blank">哔哔猫.</a>'; ?>
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
                    <a class="fts-qq" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">
                    <span class="tico tico-qq">
                      <span class="se-icon tico tico-qq"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($qq_group = tt_get_option('tt_site_qq_group')) { ?>
                    <a class="fts-qq" href="<?php echo 'http://shang.qq.com/wpa/qunwpa?idkey=' . $qq_group; ?>" target="_blank">
                    <span class="tico tico-users2">
                      <span class="se-icon tico tico-users2"></span>
                    </span>
                    </a>
                <?php } ?>
                <?php if($weibo = tt_get_option('tt_site_weibo')) { ?>
                    <a class="fts-twitter" href="<?php echo 'http://www.weibo.com/' . $weibo; ?>" target="_blank">
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
                    <a class="fts-email" href="<?php echo 'http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=' . $qq_mailme; ?>" target="_blank">
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
                    echo '·&nbsp;<a href="http://www.miitbeian.gov.cn/" rel="link" target="_blank">' . $beian . '</a>';
                } ?>
                <?php echo '·&nbsp;<b style="color: #ff4425;">♥</b>&nbsp;Theme By <a href="https://bbcatga.herokuapp.com" title="BBCat" rel="link" target="_blank">BBCat</a> & Design By <a href="https://bbcatga.herokuapp.com/" title="哔哔猫" rel="link" target="_blank">哔哔猫.</a>'; ?>
                <?php if(tt_get_option('tt_show_queries_num', false)) printf(__(' ·&nbsp;%1$s queries in %2$ss', 'tt'), get_num_queries(), timer_stop(0)); ?>
            </div>
        </div>
    </div>
</footer>
<?php } ?>
<!-- 侧边栏遮罩 -->
<div class="page__wrapper__overlay"></div>

<?php load_mod('mod.FixedControls'); ?>
<?php if(wp_is_mobile())load_mod('mod.ModalSearch'); ?>
<?php if(is_author() && current_user_can('edit_users'))load_mod('mod.ModalBanBox'); ?>
<?php if((is_home() || is_single() || is_author()) && get_post_type() != 'thread'){
    load_mod('mod.ModalPmBox');
    do_action('tt_ref'); // 推广检查的钩子
} ?>

<!-- 弹窗登录 -->
<?php if (!is_user_logged_in() && tt_get_option('tt_is_modloginform', true) ) { load_mod('mod.ModalLoginForm'); } ?>

</div><!-- 主页面End -->

<!-- 全局页面右侧展开模块 -->
<?php load_mod('mod.RightNav'); ?>
<!-- 全站js -->
<script type="text/javascript" src="<?php echo THEME_CDN_ASSET.'/js/owl.carousel.min.js'; ?>"></script>
<?php if (tt_get_option('tt_enable_k_bkpfdh', true)) { ?>
<!-- 个性化JS -->
<?php if( wp_is_mobile() ) { ?>
<script type="text/javascript" src="<?php echo THEME_CDN_ASSET.'/js/app-m.js'; ?>"></script>
<?php }else{ ?>
<script type="text/javascript" src="<?php echo THEME_CDN_ASSET.'/js/app.js'; ?>"></script>
<?php } ?>
<script>POWERMODE.colorful = true;POWERMODE.shake = false;document.body.addEventListener('input', POWERMODE);</script>
<?php } ?>
<?php if(tt_get_option('tt_foot_code')) { echo tt_get_option('tt_foot_code'); } ?>
<?php wp_footer(); ?>
<script>if( TT.isHome != 0 ){$('.nt-slider').owlCarousel({items:1,loop:true,nav:true,smartSpeed:1200,autoplay:true,autoplayTimeout:5000,autoplayHoverPause:true,navText:['<i class="tico tico-angle-double-left"></i>','<i class="tico tico-angle-double-right"></i>'],responsive:{0:{nav:false,},992:{nav:true,}}})};</script>
<?php if (is_single()) { ?>
<script>$(document).on('click','.buy-content',function(){var buy=$('.buy-content');n=parseInt(buy[["data"]]("post-id")),r=parseInt(buy[["data"]]("resource-seq"));c={_wpnonce:TT._wpnonce,postId:n,resourceSeq:r,newType:true};w=function(t,e){var o=$("#fullLoader-container");if(o[["length"]]){o[["remove"]]()}else $('<div id="fullLoader-container"><div class="box"><div class="loader"><i class="tico '+t+' spinning"></i></div><p>'+e+"</p></div></div>")[["appendTo"]]("body")[["fadeIn"]]()},w("tico-spinner2","正在请求中...");$.ajax({url:"\/api\/v1\/users\/boughtresources",type:'POST',dataType:'json',data:c,}).done(function(data){if(data.success){if(data[["data"]][["url"]]==null){b="\/me\/order\/",b+=data[["data"]][["insert_id"]];window.location.href=b}else{window.location.href=data[["data"]][["url"]]}}}).fail(function(t,e,o){w(),App.PopMsgbox.error({title:t[["responseJSON"]]?t[["responseJSON"]][["message"]]:t[["responseText"]],timer:2e3,showConfirmButton:!0})})});</script>
<?php } ?>
<!--<?php echo get_num_queries();?> queries in <?php timer_stop(1); ?> seconds.-->
</body>
</html>