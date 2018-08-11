{block name='section'}
<div class="form-group form">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="col-lg-8 control-pilot">
		<input name="{$section.name}" id="{$section.name}" value="{$section.current}" type="{$section.input_type}" />
	</div>
</div>
{/block}