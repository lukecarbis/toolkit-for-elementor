<?php
if( ! class_exists('Toolkit_For_Elementor_LPage') ){
    class Toolkit_For_Elementor_LPage {

        const DOCUMENT_TYPE = 'landing-page';
        const CPT = 'e-landing-page';

        function __construct(){

        }

        function get_landing_pages() {
            $args = array(
                'post_type'     => self::CPT,
                'post_status'   => 'any',
                'posts_per_page'=> -1,
                'meta_key'      => '_elementor_template_type',
                'meta_value'    => self::DOCUMENT_TYPE
            );
            $query = new WP_Query($args);
            $templates = [];

            if ( $query->have_posts() ) {
                foreach ( $query->get_posts() as $post ) {
                    $templates[] = $this->get_landing_page( $post );
                }
            }
            return $templates;
        }

        public function get_landing_page($post){
            $user = get_user_by( 'id', $post->post_author );
            $date = strtotime( $post->post_date );
            $data = [
                'template_id'       => $post->ID,
                'type'              => self::DOCUMENT_TYPE,
                'title'             => $post->post_title,
                'thumbnail'         => get_the_post_thumbnail_url( $post ),
                'date'              => $date,
                'human_date'        => date_i18n( get_option( 'date_format' ), $date ),
                'human_modified_date' => date_i18n( get_option( 'date_format' ), strtotime( $post->post_modified ) ),
                'author'            => $user->display_name,
                'status'            => $post->post_status,
                'hasPageSettings'   => false,
                'tags'              => [],
                'url'               => get_permalink( $post->ID )
            ];
            return $data;
        }

    }
}