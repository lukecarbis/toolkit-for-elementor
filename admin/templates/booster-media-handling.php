<?php $mediaSetting = get_option('toolkit_elementor_settings', array());
$loadOpts = array(
	'toolkit'   => __('ToolKit Lazy Load'),
	'native'    => __('Native Browser-Based')
);
$foldOpts = array(0, 1, 2, 3, 4, 5); ?>
<div class="single-tab" id="media-handling-tab">
    <div class="lazyload-setting-section">
        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>"/>
        <div class="widgets-tab-wrapper">
            <input type="radio" name="media-handle-tab" class="widgets-tab-radio" id="image-handling" checked>
            <input type="radio" name="media-handle-tab" class="widgets-tab-radio" id="video-handling">
            <?php if( version_compare(get_bloginfo('version'),'5.7', '<') ) { ?>
                <input type="radio" name="media-handle-tab" class="widgets-tab-radio" id="iframe-handling">
            <?php } ?>
            <div class="widget-tabs">
                <label class="widget-tab" id="image-handling-tab" for="image-handling"><?php _e("Images", "toolkit-for-elementor"); ?></label>
                <label class="widget-tab" id="video-handling-tab" for="video-handling"><?php _e("Videos", "toolkit-for-elementor"); ?></label>
                <?php if( version_compare(get_bloginfo('version'),'5.7', '<') ){ ?>
                    <label class="widget-tab" id="iframe-handling-tab" for="iframe-handling"><?php _e("Iframes", "toolkit-for-elementor"); ?></label>
                <?php } ?>
            </div>
            <div class="widgets-tab-panels">
                <div class="widget-panel" id="image-handling-panel">
                    <div class="widget-panel-title"><?php _e('Images Settings'); ?></div>
                    <p class="m-0"><?php _e('Lazy Loading speeds up your WP site by loading your media only as they enter the browser viewport. To manually lazy load images such as Elementor background images, use our Lazy Load CSS Helper Class (toolkit-lazybg). | <a href="https://toolkitforelementor.com/topics/booster/media-optimization/" target="_blank">Learn More</a>'); ?></p>
                    <br />
                    <div class="controls-section">
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($mediaSetting['image']) && $mediaSetting['image'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_image_loading" name="toolkit_image_loading" type="checkbox" value="1" data-message="0" data-toggler="toolkit-lazyload-options" <?php echo $checked; ?>>
                                        <label for="toolkit_image_loading"></label>
                                    </div>
                                </div>
                                <?php _e('<b>Lazy Load Images</b>'); ?>
                            </label>
                        </div><br />
                        <div class="toolkit-lazyload-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                            <div class="checkbox">
                                <label for="toolkit_img_loadtype"><b><?php _e('Lazy Load Method'); ?></b></label>
                                <br/>
                                <select id="toolkit_img_loadtype" name="toolkit_img_loadtype" class="text-field">
                                    <?php $default = (isset($mediaSetting['img_loadtype']) && $mediaSetting['img_loadtype']) ? $mediaSetting['img_loadtype'] : 'native';
                                    if( $loadOpts ){
                                        foreach ($loadOpts as $key => $value){
                                            $selected = ($default == $key) ? 'selected' : '';
                                            echo "<option value='$key' $selected>$value</option>";
                                        }
                                    } ?>
                                </select>
                                <button type="button" class="button toolkit-btn save-lazyload-setting" data-message="2"><?php _e('Update'); ?></button>
                            </div>
                            <br/>
                            <div class="checkbox">
                                <label for="toolkit_image_abvfold"><b><?php _e("Number of images from top of page to exclude from lazy load"); ?></b></label>
                                <br/>
                                <input type="number" step="1" class="text-field" id="toolkit_image_abvfold" value="<?php echo isset($mediaSetting['image_abvfold']) ? $mediaSetting['image_abvfold'] : '2'; ?>" />
                                <button type="button" class="button toolkit-btn save-lazyload-setting" data-message="3"><?php _e('Update'); ?></button>
                            </div>
                            <br/>
                            <div class="checkbox">
                                <label for="toolkit_exclude_loading"><b><?php _e("Exclude Images from Being Lazy Loaded"); ?></b></label>
                                <br/>
                                <p class="m-0"><?php _e("Add each image url, file name, or keyword in single line"); ?></p>
                                <table class="widefat">
                                    <tr>
                                        <td>
                                            <textarea rows="3" class="wd-100" id="toolkit_exclude_loading" placeholder="image-name.png"><?php echo isset($mediaSetting['exclude_loading']) ? str_replace("\\'", "'", $mediaSetting['exclude_loading']) : ''; ?></textarea>
                                        </td>
                                        <td class="wd100">
                                            <button type="button" class="button toolkit-btn save-lazyload-setting" data-message="4"><?php _e('Update'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br/>
                        </div>
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($mediaSetting['preload_images']) && $mediaSetting['preload_images'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_preload_images" name="toolkit_preload_images" type="checkbox" value="1" data-message="5" <?php echo $checked; ?>>
                                        <label for="toolkit_preload_images"></label>
                                    </div>
                                </div>
                                <b><?php _e('Preload Above the Fold Images'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Depending on how many images (and how large they are), preloading above the fold images such as your logo and any images excluded from lazy loading can potentially help improve Largest Content Paint times as well as Total Blocking Time.'); ?></span></span>
                            </label>
                        </div>
                        <br/>
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($mediaSetting['image_attrs']) && $mediaSetting['image_attrs'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_image_attrs" name="toolkit_image_attrs" type="checkbox" value="1" data-message="7" <?php echo $checked; ?>>
                                        <label for="toolkit_image_attrs"></label>
                                    </div>
                                </div>
                                <b><?php _e('Add Image Width & Height Attributes'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('ToolKit will attempt to add missing width and height attributes to images to help reduce layout shifts (CLS) in Lighthouse Recommendations.'); ?></span></span>
                            </label>
                        </div>
                        <br/>
                    </div>
                </div>
                <div class="widget-panel" id="video-handling-panel">
                    <div class="widget-panel-title"><?php _e('Videos Settings'); ?></div>
                    <div class="controls-section">
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($mediaSetting['video']) && $mediaSetting['video'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_video_loading" name="toolkit_video_loading" type="checkbox" value="1" data-message="9" <?php echo $checked; ?>>
                                        <label for="toolkit_video_loading"></label>
                                    </div>
                                </div>
                                <b><?php _e('Lazy Load Videos'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Perfect for reducing loading of javascript and the number of server requests associated with videos from Youtube and Vimeo.'); ?></span></span>
                            </label>
                        </div>
                        <br/>
                        <div class="checkbox">
                            <label>
                                <div class="switch-container">
                                    <div class="switch">
                                        <?php $checked = (isset($mediaSetting['yt_placeholder']) && $mediaSetting['yt_placeholder'] == 'on') ? 'checked' : ''; ?>
                                        <input id="toolkit_yt_placeholder" name="toolkit_yt_placeholder" type="checkbox" value="1" data-message="13" data-toggler="toolkit-yt_placeholder-options" <?php echo $checked; ?>>
                                        <label for="toolkit_yt_placeholder"></label>
                                    </div>
                                </div>
                                <b><?php _e('Optimize Youtube iFrames'); ?></b>
                                <span class="tooltip">i<span class="tooltiptext"><?php _e('Drastically reduce server requests generated by Youtube by replacing YouTube iFrames with a placeholder image and only loading the video upon user interaction.'); ?></span></span>
                            </label>
                        </div>
                        <br/>
                        <div class="toolkit-yt_placeholder-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = (isset($mediaSetting['yt_self_host']) && $mediaSetting['yt_self_host'] == 'on') ? 'checked' : ''; ?>
                                            <input id="toolkit_yt_self_host" name="toolkit_yt_self_host" type="checkbox" value="1" data-message="15" <?php echo $checked; ?>>
                                            <label for="toolkit_yt_self_host"></label>
                                        </div>
                                    </div>
                                    <b><?php _e('Self-host Youtube placeholder thumbnail'); ?></b>
                                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Self-host the Youtube placeholder thumbnail on your server for improved performance'); ?></span></span>
                                </label>
                            </div>
                            <br/>
                        </div>
                    </div>
                </div>
                <?php if( version_compare(get_bloginfo('version'),'5.7', '<') ){ ?>
                    <div class="widget-panel" id="iframe-handling-panel">
                        <div class="widget-panel-title"><?php _e('Iframes Settings'); ?></div>
                        <div class="controls-section">
                            <div class="checkbox">
                                <label>
                                    <div class="switch-container">
                                        <div class="switch">
                                            <?php $checked = (isset($mediaSetting['iframe']) && $mediaSetting['iframe'] == 'on') ? 'checked' : ''; ?>
                                            <input id="toolkit_iframe_loading" name="toolkit_iframe_loading" type="checkbox" value="1" data-message="11" <?php echo $checked; ?>>
                                            <label for="toolkit_iframe_loading"></label>
                                        </div>
                                    </div>
                                    <b><?php _e('Lazy Load Iframes'); ?></b>
                                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Perfect for reducing loading of javascript and the number of server requests associated with iframes and oEmbed content. As of WP 5.7, this feature is built into WP core and no longer needs to be enabled in ToolKit.'); ?></span></span>
                                </label>
                            </div>
                            <br/>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
