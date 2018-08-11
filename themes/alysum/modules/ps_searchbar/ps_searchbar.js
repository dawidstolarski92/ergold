/* global $ */
$(document).ready(function () {

    var $searchWidget = $('#search_widget');
    var $searchBox    = $searchWidget.find('input[type=text]');
    var searchURL     = $searchWidget.attr('data-search-controller-url');
    var marker_class  = 'pk_search_result';

    $.widget('prestashop.psBlockSearchAutocomplete', $.ui.autocomplete, {
        _renderItem: function (ul, product) {
            ul.addClass(marker_class).css({'width': $searchBox.width()+'px'});
            return $("<li>")
                .append($(
                    '<div class="mini-product"><div class="thumbnail-container relative"><div class="thumbnail product-thumbnail"><a href="'+product.link+'" class="relative"><img src="'+product.cover.small.url+'"></a></div><div class="product-description"><h3 class="product-title"><a href="'+product.link+'">'+product.name+'</a></h3><div class="product-price-and-shipping"><span class="regular-price">'+product.regular_price+'</span><span class="price">'+product.price+'</span></div></div></div>')
                ).appendTo(ul);
                
        }
    });

    $searchBox.psBlockSearchAutocomplete({
        source: function (query, response) {
            $.get(searchURL, {
                s: query.term,
                resultsPerPage: 10
            }, null, 'json')
            .then(function (resp) {
                $searchWidget.addClass('shown');
                response(resp.products);
            })
            .fail(response);
        },
        select: function (event, ui) {
            var url = ui.item.url;
            window.location.href = url;
        },
    });

    
    var target = document.getElementById('ui-id-1');
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        
        if ( $(target).css('display') == 'none' ){
            $searchWidget.removeClass('shown');
        }

      });    
    });
    
    var config = { attributes: true, childList: false, characterData: false };
    observer.observe(target, config);

});