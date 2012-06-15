<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Conf
*/
class Amasty_Conf_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getImageUrl($optionId)
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'amconf' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (file_exists($uploadDir . $optionId . '.jpg'))
        {
            return Mage::getBaseUrl('media') . 'amconf' . '/' . 'images' . '/' . $optionId . '.jpg';
        }
        return '';
    }
}