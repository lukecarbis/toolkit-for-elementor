<?php
if( ! class_exists('Theme_Disable_Public') ) {

    class Theme_Disable_Public{

        public function __construct(){
            $disable = get_option('theme_disable_themeless', 'no');
            if ($disable == 'yes') {
                add_action('template_include', array($this, 'template_include'), 9999);
				add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 9);
                add_action('style_loader_tag', array($this, 'style_loader_tag'), 99, 4);
                add_action('script_loader_tag', array($this, 'script_loader_tag'), 99, 3);
            }
            add_action('wp_head', array($this, 'wp_head'));
            add_action('wp_footer', array($this, 'wp_footer'));
            add_action('admin_footer', array($this, 'wp_dashboard_footer'));
        }
 		
		public function enqueue_styles(){
            wp_enqueue_style('theme-disable-public', TOOLKIT_FOR_ELEMENTOR_URL . 'public/css/themeless.min.css', array(), '8.0.1', 'all');
        }
		
        function template_include($template){
            if (strpos($template, 'canvas.php') === false) {
                return TOOLKIT_FOR_ELEMENTOR_PATH . 'public/partials/theme-disable-template-misc.php';
            }
            return $template;
        }

        function style_loader_tag($tag, $handle, $href, $media){
            if (strpos($href, 'wp-content/themes') !== false) {
                $wooCss = array('woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general');
                if( in_array($handle, $wooCss) && defined('WC_PLUGIN_FILE') ){
                    if( $handle == 'woocommerce-layout' ){
                        $href = plugins_url( 'assets/css/woocommerce-layout.css', WC_PLUGIN_FILE );
                        return "<link rel='stylesheet' id='$handle-css' href='$href' type='text/css' media='$media' />\n";
                    }
                    if( $handle == 'woocommerce-smallscreen' ){
                        $href = plugins_url( 'assets/css/woocommerce-smallscreen.css', WC_PLUGIN_FILE );
                        return "<link rel='stylesheet' id='$handle-css' href='$href' type='text/css' media='$media' />\n";
                    }
                    if( $handle == 'woocommerce-general' ){
                        $href = plugins_url( 'assets/css/woocommerce.css', WC_PLUGIN_FILE );
                        return "<link rel='stylesheet' id='$handle-css' href='$href' type='text/css' media='$media' />\n";
                    }
                }
                return '';
            }
            return $tag;
        }

        function script_loader_tag($tag, $handle, $src){
            if (strpos($src, 'wp-content/themes') !== false) {
                return '';
            }
            return $tag;
        }

        function wp_head(){
            $header_code = get_option('theme_disable_header_code', '');
            if( $header_code ){
                eval(' ?>'.str_replace('\"','"', str_replace("\\'",'"', $header_code)).'<?php ');
            }
        }

        function wp_footer(){
            $footer_code = get_option('theme_disable_footer_code', '');
            if( $footer_code ){
                eval(' ?>'.str_replace('\"','"', str_replace("\\'",'"', $footer_code)).'<?php ');
            }
        }

        function wp_dashboard_footer(){
            $footer_code = get_option('theme_disable_wpfooter_code', '');
            if( $footer_code ){
                eval(' ?>'.str_replace('\"','"', str_replace("\\'",'"', $footer_code)).'<?php ');
            }
        }

    }

}
