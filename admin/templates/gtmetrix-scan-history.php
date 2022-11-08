<?php
global $wpdb;
$toolkit_uploads = WP_CONTENT_DIR . '/toolkit-reports/';
$toolkit_uploads_url = get_option('siteurl') . '/wp-content/toolkit-reports/';
$limit = 5;
$scanHistory = $wpdb->get_results("SELECT" . " * FROM {$wpdb->prefix}toolkit_gtmetrix ORDER BY id desc LIMIT $limit", ARRAY_A); ?>
<?php if( $scanHistory ){ ?>
    <h4>
        <span class="widget-panel-title"><?php _e("Scan History"); ?></span>
        <button id="clear-scan-history" class="button button-primary right"><?php _e("Clear History"); ?></button>
    </h4>
    <table class="table table-bordered" id="gtmetrix-scan-history" style="background:#F9F9F9;">
        <thead>
        <tr>
            <th width="40%"><?php _e("URL"); ?></th>
            <th width="20%"><?php _e("Date"); ?></th>
            <th width="10%"><?php _e("Load Time"); ?></th>
            <th width="10%"><?php _e("Page Speed"); ?></th>
            <th width="10%"><?php _e("YSlow"); ?></th>
            <th width="10%"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($scanHistory as $s => $history) {
            $resources = json_decode($history['resources'], true); ?>
            <tr>
                <td><a href="<?php echo $history['scan_url']; ?>" target="_blank"><?php echo $history['scan_url']; ?></a></td>
                <td class="text-center"><?php echo date('M d, Y', strtotime($history['created'])); ?></td>
                <td class="text-center"><?php echo round($history['load_time'] / 1000, 2); ?></td>
                <td class="text-center"><?php echo $history['page_speed']; ?>%</td>
                <td class="text-center"><?php echo $history['yslow']; ?>%</td>
                <td class="text-center">
                    <?php if (file_exists($toolkit_uploads . 'report_pdf-' . $history['test_id'] . '.pdf')) {
                        echo '<a href="' . $toolkit_uploads_url . 'report_pdf-' . $history['test_id'] . '.pdf' . '" title="Download" class="" target="_blank"><span class="dashicons dashicons-download"></span></a>';
                    } else {
                        echo '<a href="javascript:void(0);" title="Download" class="download-full-report" data-full_report="' . $resources['report_pdf_full'] . '" data-testid="' . $history['test_id'] . '"><span class="dashicons dashicons-download"></span></a>';
                    } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>