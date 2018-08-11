/*
 ### jQuery Star Rating Plugin v2.0 - 2008-03-12 ###
*/
;if(jQuery) (function($){
$.fn.rating = function(settings) {
 settings = $.extend({
		cancel: 'Cancel Rating', // advisory title for the 'cancel' link
		cancelValue: '',         // value to submit when user click the 'cancel' link
		required: false,         // disables the 'cancel' button so user can only select one of the specified values
		readOnly: false          // disable rating plugin interaction/ values cannot be changed
	}, settings || {});
 var groups = {};
 var event = {
  fill: function(n, el, style){
	  this.drain(n);$(el).prevAll('.star').andSelf().addClass( style || 'star_hover' );},
  drain: function(n) {
  	$(groups[n].valueElem).siblings('.star').removeClass('star_on').removeClass('star_hover');},
  reset: function(n){
  	if (!$(groups[n].currentElem).is('.cancel')) {$(groups[n].currentElem).prevAll('.star').andSelf().addClass('star_on');}},
  click: function(n, el) {
			groups[n].currentElem = el;
			var curValue = $(el).children('a').attr( "title" );
			$(groups[n].valueElem).val(curValue);
			event.drain(n);
			event.reset(n);
			if(settings.callback) settings.callback.apply(groups[n].valueElem, [curValue, el]);}};
 		this.each(function(i){
		var n = this.name;if(!groups[n]) groups[n] = {count: 0};i = groups[n].count;groups[n].count++;
		if(i == 0){settings.readOnly = $(this).attr('disabled') || settings.readOnly;
		groups[n].valueElem = $('<input type="hidden" name="' + n + '" value=""' + (settings.readOnly ? ' disabled="disabled"' : '') + '>');
   		$(this).before(groups[n].valueElem);
			if(settings.readOnly || settings.required){}else{
 			$(this).before(
     		$('<div class="cancel"><a title="' + settings.cancel + '">' + settings.cancelValue + '</a></div>')
					.mouseover(function(){ event.drain(n); $(this).addClass('star_on'); })
					.mouseout(function(){ event.reset(n);	$(this).removeClass('star_on'); })
					.click(function(){ event.click(n, this); })
    	);}};
		eStar = $('<div class="star"><a title="' + (this.title || this.value) + '"><svg class="svgic svgic-pk-star smooth05" viewBox="0 0 100 100"><polygon points="50,4.348 64.833,34.403 98.002,39.223 74.001,62.618 79.667,95.652 50,80.056 20.333,95.652 25.999,62.618 1.998,39.223 35.167,34.403 "/></svg></a></div>');
		$(this).after(eStar);
		if(settings.readOnly){$(eStar).addClass('star_readonly');}
		else{
			$(eStar)
			.mouseover(function(){ event.drain(n); event.fill(n, this); })
			.mouseout(function(){ event.drain(n); event.reset(n); })
			.click(function(){ event.click(n, this); });};
		if(this.checked) groups[n].currentElem = eStar;
		$(this).remove();
		if(i + 1 == this.length) event.reset(n);
	});
	for(n in groups)
		if(groups[n].currentElem){event.fill(n, groups[n].currentElem, 'star_on');$(groups[n].valueElem).val($(groups[n].currentElem).children('a').attr( "title" ));}
	return this;};
})(jQuery);
