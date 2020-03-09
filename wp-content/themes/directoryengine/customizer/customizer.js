/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
(function($) {
$(document).ready(function() {
	wp.customize( 'header_background_color', function( value ) {
        value.bind( function( newval ) {
        	if(newval == false){
				newval = "#fff";
			}
            $('#menu-top').css('background', newval );

            // Color

        } );
    } );
	wp.customize('body_bg_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#ecf0f1";
			}
			$('body').css('background-color', newval);
		});
	});
	/**
	 * update footer background
	*/
	wp.customize('footer_background_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#34495e";
			}
			$('footer').css('background-color', newval);
		});
	});
	/**
	 * copy right area
	*/
	wp.customize('copyright_bg_color', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#2c3e50";
			}
			$('.copyright-wrapper').css('background-color', newval);
		});
	});
	/**
	 * main color
	*/
	wp.customize('main_color_config', function(value) {
		value.bind(function(newval) {
			if(newval == false){
				newval = "#1b83d3";
			}
			$('#search-places .form-search button.submit-search, .top-menu-right>li.top-add-place a button, .list-option-left-wrapper .list-option-left li a, .list-share-social li a, #add-review input[type="submit"]:hover, .detail-place-right-wrapper .section-detail-wrapper .dropdown .btn, .btn.btn-submit-login-form, .form_modal_style input[type="submit"], ul.top-menu-right li.top-search.active, .step-content-wrapper .list-price li .btn.btn-submit-price-plan, .btn.btn-submit-login-form, .sign-up button.btn-sign-up, #menu-header-top>li.select>a:after').css('background-color',newval);
			$('.de-search-form .de-search-btn, .de-why-work .why-work-icon:after, #search-location-form .slider-selection, .services-wrapper .icon-services, #review .comment-form .form-submit input[type=submit], #menu-header-top>li:hover>a:after, .media-list .media .comment-respond .comment-form .form-submit input[type=submit], .comments .comment-respond .comment-form .form-submit input[type=submit]').css('background-color',newval);
			$('.post-place-profile-btn, .btn-post-place, .claim-place, .no-claim, .modal-header .close, .comment-form .rating_submit, .paginations .load-more-post, .paginations .load-more-post:hover, .paginations .load-refesh-post:hover, .wrapper_profile .col-right-profile .right-profile .tabs-rigth-profile .list-info-user-tabs li a::after, .tab-info-wrapper .list-info-user-tab>li a::before, .tab-info-wrapper .list-info-user-tab>li a::after').css('background',newval);
			$('.wrapper_profile .col-right-profile .right-profile .tabs-rigth-profile .list-info-user-tabs li a::before, .comment-form .form-submit input[type=submit], #search-location-form .search-btn, .paginations .current, .copyright-wrapper .social-icons ul.social-network a:hover, .slider.slider-horizontal .slider-handle, .btn-more, .de-section-title:before, .de-section-title:after, .de-collection-large>a:hover, .de-collection-small>a:hover').css('background',newval);
			$('.infowindow h2.title-place a:hover, .list-places .place-wrapper .place-detail-wrapper h2.title-place a:hover, .description-place-wrapper .rate-wrapper .number-review, .paginations .page-numbers:hover, .list-news-widget .content-news h2 a, .edit-place-option a.archive, .edit-place-option a.edit, .list-option-filter .icon-list-view .icon-view.active i, .list-option-filter .icon-list-view .icon-view:hover i, .list-option-filter .sort-rates-lastest a.sort-icon.active, .list-option-filter .sort-rates-lastest a.sort-icon:hover, .tab-info-wrapper .list-info-user-tab li:hover a, .event-active-wrapper .event-wrapper time, .event-active-wrapper .event-wrapper .title-envent a, .popular-places .places-popular .place-popular .place-pop-info .place-info .user-added span, .col-right-profile .right-profile .tabs-rigth-profile .list-info-user-tabs li.active a, .wrapper_profile .col-right-profile .right-profile .tabs-rigth-profile .list-info-user-tabs li a:hover, .tab-info-wrapper .list-info-user-tab>li a:hover').css('color',newval);
			$('.wrapper_profile .col-left-profile .left-profile .user-info .content-info .username h4, .wrapper_profile .col-left-profile .left-profile .pakage-info .content-package h3, .wrapper_profile .col-left-profile .left-profile .pakage-info .content-package>p span.number, .wrapper_profile .col-right-profile .right-profile .content-tabs-right-profile .content-place .list-place-tabs li.place-search .box-search .btn-search-place i, .wrapper_profile .col-right-profile .right-profile .content-tabs-right-profile .content-reviews .list-place-publishing li .wrap-place-publishing .reviews p.username a, .wrapper_profile .col-right-profile .right-profile .content-tabs-right-profile .content-events .list-place-publishing>li .wrap-content-event h4 a, .wrapper_profile .col-right-profile .right-profile .content-tabs-right-profile .content-events .list-place-publishing>li .wrap-content-event .note-event, a.name-author, .footer-copyright a, .tab-info-wrapper .list-info-user-tab>li.active a, .de-popular-place .popular-title h2 a:hover').css('color',newval);
			$('.de-why-work-wrapper>.container>h2 span, .de-search-wrapper .de-search-desc h1 span, .search-location-pagination .paginations-wrapper a.current, .sl-slider-range>span, .reset-pagination span, .features-wrapper .icon-features i, .list-place-review.vertical>li .place-review .place-review-bottom-wrapper .place-review-bottom .name-author, .list-option-filter li .icon-view.active i, .list-option-filter li .icon-view:hover i, .list-option-filter li .sort-icon.active, .list-option-filter li .sort-icon:hover, .edit-place-option li a, .why-work h1 span, .how-work h1 span, #menu-header-top>li:hover>a, #menu-header-top>li:hover, .mega-wrapper .mega-menu .mega-list a:hover, #menu-header-top>li.select>a, #menu-header-top>li.select>.arrow-submenu, .result-pagination .nrp span, #search-location-form .sl-address>span').css('color',newval);
			$('.option-search.right input[type="submit"]').css('color', '#fff');
		});
	});
});
})(jQuery);