<?php
if( ! class_exists('Toolkit_For_Elementor_Ninja') ){
    class Toolkit_For_Elementor_Ninja {

        public $cron_hook = 'toolkit_purge_db_optimization';
        public $exc_post_types = array(
            'attachment', //Media
            'elementor_library', //Elementor Template
            'ct_template', 'oxy_user_library', // Oxygen Page Builder
            'tbuilder_layout', 'tbuilder_layout_part', // Themify Page Builder (Layout & Layout Part)
            'popup', 'popup_theme', // "Popup Maker" plugin
            'popupbuilder', // "Popup Builder" plugin
            'datafeedr-productset' // "Datafeedr Product Sets" plugin
        );
        public $skipStyles = array(
			'admin-bar', // The top admin bar
			'yoast-seo-adminbar', // Yoast "WordPress SEO" plugin
			'autoptimize-toolbar', 'query-monitor', 'wp-fastest-cache-toolbar', // WP Fastest Cache plugin toolbar CSS
			'litespeed-cache', // LiteSpeed toolbar
			'siteground-optimizer-combined-styles-header'// Combine CSS in SG Optimiser (irrelevant as it made from the combined handles)
		);
        public $skipScripts = array(
			'admin-bar', // The top admin bar
			'autoptimize-toolbar',
			'query-monitor',
			'wpfc-toolbar' // WP Fastest Cache plugin toolbar JS
		);

        public $skipWords = array('test-word');

        function __construct(){

        }

        function init_filters(){
            add_filter('show_admin_bar', '__return_false');
            add_action( 'plugins_loaded', static function() { remove_action( 'plugins_loaded', 'rocket_init' ); }, 1 );
            add_action( 'plugins_loaded', static function() { remove_action( 'plugins_loaded', 'rocket_init' ); }, 99 );
            add_filter( 'style_loader_tag', array($this, 'style_loader_tag'), PHP_INT_MAX, 2 );
            add_filter( 'script_loader_tag', array($this, 'script_loader_tag'), PHP_INT_MAX, 2 );
        }

        function style_loader_tag($styleTag, $tagHandle){
            return str_replace( "<link ", "<link data-toolkit-handler='" . $tagHandle . "' ", $styleTag );
        }

        function script_loader_tag($scriptTag, $tagHandle){
            return str_replace( "<script ", "<script data-toolkit-handler='" . $tagHandle . "' ", $scriptTag );
        }

        function wp_core_asset($url, $handler){
            global $wp_version;
            $isJQueryHandle = in_array($handler, array('jquery', 'jquery-core', 'jquery-migrate'));
            $startsWithWpIncludes = strpos($url,'/wp-includes/') !== false;
            $wpCoreOnJetpackCdn = strpos($url, '.wp.com/c/'.$wp_version.'/wp-includes/') !== false;

            if ( $isJQueryHandle || $startsWithWpIncludes || $wpCoreOnJetpackCdn ) {
                return true;
            }
            return false;
        }

        function get_assets(){
            if( isset($_POST['post_id']) && $_POST['post_id'] > 0 ){
                $post_id = $_POST['post_id'];
                $post = get_post($post_id);
                if( $post ){
                    $permalink = get_permalink($post_id);
                    $asset_link = add_query_arg('toolkit_assets', '1', $permalink);
                    $args = array(
                        'user-agent'    => 'Google',
                        'timeout'		=> 60,
                        'sslverify'		=> false,
                        'headers'       => array( 'referer'=>home_url() )
                    );
                    $page = wp_remote_get($asset_link, $args);
                    if( ! is_wp_error($page) ){
                        $resp_code = wp_remote_retrieve_response_code($page);
                        if( $resp_code === 200 ){
                            $body = wp_remote_retrieve_body($page);
                            defined('MAX_FILE_SIZE') || define('MAX_FILE_SIZE', 700000);
                            if ( ! function_exists('str_get_html') ) {
                                require_once TOOLKIT_FOR_ELEMENTOR_PATH . "includes/class-toolkit-for-elementor-html.php";
                            }
                            $html = str_get_html($body, true, true, 'UTF-8', false);
                            if( is_object($html) ){
                                $plugins = get_plugins();
                                $themes = wp_get_themes();
                                $data = '<div class="toolkit-assets-list">';
                                $data_attr = 'data-toolkit-handler';
                                $styles = $html->find('link[href][data-toolkit-handler]');
                                $stl_array = array('wp_core'=>array(), 'plugins'=>array(), 'themes'=>array(), 'external'=>array());
                                if( $styles ){
                                    foreach ($styles as $style){
                                        $skipThis = false;
                                        foreach ($this->skipWords as $keyword){
                                            if ( $keyword && strpos($style->href, $keyword) !== false ) {
                                                $skipThis = true;
                                                break;
                                            }
                                        }
                                        if( ! $skipThis && ! in_array($style->$data_attr, $this->skipStyles) ){
                                            if( $this->wp_core_asset($style->href, $style->$data_attr) ){
                                                $stl_array['wp_core'][$style->$data_attr] = $style->href;
                                            } elseif( strpos($style->href, '/wp-content/plugins/') !== false ){
                                                $hrefParts = explode('/wp-content/plugins/', $style->href);
                                                $pluginDir = explode('/', $hrefParts[1]);
                                                $stl_array['plugins'][$pluginDir[0]][$style->$data_attr] = $style->href;
                                            } elseif( strpos($style->href, '/wp-content/themes/') !== false ){
                                                $hrefParts = explode('/wp-content/themes/', $style->href);
                                                $themeDir = explode('/', $hrefParts[1]);
                                                $stl_array['themes'][$themeDir[0]][$style->$data_attr] = $style->href;
                                            } elseif ( strpos( $style->href, '/wp-content/cache/toolkit/' ) !== false ){
                                                $stl_array['plugins']['toolkit'][$style->$data_attr] = $style->href;
                                            } elseif ( strpos( $style->href, '/wp-content/uploads/oxygen/css/' ) !== false ){
                                                $stl_array['plugins']['oxygen'][$style->$data_attr] = $style->href;
                                            } elseif ( strpos( $style->href, '/wp-content/uploads/elementor/css/' ) !== false ){
                                                $stl_array['plugins']['elementor'][$style->$data_attr] = $style->href;
                                            } elseif ( strpos( $style->href, '/wp-content/cache/asset-cleanup/' ) !== false ){
                                                $stl_array['plugins']['wp-asset-clean-up'][$style->$data_attr] = $style->href;
                                            } else {
                                                $stl_array['external'][$style->$data_attr] = $style->href;
                                            }
                                        }
                                    }
                                }
                                $scripts = $html->find('script[src][data-toolkit-handler]');
                                $scr_array = array('wp_core'=>array(), 'plugins'=>array(), 'themes'=>array(), 'external'=>array());
                                if( $scripts ){
                                    foreach ($scripts as $script){
                                        $skipThis = false;
                                        foreach ($this->skipWords as $keyword){
                                            if ( $keyword && strpos($script->src, $keyword) !== false ) {
                                                $skipThis = true;
                                                break;
                                            }
                                        }
                                        if( ! $skipThis && ! in_array($script->$data_attr, $this->skipScripts) ){
                                            if( $this->wp_core_asset($script->src, $script->$data_attr) ){
                                                $scr_array['wp_core'][$script->$data_attr] = $script->src;
                                            } elseif( strpos($script->src, '/wp-content/plugins/') !== false ){
                                                $hrefParts = explode('/wp-content/plugins/', $script->src);
                                                $pluginDir = explode('/', $hrefParts[1]);
                                                $scr_array['plugins'][$pluginDir[0]][$script->$data_attr] = $script->src;
                                            } elseif( strpos($script->src, '/wp-content/themes/') !== false ){
                                                $hrefParts = explode('/wp-content/themes/', $script->src);
                                                $themeDir = explode('/', $hrefParts[1]);
                                                $scr_array['themes'][$themeDir[0]][$script->$data_attr] = $script->src;
                                            } elseif ( strpos( $script->src, '/wp-content/cache/toolkit/' ) !== false ){
                                                $scr_array['plugins']['toolkit'][$script->$data_attr] = $script->src;
                                            } elseif ( strpos( $script->src, '/wp-content/uploads/oxygen/css/' ) !== false ){
                                                $scr_array['plugins']['oxygen'][$script->$data_attr] = $script->src;
                                            } elseif ( strpos( $script->src, '/wp-content/uploads/elementor/css/' ) !== false ){
                                                $scr_array['plugins']['elementor'][$script->$data_attr] = $script->src;
                                            } elseif ( strpos( $script->src, '/wp-content/cache/asset-cleanup/' ) !== false ){
                                                $scr_array['plugins']['wp-asset-clean-up'][$script->$data_attr] = $script->src;
                                            } else {
                                                $scr_array['external'][$script->$data_attr] = $script->src;
                                            }
                                        }
                                    }
                                }
                                if( $styles || $scripts ){
                                    $pStyles = get_post_meta($post_id, 'toolkit_excluded_styles', true);
                                    $pStyles = ($pStyles && is_array($pStyles)) ? $pStyles : array();
                                    $pScripts = get_post_meta($post_id, 'toolkit_excluded_scripts', true);
                                    $pScripts = ($pScripts && is_array($pScripts)) ? $pScripts : array();
                                    $gStyles = get_option('toolkit_excluded_styles_list', array());
                                    $gStyles = ($gStyles && is_array($gStyles)) ? $gStyles : array();
                                    $gScripts = get_option('toolkit_excluded_scripts_list', array());
                                    $gScripts = ($gScripts && is_array($gScripts)) ? $gScripts : array();
                                    $css_image = '<img class="ninja-icon" height="24" width="24" src="'.TOOLKIT_FOR_ELEMENTOR_URL.'admin/images/css3-alt-brands.svg">';
                                    $js_image = '<img class="ninja-icon" height="24" width="24" src="'.TOOLKIT_FOR_ELEMENTOR_URL.'admin/images/js-square-brands.svg">';
                                    $font_img = '<img class="ninja-icon" height="24" width="24" src="'.TOOLKIT_FOR_ELEMENTOR_URL.'admin/images/fonts.svg">';
                                    $uplDir = wp_get_upload_dir();
                                    $baseUrl = str_replace('/uploads', '', $uplDir['baseurl']);
                                    $baseDir = str_replace('/uploads', '', $uplDir['basedir']);
                                    if( $plugins ){
                                        $data .= '<h3>'.__('From Plugins').'</h3>';
                                        foreach ($plugins as $key => $plugin){
                                            $parts = explode('/', $key);
                                            $slug = $parts[0];
                                            if( isset($stl_array['plugins'][$slug]) || isset($scr_array['plugins'][$slug]) ){
                                                $data .= '<table class="widefat assets-table"><thead>';
                                                if( isset($stl_array['plugins'][$slug]) || isset($scr_array['plugins'][$slug]) ){
                                                    $data .= '<tr><th colspan="2"><b>'.$plugin['Name'].'</b> v'.$plugin['Version'].'</th>';
                                                    $data .= '<th colspan="2"></th></tr>';
                                                } else {
                                                    $data .= '<tr><th colspan="4"><b>'.$plugin['Name'].'</b> v'.$plugin['Version'].'</th></tr>';
                                                }
                                                $data .= '<tr><th width="25"></th><th><b>'.__("Handler").'</b></th><th><b>'.__("Source").'</b></th><th width="80"><b>'.__("Dequeue").'</b></th></tr></thead>';
                                                $data .= '<tbody>';
                                                if( isset($stl_array['plugins'][$slug]) ){
                                                    foreach ($stl_array['plugins'][$slug] as $handler => $clean_url){
                                                        $url = remove_query_arg('ver',$clean_url);
                                                        $file_size = '';
                                                        if( strpos($url, $baseUrl) !== false ){
                                                            $file_path = str_replace($baseUrl, $baseDir, $url);
                                                            if( file_exists($file_path) ){
                                                                $file_size = $this->filesize_formatted($file_path);
                                                            }
                                                        }
                                                        $data .= '<tr><td>'.$css_image.'</td><td>'.$handler.'<br/><span class="vmiddle">Size: '.$file_size.'</span></td><td class="max400">'.$url.'</td>';
                                                        $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                        $checked = (in_array($handler, $pStyles)) ? 'checked' : '';
                                                        $data .= '<input id="tkcss-'.$handler.'" name="tkcss_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                        $data .= '<label for="tkcss-'.$handler.'"></label></div></div>';
                                                        $data .= '</div></td></tr>';
                                                        $data .= $this->get_global_row('tkgcss_exc_assets', 'css', $handler, $gStyles);
                                                    }
                                                }
                                                if( isset($scr_array['plugins'][$slug]) ){
                                                    foreach ($scr_array['plugins'][$slug] as $handler => $clean_url){
                                                        $url = remove_query_arg('ver',$clean_url);
                                                        $file_size = '';
                                                        if( strpos($url, $baseUrl) !== false ){
                                                            $file_path = str_replace($baseUrl, $baseDir, $url);
                                                            if( file_exists($file_path) ){
                                                                $file_size = $this->filesize_formatted($file_path);
                                                            }
                                                        }
                                                        $data .= '<tr><td>'.$js_image.'</td><td>'.$handler.'<br/><span class="vmiddle">Size: '.$file_size.'</span></td><td class="max400">'.$url.'</td>';
                                                        $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                        $checked = (in_array($handler, $pScripts)) ? 'checked' : '';
                                                        $data .= '<input id="tkjs-'.$handler.'" name="tkjs_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                        $data .= '<label for="tkjs-'.$handler.'"></label></div></div>';
                                                        $data .= '</div></td></tr>';
                                                        $data .= $this->get_global_row('tkgjs_exc_assets', 'js', $handler, $gScripts);
                                                    }
                                                }
                                                $data .= '</tbody></table>';
                                            }
                                        }
                                    }
                                    if( $themes ){
                                        $data .= '<h3>'.__('From Themes').'</h3>';
                                        foreach ($themes as $slug => $theme){
                                            if( isset($stl_array['themes'][$slug]) || isset($scr_array['themes'][$slug]) ){
                                                $data .= '<table class="widefat assets-table"><thead>';
                                                if( isset($stl_array['themes'][$slug]) || isset($scr_array['themes'][$slug]) ){
                                                    $data .= '<tr><th colspan="2"><b>'.$theme->get('Name').'</b> v'.$theme->get('Version').'</th>';
                                                    $data .= '<th colspan="2"></th></tr>';
                                                } else {
                                                    $data .= '<tr><th colspan="4"><b>'.$theme->get('Name').'</b> v'.$theme->get('Version').'</th></tr>';
                                                }
                                                $data .= '<tr><th width="25"></th><th><b>'.__("Handler").'</b></th><th><b>'.__("Source").'</b></th><th width="80"><b>'.__("Dequeue").'</b></th></tr></thead>';
                                                $data .= '<tbody>';
                                                if( isset($stl_array['themes'][$slug]) ){
                                                    foreach ($stl_array['themes'][$slug] as $handler => $clean_url){
                                                        $url = remove_query_arg('ver',$clean_url);
                                                        $file_size = '';
                                                        if( strpos($url, $baseUrl) !== false ){
                                                            $file_path = str_replace($baseUrl, $baseDir, $url);
                                                            if( file_exists($file_path) ){
                                                                $file_size = $this->filesize_formatted($file_path);
                                                            }
                                                        }
                                                        $data .= '<tr><td>'.$css_image.'</td><td>'.$handler.'<br/><span class="vmiddle">Size: '.$file_size.'</span></td><td class="max400">'.$url.'</td>';
                                                        $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                        $checked = (in_array($handler, $pStyles)) ? 'checked' : '';
                                                        $data .= '<input id="tkcss-'.$handler.'" name="tkcss_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                        $data .= '<label for="tkcss-'.$handler.'"></label></div></div>';
                                                        $data .= '</div></td></tr>';
                                                        $data .= $this->get_global_row('tkgcss_exc_assets', 'css', $handler, $gStyles);
                                                    }
                                                }
                                                if( isset($scr_array['themes'][$slug]) ){
                                                    foreach ($scr_array['themes'][$slug] as $handler => $clean_url){
                                                        $url = remove_query_arg('ver',$clean_url);
                                                        $file_size = '';
                                                        if( strpos($url, $baseUrl) !== false ){
                                                            $file_path = str_replace($baseUrl, $baseDir, $url);
                                                            if( file_exists($file_path) ){
                                                                $file_size = $this->filesize_formatted($file_path);
                                                            }
                                                        }
                                                        $data .= '<tr><td>'.$js_image.'</td><td>'.$handler.'<br/><span class="vmiddle">Size: '.$file_size.'</span></td><td class="max400">'.$url.'</td>';
                                                        $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                        $checked = (in_array($handler, $pScripts)) ? 'checked' : '';
                                                        $data .= '<input id="tkjs-'.$handler.'" name="tkjs_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                        $data .= '<label for="tkjs-'.$handler.'"></label></div></div>';
                                                        $data .= '</div></td></tr>';
                                                        $data .= $this->get_global_row('tkgjs_exc_assets', 'js', $handler, $gScripts);
                                                    }
                                                }
                                                $data .= '</tbody></table>';
                                            }
                                        }
                                    }
                                    if( $stl_array['external'] || $scr_array['external'] ){
                                        $data .= '<h3>'.__('From 3rd Party').'</h3>';
                                        $data .= '<table class="widefat assets-table"><thead>';
                                        if( isset($stl_array['external']) || isset($scr_array['external']) ){
                                            $data .= '<tr><th colspan="2"><b>'.__("External 3rd Party").'</b></th>';
                                            $data .= '<th colspan="2"></th></tr>';
                                        } else {
                                            $data .= '<tr><th colspan="4"><b>'.__("External 3rd Party").'</b></th></tr>';
                                        }
                                        $data .= '<tr><th width="25"></th><th><b>'.__("Handler").'</b></th><th><b>'.__("Source").'</b></th><th width="80"><b>'.__("Dequeue").'</b></th></tr></thead>';
                                        $data .= '<tbody>';
                                        if( $stl_array['external'] ){
                                            foreach ($stl_array['external'] as $handler => $clean_url){
                                                $url = remove_query_arg('ver',$clean_url);
                                                $url = explode('#038;', $url)[0];
                                                $icon = ( false !== strpos($url, 'fonts.googleapis.com/css') ) ? $font_img : $css_image;
                                                $data .= '<tr><td>'.$icon.'</td><td>'.$handler.'<br/></td><td class="max400">'.$url.'</td>';
                                                $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                $checked = (in_array($handler, $pStyles)) ? 'checked' : '';
                                                $data .= '<input id="tkcss-'.$handler.'" name="tkcss_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                $data .= '<label for="tkcss-'.$handler.'"></label></div></div>';
                                                $data .= '</div></td></tr>';
                                                $data .= $this->get_global_row('tkgcss_exc_assets', 'css', $handler, $gStyles);
                                            }
                                        }
                                        if( $scr_array['external'] ){
                                            foreach ($scr_array['external'] as $handler => $clean_url){
                                                $url = remove_query_arg('ver',$clean_url);
                                                $url = explode('#038;', $url)[0];
                                                $icon = ( false !== strpos($url, 'fonts.googleapis.com/css') ) ? $font_img : $js_image;
                                                $data .= '<tr><td>'.$icon.'</td><td>'.$handler.'<br/></td><td class="max400">'.$url.'</td>';
                                                $data .= '<td><div class="checkbox"><div class="switch-container"><div class="switch">';
                                                $checked = (in_array($handler, $pScripts)) ? 'checked' : '';
                                                $data .= '<input id="tkjs-'.$handler.'" name="tkjs_exc_assets[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$checked.'>';
                                                $data .= '<label for="tkjs-'.$handler.'"></label></div></div>';
                                                $data .= '</div></td></tr>';
                                                $data .= $this->get_global_row('tkgjs_exc_assets', 'js', $handler, $gScripts);
                                            }
                                        }
                                        $data .= '</tbody></table>';
                                    }
                                    $data .= '<input type="hidden" class="asset-post-id" value="'.$post_id.'">';
                                } else {
                                    $data .= '<div class="info">'.__("No assets available.").'</div>';
                                }
                                $data .= '</div>';
                                $response = array('success'=>true, 'data'=>$data);
                            } else {
                                $response = array('success'=>false, 'message'=>__("Invalid html page.", "toolkit-for-elementor"));
                            }
                        } else {
                            $response = array('success'=>false, 'message'=>__("Invalid html page.", "toolkit-for-elementor"));
                        }
                    } else {
                        $response = array('success'=>false, 'message'=>__("Invalid html page.", "toolkit-for-elementor"));
                    }
                } else {
                    $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
                }
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function get_global_row($input_name, $type, $handler, $gList){
            $post_types = get_post_types(['public'=>true], 'all');
            $gChecked = '';
            $excTypes = array();
            if( $gList ){
                foreach ($gList as $item) {
                    $array = explode('|', $item);
                    if( $array[0] == $handler ){
                        $gChecked = 'checked';
                        if( isset($array[1]) ){
                            $excTypes = explode(',', $array[1]);
                        }
                    }
                }
            }
            $data = '<tr class="border-bottom"><td></td><td></td><td class="max400"><b>'.__('Dequeue Globally').'</b><br/><br/>';
            $data .= '<div class="post-types-list '.($gChecked ? 'show-list' : '').'"><b>'.__('Except Post Types:').'</b><br/>';
            foreach ($post_types as $post_type){
                if( ! in_array($post_type->name, $this->exc_post_types) ){
                    $checked = in_array($post_type->name, $excTypes) ? 'checked' : '';
                    $data .= '<label for="'.$handler.'-'.$type.'-'.$post_type->name.'">';
                    $data .= ' <input id="'.$handler.'-'.$type.'-'.$post_type->name.'" name="global_'.$type.'_post_types[]" type="checkbox" class="ninja-toggler" value="'.$post_type->name.'" '.$checked.'>';
                    $data .= $post_type->label.'</label>';
                }
            }
            $data .= '</div></td><td><div class="checkbox"><div class="switch-container"><div class="switch">';
            $data .= '<input id="tkg'.$type.'-'.$handler.'" name="'.$input_name.'[]" type="checkbox" class="ninja-toggler" value="'.$handler.'" '.$gChecked.'>';
            $data .= '<label for="tkg'.$type.'-'.$handler.'"></label></div></div>';
            $data .= '</div></td></tr>';
            return $data;
        }

        function save_assets(){
            if( isset($_POST['post_id']) && $_POST['post_id'] > 0 && isset($_POST['styles']) && isset($_POST['scripts']) ){
                update_post_meta($_POST['post_id'], 'toolkit_excluded_styles', esc_sql($_POST['styles']));
                update_post_meta($_POST['post_id'], 'toolkit_excluded_scripts', esc_sql($_POST['scripts']));
                update_option('toolkit_excluded_styles_list', $_POST['gstyles']);
                update_option('toolkit_excluded_scripts_list', $_POST['gscripts']);
                $response = array('success'=>true, 'message'=>__("Excluded assets have been saved successfully.", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function dequeue_post_scripts(){
            global $post;
            if( $post && is_object($post) ){
                $post_id = $post->ID;
                $pScripts = get_post_meta($post_id, 'toolkit_excluded_scripts', true);
                $pScripts = ($pScripts && is_array($pScripts)) ? $pScripts : array();
                $gScripts = get_option('toolkit_excluded_scripts_list', array());
                $gScripts = ($gScripts && is_array($gScripts)) ? $gScripts : array();
                if( $pScripts ){
                    foreach ($pScripts as $pScript) {
                        wp_deregister_script( $pScript );
                        wp_dequeue_script( $pScript );
                    }
                }
                if( $gScripts ){
                    foreach ($gScripts as $gScript) {
                        $array = explode('|', $gScript);
                        $excTypes = isset($array[1]) ? explode(',', $array[1]) : array();
                        if( ! in_array($post->post_type, $excTypes) ){
                            wp_deregister_script( $array[0] );
                            wp_dequeue_script( $array[0] );
                        }
                    }
                }
            }
        }

        function dequeue_post_styles(){
            global $post;
            if( $post && is_object($post) ){
                $post_id = $post->ID;
                $pStyles = get_post_meta($post_id, 'toolkit_excluded_styles', true);
                $pStyles = ($pStyles && is_array($pStyles)) ? $pStyles : array();
                $gStyles = get_option('toolkit_excluded_styles_list', array());
                $gStyles = ($gStyles && is_array($gStyles)) ? $gStyles : array();
                if( $pStyles ){
                    foreach ($pStyles as $pStyle) {
                        wp_deregister_style( $pStyle );
                        wp_dequeue_style( $pStyle );
                    }
                }
                if( $gStyles ){
                    foreach ($gStyles as $gStyle) {
                        $array = explode('|', $gStyle);
                        $excTypes = isset($array[1]) ? explode(',', $array[1]) : array();
                        if( ! in_array($post->post_type, $excTypes) ){
                            wp_deregister_style( $array[0] );
                            wp_dequeue_style( $array[0] );
                        }
                    }
                }
            }
        }

        function filesize_formatted($path){
            $size = filesize($path);
            $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
        }

    }
}