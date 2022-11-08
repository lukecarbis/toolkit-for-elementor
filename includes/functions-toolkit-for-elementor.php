<?php
if( ! function_exists('is_elementor_pro_activated') ){
    function is_elementor_pro_activated(){
        if( ! function_exists('is_plugin_active') ){
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active('elementor-pro/elementor-pro.php');
    }
}

if( ! function_exists('is_toolkit_for_elementor_activated') ){
    function is_toolkit_for_elementor_activated(){
        $toolkit_license_key = sanitize_text_field(trim(get_option( 'toolkit_license_status', '' )));
        return ($toolkit_license_key) ? true : false;
    }
}

if( !function_exists('toolkit_optimize_page_content') ){
    function toolkit_optimize_page_content( $buffer ) {
        if ( ! isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
            return $buffer;
        }
        if ( isset($_GET['toolkit_test']) || isset($_GET['toolkit_assets']) || (function_exists('wp_doing_ajax') && wp_doing_ajax()) ) {
            return $buffer;
        }
        $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
        $cache = new Toolkit_For_Elementor_Cache();
        if( $cache->is_invalid_page() || ! $cache->is_valid_buffer($buffer) ){
            return $buffer;
        }
        if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on' ){
            $cache_exec_words = isset($cacheSetting['cache_exclude']) ? preg_split('/\r\n|[\r\n]/', $cacheSetting['cache_exclude']) : array();
            $cache_exec_words = (is_array($cache_exec_words)) ? $cache_exec_words : array();
            if( $cache->is_excluded_page($cache_exec_words) ){
                return $buffer;
            }
            if( $cache->is_cached_page() ){
                return $cache->get_cached_page();
            }
        }
        $minifyTweaks = get_option('toolkit_elementor_tweaks', array());
        require_once TOOLKIT_FOR_ELEMENTOR_PATH . "public/class-toolkit-minifier-public.php";
        $minifier = new Toolkit_Minifier_Public();
        if ( ! function_exists('str_get_html') ) {
            include TOOLKIT_FOR_ELEMENTOR_PATH . "includes/class-toolkit-for-elementor-html.php";
        }
        $html = str_get_html($buffer, true, true, 'UTF-8', false);
        if( is_object($html) ){
            if( isset($minifyTweaks['google_fonts']) && $minifyTweaks['google_fonts'] == 'on' ){
                $gfonts = new Toolkit_GA_Fonts();
                $gapis_fonts = $html->find('link[href*=fonts.googleapis.com/css]');
                $gapis_fonts = ($gapis_fonts) ? $gapis_fonts : array();
                $gstatic_fonts = $html->find('link[href*=fonts.gstatic.com/]');
                $gstatic_fonts = ($gstatic_fonts) ? $gstatic_fonts : array();
                $google_fonts = array_merge($gapis_fonts, $gstatic_fonts);
                if( $google_fonts ){
                    foreach ($google_fonts as $google_font) {
                        $new_file_name = substr(hash('md5', $google_font->href), 0, 11) . '.local-font.css';
                        $new_file_path = $gfonts->fpath . $new_file_name;
                        $new_file_url = $gfonts->furl . $new_file_name;
                        if ( ! file_exists($new_file_path) ) {
                            $gfonts->create_gfonts_local_files($google_font->href, $new_file_path);
                        }
                        $google_font->href = $new_file_url;
                    }
                }
            }
            if( isset($minifyTweaks['preload_fonts']) && $minifyTweaks['preload_fonts'] ){
                $gfonts = new Toolkit_GA_Fonts();
                $preload_urls = $minifyTweaks['preload_fonts'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['preload_fonts']) : array();
                if( $preload_urls ){
                    $gfonts->toolkit_preload_fonts($html, $preload_urls);
                }
            }
            if( isset($minifyTweaks['js_delay']) && $minifyTweaks['js_delay'] == 'on' ){
                $current_link = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI'];
                $current_link = rtrim($current_link, "/");
                $exclude_page = false;
                if( isset($minifyTweaks['delayed_expages']) && $minifyTweaks['delayed_expages'] ){
                    $expages = $minifyTweaks['delayed_expages'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['delayed_expages']) : array();
                    $expages = is_array($expages) ? $expages : array();
                    if( $expages ){
                        foreach ($expages as $expage){
                            if( false !== strpos( $current_link, $expage ) ){
                                $exclude_page = true;
                                break;
                            }
                        }
                    }
                }
                if( ! $exclude_page ){
                    if( isset($minifyTweaks['delayed_hkeywords']) && $minifyTweaks['delayed_hkeywords'] ){
                        $keywords = $minifyTweaks['delayed_hkeywords'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['delayed_hkeywords']) : array();
                        $keywords = is_array($keywords) ? $keywords : array();
                        if( $keywords && $current_link == home_url() ){
                            $minifier->toolkit_delay_js_files($html, $keywords);
                        }
                    }
                    if( isset($minifyTweaks['delayed_keywords']) && $minifyTweaks['delayed_keywords'] ){
                        $keywords = $minifyTweaks['delayed_keywords'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['delayed_keywords']) : array();
                        $keywords = is_array($keywords) ? $keywords : array();
                        if( $keywords ){
                            $minifier->toolkit_delay_js_files($html, $keywords);
                        }
                    }
                }
            }
            $lazyOpts = get_option('toolkit_elementor_settings', array());
            $image_abvfold = isset($lazyOpts['image_abvfold']) ? $lazyOpts['image_abvfold'] : 2;
            $lazyLoad = new Toolkit_For_Elementor_LazyLoad();
            if( isset($lazyOpts['image']) && $lazyOpts['image'] == 'on' ){
                if( $image_abvfold > 0 ){
                    $html = $lazyLoad->exclude_top_images($html, $image_abvfold);
                }
                $load_type = isset($lazyOpts['img_loadtype']) ? $lazyOpts['img_loadtype'] : 'native';
                $exclude_keywords = isset($lazyOpts['exclude_loading']) && $lazyOpts['exclude_loading'] ? preg_split('/\r\n|[\r\n]/', $lazyOpts['exclude_loading']) : array();
                $exclude_keywords = ($exclude_keywords) ? $exclude_keywords : array();
                $html = $lazyLoad->image_lazy_load($html, $load_type, $exclude_keywords);
                if( isset($lazyOpts['preload_images']) && $lazyOpts['preload_images'] == 'on' ){
                    $html = $lazyLoad->preload_critical_images($html);
                }
            }
            if( isset($lazyOpts['video']) && $lazyOpts['video'] == 'on' ){
                $html = $lazyLoad->video_lazy_load($html);
            }
            if( version_compare(get_bloginfo('version'),'5.7', '<') ){
                if( isset($lazyOpts['iframe']) && $lazyOpts['iframe'] == 'on' ){
                    $html = $lazyLoad->iframe_lazy_load($html);
                }
            }
            if( isset($lazyOpts['yt_placeholder']) && $lazyOpts['yt_placeholder'] == 'on' ){
                $self_host = (isset($lazyOpts['yt_self_host']) && $lazyOpts['yt_self_host'] == 'on') ? true : false;
                $html = $lazyLoad->yt_iframe_placeholder($html, $self_host);
            }
            if( isset($lazyOpts['image_attrs']) && $lazyOpts['image_attrs'] == 'on' ){
                $html = $lazyLoad->add_images_width_height($html);
            }
            if( isset($minifyTweaks['css_minify']) && $minifyTweaks['css_minify'] == 'on' ){
                $cdn_url = '';
                if( isset($minifyTweaks['cdn_enable']) && $minifyTweaks['cdn_enable'] == 'yes'
                    && isset($minifyTweaks['cdn_url']) && is_array($minifyTweaks['cdn_url']) && $minifyTweaks['cdn_url']
                    && isset($minifyTweaks['cdn_files']) && is_array($minifyTweaks['cdn_files']) && $minifyTweaks['cdn_files'] ){
                    $cdn_urls = $minifyTweaks['cdn_url'];
                    $cdn_files = $minifyTweaks['cdn_files'];
                    $all_key = array_search("all", $cdn_files);
                    $font_key = array_search("font", $cdn_files);
                    $all_url = ($all_key !== FALSE) ? $cdn_urls[$all_key] : '';
                    $font_url = ($font_key !== FALSE) ? $cdn_urls[$font_key] : '';
                    if( $font_url ){
                        $cdn_url = $font_url;
                    } elseif( $all_url ){
                        $cdn_url = $all_url;
                    }
                    if( false === strpos($cdn_url, 'https://') && false === strpos($cdn_url, 'http://') ){
                        $cdn_url = is_ssl() ? 'https://'.$cdn_url : 'http://'.$cdn_url;
                    }
                    if( ! filter_var($cdn_url, FILTER_VALIDATE_URL) !== FALSE ){
                        $cdn_url = '';
                    }
                }
                $combine_css = ( isset($minifyTweaks['css_combine']) && $minifyTweaks['css_combine'] == 'on' ) ? true : false;
                $exc_elementor = ( isset($minifyTweaks['css_excelem']) && $minifyTweaks['css_excelem'] == 'on' ) ? true : false;
                $excluded_urls = ( isset($minifyTweaks['exclude_css_urls']) && $minifyTweaks['exclude_css_urls'] ) ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['exclude_css_urls']) : array();
                $excluded_urls = (is_array($excluded_urls)) ? $excluded_urls : array();
                $minifier->minify_css_files($html, $excluded_urls, $combine_css, $exc_elementor, $cdn_url);
            }
            if( isset($minifyTweaks['lazy_render']) && $minifyTweaks['lazy_render'] ){
                $lazy_render_elems = preg_split('/\r\n|[\r\n]/', $minifyTweaks['lazy_render']);
                $lazy_render_elems = (is_array($lazy_render_elems)) ? $lazy_render_elems : array();
                if( $lazy_render_elems ){
                    $minifier->lazy_render_elements($html, $lazy_render_elems);
                }
            }
            if( isset($minifyTweaks['js_minify']) && $minifyTweaks['js_minify'] == 'on' ){
                $combine_js = ( isset($minifyTweaks['js_combine']) && $minifyTweaks['js_combine'] == 'on' ) ? true : false;
                $exc_elementor = ( isset($minifyTweaks['js_excelem']) && $minifyTweaks['js_excelem'] == 'on' ) ? true : false;
                $excluded_urls = ( isset($minifyTweaks['exclude_js_urls']) && $minifyTweaks['exclude_js_urls'] ) ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['exclude_js_urls']) : array();
                $excluded_urls = (is_array($excluded_urls)) ? $excluded_urls : array();
                $minifier->minify_js_files($html, $excluded_urls, $combine_js, $exc_elementor, $cdn_url);
            }
            if( isset($minifyTweaks['js_defer']) && $minifyTweaks['js_defer'] == 'on' ){
                $current_link = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI'];
                $current_link = rtrim($current_link, "/");
                if( ! isset($minifyTweaks['defer_homeonly']) || $minifyTweaks['defer_homeonly'] == 'off' || ($minifyTweaks['defer_homeonly'] == 'on' && $current_link == home_url()) ){
                    $exclude_jq = false;
                    $deferred_keywords = $minifyTweaks['deferred_keywords'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['deferred_keywords']) : array();
                    $deferred_keywords = is_array($deferred_keywords) ? $deferred_keywords : array();
                    $minifier->toolkit_defer_files($html, $exclude_jq, $deferred_keywords);
                    if( isset($minifyTweaks['jsdefer_inline']) && $minifyTweaks['jsdefer_inline'] == 'on' ){
                        $minifier->toolkit_defer_inline($html, $deferred_keywords);
                    }
                }
            }
            if( isset($minifyTweaks['cdn_enable']) && $minifyTweaks['cdn_enable'] == 'yes'
                && isset($minifyTweaks['cdn_url']) && is_array($minifyTweaks['cdn_url']) && $minifyTweaks['cdn_url']
                && isset($minifyTweaks['cdn_files']) && is_array($minifyTweaks['cdn_files']) && $minifyTweaks['cdn_files'] ){
                $excluded_keywords = $minifyTweaks['exclude_cdn_urls'] ? preg_split('/\r\n|[\r\n]/', $minifyTweaks['exclude_cdn_urls']) : array();
                $excluded_keywords = is_array($excluded_keywords) ? $excluded_keywords : array();
                $cdn_urls = $minifyTweaks['cdn_url'];
                $cdn_files = $minifyTweaks['cdn_files'];
                $all_key = array_search("all", $cdn_files);
                $js_key = array_search("js", $cdn_files);
                $css_key = array_search("css", $cdn_files);
                $jscss_key = array_search("jscss", $cdn_files);
                $font_key = array_search("font", $cdn_files);
                $img_key = array_search("img", $cdn_files);
                $all_url = ($all_key !== FALSE) ? $cdn_urls[$all_key] : '';
                $js_url = ($js_key !== FALSE) ? $cdn_urls[$js_key] : '';
                $css_url = ($css_key !== FALSE) ? $cdn_urls[$css_key] : '';
                $jscss_url = ($jscss_key !== FALSE) ? $cdn_urls[$jscss_key] : '';
                $font_url = ($font_key !== FALSE) ? $cdn_urls[$font_key] : '';
                $img_url = ($img_key !== FALSE) ? $cdn_urls[$img_key] : '';
                $cdn_url = '';
                if( $js_url ){
                    $cdn_url = $js_url;
                } elseif( $jscss_url ){
                    $cdn_url = $jscss_url;
                } elseif( $all_url ){
                    $cdn_url = $all_url;
                }
                if( false === strpos($cdn_url, 'https://') && false === strpos($cdn_url, 'http://') ){
                    $cdn_url = is_ssl() ? 'https://'.$cdn_url : 'http://'.$cdn_url;
                }
                if( filter_var($cdn_url, FILTER_VALIDATE_URL) !== FALSE ){
                    $minifier->toolkit_cdn_files($html, 'js', $cdn_url, $excluded_keywords);
                }
                $cdn_url = '';
                if( $css_url ){
                    $cdn_url = $css_url;
                } elseif( $jscss_url ){
                    $cdn_url = $jscss_url;
                } elseif( $all_url ){
                    $cdn_url = $all_url;
                }
                if( false === strpos($cdn_url, 'https://') && false === strpos($cdn_url, 'http://') ){
                    $cdn_url = is_ssl() ? 'https://'.$cdn_url : 'http://'.$cdn_url;
                }
                if( filter_var($cdn_url, FILTER_VALIDATE_URL) !== FALSE ){
                    $minifier->toolkit_cdn_files($html, 'css', $cdn_url, $excluded_keywords);
                }
                $cdn_url = '';
                if( $font_url ){
                    $cdn_url = $font_url;
                } elseif( $all_url ){
                    $cdn_url = $all_url;
                }
                if( false === strpos($cdn_url, 'https://') && false === strpos($cdn_url, 'http://') ){
                    $cdn_url = is_ssl() ? 'https://'.$cdn_url : 'http://'.$cdn_url;
                }
                if( filter_var($cdn_url, FILTER_VALIDATE_URL) !== FALSE ){
                    $minifier->toolkit_cdn_files($html, 'font', $cdn_url, $excluded_keywords);
                }
                $cdn_url = '';
                if( $img_url ){
                    $cdn_url = $img_url;
                } elseif( $all_url ){
                    $cdn_url = $all_url;
                }
                if( false === strpos($cdn_url, 'https://') && false === strpos($cdn_url, 'http://') ){
                    $cdn_url = is_ssl() ? 'https://'.$cdn_url : 'http://'.$cdn_url;
                }
                if( filter_var($cdn_url, FILTER_VALIDATE_URL) !== FALSE ){
                    $minifier->toolkit_cdn_files($html, 'img', $cdn_url, $excluded_keywords);
                }
            }
            $buffer = $html;
        }
        if( isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on' && $cache->page_should_cache() ){
            $cache->set_cached_page($buffer);
        }
        return $buffer;
    }
}

if( !function_exists('toolkit_remove_minify_css_js_files') ){
    function toolkit_remove_minify_css_js_files( $path ) {
        if( $path ){
            if( $path == TOOLKIT_FOR_ELEMENTOR_MASTER_PATH ){
                update_option('toolkit_preload_cache_meta', []);
            }
            $files = glob($path.'/*');
        } else {
            $files = false;
        }
        if( $files ){
            foreach($files as $file){
                delete_folder_and_content($file);
            }
        }
    }
}

if( !function_exists('delete_folder_and_content') ){
    function delete_folder_and_content($path){
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                delete_folder_and_content(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } elseif (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
}

if( !function_exists('toolkit_enqueue_template_css') ){
    function toolkit_enqueue_template_css() {
        global $post;
        if( is_object($post) ){
            $temp_args = array(
                'post_type'     => 'elementor_library',
                'post_status'   => 'publish',
                'posts_per_page'=> -1,
                'orderby'       => 'title',
                'order'         => 'ASC',
                'fields'        => 'ids',
                'meta_query'    => array(
                    array(
                        'key'   => '_elementor_template_type',
                        'value' => $post->post_type,
                    )
                )
            );
            $templates = get_posts($temp_args);
            if( $templates ){
                $template_id = $templates[0];
                if( file_exists(WP_CONTENT_DIR . '/uploads/elementor/css/post-' . $template_id . '.css') ){
                    $href = WP_CONTENT_URL . '/uploads/elementor/css/post-' . $template_id . '.css';
                    wp_enqueue_style( "elementor-post-" . $template_id, $href, array(), false, 'all');
                }
            }
        }
    }
}

if( ! function_exists('toolkit_encrypt_string') ){
    function toolkit_encrypt_string($string){
        if( trim($string) ){
            $array = str_split($string);
            if( count($array) > 4 ){
                $return = "";
                for( $i = 0; $i < count($array); $i++ ){
                    if( $i < (count($array) - 4) ){
                        $return .= "*";
                    } else {
                        $return .= $array[$i];
                    }
                }
                return $return;
            }
        }
        return $string;
    }
}

if( ! function_exists('toolkit_admin_display_notice') ){
    function toolkit_admin_display_notice(){
        $current_url = remove_query_arg('import', $_SERVER['REQUEST_URI']);
        $current_url = remove_query_arg('hterror', $current_url);
        $protocol = (is_ssl()) ? 'https:' : 'http:';
        $redirect_url = $protocol . "//" . $_SERVER['HTTP_HOST'] . $current_url;
        if( isset($_GET['import']) && $_GET['import'] == 'file' ){
            echo '<div class="notice notice-success is-dismissible toolkit-notice">';
            echo '<p>' . __('ToolKit settings have imported successfully.') . '</p>';
            echo '<a class="notice-dismiss" href="'.$redirect_url.'"><span class="screen-reader-text">Dismiss this notice.</span></a>';
            echo '</div>';
        }
        if( isset($_GET['import']) && $_GET['import'] == 'fail' ){
            echo '<div class="notice notice-error is-dismissible toolkit-notice">';
            echo '<p>' . __('<b>Error:</b> Failed to import settings, invalid file.') . '</p>';
            echo '<a class="notice-dismiss" href="'.$redirect_url.'"><span class="screen-reader-text">Dismiss this notice.</span></a>';
            echo '</div>';
        }
        if( isset($_GET['hterror']) && $_GET['hterror'] == 'none' ){
            echo '<div class="notice notice-error is-dismissible toolkit-notice">';
            echo '<p>' . __("<b>Error:</b> We couldn't detect an htaccess file in your root directory.") . '</p>';
            echo '<a class="notice-dismiss" href="'.$redirect_url.'"><span class="screen-reader-text">Dismiss this notice.</span></a>';
            echo '</div>';
        }
        if( isset($_SESSION['toolkit_warning_notice']) ){
            echo '<div class="notice notice-warning">';
            echo '<p>' . $_SESSION['toolkit_warning_notice'] . '</p>';
            echo '<a class="notice-dismiss" href="'.$redirect_url.'"><span class="screen-reader-text">Dismiss this notice.</span></a>';
            echo '</div>';
            unset($_SESSION['toolkit_warning_notice']);
        }
        if( isset($_SESSION['toolkit_info_notice']) ){
            echo '<div class="notice notice-info">';
            echo '<p>' . $_SESSION['toolkit_info_notice'] . '</p>';
            echo '<a class="notice-dismiss" href="'.$redirect_url.'"><span class="screen-reader-text">Dismiss this notice.</span></a>';
            echo '</div>';
            unset($_SESSION['toolkit_info_notice']);
        }
    }
}
