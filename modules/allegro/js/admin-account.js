$(document).ready(function() {
	toggleSandboxKey(0);

	$('input[name="sandbox"]').change(function(){
		toggleSandboxKey();
	});

	// On update account
	if(window.location.href.indexOf('id_allegro_account') != -1)
	{
		$('#a_login').attr('readonly', true);
		$('#id_allegro_country, #id_allegro_country option:not(:selected)').attr('disabled', true);
		$('label[for="sandbox_on"], label[for="sandbox_off"]').on('click', function(e) {e.preventDefault();return false;}).css({opacity : 0.5});
	}

	function toggleSandboxKey(speed)
	{
		if($('input[name="sandbox"]:checked').val() == 1)
			$('#sandbox_key').closest('.form-group').slideDown(speed);
		else
			$('#sandbox_key').closest('.form-group').slideUp(speed);
	}
});