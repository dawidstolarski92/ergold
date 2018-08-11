{block name='section'}
<div class="form-group form colorPickerControl">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="control-pilot col-lg-8">
		<input name="{$section.name}" id="{$section.name}" value="{$section.current}" type="{$section.input_type}" data-hex="true" />
	</div>
</div>
{/block}