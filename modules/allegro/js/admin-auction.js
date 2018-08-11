$(document).ready(function() {
    $('.auctionMapForm input[name="submitMapAuction"]').click(function(e){
        e.preventDefault();
        var obj = $(this);
        $.ajax({
            url: 'index.php'+window.location.search+'&ajax=1&action=auctionMap',
            dataType: 'json',
            type: 'POST',
            data: $(this).closest('.auctionMapForm').find('input').serialize()
        }).done(function(data){
            if(!data.error){
                obj.closest('.auctionMapForm').html(data.id);
            } else {
                alert(data.error);
            }
        });
    });

    // PS 1.5x
    if (typeof token === 'undefined') {
        token = getParameterByName('token');
    }

    $('.ac_id_product').autocomplete(
        currentIndex+'&token='+token+'&action=getProducts&ajax=1', {
            minChars: 2,
            max: 20,
            width: 500
        }
    ).result(function(event, data, formatted) {
        // Set real ID to hidden input
        $(event.target).next().val(data[1]);
    });
});

// @todo
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}