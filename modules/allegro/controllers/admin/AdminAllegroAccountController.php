<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroAccountController extends ParentAllegroController
{
    /**
     * Set bootstrap theme
     * @var boolean
     */
    public $bootstrap = true;


    /**
     * AdminController::__construct() override
     *
     * @see AdminController::__construct()
     */
    public function __construct()
    {
        $this->table = 'allegro_account';
        $this->className = 'AllegroAccount';
        $this->lang = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('refresh');

        $this->context = Context::getContext();

        $languagesS = array();
        foreach (Language::getLanguages() as $l)
            $languagesS[$l['id_lang']] = $l['name'];

        $currenciesS = array();
        foreach (Currency::getCurrencies() as $c)
            $currenciesS[$c['id_currency']] = $c['name'];

        // @todo
        if (version_compare(_PS_VERSION_, '1.5.6.3', '>='))
            $this->_select = '
            l.name AS language_name,
            c.name AS currency_name,
            IF(a.sandbox, 0, 1) badge_success,
            IF(a.sandbox, 1, 0) badge_danger,';

        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (l.`id_lang` = a.`id_language`)
        LEFT JOIN `' . _DB_PREFIX_ . 'currency` c ON (c.`id_currency` = a.`id_currency`)';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
            'enableSelection' => array('text' => $this->l('Enable selection')),
            'disableSelection' => array('text' => $this->l('Disable selection'))
        );

        parent::__construct();

        $this->fields_list = array(
            'id_allegro_account' => array(
                'title' => $this->l('ID'),
                'width' => 25
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'a!name'
            ),
            'login' => array(
                'title' => $this->l('Username'),
                'width' => 'auto'
            ),
            'sandbox' => array(
                'title' => $this->l('Sandbox'),
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'width' => 25,
                'callback' => 'printSandbox',
                'badge_danger' => true,
                'badge_success' => true,
            ),
            'currency_name' => array(
                'title' => $this->l('Currency'),
                'width' => 'auto',
                'type' => 'select',
                'list' => $currenciesS,
                'filter_key' => 'a!id_currency'
            ),
            'language_name' => array(
                'title' => $this->l('Language'),
                'width' => 'auto',
                'type' => 'select',
                'list' => $languagesS,
                'filter_key' => 'id_language'
            ),
            'token_lifetime' => array(
                'title' => $this->l('Lifetime (days)'),
                'width' => 'auto',
                'callback' => 'printLifetime',
                'filter' => false,
                'remove_onclick' => true,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'width' => 25
            )
        );

        // @todo
        if (version_compare(_PS_VERSION_, '1.5.6.3', '<')) {
            unset($this->fields_list['language_name']);
            unset($this->fields_list['currency_name']);
        }

    }


    public function printLifetime($id, $row)
    {
        $seconds = strtotime($row['token_lifetime']) - time();
        $days = floor($seconds / 86400);

        return '<span class="badge badge-'.($days < 14 ? 'danger action-refresh' : 'success').'">'.($days < 0 ? $this->l('- refresh token -') : $days).'</span>';
    }


    /**
     * Print sandbox info on list
     */
    public function printSandbox($id, $row)
    {
        return (bool)$row['sandbox'] ? $this->l('Yes') : $this->l('No');
    }

    public function displayRefreshLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');

        $obj = new AllegroAccount((int)$id);

        $tpl->assign(array(
            'href' => AllegroApi::buildAuthUrl(
                $this->getRedirectUri(
                    (int)$obj->sandbox,
                    'action=refresh&id_allegro_account='.(int)$id
                ),
                (int)$obj->sandbox
            ),
            'action' => $this->l('Refresh'),
            'id' => $id
        ));

        return $tpl->fetch();
    }


    /**
     * AdminController::renderList() override
     *
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        $this->displayInformation('&nbsp;<b>'.$this->l('Here you can manage your allegro accounts.').'</b>
            <br />
            <ul>
                <li>'.$this->l('To use module need add at least one account').'<br /></li>
                <li>'.$this->l('Account must have a valid license (except sandbox accounts)').'</li>
                <li>'.$this->l('In case of problems, please contact us').' <a href="mailto:mail@addonspresta.com">mail@addonspresta.com</a>.
                </li>
            </ul>
            <br />
            <p><b>'.$this->l('If you have many accounts, log out from allegro to be able to add various accounts.').'</b></p>
            <p><b>'.$this->l('To refresh token use link "Refresh" in "Edit" dropdown.').'</b></p>');

        $this->displayWarning($this->l('By deleting your account you will lose all associated data like related auctions (same auctions will continue), parameters, etc. be careful!'));

        return parent::renderList();
    }

    private function getRedirectUri($sandbox = false, $process = 'addallegro_account')
    {
        $url = 'http'.(Configuration::get('PS_SSL_ENABLED') ? 's' : '').'://'.
            $_SERVER['HTTP_HOST'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).
            '?controller=Admin'.$this->className.'&token='.$this->token.'&'.$process.
            ($sandbox ? '&sandbox' : '');

        return $url;
    }

    public function processRefresh()
    {
        $id = Tools::getValue('id_allegro_account');
        $code = Tools::getValue('code');

        $obj = new AllegroAccount((int)$id);

        $sandbox = (int)$obj->sandbox;

        if ($code && $obj) {

            $api = new AllegroApi($obj);

            $tk = $api->getAccessToken(
                $code,
                $this->getRedirectUri($sandbox, 'action=refresh&id_allegro_account='.(int)$id)
            );

            if (!isset($tk->error)) {

                // Code for refresh
                $obj->access_token = $tk->access_token;
                $obj->refresh_token = $tk->refresh_token;
                $obj->token_lifetime = date('Y-m-d H:i:s', time() + AllegroApi::TOKEN_LIFETIME);

                $obj->update();

                // Check login
                try {
                    $userData = $api->doGetMyData();
                } catch (SoapFault $e) {
                    $this->errors[] = $e->faultstring;
                }

                if ($userData->userData->userLogin != $obj->login) {
                    $this->errors[] = $this->l('Login not match! Logout from allegro and try again (account is disabled).');

                    $obj->active = 0;
                    $obj->save();
                } else {
                    $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
                }

            } else {
                $this->errors[] = $this->l('API: ').$tk->error;
                return;
            }
        }
    }

    /**
     * AdminController::renderForm() override
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true)))
            return;

        $code = Tools::getValue('code');
        $token = Tools::getValue('access_token');
        $sandbox = (int)Tools::getIsset('sandbox');

        // Get access token
        if ($code) {
            try {
                $tmpAccount = new AllegroAccount();
                $tmpAccount->sandbox = $sandbox;
                $api = new AllegroApi($tmpAccount);

                $tk = $api->getAccessToken($code, $this->getRedirectUri($sandbox));

                if (!isset($tk->error)) {
                    // Code for refresh
                    $this->fields_value['code'] = $code;
                    $this->fields_value['access_token'] = $tk->access_token;
                    $this->fields_value['refresh_token'] = $tk->refresh_token;
                    $this->fields_value['token_lifetime'] = date('Y-m-d H:i:s', time() + AllegroApi::TOKEN_LIFETIME);
                } else {
                    // Rest auth fail
                    if ($tk->error === 'Unauthorized') {
                        $this->errors[] = $this->l('Bad credentials - register your application and try again.');
                    } else {
                        $this->errors[] = $tk->error_description;
                    }
                    return;
                }

            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                return;
            }
        }

        $this->fields_value['sandbox'] = $sandbox;

        $employess = Employee::getEmployees();
        $employess[] = array(
            'id_employee' => 0,
            'lastname' => $this->l('All employees')
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Service account'),
                'icon' => 'icon-user'
            ),
            'input' => array()
        );

        $this->fields_form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Account name'),
            'name' => 'name',
            'col' => '4',
            'required' => true,
            'desc' => $this->l('Custom name of account in module')
        );

        $this->fields_form['input'][] = array(
            'type' => 'hidden',
            'label' => $this->l('Token'),
            'name' => 'access_token',
            'readonly' => 'readonly',
            'required' => true,
            'desc' => $this->l('Leave as is'),
        );
        $this->fields_form['input'][] = array(
            'type' => 'hidden',
            'label' => $this->l('Refresh token'),
            'name' => 'refresh_token',
            'readonly' => 'readonly',
            'required' => true,
            'desc' => $this->l('Leave as is'),
        );
        $this->fields_form['input'][] = array(
            'type' => 'hidden',
            'label' => $this->l('Token lifetime'),
            'name' => 'token_lifetime',
            'readonly' => 'readonly',
            'required' => true,
            'desc' => $this->l('Leave as is'),
        );

        // Sanbox & sandbox key
        if ($sandbox) {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'label' => $this->l('Sandbox environment'),
                'name' => 'sandbox'

            );
        }

        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Currency'),
            'name' => 'id_currency',
            'options' => array(
                'query' => Currency::getCurrencies(false, false, true),
                'id' => 'id_currency',
                'name' => 'name'
            ),
            'required' => true,
            'desc' => $this->l('Select a currency, module will use selected currency to calculate prices')
        );


        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Language'),
            'name' => 'id_language',
            'options' => array(
                'query' => Language::getLanguages(),
                'id' => 'id_lang',
                'name' => 'name'
            ),
            'required' => true,
            'desc' => $this->l('Select a language')
        );

        $this->fields_form['input'][] = array(
            'type' => 'hidden', // @todo
            'label' => $this->l('Available for'),
            'name' => 'id_employee',
            'options' => array(
                'query' => $employess,
                'id' => 'id_employee',
                'name' => 'lastname'
            ),
            'required' => true,
            'desc' => $this->l('Select a employee')
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('   Save   '),
            'class' => 'btn btn-default pull-right'
        );

        $this->fields_value['passwd'] = false;

        if (!($obj = $this->loadObject(true)))
            return;

       $this->addJs(__PS_BASE_URI__.'modules/allegro/js/admin-account.js');

        return parent::renderForm();
    }


    /**
     * AdminController::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        // PS 1.5 See next method ("initToolbar")

        // Disable "Add" button in table header
        unset($this->toolbar_btn['new']);

        if (empty($this->display)) {
            // REST account
            if (CLIENT_ID && CLIENT_SECRET && API_KEY) {
                $this->page_header_toolbar_btn['new_allegro_account_rest'] = array(
                    'href' => AllegroApi::buildAuthUrl($this->getRedirectUri()),
                    'desc' => $this->l('Add account', null, null, false),
                    'icon' => 'process-icon-new'
                );
            }

            // REST sandbox account
            if (SANDBOX_CLIENT_ID && SANDBOX_CLIENT_SECRET && SANDBOX_API_KEY) {
                $this->page_header_toolbar_btn['new_allegro_account_sandbox_rest'] = array(
                    'href' => AllegroApi::buildAuthUrl($this->getRedirectUri(true), true),
                    'desc' => $this->l('Add account', null, null, false).' (sandbox)',
                    'icon' => 'process-icon-new'
                );
            }
        }
        
        parent::initPageHeaderToolbar();
    }


    /**
    * PrestaShop 1.5x only
    */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
            default: // list
                if (CLIENT_ID && CLIENT_SECRET && API_KEY) {
                    $this->toolbar_btn['new_allegro_account_rest'] = array(
                        'href' => AllegroApi::buildAuthUrl($this->getRedirectUri()),
                        'desc' => $this->l('Add account', null, null, false),
                    );
                }

                if (SANDBOX_CLIENT_ID && SANDBOX_CLIENT_SECRET && SANDBOX_API_KEY) {
                    $this->toolbar_btn['new_allegro_account_sandbox_rest'] = array(
                        'href' => AllegroApi::buildAuthUrl($this->getRedirectUri(true), true),
                        'desc' => $this->l('Add account', null, null, false).' (sandbox)',
                    );
                }
        }
    }


    public function processSave()
    {
        // First save object to DB
        parent::processSave();

        // Set right allegro login
        if ($this->object = new AllegroAccount((int)Tools::getValue($this->identifier))) {
            try {
                $api = new AllegroApi($this->object);
                $userData = $api->doGetMyData();

                $this->object->login = $userData->userData->userLogin;
                $this->object->update();
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            // Stay one edit page if error
            if (!empty($this->errors)) {
                $this->redirect_after = null;
                $this->display = 'edit';
            }
        }        
    }
}
