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
{extends file='layouts/layout-error.tpl'}

{block name='content'}

  <section id="main">

      <img src="http://ergold.pl/img/ergold-logo-1499850853.jpg" alt="ERgolg" /><br><br><br>

      <div id="message">

        <div class="title">
          <h1>{block name='page_title'}{l s='Our website is almost ready.' d='Shop.Theme'}{/block}</h1>

          {block name='page_content_container'}
            <section id="content" class="page-content page-maintenance">
              {block name='page_content'}
                {* {$maintenance_text nofilter} *}
              {/block}
              <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fergoldpl&tabs&width=500&height=130&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=false&appId" width="500" height="130" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
            </section>
          {/block}
        </div>

        {block name='hook_comingsoon'}
          {hook h='comingsoon'}
        {/block}

        {block name='hook_maintenance'}
          {$HOOK_MAINTENANCE nofilter}
        {/block}

      </div>

  </section>

{/block}
