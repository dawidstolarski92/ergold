/**
 *  @author Marcin Kubiak
 *  @copyright  Smart Soft
 *  @license    Commercial license
 *  International Registered Trademark & Property of Smart Soft
 */

jQuery(function($) {
    //remove fields if not static
    $("table.advancedexportfield tbody tr td:nth-child(5)").each(function(){
        if($.trim($(this).html()) != "static") {
            $(this).parent().find("td:nth-child(6)").html("");
            $(this).parent().find("td:nth-child(6)").next().html("");
        }
    });

     var $topLoader = $("#topLoader").percentageLoader({width: 233, height: 233, progress : 0});

     var topLoaderRunning = false;

     $('a.export').click(function() {
         $('#loader-container').css("display", "block");
         // Ignore the click if the animation is already in progress
         if (topLoaderRunning) {
             return false;
         }
         // Set some initial values
         topLoaderRunning = true;
         $topLoader.setProgress(0);
         // We're pretending
         $topLoader.setValue('');

         // A function representing a single 'frame' of our animation
         var animateFunc = function() {
             $.post( urlJson + '&ajax=1&action=getCurrentIndex', function( data ) {
                 var dataObject = JSON.parse(data);
                 $topLoader.setProgress(dataObject.current / dataObject.total);
                 // $topLoader.setValue(dataObject.total.toString());

                 if (dataObject.current < dataObject.total) {
                     setTimeout(animateFunc, 500);
                 } else {
                     topLoaderRunning = false;
                 }
             });
         }
         setTimeout(animateFunc, 500);
     });

     $(".ds-select").each(function() {
         var selectList = $(this).find('option:selected');
         selectList.sort(sort_li);

         var unSelectList = $(this).find("option:not(:selected)");

         $(this).html(selectList);
         $(this).append(unSelectList);
     });

     function sort_li(a, b){
         return ($(b).data('sortby')) < ($(a).data('sortby')) ? 1 : -1;
     }

     $(".ds-select").each(function() {
         $(this).bsmSelect({
            addItemTarget: 'bottom',
             highlight: true,
             sortable: true,
            plugins: [
                $.bsmSelect.plugins.sortable()
            ],
            removeLabel: '<strong>X</strong>',
            containerClass: 'bsmContainer', // Class for container that wraps this widget
            listClass: 'bsmList-custom', // Class for the list ($ol)
            listItemClass: 'bsmListItem-custom', // Class for the <li> list items
            listItemLabelClass: 'bsmListItemLabel-custom', // Class for the label text that appears in list items
            removeClass: 'bsmListItemRemove-custom'

        }).after($("<a href='#' rel='" + $(this).attr('id') + "'>Remove All</a>").click(function() {
            var id = '#' + $(this).attr('rel');
             $(id).parent().find('.bsmList-custom a.bsmListItemRemove-custom').click();
            return false;

        })).after($("<a href='#' rel='" + $(this).attr('id') + "'>Select All</a><span> | </span>").click(function() {
            var id = '#' + $(this).attr('rel');
             $(id).parent().find('.bsmList-custom a.bsmListItemRemove-custom').click();
             $(id).parent().find('.bsmSelect option').each( function() {
                    $(this).attr('selected', 'selected').trigger('change');
             });
             return false;

        })).after($("<div class='clear'></div>"));
    });

     $('#bsmSelectbsmContainer0').attr("multiple", "multiple");
     $('#bsmSelectbsmContainer0 option:first').remove();

     $('span.exportspan').hide();

	$('.list-group-item').click(function(){
		var id = $(this).attr('id');
		$('.list-group-item').removeClass('active');
		$(this).addClass('active');

		$('.product-tab-content').addClass('hide');
		$('.product-tab-content[alt=' + id + ']').removeClass('hide');

        var current_tab_id = $('div.list-group a.list-group-item.active').attr('id');
        $.cookie('current_tab_id', current_tab_id);

        return false;
	});

	$('#save_type').change(function(){
		var id = $(this).val();
        $('input.process1').parent().prev().hide();
		$('input.process1').parent().hide();
        $('input.process2').parent().prev().hide();
		$('input.process2').parent().hide();
	 	var current = 'process' + id;
        $('input[class=' + current + ']').parent().prev().show();
		$('input[class=' + current + ']').parent().show();

        return false;
	});

	$('#save_type').change();

    $("input[name='only_new']:radio").change(function () {
        if($(this).val() == 1)
        {
            $('.datetimepicker').closest('div.margin-form').prev().hide();
            $('.datetimepicker').closest('div.margin-form').hide();
            $('#start_id').closest('div.margin-form').prev().hide();
            $('#start_id').closest('div.margin-form').hide();
            $('#end_id').closest('div.margin-form').prev().hide();
            $('#end_id').closest('div.margin-form').hide();

        }
        if($(this).val() == 0)
        {
            $('.datetimepicker').closest('div.margin-form').prev().show();
            $('.datetimepicker').closest('div.margin-form').show();
            $('#start_id').closest('div.margin-form').prev().show();
            $('#start_id').closest('div.margin-form').show();
            $('#end_id').closest('div.margin-form').prev().show();
            $('#end_id').closest('div.margin-form').show();
        }
    });

     if($.cookie('current_tab_id'))
     {
         var current_tab_id = 'a#' + $.cookie('current_tab_id');
         $('div.list-group a.list-group-item').removeClass('active');
         $(current_tab_id).click();
     }
     else
     {
         $('div.list-group a:first').click();
     }

    $('span.cron_url').parent().removeAttr("onclick");

    var clipboard = new Clipboard('span.cron_button');

    clipboard.on('success', function(e) {
        alert('Copied!');
    });

    clipboard.on('error', function(e) {
        alert('Press Ctrl+C to copy');
    });

    function getUrlParam(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    }

    $('#desc-advancedexportfield-save').click(function(){
        var form = $('.form');
        var action = form.attr('action').substring(0, form.attr('action').indexOf('#'));
        form.attr('action', action + "&submitSaveFields=1").submit();
    });
 });
