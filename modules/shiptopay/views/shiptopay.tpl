{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2017 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-info"></i> {l s='Important information' mod='shiptopay'}</div>
    <div class="alert alert-info">
        {l s='To reduce features redundancy and optimize shop performance - this Ship To Pay module allows to define payments for virtual products only'}<br/>
        {l s='It is because you use PrestaShop 1.7 - this version allows to define associations between standard (non-virtual) carriers and payments under section: ' mod='shiptopay'}
        <a href="{Context::getContext()->link->getAdminLink('AdminPaymentPreferences')}">{l s='Payments > preferences' mod='shiptopay'}</a>.
    </div>
</div>