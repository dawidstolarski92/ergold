{if isset($features) && count($features)}
	<ul>
	{foreach from=$features item=f key=key}
		<li>{$f.name|escape:'html':'UTF-8'} - <b>{$f.value|escape:'html':'UTF-8'}</b></li>
	{/foreach}
	</ul>
{/if}
