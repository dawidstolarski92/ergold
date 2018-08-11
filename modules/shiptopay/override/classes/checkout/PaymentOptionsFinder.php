<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2017 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */

class PaymentOptionsFinder extends PaymentOptionsFinderCore
{
    public function find() //getPaymentOptions()
    {
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = parent::find();
        if (!is_array($rawDisplayPaymentEUOptions)) {
            $rawDisplayPaymentEUOptions = array();
        }
        $displayPaymentEUOptions = array_map(
            array('PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'),
            $rawDisplayPaymentEUOptions
        );
        $this->hookName = 'advancedPaymentOptions';
        $advancedPaymentOptions = parent::find();
        if (!is_array($advancedPaymentOptions)) {
            $advancedPaymentOptions = array();
        }
        $this->hookName = 'paymentOptions';
        $this->expectedInstanceClasses = array('PrestaShop\PrestaShop\Core\Payment\PaymentOption');
        $newOption = parent::find();
        if (!is_array($newOption)) {
            $newOption = array();
        }

        $paymentOptions = array_merge($displayPaymentEUOptions, $advancedPaymentOptions, $newOption);

        $payments = array();
        if (Context::getContext()->cart->isVirtualCart()==1) {
            foreach ($paymentOptions as $key => $p_option) {
                $sql = new DbQuery();
                $sql->select('stp.`id_payment`');
                $sql->from('shiptopay', 'stp');
                foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) AS $pm => $p) {
                    foreach (PaymentModule::getInstalledPaymentModules() AS $pmd => $pd) {
                        if ($pd['name']== $key && $p['id_payment'] == $pd['id_module']) {
                            $payments[] = $paymentOptions[$key];
                        }
                    }
                }
            }
            $paymentOptions = $payments;
        }

        foreach ($paymentOptions as $paymentOptionKey => $paymentOption) {
            if (!is_array($paymentOption)) {
                unset($paymentOptions[$paymentOptionKey]);
            }
        }
        return $paymentOptions;
    }
}