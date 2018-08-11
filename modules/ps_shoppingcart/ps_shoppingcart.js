/* global $, prestashop */

/**
 * This module exposes an extension point in the form of the `showModal` function.
 *
 * If you want to override the way the modal window is displayed, simply define:
 *
 * prestashop.blockcart = prestashop.blockcart || {};
 * prestashop.blockcart.showModal = function myOwnShowModal (modalHTML) {
 *   // your own code
 *   // please not that it is your responsibility to handle closing the modal too
 * };
 *
 * Attention: your "override" JS needs to be included **before** this file.
 * The safest way to do so is to place your "override" inside the theme's main JS file.
 *
 */
$(document).ready(function(){
  console.log('function works2');


 

$('input[name="shipping_address[alias]"], input[name="shipping_address[dni]"]').val(1); 
$('.sort_data[data-percentage="14"]').hide();

$('.btn.nextStep').click(function(){

console.log('test');
var error = false;

if($('#shipping-address-existing:checked').length === 0) {
firstname = $('input[name="shipping_address[firstname]"]');
if (firstname.val() === ""){
firstname.addClass('hasError');
error = true;
}
else {
firstname.removeClass('hasError');

} 

address = $('input[name="shipping_address[address1]"]');
if (address.val() === "") {
address.addClass('hasError');
error = true;
}
else
address.removeClass('hasError');

postcode = $('input[name="shipping_address[postcode]"]');
if (postcode.val() === ""){
postcode.addClass('hasError');
error = true; 
}
else {
postcode.removeClass('hasError');

}


city = $('input[name="shipping_address[city]"]');
if (city.val() === "") {
city.addClass('hasError');
error = true;
}
else{
city.removeClass('hasError');

}

phone_mobile = $('input[name="shipping_address[phone_mobile]"]');
if (phone_mobile.val() === "")
{
phone_mobile.addClass('hasError');
error = true;
}

else
{
phone_mobile.removeClass('hasError');

}

alias = $('input[name="shipping_address[alias]"]');
if (alias.val() === ""){
alias.addClass('hasError');
error = true;
}

else{
alias.removeClass('hasError');

}

lastname = $('input[name="shipping_address[lastname]"]');
if (lastname.val() === "") {
lastname.addClass('hasError');
error = true;
}
else {
lastname.removeClass('hasError');

}



email = $('input[name="supercheckout_email"]');
if (email.val() === "") {
email.addClass('hasError');
error = true;
}
else {
email.removeClass('hasError');

}
}

if (error === false) {

$('#columnleft-1, #columnleft-2').toggleClass('active');
$('.orderSteps .stepTwo').addClass('active');

$('.orderSteps .stepOne').addClass('done');




}


});

$('.prevStep').click(function(){
$('#columnleft-1, #columnleft-2').toggleClass('active');
$('.orderSteps .stepTwo').toggleClass('active');});

});

$('.showCart.btn').click(function(){

  $('.orderSummary').toggleClass('active') 
  
  });




$(document).ready(function(){


console.log('works');

$('.blockcart.cart-preview, .dd_container2').mouseenter(function(){

$('.dd_container2').addClass('active');

});

$('.blockcart.cart-preview, .dd_container2').mouseleave(function(){


setTimeout(function(){
$('.dd_container2').removeClass('active');
}, 500);


});


  $('#newsletter-input').focus(function(){
      $('.RodoForm').removeClass('hiddenForm').addClass('shownForm');
      });




});



$(document).ready(function () {
  prestashop.blockcart = prestashop.blockcart || {};

  var showModal = prestashop.blockcart.showModal || function (modal) {
    var $body = $('body');
    $body.append(modal);
    $body.one('click', '#blockcart-modal', function (event) {
      if (event.target.id === 'blockcart-modal') {
        $(event.target).remove();
      }
    });
  };

  $(document).ready(function () {
    prestashop.on(
      'updateCart',
      function (event) {
        var refreshURL = $('.blockcart').data('refresh-url');
        var requestData = {};

        if (event && event.reason) {
          requestData = {
            id_product_attribute: event.reason.idProductAttribute,
            id_product: event.reason.idProduct,
            action: event.reason.linkAction
          };
        }

        $.post(refreshURL, requestData).then(function (resp) {
          $('.blockcart').replaceWith($(resp.preview).find('.blockcart'));
          if (resp.modal) {
            showModal(resp.modal);
          }
        }).fail(function (resp) {
          prestashop.emit('handleError', {eventType: 'updateShoppingCart', resp: resp});
        });
      }
    );
  });
});
