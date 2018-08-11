<?php

define('STRONIARZHURTCLIENT_DEBUG', false);


class WebServiceGetProducts
{
    private static $webService = null;

    protected $order;

    protected $products_prepared = false;


    public function __construct( $order )    
    {
        $this->order = $order;
    }

    public static function getInstance() 
    {
        if ( is_null( self::$webService ) ) {

            require( 'PSWebServiceLibrary.php' );

            $server_shop = Configuration::get('STRONIARZ_HURT_SHOP_DOMAIN');
            $api_key = Configuration::get('STRONIARZ_HURT_API_KEY');

            self::$webService = new PrestaShopWebservice( $server_shop, $api_key, STRONIARZHURTCLIENT_DEBUG );
        }

        return self::$webService;
    }

    public function getWebServiceProductIdsByProductReference( $reference, $quantity )
    {
        if ( !$reference ) {
            PrestaShopLogger::addLog( 'Jeden z kupinych produktów/kombinacji nie zawiera kodu produktu (index) i nie został zsynchronizowany', 2, 18 , 'Integracja Stroniarz', 1888 );
            return false;
        }

        $opt = array( 
            'resource' => 'products',
            'display' => '[id]',
            'filter' => array(
                'reference' => $reference
            )
        );

        $xml = self::getInstance()->get( $opt );

        if ( isset( $xml->products ) ) {
            if ( $products = $xml->products->children() ) {

                if ( count($products) > 1 ) {
                    PrestaShopLogger::addLog( 'Kod produktu '.$reference.' odnosi się do więcej niż jednego produktu/kombinacji, należy to poprawić by synchonizacja produktów działał bezbłędnie', 2, 18 , 'Integracja Stroniarz', 1888 );
                }
                
                if ( $id = $products[0]->id ) {
                    
                    return array(
                        'id_product' => (int) $id,
                        'id_product_attribute' => 0,
                        'quantity' => $quantity
                    );
                }
            }
        }

        return false;
    }

    public function getWebServiceProductIdsByCombinationReference( $reference, $quantity )
    {
        if ( !$reference ) {
            PrestaShopLogger::addLog( 'Jeden z kupinych produktów/kombinacji nie zawiera kodu produktu (index) i nie został zsynchronizowany', 2, 18 , 'Integracja Stroniarz', 1888 );
            return false;
        }

        $opt = array( 
            'resource' => 'combinations',
            'display' => '[id,id_product]',
            'filter' => array(
                'reference' => $reference
            )
        );

        $xml = self::getInstance()->get( $opt );

        if ( isset( $xml->combinations ) ) {
            if ( $combinations = $xml->combinations->children() ) {

                if ( count($combinations) > 1 ) {
                    PrestaShopLogger::addLog( 'Kod produktu '.$reference.' odnosi się do więcej niż jednego produktu/kombinacji, należy to poprawić by synchonizacja produktów działał bezbłędnie', 2, 23 , 'Integracja Stroniarz', 1888 );
                }

                if ( $combinations[0]->id ) {

                    return array(
                        'id_product' => (int) $combinations[0]->id_product,
                        'id_product_attribute' => (int) $combinations[0]->id,
                        'quantity' => $quantity,
                    );
                }
            }
        }

        return false;
    }

    public function prepareProducts() 
    {
        if (!$this->products_prepared) {

            if (empty($this->order->product_list)) {
                throw new PrestaShopWebserviceException('Brak produktów w koszyku');
            }

            foreach ( $this->order->product_list as $prod ) {

                if ( $prod['id_product_attribute'] ) {
                    if ( $product = $this->getWebServiceProductIdsByCombinationReference( $prod['reference'], $prod['quantity'] ) ) {
                        $product['org_id_product'] = $prod['id_product'];
                        $product['org_id_product_attribute'] = $prod['id_product_attribute'];
                        $this->products_prepared[] = $product;
                    }
                } else {
                    if ( $product = $this->getWebServiceProductIdsByProductReference( $prod['reference'], $prod['quantity'] ) ) {
                        $product['org_id_product'] = $prod['id_product'];
                        $product['org_id_product_attribute'] = $prod['id_product_attribute'];
                        $this->products_prepared[] = $product;
                    }
                }
            }
        }

        return $this->products_prepared;
    }
   
}