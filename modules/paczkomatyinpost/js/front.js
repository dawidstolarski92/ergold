/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2014 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
function bind_carrier_radio(e) {
	initPaczkomatySelect($(this), true);
}
function initPaczkomatySelect(object, animate) {
	id = parseInt(object.val());
	target = object.closest('tr').find('td.delivery_option_logo').next('td');
	if (paczkomatyinpost.opc_enabled === 1) {
		id = parseInt(desintifier(id.toString()));
		target = object.closest('tr').find('td.carrier_infos');
	}
	if ($.inArray(id, paczkomatyinpost.carrier) !== -1) {
		$("#paczkomatyinpost_cod").hide();
		$("#paczkomatyinpost").appendTo(target);
		if (animate === true)
			$("#paczkomatyinpost").fadeIn();
		else
			$("#paczkomatyinpost").show();
	} else if ($.inArray(id, paczkomatyinpost.carrier_cod) !== -1)
	{
		$("#paczkomatyinpost").hide();
		$("#paczkomatyinpost_cod").appendTo(target);
		if (animate === true)
			$("#paczkomatyinpost_cod").fadeIn();
		else
			$("#paczkomatyinpost_cod").show();
	} else {
		if (animate === true)
			$("#paczkomatyinpost, #paczkomatyinpost_cod").fadeOut();
		else
			$("#paczkomatyinpost, #paczkomatyinpost_cod").hide();

	}
}
var bind_cgv = function (e) {
	id = parseInt($("input.delivery_option_radio:checked").val());
	if (paczkomatyinpost.opc_enabled === 1)
		id = parseInt(desintifier($("#carrierTable input[name='id_carrier']:checked").val().toString()));
	if ($.inArray(id, paczkomatyinpost.carrier) != -1)
		if (paczkomatyinpost.machine.length != 6) {
			alert("Proszę wybrać paczkomat");
			return false;
		}
	if ($.inArray(id, paczkomatyinpost.carrier_cod) != -1)
		if (paczkomatyinpost.machine_cod.length != 6) {
			alert("Prosze wybrać paczkomat dla paczki za pobraniem");
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
		paczkomatyinpost.machine = name;
	} else {
		updateMachine(name, true);
		paczkomatyinpost.machine_cod = name;
	}
	$("#paczkomatyinpost_selected" + openIndex).html('Wybrany Paczkomat: ' + objMachine.name + '<br/>' + objMachine.postCode + ' ' + objMachine.town + ', ' + objMachine.street + ' ' + objMachine.buildingNumber + ' (zmień)');
}
function updateMachine(machine, cod) {
	var data = 'updateMachine=true';
	if (cod === true)
		data = 'updateMachineCod=true';
	$.ajax({
		url: paczkomatyinpost.module_dir + 'ajax.php',
		type: 'POST',
		data: 'ajax=true&machine=' + machine + '&' + data
	});
}