<?php
die('off');

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');


require('lib/WebServiceGetProducts.php');
require('lib/WebServiceStockAvailable.php');


$products_sample = array(
    array(
        'quantity' => 1,
        'reference' => 'test_123',
        'id_product_attribute' => 0,
    ),
    array(
        'quantity' => 1,
        'reference' => 'test_komb_123',
        'id_product_attribute' => 123
    )
);

$order_sample = array(
    'total_products_wt' => 20036.90,
    'total_products' => 16290.16,
    'round_mode' => 2,
    'round_type' => 2,
    'product_list' => $products_sample,
);


try {
    $web_service = new WebServiceStockAvailable( (object) $order_sample );

/* getProduct test
    $res = $web_service->getWebServiceProductIdsByProductReference('test_123', 2);
    print '<pre>';
    print_r($res);
    exit;
*/

/*  
* prepareProducts test
    $res = $web_service->prepareProducts();
    print '<pre>';
    print_r( $res );
    exit;
*/

/*
* createOrder test
    $res = $web_service->createCart();
    print '<pre>';
    print_r( $res );
*/

    $web_service->syncStocks();

}
catch (PrestaShopWebserviceException $e) {

    print $e->getMessage();
    print '<pre>';
    print_r( $e );
}
exit;