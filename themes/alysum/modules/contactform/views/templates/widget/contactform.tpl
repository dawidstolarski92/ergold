{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{block name="page_title" hide}
  {l s='Customer service - Contact us'}
{/block}

<div class="row contact-page">
<section class="contact-form-map col-sm-12 col-md-6">
  <div id="pk-map" class="google-maps" data-key="{if isset($pkts.gs_google_api_key) && $pkts.gs_google_api_key != ''}{$pkts.gs_google_api_key}{else}not_defined{/if}" data-address="{$shop.address.postcode}+{$shop.address.address1}+{$shop.address.address2}+{$shop.address.city}+{$shop.address.state}+{$shop.address.country}"><svg class="svgic in_progress"><use xlink:href="#si-down"></use></svg></div>
</section>
<section class="login-form col-sm-12 col-md-6">
  <form action="{$urls.pages.contact}" method="post" {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>

    <header>
      <h3 class="h3">{l s='Send a message'}</h3>
    </header>

    {if $notifications}
      <div class="notification {if $notifications.nw_error}notification-error{else}notification-success{/if}">
        <ul>
          {foreach $notifications.messages as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </div>
    {/if}

    <section class="form-fields">

      <div class="form-group hidden">
        <select name="id_contact">
          <option value="" disabled>{l s='Subject Heading'}</option>
          {foreach from=$contact.contacts item=contact_elt}
            <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
          {/foreach}
        </select>
      </div>

      {if $contact.orders}
        <div class="form-group">
          <select name="id_order">
            <option value="">{l s='Select reference'}</option>
            {foreach from=$contact.orders item=order}
              <option value="{$order.id_order}">{$order.reference}</option>
            {/foreach}
          </select>
        </div>
      {/if}

      {if $contact.allow_file_upload}
        <div class="form-group hidden">
          <input type="file" name="fileUpload" placeholder="{l s='Attach File'}" />
        </div>
      {/if}

      <div class="form-group">
        <div class="icon-true relative">
          <input type="email" name="from" class="form-control" value="{$contact.email}" required placeholder="{l s='Email address'}" />
          <svg class="svgic input-icon"><use xlink:href="#si-email"></use></svg>
        </div>
      </div>

      <div class="form-group">
        <div class="icon-true relative">
        <textarea cols="67" rows="3" name="message" placeholder="{l s='Message'}">{if $contact.message}{$contact.message}{/if}</textarea>
        <svg class="svgic input-icon"><use xlink:href="#si-pencil"></use></svg>
        </div>
      </div>

    </section>

    <footer class="form-footer">
      <button type="submit" class="btn btn-primary" name="submitMessage">
        {l s='Send Message'}
      </button>
    </footer>

  </form>
</section>
</div>


<div class="row contact-page-footer">
  <div class="col-xs-3">
      <svg class="svgic"><use xlink:href="#si-location"></use></svg>
      <h6>Adres</h6>
      <p class='addr'>
      {$shop.address.formatted nofilter}
      </p>
  </div>
  <div class="col-xs-3">
      <svg class="svgic"><use xlink:href="#si-email"></use></svg>
      <h6>Email</h6>
      <p>
      {l s='Email'}: {$shop.email}<br>
      {$shop.registration_number}
      </p>
  </div>
  <div class="col-xs-3">
      <svg class="svgic"><use xlink:href="#si-phone"></use></svg>
      <h6>Telefon</h6>
      <p>
      {l s='Phone'}: {$shop.phone}<br>
      </p>
  </div>
  <div class="col-xs-3">
      <svg class="svgic"><use xlink:href="#si-headphones"></use></svg>
      <h6>Biuro Obsługi Klienta</h6>
      <p>Czynne od poniedziałku do piątku 8:00 - 16:00<br>Odpisujemy w ciągu 24 godzin</p>
  </div>
</div>
