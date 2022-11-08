<?php $editable_roles = wp_roles()->role_objects; //array_reverse(get_editable_roles());
$role_names = wp_roles()->role_names;
$default_role = 'administrator';
$default_expiry = 'day';
$helper = new Toolkit_Elementor_Login_Helper();
$temp_users = $helper->get_temp_logins();
$cap_options = Toolkit_Elementor_TempLogin::get_capability_options();
$plg_options = Toolkit_Elementor_TempLogin::get_plugins_options();
$access_options = Toolkit_Elementor_TempLogin::get_access_options();
global $wpdb;
$toolkitAccess = get_option('toolkit_access_plugin_settings', array());
?>
<div class="" id="admin-access-links">
    <div class="widgets-tab-wrapper">
        <input type="radio" name="access-manager-tab" class="widgets-tab-radio" id="access-links" checked>
        <input type="radio" name="access-manager-tab" class="widgets-tab-radio" id="plugin-access">
        <div class="widget-tabs">
            <label class="widget-tab" id="access-links-tab" for="access-links"><?php _e("User Access Links", "toolkit-for-elementor"); ?></label>
            <label class="widget-tab" id="plugin-access-tab" for="plugin-access"><?php _e("Plugin Access", "toolkit-for-elementor"); ?></label>
        </div>
        <div class="widgets-tab-panels">
            <div class="widget-panel" id="access-links-panel">
                <div class="admin-access-links">
		    	    <div class="widget-panel-title"><?php _e('Easily Share Secure WP Dashboard Access'); ?></div>
			        <?php _e('User Access Links allow you to quickly and securely provide access to your WP Admin Dashboard without the need for a password. Simply fill out basic user details, customize what areas of your Dashboard they should have access to, and click "Generate Secure Access Link". You can then click on "Get Link" to copy your link and share it with the desired user.<br><br>ToolKit detects what user roles have access to the WP Dashboard, and displays those options in the available User Roles list. When creating a new User Access Link, you can then customize what areas of the Dashboard the user will have access to. <br><br>If WooCommerce is installed, ToolKit will detect this and allow you to hide your WooCommerce area from users. This is helpful if you want to provide dashboard access to another user or support team, but don\'t necessarily want them to be able to see your WooCommerce sales or customer data.'); ?>
                    <br><br>
					<button type="button" id="show-login-form" class="button toolkit-btn"><?php _e("Create New User Link"); ?></button>
                    <form method="post" id="toolkit-login-form">
                        <table class="widefat login-form">
                            <tbody>
                            <tr>
                                <th><label for="login-fname"><b><?php _e("First Name"); ?></b></label></th>
                                <td>
                                    <input type="text" id="login-fname" name="login-fname" class="text-field">
                                </td>
                                <th><label for="access-link-posts"><b><?php _e("Posts"); ?></b></label></th>
                                <td>
                                    <select id="access-link-posts" name="access-link-posts" class="text-field">
                                        <?php foreach ($cap_options as $key => $val){
                                            echo "<option value='$key'>$val</option>";
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="login-lname"><b><?php _e("Last Name"); ?></b></label></th>
                                <td>
                                    <input type="text" id="login-lname" name="login-lname" class="text-field">
                                </td>
                                <th><label for="access-link-plugins"><b><?php _e("Plugins"); ?></b></label></th>
                                <td>
                                    <select id="access-link-plugins" name="access-link-plugins" class="text-field">
                                        <?php foreach ($cap_options as $key => $val){
                                            echo "<option value='$key'>$val</option>";
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="login-email"><b><?php _e("Email *"); ?></b></label></th>
                                <td>
                                    <input type="email" id="login-email" name="login-email" class="text-field" required>
                                </td>
                                <th><label for="access-link-pages"><b><?php _e("Pages"); ?></b></label></th>
                                <td>
                                    <select id="access-link-pages" name="access-link-pages" class="text-field">
                                        <?php foreach ($cap_options as $key => $val){
                                            echo "<option value='$key'>$val</option>";
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="login-role"><b><?php _e("User Role"); ?></b></label></th>
                                <td>
                                    <?php if( $editable_roles ){
                                        $default_caps = array(
                                            'edit_pages'        => false,
                                            'edit_posts'        => false,
                                            'activate_plugins'  => false,
                                            'manage_woocommerce'=> false
                                        );
                                        $roles_caps = array(); ?>
                                        <select id="login-role" name="login-role" class="text-field">
                                            <?php
                                            $access_caps = array( 'edit_posts', 'manage_woocommerce', 'view_admin_dashboard' );
                                            foreach ($editable_roles as $role => $details) {
                                                $has_cap     = false;
                                                foreach ( $access_caps as $access_cap ) {
                                                    if ( $details->has_cap( $access_cap ) ) {
                                                        $has_cap = true;
                                                        break;
                                                    }
                                                }
                                                if ( $has_cap ) {
                                                    $name = translate_user_role( $role_names[$role] );
                                                    $selected = ($role == $default_role) ? 'selected' : '';
                                                    echo "<option value='" . esc_attr( $role ) . "' $selected>$name</option>";
                                                    $roles_caps[$role] = $default_caps;
                                                    foreach ($default_caps as $key => $value){
                                                        if ( $details->has_cap( $key ) ) {
                                                            $roles_caps[$role][$key] = true;
                                                        }
                                                    }
                                                }
                                            } ?>
                                        </select>
                                        <?php echo "<textarea id='user-roles-caps' class='user-details'>".json_encode($roles_caps)."</textarea>"; ?>
                                    <?php } else {
                                        _e("No user role available.");
                                    } ?>
                                </td>
                                <?php if( function_exists('WC') ){ ?>
                                    <th><label for="access-link-woocommerce"><b><?php _e("WooCommerce"); ?></b></label></th>
                                    <td>
                                        <select id="access-link-woocommerce" name="access-link-woocommerce" class="text-field">
                                            <?php foreach ($plg_options as $key => $val){
                                                echo "<option value='$key'>$val</option>";
                                            } ?>
                                        </select>
                                    </td>
                                <?php } else { ?>
                                    <th></th>
                                    <td></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th><label for="login-expiry"><b><?php _e("Valid For"); ?></b></label></th>
                                <td>
                                    <select id="login-expiry" name="login-expiry" class="text-field">
                                        <?php Toolkit_Elementor_TempLogin::get_expire_duration_options( $default_expiry, array('custom_date') ); ?>
                                    </select>
                                </td>
                                <th><label for="login-redirect"><b><?php _e("Redirect"); ?></b></label></th>
                                <td>
                                    <select id="login-redirect" name="login-redirect" class="text-field">
                                        <?php echo Toolkit_Elementor_Login_Helper::get_redirect_options(); ?>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?php wp_nonce_field( 'toolkit_access_nonce', 'toolkit_access_nonce', false ); ?>
                        <input type="hidden" id="update_user_id" name="update_user_id" value="0">
                        <button type="submit" id="toolkit-create-access" class="button toolkit-btn"><?php _e("Generate Secure Access Link"); ?></button>
                    </form>
                    <?php if( $temp_users ){ ?>
                        <br><br>
                        <div class="widget-panel-title">
                            <?php _e("My Access Links", "toolkit-for-elementor"); ?>
                        </div>
                        <?php add_thickbox(); ?>
                        <div id="assign-user-content" style="display:none;">
                            <div class="widget-panel-title">
                                <?php _e("Reassign Content to Other User", "toolkit-for-elementor"); ?>
                            </div>
                            <div class="content-reassign">
                                <label for="reassign-user"><?php _e('Select user to assign content') ?></label>
                                <select id="reassign-user" class="text-field">
                                    <option value=""><?php _e('Select User') ?></option>
                                    <?php foreach ($temp_users as $temp_user){
                                        echo '<option value="'.$temp_user->ID.'">'.$temp_user->user_login.'</option>';
                                    }?>
                                </select>
                                <input type="hidden" id="deleted-user" value="">
                                <br/>
                                <br/>
                                <button type="button" class="button toolkit-btn" id="toolkit-remove-user"><?php _e('Delete User') ?></button>
                            </div>
                        </div>
                        <table class="widefat login-list">
                            <thead>
                            <tr>
                                <th><?php _e("User Details"); ?></th>
                                <th><?php _e("Role"); ?></th>
                                <th><?php _e("Recent Access"); ?></th>
                                <th><?php _e("Expiration"); ?></th>
                                <th><?php _e("Status"); ?></th>
                                <th><?php _e("Link Options"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($temp_users as $temp_user){
                                $expire          = get_user_meta( $temp_user->ID, '_tfes_expire', true );
                                $last_login_time = get_user_meta( $temp_user->ID, '_tfes_last_login', true );
                                $last_login_str = __( 'Not logged in' );
                                if ( ! empty( $last_login_time ) ) {
                                    $last_login_str = Toolkit_Elementor_Login_Helper::time_elapsed_string( $last_login_time, true );
                                }
                                $tfes_status = 'Active';
                                if ( Toolkit_Elementor_Login_Helper::is_login_expired( $temp_user->ID ) ) {
                                    $tfes_status = 'Expired';
                                }
                                $tfes_expiry = 'Expired';
                                if ( $tfes_status == 'Active' && ! empty( $expire ) ) {
                                    $tfes_expiry = Toolkit_Elementor_Login_Helper::time_elapsed_string( $expire );
                                    if( $tfes_expiry != 'Expired' && $expire > 0 ){
                                        $tfes_expiry = date('M d, Y', $expire)."<br>".$tfes_expiry;
                                    }
                                }
                                if ( is_multisite() && is_super_admin( $temp_user->ID ) ) {
                                    $user_role = __( 'Super Admin' );
                                } else {
                                    $capabilities = $temp_user->{$wpdb->prefix . 'capabilities'};
                                    $wp_roles     = new WP_Roles();
                                    $user_role    = '';
                                    $key_role    = '';
                                    foreach ( $wp_roles->role_names as $role => $name ) {
                                        if ( array_key_exists( $role, $capabilities ) ) {
                                            $user_role = $name;
                                            $key_role = $role;
                                        }
                                    }
                                }
                                $temp_details = '<div><span>';
                                if ( ( esc_attr( $temp_user->first_name ) ) ) {
                                    $temp_details .= '<span>' . esc_attr( $temp_user->first_name ) . '</span>';
                                }
                                if ( ( esc_attr( $temp_user->last_name ) ) ) {
                                    $temp_details .= '<span> ' . esc_attr( $temp_user->last_name ) . '</span>';
                                }
                                $temp_details .= "  (<span class='wtlwp-user-login'>" . esc_attr( $temp_user->user_login ) . ')</span><br />';
                                if ( ( esc_attr( $temp_user->user_email ) ) ) {
                                    $temp_details .= '<span><b>' . esc_attr( $temp_user->user_email ) . '</b></span> <br />';
                                }
                                $temp_details .= '</span></div>';
                                $raw_expiry = get_user_meta($temp_user->ID, '_tfes_raw_expire', true);
                                $redirect = get_user_meta($temp_user->ID, '_tfes_raw_redirect', true);
                                $ps_access = get_user_meta($temp_user->ID, '_toolkit_cap_allow_posts', true);
                                $pl_access = get_user_meta($temp_user->ID, '_toolkit_cap_allow_plugins', true);
                                $pg_access = get_user_meta($temp_user->ID, '_toolkit_cap_allow_pages', true);
                                $wc_access = get_user_meta($temp_user->ID, '_toolkit_cap_allow_woocommerce', true);
                                $edit_data = array(
                                    'first_name'    => $temp_user->first_name,
                                    'last_name'     => $temp_user->last_name,
                                    'user_email'    => $temp_user->user_email,
                                    'user_role'     => $key_role,
                                    'expiry'        => $raw_expiry,
                                    'redirect'      => $redirect,
                                    'ps_access'     => $ps_access,
                                    'pl_access'     => $pl_access,
                                    'pg_access'     => $pg_access,
                                    'wc_access'     => $wc_access
                                );
                                $login_link = $helper->get_login_url( $temp_user->ID );
                                $is_active = ( 'active' === strtolower( $tfes_status ) ) ? true : false;
                                $action_link = "<span class='link-setting'><span data-user_id='{$temp_user->ID}' class='toolkit-edit-user'>" . __( 'Edit' ) . "</span></span>";
                                $action_link .= "<span class='copy'><span id='text-{$temp_user->ID}' class='toolkit-copy-to-clipboard' data-clipboard='{$login_link}'>" . __( 'Get Link' ) . "</span></span>";
                                $action_link .= "<span class='remove'><a href='#TB_inline?&width=400&height=350&inlineId=assign-user-content' data-user_id='{$temp_user->ID}' class='toolkit-remove-link thickbox'>" . __( 'Delete' ) . "</a></span>";
                                $action_link .= "<span id='copied-text-{$temp_user->ID}' class='copied-text-message'></span>";
                                echo "<tr>";
                                echo "<td>".$temp_details."<textarea id='user-".$temp_user->ID."-details' class='user-details'>".json_encode($edit_data)."</textarea></td>";
                                echo "<td>".$user_role."</td>";
                                echo "<td>".$last_login_str."</td>";
                                echo "<td>".$tfes_expiry."</td>";
                                echo "<td>".$tfes_status."</td>";
                                echo "<td>".$action_link."</td></tr>";
                            } ?>
                            </tbody>
                        </table>
                    <?php } else {

                    } ?>
                </div>
            </div>
            <div class="widget-panel" id="plugin-access-panel">
                <div class="access-manager-tab">
		            <div class="widget-panel-title"><?php _e('ToolKit Plugin Access'); ?></div>
                    <table class="widefat">
                        <tbody>
                        <tr>
                            <th width="320"><b><?php _e("Hide ToolKit on Plugins Page", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <div class="checkbox">
                                    <label>
                                        <div class="switch-container">
                                            <div class="switch">
                                                <?php $checked = (isset($toolkitAccess['hide_plugin']) && $toolkitAccess['hide_plugin'] == 'yes') ? 'checked' : ''; ?>
                                                <input id="toolkit_hide_plugin" name="toolkit_hide_plugin" type="checkbox" value="yes" <?php echo $checked; ?>>
                                                <label for="toolkit_hide_plugin"></label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><b><?php _e("Who should have access to Toolkit?", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_restrict_access" name="toolkit_restrict_access" class="text-field" data-toggler="restrict-access-options">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['restrict_access']) && $toolkitAccess['restrict_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                                <input type="hidden" id="restrict_access_id" value="<?php echo get_current_user_id(); ?>">
                            </td>
                        </tr>
                        <?php $checked = (isset($toolkitAccess['restrict_access']) && $toolkitAccess['restrict_access'] == 'me') ? 'checked' : ''; ?>
                        <tr class="restrict-access-options" <?php if($checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Booster Access", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_booster_access" name="toolkit_booster_access" class="text-field">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['booster_access']) && $toolkitAccess['booster_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="restrict-access-options" <?php if($checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Syncer Access", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_syncer_access" name="toolkit_syncer_access" class="text-field">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['syncer_access']) && $toolkitAccess['syncer_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="restrict-access-options" <?php if($checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Theme Manager Access", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_theme_access" name="toolkit_theme_access" class="text-field">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['theme_access']) && $toolkitAccess['theme_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="restrict-access-options" <?php if($checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("Toolbox Access", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_toolbox_access" name="toolkit_toolbox_access" class="text-field">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['toolbox_access']) && $toolkitAccess['toolbox_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="restrict-access-options" <?php if($checked){ echo 'style="display: none"'; } ?>>
                            <th><b><?php _e("My License Access", "toolkit-for-elementor"); ?></b></th>
                            <td>
                                <select id="toolkit_license_access" name="toolkit_license_access" class="text-field">
                                    <?php foreach ($access_options as $key => $val){
                                        $selected = (isset($toolkitAccess['license_access']) && $toolkitAccess['license_access'] == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$val</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="button toolkit-btn" id="save_restrict_access"><?php _e("Save Settings", "toolkit-for-elementor"); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
