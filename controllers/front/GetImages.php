<?php
/**
 *  @author    sHKamil - Kamil Hałasa
 *  @copyright sHKamil - Kamil Hałasa
 *   @license   GPL
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
        $productImagesIds = [];
        foreach (explode(",", $productIds) as $id) {
            if(is_numeric($id) && (int)$id >= 0)
            {
                $images = Image::getImages($id_lang, $id);
                foreach ($images as $img){
                    if($img['cover'] !== null) {
                        $productImagesIds[$id]['cover_id'] = $img['id_image'];
                    }else{
                        $productImagesIds[$id]['id_image'][] = $img['id_image'];
                    }
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($productImagesIds);
        exit;
    }
}
