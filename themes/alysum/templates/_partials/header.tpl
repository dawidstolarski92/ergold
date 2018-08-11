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
{if (isset($pkts.header_type) && is_numeric($pkts.header_type))}
	{if $pkts.header_type == 1}
		{include file="_partials/headers/header-1.tpl"}
	{elseif $pkts.header_type == 2}
		{include file="_partials/headers/header-2.tpl"}
	{elseif $pkts.header_type == 3}
		{include file="_partials/headers/header-3.tpl"}
	{elseif $pkts.header_type == 4}
		{include file="_partials/headers/header-4.tpl"}
	{elseif $pkts.header_type == 5}
		{include file="_partials/headers/header-5.tpl"}
	{else}
		{include file="_partials/headers/header-1.tpl"}
	{/if}
{else}
	{include file="_partials/headers/header-1.tpl"}
{/if}

{block name='back_to_top'}
	{include file="_partials/totop.tpl"}
{/block}