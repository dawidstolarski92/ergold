$(window).load(function() {

  var $el = $('.pk_bannercarousel');
  var config = {
      num: $el.data('num'),
      animation_speed: $el.data("animationspeed"),
      autoplay: $el.data("autoplay"),
      autoplay_speed: $el.data("autoplayspeed"),
      pauseonhover: $el.data("pauseonhover"),
      showbuttons: $el.data("showbuttons"),
      pref: $el.data("pref")
  }

  var v_num = config.num;
  if ( $('#left_column')[0] )
      if (v_num > 2)
        var num = (v_num - 1);
      else
        var num = v_num;          
    else
      var num = v_num;

     $("#sliderCarousel").flexisel({
          pref: config.pref,
          visibleItems: num,
          animationSpeed: config.animation_speed,
          autoPlay: config.autoplay,
          autoPlaySpeed: config.autoplay_speed,            
          pauseOnHover: config.pauseonhover,
          showbuttons : config.showbuttons,
          enableResponsiveBreakpoints: true,
          clone : true,
          responsiveBreakpoints: { 
              portrait: { 
                  changePoint:400,
                  visibleItems: 1
              }, 
              landscape: { 
                  changePoint:768,
                  visibleItems: 1
              },
              tablet: { 
                  changePoint:991,
                  visibleItems: 2
              },
              tablet_land: { 
                  changePoint:1199,
                  visibleItems: num
              }
          }
      });   
}); 