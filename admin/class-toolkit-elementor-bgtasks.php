<?php
if( ! class_exists('Toolkit_Elementor_BgTasks') ) {

    class Toolkit_Elementor_BgTasks{

        public function __construct(){

        }

        public function get_heartbeat_options(){
            return array(
                ''          => sprintf(__('%s Seconds', 'toolkit-for-elementor'), '15') . ' (' . __('Default', 'toolkit-for-elementor') . ')',
                'disable'   => __('Disable', 'toolkit-for-elementor'),
                '30'        => sprintf(__('%s Seconds', 'toolkit-for-elementor'), '30'),
                '45'        => sprintf(__('%s Seconds', 'toolkit-for-elementor'), '45'),
                '60'        => sprintf(__('%s Seconds', 'toolkit-for-elementor'), '60')
            );
        }

        public function get_post_revision_options() {
            return array(
                ''      => __('Default', 'toolkit-for-elementor'),
                'false' => __('Disable', 'toolkit-for-elementor'),
                '1'     => '1',
                '2'     => '2',
                '3'     => '3',
                '4'     => '4',
                '5'     => '5',
                '10'    => '10',
                '15'    => '15',
                '20'    => '20',
                '25'    => '25',
                '30'    => '30'
            );
        }

        public function get_autosave_options(){
            return array(
                ''      => __('1 Minute', 'toolkit-for-elementor') . ' (' . __('Default', 'toolkit-for-elementor') . ')',
                '86400' => __('Disable', 'toolkit-for-elementor'),
                '120'   => sprintf(__('%s Minutes', 'toolkit-for-elementor'), '2'),
                '180'   => sprintf(__('%s Minutes', 'toolkit-for-elementor'), '3'),
                '240'   => sprintf(__('%s Minutes', 'toolkit-for-elementor'), '4'),
                '300'   => sprintf(__('%s Minutes', 'toolkit-for-elementor'), '5')
            );
        }

        public function get_core_tweak_options() {
            return array(
                ''      => __('Native Browser Lazy Load (Default)', 'toolkit-for-elementor'),
                'js'    => __('ToolKit Lazy Loading', 'toolkit-for-elementor')
            );
        }

        public function get_woo_cart_options() {
            return array(
                'home'  => __('Home Page Only', 'toolkit-for-elementor'),
                'site'  => __('Entire Site', 'toolkit-for-elementor')
            );
        }

        function notice_post_revisions() {
            echo "<div class='notice notice-error'>";
            echo "<p>";
            echo "<strong>" . __('Toolkit Warning', 'toolkit-for-elementor') . ":</strong> ";
            echo __('WP_POST_REVISIONS is already enabled somewhere else on your site. We suggest only enabling this feature in one place.', 'toolkit-for-elementor');
            echo "</p>";
            echo "</div>";
        }

        function notice_autosave_interval() {
            echo "<div class='notice notice-error'>";
            echo "<p>";
            echo "<strong>" . __('Toolkit Warning', 'toolkit-for-elementor') . ":</strong> ";
            echo __('AUTOSAVE_INTERVAL is already enabled somewhere else on your site. We suggest only enabling this feature in one place.', 'toolkit-for-elementor');
            echo "</p>";
            echo "</div>";
        }

        function save_settings(){
            if( isset($_POST['disable_heartbeat']) && trim($_POST['disable_heartbeat']) ){
                $options = array(
                    'disable_heartbeat'     => esc_sql($_POST['disable_heartbeat']),
                    'heartbeat_frequency'   => esc_sql($_POST['heartbeat_frequency']),
                    'disable_revision'      => esc_sql($_POST['disable_revision']),
                    'revision_frequency'    => esc_sql($_POST['revision_frequency']),
                    'disable_autosave'      => esc_sql($_POST['disable_autosave']),
                    'autosave_interval'     => esc_sql($_POST['autosave_interval'])
                );
                update_option('toolkit_background_tasks_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings has saved successfully", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function disable_heartbeat(){
            if( is_admin() ){
                wp_deregister_script('heartbeat');
                if(is_admin()) {
                    wp_register_script('heartbeat', TOOLKIT_FOR_ELEMENTOR_URL . 'admin/js/heartbeat.js');
                    wp_enqueue_script('heartbeat', TOOLKIT_FOR_ELEMENTOR_URL . 'admin/js/heartbeat.js');
                }
            }
        }

        function heartbeat_frequency($settings) {
            $bgTasksOpts = get_option('toolkit_background_tasks_options', array());
            $settings['interval'] = $bgTasksOpts['heartbeat_frequency'];
            return $settings;
        }

        function disable_dashicons() {
            if( ! is_user_logged_in() ) {
                wp_dequeue_style('dashicons');
                wp_deregister_style('dashicons');
            }
        }

        function disable_rest_api_errors($result) {
            if( ! empty($result) ) {
                return $result;
            } else{
                $unloadOpts = get_option('toolkit_unload_options', array());
                $disabled = false;
                $rest_route = $GLOBALS['wp']->query_vars['rest_route'];
                if(strpos($rest_route, 'contact-form-7') !== false) {
                    return;
                }

                if($unloadOpts['condition_restapi'] == 'disable_non_admins' && !current_user_can('manage_options')) {
                    $disabled = true;
                } elseif($unloadOpts['condition_restapi'] == 'disable_logged_out' && !is_user_logged_in()) {
                    $disabled = true;
                }
            }
            if( $disabled ) {
                return new WP_Error('rest_authentication_error', __('Sorry, you do not have permission to make REST API requests.', 'toolkit-for-elementor'), array('status' => 401));
            }
            return $result;
        }

        function disable_oembed() {
            global $wp;
            $wp->public_query_vars = array_diff($wp->public_query_vars, array('embed',));
            remove_action( 'rest_api_init', 'wp_oembed_register_route' );
            add_filter( 'embed_oembed_discover', '__return_false' );
            remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
            remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
            remove_action( 'wp_head', 'wp_oembed_add_host_js' );
            add_filter( 'tiny_mce_plugins', array($this, 'disable_embeds_tiny_mce_plugin') );
            add_filter( 'rewrite_rules_array', array($this, 'disable_embeds_rewrites') );
            remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
        }

        function disable_embeds_tiny_mce_plugin($plugins) {
            return array_diff($plugins, array('wpembed'));
        }

        function disable_embeds_rewrites($rules) {
            foreach($rules as $rule => $rewrite) {
                if(false !== strpos($rewrite, 'embed=true')) {
                    unset($rules[$rule]);
                }
            }
            return $rules;
        }

        function disable_rssfeed() {
            if(!is_feed() || is_404()) {
                return;
            }
            global $wp_rewrite;
            global $wp_query;
            if(isset($_GET['feed'])) {
                wp_redirect(esc_url_raw(remove_query_arg('feed')), 301);
                exit;
            }
            if(get_query_var('feed') !== 'old') {
                set_query_var('feed', '');
            }
            redirect_canonical();
            wp_die(sprintf(__("No feed available, please visit the <a href='%s'>homepage</a>!"), esc_url(home_url('/'))));
        }

        function remove_x_pingback($headers) {
            unset($headers['X-Pingback'], $headers['x-pingback']);
            return $headers;
        }

        function remove_query_strings() {
            if( !is_admin() ) {
                add_filter('script_loader_src', array($this, 'remove_query_strings_split'), 15);
                add_filter('style_loader_src', array($this, 'remove_query_strings_split'), 15);
            }
        }

        function remove_jquery_migrate($scripts) {
            if( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
                $scripts->registered['jquery']->deps = array_diff(
                    $scripts->registered['jquery']->deps,
                    [ 'jquery-migrate' ]
                );
            }
        }

        function remove_query_strings_split($src) {
            if(strpos($src, '/admin/js/toolkit-scripts.min.js')) {
                return $src;
            }
            $output = preg_split("/(&ver|\?ver)/", $src);
            return $output[0];
        }

        function save_login_page_settings(){
            if( isset($_POST['bgimage']) && isset($_POST['bgsize']) && isset($_POST['logpage_enable']) ){
                $options = array(
                    'logpage_enable'    => $_POST['logpage_enable'] ? esc_sql($_POST['logpage_enable']) : 'off',
                    'logpage_url'       => $_POST['logpage_url'] ? esc_sql($_POST['logpage_url']) : 'login',
                    'logpage_red'       => $_POST['logpage_red'] ? esc_sql($_POST['logpage_red']) : '404'
                );
                update_option('toolkit_login_page_url_options', $options);
                $options = array(
                    'bgimage'   => esc_sql($_POST['bgimage']),
                    'bgsize'    => esc_sql($_POST['bgsize']),
                    'bgrepeat'  => esc_sql($_POST['bgrepeat']),
                    'logoimage' => esc_sql($_POST['logoimage']),
                    'lgwidth'   => esc_sql($_POST['lgwidth']),
                    'lgheight'  => esc_sql($_POST['lgheight']),
                    'logourl'   => esc_sql($_POST['logourl']),
                    'customcss' => $_POST['customcss']
                );
                update_option('toolkit_login_settings_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings has saved successfully", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function apply_login_page_settings(){
            $css_handler = 'toolkit_login_styles';
            wp_enqueue_style( $css_handler, TOOLKIT_FOR_ELEMENTOR_URL . 'admin/css/toolkit-login.css', array(), TOOLKIT_FOR_ELEMENTOR_VERSION, 'all' );
            $loginOpts = get_option( 'toolkit_login_settings_options' );
            $login_page_css = 'body.login {';
            if ( isset($loginOpts['bgimage']) && ! empty( $loginOpts['bgimage'] ) ) {
                $login_page_css .= 'background-image: url(" ' . $loginOpts['bgimage'] . ' ");';
            }
            if ( isset($loginOpts['bgsize']) && ! empty( $loginOpts['bgsize'] ) ) {
                $login_page_css .= 'background-size: ' . $loginOpts['bgsize'] . ';';
            }
            if ( isset($loginOpts['bgrepeat']) && ! empty( $loginOpts['bgrepeat'] ) ) {
                $login_page_css .= 'background-repeat: ' . $loginOpts['bgrepeat'] . ';';
            }
            $login_page_css .= '}';

            // Login Page Logo CSS
            if ( isset($loginOpts['logoimage']) && ! empty($loginOpts['logoimage'])
            && isset($loginOpts['lgwidth']) && ! empty($loginOpts['lgwidth'])
            && isset($loginOpts['lgheight']) && ! empty($loginOpts['lgheight']) ) {
                $login_page_css .= 'body.login div#login h1 {';
                $login_page_css .= 'position: absolute; left: calc(50% - '.($loginOpts['lgwidth']/2).'px);';
                $login_page_css .= '}';
                $login_page_css .= 'body.login div#login form {';
                $login_page_css .= 'margin-top: '.($loginOpts['lgheight'] + 20).'px;';
                $login_page_css .= '}';
            }
            $login_page_css .= 'body.login div#login h1 a {';
            if ( isset($loginOpts['logoimage']) && ! empty($loginOpts['logoimage']) ) {
                $login_page_css .= 'background-image: url(" ' . $loginOpts['logoimage'] . ' ");';
            }
            if ( isset($loginOpts['lgwidth']) && ! empty($loginOpts['lgwidth']) ) {
                $login_page_css .= 'width: ' . $loginOpts['lgwidth'] . 'px;';
            }
            if ( isset($loginOpts['lgheight']) && ! empty($loginOpts['lgheight']) ) {
                $login_page_css .= 'height: ' . $loginOpts['lgheight'] . 'px;';
            }
            if ( isset($loginOpts['lgwidth']) && isset($loginOpts['lgheight']) && ! empty($loginOpts['lgwidth']) || ! empty($loginOpts['lgheight']) ) {
                $login_page_css .= 'background-size: ' . $loginOpts['lgwidth'] . 'px ' . $loginOpts['lgheight'] . 'px;';
            }
            $login_page_css .= '}';
            if ( isset($loginOpts['customcss']) && ! empty( $loginOpts['customcss'] ) ) {
                $login_page_css .= $loginOpts['customcss'];
            }
            wp_add_inline_style( $css_handler, $login_page_css );
        }

        function login_headerurl($url){
            $loginOpts = get_option( 'toolkit_login_settings_options' );
            if ( isset($loginOpts['logourl']) && ! empty( $loginOpts['logourl'] ) ) {
                return $loginOpts['logourl'];
            }
            return $url;
        }

        function reset_cache_on_update( $wp_upgrader, $options ) {

            if ( ! isset( $options['action'], $options['type'] ) || $options['action'] !== 'update' ) {
                return;
            }

            if( $options['type'] === 'plugin' && isset($options['plugins']) && is_array($options['plugins']) ){
                toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH);
            }

            if( $options['type'] === 'theme' && isset($options['themes']) && is_array($options['themes']) ){
                $current_theme = wp_get_theme();
                $themes        = [
                    $current_theme->get_template(),
                    $current_theme->get_stylesheet(),
                ];
                if ( ! empty( array_intersect( $options['themes'], $themes ) ) ) {
                    toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH);
                }
            }
            //ToolKit is updating, do something
            /*$plugins = array_flip( $options['plugins'] );
            $toolkit_path = plugin_basename( TOOLKIT_FOR_ELEMENTOR_FILE );
            if ( isset( $plugins[ $toolkit_path ] ) ) {
                return;
            }*/

        }

        function reset_cache_on_deactivate($plugin){
            toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH);
        }
					
    }

}
