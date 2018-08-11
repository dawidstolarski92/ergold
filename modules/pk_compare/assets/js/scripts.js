var button = '.add_to_compare',
    $form = $('.compare_info'),
    $counter = $('.total-compare-val'),
    module_url = $form.data('ajax'),
    comparedProductsIds = $form.data('ids'),
    comparator_max_item = $form.data('comparemax'),
    max_item = $form.data('max'),
    min_item = $form.data('min');

$(document).ready(function(){

    $(document).on('click', button, function(e){
        e.preventDefault();
        addToCompare(parseInt($(this).data('pid')));
    });

    totalCompareButtons();

    $(document).on('click', '.cmp-remove', function(e){
        e.preventDefault();
        var idProduct = parseInt($(this).data('pid'));
        $.ajax({
            url: module_url+'?ajax=1&action=remove&id_product=' + idProduct,
            async: false,
            cache: false,
            success: function(data) {
                $('td.product-'+idProduct).fadeOut(600);
            },
            error: function(){
                alert('Some error');
            }
        });
    });

});

function addToCompare(productId) {
    
    var totalValueNow = parseInt($counter.text()),
        action, 
        totalVal;

    if($.inArray(parseInt(productId),comparedProductsIds) === -1)
        action = 'add';
    else
        action = 'remove';

    $.ajax({
        url: module_url+'?ajax=1&action='+action+'&id_product=' + productId,
        async: true,
        cache: false,
        success: function(data) {
            if (action === 'add' && comparedProductsIds.length < comparator_max_item) {
                comparedProductsIds.push(parseInt(productId)),
                $(button+"[data-pid='"+productId+"']").addClass('in_comparison');
                totalVal = totalValueNow +1,
                $counter.text(totalVal),
                totalValue(totalVal);

            }
            else if (action === 'remove') {
                comparedProductsIds.splice($.inArray(parseInt(productId),comparedProductsIds), 1),
                $(button+"[data-pid='"+productId+"']").removeClass('in_comparison');
                totalVal = totalValueNow -1,
                $counter.text(totalVal),
                totalValue(totalVal);
            }
            else {
                if (!!$.prototype.fancybox)
                    $.fancybox.open([{
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: '<p class="fancybox-error">' + max_item + '</p>'
                        }
                    ], {
                        padding: 0
                    });
                else
                    alert(max_item);
            }
            totalCompareButtons();
        },
        error: function(){},
        beforeSend: function(){
            $(button+"[data-pid='"+productId+"']").addClass('in_progress');
        },
        complete: function(){
            $(button+"[data-pid='"+productId+"']").removeClass('in_progress');
        }
    });
}

function compareButtonsStatusRefresh() {   

    $(button).each(function() {
        if ($.inArray(parseInt($(this).data('id-product')),comparedProductsIds)!== -1)
            $(this).addClass('in_comparison');
        else
            $(this).removeClass('in_comparison');
    });
    
}

function totalCompareButtons() {

    var totalProductsToCompare = parseInt($counter.text());
    if (typeof totalProductsToCompare !== "number" || totalProductsToCompare === 0)
        $('.bt_compare').attr("disabled",true);
    else
        $('.bt_compare').attr("disabled",false);

}

function totalValue(value) {
    $counter.text(value);
}