<?php $cacheSetting = get_option('toolkit_elementor_cache_settings', array());
$post_types = get_post_types(['public'=>true], 'all');
if( $post_types ){
    $purgeOpts = array(
        'all'   => __('All Post Types')
    );
    foreach ($post_types as $post_type) {
        $purgeOpts[$post_type->name] = $post_type->label.__(' Only');
    }
    $purgeOpts['none'] = __('None');
} else {
    $purgeOpts = array(
        'all'   => __('All Post Types'),
        'page'  => __('Pages Only'),
        'none'  => __('None')
    );
}
$lifeOpts = array(
    'quarterdaily'  => __('6 Hours'),
    'twicedaily'    => __('12 Hours'),
    'daily'         => __('24 Hours'),
    'weekly'        => __('Weekly'),
    'none'          => __('Never')
);
$preOpts = array(
    'cache'     => __('Cache Lifespan'),
    'daily'     => __('Daily'),
    'weekly'    => __('Weekly'),
    'monthly'   => __('Monthly')
);
$preloadMeta = get_option('toolkit_preload_cache_meta', array()); ?>
<div class="single-tab" id="cache-manager-tab">
    <div class="minification-setting-section">
        <div class="widget-panel-title"><?php _e('Cache Manager'); ?></div>
        <div class="controls-section">
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($cacheSetting['cache_pages']) && $cacheSetting['cache_pages'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_cache_pages" name="toolkit_cache_pages" type="checkbox" value="1" data-message="0" data-toggler="toolkit-cache-options" <?php echo $checked; ?>>
                            <label for="toolkit_cache_pages"></label>
                        </div>
                    </div>
                    <b><?php _e('Enable Cache'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('ToolKit will create and serve a cached version of your site\'s pages. This may not be needed if your site is already being cached at the server or DNS level.'); ?><br/><a href="https://toolkitforelementor.com/topics/booster/cache-manager/" target="_blank">Learn More</a></span></span>
                </label>
            </div>
            <br/>
            <div class="toolkit-cache-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                <div class="checkbox">
                    <label for="toolkit_cache_purge">
                        <b><?php _e('Automatic Cache Clearing'); ?></b>
                        <span class="tooltip">i<span class="tooltiptext"><?php _e('ToolKit will automatically clear all cache if any update to the selected post type is detected.'); ?></span></span>
                    </label>
                    <br/>
                    <select id="toolkit_cache_purge" name="toolkit_cache_purge" class="text-field">
                        <?php if( $purgeOpts ){
                            $default = (isset($cacheSetting['cache_purge']) && $cacheSetting['cache_purge']) ? $cacheSetting['cache_purge'] : 'none';
                            foreach ($purgeOpts as $key => $value){
                                $selected = ($default == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";
                            }
                        } ?>
                    </select>
                    <button type="button" class="button toolkit-btn save-cache-settings" data-message="2"><?php _e('Update'); ?></button>
                </div>
                <br/>
                <div class="checkbox">
                    <label for="toolkit_cache_lifespan">
                        <b><?php _e('Cache Expiration'); ?></b>
                        <span class="tooltip">i<span class="tooltiptext"><?php _e('ToolKit will automatically clear and regenerate your cache based on the selected frequency.'); ?></span></span>
                    </label>
                    <br/>
                    <select id="toolkit_cache_lifespan" name="toolkit_cache_lifespan" class="text-field">
                        <?php if( $lifeOpts ){
                            $default = (isset($cacheSetting['cache_lifespan']) && $cacheSetting['cache_lifespan']) ? $cacheSetting['cache_lifespan'] : 'none';
                            foreach ($lifeOpts as $key => $value){
                                $selected = ($default == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";
                            }
                        } ?>
                    </select>
                    <button type="button" class="button toolkit-btn save-cache-settings" data-message="3"><?php _e('Update'); ?></button>
                </div>
                <br/>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($cacheSetting['preload_cache']) && $cacheSetting['preload_cache'] == 'on') ? 'checked' : ''; ?>
                                <input id="toolkit_preload_cache" name="toolkit_preload_cache" type="checkbox" value="1" data-message="4" data-toggler="toolkit-preload-options" <?php echo $checked; ?>>
                                <label for="toolkit_preload_cache"></label>
                            </div>
                        </div>
                        <b><?php _e('Enable Preload Cache'); ?></b>
                        <span class="tooltip">i<span class="tooltiptext"><?php _e('This will instruct ToolKit to cache the site by visiting site content automatically (this way, the cached files are ready the first time a visitor requests them). If there is a sitemap available, then ToolKit will use that to help with the caching process.'); ?></span></span>
                    </label>
                </div>
                <div class="toolkit-preload-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                    <br/>
                    <div class="checkbox">
                        <button class="button toolkit-btn" id="run-preload-cache"><?php _e("Run Now"); ?></button>
                        <?php if( isset($preloadMeta['time'], $preloadMeta['files']) ){
                            echo __('Last preloaded').' <b>'.$preloadMeta['files'].'</b> '.__('files and finished at ') . date('h:i a , F d, Y', $preloadMeta['time']);
                        } ?>
                    </div>
                    <br/>
                    <div class="checkbox">
                        <label for="toolkit_preload_lifespan">
                            <b><?php _e('Define Preload Schedule'); ?></b>
                            <span class="tooltip">i<span class="tooltiptext"><?php _e('ToolKit will attempt to preload cache based on the scheduled frequency.'); ?></span></span>
                        </label>
                        <br/>
                        <select id="toolkit_preload_lifespan" name="toolkit_preload_lifespan" class="text-field">
                            <?php if( $preOpts ){
                                $default = (isset($cacheSetting['preload_lifespan']) && $cacheSetting['preload_lifespan']) ? $cacheSetting['preload_lifespan'] : 'none';
                                foreach ($preOpts as $key => $value){
                                    $selected = ($default == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$value</option>";
                                }
                            } ?>
                        </select>
                        <button type="button" class="button toolkit-btn save-cache-settings" data-message="6"><?php _e('Update'); ?></button>
                    </div>
                </div>
                <br/>
                <div class="checkbox">
                    <label for="toolkit_cache_exclude"><b><?php _e("Exclude Pages from Caching"); ?></b></label>
                    <br/>
                    <p class="m-0"><?php _e("Add each url in single line"); ?></p>
                    <table class="widefat">
                        <tr>
                            <td>
                                <textarea rows="3" class="wd-100" id="toolkit_cache_exclude" placeholder="/page-slug/"><?php echo isset($cacheSetting['cache_exclude']) ? str_replace("\\'", "'", $cacheSetting['cache_exclude']) : ''; ?></textarea>
                            </td>
                            <td class="wd100">
                                <button type="button" class="button toolkit-btn save-cache-settings" data-message="7"><?php _e('Update'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($cacheSetting['cache_loggedin']) && $cacheSetting['cache_loggedin'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_cache_loggedin" name="toolkit_cache_loggedin" type="checkbox" value="1" data-message="8" <?php echo $checked; ?>>
                            <label for="toolkit_cache_loggedin"></label>
                        </div>
                    </div>
                    <b><?php _e('Optimize for Logged-in Users'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Enabling this will generate and serve cached content to logged-in users.'); ?></span></span>
                </label>
            </div>
            <br/>
        </div>
    </div>
</div>
