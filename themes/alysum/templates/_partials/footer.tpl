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
<div class="footer-before">
  <div class="row">
    <div class="page-width">
      {block name='hook_footer_before'}
        {hook h='displayFooterBefore'}
      {/block}
    </div>
  </div>
</div>
<div class="footer-container">
  <div class="row footer-main">
    <div class="page-width flex-container">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
    </div>
  </div>
  <div class="row footer-after">
    <div class="page-width">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
    </div>
  </div>
  {if (isset($pkts.footer_bottom) && $pkts.footer_bottom)}
  <div class="footer-bottom flex-container{if isset($pkts.footer_bottom_align)} move-{$pkts.footer_bottom_align}{/if}">
    <div class="page-width">
      <div class="footer_logo">{include file='_partials/logo.tpl'}</div>
      <div class="footer_disclaimer">
          <div class="footer_text">Copyright © 2018 ErGold All Rights Reserved.</div>
          <div class="footer_bottom_hook dib">{hook h='footer_bottom'}</div>
      </div>
    </div>
  </div>
  {/if}
</div>
