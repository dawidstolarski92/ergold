{extends file='page.tpl'}

{block name="page_content"}
<!--noindex-->
<svg style="display:none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<defs><symbol id="si-cmp-cross" viewBox="0 0 11 11"><path d="M0.228,10.745c0.296,0.297,0.777,0.297,1.073,0l4.202-4.202l4.201,4.202 c0.297,0.297,0.777,0.297,1.073,0c0.297-0.297,0.297-0.776,0-1.073L6.576,5.471l4.201-4.203c0.297-0.295,0.297-0.777,0-1.074 c-0.296-0.295-0.776-0.295-1.073,0L5.503,4.397L1.301,0.194c-0.296-0.295-0.777-0.295-1.073,0c-0.296,0.297-0.296,0.779,0,1.074 L4.43,5.471L0.228,9.672C-0.068,9.969-0.068,10.448,0.228,10.745z"/></symbol></defs>
</svg><!--/noindex-->

  {if $hasProduct}

		<table id="product_comparison" class="table table-bordered compare-form num-{$products|count}" data-ajax="{$link->getModuleLink('pk_compare', 'compare')}">
			<tr>
				<td class="td_empty compare_extra_information">
					<h6>{l s='Product' mod="pk_compare"}</h6>
				</td>

				{foreach from=$products item=product}
				<td class="ajax_block_product wide-space relative comparison_infos product-block product-{$product.id}">

					<a href="{$product.url}" class="product-image" title="{$product.name}">
						<img src="{$product.cover.bySize.home_default.url}" alt="{$product.cover.legend}" width="{$product.cover.bySize.home_default.width}" height="{$product.cover.bySize.home_default.height}"  data-full-size-image-url="{$product.cover.large.url}">
					</a>
					{if isset($product.show_price) && $product.show_price}
						{if $product.on_sale}
						<div class="sale-box">
							<span class="sale-label">{l s='Sale' mod="pk_compare"}!</span>
						</div>
						{/if}
					{/if}

					<a class="product-name" href="{$product.url}">{$product.name}</a>
					
				</td>
				{/foreach}

			</tr>

			<tr>
				<td><h6>{l s='Availability' mod="pk_compare"}</h6></td>
				{foreach from=$products item=product}
					<td class="product-{$product.id}">
					<div class="comparison_availability_statut">
						{if !(($product.quantity <= 0 && !$product.available_later) OR ($product.quantity != 0 && !$product.available_now) OR !$product.available_for_order)}
							<span class="availability_value"{if $product.quantity <= 0} class="warning-inline"{/if}>
								{if $product.quantity <= 0}
									{l s='This product is no longer in stock' mod="pk_compare"}
								{else}
									{$product.available_now}
								{/if}
							</span>
						{/if}
					</div>
					</td>
				{/foreach}
			</tr>

			<tr>
				<td><h6>{l s='Price' mod="pk_compare"}</h6></td>
				{foreach from=$products item=product}
					<td class="product-{$product.id}">
					{if isset($product.show_price) && $product.show_price}
					<div class="prices-container">

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

					</div> <!-- end prices-container -->
					{/if}
					</td>
				{/foreach}
			</tr>

			<tr class="cmp-description hidden">
				<td><h6>{l s='Description' mod="pk_compare"}</h6></td>
				{foreach from=$products item=product}
					<td class="wide-space product-{$product.id}">
						{$product.description_short nofilter}
					</td>
				{/foreach}
			</tr>

			{if (isset($pkts.pm_stars) && $pkts.pm_stars == true) || !isset($pkts.pm_stars)}
			<tr>
				<td><h6>{l s='Rating' mod="pk_compare"}</h6></td>
				{foreach from=$products item=product}
					<td class="product-{$product.id}">
					{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
					{if $smarty.capture.displayProductListReviews}
						<div class="hook-reviews">
						{hook h='displayProductListReviews' product=$product}
						</div>
					{/if}
					</td>
				{/foreach}
			</tr>
			{/if}

			{if $ordered_features}
				{foreach from=$ordered_features item=feature}
					<tr>
						{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
						<td class="{$classname} feature-name">
							<h6>{$feature.name}</h6>
						</td>
						{foreach from=$products item=product name=for_products}
							{assign var='product_id' value=$product.id}
							{assign var='feature_id' value=$feature.id_feature}
							{if isset($product_features[$product_id])}
								{assign var='tab' value=$product_features[$product_id]}
								<td class="{$classname} comparison_infos product-{$product.id}">{if (isset($tab[$feature_id]))}{$tab[$feature_id]}{/if}</td>
							{else}
								<td class="{$classname} comparison_infos product-{$product.id}"></td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
			{else}
				<tr>
					<td></td>
					<td colspan="{$products|@count}" class="text-center">{l s='No features to compare' mod="pk_compare"}</td>
				</tr>
			{/if}
			<tr>
				<td></td>
				{foreach from=$products item=product}
					<td class="product-{$product.id} wide-space">
						<div class="button-container">
							<form action="{$urls.pages.cart}" method="post" class="add-to-cart-or-refresh">
					            <input type="hidden" name="token" value="{$static_token}">
					            <input type="hidden" name="id_product" value="{$product.id}" class="product_page_product_id">

					            <div class="product-add-to-cart">

								  {if !$configuration.is_catalog}

								    {block name='product_quantity'}
								      <div class="product-quantity">
								          <button class="btn btn-primary add-to-cart" title="{l s='Add to cart' d='Shop.Theme.Actions'}" data-button-action="add-to-cart" type="submit" {if $product.quantity <= 0}disabled{/if}>{l s='Add to Cart' d='Shop.Theme.Actions'}</button>
								          {block name='product_availability'}
								            <span class="product-availability hidden">
								              {if $product.show_availability && $product.availability_message}
								                {if $product.availability == 'available'}
								                  <i class="material-icons product-available">{l s='Available' mod="pk_compare"}</i>
								                {elseif $product.availability == 'last_remaining_items'}
								                  <i class="material-icons product-last-items">{l s='Last Items' mod="pk_compare"}</i>
								                {else}
								                  <i class="material-icons product-unavailable">{l s='Unavailable' mod="pk_compare"}</i>
								                {/if}
								              {/if}
								            </span>
								          {/block}
								      </div>
								    {/block}

								    {block name='product_minimal_quantity'}
								      <p class="product-minimal-quantity hidden">
								        {if $product.minimal_quantity > 1}
								          {l s='The minimum purchase order quantity for the product is %quantity%.' mod="pk_compare" sprintf=['%quantity%' => $product.minimal_quantity]}
								        {/if}
								      </p>
								    {/block}

								  {/if}
								</div>
					        </form>						
						</div>
						<div class="cmp-remove" data-pid="{$product.id}">
							<svg class="svgic"><use xlink:href="#si-cmp-cross"></use></svg>
						</div>
					</td>
				{/foreach}
			</tr>
			{hook h='extraProductComparison' list_ids_product=$ids}
	</table> <!-- end products_block -->

{else}

	<p class="alert alert-warning">{l s='There are no products selected for comparison' mod="pk_compare"}</p>

{/if}

{/block}