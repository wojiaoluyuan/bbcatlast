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
 * Access users
 */
class WP_REST_User_Controller extends WP_REST_Controller {

    public function __construct() {
        $this->namespace = 'v1';
        $this->rest_base = 'users';
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'            => $this->get_collection_params(),
            ),
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_item' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
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

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/me', array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => array( $this, 'get_current_item' ),
            'args'            => array(
                'context'          => array(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ));

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/email', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_item_by_email' ),
                'permission_callback' => array( $this, 'get_item_by_email_permissions_check' ),
                'args'            => array(
                    'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/key', array(
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_item_by_key' ),
                'permission_callback' => array( $this, 'update_item_by_key_permissions_check' ),
                'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

    }

    /**
     * Permissions check for getting all users.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_items_permissions_check( $request ) {
        // Check if roles is specified in GET request and if user can list users.
        if ( ! empty( $request['roles'] ) && ! current_user_can( 'list_users' ) ) {
            return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you cannot filter by role.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        if ( 'edit' === $request['context'] && ! current_user_can( 'list_users' ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you cannot view this resource with edit context.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Get all users
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items( $request ) {

        $prepared_args = array();
        $prepared_args['exclude'] = $request['exclude'];
        $prepared_args['include'] = $request['include'];
        $prepared_args['order'] = $request['order'];
        $prepared_args['number'] = $request['per_page'];
        if ( ! empty( $request['offset'] ) ) {
            $prepared_args['offset'] = $request['offset'];
        } else {
            $prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
        }
        $orderby_possibles = array(
            'id'              => 'ID',
            'include'         => 'include',
            'name'            => 'display_name',
            'registered_date' => 'registered',
        );
        $prepared_args['orderby'] = $orderby_possibles[ $request['orderby'] ];
        $prepared_args['search'] = $request['search'];
        $prepared_args['role__in'] = $request['roles'];

        if ( ! current_user_can( 'list_users' ) ) {
            $prepared_args['has_published_posts'] = true;
        }

        if ( '' !== $prepared_args['search'] ) {
            $prepared_args['search'] = '*' . $prepared_args['search'] . '*';
        }

        if ( ! empty( $request['slug'] ) ) {
            $prepared_args['search'] = $request['slug'];
            $prepared_args['search_columns'] = array( 'user_nicename' );
        }

        /**
         * Filter arguments, before passing to WP_User_Query, when querying users via the REST API.
         *
         * @see https://developer.wordpress.org/reference/classes/wp_user_query/
         *
         * @param array           $prepared_args Array of arguments for WP_User_Query.
         * @param WP_REST_Request $request       The current request.
         */
        $prepared_args = apply_filters( 'rest_user_query', $prepared_args, $request );

        $query = new WP_User_Query( $prepared_args );

        $users = array();
        foreach ( $query->results as $user ) {
            $data = $this->prepare_item_for_response( $user, $request );
            $users[] = $this->prepare_response_for_collection( $data );
        }

        $response = rest_ensure_response( $users );

        // Store pagation values for headers then unset for count query.
        $per_page = (int) $prepared_args['number'];
        $page = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

        $prepared_args['fields'] = 'ID';

        $total_users = $query->get_total();
        if ( $total_users < 1 ) {
            // Out-of-bounds, run the query again without LIMIT for total count
            unset( $prepared_args['number'] );
            unset( $prepared_args['offset'] );
            $count_query = new WP_User_Query( $prepared_args );
            $total_users = $count_query->get_total();
        }
        $response->header( 'X-WP-Total', (int) $total_users );
        $max_pages = ceil( $total_users / $per_page );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
        if ( $page > 1 ) {
            $prev_page = $page - 1;
            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    /**
     * Check if a given request has access to read a user
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_item_permissions_check( $request ) {

        $id = (int) $request['id'];
        $user = get_userdata( $id );
        $types = get_post_types( array( 'public' => true ), 'names' );

        if ( empty( $id ) || empty( $user->ID ) ) {
            return new WP_Error( 'rest_user_invalid_id', __( 'Invalid resource id.', 'tt' ), array( 'status' => 404 ) );
        }

        if ( get_current_user_id() === $id ) {
            return true;
        }

        if ( 'edit' === $request['context'] && ! current_user_can( 'list_users' ) ) {
            return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you cannot view this resource with edit context.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        } else if ( ! count_user_posts( $id, $types ) && ! current_user_can( 'edit_user', $id ) && ! current_user_can( 'list_users' ) ) {
            return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you cannot view this resource.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Get a single user
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ) {
        $id = absint($request['id']);
        $user = get_userdata( $id );

        if ( empty( $id ) || empty( $user->ID ) ) {
            return new WP_Error( 'rest_user_invalid_id', __( 'Invalid resource id.', 'tt' ), array( 'status' => 404 ) );
        }

        $user = $this->prepare_item_for_response( $user, $request );
        $response = rest_ensure_response( $user );

        return $response;
    }

    /**
     * 检查给定请求是否有权限读取单个用户
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_item_by_email_permissions_check( $request ) {
        return true;
    }

    /**
     * 通过email检索单个用户，主要判断某个邮箱是否绑定了用户
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item_by_email( $request ) {
        $email = $request->get_param('email');
        $user = get_user_by( 'email', $email );

        if ( !is_email( $email ) || empty( $user->ID ) ) {
            return new WP_Error( 'rest_user_invalid_email', __( 'Invalid resource email.', 'tt' ), array( 'status' => tt_rest_resource_not_found_code() ) );
        }

        // GET参数act=findpass
        $act = $request->get_param('act');
        if($act === 'findpass'){
            // 发送重置密码链接
            $reset_link = tt_generate_reset_password_link($email, $user->ID);
            $subject = sprintf(__('Your Password Reset Link - %s', 'tt'), get_bloginfo('name'));
            $args = array('home' => home_url(), 'userLogin' => $user->user_login, 'resetPassLink' => $reset_link);
            tt_async_mail('', $email, $subject, $args, 'findpass');
            return tt_api_success(__('Request to find password was sent', 'tt'));
        }

        return tt_api_fail(__('Unknown action request', 'tt'));
    }

    /**
     * Get the current user
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_current_item( $request ) {
        $current_user_id = get_current_user_id();
        if ( empty( $current_user_id ) ) {
            return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
        }

        $user = wp_get_current_user();
        $response = $this->prepare_item_for_response( $user, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $current_user_id ) ) );
        $response->set_status( 302 );

        return $response;
    }

    /**
     * Check if a given request has access create users
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function create_item_permissions_check( $request ) {

        if ( ! get_option('users_can_register') ) {
            return new WP_Error( 'rest_cannot_create_user', __( 'Sorry, you are not allowed to create resource.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Create a single user
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function create_item( $request ) {
//        if ( ! empty( $request['id'] ) ) {
//            return new WP_Error( 'rest_user_exists', __( 'Cannot create existing resource.', 'tt' ), array( 'status' => 400 ) );
//        }
//
//        if ( ! empty( $request['roles'] ) ) {
//            $check_permission = $this->check_role_update( $request['id'], $request['roles'] );
//            if ( is_wp_error( $check_permission ) ) {
//                return $check_permission;
//            }
//        }

        // 参数step=1 注册第一步, 第二步为激活
        $step = $request->get_param('step');
        if(intval($step) === 1){
            // Nonce验证
            $nonce = sanitize_text_field($request->get_param('nonce'));
            if(!wp_verify_nonce($nonce, 'page-signup')) {
                return new WP_Error( 'rest_invalid_nonce', __( 'Nonce verify failed', 'tt' ), array( 'status' => 400 ) );
            }

            // 密码检查
            $password = sanitize_text_field($request->get_param('password'));
            if(strlen($password) < 5) {
                return new WP_Error( 'rest_user_invalid_password', __( 'Password length does not match the requirement, must more than or equal to 6.', 'tt' ), array( 'status' => 400 ) );
            }

            // 验证码
            if(tt_get_option('tt_tencent_captcha', false)){
            if(empty($request->get_param('tcaptcha_ticket')) || empty($request->get_param('tcaptcha_randstr'))){
               return rest_ensure_response(array(
                    'success' => 0,
                    'message' => __('请先点击验证按钮', 'tt')
                ));
            }
            $ticket = $request->get_param('tcaptcha_ticket');
	        $randstr = $request->get_param('tcaptcha_randstr');
	    
            $data = [
            "aid"=>tt_get_option('tt_tencent_captcha_id'),
            "AppSecretKey"=>tt_get_option('tt_tencent_captcha_sk'),
            "Ticket"=>$ticket,
            "Randstr"=>$randstr,
            "UserIP"=>$_SERVER["REMOTE_ADDR"]
            ];
        
        
            $url = "https://ssl.captcha.qq.com/ticket/verify?".http_build_query($data);
            $result = file_get_contents($url);
            $result = json_decode($result,true);
            if($result["response"] != 1){
             return rest_ensure_response(array(
                    'success' => 0,
                    'message' => "验证失败，请重新点击验证！"
                ));
            }
            }
            // 用户名检查
            $username = sanitize_text_field($request->get_param('user_login'));
            $reg_blacklist = tt_get_option('tt_reg_blacklist');
            $is_black = false;
            $reg_blacks = !empty($reg_blacklist) ? explode(',', $reg_blacklist) : array();
            foreach ($reg_blacks as $reg_black) {
                if(stristr($username, trim($reg_black))!==false) {
                    $is_black = true;
                    $black_str = $reg_black;
                    break;
                }
            }
            if (!$username || strlen($username) < 1) {
                return new WP_Error( 'rest_user_invalid_login', __( 'Invalid resource login name.', 'tt' ), array( 'status' => 400 ) );
            }

            if (get_user_by('login', $username)) {
                return new WP_Error( 'rest_user_invalid_login', __( 'Login name is already existed.', 'tt' ), array( 'status' => 400 ) );
            }
            
            if ($is_black) {
                return new WP_Error( 'rest_user_invalid_login', __( '账户名存在禁止字符"' . $black_str . '"，请检查后重试。', 'tt' ), array( 'status' => 400 ) );
            }

            // 邮箱检查
            $email = sanitize_email($request->get_param('email'));
            if(!is_email($email)) {
                return new WP_Error( 'rest_user_invalid_email', __( 'Invalid email format.', 'tt' ), array( 'status' => 400 ) );
            }

            if (get_user_by('email', $email)) {
                return new WP_Error( 'rest_user_invalid_email', __( 'Email is already used.', 'tt' ), array( 'status' => 400 ) );
            }
            
            // 邀请码检查
            $invite_option = tt_get_option('tt_enable_k_invite', false);
            if($invite_option) {
            $invite = tt_check_invite($request->get_param('invite'));
            if (!$request->get_param('invite')) {
                return new WP_Error( '注册失败', __( '邀请码不能为空.', 'tt' ), array( 'status' => 400 ) );
            }
            if ($invite instanceof WP_Error) {
                 return $invite;
             } elseif (!$invite) {
                 return new WP_Error('注册失败', __('这个邀请码无效', 'tt'), array('status' => 404));
             }
            }
            
            $reg_email_option = tt_get_option('tt_enable_k_reg_email', true);
            if($reg_email_option){
            // 发送激活链接
            $activation_link = tt_generate_registration_activation_link($username, $email, $password);
            tt_async_mail('', $email, __('Your account activation link', 'tt'), array('email' => $email, 'name' => $username, 'link' => $activation_link), 'register-confirm');

            
              $response = rest_ensure_response(array(
                'success' => 1,
                'message' => __('Send registration request successfully, please go to your email box to find and visit the activation link', 'tt')
            ));
            }else{
              $insert_user_id = wp_insert_user( array(
                'user_login'  => $username,
                'user_email' => $email,
                'nickname'  => $username,
                'display_name'  => $username,
                'user_pass' => $password,
              ) ) ;
             wp_set_current_user( $insert_user_id, $username );
             wp_set_auth_cookie( $insert_user_id );
             do_action( 'wp_login', $username, get_user_by('ID', $insert_user_id) );
             $response = rest_ensure_response(array(
                'success' => 1,
                'message' => __('注册成功，稍后自动跳转！', 'tt'),
                'url' => 1,
            ));
            }
            return $response;
        }else{
            // 激活步骤
            $key = $request->get_param('key');
            $response = rest_ensure_response(tt_activate_registration_from_link($key));
            return $response;
        }

        // $user = $this->prepare_item_for_database( $request );

//        if ( is_multisite() ) {
//            $ret = wpmu_validate_user_signup( $user->user_login, $user->user_email );
//            if ( is_wp_error( $ret['errors'] ) && ! empty( $ret['errors']->errors ) ) {
//                return $ret['errors'];
//            }
//        }
//
//        if ( is_multisite() ) {
//            $user_id = wpmu_create_user( $user->user_login, $user->user_pass, $user->user_email );
//            if ( ! $user_id ) {
//                return new WP_Error( 'rest_user_create', __( 'Error creating new resource.', 'tt' ), array( 'status' => 403 ) );
//            }
//            $user->ID = $user_id;
//            $user_id = wp_update_user( $user );
//            if ( is_wp_error( $user_id ) ) {
//                return $user_id;
//            }
//        } else {
//            $user_id = wp_insert_user( $user );
//            if ( is_wp_error( $user_id ) ) {
//                return $user_id;
//            }
//        }
//
//        $user = get_user_by( 'id', $user_id );
//        if ( ! empty( $request['roles'] ) ) {
//            array_map( array( $user, 'add_role' ), $request['roles'] );
//        }
//
//        $this->update_additional_fields_for_object( $user, $request );
//
//        /**
//         * Fires after a user is created or updated via the REST API.
//         *
//         * @param WP_User         $user      Data used to create the user.
//         * @param WP_REST_Request $request   Request object.
//         * @param boolean         $creating  True when creating user, false when updating user.
//         */
//        do_action( 'rest_insert_user', $user, $request, true );
//
//        $request->set_param( 'context', 'edit' );
//        $response = $this->prepare_item_for_response( $user, $request );
//        $response = rest_ensure_response( $response );
//        $response->set_status( 201 );
//        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $user_id ) ) );
//
//        return $response;
    }

    /**
     * Check if a given request has access update a user
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function update_item_permissions_check( $request ) {

        $id = (int) $request['id'];

        if ( ! current_user_can( 'edit_user', $id ) ) {
            return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to edit resource.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        if ( ! empty( $request['roles'] ) && ! current_user_can( 'edit_users' ) ) {
            return new WP_Error( 'rest_cannot_edit_roles', __( 'Sorry, you are not allowed to edit roles of this resource.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Update a single user
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_item( $request ) {
        $id = (int) $request['id'];

        $user = get_userdata( $id );
        if ( ! $user ) {
            return new WP_Error( 'rest_user_invalid_id', __( 'Invalid resource id.', 'tt' ), array( 'status' => 400 ) );
        }

        if ( email_exists( $request['email'] ) && $request['email'] !== $user->user_email ) {
            return new WP_Error( 'rest_user_invalid_email', __( 'Email address is invalid.', 'tt' ), array( 'status' => 400 ) );
        }

        if ( ! empty( $request['username'] ) && $request['username'] !== $user->user_login ) {
            return new WP_Error( 'rest_user_invalid_argument', __( "Username isn't editable", 'tt' ), array( 'status' => 400 ) );
        }

        if ( ! empty( $request['slug'] ) && $request['slug'] !== $user->user_nicename && get_user_by( 'slug', $request['slug'] ) ) {
            return new WP_Error( 'rest_user_invalid_slug', __( 'Slug is invalid.', 'tt' ), array( 'status' => 400 ) );
        }

        if ( ! empty( $request['roles'] ) ) {
            $check_permission = $this->check_role_update( $id, $request['roles'] );
            if ( is_wp_error( $check_permission ) ) {
                return $check_permission;
            }
        }

        $user = $this->prepare_item_for_database( $request );

        // Ensure we're operating on the same user we already checked
        $user->ID = $id;

        $user_id = wp_update_user( $user );
        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        $user = get_user_by( 'id', $id );
        if ( ! empty( $request['roles'] ) ) {
            array_map( array( $user, 'add_role' ), $request['roles'] );
        }

        $this->update_additional_fields_for_object( $user, $request );

        /* This action is documented in lib/endpoints/class-wp-rest-users-controller.php */
        do_action( 'rest_insert_user', $user, $request, false );

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $user, $request );
        $response = rest_ensure_response( $response );
        return $response;
    }

    /**
     * Check if a given request has access delete a user
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function delete_item_permissions_check( $request ) {

        $id = (int) $request['id'];

        if ( ! current_user_can( 'delete_user', $id ) ) {
            return new WP_Error( 'rest_user_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Delete a single user
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function delete_item( $request ) {
        $id = (int) $request['id'];
        $reassign = isset( $request['reassign'] ) ? absint( $request['reassign'] ) : null;
        $force = isset( $request['force'] ) ? (bool) $request['force'] : false;

        // We don't support trashing for this type, error out
        if ( ! $force ) {
            return new WP_Error( 'rest_trash_not_supported', __( 'Users do not support trashing.', 'tt' ), array( 'status' => 403 ) );
        }

        $user = get_userdata( $id );
        if ( ! $user ) {
            return new WP_Error( 'rest_user_invalid_id', __( 'Invalid resource id.', 'tt' ), array( 'status' => 400 ) );
        }

        if ( ! empty( $reassign ) ) {
            if ( $reassign === $id || ! get_userdata( $reassign ) ) {
                return new WP_Error( 'rest_user_invalid_reassign', __( 'Invalid resource id for reassignment.', 'tt' ), array( 'status' => 400 ) );
            }
        }

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $user, $request );

        /** Include admin user functions to get access to wp_delete_user() */
        require_once ABSPATH . 'wp-admin/includes/user.php';

        $result = wp_delete_user( $id, $reassign );

        if ( ! $result ) {
            return new WP_Error( 'rest_cannot_delete', __( 'The resource cannot be deleted.', 'tt' ), array('status' => tt_rest_authorization_required_code()) );
        }

        /**
         * Fires after a user is deleted via the REST API.
         *
         * @param WP_User          $user     The user data.
         * @param WP_REST_Response $response The response returned from the API.
         * @param WP_REST_Request  $request  The request sent to the API.
         */
        do_action( 'rest_delete_user', $user, $response, $request );

        return $response;
    }


    /**
     * 检查给定请求是否有权限更新用户
     *
     * @param  WP_REST_Request $request
     * @return boolean | WP_Error
     */
    public function update_item_by_key_permissions_check( $request ) {

        return true;
    }

    /**
     * 通过key更新用户(重置密码)
     *
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function update_item_by_key( $request ) {
        $key = trim($request->get_param('key'));
        $act = $request->get_param('act');

        if($act == 'resetpass') {
            $password = $request->get_param('password');

            if(strlen($password) < 6) {
                return new WP_Error(__('The new password length is too short and not safe', 'tt'), array('status' => tt_rest_authorization_required_code()));
            }

            $user = tt_reset_password_by_key($key, $password);

            if($user instanceof WP_Error){
                return $user; // WP_Error
            }

            // $user->user_pass = $password;

            // do_action( 'rest_update_user_password', $user, $request, false );

//            $request->set_param( 'context', 'edit' );
//            $response = $this->prepare_item_for_response( $user, $request );
//            $response = rest_ensure_response( $response );
//            return $response;
            if(!$user) {
                return tt_api_fail(__('Reset password failed, please retry or contact the administrator', 'tt'));
            }

            return tt_api_success(__('Your password has been reset successfully', 'tt'));
        }

        return tt_api_fail(__('Unknown action request', 'tt'));
    }





    /**
     * Prepare a single user output for response
     *
     * @param object $user User object.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response( $user, $request ) {
        $data = array(
            'id'                 => $user->ID,
            'username'           => $user->user_login,
            'name'               => $user->display_name,
            'first_name'         => $user->first_name,
            'last_name'          => $user->last_name,
            'email'              => $user->user_email,
            'url'                => $user->user_url,
            'description'        => $user->description,
            'link'               => get_author_posts_url( $user->ID ),
            'nickname'           => $user->nickname,
            'slug'               => $user->user_nicename,
            'registered_date'    => date( 'c', strtotime( $user->user_registered ) ),
            'roles'              => $user->roles,
            'capabilities'       => $user->allcaps,
            'extra_capabilities' => $user->caps,
        );

        $schema = $this->get_item_schema();

        if ( ! empty( $schema['properties']['avatar_urls'] ) ) {
            $data['avatar_urls'] = rest_get_avatar_urls( $user->user_email );
        }

        $context = ! empty( $request['context'] ) ? $request['context'] : 'embed';
        $data = $this->add_additional_fields_to_object( $data, $request );
        $data = $this->filter_response_by_context( $data, $context );

        // Wrap the data in a response object
        $response = rest_ensure_response( $data );

        $response->add_links( $this->prepare_links( $user ) );

        /**
         * Filter user data returned from the REST API.
         *
         * @param WP_REST_Response $response  The response object.
         * @param object           $user      User object used to create response.
         * @param WP_REST_Request  $request   Request object.
         */
        return apply_filters( 'rest_prepare_user', $response, $user, $request );
    }

    /**
     * Prepare links for the request.
     *
     * @param WP_Post $user User object.
     * @return array Links for the given user.
     */
    protected function prepare_links( $user ) {
        $links = array(
            'self' => array(
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $user->ID ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
            ),
        );

        return $links;
    }

    /**
     * Prepare a single user for create or update
     *
     * @param WP_REST_Request $request Request object.
     * @return object $prepared_user User object.
     */
    protected function prepare_item_for_database( $request ) {
        $prepared_user = new stdClass;

        // required arguments.
        if ( isset( $request['email'] ) ) {
            $prepared_user->user_email = $request['email'];
        }
        if ( isset( $request['username'] ) ) {
            $prepared_user->user_login = $request['username'];
        }
        if ( isset( $request['password'] ) ) {
            $prepared_user->user_pass = $request['password'];
        }

        // optional arguments.
        if ( isset( $request['id'] ) ) {
            $prepared_user->ID = absint( $request['id'] );
        }
        if ( isset( $request['name'] ) ) {
            $prepared_user->display_name = $request['name'];
        }
        if ( isset( $request['first_name'] ) ) {
            $prepared_user->first_name = $request['first_name'];
        }
        if ( isset( $request['last_name'] ) ) {
            $prepared_user->last_name = $request['last_name'];
        }
        if ( isset( $request['nickname'] ) ) {
            $prepared_user->nickname = $request['nickname'];
        }
        if ( isset( $request['slug'] ) ) {
            $prepared_user->user_nicename = $request['slug'];
        }
        if ( isset( $request['description'] ) ) {
            $prepared_user->description = $request['description'];
        }

        if ( isset( $request['url'] ) ) {
            $prepared_user->user_url = $request['url'];
        }

        // setting roles will be handled outside of this function.
        if ( isset( $request['roles'] ) ) {
            $prepared_user->role = false;
        }

        /**
         * Filter user data before inserting user via the REST API.
         *
         * @param object          $prepared_user User object.
         * @param WP_REST_Request $request       Request object.
         */
        return apply_filters( 'rest_pre_insert_user', $prepared_user, $request );
    }

    /**
     * Determine if the current user is allowed to make the desired roles change.
     *
     * @param integer $user_id
     * @param array   $roles
     * @return WP_Error|boolean
     */
    protected function check_role_update( $user_id, $roles ) {
        global $wp_roles;

        foreach ( $roles as $role ) {

            if ( ! isset( $wp_roles->role_objects[ $role ] ) ) {
                return new WP_Error( 'rest_user_invalid_role', sprintf( __( 'The role %s does not exist.', 'tt' ), $role ), array( 'status' => 400 ) );
            }

            $potential_role = $wp_roles->role_objects[ $role ];
            // Don't let anyone with 'edit_users' (admins) edit their own role to something without it.
            // Multisite super admins can freely edit their blog roles -- they possess all caps.
            if ( ! ( is_multisite() && current_user_can( 'manage_sites' ) ) && get_current_user_id() === $user_id && ! $potential_role->has_cap( 'edit_users' ) ) {
                return new WP_Error( 'rest_user_invalid_role', __( 'You cannot give resource that role.', 'tt' ), array( 'status' => tt_rest_authorization_required_code() ) );
            }

            // The new role must be editable by the logged-in user.

            /** Include admin functions to get access to get_editable_roles() */
            require_once ABSPATH . 'wp-admin/includes/admin.php';

            $editable_roles = get_editable_roles();
            if ( empty( $editable_roles[ $role ] ) ) {
                return new WP_Error( 'rest_user_invalid_role', __( 'You cannot give resource that role.', 'tt' ), array( 'status' => 403 ) );
            }
        }

        return true;

    }

    /**
     * Get the User's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'user',
            'type'       => 'object',
            'properties' => array(
                'id'          => array(
                    'description' => __( 'Unique identifier for the resource.', 'tt' ),
                    'type'        => 'integer',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'username'    => array(
                    'description' => __( 'Login name for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    //'required'    => true,  // TODO: modified
                    'required'    => false,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_user',
                    ),
                ),
                'name'        => array(
                    'description' => __( 'Display name for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'first_name'  => array(
                    'description' => __( 'First name for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'last_name'   => array(
                    'description' => __( 'Last name for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'email'       => array(
                    'description' => __( 'The email address for the resource.', 'tt' ),
                    'type'        => 'string',
                    'format'      => 'email',
                    'context'     => array( 'edit' ),
                    // 'required'    => true, // TODO: modified
                ),
                'url'         => array(
                    'description' => __( 'URL of the resource.', 'tt' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'embed', 'view', 'edit' ),
                ),
                'description' => array(
                    'description' => __( 'Description of the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'wp_filter_post_kses',
                    ),
                ),
                'link'        => array(
                    'description' => __( 'Author URL to the resource.', 'tt' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'nickname'    => array(
                    'description' => __( 'The nickname for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'slug'        => array(
                    'description' => __( 'An alphanumeric identifier for the resource.', 'tt' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_title',
                    ),
                ),
                'registered_date' => array(
                    'description' => __( 'Registration date for the resource.', 'tt' ),
                    'type'        => 'date-time',
                    'context'     => array( 'edit' ),
                    'readonly'    => true,
                ),
                'roles'           => array(
                    'description' => __( 'Roles assigned to the resource.', 'tt' ),
                    'type'        => 'array',
                    'context'     => array( 'edit' ),
                ),
                'password'        => array(
                    'description' => __( 'Password for the resource (never included).', 'tt' ),
                    'type'        => 'string',
                    'context'     => array(), // Password is never displayed
                    // 'required'    => true, // TODO: modified
                ),
                'capabilities'    => array(
                    'description' => __( 'All capabilities assigned to the resource.', 'tt' ),
                    'type'        => 'object',
                    'context'     => array( 'edit' ),
                ),
                'extra_capabilities' => array(
                    'description' => __( 'Any extra capabilities assigned to the resource.', 'tt' ),
                    'type'        => 'object',
                    'context'     => array( 'edit' ),
                    'readonly'    => true,
                ),
            ),
        );

        if ( get_option( 'show_avatars' ) ) {
            $avatar_properties = array();

            $avatar_sizes = rest_get_avatar_sizes();
            foreach ( $avatar_sizes as $size ) {
                $avatar_properties[ $size ] = array(
                    'description' => sprintf( __( 'Avatar URL with image size of %d pixels.', 'tt' ), $size ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'embed', 'view', 'edit' ),
                );
            }

            $schema['properties']['avatar_urls']  = array(
                'description' => __( 'Avatar URLs for the resource.', 'tt' ),
                'type'        => 'object',
                'context'     => array( 'embed', 'view', 'edit' ),
                'readonly'    => true,
                'properties'  => $avatar_properties,
            );

        }

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params() {
        $query_params = parent::get_collection_params();

        $query_params['context']['default'] = 'view';

        $query_params['exclude'] = array(
            'description'        => __( 'Ensure result set excludes specific ids.', 'tt' ),
            'type'               => 'array',
            'default'            => array(),
            'sanitize_callback'  => 'wp_parse_id_list',
        );
        $query_params['include'] = array(
            'description'        => __( 'Limit result set to specific ids.', 'tt' ),
            'type'               => 'array',
            'default'            => array(),
            'sanitize_callback'  => 'wp_parse_id_list',
        );
        $query_params['offset'] = array(
            'description'        => __( 'Offset the result set by a specific number of items.', 'tt' ),
            'type'               => 'integer',
            'sanitize_callback'  => 'absint',
            'validate_callback'  => 'rest_validate_request_arg',
        );
        $query_params['order'] = array(
            'default'            => 'asc',
            'description'        => __( 'Order sort attribute ascending or descending.', 'tt' ),
            'enum'               => array( 'asc', 'desc' ),
            'sanitize_callback'  => 'sanitize_key',
            'type'               => 'string',
            'validate_callback'  => 'rest_validate_request_arg',
        );
        $query_params['orderby'] = array(
            'default'            => 'name',
            'description'        => __( 'Sort collection by object attribute.', 'tt' ),
            'enum'               => array(
                'id',
                'include',
                'name',
                'registered_date',
            ),
            'sanitize_callback'  => 'sanitize_key',
            'type'               => 'string',
            'validate_callback'  => 'rest_validate_request_arg',
        );
        $query_params['slug']    = array(
            'description'        => __( 'Limit result set to resources with a specific slug.', 'tt' ),
            'type'               => 'string',
            'validate_callback'  => 'rest_validate_request_arg',
        );
        $query_params['roles']   = array(
            'description'        => __( 'Limit result set to resources matching at least one specific role provided. Accepts csv list or single role.', 'tt' ),
            'type'               => 'array',
            'sanitize_callback'  => 'wp_parse_slug_list',
        );
        return $query_params;
    }
}
