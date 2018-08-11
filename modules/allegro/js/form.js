$(document).ready(function(){
	// Allegro category select AJAX
	$(document).on('change', 'select.cSelect', function(){
		var select = $(this);
		$.ajax({
			url: 'index.php' + window.location.search + '&ajax=1&action=getCategorySelect&id_parent=' + select.val(),
			dataType: 'json',
			beforeSend: function(xhr){
				select.nextAll().remove();
				$('#features').find('input, textarea, select').attr('disabled', true);
			}
		}).done(function(data){
			if(!data.error){
			    $(data.select_html).insertAfter(select);
			    if(data.features_html)
			    	$('#features').html(data.features_html);

				if($('select.cSelect:visible').length > 3)
					$('select.cSelect:visible').eq(0).hide();
				else if($('select.cSelect:visible').length < 3)
					$('select.cSelect:hidden').eq(-1).show();
			} else {
				alert(data.error);
			}
	  	});
	});

	// Category update
	$(document).ready(function(){
		$('#updateCategory').click(function(event){
			event.preventDefault();

			$('#features').find('input, textarea, select').attr('disabled', true);
			$('#categoryPath').remove();
			$('#categoryContainer').show();

            $('input[name="field[2]"]').val('');
		});
	});

	// Override switch
	$('input.override-switch').change(function(){
		var is_checked = $(this).is(':checked');
		$(this).closest('.form-group').find('input, textarea, select').not('.override-switch').attr('disabled', !is_checked);
		if(!is_checked && $(this).closest('.form-group').find('input, textarea, select').not('.override-switch').data('parent-value')) {
			//$(this).closest('.form-group').find('input, textarea, select').not('.override-switch').val($(this).closest('.form-group').find('input, textarea, select').not('.override-switch').data('parent-value'));
		}
	});
});
