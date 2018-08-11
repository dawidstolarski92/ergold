{block name='section'}
{foreach from=$section key=hook item=modules}
	<div class='hook-section hook-{$hook} hook-{$hook}'>
		<div class='hook-name'>Hook: {$hook}</div>
		<div class='sortable'>
			{assign var=counter value=1}
			{foreach from=$modules.modules key=module_id item=module}

				<div draggable='true' id='{$module.id_hook}_{$module.id_module}' data-modid='{$module.id_module}' data-hookid='{$module.id_hook}' data-modnum='{$counter}' class='draggable module-section variant switch module-{$module.name} num-{$counter}{if $module.current.1 == 0} mod-disabled{/if}'>

					<div class='counter'>{$counter}.</div><img class='module-icon' src='../modules/{$module.name}/logo.png' alt="" />
					<div class='module-name dragHandle' title='Drag and Drop to change order'>{$module.name}</div>		

					{if $module.active == 0}
					<div class='not-active module-state' title='This module is disabled. Click to "Show" button to enable it'></div>
					{/if}
							
					<input type='hidden' name='ordr[{$hook}][{$module.name}]' value='{$counter}' /><input type='radio' name='modules[{$hook}][{$module.name}]' id='{$hook}_{$module.name}-on' value='1' {if $module.current.1 == 1}checked{/if}/>
					<input type='radio' name='modules[{$hook}][{$module.name}]' id='{$hook}_{$module.name}-off' value='0' {if $module.current.1 == 0}checked{/if}/>

					{if in_array($module.name, $pk_modules)}
					<label for='{$hook}_{$module.name}-on' class='cb-enable{if $module.current.1 == 1} sel{/if}'><span>Show</span></label>
					<label for='{$hook}_{$module.name}-off' class='cb-disable{if $module.current.1 == 0} sel{/if}'><span>Hide</span></label>
					{/if}

					<a target='_blank' class='btn btn-default' href='index.php?controller=AdminModules&amp;configure={$module.name}&amp;token={$token}' title="Go to Module Settings"><svg class="svgic svgic-edit"><use xlink:href="#si-edit"></use></svg></a>
							
				</div>
				{assign var=counter value=$counter+1}
			{/foreach}
		</div>
	</div>
{/foreach}
{/block}