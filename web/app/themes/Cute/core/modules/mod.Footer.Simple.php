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
<!-- Footer -->
<footer class="footer simple-footer" style="text-align: center; ">
    <div class="foot-copyright">&copy;&nbsp;<?php echo tt_copyright_year(); ?><?php echo ' ' . get_bloginfo('name') . ' All Right Reserved · <b style="color: #ff4425;">♥</b>&nbsp;<a href="https://bbcatga.herokuapp.com/" title="BBCat" rel="link" target="_blank">BBCat</a> & Design by <a href="https://bbcatga.herokuapp.com/" rel="link" title="哔哔猫">哔哔猫.</a>'; ?>
    </div>
</footer>
<?php if (tt_get_option('tt_enable_k_bkpfdh', true)) { ?>
<!-- 版块动画特效JS -->
<?php if( wp_is_mobile() ) { ?>
<script type="text/javascript" src="<?php echo THEME_CDN_ASSET.'/js/app-m.js'; ?>"></script>
<?php }else{ ?>
<script type="text/javascript" src="<?php echo THEME_CDN_ASSET.'/js/app.js'; ?>"></script>
<?php } ?>
<script>POWERMODE.colorful = true;POWERMODE.shake = false;document.body.addEventListener('input', POWERMODE);</script>
<?php } ?>
<?php if(tt_get_option('tt_foot_code')) { echo tt_get_option('tt_foot_code'); } ?>
<?php wp_footer(); ?>
<!--<?php echo get_num_queries();?> queries in <?php timer_stop(1); ?> seconds.-->
</body>
</html>