// No "$(document).ready..." because of bug in PS 1.5.1x that cause load js files before jQuery
document.addEventListener("DOMContentLoaded", function(){
    // Fix missing icons in admin panel (PS 1.5)
    if(window.location.href.indexOf('AdminAllegro') > -1) {
        $('fieldset legend > img').attr('src', '../img/t/AdminPreferences.gif');
    }

    // Allegro preferences
	// if(window.location.href.indexOf('AdminAllegroPreferences') > -1) {
	// 	$('#configuration_fieldset_order_import_shipping .form-group:gt(5)').hide();
	// 	$('#configuration_fieldset_order_import_shipping .form-wrapper').append(
	// 		'<a class="btn btn-warning" onclick="$(\'#configuration_fieldset_order_import_shipping .form-group\').show();">&darr;&darr;&darr;</a>'
	// 	);
	// }
});