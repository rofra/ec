<?php

require_once "Mage/Catalog/Block/Product/View/Type/Configurable.php";

class Sweden_SPS_Block_Product_View_Type_Simple extends Mage_Catalog_Block_Product_View_Type_Simple
{
    protected function _construct()
    {
	$this->addData(array(
			     //'cache_key'           =>
			     //'cache_lifetime'  =>
			     'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG . $this->getProduct()->getId() ),
			     )); 
   }

    public function getCacheKey()
    {
	if (!$this->hasData('cache_key')) {
	    //$cacheKey = LAYOUTNAME_STORE+ID_PRODUCT+IDne
	    $currentCategory = Mage::registry('current_category');
	    $extcat = "";
	    if( $currentCategory )
		$extcat = "_".$currentCategory->getId();
	    $cacheKey = $this->getNameInLayout().'_STORE'.Mage::app()->getStore()->getId().'_PRODUCT'.$this->getProduct()->getId() .$extcat;
	    //.'_'.Mage::getDesign()->getPackageName().'_'.Mage::getDesign()->getTheme('template'). //_PACKAGE_THEME ?
	    $this->setCacheKey($cacheKey);
	}
	return $this->getData('cache_key');
    }
}