<?php $update = new Toolkit_Elementor_Update_Manager();
$remove_opts = $update->get_remove_options();
$remember_opts = $update->get_remember_options();
$export_options = $update->get_export_optoins();
$export_opts = $update->get_export_options();
$dataHandling = get_option('toolkit_data_handling_options', array()); ?>
<div class="data-handling-section">
    <div class="widgets-tab-wrapper">
        <input type="radio" name="data-handling-tab" class="widgets-tab-radio" id="data-handling" checked>
        <input type="radio" name="data-handling-tab" class="widgets-tab-radio" id="import-export">
        <input type="radio" name="data-handling-tab" class="widgets-tab-radio" id="reset-settings">
        <div class="widget-tabs">
            <label class="widget-tab" id="data-handling-tab" for="data-handling"><?php _e("Data Handling", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="import-export-tab" for="import-export"><?php _e("Import/Export", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="reset-settings-tab" for="reset-settings"><?php _e("Reset Settings", "toolkit-for-elementor"); ?></label>
        </div>
        <div class="widgets-tab-panels">
            <div class="widget-panel" id="data-handling-panel">
                <div class="widget-panel-title"><?php _e("Plugin Data Handling", "toolkit-for-elementor"); ?></div>
                <table class="widefat">
                    <tbody>
                    <tr>
                        <th width="200"><b><?php _e("Upon Uninstalling ToolKit", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_upon_uninstal" name="toolkit_upon_uninstal" class="text-field" data-toggler="data-handling-options">
                                <?php foreach ($remove_opts as $key => $val){
                                    $selected = (isset($dataHandling['upon_uninstal']) && $dataHandling['upon_uninstal'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <?php $checked = (isset($dataHandling['upon_uninstal']) && $dataHandling['upon_uninstal'] == 'rem_some') ? 'checked' : ''; ?>
                    <tr class="data-handling-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                        <th><b><?php _e("Booster Settings", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_booster_uninstall" name="toolkit_booster_uninstall" class="text-field">
                                <?php foreach ($remember_opts as $key => $val){
                                    $selected = (isset($dataHandling['booster_uninstall']) && $dataHandling['booster_uninstall'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="data-handling-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                        <th><b><?php _e("Scan History", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_scan_history" name="toolkit_scan_history" class="text-field">
                                <?php foreach ($remember_opts as $key => $val){
                                    $selected = (isset($dataHandling['scan_history']) && $dataHandling['scan_history'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="data-handling-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                        <th><b><?php _e("Theme Settings", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_theme_uninstall" name="toolkit_theme_uninstall" class="text-field">
                                <?php foreach ($remember_opts as $key => $val){
                                    $selected = (isset($dataHandling['theme_uninstall']) && $dataHandling['theme_uninstall'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="data-handling-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                        <th><b><?php _e("Toolbox Settings", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_toolbox_uninstall" name="toolkit_toolbox_uninstall" class="text-field">
                                <?php foreach ($remember_opts as $key => $val){
                                    $selected = (isset($dataHandling['toolbox_uninstall']) && $dataHandling['toolbox_uninstall'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="data-handling-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                        <th><b><?php _e("License Data", "toolkit-for-elementor"); ?></b></th>
                        <td>
                            <select id="toolkit_license_uninstall" name="toolkit_license_uninstall" class="text-field">
                                <?php foreach ($remember_opts as $key => $val){
                                    $selected = (isset($dataHandling['license_uninstall']) && $dataHandling['license_uninstall'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" class="button toolkit-btn" id="save_data_handling"><?php _e("Save Settings", "toolkit-for-elementor"); ?></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="widget-panel" id="import-export-panel">
                <div class="widget-panel-title"><?php _e("Export Settings", "toolkit-for-elementor"); ?></div>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <table class="widefat">
                        <tbody>
                        <tr>
                            <th width="200"><b><?php _e("Export ToolKit Settings", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_upon_export" name="toolkit_upon_export" class="text-field" data-toggler="settings-export-options">
                                    <?php foreach ($export_options as $key => $val){
                                        echo "<option value='$key'>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <?php $checked = ''; ?>
                        <tr class="settings-export-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Export Booster Settings", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_booster_export" name="toolkit_booster_export" class="text-field">
                                    <?php foreach ($export_opts as $key => $val){
                                        echo "<option value='$key'>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="settings-export-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Export Theme Settings", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_theme_export" name="toolkit_theme_export" class="text-field">
                                    <?php foreach ($export_opts as $key => $val){
                                        echo "<option value='$key'>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="settings-export-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Export Toolbox Settings", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_toolbox_export" name="toolkit_toolbox_export" class="text-field">
                                    <?php foreach ($export_opts as $key => $val){
                                        echo "<option value='$key'>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="settings-export-options" <?php if(!$checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Export Data Handling", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_dhandle_export" name="toolkit_dhandle_export" class="text-field">
                                    <?php foreach ($export_opts as $key => $val){
                                        echo "<option value='$key'>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="action" value="toolkit_export_settings">
                                <button type="submit" class="button toolkit-btn" id="export_toolkit_settings"><?php _e("Export Settings", "toolkit-for-elementor"); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <hr>
                <div class="widget-panel-title"><?php _e("Import Settings", "toolkit-for-elementor"); ?></div>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <table class="widefat">
                        <tbody>
                        <tr>
                            <td>
                                <input type="file" name="toolkit_import_file" accept="application/json" required>
                            </td>
                            <td>
                                <input type="hidden" name="action" value="toolkit_import_settings">
                                <button type="submit" class="button toolkit-btn" id="import_toolkit_settings"><?php _e("Import Settings", "toolkit-for-elementor"); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="widget-panel" id="reset-settings-panel">
                <div class="widget-panel-title"><?php _e('Reset All Settings'); ?></div>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <input id="toolkit_reset_settings" name="toolkit_reset_settings" type="checkbox" value="1">
                                <label for="toolkit_reset_settings"></label>
                            </div>
                        </div>
                        <?php _e('<b>Reset All Settings</b><br><strong>Please Note:</strong> This change is irreversible and will wipe all settings back to factory default.'); ?>
                    </label>
                </div>
                <br>
                <div class="form-group">
                    <button type="button" class="button toolkit-btn" id="reset-settings-options"><?php _e('Save and Apply Options'); ?></button>
                </div>
            </div>
        </div>
    </div>

</div>