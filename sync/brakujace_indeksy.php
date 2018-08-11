<?php

require( '../config/config.inc.php' );

$sql = "
    SELECT DISTINCT(pa.id_product), pl.name FROM "._DB_PREFIX_."product_attribute pa
    LEFT JOIN "._DB_PREFIX_."product_lang pl ON pa.id_product=pl.id_product
    WHERE pa.reference='' AND pl.id_lang=1 AND pl.id_shop=1
        ";

$res_comb = Db::getInstance()->executeS( $sql );

$sql = "
    SELECT DISTINCT(p.id_product), pl.name FROM "._DB_PREFIX_."product p
    LEFT JOIN "._DB_PREFIX_."product_lang pl ON p.id_product=pl.id_product
    WHERE p.reference='' AND pl.id_lang=1 AND pl.id_shop=1 
";

$res_prod = Db::getInstance()->executeS( $sql );



if ( $res_prod ) {

    print '<h2>Produkty bez indeksu</h2>';
    foreach ( $res_prod as $k => $v ) {
        print '<p>'.$v['id_product'].' - '.$v['name'].'</p>';
    }
}

print '<h2>Produkty z brakującymi indeksami w kombinacjach</h2>';
foreach ( $res_comb as $k => $v ) {
    print '<p>'.$v['id_product'].' - '.$v['name'].'</p>';
}

die("Wejdz do pliku i odkomentuj linijkę 36 jeśli chcesz automatycznie nadać indeksy na podstawie rozmiaru");

// uaktualnienie kodow dla kombinacji
$sql = "
    SELECT p.reference, pa.id_product, pa.id_product_attribute, pl.name as nazwa, al.name as rozmiar FROM "._DB_PREFIX_."product_attribute pa
    LEFT JOIN "._DB_PREFIX_."product p ON pa.id_product=p.id_product
    LEFT JOIN "._DB_PREFIX_."product_lang pl ON pa.id_product=pl.id_product
    LEFT JOIN "._DB_PREFIX_."product_attribute_combination pac ON pac.id_product_attribute=pa.id_product_attribute
    LEFT JOIN "._DB_PREFIX_."attribute_lang al ON al.id_attribute=pac.id_attribute
    WHERE pa.reference='' AND pl.id_lang=1 AND pl.id_shop=1
";

$res_comb = Db::getInstance()->executeS( $sql );

// print '<pre>';
// print_r( $res_comb );
// exit;

foreach( $res_comb as $k => $v ) {
    $c = new Combination( $v['id_product_attribute'] );
    $c->reference = trim( $v['reference'] ) . '-'. trim( $v['rozmiar'] );
    $c->update();
    // print trim( $v['reference'] ) . '-'. trim( $v['rozmiar'] );
}

?>