{if $product_combination && $product_combination->ean13}
	{$product_combination->ean13|escape:'html':'UTF-8'}
{else if $product->ean13}
	{$product->ean13|escape:'html':'UTF-8'}
{/if}
