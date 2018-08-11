{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<article class="mini-product" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">

  <div class="thumbnail-container relative">

    <div class="thumbnail product-thumbnail">

      {block name='product_thumbnail'}
      <a href="{$product.url}" class="relative">
        <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.cover.legend}" width="{$product.cover.bySize.cart_default.width}" height="{$product.cover.bySize.cart_default.height}"  data-full-size-image-url="{$product.cover.large.url}">
      </a>
      {/block}

    </div>

    <div class="product-description">

      {block name='product_name'}
        <h3 class="product-title"><a class="ellipsis" href="{$product.url}">{$product.name}</a></h3>
      {/block}

      {block name='product_price_and_shipping'}
        <div class="product-price-and-shipping">

          {if $product.has_discount}

            {hook h='displayProductPriceBlock' product=$product type="old_price"}
            <span class="regular-price">{$product.regular_price}</span>
            {if $product.discount_type === 'percentage'}
            <span class="discount-percentage">{$product.discount_percentage}</span>
            {/if}

          {/if}

          <span class="price">
          {if isset($product.light_list)}
            {$product.price}
          {else}
            {if $product.cart_quantity > 1}
              {$product.total} <span>({$product.cart_quantity} &#215; {$product.price})</span>
            {else}
              {$product.total}
            {/if}
          {/if}
          </span>

        </div>
      {/block}

      {if !isset($product.light_list)}
      <a href="{$product.remove_from_cart_url}" rel="nofollow" class="remove-product" data-link-action="remove-from-cart" title="{l s='Remove from cart' d='Shop.Theme.Actions'}">
        <svg class="svgic svgic-down"><use xlink:href="#si-down"></use></svg>
      </a>
      {/if}
      
    </div>

  </div>

</article>