{**
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
 *}

<br/>
<style>
	.hide {
		display:none;
	}
	.paczkomatyinpost_price {
		font-weight: bold;
		font-style: normal;
		font-size: 24px;
		color: #414141;
	}
	.paczkomatyinpost_packcode {
		font-weight: bold;
		font-style: normal;
		font-size: 16px;
		color: #414141;
	}
	.paczkomatyinpost_status{
		font-weight: bold;
		font-style: normal;
		font-size: 14px;
		color: #414141;
	}
</style>
<script type="text/javascript">
	{*var insurances = {$insurances|escape:'quotes':'UTF-8'};
	var packtypes = {$packtypes|escape:'quotes':'UTF-8'};
	var pricecod = {$pricelistcod|escape:'quotes':'UTF-8'};
	
	function countPackPrice() {
		type = $('#PACZKOMATYINPOST_PACKTYPE').val();
		insurance = $("#PACZKOMATYINPOST_INSURANCE").val();
		is_cod = $("#PACZKOMATYINPOST_COD_on").is(':checked');
		cod_value = parseFloat($("#PACZKOMATYINPOST_COD_VALUE").val());
		if(isNaN(cod_value)) cod_value = 0;
		price = packtypes[type] + insurances[insurance];
		if (is_cod) {
			price = (price + (cod_value * (pricecod.on_delivery_percentage / 100)));
		}
		price = Math.round(price * 100) / 100;
		$("#PACZKOMATYINPOST_CALCULATED_PRICE").val(price);
		$(".paczkomatyinpost_price").text(price + ' zł');
	}*}

	function checkFields() {
		selfSend = $("#PACZKOMATYINPOST_SELF_SEND_paczkomat").is(':checked');
		cod = $("#PACZKOMATYINPOST_COD_on").is(':checked');

		if (!selfSend)
		{
			$("#PACZKOMATYINPOST_SENDER_MACHINE").parents(".margin-form, .form-group").fadeOut();
			$("#PACZKOMATYINPOST_SENDER_MACHINE").parents(".margin-form, .form-group").prev('label').fadeOut();
		} else {
			$("#PACZKOMATYINPOST_SENDER_MACHINE").parents(".margin-form, .form-group").fadeIn();
			$("#PACZKOMATYINPOST_SENDER_MACHINE").parents(".margin-form, .form-group").prev('label').fadeIn();
		}

		if (!cod)
		{
			$("#PACZKOMATYINPOST_COD_VALUE").parents(".margin-form, .form-group").fadeOut();
			$("#PACZKOMATYINPOST_COD_VALUE").parents(".margin-form, .form-group").prev('label').fadeOut();
		} else {
			$("#PACZKOMATYINPOST_COD_VALUE").parents(".margin-form, .form-group").fadeIn();
			$("#PACZKOMATYINPOST_COD_VALUE").parents(".margin-form, .form-group").prev('label').fadeIn();
		}

	}

	$(document).ready(function () {
		{*$("#PACZKOMATYINPOST_PACKTYPE, #PACZKOMATYINPOST_INSURANCE, #PACZKOMATYINPOST_COD_VALUE, input[name='PACZKOMATYINPOST_COD']").on('change', function () {
			countPackPrice();
		});*}

		$("input[name='PACZKOMATYINPOST_SELF_SEND'], input[name='PACZKOMATYINPOST_COD']").on('change', function () {
			checkFields();
		});
{*		countPackPrice();*}
		checkFields();
		
		$(".paczkomatyinpost_form").insertBefore('#formAddPaymentPanel');
	});
</script>