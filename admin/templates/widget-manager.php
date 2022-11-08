<?php
	function toolkit_get_default_dashboard_widgets() {
        $pre_widgets = (array) get_option( 'toolkit_wp_widget_disable_dashboard', [] );
        update_option('toolkit_wp_widget_disable_dashboard', []);
        global $wp_meta_boxes;
        $screen = is_network_admin() ? 'dashboard-network' : 'dashboard';
        if( isset($_GET['page']) && $_GET['page'] == 'toolkit-performance-tool' ){
            $current_screen = get_current_screen();
            if ( ! isset( $wp_meta_boxes[ $screen ] ) || ! is_array( $wp_meta_boxes[ $screen ] ) ) {
                require_once ABSPATH . '/wp-admin/includes/dashboard.php';
                set_current_screen( $screen );
                wp_dashboard_setup();
            }
        }
        if ( isset( $wp_meta_boxes[ $screen ][0] ) ) {
            unset( $wp_meta_boxes[ $screen ][0] );
        }
        $widgets = [];
        if ( isset( $wp_meta_boxes[ $screen ] ) ) {
            $widgets = $wp_meta_boxes[ $screen ];
        }
        if( isset($_GET['page']) && $_GET['page'] == 'toolkit-performance-tool' ){
            set_current_screen( $current_screen );
        }
        update_option('toolkit_wp_widget_disable_dashboard', $pre_widgets);
		return $widgets;
	}

	function toolkit_get_default_wordpress_widgets() {
		$widgets = [];
		if ( ! empty( $GLOBALS['wp_widget_factory'] ) ) {
			$widgets = $GLOBALS['wp_widget_factory']->widgets;
		}			
		return $widgets;
	}

	function get_disabled_dashboard_widgets() {
		$widgets = (array) get_option( 'toolkit_wp_widget_disable_dashboard', [] );

		if ( is_network_admin() ) {
			$widgets = (array) get_site_option( 'toolkit_wp_widget_disable_dashboard', [] );
		}

		return $widgets;
	}
	
	function toolkit_render_elementor_widgets() { ?>
		<form id="elementor_widgets_ds" action="">
		<?php $elementor_widget_blacklist_common = ['author-box','post-comments','post-navigation','post-info','accordion','alert','audio','button','counter','divider','google_maps','heading','html','icon','icon-box','icon-list','image','image-box','image-carousel','image-gallery','menu-anchor','progress','read-more',
            'shortcode','sidebar','social-icons','spacer','star-rating','tabs','testimonial','text-editor','toggle','video','soundcloud','textpath','inner-section'];

		$elementor_widget_blacklist_pro = ['portfolio','template','login','media-carousel','testimonial-carousel','reviews','facebook-button','facebook-comments','facebook-embed','facebook-page', 'hotspot','theme-builder','posts','gallery','form','slides','nav-menu','animated-headline','price-list','price-table','flip-box',
		'call-to-action','carousel','countdown','share-buttons','theme-elements','blockquote','social','library','dynamic-tags', 'paypal-button','code-highlight',
		'sticky','wp-cli','link-actions','lottie','table-of-contents', 'video-playlist', 'progress-tracker'];

		$elementor_widget_blacklist_woo =['Archive-Products','Wc-Categories','Archive-Products-Deprecated','Archive-Description','Woocommerce-Products','Products-Deprecated',
		'Woocommerce-Breadcrumb','Wc-Pages','Wc-Add-To-Cart','Elements','Single-Elements','Categories','Woocommerce-Menu-Cart','Product-Title','Product-Images','Product-Price','Woocommerce-Product-Add-To-Cart',
		'Product-Rating','Product-Stock','Product-Meta','Product-Short-Description','Product-Content','Product-Data-Tabs','Product-Additional-Information',
		'Product-Related','Product-Upsell'];

		$options = (array) get_option( 'toolkit_elementor_widgets_disable', [] );
		
		foreach ( $elementor_widget_blacklist_common as $id ) {
			$try_replace = str_replace('_',' ',$id);
			$try_replace = str_replace('-',' ',$try_replace);
			$widget_name = strtoupper($try_replace);
			$widget_title = sprintf(_x( ' %1$s (%2$s)', 'elementor widget', 'wp-widget-disable' ), $widget_name, '<code>' . $id . '</code>');
			?>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                            <input id="<?php echo $id; ?>" name="toolkit_elementor_widgets_disable[<?php echo $id; ?>]" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" type="checkbox" value="disabled" <?php echo $checked; ?>>
                            <label for="<?php echo $id; ?>"></label>
                        </div>
                    </div>
                    <b><?php echo $widget_title; ?></b>
                </label>
            </div>
			<?php
		}
		if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
			foreach ( $elementor_widget_blacklist_pro as $id ) {
				$try_replace = str_replace('_',' ',$id);
				$try_replace = str_replace('-',' ',$try_replace);
				$widget_name = strtoupper($try_replace);
                $widget_title = sprintf(_x( ' %1$s (%2$s)', 'elementor widget', 'wp-widget-disable' ), $widget_name, '<code>' . $id . '</code>');
                ?>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                                <input id="<?php echo $id; ?>" name="toolkit_elementor_widgets_disable[<?php echo $id; ?>]" type="checkbox" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" value="disabled" <?php echo $checked; ?>>
                                <label for="<?php echo $id; ?>"></label>
                            </div>
                        </div>
                        <b><?php echo $widget_title; ?></b>
                    </label>
                </div>
                <?php
			}
		}
		if ( class_exists( 'woocommerce' ) ) { 
			foreach ( $elementor_widget_blacklist_woo as $id ) {
				$try_replace = str_replace('_',' ',$id);
				$try_replace = str_replace('-',' ',$try_replace);
				$widget_name = strtoupper($try_replace);
                $id = strtolower($id);
                $widget_title = sprintf(_x( ' %1$s (%2$s)', 'elementor widget', 'wp-widget-disable' ), $widget_name, '<code>' . $id . '</code>');
                ?>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                                <input id="<?php echo $id; ?>" name="toolkit_elementor_widgets_disable[<?php echo $id; ?>]" type="checkbox" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" value="disabled" <?php echo $checked; ?>>
                                <label for="<?php echo $id; ?>"></label>
                            </div>
                        </div>
                        <b><?php echo $widget_title; ?></b>
                    </label>
                </div>
                <?php
			}
		}		
		?>
		<div class="form-group">
			<input type="hidden" name="action" value="disable_elementor_widgets">
			<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('toolkit-elementor'); ?>"/>
		</div>
		</form>
	<?php	
	}
	
	function toolkit_render_wordpress_widgets() {
		$toolkit_widgets = (array) toolkit_get_default_wordpress_widgets(); //get_option('toolkit_wordpress_widgets', [] );
		$widgets = wp_list_sort( $toolkit_widgets, [ 'name' => 'ASC' ], null, true );
		if ( ! $widgets ) {
			printf(
				'<p>%s</p>',
				__( 'Oops, we could not retrieve your wordpress widgets! This normally occurs when there is another plugin managing your widgets or user role capabilties.', 'wp-widget-disable' )
				);
			return;
		}
		$options = (array) get_option( 'toolkit_wp_widget_disable_wordpress', [] ); ?>
		<form id="wordpress_widgets_ds" action="">
		<?php foreach ( $widgets as $id => $widget_object ) {
            $widget_title = sprintf(_x( ' %1$s (%2$s)', 'elementor widget', 'wp-widget-disable' ), $widget_object->name, '<code>' . $id . '</code>');
            ?>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                            <input id="<?php echo $id; ?>" name="toolkit_wp_widget_disable_wordpress[<?php echo $id; ?>]" type="checkbox" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" value="disabled" <?php echo $checked; ?>>
                            <label for="<?php echo $id; ?>"></label>
                        </div>
                    </div>
                    <b><?php echo $widget_title; ?></b>
                </label>
            </div>
            <?php
		} ?>
		<div class="form-group">
			<input type="hidden" name="action" value="disable_wordpress_widgets">
			<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('toolkit-elementor'); ?>"/>
		</div>
		</form>
	<?php	
	}
	
	function toolkit_render_dashboard_widgets() { ?>
		<form id="dashboard_widgets_ds" action="">
		<?php $widgets = (array) toolkit_get_default_dashboard_widgets();
		$flat_widgets = [];
		
		foreach ( $widgets as $context => $priority ) {
			foreach ( $priority as $data ) {
				foreach ( $data as $id => $widget ) {
					if ( ! $widget ) {
						continue;
					}

					$widget['title']          = isset( $widget['title'] ) ? $widget['title'] : '';
					$widget['title_stripped'] = wp_strip_all_tags( $widget['title'] );
					$widget['context']        = $context;
					$flat_widgets[ $id ] = $widget;
				}
			}
		}
		$widgets = wp_list_sort( $flat_widgets, [ 'title_stripped' => 'ASC' ], null, true );
		if ( ! $widgets ) {
			printf(
				'<p>%s</p>',
				__( 'Oops, we could not retrieve your dashboard widgets! This normally occurs when there is another plugin managing your widgets or user role capabilties.', 'wp-widget-disable' )
				);
			return;
		}
		$options    = get_disabled_dashboard_widgets();
		$wp_version = get_bloginfo( 'version' );
		?>
        <div class="checkbox">
            <label>
                <div class="switch-container">
                    <div class="switch">
                        <?php $checked = checked( 'dashboard_browser_nag', ( array_key_exists( 'dashboard_browser_nag', $options ) ? 'dashboard_browser_nag' : false ), false ); ?>
                        <input id="dashboard_browser_nag" name="toolkit_wp_widget_disable_dashboard[dashboard_browser_nag]" type="checkbox" class="widget-toggler" data-value="WP Admin Nag Messages" value="normal" <?php echo $checked; ?>>
                        <label for="dashboard_browser_nag"></label>
                    </div>
                </div>
                <b><?php printf( __( 'WP Admin Nag Messages (%s)', 'wp-widget-disable' ), '<code>dashboard_browser_nag</code>' ); ?></b>
            </label>
        </div>
		<?php
		if ( version_compare( $wp_version, '5.1.0', '>=' ) ) { ?>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = checked( 'dashboard_php_nag', ( array_key_exists( 'dashboard_php_nag', $options ) ? 'dashboard_php_nag' : false ), false ); ?>
                            <input id="dashboard_php_nag" name="toolkit_wp_widget_disable_dashboard[dashboard_php_nag]" type="checkbox" class="widget-toggler" data-value="PHP Update Messages" value="normal" <?php echo $checked; ?>>
                            <label for="dashboard_php_nag"></label>
                        </div>
                    </div>
                    <b><?php printf( __( 'PHP Update Messages (%s)', 'wp-widget-disable' ), '<code>dashboard_php_nag</code>' ); ?></b>
                </label>
            </div>
            <?php
        }

		foreach ( $widgets as $id => $widget ) {
			if ( empty( $widget['title'] ) ) {
			    $widget_title = '<code>' . esc_html( $id ) . '</code>';
			    ?>
                <div class="checkbox">
                    <label>
                        <div class="switch-container">
                            <div class="switch">
                                <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                                <input id="<?php echo esc_attr( $id ); ?>" name="toolkit_wp_widget_disable_dashboard[<?php echo esc_attr( $id ); ?>]" type="checkbox" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" value="<?php echo esc_attr($widget['context']); ?>" <?php echo $checked; ?>>
                                <label for="<?php echo esc_attr( $id ); ?>"></label>
                            </div>
                        </div>
                        <b><?php echo $widget_title; ?></b>
                    </label>
                </div>
				<?php
				continue;
			}
            $widget_title = sprintf(
                _x( '%1$s (%2$s)', 'dashboard widget', 'wp-widget-disable' ),
                wp_kses( $widget['title'], [ 'span' => [ 'class' => true ] ] ),
                '<code>' . esc_html( $id ) . '</code>'
            ); ?>
            <div class="checkbox">
                <label>
                    <div class="switch-container">
                        <div class="switch">
                            <?php $checked = checked( array_key_exists( $id, $options ), true, false ); ?>
                            <input id="<?php echo esc_attr( $id ); ?>" name="toolkit_wp_widget_disable_dashboard[<?php echo esc_attr( $id ); ?>]" type="checkbox" class="widget-toggler" data-value="<?php echo esc_attr($widget_title); ?>" value="<?php echo esc_attr($widget['context']); ?>" <?php echo $checked; ?>>
                            <label for="<?php echo esc_attr( $id ); ?>"></label>
                        </div>
                    </div>
                    <b><?php echo $widget_title; ?></b>
                </label>
            </div>
			<?php
		} ?>
		<div class="form-group">
			<input type="hidden" name="action" value="dashboard_widgets_toolkit_disable">
			<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('toolkit-elementor'); ?>"/>
		</div>
	</form>
	<?php
	}
	
	?>
<div id="widgets-tab-wrapper" class="widgets-tab-wrapper">
    <input class="widgets-tab-radio" id="one" name="group" type="radio" checked>
    <input class="widgets-tab-radio" id="two" name="group" type="radio">
    <input class="widgets-tab-radio" id="three" name="group" type="radio">
    <div class="widget-tabs">
        <label class="widget-tab" id="elementor-tab" for="one">Elementor Widgets</label>
        <label class="widget-tab" id="wordpress-tab" for="two">WP Widgets</label>
        <label class="widget-tab" id="dashboard-tab" for="three">WP Dashboard Widgets</label>
    </div>
    <div class="widgets-tab-panels">
        <div class="widget-panel" id="elementor-panel">
	<div class="widget-panel-title"><?php _e('Deregister & Remove Unused Elementor Widgets'); ?></div>	
	<?php _e('While some plugins simply "hide" the widgets in the frontend editor (via css display: none), ToolKit actually deregisters and dequeues the widget from loading. This is super helpful in keeping the Elementor editor clean and organized by making it easy to remove widgets that you are not using.<br><br>Since ToolKit deregisters the widget from loading, please be advised that not only will the widget be removed from the Elementor editor, but any instances of that widget on your site will also no longer work.'); ?><br /><br />
            <?php toolkit_render_elementor_widgets(); ?>
        </div>
    </div>
    <div class="widgets-tab-panels">
        <div class="widget-panel" id="wordpress-panel">
	<div class="widget-panel-title"><?php _e('Deregister & Remove Unused WordPress Widgets'); ?></div>	
	<?php _e('Many users may not need several of the default WP Widgets due to the plethora of available widgets in Elementor (Free and Pro) as well as other add-ons) and thus can remove them from loading.<br><br>Since ToolKit deregisters and dequeues the widget from loading, please be advised that not only will the widget be removed from the Elementor editor, but any instances of that widget on your site will also no longer work.'); ?><br /><br />
            <?php toolkit_render_wordpress_widgets(); ?>
        </div>
    </div>
    <div class="widgets-tab-panels">
        <div class="widget-panel" id="dashboard-panel">
	<div class="widget-panel-title"><?php _e('Deregister Unused WP Admin Dashboard Widgets'); ?></div>	
	<?php _e('If you\'re like most ToolKit users, we not only hate bloat, but we hate cluttered dashboards.<br><br>Though you can use WordPress\'s Screen Options area to hide certain Dashboard Widgets, the options are still listed there- and the dashboard widgets may still appear for certain users. Since ToolKit dequeues and deregisters the dashboard widgets from loading, they will not only be removed for all users, but will also not show up under Screen Options.'); ?><br /><br />
            <?php toolkit_render_dashboard_widgets(); ?>
        </div>
    </div>
</div>
