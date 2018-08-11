<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__).'/../allegro.inc.php';

abstract class CronAllegroSyncController
{
	public static function stockSync()
    {
        foreach (AllegroAccount::getAccounts() as $allegroAccount) {

            $allegroAccount = new AllegroAccount((int)$allegroAccount['id_allegro_account']);

            try {
                $api = new AllegroAPI($allegroAccount);
            } catch (SoapFault $e) {
                PrestaShopLogger::addLog('Allegro CRON - Unable to init API (ID:'.$allegroAccount->id.'), error: '.$e->faultstring, 3, null, null, null, true);
                continue;
            }

            $manager = new AllegroSyncManager($api);

            $auctionsIds = $manager->getAuctionsIdsForStockSync();
            $manager->stockSync($auctionsIds);
            $manager->updateJournalPoints();

            PrestaShopLogger::addLog('Allegro CRON - finished stock sync for account ID:'.$allegroAccount->id, 1, null, null, null, true);
        }
        
        die('OK');
    }


    public static function orderSync()
    {
        if ((int)Configuration::get('ALLEGRO_ORDER_SYNC')) {
            foreach (AllegroAccount::getAccounts() as $allegroAccount) {

                $allegroAccount = new AllegroAccount((int)$allegroAccount['id_allegro_account']);

                try {
                    $api = new AllegroAPI($allegroAccount);
                } catch (SoapFault $e) {
                    PrestaShopLogger::addLog('Allegro CRON - Unable to init API (ID:'.$allegroAccount->id.'), error: '.$e->faultstring, 3, null, null, null, true);
                    continue;
                }

                $manager = new AllegroSyncManager($api);
                
                $transactionsIds = $manager->getTransactionsFromDealJournal();
                if (count($transactionsIds)) {
                    $manager->importOrders($transactionsIds);
                }
                $manager->updateTransJournalPoints();

                PrestaShopLogger::addLog('Allegro CRON - finished order sync for account ID:'.$allegroAccount->id, 1, null, null, null, true);
            }
        }

        die('OK');
    }


    public static function relist()
    {
        foreach (AllegroAccount::getAccounts() as $allegroAccount) {

            $allegroAccount = new AllegroAccount((int)$allegroAccount['id_allegro_account']);

            try {
                $api = new AllegroAPI($allegroAccount);
            } catch (SoapFault $e) {
                PrestaShopLogger::addLog('Allegro CRON - Unable to init API (ID:'.$allegroAccount->id.'), error: '.$e->faultstring, 3, null, null, null, true);
                continue;
            }

            $manager = new AllegroSyncManager($api);
            
            Db::getInstance()->update('allegro_product', array(
                'cache_relist_error' => null
            ));

            // Create context cart to prevent error in "Product::getPriceStatic" method
            $context = Context::getContext();
            $context->cart = new Cart((int)Configuration::get('ALLEGRO_ID_CART'));

            $allegroProductsRelist = $manager->getProductsForRelist();

            foreach ($allegroProductsRelist as $allegroProductArray) {
                try {
                    $auction = $manager->createAuction((int)$allegroProductArray['id_allegro_product'], false, false, (int)$allegroProductArray['id_shop']);
                } catch (SoapFault $e) {
                 
                    $allegroProduct = new AllegroProduct((int)$allegroProductArray['id_allegro_product']);

                    // Save error code for product
                    $errorCode = 'UNKNOWN_ERROR';
                    if (isset($e->faultcode) && $e->faultcode && strlen($e->faultcode) <= 255) {
                        $errorCode = $e->faultcode;
                    }

                    $allegroProduct->cache_relist_error = $errorCode;
                    $allegroProduct->save();

                    PrestaShopLogger::addLog('Allegro SYNC - Unable to relist product ('.(int)$allegroProductArray['id_allegro_product'].') - '.$e->faultstring, 3, null, null, null, true); 
                }
            }

            PrestaShopLogger::addLog('Allegro CRON - finished relist for account ID:'.$allegroAccount->id, 1, null, null, null, true);
        }

        die('OK');
    }
}
