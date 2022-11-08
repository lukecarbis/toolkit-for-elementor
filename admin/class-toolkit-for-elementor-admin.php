<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 *
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/admin
 * @author     ToolKit For Elementor
 */
class Toolkit_For_Elementor_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/toolkit-for-elementor-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/toolkit-for-elementor-admin.js', array( 'jquery' ), $this->version, false );
	}

    function save_restrict_access() {
        if( isset($_POST['restrict_access']) && isset($_POST['hide_plugin']) && isset($_POST['only_me_id']) ){
            $data = array(
                'restrict_access'   => esc_sql($_POST['restrict_access']),
                'booster_access'    => esc_sql($_POST['booster_access']),
                'syncer_access'     => esc_sql($_POST['syncer_access']),
                'theme_access'      => esc_sql($_POST['theme_access']),
                'toolbox_access'    => esc_sql($_POST['toolbox_access']),
                'license_access'    => esc_sql($_POST['license_access']),
                'only_me_id'        => esc_sql($_POST['only_me_id']),
                'hide_plugin'       => esc_sql($_POST['hide_plugin'])
            );
            update_option('toolkit_access_plugin_settings', $data);
            $response = array('success'=>true, 'message'=>__("Settings have saved successfully."));
        } else {
            $response = array('success'=>false, 'message'=>__("Incomplete arguments passed."));
        }
        wp_send_json($response);
    }

    function hide_plugin_from_list() {
        $toolkitAccess = get_option('toolkit_access_plugin_settings', array());
        if( $toolkitAccess && isset($toolkitAccess['hide_plugin']) && $toolkitAccess['hide_plugin'] == 'yes' ){
            global $wp_list_table;
            $myplugin = plugin_basename(TOOLKIT_FOR_ELEMENTOR_FILE);
            $myplugins = $wp_list_table->items;
            foreach ($myplugins as $key => $val) {
                if ( $key == $myplugin ) {
                    unset($wp_list_table->items[$key]);
                }
            }
        }
	}

    function plugin_screen_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=toolkit-performance-tool' ) . '">' . __( 'Settings', 'toolkit-for-elementor' ) . '</a>'
        );
        $links = array_merge( $plugin_links, $links );
        return $links;
    }

    public function plugin_row_meta( $links, $file ) {
        if ( plugin_basename(TOOLKIT_FOR_ELEMENTOR_FILE) === $file ) {
            $plugin_name = 'ToolKit';
            $plugin_slug = basename( TOOLKIT_FOR_ELEMENTOR_FILE, '.php' );
            $row_meta = array(
                'view-details' => sprintf( '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
                    esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug . '&TB_iframe=true&width=600&height=550' ) ),
                    esc_attr( sprintf( __( 'More information about %s' ), $plugin_name ) ),
                    esc_attr( $plugin_name ),
                    __( 'View details' )
                ),
                'changelog' => '<a href="' . esc_url( 'https://toolkitforelementor.com/changelog/' ) . '" aria-label="' . esc_attr__( 'View ToolKit Changelog', 'toolkit-for-elementor' ) . '" target="_blank">' . esc_html__( 'Changelog', 'toolkit-for-elementor' ) . '</a>',
            );
            return array_merge( $links, $row_meta );
        }
        return (array) $links;
    }

    public function admin_bar_link( $wp_admin_bar ) {
	    if( ! current_user_can('manage_options') ){
	        return;
        }
        $protocol = (is_ssl()) ? 'https:' : 'http:';
        $current_url = $protocol . "//" . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $args = array(
            'id'    => 'toolkit_page',
            'title' => 'ToolKit',
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_page',
            'id'    => 'toolkit_cache_page',
            'title' => 'Performance',
            'href'  => '#',
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $master_url = add_query_arg('toolkit_clear_master', '1', $current_url);
        $args = array(
            'parent'=> 'toolkit_cache_page',
            'id'    => 'toolkit_page_two',
            'title' => 'Clear All Cache',
            'href'  => $master_url,
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $toolkit_url = add_query_arg('toolkit_clear_cache', '1', $current_url);
        $args = array(
            'parent'=> 'toolkit_cache_page',
            'id'    => 'toolkit_page_one',
            'title' => 'Clear ToolKit Cache',
            'href'  => $toolkit_url,
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_page',
            'id'    => 'toolkit_elem_page',
            'title' => 'Elementor Tools',
            'href'  => '#',
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        if( is_admin() ){
            $args = array(
                'parent'=> 'toolkit_elem_page',
                'id'    => 'toolkit_page_three',
                'title' => 'Regen Elementor CSS',
                'href'  => '#',
                'meta'  => array( 'class' => 'toolkit-admin-bar toolkit-regenerate-css' )
            );
            $wp_admin_bar->add_menu( $args );
        }
        if( defined('ELEMENTOR_VERSION') && version_compare( ELEMENTOR_VERSION, '3.0', '>=' ) ){
            $theme_builder_link = admin_url('admin.php?page=elementor-app&ver='.ELEMENTOR_VERSION.'#site-editor/');
        } else {
            $theme_builder_link = admin_url("edit.php?post_type=elementor_library&tabs_group=theme");
        }
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_five',
            'title' => 'Theme Builder',
            'href'  => $theme_builder_link,
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_six',
            'title' => 'Maintenance Mode',
            'href'  => admin_url('admin.php?page=elementor-tools#tab-maintenance_mode'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
	    $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_seven',
            'title' => 'Role Manager',
            'href'  => admin_url('admin.php?page=elementor-role-manager'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_nine',
            'title' => 'System Info',
            'href'  => admin_url('admin.php?page=elementor-system-info'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );   
	    $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_eight',
            'title' => 'Search & Replace',
            'href'  => admin_url('admin.php?page=elementor-tools#tab-replace_url'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
	    $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_page_four',
            'title' => 'Rollback Elementor',
            'href'  => admin_url('admin.php?page=elementor-tools#tab-versions'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );            
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_page',
            'id'    => 'toolkit_elem_links',
            'title' => 'Elementor Quicklinks',
            'href'  => '#',
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_links',
            'id'    => 'toolkit_page_thirteen',
            'title' => 'My Account',
            'href'  => 'https://my.elementor.com/',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
	$wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_links',
            'id'    => 'toolkit_page_ten',
            'title' => 'Elementor Support',
            'href'  => 'https://elementor.com/support',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_links',
            'id'    => 'toolkit_page_eleven',
            'title' => 'Changelog: Free',
            'href'  => 'https://github.com/elementor/elementor#changelog',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_elem_links',
            'id'    => 'toolkit_page_twelve',
            'title' => 'Changelog: Pro',
            'href'  => 'https://elementor.com/pro/changelog/',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_page',
            'id'    => 'toolkit_quick_links',
            'title' => 'ToolKit Quicklinks',
            'href'  => '#',
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
	    $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_quick_links',
            'id'    => 'toolkit_page_eighteen',
            'title' => 'Plugin Settings',
            'href'  => admin_url('admin.php?page=toolkit-performance-tool'),
            'meta'  => array( 'class' => 'toolkit-admin-bar' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_quick_links',
            'id'    => 'toolkit_page_seventeen',
            'title' => 'My Account',
            'href'  => 'https://toolkitforelementor.com/account',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_quick_links',
            'id'    => 'toolkit_page_fourteen',
            'title' => 'Changelogs',
            'href'  => 'https://toolkitforelementor.com/changelog',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_quick_links',
            'id'    => 'toolkit_page_fifteen',
            'title' => 'ToolKit Community',
            'href'  => 'https://www.facebook.com/groups/toolkitforelementor',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $args = array(
            'parent'=> 'toolkit_quick_links',
            'id'    => 'toolkit_page_sixteen',
            'title' => 'ToolKit Support',
            'href'  => 'https://toolkitforelementor.com/support',
            'meta'  => array( 'class' => 'toolkit-admin-bar', 'target' => '_blank' )
        );
        $wp_admin_bar->add_menu( $args );
        $preloadMeta = get_option('toolkit_preload_cache_meta', array());
        $files = (isset($preloadMeta['files'])) ? $preloadMeta['files'] : '0';
        $cache_size_info = '<h4>'.__('Cache Stats').'</h4>';
        $cache_size_info .= '<span>'.__('Cache files:').' '.$files.'</span>';
        $args = array(
            'parent'=> 'toolkit_page',
            'id'    => 'toolkit_page_nineteen',
            'title' => $cache_size_info,
            'meta'  => array( 'class' => 'toolkit-cache-stats' )
        );
        $wp_admin_bar->add_menu( $args );
        echo '<input type="hidden" id="toolkit_regenerate_nonce" value="'.wp_create_nonce('elementor_clear_cache').'">';
    }

    public function clear_plugin_cache() {
        if( isset($_GET['toolkit_clear_cache']) && $_GET['toolkit_clear_cache'] == 1 ) {
            $current_url = remove_query_arg('toolkit_clear_cache', $_SERVER['REQUEST_URI']);
            $protocol = (is_ssl()) ? 'https:' : 'http:';
            $current_url = $protocol . "//" . $_SERVER['HTTP_HOST'] . $current_url;
            toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
            wp_redirect($current_url);
            exit;
        }
        if( isset($_GET['toolkit_clear_master']) && $_GET['toolkit_clear_master'] == 1 ){
            $current_url = remove_query_arg('toolkit_clear_master', $_SERVER['REQUEST_URI']);
            $protocol = (is_ssl()) ? 'https:' : 'http:';
            $current_url = $protocol . "//" . $_SERVER['HTTP_HOST'] . $current_url;
            toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH);
            wp_redirect($current_url);
            exit;
        }

    }

    public function detect_plugin_theme_change(){
        toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MIN_PATH);
    }

    public function elementor_check(){
        if ( ! is_plugin_active('elementor/elementor.php') ) {
            add_action('admin_notices', array($this, 'elementor_check_notice'));
            deactivate_plugins('toolkit/toolkit-for-elementor.php');
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }

    public function elementor_check_notice(){
        echo '<div class="notice notice-error"><p>' . __('Please install and activate <a target="_blank" href="https://wordpress.org/plugins/elementor/"><b>Elementor Page Builder</b></a> plugin before activating.') . '</p></div>';
    }

    public function ninja_stack_settings($rules){
        $serverTweaks = get_option('toolkit_webserver_tweaks', array());
        if( isset($serverTweaks['encoding_header']) && $serverTweaks['encoding_header'] == 'on' ){
            ob_start(); ?>
<IfModule mod_headers.c>
<filesMatch ".(ico|pdf|flv|jpg|jpeg|png|gif|svg|js|css|swf)$">
        Header set Cache-Control "max-age=84600, public"
</filesMatch>
</IfModule>
            <?php
            $new_rules = ob_get_contents();
            ob_end_clean();
            $rules .= "\n" . $new_rules;
        }
        if( isset($serverTweaks['gzip_compression']) && $serverTweaks['gzip_compression'] == 'on' ){
            ob_start(); ?>
<IfModule mod_deflate.c>
	SetOutputFilter DEFLATE
    <IfModule mod_setenvif.c>
        <IfModule mod_headers.c>
            SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
            RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
        </IfModule>
    </IfModule>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE "application/atom+xml" \
                                      "application/javascript" \
                                      "application/json" \
                                      "application/ld+json" \
                                      "application/manifest+json" \
                                      "application/rdf+xml" \
                                      "application/rss+xml" \
                                      "application/schema+json" \
                                      "application/vnd.geo+json" \
                                      "application/vnd.ms-fontobject" \
                                      "application/x-font-ttf" \
                                      "application/x-font-opentype" \
                                      "application/x-font-truetype" \
                                      "application/x-javascript" \
                                      "application/x-web-app-manifest+json" \
                                      "application/xhtml+xml" \
                                      "application/xml" \
                                      "font/eot" \
                                      "font/opentype" \
                                      "font/otf" \
                                      "image/bmp" \
                                      "image/svg+xml" \
                                      "image/vnd.microsoft.icon" \
                                      "image/x-icon" \
                                      "text/cache-manifest" \
                                      "text/css" \
                                      "text/html" \
                                      "text/javascript" \
                                      "text/plain" \
                                      "text/vcard" \
                                      "text/vnd.rim.location.xloc" \
                                      "text/vtt" \
                                      "text/x-component" \
                                      "text/x-cross-domain-policy" \
                                      "text/xml"

    </IfModule>
    <IfModule mod_mime.c>
        AddEncoding gzip              svgz
    </IfModule>
</IfModule>
          <?php
            $new_rules = ob_get_contents();
            ob_end_clean();
            $rules .= "\n" . $new_rules;
        }
        if( isset($serverTweaks['keep_alive']) && $serverTweaks['keep_alive'] == 'on' ){
            ob_start(); ?>
<ifModule mod_headers.c>
Header set Connection keep-alive
</ifModule>
            <?php
            $new_rules = ob_get_contents();
            ob_end_clean();
            $rules .= "\n" . $new_rules;
        }
        if( isset($serverTweaks['ninja_etags']) && $serverTweaks['ninja_etags'] == 'on' ){
            ob_start(); ?>
<IfModule mod_headers.c>
Header unset ETag
</IfModule>
            <?php
            $new_rules = ob_get_contents();
            ob_end_clean();
            $rules .= "\n" . $new_rules;
        }
        if( isset($serverTweaks['expire_headers']) && $serverTweaks['expire_headers'] == 'on' ){
            ob_start(); ?>
<IfModule mod_expires.c>
ExpiresActive on
ExpiresDefault "access plus 1 week"
ExpiresByType text/css "access plus 1 week"
ExpiresByType application/atom+xml "access plus 1 hour"
ExpiresByType application/rss+xml "access plus 1 hour"
ExpiresByType application/json "access plus 0 seconds"
ExpiresByType application/ld+json "access plus 0 seconds"
ExpiresByType application/schema+json "access plus 0 seconds"
ExpiresByType application/xml "access plus 0 seconds"
ExpiresByType text/xml "access plus 0 seconds"
ExpiresByType image/x-icon "access plus 1 year"
ExpiresByType text/html "access plus 0 seconds"
ExpiresByType application/javascript "access plus 1 week"
ExpiresByType application/x-javascript "access plus 1 week"
ExpiresByType application/js "access plus 1 week"
ExpiresByType text/javascript "access plus 1 week"
ExpiresByType application/manifest+json "access plus 1 week"
ExpiresByType application/x-web-app-manifest+json "access plus 0 seconds"
ExpiresByType text/cache-manifest "access plus 0 seconds"
ExpiresByType text/markdown "access plus 0 seconds"
ExpiresByType audio/ogg "access plus 1 week"
ExpiresByType image/bmp "access plus 1 week"
ExpiresByType image/gif "access plus 1 week"
ExpiresByType image/jpeg "access plus 1 week"
ExpiresByType image/jpg "access plus 1 week"
ExpiresByType image/png "access plus 1 week"
ExpiresByType image/svg+xml "access plus 1 week"
ExpiresByType image/webp "access plus 1 week"
ExpiresByType video/mp4 "access plus 1 week"
ExpiresByType video/ogg "access plus 1 week"
ExpiresByType video/webm "access plus 1 week"
ExpiresByType font/collection "access plus 1 month"
ExpiresByType font/eot "access plus 1 month"
ExpiresByType font/opentype "access plus 1 month"
ExpiresByType font/otf "access plus 1 month"
ExpiresByType application/x-font-ttf "access plus 1 month"
ExpiresByType font/ttf "access plus 1 month"
ExpiresByType application/font-woff "access plus 1 month"
ExpiresByType application/x-font-woff "access plus 1 month"
ExpiresByType font/woff "access plus 1 month"
ExpiresByType application/font-woff2 "access plus 1 month"
ExpiresByType font/woff2 "access plus 1 month"
</IfModule>
            <?php
            $new_rules = ob_get_contents();
            ob_end_clean();
            $rules .= "\n" . $new_rules;
        }

        return $rules;
    }

}
