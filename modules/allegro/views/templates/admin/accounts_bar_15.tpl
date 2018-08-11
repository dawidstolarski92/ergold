<div class="toolbarBox toolbarHead allegro">
	{if $warning}<div class="alert alert-warning">{$warning|escape:'UTF-8'}</div>{/if}
    <div class="pageTitle">
        <b>{l s='Allegro account:' mod='allegro'}</b> 
        {foreach from=$allegro_accounts item=aa}
            &nbsp;&nbsp;&nbsp;<a href="{$currentIndex}&amp;token={$token}&amp;id_allegro_account={$aa.id_allegro_account|intval}" {if $allegro_account->id_allegro_account == $aa.id_allegro_account}class="selected"{/if}>{$aa.name|escape:'html':'UTF-8'}{if $aa.sandbox} ({l s='sandbox' mod='allegro'}){/if}</a>
        {/foreach}
    </div>
</div>