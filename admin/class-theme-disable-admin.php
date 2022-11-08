<?php
if( ! class_exists('Theme_Disable_Admin') ) {

    class Theme_Disable_Admin{

        public function __construct(){
            add_action('wp_ajax_theme_disable_settings', array($this, 'save_settings'));
            add_action('wp_ajax_theme_toolkit_settings', array($this, 'save_settings'));
            add_action('wp_ajax_toolkit_prefetch_setting_save', array($this, 'save_prefetch_settings'));
            add_filter('wp_resource_hints', array($this, 'apply_prefetch_settings'), 10, 2);
            $disable = get_option('theme_disable_themeless', 'no');
            if( $disable == 'yes' ) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_disable_styles'));
                add_action('admin_footer', array($this, 'disable_themes_content'));
            }
        }

        public function enqueue_disable_styles(){
            wp_enqueue_style('theme-disable-admin', TOOLKIT_FOR_ELEMENTOR_URL . 'admin/css/theme-disable-admin.css', array(), '', 'all');
        }

        public function save_settings(){
            if (isset($_POST['themeless']) && !empty(trim($_POST['themeless']))) {
                update_option('theme_disable_themeless', esc_sql($_POST['themeless']));
                toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                $response = array('success'=>true, 'message'=>'Updated Successfully', 'msg2'=>__("Cache has cleared successfully"));
            } elseif ( isset($_POST['header_code']) && isset($_POST['footer_code']) && isset($_POST['bodytag_code']) ) {
                update_option('theme_disable_header_code', $_POST['header_code']);
                update_option('theme_disable_footer_code', $_POST['footer_code']);
                update_option('theme_disable_wpfooter_code', $_POST['footer_wpcode']);
                update_option('theme_disable_bodytag_code', $_POST['bodytag_code']);
                toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                $response = array('success'=>true,'message'=>'Updated Successfully', 'msg2'=>__("Cache has cleared successfully"));
            } else {
                $response = array('success'=>false,'message'=>'Invalid Request');
            }
            wp_send_json( $response );
        }

        public function disable_themes_content(){
            $current_screen = get_current_screen();
            if ($current_screen->id != "themes") {
                return;
            } ?>
            <div class="theme-disable-overlay">
                <span class="close-notice"><span class="dashicons dashicons-no"></span></span>
                <h3><?php _e("Congratulations on going Themeless!"); ?></h3>
                <?php if( is_elementor_pro_activated() ){
                    $theme_builder_link = admin_url("edit.php?post_type=elementor_library#add_new");
                    if( defined('ELEMENTOR_VERSION') && version_compare( ELEMENTOR_VERSION, '3.0', '>=' ) ){
                        $theme_builder_link = admin_url('admin.php?page=elementor-app&ver='.ELEMENTOR_VERSION.'#site-editor');
                    } ?>
                    <p><?php _e("Elementor Pro Detected (Yay!) Please proceed to Elementor's Theme Builder to begin building out your Header, Footer and Page/Post templates."); ?></p>
                    <a href="<?php echo $theme_builder_link; ?>"><?php _e("Awesome, Let's Proceed"); ?></a>
                <?php } elseif( defined('ELEMENTOR_VERSION') && version_compare( ELEMENTOR_VERSION, '3.0', '>=' ) ) {
                    $theme_builder_link = admin_url('admin.php?page=elementor-app&ver='.ELEMENTOR_VERSION.'#site-editor/promotion'); ?>
                    <p><b><?php _e("We detect that Elementor Pro is not currently installed and/or activated."); ?></b><br /><br />
                    <?php _e("Please be advised that Themeless was designed to work with Elementor Pro's Theme Builder."); ?><br />
		            <?php _e("For Elementor Free users, we have included a method of assigning headers & footers however custom css, third-party plugins will likely be needed."); ?></p>
                    <a id="themeless_tab_link" href="<?php echo $theme_builder_link; ?>"><?php _e("Ok, I Understand. Let's Proceed"); ?></a>
                <?php } ?>
            </div>
        <?php }

        public function edd_plugin_updater() {
            $active_plugins = get_option( 'active_plugins' );
            foreach ( $active_plugins as $active_plugin ) {
                if ( false !== strpos( $active_plugin, TOOLKIT_FOR_ELEMENTOR_NAME ) ) {
                    $plugin_name = $active_plugin;
                    break;
                }
            }
            if ( ! $plugin_name ) {
                return;
            }

            $license_key = trim( get_option( 'toolkit_license_key' ) );
            if ( ! $license_key ) {
                return;
            }

            if( ! class_exists( 'Toolkit_For_Elementor_Updater' ) ) {
                include( TOOLKIT_FOR_ELEMENTOR_PATH . '/admin/class-toolkit-for-elementor-updater.php' );
            }
            $betaOpts = get_option( 'toolkit_beta_testing_options', array() );
            $betaTest = (isset($betaOpts['beta_testing']) && $betaOpts['beta_testing'] == 'yes') ? true : false;
            $edd_updater = new Toolkit_For_Elementor_Updater(
                TOOLKIT_FOR_ELEMENTOR_UPDATE_URL,
                TOOLKIT_FOR_ELEMENTOR_FILE,
                array(
                    'version' => TOOLKIT_FOR_ELEMENTOR_VERSION,
                    'license' => $license_key,
                    'item_id' => TOOLKIT_FOR_ELEMENTOR_ITEM_ID,
                    'author'  => TOOLKIT_FOR_ELEMENTOR_AUTHOR,
                    'beta'    => $betaTest
                )
            );

        }

        public function save_prefetch_settings(){
            if ( isset($_POST['pre_dns']) && isset($_POST['hkeywords']) && isset($_POST['keywords']) ) {
                $settings = array();
                $settings['pre_dns'] = isset($_POST['pre_dns']) ? $_POST['pre_dns'] : 'off';
                $settings['hkeywords'] = isset($_POST['hkeywords']) ? $_POST['hkeywords'] : '';
                $settings['keywords'] = isset($_POST['keywords']) ? $_POST['keywords'] : '';
                $settings['expages'] = isset($_POST['expages']) ? $_POST['expages'] : '';
                update_option('toolkit_prefetch_dns_options', $settings);
                $response = array('success'=>true, 'message'=>'Settings have updated successfully');
            } else {
                $response = array('success'=>false, 'message'=>'Invalid Request');
            }
            wp_send_json( $response );
        }

        public function apply_prefetch_settings($hints, $relation_type){
            if( 'dns-prefetch' === $relation_type ){
                $dnsSettings = get_option('toolkit_prefetch_dns_options', array());
                if( isset($dnsSettings['pre_dns']) && $dnsSettings['pre_dns'] == 'on' ){
                    $current_link = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI'];
                    $current_link = rtrim($current_link, "/");
                    $exclude_page = false;
                    if( isset($dnsSettings['expages']) && $dnsSettings['expages'] ){
                        $expages = $dnsSettings['expages'] ? preg_split('/\r\n|[\r\n]/', $dnsSettings['expages']) : array();
                        $expages = is_array($expages) ? $expages : array();
                        if( $expages ){
                            foreach ($expages as $expage){
                                if( false !== strpos( $current_link, $expage ) ){
                                    $exclude_page = true;
                                    break;
                                }
                            }
                        }
                    }
                    if( ! $exclude_page ){
                        if( isset($dnsSettings['hkeywords']) && $dnsSettings['hkeywords'] ){
                            $keywords = $dnsSettings['hkeywords'] ? preg_split('/\r\n|[\r\n]/', $dnsSettings['hkeywords']) : array();
                            $keywords = is_array($keywords) ? $keywords : array();
                            if( $keywords && $current_link == home_url() ){
                                foreach ($keywords as $keyword){
                                    $hints[] = $keyword;
                                }
                            }
                        }
                        if( isset($dnsSettings['keywords']) && $dnsSettings['keywords'] ){
                            $keywords = $dnsSettings['keywords'] ? preg_split('/\r\n|[\r\n]/', $dnsSettings['keywords']) : array();
                            $keywords = is_array($keywords) ? $keywords : array();
                            if( $keywords ){
                                foreach ($keywords as $keyword){
                                    $hints[] = $keyword;
                                }
                            }
                        }
                    }
                }
            }
            return $hints;
        }
					
    }

}
