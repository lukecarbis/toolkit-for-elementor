<?php
$bgTasks = new Toolkit_Elementor_BgTasks();
$bgTasksOpts = get_option('toolkit_background_tasks_options', array());
?>
<div class="bground-tasks-panel">
<div class="widget-panel-title"><?php _e('Optimize WP Background Processes'); ?></div>	
<?php _e('WordPress has a variety of tasks that run in the background and consume resources which may impact server performance. You can now use ToolKit to adjust the settings of some of these background tasks to maximize performance.  '); ?><br /><br />
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($bgTasksOpts['disable_heartbeat']) && $bgTasksOpts['disable_heartbeat'] == 'yes') ? 'checked' : ''; ?>
                    <input id="toolkit_disable_heartbeat" name="toolkit_disable_heartbeat" type="checkbox" value="1" data-message="8" data-toggler="heartbeat-frequency-options" <?php echo $checked; ?>>
                    <label for="toolkit_disable_heartbeat"></label>
                </div>
            </div>
            <?php _e('<b>Modify Heartbeat Settings</b><br />The Heartbeat API is a simple server polling API built in to WordPress, allowing near-real-time frontend updates. | <a href="https://developer.wordpress.org/plugins/javascript/heartbeat-api/">Learn More</a>'); ?>
        </label>
        <div class="heartbeat-frequency-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>><br>
            <label for="toolkit_heartbeat_frequency"><b><?php _e("Heartbeat Frequency", "toolkit-for-elementor"); ?></b></label>
            <br>
            <select id="toolkit_heartbeat_frequency" name="toolkit_heartbeat_frequency" class="text-field">
                <?php
                $freqOpts = $bgTasks->get_heartbeat_options();
                if( $freqOpts ){
                    foreach ($freqOpts as $key => $value){
                        $selected = (isset($bgTasksOpts['heartbeat_frequency']) && $bgTasksOpts['heartbeat_frequency'] == $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                } ?>
            </select>
            <button type="button" class="button toolkit-btn save-bgtasks-options" data-message="10"><?php _e('Update'); ?></button>
        </div>
    </div><br>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($bgTasksOpts['disable_revision']) && $bgTasksOpts['disable_revision'] == 'yes') ? 'checked' : ''; ?>
                    <input id="toolkit_disable_revision" name="toolkit_disable_revision" type="checkbox" value="1" data-message="11" data-toggler="posts-revision-options" <?php echo $checked; ?>>
                    <label for="toolkit_disable_revision"></label>
                </div>
            </div>
            <?php _e('<b>Modify Post Revision Limits</b><br />The WordPress revisions system stores a record of each saved draft or published update. You can easily save database resources by limiting how many Page and Post revisions WP stores. | <a href="https://wordpress.org/support/article/revisions/">Learn More</a>'); ?>
        </label>
        <div class="posts-revision-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>><br>
            <label for="toolkit_revision_frequency"><b><?php _e("Post Revision Limit", "toolkit-for-elementor"); ?></b></label>
            <br>
            <select id="toolkit_revision_frequency" name="toolkit_revision_frequency" class="text-field">
                <?php
                $postOpts = $bgTasks->get_post_revision_options();
                if( $postOpts ){
                    foreach ($postOpts as $key => $value){
                        $selected = (isset($bgTasksOpts['revision_frequency']) && $bgTasksOpts['revision_frequency'] == $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                } ?>
            </select>
            <button type="button" class="button toolkit-btn save-bgtasks-options" data-message="13"><?php _e('Update'); ?></button>
        </div>
    </div><br>
    <div class="checkbox">
        <label>
            <div class="switch-container">
                <div class="switch">
                    <?php $checked = (isset($bgTasksOpts['disable_autosave']) && $bgTasksOpts['disable_autosave'] == 'yes') ? 'checked' : ''; ?>
                    <input id="toolkit_disable_autosave" name="toolkit_disable_autosave" type="checkbox" value="1" data-message="14" data-toggler="disable-autosave-options" <?php echo $checked; ?>>
                    <label for="toolkit_disable_autosave"></label>
                </div>
            </div>
            <?php _e('<b>Modify Autosave Intervals</b><br />WordPress periodically autosaves your work as you edit your site (by default this interval is every minute). If you do not require WP to autosave your work every minute, you can adjust these settings to maximize your server resources. | <a href="https://wordpress.org/support/article/revisions/#autosaves">Learn More</a>'); ?>
        </label>
        <div class="disable-autosave-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>><br>
            <label for="toolkit_autosave_interval"><b><?php _e("Autosave Interval", "toolkit-for-elementor"); ?></b></label>
            <br/>
            <select id="toolkit_autosave_interval" name="toolkit_autosave_interval" class="text-field">
                <?php
                $postOpts = $bgTasks->get_autosave_options();
                if( $postOpts ){
                    foreach ($postOpts as $key => $value){
                        $selected = (isset($bgTasksOpts['autosave_interval']) && $bgTasksOpts['autosave_interval'] == $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                } ?>
            </select>
            <button type="button" class="button toolkit-btn save-bgtasks-options" data-message="16"><?php _e('Update'); ?></button>
        </div>
    </div>
</div>
