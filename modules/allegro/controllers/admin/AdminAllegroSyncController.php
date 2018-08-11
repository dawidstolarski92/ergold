<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroSyncController extends ParentAllegroController
{
    public $bootstrap = true;

    private static $AJAX_SYNC_N = 5;
    private static $AJAX_RELIST_N = 3;
    private static $AJAX_ORDER_N = 1;

    public function __construct()
    {
        parent::__construct();
        parent::initApi();
    }


    public function initContent()
    {
        if (!$this->ajax) {
            $this->renderAccountsBar();

            if (empty($this->moduleErrors)) {

                try {
                    $manager = new AllegroSyncManager($this->api);

                    $transaction = array();
                    if ((int)Configuration::get('ALLEGRO_ORDER_SYNC')) {
                        $transaction = $manager->getTransactionsFromDealJournal();
                        foreach ($transaction as $key => &$deal) {
                            $deal->auctionUrl = AllegroAuction::getAuctionUrl($deal->dealItemId, false, (bool)$this->api->isSandbox());
                            $deal->dealEventDate = date('Y-m-d H:i:s', $deal->dealEventTime);
                        }
                    }

                    $auctionsDB = AllegroAuction::getAuctionsById($manager->getAuctionsIdsForStockSync($this->context));

                 } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                    return;
                }

                $this->context->smarty->assign(array(
                    'outdate_auctions' => $auctionsDB,
                    'products_relist' => $manager->getProductsForRelist($this->context),
                    'transactions' => $transaction,

                    'update_stock_url' => self::$currentIndex.'&token='.$this->token.'&ajax=1&action=update_stock',
                    'sync_url' => self::$currentIndex.'&token='.$this->token.'&ajax=1&action=',
                    'module_realpath' => realpath(getcwd().'/../modules/allegro/'),
                    'module_url' => Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'].$this->module->getPathUri(),
                    'key' => $this->module->getKey(),
                    // Config
                    'PS_STOCK_MANAGEMENT' => (int)Configuration::get('PS_STOCK_MANAGEMENT'),
                    'ALLEGRO_ORDER_SYNC' => (int)Configuration::get('ALLEGRO_ORDER_SYNC'),
                    'ALLEGRO_FINISH_IF_DISABLED' => (int)Configuration::get('ALLEGRO_FINISH_IF_DISABLED'),
                    'ALLEGRO_DEV_MODE' => (int)Configuration::get('ALLEGRO_DEV_MODE'),
                ));

                $this->display = 'view';
            }
        }

        parent::initContent();
    }


    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }


    public function ajaxProcessUpdateStock()
    {
        $cursor = (int)Tools::getValue('cursor');
        $messages = array();
        $continue = true;

        $manager = new AllegroSyncManager($this->api);

        // In a first step get auction Ids list and store in session
        if ($cursor === 0) {
            $_SESSION['aslist'] = $manager->getAuctionsIdsForStockSync();
            $manager->updateJournalPoints();
        }

        // Pop `n` auctions from list and synchronize
        $auctionsIdsChunk = array_splice($_SESSION['aslist'], 0, self::$AJAX_SYNC_N);

        if (count($auctionsIdsChunk)) {
            $messages[] = $this->l('Chunk of auctions:'.implode(',', $auctionsIdsChunk));
            /*$continue = */$manager->stockSync($auctionsIdsChunk);

            if (count($manager->getLog())) {
                $messages = array_merge($messages, $manager->getLog());
            }
        } else {
            $continue = false;
        }
        
        die(Tools::jsonEncode(array(
            'continue' => (int)$continue,
            'messages' => $messages
        )));
    }


    public function ajaxProcessOrderSync()
    {
        $cursor = (int)Tools::getValue('cursor');
        $messages = array();

        $manager = new AllegroSyncManager($this->api);

        // In a first step get transactions list and store in session
        if ($cursor === 0) {
            $_SESSION['trlist'] = $manager->getTransactionsFromDealJournal();
            $manager->updateTransJournalPoints();
            $messages[] = 'Initializing, nb. transctions:'.count($_SESSION['trlist']);
        }

        // Pop `n` ransactions from list and synchronize
        $transactionsIdsChunk = array_splice($_SESSION['trlist'], 0, self::$AJAX_ORDER_N);

        if (count($transactionsIdsChunk)) {
            $manager->importOrders($transactionsIdsChunk);
        }

        die(Tools::jsonEncode(array(
            'continue' => (int)count($transactionsIdsChunk),
            'messages' => array_merge($messages, $manager->getLog())
        )));
    }


    /*
    * Get `n` allegro products and relist until there is no more products to relist
    */
    public function ajaxProcessRelist()
    {
        $cursor = (int)Tools::getValue('cursor');
        $messages = array();

        $manager = new AllegroSyncManager($this->api);

        // Clean errors before first step
        if ($cursor === 0) {
            $allegroProductsRelist = $manager->getProductsForRelist($this->context);
            $allegroProductsIds = getArrByKey($allegroProductsRelist, 'id_allegro_product');

            $_SESSION['prlist'] = $allegroProductsRelist;

            if (count($allegroProductsIds)) {
                Db::getInstance()->update('allegro_product', 
                    array('cache_relist_error' => null),
                    'id_allegro_product IN ('.implode(',', $allegroProductsIds).')'
                );
            }
        }

        if (count($_SESSION['prlist'])) {

            $productsIdsChunk = array_splice($_SESSION['prlist'], 0, self::$AJAX_RELIST_N);

            foreach ($productsIdsChunk as $allegroProductArray) {
                $allegroProduct = new AllegroProduct((int)$allegroProductArray['id_allegro_product']);
                try {
                    $auction = $manager->createAuction(
                        (int)$allegroProduct->id, false, false, (int)$allegroProductArray['id_shop']
                    );
                    $messages[] = 'Auction created sucefully: '.(float)$auction->id_auction;
                } catch (SoapFault $e) {
                    // Save error code for product
                    $errorCode = 'UNKNOWN_ERROR';
                    if (isset($e->faultcode) && $e->faultcode && strlen($e->faultcode) <= 255) {
                        $errorCode = $e->faultcode;
                    }
                    
                    $allegroProduct->cache_relist_error = $errorCode;
                    $allegroProduct->save();

                    $messages[] = 'Unable to create auction for product: '.(int)$allegroProduct->id_product.' ('.$errorCode.')';
                }
            }
        }

        die(Tools::jsonEncode(array(
            'continue' => (int)count($_SESSION['prlist']),
            'messages' => $messages
        )));
    }


    /*
    * Iterate over all allegro offres and sync each one
    */
    public function ajaxProcessFullSync()
    {
        $p = (int)Tools::getValue('cursor');
        $n = self::$AJAX_SYNC_N;

        $manager = new AllegroSyncManager($this->api);

        $context = $this->context;

        // Get auctions for update
        $sql = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT aa.*, ap.*, p.`reference`, sa.`quantity` AS stock_quantity,
        aa.`id_shop`, s.`name` AS shop_name
        FROM `'._DB_PREFIX_.'allegro_auction` aa
        JOIN `'._DB_PREFIX_.'allegro_product` ap ON aa.`id_allegro_product` = ap.`id_allegro_product`
        JOIN `'._DB_PREFIX_.'product` p ON ap.`id_product` = p.`id_product`
        JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop` = aa.`id_shop`
        JOIN `'._DB_PREFIX_.'shop_group` sg ON sg.`id_shop_group` = s.`id_shop_group`
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (ap.`id_product` = sa.`id_product`
            AND ap.`id_product_attribute` = sa.`id_product_attribute` ';

        // Set shop or group by "share_stock" param
        $sql .= 'AND ((sg.`share_stock` = 1 AND sa.`id_shop` = 0 AND sa.`id_shop_group` = s.`id_shop_group`) OR
                    (sg.`share_stock` = 0 AND sa.`id_shop` = s.`id_shop` AND sa.`id_shop_group` = 0))) ';

        // Where
        $sql .= ' WHERE aa.`id_allegro_account` = '.(int)$this->api->getAccountId().'
        AND aa.`status` < '.(int)AllegroAuction::STATUS_FINISHED.' ';

        // Handle shop context
        if ($context) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= 'AND aa.`id_shop` = '.(int)$context->shop->id.' ';
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shopGroup = $context->shop->getGroup();
                $sql .= ' AND aa.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')';
            } else { // Context all shops
                // Nothing
            }
        }

        // Limit
        $sql .= ' LIMIT '.((int)$p*(int)$n).','.(int)$n;

        $offers = Db::getInstance()->executeS($sql);
        $total = Db::getInstance()->GetValue('SELECT FOUND_ROWS()');

        if (count($offers)) {
            foreach ($offers as $key => $offer) {

                $dbOffer = AllegroAuction::getByAuctionID($offer['id_auction']);
                $allegroProduct = new AllegroProduct((int)$offer['id_allegro_product']);
                
                $fieldBuilder = new AllegroFieldBuilder();

                $manager->getProductFieldsList($fieldBuilder, $allegroProduct, (int)$offer['id_shop']);

                // New desc API bug fix
                $fieldBuilder->addField(AFField::FID_DESC, '--');

                // @todo get info about transactions and cut off restricted fields like title

                /**
                 * afterSalesServiceConditions && additionalServicesGroup
                 **/
                $allegroProductAccount = Db::getInstance()->GetRow('
                    SELECT * FROM `'._DB_PREFIX_.'allegro_product_account`
                    WHERE `id_allegro_product` = '.(int)$allegroProduct->id.'
                    AND `id_allegro_account` = '.(int)$this->api->getAccountId().'
                    AND `id_shop` = '.(int)$offer['id_shop'].'
                ');

                $impliedWarranty = (!empty($allegroProductAccount['implied_warranty'])
                    ? $allegroProductAccount['implied_warranty']
                    : Configuration::get('ALLEGRO_IMPLIED_WARRANTY_'.(int)$this->api->getAccountId())
                );

                $returnPolicy = (!empty($allegroProductAccount['return_policy'])
                    ? $allegroProductAccount['return_policy']
                    : Configuration::get('ALLEGRO_RETURN_POLICY_'.(int)$this->api->getAccountId())
                );

                $warranty = (!empty($allegroProductAccount['warranty']) 
                    ? $allegroProductAccount['warranty']
                    : Configuration::get('ALLEGRO_WARRANTY_'.(int)$this->api->getAccountId())
                );

                $additionalServices = (!empty($allegroProductAccount['additional_services'])
                    ? $allegroProductAccount['additional_services']
                    : null
                );

                try {
                    // Get current fields
                    $fieldcCurrent = $this->api->doGetItemFields(array(
                        'itemId' => (float)$offer['id_auction']
                    ))->itemFields->item;

                    $fieldsToRemove = array();
                    foreach ($fieldcCurrent as $fc) {
                        // If field not exist in new field set add it to "to remove" array
                        if (!$fieldBuilder->getField($fc->fid)) {
                            $fieldsToRemove[] = $fc->fid;
                        }
                    }

                    /**
                     * If on auction are offers we have to skip some fields
                     * for example title, price that can not be updates if there are buy offers
                     **/
                    $offers = $this->api->doGetBidItem2(array(
                        'itemId' => (float)$offer['id_auction']
                    ));

                    if (!empty($offers->biditemList->item)) {
                        $fieldBuilder->removeField(AFField::FID_DESC);
                        $fieldBuilder->removeField(AFField::FID_DESC_2);
                        $fieldBuilder->removeField(AFField::FID_TITLE);
                        $fieldBuilder->removeField(AFField::FID_PRICE);
                    }
                    $fieldBuilder->removeField(AFField::FID_QTY);


                    // Modify offer
                    $res = $this->api->doChangeItemFields(array(
                        'itemId' => (float)$offer['id_auction'],
                        'fieldsToModify' => $fieldBuilder->build(),
                        'fieldsToRemove' => $fieldsToRemove,
                        'afterSalesServiceConditions' => array(
                            'impliedWarranty' => $impliedWarranty,
                            'returnPolicy' => $returnPolicy,
                            'warranty' => $warranty,
                        ),
                        'additionalServicesGroup' => $additionalServices,
                    ));

                    if (isset($res->changedItem->itemId)) {
                        // Update auction in DB
                        if (empty($offers->biditemList->item)) {
                            $dbOffer->title = $fieldBuilder->getField(AFField::FID_TITLE);
                            $dbOffer->duration = $fieldBuilder->getField(AFField::FID_DURATION);
                            $dbOffer->price = $fieldBuilder->getField(AFField::FID_PRICE);
                        }
                        $dbOffer->status = AllegroAuction::STATUS_UPDATED;
                        $dbOffer->save();
                    }
                } catch (SoapFault $e) {

                    // Offer not exists (deleted?)
                    if ($e->faultcode == 'ERR_INCORRECT_ITEM_ID' || $e->faultcode == 'ERR_INVALID_ITEM_ID') {
                        $dbOffer = new AllegroAuction((int)$offer['id_allegro_auction']);
                        $dbOffer->status = AllegroAuction::STATUS_FINISHED;
                        $dbOffer->save();
                    }

                    // not ok
                    PrestaShopLogger::addLog('Allegro - full sync ('.(float)$offer['id_auction'].') - '.$e->faultstring, 3, null, null, null, true);
                    continue;
                }
            }
        } else {
            // End process
            die(Tools::jsonEncode(array('continue' => 0)));
        }

        // Continue
        die(Tools::jsonEncode(array(
            'continue' => 1,
            'cursor' => ($p+1)
        )));
    }
}
