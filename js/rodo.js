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
            toggler();
        });


        function toggler() {
            $menu.addClass('active');
        }

});

