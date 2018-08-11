<div class="header_user">
  <ul>        
    <li class="header_user_info">
      <svg class="svgic main_color svgic-login"><use xlink:href="#si-login"></use></svg>
      {if $logged}
        <a href="{$my_account_url}" class="account main_color_hvr main_color" rel="nofollow">{$customer.firstname} {$customer.lastname}</a>
        <a href="{$logout_url}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout main_color_hvr" rel="nofollow">{l s='Sign out' d='Shop.Theme.CustomerAccount'}</a>
      {else}
        <a href="{$my_account_url}" class="login main_color_hvr" rel="nofollow">{l s='Sign in' d='Shop.Theme.CustomerAccount'}</a> {l s='or' d='Shop.Theme'} <a href="{$urls.pages.register}" class="login main_color_hvr">{l s='Register' d='Shop.Theme.CustomerAccount'}</a>
      {/if}
    </li>
  </ul>
</div>