<?php

/**
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks. Also maintains the unique identifier of this 
 * plugin as well as the current version of the plugin.
 *
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/includes
 * @author     ToolKit For Elementor <support@toolkitforelementor.com>
 */
class Toolkit_For_Elementor {

	/**
	 * Loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Toolkit_For_Elementor_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * Current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TOOLKIT_FOR_ELEMENTOR_VERSION' ) ) {
			$this->version = TOOLKIT_FOR_ELEMENTOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'toolkit-for-elementor';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Toolkit_For_Elementor_Loader. Orchestrates the hooks of the plugin.
	 * - Toolkit_For_Elementor_i18n. Defines internationalization functionality.
	 * - Toolkit_For_Elementor_Admin. Defines all hooks for the admin area.
	 * - Toolkit_For_Elementor_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/class-toolkit-for-elementor-loader.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/functions-toolkit-for-elementor.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'includes/class-toolkit-for-elementor-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-admin.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-elementor-bgtasks.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-user-access-links.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-elementor-login-helper.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-elementor-update-manager.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-cache.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-preload.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-dbopt.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-ninja.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-login.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-theme-disable-admin.php';
        /**
         * LAZY LOAD
         */
        require_once TOOLKIT_FOR_ELEMENTOR_PATH .'admin/class-toolkit-for-elementor-lazyload.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'public/class-toolkit-for-elementor-public.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'public/class-theme-disable-public.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'public/class-toolkit-ga-fonts.php';

		/**
		 * Elementor ELEMENT CALL LOAD
		 * GT METRIX CLASS LOAD
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH .'includes/elementor-class.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH .'includes/settings.php';
		
		/**
		 * Syncer
		 */
		require_once TOOLKIT_FOR_ELEMENTOR_PATH .'includes/syncer-auth.php';
		require_once TOOLKIT_FOR_ELEMENTOR_PATH .'includes/syncer.php';
		
		$this->loader = new Toolkit_For_Elementor_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Toolkit_For_Elementor_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Toolkit_For_Elementor_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Toolkit_For_Elementor_Admin( $this->get_plugin_name(), $this->get_version() );

	//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
	//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_filter( 'plugin_action_links_'.plugin_basename(TOOLKIT_FOR_ELEMENTOR_FILE), $plugin_admin, 'plugin_screen_links' );
        $this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2 );
        $this->loader->add_action( 'wp_ajax_toolkit_restrict_access_save', $plugin_admin , 'save_restrict_access' );
        $this->loader->add_action( 'pre_current_active_plugins', $plugin_admin , 'hide_plugin_from_list' );
        //enable ninja stack options htaccess
        $this->loader->add_action( 'mod_rewrite_rules', $plugin_admin , 'ninja_stack_settings' );
        //remove cache file on plugin deactivation or theme changed
        $this->loader->add_action( 'activated_plugin', $plugin_admin , 'detect_plugin_theme_change' );
        $this->loader->add_action( 'deactivated_plugin', $plugin_admin , 'detect_plugin_theme_change' );
        $this->loader->add_action( 'after_switch_theme', $plugin_admin , 'detect_plugin_theme_change' );
        $this->loader->add_action( 'admin_bar_menu', $plugin_admin , 'admin_bar_link', 110 );
        $this->loader->add_action( 'init', $plugin_admin , 'clear_plugin_cache' );

        $theme_disable = new Theme_Disable_Admin();
        //check updates for plugin
        $this->loader->add_action( 'admin_init', $theme_disable, 'edd_plugin_updater' );

        $login = new Toolkit_Elementor_TempLogin();
        $this->loader->add_filter( 'plugin_action_links', $login, 'disable_plugin_deactivation', 10, 4 );
        $this->loader->add_action( 'wp_ajax_save_toolkit_login_user', $login, 'save_user' );
        $this->loader->add_action( 'wp_ajax_remove_toolkit_login_user', $login, 'remove_toolkit_login_user' );
        $this->loader->add_action( 'admin_menu', $login, 'remove_plugins_access' );
        $this->loader->add_action( 'register_post_type_args', $login, 'register_post_type_args', 99, 2 );

        $bgTasks = new Toolkit_Elementor_BgTasks();
        $this->loader->add_action('wp_ajax_toolkit_bgtasks_options_save', $bgTasks, 'save_settings');
        $this->loader->add_action('wp_ajax_toolkit_loginpage_options_save', $bgTasks, 'save_login_page_settings');
        $bgTasksOpts = get_option('toolkit_background_tasks_options', array());
        if( isset($bgTasksOpts['disable_heartbeat']) && $bgTasksOpts['disable_heartbeat'] == 'yes' ) {
            if( isset($bgTasksOpts['heartbeat_frequency']) && $bgTasksOpts['heartbeat_frequency'] == 'disable' ) {
                $this->loader->add_action('init', $bgTasks, 'disable_heartbeat', 1);
            } else {
                $this->loader->add_filter('heartbeat_settings', $bgTasks, 'heartbeat_frequency');
            }
        }
        $this->loader->add_action('login_enqueue_scripts', $bgTasks, 'apply_login_page_settings', 20);
        $this->loader->add_filter('login_headerurl', $bgTasks, 'login_headerurl', 101);

        $dbOpt = new Toolkit_For_Elementor_DbOpt();
        $this->loader->add_action('wp_ajax_toolkit_dbopt_setting_save', $dbOpt, 'save_settings');
        $this->loader->add_filter('cron_schedules', $dbOpt, 'cron_schedules');
        $this->loader->add_action($dbOpt->cron_hook, $dbOpt, 'clean_database');

        $update = new Toolkit_Elementor_Update_Manager();
        $this->loader->add_action('wp_ajax_toolkit_coretweak_options_save', $update, 'save_settings');
        $this->loader->add_action('wp_ajax_toolkit_data_handling_save', $update, 'save_data_handling_options');
        $this->loader->add_action('wp_ajax_toolkit_testing_options_save', $update, 'save_beta_testing_options');
        $this->loader->add_action('wp_ajax_toolkit_reset_settings_options', $update, 'reset_settings');
        $this->loader->add_action('wp_ajax_toolkit_clear_scan_history', $update, 'clear_scan_history');
        $this->loader->add_action('wp_ajax_toolkit_dashboard_options_save', $update, 'save_dashboard_options');
        $this->loader->add_filter('admin_footer_text', $update, 'admin_footer_text', 99);
        $this->loader->add_action('admin_post_toolkit_export_settings', $update, 'export_settings');
        $this->loader->add_action('admin_post_toolkit_import_settings', $update, 'import_settings');
        $this->loader->add_action('admin_post_toolkit_export_htaccess', $update, 'export_htaccess');
        $this->loader->add_filter('upload_mimes', $update, 'additional_mime_types');
        if( isset($_GET['import']) && $_GET['import'] ){
            $this->loader->add_action('admin_notices', $update, 'import_settings_notice');
        }
        if( isset($_GET['hterror']) && $_GET['hterror'] ){
            $this->loader->add_action('admin_notices', $update, 'download_htaccess_notice');
        }
        $coreTweaks = get_option('toolkit_core_tweaks_options', array());
        if( isset($coreTweaks['wpcore']) && $coreTweaks['wpcore'] == 'yes' ){
            add_filter('auto_update_core', '__return_false');
            add_filter('wp_auto_update_core', '__return_false');
        }
        if( isset($coreTweaks['plugin']) && $coreTweaks['plugin'] == 'yes' ){
            add_filter('auto_update_plugin', '__return_false');
        }
        if( isset($coreTweaks['themes']) && $coreTweaks['themes'] == 'yes' ){
            add_filter('auto_update_theme', '__return_false');
        }
        $dashOpts = get_option('toolkit_dashboard_settings_options', array());
        if( isset($dashOpts['template']) && $dashOpts['template'] > 0 ){
            $this->loader->add_action('in_admin_header', $update, 'dashboard_template');
            $this->loader->add_action('admin_footer', $update, 'admin_footer_script');
        }

        $cache = new Toolkit_For_Elementor_Cache();
        $this->loader->add_action('wp_ajax_toolkit_cache_setting_save', $cache, 'save_settings');
        $this->loader->add_action('save_post', $cache, 'additional_purge', 10, 3);
        $this->loader->add_action('woocommerce_product_set_stock', $cache, 'purge_on_stock_update');
        $this->loader->add_action('woocommerce_variation_set_stock', $cache, 'purge_on_stock_update');
        $this->loader->add_action('toolkit_purge_preload_cache', $cache, 'purge_preload_cache');
        $preload = new Toolkit_For_Elementor_Preload();
        $this->loader->add_filter('cron_schedules', $preload, 'cron_schedules');
        $this->loader->add_action('toolkit_enable_preload_cache', $preload, 'enable_preload_cache');
        $this->loader->add_action('toolkit_process_preload_cache', $preload, 'process_preload_cache');
        $this->loader->add_action('wp_ajax_toolkit_run_preload_cache', $preload, 'enable_preload_cache');
        $this->loader->add_action('admin_footer', $preload, 'admin_footer_css');
        $this->loader->add_action('wp_footer', $preload, 'admin_footer_css');

        $ninja = new Toolkit_For_Elementor_Ninja();
        $this->loader->add_action('wp_ajax_toolkit_get_post_assets', $ninja, 'get_assets');
        $this->loader->add_action('wp_ajax_toolkit_save_post_assets', $ninja, 'save_assets');
        if( isset($_GET['toolkit_assets']) && $_GET['toolkit_assets'] == '1' ){
            $ninja->init_filters();
        }
        if( ! isset($_GET['toolkit_assets']) ){
            $this->loader->add_action( 'wp_print_styles', $ninja, 'dequeue_post_styles', 999999);
            $this->loader->add_action( 'wp_print_scripts', $ninja, 'dequeue_post_scripts', 999999);
            $this->loader->add_action( 'wp_print_footer_scripts', $ninja, 'dequeue_post_styles', 1);
            $this->loader->add_action( 'wp_print_footer_scripts', $ninja, 'dequeue_post_scripts', 1);
        }
        $logUrl = new Toolkit_For_Elementor_Login();


        $lazyLoad = new Toolkit_For_Elementor_LazyLoad();
        $this->loader->add_action('wp_ajax_toolkit_lazyload_setting_save', $lazyLoad, 'save_settings');
        $lazyOpts = get_option('toolkit_elementor_settings', array());
        if( isset($lazyOpts['image']) && $lazyOpts['image'] == 'on' ){
            $this->loader->add_action('wp_enqueue_scripts', $lazyLoad, 'enqueue_scripts');
            if( isset($lazyOpts['img_loadtype']) && $lazyOpts['img_loadtype'] == 'toolkit' ){
                add_filter( 'wp_lazy_loading_enabled', '__return_false' );
            }
        }
        $minifyTweaks = get_option('toolkit_elementor_tweaks', array());
        if( isset($minifyTweaks['js_delay']) && $minifyTweaks['js_delay'] == 'on' ){
            $this->loader->add_action('wp_enqueue_scripts', $lazyLoad, 'enqueue_scripts');
        }

    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Toolkit_For_Elementor_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'bodytag_custom_code' );
		$this->loader->add_action( 'init', $plugin_public, 'minify_css_js_fonts', 9 );

        $theme_disable = new Theme_Disable_Public();

        $unloadOpts = get_option('toolkit_unload_options', array());
        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_emojis']) && $unloadOpts['disable_emojis'] == 'on' ){
            $this->loader->add_action( 'init', $plugin_public, 'disable_emojis');
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_gutenberg']) && $unloadOpts['disable_gutenberg'] == 'on' ){
            $this->loader->add_action( 'wp_print_styles', $plugin_public, 'disable_gutenberg_css', 100);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_commentreply']) && $unloadOpts['disable_commentreply'] == 'on' ){
            $this->loader->add_filter( 'wp_print_scripts', $plugin_public, 'disable_comment_reply', 100);
        }

        $bgTasks = new Toolkit_Elementor_BgTasks();

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_restapi']) && $unloadOpts['disable_restapi'] == 'on' ){
            $this->loader->add_filter('rest_authentication_errors', $bgTasks, 'disable_rest_api_errors', 20);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_dashicons']) && $unloadOpts['disable_dashicons'] == 'on' ){
            $this->loader->add_action('wp_enqueue_scripts', $bgTasks, 'disable_dashicons');
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_oembed']) && $unloadOpts['disable_oembed'] == 'on' ){
            $this->loader->add_action('init', $bgTasks, 'disable_oembed', 9999);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_rssfeed']) && $unloadOpts['disable_rssfeed'] == 'on' ){
            $this->loader->add_action('template_redirect', $bgTasks, 'disable_rssfeed', 1);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['disable_xmlrpc']) && $unloadOpts['disable_xmlrpc'] == 'on' ){
            add_filter('xmlrpc_enabled', '__return_false');
            $this->loader->add_filter('wp_headers', $bgTasks, 'remove_x_pingback');
            add_filter('pings_open', '__return_false', 9999);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_qstrings']) && $unloadOpts['remove_qstrings'] == 'on' ){
            $this->loader->add_action('init', $bgTasks, 'remove_query_strings');
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_jmigrate']) && $unloadOpts['remove_jmigrate'] == 'on' ){
            $this->loader->add_action('wp_default_scripts', $bgTasks, 'remove_jquery_migrate');
        }

        $this->loader->add_action('upgrader_process_complete', $bgTasks, 'reset_cache_on_update', 20, 2);
        $this->loader->add_action('deactivated_plugin', $bgTasks, 'reset_cache_on_deactivate', 20);

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_apilinks']) && $unloadOpts['remove_apilinks'] == 'on' ){
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
            remove_action('wp_head', 'rest_output_link_wp_head');
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_feedlinks']) && $unloadOpts['remove_feedlinks'] == 'on' ){
            remove_action('wp_head', 'feed_links', 2);
            remove_action('wp_head', 'feed_links_extra', 3);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_rsdlink']) && $unloadOpts['remove_rsdlink'] == 'on' ){
            remove_action('wp_head', 'rsd_link');
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_shortlink']) && $unloadOpts['remove_shortlink'] == 'on' ){
            remove_action('wp_head', 'wp_shortlink_wp_head');
            remove_action('template_redirect', 'wp_shortlink_header', 11);
        }

        if( $unloadOpts && is_array($unloadOpts) && isset($unloadOpts['remove_wlwlink']) && $unloadOpts['remove_wlwlink'] == 'on' ){
            remove_action('wp_head', 'wlwmanifest_link');
        }

        $coreTweaks = get_option('toolkit_core_tweaks_options', array());

        if( $coreTweaks && is_array($coreTweaks) && isset($coreTweaks['disable_sitemap']) && $coreTweaks['disable_sitemap'] == 'yes' ){
            add_filter( 'wp_sitemaps_enabled', '__return_false' );
        }

        $login = new Toolkit_Elementor_TempLogin();
        $this->loader->add_action( 'init', $login, 'init_login' );
        $this->loader->add_filter( 'wp_authenticate_user', $login, 'disable_tkfe_ual', 10, 2 );
        $this->loader->add_filter( 'allow_password_reset', $login, 'disable_password_reset', 10, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Toolkit_For_Elementor_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
