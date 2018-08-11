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
if (!defined('_PS_VERSION_'))
    exit;

require_once dirname(__FILE__).'/classes/Tools.php';
require_once dirname(__FILE__).'/classes/HelperForm.php';

require_once dirname(__FILE__).'/classes/PaczkomatyInpostModel.php';
require_once dirname(__FILE__).'/classes/PaczkomatyInpostData.php';
require_once dirname(__FILE__).'/classes/PaczkomatyInpostApi.php';

class PaczkomatyInpost extends Module
{

    public $api;
    protected $config_form = false;
    private $ps16;

    const TEST_EMAIL = 'test@testowy.pl';
    const TEST_PASSWORD = 'WqJevQy*X7';
    const SEPARATOR = ',';
    const KEY_EMAIL = 'PACZKOMATYINPOST_EMAIL';
    const KEY_PASSWORD = 'PACZKOMATYINPOST_PASSWORD';
    const KEY_CARRIER_ANY = 'PACZKOMATYINPOST_CARRIER_ANY';
    const KEY_CARRIER = 'PACZKOMATYINPOST_CARRIER';
    const KEY_CARRIER_COD = 'PACZKOMATYINPOST_CARRIER_COD';
    const KEY_DEFAULT_PACKTYPE = 'PACZKOMATYINPOST_D_PACKTYPE';
    const KEY_DEFAULT_INSURANCE = 'PACZKOMATYINPOST_D_INSURANCE';
    const KEY_DEFAULT_REFERENCE = 'PACZKOMATYINPOST_D_REFERENCE';
    const KEY_DEFAULT_SELF_SEND = 'PACZKOMATYINPOST_D_SELF_SEND';
    const KEY_DEFAULT_SENDING_MACHINE = 'PACZKOMATYINPOST_D_S_MACHINE';
    const KEY_DEFAULT_DISPATCH_POINT = 'PACZKOMATYINPOST_D_DISPATCHPOINT';
    const KEY_PRINT_TYPE = 'PACZKOMATYINPOST_PRINT_TYPE';
    const KEY_PRINT_FORMAT = 'PACZKOMATYINPOST_PRINT_FORMAT';

    /* Czy o pobraniu ma decydować sposób płatności? */
    const KEY_COD_BY_PAYMENT = 'PACZKOMATYINPOST_CODBYPAYMENT';
    const KEY_COD_PAYMENTS = 'PACZKOMATYINPOST_CODPAYMENTS';

    /* Kiedy wysyłać powiadomienie do klienta */
    const KEY_CUSTOMER_NOTIFY = 'PACZKOMATYINPOST_CUSTOMERNOTIFY';
    const OPTION_CUSTOMER_NOTIFY_NEVER = 0;
    const OPTION_CUSTOMER_NOTIFY_PREPARE = 1;
    const OPTION_CUSTOMER_NOTIFY_PAID = 2;

    /* Zmiana statusu zamówienia */
    const KEY_STATUS_PREPARE_UPDATE = 'PACZKOMATYINPOST_STATUSPREPAREU';
    const KEY_STATUS_PREPARE = 'PACZKOMATYINPOST_STATUSPREPARE';
    const KEY_STATUS_PAID_UPDATE = 'PACZKOMATYINPOST_STATUSPAIDU';
    const KEY_STATUS_PAID = 'PACZKOMATYINPOST_STATUSPAID';
    const FIELD_RECEIVER_EMAIL = 'PACZKOMATYINPOST_RECEIVER_EMAIL';
    const FIELD_RECEIVER_MOBILE = 'PACZKOMATYINPOST_RECEIVER_MOBILE';
    const FIELD_RECEIVER_MACHINE = 'PACZKOMATYINPOST_RECEIVER_MACHINE';
    const FIELD_PACKTYPE = 'PACZKOMATYINPOST_PACKTYPE';
    const FIELD_SENDER_MACHINE = 'PACZKOMATYINPOST_SENDER_MACHINE';
    const FIELD_INSURANCE = 'PACZKOMATYINPOST_INSURANCE';
    const FIELD_COD = 'PACZKOMATYINPOST_COD';
    const FIELD_COD_VALUE = 'PACZKOMATYINPOST_COD_VALUE';

    /** Sposób nadania */
    const FIELD_SELF_SEND = 'PACZKOMATYINPOST_SELF_SEND';
    /*
     * Odbiór przez kuriera
     */
    const SEND_TYPE_COURIER = 0;
    /*
     * Nadanie w paczkomacie
     */
    const SEND_TYPE_PACZKOMAT = 1;
    /*
     * Nadanie w POK
     */
    const SEND_TYPE_POK = 2;
    const FIELD_REFERENCE_NUMBER = 'PACZKOMATYINPOST_REFERENCE_NUMBER';
    const FIELD_CALCULATED_PRICE = 'PACZKOMATYINPOST_CALCULATED_PRICE';
    const SUBMIT_PREPARE = 'PACZKOMATYINPOST_PREPARE';
    const SUBMIT_CHANGE = 'PACZKOMATYINPOST_CHANGE';
    const SUBMIT_SEND = 'PACZKOMATYINPOST_SEND';
    const SUBMIT_DELETE = 'PACZKOMATYINPOST_DELETE';
    const SUBMIT_INFORM = 'PACZKOMATYINPOST_INFORM';
    const SUBMIT_PRINT_LABEL = 'PACZKOMATYINPOST_PRINT_LABEL';
    const SUBMIT_PRINT_CONFIRM = 'PACZKOMATYINPOST_PRINT_CONFIRM';
    const SUBMIT_PRINT_DISPATCH_ORDER = 'PACZKOMATYINPOST_PRINT_DISPATCH_ORDER';
    const SUBMIT_PRINT_SETTINGS = 'PACZKOMATYINPOST_PRINT_SETTINGS';
    const SUBMIT_SAVE_EXTRA = 'PACZKOMATYINPOST_SAVE_EXTRA';
    const TEMPLATE_ORDER_ID = '{order_id}';
    const TEMPLATE_ORDER_REFERENCE = '{order_reference}';

    public function __construct()
    {
        $this->name = 'paczkomatyinpost';
        $this->tab = 'shipping_logistics';
        $this->version = '1.7.0';
        $this->author = 'prestaHelp.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Integracja sklepu z serwisem Paczkomaty.pl');
        $this->description = $this->l('Nadawanie paczek nigdy nie było tak proste!');
        Configuration::deleteByName('PACZKOMATYINPOST_UPDATE');

        $this->confirmUninstall = $this->l('Odinstalowanie modułu nie powoduje utraty żadnych danych.');
        $this->ps16 = Tools::version_compare('1.6', _PS_VERSION_, '<=');
        $this->api = new PaczkomatyInpostModel($this, Configuration::get(self::KEY_EMAIL), Configuration::get(self::KEY_PASSWORD));
    }

    public function install()
    {
        Configuration::updateValue('PACZKOMATYINPOST_VERSION', $this->version);
        Configuration::updateValue(self::KEY_CARRIER_ANY, 1);

        if (!Configuration::get(self::KEY_EMAIL))
            Configuration::updateValue(self::KEY_EMAIL, self::TEST_EMAIL);
        if (!Configuration::get(self::KEY_PASSWORD))
            Configuration::updateValue(self::KEY_PASSWORD, self::TEST_PASSWORD);

        include(dirname(__FILE__).'/sql/install.php');

        $test = parent::install() && $this->installTab() && $this->registerHook('displayAdminOrder');
        return $test && $this->registerHook('displayHeader') && $this->registerHook('displayBeforeCarrier') && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PACZKOMATYINPOST_VERSION');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall() && $this->uninstallTab();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = $this->privatePostProcess();

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'module_name' => $this->name,
            'module_version' => $this->version,
            'ps16' => $this->ps16,
            'update_url' => 'http://manager.tomaszdacka.pl/module/update-status/'.$this->name.'/'.$this->version
        ));

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPaczkomatyinpostModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigForm());
    }

    protected function getConfigForm()
    {
        /** Ustawienia konta */
        $forms = array();
        $forms[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Ustawienia konta'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-envelope"></i>',
                    'desc' => $this->l('Twój login'),
                    'name' => 'PACZKOMATYINPOST_EMAIL',
                    'label' => $this->l('Login'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'PACZKOMATYINPOST_PASSWORD',
                    'prefix' => '<i class="icon icon-key"></i>',
                    'desc' => $this->l('Twoje hasło'),
                    'label' => $this->l('Hasło'),
                ),
                PHPIHelperForm::radioSwitch(true, 'PACZKOMATYINPOST_TEST', $this->l('Konto testowe')),
                array(
                    'type' => 'buttons',
                    'name' => 'buttons',
                    'label' => '',
                    'list' => array(
                        array(
                            'title' => $this->l('Zapisz dane'),
                            'name' => 'save_account',
                            'class' => ($this->ps16) ? 'btn btn-success' : 'button',
                        ),
//                        array(
//                            'title' => $this->l('Użyj danych testowych'),
//                            'name' => 'use_testdata',
//                            'class' => ($this->ps16) ? 'btn btn-warning' : 'button',
//                        )
                    )
                )
            ),
        );
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, ALL_CARRIERS);
        $carriers_count = count($carriers);
        $description = 'Tutaj możesz przypisać dowolnego przewoźnika do danej usługi.<br/>';
        $description .= 'Przypisanie takiego samego przewoźnika do dwóch usług jest niedozwolone.<br/>';
        $description .= 'Ustawienie przewoźnika dla paczek za pobraniem będzie jedynie skutkowało automatycznym';
        $description .= ' wyborem przesyłki za pobraniem podczas generowania listu przewozowego.';
        $carrier_any_desc = 'Wyłączenie tej opcji pozwoli Ci generować listy przewozowe na paczkomaty,';
        $carrier_any_desc .= ' dla zamówień z innym przewoźnikiem niż wybrane powyżej. ';
        $carrier_any_desc .= 'Np. gdy klient zmienił zdanie i nie chce wysyłki kurierem, a woli paczkomat.';
        /** Ustawienia przewoźników * */
        $forms[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Ustawienia przewoźników'),
                'icon' => 'icon-cogs',
            ),
            'description' => $description,
            'input' => array(
                array(
                    'type' => $this->ps16 ? 'select16' : 'select',
                    'multiple' => 'true',
                    'size' => $carriers_count,
                    'desc' => $this->l('Moduł będzie działał tylko dla wybranych przewoźników. Możesz wybrać kilku trzymając klawisz CTRL'),
                    'name' => self::KEY_CARRIER.'[]',
                    'label' => $this->l('Przewoźnicy dla paczek zwykłych'),
                    'options' => array(
                        'query' => $carriers,
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => $this->ps16 ? 'select16' : 'select',
                    'multiple' => 'true',
                    'prefix' => '<i class="icon icon-envelope"></i>',
                    'size' => $carriers_count,
                    'desc' => $this->l('Moduł będzie działał tylko dla wybranych przewoźników. Możesz wybrać kilku trzymając klawisz CTRL'),
                    'name' => self::KEY_CARRIER_COD.'[]',
                    'label' => $this->l('Przewoźnicy dla paczek pobraniowych'),
                    'options' => array(
                        'query' => $carriers,
                        'id' => 'id_carrier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => ($this->ps16 ? 'switch' : 'radio'),
                    'size' => 75,
                    'is_bool' => true,
                    'desc' => $this->l($carrier_any_desc),
                    'name' => self::KEY_CARRIER_ANY,
                    'label' => $this->l('Sprawdzaj przewoźników w podglądzie zamówienia'),
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => self::KEY_CARRIER_ANY.'_on',
                            'value' => 1,
                            'label' => $this->l('Tak')
                        ),
                        array(
                            'id' => self::KEY_CARRIER_ANY.'_off',
                            'value' => 0,
                            'label' => $this->l('Nie')
                        )
                    ),
                ),
                array(
                    'type' => 'buttons',
                    'name' => 'buttons',
                    'label' => '',
                    'list' => array(
                        array(
                            'title' => $this->l('Zapisz ustawienia przewoźników'),
                            'name' => 'save_carriers',
                            'class' => ($this->ps16) ? 'btn btn-success' : 'button',
                        ),
                    )
                )
            ),
        );
        $insurances = $this->api->getInsurances();
        $packtypes = $this->api->getPackTypes();
        $list_machines = $this->api->getListMachines();
        $list_dispatch_points = array(
            array(
                'name' => '',
                'label' => '-- Wybierz punkt odbioru --'
            )
        );
        $points = $this->api->getDispatchPoints();
        if ($points)
            $list_dispatch_points = array_merge($list_dispatch_points, $points);
        else
            $this->catchErrors(false);

        if (!$insurances || !$packtypes || !$list_machines)
            $this->catchErrors();
        else {
            $description = 'Poniżej możesz dowolnie zdefiniować domyślne ustawienia modułu.';
            $description .= 'Skróci to czas wyboru opcji podczas generowania listów przewozowych.';
            $default_reference_desc = 'Domyślna wartość numeru referencyjnego. Dostępne szablony: ';
            $default_reference_desc .= self::TEMPLATE_ORDER_ID.' - numer id zamówienia, '.self::TEMPLATE_ORDER_REFERENCE.' - indeks zamówienia, {product_list} - lista produktów w formacie (indeks, atrybut, ilosc), kolejne produkty po przecinku.';
            $default_self_send_desc = 'Wybierz czy paczki będą nadawane w paczkomacie, lub odbierane przez kuriera. ';
//            $default_self_send_desc .= ', lub nadawane w dowolnym POK (Punkcie obsługi klienta).';
            $default_self_send_desc .= 'Odbiór przez kuriera jest dodatkowo płatny jeśli ilość paczek jest mniejsza niż 5!';
            /** Ustawienia domyślne dla paczek * */
            $forms[]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Ustawienia domyślne dla wszystkich paczek'),
                    'icon' => 'icon-cogs',
                ),
                'description' => $description,
                'input' => array(
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Domyślny typ paczki'),
                        'name' => self::KEY_DEFAULT_PACKTYPE,
                        'label' => $this->l('Typ paczki'),
                        'options' => array(
                            'query' => $packtypes,
                            'id' => 'type',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Domyślna wartość ubezpieczenia'),
                        'name' => self::KEY_DEFAULT_INSURANCE,
                        'label' => $this->l('Ubezpieczenie'),
                        'options' => array(
                            'query' => $insurances,
                            'id' => 'limit',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'size' => 75,
                        'desc' => $this->l($default_reference_desc),
                        'name' => self::KEY_DEFAULT_REFERENCE,
                        'label' => $this->l('Numer referencyjny'),
                    ),
                    array(
                        'type' => 'radio',
                        'size' => 75,
                        'is_bool' => true,
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::KEY_DEFAULT_SELF_SEND,
                        'label' => $this->l('Sposób nadania'),
                        'desc' => $this->l($default_self_send_desc),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => self::KEY_DEFAULT_SELF_SEND.'_courier',
                                'value' => self::SEND_TYPE_COURIER,
                                'label' => $this->l('Odbiór przez kuriera')
                            ),
                            array(
                                'id' => self::KEY_DEFAULT_SELF_SEND.'_paczkomat',
                                'value' => self::SEND_TYPE_PACZKOMAT,
                                'label' => $this->l('W paczkomacie')
                            ),
//                            array(
//                                'id' => self::KEY_DEFAULT_SELF_SEND.'_pok',
//                                'value' => self::SEND_TYPE_POK,
//                                'label' => $this->l('W dowolnym POK')
//                            ),
                        ),
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Domyślny paczkomat nadawczy'),
                        'name' => self::KEY_DEFAULT_SENDING_MACHINE,
                        'label' => $this->l('Paczkomat nadawczy'),
                        'options' => array(
                            'query' => $list_machines,
                            'id' => 'name',
                            'name' => 'label'
                        )
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Domyślny punkt odbioru dla paczek odbieranych przez kuriera'),
                        'name' => self::KEY_DEFAULT_DISPATCH_POINT,
                        'label' => $this->l('Punkt odbioru dla kuriera'),
                        'options' => array(
                            'query' => $list_dispatch_points,
                            'id' => 'name',
                            'name' => 'label'
                        )
                    ),
                    array(
                        'type' => 'buttons',
                        'name' => 'buttons',
                        'label' => '',
                        'list' => array(
                            array(
                                'title' => $this->l('Zapisz domyślne ustawienia'),
                                'name' => 'save_default',
                                'class' => ($this->ps16) ? 'btn btn-success' : 'button',
                            ),
                        )
                    )
                ),
            );
        }

        /** Opcje wydruku * */
        $forms[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Ustawienia wydruku'),
                'icon' => 'icon-cogs',
            ),
            'description' => 'Poniższe ustawienia będą brane pod uwagę podczas drukowania etykiet',
            'input' => array(
                array(
                    'type' => $this->ps16 ? 'select16' : 'select',
                    'prefix' => '<i class="icon icon-envelope"></i>',
                    'name' => self::KEY_PRINT_TYPE,
                    'label' => $this->l('Typ etykiety'),
                    'options' => array(
                        'query' => array(
                            array(
                                'type' => 'A4',
                                'name' => 'A4 - etykieta standardowa – do 3 szt na stronie A4'
                            ),
                            array(
                                'type' => 'A6P',
                                'name' => 'A6P – etykieta A6 w orientacji pionowej'
                            )
                        ),
                        'id' => 'type',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => $this->ps16 ? 'select16' : 'select',
                    'prefix' => '<i class="icon icon-envelope"></i>',
                    'name' => self::KEY_PRINT_FORMAT,
                    'label' => $this->l('Format etykiety'),
                    'options' => array(
                        'query' => array(
                            array(
                                'type' => 'Pdf',
                                'name' => 'Pdf - etykieta w formacie PDF'
                            ),
                            array(
                                'type' => 'Epl2',
                                'name' => 'Epl2 – etykieta w formacie EPL2'
                            )
                        ),
                        'id' => 'type',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'buttons',
                    'name' => 'buttons',
                    'label' => '',
                    'list' => array(
                        array(
                            'title' => $this->l('Zapisz ustawienia wydruku'),
                            'name' => self::SUBMIT_PRINT_SETTINGS,
                            'class' => ($this->ps16) ? 'btn btn-success' : 'button',
                        ),
                    )
                )
            ),
        );

        /** Ustawienia dodatkowe */
        $forms[]['form'] = $this->getFormExtra();

        return $forms;
    }

    private function getFormExtra()
    {
        $payments_installed = PaymentModule::getInstalledPaymentModules();
        $payments = array();

        foreach ($payments_installed as $payment) {
            $payments[] = array(
                'id' => $payment['name'],
                'label' => Module::getModuleName($payment['name'])
            );
        }

        $order_statuses = OrderState::getOrderStates((int)$this->context->language->id);

        foreach ($order_statuses as $status) {
            $statuses[$status['id_order_state']] = $status['name'];
        }

        $statuses = PHPITools::generateSelectSource($statuses, true);

        return array(
            'legend' => array(
                'title' => $this->l('Ustawienia dodatkowe'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                PHPIHelperForm::block(true, $this->l('Pobranie'), true),
                PHPIHelperForm::radioSwitch(true, self::KEY_COD_BY_PAYMENT, $this->l('Czy o pobraniu ma decydować sposób płatności?')),
                PHPIHelperForm::checkbox(true, self::KEY_COD_PAYMENTS, $this->l('Sposób płatności dla pobrań'), $payments),
                PHPIHelperForm::block(false),
                PHPIHelperForm::block(true, $this->l('Powiadomienia email'), true),
                PHPIHelperForm::radio(true, self::KEY_CUSTOMER_NOTIFY, $this->l('Wysyłaj email do klienta z listem przewozowym'), $this->getCustomerNotifyOptions()),
                PHPIHelperForm::block(false),
                PHPIHelperForm::block(true, $this->l('Opcje zamówienia'), true),
                PHPIHelperForm::radioSwitch(true, self::KEY_STATUS_PREPARE_UPDATE, $this->l('Czy status zamówienia ma być zmieniony po wygenerowaniu przesyłki?')),
                PHPIHelperForm::selectChosen(true, self::KEY_STATUS_PREPARE, $this->l('Status zamówienia dla przygotowanych przesyłek (niewysłanych do urzędu)'), $statuses),
                PHPIHelperForm::radioSwitch(true, self::KEY_STATUS_PAID_UPDATE, $this->l('Czy status zamówienia ma być zmieniony po wysłaniu przesyłki do urzędu?')),
                PHPIHelperForm::selectChosen(true, self::KEY_STATUS_PAID, $this->l('Status zamówienia dla wysłanych do urzędu przesyłek'), $statuses),
                PHPIHelperForm::block(false),
                PHPIHelperForm::buttons(array(
                    self::SUBMIT_SAVE_EXTRA => array(
                        'title' => PHPITools::l('Zapisz ustawienia dodatkowe'),
                        'class' => (PHPITools::ps16()) ? 'btn btn-success' : 'button'
                    ),
                )),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        $config = array(
            self::KEY_EMAIL => Configuration::get(self::KEY_EMAIL),
            self::KEY_PASSWORD => Configuration::get(self::KEY_PASSWORD),
            'PACZKOMATYINPOST_TEST' => Configuration::get('PACZKOMATYINPOST_TEST'),
            self::KEY_CARRIER_ANY => Configuration::get(self::KEY_CARRIER_ANY),
            self::KEY_CARRIER.'[]' => explode(self::SEPARATOR, Configuration::get(self::KEY_CARRIER)),
            self::KEY_CARRIER_COD.'[]' => explode(self::SEPARATOR, Configuration::get(self::KEY_CARRIER_COD)),
            self::KEY_DEFAULT_PACKTYPE => Configuration::get(self::KEY_DEFAULT_PACKTYPE),
            self::KEY_DEFAULT_INSURANCE => Configuration::get(self::KEY_DEFAULT_INSURANCE),
            self::KEY_DEFAULT_REFERENCE => Configuration::get(self::KEY_DEFAULT_REFERENCE),
            self::KEY_DEFAULT_SELF_SEND => Configuration::get(self::KEY_DEFAULT_SELF_SEND),
            self::KEY_DEFAULT_SENDING_MACHINE => Configuration::get(self::KEY_DEFAULT_SENDING_MACHINE),
            self::KEY_PRINT_FORMAT => Configuration::get(self::KEY_PRINT_FORMAT),
            self::KEY_PRINT_TYPE => Configuration::get(self::KEY_PRINT_TYPE),
            self::KEY_DEFAULT_DISPATCH_POINT => Configuration::get(self::KEY_DEFAULT_DISPATCH_POINT),
            self::KEY_COD_BY_PAYMENT => Configuration::get(self::KEY_COD_BY_PAYMENT),
            self::KEY_COD_PAYMENTS => Configuration::get(self::KEY_COD_PAYMENTS),
            self::KEY_CUSTOMER_NOTIFY => Configuration::get(self::KEY_CUSTOMER_NOTIFY),
            self::KEY_STATUS_PREPARE_UPDATE => Configuration::get(self::KEY_STATUS_PREPARE_UPDATE),
            self::KEY_STATUS_PREPARE => Configuration::get(self::KEY_STATUS_PREPARE),
            self::KEY_STATUS_PAID_UPDATE => Configuration::get(self::KEY_STATUS_PAID_UPDATE),
            self::KEY_STATUS_PAID => Configuration::get(self::KEY_STATUS_PAID),
        );
        $payments = explode(',', Configuration::get(self::KEY_COD_PAYMENTS));
        foreach ($payments as $payment) {
            $config[self::KEY_COD_PAYMENTS.'_'.$payment] = true;
        }
        return $config;
    }

    /**
     * Save form data.
     */
    protected function privatePostProcess()
    {
        /* Zapisanie danych konta (email + haslo) */
        if (Tools::isSubmit('save_account')) {
            Configuration::updateValue(self::KEY_EMAIL, Tools::getValue(self::KEY_EMAIL));
            Configuration::updateValue(self::KEY_PASSWORD, Tools::getValue(self::KEY_PASSWORD));
            Configuration::updateValue('PACZKOMATYINPOST_TEST', Tools::getValue('PACZKOMATYINPOST_TEST'));
            return $this->displayConfirmation('Dane zostały zapisane');
        }
        /* Użycie danych testowych */
        if (Tools::isSubmit('use_testdata')) {
            Configuration::updateValue(self::KEY_EMAIL, self::TEST_EMAIL);
            Configuration::updateValue(self::KEY_PASSWORD, self::TEST_PASSWORD);
            return $this->displayConfirmation('Użyto danych testowych');
        }
        /* Zapisanie przewoźników */
        if (Tools::isSubmit('save_carriers')) {
            Configuration::updateValue(self::KEY_CARRIER_ANY, Tools::getValue(self::KEY_CARRIER_ANY));
            $carriers = Tools::getValue(self::KEY_CARRIER);
            $carriers_cod = Tools::getValue(self::KEY_CARRIER_COD);
            if (!is_array($carriers)) {
                $carriers = array();
            }
            if (!is_array($carriers_cod)) {
                $carriers_cod = array();
            }
            $intersect = array_intersect($carriers, $carriers_cod);
            $unique_carriers = empty($intersect);
            $error = 'Wybrano tego samego przewoźnika dla paczek zwykłych i pobraniowych.';
            $error .= ' Taka operacja jest niedozwolona. Każdy typ paczki powinien mieć unikalnego przewoźnika.';
            if (!$unique_carriers)
                return $this->displayError($error);
            if (empty($carriers)) {
                Configuration::updateValue(self::KEY_CARRIER, null);
                return $this->displayError('Nie wybrano przewoźników dla paczek zwykłych.');
            } else
                Configuration::updateValue(self::KEY_CARRIER, implode(self::SEPARATOR, array_filter($carriers)));

            if (empty($carriers_cod)) {
                Configuration::updateValue(self::KEY_CARRIER_COD, null);
                $this->context->controller->warnings[] = 'Nie wybrano przewoźników dla paczek pobraniowych';
                return false;
            } else
                Configuration::updateValue(self::KEY_CARRIER_COD, implode(self::SEPARATOR, array_filter($carriers_cod)));

            return $this->displayConfirmation('Lista przewoźników została zapisana');
        }
        /* Zapisanie domyślnych ustawień */
        if (Tools::isSubmit('save_default')) {
            Configuration::updateValue(self::KEY_DEFAULT_PACKTYPE, Tools::getValue(self::KEY_DEFAULT_PACKTYPE));
            Configuration::updateValue(self::KEY_DEFAULT_INSURANCE, Tools::getValue(self::KEY_DEFAULT_INSURANCE));
            Configuration::updateValue(self::KEY_DEFAULT_REFERENCE, Tools::getValue(self::KEY_DEFAULT_REFERENCE));
            Configuration::updateValue(self::KEY_DEFAULT_SELF_SEND, Tools::getValue(self::KEY_DEFAULT_SELF_SEND));
            Configuration::updateValue(self::KEY_DEFAULT_SENDING_MACHINE, Tools::getValue(self::KEY_DEFAULT_SENDING_MACHINE));
            Configuration::updateValue(self::KEY_DEFAULT_DISPATCH_POINT, Tools::getValue(self::KEY_DEFAULT_DISPATCH_POINT));

            return $this->displayConfirmation('Ustawienia domyślne zostały zapisane');
        }
        /* Zapisanie ustawień wydruku */
        if (Tools::isSubmit(self::SUBMIT_PRINT_SETTINGS)) {
            Configuration::updateValue(self::KEY_PRINT_FORMAT, Tools::getValue(self::KEY_PRINT_FORMAT));
            Configuration::updateValue(self::KEY_PRINT_TYPE, Tools::getValue(self::KEY_PRINT_TYPE));
            return $this->displayConfirmation('Ustawienia wydruku zostały zapisane');
        }

        /* Zapisanie dodatkowych ustawień */
        if (Tools::isSubmit(self::SUBMIT_SAVE_EXTRA)) {
            PHPITools::setConfig(array(
                self::KEY_COD_BY_PAYMENT,
                self::KEY_CUSTOMER_NOTIFY,
                self::KEY_STATUS_PREPARE_UPDATE,
                self::KEY_STATUS_PREPARE,
                self::KEY_STATUS_PAID_UPDATE,
                self::KEY_STATUS_PAID
            ));

            $payments_installed = PaymentModule::getInstalledPaymentModules();
            $payments = array();

            foreach ($payments_installed as $payment) {
                if (Tools::isSubmit(self::KEY_COD_PAYMENTS.'_'.$payment['name'])) {
                    $payments[] = $payment['name'];
                }
            }

            Configuration::updateValue(self::KEY_COD_PAYMENTS, implode(',', $payments));
            return $this->displayConfirmation('Ustawienia dodatkowe zostały zapisane');
        }
    }

    protected function renderAdminOrderForm()
    {
        $id_order = (int)Tools::getValue('id_order');
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        if ($this->ps16)
            $helper->name_controller = 'paczkomatyinpost_form';
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPaczkomatyinpostModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminOrders', false)
            .'&id_order='.$id_order.'&vieworder';
        $helper->token = Tools::getAdminTokenLite('AdminOrders');
        $helper->tpl_vars = array(
            'fields_value' => $this->getAdminOrderFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getAdminOrderForm());
    }

    protected function getAdminOrderFormValues()
    {
        $id_order = (int)Tools::getValue('id_order');
        $order = new Order((int)$id_order);
        $customer = $order->getCustomer();
        $address = new Address($order->id_address_delivery);
        $id_cart = $order->id_cart;
        $data = new PaczkomatyInpostData($id_cart);
        $customer_phone = $address->phone_mobile ? $address->phone_mobile : $address->phone;

        if (Configuration::get(self::KEY_COD_BY_PAYMENT)) {
            $is_cod = $this->checkCodByOrder($order);
        } else {
            $is_cod = $this->checkCarrier($order->id_carrier, true);
        }
        $order_value = $order->total_paid_tax_incl;

        $receiver_machine = '';
        if ($is_cod) {
            $receiver_machine = empty($data->receiver_machine_cod) ? $data->receiver_machine : $data->receiver_machine_cod;
        } else {
            $receiver_machine = empty($data->receiver_machine) ? $data->receiver_machine_cod : $data->receiver_machine;
        }

        if (isset($data->cod) && $data->cod != -1) {
            $is_cod = (bool)$data->cod;
        }

        return array_merge($this->getConfigFormValues(), array(
            self::FIELD_RECEIVER_EMAIL => Tools::getValue(self::FIELD_RECEIVER_EMAIL, !empty($data->receiver_email) ? $data->receiver_email : $customer->email),
            self::FIELD_RECEIVER_MOBILE => $this->cleanPhoneNumber(Tools::getValue(self::FIELD_RECEIVER_MOBILE, $data->receiver_mobile ? $data->receiver_mobile : $customer_phone)),
            self::FIELD_RECEIVER_MACHINE => Tools::getValue(self::FIELD_RECEIVER_MACHINE, $receiver_machine),
            self::FIELD_COD => Tools::getValue(self::FIELD_COD, $is_cod),
            self::FIELD_COD_VALUE => str_replace(',', '.', Tools::getValue(self::FIELD_COD_VALUE, !empty($data->cod_value) ? $data->cod_value : $order_value)),
            self::FIELD_PACKTYPE => Tools::getValue(self::FIELD_PACKTYPE, !empty($data->packtype) ? $data->packtype : Configuration::get(self::KEY_DEFAULT_PACKTYPE)),
            self::FIELD_SELF_SEND => Tools::getValue(self::FIELD_SELF_SEND, !empty($data->self_send) ? $data->self_send : Configuration::get(self::KEY_DEFAULT_SELF_SEND)),
            self::FIELD_INSURANCE => Tools::getValue(self::FIELD_INSURANCE, !empty($data->insurance) ? $data->insurance : Configuration::get(self::KEY_DEFAULT_INSURANCE)),
            self::FIELD_REFERENCE_NUMBER => $this->generateReferenceNumber($id_order, Tools::getValue(self::FIELD_REFERENCE_NUMBER, !empty($data->reference_number) ? $data->reference_number : Configuration::get(self::KEY_DEFAULT_REFERENCE))),
            self::FIELD_SENDER_MACHINE => Tools::getValue(self::FIELD_SENDER_MACHINE, !empty($data->sender_machine) ? $data->sender_machine : Configuration::get(self::KEY_DEFAULT_SENDING_MACHINE)),
            self::FIELD_CALCULATED_PRICE => Tools::getValue(self::FIELD_CALCULATED_PRICE, $data->calculated_charge),
        ));
    }

    protected function getAdminOrderForm()
    {
        $id_order = (int)Tools::getValue('id_order');
        $forms = array();
        $order = new Order((int)$id_order);
        $id_cart = $order->id_cart;
        $data = new PaczkomatyInpostData($id_cart);

        $insurances = $this->api->getInsurances();
        $packtypes = $this->api->getPackTypes();
        $list_machines = $this->api->getListMachines();
        if (!$insurances || !$packtypes || !$list_machines)
            $this->catchErrors();
        else {
            $list_machines = array_merge(array(
                array(
                    'name' => '',
                    'label' => 'Nie wybrano')), $list_machines);

            $summary_array = array(
                'type' => 'summary',
                'name' => 'summary',
                'packcode' => $data->packcode,
                'status' => PaczkomatyInpostPackStatus::getLabel($data->status),
                'status_date' => $data->status_date,
                'delivery_code' => $data->self_send ? $data->customer_delivering_code : null,
                'hide' => $data->isEditable()
            );
            /* Fixed bug in 1.5 at BO order page - bad look */
            if ($this->ps16 && !$data->isEditable())
                $summary_array['label'] = '';
            /** Nadanie przesyłki */
            $forms[]['form'] = array(
                'id_form' => 'paczkomatyinpost',
                'legend' => array(
                    'title' => $this->l('Nadaj przesyłkę z paczkomaty.pl'),
                    'image' => '../img/admin/delivery.gif',
                ),
                'input' => array(
                    $summary_array,
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::FIELD_RECEIVER_EMAIL,
                        'label' => $this->l('Adres e-mail odbiorcy'),
                        'size' => 50,
                        'disabled' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => self::FIELD_RECEIVER_MOBILE,
                        'prefix' => '+48',
                        'label' => $this->l('Numer komórkowy odbiorcy'),
                        'size' => 50,
                        'disabled' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'col' => 9,
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::FIELD_RECEIVER_MACHINE,
                        'label' => $this->l('Do Paczkomatu InPost'),
                        'options' => array(
                            'query' => $list_machines,
                            'id' => 'name',
                            'name' => 'label'
                        ),
                        'extra' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::FIELD_PACKTYPE,
                        'label' => $this->l('Rozmiar'),
                        'options' => array(
                            'query' => $packtypes,
                            'id' => 'type',
                            'name' => 'name'
                        ),
                        'extra' => ($data->isEditable() || $data->isPrepared()) ? '' : 'disabled',
                    ),
                    array(
                        'type' => 'radio',
                        'size' => 75,
                        'is_bool' => false,
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::FIELD_SELF_SEND,
                        'label' => $this->l('Sposób nadania'),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => self::FIELD_SELF_SEND.'_courier',
                                'value' => self::SEND_TYPE_COURIER,
                                'label' => $this->l('Odbiór przez kuriera')
                            ),
                            array(
                                'id' => self::FIELD_SELF_SEND.'_paczkomat',
                                'value' => self::SEND_TYPE_PACZKOMAT,
                                'label' => $this->l('W paczkomacie')
                            ),
//                            array(
//                                'id' => self::FIELD_SELF_SEND.'_pok',
//                                'value' => self::SEND_TYPE_POK,
//                                'label' => $this->l('W dowolnym POK')
//                            ),
                        ),
                        'disabled' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'name' => self::FIELD_SENDER_MACHINE,
                        'label' => $this->l('Paczkomat nadawczy'),
                        'options' => array(
                            'query' => $list_machines,
                            'id' => 'name',
                            'name' => 'label'
                        ),
                        'extra' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => self::FIELD_REFERENCE_NUMBER,
                        'label' => $this->l('Numer referencyjny'),
                        'size' => 50,
                        'disabled' => ($data->isEditable() || $data->isPrepared()) ? '' : 'disabled',
                    ),
                    array(
                        'type' => $this->ps16 ? 'select16' : 'select',
                        'name' => self::FIELD_INSURANCE,
                        'label' => $this->l('Ubezpieczenie'),
                        'options' => array(
                            'query' => $insurances,
                            'id' => 'limit',
                            'name' => 'name'
                        ),
                        'extra' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => ($this->ps16 ? 'switch' : 'radio'),
                        'size' => 75,
                        'is_bool' => true,
                        'name' => self::FIELD_COD,
                        'label' => $this->l('Pobranie'),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => self::FIELD_COD.'_on',
                                'value' => 1,
                                'label' => $this->l('Tak')
                            ),
                            array(
                                'id' => self::FIELD_COD.'_off',
                                'value' => 0,
                                'label' => $this->l('Nie')
                            )
                        ),
                        'disabled' => $data->isEditable() ? '' : 'disabled',
                    ),
                    array(
                        'type' => 'text',
                        'name' => self::FIELD_COD_VALUE,
                        'label' => $this->l('Wartość pobrania'),
                        'size' => 50,
                        'disabled' => $data->isEditable() ? '' : 'disabled',
                    ),
//                    array(
//                        'type' => 'price',
//                        'name' => 'calculated_charge',
//                        'label' => '',
//                        'value' => Tools::displayPrice($data->calculated_charge),
//                        'paid' => $data->paid,
//                    ),
//                    array(
//                        'type' => 'hidden',
//                        'name' => self::FIELD_CALCULATED_PRICE,
//                    ),
                    array(
                        'type' => 'buttons',
                        'name' => 'buttons',
                        'label' => '',
                        'list' => array(
                            array(
                                'title' => $this->l('Przygotuj'),
                                'name' => self::SUBMIT_PREPARE,
                                'class' => (($this->ps16) ? 'btn btn-default' : 'button').($data->isPreparable() ? '' : ' hide'),
                            ),
                            array(
                                'title' => $this->l('Zmień'),
                                'name' => self::SUBMIT_CHANGE,
                                'class' => (($this->ps16) ? 'btn btn-default' : 'button').($data->isPrepared() ? '' : ' hide'),
                            ),
                            array(
                                'title' => $this->l('Usuń'),
                                'name' => self::SUBMIT_DELETE,
                                'class' => (($this->ps16) ? 'btn btn-default' : 'button').($data->canBeDeleted() ? '' : ' hide'),
                            ),
                            array(
                                'title' => $this->l('Opłać'),
                                'name' => self::SUBMIT_SEND,
                                'class' => (($this->ps16) ? 'btn btn-warning' : 'button').($data->isEditable() || $data->isPrepared() ? '' : ' hide'),
                            ),
                            array(
                                'title' => $this->l('Drukuj etykietę'),
                                'name' => self::SUBMIT_PRINT_LABEL,
                                'class' => (($this->ps16) ? 'btn btn-warning' : 'button').($data->isEditable() || $data->isPrepared() ? ' hide' : ''),
                            ),
                            array(
                                'title' => $this->l('Drukuj potwierdzenie nadania'),
                                'name' => self::SUBMIT_PRINT_CONFIRM,
                                'class' => (($this->ps16) ? 'btn btn-warning' : 'button').(empty($data->dispatch_order_id) ? ' hide' : ''),
                            ),
                            array(
                                'title' => $this->l('Drukuj potwierdzenie odbioru'),
                                'name' => self::SUBMIT_PRINT_DISPATCH_ORDER,
                                'class' => (($this->ps16) ? 'btn btn-warning' : 'button').(empty($data->dispatch_order_id) ? ' hide' : ''),
                            ),
                        )
                    )
                ),
            );
        }
        return $forms;
    }

    protected function privatePostProcessAdmin($id_cart)
    {
        $return = '<br/>';
        $order = Order::getOrderByCartId($id_cart);
        $order_id = is_numeric($order) ? $order : $order['id_order'];
        $form_values = $this->getAdminOrderFormValues();
        $pack_data = new PaczkomatyInpostData($id_cart);
        if (!Validate::isLoadedObject($pack_data))
            $pack_data = PaczkomatyInpostData::createNew($id_cart);

        /* Przygotowanie paczki */
        if (Tools::isSubmit(self::SUBMIT_PREPARE) || ($pack_data->isEditable() && Tools::isSubmit(self::SUBMIT_SEND))) {

            if ($pack_data->isEditable()) {
                $pack_data->cod = (bool)$form_values[self::FIELD_COD];
                $pack_data->cod_value = (float)$form_values[self::FIELD_COD_VALUE];
                $pack_data->insurance = (float)$form_values[self::FIELD_INSURANCE];
                $pack_data->packtype = $form_values[self::FIELD_PACKTYPE];
                $pack_data->receiver_email = $form_values[self::FIELD_RECEIVER_EMAIL];
                if ($pack_data->cod)
                    $pack_data->receiver_machine_cod = $form_values[self::FIELD_RECEIVER_MACHINE];
                else
                    $pack_data->receiver_machine = $form_values[self::FIELD_RECEIVER_MACHINE];
                $pack_data->receiver_mobile = $form_values[self::FIELD_RECEIVER_MOBILE];
                $pack_data->reference_number = $form_values[self::FIELD_REFERENCE_NUMBER];
                $pack_data->self_send = $form_values[self::FIELD_SELF_SEND];
                $pack_data->sender_machine = $form_values[self::FIELD_SENDER_MACHINE];
            }
            else {
                $return .= $this->displayError('Status przesyłki nie pozwala na jej ponowne przygotowanie');
                return $return;
            }
            if ($pack_data->save()) {
                $return .= $this->displayConfirmation('Informacje o paczce zostały zapisane');
                $auto_labels = ($pack_data->isEditable() && Tools::isSubmit(self::SUBMIT_SEND));
                $pack_prepared = $this->api->preparePack($pack_data, $auto_labels);
				//d(array('after prepared', $pack_prepared));
                if ($pack_prepared !== false) {
                    if (!$auto_labels) {
                        $pack_data->setAsPrepared();
                        $return .= $this->displayConfirmation('Paczka została przygotowana');
                    } else {
                        $pack_data->setAsPaid();
                        $return .= $this->displayConfirmation('Paczka została przygotowana i opłacona');
                    }
                } else {
                    $this->catchErrors();
                    $return .= $this->displayError('Wystąpił błąd podczas przygotowywania przesyłki');
                }
            } else
                $return .= $this->displayError('Wystąpił błąd podczas zapisywania danych o paczce');
        }

        /* Zmiana danych paczki */
        if (Tools::isSubmit(self::SUBMIT_CHANGE) || ($pack_data->isPrepared() && Tools::isSubmit(self::SUBMIT_SEND))) {
            $change_pack_type = $this->api->changePacktype($pack_data->packcode, $form_values[self::FIELD_PACKTYPE]);
            if ($change_pack_type != false) {
                $pack_data->packtype = $form_values[self::FIELD_PACKTYPE];
                $pack_data->calculated_charge = $form_values[self::FIELD_CALCULATED_PRICE];
                $return .= $this->displayConfirmation('Typ paczki został zmieniony pomyślnie');
            }

            $change_reference = $this->api->changeReference($pack_data->packcode, $form_values[self::FIELD_REFERENCE_NUMBER]);
            if ($change_reference != false) {
                $pack_data->reference_number = $form_values[self::FIELD_REFERENCE_NUMBER];
                $return .= $this->displayConfirmation('Numer referencyjny został zmieniony pomyślnie');
            }
            if (($change_pack_type || $change_reference) && $pack_data->save())
                $return .= $this->displayConfirmation('Zmiany zostały zapisane w bazie');

            $this->catchErrors();
        }

        /* Usunięcie paczki */
        if (Tools::isSubmit(self::SUBMIT_DELETE)) {
            $delete_pack = $this->api->deletePack($pack_data->packcode);
            if ($delete_pack) {
                $pack_data->deletePack();
                $return .= $this->displayConfirmation('Paczka została usunięta z systemu');
            } else if (!$delete_pack && $pack_data->canBeDeleted()) {
                $pack_data->deletePack();
                $return .= $this->displayConfirmation('Paczka została usunięta z bazy prestashop, ale nie z manager.paczkomaty.pl');
            } else
                $this->catchErrors();
        }
        /* Opłacenie przygotowanej paczki */
        if ($pack_data->isPrepared() && Tools::isSubmit(self::SUBMIT_SEND)) {
            $payforpack = $this->api->payForPack($pack_data);
            if ($payforpack != false)
                $return .= $this->displayConfirmation('Paczka została opłacona');
            else
                $this->catchErrors();
        }

        /* Drukowanie etykiety */
        if (Tools::isSubmit(self::SUBMIT_PRINT_LABEL)) {
            $sticker = $this->api->getPackSticker($pack_data->packcode);
            if ($sticker != false)
                $this->downloadPdf('Zamówienie #'.$order_id.' - '.$pack_data->packcode, $sticker);
            else
                $this->catchErrors();
        }
        /* Drukowanie potwierdzenia nadania */
        if (Tools::isSubmit(self::SUBMIT_PRINT_CONFIRM)) {
            $confirm = $this->api->getConfirmPrintout(array(
                $pack_data->packcode));
            if (!$confirm)
                $this->catchErrors();
        }
        /* Drukowanie potwierdzenia odbioru */
        if (Tools::isSubmit(self::SUBMIT_PRINT_DISPATCH_ORDER)) {
            $confirm = $this->api->getDispatchOrderPrintout($pack_data->dispatch_order_id);
            if (!$confirm)
                $this->catchErrors();
        }
        /* Sprawdzenie aktualnego statusu paczki */
        if ($status = $this->checkPackStatus($pack_data))
            $return .= $status;
        /* Sprawdzenie czy paczka oczekuje na zamówienie kuriera */
        if ($pack_data->paid && empty($pack_data->dispatch_order_id) && !$pack_data->self_send) {
            $error = 'Uwaga! Paczka oczekuje na zamówienie kuriera. <a href="'.$this->context->link->getAdminLink('AdminPaczkomatyInpost');
            $error .= '">Kliknij tutaj, aby utworzyć listę paczek do odbioru</a>';
            $return .= $this->displayError($error);
        }

		if(!empty($this->context->controller->errors)){
			foreach($this->context->controller->errors as $e){
				$return .= $this->displayError($e);
			}
		}
        return $return;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $php_self = isset($this->context->controller->php_self) ? $this->context->controller->php_self : false;
        if ($php_self && !in_array($php_self, array(
                'order-opc',
                'order')) || (!PHPITools::ps17() && $php_self == 'order' && (int)Tools::getValue('step') != 2))
            return;

        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryPlugin('fancybox');
        if (PHPITools::ps17()) {
            $this->context->controller->addJS($this->_path.'views/js/front17.js');
        } else {
            $this->context->controller->addJS($this->_path.'views/js/front.js');
        }
        $this->context->controller->addJS($this->_path.'views/js/geo.js');
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_order = (int)$params['id_order'];
        $order = new Order($id_order);
        $id_cart = $order->id_cart;
        $id_carrier = $order->id_carrier;

        $module_enabled = false;
        $is_cod = false;
        if ($this->checkCarrier($id_carrier))
            $module_enabled = true;
        if ($this->checkCarrier($id_carrier, true))
            $is_cod = true;

        if (Configuration::get(self::KEY_CARRIER_ANY) && !$module_enabled)
            return;
        else if (!Configuration::get(self::KEY_CARRIER_ANY) && !$module_enabled) {
            $error = 'Uwaga! Do zamówienia przypisany jest inny przewoźnik niż wybrani w ustawieniach modułu paczkomatyinpost.';
            $error .= ' Proszę uważać przy generowaniu listów przewozowych!';
            $this->context->controller->warnings[] = $error;
        }
        $output = ''.$this->privatePostProcessAdmin($id_cart);

//        $insurances = $this->api->getInsurances();
//        $insurances_json = array();
//        if ($insurances)
//            foreach ($insurances as $insurance)
//                $insurances_json[$insurance['limit']] = (float)$insurance['price'];
//        $pack_types = $this->api->getPackTypes();
//        $pack_types_json = array();
//        if ($pack_types)
//            foreach ($pack_types as $pack_type)
//                $pack_types_json[$pack_type['type']] = (float)$pack_type['price'];

//        $price_list_cod = $this->api->getPriceListForCod();
        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'module_name' => $this->name,
            'module_version' => $this->version,
            'is_cod' => $is_cod,
//            'insurances' => Tools::jsonEncode($insurances_json),
//            'packtypes' => Tools::jsonEncode($pack_types_json),
//            'pricelistcod' => Tools::jsonEncode($price_list_cod),
        ));

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/order.tpl');

        return $output.$this->renderAdminOrderForm();
    }

    public function hookDisplayBeforeCarrier($params)
    {
        self::doNothing($params);
        $cookie = $this->context->cookie;

        $id_address_delivery = $this->context->cart->id_address_delivery;
        $address = new Address((int)$id_address_delivery);

        $data = new PaczkomatyInpostData($cookie->id_cart);

        $cache_id = $this->name.'|displaybeforecarrier|'.$address->postcode.'|'.$data->receiver_machine.'|'.$data->receiver_machine_cod;

        if (!$this->isCached('carrier_list.tpl', $this->getCacheId($cache_id))) {
            $images_url = (Tools::usingSecureMode() ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_).__PS_BASE_URI__.'/modules/'.$this->name.'/views/img/';
            $logo_url = (Tools::usingSecureMode() ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_).__PS_BASE_URI__.'/modules/'.$this->name.'/logo.png';

            $this->context->smarty->assign(array(
                'module_dir' => $this->_path,
                'module_name' => $this->name,
                'module_version' => $this->version,
                'receiver_machine' => $data->receiver_machine,
                'receiver_machine_cod' => $data->receiver_machine_cod,
                'carrier' => Configuration::get(self::KEY_CARRIER),
                'carrier_cod' => Configuration::get(self::KEY_CARRIER_COD),
                'marker_1' => urlencode($images_url.'marker1.png'),
                'marker_2' => urlencode($images_url.'marker2.png'),
                'opc_enabled' => (Module::isInstalled('onepagecheckout') && Module::isEnabled('onepagecheckout')) ? '1' : '0',
                'ps16' => $this->ps16,
                'ps17' => PHPITools::ps17(),
                'paczkomaty_logo' => $logo_url
            ));
            if (PHPITools::ps17()) {
                $list_machines = $this->api->getListMachines();
                $list_machines_cod = $this->api->getListMachinesCod();

                if (Validate::isLoadedObject($address)) {
                    $nearest_machines = $this->api->getNearestListMachines($address->postcode, false);
                    $nearest_machines_cod = $this->api->getNearestListMachines($address->postcode, true);
                }

                $this->context->smarty->assign(array(
                    'list_machines' => $list_machines,
                    'list_machines_cod' => $list_machines_cod,
                    'nearest_machines' => isset($nearest_machines) ? $nearest_machines : null,
                    'nearest_machines_cod' => isset($nearest_machines_cod) ? $nearest_machines_cod : null
                ));
            }
        }
        return $this->display(__FILE__, 'carrier_list.tpl', $this->getCacheId($cache_id));
    }

    public function hookDisplayFooter($params)
    {
        if (PHPITools::ps17())
            return;
        $php_self = isset($this->context->controller->php_self) ? $this->context->controller->php_self : false;
        if ($php_self && !in_array($php_self, array(
                'order-opc',
                'order')) || ($php_self == 'order' && (int)Tools::getValue('step') != 2))
            return;

        self::doNothing($params);
        $cookie = $this->context->cookie;

        $id_address_delivery = $this->context->cart->id_address_delivery;
        $address = new Address((int)$id_address_delivery);

        $data = new PaczkomatyInpostData($cookie->id_cart);

        $cache_id = $this->name.'|displayfooter|'.$address->postcode.'|'.$data->receiver_machine.'|'.$data->receiver_machine_cod;

        if (!$this->isCached('footer.tpl', $this->getCacheId($cache_id))) {
            $images_url = (Tools::usingSecureMode() ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_).__PS_BASE_URI__.'/modules/'.$this->name.'/views/img/';

            $list_machines = $this->api->getListMachines();
            $list_machines_cod = $this->api->getListMachinesCod();

            if (Validate::isLoadedObject($address)) {
                $nearest_machines = $this->api->getNearestListMachines($address->postcode, false);
                $nearest_machines_cod = $this->api->getNearestListMachines($address->postcode, true);
            }

            $this->context->smarty->assign(array(
                'list_machines' => $list_machines,
                'list_machines_cod' => $list_machines_cod,
                'nearest_machines' => isset($nearest_machines) ? $nearest_machines : null,
                'nearest_machines_cod' => isset($nearest_machines_cod) ? $nearest_machines_cod : null
            ));
        }
        return $this->display(__FILE__, 'footer.tpl', $this->getCacheId($cache_id));
    }

    private function installTab()
    {
        $error = $this->createTab($this->l('Paczkomaty Inpost'), 'AdminPaczkomatyInpost', 'AdminParentShipping');
        return $error;
    }

    private function createTab($name, $className, $parent)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang)
            $tab->name[$lang['id_lang']] = $name;

        if ($parent != null)
            $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        else
            $tab->id_parent = 0;
        $tab->module = $this->name;
        return $tab->add();
    }

    private function deleteTab($name)
    {
        $id_tab = (int)Tab::getIdFromClassName($name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else
            return false;
        return true;
    }

    private function uninstallTab()
    {
        return ($this->deleteTab('AdminPaczkomatyInpost'));
    }

    public function downloadPdf($file_name, $content)
    {
        $type = Configuration::get(self::KEY_PRINT_FORMAT);
        if (empty($type))
            $type = 'Pdf';

        ob_end_clean();
        ob_start();
        if ($type == 'Pdf') {
            $file_type = 'pdf';
            header('Content-Type: application/pdf');
        } else {
            $file_type = 'epl';
            header('Content-Type: application/octet-stream');
        }
        header("Content-Disposition: attachment; filename=\"$file_name.$file_type\"");
        exit($content);
    }

    public function catchErrors($error = true)
    {
        if (!empty($this->api->errors)) {
            if ($error)
                $this->context->controller->errors = array_merge($this->context->controller->errors, $this->api->errors);
            else
                $this->context->controller->warnings = array_merge($this->context->controller->warnings, $this->api->errors);
        }
    }

    private function generateReferenceNumber($id_order, $number)
    {
        $order = new Order($id_order);
        $search = array(
            self::TEMPLATE_ORDER_ID,
            self::TEMPLATE_ORDER_REFERENCE,
            '{product_list}');
        $replace = array(
            $order->id,
            $order->reference,
            $this->getOrderProducts($order));
        return str_replace($search, $replace, $number);
    }

    public function getOrderProducts($order)
    {

        $products = $order->getProducts();
        $products_data = array();
        if (count($products)) {
            foreach ($products as $product) {
                $attribute = '';
                $ipa = (int)$product['product_attribute_id'];
                if ($ipa) {
                    $attributes = Product::getAttributesParams($product['product_id'], $ipa);
                    if (!empty($attributes)) {
                        $attribute = $attributes[0]['name'];
                    }
                }
                $data = array(
                    $product['product_reference'],
                    $attribute,
                    $product['product_quantity']
                );
                $products_data[] = join(',', $data);
            }
        }
        return join('/', $products_data);
    }

    private function cleanPhoneNumber($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    private function checkCarrier($id_carrier, $cod_only = false)
    {
        if (in_array($id_carrier, explode(self::SEPARATOR, Configuration::get(self::KEY_CARRIER_COD))))
            return true;
        if (!$cod_only && in_array($id_carrier, explode(self::SEPARATOR, Configuration::get(self::KEY_CARRIER))))
            return true;
        return false;
    }

    public static function updateData($machine, $machine_cod = null)
    {
        $id_cart = Context::getContext()->cart->id;
        $data = new PaczkomatyInpostData((int)$id_cart);
        if (!Validate::isLoadedObject($data))
            $data = PaczkomatyInpostData::createNew($id_cart);

        if (!empty($machine))
            $data->receiver_machine = $machine;
        if (!empty($machine_cod))
            $data->receiver_machine_cod = $machine_cod;
        $data->save();
    }

    public function checkPackStatus(PaczkomatyInpostData $pack_data)
    {
        if (!Validate::isLoadedObject($pack_data) || empty($pack_data->packcode))
            return false;
        $data = $this->api->getPackStatus($pack_data->packcode);

        if ($data != false) {
            if ($pack_data->status_date == date('Y-m-d H:i:s', strtotime((string)$data->statusDate)))
                return false;
            $pack_data->status = (string)$data->status;
            $pack_data->status_date = date('Y-m-d H:i:s', strtotime((string)$data->statusDate));
            if ($pack_data->update()) {
                $conf = 'Paczka '.$pack_data->packcode.' otrzymała nowy status (';
                return $this->displayConfirmation($conf.PaczkomatyInpostPackStatus::getLabel($pack_data->status).')');
            }
        }
        return false;
    }

    /**
     * Just do nothing.
     */
    public static function doNothing($a)
    {
        return $a;
    }

    protected function getCacheId($name = null)
    {
        return parent::getCacheId($name.'|'.date('Ymd'));
    }

    /**
     * Sprawdza czy dane zamówienie jest za pobraniem
     * @param Order $order
     */
    public static function checkCodByOrder(Order $order)
    {
        if (!Configuration::get(self::KEY_COD_BY_PAYMENT)) {
            return false;
        }
        $payments = explode(',', Configuration::get(self::KEY_COD_PAYMENTS));
        return in_array($order->module, $payments);
    }

    private function getCustomerNotifyOptions()
    {
        return array(
            self::OPTION_CUSTOMER_NOTIFY_NEVER => $this->l('Nigdy'),
            self::OPTION_CUSTOMER_NOTIFY_PREPARE => $this->l('Gdy przesyłka została stworzona ale jeszcze nieopłacona'),
            self::OPTION_CUSTOMER_NOTIFY_PAID => $this->l('Gdy przesyłka została opłacona')
        );
    }
}
