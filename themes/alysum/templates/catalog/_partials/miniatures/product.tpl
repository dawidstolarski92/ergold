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
{if isset($product.product_settings)}
  {assign var="pp" value=$product.product_settings}
{/if}
{if isset($image_size)}
  {assign var="type" value=$image_size}
{else}
  {assign var="type" value='home_default'}
{/if}
{block name='product_miniature_item'}
<article class="product-miniature js-product-miniature{if ($product.new == 1)} new{/if}{if (isset($product.bestseller) && ($product.bestseller == 1))} bestsellers{/if}{if ($product.indexed == 1)} featured{/if}{if ($product.reduction > 0)} discount{/if}{if isset($product.category)} {$product.category}{/if}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">

  <div class="thumbnail-container relative">

    <div class="thumbnail product-thumbnail relative flex-container">

      {block name='product_thumbnail'}
      <a href="{$product.url}" class="relative">
        <img src="{$product.cover.bySize.$type.url}" alt="{$product.cover.legend}" width="{$product.cover.bySize.$type.width}" height="{$product.cover.bySize.$type.height}" class="cover-image" data-full-size-image-url="{$product.cover.large.url}">
        {if (isset($smarty.cookies.pm_hover_image) && ($smarty.cookies.pm_hover_image == true)) || (isset($pkts.pm_hover_image) && $pkts.pm_hover_image == true)}
          {foreach from=$product.images item=image}
            {if ($image.cover != 1)}
              <img class="subimage" src="{$image.bySize.$type.url}" alt="{$product.cover.legend}" width="{$image.bySize.$type.width}" height="{$image.bySize.$type.height}" data-full-size-image-url="{$image.bySize.$type.url}">{break}
            {/if}
          {/foreach}
        {/if}
      </a>
      {/block}

      {if $product.has_discount}
        {if $product.discount_type === 'percentage'}
          <span class="discount discount-badge discount-percentage">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
        {else}
          <span class="discount discount-badge discount-amount">
              {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
          </span>
        {/if}
      {/if}

      {block name='product_buy'}
        <div class="product-actions">

          {* {if (isset($smarty.cookies.pm_qw_button) && ($smarty.cookies.pm_qw_button == true)) || (isset($pkts.pm_qw_button) && $pkts.pm_qw_button == true)}
            <a href="#" class="quick-view btn btn-primary smooth05" data-link-action="quickview" title="{l s='Quick view' d='Shop.Theme.Actions'}">
              <svg class="svgic svgic-search"><use xlink:href="#si-search"></use></svg>
            </a>
          {/if} *}

          {if (isset($smarty.cookies.ts_cart_link) || isset($urls.pages.cart))}
          <form action="{if isset($smarty.cookies.ts_cart_link)}{$smarty.cookies.ts_cart_link}{else}{$urls.pages.cart}{/if}" method="post" class="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{if isset($smarty.cookies.ts_token)}{$smarty.cookies.ts_token}{else}{$static_token}{/if}">
            <input type="hidden" name="id_product" value="{$product.id}" class="product_page_product_id">
            {block name='product_add_to_cart'}
              {include file='catalog/_partials/product-add-to-cart-mini.tpl'}
            {/block}
          </form>
          {/if}

          {* {hook h='compareButton' product_id=$product.id} *}

        </div>
      {/block}

      {if (isset($pkts.pm_countdown) && $pkts.pm_countdown == true) || !isset($pkts.pm_countdown)}
        {include file='catalog/_partials/miniatures/countdown.tpl'}
      {/if}

    </div>

    <div class="product-desc-wrap">

      <div class="product-description clearfix">
{*
        {if (isset($smarty.cookies.pm_brand) && ($smarty.cookies.pm_brand == true)) || (isset($pkts.pm_brand) && $pkts.pm_brand == true)}
        {block name='product_manufacturer'}
          <h6 class="product-brand ellipsis">{$product.manufacturer_name}</h6>
        {/block}
        {/if}
*}
        {if (isset($smarty.cookies.pm_title) && ($smarty.cookies.pm_title == true)) || (isset($pkts.pm_title) && $pkts.pm_title == true)}
        {block name='product_name'}
          <h3 class="product-title{if (isset($pkts.pm_title_multiline) && $pkts.pm_title_multiline != true) } ellipsis{/if}" itemprop="name"><a href="{$product.url}">{$product.name}</a></h3>
        {/block}
        {/if}

        {if (isset($smarty.cookies.pm_price) && ($smarty.cookies.pm_price == true)) || (isset($pkts.pm_price) && $pkts.pm_price == true)}
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
        {/if}

        {block name='product_description_short'}
          <div class="short-desc product-description-short{if (isset($smarty.cookies.pm_desc) && ($smarty.cookies.pm_desc == true)) || (isset($pkts.pm_desc) && $pkts.pm_desc == true)} shown{else} hidden{/if}" itemprop="description">
            {$product.description_short nofilter}
          </div>
        {/block}

        {if (isset($smarty.cookies.pm_stars) && ($smarty.cookies.pm_stars == true)) || (isset($pkts.pm_stars) && $pkts.pm_stars == true)}
        {capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
        {if $smarty.capture.displayProductListReviews}
          <div class="hook-reviews">
          {hook h='displayProductListReviews' product=$product}
          </div>
        {/if}
        {/if}

      </div>

      {if (isset($smarty.cookies.pm_labels) && ($smarty.cookies.pm_labels == true)) || (isset($pkts.pm_labels) && $pkts.pm_labels == true)}
        {block name='product_flags'}
          <ul class="product-flags">
            {foreach from=$product.flags item=flag}
              <li class="{$flag.type}">{$flag.label}</li>
            {/foreach}
          </ul>
        {/block}
      {/if}

      {if (isset($smarty.cookies.pm_colors) && ($smarty.cookies.pm_colors == true)) || (isset($pkts.pm_colors) && $pkts.pm_colors == true)}
      <div class="highlighted-informations{if !$product.main_variants} no-variants{/if}">
          {block name='product_variants'}
            {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
          {/block}
      </div>
      {/if}

    </div>

  </div>

</article>
{/block}
