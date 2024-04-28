<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class kh_producthoverGetImagesModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $method = Tools::getValue('method');
        if($method === 'getSelected') $ids = Tools::getValue('ids');
        // Call the appropriate method based on the 'method' parameter
        if (!empty($method) && method_exists($this, $method)) {
            $reflection = new ReflectionMethod($this, $method);
            if ($reflection->isPublic()) {
                if($ids){
                    $this->{$method}($ids);
                }
            }else{
                throw new PrestaShopException("The called method is not public.", 500);
            }
        } else {
            parent::initContent();
        }
    }
    
    public function getSelected($productIds) {
        $id_lang = (int) $this->context->language->id;
        $in = 0;
        $productImagesIds = [];
        foreach (explode(",", $productIds) as $id) {
            if(is_numeric($id) && (int)$id >= 0)
            {
                $images = Image::getImages($id_lang, $id);
                if($images) {
                    $productImagesIds[$in]['product_id'] = (int)$id;
                    foreach ($images as $img){
                        if($img['cover'] !== null) {
                            $productImagesIds[$in]['cover_id'] = $img['id_image'];
                        }else{
                            $productImagesIds[$in]['id_image'][] = $img['id_image'];
                        }
                    }
                    $in++;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($productImagesIds);
        exit;
    }
}
