{if count($form_fields)}
	{foreach from=$form_fields item=field}
		{include file='./../form/field.tpl' field=$field}
	{/foreach}
{/if}