<?php
class Toolkit_Minifier_Public {
    
    public function minify_css_files($html, $excluded_urls, $concatenate, $exc_elementor, $cdn_url){
        $styles = $html->find('link[rel="stylesheet"][href*=".css"]');
        if( $concatenate && $exc_elementor ){
            $excluded_urls[] = 'elementor/assets/lib/';
            $excluded_urls[] = 'elementor-pro/assets/lib/';
            $excluded_urls[] = 'elementor/assets/css/';
            $excluded_urls[] = 'elementor-pro/assets/css/';
        }
        if( $concatenate ){
            $files = array();
            foreach ($styles as $style) {
                if( ! $this->is_toolkit_external_file($style->href) && ! $this->is_excluded_file($style, 'css', $excluded_urls) ){
                    $files[] = strtok($style->href, '?');
                    $style->outertext = '';
                }
            }
            if( $files ){
                $minify_url = $this->get_toolkit_minify_url($files, 'css', $cdn_url);
                if( $minify_url ){
                    $minify_tag = '<link rel="stylesheet" href="' . $minify_url . '" data-minify="1" />';
                    $html->find('head', 0)->innertext = $html->find('head', 0)->innertext.$minify_tag;
                }
            }
        } else {
            foreach ($styles as $style) {
                if( $this->is_toolkit_external_file(strtok($style->href, '?')) ){
                    continue;
                }
                if( $this->is_excluded_file($style, 'css', $excluded_urls) ){
                    continue;
                }
                $minify_url = $this->get_toolkit_minify_url(strtok($style->href, '?'), 'css', $cdn_url, $style->media);
                if( $minify_url ){
                    $style->href = $minify_url;
                    $style->{'data-minify'} = '1';
                }
                if ($style->media && $style->media != 'all') {
                    $style->media = 'all';
                }
            }
        }
    }

    public function lazy_render_elements($html, $lazy_render_elems){
        array_push($lazy_render_elems, '.lazy-render');
        $lazy_render_elems = array_filter($lazy_render_elems);
        $lazy_elements = $html->find(implode(',', $lazy_render_elems));
        foreach ($lazy_elements as $lazy_element) {
            $lazy_element->style = "content-visibility:auto;contain-intrinsic-size:1px 1000px;$lazy_element->style";
        }
    }

    public function minify_js_files($html, $excluded_urls, $concatenate, $exc_elementor, $cdn_url){
        $scripts = $html->find('script[src*=.js]');
        $excluded_urls[] = 'js/jquery/jquery';
        $excluded_urls[] = 'google-site-kit/dist/assets/js/';
        if( $concatenate && $exc_elementor ){
            $excluded_urls[] = 'elementor/assets/lib/';
            $excluded_urls[] = 'elementor-pro/assets/lib/';
            $excluded_urls[] = 'elementor/assets/js/';
            $excluded_urls[] = 'elementor-pro/assets/js/';
        }
        if( $concatenate ){
            $files = array();
            foreach ($scripts as $script) {
                if( ! $this->is_toolkit_external_file($script->src) && ! $this->is_excluded_file($script, 'js', $excluded_urls) ){
                    $files[] = strtok($script->src, '?');
                    $script->outertext = '';
                }
            }
            if( $files ){
                $minify_url = $this->get_toolkit_minify_url($files, 'js', $cdn_url);
                if( $minify_url ){
                    $minify_tag = '<script src="' . $minify_url . '" data-minify="1"></script>';
                    $html->find('body', 0)->innertext = $html->find('body', 0)->innertext.$minify_tag;
                }
            }
        } else {
            foreach ($scripts as $script) {
                if( ! $this->is_toolkit_external_file($script->src) && ! $this->is_excluded_file($script, 'js', $excluded_urls) ){
                    $minify_url = $this->get_toolkit_minify_url(strtok($script->src, '?'), 'js', $cdn_url);
                    if( $minify_url ){
                        $script->src = $minify_url;
                        $script->{'data-minify'} = '1';
                    }
                }
            }
        }
    }

    function create_toolkit_uniqid() {
        return str_replace( '.', '', uniqid( '', true ) );
    }

    function get_toolkit_parse_url( $url ) {
        if ( ! is_string( $url ) ) {
            return false;
        }
        $encoded_url = preg_replace_callback(
            '%[^:/@?&=#]+%usD',
            function ( $matches ) {
                return rawurlencode( $matches[0] );
            },
            $url
        );
        $url      = wp_parse_url( $encoded_url );
        $host     = isset( $url['host'] ) ? strtolower( urldecode( $url['host'] ) ) : '';
        $path     = isset( $url['path'] ) ? urldecode( $url['path'] ) : '';
        $scheme   = isset( $url['scheme'] ) ? urldecode( $url['scheme'] ) : '';
        $query    = isset( $url['query'] ) ? urldecode( $url['query'] ) : '';
        $fragment = isset( $url['fragment'] ) ? urldecode( $url['fragment'] ) : '';
        return apply_filters(
            'toolkit_parse_url',
            [
                'host'     => $host,
                'path'     => $path,
                'scheme'   => $scheme,
                'query'    => $query,
                'fragment' => $fragment,
            ]
        );
    }

    function toolkit_url_to_path( $url, $hosts = '' ) {
        $root_dir = trailingslashit( dirname( WP_CONTENT_DIR ) );
        $root_url = str_replace( wp_basename( WP_CONTENT_DIR ), '', content_url() );
        $url_host = wp_parse_url( $url, PHP_URL_HOST );
        if ( null === $url_host ) {
            $subdir_levels = substr_count( preg_replace( '/https?:\/\//', '', get_site_url() ), '/' );
            $url           = trailingslashit( get_site_url() . str_repeat( '/..', $subdir_levels ) ) . ltrim( $url, '/' );
        }
        if ( isset( $hosts[ $url_host ] ) && 'home' !== $hosts[ $url_host ] ) {
            $url = str_replace( $url_host, wp_parse_url( get_site_url(), PHP_URL_HOST ), $url );
        }
        $root_url = preg_replace( '/^https?:/', '', $root_url );
        $url      = preg_replace( '/^https?:/', '', $url );
        $file     = str_replace( $root_url, $root_dir, $url );
        $file     = $this->toolkit_realpath( $file );
        $file = apply_filters( 'toolkit_url_to_path', $file, $url );
        if ( ! $this->toolkit_direct_filesystem()->is_readable( $file ) ) {
            return false;
        }
        return $file;
    }

    function toolkit_realpath( $file ) {
        $path = array();
        foreach ( explode( '/', $file ) as $part ) {
            if ( '' === $part || '.' === $part ) {
                continue;
            }
            if ( '..' !== $part ) {
                array_push( $path, $part );
            }
            elseif ( count( $path ) > 0 ) {
                array_pop( $path );
            }
        }
        $prefix = 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ? '' : '/';
        return $prefix . join( '/', $path );
    }

    function get_toolkit_minify_url( $files, $extension, $cdn_url = '', $media = '' ) {
        if ( empty( $files ) ) {
            return false;
        }
        $hosts         = array();
        $hosts['home'] = $this->toolkit_extract_url_component( home_url(), PHP_URL_HOST );
        $hosts_index   = array_flip( $hosts );
        $minify_key    = get_option( 'toolkit_minify_' . $extension . '_key', '' );
        if( ! $minify_key ){
            update_option( 'toolkit_minify_' . $extension . '_key', $this->create_toolkit_uniqid() );
            $minify_key = get_option( 'toolkit_minify_' . $extension . '_key', '' );
        }
        if ( is_string( $files ) ) {
            $file      = $this->get_toolkit_parse_url( $files );
            $file_path = $this->toolkit_url_to_path( strtok( $files, '?' ), $hosts_index );
            $unique_id = md5( $files . $minify_key );
            $filename  = preg_replace( '/\.(' . $extension . ')$/', '-' . $unique_id . '.' . $extension, ltrim( $this->toolkit_realpath( $file['path'] ), '/' ) );
            $filename = basename($filename);
        } else {
            foreach ( $files as $file ) {
                $file_path[] = $this->toolkit_url_to_path( $file, $hosts_index );
            }
            $files_hash = implode( ',', $files );
            $filename   = md5( $files_hash . $minify_key ) . '.' . $extension;
        }
        $minified_file = TOOLKIT_FOR_ELEMENTOR_MIN_PATH . '/' . $filename;
        if ( ! file_exists( $minified_file ) ) {
            $minified_content = $this->toolkit_minify( $file_path, $extension );
            if ( ! $minified_content ) {
                return false;
            }
            if( 'css' === $extension ){
                $settOpts = get_option('toolkit_elementor_tweaks', array());
                if( isset($settOpts['fallback_fonts']) && $settOpts['fallback_fonts'] == 'on' ){
                    $minified_content = str_replace('@font-face{', '@font-face{font-display:swap;', $minified_content);
                }
                if( $cdn_url ){
                    $minified_content = str_replace(get_site_url(), $cdn_url, $minified_content);
                }
                if ($media && $media != 'all') {
                    $minified_content = "@media $media{{$minified_content}}";
                }
            }
            $minify_filepath = $this->toolkit_write_minify_file( $minified_content, $minified_file );
            if ( ! $minify_filepath ) {
                return false;
            }
        }
        $minify_url = TOOLKIT_FOR_ELEMENTOR_MIN_URL . '/' . $filename;
        if ( 'css' === $extension ) {
            return apply_filters( 'toolkit_css_url', $minify_url );
        }
        if ( 'js' === $extension ) {
            return apply_filters( 'toolkit_js_url', $minify_url );
        }
        return $minify_url;
    }

    function toolkit_write_minify_file( $content, $minified_file, $force_update = false ) {
        if ( file_exists( $minified_file ) && ! $force_update ) {
            return true;
        }
        if ( ! $this->toolkit_mkdir_p( dirname( $minified_file ) ) ) {
            return false;
        }
        return $this->toolkit_put_content( $minified_file, $content );
    }

    function toolkit_put_content( $file, $content ) {
        $chmod = $this->toolkit_get_filesystem_perms( 'file' );
        return $this->toolkit_direct_filesystem()->put_contents( $file, $content, $chmod );
    }

    function toolkit_mkdir_p( $target ) {
        $target = str_replace( '//', '/', $target );
        $target = untrailingslashit( $target );
        if ( empty( $target ) ) {
            $target = '/';
        }
        if ( $this->toolkit_direct_filesystem()->exists( $target ) ) {
            return $this->toolkit_direct_filesystem()->is_dir( $target );
        }
        if ( $this->toolkit_mkdir( $target ) ) {
            return true;
        } elseif ( $this->toolkit_direct_filesystem()->is_dir( dirname( $target ) ) ) {
            return false;
        }
        if ( ( '/' !== $target ) && ( $this->toolkit_mkdir_p( dirname( $target ) ) ) ) {
            return $this->toolkit_mkdir_p( $target );
        }
        return false;
    }


    function toolkit_mkdir( $dir ) {
        $chmod = $this->toolkit_get_filesystem_perms( 'dir' );
        return $this->toolkit_direct_filesystem()->mkdir( $dir, $chmod );
    }

    function toolkit_get_filesystem_perms( $type ) {
        static $perms = [];
        switch ( $type ) {
            case 'dir':
            case 'dirs':
            case 'folder':
            case 'folders':
                $type = 'dir';
                break;

            case 'file':
            case 'files':
                $type = 'file';
                break;

            default:
                return 0755;
        }
        if ( isset( $perms[ $type ] ) ) {
            return $perms[ $type ];
        }
        switch ( $type ) {
            case 'dir':
                if ( defined( 'FS_CHMOD_DIR' ) ) {
                    $perms[ $type ] = FS_CHMOD_DIR;
                } else {
                    $perms[ $type ] = fileperms( ABSPATH ) & 0777 | 0755;
                }
                break;

            case 'file':
                if ( defined( 'FS_CHMOD_FILE' ) ) {
                    $perms[ $type ] = FS_CHMOD_FILE;
                } else {
                    $perms[ $type ] = fileperms( ABSPATH . 'index.php' ) & 0777 | 0644;
                }
        }
        return $perms[ $type ];
    }

    function toolkit_clean_exclude_file( $file ) {
        if ( ! $file ) {
            return false;
        }
        return wp_parse_url( $file, PHP_URL_PATH );
    }

    function is_excluded_file( $tag, $extension, $excluded_urls = array() ) {
        if ( $tag->{'data-minify'} || $tag->{'data-no-minify'} ) {
            return true;
        }
        if( $excluded_urls ){
            foreach ($excluded_urls as $excluded_url){
                if ( false !== strpos( $tag->outertext, $excluded_url ) ) {
                    return true;
                }
            }
        }
        if ( 'css' === $extension ) {
            $file_path = $this->toolkit_extract_url_component( $tag->href, PHP_URL_PATH );
        } elseif ( 'js' === $extension ) {
            $file_path = $this->toolkit_extract_url_component( $tag->src, PHP_URL_PATH );
        } else {
            $file_path = '';
        }
        if ( pathinfo( $file_path, PATHINFO_EXTENSION ) !== $extension ) {
            return true;
        }
        return false;
    }

    function get_toolkit_minify_excluded_external_js() {
        $excluded_external_js = apply_filters(
            'toolkit_minify_excluded_external_js', array(
                'forms.aweber.com',
                'gist.github.com',
                'stats.wp.com',
                'stats.wordpress.com',
                'www.statcounter.com',
                'widget.supercounters.com',
                'releases.flowplayer.org',
                'tools.meetaffiliate.com',
                'contextual.media.net',
                'app.getresponse.com',
                's0.wp.com',
                'smarticon.geotrust.com',
                'js.gleam.io',
                'script.ioam.de',
                'ir-na.amazon-adsystem.com',
                'verify.authorize.net',
                'form.jotformeu.com',
                'speakerdeck.com',
                'content.jwplatform.com',
                'app.ecwid.com',
                's.gravatar.com',
                'cdn.jsdelivr.net',
                'cdnjs.cloudflare.com',
                'code.jquery.com',
            )
        );
        return array_flip( $excluded_external_js );
    }

    function is_toolkit_external_file( $url ) {
        $file       = $this->get_toolkit_parse_url( $url );
        $wp_content = $this->get_toolkit_parse_url( WP_CONTENT_URL );
        $hosts      = array();
        $hosts[]    = $wp_content['host'];
        $hosts_index = array_flip( array_unique( $hosts ) );
        if ( isset( $file['host'] ) && ! empty( $file['host'] ) && ! isset( $hosts_index[ $file['host'] ] ) ) {
            return true;
        }
        if ( ! isset( $file['host'] ) && ! preg_match( '#(' . $wp_content['path'] . '|wp-includes)#', $file['path'] ) ) {
            return true;
        }
        return false;
    }

    function toolkit_extract_url_component( $url, $component ) {
        return _get_component_from_parsed_url_array( wp_parse_url( $url ), $component );
    }

    function toolkit_minify( $files, $extension ) {
        require_once TOOLKIT_FOR_ELEMENTOR_PATH . "includes/vendor/autoload.php";
        require_once TOOLKIT_FOR_ELEMENTOR_PATH . "public/class-toolkit-minify-css-urirewriter.php";
        if ( 'css' === $extension ) {
            $minify = new MatthiasMullie\Minify\CSS();
        } elseif ( 'js' === $extension ) {
            $minify = new MatthiasMullie\Minify\JS();
        }
        $files = (array) $files;
        foreach ( $files as $file ) {
            $file_content = $this->toolkit_direct_filesystem()->get_contents( $file );
            if( 'css' === $extension ){
                $min_file_url = str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $file);
                $file_content = $this->rewrite_absolute_urls( $file_content, $min_file_url );
            }
            $minify->add( $file_content );
        }
        $minified_content = $minify->minify();
        if ( empty( $minified_content ) ) {
            return false;
        }
        return $minified_content;
    }

    function rewrite_absolute_urls($content, $base_url){
        $regex = '/url\([\'"]*(.*?)[\'"]*\)/';
        $content = preg_replace_callback(
            $regex,
            function ($match) use ($base_url) {
                $url_string = $match[0];
                $relative_url = $match[1];
                $absolute_url = Toolkit_Minify_CSS_UriRewriter::parse($relative_url);
                $absolute_url->makeAbsolute(Toolkit_Minify_CSS_UriRewriter::parse($base_url));
                return str_replace($relative_url, $absolute_url, $url_string);
            },
            $content
        );

        return $content;
    }

    function toolkit_direct_filesystem() {
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        return new WP_Filesystem_Direct( new StdClass() );
    }

    //cdn css & js starts
    function toolkit_cdn_files( $html, $extension, $cdn_url, $excluded_keywords ) {
        if ( 'css' === $extension ) {
            $tags_match = $html->find('link[href*=.css]');
        }
        if ( 'js' === $extension ) {
            $tags_match = $html->find('script[src]');
        }
        if ( 'img' === $extension ) {
            $tags_match = $html->find('img[src]');
        }
        if ( 'font' === $extension ) {
            $tags_match = $html->find('link[href*=.otf], link[href*=.ttf], link[href*=.svg], link[href*=.eot], link[href*=.woff], link[href*=.woff2]');
        }
        $files = array();
        $site_url = get_site_url();
        foreach ( $tags_match as $tag ) {
            if( $extension == 'css' || $extension == 'font' ){
                if ( false !== strpos( $tag->href, $site_url ) ) {
                    $exclude_url = false;
                    if( $excluded_keywords ){
                        foreach ($excluded_keywords as $excluded_keyword) {
                            if ( false !== strpos( $tag->href, $excluded_keyword ) ) {
                                $exclude_url = true;
                                break;
                            }
                        }
                    }
                    if( ! $exclude_url ){
                        $tag->href = str_replace( $site_url, $cdn_url, $tag->href );
                    }
                    $files[] = $tag;
                }
            }
            if( $extension == 'js' || $extension == 'img' ){
                if ( false !== strpos( $tag->src, $site_url ) ) {
                    $exclude_url = false;
                    if( $excluded_keywords ){
                        foreach ($excluded_keywords as $excluded_keyword) {
                            if ( false !== strpos( $tag->src, $excluded_keyword ) ) {
                                $exclude_url = true;
                                break;
                            }
                        }
                    }
                    if( ! $exclude_url ){
                        $tag->src = str_replace( $site_url, $cdn_url, $tag->src );
                    }
                    $files[] = $tag;
                }
            }
        }
    }

    //defer js starts
    function toolkit_defer_files( $html, $exclude_jq, $excluded_keywords ) {
        $scripts = $html->find('script[src]');
        if( $exclude_jq ){
            $excluded_keywords[] = 'jquery';
        }
        $excluded_keywords[] = 'toolkit-scripts';
        $excluded_keywords[] = '/wp-includes/js/';
        foreach ( $scripts as $script ) {
            $exclude_url = false;
            if( $excluded_keywords ){
                foreach ($excluded_keywords as $excluded_keyword) {
                    if ( false !== strpos( $script->src, $excluded_keyword ) ) {
                        $exclude_url = true;
                        break;
                    }
                }
            }
            if ( ! $exclude_url ) {
                $script->defer = true;
                $script->async = null;
            }
        }
    }

    function toolkit_defer_inline($html, $excluded_keywords){
        $scripts = $html->find('script[!src][!type],script[!src][type="text/javascript"]');
        foreach ($scripts as $script) {
            if (!$script->innertext) {
                continue;
            }
            $exclude_url = false;
            if( $excluded_keywords ){
                foreach ($excluded_keywords as $excluded_keyword) {
                    if ( false !== stripos( $script, $excluded_keyword ) ) {
                        $exclude_url = true;
                        break;
                    }
                }
            }
            if( ! $exclude_url ){
                $script->src = 'data:text/javascript,' . rawurlencode($script->innertext);
                $script->type = 'text/javascript';
                $script->defer = '';
                $script->innertext = '';
            }
        }
    }

    //Delay JS Start
    function toolkit_delay_js_files( $html, $keywords ) {
        $tags_match = $html->find('script');
        if( $tags_match ){
            foreach ( $tags_match as $tag ) {
                foreach ($keywords as $keyword) {
                    $keyword = str_replace("\\'", "'", $keyword);
                    if ( $keyword && $tag->src && false !== strpos( $tag->src, $keyword ) ) {
                        $tag->{'data-src'} = $tag->src;
                        $tag->{'data-tklazy-method'} = 'interaction';
                        $tag->{'data-tklazy-attributes'} = 'src';
                        $tag->src = '';
                        break;
                    } elseif( $keyword && $tag->innertext && false !== strpos( $tag->innertext, $keyword ) ){
                        $tag->{'data-src'} = 'data:text/javascript;base64,' . base64_encode( $tag->innertext );
                        $tag->{'data-tklazy-method'} = 'interaction';
                        $tag->{'data-tklazy-attributes'} = 'src';
                        $tag->innertext = '';
                        break;
                    }
                }
            }
        }
    }
    
}
