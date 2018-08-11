<!-- Allegro block -->
{if isset($allegro_order)}
<div id="formAllegro" class="panel">
	<div class="panel-heading">
		<i class="icon-gavel"></i> {l s="Allegro"}
	</div>

	<dl class="well list-detail">
		<dt>{l s="Login"}</dt>
		<dd><a href="http://allegro.pl/show_user.php?uid={$allegro_order.buyer_id|intval}" target="_blank"><i class="icon-gavel"></i> {$allegro_order.buyer_login|escape:'html':'UTF-8'}</a></dd>
		<dt>{l s="E-mail"}</dt>
		<dd><a href="mailto:{$allegro_order.buyer_email|escape:'html':'UTF-8'}"><i class="icon-envelope-o"></i> {$allegro_order.buyer_email|escape:'html':'UTF-8'}</a></dd>
		
		<dt>{l s="Invoice"}</dt>
		<dd>{if $allegro_order.invoice}<b>{l s="Yes"}</b>{else}{l s="No"}{/if}</dd>

		{if $allegro_order.gd_address && $allegro_order.gd_address.postBuyFormAdrStreet}
		<hr />
		<dt>{l s="Delivery point"}</dt>
		<dd>
			{l s="Street:"} {$allegro_order.gd_address.postBuyFormAdrStreet|escape:'html':'UTF-8'}<br />
			{l s="Postcode:"} {$allegro_order.gd_address.postBuyFormAdrPostcode|escape:'html':'UTF-8'}<br />
			{l s="City:"} {$allegro_order.gd_address.postBuyFormAdrCity|escape:'html':'UTF-8'}<br />
			{l s="Name:"} {$allegro_order.gd_address.postBuyFormAdrFullName|escape:'html':'UTF-8'}<br />
			{l s="Company:"} {$allegro_order.gd_address.postBuyFormAdrCompany|escape:'html':'UTF-8'}<br />
			{l s="Phone:"} {$allegro_order.gd_address.postBuyFormAdrPhone|escape:'html':'UTF-8'}<br />
			{l s="Date:"} {$allegro_order.gd_address.postBuyFormCreatedDate|escape:'html':'UTF-8'}<br />
		</dd>
		{/if}

        {if $allegro_order.carrier_id && $allegro_order.carrier_name}
        <dt>{l s="Carrier"}</dt>
        <dd>#{$allegro_order.carrier_id|intval} <b>{$allegro_order.carrier_name|escape:'html':'UTF-8'}</b></dd>
        {/if}
	</dl>
</div>
{/if}
