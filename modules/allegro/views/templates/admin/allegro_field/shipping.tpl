<form action="" method="post" class="panel form-horizontal shipping">
	<input type="hidden" name="id_allegro_shipping" value="{if isset($allegro_shipping.id_allegro_shipping)}{$allegro_shipping.id_allegro_shipping}{/if}" />
	<div class="panel-heading">
		{l s='Shipping pricing' mod='allegro'}
	</div>

	<div class="form-group">
		<label for="" class="control-label col-lg-3 required">{l s='Pricing name' mod='allegro'}</label>
		<div class="col-lg-4">
			<input type="text" name="name" value="{if isset($allegro_shipping.name)}{$allegro_shipping.name|escape:'html':'UTF-8'}{elseif isset($smarty.post.name)}{$smarty.post.name|escape:'html':'UTF-8'}{/if}" maxlength="32" />
		</div>
	</div>

	<div class="form-group">
		<label for="default" class="control-label col-lg-3">{l s='Is default' mod='allegro'}</label>
		<div class="col-lg-4">
			<input type="checkbox" id="default" name="default" style="margin-top: 10px;" value="1" {if isset($allegro_shipping.default) && $allegro_shipping.default}checked="checked"{/if} />
		</div>
	</div>

	<hr />

	<div class="alert alert-warning">
		{l s='Some of shipping options like "Allegro Inpost" may require a fixed price.' mod='allegro'}
	</div>

	{if $flat_form}
		{include file='./../form/fields.tpl'}
	{else}
		<div class="form-group allegro-head">
			<div class="col-lg-3"></div>
			<div class="col-lg-1"><small>{l s='First item' mod='allegro'}</small></div>
			<div class="col-lg-1"><small>{l s='Next item' mod='allegro'}</small></div>
			<div class="col-lg-1"><small>{l s='Nb. in pack' mod='allegro'} (min. 1)</small></div>
		</div>
		{for $index=36 to $max_s_fid-200}
            {if isset($form_fields[$index])}
        		{if isset($form_fields_values[$index].value)}
        			{$first_item = $form_fields_values[$index].value|floatval}
        		{else}
        			{$first_item = null}
        		{/if}
        		{if isset($form_fields_values[$index+100].value)}
        			{$next_item = $form_fields_values[$index+100].value|floatval}
        		{else}
        			{$next_item = null}
        		{/if}
        		{if isset($form_fields_values[$index+200].value)}
        			{$nb_pack = $form_fields_values[$index+200].value|intval}
        		{else}
        			{$nb_pack = null}
        		{/if}

    		<div class="form-group">
    			<label class="control-label col-lg-3" for="field_{$form_fields[$index].id_field|intval}">
    				{$form_fields[$index].title|escape:'html':'UTF-8'|regex_replace:"/\s\(.+\)/":""}&nbsp;&nbsp;
    				<input id="field_{$form_fields[$index].id_field|intval}" class="shipping_switch" type="checkbox" {if $first_item !== null}checked="checked"{/if}>
    			</label>
    			<div class="col-lg-1">
    				{if isset($form_fields[$index])}
    					<input type="text" name="field[{$index}]" value="{$first_item}" title="{$form_fields[$index].title|escape:'html':'UTF-8'}" type="text" {if $first_item == null}disabled="disabled"{/if}>
    				{/if}
    			</div>
    			<div class="col-lg-1">
    				{if isset($form_fields[$index+100])}
    					<input name="field[{$index+100}]" value="{$next_item}" title="{$form_fields[$index+100].title|escape:'html':'UTF-8'}" type="text" {if $first_item == null}disabled="disabled"{/if}>
    				{/if}
    			</div>
    			<div class="col-lg-1">
    				{if isset($form_fields[$index+200])}
    					<input name="field[{$index+200}]" value="{$nb_pack}" title="{$form_fields[$index+200].title|escape:'html':'UTF-8'}" type="text" {if $first_item == null}disabled="disabled"{/if}>
    				{/if}
    			</div>
    		</div>
            {/if}
		{/for}

		<script>
		$('input.shipping_switch').change(function(){
			var text_inputs = $(this).closest('.form-group').find('input[type="text"]');
			text_inputs.attr('disabled', !this.checked);
			if (!this.checked) {
				text_inputs.val('');
			}
		});
		</script>
	{/if}

	{if isset($form_fields_global) && count($form_fields_global)}
		<hr />
		{include file='./../form/fields.tpl' form_fields=$form_fields_global}
	{/if}

	<!-- Footer -->
	<div class="panel-footer">
		<a href="{$currentIndex}&token={$smarty.get.token}" class="btn btn-default" onclick="window.history.back();"><i class="process-icon-cancel"></i> {l s='Cancel' mod='allegro'}</a>
		<button type="submit" name="submitSaveShipping" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='allegro'}</button>
	</div>
</form>
