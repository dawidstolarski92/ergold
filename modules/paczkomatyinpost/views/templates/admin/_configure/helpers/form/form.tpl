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

{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.type == 'buttons'}
        <div class="margin-form">
            {foreach $input.list AS $button}
                <input type="submit"
                       id="{if isset($button.id)}{$button.id|escape:'htmlall':'UTF-8'}{else}{/if}"
                       value="{$button.title|escape:'htmlall':'UTF-8'}"
                       name="{if isset($button.name)}{$button.name|escape:'htmlall':'UTF-8'}{else}{$submit_action|escape:'htmlall':'UTF-8'}{/if}{if isset($button.stay) && $button.stay}AndStay{/if}"
                       class="{if isset($button.class)}{$button.class|escape:'htmlall':'UTF-8'}{/if}" /> 
            {/foreach}
        </div>
	{elseif $input.type == 'price'}
        <div class="margin-form{if isset($input.hide) && $input.hide == true} hide{/if}">
            {if isset($input.value)}
				{if $input.paid == true}Zapłacono: {else}Do zapłaty: {/if}<span class="paczkomatyinpost_price">{$input.value|escape:'htmlall':'UTF-8'}</span> (brutto)
			{/if}
        </div>
	{elseif $input.type == 'summary'}
        <div class="margin-form col-lg-9{if isset($input.hide) && $input.hide == true} hide{/if}">
            {if isset($input.packcode)}Numer paczki: <span class="paczkomatyinpost_packcode">{$input.packcode|escape:'htmlall':'UTF-8'}</span><br/>{/if}
			{if isset($input.delivery_code)}Kod nadania w paczkomacie: <span class="paczkomatyinpost_delivery_code">{$input.delivery_code|escape:'htmlall':'UTF-8'}</span><br/>{/if}
			{if isset($input.status)}Aktualny status: <span class="paczkomatyinpost_status">{$input.status|escape:'htmlall':'UTF-8'}</span><br/>{/if}
			{if isset($input.status_date)}Data zmiany statusu: <span class="paczkomatyinpost_status_date">{$input.status_date|escape:'htmlall':'UTF-8'}</span>{/if}
        </div>
	{elseif $input.type == 'select'}
		<div class="margin-form">
			{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
				{$input.empty_message|escape:'htmlall':'UTF-8'}
				{$input.required = false}
				{$input.desc = null}
			{else}
				<select name="{$input.name|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
						id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
						{if isset($input.multiple)}multiple="multiple" {/if}
						{if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
						{if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}
				{if isset($input.extra)}{$input.extra|escape:'htmlall':'UTF-8'}{/if}>
				{if isset($input.options.default)}
					<option value="{$input.options.default.value|escape:'htmlall':'UTF-8'}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
				{/if}
				{if isset($input.options.optiongroup)}
					{foreach $input.options.optiongroup.query AS $optiongroup}
						<optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
							{foreach $optiongroup[$input.options.options.query] as $option}
								<option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
										{if isset($input.multiple)}
											{foreach $fields_value[$input.name] as $field_value}
												{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
											{/foreach}
										{else}
											{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
										{/if}
										>{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</optgroup>
					{/foreach}
				{else}
					{foreach $input.options.query AS $option}
						{if is_object($option)}
							<option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
									{if isset($input.multiple)}
										{foreach $fields_value[$input.name] as $field_value}
											{if $field_value == $option->$input.options.id}
												selected="selected"
											{/if}
										{/foreach}
									{else}
										{if $fields_value[$input.name] == $option->$input.options.id}
											selected="selected"
										{/if}
									{/if}
									>{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
						{elseif $option == "-"}
							<option value="">-</option>
						{else}
							<option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
									{if isset($input.multiple)}
										{foreach $fields_value[$input.name] as $field_value}
											{if $field_value == $option[$input.options.id]}
												selected="selected"
											{/if}
										{/foreach}
									{else}
										{if $fields_value[$input.name] == $option[$input.options.id]}
											selected="selected"
										{/if}
									{/if}
									>{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

						{/if}
					{/foreach}
				{/if}
			</select>
			{if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint|escape:'htmlall':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
		{/if}
	</div>
{elseif $input.type == 'select16'}
	<div class="col-lg-9">
		{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
			{$input.empty_message|escape:'htmlall':'UTF-8'}
			{$input.required = false}
			{$input.desc = null}
		{else}
			<select name="{$input.name|escape:'htmlall':'UTF-8'}"
					class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
					id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
					{if isset($input.multiple)}multiple="multiple" {/if}
					{if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
					{if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}
					{if isset($input.extra)}{$input.extra|escape:'htmlall':'UTF-8'}{/if}>
				{if isset($input.options.default)}
					<option value="{$input.options.default.value|escape:'htmlall':'UTF-8'}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
				{/if}
				{if isset($input.options.optiongroup)}
					{foreach $input.options.optiongroup.query AS $optiongroup}
						<optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
							{foreach $optiongroup[$input.options.options.query] as $option}
								<option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
										{if isset($input.multiple)}
											{foreach $fields_value[$input.name] as $field_value}
												{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
											{/foreach}
										{else}
											{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
										{/if}
										>{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</optgroup>
					{/foreach}
				{else}
					{foreach $input.options.query AS $option}
						{if is_object($option)}
							<option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
									{if isset($input.multiple)}
										{foreach $fields_value[$input.name] as $field_value}
											{if $field_value == $option->$input.options.id}
												selected="selected"
											{/if}
										{/foreach}
									{else}
										{if $fields_value[$input.name] == $option->$input.options.id}
											selected="selected"
										{/if}
									{/if}
									>{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
						{elseif $option == "-"}
							<option value="">-</option>
						{else}
							<option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
									{if isset($input.multiple)}
										{foreach $fields_value[$input.name] as $field_value}
											{if $field_value == $option[$input.options.id]}
												selected="selected"
											{/if}
										{/foreach}
									{else}
										{if $fields_value[$input.name] == $option[$input.options.id]}
											selected="selected"
										{/if}
									{/if}
									>{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

						{/if}
					{/foreach}
				{/if}
			</select>
		{/if}
	</div>
{else}
	{$smarty.block.parent}
{/if}
{/block}