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

<div id="paczkomatyinpost" style="display:none;" class="paczkomatyinpost_container">
	<div class="left"><img src="{$paczkomaty_logo}" class="img-responsive"/></div>
	<div class='right'>
		<div style='max-width:500px; margin-bottom:10px;' class='selector2'>
			<label for="paczkomatyinpost_selected">{l s='Wybrany paczkomat:' mod='paczkomatyinpost'}</label>
			<select id="paczkomatyinpost_selected" class='paczkomat-select' >
				{capture name="paczkomaty_list"}
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
				{/capture}
				{$smarty.capture.paczkomaty_list}
			</select>
		</div>
		<a onclick="openMap('&marker_icon1={$marker_1|escape:'quotes':'UTF-8'}&marker_icon2={$marker_2|escape:'quotes':'UTF-8'}');
				return false;">{l s='Wybierz z mapy' mod='paczkomatyinpost'}</a>
	</div>
</div>
<div id="paczkomatyinpost_cod" style="display:none;" class="paczkomatyinpost_container">
	<div class="left"><img src="{$paczkomaty_logo}" class="img-responsive"/></div>
	<div class='right'>
		<div style='max-width:500px; margin-bottom:10px;' class='selector2'>
			<label for="paczkomatyinpost_selected_cod">{l s='Wybrany paczkomat za pobraniem:' mod='paczkomatyinpost'}</label>
			<select id="paczkomatyinpost_selected_cod" class='paczkomat-select cod' >
				<option value='-'>{l s='Nie wybrano' mod='paczkomatyinpost'}</option>
					{if isset($nearest_machines_cod) && !empty($nearest_machines_cod)}
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