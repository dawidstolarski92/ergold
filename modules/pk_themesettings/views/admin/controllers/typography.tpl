{block name='section'}
<div class="form-group form typography">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="col-lg-8 control-pilot">
		<div class="option-set">
			<label>Font Size</label>
			<input name="{$section.name}[font_size]" value="{$section.current.font_size}" />
			<span class="addon">px</px>
		</div>
		<div class="option-set">
			<label>Line Height</label>
			<input name="{$section.name}[line_height]" value="{$section.current.line_height}" />	
			<span class="addon">em</px>
		</div>
		<div class="option-set">
			<label>Letter Spacing</label>
			<input name="{$section.name}[letter_spacing]" value="{$section.current.letter_spacing}" />	
			<span class="addon">em</px>
		</div>
		<div class="option-set colorPickerControl relative">
			<label>Text Color</label>
			<input name="{$section.name}[color]" value="{$section.current.color}" type="color" data-hex="true" />
		</div>
		<div class="option-set">
			<label>Font Style</label>
			<select name="{$section.name}[font_style]">
				{foreach from=$section.options.font_style item=title key=key}
					<option value="{$key}"{if $key == $section.current.font_style} selected{/if}>{$title}</value>
				{/foreach}
			</select>
		</div>
		<div class="option-set">
			<label>Font Weight</label>
			<select name="{$section.name}[font_weight]">
				{foreach from=$section.options.font_weight item=title key=key}
					<option value="{$key}"{if $key == $section.current.font_weight} selected{/if}>{$title}</value>
				{/foreach}
			</select>
		</div>
		<div class="option-set">
			<label>Font Family</label>
			<select name="{$section.name}[font_family]">
				{foreach from=$section.options.font_family item=title key=key}
					<option value="{$key}"{if $key == $section.current.font_family} selected{/if}>{$title}</value>
				{/foreach}
			</select>
		</div>
		<div class="option-set">
			<label>Text Transform</label>
			<select name="{$section.name}[text_transform]">
				{foreach from=$section.options.text_transform item=title key=key}
					<option value="{$key}"{if $key == $section.current.text_transform} selected{/if}>{$title}</value>
				{/foreach}
			</select>
		</div>
	</div>
</div>
{/block}