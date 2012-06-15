<?php

class MagicToolbox_MagicZoom_Model_Observer {

    public function __construct() {

    }

    public function checkForMagic360Product($observer) {
            $event = $observer->getEvent();
            $product = $event->getProduct();
            $id = $product->getId();
            $gallery = $product->getMediaGalleryImages();

            $helper = Mage::helper('magiczoom/settings');
            if((!method_exists($helper, 'isModuleOutputEnabled') && !Mage::getStoreConfigFlag('advanced/modules_disable_output/MagicToolbox_MagicZoom')) || $helper->isModuleOutputEnabled()) {
                $tool = $helper->loadTool('product');
                if($tool->enabled($gallery->getItems(), $id)) {
                    Mage::register('magic360ClassName', 'magiczoom');
                } else {
                    Mage::register('magic360ClassName', false);
                }
            }
        }

    }

?>