<div id="block_myaccount_infos" class="block-contact">
	<h4>{l s='Your account' mod='ps_customeraccountlinks'}</h4>
	<ul>
    {foreach from=$my_account_urls item=my_account_url}
        <li><a href="{$my_account_url.url}" title="{$my_account_url.title}" rel="nofollow">{$my_account_url.title}</a></li>
    {/foreach}
    {hook h="displayMyAccountBlock"}
	</ul>
</div>
