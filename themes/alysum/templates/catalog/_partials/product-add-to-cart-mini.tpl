{**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="product-add-to-cart-mini">

  {if ( (isset($smarty.cookies.ts_is_catalog) && ($smarty.cookies.ts_is_catalog == false)) || !isset($smarty.cookies.ts_is_catalog) )}

    {block name='product_quantity'}
      <div class="product-quantity">
        <div class="add">
          <button class="btn btn-primary add-to-cart" title="{l s='Add to cart' d='Shop.Theme.Actions'}" data-button-action="add-to-cart" type="submit" {if $product.quantity <= 0}disabled{/if}><svg class="svgic svgic-button-cart"><use xlink:href="#si-button-cart"></use></svg></button>
          {block name='product_availability'}
            <span class="product-availability hidden">
              {if $product.show_availability && $product.availability_message}
                {if $product.availability == 'available'}
                  <i class="material-icons product-available">available</i>
                {elseif $product.availability == 'last_remaining_items'}
                  <i class="material-icons product-last-items">last-items</i>
                {else}
                  <i class="material-icons product-unavailable">unavailable</i>
                {/if}
              {/if}
            </span>
          {/block}
        </div>
      </div>
    {/block}

    {block name='product_minimal_quantity'}
      <p class="product-minimal-quantity hidden">
        {if $product.minimal_quantity > 1}
          {l s='The minimum purchase order quantity for the product is %quantity%.' d='Shop.Theme.Checkout' sprintf=['%quantity%' => $product.minimal_quantity]}
        {/if}
      </p>
    {/block}

  {/if}

</div>