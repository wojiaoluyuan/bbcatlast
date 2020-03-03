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

class WidgetCreditsRankVM extends BaseVM {

    /**
     * @var int 显示的用户数量
     */
    private $_count = 10;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'hourly';
        $this->_cacheInterval = 3600; // 缓存保留一小时
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $count   用户数量
     * @return  static
     */
    public static function getInstance($count = 10) {
        $items_count = max(1, absint($count));
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_count' . $items_count;
        $instance->_count = $items_count;;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $ranks = tt_credits_rank($this->_count, 0);
        $user_ids = array();
        foreach ($ranks as $rank) {
            array_push($user_ids, intval($rank->user_id));
        }
        $users = get_users(array(
            'include' => $user_ids,
            'orderby' => 'include'
        ));

        $items = array();
        for ($i = 0; $i < count($users); $i++) {
            $item = $users[$i]->data;
            unset($item->user_pass);
            unset($item->user_activation_key);
            $item->credits = intval($ranks[$i]->meta_value);
            $item->avatar = tt_get_avatar($users[$i]->data->ID);
            $item->link = home_url('/u/' . $users[$i]->data->ID);
            $item->index = $i+1;
            array_push($items, $item);
        }
        return $items;
    }
}
