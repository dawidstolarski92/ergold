/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

$( function() {
    $( "#pokaz_cechy" ).click( function( event ) {
      event.preventDefault();
         if ($( ".cechy_produktu .product-features" ).hasClass( "full_height" )) {
             $( ".cechy_produktu .product-features" ).removeClass( "full_height" );
             $( "#pokaz_cechy" ).removeClass( "rotate_before" );
             $( "#pokaz_cechy" ).text( "Pokaż więcej" );

         } else {
           $( ".cechy_produktu .product-features" ).addClass( "full_height" );
            $( "#pokaz_cechy" ).addClass( "rotate_before" );
             $( "#pokaz_cechy" ).text( "Zwiń" );

         }

    });

    $('body').on('click', ".pagination .page-list li", function() {
        $('html, body').animate({
            scrollTop: $("#products").offset().top
        }, 800);
    });

});
