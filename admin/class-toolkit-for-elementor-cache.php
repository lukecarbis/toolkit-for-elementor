<?php
if( ! class_exists('Toolkit_For_Elementor_Cache') ){
    class Toolkit_For_Elementor_Cache {

        public $path = WP_CONTENT_DIR . '/cache/toolkit/pages/';
        public $url = WP_CONTENT_URL . '/cache/toolkit/pages/';
        public $exc_words = ['/login', '/cart', '/checkout', '/account', '/my-account', '?s=', '&s=', '/wp-admin', '/feed', '.xml', '.txt', '.php',];
        public $ignore_queries = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_id', 'ref', 'fbclid',];

        function __construct(){
            $blog_id = get_current_blog_id();
            $this->path = $this->path . $blog_id . '/';
            $this->url = $this->url . $blog_id . '/';
            add_action('plugins_loaded', array($this, 'migrate_config'));
        }

        function save_settings(){
            if( isset($_POST['cache_pages']) && isset($_POST['cache_purge']) && isset($_POST['cache_lifespan']) ){
                $options = array(
                    'cache_pages'   => esc_sql($_POST['cache_pages']),
                    'cache_purge'   => esc_sql($_POST['cache_purge']),
                    'cache_lifespan'=> esc_sql($_POST['cache_lifespan']),
                    'cache_loggedin'=> esc_sql($_POST['cache_loggedin']),
                    'cache_exclude' => $_POST['cache_exclude'],
                    'preload_cache' => $_POST['preload_cache'],
                    'preload_lifespan' => $_POST['preload_lifespan'],
                );
                update_option('toolkit_elementor_cache_settings', $options);
                $this->setup_lifespan($options);
                $preload = new Toolkit_For_Elementor_Preload();
                $preload->setup_lifespan($options);
                toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                $response = array('success'=>true, 'message'=>__("Settings have been saved successfully."), 'msg2'=>__("Previous cache has cleared successfully."));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function migrate_config(){
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on' ){
                if( $this->is_invalid_page() ){
                    return false;
                }
                $cache_exec_words = isset($cacheSetting['cache_exclude']) ? preg_split('/\r\n|[\r\n]/', $cacheSetting['cache_exclude']) : array();
                $cache_exec_words = (is_array($cache_exec_words)) ? $cache_exec_words : array();
                if( $this->is_excluded_page($cache_exec_words) ){
                    return false;
                }
                if ( $this->is_cached_page() && ! headers_sent() ) {
                    header('x-toolkit-elementor-cache: HIT');
                    header('x-toolkit-elementor-source: PHP');
                    $file_path = $this->path.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'index.html';
                    $cache_last_modified = filemtime($file_path);
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cache_last_modified) . ' GMT');
                }
            }
        }

        function setup_lifespan($options){
            if ($options['cache_lifespan'] === 'none' || $options['cache_pages'] === 'off') {
                wp_clear_scheduled_hook('toolkit_purge_preload_cache');
            } elseif (!wp_next_scheduled('toolkit_purge_preload_cache')) {
                wp_schedule_event(time(), $options['cache_lifespan'], 'toolkit_purge_preload_cache');
            } else {
                wp_clear_scheduled_hook('toolkit_purge_preload_cache');
                wp_schedule_event(time(), $options['cache_lifespan'], 'toolkit_purge_preload_cache');
            }
        }

        function purge_preload_cache(){
            toolkit_remove_minify_css_js_files($this->path);
        }

        function is_invalid_page(){
            $current_url = home_url($_SERVER['REQUEST_URI']);
            $keywords = ['/wp-', '/feed', '.xml', '.xsl', '.txt', '.php'];
            array_push($keywords, wp_login_url());
            foreach ($keywords as $keyword) {
                if ( $keyword && strpos($current_url, $keyword) !== false ) {
                    return true;
                }
            }
            return false;
        }

        function is_excluded_page($keywords){
            $current_url = home_url($_SERVER['REQUEST_URI']);
            $keywords = (is_array($keywords)) ? $keywords : array();
            $keywords = array_merge($keywords, $this->exc_words);
            foreach ($keywords as $keyword) {
                if ( $keyword && strpos($current_url, $keyword) !== false ) {
                    return true;
                }
            }
            return false;
        }

        public function is_valid_buffer($content){
            if ( stripos($content, '<html') === false || stripos($content, '</body>') === false || stripos($content, '<xsl:stylesheet') !== false || stripos($content, '<xsl:stylesheet') !== false ) {
                return false;
            }
            if (!preg_match('/^<!DOCTYPE.+html/i', ltrim($content))) {
                return false;
            }
            if ( $this->is_amp_markup($content) ) {
                return false;
            }
            return true;
        }

        public function is_amp_markup($content){
            if (function_exists('is_amp_endpoint')) {
                return is_amp_endpoint();
            }
            $is_amp_markup = preg_match('/<html[^>]*(?:amp|⚡)/i', $content);
            return $is_amp_markup;
        }

        public function page_should_cache(){
            if ( is_user_logged_in() || is_404() ) {
                return false;
            }
            if ( ! empty($_COOKIE) ) {
                $cookies_regex =
                    '/(wordpress_[a-f0-9]+|comment_author|wp-postpass|wordpress_no_cache|wordpress_logged_in|woocommerce_cart_hash|woocommerce_items_in_cart|woocommerce_recently_viewed|edd_items_in_cart)/';
                $cookies = implode('', array_keys($_COOKIE));
                if ( preg_match($cookies_regex, $cookies) ) {
                    return false;
                }
            }
            if ( ! empty($_GET) ) {
                $query_strings_regex = $this->get_ignore_queries_regex();
                if (sizeof(preg_grep($query_strings_regex, array_keys($_GET), PREG_GREP_INVERT)) > 0) {
                    return false;
                }
            }
            return true;
        }

        public function get_ignore_queries_regex(){
            $queries = $this->ignore_queries;
            $queries_regex = join('|', $queries);
            $queries_regex = "/^($queries_regex)$/";
            return $queries_regex;
        }

        function is_cached_page(){
            $file_path = $this->path.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'index.html';
            if( file_exists($file_path) && filesize($file_path) > 0 ){
                return true;
            }
            return false;
        }

        function get_cached_page(){
            $file_path = $this->path.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'index.html';
            if( file_exists($file_path) ){
                return file_get_contents($file_path);
            }
            return false;
        }

        function set_cached_page($content){
            $file_path = $this->path.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'index.html';
            $minifier = new Toolkit_Minifier_Public();
            $minifier->toolkit_write_minify_file($content, $file_path, true);
            if( file_exists($file_path) ){
                return true;
            }
            return false;
        }

        function additional_purge($post_id, $post, $update){
            if ( wp_is_post_revision( $post_id ) ) {
                return;
            }
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on'
            && isset($cacheSetting['cache_purge']) && $cacheSetting['cache_purge'] != 'none' ){
                $permalink = get_permalink($post_id);
                $file_path = $this->path.parse_url($permalink, PHP_URL_PATH).'index.html';
                if( $cacheSetting['cache_purge'] != 'all' && $post->post_type == $cacheSetting['cache_purge'] && file_exists($file_path) ){
                    unlink($file_path);
                } elseif( $cacheSetting['cache_purge'] == 'all' && file_exists($file_path) ){
                    unlink($file_path);
                }
            }
        }

        function purge_on_stock_update($product){
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on'
                && isset($cacheSetting['cache_purge']) && $cacheSetting['cache_purge'] != 'none' ){
                $permalink = get_permalink($product->get_id());
                $file_path = $this->path.parse_url($permalink, PHP_URL_PATH).'index.html';
                if( $cacheSetting['cache_purge'] != 'all' && 'product' == $cacheSetting['cache_purge'] && file_exists($file_path) ){
                    unlink($this->path.parse_url($permalink, PHP_URL_PATH).'index.html');
                } elseif( $cacheSetting['cache_purge'] == 'all' && file_exists($file_path) ){
                    unlink($this->path.parse_url($permalink, PHP_URL_PATH).'index.html');
                }
            }
        }

    }
}