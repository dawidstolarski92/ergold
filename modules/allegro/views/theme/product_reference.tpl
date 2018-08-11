{if $product_combination && $product_combination->reference}
	{$product_combination->reference|escape:'html':'UTF-8'}
{else}
	{$product->reference|escape:'html':'UTF-8'}
{/if}
