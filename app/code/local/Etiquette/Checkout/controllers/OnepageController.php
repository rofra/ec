<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Etiquette_Checkout_OnepageController extends Mage_Checkout_OnepageController {

  /**
   * Coupon check
   */
  function couponAction() {
    $cart = Mage::getSingleton('checkout/cart');
    $this->loadLayout('checkout_onepage_review');

    $couponCode = (string) $this->getRequest()->getParam('coupon_code');

    if ($this->getRequest()->getParam('remove') == 1) {
      $couponCode = '';
    }
    try {
      $cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
      $cart->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')->collectTotals()->save();

      if ($couponCode) {
        if ($couponCode == $cart->getQuote()->getCouponCode()) {
          $result['message'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
          $result['success'] = true;
        }
        else {
          $result['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
          $result['success'] = false;
        }
      }
      else {
        $result['message'] = $this->__('Coupon code was canceled.');
        $result['success'] = true;
      }

    }
    catch (Mage_Core_Exception $e) {
      $result['message'] = $e->getMessage();
      $result['success'] = false;
    }
    catch (Exception $e) {
      $result['message'] = $this->__('Cannot apply the coupon code.');
      $result['success'] = false;
      Mage::log('testing: ' . $e);
    }

    // $result['goto_section'] = 'review';
    $result['update_section'] = array('name' => 'review', 'html' => $this->_getReviewHtml());

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }
}
