{if $product->weight}
    {if $product->weight < 1}
    {$product->weight*100|floatval}gr
    {else}
    {$product->weight|floatval}kg
    {/if}
{/if}
