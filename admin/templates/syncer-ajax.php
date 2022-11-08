<?php
$sites = get_posts(array(
    'post_type' => 'syncer_site',
    'orderby'   => 'title',
    'order'     => 'ASC'
));
$syncAuth = new Toolkit_Elementor_Syncer_Auth();
$syncer_keys = $syncAuth->get_syncer_keys();
?>
<div id="syncer-template">
    <div id="site-connection-manager">
        <div class="tabs-holder">
            <div class="tab-nav">
                <ul class="licensekit-tabs">
                    <li class="active active-tab" data-tabid="share-templates-tab">
                        <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/s.svg" style="float: right;" width="40">
                        <span><?php _e('CONNECT TO A SITE'); ?></span>
                        <p class="margin0">
                            <?php _e('Simply Enter a Syncer Key'); ?>
                        </p>
                    </li>
                    <li data-tabid="generate-key-tab">
                        <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/synckey.svg" style="float: right;" width="40">
                        <span><?php _e('CREATE SYNCER KEY'); ?></span>
                        <p class="margin0">
                            <?php _e('Share Access to Your Templates'); ?>
                        </p>
                    </li>
                </ul>
            </div>
            <div class="content-tab">
                <div class="single-tab" id="share-templates-tab" style="display: block">
                    <div class="widgets-tab-wrapper">
                        <input type="radio" name="syncer-manager-tab" class="widgets-tab-radio" id="site-connect" checked>
                        <input type="radio" name="syncer-manager-tab" class="widgets-tab-radio" id="manage-keys">
                        <div class="widget-tabs">
                            <label class="widget-tab" id="site-connect-tab" for="site-connect"><?php _e("Connect to a Site", "toolkit-for-elementor"); ?></label>
                            <label class="widget-tab" id="manage-keys-tab" for="manage-keys"><?php _e("Manage Bookmarks", "toolkit-for-elementor"); ?></label>
                        </div>
                        <div class="widgets-tab-panels">
                            <div class="widget-panel" id="site-connect-panel">
                                <div class="admin-site-connect">
                                    <div id="syncer-connect-wrapper">
                                        <div class="widget-panel-title"><?php _e("Connect to a Site"); ?></div>
                                        <p class="m-0"><?php _e('Please enter the Syncer Key for the site you\'d like to connect to. To learn how Syncer works, check out <a href="https://toolkitforelementor.com/topics/syncer/how-to-share-templates-using-syncer/" target="_blank">our guide here.</a>'); ?></p>
                                        <br/><br/>
                                        <div class="key-and-action-row">
                                            <input type="text" id="syncer-key-external" class="syncer-key-input connect-key"/><br/><br/>
                                            <button type="button" class="button toolkit-btn" id="syncer-key-connect"><?php _e("Connect"); ?></button>
                                            <button type="button" class="button toolkit-btn" id="clear-syncer-key"><?php _e("Clear"); ?></button>
                                            <button type="button" class="button toolkit-btn" id="bookmark-syncer-key"><?php _e("Bookmark"); ?></button>
                                        </div>
                                        <div>
                                            <div id="toolkit_syncer_remote_templates"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-panel" id="manage-keys-panel">
                                <div class="admin-manage-keys">
                                    <div class="widget-panel-title"><?php _e("Manage Syncer Keys"); ?></div>
                                    <p class="m-0"><?php _e('Syncer easily allows users to Bookmark and connect to multiple sites. To quickly connect to one of your bookmarks, simply click on "Connect" next to the site you wish to connect to.'); ?></p><br />
                                    <?php if( $sites ){ ?>
                                        <div class="manage-syncer-keys">
                                            <table class="widefat syncer-keys-table">
                                                <thead>
                                                <tr>
                                                    <th><b><?php _e("Site Title"); ?></b></th>
                                                    <th><b><?php _e("URL"); ?></b></th>
                                                    <th><b><?php _e("Action"); ?></b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($sites as $site){
                                                    echo "<tr><td>".$site->post_title."</td>";
                                                    echo "<td>".$site->post_excerpt."</td>";
                                                    echo "<td><span class='connect-site' data-site_id='".$site->ID."' data-site_key='".esc_attr($site->post_content)."'>".__("Connect")."</span> ";
                                                    echo "<span class='remove-site' data-site_id='".$site->ID."'>".__("Remove")."</span></td></tr>";
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-tab" id="generate-key-tab">
                    <div class="widget-panel-title"><?php _e("Easily Share Elementor Templates with Syncer"); ?></div>
                    <p class="m-0"><?php _e('You can now generate your own Syncer Key and share templates between other Elementor sites using ToolKit. Simply define an expiration date and click "Generate Syncer Key". You can then share your secure Syncer Key with another user, or use it on your other ToolKit sites.'); ?></p>
                    <h3><?php _e("Generate New Syncer Key"); ?></h3>
                    <p class="m-0">
                        <label for="key-expiration"><b><?php _e('Expiration'); ?></b></label>
                        <br/>
                        <input type="date" class="syncer-key-input" id="key-expiration"/>
                        <br/>
                        <input type="text" maxlength="30" class="syncer-key-input" id="syncer-key-note" placeholder="<?php _e('Notes'); ?>"/>
                        <br/>
                        <button type="button" class="button toolkit-btn generate-syncerkey"><?php _e('Generate Key'); ?></button>
                    </p>
                    <p>
                        <?php _e('For security purposes, regenerating or modifying your Syncer Key will render your current one useless.'); ?>
                    </p>
                    <?php if ($syncer_keys) { ?>
                        <h3 class="m-0"><?php _e("Syncer Keys"); ?></h3>
                        <div class="table-holder">
                            <table class="widefat">
                                <thead>
                                <tr>
                                    <th><b><?php _e('Key'); ?></b></th>
                                    <th><b><?php _e('Expiry'); ?></b></th>
                                    <th><b><?php _e('Action'); ?></b></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($syncer_keys as $key){ ?>
                                    <tr>
                                        <td class="max400">
                                            <?php echo substr($key[0], 0, 15) . '...' . substr($key[0], -15); ?>
                                            <?php if( $key[1] < time() ){
                                                echo '&nbsp;&nbsp; <span class="expire-syncerkey">'.__('Expired').'</span>';
                                            } ?>
                                            <?php echo isset($key[2]) && $key[2] ? "<br/><span class='notesection'><b>".__('Notes').":</b> <span class='notes'>".$key[2]."</span></span>" : ''; ?>
                                        </td>
                                        <td>
                                            <?php echo date('m/d/Y', $key[1]); ?>
                                        </td>
                                        <td>
                                            <input type="hidden" class="comp-syncer-key" value="<?php echo esc_attr($key[0]); ?>">
                                            <span class="copy-syncer-key"><?php _e('Copy Key'); ?></span>
                                            <span class="edit-syncer-note"><?php _e('Edit Notes'); ?></span>
                                            <span class="remove-syncerkey"><?php _e('Remove'); ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <input type="text" id="syncer-key-input" />
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
