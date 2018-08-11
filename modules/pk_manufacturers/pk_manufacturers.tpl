<!-- Manufacturers module -->
<div class="pk-brands" data-visible="{$text_list_nb_vis}">
	<div class="page-width relative oh">
	<h4 class="module-title"><span>{l s='Our Brands' mod='pk_manufacturers'}</span></h4>
	{if $manufacturers}
		<ul class="m-list flex-container relative">
		{foreach from=$manufacturers item=manufacturer name=manufacturer_list}
			{if $smarty.foreach.manufacturer_list.iteration <= $text_list_nb}		
			<li class="text-center">
				<div class="manuf-indent">
					<a class="oh db" href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" title="{l s='More about' mod='pk_manufacturers'} {$manufacturer.name}">
    					<img class="smooth02 db" src="{$link->getManufacturerImageLink($manufacturer.id_manufacturer)}" alt="" width="202" height="150" />
					</a>
					{if isset($show_title) AND $show_title == 1}
					<span>{$manufacturer.name}</span>
                    {/if}
				</div>
			</li>
			{/if}
		{/foreach}
		</ul>	
	{else}
		<p>{l s='No manufacturer' mod='pk_manufacturers'}</p>
	{/if}
	</div>
</div>	
<!-- /Manufacturers module -->