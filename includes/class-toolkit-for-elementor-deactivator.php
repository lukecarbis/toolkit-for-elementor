<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/includes
 * @author     ToolKit For Elementor <support@toolkitforelementor.com>
 */

class Toolkit_For_Elementor_Deactivator {

	public static function deactivate() {

	}

	public static function uninstall($dataHandling){
	    if( $dataHandling ){
            if( isset($dataHandling['booster_uninstall']) && $dataHandling['booster_uninstall'] != 'remember' ){
                delete_option('toolkit_webserver_tweaks');
                delete_option('toolkit_elementor_tweaks');
                delete_option('toolkit_unload_options');
                delete_option('toolkit_elementor_settings');
            }
            if( isset($dataHandling['scan_history']) && $dataHandling['scan_history'] != 'remember' ){
                global $table_prefix, $wpdb;
                $gtmetrix_table = $table_prefix . "toolkit_gtmetrix";
                $sql = "TRUNCATE `$gtmetrix_table`";
                $wpdb->query($sql);
                if( function_exists('toolkit_remove_minify_css_js_files') ){
                    toolkit_remove_minify_css_js_files(WP_CONTENT_DIR . '/toolkit-reports/');
                }
            }
            if( isset($dataHandling['theme_uninstall']) && $dataHandling['theme_uninstall'] != 'remember' ){
                delete_option('theme_disable_themeless');
                delete_option('toolkit_dashboard_settings_options');
            }
            if( isset($dataHandling['toolbox_uninstall']) && $dataHandling['toolbox_uninstall'] != 'remember' ){
                delete_option('theme_disable_header_code');
                delete_option('theme_disable_footer_code');
                delete_option('theme_disable_bodytag_code');
                delete_option('toolkit_background_tasks_options');
                delete_option('toolkit_core_tweaks_options');
                delete_option('toolkit_access_plugin_settings');
                self::delete_widgets();
            }
            if( isset($dataHandling['license_uninstall']) && $dataHandling['license_uninstall'] != 'remember' ){
                delete_option('toolkit_active');
                delete_option('toolkit_license_details');
                delete_option('toolkit_license_key');
                delete_option('toolkit_license_status');
                delete_option('toolkit_other_details');
            }
        } else {
            delete_option('theme_disable_themeless');
            delete_option('toolkit_dashboard_settings_options');
            delete_option('theme_disable_header_code');
            delete_option('theme_disable_footer_code');
            delete_option('theme_disable_bodytag_code');
            delete_option('toolkit_background_tasks_options');
            delete_option('toolkit_core_tweaks_options');
            delete_option('toolkit_access_plugin_settings');
            delete_option('toolkit_active');
            delete_option('toolkit_license_details');
            delete_option('toolkit_license_key');
            delete_option('toolkit_license_status');
            delete_option('toolkit_other_details');
            delete_option('toolkit_webserver_tweaks');
            delete_option('toolkit_elementor_tweaks');
            delete_option('toolkit_unload_options');
            delete_option('toolkit_elementor_settings');

            global $table_prefix, $wpdb;
            $gtmetrix_table = $table_prefix . "toolkit_gtmetrix";
            $sql = "TRUNCATE `$gtmetrix_table`";
            $wpdb->query($sql);
            if( function_exists('toolkit_remove_minify_css_js_files') ){
                toolkit_remove_minify_css_js_files(WP_CONTENT_DIR . '/toolkit-reports/');
            }
            self::delete_widgets();
        }
    }

    public static function delete_widgets(){
        $widget_disable_options_all = [
            'toolkit_wp_widget_disable_dashboard',
            'toolkit_wp_widget_disable_sidebar',
            'toolkit_elementor_widgets_disable',
        ];
        $widget_disable_options = [
            'toolkit_wp_widget_disable_dashboard',
            'toolkit_wp_widget_disable_sidebar',
        ];

        if ( ! is_multisite() ) {
            foreach ( $widget_disable_options_all as $widget_disable_option ) {
                delete_option( $widget_disable_option );
            }
        } else {
            global $wpdb;
            $widget_disable_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $widget_disable_ids as $widget_disable_id ) {
                switch_to_blog( $widget_disable_id );
                foreach ( $widget_disable_options as $widget_disable_option ) {
                    delete_option( $widget_disable_option );
                }
            }
            restore_current_blog();
            delete_option( 'toolkit_elementor_widgets_disable' );
        }
    }

}
