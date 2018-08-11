(function($){

    /**
     * Copyright 2012, Digital Fusion
     * Licensed under the MIT license.
     * http://teamdf.com/jquery-plugins/license/
     *
     * @author Sam Sehnert
     * @desc A small plugin that checks whether elements are within
     *       the user visible viewport of a web browser.
     *       only accounts for vertical position, not horizontal.
     */
    var $w = $(window);
    $.fn.visible = function(partial,hidden,direction){

        if (this.length < 1)
            return;

        var $t        = this.length > 1 ? this.eq(0) : this,
            t         = $t.get(0),
            vpWidth   = $w.width(),
            vpHeight  = $w.height(),
            direction = (direction) ? direction : 'both',
            clientSize = hidden === true ? t.offsetWidth * t.offsetHeight : true;

        if (typeof t.getBoundingClientRect === 'function'){

            // Use this native browser method, if available.
            var rec = t.getBoundingClientRect(),
                tViz = rec.top    >= 0 && rec.top    <  vpHeight,
                bViz = rec.bottom >  0 && rec.bottom <= vpHeight,
                lViz = rec.left   >= 0 && rec.left   <  vpWidth,
                rViz = rec.right  >  0 && rec.right  <= vpWidth,
                vVisible   = partial ? tViz || bViz : tViz && bViz,
                hVisible   = partial ? lViz || rViz : lViz && rViz;

            if(direction === 'both')
                return clientSize && vVisible && hVisible;
            else if(direction === 'vertical')
                return clientSize && vVisible;
            else if(direction === 'horizontal')
                return clientSize && hVisible;
        } else {

            var viewTop         = $w.scrollTop(),
                viewBottom      = viewTop + vpHeight,
                viewLeft        = $w.scrollLeft(),
                viewRight       = viewLeft + vpWidth,
                offset          = $t.offset(),
                _top            = offset.top,
                _bottom         = _top + $t.height(),
                _left           = offset.left,
                _right          = _left + $t.width(),
                compareTop      = partial === true ? _bottom : _top,
                compareBottom   = partial === true ? _top : _bottom,
                compareLeft     = partial === true ? _right : _left,
                compareRight    = partial === true ? _left : _right;

            if(direction === 'both')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop)) && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
            else if(direction === 'vertical')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop));
            else if(direction === 'horizontal')
                return !!clientSize && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
        }
    };

})(jQuery);

// equal height plugin
(function($) {
    $.fn.equalHeights = function() {
        var maxHeight = 0,
            $this = $(this);
        $this.each( function() {
            var height = $(this).innerHeight();
            if ( height > maxHeight ) { maxHeight = height; }
        });
        return $this.css('height', maxHeight);
    };
    // auto-initialize plugin
    $('[data-equal]').each(function(){
        var $this = $(this),
            target = $this.data('equal');
        $this.find(target).equalHeights();
    });
})(jQuery);

//plugin home http://itsmeara.com/jquery/atooltip/
 (function($) {
    $.fn.aToolTip = function(options) {
        /**
            setup default settings
        */
        var defaults = {
            // no need to change/override
            closeTipBtn: 'aToolTipCloseBtn',
            toolTipId: 'aToolTip',
            // ok to override
            fixed: false,
            clickIt: false,
            inSpeed: 200,
            outSpeed: 0,
            tipContent: '',
            toolTipClass: 'defaultTheme',
            xOffset: 5,
            yOffset: 5,
            onShow: null,
            onHide: null
        },
        // This makes it so the users custom options overrides the default ones
        settings = $.extend({}, defaults, options);
    
        return this.each(function() {
            var obj = $(this);
            /**
                Decide weather to use a title attr as the tooltip content
            */
            if(obj.attr('title')){
                // set the tooltip content/text to be the obj title attribute
                var tipContent = obj.attr('title');  
            } else {
                // if no title attribute set it to the tipContent option in settings
                var tipContent = settings.tipContent;
            }
            
            /**
                Build the markup for aToolTip
            */
            var buildaToolTip = function(){
                $('body').append("<div id='"+settings.toolTipId+"' class='"+settings.toolTipClass+"'><p class='aToolTipContent'>"+tipContent+"</p></div>");
                
                if(tipContent && settings.clickIt){
                    $('#'+settings.toolTipId+' p.aToolTipContent')
                    .append("<a id='"+settings.closeTipBtn+"' href='#' alt='close'>close</a>");
                }
            },
            /**
                Position aToolTip
            */
            positionaToolTip = function(){                
                var postop = obj.offset().top - $('#'+settings.toolTipId).outerHeight() - settings.yOffset;
                if (postop < 0)
                    postop = obj.offset().top + $('#'+settings.toolTipId).outerHeight()-10;
                var posleft = obj.offset().left + obj.outerWidth() + settings.xOffset;
                $('#'+settings.toolTipId).css({                
                    top: postop + 'px',
                    left: posleft + 'px'
                })
                .stop().fadeIn(settings.inSpeed, function(){
                    if ($.isFunction(settings.onShow)){
                        settings.onShow(obj);
                    }
                });             
            },
            /**
                Remove aToolTip
            */
            removeaToolTip = function(){
                // Fade out
                $('#'+settings.toolTipId).stop().fadeOut(settings.outSpeed, function(){
                    $(this).remove();
                    if($.isFunction(settings.onHide)){
                        settings.onHide(obj);
                    }
                });             
            };
            
            /**
                Decide what kind of tooltips to display
            */
            // Regular aToolTip
            if(tipContent && !settings.clickIt){    
                // Activate on hover    
                obj.hover(function(){
                    // remove already existing tooltip
                    $('#'+settings.toolTipId).remove();
                    obj.attr({title: ''});
                    buildaToolTip();
                    positionaToolTip();
                }, function(){ 
                    removeaToolTip();
                }); 
            }           
            
            // Click activated aToolTip
            if(tipContent && settings.clickIt){
                // Activate on click    
                obj.click(function(el){
                    // remove already existing tooltip
                    $('#'+settings.toolTipId).remove();
                    obj.attr({title: ''});
                    buildaToolTip();
                    positionaToolTip();
                    // Click to close tooltip
                    $('#'+settings.closeTipBtn).click(function(){
                        removeaToolTip();
                        return false;
                    });      
                    return false;           
                });
            }
            
            // Follow mouse if enabled
            if(!settings.fixed && !settings.clickIt){
                obj.mousemove(function(el){
                    var postop = el.pageY - $('#'+settings.toolTipId).outerHeight() - settings.yOffset;
                    if (postop < 0)
                        postop = el.pageY + $('#'+settings.toolTipId).outerHeight()-10;
                    var posleft = el.pageX + settings.xOffset;
                    $('#'+settings.toolTipId).css({
                        top: postop,
                        left: posleft
                    });
                });         
            }           
          
        }); // END: return this
    };
})(jQuery);


$(document).ready(function() {

    if ($('.pk_themesettings')[0]) {
        var pk_json = $('.pk_themesettings').data('options');
    }

    var is_touch_device = 'ontouchstart' in document.documentElement;
    var wWidth = $(window).width();
    //console.log(wWidth);
    var currentBreakpoint; // default's to blank so it's always analysed on first load
    var didResize  = true; // default's to true so it's always analysed on first load
    // on window resize, set the didResize to true
    $(window).resize(function() { didResize = true; });

    var page_name = 'index';

    // every 1/2 second, check if the browser was resized
    // we throttled this because some browsers fire the resize even continuously during resize
    // that causes excessive processing, this helps limit that
    setInterval(function() {
        if(didResize) {

            didResize = false;
            var newBreakpoint = $(window).width();

            if (newBreakpoint > 1199) 
                newBreakpoint = "breakpoint_1";
            else if ((newBreakpoint <= 1199) && (newBreakpoint >= 992)) 
                newBreakpoint = "breakpoint_2";
            else if ((newBreakpoint <= 991) && (newBreakpoint >= 768)) 
                newBreakpoint = "breakpoint_3";
            else if ((newBreakpoint <= 767) && (newBreakpoint >= 400)) 
                newBreakpoint = "breakpoint_4";
            else if (newBreakpoint <= 399) 
                newBreakpoint = "breakpoint_5";

            if (currentBreakpoint != newBreakpoint) {            

                if (newBreakpoint === 'breakpoint_1') {// min-width: 1200px
                    currentBreakpoint = 'breakpoint_1';

                }            
                if (newBreakpoint === 'breakpoint_2') {//max-width: 1199px
                    currentBreakpoint = 'breakpoint_2';

                }               
                if (newBreakpoint === 'breakpoint_3') {// max-width: 991px
                    currentBreakpoint = 'breakpoint_3';                       

                }               
                if (newBreakpoint === 'breakpoint_4') {//max-width: 768px
                    currentBreakpoint = 'breakpoint_4';
                    
                }
                if (newBreakpoint === 'breakpoint_5') {//max-width: 399px
                    currentBreakpoint = 'breakpoint_5';
                   
                }   
            }
        }
    }, 500);    
    
    var timer;
    var dd_cont = '.dd_container';
    $('.dd_el').hover(
        function () {
            clearTimeout(timer);
            $(dd_cont).stop().slideUp(200, 'easeOutExpo');
            $(this).find(dd_cont).stop().slideDown(500, 'easeOutExpo');
        }, 
        function () {
            var $self = $(this).find(dd_cont);
            timer = setTimeout(function() {
                $self.stop().slideUp(200, 'easeOutExpo');
            }, 500);
        }
    );

    var w = $(window).width();
 

    // grid/list view. change DOM
//    if (listing_view_buttons == true) {


        var $list_btn = $("#view_list"),
            $grid_btn = $("#view_grid"),
            $products = $("#products"),
            $product_list = $(".product_list");

        $grid_btn.click(function() {
             $list_btn.removeClass("active");
             $(this).addClass("active");
             $products.addClass("view_grid").removeClass("view_list"); // set class            
             $.cookie("listingView", "view_grid"); // set cookie
             $product_list.animate({opacity: "0"}, 0);// set class         
             $product_list.delay(200).animate({opacity: "1"}, 300);// set class      
        });
        $list_btn.click(function() {
            $grid_btn.removeClass("active");
             $(this).addClass("active");
             $products.addClass("view_list").removeClass("view_grid"); // set class           
             $.cookie("listingView", "view_list"); // set cookie
             $product_list.animate({opacity: "0"}, 0);// set class         
             $product_list.delay(200).animate({opacity: "1"}, 300);// set class    
        });
        
    //}  

    $('#search_block_top').hover(
        function () {
            $(this).addClass("hvr");
            $(".ac_results").removeClass('hidden');
        }, 
        function () {
            $(this).delay(200).removeClass("hvr");
    });
    
    $(document).on("mouseover", ".ac_results", function(e) {
        $('#search_block_top').addClass("hvr");
    });
    $(document).on("mouseleave", ".ac_results", function(e) {
        $('#search_block_top').delay(200).removeClass("hvr");
        $(this).addClass('hidden');
    });

    // #scroll
    if ( $('#scrollTop')[0] ) {

        $(window).scroll(function () {
            var position = $("#scrollTop").offset();


            //$("#scrollTop").text(position.top);
            if (position.top < 1200) {
                $("#scrollTop").fadeOut(600);
            } else {
                $("#scrollTop").fadeIn(600);
            }
        });

        $("#scrollTop a").click(function(){
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return false;
        });

    }

    // #menu
    
    if (typeof pk_json !== 'undefined') {

        if (pk_json.gs_sticky_menu) {

            var menu = $('.flexmenu-container');
            if ( menu[0] && ($(window).width() > 980)) {
                var stickyNavTop = menu.offset();
                var stickyNav = function(){
                    var scrollTop = $(window).scrollTop();
                    if (scrollTop > stickyNavTop.top) {
                        menu.addClass('sticky');
                    } else {
                        menu.removeClass('sticky');
                    }
                };

                stickyNav();
                $(window).scroll(function() {
                    stickyNav();
                });
            }
        }

    }

    function addFavoriteProduct(e) {
        var $element = $(e);
        if (!$element.length)
            var $element = $('.product_like');
        var $picture = $element.clone();
        var content = '<li class="favoriteproduct clearfix"><a href="#" class="favProdImage"><img src="'+$picture[0].src+'" alt=""/></a><div class="text_desc"><a href="#">'+$picture[0].alt+'</a></div></li>';
        $('.favoritelist ul').append(content);
    }
    // wishlist button    
    $(".registered #wishlist_button").click(function(){
        if (!$(this).hasClass("active")) { 
            $(this).addClass("active");
        }
    });     

    /* product page tabs */   

    /* order page tabs */
    $(".shipping-and-taxes-tab").click(function(){    
        $('.additional-cart-tabs div').removeClass('active');
        $(this).addClass("active");        
        $(".shipping-and-taxed-content").show('fast');
        $(".vouchers-content").hide('fast');
    });
    $(".vouchers-tab").click(function(){    
        $('.additional-cart-tabs div').removeClass('active');
        $(this).addClass("active");        
        $(".shipping-and-taxed-content").hide('fast');
        $(".vouchers-content").show('fast');
    });

    /* product page*/
    $(".tab-titles h3").click(function(){
        $(".tab-titles h3").removeClass("active-tab");
        $(this).addClass("active-tab");
        var num = $(this).data("title");
        $("#pb-left-column section").removeClass("active-section");
        $("#pb-left-column").find("[data-section='" + num + "']").addClass("active-section");
    });
    /* end product page*/
     
    $(function(){
        $('.sections-titles .page-product-heading').click(function(){
            $(".sections-titles h3").removeClass("active");
            $(this).addClass("active");
            var labelId = $(this).data("tab-label");            
            $(".sections").find("[data-tab]").fadeOut(200, function () {                                
                $(this).addClass("d-hidden");                
            });            
            $(".sections").find('[data-tab='+labelId+']').delay(200).fadeIn(400, function () {                
                $(this).removeClass('d-hidden');
            });
        });
    });

    $(".accordionButton").click(function(){
        var accid = $(this).data("tab-acc");
        var th = $(".tab-slider-wrapper").find('[data-acc='+accid+']');
        if ($(th).hasClass("show")) {
            $(th).removeClass("show");
        } else {
            $(".accordionContent").removeClass("activeCarousel");
            $(".tab-slider-wrapper").find('[data-acc='+accid+']').addClass("activeCarousel");
        }
    });

    // wishlist
    $(".product_wishlist").click(function(){

        var id_product = $(this).closest('li').data("productid");
        var addtowish = WishlistCart('wishlist_block_list', "add", id_product, false, 1, ".image-cover");
        var num = parseInt($(".wlQty").text());
        if ($('.wlQty')[0])
            $(".wlQty").text(num+1);
        return false;

    });

    $(".product_wishlist.remove").click(function(){

        var id_product = $(this).closest('li').data("productid");
        var addtowish = WishlistCart('wishlist_block_list', "delete", id_product, false, 1, ".image-cover");
        var num = parseInt($(".wlQty").text());
        if ($('.wlQty')[0])
            $(".wlQty").text(num-1);
        return false;

    });

    // TO DO: ajax clear tpl cache
    function clearCache(module_name) {
        $.ajax({
            type: "POST",
            headers: { "cache-control": "no-cache" },
            async: false,
            url: tsDir + 'ajax.php',
            data: "module="+module_name,
            success: function(data) {
                
            }
        });
    }

    if ( $('.cat_image')[0] ) {
        BackgroundCheck.init({
          targets: '.cat_desc',
          images: '.cat_image'
        });   
    }

    $(".cookie-message .btn").click(function(){
        $('.cookie-message').animate({'bottom':'-100px'}, 500);         
        $.cookie("cookie-message", "0"); // set cookie
    });


});