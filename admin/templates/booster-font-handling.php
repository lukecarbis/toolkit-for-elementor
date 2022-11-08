<?php $settingServer = get_option('toolkit_elementor_tweaks', array());
$elementorFont = get_option( 'elementor_font_display', 'auto' ); ?>
<div class="single-tab" id="fonts-minify-tab">
    <div class="minification-setting-section">
        <div class="widget-panel-title"><?php _e('Font Handling & Optimization'); ?></div>
        <br />
        <div class="controls-section">
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($settingServer['google_fonts']) && $settingServer['google_fonts'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_google_fonts" name="toolkit_google_fonts" type="checkbox" value="1" data-message="30" <?php echo $checked; ?>>
                            <label for="toolkit_google_fonts"></label>
                        </div>
                    </div>
                    <b><?php _e('Optimize Google Fonts'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Enabling this will locally host and optimize your Google Fonts when possible. This can help solve the common recommendation "Add Expires Headers" for your fonts.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/font-handling-optimization/" target="_blank">Learn More</a></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($settingServer['fallback_fonts']) && $settingServer['fallback_fonts'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_fallback_fonts" name="toolkit_fallback_fonts" type="checkbox" value="1" data-message="32" <?php echo $checked; ?>>
                            <label for="toolkit_fallback_fonts"></label>
                        </div>
                    </div>
                    <b><?php _e('Enable Fallback Fonts'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('When using Google Fonts, browsers normally wait before displaying text until those font files have been downloaded. This creates a Flash of Invisible Text or FOIT issue. When this happens, Google Lighthouse will display a recommendation to "Ensure Text Remains Visible During Webfont Load".<br/><br/>ToolKit solves this by instructing the browser to display a fallback system font until your Google Fonts are ready for display. This is accomplished by applying the `font-display: swap` to the proper font CSS rules. This can help improve CLS scores in Lighthouse by reducing layout shift while the font is being rendered in the browser.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/font-handling-optimization/#fallbackfonts" target="_blank">Learn More</a></span></span>
                </label>
            </div>
            <br/>
            <?php if( $elementorFont == 'swap' ){ ?>
                <div class="checkbox">
                    <label class="same-site"><?php _e('<b>Font Display Settings in Elementor Detected</b><br/>We noticed that Font Display settings in Elementor have already been enabled. If you also want to optimize all detected Google fonts instead of just the ones used by Elementor, ToolKit can handle this instead.'); ?></label>
                </div>
                <br/>
            <?php } ?>
            <div class="checkbox">
                <label for="toolkit_preload_fonts">
                    <b><?php _e("Preload Fonts for Faster Load Times"); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Preloading fonts can help improve load times by instructing the browser to begin downloading font files that it normally has to parse CSS rules to find. Google Lighthouse recommends "Preloading Key Requests" to reduce load times- and very often, you\'ll find your above the fold fonts listed in here. A preloaded font will have a better chance to meet the first paint, in which case there\'s no layout shifting- thus better CLS scores.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/font-handling-optimization/#preloading" target="_blank">Learn More</a></span></span>
                </label>
                <br/><br/>
                <p class="m-0"><?php _e('Copy/Paste those files below to Preload'); ?></p>
                <table class="widefat">
                    <tr>
                        <td>
                            <textarea rows="3" class="wd-100" id="toolkit_preload_fonts" placeholder="/wp-content/themes/your-theme/assets/fonts/font-file.woff"><?php echo isset($settingServer['preload_fonts']) ? $settingServer['preload_fonts'] : ''; ?></textarea>
                        </td>
                        <td class="wd100">
                            <button type="button" class="button toolkit-btn save-fonts-settings" data-message="34"><?php _e('Update'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
