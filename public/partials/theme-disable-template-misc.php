<?php
global $post;
$body_classes = array('theme-disable');
add_action( 'wp_enqueue_scripts', 'toolkit_enqueue_template_css' );

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );
$bodytag_code = get_option('theme_disable_bodytag_code', ''); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width">
    <?php wp_head(); ?>
</head>
<body <?php body_class($body_classes); ?>>
<?php
//insert body tag extra code
if( $bodytag_code ){
    eval(' ?>'.str_replace('\"','"', $bodytag_code).'<?php ');
}

//load elementor header
if( function_exists('elementor_theme_do_location') ){
    elementor_theme_do_location( 'header' );
}

//render content area
\Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content();

//load elementor footer
if( function_exists('elementor_theme_do_location') ){
    elementor_theme_do_location( 'footer' );
}

//wp footer
wp_footer();
?>
</body>
</html>
