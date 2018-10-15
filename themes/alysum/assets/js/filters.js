$(document).ready(function(){

    console.log('Filter function loaded!');
    var $document = $(document),
        $element = $('.filter-btn'),
        $menu = $('#PM_ASBlockOutput_2'),
        className = 'active';

    $document.scroll(function() {
        $element.toggleClass(className, $document.scrollTop() >= 200);
    });
    $element.on("click", function(){
        toggler();
    });


    function toggler() {
        $menu.addClass('active');
    }

});