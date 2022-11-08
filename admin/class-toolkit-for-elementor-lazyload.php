<?php
if ( ! class_exists( 'Toolkit_For_Elementor_LazyLoad' ) ) {
    class Toolkit_For_Elementor_LazyLoad {

        public $path = WP_CONTENT_DIR . '/cache/toolkit/youtube/';
        public $url = WP_CONTENT_URL . '/cache/toolkit/youtube/';
        public $exclude_keywords = [
            'eager',
            'skip-lazy',
            'data-src=',
            'data-srcset=',
            'data-no-lazy=',
            'data-lazy-original=',
            'data-lazy-src=',
            'data-lazysrc=',
            'data-lazyload=',
            'lazy-slider-img=',
            'class="ls-l',
            'class="ls-bg',
            'soliloquy-image',
            'skip-lazy',
            'data:image',
        ];

        function __construct(){

        }

        public function save_settings(){
            if (isset($_POST['_nonce']) && $_POST['_nonce']) {
                if (isset($_POST['image']) && isset($_POST['video']) && isset($_POST['iframe'])) {
                    $mediaSetting = array();
                    $mediaSetting['image'] = isset($_POST['image']) ? $_POST['image'] : 'off';
                    $mediaSetting['img_loadtype'] = isset($_POST['img_loadtype']) ? $_POST['img_loadtype'] : 'native';
                    $mediaSetting['image_abvfold'] = isset($_POST['image_abvfold']) ? $_POST['image_abvfold'] : '2';
                    $mediaSetting['preload_images'] = isset($_POST['preload_images']) ? $_POST['preload_images'] : 'off';
                    $mediaSetting['image_attrs'] = isset($_POST['image_attrs']) ? $_POST['image_attrs'] : 'off';
                    $mediaSetting['video'] = isset($_POST['video']) ? $_POST['video'] : 'off';
                    $mediaSetting['yt_placeholder'] = isset($_POST['yt_placeholder']) ? $_POST['yt_placeholder'] : 'off';
                    $mediaSetting['yt_self_host'] = isset($_POST['yt_self_host']) ? $_POST['yt_self_host'] : 'off';
                    $mediaSetting['iframe'] = isset($_POST['iframe']) ? $_POST['iframe'] : 'off';
                    $mediaSetting['exclude_loading'] = isset($_POST['exclude_loading']) ? $_POST['exclude_loading'] : '';
                    update_option('toolkit_elementor_settings', $mediaSetting);
                    toolkit_remove_minify_css_js_files(TOOLKIT_FOR_ELEMENTOR_MASTER_PATH.'/toolkit');
                    $response = array('success'=>true, 'message'=>'Settings has saved successfully!', 'msg2'=>__("Cache has cleared successfully"));
                } else {
                    $response = array('success' => false, 'message' => 'Invalid request.');
                }
            } else {
                $response = array('success' => false, 'message' => 'Nonce missing');
            }
            wp_send_json($response);
            exit();
        }

        function enqueue_scripts(){
            wp_enqueue_script('toolkit-lazy-load', TOOLKIT_FOR_ELEMENTOR_URL . 'public/js/lazy-load.min.js', array('jquery'), TOOLKIT_FOR_ELEMENTOR_VERSION, true);
        }

        function image_lazy_load($html, $lazy_load_method, $exclude_keywords){
            $images = $html->find('picture > source[src], img[src]');
            $placeholder = 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=';
            $exclude_keywords = array_merge($this->exclude_keywords, $exclude_keywords);
            if( $images ){
                foreach ($images as $image) {
                    $exclude = false;
                    foreach ($exclude_keywords as $keyword) {
                        if (strpos($image->outertext, $keyword) !== false) {
                            $exclude = true;
                            break;
                        }
                    }
                    if( ! $exclude ){
                        if ($lazy_load_method === 'native') {
                            $image->loading = 'lazy';
                        } else {
                            $image->{'data-tklazy-src'} = $image->src;
                            $image->{'data-tklazy-method'} = 'viewport';
                            $image->{'data-tklazy-attributes'} = 'src';
                            $image->src = $placeholder;
                            if ($image->srcset) {
                                $image->{'data-tklazy-srcset'} = $image->srcset;
                                $image->{'data-tklazy-attributes'} = 'src,srcset';
                                $image->srcset = false;
                            }
                        }
                    }
                }
            }
            $this->lazy_load_bg_style($html);
            $this->lazy_load_bg_class($html);
            return $html;
        }

        public function lazy_load_bg_style($html){
            $elements = $html->find("div[style*='url'], section[style*='url'], span[style*='url'], figure[style*='url']");
            foreach ($elements as $element) {
                $element->{'data-tklazy-style'} = $element->style;
                $element->{'data-tklazy-method'} = 'viewport';
                $element->{'data-tklazy-attributes'} = 'style';
                $element->style = null;
            }
        }

        public function lazy_load_bg_class($html){
            $elements = $html->find('.toolkit-lazybg');
            foreach ($elements as $element) {
                $element->{'data-tklazy-class'} = $element->class;
                $element->{'data-tklazy-method'} = 'viewport';
                if( $element->id ){
                    $element->{'data-tklazy-id'} = $element->id;
                    $element->{'data-tklazy-attributes'} = 'id,class';
                    $element->id = null;
                } else {
                    $element->{'data-tklazy-attributes'} = 'class';
                }
                $element->class = null;
            }
        }

        function iframe_lazy_load($html){
            $iframes = $html->find('iframe');
            if( $iframes ){
                foreach ($iframes as $iframe) {
                    $iframe->{'data-tklazy-src'} = $iframe->src;
                    $iframe->{'data-tklazy-method'} = 'viewport';
                    $iframe->{'data-tklazy-attributes'} = 'src';
                    $iframe->src = false;
                }
            }
            return $html;
        }

        function video_lazy_load($html){
            $videos = $html->find('video');
            if( $videos ){
                foreach ($videos as $video) {
                    $video->preload = 'none';
                }
            }
            return $html;
        }

        function yt_iframe_placeholder($html, $self_host)
        {
            $iframes = $html->find('iframe[src*="youtu.be"],iframe[src*="youtube.com"],iframe[src*="youtube-nocookie.com"]');
            if( $iframes ){
                foreach ($iframes as $iframe) {
                    $srcdoc = $this->get_youtube_srcdoc($iframe, $self_host);
                    $iframe->srcdoc = $srcdoc;
                    $iframe->{'data-src'} = $iframe->src;
                    $iframe->src = false;
                }
            }
            return $html;
        }

        function get_youtube_srcdoc($video, $self_host)
        {
            $video->src .= preg_match('/\?/', $video->src) ? '&autoplay=1' : '?autoplay=1';
            $video_id = $this->get_youtube_video_id($video->src);
            $video_image_url = "https://img.youtube.com/vi/$video_id/hqdefault.jpg";
            if ( $self_host ) {
                $video_image_url = $this->self_host_placeholder($video_id);
            }
            return "<style>body{overflow:hidden}img{margin:auto;width:100%;position:absolute;inset:0;height:auto;overflow:hidden}svg{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);filter:grayscale(100%);opacity:.8}a:hover svg{opacity:1;filter:none}</style><a href=$video->src><img  width=$video->width height=$video->height src='$video_image_url'><svg xmlns='http://www.w3.org/2000/svg' width=68 height=48><path fill=red d='M67 8c-1-3-3-6-6-6-5-2-27-2-27-2S12 0 7 2C4 2 2 5 1 8L0 24l1 16c1 3 3 6 6 6 5 2 27 2 27 2s22 0 27-2c3 0 5-3 6-6l1-16-1-16z'/><path d='M45 24L27 14v20' fill=#fff /></svg></a>";
        }

        function self_host_placeholder($video_id)
        {
            $video_image_url = "https://img.youtube.com/vi/$video_id/hqdefault.jpg";
            $cached_image_file = $this->path . "$video_id-hqdefault.jpg";
            $cached_image_url = $this->url . "$video_id-hqdefault.jpg";
            if ( ! is_file($cached_image_file) ) {
                $image_response = wp_remote_get($video_image_url);
                $image = wp_remote_retrieve_body($image_response);
                if ( ! $image ) {
                    return $video_image_url;
                }
                $minifier = new Toolkit_Minifier_Public();
                $minifier->toolkit_write_minify_file($image, $cached_image_file, true);
            }
            return $cached_image_url;
        }

        function get_youtube_video_id($url)
        {
            if (preg_match('/(?:\/|=)(.{11})(?:$|&|\?)/', $url, $matches)) {
                return $matches[1];
            }
            return false;
        }

        function exclude_top_images($html, $total = 2){
            $total = absint($total);
            $images = $html->find('picture > source[src], img[src]');
            if( $images ){
                foreach ($images as $index => $image) {
                    $image->loading = 'eager';
                    if ($index + 1 === $total) {
                        break;
                    }
                }
            }
            return $html;
        }

        function preload_critical_images($html){
            $images = $html->find("img[loading=eager]");
            if (is_singular() && $html->find('img.wp-post-image[src]')) {
                $featured_image = $html->find('img.wp-post-image[src]');
                array_push($images, $featured_image);
            }
            $preload_tags = '';
            if( $images ){
                foreach ($images as $image) {
                    if( $image->{'data-src'} ){
                        $image_src = $image->{'data-src'};
                    } elseif( $image->src ){
                        $image_src = $image->src;
                    } else {
                        $image_src = '';
                    }
                    if( $image_src ){
                        $tag = "<link rel='preload' as='image' href='$image_src'";
                        $tag .= $image->srcset ? "imagesrcset='$image->srcset'" : '';
                        $tag .= $image->sizes ? "imagesizes='$image->sizes'" : '';
                        $tag .= '>';
                        $preload_tags .= $tag;
                        $image->src = $image_src;
                        $image->loading = 'eager';
                        $image->{'data-tklazy-src'} = false;
                        $image->{'data-tklazy-srcset'} = false;
                        $image->{'data-tklazy-method'} = false;
                        $image->{'data-tklazy-attributes'} = false;
                        break;
                    }
                }
            }
            $html->find('title', 0)->outertext .= $preload_tags;
            return $html;
        }

        function add_images_width_height($html){
            $images = $html->find('img[!height],img[height=auto]');
            if ($images) {
                foreach ($images as $image) {
                    $size = $this->get_height_width($image->src);
                    if ($size && $size['height'] > 0 && $size['width'] > 0) {
                        $image->width = isset($image->width) ? $image->width : $size['width'];
                        $image->height = $image->width
                            ? round(($image->width * $size['height']) / $size['width'])
                            : $size['height'];
                    }
                }
            }
            return $html;
        }

        function get_height_width($img_url){
            try {
                preg_match('/(?:.+)-([0-9]+)x([0-9]+)\.(jpg|jpeg|png|gif|svg)$/', $img_url, $matches);
                if (!empty($matches) && isset($matches[1]) && isset($matches[2])) {
                    $width = $matches[1];
                    $height = $matches[2];
                    return ['width' => $width, 'height' => $height];
                }

                $abs_path = str_replace(wp_basename(WP_CONTENT_DIR), '', WP_CONTENT_DIR);
                $file_path = $abs_path . parse_url($img_url)['path'];
                if ( ! is_file($file_path) ) {
                    return false;
                }

                if ( pathinfo($file_path)['extension'] === 'svg' ) {
                    $xml = @simplexml_load_file($file_path);
                    $attr = $xml->attributes();
                    $viewbox = explode(' ', $attr->viewBox);
                    $width =
                        isset($attr->width) && preg_match('/\d+/', $attr->width, $value)
                            ? (int) $value[0]
                            : (count($viewbox) == 4
                            ? (int) $viewbox[2]
                            : null);
                    $height =
                        isset($attr->height) && preg_match('/\d+/', $attr->height, $value)
                            ? (int) $value[0]
                            : (count($viewbox) == 4
                            ? (int) $viewbox[3]
                            : null);
                    if ($width && $height) {
                        return ['width' => $width, 'height' => $height];
                    }
                }

                list($width, $height) = getimagesize($file_path);
                if ($width && $height) {
                    return ['width' => $width, 'height' => $height];
                }

                $img_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif|svg)$)/i', '', $img_url);
                $attachment_id = attachment_url_to_postid($img_url);
                $metadata = wp_get_attachment_image_src($attachment_id, 'full');
                if ($metadata) {
                    return ['width' => $metadata['width'], 'height' => $metadata['height']];
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }


    }

}