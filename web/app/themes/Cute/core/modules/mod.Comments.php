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
global $postdata;
$vm = PostCommentsVM::getInstance($postdata->ID);
if($vm->isCache && $vm->cacheTime) { ?>
    <!-- Comments cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $comment_list = $vm->modelData->list_html;
      if(!current_user_can('edit_users')){
      $pattern = '/<a(.*?)href=(.*?)class="name replyName"(.*?)>(.*?)</i';
      $comment_lists=preg_match_all($pattern, $comment_list, $matches);
      foreach ($matches[4] as $key=>$value) {
      $email_name = explode('@', $matches[4][$key]); 
      $email_name = strpos($matches[4][$key],'@') != false ? substr_replace($email_name[0],'**',-2,2).'@'.$email_name[1]:$matches[4][$key];
      $a = '<a'.$matches[1][$key].'href='.$matches[2][$key].'class="name replyName"'.$matches[3][$key].'>'.$matches[4][$key].'<';
      $b = '<a'.$matches[1][$key].'href='.$matches[2][$key].'class="name replyName"'.$matches[3][$key].'>'.$email_name.'<';
      $comment_list = str_replace($a, $b, $comment_list);
       }
      $pattern = '/moveForm\((.*?)\)/i';
      $comment_lists=preg_match_all($pattern, $comment_list, $matches);
      foreach ($matches[1] as $key=>$value) {
      $email_name = explode('@', $matches[1][$key]); 
      $email_name = strpos($matches[1][$key],'@') != false ? substr_replace($email_name[0],'**',-2,2).'@'.$email_name[1]:$matches[1][$key];
      $a = 'moveForm('.$matches[1][$key].')';
      $b = 'moveForm('.$email_name.')';
      $comment_list = str_replace($a, $b, $comment_list);
       }
     }
?>
<div id="comments-wrap">
    <ul class="comments-list">
        <input type="hidden" id="comment_star_nonce" name="tt_comment_star_nonce" value="<?php echo wp_create_nonce('tt_comment_star_nonce'); ?>">
        <?php echo $comment_list; ?>
        <div class="pages"><?php //paginate_comments_links('prev_text=«&next_text=»&type=list'); ?></div>
    </ul>
    <?php if($vm->modelData->list_count > 0){ ?>
    <div class="load-more"><button class="btn btn-primary btn-wide btn-more"><?php _e('Load More Comments', 'tt'); ?></button></div>
    <?php } ?>
    <div class="err text-primary text-center h3"></div>
</div>