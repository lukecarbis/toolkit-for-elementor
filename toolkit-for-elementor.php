<?php
/**
 * Plugin Name:       ToolKit For Elementor
 * Author:            ToolKit For Elementor
 * Plugin URI:        https://toolkitforelementor.com
 * Description:       Build Faster Elementor Sites in Less Time with 20+ Performance Tweaks, Site Admin Tools, Brand New Access Manager, Syncer, WooCommerce Optimizations & More.
 * Tags: 	      Toolkit, Toolkit for Elementor, Elementor tools, Elementor add-ons, Elementor extensions
 * Version:           1.4.9
 * Tested up to:      6.1
 * Elementor tested up to: 3.8
 * Elementor Pro tested up to: 3.8
 * WC tested up to: 7.0.1
 * Requires at least: 5.0
 * Text Domain:       toolkit-for-elementor
 * Author URI:        https://toolkitforelementor.com
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 */

// Abort if this file is called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Defines plugin version
 */
define( 'TOOLKIT_FOR_ELEMENTOR_VERSION', '1.4.9' );
define( 'TOOLKIT_FOR_ELEMENTOR_NAME', 'toolkit-for-elementor' );
define( 'TOOLKIT_FOR_ELEMENTOR_AUTHOR', 'ToolKit For Elementor' );
define( 'TOOLKIT_FOR_ELEMENTOR_FILE', __FILE__ );
define( 'TOOLKIT_FOR_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOOLKIT_FOR_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );
define( 'TOOLKIT_FOR_ELEMENTOR_UPDATE_URL', esc_url_raw('https://toolkitforelementor.com/') );
define( 'TOOLKIT_FOR_ELEMENTOR_ITEM_ID', 2705 );
define( 'TOOLKIT_FOR_ELEMENTOR_MASTER_PATH', WP_CONTENT_DIR . '/cache' );
define( 'TOOLKIT_FOR_ELEMENTOR_MIN_PATH', WP_CONTENT_DIR . '/cache/toolkit/min/' . get_current_blog_id() );
define( 'TOOLKIT_FOR_ELEMENTOR_MIN_URL', WP_CONTENT_URL . '/cache/toolkit/min/' . get_current_blog_id() );

if ( ! defined( 'ELEMENTOR_VERSION' ) && ! is_callable( 'Elementor\Plugin::instance' ) ) {
	function toolkit_dependency_not_available() {
		if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
			$url = network_admin_url() . 'plugins.php?s=elementor';
		} else {
			$url = network_admin_url() . 'plugin-install.php?s=elementor';
		}
		echo '<div class="notice notice-error">';
		echo '<p>' . sprintf( __( 'Sorry, <b>ToolKit For Elementor</b> requires <strong><a href="%s">Elementor</strong></a> plugin installed & activated in order to work.', 'toolkit-for-elementor' ) . '</p>', $url );
		echo '</div>';
	}
	add_action( 'admin_notices', 'toolkit_dependency_not_available' );
    return;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-toolkit-for-elementor-activator.php
 */
function activate_toolkit_for_elementor() {
	require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/class-toolkit-for-elementor-activator.php';
	Toolkit_For_Elementor_Activator::activate();
    if ( ! wp_next_scheduled ( 'toolkit_verify' ) ) {
        wp_schedule_event( time(), 'hourly', 'toolkit_verify' );
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-toolkit-for-elementor-deactivator.php
 */
function deactivate_toolkit_for_elementor() {
	require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/class-toolkit-for-elementor-deactivator.php';
	Toolkit_For_Elementor_Deactivator::deactivate();
    wp_clear_scheduled_hook( 'toolkit_verify' );
}

register_activation_hook( __FILE__, 'activate_toolkit_for_elementor' );
register_deactivation_hook( __FILE__, 'deactivate_toolkit_for_elementor' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/class-toolkit-for-elementor.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_toolkit_for_elementor() {

	$plugin = new Toolkit_For_Elementor();
	$plugin->run();

}
run_toolkit_for_elementor();

$bgTasks = new Toolkit_Elementor_BgTasks();
$bgTasksOpts = get_option('toolkit_background_tasks_options', array());
if( isset($bgTasksOpts['disable_revision']) && $bgTasksOpts['disable_revision'] == 'yes' ) {
    if( isset($bgTasksOpts['revision_frequency']) && (! empty($bgTasksOpts['revision_frequency'])) ) {
        if(defined('WP_POST_REVISIONS')) {
            add_action('admin_notices', array($bgTasks, 'notice_post_revisions'));
        } else {
            define('WP_POST_REVISIONS', $bgTasksOpts['revision_frequency']);
        }
    }
}

if( isset($bgTasksOpts['disable_autosave']) && $bgTasksOpts['disable_autosave'] == 'yes' ) {
    if( isset($bgTasksOpts['autosave_interval']) && (! empty($bgTasksOpts['autosave_interval'])) ) {
        if(defined('AUTOSAVE_INTERVAL')) {
            add_action('admin_notices', array($bgTasks, 'notice_autosave_interval'));
        } else {
            define('AUTOSAVE_INTERVAL', $bgTasksOpts['autosave_interval']);
        }
    }
}
