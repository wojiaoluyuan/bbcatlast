<?php
/**
 * Copyright (c) 2014-2016, bbcat.com
 * All right reserved.
 *
 * @since 2.0.0
 * @package BBCat
 * @author Kuacg
 * @date 2018/1/22 21:32
 */
?>
<?php

class WeiboImageVM extends BaseVM {

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 60*60; // 缓存1小时
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $url   原始图片地址
     * @return  static
     */
    public static function getInstance($url) {
        $instance = new static(); // 因为配置不同文章数量共用该模型，不采用单例模式
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_url_' . short_md5($url);
        $instance->_url = $url;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
     $data = array();
     $data['url'] = wp_image_to_weibo_img_replace($this->_url);
     return (object)$data;
    }
}