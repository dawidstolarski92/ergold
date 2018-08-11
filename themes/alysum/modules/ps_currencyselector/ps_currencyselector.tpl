<div class="curr-select user-select">
	<div class="currencies dd_el">
		<div class="current-item cp dib smooth02"{if isset($pkts.top_bar_height)} style="line-height:{$pkts.top_bar_height+1}px"{/if}>
			<span>{l s='Currency' d='Shop.Theme'}</span>
		</div>
		<ul class="opt-list dd_container"{if isset($pkts.top_bar_height)} style="top:{$pkts.top_bar_height+1}px"{/if}>
			{foreach from=$currencies item=currency}
			<li class="dropdown-option cp smooth02{if $currency.current} current{/if}">
				<a rel="nofollow" class="currency-sign main_color" href="{$currency.url}">{$currency.sign} {$currency.name}</a>
			</li>
			{/foreach}
		</ul>
	</div>
</div>