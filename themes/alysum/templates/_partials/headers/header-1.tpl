<header id="header" class="header-1">

{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{if (isset($pkts.top_bar) && $pkts.top_bar) || empty($pkts)}
<div class="header-top">
	<div class="page-width flex-container">
		{if isset($pkts.top_bar_short_message) && ($pkts.top_bar_short_message != '')}
		<div class="header-short-message">{$pkts.top_bar_short_message}</div>
		{/if}

		{block name='nav'}
			{hook h='displayNav'}
		{/block}

        {block name='search'}
			{hook h='displaySearch'}
		{/block}
	</div>
</div>
{/if}

<div class="header-main relative{if isset($pkts.logo_position)} {$pkts.logo_position}{/if}">
	<div class="page-width flex-container">

		{block name='menu'}
			{hook h='menu'}
		{/block}

		{block name='header_logo'}
			{include file='_partials/logo.tpl'}
		{/block}

		<div class="header-right-side relative">
		{block name='header_nav'}
		  <div class="header-nav">
		    {hook h='displayTop'}
		  </div>
		{/block}
		</div>

	</div>
</div>

{hook h='displayNavFullWidth'}

</header>
