<?php

class Link extends LinkCore
{
    public function getImageLink($name, $ids, $type = null)
    {
        if (Configuration::get('ALLEGRO_LEGACY_IMAGES') 
            && Configuration::get('PS_LEGACY_IMAGES') 
            && strpos($ids, '-') === false) {
            $image = new Image((int)$ids);
            $ids = $image->id_product.'-'.$ids;
        }

        return parent::getImageLink($name, $ids, $type);
    }
}