<?php

require_once "Mage/Catalog/Block/Navigation.php";

class Sweden_SPS_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{
    public function _construct()
    {
	$this->addData(array(            'cache_lifetime'    => false,
					 'cache_key'            => "navigation".$this->getRequest()->getRequestUri(),
					 ));
    }
}
?>