<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/includes
 * @author     ToolKit For Elementor <support@toolkitforelementor.com>
 */
class Toolkit_For_Elementor_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    //assign capability to administrator for use of Toolkit
        $accessOpts = get_option('toolkit_access_plugin_settings', array());
        if( ! $accessOpts ){
            $data = array(
                'restrict_access'   => 'administrator',
                'only_me_id'        => get_current_user_id(),
                'hide_plugin'       => 'no'
            );
            update_option('toolkit_access_plugin_settings', $data);
        }

		if ( did_action( 'elementor/loaded' ) ) {
			$gtMetrixLog = get_option( 'toolkit_gtmetrix_log');
			$gtMetrixCredit = get_option( 'toolkit_gtmetrix_credit');
			if( ! $gtMetrixLog && ! $gtMetrixCredit ){
				update_option('toolkit_gtmetrix_credit', 5);
			}
		}

		global $table_prefix, $wpdb;
		$gtmetrix_table = $table_prefix . "toolkit_gtmetrix";
		//Check to see if the table exists already, if not, then create it
		if( $wpdb->get_var( "SHOW TABLES LIKE '$gtmetrix_table'" ) != $gtmetrix_table ) {
			$sql = "CREATE"." TABLE IF NOT EXISTS `$gtmetrix_table` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `test_id` varchar(100) NOT NULL,
					  `scan_url` text NOT NULL,
					  `load_time` varchar(10) NOT NULL,
					  `page_speed` varchar(10) NOT NULL,
					  `yslow` varchar(10) NOT NULL,
					  `region` varchar(200) NOT NULL,
					  `browser` varchar(200) NOT NULL,
					  `response_log` longtext NOT NULL,
					  `resources` longtext NOT NULL,
					  `is_free` tinyint(4) NOT NULL,
					  `created` datetime NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		} else {
			if( ! $wpdb->get_var("SHOW COLUMNS FROM `$gtmetrix_table` LIKE 'scan_url';") ){
				$sql = "ALTER TABLE `$gtmetrix_table` ADD `scan_url` TEXT NOT NULL  AFTER `test_id`;";
				$wpdb->query($sql);
			}
		}
        toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
	}
}
