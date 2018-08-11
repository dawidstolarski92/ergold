<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
//include_once(_PS_MODULE_DIR_.'pk_compare/classes/CompareProduct.php');
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Pk_CompareCompareModuleFrontController extends ModuleFrontController
{
    //public $php_self = 'compare';
    public $max_to_compare = 4;

    /**
     * Display ajax content (this function is called instead of classic display, in ajax mode)
     */
    public function displayAjax() {
        
        // Add or remove product with Ajax
        if (Tools::getValue('ajax') && Tools::getValue('id_product') && Tools::getValue('action')) {
            if (Tools::getValue('action') == 'add') {
                $id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: false;
                if (CompareProduct::getNumberProducts($id_compare) < $this->max_to_compare) {
                    CompareProduct::addCompareProduct($id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } elseif (Tools::getValue('action') == 'remove') {
                if (isset($this->context->cookie->id_compare)) {
                    CompareProduct::removeCompareProduct((int)$this->context->cookie->id_compare, (int)Tools::getValue('id_product'));
                } else {
                    $this->ajaxDie('0');
                }
            } else {
                $this->ajaxDie('0');
            }
            $this->ajaxDie('1');
        }
        $this->ajaxDie('0');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Tools::getValue('ajax')) {
            return;
        }

        parent::initContent();

        CompareProduct::cleanCompareProducts();

        $hasProduct = false;

        if (!$this->max_to_compare) {
            return Tools::redirect('index.php?controller=404');
        }

        $ids = null;
        if (($product_list = urldecode(Tools::getValue('compare_product_list'))) && ($postProducts = (isset($product_list) ? rtrim($product_list, '|') : ''))) {
            $ids = array_unique(explode('|', $postProducts));
        } elseif (isset($this->context->cookie->id_compare)) {
            $ids = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
            if (count($ids)) {
                $link = $this->context->link->getModuleLink('pk_compare', 'compare', array('compare_product_list' => $ids));
                //Tools::redirect($link);
            }
        }
        
        //Clean compare product table

        if ($ids) {
            if (count($ids) > 0) {
                if (count($ids) > $this->max_to_compare) {
                    $ids = array_slice($ids, 0, $this->max_to_compare);
                }

                $listProducts = array();
                $listFeatures = array();

                foreach ($ids as $k => &$id) {

                    $listProducts[] = $this->getProduct($id);

                }

                foreach ($ids as $k => &$id) {

                    $curProduct = new Product((int)$id, true, $this->context->language->id);
                    if (!Validate::isLoadedObject($curProduct) || !$curProduct->active || !$curProduct->isAssociatedToShop()) {
                        if (isset($this->context->cookie->id_compare)) {
                            CompareProduct::removeCompareProduct($this->context->cookie->id_compare, $id);
                        }
                        unset($ids[$k]);
                        continue;
                    }

                    foreach ($curProduct->getFrontFeatures($this->context->language->id) as $feature) {
                        $listFeatures[$curProduct->id][$feature['id_feature']] = $feature['value'];
                    }

                }

                if (count($listProducts) > 0) {

                    $hasProduct = true;
                    $ordered_features = $this->getFeaturesForComparison($ids, $this->context->language->id);
                    $this->context->smarty->assign(array(
                        'ordered_features' => $ordered_features,
                        'product_features' => $listFeatures,
                        'products' => $listProducts,
                        'ids' => $ids
                    ));

                } elseif (isset($this->context->cookie->id_compare)) {

                    $object = new CompareProduct((int)$this->context->cookie->id_compare);
                    if (Validate::isLoadedObject($object)) {
                        $object->delete();
                    }

                }
            }
        }

        $this->context->smarty->assign('hasProduct', $hasProduct);

        $this->setTemplate('module:pk_compare/views/templates/front/compare.tpl');
        
    }

    public function getProduct($product_id) {

        $product = array();
        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
        
        $product_obj = new Product($product_id, false, $this->context->language->id);
        $product = (array)$product_obj;
        $product['id_product'] = $product_id;
        $product['quantity_wanted'] = 1;
        $product = $presenter->present($presentationSettings, $assembler->assembleProduct($product), $this->context->language);

        return $product;
    }

    public function getFeaturesForComparison($list_ids_product, $id_lang) {

        $ids = '';
        foreach ($list_ids_product as $id) {
            $ids .= (int)$id.',';
        }

        $ids = rtrim($ids, ',');

        if (empty($ids)) {
            return false;
        }

        return Db::getInstance()->executeS('
            SELECT f.*, fl.*
            FROM `'._DB_PREFIX_.'feature` f
            LEFT JOIN `'._DB_PREFIX_.'feature_product` fp
                ON f.`id_feature` = fp.`id_feature`
            LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
                ON f.`id_feature` = fl.`id_feature`
            WHERE fp.`id_product` IN ('.$ids.')
            AND `id_lang` = '.(int)$id_lang.'
            GROUP BY f.`id_feature`
            ORDER BY f.`position` ASC
        ');
    }

}
