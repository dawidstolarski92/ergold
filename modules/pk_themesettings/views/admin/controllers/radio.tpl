{block name='section'}
<div class="form-group form{if isset($section.class)} {$section.class}{/if}">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="col-lg-8 control-pilot">
		<span class="switch prestashop-switch fixed-width-lg">
			{foreach from=$section.options item=title key=key}
				<input type="radio" name="{$section.name}" id="{$section.name}_{$key}" value="{$key}" {if $section.current == $key}checked="checked"{/if}>
				<label for="{$section.name}_{$key}">{$title}</label>
			{/foreach}
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>
{/block}