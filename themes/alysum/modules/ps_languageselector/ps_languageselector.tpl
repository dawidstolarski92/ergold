<div class="lang-select user-select">
	<div class="langs dd_el">
		<div class="current-item cp smooth02"{if isset($pkts.top_bar_height)} style="line-height:{$pkts.top_bar_height+1}px"{/if}>
			<a class="dib" href="{$link->getLanguageLink($current_language.id_lang)}">{l s='Language' d='Shop.Theme'}</a>
		</div>
		<ul class="opt-list dd_container"{if isset($pkts.top_bar_height)} style="top:{$pkts.top_bar_height+1}px"{/if}>
			{foreach from=$languages item=language}
			<li class="smooth02 cp main_bg_hvr">
				<img class="fl" src="{$urls.img_lang_url}{$language.id_lang}.jpg" width="16" height="11" alt="{$language.name_simple}" />
				<a class="fl ellipsis" href="{$link->getLanguageLink($language.id_lang)}" title="{$language.name}">{$language.name_simple}</a>
			</li>
			{/foreach}
		</ul>
	</div>
</div>