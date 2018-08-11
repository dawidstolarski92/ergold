{if isset($pkts) && $pkts.gs_cookie_message == 1}
<div class="cookie-message">
	<div class="page-width">
		<span>{l s='This website uses cookies to ensure you get the best experience on our website' d='Modules.pk_themesettings.Shop'}<a href="http://cookiesandyou.com" target="_blank">{l s='Learn more' d='Modules.pk_themesettings.Shop'}</a></span>
		<button class="btn">{l s='Got it!' d='Modules.pk_themesettings.Shop'}</button>
	</div>
</div>
{/if}