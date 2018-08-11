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

{block name='page_header_container'}{/block}

{block name='left_column'}
  <div id="left-column" class="col-xs-12 col-sm-3">
   {** {widget name="ps_contactinfo" hook='displayLeftColumn'} *}
	<div class="block-contact">
  <h4>Informacja o sklepie</h4>
  ERgold Eryk Łapiński <br>
  ul. Brylantowa 11,<br>
  15-166 Białystok<br>
  woj. podlaskie, Polska<br>
  NIP: 9661395939<br>
  REGON: 052226985<br><br>
    
    Zadzwoń do nas: <span>602 817 791</span>
          <br>
    
    Napisz do nas: <span>biuro@ergold.pl</span>
  </div>
  </div>
{/block}

{block name='page_content'}
  {widget name="contactform"}
{/block}

