<div class="header_logo{if isset($pkts.logo_center) && ($pkts.logo_center == 1)} text-center{/if}">
	<a class="header_logo_img" href="{$urls.base_url}" title="{$shop.name}">
		{if isset($pkts.logo_type) && $pkts.logo_type == 'image'}
			<img class="logo" src="{$shop.logo}" alt="{$shop.name}" />
		{/if}
		{if isset($pkts.logo_type) && $pkts.logo_type == 'text' && isset($pkts.logo_text)}
			<span class="logo">{$pkts.logo_text}</span>
		{/if}
	</a>
</div>