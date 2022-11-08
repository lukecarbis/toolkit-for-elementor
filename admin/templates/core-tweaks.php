<?php
$bgTasks = new Toolkit_Elementor_BgTasks();
$coreTweaks = get_option('toolkit_core_tweaks_options', array());
?>
<div class="wp-core-tweaks">
    <div class="widgets-tab-wrapper">
        <input type="radio" name="wpcore-tweaks-tab" class="widgets-tab-radio" id="core-tweaks" checked>
        <input type="radio" name="wpcore-tweaks-tab" class="widgets-tab-radio" id="auto-updates">
        <input type="radio" name="wpcore-tweaks-tab" class="widgets-tab-radio" id="bground-tasks">
        <div class="widget-tabs">
            <label class="widget-tab" id="core-tweaks-tab" for="core-tweaks"><?php _e("WP Core Tweaks", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="auto-updates-tab" for="auto-updates"><?php _e("Automatic Updates", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="bground-tasks-tab" for="bground-tasks"><?php _e("Background Tasks", "toolkit-for-elementor"); ?></label>
        </div>
        <div class="widgets-tab-panels">
            <div class="widget-panel" id="core-tweaks-panel">
		        <div class="widget-panel-title"><?php _e('Modify WP Core Settings'); ?></div>
		        <?php _e('As of WordPress v5.5, there are new features such as Default Sitemaps, and Automatic Updates that users may want more control over. ToolKit allows you to modify these new features in WP Core Manager. '); ?><br /><br />
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($coreTweaks['disable_sitemap']) && $coreTweaks['disable_sitemap'] == 'yes') ? 'checked' : ''; ?>
                                <input id="toolkit_disable_sitemap" name="toolkit_disable_sitemap" type="checkbox" value="1" data-message="0" <?php echo $checked; ?>>
                                <label for="toolkit_disable_sitemap"></label>
                            </div>
                        </div>
                        <?php _e('<b>Disable WP v5.5\'s New Default Sitemap Generation</b><br>As of v5.5, WordPress now generates it\'s own sitemaps. However if you are already using an SEO plugin such as Rankmath, SEOPress, or Yoast, those plugins create and manage their own sitemaps. Enabling this will dequeue and disable the WP-generated sitemap so you can continue to use your currently existing one.'); ?>
                    </label>
                </div>
            </div>
            <div class="widget-panel" id="auto-updates-panel">
		        <div class="widget-panel-title"><?php _e('Disable Automatic Updates'); ?></div>
	    	    <?php _e('As of WordPress v5.5, Automatic Updates are now part of WP core. It is not recommended to auto-enable auto-updates on production sites due to the potential of introducing site-breaking bugs.<br /><br />ToolKit makes it easy to Disable Automatic Updates for WordPress Core, Themes, and Plugins- simply enable the toggles below to disable updates based on your preference.'); ?><br /><br />
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($coreTweaks['wpcore']) && $coreTweaks['wpcore'] == 'yes') ? 'checked' : ''; ?>
                                <input id="toolkit_disable_wpcore" name="toolkit_disable_wpcore" type="checkbox" value="1" data-message="2" <?php echo $checked; ?>>
                                <label for="toolkit_disable_wpcore"></label>
                            </div>
                        </div>
                        <?php _e('<b>WordPress Core</b>'); ?>
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($coreTweaks['plugin']) && $coreTweaks['plugin'] == 'yes') ? 'checked' : ''; ?>
                                <input id="toolkit_disable_plugin" name="toolkit_disable_plugin" type="checkbox" value="1" data-message="4" <?php echo $checked; ?>>
                                <label for="toolkit_disable_plugin"></label>
                            </div>
                        </div>
                        <?php _e('<b>Plugins</b>'); ?>
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($coreTweaks['themes']) && $coreTweaks['themes'] == 'yes') ? 'checked' : ''; ?>
                                <input id="toolkit_disable_themes" name="toolkit_disable_themes" type="checkbox" value="1" data-message="6" <?php echo $checked; ?>>
                                <label for="toolkit_disable_themes"></label>
                            </div>
                        </div>
                        <?php _e('<b>Themes</b>'); ?>
                    </label>
                </div>
            </div>
            <div class="widget-panel" id="bground-tasks-panel">
                <?php include 'background-tasks.php'; ?>
            </div>
        </div>
    </div>
</div>
