{block name='section'}
<div class="form-group form {if ($section.options != '')}available-update{/if}">
	<label class="control-label col-lg-4 text-left">{$section.label}</label>
	<div class="col-lg-8 control-pilot">
		{if ($section.options != '')}
		<span class="like-label">Update available v.{$section.options}</span> <input type="button" name="theme_update" id="theme_update" value="Run Theme Update"><svg class="svgic svgic-ld" style="width:26px;height:26px;opacity:0.5;display:none;margin:2px 0 2px 10px;animation: rotation 1s ease-in-out infinite;"><use xlink:href="#si-loading"></use></svg>
		<input type="hidden" name="versions" value="{$section.options}">
		{else}
			<span class="like-label">No available updates right now</span>
		{/if}
	</div>
</div>
{/block}