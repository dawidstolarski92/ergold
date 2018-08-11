<div class="panel">
	<div class="row">
		{if $warning}<div class="alert alert-warning">{$warning|escape:'UTF-8'}</div>{/if}
		<div class="col-lg-9">
			{if $allegro_account->id}
				<h4>{l s='Current account' mod='allegro'}: 
					<b>{$allegro_account->name|escape:'html':'UTF-8'} ({$allegro_account->login|escape:'html':'UTF-8'})</b>
					{if $allegro_account->sandbox} <span style="color: red">({l s='sandbox' mod='allegro'})</span>{/if}
				</h4>
			{/if}
		</div>
		<div class="col-lg-3 text-right">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					{l s='Select account' mod='allegro'}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
				{foreach from=$allegro_accounts item=allegro_account}
					<li><a href="{$currentIndex}&amp;token={$token}&amp;id_allegro_account={$allegro_account.id_allegro_account|intval}">{$allegro_account.name|escape:'html':'UTF-8'} ({$allegro_account.login|escape:'html':'UTF-8'}){if $allegro_account.sandbox} ({l s='sandbox' mod='allegro'}){/if}</a></li>
				{/foreach}
					<li role="separator" class="divider"></li>
					<li><a href="{$new_allegro_account_link}"><i class="icon icon-plus-circle"></i> {l s='Add new account' mod='allegro'}</a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
