<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://toolkitforelementor.com
 * @since      1.0.0
 * @author     ToolKit For Elementor <support@toolkitforelementor.com>
 * @package    Toolkit_For_Elementor
 * @subpackage Toolkit_For_Elementor/public
 */

class Toolkit_For_Elementor_Public {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
    public function bodytag_custom_code(){
        $bodytag_code = get_option('theme_disable_bodytag_code', '');
        if( $bodytag_code ){
            eval(' ?>'.str_replace('\"','"', str_replace("\\'",'"', $bodytag_code)).'<?php ');
        }
    }

    public function minify_css_js_fonts(){
	    if( ! is_admin() ){
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( ! is_user_logged_in() || (is_user_logged_in() && isset($cacheSetting['cache_loggedin']) && $cacheSetting['cache_loggedin'] == 'on') ){
                ob_start('toolkit_optimize_page_content');
            }
        }
    }

    public function disable_emojis(){
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    public function disable_gutenberg_css(){
        $unloadOpts = get_option('toolkit_unload_options', array());
        if( $unloadOpts && isset($unloadOpts['condition_gutenberg']) && $unloadOpts['condition_gutenberg'] == 'home' ){
            if( is_front_page() ){
                wp_dequeue_style( 'wp-block-library' );
                wp_dequeue_style( 'wp-block-library-theme' );
            }
        } else {
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );
        }
    }

    public function disable_comment_reply(){
        wp_dequeue_script( 'comment-reply' );
    }

}
