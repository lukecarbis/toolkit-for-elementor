<?php
if ( ! function_exists('gt_metrix_settings_display') ) {
function gt_metrix_settings_display(){
    ob_start();
    if (is_toolkit_for_elementor_activated()) {
        global $wpdb;
        $obj = new Lazy_load_Settings();
        $scanHistory = $wpdb->get_row("SELECT" . " * FROM {$wpdb->prefix}toolkit_gtmetrix ORDER BY id desc", ARRAY_A);
        $region = 'N/A';
        $pageSpeed = 0;
        $ySlow = 0;
        $loadTime = 0;
        $requests = 0;
        $lastReportTime = 0;
        $browser = 0;
        $screenshot = 0;
        $scan_url = site_url();
        $pageSize = '0KB';
        $pageSpeedCode = array();
        $ySlowCode = array();
        $toolkit_uploads = WP_CONTENT_DIR . '/toolkit-reports/';
        $toolkit_uploads_url = get_option( 'siteurl' ) . '/wp-content/toolkit-reports/';
        $scanResult = ($scanHistory) ? json_decode($scanHistory['response_log'], true) : false;
        $offset = !empty($_REQUEST['page_no']) ? (($_REQUEST['page_no'] - 1) * $obj->limit) : 0;
        $server_info = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        $is_apache = ($server_info && stripos($server_info, 'apache') !== false) ? true : false; ?>

        <div class="wrap toolkit-performance">
            <div class="tabs-holder">
                <div class="tab-nav">
                    <ul class="">
                        <li class="active-tab" data-tabid="gtmetrix-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/gt.svg" style="float: right;" width="48">
                            <span><?php _e('PERFORMANCE AUDIT'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Simply Input a URL to Test'); ?></medium>
                            </p>
                        </li>
			            <li data-tabid="unload-options">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/cc.svg" style="float: right;" width="48">
                            <span><?php _e('CODE CLEANER'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Bloat & Database Cleaner'); ?></medium>
                            </p>
                        </li>
			            <li data-tabid="cache-manager-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/caching.svg" style="float: right;" width="48">
                            <span><?php _e('CACHE'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Manage Cache Settings'); ?></medium>
                            </p>
                        </li>
			            <li data-tabid="cdn-minify-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/cdn.svg" style="float: right;" width="48">
                            <span><?php _e('CDN SUPPORT'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Serve static assets via CDN'); ?></medium>
                            </p>
                        </li>
                        <li data-tabid="css-minify-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/css.svg" style="float: right;" width="48">
                            <span><?php _e('CSS OPTIMIZATION'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Minify & Combine CSS'); ?></medium>
                            </p>
                        </li>
                        <li data-tabid="js-minify-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/js.svg" style="float: right;" width="48">
                            <span><?php _e('JS OPTIMIZATION'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Minify, Combine, Delay, Defer JS'); ?></medium>
                            </p>
                        </li>
			            <li data-tabid="fonts-minify-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/fh.svg" style="float: right;" width="48">
                            <span><?php _e('FONT OPTIMIZATION'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Optimize Google Fonts, Preload'); ?></medium>
                            </p>
                        </li>
			            <li data-tabid="prefetch-dns-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/headstart.svg" style="float: right;" width="48">
                            <span><?php _e('HEADSTART'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Prefetch DNS Requests'); ?></medium>
                            </p>
                        </li>
                        <li data-tabid="media-handling-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/ll.svg" style="float: right;" width="48">
                            <span><?php _e('MEDIA OPTIMIZATION'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Lazyload Images & Video'); ?></medium>
                            </p>
                        </li>
                        <?php if( $is_apache ){ ?>
                            <li data-tabid="additional-tweaks">
                                <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/st.svg" style="float: right;" width="48">
                                <span><?php _e('MISCELLANEOUS TWEAKS'); ?></span>
                                <p class="margin0">
                                    <medium><?php _e('Tweaks for Apache Servers'); ?></medium>
                                </p>
                            </li>
                        <?php } ?>
                        <li data-tabid="script-ninja-tab">
                            <img src="<?php echo TOOLKIT_FOR_ELEMENTOR_URL; ?>admin/images/sn.svg" style="float: right;" width="48">
                            <span><?php _e('SCRIPT NINJA'); ?></span>
                            <p class="margin0">
                                <medium><?php _e('Dequeue CSS & JS Scripts'); ?></medium>
                            </p>
                        </li>
                    </ul>
                </div>
                <div class="content-tab">
                    <div class="single-tab" id="gtmetrix-tab" style="display: block;">
                        <div class="row">
                            <div class="col-sm-8 gtmetrix-result">
                            <?php if ( $scanHistory && isset($scanResult['attributes']) ) {
                                $region = $scanHistory['region'];
                                $browser = $scanHistory['browser'];
                                $lastReportTime = $scanHistory['created'];
                                $scan_url = $scanHistory['scan_url'];
                                if ( file_exists($toolkit_uploads."screenshot-{$scanHistory['test_id']}.jpg") ) {
                                    $screenshot = $toolkit_uploads_url . 'screenshot-' . $scanHistory['test_id'] . '.jpg';
                                }
                                $screenshotDefault = ''; ?>
                                    <section class="gtmetrix-report">
                                        <div class="widget-panel-title"><?php _e('Performance Report Details'); ?></div>
                                        <div class="row report-head">
                                            <div class="col-sm-5 padding0">
                                                <img class="gtmetrix-scrshot" src="<?php echo($screenshot ? $screenshot : $screenshotDefault); ?>" alt="<?php _e('Screenshot'); ?>">
                                            </div>
                                            <div class="col-sm-7">
                                                <p><a href="<?php echo $scan_url; ?>" target="_blank" rel="nofollow noopener noreferrer" class="no-external"><?php echo $scan_url; ?></a></p>
                                                <div class="row">
                                                    <div class="col-sm-4 padding0">
                                                        <b><?php _e('Report Date:'); ?></b>
                                                    </div>
                                                    <div class="col-sm-8 padding0 text-right">
                                                        <?php echo($lastReportTime ? date('D, M d, Y, h:i A', strtotime($lastReportTime)) : 'N/A'); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4 padding0">
                                                        <b><?php _e('Test Location:'); ?></b>
                                                    </div>
                                                    <div class="col-sm-8 padding0 text-right">
                                                        <?php echo $region; ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-5 padding0">
                                                        <b><?php _e('Browser Type:'); ?></b>
                                                    </div>
                                                    <div class="col-sm-7 padding0 text-right">
                                                        <?php echo ($browser ? $browser : 'N/A'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                <?php } ?>
                            </div>
                            <div class="col-sm-4 gtmetrix-form">
                                <div class="toolkit-gtmetrix-section">
                                    <div class="gtmetrix-scan-title"><center><?php _e('Perform GTMetrix Scan'); ?></center></div>
                                    <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce($obj->nonce_key); ?>"/>
                                    <div class="form-group">
                                        <label for="sel1"><?php _e('Enter a URL To Test Here'); ?></label>
                                        <input class="form-control" type="text" name="scan_url" placeholder="https://yourdomain.com" value="<?php echo site_url(); ?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="sel1"><?php _e('Select Testing Location'); ?></label>
                                        <select class="form-control" name="location">
                                            <?php if ($obj->gtmetrix_locations) {
                                                foreach ($obj->gtmetrix_locations as $region => $locations) {
                                                    echo '<optgroup label="'.$region.'">';
                                                    foreach ($locations as $location){
                                                        echo '<option value="' . $location['id'] . '" ' . ($location['default'] ? 'selected' : '') . '>' . $location['name'] . '</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sel1"><?php _e('Select Browser Type'); ?></label>
                                        <select class="form-control" name="browser">
                                            <?php if ($obj->gtmetrix_browsers) {
                                                foreach ($obj->gtmetrix_browsers as $browsers) {
                                                    echo '<option value="' . $browsers['id'] . '" ' . (strpos($browsers['browser'], 'chrome') !== false ? 'selected' : '') . '>' . $browsers['name'] . '</option>';
                                                }
                                            } else {
                                                echo '<option value="1">Firefox (Desktop)</option>';
                                                echo '<option value="3">Chrome (Desktop)</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="form-group text-center">
                                        <button type="button" class="button toolkit-btn"><?php _e('Perform GTMetrix Scan!'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ( $scanHistory && isset($scanResult['attributes']) ) {
                            $scanResult = json_decode($scanHistory['response_log'], true);
                            $scanResult = $scanResult['attributes'];
                            $pageSpeed = $scanResult['pagespeed_score'];
                            $pageSpeedCode = $obj->gtmetrix_code($pageSpeed);
                            $pageStrctrCode = $obj->gtmetrix_code($scanResult['structure_score']); ?>
                            <section class="gtmetrix-report">
                                <div class="row">
                                    <div class="col-sm-6 report-left">
                                        <h3 class="heading3"><?php _e('GTmetrix Grade'); ?></h3>
                                        <div class="row">
                                            <div class="col-sm-4 padding0">
                                                <h3 class="gtmetrix-grade color-grade-<?php echo($pageSpeedCode ? $pageSpeedCode['code'] : 'E'); ?>"><?php echo $scanResult['gtmetrix_grade']; ?></h3>
                                            </div>
                                            <div class="col-sm-4 padding0">
                                                <p class="m-0"><?php _e('Performance'); ?></p>
                                                <h3 class="report-score-grade color-grade-<?php echo($pageSpeedCode ? $pageSpeedCode['code'] : 'E'); ?>">
                                                    <?php echo $scanResult['pagespeed_score']; ?>
                                                </h3>
                                            </div>
                                            <div class="col-sm-4 padding0">
                                                <p class="m-0"><?php _e('Structure'); ?></p>
                                                <h3 class="report-score-grade color-grade-<?php echo($pageStrctrCode ? $pageStrctrCode['code'] : 'E'); ?>">
                                                    <?php echo $scanResult['structure_score']; ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 report-right">
                                        <h3 class="heading3"><?php _e('Web Vitals'); ?></h3>
                                        <div class="row">
                                            <div class="col-sm-4 padding0">
                                                <p class="m-0"><?php _e('LCP'); ?></p>
                                                <h3 class="report-score-grade">
                                                    <?php echo round($scanResult['largest_contentful_paint']/1000, 1); ?>s
                                                </h3>
                                            </div>
                                            <div class="col-sm-5 padding0">
                                                <p class="m-0"><?php _e('TBT'); ?></p>
                                                <h3 class="report-score-grade">
                                                    <?php echo $scanResult['total_blocking_time']; ?>ms
                                                </h3>
                                            </div>
                                            <div class="col-sm-3 padding0">
                                                <p class="m-0"><?php _e('CLS'); ?></p>
                                                <h3 class="report-score-grade">
                                                    <?php echo round($scanResult['cumulative_layout_shift'], 3); ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gtmetrix-stats">
                                    <br/>
                                    <div class="normal-inner-tabs">
                                        <div class="inner-tabs-headings">
                                            <div id="tabheading-performance" data-ptype="performance" class="inner-tab-heading active-heading"><?php _e('Performance'); ?></div>
                                            <div id="tabheading-structure" data-ptype="structure" class="inner-tab-heading"><?php _e('Structure'); ?></div>
                                            <div id="tabheading-hardata" data-ptype="hardata" class="inner-tab-heading"><?php _e('Waterfall'); ?></div>
                                        </div>
                                        <div class="inner-tabs-contents">
                                            <div id="tabcontent-performance" class="inner-tab-content active-content">
                                                <div class="widget-panel-title"><?php _e('Performance Metrics'); ?></div>
                                                <div class="row">
                                                    <div class="col-sm-6 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('First Contentful Paint'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo $scanResult['first_contentful_paint']; ?>ms</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('Time to Interactive'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo $scanResult['time_to_interactive']; ?>ms</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-6 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('Speed Index'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo $scanResult['speed_index']; ?>ms</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('Total Blocking Time'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo $scanResult['total_blocking_time']; ?>ms</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-6 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('Largest Contentful Paint'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo $scanResult['largest_contentful_paint']; ?>ms</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-6 padding0">
                                                                    <h3><?php _e('Cumulative Layout Shift'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-6 padding0">
                                                                    <div class="topbar-bg">
                                                                        <span><?php _e('Good - Nothing to do here'); ?></span>
                                                                    </div>
                                                                    <div class="mainbar-bg">
                                                                        <span><?php echo round($scanResult['cumulative_layout_shift'], 2); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <h3 class="heading3"><?php _e('Browser Timings'); ?></h3>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-4 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Redirect Duration'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['redirect_duration'], 2); ?><small>ms</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 padding0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Connection Duration'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['connect_duration'], 2); ?><small>ms</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Backend Duration'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['backend_duration'], 2); ?><small>ms</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-4 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Time to First Byte'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['time_to_first_byte'], 2); ?><small>ms</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 padding0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('First Paint'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['first_paint_time']/1000, 1); ?><small>s</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('DOM Interactive Time'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['dom_interactive_time']/1000, 1); ?><small>s</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-4 pl-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('DOM Loaded Time'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['dom_content_loaded_time']/1000, 1); ?><small>s</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 padding0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Onload Time'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['onload_time']/1000, 1); ?><small>s</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 pr-0">
                                                        <div class="score-box">
                                                            <div class="row">
                                                                <div class="col-sm-9 padding0">
                                                                    <h3><?php _e('Fully Loaded Time'); ?></h3>
                                                                </div>
                                                                <div class="col-sm-3 padding0">
                                                                    <h3><?php echo round($scanResult['fully_loaded_time']/1000, 1); ?><small>s</small></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $pageData = array();
                                            $pageSpeedPath = $toolkit_uploads . "lighthouse-{$scanHistory['test_id']}.txt";
                                            if ( file_exists($pageSpeedPath) ) {
                                                $myfile = fopen($pageSpeedPath, "r");
                                                $pagespeedJson = fread( $myfile, filesize($pageSpeedPath) );
                                                fclose($myfile);
                                                if( $pagespeedJson ){
                                                    $pagespeed = json_decode($pagespeedJson, true);
                                                    if( isset($pagespeed['audits']) ){
                                                        $pageData = $pagespeed['audits'];
                                                    }
                                                }
                                            } ?>
                                            <div id="tabcontent-structure" class="inner-tab-content">
                                                <div class="table-responsive" id="pagespeed-table">
                                                    <div class="widget-panel-title"><?php _e('Recommendations'); ?></div>
                                                    <?php if( $pageData ){ ?>
                                                        <table class="pagespeed-table">
                                                            <tbody>
                                                            <?php foreach ($pageData as $id => $pageDatum) {
                                                                if( isset($pageDatum['_impactScore']) && $pageDatum['_impactScore'] > 0 ){
                                                                    $title = (isset($pageDatum['displayValue']) && $pageDatum['displayValue']) ? $pageDatum['title'].' ('.$pageDatum['displayValue'].')' : $pageDatum['title'];
                                                                    echo "<tr><td><span class='collapse-lbl' data-target='collapsed-".($id)."'>".$title."</span></td></tr>";
                                                                    if( isset($pageDatum['description']) && $pageDatum['description'] ){
                                                                        echo "<tr><td class='inner-col'><span class='collapse-content' id='collapsed-".($id)."'>".str_ireplace("[Learn more]", "", $pageDatum['description'])."<br/>";
                                                                        if( isset($pageDatum['details']['type']) && $pageDatum['details']['type'] == 'table' ){
                                                                            $heads = $pageDatum['details']['headings'];
                                                                            echo "<table class='wd-100'><thead><tr>";
                                                                            foreach ($heads as $head){
                                                                                if( in_array($head['itemType'], ['text','numeric','url','bytes','ms']) ){
                                                                                    echo "<th>".$head['text']."</th>";
                                                                                }
                                                                            }
                                                                            echo "</tr></thead><tbody>";
                                                                            foreach ($pageDatum['details']['items'] as $item){
                                                                                echo "<tr>";
                                                                                foreach ($heads as $head){
                                                                                    if( in_array($head['itemType'], ['text','numeric','url','bytes','ms']) ){
                                                                                        if( isset($item[$head['key']]) ){
                                                                                            echo "<td>".$item[$head['key']]."</td>";
                                                                                        } else {
                                                                                            echo "<td></td>";
                                                                                        }
                                                                                    }
                                                                                }
                                                                                echo "</tr>";
                                                                            }
                                                                            echo "</tbody></table>";
                                                                        }
                                                                        echo "</span></td></tr>";
                                                                    } else {
                                                                        echo "<tr><td class='inner-col'><span class='collapse-content' id='collapsed-".($id)."'>".__("No recommendations found, great work!")."</span></td></tr>";
                                                                    }
                                                                }
                                                            } ?>
                                                            </tbody>
                                                        </table>
                                                        <h3><?php _e('No Impact Audits'); ?></h3>
                                                        <table class="pagespeed-table">
                                                            <tbody>
                                                            <?php foreach ($pageData as $id => $pageDatum) {
                                                                if( isset($pageDatum['_impactScore']) && $pageDatum['_impactScore'] === 0 ){
                                                                    $title = (isset($pageDatum['displayValue']) && $pageDatum['displayValue']) ? $pageDatum['title'].' ('.$pageDatum['displayValue'].')' : $pageDatum['title'];
                                                                    echo "<tr><td><span class='collapse-lbl' data-target='collapsed-".($id)."'>".$title."</span></td></tr>";
                                                                    if( isset($pageDatum['description']) && $pageDatum['description'] ){
                                                                        echo "<tr><td class='inner-col'><span class='collapse-content' id='collapsed-".($id)."'>".str_ireplace("[Learn more]", "", $pageDatum['description'])."<br/>";
                                                                        if( isset($pageDatum['details']['type']) && $pageDatum['details']['type'] == 'table' ){
                                                                            $heads = $pageDatum['details']['headings'];
                                                                            echo "<table class='wd-100'><thead><tr>";
                                                                            foreach ($heads as $head){
                                                                                if( in_array($head['itemType'], ['text','numeric','url','bytes','ms']) ){
                                                                                    echo "<th>".$head['text']."</th>";
                                                                                }
                                                                            }
                                                                            echo "</tr></thead><tbody>";
                                                                            foreach ($pageDatum['details']['items'] as $item){
                                                                                echo "<tr>";
                                                                                foreach ($heads as $head){
                                                                                    if( in_array($head['itemType'], ['text','numeric','url','bytes','ms']) ){
                                                                                        if( isset($item[$head['key']]) ){
                                                                                            echo "<td>".$item[$head['key']]."</td>";
                                                                                        } else {
                                                                                            echo "<td></td>";
                                                                                        }
                                                                                    }
                                                                                }
                                                                                echo "</tr>";
                                                                            }
                                                                            echo "</tbody></table>";
                                                                        }
                                                                        echo "</span></td></tr>";
                                                                    } else {
                                                                        echo "<tr><td class='inner-col'><span class='collapse-content' id='collapsed-".($id)."'>".__("No recommendations found, great work!")."</span></td></tr>";
                                                                    }
                                                                }
                                                            } ?>
                                                            </tbody>
                                                        </table>
                                                    <?php } else {
                                                        echo "<p>".__("No data found.")."</p>";
                                                    } ?>
                                                </div>
                                            </div>
                                            <?php
                                            $pageData = array();
                                            $pageSpeedPath = $toolkit_uploads . "hardata-{$scanHistory['test_id']}.txt";
                                            if ( file_exists($pageSpeedPath) ) {
                                                $myfile = fopen($pageSpeedPath, "r");
                                                $pagespeedJson = fread( $myfile, filesize($pageSpeedPath) );
                                                fclose($myfile);
                                                if( $pagespeedJson ){
                                                    $pagespeed = json_decode($pagespeedJson, true);
                                                    if( isset($pagespeed['log']['entries']) ){
                                                        $pageData = $pagespeed['log']['entries'];
                                                    }
                                                }
                                            } ?>
                                            <div id="tabcontent-hardata" class="inner-tab-content">
                                                <div class="table-responsive" id="pagespeed-table">
                                                    <div class="widget-panel-title"><?php _e('Waterfall'); ?></div>
                                                    <?php if( $pageData ){
                                                        $baseTime = 0; ?>
                                                        <table class="pagespeed-table">
                                                            <thead>
                                                            <tr>
                                                                <th><?php _e('URL'); ?></th>
                                                                <th><?php _e('Status'); ?></th>
                                                                <th><?php _e('Size'); ?></th>
                                                                <th><?php _e('Timeline'); ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($pageData as $id => $pageDatum) {
                                                                echo "<tr><td><span class='file-info'><span class='file-name'>".basename($pageDatum['request']['url'])."</span><span class='file-url'>".$pageDatum['request']['url']."</span></span></td>";
                                                                echo "<td>".$pageDatum['response']['status']."</td>";
                                                                echo "<td>".$obj->formatSizeUnits($pageDatum['response']['bodySize'])."</td>";
                                                                echo "<td class='timeline'><span class='wtimeline' style='width: ".(round($pageDatum['time']/10))."px; margin-left:".$baseTime."px'></span><small>".$pageDatum['time']."ms</small></td></tr>";
                                                                if( $id === 0 ){
                                                                    $baseTime = round($pageDatum['time']/10);
                                                                }
                                                            } ?>
                                                            </tbody>
                                                        </table>
                                                    <?php } else {
                                                        echo "<p>".__("No data found.")."</p>";
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <div class="gtmetrix-history" id="gtmetrix-history">
                                <?php echo $obj->getGtmetrixScanHistory($obj->limit, $offset); ?>
                            </div>
                        <?php } else { ?>
                            <h4 class="text-center no-history"><?php _e('Please Run Your First Scan To See Your Results.'); ?></h4>
                        <?php } ?>
                    </div>
                    <div class="single-tab" id="unload-options">
                        <div class="unload-options-section">
                            <?php include 'unload-options.php'; ?>
                        </div>
                    </div>
                    <?php include_once 'cache-manager.php'; ?>
                    <?php include_once 'booster-css.php' ?>
                    <?php include_once 'booster-javascript.php' ?>
                    <?php include_once 'booster-prefetch-dns.php' ?>
                    <?php include_once 'booster-cdn.php'; ?>
                    <?php include_once 'booster-font-handling.php'; ?>
                    <?php include_once 'booster-media-handling.php'; ?>
                    <?php if( $is_apache ){ ?>
                        <div class="single-tab" id="additional-tweaks">
                            <?php include 'misc-tweaks.php'; ?>
                        </div>
                    <?php } ?>
                    <?php include_once 'booster-script-ninja.php'; ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="not-active-notice">
            <?php _e('Oops, looks like you do not have an active license yet, please activate your license first'); ?>
        </div>
    <?php }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
}
if( ! function_exists('toolkit_sort_page_descending') ){
    function toolkit_sort_page_descending($a, $b) {
        return ($a['impact'] > $b['impact']) ? -1 : 1;
    }
}
if( ! function_exists('toolkit_sort_yslow_descending') ){
    function toolkit_sort_yslow_descending($a, $b) {
        return ($a['score'] < $b['score']) ? -1 : 1;
    }
}

if( ! function_exists('toolkit_get_css_color_class') ){
    function toolkit_get_css_color_class($value) {
        if( $value > 89 ){
            $class = array('cls'=>'metrix-acolor', 'lbl'=>'A');
        } elseif( $value > 79 ){
            $class = array('cls'=>'metrix-bcolor', 'lbl'=>'B');
        } elseif( $value > 69 ){
            $class = array('cls'=>'metrix-ccolor', 'lbl'=>'C');
        } elseif( $value > 59 ){
            $class = array('cls'=>'metrix-dcolor', 'lbl'=>'D');
        } elseif( $value > 49 ){
            $class = array('cls'=>'metrix-ecolor', 'lbl'=>'E');
        } else {
            $class = array('cls'=>'metrix-fcolor', 'lbl'=>'F');
        }
        return $class;
    }
}
