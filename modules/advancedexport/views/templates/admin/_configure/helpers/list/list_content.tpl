{*
*  @author Marcin Kubiak
*  @copyright  Smart Soft
*  @license    Commercial license
*  International Registered Trademark & Property of Smart Soft
*}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
    {if isset($tr.$key)}
        {if isset($params.type) && $params.type == 'html'}
            <span class='cron_url'>
                {$tr.$key|escape:'html':'UTF-8'}
            </span>
            <span data-clipboard-text='{$tr.$key|escape:'html':'UTF-8'}' class='cron_button'>
                {if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
                    <i class='icon-copy'></i>
	            {else}
		            <i class='icon-copy'>Copy</i>
                {/if}
            </span>
        {else}
            {$smarty.block.parent}
        {/if}        
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
