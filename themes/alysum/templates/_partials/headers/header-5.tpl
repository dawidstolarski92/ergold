<header id="header" class="header-5">
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{if (isset($pkts.top_bar) && $pkts.top_bar) || empty($pkts)}
<div class="header-top">
	<div class="page-width flex-container">
		{if isset($pkts.header_short_message) && ($pkts.header_short_message != '')}
		<div class="header-short-message">{$pkts.header_short_message}</div>
		{/if}
		{hook h='displayNav'}
	</div>
</div>
{/if}

<div class="header-main">
	<div class="page-width flex-container icons-true">

		{block name='header_logo'}
			{include file='_partials/logo.tpl'}
		{/block}

		{block name='menu'}
			{hook h='menu'}
		{/block}

		{block name='search'}
			{hook h='displaySearch'}
		{/block}

		{block name='header_nav'}
		  <div class="header-nav">
		    {hook h='displayTop'}
		  </div>
		{/block}

	</div>
</div>

{hook h='displayNavFullWidth'}
</header>