<?php
$dashOpts = get_option('toolkit_dashboard_settings_options', array());
$loginOpts = get_option('toolkit_login_settings_options', array());
$logUrlOpts = get_option('toolkit_login_page_url_options', array());
$templates = get_posts(array(
    'post_type'         => 'elementor_library',
    'posts_per_page'    => '-1',
    'post_status'		=> 'publish'
));
$bgOpts = array(
    'auto'      => __('Original'),
    'contain'   => __('Fit to Screen'),
    'cover'     => __('Fill Screen')
);
$rptOpts = array(
    'no-repeat' => __('No Repeat'),
    'repeat'    => __('Repeat'),
    'repeat-x'  => __('Repeat Horizontally'),
    'repeat-y'  => __('Repeat Vertically')
); ?>
<div class="single-tab" id="dashing-tab" style="display: block">
    <div class="widgets-tab-wrapper">
        <input type="radio" name="theme-dashing-tab" class="widgets-tab-radio" id="dashing-admin" checked>
        <input type="radio" name="theme-dashing-tab" class="widgets-tab-radio" id="dashing-login">
        <div class="widget-tabs">
            <label class="widget-tab" id="dashing-admin-tab" for="dashing-admin"><?php _e("Custom Dashboard Widget", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="dashing-login-tab" for="dashing-login"><?php _e("Login Page", "toolkit-for-elementor"); ?></label>
        </div>
        <div class="widgets-tab-panels">
            <div class="widget-panel" id="dashing-admin-panel">
                <div class="widget-panel-title"><?php _e("Customize the WP Admin Dashboard", "toolkit-for-elementor"); ?></div>
                <div class="checkbox">
                    <label for="toolkit_dashboard_template">
                        <?php _e('Use Dashing to insert an Elementor template into your WP Admin Dashboard. Customize your Admin Dashboard further by pairing with our Widget Manager to dequeue unneeded Admin Dashboard widgets. | <a href="https://toolkitforelementor.com/topics/theme-manager/dashing/">Learn More</a>'); ?>
                    </label>
                    <br/><br/>
                    <select id="toolkit_dashboard_template" name="toolkit_dashboard_template" class="text-field">
                        <option value=""><?php _e("Select Template", "toolkit-for-elementor"); ?></option>
                        <?php if( $templates ){
                            foreach ($templates as $template) {
                                $selected = (isset($dashOpts['template']) && $dashOpts['template'] == $template->ID) ? 'selected' : '';
                                echo "<option value='".$template->ID."' $selected>".$template->post_title."</option>";
                            }
                        } ?>
                    </select>
                    <button type="button" class="button toolkit-btn save-dashboard-options" data-message="0"><?php _e('Update'); ?></button>
                </div>
                <br/>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = ( isset($dashOpts['showtitle']) && $dashOpts['showtitle'] == 'yes' ) ? 'checked' : ''; ?>
                                <input id="toolkit_dashboard_showtitle" name="toolkit_dashboard_showtitle" type="checkbox" value="yes" data-message="1" <?php echo $checked; ?>>
                                <label for="toolkit_dashboard_showtitle"></label>
                            </div>
                        </div>
                        <?php _e('<b>Show Widget Title</b><br />If enabled, your custom dashboard widget will share the same title as your Elementor template.'); ?>
                    </label>
                </div>
                <br/>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = ( isset($dashOpts['dismissible']) && $dashOpts['dismissible'] == 'yes' ) ? 'checked' : ''; ?>
                                <input id="toolkit_dashboard_dismiss" name="toolkit_dashboard_dismiss" type="checkbox" value="yes" data-message="3" <?php echo $checked; ?>>
                                <label for="toolkit_dashboard_dismiss"></label>
                            </div>
                        </div>
                        <?php _e('<b>Make Dismissable</b><br />Enabling this will allow your assigned Elementor template to be dismissed/exited.'); ?>
                    </label>
                </div>
            </div>
            <div class="widget-panel" id="dashing-login-panel">
                <div class="widget-panel-title"><?php _e("Customize the WP Login Page", "toolkit-for-elementor"); ?></div>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = (isset($logUrlOpts['logpage_enable']) && $logUrlOpts['logpage_enable'] == 'on') ? 'checked' : ''; ?>
                                <input id="toolkit_logpage_enable" name="toolkit_logpage_enable" type="checkbox" value="1" data-message="5" data-toggler="toolkit-logpage-options" <?php echo $checked; ?>>
                                <label for="toolkit_logpage_enable"></label>
                            </div>
                        </div>
                        <?php _e('<b>Update Login URL</b><br/>Enhance site security by hiding your login page with a custom URL. When a visitor, bot, or hacker attempts to login via any other url, they will be redirected to your defined "Redirect" page.'); ?>
                    </label>
                </div>
                <br/>
                <div class="toolkit-logpage-options" <?php if(! $checked){ echo 'style="display: none"'; } ?>>
                    <div class="checkbox">
                        <label for="toolkit_logpage_url"><b><?php _e('New Login URL'); ?></b></label>
                        <br/>
                        <?php $logpage_url = (isset($logUrlOpts['logpage_url'])) ? $logUrlOpts['logpage_url'] : '';
                        echo '<code>'.home_url('/').'</code>'; ?>
                        <input type="text" id="toolkit_logpage_url" name="toolkit_logpage_url" class="text-field" placeholder="login" value="<?php echo $logpage_url; ?>">
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="7"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                    <div class="checkbox">
                        <label for="toolkit_logpage_red"><b><?php _e('Redirect Invalid Login Attempts to this URL'); ?></b></label>
                        <br/>
                        <?php $logpage_red = (isset($logUrlOpts['logpage_red'])) ? $logUrlOpts['logpage_red'] : '';
                        echo '<code>'.home_url('/').'</code>'; ?>
                        <input type="text" id="toolkit_logpage_red" name="toolkit_logpage_red" class="text-field" placeholder="404" value="<?php echo $logpage_red; ?>">
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="8"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                </div>
                <div class="widget-panel-title"><?php _e("Login Page Design", "toolkit-for-elementor"); ?></div>
                <div class="checkbox">
                    <?php $bgimage = (isset($loginOpts['bgimage'])) ? $loginOpts['bgimage'] : ''; ?>
                    <a href="#" class="button login_background_image_button">
                        <?php _e("Set Background Image", "toolkit-for-elementor"); ?>
                    </a>
                    <div class="login-bgimage-holder">
                        <?php if( $bgimage ){
                            echo '<img class="login-background-img" src="' . $bgimage . '" /><span class="dashicons dashicons-trash"></span>';
                        } ?>
                    </div>
                    <input type="hidden" id="toolkit_login_bgimage" value="<?php echo $bgimage; ?>">
                </div>
                <div class="toolkit-loginbg-options" <?php if(! $bgimage){ echo 'style="display: none"'; } ?>>
                    <div class="checkbox">
                        <label for="toolkit_login_bgsize">
                            <b><?php _e('Background Size'); ?></b>
                        </label>
                        <br/>
                        <select id="toolkit_login_bgsize" name="toolkit_login_bgsize" class="text-field">
                            <?php if( $bgOpts ){
                                foreach ($bgOpts as $key => $value){
                                    $selected = (isset($loginOpts['bgsize']) && $loginOpts['bgsize'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$value</option>";
                                }
                            } ?>
                        </select>
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="9"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                    <div class="checkbox">
                        <label for="toolkit_login_bgrepeat">
                            <b><?php _e('Background Repeat'); ?></b>
                        </label>
                        <br/>
                        <select id="toolkit_login_bgrepeat" name="toolkit_login_bgrepeat" class="text-field">
                            <?php if( $rptOpts ){
                                foreach ($rptOpts as $key => $value){
                                    $selected = (isset($loginOpts['bgrepeat']) && $loginOpts['bgrepeat'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$value</option>";
                                }
                            } ?>
                        </select>
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="10"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                </div>
                <div class="checkbox">
                    <?php $logoimage = (isset($loginOpts['logoimage'])) ? $loginOpts['logoimage'] : ''; ?>
                    <a href="#" class="button login_logo_image_button">
                        <?php _e("Set Logo Image", "toolkit-for-elementor"); ?>
                    </a>
                    <div class="login-logoimage-holder">
                        <?php if( $logoimage ){
                            echo '<img class="login-logo-img" src="' . $logoimage . '" /><span class="dashicons dashicons-trash"></span>';
                        } ?>
                    </div>
                    <input type="hidden" id="toolkit_login_logoimage" value="<?php echo $logoimage; ?>">
                </div>
                <div class="toolkit-loginlogo-options" <?php if(! $logoimage){ echo 'style="display: none"'; } ?>>
                    <div class="checkbox">
                        <label for="toolkit_login_lgwidth">
                            <b><?php _e('Logo Width (px)'); ?></b>
                        </label>
                        <br/>
                        <?php $lgwidth = (isset($loginOpts['lgwidth'])) ? $loginOpts['lgwidth'] : ''; ?>
                        <input type="number" step="1" id="toolkit_login_lgwidth" name="toolkit_login_lgwidth" class="text-field" value="<?php echo $lgwidth; ?>">
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="11"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                    <div class="checkbox">
                        <label for="toolkit_login_lgheight">
                            <b><?php _e('Logo Height (px)'); ?></b>
                        </label>
                        <br/>
                        <?php $lgheight = (isset($loginOpts['lgheight'])) ? $loginOpts['lgheight'] : ''; ?>
                        <input type="number" step="1" id="toolkit_login_lgheight" name="toolkit_login_lgheight" class="text-field" value="<?php echo $lgheight; ?>">
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="12"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                    <div class="checkbox">
                        <label for="toolkit_login_logourl">
                            <b><?php _e('Logo URL'); ?></b>
                        </label>
                        <br/>
                        <?php $logourl = (isset($loginOpts['logourl'])) ? $loginOpts['logourl'] : ''; ?>
                        <input type="text" id="toolkit_login_logourl" name="toolkit_login_logourl" class="text-field" value="<?php echo $logourl; ?>">
                        <button type="button" class="button toolkit-btn save-loginpage-options" data-message="13"><?php _e('Update'); ?></button>
                    </div>
                    <br/>
                </div>
                <div class="checkbox">
                    <label for="toolkit_login_customcss">
                        <b><?php _e('Custom CSS'); ?></b>
                    </label>
                    <br/>
                    <?php $customcss = (isset($loginOpts['customcss'])) ? str_replace("\\'", "'", $loginOpts['customcss']) : ''; ?>
                    <table class="widefat">
                        <tr>
                            <td>
                                <textarea rows="4" id="toolkit_login_customcss" name="toolkit_login_customcss" class="wd-100" placeholder=".xyz{width:100px}"><?php echo $customcss; ?></textarea>
                            </td>
                            <td class="wd100">
                                <button type="button" class="button toolkit-btn save-loginpage-options" data-message="14"><?php _e('Update'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
