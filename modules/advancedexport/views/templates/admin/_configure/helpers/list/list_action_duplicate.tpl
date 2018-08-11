{*
*  @author Marcin Kubiak
*  @copyright  Smart Soft
*  @license    Commercial license
*  International Registered Trademark & Property of Smart Soft
*}


<a class="pointer export" title="Export" href="{$location_ok|escape:'htmlall':'UTF-8'}">
	{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
		<i class="icon-share-square"></i><span class="exportspan"> Export</span>
	{else}
		<img src="../img/admin/export.gif" alt="Export" /><span class="exportspan">Export</span>
	{/if}
</a>
