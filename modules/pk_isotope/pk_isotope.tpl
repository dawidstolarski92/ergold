<!-- MODULE IsotopeSort -->
<div class="pk-isotope view_grid oh isotope-col-{$isotope_col}">
	<div class="page-width">
	{if isset($products) AND $products}
        <div class="option-combo">
	      	<div class="filter option-set flex-container" data-filter-group="type"> 
		        <h5><a href="#" data-filter-value="" class="selected">{l s='All' mod='pk_isotope'}</a></h5>
		        {if $fea}
		        	<h5><a href="#" data-filter-value=".featured">{l s='Featured' mod='pk_isotope'}</a></h5>
		        {/if}
		        {if $spe}
		        	<h5><a href="#" data-filter-value=".discount">{l s='Special' mod='pk_isotope'}</a></h5>
		        {/if}
		        {if $new}
		        	<h5><a href="#" data-filter-value=".new">{l s='Latest' mod='pk_isotope'}</a></h5>
		        {/if}
		        {if $bes}
		        	<h5><a href="#" data-filter-value=".bestsellers">{l s='Bestsellers' mod='pk_isotope'}</a></h5>
		        {/if}
		        {if $categories}
		        	{foreach from=$categories item=category name=categories}
		        	<h5><a href="#" data-filter-value=".{$category.link_rewrite}">{$category.name}</a></h5>
		        	{/foreach}
		        {/if}
		    </div>
    	</div>    

    	<div class="block_content">
			<div class="isotope products-module flex-container">
            {foreach from=$products item=product key=p name=product}
            	{if $isotope_max > $smarty.foreach.product.index}
            	{include file="catalog/_partials/miniatures/product.tpl" product=$product}
            	{/if}
			{/foreach}
			</div>
		</div>    
	{else}
		<p class="alert alert-warning">{l s='There are no products right now' mod='pk_isotope'}</p>
	{/if}
	</div>
</div>
<!-- /MODULE IsotopeSort -->