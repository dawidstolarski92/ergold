$(window).load(function() {

  $('.tab-link').click(function() {

      var $el = $(this).closest('.productsCarousel');

      var width = $el.find('.tab-slider').width(), //The width in pixels of your #tab-slider 
          delay = 200; // Pause time between animation in Milliseconds   
          rel = $(this).data('rel');

      $el.find(".tab-content").removeClass("activeCarousel");
      $el.find("[data-acc='" + rel + "']").addClass("activeCarousel");      
      $el.find('.tab').removeClass('active');
      $(this).parent().addClass('active');
      var $contentNum = parseInt($(this).data('rel'), 10);
      $el.find('.tab-slider-wrapper').animate({ marginLeft: '-' + (width * $contentNum - width) }, delay, 'easeOutQuint');
      return false;
  });

  $(function() {

      var $container = $('.productsCarousel'),
          $countdown = $('.pk_pr_countdown');

      $countdown.each(function(index, el){
          var data_to = $(this).data('to'),
              data_id = $(this).data('id'),
              data_days = $(this).data('days'),
              data_hours = $(this).data('hours'),
              data_min = $(this).data('min'),
              data_sec = $(this).data('sec'),
              data_and = $(this).data('and'),
              data_phrase = $(this).data('phrase');

          $container.find('.countdown-'+data_id).countdown({
              date: data_to,
              render: function(data) {
                  $(this.el).html("<div>" + this.leadingZeros(data.days, 2) + " <span>"+data_days+"</span></div><div>" + this.leadingZeros(data.hours, 2) + " <span>"+data_hours+"</span></div><div>" + this.leadingZeros(data.min, 2) + " <span>"+data_min+"</span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>"+data_sec+"</span></div>");
                  $(this.el).attr('title', this.leadingZeros(data.days, 2)+" "+data_days+" "+data_and+" "+this.leadingZeros(data.hours, 2)+" "+data_hours+" "+data_phrase);
              }
          });

      });
  });

});
function tabslider() {

  var $el = $('.productsCarousel'),
      $el_mob = $(".carouselMobile"),
      width = $el.find(".tab-slider").width(), //The width in pixels of your #tab-slider 
      $tabs = $el.find('.tab'), //Your Navigation Class Name      
      rel = $el.find('.tab-nav').find('.active').find('a').data('rel'),
      shift = Math.abs(width*rel-width) * -1;

  $el.find(".tab-content").width(width);  
  $el.find('.tab-slider-wrapper').css({ "margin-left": shift, width: $tabs.length * width }); 

  // mobile
  var width2 = $el_mob.find(".tab-slider").width(), //The width in pixels of your #tab-slider 
      $tabs = $el_mob.find('.tab'), //Your Navigation Class Name      
      rel = $el_mob.find('.tab-nav').find('.active').find('a').data('rel'),
      shift = Math.abs(width2*rel-width2) * -1;

  $el_mob.find(".tab-content").width(width2);  
  $el_mob.find('.tab-slider-wrapper').css({ "margin-left": shift, width: $tabs.length * width2 }); 

}

jQuery(document).ready(function() {

  tabslider();

  var $container = $('.pk_products_list');

    $container.each(function(index, el){

      var prefix = $(this).data('prefix'),
          nonce  = $(this).data('nonce');

      var options = $('.js-pk_products-'+nonce).data('options'),
          visible = 4;

      if (typeof options !== 'undefined') {
        var visible = options.pk_nbr_vis; 
      }

      $('.'+prefix+'-pk_products-'+nonce).flexisel({
        pref: prefix,
        visibleItems: visible,
        animationSpeed: 500,
        autoPlay: false,
        autoPlaySpeed: 4500,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        clone : true,
        responsiveBreakpoints: { 
             portrait: { 
                  changePoint:400,
                  visibleItems: 1
              }, 
              landscape: { 
                  changePoint:728,
                  visibleItems: 2
              },
              tablet: { 
                  changePoint:980,
                  visibleItems: 3
              },
              tablet_land: { 
                  changePoint:1170,
                  visibleItems: 3
              }
        }
      });

    });

    var currentBreakpoint; // default's to blank so it's always analysed on first load
    var didResize  = true; // default's to true so it's always analysed on first load

    $(window).resize(function() {
      didResize = true;
    });
    setInterval(function() {
      if(didResize) {
        didResize = false;

        var newBreakpoint = $(window).width();

          if (newBreakpoint > 1170) 
              newBreakpoint = "breakpoint_1";
          else if ((newBreakpoint <= 1170) && (newBreakpoint >= 980)) 
              newBreakpoint = "breakpoint_2";
          else if ((newBreakpoint <= 979) && (newBreakpoint >= 728)) 
              newBreakpoint = "breakpoint_3";
          else if ((newBreakpoint <= 727) && (newBreakpoint >= 400)) 
              newBreakpoint = "breakpoint_4";
          else if (newBreakpoint <= 399) 
              newBreakpoint = "breakpoint_5";

        // if the new breakpoint is different to the old one, do some stuff
        if (currentBreakpoint != newBreakpoint) {                         

          if (newBreakpoint === 'breakpoint_1') {
            currentBreakpoint = 'breakpoint_1';
            $(".productsCarousel").removeClass("carouselMobile").addClass("carouselDesktop");
            tabslider();
          }
          if (newBreakpoint === 'breakpoint_2') {
              currentBreakpoint = 'breakpoint_2';                                
              $(".productsCarousel").removeClass("carouselMobile").addClass("carouselDesktop");
              tabslider();
          }   
          if (newBreakpoint === 'breakpoint_3') {                                
              currentBreakpoint = 'breakpoint_3';                                
              $(".productsCarousel").removeClass("carouselMobile").addClass("carouselDesktop");
              tabslider();
          } 
          if (newBreakpoint === 'breakpoint_4') {                                
            $(".productsCarousel").removeClass("carouselDesktop").addClass("carouselMobile");
              currentBreakpoint = 'breakpoint_4';                                
              tabslider();
          } 
          if (newBreakpoint === 'breakpoint_5') {                                
              currentBreakpoint = 'breakpoint_5';                                
              $(".productsCarousel").removeClass("carouselDesktop").addClass("carouselMobile");
              tabslider();
          } 
        }
      }
    }, 250);                                   
}); 