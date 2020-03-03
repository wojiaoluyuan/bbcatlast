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
<div class="embed-post">
  <img src="<?=$this->e($thumb)?>">
  <div class="post-info">
    <h4>
      <a href="<?=$this->e($post_link)?>" target="_blank"><?=$this->e($post_title)?></a></h4>
    <div class="post-excerpt"><?=$this->e($description)?></div>
  </div>
  <div class="entry-meta entry-meta-1">
    <span class="author vcard">
      <i class="tico tico-user"></i>
      <a class="url" href="<?=$this->e($author_url)?>" target="_blank"><?=$this->e($author)?></a></span>
    <span class="entry-date text-muted">
      <i class="tico tico-alarm"></i>
      <time class="entry-date" datetime="<?=$this->e($datetime)?>" title="<?=$this->e($datetime)?>"><?=$this->e($time)?></time></span>
    <span class="views-count text-muted">
      <i class="tico tico-folder-open-o"></i>
      <?php echo $category; ?></span>
    <span class="views-count text-muted">
      <i class="tico tico-eye"></i><?=$this->e($views)?></span>
    <span class="comments-link text-muted">
      <i class="tico tico-comments-o"></i>
      <a href="<?=$this->e($post_link)?>#respond" target="_blank"><?=$this->e($comment_count)?></a></span>
    <span class="read-more">
      <a href="<?=$this->e($post_link)?>" target="_blank">阅读全文
        <i class="tico tico-sign-in"></i></a>
    </span>
  </div>
</div>