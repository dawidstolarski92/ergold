<div class="container" id="maintenance-main" data-path="{$module_dir}" data-year="{$cs.year}" data-month="{$cs.month}" data-day="{$cs.day}">
	{if $cs.countdown}
	<div class="someTimer">
		<div class="time_circles">
			<canvas height="125" width="500"></canvas>			
		</div>
	</div>
	{/if}
	{if $cs.notify}
	<div class="send_form">
		<div class="form-indent">
			<h4>{l s='Notify me when' mod='pk_themesettings'} <strong>{l s='it\'s ready' mod='pk_themesettings'}</strong></h4>
			<form action="{$module_dir}/maintenance/maintenance.php" method="post">
				<input type="email" name="cs-email" id="cs-email" class="cs-email" value="" placeholder="{l s='your@email.com' mod='pk_themesettings'}" />
				<input type="hidden" name="path" value="{$module_dir}" />
				<input type="hidden" name="mainURL" value="{$mainURL}" />
				<input type="hidden" name="token" value="" />
				<label for="cs-submit" class="cs-submit">
				    <input type="submit" class="hide-button" id="cs-submit" />
				    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="19px" height="19px" viewBox="0 0 19 19" style="enable-background:new 0 0 19 19;fill:#555" xml:space="preserve">
					<path d="M13.422,14.866l5.424-5.022c0.098-0.09,0.156-0.216,0.156-0.343c0-0.13-0.055-0.253-0.156-0.343 l-5.424-5.022c-0.207-0.191-0.539-0.191-0.746,0s-0.207,0.499,0,0.69l4.527,4.19H0.524c-0.292,0-0.526,0.218-0.526,0.488 s0.234,0.487,0.526,0.487h16.679l-4.527,4.191c-0.102,0.094-0.156,0.22-0.156,0.343s0.051,0.249,0.156,0.344 C12.883,15.054,13.219,15.054,13.422,14.866z"/>
					</svg>
				</label>
			</form>
		</div>
	</div>
	{/if}
</div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
{if $cs.countdown}
<script src="{$module_dir}/assets/js/maintenance.js"></script>
{/if}
{if $cs.notify}
<script src="{$module_dir}/assets/js/impromtu.js"></script>
{/if}
{if isset($smarty.get.message)}
	{if $smarty.get.message == "cantopenstorage"}
		<div class="prompt" data-message="{l s='Can\'t open storage file' mod='pk_themesettings'}">
	{/if}
	{if $smarty.get.message == "cantsaveemail"}
		<div class="prompt" data-message="{l s='Can\'t save your email' mod='pk_themesettings'}">
	{/if}
	{if $smarty.get.message == "success"}
		<div class="prompt" data-message="{l s='Subscription successful' mod='pk_themesettings'}">
	{/if}
	{if $smarty.get.message == "fillemail"}
		<div class="prompt" data-message="{l s='Please fill email field' mod='pk_themesettings'}">
	{/if}
{/if}