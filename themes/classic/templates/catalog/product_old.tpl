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
{extends file=$layout}

{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}">
{/block}

{block name='head' append}
  <meta property="og:type" content="product">
  <meta property="og:url" content="{$urls.current_url}">
  <meta property="og:title" content="{$page.meta.title}">
  <meta property="og:site_name" content="{$shop.name}">
  <meta property="og:description" content="{$page.meta.description}">
  <meta property="og:image" content="{$product.cover.large.url}">
  <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
  <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
  <meta property="product:price:amount" content="{$product.price_amount}">
  <meta property="product:price:currency" content="{$currency.iso_code}">
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='content'}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    <div class="row product-page-col page-width">
      <div class="col-md-6">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              {block name='product_flags'}
                <ul class="product-flags">
                  {foreach from=$product.flags item=flag}
                    <li class="product-flag {$flag.type}">{$flag.label}</li>
                  {/foreach}
                  {if $product.has_discount}
                    {if $product.discount_type === 'percentage'}
                      <li class="product-flag discount">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</li>
                    {else}
                      <li class="product-flag discount">
                          {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
                      </li>
                    {/if}
                  {/if}
                </ul>
              {/block}

              {block name='product_cover_tumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}

            {/block}
          </section>
        {/block}
        </div>
        <div class="col-md-6 product-info-section">

          {block name='page_header_container'}
            {block name='page_header'}
              <a class="product-brand" href="{$product_brand_url}">{$product_manufacturer->name}</a>
              <h1 class="h1" itemprop="name">{block name='page_title'}{$product.name}{/block}</h1>
            {/block}
          {/block}
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}

          {block name='system_info'}
          {assign var=pid value=$product.id}
          <div class="sys-info-section">

          <div class="col-md-6 no-padding font-mini">
          <div class="sys-info">{l s='Availability' d='Shop.Theme.Catalog'}: {$product.available_now} (<span>{$product.quantity} {$product.quantity_label}</span>)</div>
          <div class="sys-info">{l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference_to_display}</div>
          </div>
        </div>

        <div class="col-md-6 no-padding font-mini">
          <div class="sys-info flex-container">{hook h='displayProductRating' product=$pid}</div>
        </div>
          {/block}

        <div class="col-md-12 no-padding top-odstep">
          <div class="product-information">

          <div class="cechy_produktu">
          {block name='product_description_short'}
              <div id="product-description-short-{$product.id}" class="short-desc" itemprop="description">{$product.description_short nofilter}</div>
            {/block}

            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}


            {block name='product_reference'}
              {if isset($product_manufacturer->id)}
                <div class="product-manufacturer hidden">
                  {if isset($manufacturer_image_url)}
                    <a href="{$product_brand_url}">
                      <img src="{$manufacturer_image_url}" class="img img-thumbnail manufacturer-logo" />
                    </a>
                  {else}
                    <label class="label">{l s='Brand' d='Shop.Theme.Catalog'}</label>
                    <span>
                      <a href="{$product_brand_url}">{$product_manufacturer->name}</a>
                    </span>
                  {/if}
                </div>
              {/if}
              {if isset($product.reference_to_display)}
                <div class="product-reference hidden">
                  <label class="label">{l s='Reference' d='Shop.Theme.Catalog'} </label>
                  <span itemprop="sku">{$product.reference_to_display}</span>
                </div>
              {/if}
              {/block}
              {block name='product_quantities'}
                {if $product.show_quantities}
                  <div class="product-quantities hidden">
                    <label class="label">{l s='In stock' d='Shop.Theme.Catalog'}</label>
                    <span>{$product.quantity} {$product.quantity_label}</span>
                  </div>
                {/if}
              {/block}
          {*    {block name='product_availability_date'}
                {if $product.availability_date}
                  <div class="product-availability-date">
                    <label>{l s='Availability date:' d='Shop.Theme.Catalog'} </label>
                    <span>{$product.availability_date}</span>
                  </div>
                {/if}
              {/block} *}
              {block name='product_out_of_stock'}
                <div class="product-out-of-stock">
                  {hook h='actionProductOutOfStock' product=$product}
                </div>
              {/block}


              {block name='product_condition'}
                {if $product.condition}
                  <div class="product-condition">
                    <label class="label">{l s='Condition' d='Shop.Theme.Catalog'} </label>
                    <link itemprop="itemCondition" href="{$product.condition.schema_url}"/>
                    <span>{$product.condition.label}</span>
                  </div>
                {/if}
              {/block}
            </div>

            <div class="product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" class="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" class="product_customization_id">

                  {block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block}

                  {if $category->id_category==17}
              <p><span class="rozmiar_gratis">Nie znalazłeś rozmiaru? Zrobimy go dla Ciebie gratis!</span></p>
                  {/if}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <h3 class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</h3>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                          {/block}
                        {/foreach}
                      </section>
                    {/if}
                  {/block}

                  {block name='product_discounts'}
                    {include file='catalog/_partials/product-discounts.tpl'}
                  {/block}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}

                  {hook h='displayProductButtons' product=$product}

                  {block name='product_refresh'}
                    <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit" value="{l s='Refresh' d='Shop.Theme.Actions'}">
                  {/block}
                </form>
              {/block}

              {block name='product_features'}
                {if $product.features}
                <div class="cechy_produktu">
                  <section class="product-features">
                    <h3 class="h6 strong">{l s='Data sheet' d='Shop.Theme.Catalog'}</h3>
                    test
                    <dl class="data-sheet">
                      {foreach from=$product.features item=feature}
                        <dt class="name">{$feature.name}</dt>
                        <dd class="value">{$feature.value}</dd>
                      {/foreach}
                    </dl>
                  </section>
                {/if}
              {/block}

              {* if product have specific references, a table will be added to product details section *}
              {block name='product_specific_references'}
                {if isset($product.specific_references)}
                  <section class="product-features">
                    <h3 class="h6">{l s='Specific References' d='Shop.Theme.Catalog'}</h3>
                      <dl class="data-sheet">
                        {foreach from=$product.specific_references item=reference key=key}
                          <dt class="name">{$key}</dt>
                          <dd class="value">{$reference}</dd>
                        {/foreach}
                      </dl>
                  </section>
                </div>
                {/if}
              {/block}
              <a id="pokaz_cechy" href="#">Zobacz więcej</a>

            </div>




            {hook h='displayReassurance'}

        </div>
      </div>
      </div>
    </div>

<div id="block-reassurance" class="row page-width">
      <div class="col-md-4">
        <img src="https://ergold.pl/modules/blockreassurance/img/reassurance-1-1.jpg" alt="Wszystkie wyroby podlegają kontroli i spełniają najwyższe normy określone przez Polskie Prawo Probiercze"> <span>Wszystkie wyroby podlegają kontroli i spełniają najwyższe normy określone przez Polskie Prawo Probiercze</span>
      </div>
      <div class="col-md-4">
        <img src="https://ergold.pl/modules/blockreassurance/img/reassurance-2-1.jpg" alt="Każdy produkt, który u nas kupujesz jest nowy, posiada metkę i paragon co potwierdza autentyczność wyrobu. Na życzenie wystawiamy fakturę VAT."> <span>Każdy produkt, który u nas kupujesz jest nowy, posiada metkę i paragon co potwierdza autentyczność wyrobu. Na życzenie wystawiamy fakturę VAT.</span>
      </div>
      <div class="col-md-4">
        <img src="https://ergold.pl/modules/blockreassurance/img/reassurance-3-1.jpg" alt="Jeżeli produkt nie spełni Twoich oczekiwań, w ciągu 14 dni masz prawo do zwrotu towaru."> <span>Jeżeli produkt nie spełni Twoich oczekiwań, w ciągu 14 dni masz prawo do zwrotu towaru.</span>
      </div>
</div>





    {block name='product_tabs'}
    <div class="tabs-container">
      <div class="tabs page-width">
        <ul class="nav nav-tabs flex-container">
          {*   {if $product.description}
          <li class="nav-item">
            <a class="nav-link{if $product.description} active{/if}" data-toggle="tab" href="#description"><h5>{l s='Description' d='Shop.Theme.Catalog'}</h5></a>
          </li>
          {/if}
          <li class="nav-item">
            <a class="nav-link{if !$product.description} active{/if}" data-toggle="tab" href="#product-details"><h5>{l s='Product Details' d='Shop.Theme.Catalog'}</h5></a>
          </li>   *}
          {if $product.attachments}
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#attachments"><h5>{l s='Attachments' d='Shop.Theme.Catalog'}</h5></a>
          </li>
          {/if}
          {foreach from=$product.extraContent item=extra key=extraKey}
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#extra-{$extraKey}"><h5>{$extra.title}</h5></a>
          </li>
          {/foreach}

          {hook h='productTab'}

        </ul>

        <div class="tab-content" id="tab-content">
      {*   <div class="tab-pane fade in{if $product.description} active{/if}" id="description">
           {block name='product_description'}
             <div class="product-description">{$product.description nofilter}</div>
           {/block}
         </div>

         {block name='product_details'}
           {include file='catalog/_partials/product-details.tpl'}
         {/block}
*}
         {block name='product_attachments'}
           {if $product.attachments}
            <div class="tab-pane fade in" id="attachments">
               <section class="product-attachments">
                 <h3 class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</h3>
                 {foreach from=$product.attachments item=attachment}
                   <div class="attachment">
                     <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                     <p>{$attachment.description}</p
                     <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                       {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                     </a>
                   </div>
                 {/foreach}
               </section>
             </div>
           {/if}
         {/block}

         {foreach from=$product.extraContent item=extra key=extraKey}
         <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
             {$extra.content nofilter}
         </div>
         {/foreach}

         {hook h='productTabContent'}

        </div>
      </div>
    </div>
    {/block}

    {block name='product_accessories'}
      {if $accessories}
        <section class="product-accessories products-carousel page-width wide oh" data-num="{$accessories|count}" data-prefix="accessories">
          <h4 class="module-title"><span>{l s='You might also like' d='Shop.Theme.Catalog'}</span></h4>
          <div class="products">
            {foreach from=$accessories item="product_accessory"}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory image_size='medium_default'}
              {/block}
            {/foreach}
          </div>
        </section>
      {/if}
    {/block}

    {block name='product_footer'}
      <div class="page-width" data-hook="displayFooterProduct">
      {hook h='displayFooterProduct' product=$product category=$category}
      </div>
    {/block}

    {block name='product_images_modal'}
      <div class="page-width">
      {include file='catalog/_partials/product-images-modal.tpl'}
      </div>
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer page-width">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>
{/block}
