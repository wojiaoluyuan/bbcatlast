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
<!-- Footer -->
<footer class="footer simple-footer">
    <div class="foot-copyright">&copy;&nbsp;<?php echo tt_copyright_year(); ?><?php echo ' ' . get_bloginfo('name') . ' All Right Reserved · <b style="color: #ff4425;">♥</b>&nbsp;<a href="https://bbcatga.herokuapp.com/" title="RogenRu" rel="link" target="_blank">RogenRu</a> & Design by <a href="https://bbcatga.herokuapp.com/" rel="link" title="洛茛艺术">洛茛艺术.</a>'; ?>
    </div>
</footer>
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