<ul>
{foreach desde=$virtualProductos item=product}
	<li>
		<a href="{$product.link|escape:'html'}">{$product.name}</a>
		{if isset($product.deadline)}
			expira en {$product.deadline}
		{/if}
		{if isset($product.downloadable)}
			descargable {$product.downloadable} vez(es)
		{/if}
	</li>
{/foreach}
</ul>