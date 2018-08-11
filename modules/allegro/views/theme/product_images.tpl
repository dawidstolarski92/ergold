{* Default values *}
{if !isset($type)}{assign var="type" value="home_default"}{/if}

{foreach from=$images item=image}
	{if isset($type_link)}<a class="at-image-link" href="{$link->getImageLink($product->link_rewrite, $image.id_image, $type_link)}">{/if}
    <img class="at-image" src="{$link->getImageLink($product->link_rewrite, $image.id_image, $type)}" alt="{$product->name|escape:'html':'UTF-8'}" />
	{if isset($type_link)}</a>{/if}
{/foreach}