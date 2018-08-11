<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroSyncManager extends ModuleAdminController
{
    private $api;
    private $allegroAccount;

    private $lastDealJournal;
    private $lastOfferJournal;
    private $lastTransactionJournal;

    private $log = array();

    const NB_TRANSACTIONS = 25; // max. 25

    const DEAL_TYPE_NEW = 1;
    const DEAL_TYPE_FOD = 2;
    const DEAL_TYPE_CANCELED = 3;
    const DEAL_TYPE_PAID = 4;

    public function __construct($api)
    {
        $this->api = $api;
        // @todo
        $this->allegroAccount = new AllegroAccount($this->api->getAccountId());

        $this->lastDealJournal = (float)Configuration::get('ALLEGRO_JOURNAL1_STOCK_'.$this->api->getAccountId());
        $this->lastOfferJournal = (float)Configuration::get('ALLEGRO_JOURNAL2_STOCK_'.$this->api->getAccountId());
        $this->lastTransactionJournal = (float)Configuration::get('ALLEGRO_ORDER_SYNC_LAST_DEAL_'.$this->api->getAccountId());
    }

    public function getLog()
    {
        return $this->log;
    }

    private function addLogMsg($msg)
    {
        $this->log[] = $msg;
    }

    public function getAuctionsIdsForStockSync($context = null)
    {
        $auctions = array_merge($this->getFromOfferJournal(), $this->getFromDealJournal(), array(0));

        $sql = 'SELECT DISTINCT aa.`id_auction`
            FROM `'._DB_PREFIX_.'allegro_auction` aa
            JOIN `'._DB_PREFIX_.'allegro_product` ap ON aa.`id_allegro_product` = ap.`id_allegro_product`
            JOIN `'._DB_PREFIX_.'product` p ON ap.`id_product` = p.`id_product`
            JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop` = aa.`id_shop`
            JOIN `'._DB_PREFIX_.'shop_group` sg ON sg.`id_shop_group` = s.`id_shop_group`
            LEFT JOIN `'._DB_PREFIX_.'allegro_product_shop` aps ON (
                aps.`id_allegro_product` = aa.`id_allegro_product` AND
                aps.`id_shop` = aa.`id_shop`
            )
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (ap.`id_product` = sa.`id_product`
                AND ap.`id_product_attribute` = sa.`id_product_attribute` 
            AND (
                    (sg.`share_stock` = 1 AND sa.`id_shop` = 0 AND sa.`id_shop_group` = s.`id_shop_group`) OR
                    (sg.`share_stock` = 0 AND sa.`id_shop` = s.`id_shop` 
                        AND sa.`id_shop_group` IN (0'.(Shop::isFeatureActive() ? '' : ',1').')))
            ) 
            WHERE aa.`id_allegro_account` = '.(int)$this->api->getAccountId().'
            AND aps.`stock_sync` = 1
            AND aa.`date_start` <= "'.date("Y-m-d H:i:s").'"
            AND (
                aa.`status` < '.(int)AllegroAuction::STATUS_FINISHED.' OR 
                aa.`id_auction` IN ('.implode(',', $auctions).')
            )
            AND (0
                '.((int)Configuration::get('ALLEGRO_STOCK_SYNC') == 1 ? 'OR sa.`quantity` != aa.`quantity`' : '').'
                '.((int)Configuration::get('ALLEGRO_FINISH_IF_DISABLED') ? 'OR p.`active` != 1' : '').'
                OR aa.`id_auction` IN ('.implode(',', $auctions).') 
            )';

        // Handle shop context
        if (isset($context)) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= 'AND aa.`id_shop` = '.(int)$context->shop->id.' ';
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $sql .= ' AND aa.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')';
            }
        }

        $auctionsIDs = Db::getInstance()->executeS($sql);
        $auctionsIDsDB = getArrByKey($auctionsIDs, 'id_auction');

        return $auctionsIDsDB;
    }

    public function getProductsForRelist($context = null, $n = null)
    {
        $idLang = 0;
        if ($context) {
            $idLang = $context->language->id;
        }

        $sql = 'SELECT ap.*, apa.*, pl.`name`, sa.`quantity`, sa.`depends_on_stock`,
        apa.`id_shop`, s.`name` AS shop_name
        FROM `'._DB_PREFIX_.'allegro_product` ap
        JOIN `'._DB_PREFIX_.'allegro_product_account` apa ON (
            apa.`id_allegro_account` = '.(int)$this->api->getAccountId().' AND
            ap.`id_allegro_product` = apa.`id_allegro_product`
        )
        JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop` = apa.`id_shop`
        JOIN `'._DB_PREFIX_.'shop_group` sg ON sg.`id_shop_group` = s.`id_shop_group`
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
            ON (sa.`id_product` = ap.`id_product`
            AND sa.`id_product_attribute` = ap.`id_product_attribute`
                AND ((sg.`share_stock` = 1 AND sa.`id_shop` = 0 AND sa.`id_shop_group` = s.`id_shop_group`) OR
                    (sg.`share_stock` = 0 AND sa.`id_shop` = s.`id_shop` AND sa.`id_shop_group` = 0)))
        JOIN `'._DB_PREFIX_.'product` p ON (ap.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
            ap.`id_product` = pl.`id_product` AND
            pl.`id_lang` = '.(int)$idLang.' AND
            pl.`id_shop` = s.`id_shop`
        )
        WHERE apa.`id_allegro_account` = '.(int)$this->api->getAccountId().'
        AND ap.`id_allegro_product` NOT IN (
            SELECT aa.`id_allegro_product`
            FROM  `'._DB_PREFIX_.'allegro_auction` aa
            WHERE aa.`status` < 3
            AND aa.`id_shop` = apa.`id_shop`
        )
        AND (apa.`relist` = 1)
        AND (ap.`relist_min_qty` = "" OR sa.`quantity` >= ap.`relist_min_qty`)
        AND sa.`quantity` > 0 ';

        // If finishing auctions of disabled product is enabled check if product is enabled
        if ((int)Configuration::get('ALLEGRO_FINISH_IF_DISABLED')) {
            $sql .= 'AND p.`active` = 1 ';
        }

        // Handle shop context
        if ($context) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= 'AND apa.`id_shop` = '.(int)$context->shop->id.' ';
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $sql .= ' AND apa.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')';
            }
            
        }

        // Limit
        $sql .= ($n ? ' LIMIT 0,'.(int)$n : '');

        return Db::getInstance()->executeS($sql);
    }

    public function getTransactionsFromDealJournal()
    {
        $dealEventTypes = array(3, 4);
        if (Configuration::get('ALLEGRO_ORDER_SYNC_TYPE') == 2) {
            $dealEventTypes[] = 2;
        }

        $data = array();
        // Get all deals (one iteration = max 100 deals)
        do {
            $res = $this->api->doGetSiteJournalDeals(
                array('journalStart' => $this->lastTransactionJournal)
            );

            @$deals = $res->siteJournalDeals->item;

            if (count($deals)) {
                foreach ($deals as $deal) {
                    $this->lastTransactionJournal = (float)$deal->dealEventId;
                    // Skip deals 
                    if ((time()-(60*60*24*4) < $deal->dealEventTime)
                        && in_array($deal->dealEventType, $dealEventTypes)) {
                        $data[] = $deal;
                    }
                }
            }
        } while (count($deals));

        return $data;
    }

    public function getFromDealJournal()
    {
        $data = array();
        // Get all deals (one iteration = max 100 deals)
        do {
            $res = $this->api->doGetSiteJournalDeals(
                array('journalStart' => $this->lastDealJournal)
            );

            @$deals = $res->siteJournalDeals->item;

            if (count($deals)) {
                foreach ($deals as $deal) {
                    $this->lastDealJournal = (float)$deal->dealEventId;
                    // Skip deals 
                    if (time()-(60*60*24*2) < $deal->dealEventTime) {
                        $data[] = (float)$deal->dealItemId;
                    }
                }
            }
        } while (count($deals));

        return $data;
    }

    public function getFromOfferJournal()
    {
        $data = array();
        // Get all deals (one iteration = max 100 deals)
        do {
            $res = $this->api->doGetSiteJournal(
                array('startingPoint' => $this->lastOfferJournal)
            );

            @$deals = $res->siteJournalArray->item;

            if (count($deals)) {
                foreach ($deals as $deal) {
                    $this->lastOfferJournal = (float)$deal->rowId;
                    // Skip old deals 
                    if (time()-(60*60*24*2) < $deal->changeDate) {
                        if (in_array($deal->changeType, array('end', 'now'))) {
                            $data[] = (float)$deal->itemId;
                        }
                    }
                }
            }
        } while (count($deals));

        return $data;
    }


    public function stockSync($auctionIDs)
    {
        // Split list into chunks (max. API input = 25)
        foreach (array_chunk($auctionIDs, 25) as $auctionIDs) {

            try {
                $auctions = $this->api->doGetItemsInfo(array('itemsIdArray' => $auctionIDs));
            } catch (SoapFault $e) {
                $this->addLogMsg('[ERROR] '.$e->faultstring);
                return false;
            }

            if (empty($auctions->arrayItemListInfo->item)
                && empty($auctions->arrayItemsNotFound->item)
                && empty($auctions->arrayItemsAdminKilled->item)) {
                // No auction - finish
                return true;
            } else {
                // Set status to auctions that not found in API (finished or removed by admin)
                $toUpdateStatus = array();
                if (!empty($auctions->arrayItemsNotFound->item)) {
                    $toUpdateStatus = array_merge($toUpdateStatus, $auctions->arrayItemsNotFound->item);
                }
                if (!empty($auctions->arrayItemsAdminKilled->item)) {
                    $toUpdateStatus = array_merge($toUpdateStatus, $auctions->arrayItemsAdminKilled->item);
                }

                if (count($toUpdateStatus)) {
                    Db::getInstance()->update('allegro_auction', array(
                        'status' => AllegroAuction::STATUS_FINISHED,
                        'date_upd' => date('Y-m-d H:i:s'),
                    ), 'id_auction IN ('.(implode(',', $toUpdateStatus)).')');
                }
                
                if (!empty($auctions->arrayItemListInfo->item)) {
                    $auctionList = $auctions->arrayItemListInfo->item;
                } else {
                    // We get some auctions - continue
                    return true;
                }
            }

            foreach ($auctionList as $key => $apiAuction) {

                $this->addLogMsg('Processing auction: '.$apiAuction->itemInfo->itId);

                $dbAuction = AllegroAuction::getByAuctionID((float)$apiAuction->itemInfo->itId);
                if($dbAuction) {

                    $allegroProduct = new AllegroProduct((int)$dbAuction->id_allegro_product);

                    // Finish aution if product not active
                    $toFinish = false;
                    if ((int)Configuration::get('ALLEGRO_FINISH_IF_DISABLED')) {
                        $product = new Product((int)$allegroProduct->id_product);

                        if (!$product->active) {
                            $toFinish = true;
                        }
                    }

                    // Get quatity sold on auction from last update
                    $deltaQuantity = (int)$apiAuction->itemInfo->itQuantity - (int)$dbAuction->quantity;

                    // If delta < 0 we need to update stock quantity
                    if($deltaQuantity < 0) {
                        $this->addLogMsg('Updating products quantity: '.(int)$allegroProduct->id_product.' ['.$deltaQuantity.']');
                        $this->_updateProductQuantity(
                            $allegroProduct->id_product,
                            $allegroProduct->id_product_attribute,
                            $deltaQuantity
                        );
                    } elseif($deltaQuantity > 0) {
                        // Manually increased qty on auction
                        // or empty qty in DB
                    }

                    // Get current stock quantity
                    $stockQuantity = (int)StockAvailable::getQuantityAvailableByProduct(
                        $allegroProduct->id_product,
                        $allegroProduct->id_product_attribute,
                        $dbAuction->id_shop
                    );

                    // @todo Base product auction in case of product with combinations

                    // Check if auction is not ended
                    if ($apiAuction->itemInfo->itEndingInfo == 1) {

                        $dbAuction->status = AllegroAuction::STATUS_UPDATED;

                        // Check if we need to update quantity on auction
                        // or product to finish (is disabled)
                        // not finish if not bidirectional sync
                        if (($stockQuantity <= 0 && (int)Configuration::get('ALLEGRO_STOCK_SYNC') == 1) || $toFinish) {
                            // Product is out of stock - finish auction
                            try {
                                // @todo collect IDs and use "doFinishItems" fo performance reason
                                $this->api->doFinishItem(array('finishItemId' => $dbAuction->id_auction));
                                $dbAuction->status = AllegroAuction::STATUS_FINISHED;
                                $this->addLogMsg('Finishing auction: '.(float)$dbAuction->id_auction.' (out fo stock)');
                            } catch (SoapFault $e) {
                                // if auction is arleady finished it is OK
                                if($e->errorcode == 'ERR_YOU_CANT_CHANGE_ITEM') {
                                    $dbAuction->status = AllegroAuction::STATUS_FINISHED;
                                } else {
                                    $this->addLogMsg('Unable to finish auction: '.(float)$dbAuction->id_auction.' ('.$e->faultstring.')');
                                }
                            }
                        } elseif ($stockQuantity != (int)$apiAuction->itemInfo->itQuantity) {
                            // If bidirectional stock sync
                            if ((int)Configuration::get('ALLEGRO_STOCK_SYNC') == 1) {
                                /**
                                 * Update auction quantity
                                 *
                                 * We updating "starting quantity" so:
                                 * new quantity = stock quantity + (starting quanity - current quantity)
                                 */
                                $newQuantity = $stockQuantity + (
                                    (int)$apiAuction->itemInfo->itStartingQuantity - (int)$apiAuction->itemInfo->itQuantity
                                );

                                try {
                                    $this->api->doChangeQuantityItem(array(
                                        'itemId' => $dbAuction->id_auction,
                                        'newItemQuantity' => $newQuantity,
                                    ));
                                    $this->addLogMsg('Updating auction quantity: '.(float)$dbAuction->id_auction.' ('.$newQuantity.')');
                                } catch (SoapFault $e) {
                                    $this->addLogMsg('Unable to update auction quantity: '.(float)$dbAuction->id_auction.' ('.$e->faultstring.')');
                                }
                            }
                        }

                        /**
                         * "Buy now" price update if needed
                         * (this is not all full catalog sync!)
                         */
                        if ($allegroProduct->price_sync && ($dbAuction->status != AllegroAuction::STATUS_FINISHED)) {

                            $price = $allegroProduct->genPrice();

                            if ($dbAuction->price != $price && ($dbAuction->price < $price || $allegroProduct->price_sync == 2)) {
                                $res = $this->api->restPut(
                                    'offers/'.(float)$dbAuction->id_auction.'/change-price-commands/[GUID]',
                                    array(
                                        'input' => array(
                                            'buyNowPrice' => array(
                                                'amount' => (float)$price,
                                                'currency' => 'PLN'
                                            )
                                        )
                                    )
                                );
                                $this->addLogMsg('Updating auction price: '.(float)$dbAuction->id_auction.' ('.sprintf('%01.2f', $price).')');

                                if (!empty($res->output->errors)) {
                                    $this->addLogMsg('Unable to update auction price: '.(float)$dbAuction->id_auction.' ('.$res->output->errors[0]->userMessage.')');
                                } else {
                                    $dbAuction->price = (float)$price;
                                }
                            }
                        }
                    } else {
                        $dbAuction->status = AllegroAuction::STATUS_FINISHED;
                    }

                    $dbAuction->quantity = (int)$stockQuantity;
                    $dbAuction->date_upd = date('Y-m-d H:i:s');
                    $dbAuction->save();
                }
            }
        }
    }


    public function updateJournalPoints()
    {
        Configuration::updateValue('ALLEGRO_JOURNAL1_STOCK_'.$this->api->getAccountId(), $this->lastDealJournal, false, 0, 0);
        Configuration::updateValue('ALLEGRO_JOURNAL2_STOCK_'.$this->api->getAccountId(), $this->lastOfferJournal, false, 0, 0);  
    }


    public function updateTransJournalPoints()
    {
        Configuration::updateValue('ALLEGRO_ORDER_SYNC_LAST_DEAL_'.$this->api->getAccountId(), (float)$this->lastTransactionJournal, false, 0, 0); 
    }


    private static function getProductWarheouseId($id_product, $id_product_attribute = 0)
    {
        // Get products warehause
        $query = new DbQuery();
        $query->select('wpl.id_warehouse');
        $query->from('warehouse_product_location', 'wpl');
        $query->where('wpl.id_product = '.(int)$id_product.'
            AND wpl.id_product_attribute = '.(int)$id_product_attribute
        );

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    protected static function _updateProductQuantity(
        $id_product,
        $id_product_attribute = 0,
        $deltaQuantity,
        $price = 0
    ) {
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            if(Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                Product::usesAdvancedStockManagement($id_product)) {

                $idWarehouse = self::getProductWarheouseId($id_product, $id_product_attribute);
                $warehouse = new Warehouse((int)$idWarehouse);

                if(Validate::isLoadedObject($warehouse)) {

                    // Set employee
                    $context = Context::getContext();
                    if(!isset($context->employee->id)) {
                        $context->employee = new Employee((int)Configuration::get('ALLEGRO_EMPLOYEE'));
                    }

                    // Add stock
                    $stock_manager = StockManagerFactory::getManager();

                    // Add product to warehause
                    if($deltaQuantity > 0) {
                        $status = $stock_manager->addProduct(
                            $id_product,
                            $id_product_attribute,
                            $warehouse,
                            abs($deltaQuantity),
                            1, // @todo reason
                            $price
                        );
                    } elseif ($deltaQuantity < 0) {
                        $status = $stock_manager->removeProduct(
                            $id_product,
                            $id_product_attribute,
                            $warehouse,
                            abs($deltaQuantity),
                            2 // @todo reason
                        );
                    }
                } else {
                    throw new Exception('Unable to determine products warehouse.');
                }
            }

            StockAvailable::updateQuantity($id_product, $id_product_attribute, $deltaQuantity);
        }
    }

    public function importOrders($transactions)
    {
        $id_lang = $this->allegroAccount->id_language;
        $id_currency = $this->allegroAccount->id_currency;
        $id_carrier = (int)Configuration::get('ALLEGRO_CARRIER');
        $tax_rate = 1;

        $shippingTaxRate = 1.23; // @todo

        // Get allegro shipping options
        try {
            $shippingApi = $this->api->doGetShipmentData()->shipmentDataList->item;
        } catch (Exception $e) {
            $this->addLogMsg('[ERROR] '.$e->getMessage());
            return false;
        }

        // Split list of transactions to chunks (25 items each)
        $transactionsChunks = array();
        foreach ($transactions as $k => $t) {
            $transactionsChunks[floor($k/25)][] = (float)$t->dealTransactionId;
        }

        // Max. 25 transactions for API ("getPostBuyFormsDataForSellers")
        foreach ($transactionsChunks as $transactionsChunk) {

           try {
                $payForms = $this->api->doGetPostBuyFormsDataForSellers(
                    array('transactionsIdsArray' => $transactionsChunk)
                )->postBuyFormData->item;
            } catch (Exception $e) {
                $this->addLogMsg('[ERROR] '.$e->getMessage());
                return true;
            }

            // Iterate over each pay form
            foreach ($payForms as $payForm) {

                // Test if we have already an crated order based on current form ID
                $query = new DbQuery();
                $query->select('ao.id_order');
                $query->from('allegro_order', 'ao');
                $query->where('ao.form_id = '.(float)$payForm->postBuyFormId);

                $id_order = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                do {
                    $reference = Order::generateReference();
                } while (Order::getByReference($reference)->count());

                // Get API carrier object for later use
                $orderCarrierApi = null;
                if (count($shippingApi)) {
                    foreach ($shippingApi as $carrierApi) {
                        if ($carrierApi->shipmentId == $payForm->postBuyFormShipmentId) {
                            $orderCarrierApi = $carrierApi;

                            // Check if we have mapped this carrier to PrestaShop carrier
                            if ($cId = (int)Configuration::get('ALLEGRO_SHIPPING_'.(int)$payForm->postBuyFormShipmentId)) {
                                $carrier = new Carrier($cId);
                                if (!$carrier->deleted) {
                                    $id_carrier = $cId;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($payForm->postBuyFormPayStatus) {
                    $payment_method = 'PayU ('.$payForm->postBuyFormPayType.')';
                } else {
                    $payment_method = 'Allegro ('.$payForm->postBuyFormPayType.')';
                }

                // PayU
                switch ($payForm->postBuyFormPayStatus) {
                    case 'Rozpoczęta':
                        $id_order_state = (int)Configuration::get('ALLEGRO_PAYU_NEW');
                        break;
                    case 'Zakończona':
                        $id_order_state = (int)Configuration::get('ALLEGRO_PAYU_FINISHED');
                        break;
                    case 'Anulowana':
                        $id_order_state = (int)Configuration::get('ALLEGRO_PAYU_CANCELED');
                        break;
                    default:
                        $id_order_state = (int)Configuration::get('ALLEGRO_PAYU_ERROR');
                        break;
                }

                // Handle cash on delivery
                if ($payForm->postBuyFormPayType == 'collect_on_delivery') {
                    $id_order_state = (int)Configuration::get('ALLEGRO_COD');
                }

                // Handle wire transfer
                if ($payForm->postBuyFormPayType == 'wire_transfer') {
                    $id_order_state = (int)Configuration::get('ALLEGRO_WIRE_TRANSFER');
                }

                // We have an order so only update status
                if ($id_order) {

                    $order = new Order((int)$id_order);
                    $order_state = new OrderState($id_order_state);

                    //$current_order_state = $order->getCurrentOrderState();

                    if ($order->getCurrentState() != $id_order_state) {

                        $history = new OrderHistory();
                        $history->id_order = $order->id;

                        $use_existings_payment = !$order->hasInvoice();
                        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                        $history->addWithemail(true/*, $templateVars*/);
                    }

                    $this->addLogMsg("Order #{$order->reference} status updated:{$order_state->name[$id_lang]}");

                    continue; // Go to next pay form
                }

                // Check if all products are from shop and set correct shop ID
                $id_shop = null;
                foreach ($payForm->postBuyFormItems->item as $payFormProduct) {

                    $allegroAuction = AllegroAuction::getByAuctionID((float)$payFormProduct->postBuyFormItId);

                    // Check if auction is an ghost auction
                    if ($allegroAuction || (int)Configuration::get('ALLEGRO_ORDER_SYNC') == 2 ) {
                        /*
                         * If all auction was created from same store put order in that store,
                         * otherwise use default store from module config.
                         */
                         if ($allegroAuction) {
                            if ($id_shop && $id_shop != $allegroAuction->id_shop) {
                                // Mixed shops - use default
                                break;
                            } else {
                                // Same shops
                                $id_shop = (int)$allegroAuction->id_shop;
                            }
                         } else {
                             break;
                         }
                    } else {
                        // Auction created outside of module - continue
                        continue 2;
                    }
                }

                if (!$id_shop) {
                    $id_shop = (int)Configuration::get('ALLEGRO_ORDER_ID_SHOP');
                }

                $shop = new Shop($id_shop);

                if ($shop->deleted) {
                    throw new Exception("Shop is deleted");
                }

                $id_shop_group = $shop->id_shop_group;

                $totalPaidTaxIncl = 0;
                $totalPaidTaxExcl = 0;
                $shippingTaxIncl = 0;
                $shippingTaxExcl = 0;
                $totalProductsTaxIncl = 0;
                $totalProductsTaxExcl = 0;

                // Customer Object
                $customer = Customer::getCustomersByEmail($payForm->postBuyFormBuyerEmail);
                if(!isset($customer[0]['id_customer'])) {
                    list($firstname, $lastname) = explode(' ', $payForm->postBuyFormShipmentAddress->postBuyFormAdrFullName);

                    $customer = new Customer();
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->email = $payForm->postBuyFormBuyerEmail;
                    $customer->passwd = md5(substr(md5(_COOKIE_KEY_.$customer->email), 0, 16)); // 16 chars form MD5
                    $customer->note = 'Allegro: '.$payForm->postBuyFormBuyerLogin;
                    $customer->newsletter = (int)Configuration::get('ALLEGRO_CUSTOMER_NEWSLETTER');
                    $customer->id_shop = $id_shop;
                    $customer->id_lang = $id_lang;

                    try {
                        $customer->add();
                    } catch (Exception $e) {
                        $this->addLogMsg('[ERROR] '.$e->getMessage());
                        continue;
                    }
                } else {
                    $customer = new Customer((int)$customer[0]['id_customer']);
                }

                // Clear address date (for alias)
                $payForm->postBuyFormShipmentAddress->postBuyFormCreatedDate = '';
                $payForm->postBuyFormInvoiceData->postBuyFormCreatedDate = '';

                $addressAlias = strtoupper(substr(md5(json_encode($payForm->postBuyFormShipmentAddress)), 0, 6));
                $idShippingAddress = $this->getCustomerAddressIdByAlias($customer->id, $addressAlias);

                // Prevent no phone number exception
                $nullPhone = null;
                if ((int)Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
                    $nullPhone = '000-000-000';
                }

                // Addresses
                if (!$idShippingAddress) {
                    list($firstname, $lastname) = explode(' ', $payForm->postBuyFormShipmentAddress->postBuyFormAdrFullName);

                    $shipping_address = new Address();
                    $shipping_address->id_customer = $customer->id;
                    $shipping_address->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT'); // @todo
                    $shipping_address->alias = $addressAlias;
                    $shipping_address->lastname = $lastname;
                    $shipping_address->firstname = $firstname;
                    $shipping_address->address1 = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrStreet;
                    $shipping_address->postcode = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrPostcode;
                    $shipping_address->city = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrCity;
                    $shipping_address->phone = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrPhone
                        ? (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrPhone
                        : $nullPhone;
                    $shipping_address->vat_number = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrNip;
                    $shipping_address->company = (string)$payForm->postBuyFormShipmentAddress->postBuyFormAdrCompany;
                } else {
                    $shipping_address = new Address($idShippingAddress);
                }


                $addressAlias = strtoupper(substr(md5(json_encode($payForm->postBuyFormInvoiceData)), 0, 6));
                $idInvoiceAddress = $this->getCustomerAddressIdByAlias($customer->id, $addressAlias);

                // Check for invoice address
                if((string)$payForm->postBuyFormInvoiceData->postBuyFormAdrCompany || (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrFullName) {

                    if (!$idInvoiceAddress) {
                        if ($payForm->postBuyFormInvoiceData->postBuyFormAdrFullName) {
                            list($firstname, $lastname) = explode(' ', $payForm->postBuyFormInvoiceData->postBuyFormAdrFullName);
                        } else {
                            $firstname = '--';
                            $lastname = '--';
                        }

                        $invoice_address = new Address();
                        $invoice_address->id_customer = $customer->id;
                        $invoice_address->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT'); // @todo
                        $invoice_address->alias = $addressAlias;
                        $invoice_address->lastname = $lastname;
                        $invoice_address->firstname = $firstname;
                        $invoice_address->address1 = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrStreet;
                        $invoice_address->postcode = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrPostcode;
                        $invoice_address->city = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrCity;
                        $invoice_address->phone = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrPhone
                            ? (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrPhone
                            : $nullPhone;
                        $invoice_address->vat_number = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrNip;
                        $invoice_address->company = (string)$payForm->postBuyFormInvoiceData->postBuyFormAdrCompany;
                    } else {
                        $invoice_address = new Address($idInvoiceAddress);
                    }
                }

                try {
                    $shipping_address->save();
                    if(isset($invoice_address)) {
                        $invoice_address->save();
                    }
                } catch (Exception $e) {
                    $this->addLogMsg('[ERROR] '.$e->getMessage());
                    continue;
                }

                // Cart Object
                $cart = new Cart();
                $cart->id_currency = $id_currency;
                $cart->id_customer = $customer->id;
                $cart->add();

                // Order Object
                $order = new Order();
                $order->id_address_delivery = $shipping_address->id;
                $order->id_address_invoice = (isset($invoice_address)) ? $invoice_address->id : $shipping_address->id;
                $order->id_cart = $cart->id;
                $order->reference = $reference;
                $order->id_shop = $id_shop;
                $order->id_shop_group = $id_shop_group;
                $order->id_currency = $id_currency;
                $order->id_lang = $id_lang;
                $order->id_customer = $customer->id;
                $order->id_carrier = $id_carrier;
                $order->payment = $payment_method;
                $order->recyclable = 0;
                $order->module = 'allegro';
                $order->secure_key = $customer->secure_key;
                $order->conversion_rate = 1;


                // Calculate prices
                // @todo
                $totalPaidTaxIncl = (float)$payForm->postBuyFormAmount;
                $totalPaidTaxExcl = 0;
                $shippingTaxIncl = (float)$payForm->postBuyFormPostageAmount;
                $shippingTaxExcl = round($shippingTaxIncl/$shippingTaxRate, 2);
                $totalProductsTaxIncl = 0;
                $totalProductsTaxExcl = 0;
                $taxRate = 0;
                foreach ($payForm->postBuyFormItems->item as $payFormProduct) {

                    $allegroAuction = AllegroAuction::getByAuctionID((float)$payFormProduct->postBuyFormItId);
                    if ($allegroAuction && $allegroAuction->id_allegro_auction) {
                        $allegroProduct = new AllegroProduct((int)$allegroAuction->id_allegro_product);
                        $product = new Product((int)$allegroProduct->id_product);

                        $taxRate = (int)$product->getTaxesRate();
                    }
                    
                    $unitPriceTaxIncl = (float)$payFormProduct->postBuyFormItAmount/(int)$payFormProduct->postBuyFormItQuantity;
                    $unitPriceTaxExcl = round($unitPriceTaxIncl/(($taxRate+100)/100), 2);

                    $totalProductsTaxIncl += ($unitPriceTaxIncl*(int)$payFormProduct->postBuyFormItQuantity);
                    $totalProductsTaxExcl += ($unitPriceTaxExcl*(int)$payFormProduct->postBuyFormItQuantity);
                }

                $totalPaidTaxExcl = $totalProductsTaxExcl + $shippingTaxExcl;
                
                $order->total_paid = $totalPaidTaxIncl;
                $order->total_paid_real = $totalPaidTaxIncl;
                $order->total_paid_tax_incl = $totalPaidTaxIncl;
                $order->total_paid_tax_excl = $totalPaidTaxExcl;

                $order->total_products_wt = $totalProductsTaxIncl;
                $order->total_products = $totalProductsTaxExcl;

                $order->total_shipping = $shippingTaxIncl;
                $order->total_shipping_tax_incl = $shippingTaxIncl;
                $order->total_shipping_tax_excl = $shippingTaxExcl;

                try {
                    $order->add();

                    // OrderDetail Object (order products)
                    foreach ($payForm->postBuyFormItems->item as $payFormProduct) {

                        $allegroAuction = AllegroAuction::getByAuctionID((float)$payFormProduct->postBuyFormItId);
                        if ($allegroAuction && $allegroAuction->id_allegro_auction) {
                            $allegroProduct = new AllegroProduct((int)$allegroAuction->id_allegro_product);
                            $product = new Product((int)$allegroProduct->id_product);
                        }

                        //$idWarehouse = $this->getProductWarheouseId($id_product, $id_product_attribute);
                        $idWarehouse = (int)Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT'); // @todo

                        $unitPriceTaxIncl = (float)$payFormProduct->postBuyFormItAmount/(int)$payFormProduct->postBuyFormItQuantity;
                        $taxRate = 0;

                        $order_detail = new OrderDetail();
                        $order_detail->id_order = $order->id;
                        $order_detail->id_warehouse = $idWarehouse;

                        if (isset($product) && $product->active) {
                            $order_detail->product_id = (int)$allegroProduct->id_product;
                            $order_detail->product_attribute_id = (int)$allegroProduct->id_product_attribute;

                            $order_detail->product_weight = round((float)$product->weight, 6);
                            $order_detail->product_reference = $product->reference;

                            // Tax
                            $taxRate = (int)$product->getTaxesRate();

                            // $totalProductsTaxIncl += ($unitPriceTaxIncl*(int)$payFormProduct->postBuyFormItQuantity);
                            // $totalProductsTaxExcl += $order_detail->total_price_tax_excl;

                            $order_detail->tax_rate = $taxRate;
                            $order_detail->tax_name = $taxRate.'%';
                        }

                        $order_detail->unit_price_tax_excl = round(($unitPriceTaxIncl/(($taxRate+100)/100)), 6);
                        $order_detail->total_price_tax_excl = round(($order_detail->unit_price_tax_excl*(int)$payFormProduct->postBuyFormItQuantity), 6);

                        $order_detail->id_shop = $id_shop;
                        $order_detail->product_name = (string)$payFormProduct->postBuyFormItTitle;
                        $order_detail->product_price = $unitPriceTaxIncl;
                        $order_detail->unit_price_tax_incl = $unitPriceTaxIncl;
                        $order_detail->total_price_tax_incl = round(($unitPriceTaxIncl*(int)$payFormProduct->postBuyFormItQuantity), 6);
                        $order_detail->product_quantity = (int)$payFormProduct->postBuyFormItQuantity;
                        $order_detail->add();

                    }

                    // Order message
                    $message = (string)$payForm->postBuyFormMsgToSeller;
                    if (isset($message) && !empty($message)) {
                        $msg = new Message();
                        $message = strip_tags($message, '<br>');
                        if (Validate::isCleanHtml($message)) {
                            $msg->message = $message;
                            $msg->id_cart = (int)$order->id_cart;
                            $msg->id_customer = (int)($order->id_customer);
                            $msg->id_order = (int)$order->id;
                            $msg->private = 1;
                            $msg->add();
                        }
                    }

                    // Order to DB
                    Db::getInstance()->insert('allegro_order', array(
                        'id_order' => (int)$order->id,
                        'form_id' => (float)$payForm->postBuyFormId,
                        'buyer_id' => (int)$payForm->postBuyFormBuyerId,
                        'buyer_email' => (string)$payForm->postBuyFormBuyerEmail,
                        'buyer_login' => (string)$payForm->postBuyFormBuyerLogin,
                        'gd_address' => serialize((array)$payForm->postBuyFormGdAddress),
                        'gd_info' => (string)$payForm->postBuyFormGdAdditionalInfo,
                        'carrier_id' => ($orderCarrierApi ? $orderCarrierApi->shipmentId : null),
                        'carrier_name' => ($orderCarrierApi ? $orderCarrierApi->shipmentName : null),
                        'invoice' => (int)$payForm->postBuyFormInvoiceOption,
                    ));

                } catch (Exception $e) {
                    $this->addLogMsg('[ERROR] '.$e->getMessage());
                    continue;
                }

                // Status
                $order_status = new OrderState((int)$id_order_state, $id_lang);

                $new_history = new OrderHistory();
                $new_history->id_order = (int)$order->id;
                $new_history->changeIdOrderState((int)$id_order_state, (int)$order->id, true);
                @$new_history->addWithemail();

                // Hook validate order
                if (Configuration::get('ALLEGRO_NO_EXECUTE_HOOK')) {
                    try {
                        Hook::exec('actionValidateOrder', array(
                            'cart' => $cart,
                            'order' => $order,
                            'customer' => $customer,
                            'currency' => new Currency((int)$id_currency),
                            'orderStatus' => $order_status
                        ));
                    } catch (Exception $e) {
                        $this->addLogMsg('[ERROR] '.$e->getMessage());
                    }
                }

                // Delivery
                if ($order->id_carrier) {
                    $order_carrier = new OrderCarrier();
                    $order_carrier->id_order = (int)$order->id;
                    $order_carrier->id_carrier = (int)$order->id_carrier;
                    $order_carrier->weight = (float)$order->getTotalWeight();
                    $order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;
                    $order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;
                    $order_carrier->add();
                }

                $this->addLogMsg("Order #{$order->reference} imported sucefully");
            }
        }

        return false;
    }

    private function getCustomerAddressIdByAlias($idCustomer, $alias)
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_address`
            FROM `'._DB_PREFIX_.'address`
            WHERE `id_customer` = '.(int)$idCustomer.' AND `deleted` = 0 AND `alias` = "'.pSql($alias).'"'
        );
    }

    public function getProductFieldsList(&$fieldBuilder, $allegroProduct, $idShop = null, $preview = false)
    {
        /**
         * Get shipping fields if shipping pricing is assigned
         * 
         * -1 - no shipping
         *  0 - default shipping
         *  x - selected shipping
         **/
        $allegroShipping = null;
        if ((int)$allegroProduct->id_allegro_shipping === 0) {
            $allegroShipping = AllegroShipping::getDefault(true); 
        } elseif ((int)$allegroProduct->id_allegro_shipping > 0) {
         $allegroShipping = new AllegroShipping((int)$allegroProduct->id_allegro_shipping);
        }

        /*
         * DB fields
        **/
        $idAllegroCategory = AFField::get(
            AFField::FID_CATEGORY, 
            array(AFField::SCOPE_PRODUCT => (int)$allegroProduct->id)
        );

        $scopes = array(
            AFField::SCOPE_PRODUCT     => (int)$allegroProduct->id,
            AFField::SCOPE_SHIPPING    => ($allegroShipping ? (int)$allegroShipping->id : null),
            AFField::SCOPE_GLOBAL      => (int)AFField::SCOPE_GLOBAL_ID,
            // If we have category saved for product do not use category mapping
            AFField::SCOPE_CATEGORY    => !$idAllegroCategory 
             ? (int)$allegroProduct->product->id_category_default 
             : null,
        );

        foreach (AFField::getList(array(), $scopes) as $fieldId => $fieldValue) {
            $fieldBuilder->addField($fieldId, $fieldValue);
        }


        /**
         * Quantity
         **/
        $fieldBuilder->addField(AFField::FID_QTY, $allegroProduct->getQuantity($idShop), false);

        /**
         * Price ("Buy now!")
         *
         * If price "Buy Now!" == '0' and starting price exists : bid without "Buy Now!"
         */
        if ($fieldBuilder->getField(AFField::FID_PRICE) !== '0' || !$fieldBuilder->getField(AFField::FID_ST_PRICE)) {
            $fieldBuilder->addField(AFField::FID_PRICE, $allegroProduct->genPrice(), false);
        }



        /**
         * Offer title
         **/
        $fieldBuilder->addField(AFField::FID_TITLE, $allegroProduct->genTitle(), false);

        /**
         * EAN 13
         **/
        if (Configuration::get('ALLEGRO_EAN') && $allegroProduct->getEan13()) {
            $fieldBuilder->addField(AFField::FID_EAN, $allegroProduct->getEan13());
        }

        /**
         * Country Code
         **/
        $fieldBuilder->addField(AFField::FID_COUNTRY, 1);

        /**
         * Images
         * 
         * 1. Product catalog image
         * 2. Allegro product images
         * 3. Theme images
         **/
        $maxNbImages = (int)Configuration::get('ALLEGRO_NB_IMAGES');
        $imageType = Configuration::get('ALLEGRO_IMAGE_TYPE');

        // Theme ID
        $idAllegroTheme = $allegroProduct->getThemeId();

        // Get images (catalog + allegro product)
        $allImages = $allegroProduct->getAPImages();

        foreach ($allImages as $i => $image) {
            // Images nb. limit
            if ($i >= $maxNbImages) {
                break;
            }

            // Shop / allegro image
            if ($image['src'] == AllegroProduct::IMAGE_SHOP) {
                $imagePath = _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image['id'])
                    .$image['id'].($imageType ? '-'.$imageType : '').'.jpg';

                // Try legacy mode
                if (!file_exists($imagePath)) {
                    $imagePath = _PS_PROD_IMG_DIR_.$allegroProduct->id_product.
                    '-'.$image['id'].($imageType 
                        ? '-'.$imageType 
                        : '').'.jpg';
                }
            } else {
                $imagePath = _ALLEGRO_IMG_DIR_.$image['id'].($imageType ? '-'.$imageType : '').'.jpg';
            }

            if (file_exists($imagePath)) {
      
                $imageContent = file_get_contents($imagePath);

                foreach (AFField::$FID_IMAGES as $i => $fieldId) {
                    if (!$fieldBuilder->getField($fieldId)) {
                        $fieldBuilder->addField($fieldId, base64_encode($imageContent));
                        break;
                    }
                }
            }
        }

        // Common theme images
        if ($allegroProduct->id_allegro_theme >= 0) {

            if ($idAllegroTheme) {
                $allegroTheme = new AllegroTheme($idAllegroTheme);

                foreach ($allegroTheme->getImages() as $imagePath) {

                    foreach (AFField::$FID_IMAGES as $i => $fieldId) {

                        if ($i >= $maxNbImages) {
                            break;
                        }

                        // If slot is empty - use it
                        if (!$fieldBuilder->getField($fieldId)) {
                            $fieldBuilder->addField($fieldId, base64_encode(file_get_contents($imagePath)));

                            // Next image
                            continue 2;
                        } else {
                            // Next slot
                            continue 1;
                        }
                    }
                }
            }
        }

        /**
         * Offer description (theme)
         * 
         * Must be generated AFTER images
         **/
        $idAllegroTheme = $allegroProduct->getThemeId();

        if (!$idAllegroTheme) {
            $offerContent = (new AllegroContentBuilder(
                AllegroContentBuilder::TYPE_JSON,
                $fieldBuilder->getField(AFField::FID_DESC_2),
                $allegroProduct,
                $fieldBuilder->build(true /*raw*/)
            ))->build($preview);
        } else {
            $allegroTheme = new AllegroTheme($idAllegroTheme);
            $offerContent = (new AllegroContentBuilder(
                $allegroTheme->format 
                    ? AllegroContentBuilder::TYPE_JSON
                    : AllegroContentBuilder::TYPE_HTML,
                $allegroTheme->content,
                $allegroProduct,
                $fieldBuilder->build(true /*raw*/)
            ))->build($preview);
        }

        if ($idAllegroTheme && !$allegroTheme->format) {
            $fieldBuilder->addField(AFField::FID_DESC, $offerContent);
            $fieldBuilder->addField(AFField::FID_DESC_2, null, true);
        } else {
            $fieldBuilder->addField(AFField::FID_DESC_2, json_encode($offerContent));
        }
    }



    public function createAuction($idAllegroProduct, $simulation = false, $startTimestamp = false, $idShop)
    {
        $allegroProduct = new AllegroProduct($idAllegroProduct);
        
        $fieldBuilder = new AllegroFieldBuilder();

        $this->getProductFieldsList($fieldBuilder, $allegroProduct, $idShop);

        // Override start time
        if ($startTime = Configuration::get('ALLEGRO_START_TIME')) {
            $startTimestamp = strtotime($startTime);
        }

        if ($startTimestamp) {
            $fieldBuilder->addField(AFField::FID_START, $startTimestamp);
        }

        $allegroProductAccount = Db::getInstance()->GetRow('
            SELECT * FROM `'._DB_PREFIX_.'allegro_product_account`
            WHERE `id_allegro_product` = '.(int)$allegroProduct->id.'
            AND `id_allegro_account` = '.(int)$this->api->getAccountId().'
            AND `id_shop` = '.(int)$idShop
        );

        $impliedWarranty = (!empty($allegroProductAccount['implied_warranty'])
            ? $allegroProductAccount['implied_warranty']
            : Configuration::get('ALLEGRO_IMPLIED_WARRANTY_'.(int)$this->allegroAccount->id)
        );

        $returnPolicy = (!empty($allegroProductAccount['return_policy'])
            ? $allegroProductAccount['return_policy']
            : Configuration::get('ALLEGRO_RETURN_POLICY_'.(int)$this->allegroAccount->id)
        );

        $warranty = (!empty($allegroProductAccount['warranty']) 
            ? $allegroProductAccount['warranty']
            : Configuration::get('ALLEGRO_WARRANTY_'.(int)$this->allegroAccount->id)
        );

        $additionalServices = (!empty($allegroProductAccount['additional_services'])
            ? $allegroProductAccount['additional_services']
            : null
        );

        // Call API method
        $res = $this->api->{(
            $simulation
            ? 'doCheckNewAuctionExt'
            : 'doNewAuctionExt'
        )}(
            array(
                'fields' => $fieldBuilder->build(), 
                'afterSalesServiceConditions' => array(
                    'impliedWarranty' => $impliedWarranty,
                    'returnPolicy' => $returnPolicy,
                    'warranty' => $warranty,
                ),
                'additionalServicesGroup' => $additionalServices,
            )
        );


        // No error - clean last error code
        if ($allegroProduct->cache_relist_error) {
            $allegroProduct->cache_relist_error = null;
            $allegroProduct->save();
        }

        if ($simulation) {
            return $res;
        } else {
            $allegroAuction = new AllegroAuction();
            $allegroAuction->id_auction        = (float)$res->itemId;
            $allegroAuction->id_allegro_product = (int)$allegroProduct->id;
            $allegroAuction->id_allegro_account = (int)$this->allegroAccount->id;
            $allegroAuction->title             = $fieldBuilder->getField(AFField::FID_TITLE);
            $allegroAuction->duration          = $fieldBuilder->getField(AFField::FID_DURATION);
            $allegroAuction->date_start        = $startTimestamp ? date("Y-m-d H:i:s", $startTimestamp) : null;
            $allegroAuction->is_standard       = !empty($res->itemIsAllegroStandard);
            $allegroAuction->cost_info         = (string)$res->itemInfo;
            $allegroAuction->quantity          = $fieldBuilder->getField(AFField::FID_QTY);
            $allegroAuction->price             = $fieldBuilder->getField(AFField::FID_PRICE);
            $allegroAuction->id_shop           = (int)$idShop;

            $allegroAuction->add();

            return $allegroAuction;
        }
    }

    public function finishAuction($idAuction)
    {
        $this->api->doFinishItem(array('finishItemId' => (float)$idAuction));

        return Db::getInstance()->update('allegro_auction', array(
            'status' => 3,
        ), 'id_auction = '.(float)$idAuction);
    }

    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($class === null || $class == 'AdminTab') {
            $class = substr(get_class($this), 0, -10);
        } elseif (strtolower(substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = substr($class, 0, -10);
        }
        return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
    }

    private static function log($data)
    {
        if (!Configuration::get('ALLEGRO_LOG')) {
            return true;
        }

        $log = date('######### Y:m:d H:i:s.'.microtime(true).' #########').PHP_EOL;
        $log .= print_r($data, true).PHP_EOL;

        // Delete old log
        @unlink(_ALLEGRO_LOGS_DIR_.date('D', strtotime(' -3 day')).'_sync.log');

        return file_put_contents(_ALLEGRO_LOGS_DIR_.date('D').'_sync.log', $log, FILE_APPEND);
    }
}
