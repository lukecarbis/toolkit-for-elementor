<?php
/**
 * Class Toolkit_Elementor_Login_Helper
 */
class Toolkit_Elementor_Login_Helper {

	public function get_temp_username( $data ) {
		$first_name = isset( $data['first_name'] ) ? $data['first_name'] : '';
		$last_name  = isset( $data['last_name'] ) ? $data['last_name'] : '';
		$email      = isset( $data['user_email'] ) ? $data['user_email'] : '';
		$name = '';
		if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
			$name = str_replace( array( '.', '+' ), '', trim( $first_name . $last_name ) );
		} else {
			if ( ! empty( $email ) ) {
				$explode = explode( '@', $email );
				$name    = str_replace( array( '.', '+' ), '', $explode[0] );
			}
		}
		if ( username_exists( $name ) ) {
			$name = $name . substr( uniqid( '', true ), - 6 );
		}
		return sanitize_user( $name, true );
	}

	public function create_user( $data ) {
        $result = array(
            'error' => true
        );
		if ( false === self::can_manage_login() ) {
            $result['message'] = __("You don't have permission to create new temp user.");
			return $result;
		}
		$expiry_option = ! empty( $data['expiry'] ) ? $data['expiry'] : 'day';
        $redirect = ! empty( $data['redirect'] ) ? $data['redirect'] : 'default';
		$date          = ! empty( $data['custom_date'] ) ? $data['custom_date'] : '';
		$password   = $this->get_temp_password();
		$username   = $this->get_temp_username( $data );
		$first_name = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
		$last_name  = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';
		$email      = isset( $data['user_email'] ) ? sanitize_email( $data['user_email'] ) : '';
		$role       = ! empty( $data['role'] ) ? $data['role'] : 'subscriber';
		$user_args  = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'user_login' => strtolower($username),
			'user_pass'  => $password,
			'user_email' => sanitize_email( $email ),
			'role'       => $role,
		);
		if( isset($data['update_id']) && $data['update_id'] > 0 ){
		    $user_args['ID'] = absint($data['update_id']);
        }
		$user_id = wp_insert_user( $user_args );
		if ( is_wp_error( $user_id ) ) {
			$code = $user_id->get_error_code();
			$result['errcode'] = $code;
			$result['message'] = $user_id->get_error_message( $code );
		} else {
            $user = new WP_User( $user_id );
            if( isset($data['cap_pages']) && $data['cap_pages'] == 'none' ){
                $user->add_cap( 'edit_pages', false);
            } else {
                $user->remove_cap( 'edit_pages' );
            }
            update_user_meta( $user_id, '_toolkit_cap_allow_pages', $data['cap_pages'] );
            if( isset($data['cap_posts']) && $data['cap_posts'] == 'none' ){
                $user->add_cap( 'edit_posts', false);
            } else {
                $user->remove_cap( 'edit_posts' );
            }
            update_user_meta( $user_id, '_toolkit_cap_allow_posts', $data['cap_posts'] );
            if( isset($data['cap_plugins']) && $data['cap_plugins'] == 'none' ){
                $user->add_cap( 'install_plugins', false);
                $user->add_cap( 'activate_plugins', false);
                $user->add_cap( 'edit_plugins', false);
            } elseif( isset($data['cap_plugins']) && $data['cap_plugins'] == 'view' ){
                $user->add_cap( 'install_plugins', false);
                $user->add_cap( 'edit_plugins', false);
            } else {
                $user->remove_cap( 'install_plugins' );
                $user->remove_cap( 'activate_plugins' );
                $user->remove_cap( 'edit_plugins' );
            }
            update_user_meta( $user_id, '_toolkit_cap_allow_plugins', $data['cap_plugins'] );
            $user->add_cap( 'list_users', false);
            $user->add_cap( 'remove_users', false);
            $user->add_cap( 'delete_users', false);
            $user->add_cap( 'create_users', false);
            $user->add_cap( 'edit_users', false);
            if( isset($data['cap_woocommerce']) && $data['cap_woocommerce'] == 'none' ){
                update_user_meta( $user_id, '_toolkit_cap_allow_woocommerce', 'none' );
                $user->add_cap( 'edit_shop_orders', false);
            } else {
                update_user_meta( $user_id, '_toolkit_cap_allow_woocommerce', 'view-edit' );
                $user->remove_cap( 'edit_shop_orders' );
            }
			if ( is_multisite() && ! empty( $data['super_admin'] ) && 'on' === $data['super_admin'] ) {
				grant_super_admin( $user_id );
				$sites = get_sites( array( 'deleted' => '0' ) );
				if ( ! empty( $sites ) && count( $sites ) > 0 ) {
					foreach ( $sites as $site ) {
						if ( ! is_user_member_of_blog( $user_id, $site->blog_id ) ) {
							add_user_to_blog( $site->blog_id, $user_id, 'administrator' );
						}
					}
				}
			}
			update_user_meta( $user_id, '_toolkit_created_user', true );
			update_user_meta( $user_id, '_tfes_created', self::get_current_gmt_timestamp() );
			update_user_meta( $user_id, '_tfes_raw_expire', $expiry_option );
			update_user_meta( $user_id, '_tfes_raw_redirect', $redirect );
			update_user_meta( $user_id, '_tfes_expire', self::get_user_expire_time( $expiry_option, $date ) );
			update_user_meta( $user_id, '_tfes_token', self::generate_tfes_token( $user_id ) );
			update_user_meta( $user_id, 'show_welcome_panel', 0 );
			$locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
			update_user_meta( $user_id, 'locale', $locale );
			$result['error']   = false;
			$result['user_id'] = $user_id;
		}
		return $result;
	}

	public static function update_user( $user_id, $data ) {

		if ( false === self::can_manage_login() || ( 0 === $user_id ) ) {
			return 0;
		}
		$expiry_option = ! empty( $data['expiry'] ) ? $data['expiry'] : 'day';
		$date          = ! empty( $data['custom_date'] ) ? $data['custom_date'] : '';
		$first_name = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
		$last_name  = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';
		$role       = ! empty( $data['role'] ) ? $data['role'] : 'subscriber';
		$user_args  = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => $role,
			'ID'         => $user_id
		);
		$user_id = wp_update_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			$code = $user_id->get_error_code();

			return array(
				'error'   => true,
				'errcode' => $code,
				'message' => $user_id->get_error_message( $code ),
			);
		}
		if ( is_multisite() && ! empty( $data['super_admin'] ) && 'on' === $data['super_admin'] ) {
			grant_super_admin( $user_id );
		}
		update_user_meta( $user_id, '_tfes_updated', self::get_current_gmt_timestamp() );
		update_user_meta( $user_id, '_tfes_expire', self::get_user_expire_time( $expiry_option, $date ) );
		
		// Define User Locale
		$locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
		update_user_meta( $user_id, 'locale', $locale );
		return $user_id;
	}

	public static function get_expire_options() {
		$tkfe_access = array(
			'hour'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Hour', 'toolkit-for-elementor' ), 'timestamp' => HOUR_IN_SECONDS, 'order' => 5 ),
			'day'                  => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Day', 'toolkit-for-elementor' ), 'timestamp' => DAY_IN_SECONDS, 'order' => 15 ),
			'week'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Week', 'toolkit-for-elementor' ), 'timestamp' => WEEK_IN_SECONDS, 'order' => 25 ),
			'month'                => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Month', 'toolkit-for-elementor' ), 'timestamp' => MONTH_IN_SECONDS, 'order' => 30 ),
			'year'                 => array( 'group' => 'from_now', 'group_name' => __( 'Link Expiration', 'toolkit-for-elementor' ), 'label' => __( 'One Year', 'toolkit-for-elementor' ), 'timestamp' => MONTH_IN_SECONDS * 12, 'order' => 35 ),
			'hour_after_access'    => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'toolkit-for-elementor' ), 'label' => __( 'One Hour', 'toolkit-for-elementor' ), 'expiry_label' => __( '1 hour after access', 'toolkit-for-elementor' ), 'timestamp' => HOUR_IN_SECONDS, 'order' => 6 ),
			'day_after_access'     => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'toolkit-for-elementor' ), 'label' => __( 'One Day', 'toolkit-for-elementor' ), 'expiry_label' => __( '1 day after access', 'toolkit-for-elementor' ), 'timestamp' => DAY_IN_SECONDS, 'order' => 16 ),
			'week_after_access'    => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'toolkit-for-elementor' ), 'label' => __( 'One Week', 'toolkit-for-elementor' ), 'expiry_label' => __( '1 week after access', 'toolkit-for-elementor' ), 'timestamp' => WEEK_IN_SECONDS, 'order' => 26 ),
			'month_after_access'   => array( 'group' => 'after_access', 'group_name' => __( 'After Access', 'toolkit-for-elementor' ), 'label' => __( 'One Month', 'toolkit-for-elementor' ), 'expiry_label' => __( '1 month after access', 'toolkit-for-elementor' ), 'timestamp' => MONTH_IN_SECONDS, 'order' => 31 ),
			'custom_date'          => array( 'group' => 'custom', 'group_name' => __( 'Custom', 'toolkit-for-elementor' ), 'label' => __( 'Custom Date', 'toolkit-for-elementor' ), 'timestamp' => 0, 'order' => 35 ),
		);
		// User Options Expanded
		$tkfe_access = apply_filters( 'tfes_expiry_options', $tkfe_access );

		// Retrieve Order options and sort $tkfe_access array by it's array
		foreach ( $tkfe_access as $key => $options ) {
			$tkfe_access[ $key ]['order']        = ! empty( $options['order'] ) ? $options['order'] : 100;
			$tkfe_access[ $key ]['group']        = ! empty( $options['group'] ) ? $options['group'] : __( 'from_now', '' );
			$tkfe_access[ $key ]['group_name']  = ! empty( $options['group_name'] ) ? $options['group_name'] : __( 'From Now', '' );
			$tkfe_access[ $key ]['expiry_label'] = ! empty( $options['expiry_label'] ) ? $options['expiry_label'] : '';
			$orders[ $key ] = ! empty( $options['order'] ) ? $options['order'] : 100;
		}

		// Organize and sort $tkfe_access array by order value
		array_multisort( $orders, SORT_ASC, $tkfe_access );
		return $tkfe_access;
	}

    public static function get_redirect_options( $selected = '' ) {

        $pages = (array) get_pages();

        //array_unshift( $pages, array( 'ID' => 'home_page', 'post_title' => __( 'Home Page', 'toolkit-for-elementor' ) ) );
        //array_unshift( $pages, array( 'ID' => 'system_default', 'post_title' => __( 'System Default', 'toolkit-for-elementor' ) ) );
        //array_unshift( $pages, array( 'ID' => 'wp_dashboard', 'post_title' => __( 'Dashboard', 'toolkit-for-elementor' ) ) );

        $html = '';
        $html .= "\n\t<option value='default' ".($selected == 'default' ? 'selected' : '').">" . __( 'Default Page', 'toolkit-for-elementor' ) . "</option>";
        $html .= "\n\t<option value='home_page' ".($selected == 'home_page' ? 'selected' : '').">" . __( 'Home Page', 'toolkit-for-elementor' ) . "</option>";
        $html .= "\n\t<option value='wp_dashboard' ".($selected == 'wp_dashboard' ? 'selected' : '').">" . __( 'WP Dashboard', 'toolkit-for-elementor' ) . "</option>";
        if ( count( $pages ) > 0 ) {

            $html .= "<optgroup label='" . __( 'Pages', 'toolkit-for-elementor' ) . "'>";
            foreach ( $pages as $page ) {
                $page = (array) $page;
                // preselect specified role
                if ( $selected == $page['ID'] ) {
                    $html .= "\n\t<option selected='selected' value='" . esc_attr( $page['ID'] ) . "'>" . $page['post_title'] . "</option>";
                } else {
                    $html .= "\n\t<option value='" . esc_attr( $page['ID'] ) . "'>" . $page['post_title'] . "</option>";
                }
            }
            $html .= "</optgroup>";
        }

        return $html;
    }

	public function get_temp_password() {
		return wp_generate_password( absint( 15 ), true, false );

	}

	public static function get_user_expire_time( $expiry_option = 'day', $date = '' ) {
		$tkfe_access = self::get_expire_options();
		$expiry_option = in_array( $expiry_option, array_keys( $tkfe_access ) ) ? $expiry_option : 'day';
		if ( 'custom_date' === $expiry_option ) {

			// For our custom date option we need to simply expire login at particular date and don't need to do anything to the current timestamp
			$current_timestamp = 0;
			$timestamp         = strtotime( $date );
		} elseif ( strpos( $expiry_option, '_after_access' ) > 0 ) {
			return $expiry_option;
		} else {
			// Retrieves current gmt timestamp and expires temporary login after specified time
			$current_timestamp = self::get_current_gmt_timestamp();
			$timestamp         = $tkfe_access[ $expiry_option ]['timestamp'];
		}
		return $current_timestamp + floatval( $timestamp );
	}
	
	public static function get_current_gmt_timestamp() {
		return strtotime( gmdate( 'Y-m-d H:i:s', time() ) );
	}
	
	public function get_temp_logins( $role = '' ) {
		$args = array(
			'fields'     => 'all',
			'meta_key'   => '_tfes_expire',
			'order'      => 'DESC',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				0 => array(
					'key'   => '_toolkit_created_user',
					'value' => 1,
				),
			),
		);
		
		if ( ! empty( $role ) ) {
			$args['role'] = $role;
		}

		$users = new WP_User_Query( $args );
		$users_data = $users->get_results();
		return $users_data;
	}
	
	public static function format_date_display( $stamp = 0, $type = 'date_format' ) {

		$type_format = 'date_format';
		if ( 'date_format' === $type ) {
			$type_format = get_option( 'date_format' );
		} elseif ( 'time_format' === $type ) {
			$type_format = get_option( 'time_format' );
		}

		$timezone = get_option( 'timezone_string' );
		if ( empty( $timezone ) ) {
			return date( $type_format, $stamp );
		}
		$date = new DateTime( '@' . $stamp );
		$date->setTimezone( new DateTimeZone( $timezone ) );
		return $date->format( $type_format );

	}

	public static function get_redirect_link( $result = array() ) {
		if ( empty( $result ) ) {
			return false;
		}
		$base_url = menu_page_url( 'toolkit-performance-tool', false );
		if ( empty( $base_url ) ) {
			return false;
		}
		$query_string = '';
		if ( ! empty( $result['status'] ) ) {
			if ( 'success' === $result['status'] ) {
				$query_string .= '&tfes_success=1';
			} elseif ( 'error' === $result['status'] ) {
				$query_string .= '&tfes_error=1';
			}
		}
		if ( ! empty( $result['message'] ) ) {
			$query_string .= '&tfes_message=' . $result['message'];
		}
		if ( ! empty( $result['tab'] ) ) {
			$query_string .= '&tab=' . $result['tab'];
		}
		$redirect_link = $base_url . $query_string;
		return $redirect_link;
	}
	
	public static function can_manage_login( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $user_id ) ) {
			return false;
		}
		// Temporary Users Cannot Manage/Edit Temporary Users
		$check = get_user_meta( $user_id, '_toolkit_created_user', true );
		return ! empty( $check ) ? false : true;
	}

	public static function is_login_expired( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( empty( $user_id ) ) {
			return false;
		}
		$expire = get_user_meta( $user_id, '_tfes_expire', true );
		return ! empty( $expire ) && is_numeric( $expire ) && self::get_current_gmt_timestamp() >= floatval( $expire ) ? true : false;
	}

	public static function generate_tfes_token( $user_id ) {
		$str = $user_id . time() . uniqid( '', true );
		return md5( $str );
	}

	public function get_valid_user_based_on_tfes_token( $token = '', $fields = 'all' ) {
		if ( empty( $token ) ) {
			return false;
		}

		$args = array(
			'fields'     => $fields,
			'meta_key'   => '_tfes_expire',
			'order'      => 'DESC',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				0 => array(
					'key'     => '_tfes_token',
					'value'   => sanitize_text_field( $token ),
					'compare' => '=',
				),
			),
		);

		$users = new WP_User_Query( $args );
		$users_data = $users->get_results();
		if ( empty( $users_data ) ) {
			return false;
		}
		foreach ( $users_data as $key => $user ) {
			$expire = get_user_meta( $user->ID, '_tfes_expire', true );
			if ( is_string( $expire ) && strpos( $expire, '_after_access' ) ) {
				$tkfe_access = self::get_expire_options();
				$timestamp      = ! empty( $tkfe_access[ $expire ] ) ? $tkfe_access[ $expire ]['timestamp'] : 0;
				$expire         = self::get_current_gmt_timestamp() + $timestamp;
				update_user_meta( $user->ID, '_tfes_expire', $expire );
			} elseif ( $expire <= self::get_current_gmt_timestamp() ) {
				unset( $users_data[ $key ] );
			}
		}
		return $users_data;
	}
	
	public static function is_valid_temp_login( $user_id = 0, $check_expiry = true ) {
		if ( empty( $user_id ) ) {
			return false;
		}
		$check = get_user_meta( $user_id, '_toolkit_created_user', true );
		if ( ! empty( $check ) && $check_expiry ) {
			$check = ! ( self::is_login_expired( $user_id ) );
		}
		return ! empty( $check ) ? true : false;

	}

	/**
	 * Retrieive Access Link
	 * @since 1.1
	 */
	public static function get_manage_login_url( $user_id, $action = '' ) {
		if ( empty( $user_id ) || empty( $action ) ) {
			return '';
		}
		$base_url = menu_page_url( 'wp-toolkit-for-elementor', false );
		$args     = array();
		$valid_actions = array( 'disable', 'enable', 'delete', 'update' );
		if ( in_array( $action, $valid_actions ) ) {
			$args = array(
				'tfes_action' => $action,
				'user_id'      => $user_id,
			);
		}
		$manage_login_url = '';
		if ( ! empty( $args ) ) {
			$base_url         = add_query_arg( $args, trailingslashit( $base_url ) );
			$manage_login_url = wp_nonce_url( $base_url, 'manage-temporary-login_' . $user_id, 'manage-temporary-login' );
		}
		return $manage_login_url;
	}

	public function get_login_url( $user_id ) {
		if ( empty( $user_id ) ) {
			return '';
		}
		$is_valid_temp_login = self::is_valid_temp_login( $user_id, false );
		if ( ! $is_valid_temp_login ) {
			return '';
		}
		$tfes_token = get_user_meta( $user_id, '_tfes_token', true );
		if ( empty( $tfes_token ) ) {
			return '';
		}
		$login_url = add_query_arg( 'tfes_token', $tfes_token, trailingslashit( admin_url() ) );
		// Compatibility with iThemes Security plugin if Custom URL Login defined
		$login_url = apply_filters( 'itsec_notify_admin_page_url', $login_url );

		return apply_filters( 'tfes_login_link', $login_url, $user_id );

	}

	/**
	 * Manage temporary logins
	 * @param int $user_id
	 * @param string $action
	 * @return bool
	 * @since 1.1
	 */
	
	public static function manage_login( $user_id = 0, $action = '' ) {
		if ( empty( $user_id ) || empty( $action ) ) {
			return false;
		}
		$is_valid_temp_login = self::is_valid_temp_login( $user_id, false );
		if ( ! $is_valid_temp_login ) {
			return false;
		}
		$manage_login = false;
		if ( 'disable' === $action ) {
			$manage_login = update_user_meta( $user_id, '_tfes_expire', self::get_current_gmt_timestamp() );
		} elseif ( 'enable' === $action ) {
			$manage_login = update_user_meta( $user_id, '_tfes_expire', self::get_user_expire_time() );
		}
		if ( $manage_login ) {
			return true;
		}
		return false;
	}

	/**
	 * Retrieve Time Elapsed String in a Readable Format
	 * @param int $time
	 * @param bool $ago
	 * @return string
	 * @since 1.1
	 */
	public static function time_elapsed_string( $time, $ago = false ) {
		if ( is_numeric( $time ) ) {
			if ( $ago ) {
				$etime = self::get_current_gmt_timestamp() - $time;
			} else {
				$etime = $time - self::get_current_gmt_timestamp();
			}
			if ( $etime < 1 ) {
				return __( 'Expired', 'toolkit-for-elementor' );
			}
			$a = array(
				// 365 * 24 * 60 * 60 => 'year',
				 30 * 24 * 60 * 60 => 'month',
				24 * 60 * 60 => 'day',
				60 * 60      => 'hour',
				60           => 'minute',
				1            => 'second',
			);
			$a_plural = array(
				'year'   => 'years',
				'month'  => 'months',
				'day'    => 'days',
				'hour'   => 'hours',
				'minute' => 'minutes',
				'second' => 'seconds',
			);
			foreach ( $a as $secs => $str ) {
				$d = $etime / $secs;
				if ( $d >= 1 ) {
					$return = round( $d );
					$time_string = ( $return > 1 ) ? $a_plural[ $str ] : $str;
					if ( $ago ) {
						return __( sprintf( '%d %s ago', $return, $time_string ), 'toolkit-for-elementor' );
					} else {
						return __( sprintf( '%d %s left', $return, $time_string ), 'toolkit-for-elementor' );
					}
				}
			}
			return __( 'Expired', 'toolkit-for-elementor' );
		} else {
			$tkfe_access = self::get_expire_options();
			return ! empty( $tkfe_access[ $time ] ) ? $tkfe_access[ $time ]['expiry_label'] : '';
		}
	}
	/**
	 * Temporary Users Cannot Access User-New, User-Edit, or Profile pages
	 * @return array
	 * @since 1.1
	 */
	public static function get_blocked_pages() {
		$blocked_pages = array( 'user-new.php', 'user-edit.php', 'profile.php' );
		$blocked_pages = apply_filters( 'tfes_restricted_pages_for_tkfe_uals', $blocked_pages );
		return $blocked_pages;
	}
	public function delete_tkfe_uals() {
		$tkfe_uals = $this->get_temp_logins();
		if ( count( $tkfe_uals ) > 0 ) {
			foreach ( $tkfe_uals as $user ) {
				if ( $user instanceof WP_User ) {
					wp_delete_user( $user->ID );
				}
			}
		}
	}

	public static function tfes_multi_select_dropdown_roles( $selected_roles = array() ) {
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
	/**
	 * Get tkfe_ual details.
	 * @param int $user_id
	 * @return array
	 * @since 1.1
	 */
	public static function get_temp_logins_data( $user_id = 0 ) {

		$user_data = array();
		if ( $user_id ) {

			$is_tfes_user = get_user_meta( $user_id, '_toolkit_created_user', true );

			if ( $is_tfes_user ) {

				$tkfe_ual_info = get_userdata( $user_id );

				$email      = $tkfe_ual_info->user_email;
				$first_name = $tkfe_ual_info->first_name;
				$last_name  = $tkfe_ual_info->last_name;
				$role       = array_shift( $tkfe_ual_info->roles );
				$meta = get_user_meta($user_id);
				$created_on  = isset($meta['_tfes_created'][0]) ? $meta['_tfes_created'][0] : '';
				$expire_on   = isset($meta['_tfes_expire'][0]) ? $meta['_tfes_expire'][0] : '';
				$tfes_token = isset($meta['_tfes_token'][0]) ? $meta['_tfes_token'][0] : '';
				$user_locale = isset($meta['locale'][0]) ? $meta['locale'][0] : '';

				$user_data = array(
					'is_tfes_user' => $is_tfes_user,
					'email'        => $email,
					'first_name'   => $first_name,
					'last_name'    => $last_name,
					'created_on'   => $created_on,
					'expire_on'    => $expire_on,
					'tfes_token'    => $tfes_token,
					'role'         => $role,
					'locale'       => $user_locale
				);
			}
		}
		return $user_data;
	}
	/**
	 * Print out option html elements for role selectors.
	 * @param string $selected Slug for the role that should be already selected.
	 * @since 1.1
	 */
	public static function tfes_dropdown_roles( $visible_roles = array(), $selected = '' ) {
		$return = '';
		$editable_roles = array_reverse( get_editable_roles() );
		$visible_roles = ! empty( $visible_roles ) ? $visible_roles : array_keys( $editable_roles );
		/**
		 * NinjaSolution: Sometimes when editing a temporary user's settings, the $selected role may or may not be available in visible roles- if so, add $selected role into $visible_roles array
		 */
		if ( ! in_array( $selected, $visible_roles ) ) {
			$visible_roles[] = $selected;
		}
		foreach ( $editable_roles as $role => $details ) {
			if ( in_array( $role, $visible_roles ) ) {
				$name = translate_user_role( $details['name'] );
				// preselect specified role
				if ( $selected == $role ) {
					$return .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
				} else {
					$return .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
				}
			}
		}
		echo $return;
	}
	/**
	 * Generate mailto link to send temporary login link directly into email
	 *
	 * @param $email
	 * @param $tkfe_ual_link
	 *
	 * @return string Generated mail to link
	 * @since 1.1
	 *
	 */
	public static function generate_mailto_link( $email, $tkfe_ual_link ) {

		$tkfe_ual_link = urlencode( $tkfe_ual_link );
		$double_line_break    = '%0D%0A%0D%0A';    // as per RFC2368
		$mailto_greeting      = __( 'Hello,', 'toolkit-for-elementor' );
		$mailto_instruction   = __( 'Click the following link to log into the system:', 'toolkit-for-elementor' );
		$mailto_subject       = __( 'Temporary Login Link', 'toolkit-for-elementor' );
		$mailto_body          = "$mailto_greeting $double_line_break $mailto_instruction $double_line_break $tkfe_ual_link $double_line_break";

		return __( sprintf( "mailto:%s?subject=%s&body=%s", $email, $mailto_subject, $mailto_body ), 'toolkit-for-elementor' );
	}

	/**
	 * Render Quick Feedback Widget
	 *
	 * @param $params
	 *
	 */
	public static function render_feedback_widget( $params ) {
		global $tfes_feedback;

		$feedback = $tfes_feedback;

		$default_params = array(
			'set_transient' => true,
			'force'         => false
		);

		$params = wp_parse_args( $params, $default_params );

		if ( ! empty( $params['event'] ) ) {

			$event = $feedback->event_prefix . $params['event'];
			$force = ! empty( $params['force'] ) ? $params['force'] : false;

			$can_show = false;

			if ( $force ) {
				$can_show = true;
			} else {
				if ( ! $feedback->is_event_transient_set( $event ) ) {
					$can_show = true;

					$feedback_data = $feedback->get_event_feedback_data( $feedback->plugin_abbr, $event );
					if ( count( $feedback_data ) > 0 ) {
						$feedback_data          = array_reverse( $feedback_data );
						$last_feedback_given_on = $feedback_data[0]['created_on'];
						if ( strtotime( $last_feedback_given_on ) > strtotime( '-45 days' ) ) {
							$can_show = false;
						}
					}
				}
			}

			if ( $can_show ) {
				if ( 'star' === $params['type'] ) {
					$feedback->render_stars( $params );
				} elseif ( 'emoji' === $params['type'] ) {
					$feedback->render_emoji( $params );
				}
			}
		}

	}

	public static function get_tfes_meta_info() {

		$meta_info = array();

		return $meta_info;
	}

	/**
	 * Check whether TLWP admin page?
	 * @return bool
	 * @since 1.1
	 */
	public static function is_tfes_admin_page() {
		$pages = array(
			'users_page_wp-toolkit-for-elementor',
			'users_page_wp-toolkit-for-elementor-network'
		);
		$screen = get_current_screen();
		if ( in_array( $screen->id, $pages ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Check Whether current user is temporary user
	 * @since 1.1
	 */
	public static function is_current_user_valid_tkfe_ual() {
		$current_user_id = get_current_user_id();
		return self::is_valid_temp_login( $current_user_id );
	}
}
