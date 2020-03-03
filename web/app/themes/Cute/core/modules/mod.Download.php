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
<?php global $origin_post;global $current_user; get_currentuserinfo(); ?>
<?php
    $free_dls = trim(get_post_meta($origin_post->ID, 'tt_free_dl', true));
    $free_dls = !empty($free_dls) ? explode(',', str_replace(PHP_EOL, ',', $free_dls)) : array();
    $sale_dls2 = tt_get_post_sale_resources($origin_post->ID);
    $member = new Member($current_user->ID);
    $monthly_vip_free_down = tt_get_option('tt_enable_monthly_vip_free_down');
    $annual_vip_free_down = tt_get_option('tt_enable_annual_vip_free_down');
    $permanent_vip_free_down = tt_get_option('tt_enable_permanent_vip_free_down');
    $donate_option = tt_get_option('tt_enable_k_donate', false);
    $active_time = tt_get_option('tt_k_donate_active_time');
    $active_price = tt_get_option('tt_k_donate_price');
    $donate_order = get_user_meta($current_user->ID,'donate_order',true);
    if(isset($_COOKIE['donate_order'])){
      $order_id = tt_decrypt($_COOKIE['donate_order'], tt_get_option('tt_private_token'));
      $order = tt_get_order($order_id);
      $success_time = strtotime($order->order_success_time);
    }elseif(is_user_logged_in() && $donate_order){
      $order_id = tt_decrypt($donate_order, tt_get_option('tt_private_token'));
      $order = tt_get_order($order_id);
      $success_time = strtotime($order->order_success_time);
    }
    $tt_monthly_vip_down_count = tt_get_option('tt_monthly_vip_down_count');
    $tt_annual_vip_down_count = tt_get_option('tt_annual_vip_down_count');
    $tt_permanent_vip_down_count = tt_get_option('tt_permanent_vip_down_count');
    $vip_down_count = (int) get_user_meta($current_user->ID, 'tt_vip_down_count', true);
?>
<div id="main" class="main primary col-md-8 download-box" role="main">
  <nav class="bbcat-breadcrumb">
                        <a href="<?php echo home_url(); ?>"><i class="tico tico-home"></i><?php _e('HOME', 'tt'); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        <?php echo get_the_category_list(' ', '', $origin_post->ID); ?>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        <a href="<?php echo get_permalink($origin_post); ?>"><?php echo get_the_title($origin_post); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        资源下载
                    </nav>
    <div class="download">
        <div class="dl-declaration contextual-callout callout-warning">
            <p>本站所刊载内容均为网络上收集整理，包括但不限于代码、应用程序、影音资源、电子书籍资料等，并且以研究交流为目的，所有仅供大家参考、学习，不存在任何商业目的与商业用途。若您使用开源的软件代码，请遵守相应的开源许可规范和精神，
              若您需要使用非免费的软件或服务，您应当购买正版授权并合法使用。如果你下载此文件，表示您同意只将此文件用于参考、学习使用而非任何其他用途。</p><br/>
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;如果下载本资源积分或余额不足，可点击这里<strong><a href="/me/credits" rel="nofollow" title="充值积分" rel="link" target="_blank">充值积分</a><a href="/me/cash" rel="nofollow" title="充值余额" rel="link" target="_blank">充值余额</a></strong></p> 
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;当前积分兑换比率为：<strong><?php printf(__('100 积分 = %d 元', 'tt'), tt_get_option('tt_hundred_credit_price', 1)); ?></strong></p>
          <?php if(is_user_logged_in()) { ?>
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;也可以在这里通过推广连接免费获取积分.</p> 
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;你的专属推广链接：【&nbsp;<strong><?php echo home_url('?ref=') . $current_user->ID . "\n";?></strong>&nbsp;】</p>
          <?php } ?>
        </div>
        <?php load_mod(('banners/bn.Download.Top')); ?>
        <div class="dl-detail">
        <?php if(count($free_dls)) { ?>
            <h2><?php _e('Free Resources', 'tt'); ?></h2>
            <?php if ($donate_option && (time()-$success_time)/3600 > $active_time && !in_array($member->vip_type, array(1, 2, 3))) { ?>
            <p><a class="btn-donate" href="<?php echo tt_url_for('paydonate'); ?>">捐赠<?php echo $active_price; ?>元</a>解锁网站所有免费资源的<?php echo $active_time; ?>小时下载权限！</p>
            <div class="donte-tips" style="padding-top: 20px;color: #34495e;">
            <p>为什么要捐赠？</p>
            <p>捐赠费用仅用于支持网站服务器的运行，域名的续费，以便提供更多的优质免费资源！</p>
            <p>注意事项：建议注册帐号登录后捐赠以防cookie丢失导致权限失效，未登录游客将以cookie方式保存权限</p> </div>
            <?php } else { ?>
            <ul class="free-resources">
            <?php $seq = 0; foreach ($free_dls as $free_dl) { ?>
                <?php $free_dl = explode('|', $free_dl); ?>
                <?php if(count($free_dl) < 2) {continue;}else{ $seq++; ?>
                <li>
                    <?php if(isset($free_dl[2])) { ?>
                    <?php echo sprintf(__('%d. %2$s <a href="%3$s" target="_blank"><button class="itemCopy btn btn-download" data-clipboard-text="%4$s">下载地址</button></a> (密码: %4$s) 提示：点击下载地址自动复制密码', 'tt'), $seq, $free_dl[0], tt_links_to_internal_links($free_dl[1]), isset($free_dl[2]) ? $free_dl[2] : __('None', 'tt')); ?>
                    <?php } else { ?>
                    <?php echo sprintf(__('%d. %2$s <a class="btn btn-download" href="%3$s" target="_blank">下载地址</a> (密码: %4$s)', 'tt'), $seq, $free_dl[0], $free_dl[1], isset($free_dl[2]) ? $free_dl[2] : __('None', 'tt')); ?>
                    <?php } ?>
                </li>
                <?php } ?>
            <?php } ?>
            </ul>
            <?php if ($donate_option && (time()-$success_time)/3600 < $active_time){ ?>
            <div class="donte-tips" style="padding-top: 20px;color: #34495e;">
            <p>捐赠下载权限到期时间：<?php echo date('Y-m-d H:i:s',$success_time+($active_time*3600)) ?></p>
            </div>
            <?php } ?>
            <?php } ?>
        <?php } ?>
        <?php if(count($sale_dls2)) { ?>
            <h2><?php _e('Sale Resources', 'tt'); ?></h2>
            <?php if (is_user_logged_in()) { ?>
                <ul class="sale-resources">
                    <?php foreach ($sale_dls2 as $sale_dl) { ?>
                        <li>
                            <!-- 资源名称|资源下载url1_密码1,资源下载url2_密码2|资源价格|币种 -->
                            <?php if(tt_check_bought_post_resources2($origin_post->ID, $sale_dl['seq']) || ($member->vip_type == 1 && $monthly_vip_free_down) || ($member->vip_type == 2 && $annual_vip_free_down) || ($member->vip_type == 3 && $permanent_vip_free_down)) { ?>
                                <?php echo sprintf(__('%d. %2$s ', 'tt'), $sale_dl['seq'], $sale_dl['name']); ?>
                                <?php $pans = $sale_dl['downloads'];
                                $pan_seq = 0;
                                foreach ($pans as $pan) {
                                    $pan_seq++;
                                    if(isset($pan['password'])) {
                                    echo sprintf(__('<a href="%1$s" target="_blank"><button class="itemCopy btn btn-download" data-clipboard-text="%3$s">下载地址%2$d</button></a> (密码: %3$s)', 'tt'), tt_links_to_internal_links($pan['url']), $pan_seq, isset($pan['password']) ? $pan['password'] : __('None', 'tt'));
                                    }else{
                                    echo sprintf(__('<a class="btn btn-download" href="%1$s" target="_blank">下载地址%2$d</a> (密码: %3$s)', 'tt'), $pan['url'], $pan_seq, isset($pan['password']) ? $pan['password'] : __('None', 'tt')); 
                                    }
                                }
                                ?>
                            <?php }else{ ?>
                                <?php if(($member->vip_type == 1 && $tt_monthly_vip_down_count > 0 && $vip_down_count >= $tt_monthly_vip_down_count) || ($member->vip_type == 2 && $tt_annual_vip_down_count > 0 && $vip_down_count >= $tt_annual_vip_down_count) || ($member->vip_type == 3 && $tt_permanent_vip_down_count > 0 && $vip_down_count >= $tt_permanent_vip_down_count)){ ?>
                                <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, $sale_dl['price'], $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                                <p>当日会员优惠下载次数已达到限制，恢复原价，次日重置优惠下载次数</p>
                             <?php }elseif(in_array($member->vip_type, array(1, 2, 3))) { ?>
                                <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (会员专享价：%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, tt_get_specified_user_post_price($sale_dl['price'],$sale_dl['currency']), $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                             <?php }else{ ?>
                            <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, $sale_dl['price'], $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                          <?php } ?>
                          <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p>此付费资源需要<a class="btn btn-login" href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>"><i class="tico tico-sign-in"></i>登录</a>才能购买与查看</p>
            <?php } ?>
        <?php } ?>
        </div>
        <div class="tt-gg"></div>
        <div class="dl-help contextual-bg bg-info">
            <p><?php _e('如果您发现本文件已经失效不能下载，请联系站长修正！', 'tt'); ?></p>
            <p><?php _e('本站提供的资源多数为百度网盘下载，对于大文件，你需要安装百度云客户端才能下载！', 'tt'); ?></p>
            <p><?php _e('部分文件引用的官方或者非网盘类他站下载链接，你可能需要使用迅雷、BT等下载工具下载！', 'tt'); ?></p>
            <p><?php _e('本站推荐的资源均经由站长检测或者个人发布，不包含恶意软件病毒代码等，如果你发现此类问题，请向站长举报！', 'tt'); ?></p>
            <p><?php _e('本站仅提供文件的下载服务，除本站原创产品及特别注明的产品外，其他免费及付费资源均只提供简单的售后！', 'tt'); ?></p>
            <p><?php _e('由于本站资源大多来源于互联网，非原创及特别注明的产品外，如下载资源存在BUG以及其他任何问题，请自行解决，本站无需负责！', 'tt'); ?></p>
        </div>
    </div>
    <?php load_mod(('banners/bn.Download.Bottom')); ?>
</div>