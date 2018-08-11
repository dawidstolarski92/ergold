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
{block name='product_micro_item'}
<article class="product-miniature micro-product js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">

  <div class="thumbnail-container relative">

    <div class="thumbnail product-thumbnail relative flex-container">

      {block name='product_thumbnail'}
      <a href="{$product.url}" class="relative">
        <img src="{$product.cover.bySize.home_default.url}" alt="{$product.cover.legend}" width="{$product.cover.bySize.home_default.width}" height="{$product.cover.bySize.home_default.height}" class="cover-image" data-full-size-image-url="{$product.cover.large.url}">
      </a>
      {/block}

    </div>

    <div class="product-desc-wrap">

      <div class="product-description">

        {block name='product_manufacturer'}
          <h6 class="product-brand ellipsis">{$product.manufacturer_name}</h6>
        {/block}

        {block name='product_name'}
          <h3 class="product-title" itemprop="name"><a href="{$product.url}">{$product.name}</a></h3>
        {/block}

        {block name='product_price_and_shipping'}

          {if $product.show_price}
            <div class="product-price-and-shipping">

              {if $product.has_discount}

                {hook h='displayProductPriceBlock' product=$product type="old_price"}
                <span class="regular-price">{$product.regular_price}</span>
                {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage">{$product.discount_percentage}</span>
                {/if}

              {/if}

              {hook h='displayProductPriceBlock' product=$product type="before_price"}
              <span itemprop="price" class="price">{$product.price}</span>
              {hook h='displayProductPriceBlock' product=$product type='unit_price'}
              {hook h='displayProductPriceBlock' product=$product type='weight'}

            </div>
          {/if}
        {/block}
        
      </div>

    </div>

  </div>

</article>
{/block}