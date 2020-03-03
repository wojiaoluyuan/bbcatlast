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
<?php if (tt_get_option('tt_cms_home_style') == 'tinection'): ?>
    <?php load_tpl('tpl.CmsHome'); ?>
<?php else: ?>
    <?php load_tpl('tpl.NewHome'); ?>
<?php endif; ?>