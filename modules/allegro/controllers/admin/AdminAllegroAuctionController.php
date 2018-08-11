<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroAuctionController extends ParentAllegroController
{
    /** @var array Number of results in list per page (used in select field) */
    protected $_pagination = array(20, 50, 100, 300, 1000);

    /** @var int Default number of results in list per page */
    protected $_default_pagination = 20;

    protected $currency = null;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->list_id = 'auction';
        $this->table = 'auction'; // For delete_xxx
        $this->list_no_link = true;
        $this->bulk_actions = array(
            'finish' => array(
                'text' => $this->l('Finish auctions'),
                'icon' => 'icon-remove',
                'confirm' => $this->l('Finish auctions?')
            ),
        );

        $this->addRowAction('finish');
        $this->addRowAction('unlink');

        parent::__construct();
        parent::initApi();

        if (!count($this->moduleErrors)) {
            $allegroAccount = new AllegroAccount((int)$this->api->getAccountId());
            $this->currency = new Currency((int)$allegroAccount->id_currency);
        }
    }


    public function initContent()
    {
        $this->display = 'view';
        $this->renderAccountsBar();

        $this->addJs(__PS_BASE_URI__.'modules/allegro/js/admin-auction.js');
        $this->addJqueryPlugin(array('autocomplete'));

        return parent::initContent();
    }


    public function display()
    {
        $this->_conf[101] = $this->l('Auction(s) finished sucefully (the disappearance from the list may be delayed up to a few minutes).');
        $this->_conf[102] = $this->l('Auction unlinked sucefully.');

        $this->displayInformation('&nbsp;<b>'.$this->l('On this page you can manage your auctions.').'</b>
            <br />
            <ul>
                <li>'.$this->l('All auctions for selected account will be visible in table below').'<br /></li>
                <li>'.$this->l('Only auctions with "ID product" field are linked to shop product and will be synchronized').'</li>
                <li>'.$this->l('Auctions created outside module will no be synchronized but you can link these auctions to shop products').'
                </li>
            </ul>');

        parent::display();
    }


    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }


    public function displayFinishLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');
		if (!array_key_exists('Finish', self::$cache_lang))
			self::$cache_lang['Finish'] = $this->l('Finish');

		$tpl->assign(array(
				'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&finish'.$this->table.'&token='.($token != null ? $token : $this->token),
				'action' => self::$cache_lang['Finish'],
				'id' => $id
		));

		return $tpl->fetch();
	}


    public function printPriceC($price, $row)
    {
        if ($this->currency) {
            return Tools::displayPrice($price, $this->currency);
        }
        return '--';
    }

    public function displayUnlinkLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');
        if (!array_key_exists('Unlink', self::$cache_lang))
            self::$cache_lang['Unlink'] = $this->l('Unlink');

        $tpl->assign(array(
                'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&unlink'.$this->table.'&token='.($token != null ? $token : $this->token),
                'action' => self::$cache_lang['Unlink'],
                'id' => $id
        ));

        return $tpl->fetch();
    }

    public function printMapLink($price, $row)
    {
        if (!$row['idProduct']) {

            $qty = ($row['itemStartQuantity'] - $row['itemSoldQuantity']);

            $html = '
            <div class="auctionMapForm">
                <input type="hidden" name="id_offer" value="'.(float)$row['itemId'].'" />
                <input type="hidden" name="title" value="'.$row['itemTitle'].'" />
                <input type="hidden" name="date_start" value="'.(int)$row['itemStartTime'].'" />
                <input type="hidden" name="quantity" value="'.(int)$qty.'" />

                <input type="text" name="ac_id_product" class="ac_id_product" id="id_product_'.(float)$row['itemId'].'" placeholder="'.$this->l('Enter ID, name or reference...').'" />
                <input type="hidden" name="id_product">

                <input class="btn btn-default" type="submit" name="submitMapAuction" value="'.$this->l('Save').'" />
            </div>';

            return $html;
        } else {
            return $row['idProduct'];
        }
    }


    public function processFinish()
    {
        $idAuction = (float)Tools::getValue('id_auction');
        $allegroAuction = AllegroAuction::getByAuctionID($idAuction);

        try {
            $res = $this->api->doFinishItem(array('finishItemId' => $idAuction));
        } catch (SoapFault $e) {
            $this->errors[] =  $e->faultstring;
            $faultcode = $e->faultcode;
        }

        // Update status if no error OR if "auction not exist" error but min. 5 minutes after auction create
        if(!isset($faultcode) || ($faultcode == 'ERR_INVALID_ITEM_ID' && time() > strtotime($allegroAuction->date_add) + 300))
            Db::getInstance()->update('allegro_auction', array(
                'status' => 3,
            ), 'id_auction = '.(float)$idAuction);

        if(empty($this->errors)) {
            $this->redirect_after = self::$currentIndex.'&token='.$this->token.'&conf=101';
        }
    }


    public function processUnlink()
    {
        $allegroAuction = AllegroAuction::getByAuctionID((float)Tools::getValue('id_auction'));

        if($allegroAuction->delete()) {
            $this->redirect_after = self::$currentIndex.'&token='.$this->token.'&conf=102';
        } else {
            $this->errors[] = $this->l('Unable to unlink auction.');
        }
    }

    public function postProcess()
    {
        if(Tools::getIsset('finish'.$this->table)) {
            $this->processFinish();
        } if(Tools::getIsset('unlink'.$this->table)) {
            $this->processUnlink();
        } else {
            parent::postProcess();
        }
    }


    public function processFilter()
    {
        $prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix.$key});
                } elseif (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
                if (stripos($key, $this->list_id.'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                } elseif (stripos($key, $this->list_id.'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix.$this->list_id.'Filter_');
    }


    public function displayCover($id, $row)
    {
        return '<img src="'.($row['itemThumbnailUrl']).'" class="img-thumbnail">';
    }


    public function renderView()
    {
        $this->_orderBy = $this->context->cookie->{'allegroauctionauctionOrderby'};
        $this->_orderWay = strtoupper($this->context->cookie->{'allegroauctionauctionOrderway'});

        if($pagination = (int)Tools::getValue($this->list_id.'_pagination'))
            $this->context->cookie->{$this->list_id.'_pagination'} = $pagination;
        elseif ($this->context->cookie->{$this->list_id.'_pagination'})
            $pagination = $this->context->cookie->{$this->list_id.'_pagination'};

        $page = (int)Tools::getValue('submitFilterauction');
        if(!$page) {
            $page = 1;
        }

        // Order and sort
        $sortOrder = 1;
        if($this->_orderWay == 'DESC')
            $sortOrder = 2;

        $sortType = 1; // End time
        if($this->_orderBy == 'priceBuyNow')
            $sortType = 2;
        else if($this->_orderBy == 'itemTitle')
            $sortType = 3;
        else if($this->_orderBy == 'itemStartQuantity')
            $sortType = 9;
        else if($this->_orderBy == 'itemSoldQuantity')
            $sortType = 10;

        // Filter
        $id = (float)trim(Tools::getValue($this->list_id.'Filter_itemId', $this->context->cookie->allegroauctionauctionFilter_itemId));
        $price = (float)Tools::getValue($this->list_id.'Filter_price_buy_now');
        $filterToEnd = (int)Tools::getValue($this->list_id.'Filter_itemEndTimeLeft');

        // @todo on error clear filter
        try {
            $auctions = $this->api->doGetMySellItems(
                array(
                    'searchValue' => Tools::getValue('auctionFilter_itemTitle', $this->context->cookie->allegroauctionauctionFilter_itemTitle), // TODO
                    'itemIds' => $id ? array($id) : null, // Null - not empty array
                    'pageSize' => $pagination,
                    'pageNumber' => ($page-1),
                    'sortOptions' => array(
                        'sortType' => $sortType,
                        'sortOrder' => $sortOrder,
                    ),
                    'filterOptions' => array(
                        'filterToEnd' => $filterToEnd,
                    )
                )
            );

            $auctions_total = (int)$auctions->sellItemsCounter;
        } catch (SoapFault $e) {
            $this->errors[] =  $e->faultstring;
            return;
        }

        $this->_listTotal = $auctions_total;

        $end_flags_array = array(
            2 => '1 '.$this->l('hour'),
            3 => '3 '.$this->l('hours'),
            4 => '6 '.$this->l('hours'),
            5 => '12 '.$this->l('hours'),
            6 => '24 '.$this->l('hours'),
            7 => '2 '.$this->l('days'),
            8 => '3 '.$this->l('days'),
            9 => '4 '.$this->l('days'),
            10 => '5 '.$this->l('days'),
            11 => '6 '.$this->l('days'),
            12 => '7 '.$this->l('days')
        );

        $fields_list = array(
            'itemId' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ),
            'idProduct' => array(
                'title' => $this->l('ID product'),
                'orderby' => false,
                'search' => false,
                'callback' => 'printMapLink',
            ),
            'itemThumbnailUrl' => array(
                'title' => $this->l('Cover'),
                'align' => 'center',
                'callback' => 'displayCover',
                'orderby' => false,
                'search' => false,
            ),
            'itemTitle' => array(
                'title' => $this->l('Auction title'),
                'callback' => 'printAuctionLink'
            ),
            'name' => array(
                'title' => $this->l('Attribute'),
                'orderby' => false,
                'search' => false,
            ),
            'itemStartQuantity' => array(
                'title' => $this->l('Start qty'),
                'align' => 'center',
                'type' => 'int',
                'search' => false,
            ),
            'itemSoldQuantity' => array(
                'title' => $this->l('Sold qty'),
                'align' => 'center',
                'type' => 'int',
                'search' => false,
            ),
            'priceBuyNow' => array(
                'title' => $this->l('Price'),
                'align' => 'right',
                'search' => false,
                'callback' => 'printPriceC',
            ),
            'itemEndTimeLeft' => array(
                'title' => $this->l('Time end'),
                'type' => 'select',
                'list' => $end_flags_array,
                'filter_type' => 'int',
                'filter_key' => 'itemEndTimeLeft',
                'search' => false, // @todo
            ),
        );

        $ccc = array();
        $auctionsExt =  array();
        if(isset($auctions->sellItemsList->item)) {
            foreach ($auctions->sellItemsList->item as $key => $a) {
                // Price
                $a->priceBuyNow = 0;
                foreach ($a->itemPrice->item as $price) {
                    if ($price->priceType == 1) {
                            $a->priceBuyNow = (float)$price->priceValue;
                    }
                }

                // Product
                // @todo create index at the beginig
                $allegroProduct = Db::getInstance()->getRow(
                    'SELECT ap.* FROM `'._DB_PREFIX_.'allegro_auction` aa
                    LEFT JOIN `'._DB_PREFIX_.'allegro_product` ap
                        ON (ap.`id_allegro_product` = aa.`id_allegro_product`)
                    WHERE aa.`id_auction` = '.(float)$a->itemId
                );

                $a->idProduct = '';
                if ($allegroProduct['id_allegro_product']) {
                    $a->idProduct = $allegroProduct['id_product'];
                } else {
                    $auctionsExt[] = (float)$a->itemId;
                }

                if ($a->idProduct) {
                    // Name
                    $a->name = (string)Db::getInstance()->GetValue('SELECT GROUP_CONCAT(agl.`name`, \' - \',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \', \') as attribute_designation
                        FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                        LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                        LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$this->context->language->id.')
                        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$this->context->language->id.')
                        WHERE pac.id_product_attribute = '.(int)$allegroProduct['id_product_attribute'].'
                        GROUP BY pac.id_product_attribute');
                } else {
                    $a->name = '--';
                }

                $ccc[] = (array)$a;
            }
        }

        // Get list of auctions created directly in allegro
        if (count($auctionsExt) && Configuration::get('ALLEGRO_A_LIST_SUGGEST')) {
            $auctionsExt = array_chunk($auctionsExt, 25);
            foreach ($auctionsExt as $auctionsExtChunk) {
                try {
                    $this->auctionsExt = $this->api->doGetItemsInfo(
                        array('itemsIdArray' => $auctionsExtChunk, 'getDesc' => 1)
                    )->arrayItemListInfo->item;
                } catch (SoapFault $e) {
                    $this->errors[] =  $e->faultstring;
                    return;
                }
            }
        }


        $helper = new HelperList();
        $this->setHelperDisplay($helper);
        $helper->identifier = 'itemId';
        $helper->toolbar_btn = array();

        // @todo Not working in PS 1.5
        //$helper->_pagination = $this->_pagination;

        $list = $helper->generateList($ccc, $fields_list);

        return $list;
    }

    public function printAuctionLink($value, $row)
    {
        $url = AllegroAuction::getAuctionUrl($row['itemId'], false, $this->api->isSandbox());

        return '<a href="'.$url.'">'.$value.'</a>';
    }

    public function ajaxProcessAuctionMap()
    {
        $errors = array();

        $idOffer = (float)Tools::getValue('id_offer');
        $idProduct = (string)Tools::getValue('id_product');
        $idProductAttribute = 0;

        if (strpos($idProduct, '/') !== false) {
            list($idProduct, $idProductAttribute) = explode('/', $idProduct);
        }

        $idAllegroProduct = AllegroProduct::getIdByPAId($idProduct, $idProductAttribute);

        // Add product to index
        if (!$idAllegroProduct) {
            $idAllegroProduct = $this->addProductToIndex(
                array('id_product' => $idProduct,'id_product_attribute' => $idProductAttribute)
            );
        }

        if (!$idOffer) {
            $errors[] = $this->l('No id offer');
        } elseif (!$idProduct) {
            $errors[] = $this->l('No id product');
        } else {
            $allegroAuction = new AllegroAuction();
            $allegroAuction->id_auction        = (float)$idOffer;
            $allegroAuction->id_allegro_product = (int)$idAllegroProduct;
            $allegroAuction->id_allegro_account = (int)$this->api->getAccountId();
            $allegroAuction->title             = Tools::getValue('title');
            $allegroAuction->duration          = (int)Tools::getValue('duration');
            $allegroAuction->date_start        = null;
            $allegroAuction->is_standard       = (int)Tools::getValue('is_standard');
            $allegroAuction->price             = (float)Tools::getValue('price');
            $allegroAuction->cost_info         = '';
            $allegroAuction->quantity          = (int)Tools::getValue('quantity');
            $allegroAuction->id_shop           = (int)$this->context->shop->id;
            $allegroAuction->date_add          = $allegroAuction->date_start;

            try {  
                $allegroAuction->add();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        die(Tools::jsonEncode(array('error' => implode(',', $errors), 'id' => $idProduct)));
    }

    public function ajaxProcessGetProducts()
    {
        $context = Context::getContext();

        $query = Tools::getValue('q', false);

        if (!$query || strlen($query) < 2) {
            die();
        }

        $sql = 'SELECT p.`id_product`, p.`reference`, pl.`name`, p.`cache_default_attribute`
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
                WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\' OR p.id_product LIKE \'%'.pSQL($query).'%\')'.
                ' GROUP BY p.id_product
                LIMIT 100';

        $items = Db::getInstance()->executeS($sql);

        foreach ($items as $item) {
            $item['name'] = str_replace('|', '&#124;', $item['name']);
            echo trim('['.(int)$item['id_product'].'] '.$item['name']).(!empty($item['reference']) ? ' (ref: '.$item['reference'].')' : '').'|'.(int)($item['id_product'])."\n";

            // check if product have combination
            if (Combination::isFeatureActive() && ($item['cache_default_attribute'] || version_compare(_PS_VERSION_, '1.6', '<'))) {
                $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                            a.`id_attribute`
                        FROM `'._DB_PREFIX_.'product_attribute` pa
                        '.Shop::addSqlAssociation('product_attribute', 'pa').'
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                        LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                        LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$context->language->id.')
                        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$context->language->id.')
                        WHERE pa.`id_product` = '.(int)$item['id_product'].'
                        GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                        ORDER BY pa.`id_product_attribute`';

                $combinations = Db::getInstance()->executeS($sql);
                if (!empty($combinations)) {
                    $results = array();
                    foreach ($combinations as $k => $combination) {
                        $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                        !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' '.$combination['group_name'].'-'.$combination['attribute_name']
                        : $results[$combination['id_product_attribute']]['name'] = $item['name'].' '.$combination['group_name'].'-'.$combination['attribute_name'];
                        if (!empty($combination['reference'])) {
                            $results[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                        } else {
                            $results[$combination['id_product_attribute']]['reference'] = !empty($item['reference']) ? $item['reference'] : '';
                        }
                    }

                    foreach ($results as $k => $combination) {
                        $combination['name'] = str_replace('|', '&#124;', $combination['name']);
                        echo trim('['.(int)$item['id_product'].'/'.(int)$combination['id_product_attribute'].'] '.$combination['name']).(!empty($combination['reference']) ? ' (ref: '.$combination['reference'].')' : '').'|'.(int)($item['id_product']).'/'.(int)($combination['id_product_attribute'])."\n";
                    }
                }
            } 
        }

        die();
    }

    protected function processBulkFinish()
    {
        $auctionsIds = Tools::getValue($this->table.'Box', array());
        if(count($auctionsIds)) {

            $auctionsIdsChunk = array_chunk($auctionsIds, 25);
            foreach ($auctionsIdsChunk as $auctionsIds) {
                $auctionsIdsChunkFormated = array();
                foreach ($auctionsIds as $auctionId) {
                    $auctionsIdsChunkFormated['finishItemsList'][] = array(
                        'finishItemId' => (float)$auctionId,
                    );
                }

                try {
                    $this->api->doFinishItems($auctionsIdsChunkFormated);
                } catch (Exception $e) {
                    $this->warnings[] = $e->getMessage();
                }
            }

            Db::getInstance()->update(
                'allegro_auction', 
                array('status' => AllegroAuction::STATUS_FINISHED),
                'id_auction IN ('.implode(',', $auctionsIds).')'
            );
        }

        if (empty($this->errors)) {
            $this->redirect_after = self::$currentIndex.'&conf=101&token='.$this->token;
        }
    }
}
