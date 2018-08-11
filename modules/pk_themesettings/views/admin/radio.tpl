{block name='section'}
<div class="form-group form">
	<div class="col-lg-4">
		<span class="switch prestashop-switch fixed-width-lg">
			{foreach from=$section.options item=title key=key}
				<input type="radio" name="{$section.name}" id="{$section.name}_{$key}" value="{$key}" {if $section.current == $key}checked="checked"{/if}>
				<label for="{$section.name}_{$key}">{$title}</label>
			{/foreach}
			<a class="slide-button btn"></a>
		</span>
	</div>
	<label class="control-label col-lg-8 text-left">{$section.label}</label>
</div>
{/block}