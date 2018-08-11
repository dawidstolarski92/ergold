jQuery(document).ready(function() {
  $(".pk-categories-list ul").flexisel({
      pref: "pkcl",
      visibleItems: 4,
      animationSpeed: 500,
      autoPlay: false,
      autoPlaySpeed: 3000,            
      pauseOnHover: true,
      enableResponsiveBreakpoints: true,
      clone : true,
      responsiveBreakpoints: { 
          portrait: { 
              changePoint:400,
              visibleItems: 1
          }, 
          landscape: { 
              changePoint:768,
              visibleItems: 2
          },
          tablet: { 
              changePoint:991,
              visibleItems: 3
          },
          tablet_land: { 
              changePoint:1199,
              visibleItems: 4
          }
      }
  });                               
}); 