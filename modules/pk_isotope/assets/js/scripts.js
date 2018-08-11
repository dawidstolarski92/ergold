$(window).load(function(){
    // cache container
    var $container = $('.isotope'),
        $countdown = $('.pk_countdown');

    $container.isotope({layoutMode: 'fitRows'});

    // filter items when filter link is clicked
    $('.filter a').click(function(){

        var selector = $(this).attr('data-filter-value');
        $container.isotope({
            filter: selector
        });

        $('.filter a.selected').removeClass('selected');
        $(this).addClass('selected');

        return false; // prevent default for <a>
    });

    if ($(window).width() < 768) {
        $(window).resize(function(){
            $container.isotope('reLayout');
        });
    }

    $(function() {

        $countdown.each(function(index, el){
            var data_to = $(this).data('to'),
                data_id = $(this).data('id'),
                data_days = $(this).data('days'),
                data_hours = $(this).data('hours'),
                data_min = $(this).data('min'),
                data_sec = $(this).data('sec'),
                data_and = $(this).data('and'),
                data_phrase = $(this).data('phrase');

            $container.find('.countdown-'+data_id).countdown({
                date: data_to,
                render: function(data) {
                    $(this.el).html("<div>" + this.leadingZeros(data.days, 2) + " <span>"+data_days+"</span></div><div>" + this.leadingZeros(data.hours, 2) + " <span>"+data_hours+"</span></div><div>" + this.leadingZeros(data.min, 2) + " <span>"+data_min+"</span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>"+data_sec+"</span></div>");
                    $(this.el).attr('title', this.leadingZeros(data.days, 2)+" "+data_days+" "+data_and+" "+this.leadingZeros(data.hours, 2)+" "+data_hours+" "+data_phrase);
                }
            });

        });
    });

});
