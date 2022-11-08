<?php
function my_license_settings_display(){
	$obj = new Lazy_load_Settings();
	$status  = get_option( 'toolkit_license_status' );
	$betaTest = get_option( 'toolkit_beta_testing_options', array() );
    $maskedLicenseKey = '';
	if( ! empty($obj->toolkit_license_key) ){
		$maskedLicenseKey = toolkit_encrypt_string($obj->toolkit_license_key);
	}
    ob_start(); ?>
    <div class="toolkit-my-license" id="toolkit-my-license">
        <div class="tabs-holder">
            <div class="tab-nav">
                <ul class="licensekit-tabs">
                    <li class="active-tab" data-tabid="license-tab">
                        <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/license.svg" style="float: right;" width="40">
                        <span><?php _e('LICENSE SETTINGS'); ?></span>
                        <p class="margin0">
                            <medium><?php _e('Activate ToolKit License'); ?></medium>
                        </p>
                    </li>
                    <li data-tabid="data-handling-tab">
                        <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/dh.svg" style="float: right;" width="48">
                        <span><?php _e('DATA HANDLING'); ?></span>
                        <p class="margin0">
                            <medium><?php _e('Plugin Data Preferences'); ?></medium>
                        </p>
                    </li>
                    <?php if( is_toolkit_for_elementor_activated() ){ ?>
                        <li data-tabid="beta-testing-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/beta.svg" style="float: right;" width="48">
                            <span><?php _e('BETA TESTING'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Become a Beta Tester'); ?></medium>
                            </p>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="content-tab">
                <div class="single-tab" id="license-tab" style="display: block;">
                    <div class="col-md-5" id="toolkit-license-verification">
                        <div class="widget-panel-title"><?php _e('My ToolKit License'); ?></div>
                        <?php if( $status !== false && $status == 'valid' ) { ?>
                            <div class="form-group">
                                  <label for="sel1">License Key</label>
                                  <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>" />
                                  <span style="color:#069825;vertical-align: bottom;">active</span>
                                  <input class="form-control" name="template-key" style="border:2px solid #069825!important" data-license="<?php echo ( ! empty($obj->toolkit_license_key) ) ? $obj->toolkit_license_key : ''; ?>" value="<?php echo $obj->toolkit_license_key ? $maskedLicenseKey : ''; ?>"/>
                            </div>
                            <div class="form-group pull-left">
                                <button type="button" class="button toolkit-btn" id="key-deactivate">Deactivate License</button>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <label for="sel1">License Key</label>
                                <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>" />
                                <input class="form-control" name="template-key" data-license="<?php echo ( ! empty($obj->toolkit_license_key) ) ? $obj->toolkit_license_key : ''; ?>" value="<?php echo $obj->toolkit_license_key ? $maskedLicenseKey : ''; ?>"/>
                            </div>
                            <div class="form-group pull-left">
                                <button type="button" class="button toolkit-btn" id="key-verify">Activate License</button>
                            </div>
                        <?php } ?>
                        <div class='tab-fade'></div>
                    </div>
                </div>
                <div class="single-tab" id="data-handling-tab">
                    <?php include 'data-handling.php'; ?>
                </div>
                <?php if( is_toolkit_for_elementor_activated() ){ ?>
                    <div class="single-tab" id="beta-testing-tab">
                        <div class="widget-panel-title"><?php _e('ToolKit Beta Testing'); ?></div>
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($betaTest['beta_testing']) && $betaTest['beta_testing'] == 'yes') ? 'checked' : ''; ?>
                                        <input id="toolkit_beta_testing" name="toolkit_beta_testing" type="checkbox" value="1" <?php echo $checked; ?>>
                                        <label for="toolkit_beta_testing"></label>
                                    </div>
                                </div>
                                <?php _e('<b>Become a Beta Tester</b><br><strong>Please Note:</strong> We do not recommend updating to a beta version on production sites.'); ?>
                            </label><br>
                            <input type="hidden" id="toolkit_testing_email" name="toolkit_testing_email" class="text-field" value="<?php echo isset($betaTest['testing_email']) ? $betaTest['testing_email'] : ''; ?>">
                            <input type="hidden" id="toolkit_testing_name" name="toolkit_testing_name" class="text-field" value="">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
