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
 * Class WP_REST_Invite_Controller
 */
class WP_REST_Invite_Controller extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'v1';
        $this->rest_base = 'invites';
    }

    /**
     * 注册路由
     */
    public function register_routes(){

        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args' => array(
                    'context' => $this->get_context_param(array('default' => 'view')),
                ),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
            ),
            'schema' => array($this, 'get_public_item_schema'),
        ));

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'            => array(
                    'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_item' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
                'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array( $this, 'delete_item' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                'args' => array(
                    'force'    => array(
                        'default'     => false,
                        'description' => __( 'Required to be true, as resource does not support trashing.' ),
                    ),
                    'reassign' => array(),
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
    }


    /**
     * 检查是否有获取多个邀请码的权限
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_items_permissions_check($request)
    {
        if (!current_user_can('administrator')) {
            return new WP_Error('不能查看邀请码', __('对不起,你没有权限查看邀请码.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }

    /**
     * 获取多个优惠码
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request)
    {
        $limit = absint($request->get_param('limit')) ? : 20;
        $offset = absint($request->get_param('offset')) ? : 0;
        $results = tt_get_invites(0, $limit, $offset);

        if( !$results || $results instanceof WP_Error/*is_wp_error($results)*/ ) {
            return tt_api_fail(__('获取邀请码失败', 'tt'), array(), 500);
        }

        return tt_api_success('', array('data' => $results));
    }


    /**
     * 判断请求是否有创建优惠码的权限
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function create_item_permissions_check($request)
    {
        if (!current_user_can('administrator')) {
            return new WP_Error('不能创建邀请码', __('对不起, 你没有权限创建邀请码.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }


    /**
     * 创建一个或多个邀请码
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function create_item($request)
    {
        $type = $request->get_param('type');
        $effect_date = $request->get_param('effectDate');
        $expire_date = $request->get_param('expireDate');
        $code = sanitize_text_field($request->get_param('code'));

        // type 验证
        if(!in_array($type, array('once', 'multi'))){
            return tt_api_fail(__('邀请码类型不对', 'tt'), array(), 400);
        }

        // Code 验证
       // if(strlen($code) < 4){
        //    return tt_api_fail(__('邀请码长度不对', 'tt'), array(), 400);
        //}

        //
        if(!$effect_date) {
            return tt_api_fail(__('邀请码开始时间不能为空', 'tt'), array(), 400);
        }

        if(!$expire_date) {
            return tt_api_fail(__('邀请码结束时间不能为空', 'tt'), array(), 400);
        }
        if(is_numeric($code) && $code <= 100) {
            $quantity = (int)max(1, min($code, 100));
            $add = tt_gen_invites($quantity, $type, $effect_date, $expire_date);
        }else{
            $add = tt_add_invite($code, $type, $effect_date, $expire_date);
        }
        if($add instanceof WP_Error) {
            return $add;
        }elseif(!$add) {
            return tt_api_fail(__('添加邀请码失败', 'tt'), array(), 400);
        }

        return tt_api_success(__('添加邀请码成功', 'tt'), array());
    }
   
    /**
	 * 创建多个优惠码
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_items($request)
	{
		$quantity = (int)max(1, min($request->get_param('quantity'), 100));

		$gen = tt_gen_invites($quantity, $request->get_param('batch_type'), $request->get_param('batch_effectDate'), $request->get_param('batch_expireDate'));

		if($gen instanceof WP_Error) {
			return $gen;
		}elseif(!$gen) {
			return tt_api_fail(__('批量生成失败', 'tt'), array(), 400);
		}

		return tt_api_success(__('批量生成成功', 'tt'), array('cards' => $gen));
	}

    /**
     * 判断请求是否有权限读取单个优惠码
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function get_item_permissions_check( $request ) {
        if (!current_user_can('administrator')) {
            return new WP_Error('不能查看邀请码', __('对不起,你没有权限查看邀请码.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }

    /**
     * 读取单个优惠码
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error | WP_REST_Response
     */
    public function get_item( $request ) {
        $id = absint($request['id']);
        $invite = tt_get_invite($id);
        if(!$invite) {
            return tt_api_fail(__('无法获得指定的邀请码', 'tt'));
        }
        return tt_api_success('', array('data' => $invite));
    }


    /**
     * 判断当前请求是否有权限更新指定优惠码
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function update_item_permissions_check( $request ) {
        if (!current_user_can('administrator')) {
            return new WP_Error('不能更新邀请码', __('对不起,你没有权限更新邀请码.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }

    /**
     * 更新单个优惠码
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_item( $request ) {
        $id = absint($request['id']);
        //TODO
        return null;
    }

    /**
     * 检查请求是否有删除指定优惠码的权限
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function delete_item_permissions_check( $request ) {
        if (!current_user_can('administrator')) {
            return new WP_Error('不能删除邀请码', __('对不起,你没有权限删除邀请码.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }

    /**
     * 删除单个优惠码
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function delete_item( $request ) {
        $id = (int) $request['id'];

        $result = tt_delete_invite($id);
        if(!$result) {
            return new WP_Error( '删除失败', __( '邀请码删除失败.', 'tt' ), array('status' => tt_rest_authorization_required_code()) );
        }

        return tt_api_success(__('邀请码删除成功', 'tt'), array('invite_id' => $id));
    }
}
