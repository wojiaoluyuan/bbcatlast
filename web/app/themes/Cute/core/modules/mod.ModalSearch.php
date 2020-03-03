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
<!-- 搜索模态框 -->
<div id="globalSearch" class="js-search search-form search-form-modal fadeZoomIn" role="dialog" aria-hidden="true">
    <form method="get" action="<?php echo home_url(); ?>" role="search">
        <div class="search-form-inner">
            <div class="search-form-box">
                <input class="form-search" type="text" name="s" placeholder="<?php _e('Type a keyword', 'tt'); ?>">
            </div>
        </div>
    </form>
</div>