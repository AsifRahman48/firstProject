$.noConflict();

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
		new SelectFx(el);
	});

	jQuery('.selectpicker').selectpicker;


	

	$('.search-trigger').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').addClass('open');
	});

	$('.search-close').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').removeClass('open');
	});

	$('.equal-height').matchHeight({
		property: 'max-height'
	});

	// var chartsheight = $('.flotRealtime2').height();
	// $('.traffic-chart').css('height', chartsheight-122);


	// Counter Number
	$('.count').each(function () {
		$(this).prop('Counter',0).animate({
			Counter: $(this).text()
		}, {
			duration: 3000,
			easing: 'swing',
			step: function (now) {
				$(this).text(Math.ceil(now));
			}
		});
	});


	 
	 
	// Menu Trigger
	$('#menuToggle').on('click', function(event) {
		var windowWidth = $(window).width();   		 
		if (windowWidth<1010) { 
			$('body').removeClass('open'); 
			if (windowWidth<760){ 
				$('#left-panel').slideToggle(); 
			} else {
				$('#left-panel').toggleClass('open-menu');  
			} 
		} else {
			$('body').toggleClass('open');
			$('#left-panel').removeClass('open-menu');  
		} 
			 
	}); 

	 
	$(".menu-item-has-children.dropdown").each(function() {
		$(this).on('click', function() {
			var $temp_text = $(this).children('.dropdown-toggle').html();
			$(this).children('.sub-menu').prepend('<li class="subtitle">' + $temp_text + '</li>'); 
		});
	});


	// Load Resize 
	$(window).on("load resize", function(event) { 
		var windowWidth = $(window).width();  		 
		if (windowWidth<1010) {
			$('body').addClass('small-device'); 
		} else {
			$('body').removeClass('small-device');  
		} 
		
	});

	// Menu Toggle event js Start
  	$(".toggle-menu-button").on("click", function(){
  		$(".nav-side-menu").toggleClass("menu-collapsed");
  		$(".right-panel").toggleClass("full-width");
  		$(".nav-side-menu.menu-collapsed .menu-list .menu-content li").removeClass("active show")
        $(".nav-side-menu.menu-collapsed .menu-list .menu-content ul.sub-menu").removeClass("show");
	});
	$(document).on("click",".nav-side-menu.menu-collapsed .menu-list .menu-content li",function() {
		if($(this).hasClass("active show")) {
			$(this).removeClass("active show")
        	$(this).next(".sub-menu").removeClass("show in")
		} else {
			$(".nav-side-menu.menu-collapsed .menu-list .menu-content li").removeClass("active show")
        $(".nav-side-menu.menu-collapsed .menu-list .menu-content ul.sub-menu").removeClass("show");
        $(this).toggleClass("active show")
        $(this).next(".sub-menu").toggleClass("show")
		}

    });
    if ( $(window).width() < 768 ) {
    	$(".top-right").clone().insertAfter(".menu-list");
    }
    if ( $(window).width() < 991 && $(window).width() > 767 ) {
    	$(".nav-side-menu").toggleClass("menu-collapsed");
  		$(".right-panel").toggleClass("full-width");
  		$(".nav-side-menu.menu-collapsed .menu-list .menu-content li").removeClass("active show")
        $(".nav-side-menu.menu-collapsed .menu-list .menu-content ul.sub-menu").removeClass("show");
    }
    if ( $(window).width() < 1500 ) {
    	$(".table-responsive").addClass("text-nowrap");
    } else { //done
    	$(".table-responsive").removeClass("text-nowrap");
    }


    $( window ).resize(function() {
		  if ( $(window).width() < 1500 ) {
		    	$(".table-responsive").addClass("text-nowrap");
		    } else {
		    	$(".table-responsive").removeClass("text-nowrap");
	    }
	});
});