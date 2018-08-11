{if isset($smarty.get.dev)}
    {$max_items = 1000}
{else}
    {$max_items = 20}
{/if}

<div class="alert ajax-warning alert-warning" style="display: none">
    {l s='Sync is in progress. Please do not leave this page' mod='allegro'}
</div>
<div class="conf ajax-success alert alert-success" style="display: none;">
    <span class="message"></span>
</div>
<div class="conf ajax-danger alert alert-danger" style="display: none;">
    <span class="message"></span>
</div>

{if $ALLEGRO_DEV_MODE}
<div class="log" style="display: none;"></div>
{/if}

<div class="panel">
    <div class="panel-heading">{l s='Sync' mod='allegro'}</div>

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info">
                <a class="ajaxcall-recurcive btn btn-default" href="{$sync_url}full_sync">{l s='Full sync' mod='allegro'}</a> 
                {l s='Full sync will update all parameters (title, price, images, description etc.) except qunatity on all your auctions.' mod='allegro'}
            </div>
        </div>
    </div>

    <p>&nbsp;</p>
    <div class="alert alert-info">
        <p>{l s='You can set a cron job that will rebuild sync stock, import orders and relist auctions, below you can fin full paths to cron scripts:' mod='allegro'}</p>
        <br>
        <ul>
            <li><b>{$module_realpath|escape:html:'UTF-8'}/cron-stock.php</b></li>
            <li><b>{$module_realpath|escape:html:'UTF-8'}/cron-order.php</b></li>
            <li><b>{$module_realpath|escape:html:'UTF-8'}/cron-relist.php</b></li>
        </ul>
        <br>
        <p>{l s='Native cron command should looks like (recommended):' mod='allegro'}<br><b>/bin/php {$module_realpath|escape:html:'UTF-8'}/cron-xxxx.php{* param=value*}</b></p>
        <p>{l s='HTTP urls:' mod='allegro'}
            <b>
                <a href="{$module_url|escape:html:'UTF-8'}cron-stock.php?key={$key}">{l s='stock' mod='allegro'}</a>,
                <a href="{$module_url|escape:html:'UTF-8'}cron-order.php?key={$key}">{l s='orders' mod='allegro'}</a>,
                <a href="{$module_url|escape:html:'UTF-8'}cron-relist.php?key={$key}">{l s='relist' mod='allegro'}</a>.
            </b>
        </p>
    </div>
    <div class="alert alert-info">{l s='The configuration may vary depending on the hosting provider, in case of problems best to ask the server administrator.' mod='allegro'}</div>
</div>

<div class="panel">
    <div class="panel-heading">{l s='Update/finish' mod='allegro'} <span class="badge">{count($outdate_auctions)}</span></div>

    {if $PS_STOCK_MANAGEMENT}
    <div class="alert alert-info">
        <a class="ajaxcall-recurcive btn btn-default" href="{$update_stock_url}">{l s='Update stock' mod='allegro'}</a> 
        {l s='List of auctions waiting to update stock or finish.' mod='allegro'}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='allegro'}</th>
                <th>{l s='Title' mod='allegro'}</th>
                <th>{l s='Stock qty' mod='allegro'}</th>
                <th>{l s='Last auction qty' mod='allegro'}</th>
                <th>{l s='Price' mod='allegro'}</th>
                <th>{l s='Date upd' mod='allegro'}</th>
                <th>{l s='Shop' mod='allegro'}</th>
                <th>{l s='Info' mod='allegro'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$outdate_auctions item=auction key=k}
            {if $k < $max_items}
            <tr>
                <td>{$auction.id_auction|floatval}</td>
                <td><a href="{$auction.auction_url|escape:html:'UTF-8'}">{$auction.title|escape:html:'UTF-8'}</a></td>
                <td>{$auction.stock_quantity|intval}</td>
                <td>{$auction.quantity|intval}</td>
                <td>{convertPrice price=$auction.price|floatval}</td>
                <td>{$auction.date_upd|escape:html:'UTF-8'}</td>
                <td>{$auction.shop_name|truncate:16|escape:html:'UTF-8'}</td>
                 <td>
                    {if $ALLEGRO_FINISH_IF_DISABLED && !$auction.active}
                        <i class="icon icon-lg icon-trash" title="{l s='Finish (product disabled)' mod='allegro'}">
                    {else}
                        {if $auction.quantity < $auction.stock_quantity}
                            <i class="icon icon-lg icon-level-up" title="{l s='Qty up' mod='allegro'}">
                        {elseif $auction.quantity > $auction.stock_quantity}
                            {if $auction.stock_quantity <= 0}
                                <i class="icon icon-lg icon-trash" title="{l s='Finish (out of stock)' mod='allegro'}">
                            {else}
                                <i class="icon icon-lg icon-level-down" title="{l s='Qty down' mod='allegro'}">
                            {/if}
                        {else}
                            <i class="icon icon-lg icon-gavel" title="{l s='New transaction' mod='allegro'}">
                        {/if}
                    {/if}
                </td>
            </tr>
            {else}
            <tr>
                <td class="list-empty" colspan="99">
                    <div>
                        <a href="{$currentIndex}&amp;token={$token}&amp;dev">{l s='And more' mod='allegro'} ({count($outdate_auctions)-$max_items})...</a>
                    </div>
                </td>
            </tr>
            {break}
            {/if}
        {foreachelse}
            <tr>
                <td class="list-empty" colspan="99">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No items' mod='allegro'}
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {else}
    <div class="alert alert-warning">{l s='Stock management is disabled.' mod='allegro'}</div>
    {/if}
</div>

<div class="panel">
    <div class="panel-heading">{l s='Orders' mod='allegro'} <span class="badge">{count($transactions)}</span></div> 

    {if $ALLEGRO_ORDER_SYNC}
    <div class="alert alert-info">
        <a class="ajaxcall-recurcive btn btn-default" href="{$sync_url}order_sync">{l s='Import/update orders' mod='allegro'}</a> 
        {l s='List of transactions that will be converted to an orders i PrestaShop.' mod='allegro'}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='allegro'}</th>
                <th>{l s='ID transaction' mod='allegro'}</th>
                <th>{l s='Qty' mod='allegro'}</th>
                <th>{l s='Date add' mod='allegro'}</th>
                <th>{l s='Info' mod='allegro'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$transactions item=tr key=k}
            {if $k < $max_items}
            <tr>
                <td><a href="{$tr->auctionUrl|escape:html:'UTF-8'}">{$tr->dealItemId|floatval}</a></td>
                <td>{$tr->dealTransactionId|intval}</td>
                <td>{$tr->dealQuantity|intval}</td>
                <td>{$tr->dealEventDate|escape:html:'UTF-8'}</td>
                <td>
                    {if $tr->dealEventType == 2}
                        <i class="icon icon-lg icon-gavel" title="{l s='Created' mod='allegro'}">
                    {elseif $tr->dealEventType == 3}
                        <i class="icon icon-lg icon-trash" title="{l s='Canceled' mod='allegro'}">
                    {elseif $tr->dealEventType == 4}
                         <i class="icon icon-lg icon-dollar" title="{l s='Paid' mod='allegro'}">
                    {/if}
                </td>
            </tr>
            {else}
            <tr>
                <td class="list-empty" colspan="99">
                    <div>
                        <a href="{$currentIndex}&amp;token={$token}&amp;dev">{l s='And more' mod='allegro'} ({count($transactions)-$max_items})...</a>
                    </div>
                </td>
            </tr>
            {break}
            {/if}
        {foreachelse}
            <tr>
                <td class="list-empty" colspan="99">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No items' mod='allegro'}
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {else}
    <div class="alert alert-warning">{l s='Order sync is disabled.' mod='allegro'}</div>
    {/if}
</div>


<div class="panel">
    <div class="panel-heading">{l s='Relist' mod='allegro'} <span class="badge">{count($products_relist)}</span></div> 
    
    <div class="alert alert-info">
        <a class="ajaxcall-recurcive btn btn-default" href="{$sync_url}relist">{l s='Relist auctions' mod='allegro'}</a>
        {l s='List of products waiting for relist.' mod='allegro'}
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{l s='ID' mod='allegro'}</th>
                <th>{l s='ID product' mod='allegro'}</th>
                <th>{l s='Name' mod='allegro'}</th>
                <th>{l s='Stock qty' mod='allegro'}</th>
                <th>{l s='Min. relist qty' mod='allegro'}</th>
                <th>{l s='Shop' mod='allegro'}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        {$hasFail = false}
        {foreach from=$products_relist item=allegro_product key=k}
            {if $k < $max_items}
            <tr>
                <td>{$allegro_product.id_allegro_product|intval}</td>
                <td>
                    {$allegro_product.id_product|intval}{if $allegro_product.id_product_attribute}-{$allegro_product.id_product_attribute|intval}{/if}
                </td>
                <td>{$allegro_product.name|escape:html:'UTF-8'}</td>
                <td>{$allegro_product.quantity|intval}</td>
                <td>{$allegro_product.relist_min_qty|intval}</td>
                <td>{$allegro_product.shop_name|escape:html:'UTF-8'}</td>
                <td>
                    {if $allegro_product.cache_relist_error}
                        {$hasFail = true}
                        <span class="label label-danger" title="{$allegro_product.cache_relist_error|escape:html:'UTF-8'}">{l s='Relist fail - check product' mod='allegro'}</span>
                    {/if}
                </td>
            </tr>
            {else}
            <tr>
                <td class="list-empty" colspan="99">
                    <div>
                        <a href="{$currentIndex}&amp;token={$token}&amp;dev">{l s='And more' mod='allegro'} ({count($products_relist)-$max_items})...</a>
                    </div>
                </td>
            </tr>
            {break}
            {/if}
        {foreachelse}
            <tr>
                <td class="list-empty" colspan="99">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No items' mod='allegro'}
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {if $hasFail}
        <p>&nbsp;</p>
        <div class="alert alert-warning">
            {l s='There is problem with relisting one or more product, go to allegro products tab and check product params.' mod='allegro'}
        </div>
    {/if}
</div>

<script>
var translations = new Array();
translations['in_progress'] = ' {l s='(in progress)' js=1 mod='allegro'}';
translations['finished'] = '{l s='Sync finished' js=1 mod='allegro'}';
translations['failed'] = '{l s='Sync failed' js=1 mod='allegro'}';

$('.ajaxcall-recurcive').each(
	function(it, elm) {
		$(elm).click(
			function() {
				if (this.cursor == undefined)
					this.cursor = 0;

				if (this.legend == undefined)
					this.legend = $(this).html();

				if (this.running == undefined)
					this.running = false;

				if (this.running == true)
					return false;

				$('.ajax-message, .ajax-danger, .ajax-success').hide();

				this.running = true;

				$(this).html(this.legend+translations['in_progress']);
				$('.ajax-warning').show();

				$.ajax({
					url: this.href+'&cursor='+this.cursor,
					context: this,
					dataType: 'json',
					cache: 'false',
					success: function(res) {
						this.running = false;

                        if (res.messages) {
                            res.messages.forEach(function(entry) {
                                $('.log').show().prepend('<p>'+entry+'</p>');
                            });
                        }

						if (res.continue) {
                            this.cursor = this.cursor+1;
                            $(this).html(this.legend+translations['in_progress'].replace('%s', res.count));
                            $(this).click();
                            return;
						}

                        this.cursor = 0;
                        $('.ajax-warning').hide();
                        $(this).html(this.legend);
                        $('.ajax-success span').html(translations['finished']);
                        $('.ajax-success').show();
                        return;

					},
					error: function(res) {
						$('.ajax-warning').hide();
						$('.ajax-danger span').html(translations['failed']);
						$('.ajax-danger').show();
						$(this).html(this.legend);

						this.cursor = 0;
						this.running = false;
					}
				});
				return false;
			}
		);
	}
);
</script>
