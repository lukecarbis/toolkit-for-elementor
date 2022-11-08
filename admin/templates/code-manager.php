<?php
$header_code = get_option('theme_disable_header_code', '');
$footer_code = get_option('theme_disable_footer_code', '');
$footer_wpcode = get_option('theme_disable_wpfooter_code', '');
$bodytag_code = get_option('theme_disable_bodytag_code', ''); ?>

<div class="widgets-tab-wrapper">
    <input type="radio" name="code-manager-tab" class="widgets-tab-radio" id="cmcode-header" checked>
    <input type="radio" name="code-manager-tab" class="widgets-tab-radio" id="cmcode-bodytag">
    <input type="radio" name="code-manager-tab" class="widgets-tab-radio" id="cmcode-footer">
    <input type="radio" name="code-manager-tab" class="widgets-tab-radio" id="cmcode-adfooter">
    <div class="widget-tabs">
        <label class="widget-tab" id="cmcode-header-tab" for="cmcode-header"><?php _e("Header", "toolkit-for-elementor"); ?></label>
        <label class="widget-tab" id="cmcode-bodytag-tab" for="cmcode-bodytag"><?php _e("After Opening Body", "toolkit-for-elementor"); ?></label>
        <label class="widget-tab" id="cmcode-footer-tab" for="cmcode-footer"><?php _e("Footer", "toolkit-for-elementor"); ?></label>
        <label class="widget-tab" id="cmcode-adfooter-tab" for="cmcode-adfooter"><?php _e("Admin Dashboard Footer", "toolkit-for-elementor"); ?></label>
    </div>
    <div class="widgets-tab-panels">
        <div class="widget-panel" id="cmcode-header-panel">
            <div class="widget-panel-title"><?php _e('Insert '.'Code into Header'); ?></div>
            <p class="m-0"><?php _e('You can use Code Manager to insert custom into various areas of your site.'); ?></p>
            <table class="widefat">
                <tr>
                    <td>
                        <label for="theme_disable_header"><b><?php _e('Insert'.' Code Into Header'); ?></b></label>
                        <br>
                        <textarea rows="8" class="wd-100" id="theme_disable_header"><?php echo str_replace('\"','"', str_replace("\\'",'"', $header_code)); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="right-align">
                        <button type="button" class="button toolkit-btn save-toolkit"><?php _e('Save Code'); ?></button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="widget-panel" id="cmcode-bodytag-panel">
            <div class="widget-panel-title"><?php _e('Insert '.'Code after Opening Body Tag'); ?></div>
            <p class="m-0"><?php _e('You can use Code Manager to insert custom into various areas of your site.'); ?></p>
            <table class="widefat">
                <tr>
                    <td>
                        <label for="theme_disable_bodytag"><b><?php _e('Insert'.' Code After Opening Body Tag'); ?></b></label>
                        <br>
                        <textarea rows="8" class="wd-100" id="theme_disable_bodytag"><?php echo str_replace('\"','"', str_replace("\\'",'"', $bodytag_code)); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="right-align">
                        <button type="button" class="button toolkit-btn save-toolkit"><?php _e('Save Code'); ?></button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="widget-panel" id="cmcode-footer-panel">
            <div class="widget-panel-title"><?php _e('Insert '.'Code into Footer'); ?></div>
            <p class="m-0"><?php _e('You can use Code Manager to insert custom into various areas of your site.'); ?></p>
            <table class="widefat">
                <tr>
                    <td>
                        <label for="theme_disable_footer"><b><?php _e('Insert'.' Code Into Footer'); ?></b></label>
                        <br>
                        <textarea rows="8" class="wd-100" id="theme_disable_footer"><?php echo str_replace('\"','"', str_replace("\\'",'"', $footer_code)); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="right-align">
                        <button type="button" class="button toolkit-btn save-toolkit"><?php _e('Save Code'); ?></button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="widget-panel" id="cmcode-adfooter-panel">
            <div class="widget-panel-title"><?php _e('Insert '.'Code into Admin Dashboard Footer'); ?></div>
            <p class="m-0"><?php _e('You can use Code Manager to insert custom into various areas of your site.'); ?></p>
            <table class="widefat">
                <tr>
                    <td>
                        <label for="theme_disable_wpfooter"><b><?php _e('Insert'.' Code Into WP Admin Dashboard Footer (Great for Live Chat Widgets & Services)'); ?></b></label>
                        <br>
                        <textarea rows="8" class="wd-100" id="theme_disable_wpfooter"><?php echo str_replace('\"','"', str_replace("\\'",'"', $footer_wpcode)); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="right-align">
                        <button type="button" class="button toolkit-btn save-toolkit"><?php _e('Save Code'); ?></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>