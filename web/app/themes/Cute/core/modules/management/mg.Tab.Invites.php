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
<?php global $tt_mg_vars; $tt_user_id = $tt_mg_vars['tt_user_id']; $tt_page = $tt_mg_vars['tt_paged']; ?>
<div class="col col-right coupons">
    <?php $vm = MgInvitesVM::getInstance($tt_page); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Manage invites cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php $data = $vm->modelData; $invites = $data->invites; $count = $data->count; $max_pages = $data->max_pages; ?>
    <div class="mg-tab-box invites-tab">
        <div class="tab-content">
            <!-- 添加邀请码 -->
            <section class="mg-invite clearfix">
                <header><h2>添加邀请码</h2></header>
                <div class="form-group info-group clearfix">
                    <div class="invite-radios">
                        邀请码类型
                        <label class="radio-inline" style="margin-left: 10px;">
                            <input type="radio" name="invite_type" value="once" checked><?php _e('ONCE COUPON', 'tt'); ?>
                        </label>
                        <label class="radio-inline" style="margin-left: 10px;">
                            <input type="radio" name="invite_type" value="multi"><?php _e('MULTI COUPON', 'tt'); ?>
                        </label>
                    </div>
                </div>
                <div class="form-group info-group clearfix">
                    <p class="help-block">可自定义填写邀请码或者直接填写数量批量生成（批量生成最大同时生成100个）</p>
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="input-group active">
                                <div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;">邀请码/数量</div>
                                <input class="form-control" type="text" name="invite_code" value="" aria-required="true" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="input-group active">
                                <div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;"><?php _e('Effect Date', 'tt'); ?></div>
                                <input class="form-control" type="datetime-local" name="effect_date" value="<?php echo (new DateTime())->format('Y-m-d\TH:i:s'); ?>" aria-required="true" required>
                            </div>
                            <div class="input-group active">
                                <div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;"><?php _e('Expire Date', 'tt'); ?></div>
                                <input class="form-control" type="datetime-local" name="expire_date" value="" aria-required="true" required>
                            </div>
                        </div>
                        <button class="btn btn-inverse" type="submit" id="add-invite"><?php _e('ADD', 'tt'); ?></button>
                    </div>
                </div>
            </section>
            <!-- 优惠码列表 -->
            <section class="mg-invites clearfix">
                <header><h2>邀请码列表</h2></header>
                <?php if($count > 0) { ?>
                    <div class="table-wrapper">
                        <table class="table table-striped table-framed table-centered">
                            <thead>
                            <tr>
                                <th class="th-cid"><?php _e('Coupon Sequence', 'tt'); ?></th>
                                <th class="th-code">邀请码</th>
                                <th class="th-type"><?php _e('Coupon Type', 'tt'); ?></th>
                                <th class="th-status"><?php _e('Coupon Status', 'tt'); ?></th>
                                <th class="th-effect"><?php _e('Coupon Effect Date', 'tt'); ?></th>
                                <th class="th-expire"><?php _e('Coupon Expire Date', 'tt'); ?></th>
                                <th class="th-actions"><?php _e('Actions', 'tt'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $seq = 0; ?>
                            <?php foreach ($invites as $invite){ ?>
                                <?php $seq++; ?>
                                <tr id="cid-<?php echo $invite->id; ?>">
                                    <td><?php echo $seq; ?></td>
                                    <td><?php echo $invite->invite_code; ?></td>
                                    <td><?php if($invite->invite_type !== 'multi'){_e('ONCE COUPON', 'tt');}else{_e('MULTI COUPON', 'tt');} ?></td>
                                    <td><?php if($invite->invite_status == 1){_e('Not Used', 'tt');}else{_e('Used', 'tt');} ?></td>
                                    <td><?php echo $invite->begin_date ?></td>
                                    <td><?php echo $invite->expire_date ?></td>
                                    <td>
                                        <div class="coupon-actions">
                                            <a class="delete-invite" href="javascript:;" data-invite-action="delete" data-invite-id="<?php echo $invite->id; ?>" title="删除邀请码">删除</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($max_pages > 1) { ?>
                        <div class="pagination-mini clearfix">
                            <?php if($tt_page == 1) { ?>
                                <div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php }else{ ?>
                                <div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php } ?>
                            <div class="col-md-6 page-nums">
                                <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_page); ?></span>
                                <span class="separator">/</span>
                                <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
                            </div>
                            <?php if($tt_page != $data->max_pages) { ?>
                                <div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php }else{ ?>
                                <div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="empty-content">
                        <span class="tico tico-ticket"></span>
                        <p><?php _e('Nothing found here', 'tt'); ?></p>
<!--                        <a class="btn btn-info" href="/">--><?php //_e('Back to home', 'tt'); ?><!--</a>-->
                    </div>
                <?php } ?>
            </section>
        </div>
    </div>
</div>