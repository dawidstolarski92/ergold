{block name='section'}
<div class="preset-info">
	<input type="submit" name="savePreset" value="{l s='Save Current Configuration' d='Modules.ThemeSettings.Shop'}" class="button" />
	<a class="button" href="{$path}/presets/{$section.current}.json" target="_blank">Download Current Preset</a>
	<input type="button" name="importPreset" value="{l s='Import New Preset' d='Modules.ThemeSettings.Shop'}" class="button import-preset" />
</div>
<div class="preset-import" style="display:none">
	<textarea name="preset_to_import" placeholder="Paste your config here. Should be valid JSON"></textarea>
	<input type="submit" name="savePresetToFile"  value="{l s='Add Preset' d='Modules.ThemeSettings.Shop'}" class="button">
</div>
<div class="preset-list{if isset($section.class)} {$section.class}{/if}">
	{foreach from=$section.options item=title key=key}
		<label for="{$key}" class="preset{if $section.current == $key} active{/if}">
			{if $key != 'alysum' && $key != 'complex' && $key != 'classic' && $key != 'oldstyle' && $key != 'fullpage' && $key != 'electronics'}
			<input type="submit" class="remove-preset" name="removePreset" title="Remove Preset" value="{$key}" />
			{/if}
			<input class="hidden" type="radio" name="preset" value="{$key}" id="{$key}"{if $section.current == $key} checked="checked"{/if}>
			{assign var="preview" value="{$imgs}presets/{$key}.jpg"}
			{if !file_exists($preview)}{/if}
			<img src="{$preview}" width="100" height="150" alt="">
			<div>{$title}</div>
		</label>
	{/foreach}
</div>
{/block}