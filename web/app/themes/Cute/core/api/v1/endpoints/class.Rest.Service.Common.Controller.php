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
 * Class WP_REST_Common_Service_Controller.
 */
class WP_REST_Common_Service_Controller extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'v1';
        $this->rest_base = 'commonservice';
    }

    /**
     * 注册路由.
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/'.$this->rest_base.'/(?P<service>[\S]+)', array(
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'exec_service'),
                'permission_callback' => array($this, 'exec_service_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
            ),
            'schema' => array($this, 'get_public_item_schema'),
        ));
    }

    /**
     * 判断当前请求是否有权限执行服务
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return bool | WP_Error
     */
    public function exec_service_permissions_check($request)
    {
        if ($request->get_method() != 'POST') {
            return new WP_Error('rest_request_invalid', __('Sorry, the request method is invalid.', 'tt'), array('status' => 400));
        }
        $request['service'] = str_replace('/', '.', $request['service']);
        $allow_services_kv = (array) json_decode(ALLOWED_COMMON_SERVICES);
        $allow_services = array_keys($allow_services_kv);
        if (!in_array($request['service'], $allow_services)) {
            return new WP_Error('rest_service_invalid', __('Sorry, the service is invalid.', 'tt'), array('status' => 404));
        }

        $uid = get_current_user_id();
        if ($allow_services_kv[$request['service']] == 1 && !$uid) {
            return new WP_Error('rest_service_cannot_execute', __('Sorry, you cannot execute the service without authorization.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }

        $request['uid'] = $uid;

        return true;
    }

    /**
     * Service.
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function exec_service($request)
    {
        return tt_exec_common_api_services($request['service'], $request->get_params());
    }
}
