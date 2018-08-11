$(document).ready(function(){

    var pkm_vis = $('.pk-brands').data('visible');
    if ( (typeof pkm_vis === 'undefined') || (pkm_vis === '') ) {
        pkm_vis = 7;
    }

    $(".m-list").flexisel({
    	pref: "mnf",
        visibleItems: pkm_vis,
        animationSpeed: 1000,
        autoPlay: false,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 2
            }, 
            landscape: { 
                changePoint:728,
                visibleItems: 3
            },
            tablet: { 
                changePoint:980,
                visibleItems: 5
            },
            tablet_land: { 
                changePoint:1170,
                visibleItems: pkm_vis
            }
        }
    });
});