<?php
$unloadOpts = get_option('toolkit_unload_options', array());
$apiOpts = array(
    'disable_non_admins'    => __('Disable for Non-Admins', 'toolkit-for-elementor'),
    'disable_logged_out'    => __('Disable When Logged Out', 'toolkit-for-elementor')
);
$gtbOpts = array(
    'all'       => __('Disable Site-Wide', 'toolkit-for-elementor'),
    'home'      => __('Disable Homepage Only', 'toolkit-for-elementor')
); ?>
<div class="widgets-tab-wrapper">
    <input type="radio" name="unload-options-tab" class="widgets-tab-radio" id="common-wpfiles" checked>
    <input type="radio" name="unload-options-tab" class="widgets-tab-radio" id="source-code">
    <input type="radio" name="unload-options-tab" class="widgets-tab-radio" id="database-optimize">
    <div class="widget-tabs">
        <label class="widget-tab" id="common-wpfiles-tab" for="common-wpfiles"><?php _e("WP Core Files", "toolkit-for-elementor"); ?></label>
        <label class="widget-tab" id="source-code-tab" for="source-code"><?php _e("Source Code", "toolkit-for-elementor"); ?></label>
        <label class="widget-tab" id="database-optimize-tab" for="database-optimize"><?php _e("Database", "toolkit-for-elementor"); ?></label>
    </div>
    <div class="widgets-tab-panels">
        <div class="widget-panel" id="common-wpfiles-panel">
            <div class="widget-panel-title"><?php _e('Disable Unneeded WP Core Features'); ?></div>
            <?php _e('Depending on your setup, you may not need some default WordPress features enabled. In fact, unless you absolutely need them, some features such as XML-RPC should actually be disabled for security purposes. ToolKit enables you to selectively dequeue or modify several common WP core functions for better performance and security.'); ?><br /><br />
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_emojis']) && $unloadOpts['disable_emojis'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_emojis" name="toolkit_disable_emojis" type="checkbox" value="1" data-message="0" <?php echo $checked; ?>>
                            <label for="toolkit_disable_emojis"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable Emojis Site-Wide'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Normally about 12KB, WordPress 4.2 and higher began loading CSS and JS for the new Emoji framework. If you do not use any emojis on your site, you can disable this and reduce your server requests and site size a bit more.'); ?></span></span>
                </label>
            </div>
            <br>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_dashicons']) && $unloadOpts['disable_dashicons'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_dashicons" name="toolkit_disable_dashicons" type="checkbox" value="1" data-message="2" <?php echo $checked; ?>>
                            <label for="toolkit_disable_dashicons"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable Dashicons'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Nearly 50kb in size, the CSS for Dashicons are normally needed anytime the top WP Admin Bar is displayed. That being said, logged out or normal site visitors normally do not see the WP Admin Bar so you can dequeue this and shave off a bit of unneeded bloat. Enabling this will dequeue the Dashicons CSS from loading if the WP Admin Bar is not displayed.'); ?><br><a href="https://developer.wordpress.org/resource/dashicons/#admin-site-alt">Learn More About Dashicons</a></span></span>
                </label>
            </div>
            <br>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_oembed']) && $unloadOpts['disable_oembed'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_oembed" name="toolkit_disable_oembed" type="checkbox" value="1" data-message="4" <?php echo $checked; ?>>
                            <label for="toolkit_disable_oembed"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable oEmbed'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('This feature of WP core allows users to directly embed online content from Youtube, Twitter, and other websites sites by simply inserting a website URL. Many websites do not actually need this feature and can easily disable this. Disabling oEmbed will dequeue and remove the <i>/wp-includes/js/wp-embed.min.js server request</i>.'); ?><br><a href="https://wordpress.org/support/article/embeds/">Learn More About oEmbed</a></span></span>
                </label>
            </div>
            <br>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_rssfeed']) && $unloadOpts['disable_rssfeed'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_rssfeed" name="toolkit_disable_rssfeed" type="checkbox" value="1" data-message="6" <?php echo $checked; ?>>
                            <label for="toolkit_disable_rssfeed"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable RSS Feeds'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('By default, WordPress comes with a few default feeds. They are generated by template tags for bloginfo() for each type of feed, and are typically listed in the sidebar and/or footer of most WordPress Themes. Unless you are using your site as a blogging platform that needs to be compatible with RSS readers, you can disable RSS feeds on your website.'); ?><br><a href="https://wordpress.org/support/article/wordpress-feeds/">Learn More About RSS Feeds</a></span></span>
                </label>
            </div>
            <br>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_xmlrpc']) && $unloadOpts['disable_xmlrpc'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_xmlrpc" name="toolkit_disable_xmlrpc" type="checkbox" value="1" data-message="8" <?php echo $checked; ?>>
                            <label for="toolkit_disable_xmlrpc"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable XML-RPC'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('XML-RPC is an API framework used by WordPress for communicating between third-party apps, plugins, and blogs. It can also be used to remotely post and manage content on your WP site. Thus, unless you are using JetPack or require this particular feature, you can disable XML-RPC to strengthen security against hackers and protect against brute force attacks.'); ?><br><a href="https://codex.wordpress.org/XML-RPC_Support/">Learn More About XML-RPC</a></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['disable_commentreply']) && $unloadOpts['disable_commentreply'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_disable_commentreply" name="toolkit_disable_commentreply" type="checkbox" value="1" data-message="10" <?php echo $checked; ?>>
                            <label for="toolkit_disable_commentreply"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable Comment Reply Site-Wide'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('If you have disabled comments site-wide, you can dequeue the comment reply JS which is normally loaded by WordPress by default. If you are using Facebook or Discus for comments, you can also disable the default WP comment reply JS (/wp-includes/js/comment-reply(.min).js)'); ?></span></span>
                </label>
            </div>
            <br>
            <div class="checkbox">
                <label>
                    <?php $checked = (isset($unloadOpts['disable_restapi']) && $unloadOpts['disable_restapi'] == 'on') ? 'checked' : ''; ?>
                    <div class="switch-container">
                        <div class="switch">
                            <input id="toolkit_disable_restapi" name="toolkit_disable_restapi" type="checkbox" value="1" data-message="12" data-toggler="disable-restapi-options" <?php echo $checked; ?>>
                            <label for="toolkit_disable_restapi"></label>
                        </div>
                    </div>
                    <b><?php _e("Disable Rest API"); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('According to the official WordPress.org Developer Handbook, "The WordPress REST API provides an interface for applications to interact with your WordPress site by sending and receiving data as JSON (JavaScript Object Notation) objects. It is the foundation of the WordPress Block Editor, and can likewise enable your theme, plugin or custom application to present new, powerful interfaces for managing and publishing your site content".<br><br>Keep in mind that many plugins, admin dashboard widgets, and even the Gutenberg Editor use the REST API. Thus many performance plugins which disable the REST API do so for <b>users that are either not logged in or for non-administrators.</b><br><br><b>If "Disable Rest API" is enabled, any site attempting to connect to this site via Syncer will not be able to do so.</b>'); ?></span></span>
                </label>
                <br/>
                <div class="disable-restapi-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                    <select id="toolkit_condition_restapi" name="toolkit_condition_restapi" class="text-field">
                        <?php
                        if( $apiOpts ){
                            foreach ($apiOpts as $key => $value){
                                $selected = (isset($unloadOpts['condition_restapi']) && $unloadOpts['condition_restapi'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";
                            }
                        } ?>
                    </select>
                    <button type="button" class="button toolkit-btn save-unload-options" data-message="26"><?php _e("Update"); ?></button>
                </div>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <?php $checked = (isset($unloadOpts['disable_gutenberg']) && $unloadOpts['disable_gutenberg'] == 'on') ? 'checked' : ''; ?>
                    <div class="switch-container">
                        <div class="switch">
                            <input id="toolkit_disable_gutenberg" name="toolkit_disable_gutenberg" type="checkbox" value="1" data-message="14" data-toggler="disable-gutenberg-options" <?php echo $checked; ?>>
                            <label for="toolkit_disable_gutenberg"></label>
                        </div>
                    </div>
                    <b><?php _e('Disable Gutenberg CSS Block Library'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('For Elementor users that do not use Gutenberg at all, you can disable this. Disabling this will dequeue /wp-includes/css/dist/block-library/style.min.css (saves approx 25 KB) .'); ?></span></span>
                </label>
                <br/>
                <div class="disable-gutenberg-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                    <select id="toolkit_condition_gutenberg" name="toolkit_condition_gutenberg" class="text-field">
                        <?php
                        if( $gtbOpts ){
                            foreach ($gtbOpts as $key => $value){
                                $selected = (isset($unloadOpts['condition_gutenberg']) && $unloadOpts['condition_gutenberg'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";
                            }
                        } ?>
                    </select>
                    <button type="button" class="button toolkit-btn save-unload-options" data-message="27"><?php _e("Update"); ?></button>
                </div>
            </div>
        </div>
        <div class="widget-panel" id="source-code-panel">
            <div class="widget-panel-title"><?php _e('Clean Up Source Code'); ?></div>
            <?php _e('You can now use ToolKit to help clean your site\'s frontend source code. This can help harden the security of your site, while also reducing how much sensitive info you reveal to any site visitor.'); ?><br /><br />
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_qstrings']) && $unloadOpts['remove_qstrings'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_qstrings" name="toolkit_remove_qstrings" type="checkbox" value="1" data-message="16" <?php echo $checked; ?>>
                            <label for="toolkit_remove_qstrings"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove Query Strings'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('WP commonly appends version numbers and query strings such as “?” or “&” on the ends of certain assets (CSS, JS). Some CDNs may have issues caching these, and some performance tests often recommend removing query strings. Please keep in mind that some page builders use query strings for live site editing and versioning, so enabling this while you are working on the site is not recommended. However, once you are done editing your site, you can safely enable this feature.'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_jmigrate']) && $unloadOpts['remove_jmigrate'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_jmigrate" name="toolkit_remove_jmigrate" type="checkbox" value="1" data-message="30" <?php echo $checked; ?>>
                            <label for="toolkit_remove_jmigrate"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove jQuery Migrate'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('jQuery Migrate is used to help with compatibility issues between older jQuery code and newer versions by identifying and restoring deprecated features.<br />Most up-to-date themes and plugins don’t require jquery-migrate.min.js in which case, it can be dequeued. Please test this with caution.'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_apilinks']) && $unloadOpts['remove_apilinks'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_apilinks" name="toolkit_remove_apilinks" type="checkbox" value="1" data-message="18" <?php echo $checked; ?>>
                            <label for="toolkit_remove_apilinks"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove Rest API Links'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Enabling this feature will dequeue and remove the REST API links from your site header.'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_feedlinks']) && $unloadOpts['remove_feedlinks'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_feedlinks" name="toolkit_remove_feedlinks" type="checkbox" value="1" data-message="20" <?php echo $checked; ?>>
                            <label for="toolkit_remove_feedlinks"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove RSS Feed Links'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('RSS Feeds are included and enabled by default in WordPress core. Most users do not require or use the RSS system and thus can disable it. We have included the ability to disable RSS feeds under the Common WP Files tab in Booster. Enabling this feature removes the RSS Feeds link and reference from the front end source code of your site.'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_rsdlink']) && $unloadOpts['remove_rsdlink'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_rsdlink" name="toolkit_remove_rsdlink" type="checkbox" value="1" data-message="22" <?php echo $checked; ?>>
                            <label for="toolkit_remove_rsdlink"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove Really Simple Discovery (RSD) Link'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('RSD is normally needed for XML-RPC clients, remote management of posts, and pingbacks. However, most sites do not utilize these features and can easily dequeue RSD from loading on the front end (great for performance and security).'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_shortlink']) && $unloadOpts['remove_shortlink'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_shortlink" name="toolkit_remove_shortlink" type="checkbox" value="1" data-message="24" <?php echo $checked; ?>>
                            <label for="toolkit_remove_shortlink"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove WP Shortlinks'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('By default, all WordPress installs have an extra server request for your page/post shortlink. Often times this is not needed due to most users pretty permalink settings (https://yourdomain.com/post) and thus can be dequeued to save a server request (great for performance with no impact on SEO).'); ?></span></span>
                </label>
            </div>
            <br/>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = (isset($unloadOpts['remove_wlwlink']) && $unloadOpts['remove_wlwlink'] == 'on') ? 'checked' : ''; ?>
                            <input id="toolkit_remove_wlwlink" name="toolkit_remove_wlwlink" type="checkbox" value="1" data-message="26" <?php echo $checked; ?>>
                            <label for="toolkit_remove_wlwlink"></label>
                        </div>
                    </div>
                    <b><?php _e('Remove Windows Live Writer Link'); ?></b>
                    <span class="tooltip">i<span class="tooltiptext"><?php _e('Does your site utilize tagging support with Windows Live Writer (WLM)? If not, you can enable this to remove it from your site.'); ?></span></span>
                </label>
            </div>
        </div>
        <div class="widget-panel" id="database-optimize-panel">
            <?php include_once 'database-optimization.php'; ?>
        </div>
    </div>
</div>
