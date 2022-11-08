<?php
$settingServer = get_option('toolkit_elementor_tweaks', array());
?>
<div class="single-tab" id="css-minify-tab">
    <div class="minification-setting-section">
        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>"/>
        <div class="widget-panel-title"><?php _e('Minification Settings'); ?></div>
        <div class="controls-section">
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($settingServer['css_minify']) && $settingServer['css_minify'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_css_minify" name="toolkit_css_minify" type="checkbox" value="1" data-message="4" <?php echo $checked; ?>>
                            <label for="toolkit_css_minify"></label>
                        </div>
                    </div>
                    <b><?php _e('Minify CSS'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Minifying CSS can help optimize files by removing whitespace in your code and reducing the overall file size.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/minify-combine-css-and-javascript/" target="_blank">Learn More</a></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = ($checked && isset($settingServer['css_combine']) && $settingServer['css_combine'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_css_combine" name="toolkit_css_combine" type="checkbox" value="1" data-message="6" data-toggler="toolkit-csscom-options" <?php echo $checked; ?>>
                            <label for="toolkit_css_combine"></label>
                        </div>
                    </div>
                    <b><?php _e('Combine CSS'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Combining CSS files can help speed up downloading, parsing, and load time by reducing the number of server requests made. However, if your site is on a server using <a href="https://http2.github.io/faq/" target="_blank">HTTP/2</a>, then \'Combining CSS\' is not recommended due to the <a href="https://http2.github.io/faq/#what-are-the-key-differences-to-http1x" target="_blank">multi-threading capabilities</a> in HTTP/2. Instead of combining CSS, we recommend delaying or unloading unneccessary CSS files. However, since every site is different, feel free to test all options to see which works best for you.'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="toolkit-csscom-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($settingServer['css_excelem']) && $settingServer['css_excelem'] == 'on') || ! isset($settingServer['css_excelem']) ? 'checked' : ''; ?>
                                <input id="toolkit_css_excelem" name="toolkit_css_excelem" type="checkbox" value="1" data-message="8" <?php echo $checked; ?>>
                                <label for="toolkit_css_excelem"></label>
                            </div>
                        </div>
                        <b><?php _e('Exclude Elementor Core Files'); ?></b>
                    </label>
                </div>
            </div>
            <div class="checkbox">
                <label for="excluded-css-urls"><b><?php _e("Exclude Files From Being Minified or Combined"); ?></b></label>
                <br/>
                <p class="m-0"><?php _e("Add each file url in single line"); ?></p>
                <table class="widefat">
                    <tr>
                        <td>
                            <textarea rows="3" class="wd-100" id="excluded-css-urls" placeholder="/file-name.css"><?php echo isset($settingServer['exclude_css_urls']) ? str_replace("\\'", "'", $settingServer['exclude_css_urls']) : ''; ?></textarea>
                        </td>
                        <td class="wd100">
                            <button type="button" class="button toolkit-btn save-cssminify-settings" data-message="10"><?php _e('Update'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
            <p><?php _e('As WordPress core already includes minified CSS, ToolKit ignores CSS files that are already minified such as <code>/wp-includes/css/admin-bar.min.css</code>'); ?></p>
            <br/>
            <div class="checkbox">
                <label for="lazy-render-words"><b><?php _e("Lazy Elements"); ?></b>
                <span class="tooltip">i<span class="tooltiptext"><?php _e('Lazy Elements works similarly to lazy loading images. Improve TBT, LCP, and Time to Interactive by lazy rendering HTML elements below the fold.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/lazy-elements/" target="_blank">Learn More</a></span></span>
                </label>
                <br/>
                <p class="m-0"><?php _e("Add each CSS selector in single line"); ?></p>
                <table class="widefat">
                    <tr>
                        <td>
                            <textarea rows="3" class="wd-100" id="lazy-render-words" placeholder="Example #comments"><?php echo isset($settingServer['lazy_render']) ? str_replace("\\'", "'", $settingServer['lazy_render']) : ''; ?></textarea>
                        </td>
                        <td class="wd100">
                            <button type="button" class="button toolkit-btn save-cssminify-settings" data-message="35"><?php _e('Update'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
