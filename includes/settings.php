<?php
if ( ! class_exists('Lazy_load_Settings' ) ) {
    class Lazy_load_Settings
    {
        private $lazy_load_setting;

        public function __construct()
        {
            $this->lazy_load_setting = new Toolkit_Elementor;
            $this->nonce_key = 'toolkit-elementor';
            ########		IS SCRIPT ACTIVE		##########
            $this->toolkit_active = get_option('toolkit_active', '');
            $this->toolkit_license_key = sanitize_text_field(trim(get_option('toolkit_license_key', '')));
            $this->toolkit_license_details = get_option('toolkit_license_details', '');
            $this->toolkit_other_details = get_option('toolkit_other_details', '');
            $this->gtmetrix_locations = array(
                'North America' => array(
                    '10' => array('name' => 'Cheyenne, WY, USA', 'default' => '', 'id' => 10, 'browsers' => [1, 3]),
                    '11' => array('name' => 'Chicago,  IL, USA', 'default' => '', 'id' => 11, 'browsers' => [1, 3]),
                    '4' => array('name' => 'Dallas, USA', 'default' => '', 'id' => 4, 'browsers' => [1, 3]),
                    '12' => array('name' => 'Danville, VA, USA', 'default' => '', 'id' => 12, 'browsers' => [1, 3]),
                    '8' => array('name' => 'Quebec City, Canada', 'default' => '', 'id' => 8, 'browsers' => [1, 3]),
                    '9' => array('name' => 'San Francisco, CA, USA', 'default' => '', 'id' => 9, 'browsers' => [1, 3]),
                    '1' => array('name' => 'Vancouver, Canada', 'default' => 1, 'id' => 1, 'browsers' => [1, 3]),
                ),
                'Latin America' => array(
                    '6' => array('name' => 'São Paulo, Brazil', 'default' => '', 'id' => 6, 'browsers' => [1, 3]),
                ),
                'Europe'    => array(
                    '13' => array('name' => 'Amsterdam, Netherlands', 'default' => '', 'id' => 13, 'browsers' => [1, 3]),
                    '15' => array('name' => 'Frankfurt, Germany', 'default' => '', 'id' => 15, 'browsers' => [1, 3]),
                    '2' => array('name' => 'London, UK', 'default' => '', 'id' => 2, 'browsers' => [1, 3]),
                    '14' => array('name' => 'Paris, France', 'default' => '', 'id' => 14, 'browsers' => [1, 3]),
                    '16' => array('name' => 'Stockholm, Sweden', 'default' => '', 'id' => 16, 'browsers' => [1, 3]),
                ),
                'Middle East'   => array(
                    '22' => array('name' => 'Dubai, UAE', 'default' => '', 'id' => 22, 'browsers' => [1, 3])
                ),
                'Asia Pacific'  => array(
                    '19' => array('name' => 'Busan, South Korea', 'default' => '', 'id' => 19, 'browsers' => [1, 3]),
                    '20' => array('name' => 'Chennai, India', 'default' => '', 'id' => 20, 'browsers' => [1, 3]),
                    '7' => array('name' => 'Hong Kong, China', 'default' => '', 'id' => 7, 'browsers' => [1, 3]),
                    '5' => array('name' => 'Mumbai, India', 'default' => '', 'id' => 5, 'browsers' => [1, 3]),
                    '17' => array('name' => 'Singapore', 'default' => '', 'id' => 17, 'browsers' => [1, 3]),
                    '3' => array('name' => 'Sydney, Australia', 'default' => '', 'id' => 3, 'browsers' => [1, 3]),
                    '18' => array('name' => 'Tokyo, Japan', 'default' => '', 'id' => 18, 'browsers' => [1, 3]),
                ),
                'Africa'    => array(
                    '21' => array('name' => 'Johannesburg, South Africa', 'default' => '', 'id' => 21, 'browsers' => [1, 3]),
                )
            );
            $default_browsers = array(
                '1' => array(
                    'features' => array('dns' => 1, 'cookies' => 1, 'adblock' => 1, 'http_auth' => 1, 'video' => 1, 'user_agent' => 1, 'throttle' => 1, 'filtering' => 1, 'resolution' => 1),
                    'browser' => 'firefox',
                    'name' => 'Firefox (Desktop)',
                    'platform' => 'desktop',
                    'id' => 1,
                    'device' => ''
                ),
                '3' => array(
                    'features' => array('dns' => 1, 'cookies' => 1, 'adblock' => 1, 'http_auth' => 1, 'video' => 1, 'user_agent' => 1, 'throttle' => 1, 'filtering' => 1, 'resolution' => 1),
                    'browser' => 'chrome',
                    'name' => 'Chrome (Desktop)',
                    'platform' => 'desktop',
                    'id' => 3, 'device' => ''
                )
            );
            $this->gtmetrix_browsers = get_option('toolkit_gtmetrix_browsers', array());
            if( ! $this-> gtmetrix_browsers ){
                $this->gtmetrix_browsers = $default_browsers;
                update_option('toolkit_gtmetrix_browsers', $default_browsers);
            }
            ########		LICENSE KEY				##########
            $this->time_now = date('Y-m-d H:i:s');
            $this->limit = 5; //BOOSTER PER PAGE
            $this->updateDiff = 10; //IN MINUTES 720 (12 HOURS)
            $this->limitWebsite = 10; //MY LICENSE PER PAGE
            $this->scan_url = esc_url_raw('https://toolkitforelementor.com/api/gtmetrix-scan.php');
            $this->report_download_url = esc_url_raw('https://toolkitforelementor.com/api/gtmetrix-report-download.php');
            ########	REDIRECT AFTER ACTIVATION	##########
            add_action('admin_init', array($this, 'admin_inits'));
            add_action('admin_menu', array($this, 'admin_menu'), 502);
            add_action('admin_enqueue_scripts', array($this, "toolkit_enqueue_script"));
            ########		GTMETRIX SCAN CALL				##########
            add_action('wp_ajax_toolkit_performance_gtmetrix_scan', array($this, 'toolkit_performance_gtmetrix_scan'));
            add_action('wp_ajax_toolkit_performance_gtmetrix_scan_result', array($this, 'toolkit_performance_gtmetrix_scan_result'));
            ########		GTMETRIX FULL REPORT DOWNLOAD API CALL				##########
            add_action('wp_ajax_toolkit_performance_gtmetrix_download_report', array($this, 'toolkit_performance_gtmetrix_download_report'));
            add_action('wp_ajax_toolkit_performance_gtmetrix_history', array($this, 'toolkit_performance_gtmetrix_history'));
            ########		LICENSE KEY VERIFY API CALL				##########
            add_action('wp_ajax_toolkit_license_key_verify', array($this, 'toolkit_license_key_verify'));
			add_action('wp_ajax_toolkit_deactivate_license', array($this, 'toolkit_deactivate_license'));
			add_action( 'toolkit_verify',  array($this, 'toolkit_check_license' ) );
			########		Widgets Enable/Disable API CALL				##########
			add_action('wp_ajax_disable_wordpress_widgets', array($this, 'disable_wordpress_widgets'));
			add_action( 'widgets_init', array($this, 'set_default_wordpress_widgets'), 100 );
			if( ! isset($_GET['page']) || $_GET['page'] != 'toolkit-performance-tool' ){
                add_action( 'widgets_init', array($this, 'disable_wordpress_widgets'), 100 );
            }
			add_action( 'load-index.php', array($this, 'disable_dashboard_widgets_with_remote_requests') );
			add_action( 'wp_dashboard_setup', array($this, 'dashboard_widgets_toolkit_disable'), 100 );
			add_action( 'wp_network_dashboard_setup', array($this, 'dashboard_widgets_toolkit_disable'), 100 );
			//add_action( 'admin_init', array($this, 'get_default_dashboard_widgets'), 100 );
			add_action( 'wp_ajax_dashboard_widgets_toolkit_disable', array($this, 'dashboard_widgets_toolkit_disable'), 100 ); 
			add_action('wp_ajax_disable_elementor_widgets', array($this, 'disable_elementor_widgets'));	
			add_action('elementor/widgets/widgets_registered', array($this , 'toolkit_disable_elementor_elements'), 15);
            ########			SERVER SETTING TWEAK SAVE				##########
            add_action('wp_ajax_toolkit_server_setting_save', array($this, 'toolkit_server_setting_save'));
            add_action('wp_ajax_toolkit_unload_options_save', array($this, 'toolkit_unload_options_save'));
        }

        public function admin_inits()
        {
            if( isset($_GET['page']) && $_GET['page'] == 'toolkit-performance-tool' ){
                $this->lazy_load_setting->set_sections($this->get_settings_sections());
                $this->lazy_load_setting->set_fields($this->get_settings_fields());
                $this->lazy_load_setting->admin_init();
            }
        }

        /*****        PLUGIN EXTERNAL STYPE & STYLE REGISTER    *****/
        public function toolkit_enqueue_script()
        {
            $handler = TOOLKIT_FOR_ELEMENTOR_NAME;
            if (isset($_GET['page']) && $_GET['page'] == 'toolkit-performance-tool') {
                if ( ! did_action( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                }
                wp_enqueue_style('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css', array(), '4.1.0-beta.1');
                wp_enqueue_script('select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array('jquery'), '4.1.0-beta.1', true);
                wp_enqueue_style($handler, TOOLKIT_FOR_ELEMENTOR_URL . 'admin/css/toolkit-styles.min.css', array());
            }
            wp_enqueue_script($handler, TOOLKIT_FOR_ELEMENTOR_URL . 'admin/js/toolkit-scripts.min.js', array(), false, true);
            wp_localize_script($handler, 'toolkit', array('ajax_url'=>admin_url('admin-ajax.php'), 'post_url'=>admin_url('admin-post.php'), 'admin_url'=>admin_url(), 'site_url'=>site_url(), 'site_name'=>get_bloginfo('name'), 'temp_thumb'=>TOOLKIT_FOR_ELEMENTOR_URL.'admin/images/syncer-template-thumbnail.png', '_nonce'=>wp_create_nonce($this->nonce_key)));
        }

        public function getGtmetrixScanHistory($limit = 10, $offset = 0)
        {
            ob_start();
            include_once TOOLKIT_FOR_ELEMENTOR_PATH.'admin/templates/gtmetrix-scan-history.php';
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }

        public function toolkit_server_setting_save()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST['_nonce'])) {
                    if (isset($_POST['gzip_compression'])) {
                        $tweakSetting = array();
                        $server_info = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
                        $is_apache = ($server_info && stripos($server_info, 'apache') !== false) ? true : false;
                        if (!$is_apache) {
                            $tweakSetting['encoding_header'] = 'off';
                            $tweakSetting['gzip_compression'] = 'off';
                            $tweakSetting['keep_alive'] = 'off';
                            $tweakSetting['ninja_etags'] = 'off';
                            $tweakSetting['leverage_caching'] = 'off';
                            $tweakSetting['expire_headers'] = 'off';
                        } else {
                            $tweakSetting['encoding_header'] = isset($_POST['encoding_header']) ? $_POST['encoding_header'] : 'off';
                            $tweakSetting['gzip_compression'] = isset($_POST['gzip_compression']) ? $_POST['gzip_compression'] : 'off';
                            $tweakSetting['keep_alive'] = isset($_POST['keep_alive']) ? $_POST['keep_alive'] : 'off';
                            $tweakSetting['ninja_etags'] = isset($_POST['ninja_etags']) ? $_POST['ninja_etags'] : 'off';
                            $tweakSetting['leverage_caching'] = isset($_POST['leverage_caching']) ? $_POST['leverage_caching'] : 'off';
                            $tweakSetting['expire_headers'] = isset($_POST['expire_headers']) ? $_POST['expire_headers'] : 'off';
                        }
                        update_option('toolkit_webserver_tweaks', $tweakSetting);
                        if (function_exists('flush_rewrite_rules')) {
                            flush_rewrite_rules();
                        }
                        toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                        $response = array('success' => true, 'message' => 'Updated Successfully', 'msg2'=>__("Cache has cleared successfully"));
                        if( ! $is_apache ){
                            $response['alert'] = __("We detect that you are on an NGINX server. NGINX servers do not use htaccess and actually have most of these settings already enabled");
                        }
                    } else {
                        $settingServer = array();
                        $settingServer['css_minify'] = isset($_POST['css_minify']) ? $_POST['css_minify'] : 'off';
                        $settingServer['css_combine'] = isset($_POST['css_combine']) ? $_POST['css_combine'] : 'off';
                        $settingServer['css_excelem'] = isset($_POST['css_excelem']) ? $_POST['css_excelem'] : 'off';
                        $settingServer['js_minify'] = isset($_POST['js_minify']) ? $_POST['js_minify'] : 'off';
                        $settingServer['js_combine'] = isset($_POST['js_combine']) ? $_POST['js_combine'] : 'off';
                        $settingServer['js_excelem'] = isset($_POST['js_excelem']) ? $_POST['js_excelem'] : 'off';
                        $settingServer['js_defer'] = isset($_POST['js_defer']) ? $_POST['js_defer'] : 'off';
                        $settingServer['js_delay'] = isset($_POST['js_delay']) ? $_POST['js_delay'] : 'off';
                        $settingServer['delayed_hkeywords'] = isset($_POST['delayed_hkeywords']) ? $_POST['delayed_hkeywords'] : '';
                        $settingServer['delayed_keywords'] = isset($_POST['delayed_keywords']) ? $_POST['delayed_keywords'] : '';
                        $settingServer['delayed_expages'] = isset($_POST['delayed_expages']) ? $_POST['delayed_expages'] : '';
                        $settingServer['defer_homeonly'] = isset($_POST['defer_homeonly']) ? $_POST['defer_homeonly'] : 'off';
                        $settingServer['jsdefer_inline'] = isset($_POST['jsdefer_inline']) ? $_POST['jsdefer_inline'] : 'off';
                        $settingServer['deferred_keywords'] = isset($_POST['deferred_keywords']) ? $_POST['deferred_keywords'] : '';
                        $settingServer['exclude_css_urls'] = isset($_POST['exclude_css_urls']) ? $_POST['exclude_css_urls'] : '';
                        $settingServer['lazy_render'] = isset($_POST['lazy_render']) ? $_POST['lazy_render'] : '';
                        $settingServer['exclude_js_urls'] = isset($_POST['exclude_js_urls']) ? $_POST['exclude_js_urls'] : '';
                        $settingServer['cdn_enable'] = isset($_POST['cdn_enable']) ? $_POST['cdn_enable'] : 'no';
                        $settingServer['cdn_url'] = isset($_POST['cdn_url']) ? $_POST['cdn_url'] : '';
                        $settingServer['cdn_files'] = isset($_POST['cdn_files']) ? $_POST['cdn_files'] : '';
                        $settingServer['exclude_cdn_urls'] = isset($_POST['exclude_cdn_urls']) ? $_POST['exclude_cdn_urls'] : '';
                        $settingServer['google_fonts'] = isset($_POST['google_fonts']) ? $_POST['google_fonts'] : 'off';
                        $settingServer['fallback_fonts'] = isset($_POST['fallback_fonts']) ? $_POST['fallback_fonts'] : 'off';
                        $settingServer['preload_fonts'] = isset($_POST['preload_fonts']) ? $_POST['preload_fonts'] : '';
                        toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                        update_option('toolkit_elementor_tweaks', $settingServer);
                        $response = array('success' => true, 'message' => 'Updated Successfully', 'msg2'=>__("Cache has cleared successfully"));
                    }
                } else {
                    $response = array('success' => false, 'message' => 'Nonce missing');
                }
            } else {
                $response = array('success' => false, 'message' => 'Invalid Request');
            }
            a:
            wp_send_json($response);
            exit();
        }

        public function toolkit_unload_options_save()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST)) {
                    $unloadOpts = array();
                    //common wp files tab options
                    $unloadOpts['disable_emojis'] = isset($_POST['disable_emojis']) ? $_POST['disable_emojis'] : 'off';
                    $unloadOpts['disable_dashicons'] = isset($_POST['disable_dashicons']) ? $_POST['disable_dashicons'] : 'off';
                    $unloadOpts['disable_oembed'] = isset($_POST['disable_oembed']) ? $_POST['disable_oembed'] : 'off';
                    $unloadOpts['disable_rssfeed'] = isset($_POST['disable_rssfeed']) ? $_POST['disable_rssfeed'] : 'off';
                    $unloadOpts['disable_xmlrpc'] = isset($_POST['disable_xmlrpc']) ? $_POST['disable_xmlrpc'] : 'off';
                    $unloadOpts['disable_restapi'] = isset($_POST['disable_restapi']) ? $_POST['disable_restapi'] : 'off';
                    $unloadOpts['condition_restapi'] = isset($_POST['condition_restapi']) ? $_POST['condition_restapi'] : 'disable_non_admins';
                    $unloadOpts['disable_gutenberg'] = isset($_POST['disable_gutenberg']) ? $_POST['disable_gutenberg'] : 'off';
                    $unloadOpts['condition_gutenberg'] = isset($_POST['condition_gutenberg']) ? $_POST['condition_gutenberg'] : 'all';
                    $unloadOpts['disable_commentreply'] = isset($_POST['disable_commentreply']) ? $_POST['disable_commentreply'] : 'off';
                    //source code tab options
                    $unloadOpts['remove_qstrings'] = isset($_POST['remove_qstrings']) ? $_POST['remove_qstrings'] : 'off';
                    $unloadOpts['remove_jmigrate'] = isset($_POST['remove_jmigrate']) ? $_POST['remove_jmigrate'] : 'off';
                    $unloadOpts['remove_apilinks'] = isset($_POST['remove_apilinks']) ? $_POST['remove_apilinks'] : 'off';
                    $unloadOpts['remove_feedlinks'] = isset($_POST['remove_feedlinks']) ? $_POST['remove_feedlinks'] : 'off';
                    $unloadOpts['remove_rsdlink'] = isset($_POST['remove_rsdlink']) ? $_POST['remove_rsdlink'] : 'off';
                    $unloadOpts['remove_shortlink'] = isset($_POST['remove_shortlink']) ? $_POST['remove_shortlink'] : 'off';
                    $unloadOpts['remove_wlwlink'] = isset($_POST['remove_wlwlink']) ? $_POST['remove_wlwlink'] : 'off';
                    update_option('toolkit_unload_options', $unloadOpts);
                    $response = array('success' => true, 'message' => 'Updated Successfully.');
                } else {
                    $response = array('success' => false, 'message' => 'Parameter Missing.');
                }
            } else {
                $response = array('success' => false, 'message' => 'Invalid Request.');
            }
            wp_send_json($response);
        }

        function get_location_name($location_id){
            foreach ($this->gtmetrix_locations as $region => $locations) {
                foreach ($locations as $location){
                    if( $location_id == $location['id'] ){
                        return $location['name'];
                    }
                }
            }
            return '';
        }

        /*****        GTMETRIX SCAN CALL    *****/
        public function toolkit_performance_gtmetrix_scan()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST['_nonce']) && !empty($_POST['scan_url'])) {
                    if (wp_verify_nonce(sanitize_text_field($_POST['_nonce']), $this->nonce_key)) {
                        $license_key = sanitize_text_field(trim($this->toolkit_license_key));
                        if (!$license_key) {
                            $response = array('status' => 0, 'message' => 'License key not verified.');
                            goto a;
                        }
                        global $wpdb;
                        $scan_url = $_POST['scan_url'];
                        if ( ! trim($license_key) ) {
                            $response = array('status' => 0, 'message' => "License key is not valid.");
                        } else {
                            $apiResponse = wp_remote_post('https://toolkitforelementor.com/api/2.0/gtmetrix-scan.php', array(
                                'timeout'   => 120,
                                'body'      => [
                                    'scan_url'  => $scan_url,
                                    'location'  => $_POST['scan_location'],
                                    'browser'   => $_POST['scan_browser']
                                ]
                            ));
                            if( is_wp_error($apiResponse) ){
                                $response = array('status' => 0, 'message' => 'API server error, try again later.');
                            } else {
                                $rmtData = json_decode(wp_remote_retrieve_body($apiResponse), true);
                                if( $rmtData['report'] ){
                                    $rptData = $rmtData['data'];
                                    $location_name = $this->get_location_name($_POST['scan_location']);
                                    $browser_name = isset($this->gtmetrix_browsers[$_POST['scan_browser']]) ? $this->gtmetrix_browsers[$_POST['scan_browser']]['name'] : $_POST['scan_browser'];
                                    $response_log = $rptData;
                                    unset($response_log['content']);
                                    $wpdb->insert(
                                        "{$wpdb->prefix}toolkit_gtmetrix",
                                        array(
                                            'test_id'       => $rptData['test_id'],
                                            'scan_url'      => $scan_url,
                                            'load_time'     => $rptData['attributes']['onload_time'],
                                            'page_speed'    => $rptData['attributes']['pagespeed_score'],
                                            'yslow'         => $rptData['attributes']['yslow_score'],
                                            'region'        => esc_sql($location_name),
                                            'browser'       => $browser_name,
                                            'resources'     => json_encode($rptData['links']),
                                            'response_log'  => json_encode($response_log),
                                            'is_free'       => 0,
                                            'created'       => $this->time_now
                                        )
                                    );
                                    if ($wpdb->insert_id) {
                                        if ( ! empty($rptData['links']['screenshot']) ) {
                                            $toolkit_uploads = WP_CONTENT_DIR . '/toolkit-reports/';
                                            if( ! file_exists($toolkit_uploads) ){
                                                mkdir($toolkit_uploads, 0777, true);
                                            }
                                            file_put_contents($toolkit_uploads."report_pdf-{$rptData['test_id']}.pdf", base64_decode($rptData['content']['report_pdf_full']));
                                            file_put_contents($toolkit_uploads."screenshot-{$rptData['test_id']}.jpg", base64_decode($rptData['content']['screenshot']));
                                            file_put_contents($toolkit_uploads."lighthouse-{$rptData['test_id']}.txt", base64_decode($rptData['content']['lighthouse']));
                                            file_put_contents($toolkit_uploads."pagespeed-{$rptData['test_id']}.txt", base64_decode($rptData['content']['pagespeed']));
                                            file_put_contents($toolkit_uploads."hardata-{$rptData['test_id']}.txt", base64_decode($rptData['content']['hardata']));
                                        }
                                        $response = array('status' => 1, 'message' => __('Scan has completed successfully'), 'bodyResult' => $rptData);
                                    } else {
                                        $response = array('status' => 0, 'message' => 'Something went wrong, try again later.');
                                    }
                                } else {
                                    $response = array('status' => 0, 'message' => $rmtData['message']);
                                }
                            }
                        }
                    } else {
                        $response = array('status' => 0, 'message' => 'Invalid nonce.');
                    }
                } else {
                    $response = array('status' => 0, 'message' => 'Nonce missing');
                }
            } else {
                $response = array('status' => 0, 'message' => 'Invalid Request');
            }
            a:
            wp_send_json($response);
            exit();
        }

		/*****        Default Wordpress Widgets    *****/
		public function set_default_wordpress_widgets() {
			$widgets = [];
			if ( ! empty( $GLOBALS['wp_widget_factory'] ) ) {
				$widgets = $GLOBALS['wp_widget_factory']->widgets;
			}			
			//update_option('toolkit_wordpress_widgets', $widgets );
		}
		/*****        Disable Wordpress Widgets    *****/
		public function disable_wordpress_widgets() {
			if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] == 'disable_wordpress_widgets' ) {
                $input = $_POST['toolkit_wp_widget_disable_wordpress'];
                $output  = [];
                $message = null;
                if ( empty( $input ) ) {
                    $message = __( 'All wordpress widgets are enabled again.', 'wp-widget-disable' );
                } else {
                    // Loop through each of the incoming options.
                    foreach ( array_keys( $input ) as $key ) {
                        // Check to see if the current option has a value. If so, process it.
                        if ( isset( $input[ $key ] ) ) {
                            // Strip all HTML and PHP tags and properly handle quoted strings.
                            $output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
                        }
                    }
                    $output_count = count( $output );
                    if ( 1 === $output_count ) {
                        $message = __( 'Settings saved. One wordpress widget disabled.', 'wp-widget-disable' );
                    } else {
                        $message = sprintf(
                            /* translators: %d: number of disabled widgets */
                            _n(
                                'Settings saved. %d wordpress widget disabled.',
                                'Settings saved. %d wordpress widgets disabled.',
                                number_format_i18n( $output_count ),
                                'wp-widget-disable'
                            ),
                            $output_count
                        );
                    }
                }
                update_option('toolkit_wp_widget_disable_wordpress',$output);
                $response = array('success' => true, 'message' => $message);
                wp_send_json($response);
                exit();
			} else {				
				$widgets = (array) get_option( 'toolkit_wp_widget_disable_wordpress', [] );
				if ( ! empty( $widgets ) ) {
					foreach ( array_keys( $widgets ) as $widget_class ) {
						unregister_widget( $widget_class );
					}
				}
            }        
		}
		/*****        Update List Of Elementor Widgets To Disable    *****/
		public function disable_elementor_widgets() {			
			
			if (defined('DOING_AJAX') && DOING_AJAX && $_POST['action'] == 'disable_elementor_widgets' ) {
                $input = $_POST['toolkit_elementor_widgets_disable'];
                $output  = [];
                $message = null;
                if ( empty( $input ) ) {
                    $message = __( 'All elementor widgets are enabled again.', 'wp-widget-disable' );
                } else {
                    // Loop through each of the incoming options.
                    foreach ( array_keys( $input ) as $key ) {
                        // Check to see if the current option has a value. If so, process it.
                        if ( isset( $input[ $key ] ) ) {
                            // Strip all HTML and PHP tags and properly handle quoted strings.
                            $output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
                        }
                    }
                    $output_count = count( $output );
                    if ( 1 === $output_count ) {
                        $message = __( 'Settings saved. One elementor widget disabled.', 'wp-widget-disable' );
                    } else {
                        $message = sprintf(
                        /* translators: %d: number of disabled widgets */
                            _n(
                                'Settings saved. %d elementor widget disabled.',
                                'Settings saved. %d elementor widgets disabled.',
                                number_format_i18n( $output_count ),
                                'wp-widget-disable'
                            ),
                            $output_count
                        );
                    }
                }
                update_option('toolkit_elementor_widgets_disable',$output);
                $response = array('success' => true, 'message' => $message);
                wp_send_json($response);
                exit();
			}
		}
		/*****       Disable Elementor Widgets    *****/
		public function toolkit_disable_elementor_elements($widgets_manager) {
			$widgets = (array) get_option( 'toolkit_elementor_widgets_disable', [] );
			if ( ! empty( $widgets ) ) {					
				foreach ( array_keys( $widgets ) as $widget_name ) {
					$widgets_manager->unregister_widget_type($widget_name);
				}					
			}
        }
		/*****        Disable Dashboard Widgets    *****/
		function dashboard_widgets_toolkit_disable() {
			if (defined('DOING_AJAX') && DOING_AJAX && $_POST['action'] == 'dashboard_widgets_toolkit_disable' ) {
                $input_dash = $_POST['toolkit_wp_widget_disable_dashboard'];
                $output  = [];
                $message_dash = null;
                if ( empty( $input_dash ) ) {
                    $message_dash = __( 'All dashboard widgets are enabled again.', 'wp-widget-disable' );
                } else {
                    foreach ( array_keys( $input_dash ) as $key ) {
                        if ( isset( $input_dash[ $key ] ) ) {
                            $output[ $key ] = wp_strip_all_tags( stripslashes( $input_dash[ $key ] ) );
                        }
                    }
                    $output_count = count( $output );
                    if ( 1 === $output_count ) {
                        $message_dash = __( 'Settings saved. One dashboard widget disabled.', 'wp-widget-disable' );
                    } else {
                        $message_dash = sprintf(
                            _n(
                                'Settings saved. %d dashboard widget disabled.',
                                'Settings saved. %d dashboard widgets disabled.',
                                number_format_i18n( $output_count ),
                                'wp-widget-disable'
                            ),
                            $output_count
                        );
                    }
                }
                update_option('toolkit_wp_widget_disable_dashboard',$output);
                $response_dash = array('success' => true, 'message' => $message_dash);
                wp_send_json($response_dash);
                exit();
			} else {
				$widgets = (array) get_option( 'toolkit_wp_widget_disable_dashboard', [] );
				if ( is_network_admin() ) {
					$widgets = (array) get_site_option( 'toolkit_wp_widget_disable_dashboard', [] );
				}				
				if ( ! $widgets ) {
					return;
				}					
				foreach ( $widgets as $widget_id => $meta_box ) {
					if ( 'dashboard_welcome_panel' === $widget_id ) {
						remove_action( 'welcome_panel', 'wp_welcome_panel' );
						continue;
					}
					if ( 'try_gutenberg_panel' === $widget_id ) {
						remove_action( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
						continue;
					}
					if ( 'dashboard_browser_nag' === $widget_id || 'dashboard_php_nag' === $widget_id ) {
						continue;
					}
					remove_meta_box( $widget_id, get_current_screen()->base, $meta_box );
				}				
            }     
		}
		/*****        Disable Dashboard Widgets    *****/		
		public function disable_dashboard_widgets_with_remote_requests() {
            $widgets = [];
			if ( is_network_admin() ) {
				$widgets = (array) get_site_option( 'toolkit_wp_widget_disable_dashboard', [] );
			}
			if ( ! $widgets ) {
				return;
			}
			foreach ( $widgets as $widget_id => $meta_box ) {
				if ( 'dashboard_browser_nag' === $widget_id ) {
					$key = md5( $_SERVER['HTTP_USER_AGENT'] );
					add_filter( 'pre_site_transient_browser_' . $key, '__return_null' );
					continue;
				}
				if ( 'dashboard_php_nag' === $widget_id ) {
					$key = md5( phpversion() );
					add_filter( 'pre_site_transient_php_check_' . $key, '__return_null' );
					continue;
				}
			}
		}

        /*****        TOOLKIT LICENSE KEY VERIFY API CALL    *****/
        public function toolkit_license_key_verify()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST['_nonce']) && !empty($_POST['license_key'])) {
                    if (wp_verify_nonce(sanitize_text_field($_POST['_nonce']), $this->nonce_key)) {
                        $license_key = sanitize_text_field(trim($_POST['license_key']));
                        $site_url = site_url();
                        $args = array(
                            'edd_action' => 'activate_license',
                            'license' => $license_key,
                            'item_id' => TOOLKIT_FOR_ELEMENTOR_ITEM_ID,
                            'url' => sanitize_text_field($site_url),
                        );
                        $httpResponse = wp_remote_post(TOOLKIT_FOR_ELEMENTOR_UPDATE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $args));
                        if (is_wp_error($httpResponse)) {
                            $error_message = $httpResponse->get_error_message();
                            $response = array('success' => false, 'message' => "Something went wrong: $error_message");
                        } else {
                            if (isset($httpResponse['response']['code']) && $httpResponse['response']['code'] == 200) {
                                $bodyResult = json_decode(wp_remote_retrieve_body($httpResponse), true);
                                if (isset($bodyResult['success']) && $bodyResult['success'] && isset($bodyResult['license']) && $bodyResult['license'] == 'valid') {
                                    update_option('toolkit_license_key', $license_key);
                                    update_option('toolkit_license_details', $bodyResult);
									update_option( 'toolkit_license_status', $bodyResult['license'] );
                                    $response = array('success' => true, 'message' => 'License activated successfully.');
                                } elseif ( isset($bodyResult['success']) && ! $bodyResult['success'] ){
                                    $expDate = isset($bodyResult['expires']) ? $bodyResult['expires'] : '';
                                    $errorMsgs = array(
                                        "missing"                   => "Invalid License Key. Please double-check your license key.",
                                        "missing_url"               => "URL not provided",
                                        "license_not_activatable"   => "Sorry, this license is not activatable for this product.",
                                        "disabled"                  => "Your License key has been disabled. Please contact support",
                                        "no_activations_left"       => "Your license key has reached its activation limit. Please upgrade your plan.",
                                        "expired"                   => "Your license key expired on ".$expDate." Please renew.",
                                        "key_mismatch"              => "Invalid License Key. Please double-check your license key.",
                                        "invalid_item_id"           => "This license key does not match this product.",
                                        "item_name_mismatch"        => "This License is not valid for this product"
                                    );
                                    $message = 'Invalid License Key. Please double-check your license key.';
                                    if( isset($bodyResult['error']) && isset($errorMsgs[$bodyResult['error']]) ){
                                        $message = $errorMsgs[$bodyResult['error']];
                                    }
                                    $response = array('success' => false, 'message' => $message);
                                } else {
                                    $response = array('success' => false, 'message' => 'There was an error activating your license');
                                }
                            } elseif( isset($httpResponse['response']['message']) && $httpResponse['response']['message'] ){
                                $response = array('success' => false, 'message' => $httpResponse['response']['message']);
                            } else {
                                $response = array('success' => false, 'message' => 'There was an error activating your license.');
                            }
                        }
                    } else {
                        $response = array('success' => false, 'message' => 'Invalid nonce.');
                    }
                } else {
                    $response = array('success' => false, 'message' => 'Enter License key');
                }
            } else {
                $response = array('success' => false, 'message' => 'Invalid Request');
            }
            a:
            wp_send_json($response);
            exit();
        }
		/*****        TOOLKIT Disable License    *****/
		function toolkit_deactivate_license() {
			// listens for activation 
			if (!empty($_POST['_nonce']) && !empty($_POST['license_key'])) {
				// retrieve the license from the database
				$license = trim( $_POST['license_key'] );
				// data to send in our API request
				$api_params = array(
					'edd_action'    => 'deactivate_license',
					'license'       => $license,
					'item_id'       => TOOLKIT_FOR_ELEMENTOR_ITEM_ID,
					'url'           => home_url()
				);
				// Call the custom API.
				$response = wp_remote_post( TOOLKIT_FOR_ELEMENTOR_UPDATE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
				// make sure the response came back okay
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					if ( is_wp_error( $response ) ) {
						$message = array('status' => 0, 'message' => $response->get_error_message() );
					} else {
						$message = array('status' => 0, 'message' => 'An error occurred, please try again.');
					}
                    wp_send_json($message);
					exit();
				}
				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->success && $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
					delete_option( 'toolkit_license_key' );
					delete_option( 'toolkit_license_status' );
					delete_option( 'toolkit_license_details' );
				}
				$message = array('success' => true, 'message' => 'License has deactivated successfully.');
				wp_send_json($message);
				exit();
			}
		}
		/*****        ToolKit License API Check    *****/
		function toolkit_check_license() {
			$license = trim( get_option( 'toolkit_license_key' ) );
			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,		
				'item_name' => urlencode( 'ToolKit For Elementor' ),
				'url'       => home_url()
			);
			$response = wp_remote_post( TOOLKIT_FOR_ELEMENTOR_UPDATE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			if ( ! is_wp_error( $response ) ){
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                if( $license_data->license != 'valid' ) {
                    delete_option( 'toolkit_license_key' );
                    delete_option( 'toolkit_license_status' );
                    delete_option( 'toolkit_license_details' );
                }
            }
			exit;
		}

        /*****        GTMETRIX SCAN CALL    *****/
        public function toolkit_performance_gtmetrix_scan_result()
        {
            $this->get_template('gtmetrix-scan');
            echo gt_metrix_settings_display();
            exit();
        }

        public function toolkit_performance_gtmetrix_history()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST['_nonce']) && !empty($_POST['page_no'])) {
                    if (wp_verify_nonce(sanitize_text_field($_POST['_nonce']), $this->nonce_key)) {
                        $offset = !empty($_POST['page_no']) ? (($_POST['page_no'] - 1) * $this->limit) : 0;
                        $html = $this->getGtmetrixScanHistory($this->limit, $offset);
                        $response = array('status' => 1, 'message' => 'Successful', 'html' => $html);
                    } else {
                        $response = array('status' => 0, 'message' => 'Invalid nonce');
                    }
                } else {
                    $response = array('status' => 0, 'message' => 'Invalid Request');
                }
            } else {
                $response = array('status' => 0, 'message' => 'Invalid Request');
            }
            a:
            wp_send_json($response);
            exit();
        }

        /*****        GTMETRIX FULL REPORT DOWNLOAD CALL    *****/
        public function toolkit_performance_gtmetrix_download_report()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                if (!empty($_POST['report_url'] && $_POST['testid'])) {
                    global $wpdb;
                    $postData = array('_nonce' => wp_create_nonce($this->nonce_key), 'report_url' => $_POST['report_url']);
                    $url = $this->report_download_url;
                    $httpResponse = $this->toolkitwebHTTPPost($url, $postData);
                    if (is_wp_error($httpResponse)) {
                        $error_message = $httpResponse->get_error_message();
                        $response = array('status' => 0, 'message' => "Something went wrong: $error_message");
                    } else {
                        if (!empty($httpResponse['response']['code']) && $httpResponse['response']['code'] == 200) {
                            $bodyResult = json_decode(wp_remote_retrieve_body($httpResponse), true);
                            // echo json_decode($bodyResult['report']);
                            if ($bodyResult['status']) {
                                $reportPath = $this->get_plugin_dir() . "admin/gtmetrix/pdf/report_pdf-{$_POST['testid']}.pdf";
                                chmod($this->get_plugin_dir() . "admin/gtmetrix/pdf/", 0777);
                                file_put_contents($reportPath, base64_decode($bodyResult['report']));
                                $response = array('status' => 1, 'message' => 'Successful', 'report' => $bodyResult['report']);
                            } else {
                                $response = array('status' => 0, 'message' => $bodyResult['message']);
                            }
                        } else {
                            $response = array('status' => 0, 'message' => 'Try Again.');
                        }
                    }
                } else {
                    $response = array('status' => 0, 'message' => 'Invalid data');
                }
            } else {
                $response = array('status' => 0, 'message' => 'Invalid Request');
            }
            a:
            wp_send_json($response);
            exit();
        }

        /*****     WP Request Verification    *****/
        public function toolkitwebHTTPPost($url, $postData)
        {
            $requestVerify = wp_remote_post($url, array(
                    'method' => 'POST',
                    'timeout' => 600,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(
                        'Content-Type: application/json',
                        'Content-Length: ' . count($postData)
                    ),
                    'body' => $postData,
                    'cookies' => array()
                )
            );
            return $requestVerify;
        }

        /*****        GTMETRIX INFO FUNCTION    *****/
        public function toolkitGtmetrixScan($httpResponse, $region = '', $browser = '')
        {
            global $wpdb;
            if (is_wp_error($httpResponse)) {
                $error_message = $httpResponse->get_error_message();
                $response = array('status' => 0, 'message' => "Something went wrong: $error_message");
            } else {
                if (!empty($httpResponse['response']['code']) && $httpResponse['response']['code'] == 200) {
                    $bodyResult = json_decode(explode(',"hq_ad_msg"', wp_remote_retrieve_body($httpResponse))[0].'}', true);
                    if ($bodyResult['status']) {
                        if (empty($bodyResult['results']) || empty($bodyResult['testid'])) {
                            $response = array('status' => 0, 'message' => $bodyResult['message'], 'body' => $bodyResult);
                        } else {
                            $gt = $bodyResult['results'];
                            $other = $bodyResult['other_detail'];
                            $wpdb->insert(
                                "{$wpdb->prefix}toolkit_gtmetrix",
                                array(
                                    'test_id' => $bodyResult['testid'],
                                    'scan_url' => $bodyResult['scan_url'],
                                    'load_time' => $gt['fully_loaded_time'],
                                    'page_speed' => $gt['pagespeed_score'],
                                    'yslow' => $gt['yslow_score'],
                                    'region' => $region,
                                    'browser' => $browser,
                                    'resources' => json_encode($bodyResult['resources']),
                                    'response_log' => json_encode($bodyResult['results']),
                                    'is_free' => $bodyResult['is_free'],
                                    'created' => $this->time_now
                                )
                            );
                            if ($wpdb->insert_id) {
                                if (!empty($bodyResult['hq_settings'])) {
                                    update_option('toolkit_hq_my_settings', $bodyResult['hq_settings']);
                                }
                                $otherDetails['last_update_time'] = $this->time_now;
                                $otherDetails = array_merge($otherDetails, $bodyResult['other_detail']);
                                update_option('toolkit_gtmetrix_other_details', $this->recursive_sanitize_text_field($otherDetails));
                                if (!empty($bodyResult['screenshot'])) {
                                    $toolkit_uploads = WP_CONTENT_DIR . '/toolkit-reports/';
                                    if( ! file_exists($toolkit_uploads) ){
                                        mkdir($toolkit_uploads, 0777, true);
                                    }
                                    file_put_contents($toolkit_uploads."report_pdf-{$bodyResult['testid']}.pdf", base64_decode($bodyResult['report_pdf_full']));
                                    file_put_contents($toolkit_uploads."screenshot-{$bodyResult['testid']}.jpg", base64_decode($bodyResult['screenshot']));
                                    file_put_contents($toolkit_uploads."yslow-{$bodyResult['testid']}.txt", base64_decode($bodyResult['yslow']));
                                    file_put_contents($toolkit_uploads."pagespeed-{$bodyResult['testid']}.txt", base64_decode($bodyResult['pagespeed']));
                                }
                                $response = array('status' => 1, 'message' => $bodyResult['message'], 'bodyResult' => $bodyResult);
                            } else {
                                $response = array('status' => 0, 'message' => 'Try Again.');
                            }
                        }
                    } else {
                        $response = array('status' => 0, 'message' => $bodyResult['message'], 'is_free' => 1);
                        if (isset($bodyResult['is_free'])) {
                            update_option('toolkit_gtmetrix_credit', 0);
                            $response['is_free'] = 0;
                        }
                    }
                } else {
                    $response = array('status' => 0, 'message' => 'Try Again.');
                }
            }
            return $response;
        }

        public function admin_menu()
        {
            $toolkitAccess = get_option('toolkit_access_plugin_settings', array());
            $user = wp_get_current_user();
            if( (! $toolkitAccess) || ($toolkitAccess['restrict_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['restrict_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                add_submenu_page(Elementor\Settings::PAGE_ID, __('ToolKit For Elementor', 'toolkit-for-elementor'), __('ToolKit', 'toolkit-for-elementor'), 'manage_options', 'toolkit-performance-tool', array($this, 'plugin_page'));
            }
        }

        public function plugin_page(){
            echo '<div class="toolkit-message" style="display:none;"></div>';
            echo '<div class="wrap">';
            echo '<div class="toolkit-banner">';
            echo '<div class="toolkit-logo"><img src="'.TOOLKIT_FOR_ELEMENTOR_URL.'admin/images/toolkit-logo-white.png" alt="ToolKit for Elementor" /></div>';
            echo '<div class="toolkit-meta"><span class="toolkit-version">'.__("Version ", "toolkit-for-elementor").''.TOOLKIT_FOR_ELEMENTOR_VERSION.'</span> <span class="toolkit-tutorial"><a href="https://toolkitforelementor.com/kb" target="_blank">'.__("Tutorials", "toolkit-for-elementor").'</a></span></div>';
            echo '<div class="clearfix"></div></div><div class="clearfix"></div>';
            $this->lazy_load_setting->show_navigation();
            $this->lazy_load_setting->show_forms();
            echo '</div>';
        }

        /*****        RECURSIVE SANITIZE TEXT    *****/
        public function recursive_sanitize_text_field($array)
        {
            if (is_array($array)) {
                foreach ($array as $key => &$value) {
                    if (is_array($value)) {
                        $value = $this->recursive_sanitize_text_field($value);
                    } else {
                        $value = sanitize_text_field($value);
                    }
                }
            }
            return $array;
        }

        /*****        RECURSIVE SANITIZE    *****/
        public function recursive_sanitize_html_field($array)
        {
            if (is_array($array)) {
                foreach ($array as $key => &$value) {
                    if (is_array($value)) {
                        $value = $this->recursive_sanitize_html_field($value);
                    } else {
                        $value = sanitize_text_field(htmlentities($value));
                    }
                }
            }
            return $array;
        }

        public function get_plugin_dir()
        {
            return TOOLKIT_FOR_ELEMENTOR_PATH;
        }

        public function get_template($template)
        {
            $template_name = 'admin/templates/' . $template . '.php';
            require_once TOOLKIT_FOR_ELEMENTOR_PATH . $template_name;
        }

        public function get_plugin_url($url)
        {
            return TOOLKIT_FOR_ELEMENTOR_URL . $url;
        }

        public function maskLicenseKey($key)
        {
            $key = explode('-', $key);
            $mask_key = '';
            foreach ($key as $v => $n) {
                if (count($key) != ($v + 1)) {
                    $mask_key .= '-' . str_repeat("X", strlen($n));
                } else {
                    $mask_key .= '-' . $n;
                }
            }
            return ltrim($mask_key, '-');
        }

        public function gtmetrix_code($value)
        {
            if ($value >= 90) {
                $code = array('code' => 'A', 'color' => '4bb32b');
            } elseif ($value >= 80 && $value < 90) {
                $code = array('code' => 'B', 'color' => '90c779');
            } elseif ($value >= 70 && $value < 80) {
                $code = array('code' => 'C', 'color' => 'd2bf2f');
            } elseif ($value >= 60 && $value < 70) {
                $code = array('code' => 'D', 'color' => 'e4a63d');
            } elseif ($value >= 50 && $value < 60) {
                $code = array('code' => 'E', 'color' => 'ca7c55');
            } else {
                $code = array('code' => 'F', 'color' => 'd62f30');
            }
            return $code;
        }

        public function formatSizeUnits($bytes)
        {
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . 'GB';
            } elseif ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . 'MB';
            } elseif ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . 'KB';
            } elseif ($bytes >= 1) {
                $bytes = $bytes . 'B';
            } else {
                $bytes = '0 B';
            }
            return $bytes;
        }

        public function get_settings_sections()
        {
            $sections = array();
            $toolkitAccess = get_option('toolkit_access_plugin_settings', array());
            $user = wp_get_current_user();
            if( (! $toolkitAccess) || (! isset($toolkitAccess['booster_access'])) || ($toolkitAccess['booster_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['booster_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                $this->get_template('gtmetrix-scan');
                $sections[] = array(
                    'id'    => 'toolkit_performance_tool',
                    'title' => __('BOOSTER', 'toolkit-for-elementor'),
                    'desc'  => gt_metrix_settings_display()
                );
            }
            if( (! $toolkitAccess) || (! isset($toolkitAccess['syncer_access'])) || ($toolkitAccess['syncer_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['syncer_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                $this->get_template('syncer-template');
                $sections[] = array(
                    'id'    => 'toolkit_my_templates_syncer',
                    'title' => __('SYNCER', 'toolkit-for-elementor'),
                    'desc'  => syncer_template_settings_display()
                );
            }
            if( (! $toolkitAccess) || (! isset($toolkitAccess['theme_access'])) || ($toolkitAccess['theme_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['theme_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                $this->get_template('theme-disable');
                $sections[] = array(
                    'id'    => 'toolkit_theme_less',
                    'title' => __('THEME MANAGER', 'toolkit-for-elementor'),
                    'desc'  => theme_disable_settings_display()
                );
            }
            if( (! $toolkitAccess) || (! isset($toolkitAccess['toolbox_access'])) || ($toolkitAccess['toolbox_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['toolbox_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                $this->get_template('theme-toolkit');
                $sections[] = array(
                    'id'    => 'toolkit_theme_toolkit',
                    'title' => __('TOOLBOX', 'toolkit-for-elementor'),
                    'desc'  => theme_toolkit_settings_display()
                );
            }
            if( (! $toolkitAccess) || (! isset($toolkitAccess['license_access'])) || ($toolkitAccess['license_access'] == 'me' && $toolkitAccess['only_me_id'] == $user->ID) || ($toolkitAccess['license_access'] == 'administrator' && in_array( 'administrator', (array) $user->roles )) ){
                $this->get_template('my-license');
                $sections[] = array(
                    'id'    => 'toolkit_my_license',
                    'title' => __('MY LICENSE', 'toolkit-for-elementor'),
                    'desc'  => my_license_settings_display()
                );
            }
            return $sections;
        }

        // BOOSTER Tab
        public function get_settings_fields()
        {
            $settings_fields = array(
                'toolkit_elementor_settings' => array(
                    array(
                        'name' => 'image',
                        'label' => __('Lazy Load Images', 'toolkit-for-elementor'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                    array(
                        'name' => 'iframe_video',
                        'label' => __('Lazy Load Iframes & Videos', 'toolkit-for-elementor'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                ),
                'toolkit_elementor_tweaks' => array(
                    array(
                        'name' => 'html_minify',
                        'label' => __('HTML Minify', 'toolkit-for-elementor'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                    array(
                        'name' => 'css_minify',
                        'label' => __('Minify CSS', 'toolkit-for-elementor'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                    array(
                        'name' => 'js_minify',
                        'label' => __('Minify JS', 'toolkit-for-elementor'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                ),
            );
            return $settings_fields;
        }
    }

    new Lazy_load_Settings();
}
