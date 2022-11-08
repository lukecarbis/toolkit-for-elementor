<?php
if( ! class_exists('Toolkit_Elementor_Update_Manager') ) {

    class Toolkit_Elementor_Update_Manager{
        public function __construct(){

        }

        function additional_mime_types($mimes){
            $mimes['json'] = 'application/json';
            return $mimes;
        }

        public function get_remove_options() {
            return array(
                'remove'      => __('Remove All Data', 'toolkit-for-elementor'),
                'rem_some'    => __('Remember Some Settings', 'toolkit-for-elementor'),
                'remember'    => __('Remember All Settings', 'toolkit-for-elementor')
            );
        }

        public function get_remember_options() {
            return array(
                'remove'      => __('Remove All Data', 'toolkit-for-elementor'),
                'remember'    => __('Remember Settings', 'toolkit-for-elementor')
            );
        }

        public function get_export_optoins() {
            return array(
                'all_exp'   => __('Export All Settings', 'toolkit-for-elementor'),
                'rem_some'  => __('Export Some Settings', 'toolkit-for-elementor')
            );
        }

        public function get_export_options() {
            return array(
                'yes'   => __('Yes', 'toolkit-for-elementor'),
                'no'    => __('No', 'toolkit-for-elementor')
            );
        }

        function save_settings(){
            if( isset($_POST['wpcore']) && isset($_POST['plugin']) && isset($_POST['themes']) ){
                $options = array(
                    'disable_sitemap'   => esc_sql($_POST['disable_sitemap']),
                    'wpcore'            => $_POST['wpcore'],
                    'plugin'            => $_POST['plugin'],
                    'themes'            => $_POST['themes']
                );
                update_option('toolkit_core_tweaks_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings have been saved successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function save_dashboard_options(){
            if( isset($_POST['template']) && isset($_POST['dismissible']) ){
                $options = array(
                    'template'      => esc_sql($_POST['template']),
                    'showtitle'     => esc_sql($_POST['showtitle']),
                    'dismissible'   => esc_sql($_POST['dismissible'])
                );
                update_option('toolkit_dashboard_settings_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings have been saved successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function clear_scan_history(){
            if( isset($_POST['clear']) && $_POST['clear'] ){
                global $table_prefix, $wpdb;
                $gtmetrix_table = $table_prefix . "toolkit_gtmetrix";
                $sql = "TRUNCATE `$gtmetrix_table`";
                $wpdb->query($sql);
                if( function_exists('toolkit_remove_minify_css_js_files') ){
                    toolkit_remove_minify_css_js_files(WP_CONTENT_DIR . '/toolkit-reports/');
                }
                $response = array('success'=>true, 'message'=>__("Scan history has been cleared successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function save_data_handling_options(){
            if( isset($_POST['upon_uninstal']) ){
                $options = array(
                    'upon_uninstal'     => esc_sql($_POST['upon_uninstal']),
                    'booster_uninstall' => esc_sql($_POST['booster_uninstall']),
                    'scan_history'      => esc_sql($_POST['scan_history']),
                    'theme_uninstall'   => esc_sql($_POST['theme_uninstall']),
                    'toolbox_uninstall' => esc_sql($_POST['toolbox_uninstall']),
                    'license_uninstall' => esc_sql($_POST['license_uninstall'])
                );
                update_option('toolkit_data_handling_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings have been saved successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function save_beta_testing_options(){
            if( isset($_POST['beta_testing']) && isset($_POST['testing_email']) ){
                $options = array(
                    'beta_testing'  => esc_sql($_POST['beta_testing']),
                    'testing_email' => esc_sql($_POST['testing_email']),
                    'testing_name'  => esc_sql($_POST['testing_name'])
                );
                /*if( $_POST['beta_testing'] == 'yes' && $_POST['testing_email'] != 'sent' ){
                    $to = 'beta@toolkitforelementor.com';
                    $subject = 'New ToolKit Beta Tester';
                    $body = 'Hello!<br><br>This is a system notification that a new user has joined ToolKit Beta';
                    //$body .= '<br>Email: '.$_POST['testing_email'];
                    //$body .= '<br>Name: '.$_POST['testing_name'];
                    $body .= '<br>Email: '.get_bloginfo('admin_email');
                    $body .= '<br>Title: '.get_bloginfo('name');
                    $body .= '<br>Site: '.site_url();
                    $body .= '<br><br>ToolKit For Elementor';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail( $to, $subject, $body, $headers );
                    $options['testing_email'] = 'sent';
                }*/
                update_option('toolkit_beta_testing_options', $options);
                $response = array('success'=>true, 'message'=>__("Settings have saved successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function reset_settings(){
            if( isset($_POST['reset_all']) && $_POST['reset_all'] ){
                update_option('toolkit_webserver_tweaks', array());
                update_option('toolkit_elementor_tweaks', array());
                update_option('toolkit_unload_options', array());
                update_option('toolkit_elementor_settings', array());
                update_option('toolkit_elementor_dbopt_settings', array());
                update_option('toolkit_elementor_cache_settings', array());
                update_option('theme_disable_themeless', '');
                update_option('toolkit_dashboard_settings_options', array());
                update_option('toolkit_login_settings_options', array());
                update_option('toolkit_login_page_url_options', array());
                update_option('theme_disable_header_code', '');
                update_option('theme_disable_footer_code', '');
                update_option('theme_disable_bodytag_code', '');
                update_option('toolkit_background_tasks_options', array());
                update_option('toolkit_core_tweaks_options', array());
                update_option('toolkit_access_plugin_settings', array());
                update_option('toolkit_wp_widget_disable_dashboard', array());
                update_option('toolkit_wp_widget_disable_sidebar', array());
                update_option('toolkit_elementor_widgets_disable', array());
                update_option('toolkit_data_handling_options', array());
                update_option('toolkit_beta_testing_options', array());
                update_option('toolkit_license_key', '');
                update_option('toolkit_license_details', array());
                update_option('toolkit_license_status', array());
                update_option('_toolkit_syncer_key', array());
                update_option('toolkit_preload_cache_meta', array());
                update_option('toolkit_preload_cache_pages', '');
                $response = array('success'=>true, 'message'=>__("Settings have saved successfully."));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed."));
            }
            wp_send_json($response);
        }

        public function admin_footer_text( $footer_text ) {
            $is_toolkit_screen = ( isset($_GET['page']) && $_GET['page'] == 'toolkit-performance-tool' );
            if ( $is_toolkit_screen ) {
                $footer_text = sprintf(
                    __( 'Thanks for using %1$s! Enjoy using ToolKit? Come %2$s.', 'toolkit-for-elementor' ),
                    // '<strong>' . __( 'ToolKit For Elementor', 'toolkit-for-elementor' ) . '</strong>',
		    '<a href="https://toolkitforelementor.com" target="_blank">'.__('ToolKit For Elementor', 'toolkit-for-elementor').'</a>',
                    '<a href="https://www.facebook.com/groups/toolkitforelementor" target="_blank">'.__('Join Our Community', 'toolkit-for-elementor').'</a>'
                );
            }
            return $footer_text;
        }

        function export_settings(){
            if( isset($_POST['toolkit_upon_export']) && $_POST['toolkit_upon_export'] ){
                $output = array('toolkit_exported_settings' => time());
                if( $_POST['toolkit_upon_export'] == 'rem_some' ){
                    if( $_POST['toolkit_booster_export'] == 'yes' ){
                        $output['toolkit_webserver_tweaks'] = get_option('toolkit_webserver_tweaks', array());
                        $output['toolkit_elementor_tweaks'] = get_option('toolkit_elementor_tweaks', array());
                        $output['toolkit_unload_options'] = get_option('toolkit_unload_options', array());
                        $output['toolkit_elementor_settings'] = get_option('toolkit_elementor_settings', array());
                        $output['toolkit_elementor_dbopt_settings'] = get_option('toolkit_elementor_dbopt_settings', array());
                        $output['toolkit_elementor_cache_settings'] = get_option('toolkit_elementor_cache_settings', array());
                    }
                    if( $_POST['toolkit_theme_export'] == 'yes' ){
                        $output['theme_disable_themeless'] = get_option('theme_disable_themeless', '');
                        $output['toolkit_dashboard_settings_options'] = get_option('toolkit_dashboard_settings_options', array());
                        $output['toolkit_login_settings_options'] = get_option('toolkit_login_settings_options', array());
                        $output['toolkit_login_page_url_options'] = get_option('toolkit_login_page_url_options', array());
                    }
                    if( $_POST['toolkit_toolbox_export'] == 'yes' ){
                        $output['theme_disable_header_code'] = get_option('theme_disable_header_code');
                        $output['theme_disable_footer_code'] = get_option('theme_disable_footer_code');
                        $output['theme_disable_bodytag_code'] = get_option('theme_disable_bodytag_code');
                        $output['toolkit_background_tasks_options'] = get_option('toolkit_background_tasks_options', array());
                        $output['toolkit_core_tweaks_options'] = get_option('toolkit_core_tweaks_options', array());
                        $output['toolkit_access_plugin_settings'] = get_option('toolkit_access_plugin_settings', array());
                        $output['toolkit_wp_widget_disable_dashboard'] = get_option('toolkit_wp_widget_disable_dashboard');
                        $output['toolkit_wp_widget_disable_sidebar'] = get_option('toolkit_wp_widget_disable_sidebar');
                        $output['toolkit_elementor_widgets_disable'] = get_option('toolkit_elementor_widgets_disable');
                    }
                    if( $_POST['toolkit_dhandle_export'] == 'yes' ){
                        $output['toolkit_data_handling_options'] = get_option('toolkit_data_handling_options', array());
                    }
                } else {
                    $output['toolkit_webserver_tweaks'] = get_option('toolkit_webserver_tweaks', array());
                    $output['toolkit_elementor_tweaks'] = get_option('toolkit_elementor_tweaks', array());
                    $output['toolkit_unload_options'] = get_option('toolkit_unload_options', array());
                    $output['toolkit_elementor_settings'] = get_option('toolkit_elementor_settings', array());
                    $output['toolkit_elementor_dbopt_settings'] = get_option('toolkit_elementor_dbopt_settings', array());
                    $output['toolkit_elementor_cache_settings'] = get_option('toolkit_elementor_cache_settings', array());
                    $output['theme_disable_themeless'] = get_option('theme_disable_themeless', '');
                    $output['toolkit_dashboard_settings_options'] = get_option('toolkit_dashboard_settings_options', array());
                    $output['toolkit_login_settings_options'] = get_option('toolkit_login_settings_options', array());
                    $output['toolkit_login_page_url_options'] = get_option('toolkit_login_page_url_options', array());
                    $output['theme_disable_header_code'] = get_option('theme_disable_header_code');
                    $output['theme_disable_footer_code'] = get_option('theme_disable_footer_code');
                    $output['theme_disable_bodytag_code'] = get_option('theme_disable_bodytag_code');
                    $output['toolkit_background_tasks_options'] = get_option('toolkit_background_tasks_options', array());
                    $output['toolkit_core_tweaks_options'] = get_option('toolkit_core_tweaks_options', array());
                    $output['toolkit_access_plugin_settings'] = get_option('toolkit_access_plugin_settings', array());
                    $output['toolkit_wp_widget_disable_dashboard'] = get_option('toolkit_wp_widget_disable_dashboard');
                    $output['toolkit_wp_widget_disable_sidebar'] = get_option('toolkit_wp_widget_disable_sidebar');
                    $output['toolkit_elementor_widgets_disable'] = get_option('toolkit_elementor_widgets_disable');
                    $output['toolkit_data_handling_options'] = get_option('toolkit_data_handling_options', array());
                }
                $file_name = "toolkit-settings-".date('Y-m-d-H-i-s').".json";
                header("Content-Type: application/json");
                header("Content-Disposition: attachment; filename=".$file_name);
                header("Pragma: no-cache");
                header("Expires: 0");
                echo json_encode($output);
                exit;
            } else {
                $redirect = $_SERVER['HTTP_REFERER'];
            }
            wp_redirect($redirect);
            exit;
        }

        function export_htaccess(){
            $file_name = ".htaccess";
            $file_path = ABSPATH . '.htaccess';
            if( file_exists($file_path) ){
                header("Content-Type: ".mime_content_type($file_path));
                header("Content-Disposition: attachment; filename=".$file_name);
                header("Pragma: no-cache");
                header("Expires: 0");
                readfile($file_path);
            } else {
                $redirect = add_query_arg('hterror', 'none', $_SERVER['HTTP_REFERER']);
                wp_redirect($redirect);
            }
            exit;
        }

        function download_htaccess_notice(){
            toolkit_admin_display_notice();
        }

        function import_settings(){
            $redirect = add_query_arg('import', 'fail', $_SERVER['HTTP_REFERER']);
            if( isset($_FILES['toolkit_import_file']) && $_FILES['toolkit_import_file'] ){
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                $attachment_id = media_handle_upload( 'toolkit_import_file', 0 );
                if ( ! is_wp_error( $attachment_id ) ) {
                    $file_path = get_attached_file($attachment_id);
                    if( $file_path ){
                        $myfile = fopen($file_path, "r");
                        $settingsJson = fread( $myfile, filesize($file_path) );
                        fclose($myfile);
                        if( $settingsJson ){
                            $settingsData = json_decode($settingsJson, true);
                            if( isset($settingsData['toolkit_exported_settings']) && $settingsData['toolkit_exported_settings'] ){
                                unset($settingsData['toolkit_exported_settings']);
                                if( $settingsData ){
                                    foreach ($settingsData as $key => $data) {
                                        update_option($key, $data);
                                    }
                                }
                                wp_delete_attachment($attachment_id, true);
                                $redirect = add_query_arg('import', 'file', $_SERVER['HTTP_REFERER']);
                            }
                        }
                    }
                }
            }
            wp_redirect($redirect);
            exit;
        }

        function import_settings_notice(){
            toolkit_admin_display_notice();
        }

        public function dashboard_template(){
            global $pagenow;
            if( $pagenow == 'index.php' ){
                $dashOpts = get_option('toolkit_dashboard_settings_options', array());
                $template_key = 'toolkit_dashboard_template';
                $template_id = $dashOpts['template'];
                $has_dismissable = ( isset($dashOpts['dismissible']) && $dashOpts['dismissible'] == 'yes' );
                $is_show_title = ( isset($dashOpts['showtitle']) && $dashOpts['showtitle'] == 'yes' );
                ?>
                <div id="welcome-panel<?php echo $template_key?>" data-welcome_key="<?php echo $template_key?>" class="welcome-panel toolkit-welcome-panel" style="display:none">
                    <?php if($has_dismissable){ ?>
                        <a class="welcome-panel-close" href="#" aria-label="Dismiss the welcome panel">Dismiss</a>
                    <?php } ?>
                    <div class="welcome-panel-content welcome-panel-content<?php echo $template_key?>">
                        <?php if( $is_show_title ){ ?>
                            <h2><?php echo get_the_title($template_id)?></h2>
                        <?php } ?>
                        <?php $elementor = @Elementor\Plugin::instance();
                        $elementor->frontend->register_styles();
                        $elementor->frontend->enqueue_styles();
                        $elementor->frontend->register_scripts();
                        $elementor->frontend->enqueue_scripts();
                        echo $elementor->frontend->get_builder_content($template_id, true); ?>
                    </div>
                </div>
                <?php
            }
        }

        public function admin_footer_script(){
            global $pagenow;
            if( $pagenow == 'index.php' ){
                $dashOpts = get_option('toolkit_dashboard_settings_options', array());
                $template_key = 'toolkit_dashboard_template';
                $welcome = sprintf("jQuery('#welcome-panel%1\$s').insertBefore('#dashboard-widgets-wrap');jQuery('#welcome-panel%1\$s').show();", $template_key);
                if( isset($dashOpts['dismissible']) && $dashOpts['dismissible'] == 'yes' ){
                    $welcome .= sprintf(" jQuery('.toolkit-welcome-panel a.welcome-panel-close').on('click', function(e){
                e.preventDefault();
                var vum_panel = jQuery(this).parent('.toolkit-welcome-panel');
                vum_panel.hide();
                });");
                }
                $scripts = '<script type="text/javascript">';
                $scripts .= '/* <![CDATA[ */';
                $scripts .= '   jQuery(document).ready(function() { ' . $welcome . ' });';
                $scripts .= '/* ]]> */';
                $scripts .= '</script>';
                echo $scripts;
            }
        }
					
    }

}
