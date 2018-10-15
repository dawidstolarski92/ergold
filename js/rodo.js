$(document).ready(function(){
    var $document = $(document),
        $element = $('.filter-btn'),
        $menu = $('#PM_ASBlockOutput_2'),
        className = 'active';

    $('#newsletter-input').focus(function(){
        $('.RodoForm').removeClass('hiddenForm').addClass('shownForm');
        });

        $document.scroll(function() {
            $element.toggleClass(className, $document.scrollTop() >= 200);
        });
        $element.on("click", function(){
            console.log('clicked filters');
            toggler();
        });


        function toggler() {
            console.log('function toggler also works');
            $('#PM_ASBlockOutput_2').addClass('active');
        }

});

