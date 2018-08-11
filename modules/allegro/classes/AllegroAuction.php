<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroAuction extends ObjectModel
{
    public $id_allegro_auction;
    public $id_auction;
    public $id_allegro_product;
    public $id_allegro_account;
    public $id_shop;
    public $duration;
    public $date_start;
    public $is_standard;
    public $title;
    public $quantity;
    public $price;
    public $cost_info;
    public $status = self::STATUS_NEW;
    public $date_add;
    public $date_upd;

    const STATUS_NEW = 1;
    const STATUS_UPDATED = 2;
    const STATUS_FINISHED = 3;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'allegro_auction',
        'primary' => 'id_allegro_auction',
        'multilang_shop' => false,
        'fields' => array(
            'id_auction'            => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'id_allegro_product'    => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_allegro_account'    => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_shop'               => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'title'                 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'duration'              => array('type' => self::TYPE_INT, 'validate' => 'isInt'/*, 'required' => true*/),
            'date_start'            => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'is_standard'           => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'quantity'              => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'price'                 => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'cost_info'             => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'status'                => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'date_add'              => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'              => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function getByAuctionID($id_auction)
    {
        $id = (int)Db::getInstance()->GetValue('SELECT `id_allegro_auction` FROM `'._DB_PREFIX_.'allegro_auction` WHERE `id_auction` = '.(float)$id_auction);

        if ($id) {
            return new AllegroAuction($id);
        }
    }

    public static function getAuctionsByAllegroProductId($id_allegro_account, $id_allegro_product, $status = null)
    {
        return Db::getInstance()->executeS('SELECT *
            FROM `'._DB_PREFIX_.'allegro_auction`
            WHERE `id_allegro_product` = '.(int)$id_allegro_product.'
            AND `id_allegro_account` = '.(int)$id_allegro_account.
            ($status ? ' AND `status` = '.(int)$status : '').'
            ORDER BY `id_auction`
        ');
    }

    public static function getAuctionUrl($idOffer, $future = false, $sandbox = false) {
        return 'http://allegro.pl'.
            ($sandbox ? '.webapisandbox.pl' : '').'/show_item.php?'.
            ($future ? 'future_item_id=' : 'item=').(float)$idOffer;
    }

    public static function getAuctionsById($auctionsIds = array())
    {
        $context = Context::getContext();

        if (empty($auctionsIds)) {
            return array();
        }

        // Subiekt import bug fix
        $groups = array(0);
        if (!Shop::isFeatureActive()) {
            $groups[] = 1;
        }

        $sql = 'SELECT DISTINCT aa.*, ap.*, p.`reference`, sa.`quantity` AS stock_quantity, p.`active`,
        aa.`id_shop`, s.`name` AS shop_name, aac.`sandbox`
        FROM `'._DB_PREFIX_.'allegro_auction` aa
        JOIN `'._DB_PREFIX_.'allegro_product` ap ON aa.`id_allegro_product` = ap.`id_allegro_product`
        JOIN `'._DB_PREFIX_.'allegro_account` aac ON aac.`id_allegro_account` = aa.`id_allegro_account`
        JOIN `'._DB_PREFIX_.'product` p ON ap.`id_product` = p.`id_product`
        JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop` = aa.`id_shop`
        JOIN `'._DB_PREFIX_.'shop_group` sg ON sg.`id_shop_group` = s.`id_shop_group`
        LEFT JOIN `'._DB_PREFIX_.'allegro_product_shop` aps ON (
            aps.`id_allegro_product` = aa.`id_allegro_product` AND
            aps.`id_shop` = aa.`id_shop`
        )
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (ap.`id_product` = sa.`id_product`
            AND ap.`id_product_attribute` = sa.`id_product_attribute` ';

        // Set shop or group by "share_stock" param
        $sql .= 'AND ((sg.`share_stock` = 1 AND sa.`id_shop` = 0 AND sa.`id_shop_group` = s.`id_shop_group`) OR
                    (sg.`share_stock` = 0 AND sa.`id_shop` = s.`id_shop` AND sa.`id_shop_group` IN ('.implode(',', $groups).')))) ';

        // Where
        $sql .= '
        WHERE aa.`id_auction` IN ('.implode(',', $auctionsIds).') ';

        // Handle shop context
        if ($context) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= 'AND aa.`id_shop` = '.(int)$context->shop->id.' ';
            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $sql .= ' AND aa.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')';
            }
        }

        $auctions = Db::getInstance()->executeS($sql);

        // Assign auction URL
        foreach ($auctions as $key => $auction) {
            $auctions[$key]['auction_url'] = AllegroAuction::getAuctionUrl(
                (float)$auction['id_auction'], 
                (bool)(strtotime($auction['date_start']) > time()), 
                (bool)$auction['sandbox']
            );
        }

        return $auctions;
    }
}