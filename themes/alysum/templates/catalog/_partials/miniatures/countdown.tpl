{block name='product_countdown'}
  {if isset($product.embedded_attributes.specific_prices.to)}
  {assign var=to value="-"|explode:$product.embedded_attributes.specific_prices.to}
  {if $to[0] != "0000"}
    <div class="countdown flex-container countdown-{$product.id_product|intval}"
      data-product_id="{$product.id_product|intval}"
      data-until="{$product.embedded_attributes.specific_prices.to|date_format:'%B %e, %Y %H:%M:%S'}"
      data-titles='&lcub;"year":"{l s='Year' d='Shop.Theme.Actions'}","month":"{l s='Months' d='Shop.Theme.Actions'}","day":"{l s='Days' d='Shop.Theme.Actions'}","hour":"{l s='Hours' d='Shop.Theme.Actions'}","minute":"{l s='Minutes' d='Shop.Theme.Actions'}","second":"{l s='Seconds' d='Shop.Theme.Actions'}"&rcub;'>
    </div>
  {/if}
  {/if}
{/block}