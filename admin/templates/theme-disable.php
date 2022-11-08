<?php
if( !function_exists('theme_disable_settings_display')) {
    function theme_disable_settings_display(){
        ob_start();
        if( is_toolkit_for_elementor_activated() ){
            $disable = get_option('theme_disable_themeless', 'no'); ?>
            <div id="theme-disable-themeless">
                <div class="tabs-holder">
                    <div class="tab-nav">
                        <ul class="themekit-tabs">
                            <li class="active active-tab" data-tabid="dashing-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/dashing.svg" style="float: right;" width="40">
                                <span><?php _e('DASHING'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Customize WP Admin Areas'); ?></medium>
                                </p>
                            </li>
                            <li class="" data-tabid="themeless-tab">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/themeless.svg" style="float: right;" width="40">
                                <span><?php _e('THEMELESS'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Disable the WP Theme Framework'); ?></medium>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="content-tab">
                        <?php include_once 'theme-dashing.php'; ?>
                        <div class="single-tab" id="themeless-tab">
                            <div class="widget-panel-title"><?php _e("Disable the Theme Framework", "toolkit-for-elementor"); ?></div>
                            <?php _e('When this is enabled, the WP Theme Framework is disabled and your theme\'s CSS & JS files are not loaded. Simply use Elementor\'s <b>Theme Builder</b> (Header, Footer, Single, etc) and <b>Theme Styles</b> (Global Styles) to build lighter sites natively.'); ?><br /><br />
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = ($disable == 'yes') ? 'checked' : ''; ?>
                                            <input id="theme_disable_themeless" name="theme_disable_themeless" type="checkbox" value="yes" data-message="15" <?php echo $checked; ?>>
                                            <label for="theme_disable_themeless"></label>
                                        </div>
                                    </div>
                                    <?php _e('<b>Disable WP Theme Framework & Go Themeless</b><br /><br />For custom code snippets, consider using ToolKit\'s Code Manager or the <a href="https://wordpress.org/plugins/code-snippets/" target="_blank">Code Snippets plugin</a>.'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?php
        } else { ?>
            <div class="not-active-notice">
                <?php _e('Oops, looks like you do not have an active license yet, please activate your license first in My License'); ?>
            </div>
        <?php }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}
