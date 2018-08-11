{if !isset($description)}{$description = ''}{/if}
{if !isset($placeholder)}{$placeholder = ''}{/if}

{* Input name *}
{assign var="input_name" value="field[`$field.id_field`]"}

{$scope = null}
{$value = null}
{$field_value = null}
{$parent_value = null}
{$af_value = null}

{if isset($form_fields_values[$field.id_field].parent_value)}
	{$parent_value = $form_fields_values[$field.id_field].parent_value}
	{$value = $parent_value}
	{$scope = $form_fields_values[$field.id_field].scope}
{/if}

{if isset($form_fields_values[$field.id_field].value)}
	{$field_value = $form_fields_values[$field.id_field].value}
	{$value = $field_value}
{/if}

{assign var="disabled" value=($parent_value && $field_value === null)}
{assign var="max_checkbox_in_row" value=12}

<div class="form-group attribs">
	<label class="control-label col-lg-3{if $field.required} required allegro{/if}">{$field.title|escape:'html':'UTF-8'}</label>
	<div class="col-lg-{if $field.type == 6 && count($field.options) > $max_checkbox_in_row}6{elseif $field.type == 8}6{else}3{/if}">
	{* Input *}
	{if $field.type == 1 || $field.type == 2 || $field.type == 3}
		<input {if $field.type == 3}type="number" step="0.01"{else if $field.type == 2}type="number" step="1"{else}type="text"{/if}
			class="form-control"
			data-parent-value="{$parent_value}"
			value="{$value}"
			{if $disabled}disabled="disabled"{/if}
			{if $placeholder}placeholder="{$placeholder|escape:'html':'UTF-8'}"{/if}
			name="{$input_name}" />
	{* Select *}
	{elseif $field.type == 4}
		<select name="{$input_name}" {if $disabled}disabled="disabled"{/if} data-parent-value="{$parent_value}">
			{foreach from=$field.options item=name key=v}
			<option value="{$v}" {if $value == $v || (!$value && $af_value == $name|strtolower)}selected="selected"{/if}>{$name|escape:'html':'UTF-8'}{if !$value && $af_value == $name|strtolower} (*){/if}</option>
			{/foreach}
		</select>
	{* Checkbox/radio (AND|OR) *}
	{elseif $field.type == 5 || $field.type == 6}
		{* Empty select *}
		<input type="hidden" name="{$input_name}" value="" />
		<div class="divider">
		{foreach from=$field.options item=name key=v name=options}
			<div>
				<input
					type="{if $field.type == 5}radio{else}checkbox{/if}"
					name="{$input_name}{if $field.type != 5}[]{/if}"
					value="{$v|intval}"
					class="variant-radio"
					id="fid_{$field.id_field|intval}_{$v|intval}"
					{if $field.type == 5}{* Radio *}
						{if $value == $v}checked="checked"{/if}
					{else}
						{if $value & $v}checked="checked"{/if}
					{/if}
					{if $disabled}disabled="disabled"{/if}
					 />
				<label class="control-label" for="fid_{$field.id_field}_{$v}" style="min-width: 50px;">{$name|escape:'html':'UTF-8'}</label>

				{if isset($variant_field_ids) && in_array($field.id_field|intval, $variant_field_ids)}
					{assign var="qty" value=''}
					{if isset($field_quantity) && isset($field_quantity[$field.id_field]) && isset($field_quantity[$field.id_field][$value])}
						{assign var="qty" value=$field_quantity[$field.id_field][$value]}
					{/if}

					<input type="number" step="1" name="field_quantity[{$field.id_field|intval}][{$value|intval}]" value="{$qty}" style="width: 50px;" />
				{/if}
			</div>
		{if count($field.options) > $max_checkbox_in_row && ($smarty.foreach.options.index+1) % $max_checkbox_in_row === 0}</div><div class="divider">{/if}
		{/foreach}
		</div>
	{* Textarea *}
	{elseif $field.type == 8}
		<textarea name="{$input_name}" cols="30" rows="6" {if $disabled}disabled="disabled"{/if} data-parent-value="{$parent_value}">{$field_value}</textarea>
	{/if}
		{if $field.description || $description}
			<p class="help-block">{if $description}{$description|escape:'UTF-8'}{else}{$field.description|escape:'UTF-8'}{/if}</p>
		{/if}
	</div>
	<div class="col-lg-2 override-box">
	{* Override *}
	{if $parent_value}
		<input type="checkbox" class="override-switch" id="override_{$field.id_field|intval}" {if !$disabled}checked="checked"{/if} />
		<label for="override_{$field.id_field|intval}" title="xxx">
			{l s='Override' mod='allegro'}
			{if $scope == 1}
				({l s='global params' mod='allegro'})
			{elseif $scope == 2}
				({l s='category' mod='allegro'})
			{elseif $scope == 4}
				({l s='shipping' mod='allegro'})
			{/if}
		</label>
	{/if}
	</div>
</div>
