<div class="allegro-msg auction-create">
    <p>
    	{if $allegro_auction->date_start !== '0000-00-00 00:00:00'}{l s='Future offer' mod='allegro'}{else}{l s='Offer' mod='allegro'}{/if} 
    	({$allegro_auction->id_auction|floatval}) {l s='created sucefully!' mod='allegro'}<br /><small>({l s='auction may appear with delay up to 5 minutes' mod='allegro'})</small>
    </p>
    {if $allegro_auction->date_start !== '0000-00-00 00:00:00'}
    <p>{l s='Date start' mod='allegro'}: {$allegro_auction->date_start}</p>
    {/if}
    <ul style="margin: 15px 0 20px 0;">
        <li>{l s='Cost info' mod='allegro'}: {if $allegro_auction->cost_info}{$allegro_auction->cost_info|escape:'html':'UTF-8'}{else}{l s='0 zł' mod='allegro'}{/if}</li>
        {if 1}
    	<li>{l s='Allegro "standard"' mod='allegro'}: {if $allegro_auction->is_standard}{l s='Yes' mod='allegro'}{else}{l s='No' mod='allegro'}{/if}</li>
        {/if}
    </ul>
    <p>
        <a target="blank" class="btn btn-default" href="{$auction_url}"><i class="icon-link"></i> {l s='Go to auction' mod='allegro'}</a>
        <a href="{$currentIndex}&token={$token}&id_allegro_product={$allegro_auction->id_allegro_product|intval}&updateallegro_product&submitFinishAuction&id_auction={$allegro_auction->id_auction|floatval}&page={if isset($smarty.get.page)}{$smarty.get.page|intval}{/if}" class="btn btn-default">
            <i class="icon-trash"></i> {l s='Finish auction' mod='allegro'}
        </a>
    </p>
</div>