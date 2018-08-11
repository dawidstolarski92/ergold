$(document).ready(function () {

	var $container = $("#newsletter-input-popup"),
		$popup = $(".pk_popup_container"),
		$close = $(".fancybox-close"),
		$el = $(".pk_popup"),
		config = {
	        width: $el.data("width"),
	        height: $el.data("height"),
	        path: $el.data("path")
	    };


	if ($.cookie("pk_popup") != "true") {

		$.fancybox.open($popup, {
	        'padding' : 0,
	        'height' : config.height,
	        'width' : config.width,
	        'type': 'inline',
	        'fitToView' : true,
   			'autoSize' : false,
	        'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'openEffect'	: 'elastic',
			'openSpeed'		: '600',
			'easingIn'      : 'swing',
			'easingOut'     : 'swing',
			'beforeClose' 	: function() {
				$.cookie("pk_popup", "true");
				$('.fancybox-overlay-custom').remove();
			}
	    });

		$popup.closest(".fancybox-overlay").addClass('pkpopup');
		$(".fancybox-skin").append("<div class='fancybox-close-overlay'><svg><use xlink:href='#si-close'></use></svg></div>");
		//$("body").append("<div class='fancybox-overlay-custom'></div>");

		$(".send-reqest").click(function(e){
			e.preventDefault();
			var email = $container.val();
			$.ajax({
				type: "POST",
				headers: { "cache-control": "no-cache" },
				async: false,
				url: config.path,
				data: "email="+email,
				success: function(data) {
					if (data) {
						$(".send-response").text(data);
					}
				}
			});
		});

	}
       
});