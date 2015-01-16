jQuery(document).ready(function ($) {

	"use strict";

	jQuery.browser={};(function(){jQuery.browser.msie=false;
	jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
	jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
	jQuery("#layout").fitVids();

	if ($(".res_mode")[0]) {
		if ($("#mymenuone")[0]) {
			jQuery('#mymenuone').before('<a class="btn_canvas mymenuone" href="#mymenuone"><i class="fa fa-bars"></i></a>');
			jQuery('#mymenuone').mmenu({
				dragOpen: true,
				position: "left",
				zposition: "next"
			}, {
				clone  : true
			});
		}
		if ($("#mymenutwo")[0]) {
			jQuery('#mymenutwo').before('<a class="btn_canvas mymenutwo" href="#mymenutwo"><i class="fa fa-bars"></i></a>');
			jQuery("#mymenutwo").mmenu({
				dragOpen: true,
				position: "left",
				zposition: "next"
			}, {
				clone  : true
			});
		}
		jQuery('.mm-menu .mm-list').removeClass('sf-menu res_mode sf-js-enabled sf-shadow');
	}
	// Superfish
	if ($(".sf-menu")[0]) {
		$('.sf-menu').superfish({
			delay: 100,
			autoArrows: true,
			animation: {
				opacity: 'none', height: 'show'
			},
			speed: 300
		});
		$('.sf-menu li li .sf-sub-indicator i').removeClass('fa-chevron-down').addClass('icon-chevron-right');
	}

	// Tabs widget
	(function() {
		var $tabsNav	= $('.T20-tabs-nav'),
		$tabsNavLis	= $tabsNav.children('li'),
		$tabsContainer	= $('.T20-tabs-container');

		$tabsNav.each(function() {
			var $this = $(this);
			$this.next().children('.T20-tab').stop(true,true).hide()
			.siblings( $this.find('a').attr('href') ).show();
			$this.children('li').first().addClass('active').stop(true,true).show();
		});

		$tabsNavLis.on('click', function(e) {
			var $this = $(this);

			$this.siblings().removeClass('active').end()
			.addClass('active');
			
			$this.parent().next().children('.T20-tab').stop(true,true).hide()
			.siblings( $this.find('a').attr('href') ).fadeIn();
			e.preventDefault();
		}).children( window.location.hash ? 'a[href=' + window.location.hash + ']' : 'a:first' ).trigger('click');

	})();

	// ToTop
	jQuery('#toTop').click(function () {
		jQuery('body,html').animate({
			scrollTop: 0
		}, 1000);
	});
	jQuery("#toTop").addClass("hidett");
	jQuery(window).scroll(function () {
		if (jQuery(this).scrollTop() < 400) {
			jQuery("#toTop").addClass("hidett").removeClass("showtt");
		} else {
			jQuery("#toTop").removeClass("hidett").addClass("showtt");
		}
	});

	// News
	if ($("#ticker")[0]) {
		jQuery("#ticker").liScroll({travelocity: 0.07});
	}

	// Slideshows Big Home
	if ($(".in-view-2")[0]) {
		jQuery("#big_carousel").owlCarousel({
			slideSpeed : 1500,
			paginationSpeed : 600,
			items : 2,
			autoPlay: true,
			stopOnHover: true,
			addClassActive: true,
			autoHeight: true,
			responsive: true,
			navigation: false,
			navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			pagination : true,
			paginationNumbers: false
		});
	}
	if ($(".in-view-3")[0]) {
		jQuery("#big_carousel").owlCarousel({
			slideSpeed : 1500,
			paginationSpeed : 600,
			items : 3,
			autoPlay: true,
			stopOnHover: true,
			addClassActive: true,
			autoHeight: true,
			responsive: true,
			navigation: false,
			navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			pagination : true,
			paginationNumbers: false
		});
	}
	if ($(".in-view-4")[0]) {
		jQuery("#big_carousel").owlCarousel({
			slideSpeed : 1500,
			paginationSpeed : 600,
			items : 4,
			autoPlay: true,
			stopOnHover: true,
			addClassActive: true,
			autoHeight: true,
			responsive: true,
			navigation: false,
			navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			pagination : true,
			paginationNumbers: false
		});
	}
	if ($(".in-view-5")[0]) {
		jQuery("#big_carousel").owlCarousel({
			slideSpeed : 1500,
			paginationSpeed : 600,
			items : 5,
			autoPlay: true,
			stopOnHover: true,
			addClassActive: true,
			autoHeight: true,
			responsive: true,
			navigation: false,
			navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			pagination : true,
			paginationNumbers: false
		});
	}
	if ($("#big_carousel_full")[0]) {
		jQuery("#big_carousel_full").owlCarousel({
			slideSpeed : 1500,
			paginationSpeed : 500,
			singleItem: true,
			autoPlay: true,
			stopOnHover: true,
			addClassActive: true,
			autoHeight: true,
			responsive: true,
			navigation: true,
			navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			pagination : false,
			paginationNumbers: false
		});
	}

	// Slideshows
	if ($("#Flexslidert")[0]) {
		$('#Flexslidert').flexslider({
			animation: "slide",
			manualControls: "#slider_nav li"
		});
	}
	if ($("[class^='gallery_']")[0]) {
		$("[class^='gallery_']").owlCarousel({
			slideSpeed : 500,
			paginationSpeed : 500,
			autoPlay: true,
			autoHeight: true,
			stopOnHover: true,
			transitionStyle : "goDown",
			singleItem:true,
			navigation : true,
			navigationText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			rewindNav : true,
			scrollPerPage : false,
			pagination : false
		});
	}
	if ($("[class^='slideshow_']")[0]) {
		$("[class^='slideshow_']").owlCarousel({
			slideSpeed : 500,
			paginationSpeed : 1500,
			autoPlay: true,
			autoHeight: true,
			stopOnHover: true,
			transitionStyle : "goDown",
			singleItem:true,
			navigation : false,
			rewindNav : true,
			scrollPerPage : false,
			pagination : true
		});
	}

	if ($("[class^='carousel_posts_']")[0]) {
		$(".grid_2 [class^='carousel_posts_'], .grid_3 [class^='carousel_posts_']").owlCarousel({
			slideSpeed : 600,
			paginationSpeed : 1000,
			items : 1,
			autoPlay: true,
			stopOnHover: true,
			autoHeight: false,
			responsive: true,
			pagination: true,
			paginationNumbers: false
		});
		$(".grid_4 [class^='carousel_posts_']").owlCarousel({
			slideSpeed : 600,
			paginationSpeed : 1000,
			items : 2,
			autoPlay: true,
			stopOnHover: true,
			autoHeight: false,
			responsive: true,
			pagination: true,
			paginationNumbers: false
		});
		$(".grid_8 [class^='carousel_posts_'], .grid_6 [class^='carousel_posts_'], .grid_7 [class^='carousel_posts_'], .grid_5 [class^='carousel_posts_']").owlCarousel({
			slideSpeed : 600,
			paginationSpeed : 1000,
			items : 3,
			autoPlay: true,
			stopOnHover: true,
			autoHeight: false,
			responsive: true,
			pagination: true,
			paginationNumbers: false
		});
		$(".grid_10 [class^='carousel_posts_'], .grid_9 [class^='carousel_posts_']").owlCarousel({
			slideSpeed : 600,
			paginationSpeed : 1000,
			items : 4,
			autoPlay: true,
			stopOnHover: true,
			autoHeight: false,
			responsive: true,
			pagination: true,
			paginationNumbers: false
		});
		$(".grid_11 [class^='carousel_posts_'], .grid_12 [class^='carousel_posts_']").owlCarousel({
			slideSpeed : 600,
			paginationSpeed : 1000,
			items : 5,
			autoPlay: true,
			stopOnHover: true,
			autoHeight: false,
			responsive: true,
			pagination: true,
			paginationNumbers: false
		});
	}

	// Search
	var popupStatus = 0;
	$(".search_icon").click(function() {
		var windowWidth = document.documentElement.clientWidth;
		var windowHeight = document.documentElement.clientHeight;
		var popupHeight = $("#popup").height();
		var popupWidth = $("#popup").width();
		$("#popup").css({
			"top": windowHeight / 2 - popupHeight / 2,
			"left": windowWidth / 2 - popupWidth / 2
		});
		// Aligning bg
		$("#SearchBackgroundPopup").css({"height": windowHeight});
		if (popupStatus == 0) {
			$("#SearchBackgroundPopup").fadeIn(400);
			$("#popup").fadeIn(400);
			$(".search_place").addClass('animated slideDown');
			popupStatus = 1;
		}
		$('#inputhead').select();
	});
	// Close Popup
	$("#popupLoginClose").click(function() {
		if (popupStatus == 1) {
			$("#SearchBackgroundPopup").fadeOut();
			$("#popup").fadeOut();
			popupStatus = 0;
		}
	});
	$("body").click(function() {
		$("#SearchBackgroundPopup").fadeOut();
		$("#popup").fadeOut();
		popupStatus = 0;
	});
	$('#popup, .search_icon').click(function(e) {
		e.stopPropagation();
	});

	// Tipsy
	$('.toptip').tipsy({fade: true,gravity: 's'});
	$('.bottomtip').tipsy({fade: true,gravity: 'n'});
	$('.righttip').tipsy({fade: true,gravity: 'w'});
	$('.lefttip').tipsy({fade: true,gravity: 'e'});

	// Ajax Contact
	if ($("#contactForm")[0]) {
		$('#contactForm').submit(function () {
			$('#contactForm .error').remove();
			$('#contactForm .requiredField').removeClass('fielderror');
			$('#contactForm .requiredField').addClass('fieldtrue');
			$('#contactForm span strong').remove();
			var hasError = false;
			$('#contactForm .requiredField').each(function () {
				if (jQuery.trim($(this).val()) === '') {
					var labelText = $(this).prev('label').text();
					$(this).addClass('fielderror');
					$('#contactForm span').html('<strong>*Please fill out all fields.</strong>');
					hasError = true;
				} else if ($(this).hasClass('email')) {
					var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					if (!emailReg.test(jQuery.trim($(this).val()))) {
						var labelText = $(this).prev('label').text();
						$(this).addClass('fielderror');
						$('#contactForm span').html('<strong>Is incorrect your email address</strong>');
						hasError = true;
					}
				}
			});
			if (!hasError) {
				$('#contactForm').slideDown('normal', function () {
					$("#contactForm #sendMessage").addClass('load-color');
					$("#contactForm #sendMessage").attr("disabled", "disabled").addClass("btn-success").val('Sending message. Please wait...');
				});
				var formInput = $(this).serialize();
				$.post($(this).attr('action'), formInput, function (data) {
					$('#contactForm').slideUp("normal", function () {
						$(this).before('<div class="notification-box notification-box-success"><p><i class="fa fa-check"></i>Thanks!</strong> Your email was successfully sent. We check Our email all the time.</p></div>');
					});
				});
			}
			return false;
		});
	}
});