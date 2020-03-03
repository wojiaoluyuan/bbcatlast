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

/**
 * Class UCWidgetVM
 */
class UCWidgetVM extends BaseVM {

    /**
     * @var int 用户ID
     */
    private $_userId;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'hourly';
        $this->_cacheInterval = 1800; // 缓存保留半小时
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $user_id   用户ID
     * @return  static
     */
    public static function getInstance($user_id = 0) {
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_user' . $user_id;
        $instance->_userId = $user_id;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $data = get_userdata($this->_userId);
        if(!$data) return null;

        $user_info = array();
        $user_info['ID'] = $this->_userId;
        $user_info['display_name'] = $data->display_name;
        $user_info['my_settings'] = tt_url_for('my_settings');
        $user_info['cap'] = tt_get_user_cap_string($data->ID);
        $user_info['HomePage'] = get_author_posts_url($data->ID);
        $user_info['admin_url'] = admin_url();
        $user_info['new_post'] = tt_url_for('new_post');
        $user_info['stared_count'] = tt_count_user_star_posts($data->ID);
        $user_info['uc_latest'] = tt_url_for('uc_latest', $data->ID);
        $user_info['user_posts'] = count_user_posts($data->ID);
        $user_info['uc_comments'] = tt_url_for('uc_comments', $data->ID);
        $user_info['uc_comments_count'] = get_comments( array('status' => '1', 'user_id'=>$data->ID, 'count' => true) );
        $user_info['uc_stars'] = tt_url_for('uc_stars', $data->ID);

        $avatar = new Avatar($data->ID, 'medium');
        $user_info['avatar'] = $avatar->getAvatar();

        return (object)$user_info;
    }
}