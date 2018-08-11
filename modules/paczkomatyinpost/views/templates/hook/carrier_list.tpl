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


<!-- {$module_name|escape:'htmlall':'UTF-8'} {$module_version|escape:'htmlall':'UTF-8'} -->
{if $ps17}
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
{/if}
<script type="text/javascript">
	var paczkomatyinpost = {
		module_dir: '{$module_dir|escape:'quotes':'UTF-8'}',
		module_version: '{$module_version|escape:'quotes':'UTF-8'}',
		opc_enabled: {$opc_enabled|escape:'quotes':'UTF-8'},
		carrier: [{$carrier|escape:'quotes':'UTF-8'}],
		carrier_cod: [{$carrier_cod|escape:'quotes':'UTF-8'}],
		machine: '{if isset($receiver_machine)}{$receiver_machine|escape:'quotes':'UTF-8'}{/if}',
		machine_cod: '{if isset($receiver_machine_cod)}{$receiver_machine_cod|escape:'quotes':'UTF-8'}{/if}',
		{* option_radio: {if $ps17}".delivery-option input[type=radio]"{else}{if $opc_enabled == 1}"#carrierTable input[name='id_carrier']"{else}".delivery_option_radio"{/if}{/if} *}
		option_radio: {if $ps17}"#shipping-method input[type=radio]"{else}{if $opc_enabled == 1}"#carrierTable input[name='id_carrier']"{else}".delivery_option_radio"{/if}{/if}
	};

	$(document).ready(function () {

		$("#cgv").unbind('click', bind_cgv).bind('click', bind_cgv);
	{if $ps17}
		var target = $("#js-delivery .order-options").first();
		$("#hook-display-before-carrier #paczkomatyinpost").appendTo(target);
		$("#hook-display-before-carrier #paczkomatyinpost_cod").appendTo(target);
		$(paczkomatyinpost.option_radio).on('change', bind_carrier_radio);
		$('button[name=confirmDeliveryOption]').click(bind_cgv);

	{elseif $ps16}
		$(paczkomatyinpost.option_radio).off('click', paczkomatyinpost.option_radio, bind_carrier_radio).on('click', paczkomatyinpost.option_radio, bind_carrier_radio);

	{else}
		$(paczkomatyinpost.option_radio).off('click', bind_carrier_radio).on('click', bind_carrier_radio);
	{/if}
		initPaczkomatySelect($(paczkomatyinpost.option_radio + ":checked"), false);
		$(".paczkomat-select").change(function () {
			updateMachine($(this).val(), $(this).hasClass('cod'));
		});

		$("#paczkomatyinpost_selected").val(paczkomatyinpost.machine.length ? paczkomatyinpost.machine : '-');
		$("#paczkomatyinpost_selected_cod").val(paczkomatyinpost.machine_cod.length ? paczkomatyinpost.machine_cod : '-');
	});
</script>

{if $ps17}

	<div id="paczkomatyinpost" style="display:none;" class="paczkomatyinpost_container">
		<div class="left"><img src="{$paczkomaty_logo}" class="img-responsive"/></div>
		<div class='right'>
			<div style='max-width:200px; margin-bottom:10px;' class='selector2'>
				<label for="paczkomatyinpost_selected">{l s='Wybrany paczkomat:' mod='paczkomatyinpost'}</label>
				<select id="paczkomatyinpost_selected" class='paczkomat-select' style='max-width:100%'>
					<option value='-'>{l s='Nie wybrano' mod='paczkomatyinpost'}</option>
					{if isset($nearest_machines) && !empty($nearest_machines)}
						<optgroup label='{l s='W pobliżu'  mod='paczkomatyinpost'}'>
							{foreach $nearest_machines as $machine}
								<option value='{$machine.name}'>{$machine.label}</option>
							{/foreach}
						</optgroup>
					{/if}
					<optgroup label='{l s='Wszystkie' mod='paczkomatyinpost'}'>
						{foreach $list_machines as $machine}
							<option value='{$machine.name}'>{$machine.label}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
			<a onclick="openMap('&marker_icon1={$marker_1|escape:'quotes':'UTF-8'}&marker_icon2={$marker_2|escape:'quotes':'UTF-8'}');
					return false;">{l s='Wybierz z mapy' mod='paczkomatyinpost'}</a>
		</div>
	</div>
	<div id="paczkomatyinpost_cod" style="display:none;" class="paczkomatyinpost_container">
		<div class="left"><img src="{$paczkomaty_logo}" class="img-responsive"/></div>
		<div class='right'>
			<div style='max-width:200px; margin-bottom:10px;' class='selector2'>
				<label for="paczkomatyinpost_selected_cod">{l s='Wybrany paczkomat za pobraniem:' mod='paczkomatyinpost'}</label>
				<select id="paczkomatyinpost_selected_cod" class='paczkomat-select cod' style='max-width:100%'>
					<option value='-'>{l s='Nie wybrano' mod='paczkomatyinpost'}</option>
					{if isset($nearest_machines) && !empty($nearest_machines)}
						<optgroup label='{l s='W pobliżu'  mod='paczkomatyinpost'}'>
							{foreach $nearest_machines_cod as $machine}
								<option value='{$machine.name}'>{$machine.label}</option>
							{/foreach}
						</optgroup>
					{/if}
					<optgroup label='{l s='Wszystkie' mod='paczkomatyinpost'}'>
						{foreach $list_machines_cod as $machine}
							<option value='{$machine.name}'>{$machine.label}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
			<a onclick="openMapCod('_cod&marker_icon1={$marker_1|escape:'quotes':'UTF-8'}&marker_icon2={$marker_2|escape:'quotes':'UTF-8'}');
					return false;">{l s='Wybierz z mapy' mod='paczkomatyinpost'}</a>
		</div>
	</div>

{/if}