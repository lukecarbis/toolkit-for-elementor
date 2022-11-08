<?php $dbOptSetting = get_option('toolkit_elementor_dbopt_settings', array());
$lifeOpts = array(
    'never'     => __('Never'),
    'daily'     => __('Daily'),
    'weekly'    => __('Weekly'),
    'monthly'   => __('Monthly')
);
$dbOpt = new Toolkit_For_Elementor_DbOpt();
$counts = $dbOpt->get_stats();
$count = '0'; ?>
<div class="widget-panel-title"><?php _e('Database Optimization'); ?></div>
<?php _e('Please make sure you have a backup of your site. Cleaning your database is an irreversible function and cannot be undone.'); ?><br /><br />
<div class="controls-section">
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['posts_revs']) && $dbOptSetting['posts_revs'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_posts_revs" name="toolkit_posts_revs" type="checkbox" value="1" data-message="0" <?php echo $checked; ?>>
                    <label for="toolkit_posts_revs"></label>
                </div>
            </div>
            <?php _e('<b>Post Revisions</b><br/>'.$counts['posts_revs'].' post revisions'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['auto_drafts']) && $dbOptSetting['auto_drafts'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_auto_drafts" name="toolkit_auto_drafts" type="checkbox" value="1" data-message="2" <?php echo $checked; ?>>
                    <label for="toolkit_auto_drafts"></label>
                </div>
            </div>
            <?php _e('<b>Post Auto Drafts</b><br/>'.$counts['auto_drafts'].' post auto drafts'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['trash_posts']) && $dbOptSetting['trash_posts'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_trash_posts" name="toolkit_trash_posts" type="checkbox" value="1" data-message="4" <?php echo $checked; ?>>
                    <label for="toolkit_trash_posts"></label>
                </div>
            </div>
            <?php _e('<b>Trashed Posts</b><br/>'.$counts['trash_posts'].' trashed posts'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['spam_comments']) && $dbOptSetting['spam_comments'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_spam_comments" name="toolkit_spam_comments" type="checkbox" value="1" data-message="6" <?php echo $checked; ?>>
                    <label for="toolkit_spam_comments"></label>
                </div>
            </div>
            <?php _e('<b>Spam Comments</b><br/>'.$counts['spam_comments'].' spam comments'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['trash_comments']) && $dbOptSetting['trash_comments'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_trash_comments" name="toolkit_trash_comments" type="checkbox" value="1" data-message="8" <?php echo $checked; ?>>
                    <label for="toolkit_trash_comments"></label>
                </div>
            </div>
            <?php _e('<b>Trashed Comments</b><br/>'.$counts['trash_comments'].' trashed comments'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['exp_transients']) && $dbOptSetting['exp_transients'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_exp_transients" name="toolkit_exp_transients" type="checkbox" value="1" data-message="10" <?php echo $checked; ?>>
                    <label for="toolkit_exp_transients"></label>
                </div>
            </div>
            <?php _e('<b>Expired Transients</b><br/>'.$counts['exp_transients'].' expired transients'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['all_transients']) && $dbOptSetting['all_transients'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_all_transients" name="toolkit_all_transients" type="checkbox" value="1" data-message="12" <?php echo $checked; ?>>
                    <label for="toolkit_all_transients"></label>
                </div>
            </div>
            <?php _e('<b>All Transients</b><br/>'.$counts['all_transients'].' transients'); ?>
        </label>
    </div>
    <br/>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($dbOptSetting['opt_tables']) && $dbOptSetting['opt_tables'] == 'on') ? 'checked' : ''; ?>
                    <input id="toolkit_opt_tables" name="toolkit_opt_tables" type="checkbox" value="1" data-message="14" <?php echo $checked; ?>>
                    <label for="toolkit_opt_tables"></label>
                </div>
            </div>
            <?php _e('<b>Optimize Tables</b><br/>'.$counts['opt_tables'].' unoptimized tables'); ?>
        </label>
    </div>
</div>
<br/>
<div class="checkbox">
    <label for="toolkit_dbauto_clean"><b><?php _e('Automatic Cleaning'); ?></b></label>
    <br/>
    <select id="toolkit_dbauto_clean" name="toolkit_dbauto_clean" class="text-field">
        <?php if( $lifeOpts ){
            $default = (isset($dbOptSetting['dbauto_clean']) && $dbOptSetting['dbauto_clean']) ? $dbOptSetting['dbauto_clean'] : 'never';
            foreach ($lifeOpts as $key => $value){
                $selected = ($default == $key) ? 'selected' : '';
                echo "<option value='$key' $selected>$value</option>";
            }
        } ?>
    </select>
    <button type="button" class="button toolkit-btn save-database-settings" data-message="16"><?php _e('Update'); ?></button>
</div>
