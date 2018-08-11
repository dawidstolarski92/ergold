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
 *
 * 1. top_slider
 * 2. content_top
 * 3. displayTopColumn
 * 4. displayHome
 * 5. content_bottom
 * 6. footer_twitter
 *
 *}
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    {block name='svg_graphics'}
      {include file="_partials/svg.tpl"}
    {/block}

    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <div class="{if isset($pkts) && isset($pkts.pattern)}back_{$pkts.pattern}{/if}" id="pattern">

      {block name='header'}
        {include file='_partials/header.tpl'}
      {/block}

      <section class="main-contant-wrapper clearfix" data-location="/templates/layouts/layout-both-columns.tpl">

        {if ($page.page_name == 'index')}
        {block name='top_slider'}
          {hook h='top_slider'}
        {/block}
        {/if}

        {if ($page.page_name == 'index')}
          {block name='displayTopColumn'}
          <div class="hook-displayTopColumn hook-container displayTopColumn">{hook h='displayTopColumn'}</div>
          {/block}
        {/if}

        <div class="page-width top-content">

          {block name='notifications'}
            {include file='_partials/notifications.tpl'}
          {/block}

          {block name='breadcrumb'}
            {include file='_partials/breadcrumb.tpl'}
          {/block}

        </div>

        {block name='content_top'}
        <div class="hook-content_top hook-container wide">{hook h='content_top'}</div>
        {/block}

        <div class="page-width main-content">

          <div id="wrapper" class="clearfix">

            {block name='left_column'}
              <div id="left-column" class="sidebar col-xs-12 col-sm-4 col-md-3">
                {if $page.page_name == 'product'}
                  {hook h='displayLeftColumnProduct'}
                {else}
                  {hook h="displayLeftColumn"}
                {/if}
              </div>
            {/block}

            {block name='right_column'}
              <div id="right-column" class="sidebar col-xs-12 col-sm-4 col-md-3">
                {if $page.page_name == 'product'}
                  {hook h='displayRightColumnProduct'}
                {else}
                  {hook h="displayRightColumn"}
                {/if}
              </div>
            {/block}

            {block name='content_wrapper'}
              <div id="content-wrapper" class="wide left-column right-column">
                {block name='content'}
                  <p>Hello world! This is HTML5 Boilerplate.</p>
                {/block}
              </div>
            {/block}

          </div>

        </div>

        {block name='content_bottom'}
        <div class="hook-content_bottom hook-container wide">{hook h='content_bottom'}</div>
        {/block}

        {block name='hook_footer_twitter'}
          {hook h='footer_twitter'}
        {/block}

      </section>

      <footer id="footer" class="relative">
        {block name='footer'}
          {include file='_partials/footer.tpl'}
        {/block}
      </footer>

      {block name='javascript_bottom'}
        {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
      {/block}



      {block name='hook_before_body_closing_tag'}
        {hook h='displayBeforeBodyClosingTag'}
      {/block}

    </div>
    <script type="text/javascript" src="//ergold.pl/themes/alysum/assets/js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="//ergold.pl/themes/alysum/assets/js/owl.js"></script>
      <script type="text/javascript" src='//ergold.pl/js/rodo.js'></script>

      <script>
      (function ($) {
          $(document).ready(function(){
              $('.blockcart.cart-preview, .dd_container2').mouseenter(function(){
                  $('.dd_container2').addClass('active');
              });

              $('.blockcart.cart-preview, .dd_container2').mouseout(function(){
                  setTimeout(function(){
                      $('.dd_container2').removeClass('active');
                  }, 500);
              });

          });
      })(jQuery);
      </script>
  </body>

</html>
