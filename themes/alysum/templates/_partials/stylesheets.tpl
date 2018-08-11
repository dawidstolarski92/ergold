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
{if isset($pkts) && ($pkts.used_fonts != false)}
	<link href='//fonts.googleapis.com/css?family={$pkts.used_fonts}' rel='stylesheet'>
{/if}

{foreach $stylesheets.external as $stylesheet}
	<link rel="stylesheet" href="{$stylesheet.uri}" type="text/css" media="{$stylesheet.media}">
{/foreach}

{foreach $stylesheets.inline as $stylesheet}
	<style>
	{$stylesheet.content}
	</style>
{/foreach}

<style>/* 13px - line-height | 1em = 1 ÷ 14px = 0.0714*/
{if isset($pkts.middle_bar_height)}
.header-main #desktop_cart .shopping_cart, 
.header-main .pk_customlinks .dd_container { top:{$pkts.middle_bar_height-(($pkts.middle_bar_height-14)/2)}px }
.header-main .icons-true #desktop_cart .shopping_cart, 
.header-main .icons-true .pk_customlinks .dd_container { top:{$pkts.middle_bar_height-(($pkts.middle_bar_height-19)/2)}px }
.header-main .flexmenu .submenu { top:{$pkts.middle_bar_height}px }
{/if}
{if isset($pkts.top_bar_height)}
.header-top #desktop_cart .shopping_cart, 
.header-top .pk_customlinks .dd_container { top:{$pkts.top_bar_height-(($pkts.top_bar_height-13)/2)}px }
.header-top .flexmenu .submenu { top:{$pkts.top_bar_height}px }
{/if}
</style>