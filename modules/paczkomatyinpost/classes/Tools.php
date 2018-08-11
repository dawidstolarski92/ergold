<?php
/**
 * LICENCE
 *
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 *
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 *
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

/**
 * PHPITools
 */
class PHPITools
{

    const MODULE_NAME = 'paczkomatyinpost';
    const SEPARATOR = ';';

    public static function getDate()
    {
        return date('Y-m-d');
    }

    public static function getDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Generowanie GUID
     */
    public static function getGuid()
    {
        mt_srand((double)microtime() * 12345);
        $charid = Tools::strtoupper(md5(uniqid(rand(), true)));
        $retval = Tools::substr($charid, 0, 32);
        return $retval;
    }

    public static function getValueArray($key)
    {
        return array_filter((array)Tools::getValue($key));
    }

    /**
     * Pobiera konfigurację
     *
     * @param string|array $keys
     * @return array config key=>value
     */
    public static function getConfig($keys)
    {
        $config = array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $config[$key] = Configuration::get($key);
            }
        } else {
            $config[$keys] = Configuration::get($keys);
        }
        return $config;
    }

    /**
     * Zapisuje konfigurację z POST lub GET
     *
     * @param string|array $keys
     */
    public static function setConfig($keys)
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                Configuration::updateValue($key, Tools::getValue($key, null));
            }
        } else {
            Configuration::updateValue($keys, Tools::getValue($keys, null));
        }
    }

    public static function setConfigCarriers($carriers, $key, $service)
    {
        if (empty($carriers)) {
            Configuration::updateValue($key, null);
            self::setWarning(self::l('Nie wybrano przewoźnika dla '.$service));
            return false;
        } else {
            Configuration::updateValue($key, implode(PHPITools::SEPARATOR, array_filter($carriers)));
        }
        return true;
    }

    public static function getConfigCarriers($key, &$config = null)
    {
        $value = array_filter(explode(PHPITools::SEPARATOR, Configuration::get($key)));
        if (!is_null($config)) {
            $config[$key.'[]'] = $value;
        }
        return $value;
    }

    public static function setError($message)
    {
        if (!self::checkController()) {
            return;
        }
        Context::getContext()->controller->errors[] = '<a href="'.self::getLinkToModule(self::MODULE_NAME).'"><strong>['.self::MODULE_NAME.']</strong></a> '.$message;
    }

    public static function setSuccess($message)
    {
        if (!self::checkController()) {
            return;
        }
        Context::getContext()->controller->confirmations[] = '<a href="'.self::getLinkToModule(self::MODULE_NAME).'"><strong>['.self::MODULE_NAME.']</strong></a> '.$message.'</br>';
    }

    public static function setWarning($message)
    {
        if (!self::checkController()) {
            return;
        }
        Context::getContext()->controller->warnings[] = '<a href="'.self::getLinkToModule(self::MODULE_NAME).'"><strong>['.self::MODULE_NAME.']</strong></a> '.$message;
    }

    private static function checkController()
    {
        $available = array('AdminOrders', 'AdminModules', 'AdminPHPIadawca');

        return in_array(Context::getContext()->controller->controller_name, $available);
    }

    /**
     * Sprawdza czy przewoźnicy są tacy sami (taki sam id_reference)
     * @param int $id_carrier
     * @param int $id_carrier_reference
     * @return boolean
     */
    public static function compareCarriersById($id_carrier, $id_carrier_reference)
    {
        $carrier = new Carrier($id_carrier);
        if (Validate::isLoadedObject($carrier)) {
            return ($carrier->id_reference == $id_carrier_reference);
        }
        return false;
    }

    /**
     *
     * @param Order $order
     * @param int|array $carriers id_reference or array of id_reference
     * @return boolean
     */
    public static function checkOrderCarrier($order, $carriers)
    {
        if (is_array($carriers) && !empty($carriers)) {
            foreach ($carriers as $carrier_reference) {
                if (PHPITools::compareCarriersById($order->id_carrier, $carrier_reference)) {
                    return true;
                }
            }
        } elseif (is_numeric($carriers) && PHPITools::compareCarriersById($order->id_carrier, $carriers)) {
            return true;
        }
        return false;
    }

    /**
     *  Tłumaczenia
     */
    public static function l($string)
    {
        return Translate::getModuleTranslation(self::MODULE_NAME, $string, __FILE__);
    }

    public static function ps16()
    {
        return Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=');
    }

     public static function ps17()
    {
        return Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    public static function generateSelectSource($array, $with_labels = false)
    {
        $return = array();
        foreach ($array as $item => $value) {
            $return[] = array(
                'id' => (string)$with_labels ? $item : $value,
                'label' => str_replace('_', ' ', $value)
            );
        }
        return $return;
    }

    public static function calcWeight($value, $from = 'kg', $divide = false)
    {
        $value = str_replace(',', '.', $value);
        switch ($from) {
            case 'kg':
                return $divide ? $value / 1000 : $value * 1000;
            default: return $value;
        }
    }

    public static function calcPrice($value, $divide = false)
    {
        $value = str_replace(',', '.', $value);
        if ($divide) {
            return $value / 100;
        } else {
            return $value * 100;
        }
    }

    public static function d($object, $die = true)
    {
        echo '<pre>';
        var_dump($object);
        echo '</pre>';
        if ($die) {
            die();
        }
    }

    public static function getLinkToOrder($id_order)
    {
        $link = Context::getContext()->link;
        return $link->getAdminLink('AdminOrders').'&vieworder&id_order='.(int)$id_order;
    }

    public static function getLinkToModule($module_name)
    {
        $link = Context::getContext()->link;
        return $link->getAdminLink('AdminModules').'&configure='.$module_name;
    }

    public static function doNothing($something = null)
    {
        return $something;
    }

    public static function sendCustomerNotify($id_cart, $packcode, $link_to_status)
    {
        $id_order = Order::getOrderByCartId((int)$id_cart);
        $order = new Order((int)$id_order);
        $customer = new Customer((int)$order->id_customer);
        $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('Can\'t load Order object');
        }
        if (!Validate::isLoadedObject($customer)) {
            throw new PrestaShopException('Can\'t load Customer object');
        }
        if (!Validate::isLoadedObject($carrier)) {
            throw new PrestaShopException('Can\'t load Carrier object');
        }
        $templateVars = array(
            '{followup}' => $link_to_status,
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{id_order}' => $order->id,
            '{shipping_number}' => $packcode,
            '{order_name}' => $order->getUniqReference()
        );
        if (@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars, $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null, _PS_MAIL_DIR_, true, (int)$order->id_shop)) {
            PHPITools::setSuccess('Powiadomienie do klienta ('.$customer->firstname.' '.$customer->lastname.') z nr listu: '.$packcode.' wysłane pomyślnie');
        } else {
            PHPITools::setError('Nie udało się wysłać powiadomienia z listem przewozowym do klienta ('.$customer->firstname.' '.$customer->lastname.') z nr listu: '.$packcode);
        }
    }

    public static function changeOrderState($id_cart, $id_order_state, $status_link)
    {
        $id_order = Order::getOrderByCartId((int)$id_cart);
        $context = Context::getContext();
        $order_state = new OrderState($id_order_state, $context->language->id);
        if (!Validate::isLoadedObject($order_state)) {
            PHPITools::setError(sprintf('Status zamówienia #%d nie istnieje', $id_order_state));
        } else {
            $order = new Order((int)$id_order);
            if (!Validate::isLoadedObject($order)) {
                PHPITools::setError(sprintf(Tools::displayError('Zamówienie #%d nie istnieje'), $id_order));
            } else {
                $current_order_state = $order->getCurrentOrderState();
                if ($current_order_state->id == $order_state->id) {
                    PHPITools::setError(sprintf('Zamówienie #%d ma już taki status.', $id_order));
                } else {
                    $history = new OrderHistory();
                    $history->id_order = $order->id;
                    $history->id_employee = (int)$context->employee->id;

                    $use_existings_payment = !$order->hasInvoice();
                    $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                    $templateVars = array();
                    if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $status_link) {
                        $templateVars = array('{followup}' => $status_link);
                    }
                    if ($history->addWithemail(true, $templateVars)) {
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            foreach ($order->getProducts() as $product) {
                                if (StockAvailable::dependsOnStock($product['product_id'])) {
                                    StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                }
                            }
                        }
                        PHPITools::setSuccess(sprintf('Status zamówienia #%d zmienił się na %s', $id_order, $order_state->name));
                    } else {
                        PHPITools::setError(sprintf(Tools::displayError('Nie udało się zmienić statusu dla zamówienia #%d.'), $id_order));
                    }
                }
            }
        }
    }
}
