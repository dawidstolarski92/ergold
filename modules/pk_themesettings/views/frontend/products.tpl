<div class="container footer-top-content">
	<div class="page_width">
		<div class="footer_top_col footer-new">
			<h4 class="lmroman">{l s='Latest' mod='pk_themesettings'}</h4>
			{if $ts_new}
				<ul class="footer-products">
				{foreach from=$ts_new item=product name=new}
					<li>
						<a href="{$product.link}" title="{$product.legend}">
							<img class="" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_'|cat:$cookie->img_name)}" alt="{$product.name}">
						</a><div class="info-section">
							<h5>
                            	<a class="product-name ellipsis main_color_hvr" href="{$product.link}" title="{$product.name}">{$product.name|strip_tags}</a>
                            </h5>
							<div class="rating">{hook h='displayProductListReviews' product=$product}</div>
							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                            	{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                    <div class="price-box">
                                        <span class="price">
                                        	{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                        </span>
                                    </div>
                                {/if}
                            {/if}
						</div>
					</li>
				{/foreach}
				</ul>
			{/if}
		</div>
		<div class="footer_top_col footer-spe">
			<h4 class="lmroman">{l s='Special' mod='pk_themesettings'}</h4>
			{if $ts_spe}
				<ul class="footer-products">
				{foreach from=$ts_spe item=product name=new}
					<li>
						<a href="{$product.link}" title="{$product.legend}">
							<img class="" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_'|cat:$cookie->img_name)}" alt="{$product.name}">
						</a>
						<div class="info-section">
							<h5>
                            	<a class="product-name lmromancaps ellipsis main_color_hvr" href="{$product.link}" title="{$product.name}">{$product.name|strip_tags}</a>
                            </h5>
							<div class="rating">{hook h='displayProductListReviews' product=$product}</div>
							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                            	{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                    <div class="price-box">
                                        <span class="price">
                                        	{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                        </span>
                                    </div>
                                {/if}
                            {/if}
						</div>
					</li>
				{/foreach}
				</ul>
			{/if}
		</div>
		<div class="footer_top_col footer-fea">
			<h4 class="lmroman">{l s='Featured' mod='pk_themesettings'}</h4>
			{if $ts_fea}
				<ul class="footer-products">
				{foreach from=$ts_fea item=product name=new}
					<li>
						<a href="{$product.link}" title="{$product.legend}">
							<img class="" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_'|cat:$cookie->img_name)}" alt="{$product.name}">
						</a>
						<div class="info-section">
							<h5>
                            	<a class="product-name ellipsis main_color_hvr" href="{$product.link}" title="{$product.name}">{$product.name|strip_tags}</a>
                            </h5>
							<div class="rating">{hook h='displayProductListReviews' product=$product}</div>
							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                            	{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                    <div class="price-box">
                                        <span class="price">
                                        	{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                        </span>
                                    </div>
                                {/if}
                            {/if}
						</div>
					</li>
				{/foreach}
				</ul>
			{/if}
		</div>
	</div>
</div>