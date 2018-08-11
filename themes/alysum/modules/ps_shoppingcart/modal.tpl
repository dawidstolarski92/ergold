<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><svg class="svgic"><use xlink:href="#si-cross-thin"></use></svg></span>
        </button>
        <strong class="modal-title h6 text-xs-center" id="myModalLabel">{l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}</strong>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-7 divide-right">
            <div class="flex-container">
              <div class="col-md">
                <img class="product-image" src="{$product.cover.medium.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image">
              </div>
              <div class="col-md">
                <span class="h6 product-name">{$product.name}</span>
                <p class="price">{$product.price}</p>
                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                {foreach from=$product.attributes item="property_value" key="property"}
                  <span>{$property}: <i>{$property_value}</i></span><br>
                {/foreach}
                <span>{l s='Quantity:' d='Shop.Theme.Checkout'}&nbsp;<i>{$product.cart_quantity}</i></span>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="cart-content">
              {if $cart.products_count > 1}
                <p class="cart-products-count">{l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products_count] d='Shop.Theme.Checkout'}</p>
              {else}
                <p class="cart-products-count">{l s='There is %product_count% item in your cart.' sprintf=['%product_count%' =>$cart.products_count] d='Shop.Theme.Checkout'}</p>
              {/if}
              <p><strong>{l s='Total products:' d='Shop.Theme.Checkout'}</strong>&nbsp;<i>{$cart.subtotals.products.value}</i></p>
              <p><strong>{l s='Total shipping:' d='Shop.Theme.Checkout'}</strong>&nbsp;<i>{$cart.subtotals.shipping.value} {hook h='displayCheckoutSubtotalDetails' subtotal=$cart.subtotals.shipping}</i></p>
              {if $cart.subtotals.tax}
              	<p><strong>{$cart.subtotals.tax.label}</strong>&nbsp;<i>{$cart.subtotals.tax.value}</i></p>
              {/if}
              <p><strong>{l s='Total:' d='Shop.Theme.Checkout'}</strong>&nbsp;<i>{$cart.totals.total.value} <span class="subtext">{$cart.labels.tax_short}</span></i></p>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Continue shopping' d='Shop.Theme.Actions'}</button>
              <a href="{if isset($smarty.cookies.ts_order_link)}{$smarty.cookies.ts_order_link}{else}{$urls.pages.order}{/if}" class="btn btn-primary">{l s='checkout' d='Shop.Theme.Actions'}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
