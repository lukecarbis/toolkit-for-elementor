<?php
if( !function_exists('theme_toolkit_settings_display')) {
    function theme_toolkit_settings_display(){
        ob_start();
        if( is_toolkit_for_elementor_activated() ){ ?>
            <div class="" id="theme-toolkit-themeless">
                <div class="tabs-holder">
                    <div class="tab-nav">
                        <ul>
                            <li class="active-tab" data-tabid="inshdft-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/cm.svg" style="float: right;" width="48">
                                <span><?php _e('CODE MANAGER'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Insert Custom Code'); ?></medium>
                                </p>
                            </li>
                            <li data-tabid="accmanager-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/acl.svg" style="float: right;" width="48">
                                <span><?php _e('ACCESS MANAGER'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Manage ToolKit & Plugin Access'); ?></medium>
                                </p>
                            </li>
                            <li data-tabid="core-tweaks-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/wpct.svg" style="float: right;" width="48">
                                <span><?php _e('WP CORE MANAGER'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Modify WP Core Settings'); ?></medium>
                                </p>
                            </li>
                            <li data-tabid="widget-manager-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/wm.svg" style="float: right;" width="48">
                                <span><?php _e('WIDGET MANAGER'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Disable Unused Elementor Widgets'); ?></medium>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="content-tab">
                        <div class="single-tab" id="inshdft-tab" style="display: block">
                            <?php include_once 'code-manager.php'; ?>
                        </div>
                        <div class="single-tab" id="accmanager-tab">
                            <?php include_once 'admin-access-links.php'; ?>
                        </div>
                        <div class="single-tab" id="core-tweaks-tab">
                            <?php include_once 'core-tweaks.php'; ?>
                        </div>
                        <div class="single-tab" id="widget-manager-tab">
                            <div class="widget-manager-section">
                                <?php include 'widget-manager.php'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class='tab-fade'></div>
                </div>
            </div>
            <?php
        } else { ?>
            <div class="not-active-notice">
                <?php _e('Oops, looks like you do not have an active license yet, please activate your license first in the My License tab'); ?>
            </div>
        <?php }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}
