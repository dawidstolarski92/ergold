<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Advancedexport extends Module
{
    public $hasAttr;
    public $lastElement;
    public $rowsNumber;
    public $link;
    private $selected_cat;

    private $export_types = array(
        'products',
        'orders',
        'categories',
        'manufacturers',
        'newsletters',
        'suppliers',
        'customers',
        'addresses',
    );

    public $products = array(
        array('name' => 'Product Id', 'field' => 'id_product', 'database' => 'products', 'import' => 1, 'import_combination' => 1, 'import_combination_name' => 'Product ID*', 'import_name' => 'ID', 'alias' => 'p'),
        array('name' => 'Product Reference', 'field' => 'reference', 'database' => 'products', 'import' => 13,  'import_combination' => 5, 'import_combination_name' => 'Reference', 'import_name' => 'Reference #', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Name', 'field' => 'name', 'database' => 'products_lang', 'import' => 2, 'import_name' => 'Name *', 'alias' => 'pl'),
        array('name' => 'Short Description', 'field' => 'description_short', 'database' => 'products_lang', 'import' => 30, 'import_name' => 'Description', 'alias' => 'pl'),
        array('name' => 'Long Description', 'field' => 'description', 'database' => 'products_lang', 'import' => 31, 'import_name' => 'Short description', 'alias' => 'pl'),
        array('name' => 'Quantity', 'field' => 'quantity', 'database' => 'other', 'import' => 24, 'import_name' => 'Quantity', 'import_combination' => 10, 'import_combination_name' => 'Quantity', 'attribute' => true),
        array('name' => 'Price', 'field' => 'price', 'database' => 'products', 'alias' => 'p', 'import_combination' => 9, 'import_combination_name' => 'Impact on Price', 'attribute' => true),
        array('name' => 'Price Catalogue TTC', 'field' => 'price_tax_nodiscount', 'database' => 'other', 'attribute' => true),
        array('name' => 'Price Tax', 'field' => 'price_tax', 'database' => 'other', 'import' => 5, 'import_name' => 'Price tax included',  'attribute' => true),
        array('name' => 'Wholesale Price', 'field' => 'wholesale_price', 'database' => 'products', 'import' => 7,  'import_combination' => 8, 'import_name' => 'Wholesale price', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Supplier Id (default)', 'field' => 'id_supplier', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Suppliers Ids', 'field' => 'id_supplier_all', 'database' => 'other', 'attribute' => true),
        array('name' => 'Supplier Name (default)', 'field' => 'supplier_name', 'as' => true, 'database' => 'supplier', 'import' => 15, 'import_name' => 'Supplier', 'alias' => 's'),
        array('name' => 'Supplier Names', 'field' => 'supplier_name_all', 'database' => 'other', 'attribute' => true),
        array('name' => 'Manufacturer Id', 'field' => 'id_manufacturer', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Manufacturer Name', 'field' => 'manufacturer_name', 'database' => 'other', 'import' => 16, 'import_name' => 'Manufacturer'),
        array('name' => 'Tax Id Rules Group', 'field' => 'id_tax_rules_group', 'database' => 'products', 'import' => 6, 'import_name' => 'Tax rules ID', 'alias' => 'p'),
        array('name' => 'Tax Rate', 'field' => 'tax_rate', 'database' => 'other'),
        array('name' => 'Default Category Id', 'field' => 'id_category_default', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Default Category Name', 'field' => 'nameCategoryDefault', 'database' => 'other'),
        array('name' => 'Categories Names', 'field' => 'categories_names', 'database' => 'other'),
        array('name' => 'Categories Ids', 'field' => 'categories_ids', 'database' => 'other', 'import' => 4, 'import_name' => 'Categories (x,y,z...)'),
        array('name' => 'On Sale', 'field' => 'on_sale', 'database' => 'products', 'import' => 8, 'import_name' => 'On sale (0/1)', 'alias' => 'p'),
        array('name' => 'EAN 13', 'field' => 'ean13', 'database' => 'products', 'alias' => 'p', 'import' => 17,  'import_combination' => 6, 'import_combination_name' => 'EAN 13', 'import_name' => 'EAN13', 'attribute' => true),
        array('name' => 'Supplier Reference', 'field' => 'supplier_reference', 'database' => 'other', 'import' => 14,  'import_combination' => 4, 'import_combination_name' => 'Supplier reference', 'import_name' => 'Supplier reference #', 'attribute' => true),
        array('name' => 'Date Added', 'field' => 'date_add', 'database' => 'products', 'import' => 40, 'import_name' => 'Product creation date', 'alias' => 'p'),
        array('name' => 'Date Update', 'field' => 'date_upd', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Active', 'field' => 'active', 'database' => 'products', 'import' => 2, 'import_name' => 'Active (0/1)', 'alias' => 'p'),
        array('name' => 'Meta Title', 'field' => 'meta_title', 'database' => 'products_lang', 'import' => 33, 'import_name' => 'Meta title', 'alias' => 'pl'),
        array('name' => 'Meta Description', 'field' => 'meta_description', 'database' => 'products_lang', 'import' => 35, 'import_name' => 'Meta description', 'alias' => 'pl'),
        array('name' => 'Meta Keywords', 'field' => 'meta_keywords', 'database' => 'products_lang', 'import' => 35, 'import_name' => 'Meta keywords', 'alias' => 'pl'),
        array('name' => 'Available Now', 'field' => 'available_now', 'database' => 'products_lang', 'import' => 36, 'import_name' => 'Text when in stock', 'alias' => 'pl'),
        array('name' => 'Available Later', 'field' => 'available_later', 'database' => 'products_lang', 'import' => 37, 'import_name' => 'Text when backorder allowed', 'alias' => 'pl'),
        array('name' => 'Tags', 'field' => 'tags', 'database' => 'other', 'import' => 32, 'import_name' => 'Tags (x,y,z...)'),
        array('name' => 'Accessories', 'field' => 'accessories', 'database' => 'other'),
        array('name' => 'Images', 'field' => 'images', 'database' => 'other', 'attribute' => true, 'import_combination' => 16, 'import_combination_name' => 'Image URLs (x,y,z...)'),
        array('name' => 'Online only', 'field' => 'online_only', 'database' => 'products', 'import' => 47, 'import_name' => 'Available online only (0 = No, 1 = Yes)', 'alias' => 'p'),
        array('name' => 'Upc', 'field' => 'upc', 'database' => 'products', 'import' => 18,  'import_combination' => 7, 'import_combination_name' => 'UPC', 'import_name' => 'UPC', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Ecotax', 'field' => 'ecotax', 'database' => 'products', 'import' => 19, 'import_combination' => 9, 'import_combination_name' => 'EcoTax', 'import_name' => 'Ecotax', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Unity', 'field' => 'unity', 'database' => 'products', 'import' => 28, 'import_name' => 'Unity', 'alias' => 'p'),
        array('name' => 'Unit Price Ratio', 'field' => 'unit_price_ratio', 'database' => 'products', 'import' => 29, 'import_name' => 'Unit Price', 'alias' => 'p'),
        array('name' => 'Minimal Quantity', 'field' => 'minimal_quantity', 'database' => 'products', 'import' => 25, 'import_combination' => 11, 'import_combination_name' => 'Minimal quantity', 'import_name' => 'Minimal quantity', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Additional Shipping Cost', 'field' => 'additional_shipping_cost', 'database' => 'products', 'import' => 27, 'import_name' => 'Additional shipping cost', 'alias' => 'p'),
        array('name' => 'Location', 'field' => 'location', 'database' => 'products', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Width', 'field' => 'width', 'database' => 'products', 'import' => 20, 'import_name' => 'Width', 'alias' => 'p'),
        array('name' => 'Height', 'field' => 'height', 'database' => 'products', 'import' => 21, 'import_name' => 'Height', 'alias' => 'p'),
        array('name' => 'Depth', 'field' => 'depth', 'database' => 'products', 'import' => 22, 'import_name' => 'Depth', 'alias' => 'p'),
        array('name' => 'Weight', 'field' => 'weight', 'database' => 'products', 'import' => 23, 'import_combination' => 10, 'import_combination_name' => 'Impact on weight', 'import_name' => 'Weight', 'alias' => 'p', 'attribute' => true),
        array('name' => 'Out Of Stock', 'field' => 'out_of_stock', 'database' => 'products', 'import' => 53, 'import_name' => 'Out of stock', 'alias' => 'p'),
        array('name' => 'Quantity Discount', 'field' => 'quantity_discount', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Customizable', 'field' => 'customizable', 'database' => 'products', 'import' => 49, 'import_name' => 'Customizable (0 = No, 1 = Yes)', 'alias' => 'p'),
        array('name' => 'Uploadable Files', 'field' => 'uploadable_files', 'database' => 'products', 'import' => 50, 'import_name' => 'Uploadable files (0 = No, 1 = Yes)', 'alias' => 'p'),
        array('name' => 'Text Fields', 'field' => 'text_fields', 'database' => 'products', 'import' => 52, 'import_name' => 'Text fields (0 = No, 1 = Yes)', 'alias' => 'p'),
        array('name' => 'Available For Order', 'field' => 'available_for_order', 'database' => 'products', 'import' => 38, 'import_name' => 'Available for order (0 = No, 1 = Yes)', 'alias' => 'p'),
        array('name' => 'Condition', 'field' => 'condition', 'database' => 'products', 'import' => 48, 'import_name' => 'Condition', 'alias' => 'p'),
        array('name' => 'Show Price', 'field' => 'show_price', 'database' => 'products', 'import' => 41, 'import_name' => 'Show Price', 'alias' => 'p'),
        array('name' => 'Indexed', 'field' => 'indexed', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Cache Is Pack', 'field' => 'cache_is_pack', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Cache Has Attachments', 'field' => 'cache_has_attachments', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Cache Default Attribute', 'field' => 'cache_default_attribute', 'database' => 'products', 'alias' => 'p'),
        array('name' => 'Link Rewrite', 'field' => 'link_rewrite', 'database' => 'products_lang', 'import' => 36, 'import_name' => 'URL rewritten', 'alias' => 'pl'),
        array('name' => 'Url Product', 'field' => 'url_product', 'database' => 'other'),
        array('name' => 'Features', 'field' => 'features', 'database' => 'other', 'import' => 46, 'import_name' => 'Feature(Name:Value:Position)'),
        array('name' => 'Attributes', 'field' => 'attributes', 'database' => 'other', 'attribute' => true),
        array('name' => 'Attributes Name', 'field' => 'attributes_name', 'database' => 'other', 'attribute' => true, 'import_combination' => 2, 'import_combination_name' => 'Attributes Name'),
        array('name' => 'Attributes Value', 'field' => 'attributes_value', 'database' => 'other', 'attribute' => true, 'import_combination' => 3, 'import_combination_name' => 'Attributes Value'),
        array('name' => 'Visibility', 'field' => 'visibility', 'database' => 'products', 'import' => 26, 'import_name' => 'Visibility', 'alias' => 'p'),
        array('name' => 'Product available date', 'field' => 'available_date', 'database' => 'products', 'import' => 39, 'import_name' => 'Product available date', 'alias' => 'p'),
        array('name' => 'Discount amount', 'field' => 'discount_amount', 'database' => 'specific_price', 'import' => 9, 'import_name' => 'Discount amount', 'alias' => 'sp_tmp'),
        array('name' => 'Discount percent', 'field' => 'discount_percent', 'database' => 'specific_price', 'import' => 10, 'import_name' => 'Discount percent', 'alias' => 'sp_tmp'),
        array('name' => 'Discount from (yyyy-mm-dd)', 'field' => 'from', 'database' => 'specific_price', 'import' => 11, 'import_name' => 'Discount from (yyyy-mm-dd)', 'alias' => 'sp_tmp'),
        array('name' => 'Discount to (yyyy-mm-dd)', 'field' => 'to', 'database' => 'specific_price', 'import' => 12, 'import_name' => 'Discount to (yyyy-mm-dd)', 'alias' => 'sp_tmp'),
        array('name' => 'Cover', 'field' => 'image', 'database' => 'other', 'import' => 42, 'import_name' => 'Image URLs (x,y,z...)'),
        array('name' => 'Id shop default', 'field' => 'id_shop_default', 'database' => 'products', 'import' => 54, 'import_name' => 'ID / Name of shop', 'alias' => 'p', 'import_combination' => 2, 'import_combination_name' => 'ID / Name of shop'),
        array('name' => 'Advanced stock management', 'field' => 'advanced_stock_management', 'database' => 'products', 'import' => 55, 'import_name' => 'Advanced stock managment', 'import_combination' => 20, 'import_combination_name' => 'Advanced stock managment', 'alias' => 'p'),
        array('name' => 'Depends On Stock', 'field' => 'depends_on_stock', 'database' => 'other', 'import' => 56, 'import_name' => 'Depends On Stock', 'import_combination' => 21, 'import_combination_name' => 'Depends on stock'),
        array('name' => 'Warehouse', 'field' => 'warehouse', 'database' => 'other', 'import' => 57, 'import_name' => 'Warehouse', 'import_combination' => 22, 'import_combination_name' => 'Warehouse'),
        array('name' => 'Image alt', 'field' => 'image_alt', 'database' => 'other', 'import' => 17, 'import_name' => 'Image alt', 'import_combination' => 17, 'import_combination_name' => 'Image alt texts (x,y,z...)', 'attribute' => true),
        array('name' => 'Image position', 'field' => 'image_position', 'database' => 'other', 'import' => 15, 'import_name' => 'Image position', 'import_combination' => 16, 'import_combination_name' => 'Image position', 'attribute' => true),
        array('name' => 'Default (0 = No 1 = Yes)', 'field' => 'default_combination', 'database' => 'other', 'import_combination' => 16, 'import_combination_name' => 'Default (0 = No, 1 = Yes)', 'attribute' => true),
    );
    public $orders = array(
        //PS_ORDER
        array('name' => 'Order No', 'field' => 'id_order', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Reference', 'field' => 'reference', 'database' => 'orders', 'alias' => 'o', 'attribute' => true),
        array('name' => 'Code (voucher)', 'field' => 'code', 'database' => 'other'),
        //SHOP
        array('name' => 'Payment module', 'field' => 'module', 'database' => 'orders',  'alias' => 'o'),
        array('name' => 'Payment', 'field' => 'payment', 'database' => 'orders',  'alias' => 'o'),
        array('name' => 'Total paid', 'field' => 'total_paid', 'database' => 'orders',  'alias' => 'o'),
        array('name' => 'Total paid tax incl', 'field' => 'total_paid_tax_incl', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total paid tax excl', 'field' => 'total_paid_tax_excl', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total products with tax', 'field' => 'total_products_wt', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total paid real', 'field' => 'total_paid_real', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total products', 'field' => 'total_products', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total shipping', 'field' => 'total_shipping', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total wrapping', 'field' => 'total_wrapping', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Shipping number', 'field' => 'shipping_number', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Delivery number', 'field' => 'delivery_number', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Invoice number', 'field' => 'invoice_number', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Invoice date', 'field' => 'invoice_date', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Delivery date', 'field' => 'delivery_date', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Date added', 'field' => 'date_add', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Date updated', 'field' => 'date_upd', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Total discounts', 'field' => 'total_discounts', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Gift message', 'field' => 'gift_message', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Valid', 'field' => 'valid', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Carrier id', 'field' => 'id_carrier', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Customer id', 'field' => 'id_customer', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Recycled packaging', 'field' => 'recyclable', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Gift wrapping', 'field' => 'gift', 'database' => 'orders', 'alias' => 'o'),
        array('name' => 'Customization', 'field' => 'customization', 'database' => 'other', 'alias' => 'o'),
        //PS_CUSTOMER
        array('name' => 'Customer Firstname', 'field' => 'firstname', 'database' => 'customer', 'alias' => 'cu'),
        array('name' => 'Customer Lastname', 'field' => 'lastname', 'database' => 'customer', 'alias' => 'cu'),
        array('name' => 'Customer Email', 'field' => 'email', 'database' => 'customer', 'alias' => 'cu'),
        array('name' => 'Customer id language', 'field' => 'id_lang', 'database' => 'customer', 'alias' => 'cu'),
        //PS_ADRESS
        array('name' => 'Delivery Gender', 'field' => 'delivery_name', 'as' => true, 'database' => 'gender', 'alias' => 'gl'),
        array('name' => 'Delivery Company Name', 'field' => 'company', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery Firstname', 'field' => 'delivery_firstname', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery Lastname', 'field' => 'delivery_lastname', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery address line 1', 'field' => 'delivery_address1', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery address line 2', 'field' => 'delivery_address2', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery postcode', 'field' => 'delivery_postcode', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery city', 'field' => 'delivery_city', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery phone', 'field' => 'delivery_phone', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery phone(mobile)', 'field' => 'delivery_phone_mobile', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery VAT', 'field' => 'delivery_vat_number', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        array('name' => 'Delivery DNI', 'field' => 'delivery_dni', 'as' => true, 'database' => 'address', 'alias' => 'a'),
        //PS_STATE
        array('name' => 'Delivery country iso code', 'field' => 'iso_code', 'database' => 'country', 'alias' => 'co'),
        array('name' => 'Delivery state', 'field' => 'state_name', 'as' => true, 'database' => 'state', 'alias' => 's'),
        //PS_COUNTRY_LANG
        array('name' => 'Delivery country', 'field' => 'country_name', 'as' => true, 'database' => 'country_lang', 'alias' => 'cl'),
        //PS_ADRESS
        array('name' => 'Invoice address line 1', 'field' => 'invoice_address1', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice address line 2', 'field' => 'invoice_address2', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice postcode', 'field' => 'invoice_postcode', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice city', 'field' => 'invoice_city', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice phone', 'field' => 'invoice_phone', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice phone (mobile)', 'field' => 'invoice_phone_mobile', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice gender', 'field' => 'invoice_name', 'as' => true, 'database' => 'gender', 'alias' => 'inv_gl'),
        array('name' => 'Invoice firstname', 'field' => 'invoice_firstname', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice lastname', 'field' => 'invoice_lastname', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        array('name' => 'Invoice company name', 'field' => 'invoice_company', 'as' => true, 'database' => 'address', 'alias' => 'inv_a'),
        //ORDER_PAYMENT
        array('name' => 'Transaction Id', 'field' => 'transaction_id', 'database' => 'order_payment', 'alias' => 'op'),
        //PS_CARRIER
        array('name' => 'Name carrier', 'field' => 'carrier_name', 'as' => true, 'database' => 'carrier', 'alias' => 'ca'),
        //PS_ORDER_DETAIL
        array('name' => 'Product ID', 'field' => 'product_id', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Ref', 'field' => 'product_reference', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Name', 'field' => 'product_name', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Price', 'field' => 'product_price', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Quantity', 'field' => 'product_quantity', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Shop name', 'field' => 'shop_name', 'database' => 'shop', 'as' => true, 'alias' => 'sh'),

        array('name' => 'Message', 'field' => 'message', 'database' => 'message', 'alias' => 'm'),
        array('name' => 'Order currency', 'field' => 'currency_iso_code', 'database' => 'currency', 'as' => true, 'alias' => 'cur'),
        array('name' => 'Product quantity discount', 'field' => 'product_quantity_discount', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Reduction amount', 'field' => 'reduction_amount', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Reduction amount tax incl', 'field' => 'reduction_amount_tax_incl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Reduction amount tax excl', 'field' => 'reduction_amount_tax_excl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product group reduction', 'field' => 'group_reduction', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product ean13', 'field' => 'product_ean13', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Unit price tax incl', 'field' => 'unit_price_tax_incl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Unit price tax excl', 'field' => 'unit_price_tax_excl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Total price tax excl', 'field' => 'total_price_tax_incl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Total price tax excl', 'field' => 'total_price_tax_excl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product Total shipping price tax excl', 'field' => 'total_shipping_price_tax_incl', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product ecotax', 'field' => 'ecotax', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product ecotax rate', 'field' => 'ecotax_tax_rate', 'database' => 'order_detail', 'alias' => 'od'),
        array('name' => 'Product tax rate (order detail table)', 'field' => 'tax_rate', 'database' => 'tax', 'alias' => 'od'),
        array('name' => 'Product tax rate (tax table)', 'field' => 'rate', 'database' => 'tax', 'alias' => 't'),
        array('name' => 'Product tax unit amount', 'field' => 'unit_amount', 'database' => 'order_detail_tax', 'alias' => 'odt'),
        array('name' => 'Product tax total amount', 'field' => 'total_amount', 'database' => 'order_detail_tax', 'alias' => 'odt'),
        array('name' => 'Order state', 'field' => 'orderstate_name', 'database' => 'order_state_lang', 'as' => true, 'alias' => 'osl'),
        array('name' => 'Employee name (last state)', 'field' => 'employee_name', 'database' => 'other'),
        array('name' => 'Delivery state iso', 'field' => 'state_iso_code', 'as' => true, 'database' => 'state', 'alias' => 's'),
    );
    public $categories = array(
        array('name' => 'Id category', 'field' => 'id_category', 'database' => 'category', 'alias' => 'c', 'import' => 1, 'import_name' => 'ID'),
        array('name' => 'Id parent', 'field' => 'id_parent', 'database' => 'category', 'alias' => 'c', 'import' => 4, 'import_name' => 'Parent category'),
        array('name' => 'Id shop default', 'field' => 'id_shop_default', 'database' => 'category', 'alias' => 'c'),
        array('name' => 'Level depth', 'field' => 'level_depth', 'database' => 'category', 'alias' => 'c'),
        array('name' => 'nleft', 'field' => 'nleft', 'database' => 'category', 'alias' => 'c'),
        array('name' => 'nright', 'field' => 'nright', 'database' => 'category', 'alias' => 'c'),
        array('name' => 'active', 'field' => 'active', 'database' => 'category', 'alias' => 'c', 'import' => 2, 'import_name' => 'Active (0/1)'),
        array('name' => 'Is root category', 'field' => 'is_root_category', 'database' => 'category', 'alias' => 'c', 'import' => 5, 'import_name' => 'Root category (0/1)'),
        array('name' => 'Id group', 'field' => 'id_group', 'database' => 'other'),
        array('name' => 'Id shop', 'field' => 'id_shop', 'database' => 'category_lang', 'alias' => 'cl'),
        array('name' => 'Name', 'field' => 'name', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 3, 'import_name' => 'Name *'),
        array('name' => 'Description', 'field' => 'description', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 6, 'import_name' => 'Description'),
        array('name' => 'Link rewrite', 'field' => 'link_rewrite', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 10, 'import_name' => 'URL rewritten'),
        array('name' => 'Meta title', 'field' => 'meta_title', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 7, 'import_name' => 'Meta title'),
        array('name' => 'Meta keywords', 'field' => 'meta_keywords', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 8, 'import_name' => 'Meta keywords'),
        array('name' => 'Meta description', 'field' => 'meta_description', 'database' => 'category_lang', 'alias' => 'cl', 'import' => 9, 'import_name' => 'Meta description'),
        array('name' => 'Position', 'field' => 'position', 'database' => 'category_shop', 'alias' => 'category_shop'),
        array('name' => 'Image URL', 'field' => 'image', 'database' => 'other', 'import' => 11, 'import_name' => 'Image URL'),
    );
    public $manufacturers = array(
        array('name' => 'id manufacturer', 'field' => 'id_manufacturer', 'database' => 'manufacturer', 'alias' => 'm', 'import' => 1, 'import_name' => 'ID'),
        array('name' => 'name', 'field' => 'name', 'database' => 'manufacturer', 'alias' => 'm', 'import' => 3, 'import_name' => 'Name *'),
        array('name' => 'active', 'field' => 'active', 'database' => 'manufacturer', 'alias' => 'm', 'import' => 2, 'import_name' => 'Active (0/1)'),
        array('name' => 'description', 'field' => 'description', 'database' => 'manufacturer', 'alias' => 'ml', 'import' => 4, 'import_name' => 'Description'),
        array('name' => 'short description', 'field' => 'short_description', 'database' => 'manufacturer', 'alias' => 'ml', 'import' => 5, 'import_name' => 'Short description'),
        array('name' => 'meta title', 'field' => 'meta_title', 'database' => 'manufacturer', 'alias' => 'ml', 'import' => 6, 'import_name' => 'Meta title'),
        array('name' => 'meta keywords', 'field' => 'meta_keywords', 'database' => 'manufacturer', 'alias' => 'ml', 'import' => 7, 'import_name' => 'Meta keywords'),
        array('name' => 'meta description', 'field' => 'meta_description', 'database' => 'manufacturer', 'alias' => 'ml', 'import' => 8, 'import_name' => 'Meta description'),
        array('name' => 'id shop', 'field' => 'id_shop', 'database' => 'manufacturer', 'alias' => 'manufacturer_shop'),
        array('name' => 'Image URL', 'field' => 'image', 'database' => 'other', 'import' => 9, 'import_name' => 'Image URL'),
        );
    public $suppliers = array(
        array('name' => 'id supplier', 'field' => 'id_supplier', 'database' => 'supplier', 'alias' => 's', 'import' => 1, 'import_name' => 'ID'),
        array('name' => 'name', 'field' => 'name', 'database' => 'supplier', 'alias' => 's', 'import' => 3, 'import_name' => 'Name *'),
        array('name' => 'active', 'field' => 'active', 'database' => 'supplier', 'alias' => 's', 'import' => 2, 'import_name' => 'Active (0/1)'),
        array('name' => 'description', 'field' => 'description', 'database' => 'supplier', 'alias' => 'sl', 'import' => 4, 'import_name' => 'Description'),
        array('name' => 'meta title', 'field' => 'meta_title', 'database' => 'supplier', 'alias' => 'sl', 'import' => 6, 'import_name' => 'Meta title'),
        array('name' => 'meta keywords', 'field' => 'meta_keywords', 'database' => 'supplier', 'alias' => 'sl', 'import' => 7, 'import_name' => 'Meta keywords'),
        array('name' => 'meta description', 'field' => 'meta_description', 'database' => 'supplier', 'alias' => 'sl', 'import' => 8, 'import_name' => 'Meta description'),
        array('name' => 'id shop', 'field' => 'id_shop', 'database' => 'supplier', 'alias' => 'supplier_shop'),
        array('name' => 'Image URL', 'field' => 'image', 'database' => 'other', 'import' => 9, 'import_name' => 'Image URL'),
        );
    public $customers = array(
        array('name' => 'id customer', 'field' => 'id_customer', 'database' => 'customer', 'alias' => 'c', 'import' => 1, 'import_name' => 'ID'),
        array('name' => 'id gender', 'field' => 'id_gender', 'database' => 'customer', 'alias' => 'c', 'import' => 3, 'import_name' => 'Titles ID (Mr = 1, Ms = 2, else 0)'),
        array('name' => 'company', 'field' => 'company', 'database' => 'customer', 'alias' => 'c'),
        array('name' => 'siret', 'field' => 'siret', 'database' => 'customer', 'alias' => 'c'),
        array('name' => 'ape', 'field' => 'ape', 'database' => 'customer', 'alias' => 'c'),
        array('name' => 'firstname', 'field' => 'firstname', 'database' => 'customer', 'alias' => 'c', 'import' => 8, 'import_name' => 'First Name *'),
        array('name' => 'lastname', 'field' => 'lastname', 'database' => 'customer', 'alias' => 'c', 'import' => 7, 'import_name' => 'Last Name *'),
        array('name' => 'email', 'field' => 'email', 'database' => 'customer', 'alias' => 'c', 'import' => 4, 'import_name' => 'Email *'),
        array('name' => 'birthday', 'field' => 'birthday', 'database' => 'customer', 'alias' => 'c', 'import' => 6, 'import_name' => 'Birthday (yyyy-mm-dd)'),
        array('name' => 'newsletter', 'field' => 'newsletter', 'database' => 'customer', 'alias' => 'c', 'import' => 9, 'import_name' => 'Newsletter (0/1)'),
        array('name' => 'website', 'field' => 'website', 'database' => 'customer', 'alias' => 'c'),
        array('name' => 'password', 'field' => 'passwd', 'database' => 'customer', 'alias' => 'c', 'import' => 5, 'import_name' => 'Passowrd *'),
        array('name' => 'active', 'field' => 'active', 'database' => 'customer', 'alias' => 'c', 'import' => 2, 'import_name' => 'Active (0/1)'),
        array('name' => 'optin', 'field' => 'optin', 'database' => 'customer', 'alias' => 'c', 'import' => 10, 'import_name' => 'Opt-in (0/1)'),
        array('name' => 'date add', 'field' => 'date_add', 'database' => 'customer', 'alias' => 'c', 'import' => 11, 'import_name' => 'Registration date (yyyy-mm-dd)'),
        array('name' => 'default group id', 'field' => 'id_default_group', 'database' => 'customer', 'alias' => 'c', 'import' => 12, 'import_name' => 'Default group ID'),
        array('name' => 'groups', 'field' => 'groups', 'database' => 'other', 'import' => 13, 'import_name' => 'Groups (x,y,z...)'),

        array('name' => 'address company', 'field' => 'address_company', 'database' => 'address', 'as' => true, 'alias' => 'a'),
        array('name' => 'address firstname', 'field' => 'address_firstname', 'database' => 'address', 'as' => true, 'alias' => 'a'),
        array('name' => 'address lastname', 'field' => 'address_lastname', 'database' => 'address', 'as' => true, 'alias' => 'a'),
        array('name' => 'address address1', 'field' => 'address1', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address address2', 'field' => 'address2', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address postcode', 'field' => 'postcode', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address city', 'field' => 'city', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address other', 'field' => 'other', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address phone', 'field' => 'phone', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address phone_mobile', 'field' => 'phone_mobile', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address vat_number', 'field' => 'vat_number', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address dni', 'field' => 'dni', 'database' => 'address', 'alias' => 'a'),
        array('name' => 'address active', 'field' => 'address_active', 'database' => 'address', 'alias' => 'a', 'as' => true),
        array('name' => 'address state', 'field' => 'name', 'database' => 'state', 'alias' => 's'),
        array('name' => 'address country', 'field' => 'country_name', 'database' => 'country_lang', 'alias' => 'co', 'as' => true),
        );
    public $newsletters = array(
        array('name' => 'Email', 'field' => 'email', 'database' => 'newsletter'),
        array('name' => 'Date add', 'field' => 'newsletter_date_add', 'database' => 'newsletter'),
        array('name' => 'Ip', 'field' => 'ip_registration_newsletter', 'database' => 'newsletter'),
        array('name' => 'Referer', 'field' => 'http_referer', 'database' => 'newsletter'),
        array('name' => 'Active', 'field' => 'active', 'database' => 'newsletter'),
    );
    public $addresses = array(
        array('name' => 'id', 'field' => 'id_address', 'database' => 'address', 'alias' => 'a', 'import' => 1, 'import_name' => 'id'),
        array('name' => 'alias', 'field' => 'alias', 'database' => 'address', 'alias' => 'a', 'import' => 2, 'import_name' => 'Alias*'),
        array('name' => 'active', 'field' => 'active', 'database' => 'address', 'alias' => 'a', 'import' => 3, 'import_name' => 'Active (0/1)'),
        array('name' => 'email', 'field' => 'email', 'database' => 'address', 'alias' => 'cu', 'import' => 4, 'import_name' => 'Customer e-mail*'),
        array('name' => 'id customer', 'field' => 'id_customer', 'database' => 'address', 'alias' => 'a', 'import' => 5, 'import_name' => 'Customer ID'),
        array('name' => 'manufacturer', 'field' => 'manufacturer_name', 'database' => 'manufacturer', 'alias' => 'm', 'as' => true, 'import' => 6, 'import_name' => 'Manufacturer'),
        array('name' => 'supplier', 'field' => 'supplier_name', 'database' => 'supplier', 'alias' => 's', 'as' => true, 'import' => 7, 'import_name' => 'Supplier'),
        array('name' => 'company', 'field' => 'company', 'database' => 'address', 'alias' => 'a', 'import' => 8, 'import_name' => 'Company'),
        array('name' => 'lastname', 'field' => 'lastname', 'database' => 'address', 'alias' => 'a', 'import' => 9, 'import_name' => 'Lastname*'),
        array('name' => 'firstname', 'field' => 'firstname', 'database' => 'address', 'alias' => 'a', 'import' => 10, 'import_name' => 'Firstname*'),
        array('name' => 'address 1', 'field' => 'address1', 'database' => 'address', 'alias' => 'a', 'import' => 11, 'import_name' => 'Address 1*'),
        array('name' => 'address 2', 'field' => 'address2', 'database' => 'address', 'alias' => 'a', 'import' => 12, 'import_name' => 'Address 2*'),
        array('name' => 'postcode', 'field' => 'postcode', 'database' => 'address', 'alias' => 'a', 'import' => 13, 'import_name' => 'Zipcode*'),
        array('name' => 'city', 'field' => 'city', 'database' => 'address', 'alias' => 'a', 'import' => 14, 'import_name' => 'City*'),
        array('name' => 'country', 'field' => 'country_name', 'database' => 'country_lang', 'alias' => 'cl', 'as' => true, 'import' => 15, 'import_name' => 'Country*'),
        array('name' => 'state', 'field' => 'state_name', 'database' => 'state', 'alias' => 'st', 'as' => true, 'import' => 16, 'import_name' => 'State*'),
        array('name' => 'other', 'field' => 'other', 'database' => 'address', 'alias' => 'a', 'import' => 17, 'import_name' => 'Other'),
        array('name' => 'phone', 'field' => 'phone', 'database' => 'address', 'alias' => 'a', 'import' => 18, 'import_name' => 'Phone'),
        array('name' => 'mobile', 'field' => 'phone_mobile', 'database' => 'address', 'alias' => 'a', 'import' => 19, 'import_name' => 'Mobile Phone'),
        array('name' => 'vat number', 'field' => 'vat_number', 'database' => 'address', 'alias' => 'a', 'import' => 20, 'import_name' => 'VAT number'),
        array('name' => 'dni', 'field' => 'dni', 'database' => 'address', 'alias' => 'a', 'import' => 21, 'import_name' => 'DNI'),
    );

    public $switch;
    public $showTimeAndMemory;

    public function __construct()
    {
        $this->name = 'advancedexport';
        $this->tab = 'administration';
        $this->bootstrap = true;
        $this->author = 'Smart Soft';
        $this->version = '4.3.1';
        $this->displayName = $this->l('Advanced Export');
        $this->description = $this->l('Complete export tool');
        $this->module_key = 'a3895af3e1e55fa47a756b6e973e77fe';
        $this->link = new Link();
        $this->showTimeAndMemory = false;
        $this->switch = (_PS_VERSION_ >= 1.6 ? 'switch' : 'radio');
        parent::__construct();

        $path = dirname(__FILE__);

        if (strpos(__FILE__, 'Module.php') !== false) {
            $path .= '/../modules/'.$this->name;
        }

        include_once $path.'/classes/AdvancedExportClass.php';
        include_once $path.'/classes/AdvancedExportFieldClass.php';
    }

    public function install()
    {
        if (!$this->createFieldTable()
             || !$this->createSettingsTables()
             || !Configuration::updateGlobalValue('AdvancedExport_CURRENT', 0)
             || !Configuration::updateGlobalValue('AdvancedExport_TOTAL', 0)
             || !Configuration::updateGlobalValue('ADVANCEDEXPORT_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)))) {
            return false;
        }

        if (!parent::install()) {
            return false;
        }

        return true;
    }

    public function createSettingsTables()
    {
        //for test create tmp table
        $table_name = _DB_PREFIX_.'advancedexport';

        $query = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` (
			`id_advancedexport` int(10) unsigned NOT NULL auto_increment,
			`type` varchar(200) NOT NULL,
			`name` varchar(200) NOT NULL,
			`delimiter` varchar(255) NOT NULL,
			`separator` varchar(255) NOT NULL,
			`id_lang` int(10) NOT NULL,
			`charset` varchar(255) NOT NULL,
			`add_header` BOOL NOT NULL DEFAULT 0,
			`decimal_separator` varchar(10) NOT NULL,
			`decimal_round` int(10) NOT NULL,
			`strip_tags` BOOL NOT NULL DEFAULT 0,
			`only_new` BOOL NOT NULL DEFAULT 0,
			`date_from` varchar(255) NOT NULL,
			`date_to` varchar(255) NOT NULL,
            `last_exported_id` int(10) NOT NULL DEFAULT 0,
            `start_id` int(10) NOT NULL DEFAULT 0,
			`end_id` int(10) NOT NULL DEFAULT 0,
			`save_type` int(10) NOT NULL DEFAULT 0,
			`filename` varchar(255) NOT NULL,
			`image_type` varchar(255) NOT NULL,
			`email` varchar(255) NOT NULL,
			`ftp_hostname` varchar(255) NOT NULL,
			`ftp_user_name` varchar(255) NOT NULL,
			`ftp_user_pass` varchar(255) NOT NULL,
			`fields` text  NOT NULL,
			PRIMARY KEY  (`id_advancedexport`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        if (!$this->dbExecute($query)) {
            return false;
        }

        return true;
    }

    public function dbExecute($query)
    {
        return Db::getInstance()->Execute($query);
    }

    public function uninstall()
    {
        if (!$this->removeTables()
            || !Configuration::deleteByName('AdvancedExport_CURRENT')
            || !Configuration::deleteByName('AdvancedExport_TOTAL')) {  //install tables
            return false;
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function removeTables()
    {
        //remove main table
        if (!$this->dbExecute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'advancedexport`') ||
            !$this->dbExecute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'advancedexportfield`')) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $this->_html = '';
        $this->_postProcess();

        if ($this->getValue('addfield') || $this->isSubmit('updateadvancedexportfield')) {
            $this->_html .= $this->displayAddFieldForm(Tools::getValue('type'));
        } elseif ($this->getValue('add_model') || Tools::isSubmit('updateadvancedexport') || Tools::isSubmit('btnSubmit')) {
            $this->_html .= $this->displayAddModelForm($helper = new HelperForm());
        } elseif ($this->getValue('editfields') || $this->isSubmit('editfields')) {
            $this->_html .= $this->displayEditFieldsForm(Tools::getValue('type'));
        } else {
            $this->_html .= $this->displayIndexForm();
        }

        $this->addHeader();

        return $this->_html;
    }

    public function displayEditFieldsForm($type)
    {
        $fields_list = array(
            'id' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'editable',
                'class' => 'another-custom_class',
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'field' => array(
                'title' => $this->l('Field'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'table' => array(
                'title' => $this->l('Table'),
                'orderby' => false,
                'filter' => false,
                'class' => 'ds-table',
                'search' => false,
            ),
            'return' => array(
                'title' => $this->l('Return value'),
                'type' => 'editable',
                'class' => 'ds-return',
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
        );

        if (Tools::isSubmit('cleanpage')) {
            unset($this->context->cookie->{'advancedexportfield_start'});
        }

        if (Tools::getValue('submitFilteradvancedexportfield') != false) {
            $start = Tools::getValue('submitFilteradvancedexportfield') - 1;
            $this->context->cookie->{'advancedexportfield_start'} = $start;
        } elseif (isset($this->context->cookie->{'advancedexportfield_start'})) {
            $start = $this->context->cookie->{'advancedexportfield_start'};
        } else {
            $start = 0;
        }

        if (Tools::getValue('advancedexportfield_pagination') != false) {
            $limit = Tools::getValue('advancedexportfield_pagination');
            $this->context->cookie->{'advancedexportfield_pagination'} = Tools::getValue('advancedexportfield_pagination');
        } elseif (isset($this->context->cookie->{'advancedexportfield_pagination'})) {
            $limit = $this->context->cookie->{'advancedexportfield_pagination'};
        } else {
            $limit = 20;
        }

        $fields = AdvancedExportFieldClass::getAllFieldsWithPagination($type, $limit, $start);

        for ($i = 0; $i < count($fields); ++$i) {
            $fields[$i]['id'] = $fields[$i]['id_advancedexportfield'];
        }

        $helper = new HelperList();
        $this->_display = 'editfields';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('delete', 'edit');
        $helper->no_link = true;
        $helper->_default_pagination = 20;
        //$helper->_pagination = array(20, 40, 50);
        $helper->show_toolbar = true;
        $helper->row_hover = false;
        $helper->toolbar_btn = $this->initToolbar($type);
        $helper->module = $this;
        $helper->listTotal = AdvancedExportFieldClass::getNumberOfRows($type);
        $helper->identifier = 'id_advancedexportfield';
        $helper->title = 'This list of fields avabile under tab';
        $helper->table = 'advancedexportfield';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        if (!(int) Tools::getValue('submitFilteradvancedexportfield')) {
            $page = (int) Tools::getValue('submitFilteradvancedexportfield');
        } else {
            $page = 1;
        }
        $helper->currentIndex = AdminController::$currentIndex.'&editfields=1&type='.$type.'&configure='.$this->name.'&submitFilteradvancedexportfield='.$page;

        $return = $helper->generateList($fields, $fields_list);

        return $return;
    }

    public function addHeader()
    {
        $this->addCSS($this->_path.'views/css/admin.css');
        $this->addCSS($this->_path.'views/css/bootstrap-editable.css');
        $this->addCSS($this->_path.'views/css/jquery.percentageloader-0.1.css');

        if (_PS_VERSION_ >= 1.6) {
            $this->addJS(_PS_JS_DIR_.'jquery/ui/jquery.ui.sortable.min.js');
            $this->addJS($this->_path.'views/js/admin.js');
        } else {
            $this->addJS($this->_path.'views/js/jquery-ui-1.10.4.custom.min.js');
            $this->addJS($this->_path.'views/js/admin15.js');
        }

        $this->addJS($this->_path.'views/js/jquery.percentageloader-0.1.min.js');
        $this->addJS($this->_path.'views/js/jquery.bsmselect.js');
        $this->addJS($this->_path.'views/js/jquery.bsmselect.compatibility.js');
        $this->addJS($this->_path.'views/js/jquery.bsmselect.sortable.js');

        $this->addJS($this->_path.'views/js/jquery.cooki-plugin.js');
        $this->addJS($this->_path.'views/js/clipboard.min.js');
    }

    public function _postProcess()
    {
        if ($this->getValue('ajax')) {
            $this->hookAjaxCall();
        }

        if ($this->_postValidation() == false) {
            return false;
        }

        $errors = '';

        if (Tools::isSubmit('btnSubmit')) {
            if ($this->saveModel()) {
                $this->redirect('saveModelConfirmation');
            }
        } elseif (Tools::isSubmit('deleteadvancedexport')) {
            if (!$this->deleteModel()) {
                $this->redirect('deleteModelConfirmation');
            }
        } elseif ($this->isSubmit('duplicateadvancedexport') && $this->getValue('id_advancedexport')) {
            $time_start = microtime(true);  //debug

            $this->getExportType($this->getValue('id_advancedexport'));

            if ($this->showTimeAndMemory) {
                $this->showTimeAndMemoryUsage($time_start);
            } else {
                $this->redirect('exportConfirmation');
            }
        } elseif ($this->isSubmit('viewfiles') && $this->getValue('url')) {
            $this->getFile($this->getValue('url'));
        } elseif ($this->isSubmit('generate') && $type = $this->getValue('type')) {
            $this->generateDefaultCsvForImport($type);
            $this->redirect('generateConfirmation');
        } elseif ($this->isSubmit('deletefiles') && $url = $this->getValue('url')) {
            $this->deleteFile($url);
            $this->redirect('deleteFileConfirmation');
        } elseif ($this->isSubmit('submitSaveFields')) {
            $fields = AdvancedExportFieldClass::getAllFields(Tools::getValue('type'));
            foreach ($fields as $field) {
                $name = Tools::getValue('name_'.$field['id_advancedexportfield']);
                if ($name != false && $name != '') {
                    $f = new AdvancedExportFieldClass($field['id_advancedexportfield']);
                    $f->name = $name;
                    $return = Tools::getValue('return_'.$field['id_advancedexportfield']);
                    if ($return != false && $f->table = 'static') {
                        $f->return = $return;
                    }
                    $f->save();
                }
            }
            $this->redirect('editfields&updateFieldsConfirmation&type='.Tools::getValue('type'));
        } elseif ($this->isSubmit('deleteadvancedexportfield')) {
            $id = Tools::getValue('id_advancedexportfield');
            $field = new AdvancedExportFieldClass($id);
            if ($field->table == 'static') {
                $field->delete();

                $this->redirect('editfields&deleteFieldConfirmation&type='.Tools::getValue('type').'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield'));
            } else {
                $this->redirect('editfields&deleteFieldError&type='.Tools::getValue('type').'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield'));
            }
        } elseif ($this->isSubmit('submitAddField')) {
            $id = Tools::getValue('id_advancedexportfield');
            $field = new AdvancedExportFieldClass($id);
            $field->tab = Tools::getValue('type');
            $field->name = Tools::getValue('name');
            $field->return = Tools::getValue('return');
            $field->table = 'static';
            $field->isCustom = 1;
            if ($id) {
                $field->field = 'field_'.$field->id;
                $field->save();
            } else {
                $field->save();
                $field->field = 'field_'.$field->id;
                $field->save();
            }

            $this->redirect('editfields&saveFieldConfirmation&type='.Tools::getValue('type').'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield'));
        } elseif (Tools::isSubmit('saveModelConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Model save successfully.'));
        } elseif (Tools::isSubmit('deleteModelConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Model deleted successfully.'));
        } elseif (Tools::isSubmit('deleteFileConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('File deleted successfully.'));
        } elseif (Tools::isSubmit('exportConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Export finish successfully.'));
        } elseif (Tools::isSubmit('deleteFieldConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Field deleted successfully.'));
        } elseif (Tools::isSubmit('updateFieldsConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Fields updated successfully.'));
        } elseif (Tools::isSubmit('deleteFieldError')) {
            $this->_html .= $this->displayError($this->l('You can nott delete none static fields.'));
        } elseif (Tools::isSubmit('saveFieldConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Field saved successfully.'));
        } elseif (Tools::isSubmit('generateConfirmation')) {
            $this->_html .= $this->displayConfirmation($this->l('Model generated successfully.'));
        }

        if ($errors) {
            $this->_html .= $this->displayError($errors);
        }
    }

    protected function _postValidation()
    {
        $this->_errors = array();

        if (Tools::isSubmit('btnSubmit')) {
            $fields = Tools::getValue('fields');
            if (!is_array($fields)) {
                $this->_errors[] = $this->l('You must choose at least one field.');
            }
            if (!Validate::isGenericName(Tools::getValue('name')) || Tools::getValue('name') == '') {
                $this->_errors[] = $this->l('Invalid or empty name.');
            }
            if (Tools::getValue('filename') != '' && !Validate::isGenericName(Tools::getValue('filename'))) {
                $this->_errors[] = $this->l('Invalid file name.');
            }
            if (Tools::getValue('date_from') != '' && !Validate::isDate(Tools::getValue('date_from'))) {
                $this->_errors[] = $this->l('Invalid date from.');
            }
            if (Tools::getValue('date_to') != '' && !Validate::isDate(Tools::getValue('date_to'))) {
                $this->_errors[] = $this->l('Invalid date to.');
            }
            if (Tools::getValue('start_id') != '' && !Validate::isInt(Tools::getValue('start_id'))) {
                $this->_errors[] = $this->l('Invalid begin id field.');
            }
            if (Tools::getValue('end_id') != '' && !Validate::isInt(Tools::getValue('end_id'))) {
                $this->_errors[] = $this->l('Invalid finish id field.');
            }
        }

        if (Tools::isSubmit('submitAddField')) {
            if (!Validate::isName(Tools::getValue('name')) || Tools::getValue('name') == '') {
                $this->_errors[] = $this->l('Invalid or empty name.');
            }
        }

        if (count($this->_errors)) {
            foreach ($this->_errors as $err) {
                $this->_html .= '<div class="alert alert-danger">'.$err.'</div>';
            }

            return false;
        }

        return true;
    }

    public function saveModel()
    {
        $functionName = ($this->getValue('type') ? $this->getValue('type') : '').'FormFields';
        $specific = $this->$functionName();

        $to_serialize = null;
        foreach ($specific as $value) {
            $trimmed = str_replace('[]', '', $value['name']);
            if ($this->getValue($trimmed) != '') {
                $to_serialize[$value['name']] = $this->getValue($trimmed);
            }
        }

        $AdvancedExport = new AdvancedExportClass($this->getValue('id_advancedexport'));
        $AdvancedExport->copyFromPost();
        $AdvancedExport->fields = Tools::jsonEncode($to_serialize);
        $AdvancedExport->save();

        return true;
    }

    public function getValue($value)
    {
        return Tools::getValue($value);
    }

    public function redirect($action)
    {
        Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&'.$action);
    }

    public function deleteModel()
    {
        $AdvancedExport = new AdvancedExportClass($this->getValue('id_advancedexport'));
        $AdvancedExport->delete();

        return true;
    }

    public function addCSS($path)
    {
        return $this->context->controller->addCSS($path);
    }

    public function addJS($path)
    {
        return $this->context->controller->addJS($path);
    }

    public function displayAddModelForm($helper)
    {
        $type = null;
        if ($id = Tools::getValue('id_advancedexport')) {
            $aec = new AdvancedExportClass($id);
            if (is_object($aec)) {
                $type = $aec->type;
            }
        } else {
            $type = Tools::getValue('type');
        }

        $helper->module = $this;
        $helper->show_toolbar = true;
        $helper->table = 'advancedexport';
        $helper->id = (int) $id;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'btnSubmit';
        $helper->token = $this->getValue('token');
        $helper->currentIndex = $this->getAdminLink().'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->fields_value = $this->getModelFieldsValues($type);

        return $helper->generateForm($this->modelFormFields($type));
    }

    public function displayAddFieldForm()
    {
        if ($id = Tools::getValue('id_advancedexportfield')) {
            $field = new AdvancedExportFieldClass($id);
            if (is_object($field)) {
                $type = $field->tab;
            }
        } else {
            $type = Tools::getValue('type');
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = true;
        $helper->table = 'advancedexportfield';
        $helper->id = (int) $id;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitAddField';
        $helper->token = $this->getValue('token');
        $helper->currentIndex = $this->getAdminLink().'&configure='.$this->name.'&addfield=1&tab_module='.$this->tab.'&module_name='.$this->name.'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield');
        $helper->fields_value = $this->getAddFieldFieldsValues($type);

        return $helper->generateForm($this->addFieldFormFields($type));
    }

    public function getAdminLink()
    {
        return $this->context->link->getAdminLink('AdminModules', false);
    }

    public function getAddFieldFieldsValues()
    {
        $fields_value = null;

        if ($this->getValue('id_advancedexportfield')) {
            $field = new AdvancedExportFieldClass($this->getValue('id_advancedexportfield'));
        }

        if (Tools::getValue('id_advancedexportfield')) {
            $fields_value['id_advancedexportfield'] = Tools::getValue('id_advancedexportfield');
        } elseif (isset($field) && isset($field->id)) {
            $fields_value['id_advancedexportfield'] = $field->id;
        } else {
            $fields_value['id_advancedexportfield'] = '';
        }

        if (Tools::getValue('type')) {
            $fields_value['type'] = Tools::getValue('type');
        } elseif (isset($field) && isset($field->tab)) {
            $fields_value['type'] = $field->tab;
        } else {
            $fields_value['type'] = '';
        }

        if (Tools::getValue('name')) {
            $fields_value['name'] = Tools::getValue('name');
        } elseif (isset($field) && isset($field->name)) {
            $fields_value['name'] = $field->name;
        } else {
            $fields_value['name'] = '';
        }

        if (Tools::getValue('return')) {
            $fields_value['return'] = Tools::getValue('return');
        } elseif (isset($field) && isset($field->return)) {
            $fields_value['return'] = $field->return;
        } else {
            $fields_value['return'] = '';
        }

        if (Tools::getValue('table')) {
            $fields_value['table'] = Tools::getValue('table');
        } elseif (isset($field) && isset($field->table)) {
            $fields_value['table'] = ($field->table == 'static' ? 1 : 2);
        } else {
            $fields_value['table'] = '';
        }

        return $fields_value;
    }

    public function getModelFieldsValues($type)
    {
        $fields = array('id_advancedexport', 'type', 'name', 'delimiter', 'separator', 'id_lang', 'charset', 'add_header', 'decimal_separator', 'decimal_round', 'strip_tags', 'file_type', 'only_new', 'save_type', 'shops', 'image_type', 'filename', 'email', 'ftp_hostname', 'ftp_user_pass', 'ftp_user_name', 'date_from', 'date_to', 'start_id', 'end_id');
        $fields_specific = null;
        $fields_value = null;

        if ($this->getValue('id_advancedexport')) {
            $ae = new AdvancedExportClass($this->getValue('id_advancedexport'));
            $fields_specific = Tools::jsonDecode($ae->fields, true);
        }
        foreach ($fields as $field) {
            if (Tools::getValue($field)) {
                $fields_value[$field] = Tools::getValue($field);
            } elseif (isset($ae) && isset($ae->$field)) {
                $fields_value[$field] = $ae->$field;
            } else {
                $fields_value[$field] = '';
            }
        }

        $functionName = ($type ? $type : 'products').'FormFields';
        $specific = $this->$functionName();

        foreach ($specific as $value) {
            if (isset($fields_specific[$value['name']])) {
                $fields_value[$value['name']] = $fields_specific[$value['name']];
            } else {
                $fields_value[$value['name']] = null;
            }
        }

        if (isset($fields_specific['categories'])) {
            $this->selected_cat = $fields_specific['categories'];
        }

        return $fields_value;
    }

    public function addFieldFormFields($type)
    {
        $result = array();

        $result[0] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Add Field Form'),
                    'icon' => 'icon-envelope',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_advancedexportfield',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'type',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'name',
                        'required' => true,
                        'desc' => $this->l('Field name'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Return value'),
                        'name' => 'return',
                        'desc' => $this->l('Return value'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    'cancelBlock' => array(
                        'title' => $this->l('Cancel'),
                        'href' => $this->getAdminUrl().'&editfields&type='.$type.'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield'),
                        'icon' => 'process-icon-cancel',
                    ),
                ),
            ),
        );

        return $result;
    }

    public function modelFormFields($type)
    {
        $functionName = ($type ? $type : 'products').'FormFields';
        $result = null;

        $result[0] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings Form'),
                    'icon' => 'icon-envelope',
                ),
                'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_advancedexport',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'type',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'desc' => $this->l('Settings insternal name'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('File name'),
                    'name' => 'filename',
                    'desc' => $this->l('You can set name for file or leave blank name will be given by system.'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Delimiter'),
                    'name' => 'delimiter',
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => ',',
                                'name' => '[,] '.$this->l('comma'),
                            ),
                            array(
                                'value' => ';',
                                'name' => '[;] '.$this->l('semi-colons'),
                            ),
                            array(
                                'value' => ':',
                                'name' => '[:] '.$this->l('colons'),
                            ),
                            array(
                                'value' => '|',
                                'name' => '[|] '.$this->l('pipes'),
                            ),
                            array(
                                'value' => '~',
                                'name' => '[~] '.$this->l('tilde'),
                            ),
                        ),
                        'id' => 'value',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Separrator for each line in csv file'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Separator'),
                    'name' => 'separator',
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => '&#34;',
                                'name' => '["] '.$this->l('quotation marks'),
                            ),
                            array(
                                'value' => "'",
                                'name' => "['] ".$this->l('single quotation marks'),
                            ),
                            // array(
                            //  'value' => '',
                            //     'name' => $this->l('no separator'),
                            // )
                        ),
                        'id' => 'value',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Separrator for each value in csv file'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Language'),
                    'name' => 'id_lang',
                    'col' => '4',
                    'options' => array(
                        'query' => Language::getLanguages(),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Default utf-8.'),

                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Encoding type'),
                    'name' => 'charset',
                    'col' => '4',
                    'options' => array(
                        'query' => $this->getCharsets(),
                        'id' => 'name',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Default utf-8.'),

                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Decimal separator'),
                    'name' => 'decimal_separator',
                    'options' => array(
                        'query' => array(
                            array(
                                'value' => ',',
                                'name' => '[,] '.$this->l('comma'),
                            ),
                            array(
                                'value' => '.',
                                'name' => '[.] '.$this->l('dot'),
                            ),
                        ),
                        'default' => array(
                            'label' => $this->l('default'),
                            'value' => -1,
                        ),
                        'id' => 'value',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Round values'),
                    'name' => 'decimal_round',
                    'options' => array(
                        'default' => array(
                            'label' => $this->l('default'),
                            'value' => '-1',
                        ),
                        'query' => array(
                            array(
                                'value' => '0',
                                'label' => '0',
                            ),
                            array(
                                'value' => '1',
                                'label' => '1',
                            ),
                            array(
                                'value' => '2',
                                'label' => '2',
                            ),
                            array(
                                'value' => '3',
                                'label' => '3',
                            ),
                            array(
                                'value' => '4',
                                'label' => '4',
                            ),
                            array(
                                'value' => '5',
                                'label' => '5',
                            ),
                            array(
                                'value' => '6',
                                'label' => '6',
                            ),
                        ),
                        'id' => 'value',
                        'name' => 'label',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Save type'),
                    'name' => 'save_type',
                    'default' => '0',
                    'options' => array(
                        'query' => $this->getSaveTypes(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('Save your file, sent to server or email.'),

                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'email',
                    'class' => 'process2',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hostname'),
                    'name' => 'ftp_hostname',
                    'class' => 'process1',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Username'),
                    'name' => 'ftp_user_name',
                    'class' => 'process1',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Password'),
                    'name' => 'ftp_user_pass',
                    'class' => 'process1',
                ),
                array(
                    'type' => $this->switch,
                    'label' => $this->l('Display labels'),
                    'name' => 'add_header',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => $this->switch,
                    'label' => $this->l('Strip tags'),
                    'name' => 'strip_tags',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => $this->switch,
                    'label' => $this->l('Export not exported'),
                    'name' => 'only_new',
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Export not exported yet.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                         ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                         ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Begin id'),
                    'name' => 'start_id',
                    'desc' => $this->l('You can specyify start id number.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Finish id'),
                    'name' => 'end_id',
                    'desc' => $this->l('You can specify end id number.'),
                ),
                array(
                    'type' => (_PS_VERSION_ >= 1.6 ? 'datetime' : 'date'),
                    'label' => $this->l('From'),
                    'name' => 'date_from',
                    'size' => 10,
                    'maxlength' => 10,
                    'desc' => $this->l('Format: 2011-12-31 HH-MM-SS(inclusive).'),
                ),
                array(
                    'type' => (_PS_VERSION_ >= 1.6 ? 'datetime' : 'date'),
                    'label' => $this->l('To'),
                    'name' => 'date_to',
                    'size' => 10,
                    'maxlength' => 10,
                    'desc' => $this->l('Format: 2012-12-31 HH-MM-SS(inclusive).'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
             ),
            'buttons' => array(
                    'cancelBlock' => array(
                        'title' => $this->l('Cancel'),
                        'href' => $this->getAdminUrl(),
                        'icon' => 'process-icon-cancel',
                    ),
                ),
            ),
         );

        if ($type != 'orders' && $type != 'newsletters' && $type != 'customers' && $type != 'addresses') {
            $result[0]['form']['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Image type'),
                'name' => 'image_type',
                'col' => '4',
                'options' => array(
                    'query' => ImageType::getImagesTypes($type),
                    'id' => 'name',
                    'name' => 'name',
                ),
            );
        }

        $specific = $this->$functionName();
        $result[0]['form']['input'] = array_merge($result[0]['form']['input'], $specific);

        return $result;
    }

    public function getCharsets()
    {
        return array(
            array('id' => 1, 'name' => 'UTF-8'),
            array('id' => 2, 'name' => 'ISO-8859-1'),
            array('id' => 3, 'name' => 'GB2312'),
            array('id' => 4, 'name' => 'Windows-1251'),
            array('id' => 5, 'name' => 'Windows-1252'),
            array('id' => 6, 'name' => 'Shift JIS'),
            array('id' => 7, 'name' => 'GBK'),
            array('id' => 8, 'name' => 'Windows-1256'),
            array('id' => 9, 'name' => 'ISO-8859-2'),
            array('id' => 10, 'name' => 'EUC-JP'),
            array('id' => 11, 'name' => 'ISO-8859-15'),
            array('id' => 12, 'name' => 'ISO-8859-9'),
            array('id' => 13, 'name' => 'Windows-1250'),
            array('id' => 14, 'name' => 'Windows-1254'),
            array('id' => 15, 'name' => 'EUC-KR'),
            array('id' => 16, 'name' => 'Big5'),
            array('id' => 17, 'name' => 'Windows-874'),
            array('id' => 18, 'name' => 'US-ASCII'),
            array('id' => 19, 'name' => 'TIS-620'),
            array('id' => 20, 'name' => 'ISO-8859-7'),
            array('id' => 21, 'name' => 'Windows-1255'),
        );
    }

    public function getSaveTypes()
    {
        return array(
            array('id' => 0, 'name' => $this->l('Save to disc'), 'short_name' => $this->l('disc')),
            array('id' => 1, 'name' => $this->l('Save to server'), 'short_name' => $this->l('ftp')),
            array('id' => 2, 'name' => $this->l('Sent to emal'), 'short_name' => $this->l('email')),
        );
    }

    public function getFileTypes()
    {
        return array(
            array('id' => 1, 'name' => 'csv'),
            array('id' => 2, 'name' => 'xml'),
        );
    }

    public function getAdminUrl()
    {
        return AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
    }

    public function isFeatureActive()
    {
        return Shop::isFeatureActive();
    }

    public function isSubmit($value)
    {
        return Tools::isSubmit($value);
    }

    public function getExportType($id_advancedexport)
    {
        Configuration::updateGlobalValue('AdvancedExport_CURRENT', 0);

        $ae = new AdvancedExportClass($id_advancedexport);
        $this->createExportFile($ae);
    }

    public function showTimeAndMemoryUsage($time_start)
    {
        echo "</br></br></br></br></br></br></br><b>Using <span style='font-size:20px;'>".memory_get_peak_usage(1).'</span> bytes of ram.</b>';
        $time_end = microtime(true);
        //dividing with 60 will give the execution time in minutes other wise seconds
        $execution_time = ($time_end - $time_start) / 60;
        //execution time of the script
        echo '</br><b>Total Execution Time: <span style="font-size:20px;"> '.$execution_time.'</span> Mins</b>';
    }

    public function createExportFile($ae)
    {
        ini_set('memory_limit', '725M');
        $ae->fields = Tools::jsonDecode($ae->fields, true);
        $sorted_fields = $this->getLabelsAndFields($ae->type, $ae->fields);

        $functionName = $ae->type.'Query';
        $elements = $this->$functionName($ae, $sorted_fields);
        //total number for progress bar
        Configuration::updateGlobalValue('AdvancedExport_TOTAL', DB::getInstance()->numRows());

        $url = $this->writeToFile($ae, $sorted_fields, $elements);

        $this->saveLastId($ae, $this->lastElement);

        $this->processFile($ae->save_type, $url, $ae);
    }

    public function getLabelsAndFields($type, $fields)
    {
        if ($fields) {
            set_time_limit(0);
            $allFields = AdvancedExportFieldClass::getAllFields($type);

            foreach ($fields['fields[]'] as $field) {
                $fields['allexportfields'][] = $allFields[$field]['field'];
                $fields['labels'][] = $allFields[$field]['name'];

                if ($allFields[$field]['table'] == 'other') {
                    $fields['otherfields'][$allFields[$field]['field']] = $allFields[$field]['field'];
                } elseif ($allFields[$field]['table'] == 'static') {
                    $fields['static'][$allFields[$field]['field']] = $allFields[$field]['return'];
                } else {
                    //Jeśli jest podany alias w array
                    //to użyj ten alias jako prefix w nazwie tabeli
                    //wraz z kropką.
                    $alias = (isset($allFields[$field]['alias']) && $allFields[$field]['alias'] ? $allFields[$field]['alias'].'.' : '');

                    //jeśli wartość as jest false
                    //to utwórz polecenie as
                    //wymagane przy nazwach pól które się powtarzają
                    if (isset($allFields[$field]['as']) && $allFields[$field]['as']) {
                        $fields['sqlfields'][] = $alias.'`'.Tools::substr(strstr($allFields[$field]['field'], '_'), Tools::strlen('_')).'` as '.$allFields[$field]['field'].'';
                    } else {
                        $fields['sqlfields'][] = $alias.'`'.$allFields[$field]['field'].'`';
                    }
                }

                if (isset($allFields[$field]['attribute']) && $allFields[$field]['attribute'] == true) {
                    $fields['attribute_fields'][] = $allFields[$field]['field'];
                }

                if ($allFields[$field]['table'] == 'order_detail') {
                    $fields['order_detail'] = true;
                    $fields['sqlfields'][] = 'od.`product_id`';
                    $fields['sqlfields'][] = 'od.`product_attribute_id`';
                    $fields['sqlfields'][] = 'o.`id_cart`';
                }
            }
        }

        return $fields;
    }

    //manufacturer name

    public function writeToFile($ae, $sorted_fields, $elements)
    {
        $url = $this->getFileUrl($ae->filename, $ae->type);

        $file = @fopen($url, 'w');
        //add labels for export data
        if ($ae->add_header) {
            $this->filewrite($ae, $sorted_fields, $file);
        }

        $i = 0;
        while ($element = $this->nextRow($elements)) {
            if ($i == $this->rowsNumber - 1) {
                $this->lastElement = $element;
            }
            Configuration::updateGlobalValue('AdvancedExport_CURRENT', (string) $i);
            //progress bar
            $this->getDataObjectFromAndStaticFields($element, $file, $sorted_fields, $ae);
            ++$i; //progress bar
        }

        //close file
        if ($file) {
            fclose($file);
        }

        return $url;
    }

    public function nextRow($elements)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($elements);
    }

    public function hookAjaxCall()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');

        $action = Tools::getValue('action');

        switch ($action) {
            case 'getCurrentIndex':
                $response = array(
                    'total' => (int) Configuration::get('AdvancedExport_TOTAL'),
                    'current' => (int) Configuration::get('AdvancedExport_CURRENT'),
                );
                echo Tools::jsonEncode($response);
                break;
        }
    }

    public function getFileUrl($filename, $type)
    {
        //open file for write
        if ($filename == null || $filename == '') {
            $filename = $type.date('Y-m-d_His').'.csv';
        } else {
            $filename = $filename.'.csv';
        }

        $url = _PS_ROOT_DIR_.'/modules/advancedexport/csv/'.$type;
        if (!is_dir($url)) {
            mkdir($url);
        }

        return $url.'/'.$filename;
    }

    /**
     * Translates a string with underscores
     * into camel case (e.g. first_name -> firstName)
     *
     * @param string $str String in underscore format
     * @param bool $capitalise_first_char If true, capitalise the first char in $str
     * @return string $str translated into camel caps
     */
    public function toCamelCase($str, $capitalise_first_char = false)
    {
        if ($capitalise_first_char) {
            $str[0] = Tools::strtoupper($str[0]);
        }
        $func = create_function('$c', 'return Tools::strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    public function getDataObjectFromAndStaticFields($element, $file, $sorted_fields, $ae)
    {
        $id_object = $element[$this->getId($ae->type)];
        //instance od product object
        $obj = $this->getObject($id_object, $ae->type);
        //has attributes clean varible
        $this->hasAttr = 0;
        if (isset($sorted_fields['otherfields'])) {
            foreach ($sorted_fields['otherfields'] as $key => $value) {
                //convert string to camel case
                //to meet prestashop validation rools
                $run = $this->toCamelCase($ae->type.'_'.$value);
                $element[$value] = $this->$run($obj, $ae, $element);
            }
        }

        //add static fields
        if (isset($sorted_fields['static'])) {
            foreach ($sorted_fields['static'] as $key => $value) {
                $element[$key] = $value;
            }
        }

        if ($ae->type == 'products' && isset($ae->fields['attributes']) && $ae->fields['attributes'] && isset($sorted_fields['attribute_fields'])) {
            $this->processWithAttributes($obj, $element, $file, $sorted_fields, $ae);
        }

        if ($this->hasAttr == 0) {
            $this->fputToFile($file, $sorted_fields['allexportfields'], $element, $ae);
        }

        return $element;
    }

    //get default category name

    public function getObject($id_object, $type, $full = false)
    {
        switch ($type) {
            case 'categories':
                $type = new Category($id_object);
                break;
            case 'manufacturers':
                $type = new Manufacturer($id_object);
                break;
            case 'newsletters':
                $type = null;
                break;
            case 'orders':
                $type = new Order($id_object);
                break;
            case 'products':
                $type = new Product($id_object, $full);
                break;
            case 'suppliers':
                $type = new Supplier($id_object);
                break;
            case 'customers':
                $type = new Customer($id_object);
                break;
            default:
                $type = null;
                break;
        }

        return $type;
    }

    //get images url

    public function processWithAttributes($obj, $element, $file, $sorted_fields, $ae)
    {
        $combArray = null;
        $combArray = $this->getProductCombination($obj, $ae);
        $elementCopy = null;
        if (isset($combArray)) {
            $this->hasAttr = 1;
            foreach ($combArray as $products_attribute) {
                $elementCopy = null;
                $elementCopy = $element;

                foreach ($sorted_fields['attribute_fields'] as $value) {
                    $run = $this->toCamelCase('combination_'.$value);
                    $elementCopy[$value] = $this->$run($obj, $products_attribute, $ae);
                }

                $this->fputToFile($file, $sorted_fields['allexportfields'], $elementCopy, $ae);
            }
        }

        return $elementCopy;
    }

    //tags

    public function fputToFile($file, $allexportfields, $object, $ae)
    {
        if ($allexportfields && $file && $object && $ae) {
            //one ready for export product
            $readyForExport = array();
            //put in correct sort order
            foreach ($allexportfields as $value) {
                $object = $this->processDecimalSettings($object, $ae, $value);
                $readyForExport[$value] = iconv('UTF-8', $ae->charset, $object[$value]);
            }

            //write into csv line by line
            if ($ae->separator == '') {
                fputs($file, implode($readyForExport, $ae->delimiter)."\n");
            } else {
                fputcsv($file, $readyForExport, $ae->delimiter, $ae->separator);
            }
        }
    }

    //tags

    public function saveLastId($ae, $myLastElement)
    {
        if ($ae->only_new && isset($myLastElement[$this->getId($ae->type)])) {
            $ae->last_exported_id = $myLastElement[$this->getId($ae->type)];
            $ae->fields = Tools::jsonEncode($ae->fields);
            $ae->save();
        }

        return $ae;
    }

    public function getId($type)
    {
        $id = '';
        switch ($type) {
            case 'categories':
                $id = 'id_category';
                break;
            case 'customers':
                $id = 'id_customer';
                break;
            case 'manufacturers':
                $id = 'id_manufacturer';
                break;
            case 'newsletters':
                $id = 'id';
                break;
            case 'orders':
                $id = 'id_order';
                break;
            case 'products':
                $id = 'id_product';
                break;
            case 'suppliers':
                $id = 'id_supplier';
                break;
        }

        return $id;
    }

    public function processFile($process, $url, $ae)
    {
        switch ($process) {
            case 0:
                break;
            case 1:
                $this->ftpFile($ae->ftp_hostname, $ae->ftp_user_name, $ae->ftp_user_pass, $url);
                break;
            case 2:
                $this->sentFile($url, $ae->email, $ae->filename, $ae->name);
                break;
            default:
                break;
        }
    }

    public function ftpFile($ftp_hostname, $ftp_user_name, $ftp_user_pass, $export_file)
    {
        // open some file for reading
        $file = basename($export_file);
        $fp = fopen($export_file, 'r');

        // set up basic connection
        $conn = ftp_connect($ftp_hostname);

        // login with username and password
        ftp_login($conn, $ftp_user_name, $ftp_user_pass);

        // try to upload $file
        if (ftp_fput($conn, $file, $fp, FTP_BINARY)) {
            echo "Successfully uploaded $file\n";
        } else {
            echo "There was a problem while uploading $file\n";
        }

        // close the connection and the file handler
        ftp_close($conn);
        fclose($fp);
    }

    public function sentFile($export_file, $email, $filename, $name)
    {
        $file_attachement = null;
        $file_attachement['content'] = Tools::file_get_contents($export_file);
        $file_attachement['name'] = $filename;
        $file_attachement['mime'] = 'text/csv';

        $id_lang = $this->getConfiguration("PS_LANG_DEFAULT");
        Mail::Send($id_lang, 'index', $name, null, $email, null, null, "advanced export", $file_attachement, null, dirname(__FILE__).'/mails/');
    }

    public function getConfiguration($value)
    {
        return (int) Configuration::get($value);
    }

    public function displayIndexForm()
    {
        $html = '<script type="text/javascript">
            var urlJson = "index.php?controller=AdminModules&configure=advancedexport&module_name=advancedexport&token='.Tools::getAdminTokenLite('AdminModules').'";
        </script>';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-12">';
        $html .= '<div id="loader-container">';
        $html .= '<div id="topLoader"> </div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-2 col-md-3">';
        $html .= '<div class="list-group">';
        foreach ($this->export_types as $value) {
            $html .= '<a href="#" id="'.$value.'" class="list-group-item">'.$value.'</a>';
        }
        $html .= '<a href="#" id="tutorial" class="list-group-item"><b>tutorials</b></a>';
        $html .= '<a href="#" id="support" class="list-group-item"><b>Support</b></a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-horizontal col-lg-10">';
        $html .= '<div class="list-group">';

        foreach ($this->export_types as $value) {
            $html .= "<div class='product-tab-content hide' alt='$value'>";
            $html .= $this->displayModelListForm($helper = new HelperList(), $value);
            $html .= $this->displayFilesForm($helper = new HelperList(), $value);
            $html .= '</div>';
        }

        $html .= "<div class='product-tab-content hide' alt='tutorial'>";
        $html .= $this->displayTutorialForm();
        $html .= '</div>';
        $html .= "<div class='product-tab-content hide' alt='support'>";
        $html .= $this->displaySupportForm();
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function displayModelListForm($helper, $type)
    {
        $this->fields_list = $this->listFieldsForm();
        $links = $this->getLinks($type);
        $this->_display = 'index';
        $helper->module = $this;
        $helper->identifier = 'id_advancedexport';
        $helper->actions = array('edit', 'duplicate', 'delete');
        $helper->show_toolbar = true;
        //$helper->simple_header = true;
        $helper->shopLinkType = '';
        $helper->listTotal = count($links);
        $helper->toolbar_btn = $this->initToolbar($type);
        $helper->title = $type.' models';
        $helper->table = 'advancedexport';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->tpl_vars['version'] = false;

        return $helper->generateList($links, $this->fields_list);
    }

    public function listFieldsForm()
    {
        return array(
            'id_advancedexport' => array(
                'title' => $this->l('ID'),
                'width' => 20,
                'orderby' => false,
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 100,
                'orderby' => false,
                'search' => false,
            ),
            'cron_url' => array(
                'title' => $this->l('Cron url'),
                'type' => 'html',
                'orderby' => false,
                'search' => false,
            ),
            'save_type' => array(
                'title' => $this->l('Save Type'),
                'width' => 30,
                'orderby' => false,
                'search' => false,
            ),
            'only_new' => array(
                'title' => $this->l('New entries'),
                'active' => 'only_new',
                'type' => 'bool',
                'align' => 'center',
                'width' => 30,
                'orderby' => false,
                'search' => false,
            ),
        );
    }

    //get features

    public function dbExecuteS($query)
    {
        return Db::getInstance()->ExecuteS($query);
    }

    //price tax

    public function initToolbar($type)
    {
        $current_index = AdminController::$currentIndex;
        $token = Tools::getAdminTokenLite('AdminModules');

        $back = Tools::safeOutput(Tools::getValue('back', ''));

        if (!isset($back) || empty($back)) {
            $back = $current_index.'&configure='.$this->name.'&token='.$token;
        }

        switch ($this->_display) {
            case 'add':
                $this->toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel'),
                );
                break;
            case 'edit':
                $this->toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel'),
                );
                break;
            case 'index':
                $this->toolbar_btn['new'] = array(
                    'href' => $current_index.'&configure='.$this->name.'&token='.$token.'&add_model=1&type='.$type,
                    'desc' => $this->l('Add new'),
                );
                if ($type != 'orders' && $type != 'newsletters') {
                    $this->toolbar_btn['import'] = array(
                        'href' => $current_index.'&configure='.$this->name.'&token='.$token.'&generate=1&type='.$type,
                        'desc' => $this->l('Generate import models'),
                    );
                } else {
                    unset($this->toolbar_btn['import']);
                }
                $this->toolbar_btn['edit'] = array(
                    'href' => $current_index.'&configure='.$this->name.'&token='.$token.'&editfields=1&type='.$type,
                    'desc' => $this->l('Edit fields'),
                );
                break;
            case 'editfields':
                $this->toolbar_btn['new'] = array(
                    'href' => $current_index.'&configure='.$this->name.'&token='.$token.'&addfield=1&type='.$type.'&submitFilteradvancedexportfield='.(int) Tools::getValue('submitFilteradvancedexportfield'),
                    'desc' => $this->l('Add new'),
                );
                $this->toolbar_btn['cancel'] = array(
                    'href' => $current_index.'&configure='.$this->name.'&token='.$token.'&type='.$type,
                    'desc' => $this->l('Cancel'),
                );
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save'),
                );
                break;
        }

        return $this->toolbar_btn;
    }

    //price tax without discount

    public function displayFilesForm($helper, $type)
    {
        $this->fields_list = $this->filesFieldsForm();
        $files = $this->getFiles($type);
        $this->_display = 'index';
        $helper->module = $this;
        $helper->identifier = 'url';
        $helper->simple_header = true;
        $helper->actions = array('view', 'delete');
        $helper->show_toolbar = true;
        $helper->shopLinkType = '';
        $helper->listTotal = count($files);
        $helper->title = $type.' files ';
        $helper->table = 'files';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList($files, $this->fields_list);
    }

    public function formatSize($size)
    {
        $sizes = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        if ($size == 0) {
            return 'n/a';
        } else {
            return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).$sizes[$i];
        }
    }

    public function filesFieldsForm()
    {
        return array(
            'id_file' => array(
                'title' => $this->l('ID'),
                'width' => 25,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 100,
            ),
            'filesize' => array(
                'title' => $this->l('File size'),
                'width' => 100,
            ),
        );
    }

    //wholesale_price

    public function getFiles($type)
    {
        $dirname = _PS_ROOT_DIR_.'/modules/advancedexport/csv/'.$type.'/';
        $files = glob($dirname.'*.{csv}', GLOB_BRACE);

        $result = array();
        $lp = 1;

        if ($files) {
            foreach ($files as $value) {
                $result[] = array(
                    'id_file' => $lp,
                    'name' => basename($value),
                    'filesize' => $this->formatSize(filesize($value)),
                    'url' => urlencode($type.'/'.basename($value)),
                );
                ++$lp;
            }
        }

        return $result;
    }

    public function getFile($url)
    {
        $dir = (string) realpath(dirname(__FILE__).'/csv/'.$url);
        if (file_exists($dir)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.basename($dir));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($dir));
            //ob_clean();
            flush();
            readfile($dir);
            exit;
        }
    }

    public function cronTask()
    {
        Context::getContext()->link = new Link();
        $id = Tools::getValue('id');
        $this->getExportType($id);
    }

    public function deleteFile($url)
    {
        $dir = (string) realpath(dirname(__FILE__).'/csv/'.$url);
        if (file_exists($dir)) {
            unlink($dir);
        }
    }

    public function groupFields($input_arr)
    {
        $grouped = array();
        $level_arr = array();

        foreach ($input_arr as $key => &$entry) {
            $level_arr[$entry['table']][$key] = $entry;
        }
        foreach ($level_arr as $key => &$level) {
            foreach ($level as $l) {
                $l['name'] = $l['table'].' > '.$l['name'];
                $grouped[] = $l;
            }
        }

        return $grouped;
    }

    public function productsFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Products fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'ds-select products',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('products')),
                    'id' => 'field',
                    'name' => 'name',
                    'color' => 'color',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Active products only'),
                'name' => 'active',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                     ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                     ),
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Products out of stock'),
                'name' => 'out_of_stock',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                     ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                     ),
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Products with ean13'),
                'name' => 'ean',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                     ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                     ),
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Export attributes'),
                'name' => 'attributes',
                'is_bool' => true,
                'class' => 't',
                'desc' => $this->l('Each combination will be exported in new line and specific values will be overwrite'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                     ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Suppliers'),
                'name' => 'suppliers[]',
                'id' => 'suppliers',
                'class' => 'ds-select',
                'multiple' => true,
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'options' => array(
                    'query' => Supplier::getSuppliers(false, $this->getConfiguration('PS_LANG_DEFAULT')),
                    'id' => 'id_supplier',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Manufacturers'),
                'name' => 'manufacturers[]',
                'id' => 'manufacturers',
                'class' => 'ds-select',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'multiple' => true,
                'options' => array(
                    'query' => Manufacturer::getManufacturers(false, $this->getConfiguration('PS_LANG_DEFAULT')),
                    'id' => 'id_manufacturer',
                    'name' => 'name',
                ),
            ),
        );
        if (_PS_VERSION_ >= 1.6) {
            $fields[] = array(
                'type' => 'categories',
                'label' => $this->l('Categories'),
                'name' => 'categories',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'tree' => array(
                    'id' => 'categories-tree',
                    'selected_categories' => $this->selected_cat,
                    'use_search' => true,
                    'use_checkbox' => true,
                ),
            );
        } else {
            $root_category = Category::getRootCategory();
            $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);

            $fields[] = array(
                'type' => 'categories',
                'label' => $this->l('Parent category:'),
                'name' => 'categories',
                'values' => array(
                    'trads' => array(
                        'Root' => $root_category,
                        'selected' => $this->l('Selected'),
                        'Collapse All' => $this->l('Collapse All'),
                        'Expand All' => $this->l('Expand All'),
                        'Check All' => $this->l('Check All'),
                        'Uncheck All' => $this->l('Uncheck All'),
                    ),
                    'selected_cat' => ($this->selected_cat ? $this->selected_cat : array()),
                    'input_name' => 'categories[]',
                    'disabled_categories' => array(),
                    'use_checkbox' => true,
                    'use_radio' => false,
                    'use_search' => false,
                    'top_category' => Category::getTopCategory(),
                    'use_context' => true,
                ),
            );
        }

        return $fields;
    }

    public function addToFieldId($fields)
    {
        for ($i = 0; $i < count($fields); ++$i) {
            //add id
            $fields[$i]['id'] = $i;
        }

        return $fields;
    }

    public function productsQuery($ae, $set)
    {
        $sql = 'SELECT p.`id_product` '.(empty($set['sqlfields']) ? '' : ', '.implode(', ', $set['sqlfields'])).
            ' FROM '._DB_PREFIX_.'product as p'.
            Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (p.`id_supplier` = s.`id_supplier`)
            LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
			LEFT JOIN ( SELECT s1.`id_product`, s1.`from`, s1.`to`, s1.`id_cart`,
				IF(s1.`reduction_type` = "percentage", s1.`reduction`, "") as discount_percent,
				IF(s1.`reduction_type` = "amount", s1.`reduction`, "") as discount_amount
				FROM `'._DB_PREFIX_.'specific_price` as s1
				LEFT JOIN `'._DB_PREFIX_.'specific_price` AS s2
					 ON s1.id_product = s2.id_product AND s1.id_specific_price < s2.id_specific_price
				WHERE s2.id_specific_price IS NULL ) as sp_tmp
			ON (p.`id_product` = sp_tmp.`id_product`  && sp_tmp.`id_cart` = 0)'.
            (isset($set['categories']) && $set['categories'] ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
			WHERE pl.`id_lang` = '.(int) $ae->id_lang.
            (isset($ae->only_new) && $ae->only_new && $ae->last_exported_id ? ' AND p.`id_product` > '.$ae->last_exported_id : '').
            ($ae->only_new == false && $ae->start_id ? ' AND p.`id_product` >= '.$ae->start_id : '').
            ($ae->only_new == false && $ae->end_id ? ' AND p.`id_product` <= '.$ae->end_id : '').
            (isset($set['categories']) && $set['categories'] ? ' AND c.`id_category` IN ('.implode(',', $set['categories']).')' : '').
            (isset($set['suppliers[]']) && $set['suppliers[]'] ? ' AND p.`id_supplier` IN ('.implode(',', $set['suppliers[]']).')' : '').
            (isset($set['manufacturers[]']) && $set['manufacturers[]'] ? ' AND p.`id_manufacturer` IN ('.implode(',', $set['manufacturers[]']).')' : '').
            (isset($set['active']) && $set['active'] ? ' AND product_shop.`active` = 1' : '').
            (isset($set['out_of_stock']) && $set['out_of_stock'] ? ' AND p.`quantity` <= 0' : '').
            (isset($set['ean13']) && $set['ean13'] ? ' AND p.`ean13` != ""' : '').
            (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND p.`date_add` >= "'.($ae->date_from).'"' : '').
            (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND p.`date_add` <= "'.($ae->date_to).'"' : '').
            ' GROUP BY p.`id_product`';

        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function executeS($sql)
    {
        return Db::getInstance()->ExecuteS($sql);
    }

    public function execute($sql)
    {
        return Db::getInstance()->Execute($sql);
    }

    public function query($sql_query)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql_query);
    }

    public function productsAttributes()
    {
        return null;
    }

    public function productsAttributesName()
    {
        return null;
    }

    public function productsAttributesValue()
    {
        return null;
    }

    public function productsDependsOnStock($obj)
    {
        return $obj->depends_on_stock;
    }

    public function productsSupplierNameAll($obj)
    {
        $sups = $this->executeS('
		SELECT DISTINCT(s.`name`)
		FROM `'._DB_PREFIX_.'product_supplier` ps
		LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (ps.`id_supplier`= s.`id_supplier`)
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
		WHERE ps.`id_product` = '.$obj->id);
        $suppliers = array();
        foreach ($sups as $sup) {
            $suppliers[] = $sup['name'];
        }

        return implode(',', $suppliers);
    }

    public function productsIdSupplierAll($obj)
    {
        $sups = $this->executeS('
		SELECT DISTINCT(ps.`id_supplier`)
		FROM `'._DB_PREFIX_.'product_supplier` ps
		JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
		WHERE ps.`id_product` = '.$obj->id);
        $suppliers = array();
        foreach ($sups as $sup) {
            $suppliers[] = $sup['id_supplier'];
        }

        return implode(',', $suppliers);
    }

    public function productsFeatures($obj, $ae)
    {
        $features = $obj->getFrontFeaturesStatic($ae->id_lang, $obj->id);
        $feats = array();
        foreach ($features as $feature) {
            $feats[] = $feature['name'].'-'.$feature['value'];
        }

        return implode(',', $feats);
    }

    public function productsWarehouse($obj)
    {
        $warehouse = $this->executeS('
		SELECT `id_warehouse`
		FROM `'._DB_PREFIX_.'warehouse_product_location`
		WHERE `id_product` = '.$obj->id.' AND `id_product_attribute` = 0');

        if (isset($warehouse[0])) {
            return $warehouse[0]['id_warehouse'];
        } else {
            return '';
        }
    }

    public function productsPriceTax($obj)
    {
        return $obj->getPrice(true);
    }

    public function productsTaxRate($obj)
    {
        return $obj->getTaxesRate();
    }

    public function productsQuantity($obj)
    {
        return Product::getQuantity((int) $obj->id);
    }

    public function productsPriceTaxNodiscount($obj)
    {
        return $obj->getPrice(true, null, 6, null, false, false);
    }

    public function productsUrlProduct($obj, $ae)
    {
        $category = Category::getLinkRewrite((int) $obj->id_category_default, (int) $ae->id_lang);

        return $this->context->link->getProductLink((int) $obj->id, $obj->link_rewrite[$ae->id_lang], $category);
    }

    public function productsManufacturerName($obj)
    {
        return $obj->getWsManufacturerName();
    }

    public function productsCategoriesNames($obj, $ae)
    {
        $categories = $obj->getCategories();
        $cats = array();
        foreach ($categories as $cat) {
            $category = new Category($cat, $ae->id_lang);
            $cats[] = $category->name;
        }

        return implode(',', $cats);
    }

    public function productsSupplierReference($obj, $ae)
    {
        // build query
        $query = new DbQuery();
        $query->select('ps.product_supplier_reference');
        $query->from('product_supplier', 'ps');
        $query->where(
            'ps.id_product = '.(int) $obj->id.' AND ps.id_product_attribute = 0'
        );
        $suppliers = null;
        $result = null;
        $suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        foreach ($suppliers as $supplier) {
            if ($supplier['product_supplier_reference']) {
                $result[] = $supplier['product_supplier_reference'];
            }
        }

        return (is_array($result) ? implode(',', $result) : '');
    }

    public function productsCategoriesIds($obj, $ae)
    {
        $categories = $obj->getCategories();
        $cats = array();
        foreach ($categories as $cat) {
            $category = new Category($cat, $ae->id_lang);
            $cats[] = $category->id;
        }

        return implode(',', $cats);
    }

    public function productsNameCategoryDefault($obj, $ae)
    {
        $category = new Category($obj->id_category_default, $ae->id_lang);

        return $category->name;
    }

    public function productsImages($obj, $ae)
    {
        $imagelinks = array();
        $images = $obj->getImages($obj->id);
        foreach ($images as $image) {
            $imagelinks[] = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$image['id_image'], $ae->image_type);
        }

        return implode(',', $imagelinks);
    }

    public function productsImage($obj, $ae)
    {
        $image = Product::getCover($obj->id);
        $imageLink = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$image['id_image'], $ae->image_type);

        return $imageLink;
    }

    public function productsImagePosition($obj, $ae)
    {
        $imagePosition = array();
        $images = $obj->getImages($obj->id);
        foreach ($images as $image) {
            $imagePosition[] = $image['position'];
        }

        return implode(',', $imagePosition);
    }

    public function productsImageAlt($obj, $ae)
    {
        $imagealts = array();
        $images = $obj->getImages($obj->id);
        foreach ($images as $image) {
            if ($image['legend']) {
                $imagealts[] = $image['legend'];
            }
        }

        return implode(',', $imagealts);
    }

    public function productsDefaultCombination()
    {
        return '';
    }

    public function productsTags($obj)
    {
        return $obj->getTags(1);
    }

    public function productsAccessories($obj, $ae)
    {
        if ($accessories = $obj->getAccessories($ae->id_lang, false)) {
            foreach ($accessories as $value) {
                $accessories[] = $value['reference'];
            }

            return implode(',', $accessories);
        } else {
            return '';
        }
    }

    public function getProductCombination($obj, $ae)
    {
        $combArray = null;
        $groups = null;

        $combinaisons = $obj->getAttributeCombinations((int) ($ae->id_lang));
        if (is_array($combinaisons)) {
            $combinationImages = $obj->getCombinationImages((int) ($ae->id_lang));

            foreach ($combinaisons as $combinaison) {
                $combArray[$combinaison['id_product_attribute']]['wholesale_price'] = $combinaison['wholesale_price'];
                $combArray[$combinaison['id_product_attribute']]['price'] = $combinaison['price'];
                $combArray[$combinaison['id_product_attribute']]['weight'] = $combinaison['weight'];
                $combArray[$combinaison['id_product_attribute']]['unit_impact'] = $combinaison['unit_price_impact'];
                $combArray[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
                $combArray[$combinaison['id_product_attribute']]['supplier_reference'] = $combinaison['supplier_reference'];
                $combArray[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
                $combArray[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
                $combArray[$combinaison['id_product_attribute']]['minimal_quantity'] = $combinaison['minimal_quantity'];
                $combArray[$combinaison['id_product_attribute']]['location'] = $combinaison['location'];
                $combArray[$combinaison['id_product_attribute']]['quantity'] = $combinaison['quantity'];
                $combArray[$combinaison['id_product_attribute']]['id_image'] = isset($combinationImages[$combinaison['id_product_attribute']][0]['id_image']) ? $combinationImages[$combinaison['id_product_attribute']][0]['id_image'] : 0;
                $combArray[$combinaison['id_product_attribute']]['images'] = isset($combinationImages[$combinaison['id_product_attribute']]) ? $combinationImages[$combinaison['id_product_attribute']] : '';
                $combArray[$combinaison['id_product_attribute']]['default_on'] = $combinaison['default_on'];
                $combArray[$combinaison['id_product_attribute']]['ecotax'] = $combinaison['ecotax'];
                $combArray[$combinaison['id_product_attribute']]['id_product_attribute'] = $combinaison['id_product_attribute'];
                $combArray[$combinaison['id_product_attribute']]['attributes'][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute']);
                $combArray[$combinaison['id_product_attribute']]['attributes_name'][] = array($combinaison['group_name'], $combinaison['id_attribute_group']);
                $combArray[$combinaison['id_product_attribute']]['attributes_value'][] = array($combinaison['attribute_name'], $combinaison['id_attribute']);
                if ($combinaison['is_color_group']) {
                    $groups[$combinaison['id_attribute_group']] = $combinaison['group_name'];
                }
            }
        }

        return $combArray;
    }

    public function combinationSupplierNameAll($obj, $product_attribute, $ae)
    {
        $sups = $this->executeS('
            SELECT DISTINCT(s.`name`)
            FROM `'._DB_PREFIX_.'product_supplier` ps
            LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (ps.`id_supplier`= s.`id_supplier`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
            WHERE ps.`id_product` = '.$obj->id.' AND ps.id_product_attribute = '.(int) $product_attribute['id_product_attribute']);
        $suppliers = array();
        foreach ($sups as $sup) {
            $suppliers[] = $sup['name'];
        }

        return implode(',', $suppliers);
    }

    public function combinationIdSupplierAll($obj, $product_attribute)
    {
        $sups = $this->executeS('
			SELECT DISTINCT(ps.`id_supplier`)
			FROM `'._DB_PREFIX_.'product_supplier` ps
			JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
			WHERE ps.`id_product` = '.$obj->id.' AND ps.id_product_attribute = '.(int) $product_attribute['id_product_attribute']);

        $suppliers = array();
        if (is_array($suppliers)) {
            foreach ($sups as $sup) {
                $suppliers[] = $sup['id_supplier'];
            }
        }
        return (is_array($suppliers) ? implode(',', $suppliers) : '');
    }

    public function combinationWarehosue($obj, $products_attribute)
    {
        $warehouse = $this->executeS('
		SELECT `id_warehouse`
		FROM `'._DB_PREFIX_.'warehouse_product_location`
		WHERE `id_product` = '.$obj->id.' AND `id_product_attribute` = '.$products_attribute['id_product_attribute']);

        return $warehouse[0]['id_warehouse'];
    }

    public function combinationAttributes($obj, $products_attribute)
    {
        $name = null;
        foreach ($products_attribute['attributes'] as $attribute) {
            $name .= addslashes(htmlspecialchars($attribute[0])).': '.addslashes(htmlspecialchars($attribute[1])).' ';
        }

        return Tools::stripslashes($name);
    }

    public function combinationAttributesName($obj, $products_attribute)
    {
        $name = array();
        foreach ($products_attribute['attributes_name'] as $attribute) {
            $attributeGroup = new AttributeGroup($attribute[1]);
            $name[] = addslashes(htmlspecialchars($attribute[0])).':'.addslashes(htmlspecialchars($attributeGroup->group_type)).':'.
                addslashes(htmlspecialchars($attributeGroup->position));
        }

        return implode(',', $name);
    }

    public function combinationAttributesValue($obj, $products_attribute)
    {
        $value = array();
        foreach ($products_attribute['attributes_value'] as $attribute) {
            $attr = new Attribute($attribute[1]);
            $value[] = addslashes(htmlspecialchars($attribute[0])).':'.addslashes(htmlspecialchars($attr->position));
        }

        return implode(',', $value);
    }

    public function combinationDefaultCombination($obj, $product_attribute)
    {
        return $product_attribute['default_on'] ? 1 : 0;
    }

    public function combinationWholesalePrice($obj, $product_attribute)
    {
        return $product_attribute['wholesale_price'];
    }

    public function combinationPrice($obj, $product_attribute)
    {
        return Product::getPriceStatic((int) $obj->id, false, (int) $product_attribute['id_product_attribute']);
    }

    public function combinationWeight($obj, $product_attribute)
    {
        return $product_attribute['weight'];
    }

    public function combinationPriceTax($obj, $product_attribute)
    {
        return $obj->getPrice(true, (int) $product_attribute['id_product_attribute']);
    }

    public function combinationPriceTaxNodiscount($obj, $product_attribute)
    {
        return $obj->getPrice(true, (int) $product_attribute['id_product_attribute'], 2, null, false, false);
    }

    public function combinationUnitImpact($obj, $product_attribute)
    {
        return $product_attribute['unit_impact'];
    }

    public function combinationReference($obj, $product_attribute)
    {
        return $product_attribute['reference'];
    }

    public function combinationSupplierReference($obj, $product_attribute)
    {
        // build query
        $query = new DbQuery();
        $query->select('ps.product_supplier_reference');
        $query->from('product_supplier', 'ps');
        $query->where(
            'ps.id_product = '.(int) $obj->id.
            ' AND ps.id_product_attribute = '.(int) $product_attribute['id_product_attribute']
        );
        $suppliers = null;
        $result = null;
        $suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        foreach ($suppliers as $supplier) {
            if ($supplier['product_supplier_reference']) {
                $result[] = $supplier['product_supplier_reference'];
            }
        }

        return (is_array($result) ? implode(',', $result) : '');
    }

    public function combinationEan13($obj, $product_attribute)
    {
        return $product_attribute['ean13'];
    }

    public function combinationUpc($obj, $product_attribute)
    {
        return $product_attribute['upc'];
    }

    public function combinationMinimalQuantity($obj, $product_attribute)
    {
        return $product_attribute['minimal_quantity'];
    }

    public function combinationLocation($obj, $product_attribute)
    {
        return $product_attribute['location'];
    }

    public function combinationQuantity($obj, $product_attribute)
    {
        return $product_attribute['quantity'];
    }

    public function combinationEcotax($obj, $product_attribute)
    {
        return $product_attribute['ecotax'];
    }

    public function combinationImages($obj, $product_attribute, $ae)
    {
        $images = array();
        if (isset($product_attribute['images']) and is_array($product_attribute['images'])) {
            foreach ($product_attribute['images'] as $image) {
                $attrImage = ($image['id_image'] ? new Image($image['id_image']) : null);
                $images[] = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id . '-' . $attrImage->id, $ae->image_type);
            }
        }

        return (is_array($images) ? implode(',', $images) : '');
    }

    public function combinationImagePosition($obj, $product_attribute, $ae)
    {
        $images = array();
        if (isset($product_attribute['images']) and is_array($product_attribute['images'])) {
            foreach ($product_attribute['images'] as $image) {
                $attrImage = ($image['id_image'] ? new Image($image['id_image']) : null);
                if (is_object($attrImage)) {
                    $images[] = $attrImage->position;
                }
            }
        }

        return (is_array($images) ? implode(',', $images) : '');
    }

    public function combinationImageAlt($obj, $product_attribute, $ae)
    {
        $images = array();
        if (isset($product_attribute['images']) and is_array($product_attribute['images'])) {
            $ids = implode(', ', array_map(function ($entry) {
                return $entry['id_image'];
            }, $product_attribute['images']));
            $images = $this->executeS('SELECT legend
			FROM '._DB_PREFIX_.'image_lang
			WHERE id_image IN ('.$ids.')
			AND id_lang = '. $ae->id_lang);
        }

        return (is_array($images) ? implode(', ', array_map(function ($entry) {
            return $entry['legend'];
        }, $images)) : '');
    }

    public function combinationImage($obj, $product_attribute, $ae)
    {
        $attrImage = ($product_attribute['id_image'] ? new Image($product_attribute['id_image']) : null);
        if ($attrImage) {
            return 'http://'. $this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$attrImage->id, $ae->image_type);
        } else {
            return '';
        }
    }

    public function ordersFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Orders fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('orders')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Customers groups'),
                'name' => 'groups[]',
                'id' => 'groups',
                'class' => 'ds-select',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'multiple' => true,
                'options' => array(
                    'query' => Group::getGroups($this->getConfiguration('PS_LANG_DEFAULT')),
                    'id' => 'id_group',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Payments'),
                'name' => 'payments[]',
                'id' => 'payments',
                'class' => 'ds-select',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'multiple' => true,
                'options' => array(
                    'query' => PaymentModule::getInstalledPaymentModules(),
                    'id' => 'name',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Carrier type'),
                'name' => 'carriers[]',
                'id' => 'carriers',
                'class' => 'ds-select',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'multiple' => true,
                'options' => array(
                    'query' => Carrier::getCarriers($this->getConfiguration('PS_LANG_DEFAULT')),
                    'id' => 'id_carrier',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Order state'),
                'name' => 'state[]',
                'id' => 'state',
                'class' => 'ds-select',
                'desc' => $this->l('If you want all leave blank. All are exported by default.'),
                'multiple' => true,
                'options' => array(
                    'query' => OrderState::getOrderStates($this->getConfiguration('PS_LANG_DEFAULT')),
                    'id' => 'id_order_state',
                    'name' => 'name',
                ),
            ),
        );

        return $fields;
    }

    public function ordersQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT o.`id_order` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
                FROM '._DB_PREFIX_.'orders o
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON ( od.`id_order` = o.`id_order` )
                LEFT JOIN `'._DB_PREFIX_.'shop` sh ON ( o.`id_shop` = sh.`id_shop` )
                LEFT JOIN `'._DB_PREFIX_.'customer` cu ON ( o.`id_customer` = cu.`id_customer` )
                LEFT JOIN `'._DB_PREFIX_.'gender_lang` gl ON ( cu.`id_gender` = gl.`id_gender` AND gl.`id_lang` = '.$ae->id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'gender_lang` inv_gl ON ( cu.`id_gender` = inv_gl.`id_gender` AND inv_gl.`id_lang` = '.$ae->id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'address` a ON ( a.`id_address` = o.`id_address_delivery` )
                LEFT JOIN `'._DB_PREFIX_.'address` inv_a ON ( inv_a.`id_address` = o.`id_address_invoice` )
                LEFT JOIN `'._DB_PREFIX_.'state` s ON ( s.`id_state` = a.`id_state` )
                LEFT JOIN `'._DB_PREFIX_.'country` co ON ( co.`id_country` = a.`id_country` )
                LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON ( cl.`id_country` = co.`id_country` AND cl.`id_lang`= '.$ae->id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON ( ca.`id_carrier` = o.`id_carrier` )
                LEFT JOIN `'._DB_PREFIX_.'order_payment` op ON ( op.`order_reference` = o.`reference` )
                LEFT JOIN `'._DB_PREFIX_.'message` m ON ( m.`id_order` = o.`id_order` )
                LEFT JOIN `'._DB_PREFIX_.'currency` cur ON ( o.`id_currency` = cur.`id_currency` )
                LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` odt ON ( od.`id_order_detail` = odt.`id_order_detail` )
                LEFT JOIN `'._DB_PREFIX_.'tax` t ON ( odt.`id_tax` = t.`id_tax` )
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON ( o.`current_state` = osl.`id_order_state` AND osl.`id_lang` = '.$ae->id_lang.')
                WHERE 1'.
                (isset($ae->only_new) && $ae->only_new ? ' AND o.`id_order` > '.$ae->last_exported_id : '').
                ($ae->only_new == false && $ae->start_id ? ' AND o.`id_order` >= '.$ae->start_id : '').
                ($ae->only_new == false && $ae->end_id ? ' AND o.`id_order` <= '.$ae->end_id : '').
                (isset($sorted_fields['groups[]']) && $sorted_fields['groups[]'] ? ' AND cu.`id_default_group` IN ('.implode(', ', $sorted_fields['groups[]']).')' : '').
                (isset($sorted_fields['payments[]']) && $sorted_fields['payments[]'] ? ' AND o.`module` IN ("'.implode('", "', $sorted_fields['payments[]']).'")' : '').
                (isset($sorted_fields['carriers[]']) && $sorted_fields['carriers[]'] ? ' AND o.`id_carrier` IN ('.implode(', ', $sorted_fields['carriers[]']).')' : '').
                (isset($sorted_fields['state[]']) && $sorted_fields['state[]'] ? ' AND o.`current_state` IN ('.implode(', ', $sorted_fields['state[]']).')' : '').
                (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND o.`date_add` >= "'.($ae->date_from).'"' : '').
                (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND o.`date_add` <= "'.($ae->date_to).'"' : '')
                             .Shop::addSqlRestriction(false, 'o').
                ' GROUP BY '.(isset($sorted_fields['order_detail']) && $sorted_fields['order_detail'] ? 'od.`id_order_detail`' : 'o.`id_order`');

        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function ordersCode($obj)
    {
        $result = $obj->getCartRules();
        $codes = array();
        foreach ($result as $res) {
            $codes[] = $res['name'];
        }

        return implode(',', $codes);
    }

    public function ordersEmployeeName($obj)
    {
        $employee = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `firstname`, `lastname`
		FROM `'._DB_PREFIX_.'employee` e
		LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON ( oh.`id_employee` = e.`id_employee` )
		WHERE `id_order` = '.(int) $obj->id.'
		ORDER BY `date_add` DESC, `id_order_history` DESC LIMIT 1');

        return (isset($employee[0]) ? $employee[0]['firstname'].' '.$employee[0]['lastname'] : '');
    }

    public function ordersCustomization($obj, $ae, $element)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT cud.`value`, cu.`quantity`
				FROM `'._DB_PREFIX_.'customization` cu
				INNER JOIN `'._DB_PREFIX_.'customized_data` cud ON (cud.`id_customization` = cu.`id_customization`)
				WHERE cu.`id_product` = '.(int) ($element['product_id']).' AND cu.`id_product_attribute` = '.(int) ($element['product_attribute_id']).'  AND cu.`id_cart` = '.(int) ($element['id_cart']));
        $cud = array();
        foreach ($result as $res) {
            $cud[] = 'value:'.$res['value'].' '.'quantity:'.$res['quantity'];
        }

        return implode(',', $cud);
    }

    public function categoriesQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT c.`id_category` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
            FROM `'._DB_PREFIX_.'category` c
			'.Shop::addSqlAssociation('category', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
			WHERE 1'.($ae->id_lang ? ' AND `id_lang` = '.(int) $ae->id_lang : '').
            (isset($ae->only_new) && $ae->only_new ? ' AND c.`id_category` > '.$ae->last_exported_id : '').
            ($ae->only_new == false && $ae->start_id ? ' AND c.`id_category` >= '.$ae->start_id : '').
            ($ae->only_new == false && $ae->end_id ? ' AND c.`id_category` <= '.$ae->end_id : '').
            (isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND c.`active` = 1' : '').
            (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND c.`date_add` >= "'.($ae->date_from).'"' : '').
            (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND c.`date_add` <= "'.($ae->date_to).'"' : '').'
			GROUP BY c.id_category
			ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';
        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function categoriesIdGroup($obj)
    {
        $result = $this->executeS('
			SELECT cg.`id_group`
			FROM '._DB_PREFIX_.'category_group cg
			WHERE cg.`id_category` = '.(int) $obj->id);
        $groups = null;
        foreach ($result as $group) {
            $groups = $group['id_group'];
        }

        return $groups;
    }

    public function categoriesImage($obj, $ae)
    {
        $imageLink = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$obj->id_image, $ae->image_type);

        return $imageLink;
    }

    public function categoriesFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Orders fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'sminit, ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('categories')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'name' => 'active',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function manufacturersQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT m.`id_manufacturer` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
		FROM `'._DB_PREFIX_.'manufacturer` m
		'.Shop::addSqlAssociation('manufacturer', 'm').'
		INNER JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int) $ae->id_lang.')'.'
            WHERE 1'.(isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND m.`active` = 1' : '').
            (isset($ae->only_new) && $ae->only_new ? ' AND m.`id_manufacturer` > '.$ae->last_exported_id : '').
            ($ae->only_new == false && $ae->start_id ? ' AND m.`id_manufacturer` >= '.$ae->start_id : '').
            ($ae->only_new == false && $ae->end_id ? ' AND m.`id_manufacturer` <= '.$ae->end_id : '').
            (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND m.`date_add` >= "'.($ae->date_from).'"' : '').
            (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND m.`date_add` <= "'.($ae->date_to).'"' : '');
        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function manufacturersImage($obj, $ae)
    {
        $imageLink = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$obj->id_image, $ae->image_type);

        return $imageLink;
    }

    public function manufacturersFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Manufacturers fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('manufacturers')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'name' => 'active',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function suppliersQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT s.`id_supplier` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
		FROM `'._DB_PREFIX_.'supplier` s
		'.Shop::addSqlAssociation('supplier', 's').'
		INNER JOIN `'._DB_PREFIX_.'supplier_lang` sl ON (s.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int) $ae->id_lang.')'.'
            WHERE 1'.(isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND s.`active` = 1' : '').
            (isset($ae->only_new) && $ae->only_new ? ' AND s.`id_supplier` > '.$ae->last_exported_id : '').
            ($ae->only_new == false && $ae->start_id ? ' AND s.`id_supplier` >= '.$ae->start_id : '').
            ($ae->only_new == false && $ae->end_id ? ' AND s.`id_supplier` <= '.$ae->end_id : '').
            (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND s.`date_add` >= "'.($ae->date_from).'"' : '').
            (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND s.`date_add` <= "'.($ae->date_to).'"' : '');
        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function suppliersImage($obj, $ae)
    {
        $imageLink = 'http://'.$this->link->getImageLink($obj->link_rewrite[$ae->id_lang], $obj->id.'-'.$obj->id_image, $ae->image_type);

        return $imageLink;
    }

    public function suppliersFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Orders fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'sminit, ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('suppliers')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'name' => 'active',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function customersFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Customers fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'sminit, ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('customers')),
                    'id' => 'field',
                    'name' => 'name',
                ),

            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'name' => 'active',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function customersQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT c.`id_customer` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
				FROM '._DB_PREFIX_.'customer c
                LEFT JOIN `'._DB_PREFIX_.'address` a ON ( a.`id_customer` = c.`id_customer` )
                LEFT JOIN `'._DB_PREFIX_.'state` s ON ( a.`id_state` = s.`id_state` )
                LEFT JOIN `'._DB_PREFIX_.'country_lang` co ON ( co.`id_country` = a.`id_country` AND co.`id_lang` = '.$ae->id_lang.')
				WHERE 1'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            (isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND c.`active` = 1' : '').
            (isset($ae->only_new) && $ae->only_new ? ' AND c.`id_customer` > '.$ae->last_exported_id : '').
            ($ae->only_new == false && $ae->start_id ? ' AND c.`id_customer` >= '.$ae->start_id : '').
            ($ae->only_new == false && $ae->end_id ? ' AND c.`id_customer` <= '.$ae->end_id : '').
            (isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND c.`active` = 1' : '').
            (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND c.`date_add` >= "'.($ae->date_from).'"' : '').
            (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND c.`date_add` <= "'.($ae->date_to).'"' : '');
        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function customersGroups($obj, $ae)
    {
        $groupsIds = Customer::getGroupsStatic((int) $obj->id);
        $groupeNames = array();
        if (is_array($groupsIds)) {
            foreach ($groupsIds as $id) {
                $group = new Group($id);
                $groupeNames[] = $group->name[$ae->id_lang];
            }
            return implode(',', $groupeNames);
        } else {
            return '';
        }
    }

    public function newslettersQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT n.`id` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
				FROM '._DB_PREFIX_.'newsletter as n
				WHERE 1'.(isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND n.`active` = 1' : '').
                (isset($ae->only_new) && $ae->only_new ? ' AND n.`id` > '.$ae->last_exported_id : '').
                ($ae->only_new == false && $ae->start_id ? ' AND n.`id` >= '.$ae->start_id : '').
                ($ae->only_new == false && $ae->end_id ? ' AND n.`id` <= '.$ae->end_id : '').
                (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND n.`newsletter_date_add` >= "'.($ae->date_from).'"' : '').
                (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND n.`newsletter_date_add` <= "'.($ae->date_to).'"' : '').'
				AND n.`id_shop` = '.$this->context->shop->id;
        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function newslettersFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Newsletters fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'sminit, ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('newsletters')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'class' => 't',
                'name' => 'active',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function addressesQuery($ae, $sorted_fields)
    {
        $sql = 'SELECT a.`id_address` '.(empty($sorted_fields['sqlfields']) ? '' : ', '.implode(', ', $sorted_fields['sqlfields'])).'
				FROM '._DB_PREFIX_.'address as a
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON ( a.`id_manufacturer` = m.`id_manufacturer` )
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON ( a.`id_supplier` = s.`id_supplier` )
				LEFT JOIN `'._DB_PREFIX_.'state` st ON ( a.`id_state` = st.`id_state`)
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON ( a.`id_country` = cl.`id_country` AND cl.`id_lang` = '.$ae->id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'customer` cu ON ( a.`id_customer` = cu.`id_customer`)
				WHERE 1'.(isset($sorted_fields['active']) && $sorted_fields['active'] ? ' AND a.`active` = 1' : '').
                (isset($ae->only_new) && $ae->only_new ? ' AND a.`id` > '.$ae->last_exported_id : '').
                ($ae->only_new == false && $ae->start_id ? ' AND a.`id` >= '.$ae->start_id : '').
                ($ae->only_new == false && $ae->end_id ? ' AND a.`id` <= '.$ae->end_id : '').
                (isset($ae->date_from) && $ae->date_from && !$ae->only_new ? ' AND a.`date_add` >= "'.($ae->date_from).'"' : '').
                (isset($ae->date_to) && $ae->date_to && !$ae->only_new ? ' AND a.`date_add` <= "'.($ae->date_to).'"' : '');

        $result = $this->query($sql);
        $this->rowsNumber = $this->query('SELECT FOUND_ROWS()')->fetchColumn();

        return $result;
    }

    public function addressesFormFields()
    {
        $fields = array(
            array(
                'type' => 'bsmselect',
                'label' => $this->l('Addresses fields'),
                'name' => 'fields[]',
                'id' => 'fields',
                'class' => 'sminit, ds-select',
                'multiple' => true,
                'options' => array(
                    'query' => $this->groupFields(AdvancedExportFieldClass::getAllFields('addresses')),
                    'id' => 'field',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $this->switch,
                'label' => $this->l('Only active'),
                'class' => 't',
                'name' => 'active',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ),
                ),
            ),
        );

        return $fields;
    }

    public function getLanguages()
    {
        return Language::getLanguages();
    }

    /**
     * Add key to array element as id
     * for select control.
     *
     * @param array $array
     *
     * @return array $array
     */
    public function addKeyAsArrayElementId($array)
    {
        for ($i = 0; $i < count($array); ++$i) {
            $array[$i]['id'] = $i;
        }

        return $array;
    }

    /**
     * @param $object
     * @param $ae
     * @param $value
     *
     * @return mixed
     */
    public function processDecimalSettings($object, $ae, $value)
    {
        if ($this->isDecimal($object[$value])) {
            //this have to be first becasue if we change separator
            //the value is not recognise
            if ($ae->decimal_round != -1 && $ae->decimal_round != '-1') {
                $object[$value] = Tools::ps_round((float) $object[$value], $ae->decimal_round);
            }

            if ($ae->decimal_separator != -1 && $ae->decimal_separator != '-1') {
                $object[$value] = str_replace(',', $ae->decimal_separator, $object[$value]);
                $object[$value] = str_replace('.', $ae->decimal_separator, $object[$value]);
            }
        }
        if ($ae->strip_tags) {
            $object[$value] = strip_tags($object[$value]);
        }

        return $object;
    }

    public function isDecimal($val)
    {
        return is_numeric($val) && strpos($val, '.') !== false;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getLinks($type)
    {
        $links = $this->dbExecuteS('select * from '._DB_PREFIX_."advancedexport where type = '$type'");
        for ($i = 0; $i < count($links); ++$i) {
            $url = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.
                'modules/advancedexport/cron.php?secure_key='.
                Configuration::get('ADVANCEDEXPORT_SECURE_KEY').
                '&id='.$links[$i]['id_advancedexport'];
            $links[$i]['cron_url'] = $url;
            $type = $this->getSaveTypes();
            $links[$i]['save_type'] = $type[$links[$i]['save_type']]['short_name'];
        }

        return $links;
    }

    public function getAllLinks()
    {
        $links = $this->dbExecuteS('select * from '._DB_PREFIX_.'advancedexport');

        return $links;
    }

    /**
     * @param $ae
     * @param $sorted_fields
     * @param $file
     */
    public function filewrite($ae, $sorted_fields, $file)
    {
        fwrite($file, implode($ae->delimiter, $sorted_fields['labels'])."\r\n");
    }

    /**
     * @throws PrestaShopException
     */
    public function addFieldsToTable()
    {
        foreach ($this->export_types as $tab) {
            foreach ($this->$tab as $item) {
                $field = new AdvancedExportFieldClass();
                $field->tab = $tab;
                $field->name = $item['name'];
                $field->field = $item['field'];
                $field->table = $item['database'];
                $field->alias = (isset($item['alias']) ? $item['alias'] : '');
                $field->as = (isset($item['as']) ? $item['as'] : false);
                $field->attribute = (isset($item['attribute']) ? $item['attribute'] : false);
                $field->import = (isset($item['import']) ? $item['import'] : false);
                $field->import_name = (isset($item['import_name']) ? $item['import_name'] : '');
                $field->import_combination = (isset($item['import_combination']) ? $item['import_combination'] : false);
                $field->import_combination_name = (isset($item['import_combination_name']) ? $item['import_combination_name'] : '');

                $field->add();
            }
        }
    }

    private function generateDefaultCsvForImport($type)
    {
        if ($type == 'products') {
            $this->generateCombination($type);
        }

        $this->generateDefaultCsvByType($type);
    }

    /**
     * @return string
     */
    private function displayTutorialForm()
    {
        $html = '<div class="panel col-lg-12">';
        $html .= '<div class="panel-heading">Tutorials</div>';
        $html .= '<a class="clear" target="_blank" href="https://www.youtube.com/watch?v=fLraOP7XLdI">';
        $html .= '<b>1. Create export model</b>';
        $html .= '</a>';
        $html .= '<a class="clear" target="_blank" href="https://www.youtube.com/watch?v=fLraOP7XLdI">';
        $html .= '<b>2. Setup cron job</b>';
        $html .= '</a>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    private function displaySupportForm()
    {
        $html = '<div class="panel col-lg-12">';
        $html .= '<div class="panel-heading">Support</div>';
        $html .= 'Please feel free to contact us if you require any further information or help.';
        $html .= '</br>Please use PrestaShop addons contact form';
        $html .= '<a class="clear" href="https://addons.prestashop.com/contact-form.php?id_product=6927">';
        $html .= '<b>https://addons.prestashop.com/contact-form.php?id_product=6927</b></a>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param $type
     *
     * @throws PrestaShopException
     *
     * @internal param $combination_fields
     */
    private function generateCombination($type)
    {
        $combination_fields = AdvancedExportFieldClass::getDefaultCombinationImportFields($type);

        $ae = new AdvancedExportClass();
        $ae->delimiter = ',';
        $ae->separator = '"';
        $ae->add_header = true;
        $ae->id_lang = Configuration::get('PS_LANG_DEFAULT');
        $ae->charset = 'UTF-8';
        $ae->decimal_round = -1;
        $ae->decimal_separator = -1;
        $ae->strip_tags = 0;
        $ae->only_new = 0;
        $ae->last_exported_id = 0;
        $ae->start_id = 0;
        $ae->end_id = 0;
        $ae->type = 'products';
        $ae->name = 'combination_import';
        $ae->filename = 'combination_import';
        $ae->fields = Tools::jsonEncode(
            array(
                'fields[]' => $combination_fields,
                'attributes' => 1,
            )
        );
        $ae->add();
    }

    /**
     * @param $type
     *
     * @throws PrestaShopException
     */
    private function generateDefaultCsvByType($type)
    {
        $fields = AdvancedExportFieldClass::getDefaultImportFields($type);

        $ae = new AdvancedExportClass();
        $ae->delimiter = ',';
        $ae->separator = '"';
        $ae->add_header = true;
        $ae->id_lang = Configuration::get('PS_LANG_DEFAULT');
        $ae->charset = 'UTF-8';
        $ae->decimal_round = -1;
        $ae->decimal_separator = -1;
        $ae->strip_tags = 0;
        $ae->only_new = 0;
        $ae->last_exported_id = 0;
        $ae->start_id = 0;
        $ae->end_id = 0;
        $ae->type = $type;
        $ae->name = $type.'_import';
        $ae->filename = $type.'_import';
        $ae->fields = Tools::jsonEncode(array('fields[]' => $fields));
        $ae->add();
    }

    /**
     * @return bool
     */
    public function createFieldTable()
    {
        $table_name = _DB_PREFIX_.'advancedexportfield';

        $query = 'CREATE TABLE IF NOT EXISTS `'.$table_name.'` (
			`id_advancedexportfield` int(10) unsigned NOT NULL auto_increment,
			`tab` varchar(255) NOT NULL,
			`name` varchar(255) NOT NULL,
			`field` varchar(255) NOT NULL,
			`table` varchar(255) NOT NULL,
			`alias` varchar(255) NOT NULL,
			`as` varchar(255) NOT NULL,
			`attribute` BOOL NOT NULL DEFAULT 0,
			`return` varchar(255) NOT NULL,
			`import` BOOL NOT NULL DEFAULT 0,
			`import_name` varchar(255) NOT NULL,
			`import_combination` BOOL NOT NULL DEFAULT 0,
			`import_combination_name` varchar(255) NOT NULL,
			`isCustom` BOOL NOT NULL DEFAULT 0,
			PRIMARY KEY  (`id_advancedexportfield`)
			) ENGINE=' ._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        if (!$this->dbExecute($query)) {
            return false;
        }

        $this->addFieldsToTable();

        return true;
    }
}
