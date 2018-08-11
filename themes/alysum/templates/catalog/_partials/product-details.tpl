{*<div class="tab-pane fade{if !$product.description} in active{/if}"
     id="product-details"
     data-product="{$product.embedded_attributes|json_encode}"
  >
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
    {block name='product_availability_date'}
      {if $product.availability_date}
        <div class="product-availability-date">
          <label>{l s='Availability date:' d='Shop.Theme.Catalog'} </label>
          <span>{$product.availability_date}</span>
        </div>
      {/if}
    {/block}
    {block name='product_out_of_stock'}
      <div class="product-out-of-stock">
        {hook h='actionProductOutOfStock' product=$product}
      </div>
    {/block}

    {block name='product_features'}
      {if $product.features}
        <section class="product-features">
          <h3 class="h6">{l s='Data sheet' d='Shop.Theme.Catalog'}</h3>
          <dl class="data-sheet">
            {foreach from=$product.features item=feature}
              <dt class="name">{$feature.name}</dt>
              <dd class="value">{$feature.value}</dd>
            {/foreach}
          </dl>
        </section>
      {/if}
    {/block}


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
      {/if}
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
*}

<div id="product-details">
  {*<span>Ilość: {$product.quantity}</span>*}
  <div class="sys-info-section">

          <div class="col-md-6 no-padding font-mini">
          <div class="sys-info">{l s='Availability' d='Shop.Theme.Catalog'}: {$product.available_now} (<span>{$product.quantity} {$product.quantity_label}</span>)</div>
          {if $product.available_for_order == 1}
            <div class="sys-info">
                Wysyłka:
            {if $product.id_category_default == 22 || $category->id_parent == 22 }
                <span>3-7 dni</span>
            {else}
                <span>24 godziny</span>
            {/if}
            </div>
            {/if}
          <div class="sys-info">{l s='Reference' d='Shop.Theme.Catalog'}: {$product.reference_to_display}</div>


          </div>
        </div>
</div>
