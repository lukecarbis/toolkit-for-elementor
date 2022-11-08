<?php
$serverTweaks = get_option('toolkit_webserver_tweaks', array()); ?>
<div class="server-tweaks-section">
    <div class="widget-panel-title"><?php _e('Additional Server Tweaks'); ?></div>
    <p class="m-0"><?php _e('We have included a few extra server-level tweaks for users that are using Apache servers. For users on NGINX & LiteSpeed servers, most of these features are natively enabled already, however we have included some useful resources and links in the event you need to customize these server settings.'); ?></p>
    <br />
    <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>"/>
    <div class="controls-section">
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = (isset($serverTweaks['gzip_compression']) && $serverTweaks['gzip_compression'] == 'on') ? 'checked' : ''; ?>
                        <input id="toolkit_gzip_compression" name="toolkit_gzip_compression" type="checkbox" value="1" data-message="0" <?php echo $checked; ?>>
                        <label for="toolkit_gzip_compression"></label>
                    </div>
                </div>
                <b><?php _e('GZip Compression'); ?></b>
                <span class="tooltip">i
                    <span class="tooltiptext">
                        <?php _e('Reduces the size of files sent from your server to a browser.'); ?>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/gzip-compression-elementor/" target="_blank">Learn about GZip Here</a>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/gzip-compression-elementor/#nginx" target="_blank">For NGINX Users</a>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/gzip-compression-elementor/#litespeed" target="_blank">For LiteSpeed Users</a>
                    </span>
                </span>
            </label>
        </div>
        <br/>
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = (isset($serverTweaks['keep_alive']) && $serverTweaks['keep_alive'] == 'on') ? 'checked' : ''; ?>
                        <input id="toolkit_keep_alive" name="toolkit_keep_alive" type="checkbox" value="1" data-message="2" <?php echo $checked; ?>>
                        <label for="toolkit_keep_alive"></label>
                    </div>
                </div>
                <b><?php _e('Enable Keep-Alive Connections'); ?></b>
                <span class="tooltip">i
                    <span class="tooltiptext">
                        <?php _e('Keep-Alive or HTTP persistent connections allow the same initial server connection to send and receive multiple requests, thus reducing the lag for subsequent requests.'); ?>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/keep-alive-connections-elementor/" target="_blank">Learn about Keep-Alive</a>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/keep-alive-connections-elementor/#nginx" target="_blank">For NGINX Users</a>
                        <br/><a href="https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:config:keep_alive" target="_blank">For LiteSpeed Users</a>
                    </span>
                </span>
            </label>
        </div>
        <br/>
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = (isset($serverTweaks['ninja_etags']) && $serverTweaks['ninja_etags'] == 'on') ? 'checked' : ''; ?>
                        <input id="toolkit_ninja_etags" name="toolkit_ninja_etags" type="checkbox" value="1" data-message="4" <?php echo $checked; ?>>
                        <label for="toolkit_ninja_etags"></label>
                    </div>
                </div>
                <b><?php _e('Enable Entity Tags (ETags)'); ?></b>
                <span class="tooltip">i
                    <span class="tooltiptext">
                        <?php _e('ETags is a mechanism that servers and browsers use to determine whether a component in the browser cache matches one on the original server.'); ?>
                        <br/><a href="https://toolkitforelementor.com/tutorials/members-only/configure-entity-tags-elementor/" target="_blank">Learn about ETags</a>
                        <br/><a href="https://toolkitforelementor.com/tutorials/members-only/configure-entity-tags-elementor/#nginx" target="_blank">For NGINX Users</a>
                        <br/><a href="https://www.litespeedtech.com/docs/webserver/config/tuning#fileETag" target="_blank">For LiteSpeed Users</a>
                    </span>
                </span>
            </label>
        </div>
        <br/>
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = (isset($serverTweaks['expire_headers']) && $serverTweaks['expire_headers'] == 'on') ? 'checked' : ''; ?>
                        <input id="toolkit_expire_headers" name="toolkit_expire_headers" type="checkbox" value="1" data-message="6" <?php echo $checked; ?>>
                        <label for="toolkit_expire_headers"></label>
                    </div>
                </div>
                <b><?php _e('Leverage Browser Caching & Expires Headers'); ?></b>
                <span class="tooltip">i
                    <span class="tooltiptext">
                        <?php _e('LBC reduces server load and load times by marking and storing certain pages, or parts of pages in the browser. Then it marks the files as being needed to be updated at various internals. Expires Headers let the browser know whether to serve a cached version of a page or file, or to request a fresh version from the server.'); ?>
                        <br/><a href="https://gtmetrix.com/add-expires-headers.html" target="_blank">Learn about Expires Headers</a>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/leverage-browser-caching-for-elementor/" target="_blank">Leverage Browser Caching</a>
                        <br/><a href="https://toolkitforelementor.com/kb/booster/leverage-browser-caching-for-elementor/#nginx" target="_blank">For NGINX Users</a>
                        <br/><a href="https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:browser_cache" target="_blank">For LiteSpeed Users</a>
                    </span>
                </span>
            </label>
        </div>
        <br/>
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = (isset($serverTweaks['encoding_header']) && $serverTweaks['encoding_header'] == 'on') ? 'checked' : ''; ?>
                        <input id="toolkit_encoding_header" name="toolkit_encoding_header" type="checkbox" value="1" data-message="8" <?php echo $checked; ?>>
                        <label for="toolkit_encoding_header"></label>
                    </div>
                </div>
                <b><?php _e('Specify a Vary: Accept-Encoding Header'); ?></b>
                <span class="tooltip">i
                    <span class="tooltiptext">
                        <?php _e('Bugs or hiccups in some public proxies can lead to compressed versions of your resources being served to users that do not support compression. This option instructs the proxy to store both a compressed and uncompressed version of the resource.'); ?>
                        <br/><a href="https://kinsta.com/knowledgebase/specify-vary-accept-encoding-header/" target="_blank">Learn about Specify a Vary from Kinsta</a>
                        <br/><a href="https://gtmetrix.com/specify-a-vary-accept-encoding-header.html" target="_blank">from GTMetrix</a>
                    </span>
                </span>
            </label>
        </div>
        <br/>
    </div>
    <br/>
    <div class="form-group">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="toolkit_export_htaccess">
            <button type="submit" class="button toolkit-btn" id="download-htaccess"><?php _e('Download/Backup htaccess File'); ?></button>
        </form>
    </div>
</div>
