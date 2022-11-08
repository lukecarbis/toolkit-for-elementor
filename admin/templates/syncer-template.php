<?php
// Syncer template
function syncer_template_settings_display() {
	// Check if there is license key or not
	if ( is_toolkit_for_elementor_activated() ) {
		// Get the syncer template
		ob_start();
		include_once(__DIR__ . '/syncer-ajax.php');
		$syncerTemplate = ob_get_contents();
		ob_end_clean();
	} else {
		$syncerTemplate = 'Oops, looks like you do not have a active license yet, please activate your license first in My License';
	}

	// Output the template
	ob_start(); ?>
	<div class="wrap toolkit-my-templates">
		<div class="controls-section">
			<div class="elementor-syncer">
				<?php echo $syncerTemplate; ?>
			</div>
		</div>
	</div>
<?php
	// Get the content
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}
