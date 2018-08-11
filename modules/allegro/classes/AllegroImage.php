<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroImage extends ObjectModel
{
	const IMG_SHOP 	= 'shop';
	const IMG_MOD	= 'allegro';

	public $id_allegro_image;
	public $id_allegro_product;

	public $image_format = 'jpg';

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'allegro_image',
		'primary' => 'id_allegro_image',
		'multilang' => false,
		'fields' => array(
			'id_allegro_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
		),
	);

	public function delete()
	{
		if (!parent::delete()) {
			return false;
		}

		if (!$this->deleteImage()) {
			return false;
		}

		return true;
	}

	/**
	 * @param  boolean
	 * @return [type]
	 */
	public function deleteImage($force_delete = false)
	{
		if (!$this->id) {
			return false;
		}

		// Delete base image
		if (file_exists(_ALLEGRO_IMG_DIR_.$this->id.'.'.$this->image_format)) {
			unlink(_ALLEGRO_IMG_DIR_.$this->id.'.'.$this->image_format);
		} else {
			return false;
		}

        foreach (ImageType::getImagesTypes('products') as $image_type) {
            unlink(_ALLEGRO_IMG_DIR_.$this->id.'-'.$image_type['name'].'.'.$this->image_format);
        }

        return true;
	}

    public static function getAllegroImageLink($name, $id, $type = null)
    {
        $uri_path = __PS_BASE_URI__.'/modules/allegro/img/product/'.$id.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';

        return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }
}
