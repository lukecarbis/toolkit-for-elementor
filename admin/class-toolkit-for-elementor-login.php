<?php
if( ! class_exists('Toolkit_For_Elementor_Login') ){
    class Toolkit_For_Elementor_Login {

        private $wp_login_php;
        private $settings;

        function __construct(){
            $this->settings = get_option('toolkit_login_page_url_options', array());
            if( isset($this->settings['logpage_enable']) && $this->settings['logpage_enable'] == 'on' ){
                $this->init();
            }
        }

        protected function init() {
            global $wp_version;

            if ( version_compare( $wp_version, '4.0-RC1-src', '<' ) ) {
                add_action( 'admin_notices', array( $this, 'admin_notices_incompatible' ) );
                add_action( 'network_admin_notices', array( $this, 'admin_notices_incompatible' ) );
                return;
            }


            if ( is_multisite() && ! function_exists( 'is_plugin_active_for_network' ) || ! function_exists( 'is_plugin_active' ) ) {
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }

            if ( is_plugin_active_for_network( 'rename-wp-login/rename-wp-login.php' ) || is_plugin_active_for_network( 'wps-hide-login/wps-hide-login.php' ) ) {
                add_action( 'network_admin_notices', array( $this, 'admin_notices_plugin_conflict' ) );
                return;
            }

            if ( is_plugin_active( 'rename-wp-login/rename-wp-login.php' ) || is_plugin_active( 'wps-hide-login/wps-hide-login.php' ) ) {
                add_action( 'admin_notices', array( $this, 'admin_notices_plugin_conflict' ) );
                return;
            }

            add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 9999 );
            add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
            add_action( 'setup_theme', array( $this, 'setup_theme' ), 1 );

            add_filter( 'site_url', array( $this, 'site_url' ), 10, 4 );
            add_filter( 'network_site_url', array( $this, 'network_site_url' ), 10, 3 );
            add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );
            add_filter( 'site_option_welcome_email', array( $this, 'toolkit_welcome_email' ) );

            remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

            add_action( 'template_redirect', array( $this, 'redirect_export_data' ) );
            add_filter( 'login_url', array( $this, 'login_url' ), 10, 3 );

            add_filter( 'user_request_action_email_content', array( $this, 'user_request_action_email_content' ), 999, 2 );

            add_filter( 'site_status_tests', array( $this, 'site_status_tests' ) );
        }

        public function site_status_tests( $tests ) {
            unset( $tests['async']['loopback_requests'] );

            return $tests;
        }

        public function user_request_action_email_content( $email_text, $email_data ) {
            $email_text = str_replace( '###CONFIRM_URL###', esc_url_raw( str_replace( $this->toolkit_login_slug() . '/', 'wp-login.php', $email_data['confirm_url'] ) ), $email_text );

            return $email_text;
        }

        private function use_trailing_slashes() {

            return ( '/' === substr( get_option( 'permalink_structure' ), - 1, 1 ) );

        }

        private function user_trailingslashit( $string ) {

            return $this->use_trailing_slashes() ? trailingslashit( $string ) : untrailingslashit( $string );

        }

        private function wp_template_loader() {
            global $pagenow;
            $pagenow = 'index.php';
            if ( ! defined( 'WP_USE_THEMES' ) ) {
                define( 'WP_USE_THEMES', true );
            }
            wp();
            require_once( ABSPATH . WPINC . '/template-loader.php' );
            die;
        }

        private function toolkit_login_slug() {
            if( isset($this->settings['logpage_url']) && $this->settings['logpage_url'] ){
                return trim($this->settings['logpage_url']);
            } else {
                return 'login';
            }
        }

        private function toolkit_redirect_slug() {
            if( isset($this->settings['logpage_red']) && trim($this->settings['logpage_red']) ){
                return trim($this->settings['logpage_red']);
            } else {
                return '404';
            }
        }

        public function toolkit_login_url( $scheme = null ) {
            $url = apply_filters( 'toolkit_hide_login_home_url', home_url( '/', $scheme ) );
            if ( get_option( 'permalink_structure' ) ) {
                return $this->user_trailingslashit( $url . $this->toolkit_login_slug() );
            } else {
                return $url . '?' . $this->toolkit_login_slug();
            }
        }

        public function toolkit_redirect_url( $scheme = null ) {
            if ( get_option( 'permalink_structure' ) ) {
                return $this->user_trailingslashit( home_url( '/', $scheme ) . $this->toolkit_redirect_slug() );
            } else {
                return home_url( '/', $scheme ) . '?' . $this->toolkit_redirect_slug();
            }
        }

        public function admin_notices_incompatible() {
            echo '<div class="error notice is-dismissible"><p>' . __( 'Please update to a supported version of WordPress to use ToolKit for Elementor.', 'toolkit-for-elementor' ) . ' <strong>' . __( 'ToolKit Update Login', 'toolkit-for-elementor' ) . '</strong>.</p></div>';
        }

        public function admin_notices_plugin_conflict() {
            echo '<div class="error notice is-dismissible"><p>' . __( 'ToolKit has detected a plugin that may cause conflicts with features already built into ToolKit. Please deactivate any conflicting plugins to minimize potential plugin conflicts.', 'toolkit-for-elementor' ) . '</p></div>';
        }

        public function redirect_export_data() {
            if ( ! empty( $_GET ) && isset( $_GET['action'] ) && 'confirmaction' === $_GET['action'] && isset( $_GET['request_id'] ) && isset( $_GET['confirm_key'] ) ) {
                $request_id = (int) $_GET['request_id'];
                $key        = sanitize_text_field( wp_unslash( $_GET['confirm_key'] ) );
                $result     = wp_validate_user_request_key( $request_id, $key );
                if ( ! is_wp_error( $result ) ) {
                    wp_redirect( add_query_arg( array(
                        'action'      => 'confirmaction',
                        'request_id'  => $_GET['request_id'],
                        'confirm_key' => $_GET['confirm_key']
                    ), $this->toolkit_login_url()
                    ) );
                    exit();
                }
            }
        }

        public function plugins_loaded() {
            global $pagenow;
            if ( ! is_multisite()
                && ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-signup' ) !== false
                    || strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-activate' ) !== false ) && apply_filters( 'toolkit_hide_login_signup_enable', false ) === false ) {

                wp_die( __( 'Sorry, this feature is not available.', 'toolkit-for-elementor' ) );

            }
            $request = parse_url( rawurldecode( $_SERVER['REQUEST_URI'] ) );

            if ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-login.php' ) !== false
                    || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-login', 'relative' ) ) )
                && ! is_admin() ) {

                $this->wp_login_php = true;
                $_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );
                $pagenow = 'index.php';

            } elseif ( ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === home_url( $this->toolkit_login_slug(), 'relative' ) )
                || ( ! get_option( 'permalink_structure' )
                    && isset( $_GET[ $this->toolkit_login_slug() ] )
                    && empty( $_GET[ $this->toolkit_login_slug() ] ) ) ) {

                $pagenow = 'wp-login.php';

            } elseif ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-register.php' ) !== false
                    || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-register', 'relative' ) ) )
                && ! is_admin() ) {

                $this->wp_login_php = true;
                $_SERVER['REQUEST_URI'] = $this->user_trailingslashit( '/' . str_repeat( '-/', 10 ) );
                $pagenow = 'index.php';
            }

        }

        public function setup_theme() {
            global $pagenow;

            if ( ! is_user_logged_in() && 'customize.php' === $pagenow ) {
                wp_die( __( 'This feature has been disabled', 'toolkit-for-elementor' ), 403 );
            }
        }

        public function wp_loaded() {

            global $pagenow;

            $request = parse_url( rawurldecode( $_SERVER['REQUEST_URI'] ) );

            if ( ! isset( $_POST['post_password'] ) ) {

                if ( is_admin() && ! is_user_logged_in() && ! defined( 'DOING_AJAX' ) && $pagenow !== 'admin-post.php' && $request['path'] !== '/wp-admin/options.php' ) {
                    wp_safe_redirect( $this->toolkit_redirect_url() );
                    die();
                }

                if ( $pagenow === 'wp-login.php'
                    && $request['path'] !== $this->user_trailingslashit( $request['path'] )
                    && get_option( 'permalink_structure' ) ) {

                    wp_safe_redirect( $this->user_trailingslashit( $this->toolkit_login_url() )
                        . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );

                    die;

                } elseif ( $this->wp_login_php ) {

                    if ( ( $referer = wp_get_referer() )
                        && strpos( $referer, 'wp-activate.php' ) !== false
                        && ( $referer = parse_url( $referer ) )
                        && ! empty( $referer['query'] ) ) {

                        parse_str( $referer['query'], $referer );

                        @require_once WPINC . '/ms-functions.php';

                        if ( ! empty( $referer['key'] )
                            && ( $result = wpmu_activate_signup( $referer['key'] ) )
                            && is_wp_error( $result )
                            && ( $result->get_error_code() === 'already_active'
                                || $result->get_error_code() === 'blog_taken' ) ) {

                            wp_safe_redirect( $this->toolkit_login_url()
                                . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );

                            die;

                        }

                    }

                    $this->wp_template_loader();

                } elseif ( $pagenow === 'wp-login.php' ) {
                    global $error, $interim_login, $action, $user_login;

                    $redirect_to = admin_url();

                    $requested_redirect_to = '';
                    if ( isset( $_REQUEST['redirect_to'] ) ) {
                        $requested_redirect_to = $_REQUEST['redirect_to'];
                    }

                    if ( is_user_logged_in() ) {
                        $user = wp_get_current_user();
                        if ( ! isset( $_REQUEST['action'] ) ) {
                            $logged_in_redirect = apply_filters( 'whl_logged_in_redirect', $redirect_to, $requested_redirect_to, $user );
                            wp_safe_redirect( $logged_in_redirect );
                            die();
                        }
                    }

                    @require_once ABSPATH . 'wp-login.php';

                    die;

                }

            }

        }

        public function site_url( $url, $path, $scheme, $blog_id ) {

            return $this->filter_wp_login_php( $url, $scheme );

        }

        public function network_site_url( $url, $path, $scheme ) {

            return $this->filter_wp_login_php( $url, $scheme );

        }

        public function wp_redirect( $location, $status ) {

            if ( strpos( $location, 'https://wordpress.com/wp-login.php' ) !== false ) {
                return $location;
            }

            return $this->filter_wp_login_php( $location );

        }

        public function filter_wp_login_php( $url, $scheme = null ) {

            if ( strpos( $url, 'wp-login.php?action=postpass' ) !== false ) {
                return $url;
            }

            if ( strpos( $url, 'wp-login.php' ) !== false && strpos( wp_get_referer(), 'wp-login.php' ) === false ) {
                if ( is_ssl() ) {
                    $scheme = 'https';
                }
                $args = explode( '?', $url );
                if ( isset( $args[1] ) ) {
                    parse_str( $args[1], $args );
                    if ( isset( $args['login'] ) ) {
                        $args['login'] = rawurlencode( $args['login'] );
                    }
                    $url = add_query_arg( $args, $this->toolkit_login_url( $scheme ) );
                } else {
                    $url = $this->toolkit_login_url( $scheme );
                }

            }
            return $url;

        }

        public function toolkit_welcome_email( $value ) {

            return $value = str_replace( 'wp-login.php', trailingslashit( $this->toolkit_login_slug() ), $value );

        }

        public function forbidden_slugs() {

            $wp = new \WP;

            return array_merge( $wp->public_query_vars, $wp->private_query_vars );

        }

        /**
         *
         * Update url redirect : wp-admin/options.php
         * @param $login_url
         * @param $redirect
         * @param $force_reauth
         * @return string
         */
        public function login_url( $login_url, $redirect, $force_reauth ) {
            global $wp_query;
            if ( isset( $wp_query ) && is_404() ) {
                return '#';
            }

            if ( $force_reauth === false ) {
                return $login_url;
            }

            if ( empty( $redirect ) ) {
                return $login_url;
            }

            $redirect = explode( '?', $redirect );

            if ( $redirect[0] === admin_url( 'options.php' ) ) {
                $login_url = admin_url();
            }

            return $login_url;
        }

    }
}
