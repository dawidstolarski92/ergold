<div id="desktop_cart">
  <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
    <div class="header dd_el relative">

      <a rel="nofollow" href="{if isset($smarty.cookies.ts_order_link)}{$smarty.cookies.ts_order_link}{else}{$urls.pages.order}{/if}" class="dib">
        <svg class="svgic"><use xlink:href="#si-cart"></use></svg>
        <span class="cart-title">{l s='Cart' d='Shop.Theme.Checkout'}</span>
        <span class="cart-products-count" data-cartproducts="{$cart.products_count}">({$cart.products_count})</span>
      </a>

      {if $cart.products_count > 0}
      <div class="shopping_cart dd_container2">
        <div class="indent">

        {if isset($pkts.mini_cart)}
          {if file_exists($pkts.mini_cart)}
            {foreach from=$cart.products item=product name=cartProduct}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/mini-product.tpl' product=$product}
              {/block}
            {/foreach}
          {/if}
        {/if}
        <div class="flex-container cart-header">
          <div class="cart-total">
            <div>{l s='Shipping' mod='ps_shoppingcart'}: <i>{$cart.subtotals.shipping.value}</i></div>
          {*  <div>{l s='Tax' mod='ps_shoppingcart'}: <i>{$cart.subtotals.tax.value}</i></div> *}
            <div>{l s='Razem' mod='ps_shoppingcart'}: <i>{$cart.totals.total.value}</i></div>
          </div>
          <div class="cart-button cart-header">
            <a href="{if isset($smarty.cookies.ts_order_link)}{$smarty.cookies.ts_order_link}{else}{$urls.pages.order}{/if}" class="btn">{l s='Checkout' mod='ps_shoppingcart'}</a>
          {*  <a href="{if isset($smarty.cookies.ts_cart_link)}{$smarty.cookies.ts_cart_link}{else}{$urls.pages.cart}{/if}" class="btn">{l s='Cart' mod='ps_shoppingcart'}</a> *}
          </div>
        </div>
        </div>
      </div>
      {/if}
    </div>
  </div>
</div>
