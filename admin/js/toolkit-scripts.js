jQuery.fn.setLoading = function(pct) {
    var indicatorID = jQuery(this).attr('id');
    $('#loading-indicator-' + indicatorID).html(pct + '%');
};
jQuery.fn.showLoading = function(options) {
    var indicatorID;
    var settings = {
        'addClass': '',
        'beforeShow': '',
        'afterShow': '',
        'hPos': 'center',
        'vPos': 'center',
        'indicatorZIndex' : 5001,
        'overlayZIndex': 5000,
        'parent': '',
        'waitingText' : '',
        'marginTop': 0,
        'marginLeft': 0,
        'overlayWidth': null,
        'overlayHeight': null
    };
    jQuery.extend(settings, options);
    var loadingDiv = jQuery('<div style="text-align:center"></div>');
    var loadingTextDiv = jQuery('<div style="text-align:center">'+settings.waitingText+'</div>');
    var overlayDiv = jQuery('<div></div>');
    if ( settings.indicatorID ) {
        indicatorID = settings.indicatorID;
    } else {
        indicatorID = jQuery(this).attr('id');
    }
    jQuery(loadingDiv).attr('id', 'loading-indicator-' + indicatorID );
    jQuery(loadingDiv).addClass('loading-indicator');
    jQuery(loadingTextDiv).attr('id', 'loading-indicator-text' );
    jQuery(loadingTextDiv).addClass('loading-indicator-text');
    if ( settings.addClass ){
        jQuery(loadingDiv).addClass(settings.addClass);
    }
    jQuery(overlayDiv).css('display', 'none');
    jQuery(document.body).append(overlayDiv);
    jQuery(overlayDiv).attr('id', 'loading-indicator-' + indicatorID + '-overlay');
    jQuery(overlayDiv).addClass('loading-indicator-overlay');
    if ( settings.addClass ){
        jQuery(overlayDiv).addClass(settings.addClass + '-overlay');
    }
    var overlay_width;
    var overlay_height;
    var border_top_width = jQuery(this).css('border-top-width');
    var border_left_width = jQuery(this).css('border-left-width');
    border_top_width = isNaN(parseInt(border_top_width)) ? 0 : border_top_width;
    border_left_width = isNaN(parseInt(border_left_width)) ? 0 : border_left_width;
    var overlay_left_pos = jQuery(this).offset().left + parseInt(border_left_width);// +  $(document.body).css( "border-left" );
    var overlay_top_pos = jQuery(this).offset().top + parseInt(border_top_width);
    if ( settings.overlayWidth !== null ) {
        overlay_width = settings.overlayWidth;
    } else {
        overlay_width = parseInt(jQuery(this).width()) + parseInt(jQuery(this).css('padding-right')) + parseInt(jQuery(this).css('padding-left'));
    }
    if ( settings.overlayHeight !== null ) {
        overlay_height = settings.overlayWidth;
    } else {
        overlay_height = parseInt(jQuery(this).height()) + parseInt(jQuery(this).css('padding-top')) + parseInt(jQuery(this).css('padding-bottom'));
    }
    jQuery(overlayDiv).css('width', overlay_width.toString() + 'px');
    jQuery(overlayDiv).css('height', overlay_height.toString() + 'px');
    jQuery(overlayDiv).css('left', overlay_left_pos.toString() + 'px');
    jQuery(overlayDiv).css('position', 'absolute');
    jQuery(overlayDiv).css('top', overlay_top_pos.toString() + 'px' );
    jQuery(overlayDiv).css('z-index', settings.overlayZIndex);
    if ( settings.overlayCSS ) {
        jQuery(overlayDiv).css ( settings.overlayCSS );
    }
    jQuery(loadingDiv).css('display', 'none');
    jQuery(document.body).append(loadingDiv);
    jQuery(loadingTextDiv).css('display', 'none');
    jQuery(document.body).append(loadingTextDiv);
    jQuery(loadingDiv).css('position', 'absolute');
    jQuery(loadingDiv).css('z-index', settings.indicatorZIndex);
    jQuery(loadingTextDiv).css('position', 'absolute');
    jQuery(loadingTextDiv).css('z-index', settings.indicatorZIndex);
    var indicatorTop = overlay_top_pos;
    if ( settings.marginTop ) {
        indicatorTop += parseInt(settings.marginTop);
    }
    var indicatorLeft = overlay_left_pos;
    if ( settings.marginLeft ) {
        indicatorLeft += parseInt(settings.marginTop);
    }
    if ( settings.hPos.toString().toLowerCase() == 'center' ) {
        jQuery(loadingDiv).css('left', (indicatorLeft + ((jQuery(overlayDiv).width() - parseInt(jQuery(loadingDiv).width())) / 2)).toString()  + 'px');
        jQuery(loadingTextDiv).css('left', (indicatorLeft + ((jQuery(overlayDiv).width() - parseInt(jQuery(loadingTextDiv).width())) / 2)).toString()  + 'px');
    } else if ( settings.hPos.toString().toLowerCase() == 'left' ) {
        jQuery(loadingDiv).css('left', (indicatorLeft + parseInt(jQuery(overlayDiv).css('margin-left'))).toString() + 'px');
        jQuery(loadingTextDiv).css('left', (indicatorLeft + parseInt(jQuery(overlayDiv).css('margin-left'))).toString() + 'px');
    } else if ( settings.hPos.toString().toLowerCase() == 'right' ) {
        jQuery(loadingDiv).css('left', (indicatorLeft + (jQuery(overlayDiv).width() - parseInt(jQuery(loadingDiv).width()))).toString()  + 'px');
        jQuery(loadingTextDiv).css('left', (indicatorLeft + (jQuery(overlayDiv).width() - parseInt(jQuery(loadingTextDiv).width()))).toString()  + 'px');
    } else {
        jQuery(loadingDiv).css('left', (indicatorLeft + parseInt(settings.hPos)).toString() + 'px');
        jQuery(loadingTextDiv).css('left', (indicatorLeft + parseInt(settings.hPos)).toString() + 'px');
    }
    if ( settings.vPos.toString().toLowerCase() == 'center' ) {
        jQuery(loadingDiv).css('top', (indicatorTop + ((jQuery(overlayDiv).height() - parseInt(jQuery(loadingDiv).height())) / 2)).toString()  + 'px');
        jQuery(loadingTextDiv).css('top', (indicatorTop + ((jQuery(overlayDiv).height() - parseInt(jQuery(loadingTextDiv).height())) / 1.75)).toString()  + 'px');
    } else if ( settings.vPos.toString().toLowerCase() == 'top' ) {
        jQuery(loadingDiv).css('top', indicatorTop.toString() + 'px');
        jQuery(loadingTextDiv).css('top', indicatorTop.toString() + 'px');
    } else if ( settings.vPos.toString().toLowerCase() == 'bottom' ) {
        jQuery(loadingDiv).css('top', (indicatorTop + (jQuery(overlayDiv).height() - parseInt(jQuery(loadingDiv).height()))).toString()  + 'px');
        jQuery(loadingTextDiv).css('top', (indicatorTop + (jQuery(overlayDiv).height() - parseInt(jQuery(loadingDiv).height()))).toString()  + 'px');
    } else {
        jQuery(loadingDiv).css('top', (indicatorTop + parseInt(settings.vPos)).toString() + 'px' );
        jQuery(loadingTextDiv).css('top', (indicatorTop + parseInt(settings.vPos)).toString() + 'px' );
    }
    if ( settings.css ) {
        jQuery(loadingDiv).css ( settings.css );
        jQuery(loadingTextDiv).css ( settings.css );
    }
    var callback_options = {
		'overlay': overlayDiv,
		'indicator': loadingDiv,
		'element': this
	};
    if ( typeof(settings.beforeShow) == 'function' ) {
        settings.beforeShow( callback_options );
    }
    jQuery(overlayDiv).show();
    jQuery(loadingDiv).show();
    jQuery(loadingTextDiv).show();
    if ( typeof(settings.afterShow) == 'function' ) {
        settings.afterShow( callback_options );
    }
    return this;
};
jQuery.fn.hideLoading = function(options) {
    var settings = {};
    jQuery.extend(settings, options);
    if ( settings.indicatorID ) {
        indicatorID = settings.indicatorID;
    } else {
        indicatorID = jQuery(this).attr('id');
    }
    jQuery(document.body).find('#loading-indicator-text' ).remove();
    jQuery(document.body).find('#loading-indicator-' + indicatorID ).remove();
    jQuery(document.body).find('#loading-indicator-' + indicatorID + '-overlay' ).remove();
    return this;
};
function set_js_cookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function get_js_cookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function toolkit_get_success_message(group, index){
    var messages = {
        code_cleaner: [
            'Emojis have been re-enabled successfully', //0
            'Emojis have been disabled successfully', //1
            'Dashicons have been re-enabled successfully', //2
            'Dashicons have been disabled successfully', //3
            'oEmbed has been re-enabled successfully', //4
            'oEmbed has been disabled successfully', //5
            'RSS Feeds have been re-enabled successfully', //6
            'RSS Feeds have been disabled successfully', //7
            'XML-RPC has been re-enabled successfully', //8
            'XML-RPC has been disabled successfully', //9
            'Comments have been re-enabled successfully', //10
            'Comments have been disabled successfully', //11
            'Rest API has been re-enabled successfully', //12
            'Rest API has been disabled successfully', //13
            'Gutenberg CSS Block Library has been re-enabled successfully', //14
            'Gutenberg CSS Block Library has been disabled successfully', //15
            'Query Strings have been re-enabled successfully', //16
            'Query Strings have been disabled successfully', //17
            'Rest API Links have been re-enabled successfully', //18
            'Rest API Links have been disabled successfully', //19
            'RSS Feed Links have been re-enabled successfully', //20
            'RSS Feed Links have been disabled successfully', //21
            'Really Simple Discovery (RSD) Link has been re-enabled successfully', //22
            'Really Simple Discovery (RSD) Link has been disabled successfully', //23
            'WP Shortlinks have been re-enabled successfully', //24
            'WP Shortlinks have been disabled successfully', //25
            'Windows Live Writer Link has been re-enabled successfully', //26
            'Windows Live Writer Link has been disabled successfully', //27
            'Disable Rest API setting has been updated successfully', //28
            'Gutenberg CSS Block Library setting has been updated successfully', //29
            'jQuery Migrate has been re-enabled successfully', //30
            'jQuery Migrate has been disabled successfully', //31
        ],
        database_cleaner: [
            'Post Revisions will no longer be cleaned by ToolKit', //0
            'Post Revisions will be cleaned by ToolKit', //1
            'Post Auto Drafts will no longer be cleaned by ToolKit', //2
            'Post Auto Drafts will be cleaned by ToolKit', //3
            'Trashed Posts will no longer be cleaned by ToolKit', //4
            'Trashed Posts will be cleaned by ToolKit', //5
            'Spam Comments will no longer be cleaned by ToolKit', //6
            'Spam Comments will be cleaned by ToolKit', //7
            'Trashed Comments will no longer be cleaned by ToolKit', //8
            'Trashed Comments will be cleaned by ToolKit', //9
            'Expired Transients will no longer be cleaned by ToolKit', //10
            'Expired Transients will be cleaned by ToolKit', //11
            'All Transients will no longer be cleaned by ToolKit', //12
            'All Transients will be cleaned by ToolKit', //13
            'Optimize Tables will no longer be cleaned by ToolKit', //14
            'Optimize Tables will be cleaned by ToolKit', //15
            'DB Automatic Cleaning preferences successfully updated', //16
        ],
        cache_manager: [
            'ToolKit Caching has been disabled successfully', //0
            'ToolKit Caching has been enabled successfully', //1
            'Automatic Cache Clearing preferences updated successfully', //2
            'Cache Expiration preferences updated successfully', //3
            'Preload Cache has been disabled successfully', //4
            'ToolKit will now preload cache for site visitors', //5
            'Preload Cache schedule updated successfully', //6
            'Cache Exclusions updated successfully', //7
            'ToolKit will no longer optimize cached content for logged-in users', //8
            'ToolKit will now serve cached content to logged-in users', //9
        ],
        booster_main: [
            'CDN support has been disabled successfully', //0
            'CDN support is now enabled', //1
            'CDN settings updated successfully', //2
            'CDN Exclusions updated successfully', //3
            'CSS Minification has been disabled successfully', //4
            'CSS Minification is now enabled', //5
            'Combine CSS has been disabled successfully', //6
            'Combine CSS is now enabled', //7
            'Combining CSS will include Elementor core files', //8
            'Combining CSS will no longer include Elementor core files', //9
            'Exclusions updated successfully', //10
            'JS Minification has been disabled successfully', //11
            'JS Minification has been enabled successfully', //12
            'Combine JS has been disabled successfully', //13
            'Combine JS has been enabled successfully', //14
            'Combining JS will include Elementor core files', //15
            'Combining JS will no longer include Elementor core files', //16
            'Exclusions updated successfully', //17
            'Defer JS has been disabled successfully', //18
            'Defer JS has been enabled successfully', //19
            'Defer Inline JS has been disabled successfully', //20
            'Defer Inline JS has been enabled successfully', //21
            '', //22
            '', //23
            'Defer JS will now apply site-wide', //24
            'Defer JS will apply on the Homepage Only', //25
            'Exclusions updated successfully', //26
            'Delay JS has been disabled successfully', //27
            'Delay JS is now enabled', //28
            'Delay JS settings updated successfully', //29
            'ToolKit will no longer optimize or locally host Google Fonts', //30
            'ToolKit will now locally host and optimize Google Fonts', //31
            'Enable Fallback Fonts has been disabled successfully', //32
            'Enable Fallback Fonts has been enabled successfully', //33
            'Preload Fonts settings updated successfully', //34
            'Lazy Render Elements updated successfully', //35
        ],
        prefetch_dns: [
            'Prefetch DNS has been disabled successfully', //0
            'Prefetch DNS has been enabled successfully', //1
            'Prefetch DNS settings updated successfully', //2
        ],
        media_loading: [
            'Lazy Load has been disabled successfully', //0
            'Lazy Load has been enabled successfully', //1
            'Lazy Load Method updated successfully', //2
            'Setting updated successfully', //3
            'Lazy Load exclusions updated successfully', //4
            'ToolKit will no longer Preload Above the Fold Images', //5
            'ToolKit will now Preload Above the Fold Images', //6
            'ToolKit will no longer Add Image Width & Height attributes to images', //7
            'ToolKit will now Add Image Width & Height attributes to images', //8
            'Lazy Load Video has been disabled successfully', //9
            'Lazy Load Video has been enabled successfully', //10
            'Lazy Load Iframe has been disabled successfully', //11
            'Lazy Load Iframe has been enabled successfully', //12
            'Optimize Youtube iFrames has been disabled successfully', //13
            'Optimize Youtube iFrames has been enabled successfully', //14
            'Youtube Placeholder thumbnails will no longer be self-hosted', //15
            'Youtube Placeholder thumbnails will now be self-hosted', //16
        ],
        server_tweaks: [
            'GZip Compression has been disabled successfully', //0
            'GZip Compression has been enabled successfully', //1
            'Enable Keep-Alive Connections has been disabled successfully', //2
            'Enable Keep-Alive Connections has been enabled successfully', //3
            'Enable Entity Tags (ETags) has been disabled successfully', //4
            'Enable Entity Tags (ETags) has been enabled successfully', //5
            'Leverage Browser Caching & Expires Headers has been disabled successfully', //6
            'Leverage Browser Caching & Expires Headers has been enabled successfully', //7
            'Specify a Vary: Accept-Encoding Header has been disabled successfully', //8
            'Specify a Vary: Accept-Encoding Header has been enabled successfully', //9
        ],
        theme_dashing: [
            'Admin Dashboard Template updated successfully', //0
            'Your Widget Title will no longer be displayed', //1
            'Widget Title enabled successfully', //2
            'Your custom dashboard widget is no longer dismissable', //3
            'Your custom dashboard widget is now dismissable', //4
            'Update Login URL has been disabled successfully', //5
            'Update Login URL has been enabled successfully', //6
            'New Login URL updated successfully', //7
            'Invalid Login Attempts URL updated successfully', //8
            'Background Size updated successfully', //9
            'Background Repeat setting updated successfully', //10
            'Logo Width updated successfully', //11
            'Logo Height updated successfully', //12
            'Logo URL updated successfully', //13
            'Custom CSS updated successfully', //14
            'Themeless has been disabled successfully', //15
            'Themeless has been enabled successfully', //16
        ],
        core_manager: [
            'WP\'s default sitemap generation processes re-enabled successfully', //0
            'WP\'s default sitemap generation processes are now disabled', //1
            'Automatic updates for WP Core have been re-enabled successfully', //2
            'Automatic updates for WP Core have been disabled successfully', //3
            'Automatic updates for Plugins have been re-enabled successfully', //4
            'Automatic updates for Plugins have been disabled successfully', //5
            'Automatic updates for themes have been re-enabled successfully', //6
            'Automatic updates for themes have been disabled successfully', //7
            'Modify Heartbeat Settings disabled successfully', //8
            'Modify Heartbeat Settings enabled successfully', //9
            'Heartbeat Frequency updated successfully', //10
            'Modify Post Revision Limits disabled successfully', //11
            'Modify Post Revision Limits enabled successfully', //12
            'Post Revision Limit updated successfully', //13
            'Modify Autosave Intervals disabled successfully', //14
            'Modify Autosave Intervals enabled successfully', //15
            'Autosave Interval updated successfully', //16
        ]
    };
    return messages[group][index];
}
function toolkit_get_random_string(strLength) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < strLength; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
function toolkit_save_admin_settings(postData, message, reload = false){
    jQuery.ajax({
        type: "POST",
        url: toolkit.ajax_url,
        dataType: "json",
        data: postData,
        beforeSend: function(){
            jQuery('#wpbody-content').showLoading();
        },
        success: function(response){
            jQuery('#wpbody-content').hideLoading();
            var randID = toolkit_get_random_string(15);
            if( response.success ){
                if( ! message || typeof message === 'undefined' ){
                    message = response.message;
                }
                jQuery('.toolkit-message').show().append('<p class="success-msg '+randID+'"><b>Success:</b><br/>'+message+'</p>');
                if( typeof response.msg2 !== 'undefined' ){
                    setTimeout(function(){
                        jQuery('.toolkit-message').append('<p class="success-msg '+randID+'"><b>Success:</b><br/>'+response.msg2+'</p>');
                    }, 500);
                }
                if( reload ){
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                }
                if( typeof response.alert !== 'undefined' ){
                    alert(response.alert);
                }
            } else {
                jQuery('.toolkit-message').show().append('<p class="error-msg '+randID+'"><b>Error:</b><br/>'+response.message+'</p>');
            }
            setTimeout(function(){
                jQuery('.'+randID).remove();
            }, 5000);
        },
        error: function(request, status, error) {
            jQuery('#wpbody-content').hideLoading();
        }
    });
}
jQuery(document).ready(function ($) {
	$('.tabs-holder .tab-nav li').on('click', function(){
        set_js_cookie('sub_menu_sub_tab', $(this).data('tabid'), 7);
	});
    //toolkit tabs toggle
    var toolkitTabs = '.tab-nav ul li';
	$(document).on('click', toolkitTabs, function () {
		var tabsHolder = $(this).closest('.tabs-holder');
		tabsHolder.find(toolkitTabs).removeClass('active-tab');
		var tabId = $(this).data('tabid');
		$(this).addClass('active-tab');
		tabsHolder.find('.content-tab .single-tab').hide();
		$( '#' + tabId ).fadeIn('slow');
	});
	if( $(toolkitTabs).length > 0 ){
        var active_sub_tab = get_js_cookie('sub_menu_sub_tab');
        if( active_sub_tab ){
            $('li[data-tabid="'+active_sub_tab+'"]').trigger('click');
        } else {
            $('.tab-nav ul li:eq(0)').trigger('click');
		}
    }
    $(document).on('click', '.inner-tab-heading', function () {
        var tabsHolder = $(this).closest('.normal-inner-tabs');
        tabsHolder.find('.inner-tab-heading').removeClass('active-heading');
        $(this).addClass('active-heading');
        tabsHolder.find('.inner-tab-content').removeClass('active-content');
        $( '#tabcontent-' + $(this).data('ptype') ).addClass('active-content');
    });
	if( typeof $.fn.select2 !== 'undefined' ){
        $('.post-type-script').select2();
    }
    if( $('.collapse-lbl').length > 0 ){
        $('.collapse-lbl').on('click', function () {
            $('#'+$(this).data('target')).toggleClass('open');
        });
    }
    var leftRowSlctr = $('.gtmetrix-report .report-left > .row');
    if( leftRowSlctr.length > 0 ){
        var rightRowSlctr = $('.gtmetrix-report .report-right > .row');
        if( leftRowSlctr.outerHeight() > rightRowSlctr.outerHeight() ){
            rightRowSlctr.css({'height':leftRowSlctr.outerHeight()+'px'});
		} else {
            leftRowSlctr.css({'height':rightRowSlctr.outerHeight()+'px'});
		}
	}
	var delay = 5000;
	var fadeSpeed = 'slow';

	jQuery('body').on('click', '.toolkit-performance #gtmetrix-scan-history .download-full-report', function() {
		var report_url = jQuery(this).data('full_report');
		var testid = jQuery(this).data('testid');
		if(report_url && testid){
			jQuery.ajax({
				type: "POST",
				url: toolkit.ajax_url,
				dataType: "html",
				data: {action:'toolkit_performance_gtmetrix_download_report',report_url:report_url,testid:testid},
				beforeSend: function(){
					jQuery('#gtmetrix-history-section').showLoading({'addClass': 'loading-indicator-bars'});
				},
				complete: function(){
				},
				success: function(response){
					jQuery('#gtmetrix-history-section').hideLoading();
					var json = jQuery.parseJSON(response);
					if(json.status){
						if(json.hasOwnProperty('report')){
							var a = document.createElement("a");
							a.href = 'data:application/pdf;base64,'+json.report;
							a.download = 'report_pdf-'+testid+".pdf"; //update for filename
							document.body.appendChild(a);
							a.click();
							// remove `a` following `Save As` dialog,
							// `window` regains `focus`
							window.onfocus = function () {
								document.body.removeChild(a)
							}
						}
						jQuery.ajax({
							type: "POST",
							url: toolkit.ajax_url,
							dataType: "html",
							data: {action:'toolkit_performance_gtmetrix_scan_result'},
							beforeSend: function(){
								jQuery('.toolkit-gtmetrix-section').showLoading();
							},
							complete: function(){
							},
							success: function(response){
								jQuery('.toolkit-gtmetrix-section').hideLoading();
								jQuery('.toolkit-performance').html(response);
							},
							error: function(request, status, error) {
								alert(status);
							}
						});
					} else {
						jQuery('.toolkit-message').removeClass('updated').addClass('error').show().html('<p>'+json.message+'</p>').delay(delay).fadeOut(fadeSpeed);
						jQuery("html, body").animate({ scrollTop: 0 }, "slow");
					}
				},
				error: function(request, status, error) {
					alert(status);
				}
			});
		} else {
			jQuery('.toolkit-message').removeClass('updated').addClass('error').show().html('<p>Report URL missing.</p>').delay(delay).fadeOut(fadeSpeed);
			jQuery("html, body").animate({ scrollTop: 0 }, "slow");
		}
	});

	jQuery('body').on('click', '.toolkit-performance .toolkit-gtmetrix-section button', function() {
		var elem = jQuery(this);
		var scan_url = jQuery(".toolkit-gtmetrix-section input[name=scan_url]").val();
		if(validURL(scan_url) === false){
			jQuery('.toolkit-message').removeClass('updated').addClass('error').show().html('<p>Error: Please Enter a valid URL to test.</p>').delay(delay).fadeOut(fadeSpeed);
			jQuery("html, body").animate({ scrollTop: 0 }, "slow");
			return false;
		}
		var scan_location = jQuery(".toolkit-gtmetrix-section select[name=location] option:selected").val();
		var scan_browser = jQuery(".toolkit-gtmetrix-section select[name=browser] option:selected").val();
		var _nonce = jQuery(".toolkit-gtmetrix-section input[name=_nonce]").val();
        jQuery.ajax({
            type: "POST",
            url: toolkit.ajax_url,
            dataType: "json",
            data: {action:'toolkit_performance_gtmetrix_scan',scan_url:scan_url,scan_location:scan_location,scan_browser:scan_browser,_nonce:_nonce},
            beforeSend: function(){
                jQuery('.toolkit-gtmetrix-section').showLoading({'addClass': 'loading-indicator-bars',waitingText : 'Performing Scan Now, Please Wait'});
            },
            success: function(response){
                jQuery('.toolkit-gtmetrix-section').hideLoading();
                if( response.status === 1 ){
                    jQuery('.toolkit-message').show().append('<p class="success-msg gtmetrix-message"><b>Success:</b><br/>'+response.message+'</p>');
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                } else {
                    jQuery('.toolkit-message').show().append('<p class="error-msg gtmetrix-message"><b>Error:</b><br/>'+response.message+'</p>');
                }
            },
            error: function(request, status, error) {
                alert(status);
            }
        });
	});

	jQuery('body').on('change', '.toolkit-performance #gtmetrix-package-section select[name=gtmetrix-packages]', function() {
		var url = jQuery(this).val();
		if(url){
			// window.open(url,'_blank');
			window.location.href = url;
		}
	});

    //SAVE CACHE SETTING
    jQuery('#toolkit_cache_pages, #toolkit_preload_cache, #toolkit_cache_loggedin, .save-cache-settings').on('click', function() {
        var postData = {action: 'toolkit_cache_setting_save'};
        postData.cache_pages = ( $('#toolkit_cache_pages').prop('checked') === true ) ? 'on' : 'off';
        postData.cache_purge = $('#toolkit_cache_purge').val();
        postData.cache_lifespan = $('#toolkit_cache_lifespan').val();
        postData.cache_exclude = $('#toolkit_cache_exclude').val();
        postData.preload_cache = ( $('#toolkit_preload_cache').prop('checked') === true ) ? 'on' : 'off';
        postData.preload_lifespan = $('#toolkit_preload_lifespan').val();
        postData.cache_loggedin = ( $('#toolkit_cache_loggedin').prop('checked') === true ) ? 'on' : 'off';
        postData._nonce = $(".minification-setting-section input[name=_nonce]").val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('cache_manager', index);
        toolkit_save_admin_settings(postData, message);
        if( $(this).attr('id') === 'toolkit_preload_cache' && $('#toolkit_preload_cache').prop('checked') === true ){
            setTimeout(function(){
                $('#run-preload-cache').trigger('click');
            }, 1000);
        }
    });

    jQuery('#run-preload-cache').on('click', function() {
        var postData = {action: 'toolkit_run_preload_cache'};
        postData._nonce = $(".minification-setting-section input[name=_nonce]").val();
        toolkit_save_admin_settings(postData, '', true);
    });

    //SAVE DB OPTIMIZATION SETTING
    var database_options = "#toolkit_posts_revs, #toolkit_auto_drafts, #toolkit_trash_posts, #toolkit_spam_comments, #toolkit_trash_comments, #toolkit_exp_transients, #toolkit_all_transients, #toolkit_opt_tables";
    jQuery('.save-database-settings, '+database_options).on('click', function() {
        var postData = {action: 'toolkit_dbopt_setting_save'};
        postData.posts_revs = ( $('#toolkit_posts_revs').prop('checked') === true ) ? 'on' : 'off';
        postData.auto_drafts = ( $('#toolkit_auto_drafts').prop('checked') === true ) ? 'on' : 'off';
        postData.trash_posts = ( $('#toolkit_trash_posts').prop('checked') === true ) ? 'on' : 'off';
        postData.spam_comments = ( $('#toolkit_spam_comments').prop('checked') === true ) ? 'on' : 'off';
        postData.trash_comments = ( $('#toolkit_trash_comments').prop('checked') === true ) ? 'on' : 'off';
        postData.exp_transients = ( $('#toolkit_exp_transients').prop('checked') === true ) ? 'on' : 'off';
        postData.all_transients = ( $('#toolkit_all_transients').prop('checked') === true ) ? 'on' : 'off';
        postData.opt_tables = ( $('#toolkit_opt_tables').prop('checked') === true ) ? 'on' : 'off';
        postData.dbauto_clean = $('#toolkit_dbauto_clean').val();
        postData._nonce = $(".minification-setting-section input[name=_nonce]").val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('database_cleaner', index);
        toolkit_save_admin_settings(postData, message);
    });

	//SAVE SETTING
    $(document).on('click', '#cdn-holder-table tbody .save-cdn-settings.jsbtn', function () {
        $('.save-cdn-settings:eq(0)').trigger('click');
    });
    var css_fonts_options = "#toolkit_css_minify, #toolkit_css_combine, #toolkit_css_excelem, #toolkit_google_fonts, #toolkit_fallback_fonts,";
    var js_options = "#toolkit_js_minify, #toolkit_js_combine, #toolkit_js_excelem, #toolkit_js_defer, #toolkit_jsdefer_inline, #toolkit_defer_homeonly, #toolkit_js_delay,";
	jQuery(css_fonts_options + ' .save-cssminify-settings, '+js_options+' .save-javascript-settings, .save-cdn-settings, #toolkit_cdn_enable, .save-fonts-settings').on('click', function() {
        var postData = {action: 'toolkit_server_setting_save'};
        postData.css_minify = ( $('#toolkit_css_minify').prop('checked') === true ) ? 'on' : 'off';
        postData.css_combine = ( $('#toolkit_css_combine').prop('checked') === true ) ? 'on' : 'off';
        postData.css_excelem = ( $('#toolkit_css_excelem').prop('checked') === true ) ? 'on' : 'off';
        postData.js_minify = ( $('#toolkit_js_minify').prop('checked') === true ) ? 'on' : 'off';
        postData.js_combine = ( $('#toolkit_js_combine').prop('checked') === true ) ? 'on' : 'off';
        postData.js_excelem = ( $('#toolkit_js_excelem').prop('checked') === true ) ? 'on' : 'off';
        postData.js_defer = ( $('#toolkit_js_defer').prop('checked') === true ) ? 'on' : 'off';
        postData.js_delay = ( $('#toolkit_js_delay').prop('checked') === true ) ? 'on' : 'off';
        postData.delayed_hkeywords = $('#delayed-hkeywords').val();
        postData.delayed_keywords = $('#delayed-keywords').val();
        postData.delayed_expages = $('#delayed-expages').val();
        postData.defer_homeonly = ( $('#toolkit_defer_homeonly').prop('checked') === true ) ? 'on' : 'off';
        postData.jsdefer_inline = ( $('#toolkit_jsdefer_inline').prop('checked') === true ) ? 'on' : 'off';
        postData.deferred_keywords = $('#deferred_keywords').val();
        postData.exclude_css_urls = $('#excluded-css-urls').val();
        postData.lazy_render = $('#lazy-render-words').val();
        postData.exclude_js_urls = $('#excluded-js-urls').val();
        postData.cdn_enable = ( $('#toolkit_cdn_enable').prop('checked') === true ) ? 'yes' : 'no';
        postData.cdn_url = $('input[name="toolkit_cdn_url[]"]').map(function(){return $(this).val()}).get();
        postData.cdn_files = $('select[name="toolkit_cdn_files[]"]').map(function(){return $(this).val()}).get();
        postData.exclude_cdn_urls = $('#excluded-cdn-urls').val();
        postData.google_fonts = ( $('#toolkit_google_fonts').prop('checked') === true ) ? 'on' : 'off';
        postData.fallback_fonts = ( $('#toolkit_fallback_fonts').prop('checked') === true ) ? 'on' : 'off';
        postData.preload_fonts = $('#toolkit_preload_fonts').val();
        postData._nonce = $(".minification-setting-section input[name=_nonce]").val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        if( $(this).attr('id') === 'toolkit_js_defer' && $(this).prop('checked') === true ){
            postData.jsdefer_inline = 'on';
            $('#toolkit_jsdefer_inline').prop('checked', true);
        }
        var message = toolkit_get_success_message('booster_main', index);
        toolkit_save_admin_settings(postData, message);
	});

    jQuery('#toolkit_pre_dns, .save-prefetch-settings').on('click', function() {
        var postData = {action: 'toolkit_prefetch_setting_save'};
        postData.pre_dns = ( $('#toolkit_pre_dns').prop('checked') === true ) ? 'on' : 'off';
        postData.hkeywords = $('#prefetch-hkeywords').val();
        postData.keywords = $('#prefetch-keywords').val();
        postData.expages = $('#prefetch-expages').val();
        postData._nonce = $(".minification-setting-section input[name=_nonce]").val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('prefetch_dns', index);
        toolkit_save_admin_settings(postData, message);
    });

    //SAVE lazy load SETTING
    jQuery('.save-lazyload-setting, #toolkit_image_loading, #toolkit_preload_images, #toolkit_image_attrs, #toolkit_video_loading, #toolkit_yt_placeholder, #toolkit_yt_self_host, #toolkit_iframe_loading').on('click', function() {
        var postData = {action: 'toolkit_lazyload_setting_save'};
        postData.image = $('#toolkit_image_loading').prop('checked') === true ? 'on' : 'off';
        postData.img_loadtype = $('#toolkit_img_loadtype').val();
        postData.image_abvfold = $('#toolkit_image_abvfold').val();
        postData.preload_images = $('#toolkit_preload_images').prop('checked') === true ? 'on' : 'off';
        postData.image_attrs = $('#toolkit_image_attrs').prop('checked') === true ? 'on' : 'off';
        postData.video = $('#toolkit_video_loading').prop('checked') === true ? 'on' : 'off';
        postData.yt_placeholder = $('#toolkit_yt_placeholder').prop('checked') === true ? 'on' : 'off';
        postData.yt_self_host = $('#toolkit_yt_self_host').prop('checked') === true ? 'on' : 'off';
        postData.iframe = $('#toolkit_iframe_loading').prop('checked') === true ? 'on' : 'off';
        postData.exclude_loading = $('#toolkit_exclude_loading').val();
        postData._nonce = $(".lazyload-setting-section input[name=_nonce]").val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('media_loading', index);
        toolkit_save_admin_settings(postData, message);
    });
    //save server tweaks
	jQuery('#toolkit_gzip_compression, #toolkit_keep_alive, #toolkit_ninja_etags, #toolkit_expire_headers, #toolkit_encoding_header').on('click', function() {
	    if( confirm('This will edit your htaccess file. Before saving ensure you have downloaded backup of htaccess file.') ){
            var postData = {
                action: 'toolkit_server_setting_save',
                _nonce: $(".server-tweaks-section input[name=_nonce]").val()
            };
            postData.encoding_header = ( $('input[name=toolkit_encoding_header]').prop('checked') === true ) ? 'on' : 'off';
            postData.gzip_compression = ( $('input[name=toolkit_gzip_compression]').prop('checked') === true ) ? 'on' : 'off';
            postData.keep_alive = ( $('input[name=toolkit_keep_alive]').prop('checked') === true ) ? 'on' : 'off';
            postData.ninja_etags = ( $('input[name=toolkit_ninja_etags]').prop('checked') === true ) ? 'on' : 'off';
            postData.leverage_caching = ( $('input[name=toolkit_leverage_caching]').prop('checked') === true ) ? 'on' : 'off';
            postData.expire_headers = ( $('input[name=toolkit_expire_headers]').prop('checked') === true ) ? 'on' : 'off';
            var index = $(this).data('message');
            if( $(this).prop('checked') === true ){
                index = parseInt(index) + 1;
            }
            var message = toolkit_get_success_message('server_tweaks', index);
            toolkit_save_admin_settings(postData, message);
        } else {
	        if( $(this).prop('checked') === true ){
                $(this).prop('checked', false);
            } else{
                $(this).prop('checked', true);
            }
        }
	});
	 //disable wordpress widgets
	jQuery('#wordpress_widgets_ds .widget-toggler').on('click', function() {
		var postData = $('#wordpress_widgets_ds').serialize();
        postData._nonce = jQuery("#wordpress-panel").find("input[name=_nonce]").val();
        var message = $(this).data('value');
        if( $(this).prop('checked') === true ){
            message += " has been de-registered successfully";
        } else {
            message += " has been re-registered successfully";
        }
        toolkit_save_admin_settings(postData, message);
	});
	 //disable dashboard widgets
	jQuery('#dashboard_widgets_ds .widget-toggler').on('click', function() {
		var postData = $('#dashboard_widgets_ds').serialize();
        postData._nonce = jQuery("#dashboard-panel").find("input[name=_nonce]").val();
        var message = $(this).data('value');
        if( $(this).prop('checked') === true ){
            message += " has been de-registered successfully";
        } else {
            message += " has been re-registered successfully";
        }
        toolkit_save_admin_settings(postData, message);
	});
	 //disable dashboard widgets
	jQuery('#elementor_widgets_ds .widget-toggler').on('click', function() {
		var postData = $('#elementor_widgets_ds').find("input[type='checkbox']:checked").serialize();
        postData += postData + '&action=disable_elementor_widgets';
        postData += postData + '&_nonce=' +jQuery("#elementor-panel").find("input[name=_nonce]").val();
        var message = $(this).data('value');
        if( $(this).prop('checked') === true ){
            message += " has been de-registered successfully";
        } else {
            message += " has been re-registered successfully";
        }
        toolkit_save_admin_settings(postData, message);
	});
    //save theme disable settings
	var themeDisCheck = jQuery('#theme_disable_themeless');
	if( themeDisCheck.length > 0 ){
        themeDisCheck.on('click', function() {
            var postData = {
                action: 'theme_disable_settings',
                themeless: ( themeDisCheck.prop('checked') === true ) ? 'yes' : 'no'
            };
            var index = $(this).data('message');
            if( $(this).prop('checked') === true ){
                index = parseInt(index) + 1;
            }
            var message = toolkit_get_success_message('theme_dashing', index);
            toolkit_save_admin_settings(postData, message);
        });
	}
	//save dashboard options
    jQuery('.save-dashboard-options, #toolkit_dashboard_showtitle, #toolkit_dashboard_dismiss').on('click', function() {
        var postData = {action: 'toolkit_dashboard_options_save'};
        postData.template = $('#toolkit_dashboard_template').val();
        postData.showtitle = ( $('#toolkit_dashboard_showtitle').prop('checked') === true ) ? 'yes' : 'no';
        postData.dismissible = ( $('#toolkit_dashboard_dismiss').prop('checked') === true ) ? 'yes' : 'no';
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('theme_dashing', index);
        toolkit_save_admin_settings(postData, message);
    });

    //save unload options
    var unload_options = "#toolkit_disable_emojis, #toolkit_disable_dashicons, #toolkit_disable_oembed, #toolkit_disable_rssfeed, #toolkit_disable_xmlrpc, #toolkit_disable_commentreply, #toolkit_disable_restapi, #toolkit_disable_gutenberg, .save-unload-options,";
    var source_options = "#toolkit_remove_qstrings, #toolkit_remove_jmigrate, #toolkit_remove_apilinks, #toolkit_remove_feedlinks, #toolkit_remove_rsdlink, #toolkit_remove_shortlink, #toolkit_remove_wlwlink,";
    jQuery(unload_options+' #save-source-options, '+source_options+' #save-woocommerce-options').on('click', function() {
        var postData = {action: 'toolkit_unload_options_save'};
        //common wp files tab options
        postData.disable_emojis = ( $('input[name=toolkit_disable_emojis]').prop('checked') === true ) ? 'on' : 'off';
        postData.disable_dashicons = ( $('input[name=toolkit_disable_dashicons]').prop('checked') === true ) ? 'on' : 'off';
        postData.disable_oembed = ( $('input[name=toolkit_disable_oembed]').prop('checked') === true ) ? 'on' : 'off';
        postData.disable_rssfeed = ( $('input[name=toolkit_disable_rssfeed]').prop('checked') === true ) ? 'on' : 'off';
        postData.disable_xmlrpc = ( $('input[name=toolkit_disable_xmlrpc]').prop('checked') === true ) ? 'on' : 'off';
        postData.disable_restapi = ( $('input[name=toolkit_disable_restapi]').prop('checked') === true ) ? 'on' : 'off';
        postData.condition_restapi = $('#toolkit_condition_restapi').val();
        postData.disable_gutenberg = ( $('input[name=toolkit_disable_gutenberg]').prop('checked') === true ) ? 'on' : 'off';
        postData.condition_gutenberg = $('#toolkit_condition_gutenberg').val();
        postData.disable_commentreply = ( $('input[name=toolkit_disable_commentreply]').prop('checked') === true ) ? 'on' : 'off';
        //source code tab options
        postData.remove_qstrings = ( $('input[name=toolkit_remove_qstrings]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_jmigrate = ( $('input[name=toolkit_remove_jmigrate]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_apilinks = ( $('input[name=toolkit_remove_apilinks]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_feedlinks = ( $('input[name=toolkit_remove_feedlinks]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_rsdlink = ( $('input[name=toolkit_remove_rsdlink]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_shortlink = ( $('input[name=toolkit_remove_shortlink]').prop('checked') === true ) ? 'on' : 'off';
        postData.remove_wlwlink = ( $('input[name=toolkit_remove_wlwlink]').prop('checked') === true ) ? 'on' : 'off';
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('code_cleaner', index);
        toolkit_save_admin_settings(postData, message);
    });

    //save theme toolkit settings
	var saveToolkitBtn = jQuery('.save-toolkit');
	if( saveToolkitBtn.length > 0 ){
        saveToolkitBtn.on('click', function() {
            var postData = {
                action: 'theme_toolkit_settings',
                header_code: $('#theme_disable_header').val(),
                footer_code: $('#theme_disable_footer').val(),
                footer_wpcode: $('#theme_disable_wpfooter').val(),
                bodytag_code: $('#theme_disable_bodytag').val(),
            };
            toolkit_save_admin_settings(postData);
        });
	}

	//KEY VERIFY
	$('#toolkit-license-verification #key-verify').on('click', function() {
		var license_key = $("#toolkit-license-verification input[name=template-key]").val();
		if(license_key){
		    var postData = {
		        action:'toolkit_license_key_verify',
                _nonce:$("#toolkit-license-verification input[name=_nonce]").val(),
                license_key:license_key
		    };
            toolkit_save_admin_settings(postData, null, true);
		} else {
			alert('Please enter valid license key');
		}

	});
	//KEY DEACTIVATE
	$('#key-deactivate').on('click', function() {
        var postData = {
            action:'toolkit_license_key_verify',
            _nonce:$("#toolkit-license-verification input[name=_nonce]").val(),
            license_key:$("#toolkit-license-verification input[name=template-key]").data('license')
        };
        toolkit_save_admin_settings(postData, null, true);
	});

	$('#login-role').on('change', function () {
		var role = $(this).val();
		var roles_caps = JSON.parse($('#user-roles-caps').val());
		if( roles_caps[role].edit_posts ){
            $('#access-link-posts').removeAttr('disabled');
		} else {
            $('#access-link-posts').val('none').attr('disabled', true);
		}
        if( roles_caps[role].activate_plugins ){
            $('#access-link-plugins').removeAttr('disabled');
        } else {
            $('#access-link-plugins').val('none').attr('disabled', true);
        }
        if( roles_caps[role].edit_pages ){
            $('#access-link-pages').removeAttr('disabled');
        } else {
            $('#access-link-pages').val('none').attr('disabled', true);
        }
        if( roles_caps[role].manage_woocommerce ){
            $('#access-link-woocommerce').removeAttr('disabled');
        } else {
            $('#access-link-woocommerce').val('none').attr('disabled', true);
        }
        if( role === 'administrator' ){
			$('#access-link-posts option[value="view"]').attr('disabled', true);
			$('#access-link-plugins option[value="view"]').attr('disabled', true);
			$('#access-link-pages option[value="view"]').attr('disabled', true);
		} else {
            $('#access-link-posts option[value="view"]').removeAttr('disabled');
            $('#access-link-plugins option[value="view"]').removeAttr('disabled');
            $('#access-link-pages option[value="view"]').removeAttr('disabled');
		}
    });

    $('#toolkit-login-form').on('submit', function (e) {
		e.preventDefault();
		var postData = {
            action: 'save_toolkit_login_user',
            user_email: $('#login-email').val(),
            first_name: $('#login-fname').val(),
            last_name: $('#login-lname').val(),
            role: $('#login-role').val(),
            expiry: $('#login-expiry').val(),
            redirect: $('#login-redirect').val(),
            cap_pages: $('#access-link-pages').val(),
            cap_posts: $('#access-link-posts').val(),
            cap_plugins: $('#access-link-plugins').val(),
            cap_woocommerce: $('#access-link-woocommerce').val(),
            update_id: $('#update_user_id').val(),
            toolkit_access_nonce: $('#toolkit_access_nonce').val()
        };
        toolkit_save_admin_settings(postData, null, true);
    });

    $('#toolkit-remove-user').on('click', function (e) {
        var user_id = $('#deleted-user').val();
        if( parseInt(user_id) > 0 && confirm("Are you sure you'd like to delete this User Access Link?") ){
            var postData = {
                action: 'remove_toolkit_login_user',
                user_id: user_id,
                reassign_id: $('#reassign-user').val(),
                toolkit_access_nonce: $('#toolkit_access_nonce').val()
            };
            toolkit_save_admin_settings(postData, null, true);
		}
    });

    $('.toolkit-remove-link').on('click', function (e) {
        var user_id = $(this).data('user_id');
        if( parseInt(user_id) > 0 ){
            $('#deleted-user').val(user_id);
            $('#reassign-user option').show();
            $('#reassign-user option[value="'+user_id+'"]').hide();
        }
    });

    $('#show-login-form').on('click', function(){
    	$('#login-fname').val('');
    	$('#login-lname').val('');
    	$('#login-email').val('');
    	$('#login-expiry').val('day');
    	$('#login-redirect').val('default');
    	$('#access-link-posts').val('view-edit');
    	$('#access-link-plugins').val('view-edit');
    	$('#access-link-pages').val('view-edit');
    	$('#access-link-woocommerce').val('view-edit');
    	$('#update_user_id').val('0');
        $('#login-role').val('administrator').trigger('change');
        $('#toolkit-login-form').slideToggle();
        $('#toolkit-create-access').text('Generate Secure Access Link');
    });

    $('.toolkit-edit-user').on('click', function () {
		var user_id = $(this).data('user_id');
		if( parseInt(user_id) > 0 ){
			var user_data = JSON.parse($('#user-'+user_id+'-details').val());
            $('#login-fname').val(user_data.first_name);
            $('#login-lname').val(user_data.last_name);
            $('#login-email').val(user_data.user_email);
            $('#login-expiry').val(user_data.expiry);
            $('#login-redirect').val(user_data.redirect);
            $('#access-link-posts').val(user_data.ps_access);
            $('#access-link-plugins').val(user_data.pl_access);
            $('#access-link-pages').val(user_data.pg_access);
            $('#access-link-woocommerce').val(user_data.wc_access);
            $('#update_user_id').val(user_id);
            $('#login-role').val(user_data.user_role).trigger('change');
            $('#toolkit-login-form').slideDown();
            $('#toolkit-create-access').text('Save Changes');
		}
    });

    $('.toolkit-copy-to-clipboard').on('click', function () {
        copyToClipboard(this);
        var id = $(this).attr('id');
        $('#copied-' + id).text('Copied').fadeIn();
        $('#copied-' + id).fadeOut('slow');
    });

    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).data('clipboard')).select();
        document.execCommand("copy");
        $temp.remove();
    }

	function validURL(str) {
        var pattern = new RegExp('^(http(s)?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
	}

    //save unload options
    jQuery('#save_restrict_access').on('click', function() {
        var postData = {action: 'toolkit_restrict_access_save'};
        var restrict_access = jQuery('#toolkit_restrict_access').val();
        postData.hide_plugin = ( jQuery('input[name=toolkit_hide_plugin]').prop('checked') === true ) ? 'yes' : 'no';
        postData.restrict_access = restrict_access;
        if( restrict_access === 'me' ){
            postData.booster_access = 'me';
            postData.syncer_access = 'me';
            postData.theme_access = 'me';
            postData.toolbox_access = 'me';
            postData.license_access = 'me';
		} else {
            postData.booster_access = jQuery('#toolkit_booster_access').val();
            postData.syncer_access = jQuery('#toolkit_syncer_access').val();
            postData.theme_access = jQuery('#toolkit_theme_access').val();
            postData.toolbox_access = jQuery('#toolkit_toolbox_access').val();
            postData.license_access = jQuery('#toolkit_license_access').val();
		}
        postData.only_me_id = jQuery('#restrict_access_id').val();
        toolkit_save_admin_settings(postData);
    });

    $('.close-notice').on('click', function(){
        $('#theme-disable-admin-css').attr('disabled', 'disabled');
    	$('.theme-disable-overlay').hide();
	});

    //save backgroung options
    jQuery('.save-bgtasks-options, #toolkit_disable_heartbeat, #toolkit_disable_revision, #toolkit_disable_autosave').on('click', function() {
        var postData = {action: 'toolkit_bgtasks_options_save'};
        postData.disable_heartbeat = ( jQuery('input[name=toolkit_disable_heartbeat]').prop('checked') === true ) ? 'yes' : 'no';
        postData.heartbeat_frequency = jQuery('#toolkit_heartbeat_frequency').val();
        postData.disable_revision = ( jQuery('input[name=toolkit_disable_revision]').prop('checked') === true ) ? 'yes' : 'no';
        postData.revision_frequency = jQuery('#toolkit_revision_frequency').val();
        postData.disable_autosave = ( jQuery('input[name=toolkit_disable_autosave]').prop('checked') === true ) ? 'yes' : 'no';
        postData.autosave_interval = jQuery('#toolkit_autosave_interval').val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('core_manager', index);
        toolkit_save_admin_settings(postData, message);
    });

    //save core tweak options
    jQuery('#toolkit_disable_sitemap, #toolkit_disable_wpcore, #toolkit_disable_plugin, #toolkit_disable_themes').on('click', function() {
        var postData = {action: 'toolkit_coretweak_options_save'};
        postData.disable_sitemap = ( jQuery('input[name=toolkit_disable_sitemap]').prop('checked') === true ) ? 'yes' : 'no';
        postData.wpcore = ( jQuery('input[name=toolkit_disable_wpcore]').prop('checked') === true ) ? 'yes' : 'no';
        postData.plugin = ( jQuery('input[name=toolkit_disable_plugin]').prop('checked') === true ) ? 'yes' : 'no';
        postData.themes = ( jQuery('input[name=toolkit_disable_themes]').prop('checked') === true ) ? 'yes' : 'no';
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('core_manager', index);
        toolkit_save_admin_settings(postData, message);
    });

    //clear scan history
    jQuery('#clear-scan-history').on('click', function() {
    	if( confirm('Are you sure to clear scan history?') ){
            var postData = {
                action: 'toolkit_clear_scan_history',
				clear: true
            };
            toolkit_save_admin_settings(postData, null, true);
		}
    });

    $('.toolkit-regenerate-css a').on('click', function (event) {
        event.preventDefault();
        var $thisButton = $(this);
        $thisButton.text('Regenerating...');
        $.post(ajaxurl, {
            action: 'elementor_clear_cache',
            _nonce: $('#toolkit_regenerate_nonce').val()
        }).done(function () {
            $thisButton.text('Regenerated');
            setTimeout(function(){
                $thisButton.text('Regenerate CSS');
            }, 2000);
        });
    });

    //save data handling options
    jQuery('#save_data_handling').on('click', function() {
        var postData = {action: 'toolkit_data_handling_save'};
        postData.upon_uninstal = jQuery('#toolkit_upon_uninstal').val();
        postData.booster_uninstall = jQuery('#toolkit_booster_uninstall').val();
        postData.scan_history = jQuery('#toolkit_scan_history').val();
        postData.theme_uninstall = jQuery('#toolkit_theme_uninstall').val();
        postData.toolbox_uninstall = jQuery('#toolkit_toolbox_uninstall').val();
        postData.license_uninstall = jQuery('#toolkit_license_uninstall').val();
        toolkit_save_admin_settings(postData);
    });

    //save beta testing options
    jQuery('#toolkit_beta_testing').on('click', function() {
        var postData = {action: 'toolkit_testing_options_save'};
        postData.beta_testing = ( jQuery('input[name=toolkit_beta_testing]').prop('checked') === true ) ? 'yes' : 'no';
        postData.testing_email = jQuery('input[name=toolkit_testing_email]').val();
        postData.testing_name = jQuery('input[name=toolkit_testing_name]').val();
        toolkit_save_admin_settings(postData);
    });

    //save beta testing options
    jQuery('#reset-settings-options').on('click', function() {
        if( jQuery('#toolkit_reset_settings').prop('checked') === true ){
            if( confirm('Are you sure you want to wipe all settings back to factory default?') ){
                var postData = {
                    action: 'toolkit_reset_settings_options',
                    reset_all: true
                };
                toolkit_save_admin_settings(postData, null, true);
            }
        } else {
            alert('Check reset option and click on save again.');
        }
    });

    jQuery('#toolkit_disable_restapi, #toolkit_disable_gutenberg, #toolkit_disable_heartbeat, #toolkit_disable_revision, #toolkit_disable_autosave, #toolkit_lazy_loading, #toolkit_yt_placeholder, #toolkit_beta_testing, #toolkit_cache_pages,  #toolkit_preload_cache, #toolkit_css_combine, #toolkit_cdn_enable, #toolkit_js_combine, #toolkit_js_defer, #toolkit_js_delay, #toolkit_pre_dns, #toolkit_image_loading, #toolkit_logpage_enable').on('click', function () {
    	if( jQuery(this).prop('checked') === true ){
    		jQuery('.'+jQuery(this).data('toggler')).slideDown();
		} else {
            jQuery('.'+jQuery(this).data('toggler')).slideUp();
		}
    });

    $('#toolkit_css_combine').on('click', function () {
        if( $(this).prop('checked') === true && $('#toolkit_css_minify').prop('checked') === false ){
            $('#toolkit_css_minify').trigger('click');
        }
    });

    $('#toolkit_js_combine').on('click', function () {
        if( $(this).prop('checked') === true && $('#toolkit_js_minify').prop('checked') === false ){
            $('#toolkit_js_minify').trigger('click');
        }
    });

    $('#toolkit_exclude_jquery').on('click', function () {
        if( $(this).prop('checked') === true && $('#toolkit_fixed_jquery').prop('checked') === true ){
            $('#toolkit_fixed_jquery').trigger('click');
        }
    });

    $('#toolkit_fixed_jquery').on('click', function () {
        if( $(this).prop('checked') === true && $('#toolkit_exclude_jquery').prop('checked') === true ){
            $('#toolkit_exclude_jquery').trigger('click');
        }
    });

    $('#toolkit_restrict_access').on('change', function () {
        if( $(this).val() !== 'me' ){
            $('.'+$(this).data('toggler')).slideDown();
        } else {
            $('.'+$(this).data('toggler')).slideUp();
        }
    });

    $('#toolkit_upon_uninstal, #toolkit_upon_export').on('change', function () {
        if( $(this).val() === 'rem_some' ){
            $('.'+$(this).data('toggler')).slideDown();
        } else {
            $('.'+$(this).data('toggler')).slideUp();
        }
    });

    /*** Syncer ***/

    $('.generate-syncerkey').on('click', function () {
        var expiration = $('#key-expiration').val();
        if( expiration ){
            jQuery.ajax({
                type: "POST",
                url: toolkit.post_url,
                data: {
                    action: 'toolkit_generate_syncer_key',
                    expiration: expiration,
                    notes: $('#syncer-key-note').val()
                },
                dataType: "json",
                beforeSend: function(){
                    $('#site-connection-manager').showLoading();
                },
                success: function (response) {
                    $('#site-connection-manager').hideLoading();
                    if( response ){
                        window.location.reload();
                    } else {
                        alert('Sorry, we could not generate a syncer key. Try again.');
                    }
                },
                error: function (request, status, error) {
                    $('#site-connection-manager').hideLoading();
                    alert('Sorry, we could not generate a syncer key. Try again.');
                }
            });
        }
    });

    $('.remove-syncerkey').on('click', function () {
        var rem_key = $(this).closest('td').find('.comp-syncer-key').val();
        if( confirm('Are you sure to remove?') && rem_key ){
            jQuery.ajax({
                type: "POST",
                url: toolkit.post_url,
                data: {
                    action: 'toolkit_delete_syncer_key',
                    rem_key: rem_key
                },
                dataType: "json",
                beforeSend: function(){
                    $('#site-connection-manager').showLoading();
                },
                success: function (response) {
                    $('#site-connection-manager').hideLoading();
                    if( response ){
                        window.location.reload();
                    } else {
                        alert('Sorry, we could not remove a syncer key. Try again.');
                    }
                },
                error: function (request, status, error) {
                    $('#site-connection-manager').hideLoading();
                    alert('Sorry, we could not remove a syncer key. Try again.');
                }
            });
        }
    });

    $('.copy-syncer-key').on('click', function () {
        $("#syncer-key-input").val($(this).closest('td').find('.comp-syncer-key').val());
        const tempInput = document.getElementById("syncer-key-input");
        tempInput.select();
        const copied = document.execCommand("copy");
        if (copied) {
            $("#syncer-key-input").val('').blur();
            alert('Key copied to clipboard');
        }
    });

    $('.edit-syncer-note').on('click', function () {
        var notes = $(this).closest('tr').find('.notes').text();
        $(this).closest('tr').find('.notesection').hide();
        $(this).closest('tr').find('.max400').find('.note-updates').remove();
        $(this).closest('tr').find('.max400').append('<div class="note-updates"><input type="text" class="syncer-key-input" value="'+notes+'"/> <button type="button" class="button toolkit-btn syncer-key-edit">Update</button></div>');
    });

    $('body').on('click', '.syncer-key-edit', function () {
        var rem_key = $(this).closest('tr').find('.comp-syncer-key').val();
        var notes = $(this).closest('td').find('.syncer-key-input').val();
        if( rem_key ){
            jQuery.ajax({
                type: "POST",
                url: toolkit.post_url,
                data: {
                    action: 'toolkit_update_syncer_key',
                    rem_key: rem_key,
                    notes: notes
                },
                dataType: "json",
                beforeSend: function(){
                    $('#site-connection-manager').showLoading();
                },
                success: function (response) {
                    $('#site-connection-manager').hideLoading();
                    if( response ){
                        window.location.reload();
                    } else {
                        alert('Sorry, we could not update syncer key note. Try again.');
                    }
                },
                error: function (request, status, error) {
                    $('#site-connection-manager').hideLoading();
                    alert('Sorry, we could not update syncer key note. Try again.');
                }
            });
        }
    });

    $('#clear-syncer-key').on('click', function () {
        $("#syncer-key-external").val('');
        $("#toolkit_syncer_remote_templates").html('');
    });

    $('#syncer-key-connect').on('click', function () {
        var externalKey = $('#syncer-key-external').val();
        var templatesHolder = $('#toolkit_syncer_remote_templates');
        if( externalKey.trim() ){
            if( externalKey.split(':')[0] === toolkit.site_name ){
                templatesHolder.html('<p class="same-site">Oops! Looks like you are trying to connect to the same site. Please enter a Syncer key for another remote site.</p>');
            } else {
                jQuery.ajax({
                    type: "POST",
                    url: toolkit.post_url,
                    data: {
                        action: 'toolkit_remote_templates',
                        key: externalKey
                    },
                    dataType: "json",
                    beforeSend: function(){
                        $('#site-connection-manager').showLoading();
                    },
                    success: function (templates) {
                        $('#site-connection-manager').hideLoading();
                        if( templates.length > 0 ){
                        	$('#bookmark-syncer-key').show();
                            var tempsHtml = '<table class="table table-bordered" id="remote-template-section" style="background:#F9F9F9" width="100%">';
                            tempsHtml += '<tbody><tr>';
                            tempsHtml += '<td class="template-cell">';
                            tempsHtml += '<loading v-if="loading" el="#remote-template-section" />';
                            tempsHtml += '<div class="template-listing-search dashicons-before">';
                            tempsHtml += '<input type="text" id="sync-temp-search" class="form-control" placeholder="Search here"/>';
                            tempsHtml += '</div>';
                            tempsHtml += '<div class="template-listing-tabs">';
                            var tabs = toolkitGetArrayUnique(['all'].concat(templates.map(function(entry) {
                                return entry.type;
                            })));
                            tempsHtml += '<h2 class="nav-tab-wrapper">';
                            for( var i=0; i<tabs.length; i++ ){
                                tempsHtml += '<a class="nav-tab" data-temp_type="'+tabs[i]+'">'+capitalizeFirstLetter(tabs[i])+'</a>';
                            }
                            tempsHtml += '</h2>';
                            tempsHtml += '<div class="tab-content"><ul class="templates-wrapper clearfix">';
                            for( i=0; i<templates.length; i++ ){
                                tempsHtml += '<li class="template template-'+templates[i].type+'">';
                                tempsHtml += '<div><h4>'+templates[i].title+'</h4>';
                                tempsHtml += '<img src="'+toolkit.temp_thumb+'" title="'+templates[i].title.toLowerCase()+'" /></div>';
                                tempsHtml += '<h4><button type="button" class="button toolkit-btn template-import" data-template_id="'+templates[i].template_id+'">Import Template</button></h4></li>';
                            }
                            tempsHtml += '</div></ul>';
                            tempsHtml += '</div>';
                            tempsHtml += '</td></tr></tbody></table>';
                            templatesHolder.html(tempsHtml);
                        } else {
                            $('#bookmark-syncer-key').hide();
                            templatesHolder.html('<p class="same-site">Sorry, we didn\'t find any Elementor templates on the selected site.</p>');
                        }
                    },
                    error: function (request, status, error) {
                        $('#site-connection-manager').hideLoading();
                        templatesHolder.html('<p class="same-site">Sorry, we didn\'t connect to the selected site, try again later.</p>');
                    }
                });
            }
        }
    });

    $(document).on('click', '.template-import', function(){
        var template_id = $(this).data('template_id');
        var externalKey = $('#syncer-key-external').val();
        if( externalKey && parseInt(template_id) > 0 ){
            jQuery.ajax({
                type: "POST",
                url: toolkit.post_url,
                data: {
                    action: 'toolkit_remote_template',
                    key: externalKey,
                    id: template_id
                },
                dataType: "json",
                beforeSend: function(){
                    $('#site-connection-manager').showLoading();
                },
                success: function (response) {
                    $('#site-connection-manager').hideLoading();
                    if( response ){
                        alert('Success! Your template has been downloaded.');
                    } else {
                        alert('Sorry, downloading this template failed. Please try again.');
                    }
                },
                error: function (request, status, error) {
                    $('#site-connection-manager').hideLoading();
                    alert('Sorry, downloading this template failed. Please try again.');
                }
            });
        }
    });
    $('#bookmark-syncer-key').on('click', function(){
        var externalKey = $('#syncer-key-external').val();
        if( externalKey ){
            var postData = {
                action: 'toolkit_bookmark_syncer_key',
                key: externalKey
            };
            toolkit_save_admin_settings(postData);
        }
    });
    $(document).on('click', '.template-listing-tabs .nav-tab', function () {
        var temp_type = $(this).data('temp_type');
        if( temp_type === 'all' ){
            $('.templates-wrapper .template').show();
        } else {
            $('.templates-wrapper .template').hide();
            $('.templates-wrapper .template-'+temp_type).show();
        }
    });
    $(document).on('keyup', '#sync-temp-search', function () {
        var search_term = $(this).val();
        if( search_term ){
            $('.templates-wrapper .template').hide();
            $('.templates-wrapper .template img[title*="'+search_term.toLowerCase()+'"]').closest('.template').show();
        } else {
            $('.templates-wrapper .template').show();
        }
    });
    $('.syncer-keys-table .connect-site').on('click', function(){
        var site_id = $(this).data('site_id');
        var site_key = $(this).data('site_key');
        if( site_key && parseInt(site_id) > 0 ){
            $("#syncer-key-external").val(site_key);
            $('#site-connect-tab').trigger('click');
            $('#syncer-key-connect').trigger('click');
		}
    });

    $('.syncer-keys-table .remove-site').on('click', function(){
        var site_id = $(this).data('site_id');
        if( confirm('Are you sure to remove?') && parseInt(site_id) > 0 ){
        	var $this = $(this);
            jQuery.ajax({
                type: "POST",
                url: toolkit.ajax_url,
                data: {
                    action: 'toolkit_remove_syncer_key',
                    site_id: site_id
                },
                dataType: "json",
                beforeSend: function(){
                    $('#site-connection-manager').showLoading();
                },
                success: function (response) {
                    $('#site-connection-manager').hideLoading();
                    if(response.success){
                        $this.closest('tr').remove();
                        jQuery('.toolkit-message').removeClass('updated').addClass('updated').show().html('<p>'+response.message+'</p>').delay(delay).fadeOut(fadeSpeed);
                        jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                    } else {
                        jQuery('.toolkit-message').removeClass('updated').addClass('error').show().html('<p>'+response.message+'</p>').delay(delay).fadeOut(fadeSpeed);
                        jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                },
                error: function (request, status, error) {
                    $('#site-connection-manager').hideLoading();
                }
            });
        }
    });

    $('#add-more-cdn').on('click', function(){
    	var rowHtml = '<tr>'+$('#cdn-holder-table tbody').find('tr:eq(0)').html()+'<td><span class="dashicons dashicons-no"></span></td></tr>';
        $('#cdn-holder-table > tbody').append(rowHtml);
        $('#cdn-holder-table tbody tr:last').find('input').val('');
        $('#cdn-holder-table tbody tr:last').find('input').val('');
        $('#cdn-holder-table tbody tr:last').find('button').addClass('jsbtn');
	});

    $(document).on('click', '#cdn-holder-table tbody .dashicons-no', function () {
    	var $this = $(this);
        $this.closest('tr').fadeOut('slow');
        setTimeout(function () {
			$this.closest('tr').remove();
        }, 2000);
    });

    $('.login_background_image_button').on('click', function(e){
        e.preventDefault();
        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    type : 'image'
                },
                button: {
                    text: 'Use this image'
                },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(button).text('Change Background Image');
                $('.login-bgimage-holder').html('<img class="login-background-img" src="' + attachment.url + '" /><span class="dashicons dashicons-trash"></span>');
                $('#toolkit_login_bgimage').val(attachment.url); //attachment.id
                $('.toolkit-loginbg-options').slideDown();
            }).open();
    });
    $('body').on('click', '.login-bgimage-holder .dashicons-trash', function(){
        $('.login-bgimage-holder').html('');
        $('#toolkit_login_bgimage').val('');
        $('.toolkit-loginbg-options').slideUp();
        $('.login_background_image_button').text('Set Background Image');
    });
    $('.login_logo_image_button').on('click', function(e){
        e.preventDefault();
        var button = $(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                library : {
                    type : 'image'
                },
                button: {
                    text: 'Use this image'
                },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(button).text('Change Logo Image');
                $('.login-logoimage-holder').html('<img class="login-logo-img" src="' + attachment.url + '" /><span class="dashicons dashicons-trash"></span>');
                $('#toolkit_login_logoimage').val(attachment.url); //attachment.id
                $('.toolkit-loginlogo-options').slideDown();
            }).open();
    });
    $('body').on('click', '.login-logoimage-holder .dashicons-trash', function(){
        $('.login-logoimage-holder').html('');
        $('#toolkit_login_logoimage').val('');
        $('.toolkit-loginlogo-options').slideUp();
        $('.login_logo_image_button').text('Set Logo Image');
    });
    //save backgroung options
    jQuery('#toolkit_logpage_enable, .save-loginpage-options').on('click', function() {
        var postData = {action: 'toolkit_loginpage_options_save'};
        postData.logpage_enable = ( $('#toolkit_logpage_enable').prop('checked') === true ) ? 'on' : 'off';
        postData.logpage_url = $('#toolkit_logpage_url').val();
        postData.logpage_red = $('#toolkit_logpage_red').val();
        postData.bgimage = $('#toolkit_login_bgimage').val();
        postData.bgsize = $('#toolkit_login_bgsize').val();
        postData.bgrepeat = $('#toolkit_login_bgrepeat').val();
        postData.logoimage = $('#toolkit_login_logoimage').val();
        postData.lgwidth = $('#toolkit_login_lgwidth').val();
        postData.lgheight = $('#toolkit_login_lgheight').val();
        postData.logourl = $('#toolkit_login_logourl').val();
        postData.customcss = $('#toolkit_login_customcss').val();
        var index = $(this).data('message');
        if( $(this).prop('checked') === true ){
            index = parseInt(index) + 1;
        }
        var message = toolkit_get_success_message('theme_dashing', index);
        toolkit_save_admin_settings(postData, message);
    });

    $('.show-ninja-scripts').on('click', function () {
        var controls_section = $(this).closest('.controls-section');
        var post_id = controls_section.find('.post-type-script').val();
        var $this = $(this);
        var randID = toolkit_get_random_string(15);
        if( post_id && parseInt(post_id) > 0 ){
            $.ajax({
                type: "POST",
                url: toolkit.ajax_url,
                dataType: "json",
                data: {
                    action: 'toolkit_get_post_assets',
                    post_id: post_id
                },
                beforeSend: function(){
                    $this.closest('.minification-setting-section').showLoading();
                },
                success: function(response){
                    $this.closest('.minification-setting-section').hideLoading();
                    if(response.success){
                        controls_section.find('.display-assets-list').html(response.data);
                    } else {
                        $('.toolkit-message').show().append('<p class="error-msg '+randID+'"><b>Error:</b><br/>'+response.message+'</p>');
                        setTimeout(function(){ $('.'+randID).remove() }, 5000);
                    }
                },
                error: function(request, status, error) {
                    $this.closest('.minification-setting-section').hideLoading();
                }
            });
        } else {
            $('.toolkit-message').show().append('<p class="error-msg '+randID+'"><b>Error:</b><br/>Please select post.</p>');
            setTimeout(function(){ $('.'+randID).remove() }, 5000);
        }
    });

    $('body').on('click', '.ninja-toggler', function () {
        var controls_section = $(this).closest('.controls-section');
        var post_id = controls_section.find('.asset-post-id').val();
        if( post_id && parseInt(post_id) > 0 ){
            var toolkit_styles = ['1'], toolkit_scripts = ['1'], global_styles = ['1'], global_scripts = ['1'];
            $('input[name="tkcss_exc_assets[]"]:checked').each(function(index, value){
                toolkit_styles[index] = $(this).val();
            });
            $('input[name="tkjs_exc_assets[]"]:checked').each(function(index, value){
                toolkit_scripts[index] = $(this).val();
            });
            $('input[name="tkgcss_exc_assets[]"]:checked').each(function(index, value){
                var post_types = [];
                $(this).closest('tr').find('input[name="global_css_post_types[]"]:checked').each(function (index, value){
                    post_types[index] = $(this).val();
                });
                if( post_types ){
                    global_styles[index] = $(this).val() + '|' + post_types.join(',');
                } else {
                    global_styles[index] = $(this).val();
                }
            });
            $('input[name="tkgjs_exc_assets[]"]:checked').each(function(index, value){
                var post_types = [];
                $(this).closest('tr').find('input[name="global_js_post_types[]"]:checked').each(function (index, value){
                    post_types[index] = $(this).val();
                });
                if( post_types ){
                    global_scripts[index] = $(this).val() + '|' + post_types.join(',');
                } else {
                    global_scripts[index] = $(this).val();
                }
            });
            var postData = {
                action: 'toolkit_save_post_assets',
                post_id: post_id,
                styles: toolkit_styles,
                scripts: toolkit_scripts,
                gstyles: global_styles,
                gscripts: global_scripts
            };
            var message = $(this).val();
            if( $(this).prop('checked') === true ){
                message += " has been dequeued successfully";
            } else {
                message += " has been enqueued successfully";
            }
            toolkit_save_admin_settings(postData, message);
        } else {
            alert('Invalid scripts request');
        }
    });

    $('body').on('click', '.border-bottom .switch .ninja-toggler', function () {
        $(this).closest('tr').find('.post-types-list').toggleClass('show-list');
    });

});

function toolkitGetArrayUnique(a) {
    return a.filter(function(value, index){
        return a.indexOf(value) == index
    });
}

function capitalizeFirstLetter(string) {
    return (string) ? string.charAt(0).toUpperCase() + string.slice(1) : '';
}
