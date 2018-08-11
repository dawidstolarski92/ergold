<!-- Block customlinks module -->
<div class="pk_customlinks pk_cl">
	<ul>
		{if $main_links.reg == true}
		<li class="pk_register dd_el dib smooth02">
			<a href="#" title="{l s='Register' mod='pk_customlinks'}">
				<svg class="svgic main_color svgic-lock2"><use xlink:href="#si-lock2"></use></svg>
				<span>{l s='Sign In / Up' mod='pk_customlinks'}</span>
			</a>
			<div class="customer-form-container dd_container">
				<div class="indent">

					{if ($customer.is_logged != 1)}

					<div class="title-wrap flex-container">	
						<h4 class="customer-form-tab login-tab active"><span>{l s='Sign In' mod='pk_customlinks'}</span></h4>
						<h4>{l s='OR' mod='pk_customlinks'}</h4>
						<h4 class="customer-form-tab register-tab"><span>{l s='Register' mod='pk_customlinks'}</span></h4>
					</div>

					<div class="form-wrap">

						<form id="login-form" class="customer-form active" action="{$urls.pages.authentication}" method="post">
							<div class="relative">
								<div class="icon-true">
									<input class="form-control" name="email" type="email" value="" placeholder="{l s='Email' mod='pk_customlinks'}" required="">
									<span class="focus-border"><i></i></span>
									<svg class="svgic input-icon"><use xlink:href="#si-email"></use></svg>
								</div>
							</div>
							<div class="relative">
								<div class="input-group-dis js-parent-focus">
									<div class="icon-true relative">
										<input class="form-control js-child-focus js-visible-password" name="password" type="password" value="" placeholder="{l s='Password' mod='pk_customlinks'}" required="">
										<span class="focus-border"><i></i></span>
										<svg class="svgic input-icon"><use xlink:href="#si-password"></use></svg>
									</div>
								</div>
							</div>
							<div class="forgot-password">
								<input type="hidden" name="submitLogin" value="1">
								<button class="btn btn-primary" data-link-action="sign-in" type="submit">{l s='Sign in' mod='pk_customlinks'}</button>
								<a href="{$urls.pages.password}" rel="nofollow" class="hidden">{l s='Forgot your password?' mod='pk_customlinks'}</a>
							</div>
						</form>

						<form action="{$urls.pages.register}?back=identity" id="customer-form" class="customer-form" method="post">

							<input type="hidden" value="1" name="submitCreate">
							<input type="hidden" value="0" name="newsletter">
							<input type="hidden" value="0" name="optin">
							<input type="hidden" value=""  name="id_customer">
							<input type="hidden" value="1" name="id_gender">
							<input type="hidden" value="FirstName" name="firstname">
							<input type="hidden" value="LastName" name="lastname">

							<div class="relative">
								<div class="icon-true">
									<input class="form-control" name="email" type="email" value="" placeholder="{l s='Email' mod='pk_customlinks'}" required="">
									<span class="focus-border"><i></i></span>
									<svg class="svgic input-icon"><use xlink:href="#si-email"></use></svg>
								</div>
							</div>
							<div class="relative">
								<div class="input-group-dis js-parent-focus">
									<div class="icon-true relative">
										<input class="form-control" name="password" placeholder="Password" type="password" value="" required="" pattern=".{literal}{{/literal}5,{literal}}{/literal}">
										<span class="focus-border"><i></i></span>
										<svg class="svgic input-icon"><use xlink:href="#si-password"></use></svg>
									</div>
								</div>
							</div>
							<button class="btn btn-primary form-control-submit register-button" type="submit" data-back="{$urls.pages.identity}">{l s='Register' mod='pk_customlinks'}</button>
						</form>

					</div>

					{else}
						{l s='Hello' mod='pk_customlinks'}, {$customer.firstname} {$customer.lastname}<br><br><a href="{$urls.actions.logout}" class="btn">{l s='Sign Out' mod='pk_customlinks'}</a>
					{/if}

				</div>
			</div>
		</li>
		{/if}
		{if $main_links.myacc == true}
		<li class="pk_account dd_el dib smooth02">
			<a href="#" title="{l s='My Account' mod='pk_customlinks'}">
				<svg class="svgic main_color svgic-account"><use xlink:href="#si-account"></use></svg>
				<span>{l s='My Account' mod='pk_customlinks'}</span>
			</a>
			<div class="dd_container">
				<div class="indent">
				<ul>
					<li class="smooth02"><a href="{$link->getPageLink('history', true)}" title="{l s='My orders' mod='pk_customlinks'}" rel="nofollow">{l s='My orders' mod='pk_customlinks'}</a></li>
					{if $pk_returnAllowed}<li><a href="{$link->getPageLink('order-follow', true)}" title="{l s='My returns' mod='pk_customlinks'}" rel="nofollow">{l s='My merchandise returns' mod='pk_customlinks'}</a></li>{/if}
					<li class="smooth02"><a href="{$link->getPageLink('order-slip', true)}" title="{l s='My credit slips' mod='pk_customlinks'}" rel="nofollow">{l s='My credit slips' mod='pk_customlinks'}</a></li>
					<li class="smooth02"><a href="{$link->getPageLink('addresses', true)}" title="{l s='My addresses' mod='pk_customlinks'}" rel="nofollow">{l s='My addresses' mod='pk_customlinks'}</a></li>
					<li class="smooth02"><a href="{$link->getPageLink('identity', true)}" title="{l s='Manage my personal information' mod='pk_customlinks'}" rel="nofollow">{l s='My personal info' mod='pk_customlinks'}</a></li>
					{if $pk_voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}" title="{l s='My vouchers' mod='pk_customlinks'}" rel="nofollow">{l s='My vouchers' mod='pk_customlinks'}</a></li>{/if}
				</ul>
				</div>
			</div>
		</li>
		{/if}
		{if $main_links.mywtl == true}
		<li class="pk_watchlist dd_el dib smooth02">
			<a href="#" title="{l s='Watch List' mod='pk_customlinks'}">
				<svg class="svgic main_color svgic-eye"><use xlink:href="#si-eye"></use></svg>
				<span>{l s='Watch List' mod='pk_customlinks'} <span>({count($watchlist)})</span></span>
			</a>
			{if file_exists($tpl) && $watchlist}
			<div class="watchlist dd_container">
				<div class="indent">
					{foreach from=$watchlist item=product name=loop}
					{include file='catalog/_partials/miniatures/mini-product.tpl' product=$product}
					{/foreach}
				</div>
			</div>
			{/if}
		</li>
		{/if}
		{foreach from=$customlinks_links item=blocklink_link}
			{if isset($blocklink_link.$lang)}
			<li class="dib">
				<a href="{$blocklink_link.url|escape}"{if $blocklink_link.newWindow} onclick="window.open(this.href);return false;"{/if}>{$blocklink_link.$lang|escape}</a>
			</li>
			{/if}
		{/foreach}
	</ul>
</div>
<!-- /Block customlinks module -->