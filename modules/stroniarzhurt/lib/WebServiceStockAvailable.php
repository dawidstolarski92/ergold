<?php

class WebServiceStockAvailable extends WebServiceGetProducts 
{

    public function getSingleStockId( $id_product, $id_product_attribute=0 ) 
    {
        $opt = array( 
            'resource' => 'stock_availables',
            'display' => '[id]',
            'filter' => array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute
            )
        );

        $xml = self::getInstance()->get( $opt );

        if ( !isset( $xml->stock_availables->stock_available->id ) || empty( $xml->stock_availables->stock_available->id ) ) {
            new PrestaShopWebserviceException('Brak stanów magazynowych na zewnętrznym sklepie (dla id_product: '.$id_product.' id_prod_attrib: '.$id_product_attribute.')');
        }

        return (int) $xml->stock_availables->stock_available->id;
    }


    public function syncStocks()
    {
        $this->prepareProducts();

        $server_shop = Configuration::get('STRONIARZ_HURT_SHOP_DOMAIN');

        $xml = self::getInstance()->get(array('url' => $server_shop . '/api/stock_availables?schema=blank'));

        foreach( $this->products_prepared as $prod ) {

            $id =  $this->getSingleStockId( $prod['id_product'], $prod['id_product_attribute'] );

            $xml->stock_available->id = $id;
            $xml->stock_available->id_product = $prod['id_product']; 
            $xml->stock_available->id_product_attribute = $prod['id_product_attribute'];
            $xml->stock_available->id_shop = 1;
            $xml->stock_available->id_shop_group = 0;
            $xml->stock_available->quantity = Product::getQuantity( $prod['org_id_product'], $prod['org_id_product_attribute'] );
            $xml->stock_available->depends_on_stock = 0;    
            $xml->stock_available->out_of_stock = 2;

            $opt = array(
                'resource' => 'stock_availables',
                'putXml' => $xml->asXML(),
                'id' => $id,
            );
            
            $remote = self::getInstance()->edit( $opt );

            if ( ! $remote->stock_available->id ) {
                new PrestaShopWebserviceException('Nie udało się zsynchronizować magazynu');
            }
        }
    }
}