<header id="header" class="header-2">

{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{if (isset($pkts.top_bar) && $pkts.top_bar) || empty($pkts)}
<div class="header-top">
	<div class="page-width flex-container hook-displayNav">

		{if isset($pkts.top_bar_short_message) && ($pkts.top_bar_short_message != '')}
		<div class="header-short-message">{$pkts.top_bar_short_message}</div>
		{/if}

		{block name='nav'}
			{hook h='displayNav'}
		{/block}

	</div>
</div>
{/if}

<div class="header-main relative">
	<div class="page-width flex-container icons-true">

		{block name='header_logo'}
			{include file='_partials/logo.tpl'}
		{/block}

		<div class="header-right-side relative flex-container">
		{block name='search'}
			{hook h='displaySearch'}
		{/block}

		{block name='header_nav'}
		  <div class="header-nav hook-displayTop">
		    {hook h='displayTop'}
		  </div>
		{/block}
		</div>

	</div>
</div>

{block name='menu'}
	{hook h='menu'}
{/block}

{hook h='displayNavFullWidth'}

</header>
