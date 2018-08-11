<div class="panel">
	<div class="panel-heading"><i class="icon-gear"></i> {l s='Manual sync' mod='allegro'}</div>
	<div class="row">

	</div>
</div>
<div class="panel">
	<div class="panel-heading"><i class="icon-gears"></i> {l s='Automatic sync (CRON)' mod='allegro'}</div>
	<div class="row">

	</div>
</div>

<!-- Auctions table -->
<form action="{$currentIndex}&amp;token={$token}" method="post">
	<div class="panel">
		<div class="panel-heading"><i class="icon-legal"></i> {l s='Aucions list' mod='allegro'}</div>
		<div class="panel">
			<table class="table table-responsive-row clearfix">
				<thead>
					<tr>
						<th class="fixed-width-xs center">{l s='ID' mod='allegro'}</th>
						<th class="center">{l s='Image' mod='allegro'}</th>
						<th>{l s='Title' mod='allegro'}</th>
						<th>{l s='Offers nb.' mod='allegro'}</th>
						<th>{l s='Sold qty.' mod='allegro'}</th>
						<th class="center">{l s='Stats' mod='allegro'}</th>
						<th class="center">{l s='Price' mod='allegro'}</th>
						<th>{l s='End' mod='allegro'}</th>
						<th>{l s='' mod='allegro'}</th>
					</tr>
					<tr class="filter">
						<th>
							<input type="text" class="filter">
						</th>
						<th class="center">--</th>
						<th>
							<input type="text" class="filter" name="allegro_auctionFilter_name">
						</th>
						<th>
							<input type="text" class="filter">
						</th>
						<th></th>
						<th class="center"></th>
						<th></th><th></th>
						<th>
							<span class="pull-right">
								<button type="submit" id="submitFilterButtonallegro_product" name="submitFilter" class="btn btn-default" data-list-id="allegro_product">
									<i class="icon-search"></i> {l s='Search' mod='allegro'}
								</button>
							</span>
						</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$auctions item=auction}
					<tr>
						<td><a href="">{$auction.auction_id|floatval}</a></td>
						<td class="center"><img src="{$auction.thumbail_url}" alt="" class="img-thumbnail"></td>
						<td>{$auction.title|escape:'html':'UTF-8'}</td>
						<td>{$auction.nb_offers|intval}</td>
						<td>{$auction.quantity_sold|intval}</td>
						<td>
							<div>{l s='Watch:' mod='allegro'}<span class="pull-right">{$auction.nb_watch|intval}</span></div>
							<div>{l s='Views:' mod='allegro'}<span class="pull-right">{$auction.nb_views|intval}</span></div>
						</td>
						<td class="center"><b>{convertPrice price=$auction.price_buy_now|floatval}</b></td>
						<td>
							<div {if $auction.end_time_left < $smarty.now + (15 * 60)}style="color: red;"{/if}>{$auction.end_time_left_text|escape:'html':'UTF-8'}</div>
							<div>{$auction.end_time_left|date_format:"%F %H:%M:%S"}</div>
						</td>
						<td>
							<a href="#" class="btn btn-default">
								<i class="icon-trash"></i> {l s='Finish auction' mod='allegro'}
							</a>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td class="list-empty" colspan="7">
							<div class="list-empty-msg">
								<i class="icon-warning-sign list-empty-icon"></i>
								{l s='No auctions found' mod='allegro'}
							</div>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			<div class="row">
				<div class="col-lg-6"></div>
				<div class="col-lg-6">
					<div class="pagination">
						{l s='Display' mod='allegro'}
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							5
							<i class="icon-caret-down"></i>
						</button>
						<ul class="dropdown-menu">
										<li>
								<a href="javascript:void(0);" class="pagination-items-page" data-items="5" data-list-id="allegro_product">5</a>
							</li>
										<li>
								<a href="javascript:void(0);" class="pagination-items-page" data-items="20" data-list-id="allegro_product">20</a>
							</li>
										<li>
								<a href="javascript:void(0);" class="pagination-items-page" data-items="50" data-list-id="allegro_product">50</a>
							</li>
										<li>
								<a href="javascript:void(0);" class="pagination-items-page" data-items="100" data-list-id="allegro_product">100</a>
							</li>
									</ul>
						/ 11 wynik(i)
						<input type="hidden" id="allegro_product-pagination-items-page" name="allegro_product_pagination" value="5">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>