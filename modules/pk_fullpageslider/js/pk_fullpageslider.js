$(document).ready(function() {

	if (typeof(fps_loop) == 'undefined')
		fps_loop = true;
	if (typeof(fps_speed) == 'undefined')
		fps_speed = 700;
	if (typeof(fps_pause) == 'undefined')
		fps_pause = 3000;
	if (typeof(fps_nav) == 'undefined')
		fps_nav = true;
	if (typeof(fps_navpos) == 'undefined')
		fps_navpos = 'left';
	if (typeof(fps_infinite) == 'undefined')
		fps_infinite = 0;
	if (typeof(titles) == 'undefined')
		titles = '';
	

	if (fps_navpos == 1)
		fps_navpos = 'left';
	else 
		fps_navpos = 'right';

    var is_touch = 'ontouchstart' in document.documentElement;
    console.log(is_touch);


    $('#fullpageslider').fullpage({
    	navigation: fps_nav,
    	navigationPosition: fps_navpos,
    	navigationTooltips: titles,
    	scrollingSpeed: fps_speed,
    	continuousVertical: fps_infinite,
        scrollBar: is_touch,
        afterRender: function () {
        	if (fps_loop == true)
	            setInterval(function () {
	                $.fn.fullpage.moveSectionDown();
	            }, fps_pause);
        },
        onLeave: function(index, nextIndex, direction){
        	$("#fullpageslider .section").addClass("notact");
        },
        afterLoad: function(anchorLink, index){
        	$("#section"+index).removeClass("notact");
            if ($(window).width() > 728) {
                if (index != 1)
                    $("#header").animate({"top":"-120px"}, 500);
                if (index == 1)
                    $("#header").animate({"top":"0px"}, 500);
            }
        },
        afterResize: function(){},
        afterSlideLoad: function(anchorLink, index, slideAnchor, slideIndex){},
        onSlideLeave: function(anchorLink, index, slideIndex, direction){}
    });

    var maxH = 1200;
    var maxW = 1920;
    var currentW = $(window).width();
    var coef = maxW/currentW;
    var newH = currentW*maxH/maxW;
    
    //$('#fullpageslider').height(newH);        
    $('#fullpageslider .subimage').each(function(i, obj) {
        elW = $(this).attr('width');
        $(this).width(elW/coef);
    });                
    $(window).resize(function() {            
        var currentW = $(window).width();
        var coef = maxW/currentW;   
        var newH = currentW*maxH/maxW;
        $('#fullpageslider .subimage').each(function(i, obj) {
            elW = $(this).attr('width');
            $(this).width(elW/coef);
        });
        //$('#fullpageslider').height(newH);                        
    });

});