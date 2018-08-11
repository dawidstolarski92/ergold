$(document).ready(function(){

    var aw_module = $('.pk_awshowcaseslider');
    var config = {
        mode: aw_module.data('mode'),
        speed: aw_module.data('speed'),
        auto: aw_module.data('auto'),
        pause: aw_module.data('pause'),
        random: aw_module.data('random'),
        controls: aw_module.data('controls'),
        pager: true,
        hover: aw_module.data('hover'),
    }

    var video = $(".videoframe").attr("src");

    var slider = $('#aw_slider').bxSlider_mod({   
        mode: config.mode, 
        speed: config.speed, 
        auto: config.auto,
        pause: config.pause,
        randomStart: config.random,
        controls: config.controls, 
        pager: config.pager, 
        autoHover: config.hover, 
        onSlideBefore: function() {
            $(".showcase-tooltips a").hide();                
        },
        onSlideAfter: function() {                   
           $(".showcase-tooltips a").fadeIn('500');
        }
  });
    
  $(".showcase-plus-anchor").mouseenter(function()
    {
        var y = (parseInt($(this).css('top').replace('px', '')) + (parseInt($(this).height()))/2);
        var x = (parseInt($(this).css('left').replace('px', '')) + (parseInt($(this).width()))/2);
        var content = $(this).html();           
        slider.animateTooltip(".bx-viewport", x, y, content);

    });
    $(".showcase-plus-anchor").mouseleave(function()
    {
        var y = (parseInt($(this).css('top').replace('px', '')) + (parseInt($(this).height()))/2);
        var x = (parseInt($(this).css('left').replace('px', '')) + (parseInt($(this).width()))/2);
        var content = $(this).html();
        slider.animateTooltip(".bx-viewport", x, y, content);
    });
    
});
