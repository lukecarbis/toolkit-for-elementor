<?php $settingServer = get_option('toolkit_elementor_tweaks', array());
$fileOpts = array(
    ''      => __('Select File Type'),
    'all'   => __('All Files'),
    'jscss' => __('JS & CSS'),
    'js'    => __('JS Only'),
    'css'   => __('CSS Only'),
    'font'  => __('Font Files Only'),
    'img'   => __('Images Only')
); ?>
<div class="single-tab" id="cdn-minify-tab">
    <div class="minification-setting-section">
        <div class="widget-panel-title"><?php _e('CDN Settings'); ?></div>
        <div class="controls-section">
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($settingServer['cdn_enable']) && $settingServer['cdn_enable'] == 'yes') ? 'checked' : ''; ?>
                            <input id="toolkit_cdn_enable" name="toolkit_cdn_enable" type="checkbox" value="1" data-message="0" data-toggler="toolkit-cdn-options" <?php echo $checked; ?>>
                            <label for="toolkit_cdn_enable"></label>
                        </div>
                    </div>
                    <b><?php _e('Enable Content Delivery Network (CDN)'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Enabling this will enable support for your CDN URL, and rewrite all static asset URL\'s to be delivered via the CDN. This is not needed for DNS-level services like Cloudflare or Sucuri.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/cdn/">Learn More</a></span></span>
                </label>
            </div>
        </div>
        <br/>
        <div class="toolkit-cdn-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
            <table class="widefat" id="cdn-holder-table">
                <thead>
                <tr>
                    <td colspan="5">
                        <b><?php _e('Specify CDNs'); ?></b>
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php if( isset($settingServer['cdn_url']) && $settingServer['cdn_url'] && is_array($settingServer['cdn_url']) ){
                    $cdn_files = (isset($settingServer['cdn_files']) && is_array($settingServer['cdn_files'])) ? $settingServer['cdn_files'] : array();
                    foreach ($settingServer['cdn_url'] as $i => $item) { ?>
                        <tr>
                            <td class="wd300"><input type="text" name="toolkit_cdn_url[]" class="text-field" value="<?php echo $item; ?>" placeholder="https://cdn.example.com"></td>
                            <td class="wd100"><?php _e('should serve'); ?></td>
                            <td class="wd300">
                                <select name="toolkit_cdn_files[]" class="text-field">
                                    <?php if( $fileOpts ){
                                        foreach ($fileOpts as $key => $value){
                                            $selected = (isset($cdn_files[$i]) && $cdn_files[$i] == $key) ? 'selected' : '';
                                            echo "<option value='$key' $selected>$value</option>";
                                        }
                                    } ?>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="button toolkit-btn save-cdn-settings" data-message="2"><?php _e('Update'); ?></button>
                            </td>
                            <?php if( $i > 0 ){ ?>
                                <td><span class="dashicons dashicons-no"></span></td>
                            <?php } ?>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td class="wd300"><input type="text" name="toolkit_cdn_url[]" class="text-field" placeholder="https://cdn.example.com"></td>
                        <td class="wd100"><b><?php _e('should serve'); ?></b></td>
                        <td class="wd300">
                            <select name="toolkit_cdn_files[]" class="text-field">
                                <?php if( $fileOpts ){
                                    foreach ($fileOpts as $key => $value){
                                        echo "<option value='$key'>$value</option>";
                                    }
                                } ?>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="button toolkit-btn save-cdn-settings" data-message="2"><?php _e('Update'); ?></button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <button type="button" id="add-more-cdn" class="button toolkit-btn"><?php _e('Add Another CDN'); ?></button>
                    </td>
                </tr>
                </tfoot>
            </table>
            <br/>
            <div class="checkbox">
                <label for="excluded-cdn-urls"><b><?php _e("Exclude Files from CDN Delivery"); ?></b></label>
                <br/>
                <p class="m-0"><?php _e("Add each url in single line"); ?></p>
                <table class="widefat">
                    <tr>
                        <td>
                            <textarea rows="3" class="wd-100" id="excluded-cdn-urls" placeholder="/file-name"><?php echo isset($settingServer['exclude_cdn_urls']) ? $settingServer['exclude_cdn_urls'] : ''; ?></textarea>
                        </td>
                        <td class="wd100">
                            <button type="button" class="button toolkit-btn save-cdn-settings" data-message="3"><?php _e('Update'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
            <br/><br/>
        </div>
    </div>
</div>
