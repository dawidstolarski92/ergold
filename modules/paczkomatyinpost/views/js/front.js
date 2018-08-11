/**
 * LICENCE
 * 
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 * 
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 * 
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

function bind_carrier_radio(e) {
	uncheck_cgv();
	initPaczkomatySelect($(this), true);
}
function uncheck_cgv() {
	$("#cgv").prop('checked', false);
	if (typeof $.uniform !== 'undefined')
		$.uniform.update();
	if (typeof updatePaymentMethodsDisplay === 'function')
		updatePaymentMethodsDisplay();
}
function initPaczkomatySelect(object, animate) {
	$("#center_column .paczkomatyinpost_container").remove();
	id = parseInt(object.val());
	target = $(".delivery_options_address").first();
	if (paczkomatyinpost.opc_enabled === 1) {
		id = parseInt(desintifier(id.toString()));
		//target = object.closest('tr,.delivery_option').find('td.carrier_infos');
	}
	if ($.inArray(id, paczkomatyinpost.carrier) !== -1) {
		$("#paczkomatyinpost_cod").hide();
		$("#footer #paczkomatyinpost").clone(true).appendTo(target);
		if (animate === true)
			$("#paczkomatyinpost").fadeIn();
		else
			$("#paczkomatyinpost").show();
		if (paczkomatyinpost.machine === '')
			uncheck_cgv();
	} else if ($.inArray(id, paczkomatyinpost.carrier_cod) !== -1)
	{
		$("#paczkomatyinpost").hide();
		$("#footer #paczkomatyinpost_cod").clone(true).appendTo(target);
		if (animate === true)
			$("#paczkomatyinpost_cod").fadeIn();
		else
			$("#paczkomatyinpost_cod").show();
		if (paczkomatyinpost.machine_cod === '')
			uncheck_cgv();
	} else {
		if (animate === true)
			$("#paczkomatyinpost, #paczkomatyinpost_cod").fadeOut();
		else
			$("#paczkomatyinpost, #paczkomatyinpost_cod").hide();
	}
	if (typeof $.uniform !== 'undefined')
		$.uniform.update();
}
var bind_cgv = function (e) {

	id = parseInt($("input.delivery_option_radio:checked").val());
	if (paczkomatyinpost.opc_enabled === 1)
		id = parseInt(desintifier($("#carrierTable input[name='id_carrier']:checked").val().toString()));
	if ($.inArray(id, paczkomatyinpost.carrier) != -1)
		if (paczkomatyinpost.machine.length <= 1) {
			alert("Proszę wybrać paczkomat");
			e.preventDefault();
			e.stopPropagation();
			uncheck_cgv();

			return false;
		}
	if ($.inArray(id, paczkomatyinpost.carrier_cod) != -1)
		if (paczkomatyinpost.machine_cod.length <= 1) {
			alert("Prosze wybrać paczkomat dla paczki za pobraniem");
			e.preventDefault();
			e.stopPropagation();
			uncheck_cgv();
			return false;
		}
};

function desintifier(number) {
	$delimiter_len = parseInt(number[0]);
	number = strrev(number.substr(1));
	$elm = number.split("0".repeat($delimiter_len + 1));
	return strrev($elm.join(','));
}
function strrev(string) {
	return string.split("").reverse().join("");
}

String.prototype.repeat = function (num)
{
	return new Array(num + 1).join(this);
};

function paczkomatyinpostMachineSelected(name, objMachine, openIndex) {
	if (openIndex != '_cod') {
		openIndex = '';
		updateMachine(name, false);
	} else {
		updateMachine(name, true);
	}
	$("#paczkomatyinpost_selected" + openIndex).val(name);
	if (typeof $.uniform !== 'undefined') {
		$.uniform.update("#paczkomatyinpost_selected" + openIndex);
	}

//	$("#paczkomatyinpost_selected" + openIndex).html('Wybrany Paczkomat: ' + objMachine.name + '<br/>' + objMachine.postCode + ' ' + objMachine.town + ', ' + objMachine.street + ' ' + objMachine.buildingNumber + ' (zmień)');
}
function updateMachine(machine, cod) {
	var data = 'updateMachine=true';
	if (cod === true) {
		data = 'updateMachineCod=true';
		paczkomatyinpost.machine_cod = machine;
	} else {
		paczkomatyinpost.machine = machine;
	}
	$.ajax({
		url: paczkomatyinpost.module_dir + 'ajax.php',
		type: 'POST',
		data: 'ajax=true&machine=' + machine + '&' + data
	});
}
