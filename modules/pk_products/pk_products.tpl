<div class="pk-carousel view_grid">

  {if ($bundle.$pk_pr_hook.opts.pk_highlighted == 1)}

    <h3 class="module-title">
      <span>{l s='Deal of the day' mod='pk_products'}</span>
    </h3>
    
    <div class="highlighted-container flex-container hook-{$pk_pr_hook}">

      <div class="hl-listing flex-container">

        {if $bundle.$pk_pr_hook.products_kit}
          {foreach from=$bundle.$pk_pr_hook.products_kit item=set name=set key=k}
            {if $set}                  
              {foreach from=$set item=product name=products key=id}
                {if ($smarty.foreach.products.index < 4)}
                  {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                {/if}
              {/foreach}
            {/if}
          {/foreach}
        {/if}

      </div>

      <div class="highlighted-product">
        {if isset($bundle.$pk_pr_hook.hl_product.0)}
        {include file="catalog/_partials/miniatures/product.tpl" product=$bundle.$pk_pr_hook.hl_product.0 image_size='large_default'}
        {/if}
      </div>

    </div>

  {else}

    <div class="productsCarousel carouselDesktop homemodule{if !$bundle.$pk_pr_hook.opts.pk_wide} page-width{/if} hook-{$pk_pr_hook}">
        <div class="hidden pk_products js-pk_products-{$nonce}" data-options="{$json_opts}"></div> 
        {if ($bundle.$pk_pr_hook.products_kit|count > 1)}
          <div class="wht-bg tabs-wrapper">
            <ul class="tab-nav flex-container">
              {assign var="counter" value=1}
              {foreach from=$bundle.$pk_pr_hook.products_types item=type key=k}
                <li class="dib tab{if $bundle.$pk_pr_hook.opts.type_active == $k} active{/if}">
                  <a class="tab-link" data-rel="{$counter++}">
                    <h3 class="module-title">
                      <span>{$bundle.$pk_pr_hook.tabs.$type}</span>
                    </h3>
                  </a>
                </li>
              {/foreach}
            </ul>
          </div>
        {else}
          <h4 class="module-title"><span>{foreach from=$bundle.$pk_pr_hook.products_types item=type key=k}{$bundle.$pk_pr_hook.tabs.$type}{/foreach} products</span></h4>
        {/if}

        {if ($bundle.$pk_pr_hook.products_kit|count > 1)}
        <div class="wht-bg forStart">
          <div class="indent">
            <div class="tab-slider">
              <div class="tab-slider-wrapper">      
                {foreach from=$bundle.$pk_pr_hook.products_kit item=set name=set key=k}
                  <div class="accordionButton" data-tab-acc="{$smarty.foreach.set.index+1}">
                    <span>
                      
                    </span>
                  </div>
                  <div class="accordionContent tab-content{if $bundle.$pk_pr_hook.opts.type_active == $k} activeCarousel{/if}" data-acc="{$smarty.foreach.set.index+1}">
                    <div class="{$k}-pk_products-{$nonce} pk_products_list" data-prefix="{$k}" data-nonce="{$nonce}">
                      {if $set}                  
                        {foreach from=$set item=product name=products key=id}                      
                          {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                        {/foreach}
                      {/if}
                    </div>
                  </div>
                {/foreach}
              </div>
            </div>
          </div>
        </div>
        {else}
          {foreach from=$bundle.$pk_pr_hook.products_kit item=set name=set key=k}
            <div class="{$k}-pk_products-{$nonce} pk_products_list" data-prefix="{$k}" data-nonce="{$nonce}">
              {if $set}                  
                {foreach from=$set item=product name=products key=id}                      
                  {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                {/foreach}
              {/if}
            </div>
          {/foreach}
        {/if}
    </div>

  {/if}

</div>