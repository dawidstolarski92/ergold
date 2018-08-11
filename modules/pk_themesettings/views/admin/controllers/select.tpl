{block name='section'}
<div class="form-group form">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="col-lg-8 control-pilot">
		<select name="{$section.name}" id="{$section.name}">
			{foreach from=$section.options item=title key=key}
				<option value="{$key}"{if $key == $section.current} selected{/if}>{$title}</value>
			{/foreach}
		</select>
	</div>
</div>
{/block}