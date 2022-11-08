<?php
if( ! class_exists('Toolkit_GA_Fonts') ){
    class Toolkit_GA_Fonts {

        public $fpath = WP_CONTENT_DIR . '/cache/toolkit/fonts/';
        public $furl = WP_CONTENT_URL . '/cache/toolkit/fonts/';

        function __construct(){
            $blog_id = get_current_blog_id();
            $this->fpath = $this->fpath . $blog_id . '/';
            $this->furl = $this->furl . $blog_id . '/';
        }

        function create_gfonts_local_files($url, $new_file_path){
            if (substr($url, 0, 2) === '//') {
                $url = 'https:' . $url;
            }
            $user_agent =
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36';

            $css_file_response = wp_remote_get($url, [
                'user-agent' => $user_agent,
            ]);
            $css = $css_file_response['body'];
            $fonts_to_download = $this->get_font_urls($css);
            $minifier = new Toolkit_Minifier_Public();
            $minifier->toolkit_mkdir_p($this->fpath);
            $downloaded_fonts = $this->download_and_self_host_files($fonts_to_download);
            foreach ($downloaded_fonts as $font) {
                $css = str_replace($font['original_url'], $font['new_url'], $css);
            }
            $settOpts = get_option('toolkit_elementor_tweaks', array());
            if( isset($settOpts['fallback_fonts']) && $settOpts['fallback_fonts'] == 'on' ){
                if( strpos($css, 'font-display') === false ){
                    $css = str_replace('@font-face{', '@font-face {font-display:swap;', $css);
                    $css = str_replace('@font-face {', '@font-face {font-display:swap;', $css);
                }
            }
            $minifier->toolkit_write_minify_file($css, $new_file_path, true);
        }

        function get_font_urls($css){
            $regex = '/url\((https:\/\/fonts\.gstatic\.com\/.*?)\)/';
            preg_match_all($regex, $css, $matches);
            return $matches[1];
        }

        function download_and_self_host_files($urls){
            $multi_handle = curl_multi_init();
            $file_pointers = [];
            $curl_handles = [];
            $self_hosted_fonts = [];
            
            foreach ($urls as $key => $url) {
                $file_name = basename($url);
                $file_path = $this->fpath . $file_name;
                $new_file_url = $this->furl . $file_name;
                if ( ! is_file($file_path) ) {
                    $curl_handles[$key] = curl_init($url);
                    $file_pointers[$key] = fopen($file_path, 'w');
                    curl_setopt($curl_handles[$key], CURLOPT_FILE, $file_pointers[$key]);
                    curl_setopt($curl_handles[$key], CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl_handles[$key], CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl_handles[$key], CURLOPT_HEADER, 0);
                    curl_setopt($curl_handles[$key], CURLOPT_CONNECTTIMEOUT, 120);
                    curl_multi_add_handle($multi_handle, $curl_handles[$key]);
                    array_push($self_hosted_fonts, [
                        'original_url'  => $url,
                        'new_url'       => $new_file_url,
                    ]);
                }
            }

            do {
                curl_multi_exec($multi_handle, $running);
            } while ($running > 0);

            foreach ($urls as $key => $url) {
                if( isset($curl_handles[$key], $file_pointers[$key]) ){
                    curl_multi_remove_handle($multi_handle, $curl_handles[$key]);
                    curl_close($curl_handles[$key]);
                    fclose($file_pointers[$key]);
                }
            }
            curl_multi_close($multi_handle);
            return $self_hosted_fonts;
        }

        function toolkit_preload_fonts($html, $font_urls){
            $preload_tags = '';
            foreach ($font_urls as $url) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $tag = "<link rel='preload' href='$url' as='font' type='font/$extension' crossorigin>";
                $preload_tags .= $tag;
            }
            $html->find('title', 0)->outertext .= $preload_tags;
        }

    }
}