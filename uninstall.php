<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$dataHandling = get_option('toolkit_data_handling_options', array());
if( isset($dataHandling['upon_uninstal']) && $dataHandling['upon_uninstal'] == 'remember' ){
    return;
} elseif( isset($dataHandling['upon_uninstal']) && $dataHandling['upon_uninstal'] == 'rem_some' ){
    require_once 'includes/class-toolkit-for-elementor-deactivator.php';
    Toolkit_For_Elementor_Deactivator::uninstall($dataHandling);
} else {
    require_once 'includes/class-toolkit-for-elementor-deactivator.php';
    Toolkit_For_Elementor_Deactivator::uninstall(array());
}