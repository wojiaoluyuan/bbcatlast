<?php
/**
 * Copyright (c) 2019-2025, bbcatga.herokuapp.com
 * All right reserved.
 *
 * @since 2.5.0
 * @package BBcat-K
 * @author 洛茛艺术影视在线
 * @date 2019-04-03 10:00
 * @link https://bbcatga.herokuapp.com
 */
?>
<?php

/**
 * Class MgOrdersVM
 */
class MgOrdersVM extends BaseVM {
    /**
     * @var int  分页
     */
    protected $_page = 1;

    /**
     * @var string  订单货币类型
     */
    protected $_type = 'all';

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   $page
     * @param   $type
     * @return  static
     */
    public static function getInstance($page = 1, $type = 'all') {
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_type' . $type . '_page' . $page;
        $instance->_page = $page;
        $instance->_type = $type;
        $instance->_enableCache = false; //不使用缓存
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        $limit = 20; // 每页20条
        $orders = tt_get_orders($limit, ($this->_page - 1) * $limit, $this->_type);
        $count = $orders ? count($orders) : 0;
        $total_count = tt_count_orders($this->_type);
        $max_pages = ceil($total_count / $limit);

        switch ($this->_type) {
            case 'cash':
                $url_key = 'manage_cash_orders';
                break;
            case 'credit':
                $url_key = 'manage_credit_orders';
                break;
            default:
                $url_key = 'manage_orders';
        }
        $pagination_base = tt_url_for($url_key) . '/page/%#%';

        return (object)array(
            'count' => $count,
            'orders' => $orders,
            'total' => $total_count,
            'max_pages' => $max_pages,
            'pagination_base' => $pagination_base,
            'prev_page' => str_replace('%#%', max(1, $this->_page - 1), $pagination_base),
            'next_page' => str_replace('%#%', min($max_pages, $this->_page + 1), $pagination_base)
        );
    }
}