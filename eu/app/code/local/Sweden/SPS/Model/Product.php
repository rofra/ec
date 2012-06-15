<?php class Sweden_SPS_Model_Product extends Mage_Catalog_Model_Product
{

    public function getProductUrlForCategory( $catid = -1 )
    {
//        global $_COOKIE;
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $idtouse = $this->getId();

        if( $catid == -1 )
        {
            $categories = $this->getCategoryIds();
            $catid = $categories[count( $categories )-1];
            if( $catid == 95 && count( $categories ) > 1 )
                $catid = $categories[count( $categories )-2];
        }
        if (!$this->isSuper()) {
            $parents = Mage::getModel('catalog/product_type_configurable')
                ->getParentIdsByChild($this->getId());
            if( count( $parents ) > 0 )
                $idtouse  = $parents[0];

        }

        $results = $readConnection->fetchOne("select request_path from core_url_rewrite where product_id = " . $idtouse . " and category_id = " . $catid . " order by url_rewrite_id limit 1" );
        if( !$results )
            $results = $readConnection->fetchOne("select request_path from core_url_rewrite where product_id = " . $idtouse . " and category_id is null order by url_rewrite_id limit 1" );
        $c = strpos( $_SERVER["REQUEST_URI"], "/eu/" ) !== false?"eu":"us";

        return "/$c/". $results;
    }

    function getProductUrlWithCategory()
    {
	$_categories = $this->getCategoryIds();
    $touse = $_categories[count( $_categories )-1];
    if( $touse == 95 )
	    $touse = $_categories[count( $_categories )-2];

	$_category = Mage::getModel('catalog/category')->load($touse);
	$oldurl = $this->getProductUrl();
	if( !$this->getUrlKey() )
	    {
		return $oldurl;
	    }
	$oldurl = basename( $oldurl );
	if( strpos( $oldurl, "html" ) === false && $this->getUrlKey() )
	    $oldurl = $this->getUrlKey() . ".html";

	if( $_category )
	    {
		$url = Mage::app()->getFrontController()->getRequest()->getBaseUrl(). "/" . str_replace( ".html", "", $_category->getUrlPath() )."/". $oldurl;
	    }
	else $url = $this->getProductUrl();

	return $url;
    }

}
?>