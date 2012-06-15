<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Ajaxcartpro_Model_Observer{

	public function addToCartEvent($observer){
        $request = Mage::app()->getFrontController()->getRequest();
        if ( !$request->getParam('in_cart') && !$request->getParam('is_checkout')
            && $request->getParam('awacp') )
		{
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            Mage::getModel('ajaxcartpro/response')
                ->setCart(Mage::helper('ajaxcartpro')->renderCart())
                ->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle())
                ->setProductName($observer->getProduct()->getName())
                ->send();
		}

		if ( $request->getParam('is_checkout')	)
		{
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            Mage::getModel('ajaxcartpro/response')
                ->setCart(Mage::helper('ajaxcartpro')->renderBigCart())
                ->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle())
                ->setProductName($observer->getProduct()->getName())
                ->send();
		}

	}
    public function addCustomOptions($observer)
    {
        $params = $observer->getControllerAction()->getRequest()->getParams();
        if (!isset($params['options']) || $params['options'] != 'cart' || !isset($params['ajaxcartpro'])) return;

        $product = Mage::registry('current_product');
        /* If product type is not simple, configurable or downloadable -- return false (will move to product page) */
        if (!$product->isConfigurable() && $product->getTypeId() != 'simple' &&
            $product->getTypeId() != 'downloadable') {echo 'false'; die;}
        /* If product have custom option of file type -- return false (will move to product page) */
        if (Mage::helper('ajaxcartpro')->hasFileOption()) {echo 'false'; die;}
        $block = Mage::getSingleton('core/layout');
        $options = $block->createBlock('catalog/product_view_options', 'product_options')
                            ->setTemplate('catalog/product/view/options.phtml')
                            ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                            ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
                            ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml');
        $price = $block->createBlock('catalog/product_view', 'product_price')
                            ->setTemplate('catalog/product/view/price_clone.phtml');
        $js = $block->createBlock('core/template', 'product_js')
                            ->setTemplate('catalog/product/view/options/js.phtml');

        if ($product->isConfigurable())
        {
            $configurable = $block->createBlock('catalog/product_view_type_configurable', 'product_configurable_options')
                            ->setTemplate('ajaxcartpro/options/configurable.phtml');
            $configurableData = $block->createBlock('catalog/product_view_type_configurable', 'product_type_data')
                            ->setTemplate('catalog/product/view/type/configurable.phtml');
        }
        if ($product->getTypeId() == 'downloadable')
        {
            $downloadable = $block->createBlock('downloadable/catalog_product_links', 'product_downloadable_options')
                            ->setTemplate('ajaxcartpro/options/downloadable.phtml');
            $downloadableData = $block->createBlock('downloadable/catalog_product_view_type', 'product_type_data')
                            ->setTemplate('downloadable/catalog/product/type.phtml');
        }
        $main = $block->createBlock('catalog/product_view')
                        ->setTemplate('ajaxcartpro/options.phtml')
                        ->append($options);
        
        if ($product->isConfigurable()) 
        {
            $main->append($configurableData);
            $main->append($configurable);
        }
        if ($product->getTypeId() == 'downloadable')
        {
            $main->append($downloadableData);
            $main->append($downloadable);
        }
        
        $main->append($js)->append($price);

        $observer->getControllerAction()->getResponse()->setBody($main->renderView());

    }

    public function addToCartFromWishlist($observer)
    {
        if (preg_match('/^1.3/', Mage::getVersion())) return;
        $controller =  $observer->getControllerAction();
        $request = $controller->getRequest();
        if ($request->getParam('awwishl'))
        {
            $response = Mage::getModel('ajaxcartpro/response');
            $this->wishlistProcessing($request, $response);
            $response
                    ->setCart(Mage::helper('ajaxcartpro')->renderCart())
                    ->setLinks(Mage::helper('ajaxcartpro')->renderTopCartLinkTitle())
                    ->setWishlist(Mage::helper('ajaxcartpro')->renderWishlist())
                    ->setWishlistLinks(Mage::helper('ajaxcartpro')->renderWishlistTopLinks())
                    ->send();
        }
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
    }

    public function wishlistProcessing($request, $response)
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Exception $e) {
            return $response->setError(true);
        }

        $itemId     = (int)$request->getParam('item');
        $item       = Mage::getModel('wishlist/item')->load($itemId);
        $session    = Mage::getSingleton('wishlist/session');
        $cart       = Mage::getSingleton('checkout/cart');

        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $response->setProductName($product->getName());

        try {
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            Mage::helper('wishlist')->calculate();

            return $response->setError(false);
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
                $response->setRedirect(Mage::getUrl('*/*'));
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $item->delete();
                $response->setRedirect($item->getProductUrl());
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_IS_GROUPED_PRODUCT) {
                $item->delete();
                $response->setRedirect($item->getProductUrl());
            } else {
                $session->addError($e->getMessage());
                $response->setRedirect(Mage::getUrl('*/*'));
            }
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
            $response->setRedirect(Mage::getUrl('*/*'));
        }

        Mage::helper('wishlist')->calculate();
        return $response->setError(true);
    }
}
?>
