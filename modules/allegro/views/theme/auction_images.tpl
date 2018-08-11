{* Default values *}
{if !isset($type)}{assign var="type" value="home_default"}{/if}

{foreach from=$allegro_images item=image}
	{if isset($type_link)}<a class="at-image-link" href="{$allegro_img_url}{$image.id_allegro_image|intval}-{$type}.jpg">{/if}
    <img class="at-image" src="{$allegro_img_url}{$image.id_allegro_image|intval}-{$type}.jpg" alt="{$product->name|escape:'html':'UTF-8'}" />
	{if isset($type_link)}</a>{/if}
{/foreach}
