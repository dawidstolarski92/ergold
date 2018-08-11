{* Default values *}
{if !isset($index)}{assign var="index" value=0}{/if}
{if !isset($type)}{assign var="type" value="medium_default"}{/if}
{if !isset($type_link)}{assign var="type_link" value="large_default"}{/if}

{foreach from=$images key=key item=image}
    {if $index == $key}
        {if $image.src == 'shop'}
            {if isset($type_link)}
            <a class="at-image-link" href="{$link->getImageLink($product->link_rewrite, $image.id, $type_link)}">
            {/if}
                <img class="at-image" src="{$link->getImageLink($product->link_rewrite, $image.id, $type)}" alt="{$product->name|escape:'html':'UTF-8'}" />
        	{if isset($type_link)}
            </a>
            {/if}
        {else}
            {if isset($type_link)}
            <a class="at-image-link" href="{$allegro_img_url}{$image.id|intval}-{$type_link|escape:'html':'UTF-8'}.jpg">
            {/if}
                <img class="at-image" src="{$allegro_img_url}{$image.id|intval}-{$type|escape:'html':'UTF-8'}.jpg" alt="{$product->name|escape:'html':'UTF-8'}" />
        	{if isset($type_link)}
            </a>
            {/if}
        {/if}
    {/if}
{/foreach}
