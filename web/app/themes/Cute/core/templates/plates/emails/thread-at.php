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
<?php $this->layout('base', array('blogName' => get_bloginfo('name'), 'logo' => tt_get_option('tt_logo'), 'home' => home_url(), 'shopHome' => tt_url_for('shop_archive'))) ?>

<style>
    img{max-width:100%;}
</style>
<p><?=$this->e($commentAuthor)?>在帖子<a href="<?=$this->e($commentLink)?>" target="_blank"><?=$this->e($postTitle)?></a>中@了你，快去看看吧：<br></p>
<p style="padding:10px 15px;background-color:#f4f4f4;margin-top:10px;color:#000;border-radius:3px;"><?=$this->e($commentContent)?></p>