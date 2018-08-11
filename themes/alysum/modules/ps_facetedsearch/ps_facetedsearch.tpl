{if isset($listing.rendered_facets)}
<div id="search_filters_wrapper">
	{block name='product_list_active_filters'}
		{$listing.rendered_active_filters nofilter}
	{/block}
	{$listing.rendered_facets nofilter}
</div>
{/if}