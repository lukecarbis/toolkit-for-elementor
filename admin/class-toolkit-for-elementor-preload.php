<?php
if( ! class_exists('Toolkit_For_Elementor_Preload') ){
    class Toolkit_For_Elementor_Preload {

        public $path = WP_CONTENT_DIR . '/cache/toolkit/pages/';
        
        function __construct(){

        }

        public function get_post_urls() {
            global $post;

            $offset = 0;
            $posts_per_page = 1000;
            $urls = array();

            $urls[] = home_url('/');

            do {
                $query = new WP_Query(array(
                    'post_type'         => 'any',
                    'post_status'       => 'publish',
                    'posts_per_page'    => $posts_per_page,
                    'offset'            => $offset,
                    'orderby'           => 'ID',
                    'order'             => 'ASC',
                    'cache_results'     => false,
                ));

                $posts_loaded = $query->post_count;

                while ($query->have_posts()) {
                    $query->the_post();
                    $permalink = get_permalink();
                    $urls[] = $permalink;

                    // check page separators in the post content
                    preg_match_all('/\<\!--nextpage--\>/', $post->post_content, $matches);
                    // if there any separators add urls for each page
                    if (count($matches[0])) {
                        $prefix = strpos($permalink, '?') ? '&page=' : '';
                        for ($page = 0; $page < count($matches[0]); $page++) {
                            if ('' != $prefix) {
                                $urls[] = $permalink . $prefix . ($page+2);
                            } else {
                                $urls[] = trailingslashit($permalink) . ($page+2);
                            }
                        }
                    }
                }

                $offset += $posts_loaded;
            } while ($posts_loaded > 0);

            wp_reset_postdata();

            return array_unique($urls);
        }

        public function preload_desktop($url) {
            $desktop_args = array(
                'httpversion' => '1.1',
                'user-agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36',
                'timeout'     => 10,
            );

            $desktop_args = apply_filters('toolkit_page_cache_preload_args', $desktop_args, $url);

            wp_remote_get($url, $desktop_args);
        }

        function enable_preload_cache(){
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on' && isset($cacheSetting['preload_cache']) && $cacheSetting['preload_cache'] == 'on' ){
                $posts_urls = $this->get_post_urls();
                if( $posts_urls ){
                    $total = count($posts_urls);
                    $page = 1;
                    update_option('toolkit_preload_cache_pages', ceil($total/300));
                    for( $i=0; $i<$total; $i=$i+300 ){
                        $page_urls = [];
                        for( $u=$i; $u<$i+300; $u++ ){
                            if( isset($posts_urls[$u]) ){
                                $page_urls[] = $posts_urls[$u];
                            } else {
                                break;
                            }
                        }
                        if( $page_urls ){
                            $data = ['status'=>'pending', 'urls'=>$page_urls];
                            update_option('toolkit_preload_urls_page_'.$page, $data);
                            $page++;
                        } else {
                            break;
                        }
                    }
                }
                $meta = array('time'=>time(), 'files'=>count($posts_urls));
                update_option('toolkit_preload_cache_meta', $meta);
                toolkit_remove_minify_css_js_files($this->path);
                wp_clear_scheduled_hook('toolkit_process_preload_cache');
                wp_schedule_event(time(), 'fiveminutes', 'toolkit_process_preload_cache');
                $response = array('success'=>true, 'message'=>__("ToolKit Cache Cleared Successfully."), 'msg2'=>__("Preload Cache has run successfully."));
            } else {
                $response = array('success'=>false, 'message'=>__("No posts found to preload."));
            }
            wp_send_json($response);
            exit;
        }

        function process_preload_cache(){
            $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
            if( isset($cacheSetting['cache_pages'], $cacheSetting['preload_cache']) && $cacheSetting['cache_pages'] == 'on' && $cacheSetting['preload_cache'] == 'on' ){
                $total_pages = get_option('toolkit_preload_cache_pages', 0);
                if( $total_pages > 0 ){
                    for ( $page=1; $page<=$total_pages; $page++ ){
                        $posts_urls = get_option('toolkit_preload_urls_page_'.$page, []);
                        if( isset($posts_urls['status'], $posts_urls['urls']) && $posts_urls['status'] == 'pending' && $posts_urls['urls'] ){
                            foreach ($posts_urls['urls'] as $posts_url){
                                $this->preload_desktop($posts_url);
                            }
                            $data = ['status'=>'complete', 'urls'=>$posts_urls['urls']];
                            update_option('toolkit_preload_urls_page_'.$page, $data);
                        }
                    }
                }
                $meta = get_option('toolkit_preload_cache_meta', []);
                $meta['time'] = time();
                update_option('toolkit_preload_cache_meta', $meta);
            }
            wp_clear_scheduled_hook('toolkit_process_preload_cache');
        }

        function setup_lifespan($options){
            if ($options['preload_cache'] === 'off' || $options['cache_pages'] === 'off') {
                wp_clear_scheduled_hook('toolkit_enable_preload_cache');
            } elseif( $options['preload_lifespan'] === 'cache' ) {
                $settings = get_option('toolkit_elementor_cache_settings', []);
                if( isset($settings['cache_lifespan']) && $settings['cache_lifespan'] != 'none' ){
                    wp_clear_scheduled_hook('toolkit_enable_preload_cache');
                    wp_schedule_event(time(), $settings['cache_lifespan'], 'toolkit_enable_preload_cache');
                } else {
                    wp_clear_scheduled_hook('toolkit_enable_preload_cache');
                }
            } elseif ( ! wp_next_scheduled('toolkit_enable_preload_cache') ) {
                wp_schedule_event(time(), $options['preload_lifespan'], 'toolkit_enable_preload_cache');
            } else {
                wp_clear_scheduled_hook('toolkit_enable_preload_cache');
                wp_schedule_event(time(), $options['preload_lifespan'], 'toolkit_enable_preload_cache');
            }
        }

        function cron_schedules($schedules){
            $schedules['monthly'] = array(
                'interval' => 30 * DAY_IN_SECONDS,
                'display'  => __('Monthly')
            );
            $schedules['quarterdaily'] = array(
                'interval' => 6 * HOUR_IN_SECONDS,
                'display'  => __('6 Hours')
            );
            $schedules['fiveminutes'] = array(
                'interval' => 5 * MINUTE_IN_SECONDS,
                'display'  => __('Five Minutes')
            );
            return $schedules;
        }

        function admin_footer_css(){ ?>
             <style>
                 #wpadminbar .quicklinks .menupop ul li.toolkit-cache-stats::before {
                     content: '';
                     display: block;
                     margin-left: 10px;
                     margin-right: 10px;
                     border-top: 1px solid rgba(114,119,124,0.48);
                     padding-top: 5px;
                     margin-top: 5px;
                 }
                 #wpadminbar .quicklinks .menupop ul li.toolkit-cache-stats .ab-item {
                     height: auto;
                     line-height: 1.2;
                 }
                 #wpadminbar .quicklinks .menupop ul li.toolkit-cache-stats .ab-item h4 {
                     font-weight: 500;
                     text-transform: uppercase;
                     color: #FF6BB5;
                     line-height: 1.2;
                 }
             </style>
        <?php
        }

    }
}