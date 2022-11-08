<?php $dnsSettings = get_option('toolkit_prefetch_dns_options', array()); ?>
<div class="single-tab" id="prefetch-dns-tab">
    <div class="minification-setting-section widget-panel" style="display: block">
        <div class="widget-panel-title"><?php _e('Prefetch DNS Settings'); ?></div>
        <div class="controls-section">
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($dnsSettings['pre_dns']) && $dnsSettings['pre_dns'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_pre_dns" name="toolkit_pre_dns" type="checkbox" value="1" data-message="0" data-toggler="toolkit-prefetch-options" <?php echo $checked; ?>>
                            <label for="toolkit_pre_dns"></label>
                        </div>
                    </div>
                    <b>Prefetch DNS</b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('By Prefetching an external domain, browsers can begin connecting and downloading a resource faster. This helps improve load and render times. The Lighthouse recommendation "Preconnect to Required Origins" will list external domains that you can copy/paste here.'); ?><br/><a href="https://web.dev/uses-rel-preconnect" target="_blank">Learn More</a></span></span>
                </label>
            </div><br/>
            <div class="toolkit-prefetch-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                <div class="widgets-tab-wrapper">
                    <input type="radio" name="prefetch-dns-tab" class="widgets-tab-radio" id="prefetch-globally" checked>
                    <input type="radio" name="prefetch-dns-tab" class="widgets-tab-radio" id="prefetch-homepage">
                    <input type="radio" name="prefetch-dns-tab" class="widgets-tab-radio" id="prefetch-globaldis">
                    <div class="widget-tabs">
                        <label class="widget-tab" id="prefetch-globally-tab" for="prefetch-globally"><?php _e("Globally", "toolkit-for-elementor"); ?></label><label class="widget-tab" id="prefetch-homepage-tab" for="prefetch-homepage"><?php _e("Homepage Only", "toolkit-for-elementor"); ?></label><label class="widget-tab" id="prefetch-globaldis-tab" for="prefetch-globaldis"><?php _e("Exclude Pages", "toolkit-for-elementor"); ?></label>
                    </div>
                    <div class="widgets-tab-panels">
                        <div class="widget-panel" id="prefetch-globally-panel">
                            <div class="checkbox">
                                <label for="prefetch-keywords"><b><?php _e("Prefetch External Hosts"); ?></b></label>
                                <br/>
                                <p class="m-0"><?php _e("Add each host on a separate line. No <code>http:</code> or <code>https:</code> needed"); ?></p>
                                <table class="widefat">
                                    <tr>
                                        <td>
                                            <textarea rows="8" class="wd-100" id="prefetch-keywords" placeholder="//example.com"><?php echo isset($dnsSettings['keywords']) ? str_replace("\\'", "'", $dnsSettings['keywords']) : ''; ?></textarea>
                                        </td>
                                        <td class="wd100">
                                            <button type="button" class="button toolkit-btn save-prefetch-settings" data-message="2"><?php _e('Update'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br/>
                        </div>
                        <div class="widget-panel" id="prefetch-homepage-panel">
                            <div class="checkbox">
                                <label for="prefetch-hkeywords"><b><?php _e("Prefetch External Hosts"); ?></b></label>
                                <br/>
                                <p class="m-0"><?php _e("Add each host on a separate line. No <code>http:</code> or <code>https:</code> needed"); ?></p>
                                <table class="widefat">
                                    <tr>
                                        <td>
                                            <textarea rows="8" class="wd-100" id="prefetch-hkeywords" placeholder="//example.com"><?php echo isset($dnsSettings['hkeywords']) ? str_replace("\\'", "'", $dnsSettings['hkeywords']) : ''; ?></textarea>
                                        </td>
                                        <td class="wd100">
                                            <button type="button" class="button toolkit-btn save-prefetch-settings" data-message="2"><?php _e('Update'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br/>
                        </div>
                        <div class="widget-panel" id="prefetch-globaldis-panel">
                            <div class="checkbox">
                                <label for="prefetch-expages"><b><?php _e("Disable On Selected Pages"); ?></b></label>
                                <br/>
                                <p class="m-0"><?php _e("Add each page URL on a separate line in order to exclude from prefetching."); ?></p>
                                <table class="widefat">
                                    <tr>
                                        <td>
                                            <textarea rows="8" class="wd-100" id="prefetch-expages" placeholder="/checkout"><?php echo isset($dnsSettings['expages']) ? str_replace("\\'", "'", $dnsSettings['expages']) : ''; ?></textarea>
                                        </td>
                                        <td class="wd100">
                                            <button type="button" class="button toolkit-btn save-prefetch-settings" data-message="2"><?php _e('Update'); ?></button>
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
