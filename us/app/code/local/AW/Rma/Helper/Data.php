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
 * @package    AW_Rma
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Rma_Helper_Data extends Mage_Core_Helper_Abstract {
    public static function checkExtensionVersion($extensionName, $minVersion)
    {
        if(($version = Mage::getConfig()->getModuleConfig($extensionName)->version)) {
            return version_compare($version, $minVersion, '>=');
        }
        return false;
    }

    /**
     * Check is extension enabled in advanced tab
     * @return bool
     */
    public static function isEnabled() {
        return !((bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_Rma'));
    }

    public static function getTypeLabel($id) {
        $_type = Mage::getModel('awrma/entitytypes')->load($id);
        if($_type->getData() == array()) return null;
        else return $_type->getName();
    }
    
    public static function getItemsHtml($rmaRequest, $params = array()) {
        $params = array_merge($params, array('itemscount' => $rmaRequest->getOrderItems()));
        return self::getItemsForOrderHtml($rmaRequest->getData('order_id'), 2, '', $params);
    }

    /**
     * Returns max order item count available for RMA
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    function getItemMaxCount($item) {
        return intval($item->getQtyToRefund() ? $item->getQtyToRefund() : $item->getQtyOrdered());
    }

    /**
     * Returns current requested items
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    function getItemCount($item) {
        return intval($item->getData('awrma_qty') ? $item->getData('awrma_qty') : $this->getItemMaxCount($item));
    }

    /**
     * Return html view of order items
     * @param <type> $orderId
     * @param <type> $guestMode
     * @param <type> $data
     * @return array
     */
    public static function getItemsForOrderHtml($orderId, $guestMode, $data, $params = array())
    {
        $result = array();
        $_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        
        if(!($_order->getData() == array())) {
            if($guestMode === true) $guestMode = 1;
            if($guestMode === false) $guestMode = 0;
            switch($guestMode) {
                case 0:
                    if($_order->getCustomerId() != $data) return $result;
                    break;
                case 1:
                    if($_order->getCustomerId() || $_order->getCustomerEmail() != $data) return $result;
                    break;
                default:
                    $guestMode = 2; //admin
            }
            $_orderItems = $_order->getItemsCollection();

            $_itemsRenderer = new Mage_Sales_Block_Order_Items();
            $_itemsRenderer
                ->setLayout(Mage::getSingleton('core/layout'));
            if($guestMode != 2) {
                if(Mage::helper('awrma')->checkExtensionVersion('Mage_Core', '0.8.25')) {
                    $_itemsRenderer
                        ->addItemRender('default', 'sales/order_item_renderer_default', 'aw_rma/sales/order/items/renderer/default.phtml')
                        ->addItemRender('grouped', 'sales/order_item_renderer_grouped', 'aw_rma/sales/order/items/renderer/default.phtml');
                } else {
                    $_itemsRenderer
                        ->addItemRender('default', 'sales/order_item_renderer_default', 'aw_rma/sales/order/items/renderer/default13x.phtml')
                        ->addItemRender('grouped', 'sales/order_item_renderer_grouped', 'aw_rma/sales/order/items/renderer/default13x.phtml');
                }
            } else {
                $_itemsRenderer
                    ->addItemRender('default', 'sales/order_item_renderer_default', 'aw_rma/sales/order/items/renderer/default.phtml')
                    ->addItemRender('grouped', 'sales/order_item_renderer_grouped', 'aw_rma/sales/order/items/renderer/default.phtml');
            }
            
            foreach($_orderItems as $_item) {
                if($_item->getParentItem() || (isset($params['items']) && !in_array($_item->getId(), $params['items']))) continue;
                if(isset($params['view_only']))
                    $_item->setData('awrma_view_only', $params['view_only']);
                if(isset($params['itemscount']) && isset($params['itemscount'][$_item->getId()])) {
                    $_item->setData('awrma_qty', $params['itemscount'][$_item->getId()]);
                }
                $result[] = $_itemsRenderer->getItemHtml($_item);
            }
        }

        return $result;
    }

    /**
     * Return url for order
     * @param string $incrementId
     * @param bool $admin
     * @return string
     */
    public static function getOrderUrl($incrementId, $admin = TRUE) {
        $_order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        if($_order->getData() != array()) {
            if($admin)
                return Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $_order->getId()));
            else
                return Mage::app()->getStore()->getUrl('sales/order/view', array('order_id' => $_order->getId()));
        } else {
            return null;
        }
    }

    /**
     * Return customer edit url
     * @param int $id
     * @return string
     */
    public static function getCustomerUrl($id) {
        $_customer = Mage::getModel('customer/customer')->load($id);
        if($_customer->getData() != array())
            return Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $id));
        else
            return null;
    }

    /**
     * Generate external link for RMA request
     * @return string
     */
    public static function getExtLink() {
        $extLink = strtoupper(uniqid(dechex(rand())));
        return $extLink;
    }

    public static function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public static function isAllowedForOrder($order) {
        if($order->getState() == 'complete') {
            $orderInvoices = $order->getInvoiceCollection();
            $lastInvoiceTime = 0;
            foreach($orderInvoices as $invoice) {
                $invoiceTime = strtotime($invoice->getCreatedAt());
                if($invoiceTime > $lastInvoiceTime) {
                    $lastInvoiceTime = $invoiceTime;
                }
            }
            if($lastInvoiceTime && $lastInvoiceTime >= strtotime('-'.Mage::helper('awrma/config')->getDaysAfter().' day', time())) {
                return true;
            }
        }
        return false;
    }

    public static function getRegionName($regionId) {
        $region = Mage::getModel('directory/region')->load($regionId);
        if($region->getData() != array())
            return $region->getName();
        else
            return null;
    }

    public static function isCustomSMTPInstalled() {
        $_modules = (array) Mage::getConfig()->getNode('modules')->children();
        if(array_key_exists('AW_Customsmtp', $_modules)
            && 'true' == (string) $_modules['AW_Customsmtp']->active
            && !(bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_Customsmtp')
            && @class_exists('AW_Customsmtp_Model_Email_Template'))
            return TRUE;
        return FALSE;
    }
}
