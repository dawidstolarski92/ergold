{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_content'}
<div class="authentication-page row">

  <div class="register-section col-xs-12 col-sm-6">
    <h2>{l s='New customers' d='Shop.Theme.CustomerAccount'}</h2>
    <div class="section-content">
      <p>
      {l s='By creating an account with our store, you will be able to move through the checkout process faster, store multiple shipping addresses, view and track your orders in your account and more.' d='Shop.Theme.CustomerAccount'}
      </p>
      <a class="btn btn-primary" href="{$urls.pages.register}" data-link-action="display-register-form">
          {l s='Create an account' d='Shop.Theme.CustomerAccount'}
      </a>
    </div>
  </div>

  <div class="login-section col-xs-12 col-sm-6">

    <h2>{l s='Log in to your account' d='Shop.Theme.CustomerAccount'}</h2>

    {block name='login_form_container'}
      <section class="login-form">
        {render file='customer/_partials/login-form.tpl' ui=$login_form}
      </section>

      {block name='display_after_login_form'}
        {hook h='displayCustomerLoginFormAfter'}
      {/block}

    {/block}

  </div>

</div>
{/block}