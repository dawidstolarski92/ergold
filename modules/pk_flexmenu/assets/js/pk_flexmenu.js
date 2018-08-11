$(document).ready(function () {

	var is_touch_device = 'ontouchstart' in document.documentElement,
		wdth = $( window ).width();	

	if (wdth >= 800) {

		setTimeout(alignSubmenu, 2000);

		function alignSubmenu() {
			var narrowItems = $('.flexmenuitem.narrow');
			jQuery.each( narrowItems, function( index, menuItem ){
				var position = $(menuItem).offset();
				$(menuItem).find('.submenu').css({'left': position.left+'px'})
			});
		}

	} else {

		$mobileMenuEl = $('.mobileMenuTitle > div');
		$mobileMenuEl.click(function(){
			$(this).closest('.flexmenu').toggleClass('showMenu');
			return false;
		});

		$('.opener').click(function(){
			$(this).parent('li').toggleClass('showMenu');
			return false;
		});

	}


/*
	$('.flexmenu').click(function(){
		var wdth = $( window ).width();	
		var el = $(".flexmenu > ul");
		if (wdth < 768) {
	        el.animate({
	            "height": "toggle"
	        }, 
	        500,
	        function(){
	            if (el.is(':visible')) {
	                el.addClass("act");
	                if ($("body").hasClass("fp-viewing-0"))
	                	$("html").css({"overflow": "scroll"});
	            } else {
	                el.removeClass("act");
	                if ($("body").hasClass("fp-viewing-0"))
	                	$("html").css({"overflow": "hidden"});
	            }
	        });
		}
	});

	$('.flexmenu .opener').click(function(){
		var el = $(this).next('.dd_container');
		var switcher = $(this);
		var wdth = $( window ).width();			
		if (wdth < 768) {
	        el.animate({
	            "height": "toggle"
	        }, 
	        500,
	        function(){
	        	if (el.is(':visible')) {
	                el.addClass("act");
	                switcher.addClass('opn');
	            } else {
	            	switcher.removeClass('opn');
	                el.removeClass("act");
	            }
	        });
		}
		return false;
	});

	var wdth = $( window ).width();	
	if (wdth > 767) {

		$( ".main-section-sublinks > li" ).hover(
		  function() {
		    $(this).find("ul").stop().slideDown("fast");
		  }, function() {
    		$(this).find('.submenu').delay(100).fadeOut(100);
		  }
		);	

		$( ".flexmenuitem" ).hover(
	  	function() {
		    $(this).find('.submenu').fadeIn(400);
		  }, function() {
    		$(this).find('.submenu').delay(100).fadeOut(100);
		  }
		);
	}
	*/
});