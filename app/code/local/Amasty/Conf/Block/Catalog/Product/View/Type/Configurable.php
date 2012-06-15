<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Conf
*/
class Amasty_Conf_Block_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    protected $_optionProducts;
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if (!Mage::getStoreConfig('amconf/general/enable'))
        {
            return $html;
        }
        if ('product.info.options.configurable' == $this->getNameInLayout())
        {
            if (Mage::getStoreConfig('amconf/general/hide_dropdowns'))
            {
                $html = str_replace('super-attribute-select', 'super-attribute-select no-display', $html);
            }
            
            if (Mage::getStoreConfig('amconf/general/show_clear'))
            {
                $html = '<a href="#" onclick="javascript: spConfig.clearConfig(); return false;">' . $this->__('Reset Configuration') . '</a>' . $html;
            }
            
            $html = '<script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'amasty/amconf/configurable.js"></script>'
                        . $html;
            $simpleProducts = $this->getProduct()->getTypeInstance(true)->getUsedProducts(null, $this->getProduct());
            
            if ($this->_optionProducts)
            {
                $this->_optionProducts = array_values($this->_optionProducts);
                foreach ($simpleProducts as $simple)
                {
                    $key = array();
                    for ($i = 0; $i < count($this->_optionProducts); $i++)
                    {
                        foreach ($this->_optionProducts[$i] as $optionId => $productIds)
                        {
                            if (in_array($simple->getId(), $productIds))
                            {
                                $key[] = $optionId;
                            }
                        }
                    }
                    if ($key)
                    {
                        // @todo check settings:
                        // array key here is a combination of choosen options
                        $confData[implode(',', $key)] = array(
                            'short_description' => $simple->getShortDescription(),
                            'description'       => $simple->getDescription(),
                        );
                        
                        if ($simple->getImage() && Mage::getStoreConfig('amconf/general/reload_images'))
                        {
                            $confData[implode(',', $key)]['media_url'] = $this->getUrl('amconf/media', array('id' => $simple->getId())); // media_url should only exist if we need to re-load images
                        }
                    }
                }
                $html .= '<script type="text/javascript">var confData = new AmConfigurableData(' . Zend_Json::encode($confData) . ');</script>';
                if (Mage::getStoreConfig('amconf/general/hide_dropdowns'))
                {
                    $html .= '<script type="text/javascript">Event.observe(window, \'load\', spConfig.processEmpty);</script>';
                }
                $html .= '<script type="text/javascript">confData.textNotAvailable = "' . $this->__('Choose previous option please...') . '";</script>';
                $html .= '<script type="text/javascript">confData.mediaUrlMain = "' . $this->getUrl('amconf/media', array('id' => $this->getProduct()->getId())) . '";</script>';
            }
        }
        return $html;
    }
    
    public function getJsonConfig()
    {
        $jsonConfig = parent::getJsonConfig();
        if (!Mage::getStoreConfig('amconf/general/enable'))
        {
            return $jsonConfig;
        }
        $config = Zend_Json::decode($jsonConfig);
        foreach ($config['attributes'] as $attributeId => $attribute)
        {
            if (Mage::getModel('amconf/attribute')->load($attributeId, 'attribute_id')->getUseImage())
            {
                $config['attributes'][$attributeId]['use_image'] = 1;
            }
            foreach ($attribute['options'] as $i => $option)
            {
                $this->_optionProducts[$attributeId][$option['id']] = $option['products'];
                if (Mage::getModel('amconf/attribute')->load($attributeId, 'attribute_id')->getUseImage())
                {
                    $config['attributes'][$attributeId]['options'][$i]['image'] = Mage::helper('amconf')->getImageUrl($option['id']);
                }
            }
        }
        return Zend_Json::encode($config);
    }
}