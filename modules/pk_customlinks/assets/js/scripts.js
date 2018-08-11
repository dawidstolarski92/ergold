$(document).ready(function () {

	var $tab = $('.customer-form-tab'),
		$form = $('.customer-form'),
		$regform = $('.register-button2');

	$tab.click(function(){
		$tab.toggleClass('active');
		$form.toggleClass('active');
	});

});