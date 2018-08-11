<div class="text-block hook-{$text_block_hook}">
	{if $text_block_hook != 'displayFooter'}<div class="page-width">{/if}
	{if $text_block}{$text_block.text nofilter}{/if}
	{if $text_block_hook != 'displayFooter'}</div>{/if}
</div>
