<?php
if( ! class_exists('Toolkit_Elementor_TempLogin') ) {

    class Toolkit_Elementor_TempLogin{

        public function __construct(){

        }

        public static function get_capability_options(){
            return array(
                'view-edit' => __('View & Edit'),
                'view'      => __('View Only'),
                'none'      => __('No Access')
            );
        }

        public static function get_plugins_options(){
            return array(
                'view-edit' => __('View & Edit'),
                'none'      => __('Hide Completely')
            );
        }

        public static function get_access_options(){
            return array(
                'administrator' => __('All Administrators'),
                'me'            => __('Only Me (Current User)')
            );
        }

        public static function get_expire_options() {
            $tkfe_access = array(
                'hour'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Hour', 'toolkit-for-elementor' ), 'timestamp' => HOUR_IN_SECONDS, 'order' => 5 ),
		        'day'                  => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Day', 'toolkit-for-elementor' ), 'timestamp' => DAY_IN_SECONDS, 'order' => 15 ),
		        'week'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Week', 'toolkit-for-elementor' ), 'timestamp' => WEEK_IN_SECONDS, 'order' => 25 ),
                'month'                => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Month', 'toolkit-for-elementor' ), 'timestamp' => MONTH_IN_SECONDS, 'order' => 30 ),
                'year'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Year', 'toolkit-for-elementor' ), 'timestamp' => MONTH_IN_SECONDS * 12, 'order' => 35 ),
            );
            $tkfe_access = apply_filters( 'toolkit_login_expiry_options', $tkfe_access );
            foreach ( $tkfe_access as $key => $options ) {
                $tkfe_access[ $key ]['order']        = ! empty( $options['order'] ) ? $options['order'] : 100;
                $tkfe_access[ $key ]['group']        = ! empty( $options['group'] ) ? $options['group'] : __( 'from_now', '' );
                $tkfe_access[ $key ]['group_name']  = ! empty( $options['group_name'] ) ? $options['group_name'] : __( 'From Now', '' );
                $tkfe_access[ $key ]['expiry_label'] = ! empty( $options['expiry_label'] ) ? $options['expiry_label'] : '';
                $orders[ $key ] = ! empty( $options['order'] ) ? $options['order'] : 100;
            }
            array_multisort( $orders, SORT_ASC, $tkfe_access );
            return $tkfe_access;
        }

        static function get_expire_duration_options( $selected = '', $excluded = array() ) {
            $return = '';
            $tkfe_access = self::get_expire_options();
            if ( is_array( $tkfe_access ) && count( $tkfe_access ) > 0 ) {
                $grouped_expiry_options = $groups = array();
                foreach ( $tkfe_access as $key => $option ) {
                    if ( ! empty( $excluded ) && in_array( $key, $excluded ) ) {
                        continue;
                    }
                    $groups[ $option['group'] ] = $option['group_name'];
                    $grouped_expiry_options[ $option['group'] ][ $key ] = $option;
                }

                foreach ( $grouped_expiry_options as $group => $options ) {
                    $return .= "\n\t<optgroup label='$groups[$group]'>";
                    foreach ( $options as $key => $option ) {
                        $label = ! empty( $option['label'] ) ? $option['label'] : '';
                        $return .= "\n\t<option ";
                        if ( $selected === $key ) {
                            $return .= "selected='selected' ";
                        }
                        $return .= "value='" . esc_attr( $key ) . "'>$label</option>";
                    }
                    $return .= "</optgroup>";
                }
            }
            echo $return;
        }

        public static function multi_select_dropdown_roles( $selected_roles = array() ) {
            $return = '';
            $editable_roles = array_reverse( get_editable_roles() );
            foreach ( $editable_roles as $role => $details ) {
                $name = translate_user_role( $details['name'] );
                if ( count( $selected_roles ) > 0 && in_array( $role, $selected_roles ) ) {
                    $return .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
                } else {
                    $return .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
                }
            }
            echo $return;
        }

        public function save_user(){
            if( ! isset($_POST['toolkit_access_nonce']) || ! wp_verify_nonce($_POST['toolkit_access_nonce'], 'toolkit_access_nonce') ) {
                $response = array('success'=>false, 'message'=>__("Security nonce missing, reload and try again.", "toolkit-for-elementor"));
            } else {
                if( isset($_POST['user_email']) && trim($_POST['user_email']) && is_email($_POST['user_email']) ){
                    $helper = new Toolkit_Elementor_Login_Helper();
                    $user = $helper->create_user( $_POST );
                    if ( isset( $user['error'] ) && $user['error'] === true ) {
                        $message = (isset($user['message'])) ? $user['message'] : __("Sorry, something went wrong. Please try again.", "toolkit-for-elementor");
                        $response = array('success'=>false, 'message'=>$message);
                    } else {
                        $response = array('success'=>true, 'message'=>__("Your Access Link has been created successfully", "toolkit-for-elementor"));
                    }
                } else {
                    $response = array('success'=>false, 'message'=>__("Invalid user email.", "toolkit-for-elementor"));
                }
            }
            wp_send_json($response);
        }

        public function init_login() {
            if ( ! empty( $_GET['tfes_token'] ) ) {
                $tfes_token = sanitize_key( $_GET['tfes_token'] );
                $helper = new Toolkit_Elementor_Login_Helper();
                $users = $helper->get_valid_user_based_on_tfes_token( $tfes_token );
                $tkfe_ual = '';
                if ( ! empty( $users ) ) {
                    $tkfe_ual = $users[0];
                }
                if ( ! empty( $tkfe_ual ) ) {
                    $tkfe_ual_id = $tkfe_ual->ID;
                    $do_login          = true;
                    if ( is_user_logged_in() ) {
                        $current_user_id = get_current_user_id();
                        if ( $tkfe_ual_id !== $current_user_id ) {
                            wp_logout();
                        } else {
                            $do_login = false;
                        }
                    }
                    if ( $do_login ) {
                        $tkfe_ual_login = $tkfe_ual->login;
                        update_user_meta( $tkfe_ual_id, '_tfes_last_login', Toolkit_Elementor_Login_Helper::get_current_gmt_timestamp() ); // phpcs:ignore
                        wp_set_current_user( $tkfe_ual_id, $tkfe_ual_login );
                        wp_set_auth_cookie( $tkfe_ual_id );
                        $login_count_key = '_tfes_login_count';
                        $login_count     = get_user_meta( $tkfe_ual_id, $login_count_key, true );
                        if ( ! empty( $login_count ) ) {
                            $login_count ++;
                        } else {
                            $login_count = 1;
                        }
                        update_user_meta( $tkfe_ual_id, $login_count_key, $login_count );
                        do_action( 'wp_login', $tkfe_ual_login, $tkfe_ual );
                    }
                    $request_uri = $_SERVER['REQUEST_URI'];
                    if ( ! is_multisite() ) {
                        $component = trim(parse_url( get_site_url(), PHP_URL_PATH ));
                        if ( ! empty( $component ) ) {
                            $component .= '/';
                            $request_uri = str_replace( $component, '', $request_uri );
                        }
                    }
                    $redirect = get_user_meta($tkfe_ual_id, '_tfes_raw_redirect', true);
                    if( $redirect == 'home_page' ){
                        $redirect_to = home_url();
                    } elseif( $redirect == 'wp_dashboard' ){
                        $redirect_to = admin_url();
                    } elseif( $redirect > 0 ){
                        $redirect_to = get_permalink($redirect);
                    } else {
                        $redirect_to = ( isset( $_REQUEST['redirect_to'] ) ) ? $_REQUEST['redirect_to'] : apply_filters( 'login_redirect', network_site_url( remove_query_arg( 'tfes_token', $request_uri ) ), false, $tkfe_ual ); // phpcs:ignore
                    }
                } else {
                    // If User Access is not available, redirect to homepage
                    $redirect_to = home_url();
                }
                wp_safe_redirect( $redirect_to ); // Redirect to given url after successful login.
                exit();
            }

            // Restrict unauthorized page access for User Access Link users
            if ( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                if ( ! empty( $user_id ) && Toolkit_Elementor_Login_Helper::is_valid_temp_login( $user_id, false ) ) {
                    if ( Toolkit_Elementor_Login_Helper::is_login_expired( $user_id ) ) {
                        wp_logout();
                        wp_safe_redirect( home_url() );
                        exit();
                    } else {
                        global $pagenow;
                        $bloked_pages = Toolkit_Elementor_Login_Helper::get_blocked_pages();
                        $page         = ! empty( $_GET['page'] ) ? $_GET['page'] : ''; //phpcs:ignore
                        if ( ! empty( $page ) && in_array( $page, $bloked_pages ) || ( ! empty( $pagenow ) && ( in_array( $pagenow, $bloked_pages ) ) ) || ( ! empty( $pagenow ) && ( 'users.php' === $pagenow && isset( $_GET['action'] ) && ( 'deleteuser' === $_GET['action'] || 'delete' === $_GET['action'] ) ) ) ) { //phpcs:ignore
                            wp_die( esc_attr__( "You don't have permission to access this page", 'toolkit-for-elementor' ) );
                        }
                    }
                }
            }

        }
        public function disable_tkfe_ual( $user, $password ) {
            if ( $user instanceof WP_User ) {
                $check_expiry             = false;
                $is_valid_temp_login = Toolkit_Elementor_Login_Helper::is_valid_temp_login( $user->ID, $check_expiry );
                if ( $is_valid_temp_login ) {
                    $user = new WP_Error( 'denied', __( "ERROR: User can't find." ) );
                }
            }
            return $user;
        }
        public function disable_password_reset( $allow, $user_id ) {
            if ( is_int( $user_id ) ) {
                $check_expiry             = false;
                $is_valid_temp_login = Toolkit_Elementor_Login_Helper::is_valid_temp_login( $user_id, $check_expiry );
                if ( $is_valid_temp_login ) {
                    $allow = false;
                }
            }
            return $allow;
        }
        public function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {
            $current_user_id = get_current_user_id();
            if ( Toolkit_Elementor_Login_Helper::is_valid_temp_login( $current_user_id ) && ( plugin_basename(TOOLKIT_FOR_ELEMENTOR_FILE) === $plugin_file ) ) {
                unset( $actions['deactivate'] );
            }
            return $actions;
        }
        function remove_toolkit_login_user(){
            if( ! isset($_POST['toolkit_access_nonce']) || ! wp_verify_nonce($_POST['toolkit_access_nonce'], 'toolkit_access_nonce') ) {
                $response = array('success'=>false, 'message'=>__("Security nonce missing, reload and try again.", "toolkit-for-elementor"));
            } else {
                if( isset($_POST['user_id']) && $_POST['user_id'] > 0 ){
                    $reassign_id = isset($_POST['reassign_id']) ? $_POST['reassign_id'] : null;
                    $deleted = wp_delete_user( $_POST['user_id'], $reassign_id );
                    if ( $deleted ) {
                        $response = array('success'=>true, 'message'=>__("User Access Link has been deleted successfully.", "toolkit-for-elementor"));
                    } else {
                        $response = array('success'=>false, 'message'=>__("Something went wrong, try again.", "toolkit-for-elementor"));
                    }
                } else {
                    $response = array('success'=>false, 'message'=>__("Invalid request.", "toolkit-for-elementor"));
                }
            }
            wp_send_json($response);
        }

        function remove_plugins_access(){
            $user_id = get_current_user_id();
            $wc_access = get_user_meta( $user_id, '_toolkit_cap_allow_woocommerce', true );
            if( ! current_user_can('edit_posts') ){
                remove_menu_page( 'edit.php' );
                remove_menu_page( 'edit-tags.php?taxonomy=category' );
                remove_menu_page( 'edit-tags.php?taxonomy=post_tag' );
            }
            if( $wc_access == 'none' ){
                remove_menu_page( 'woocommerce' );
            }
        }

        function register_post_type_args($args, $post_type){
            if( $post_type == 'post' ){
                $user_id = get_current_user_id();
                $ps_access = get_user_meta($user_id, '_toolkit_cap_allow_posts', true);
                if( $ps_access == 'view' ){
                    $args['capabilities']['create_posts'] = 'do_not_allow';
                    $args['capabilities']['delete_published_posts'] = 'do_not_allow';
                    $args['capabilities']['edit_published_posts'] = 'do_not_allow';
                }
            }
            if( $post_type == 'page' ){
                $user_id = get_current_user_id();
                $pg_access = get_user_meta($user_id, '_toolkit_cap_allow_pages', true);
                if( $pg_access == 'view' ){
                    $args['capabilities']['create_posts'] = 'do_not_allow';
                    $args['capabilities']['delete_published_posts'] = 'do_not_allow';
                    $args['capabilities']['edit_published_posts'] = 'do_not_allow';
                }
            }
            return $args;
        }
					
    }

}
