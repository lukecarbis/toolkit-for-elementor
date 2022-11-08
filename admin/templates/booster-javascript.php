<?php $settingServer = get_option('toolkit_elementor_tweaks', array());
$delayDefVal = "/app/js/api.min.js
addtoany
fbevents.js
fbq(
getbutton.io
livechatinc.com/tracking.js
LiveChatWidget
pinit.js
pixel-caffeine
platform.twitter.com/widgets.js
shareaholic
sharethis
simple-share-buttons-adder
snap.licdn.com/li.lms-analytics/insight.min.js
static.ads-twitter.com/uwt.js
twq(
wpdiscuz
xfbml.customerchat.js";
?>
<div class="single-tab" id="js-minify-tab">
    <div class="minification-setting-section">
        <div class="widgets-tab-wrapper">
            <input type="radio" name="jscode-manager-tab" class="widgets-tab-radio" id="jscode-minify" checked>
            <input type="radio" name="jscode-manager-tab" class="widgets-tab-radio" id="jscode-defer">
            <input type="radio" name="jscode-manager-tab" class="widgets-tab-radio" id="jscode-delay">
            <div class="widget-tabs">
                <label class="widget-tab" id="jscode-minify-tab" for="jscode-minify"><?php _e("Minify & Combine", "toolkit-for-elementor"); ?></label>
                <label class="widget-tab" id="jscode-defer-tab" for="jscode-defer"><?php _e("Defer", "toolkit-for-elementor"); ?></label>
                <label class="widget-tab" id="jscode-delay-tab" for="jscode-delay"><?php _e("Delay", "toolkit-for-elementor"); ?></label>
            </div>
            <div class="widgets-tab-panels">
                <div class="widget-panel" id="jscode-minify-panel">
                    <div class="widget-panel-title"><?php _e('JS Minification Settings'); ?></div>
                    <div class="controls-section">
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($settingServer['js_minify']) && $settingServer['js_minify'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_js_minify" name="toolkit_js_minify" type="checkbox" value="1" data-message="11" <?php echo $checked; ?>>
                                        <label for="toolkit_js_minify"></label>
                                    </div>
                                </div>
                                <b><?php _e('Minify Javascript'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Minifying JS can help optimize files by removing whitespace in your code and reducing the overall file size.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/minify-combine-css-and-javascript/" target="_blank">Learn More</a></span></span>
                            </label>
                        </div>
                        <br/>
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = ($checked && isset($settingServer['js_combine']) && $settingServer['js_combine'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_js_combine" name="toolkit_js_combine" type="checkbox" value="1" data-message="13" data-toggler="toolkit-jscom-options" <?php echo $checked; ?>>
                                        <label for="toolkit_js_combine"></label>
                                    </div>
                                </div>
                                <b><?php _e('Combine Javascript'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Combining your JS files can help speed up download time by reducing the number of server requests made. However, if your site is on a server using <a href="https://http2.github.io/faq/" target="_blank">HTTP/2</a>, then \'Combining JS\' isn\'t recommended due to the <a href="https://http2.github.io/faq/#what-are-the-key-differences-to-http1x" target="_blank">multi-threading capabilities</a> in HTTP/2. Instead of combining, we recommend deferring, delaying, or dequeuing unneccessary JS files. However, since every site is different, feel free to test all options to see which works best for you.'); ?></span></span>
                            </label>
                        </div><br/>
                        <div class="toolkit-jscom-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = (isset($settingServer['js_excelem']) && $settingServer['js_excelem'] == 'on') || ! isset($settingServer['js_excelem']) ? 'checked' : ''; ?>
                                            <input id="toolkit_js_excelem" name="toolkit_js_excelem" type="checkbox" value="1" data-message="15" <?php echo $checked; ?>>
                                            <label for="toolkit_js_excelem"></label>
                                        </div>
                                    </div>
                                    <b><?php _e('Exclude Elementor Core Files'); ?></b>
                                </label>
                            </div>
                        </div>
                        <div class="checkbox">
                            <label for="excluded-js-urls"><b><?php _e("Exclude Files From Being Minified or Combined"); ?></b></label>
                            <br/>
                            <p class="m-0"><?php _e("Add each url in single line"); ?></p>
                            <table class="widefat">
                                <tr>
                                    <td>
                                        <textarea rows="3" class="wd-100" id="excluded-js-urls" placeholder="file-name.js"><?php echo isset($settingServer['exclude_js_urls']) ? $settingServer['exclude_js_urls'] : ''; ?></textarea>
                                    </td>
                                    <td class="wd100">
                                        <button type="button" class="button toolkit-btn save-javascript-settings" data-message="17"><?php _e('Update'); ?></button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <p><?php _e('As WordPress core already includes minified JS, ToolKit ignores JS files that are already minified such as <code>/wp-includes/js/jquery-migrate.min.js</code>'); ?></p>
                    </div>
                </div>
                <div class="widget-panel" id="jscode-defer-panel">
                    <div class="widget-panel-title"><?php _e('JS Defer Settings'); ?></div>
                    <div class="controls-section">
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($settingServer['js_defer']) && $settingServer['js_defer'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_js_defer" name="toolkit_js_defer" type="checkbox" value="1" data-message="18" data-toggler="toolkit-defer-options" <?php echo $checked; ?>>
                                        <label for="toolkit_js_defer"></label>
                                    </div>
                                </div>
                                <b><?php _e('Defer Javascript'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Enabling this can help reduce Render Blocking JS, however test with caution as some plugins may need certain JS files.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/remove-render-blocking-resources-with-defer-js/" target="_blank">Learn More</a></span></span>
                            </label>
                        </div>
                        <br/>
                        <div class="toolkit-defer-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = (isset($settingServer['defer_homeonly']) && $settingServer['defer_homeonly'] == 'on' && ! $checked) ? 'checked' : ''; ?>
                                            <input id="toolkit_defer_homeonly" name="toolkit_defer_homeonly" type="checkbox" value="1" data-message="24" <?php echo $checked; ?>>
                                            <label for="toolkit_defer_homeonly"></label>
                                        </div>
                                    </div>
                                    <b><?php _e('Defer JS on Homepage Only'); ?></b>
                                </label>
                            </div>
                            <br/>
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = (isset($settingServer['jsdefer_inline']) && $settingServer['jsdefer_inline'] == 'on') ? 'checked' : ''; ?>
                                            <input id="toolkit_jsdefer_inline" name="toolkit_jsdefer_inline" type="checkbox" value="1" data-message="20" <?php echo $checked; ?>>
                                            <label for="toolkit_jsdefer_inline"></label>
                                        </div>
                                    </div>
                                    <b><?php _e('Defer Inline JavaScript'); ?></b>
                                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Execute inline scripts only after HTML is parsed and other scripts are ready.'); ?></span></span>
                                </label>
                            </div>
                            <br/>
                            <div class="checkbox">
                                <label for="deferred_keywords"><b><?php _e("Exclude JS Files From Being Deferred"); ?></b></label>
                                <br/>
                                <p class="m-0"><?php _e("Add each keyword or file url on a separate line"); ?></p>
                                <table class="widefat">
                                    <tr>
                                        <td>
                                            <textarea rows="8" class="wd-100" id="deferred_keywords" placeholder="file-name.js"><?php echo isset($settingServer['deferred_keywords']) ? $settingServer['deferred_keywords'] : ''; ?></textarea>
                                        </td>
                                        <td class="wd100">
                                            <button type="button" class="button toolkit-btn save-javascript-settings" data-message="26"><?php _e('Update'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br/>
                        </div>
                    </div>
                </div>
                <div class="widget-panel" id="jscode-delay-panel">
                    <div class="widget-panel-title"><?php _e('JS Delay Settings'); ?></div>
                    <div class="controls-section">
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($settingServer['js_delay']) && $settingServer['js_delay'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_js_delay" name="toolkit_js_delay" type="checkbox" value="1" data-message="27" data-toggler="toolkit-delay-options" <?php echo $checked; ?>>
                                        <label for="toolkit_js_delay"></label>
                                    </div>
                                </div>
                                <b><?php _e('Delay Javascript'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('This will delay the loading of your specified JS files until any visitor interaction is achieved (such as scroll, click, mouse movement, keyboard action, etc). We\'ve included a starting set of well-known JS scripts that can be optimized.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/delay-javascript/">Learn More</a></span></span>
                            </label>
                        </div><br/>
                        <div class="toolkit-delay-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                            <div class="widgets-tab-wrapper">
                                <input type="radio" name="jsdelay-manager-tab" class="widgets-tab-radio" id="jsdelay-homepage" checked>
                                <input type="radio" name="jsdelay-manager-tab" class="widgets-tab-radio" id="jsdelay-globally">
                                <input type="radio" name="jsdelay-manager-tab" class="widgets-tab-radio" id="jsdelay-globaldis">
                                <div class="widget-tabs">
                                    <label class="widget-tab" id="jsdelay-homepage-tab" for="jsdelay-homepage"><?php _e("Homepage", "toolkit-for-elementor"); ?></label><label class="widget-tab" id="jsdelay-globally-tab" for="jsdelay-globally"><?php _e("Globally", "toolkit-for-elementor"); ?></label><label class="widget-tab" id="jsdelay-globaldis-tab" for="jsdelay-globaldis"><?php _e("Exclude Pages", "toolkit-for-elementor"); ?></label>
                                </div>
                                <div class="widgets-tab-panels">
                                    <div class="widget-panel" id="jsdelay-homepage-panel">
                                        <div class="checkbox">
                                            <label for="delayed-hkeywords"><b><?php _e("Delay JS Files by URL or Keyword"); ?></b></label>
                                            <br/>
                                            <p class="m-0"><?php _e("Add each keyword or file url on a separate line"); ?></p>
                                            <table class="widefat">
                                                <tr>
                                                    <td>
                                                        <textarea rows="8" class="wd-100" id="delayed-hkeywords" placeholder="file-name.js"><?php echo isset($settingServer['delayed_hkeywords']) ? str_replace("\\'", "'", $settingServer['delayed_hkeywords']) : ''; ?></textarea>
                                                    </td>
                                                    <td class="wd100">
                                                        <button type="button" class="button toolkit-btn save-javascript-settings" data-message="29"><?php _e('Update'); ?></button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br/>
                                    </div>
                                    <div class="widget-panel" id="jsdelay-globally-panel">
                                        <div class="checkbox">
                                            <label for="delayed-keywords"><b><?php _e("Delay JS Files by URL or Keyword"); ?></b></label>
                                            <br/>
                                            <p class="m-0"><?php _e("Add each keyword or file url on a separate line"); ?></p>
                                            <table class="widefat">
                                                <tr>
                                                    <td>
                                                        <textarea rows="8" class="wd-100" id="delayed-keywords" placeholder="file-name.js"><?php echo isset($settingServer['delayed_keywords']) ? str_replace("\\'", "'", $settingServer['delayed_keywords']) : $delayDefVal; ?></textarea>
                                                    </td>
                                                    <td class="wd100">
                                                        <button type="button" class="button toolkit-btn save-javascript-settings" data-message="29"><?php _e('Update'); ?></button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br/>
                                    </div>
                                    <div class="widget-panel" id="jsdelay-globaldis-panel">
                                        <div class="checkbox">
                                            <label for="delayed-expages"><b><?php _e("Disable On Selected Pages"); ?></b></label>
                                            <br/>
                                            <p class="m-0"><?php _e("Add each page url on a separate line"); ?></p>
                                            <table class="widefat">
                                                <tr>
                                                    <td>
                                                        <textarea rows="8" class="wd-100" id="delayed-expages" placeholder="/checkout"><?php echo isset($settingServer['delayed_expages']) ? str_replace("\\'", "'", $settingServer['delayed_expages']) : ''; ?></textarea>
                                                    </td>
                                                    <td class="wd100">
                                                        <button type="button" class="button toolkit-btn save-javascript-settings" data-message="29"><?php _e('Update'); ?></button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
