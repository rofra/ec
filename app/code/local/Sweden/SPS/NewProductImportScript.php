<?php


class NewProductImport
{
    // this is going to be an array of configurable style # => ( array of simple products )
    var $simplemapping = array();
    var $simplecolors = array(); // array of sku__color=> 1 that has already been added to 
    var $sizeattributeid = 953; // fill this in
    //        var $colorattributeid = 970; // fill this in
    var $attributesetid = 9; // fill this in
    // this will be a map of simple product id => $sizeoption
    var $sizeoptionarray = array();
    var $createdconfigs = array();

    function findCategory( $catname, $startfrom = '3' )
    {
        $category = Mage::getModel('catalog/category')->load( $startfrom );
        $children = $category->getResource()->getChildren( $category, true );
        if( count( $children  ) )
            foreach( $children as $child )
            {
                $maybemen = Mage::getModel('catalog/category')->load( $child );
                if( $catname == $maybemen->getName() )
                {
                    return $maybemen->getId();
                }
                $val = $this->findCategory( $catname, $maybemen->getId() );
                if( $val )
                    return $val;
            }
    }
    

    function _getAttributeSetId( )
    {
	return $this->attributesetid; 
    }

    function getEntityTypeId()
    {
	return Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
    }

    function getAttribute( $attribute_code )
    {
	$entityTypeId = $this->getEntityTypeId();
	$attribute = Mage::getModel('catalog/resource_eav_attribute')
	    ->loadByCode($entityTypeId, $attribute_code );

	return $attribute;
    }

	/**
	 * $options is an array in the form:
	 * Array
	 * (
	 *    [BLACK] => Array
	 *    (
	 *            [value] => 1
	 *            [Surcharge] => 0.00
	 *    )
	 *    [DARK GREY] => Array
	 *    (
	 *            [value] => 2
	 *            [Surcharge] => 0.00
	 *    )
	 *
	 * )
	 *
	 * and returns an array like this:
	 *
	 * Array
	 * (
	 *    [2] => Array
	 *    (
	 *            [option_id] => 146
	 *            [Surcharge] => 0.00
	 *            [label] => DARK GREY
	 *    )
	 *    [1] => Array
	 *    (
	 *            [option_id] => 145
	 *            [Surcharge] => 0.00
	 *            [label] => Black
	 *    )
	 * )
	 */
    function ensureOptionExists( $attribute, $newoption )
    {
        
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('desc', true)
            ->load();

        $found = 0;
        foreach( $optionCollection as $option )
        {
            if( $option->value == $newoption )
                $found = 1;
        }

        if( !$found )
        {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $option = array();
            $option['attribute_id'] =  $attribute->getId();
            $option['value']['asdf'][0] = $newoption;
            $setup->addAttributeOption( $option );
            echo( "created new attribute option: $newoption\n" );
        }
        
        
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('desc', true)
            ->load();


        $result = array();
        foreach( $optionCollection as $option )
        {
            if( $option->value == $newoption )
            {
                $new_values = array( "option_id" => $option->option_id,
                                     "label" => $option->value
                                     );
                return $new_values;
            }
        }
        
    }

    public function createMissingProducts( $simple_product_orig )
    {
        try
        {
            $simple_product = Mage::getModel('catalog/product');
            $simple_product->load( $simple_product_orig->getId() );
            
            $itemcode = $simple_product->getData( 'style_number' );
            echo( "starting at $itemcode <br>\n" );
            
                //	    return;
            $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
            $attributeSet = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId)
                ->addFilter('attribute_set_name', 'Default');
            
            $item = $itemDetails; // these are really the same
            
            $configurable_product = Mage::getModel('catalog/product');
            
            $configurable_productID = $configurable_product->getIdBySku( $itemcode );
            if( $configurable_productID && $configurable_productID <= 13220 )
            {
                echo( "this was an OLD one $configurable_productID<br>" );
                return;
            }
            echo( "got id ( $configurable_productID ) for $itemcode (" . $configurable_product->getId() . ")<Br>\n" );
            
            if( $configurable_productID )
            {
                    //		echo( "config product existed! $configurable_productID<br>" );
                $configurable_product->load( $configurable_productID );
                $baseprice = $simple_product->getPrice();
                $baseprice = (float)preg_replace( "/[^0-9.]/", '', $baseprice );
                if( !$baseprice )
                    $baseprice = 0.00;
                
                echo( "setting price to: $baseprice\n" );
                $configurable_product->setPrice( $baseprice );
                $configurable_product->save();
                    //                $configurable_product->setStatus($simple_product->getStatus() );
		//		return; 
            }
            else
            {
                    // creating a new config product
                echo( "CREATING NEW CONFIG\nsetting config sku to $itemcode\n" );
                $configurable_product->setSku( $itemcode );
                $configurable_product->setTypeId( "configurable" );
                $configurable_product->setStatus($simple_product->getStatus() );
                $configurable_product->setAttributeSetId( $this->_getAttributeSetId( ) );
                $configurable_product->setVisibility( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH );
                $this->updateProduct( $simple_product, $configurable_product );
                $configurable_product->save();
                $configurable_productID = $configurable_product->getId();
                echo("after config save<br>\n" );
                echo( "created id $configurable_productID <br>\n" );
                
            }
            $galleryData = $configurable_product->getMediaGallery();
	    if( !count( $galleryData['images'] ) )
		{
		    $sdata = $simple_product->getMediaGallery();
		    foreach( array( "image","swatch","small_image","thumbnail","rollover","alt1","alt2","alt3","alt4" ) as $type )
			{
			    $file = $simple_product->getData( $type );
			    if( $file )
				{
				    echo( Mage::getBaseDir('media') .  "/catalog/product" . $file . "\n" );
				    $configurable_product->addImageToMediaGallery(Mage::getBaseDir('media') . "/catalog/product" . $file, $type, false, false);
				}
			}
		    
		}

            $simple_products_array = $this->simplemapping[$configurable_productID];
            if( !$simple_products_array ) $simple_products_array = array();

            $attribute_map = array();
            $attribute_order = 0;
            
		    // go through all of the dimensions listed in WDIR
		    // output, make sure corresponding options exist
		    // in thee magento catalog.  and get a mapping
		    // from the oder motion option id to the magento
		    // option id.  (see the ensureOptionsExist
		    // documentation).

            $attribute = $this->getAttribute( "size" );
	    //	    $colorattribute = $this->getAttribute( "colorway" );
            if( $attribute === false )
            {
                echo ("NO ATTRIBUTE NAMED size FOUND;\n" );
            }
            
	    $already = $this->simplemappingcolors[$configurable_productID];// . " " . $simple_product->getColor()
	    if( !$already || 1 )
		{
		    if( $simple_product->getVisibility() != Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH )
			{
// 			    echo( "setting " . $simple_product->getId() . " to VISIBLE \n" );
// 			    $simple_product->setVisibility( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH );
// 			    $simple_product->save();
			}
		    $this->simplemappingcolors[$configurable_productID] = 1;	    // . " " . $simple_product->getColor()
		}
	    else
		{
		    //			    echo( "NOTTTTT setting " . $simple_product->getId() . " to VISIBLE \n" );
		}
                
	    $sizeoption = false;
	    //	    $coloroption = false;
	    
	    if( $simple_product->getSize() )
		$sizeoption = $this->ensureOptionExists( $attribute, $simple_product->getSize() );
	    else
		{
		    echo( "NO SIZE FOR ".$simple_product->getId() ."\n" );
		}

// 	    if( $simple_product->getColor() )
// 		$coloroption = $this->ensureOptionExists( $colorattribute, $simple_product->getColorway() );
// 		else
// 		{
// 		echo( "NO COLOR FOR ".$simple_product->getId() ."\n" );
// 		}

//            gc_collect_cycles();

            $simple_productID = $simple_product->getId();


	    $configurable_product->save();
            $simple_products_array[] = $simple_product;
            $this->simplemapping[$configurable_productID] = $simple_products_array;
	    if( $sizeoption )
		$this->sizeoptionarray[$simple_productID] = $sizeoption;
//  	    if( $coloroption )
//  		$this->coloroptionarray[$simple_productID] = $coloroption;

        } catch (Exception $e) {
            echo( $e );
            $hand = fopen( "/tmp/rc2", "a+" );
            fwrite( $hand, "error during product import: " . $e->getMessage() . "\n" ) ;
            fclose( $hand );
        }
    }

    function findOption( $attname,  $value )
    {
	//	echo( "returning $value\n " );
	return $value ;
        $value = trim( $value );
        $attribute = $this->getAttribute( $attname );
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('desc', true)
            ->load();

        foreach( $optionCollection as $option )
        {
            if( $option->value == $value )
            {
//                echo( "found ".$option->option_id." for $value \n" );
                return $option->option_id;
            }
        }

        foreach( $optionCollection as $option )
        {
//            echo("was looking for $value, ".$option->value . "\n");
        }
        echo("NO MATCH FOR ".$value." within $attname\n" );

        if( !$found )
        {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $option = array();
            $option['attribute_id'] =  $attribute->getId();
            $option['value']['asdf'][0] = $value;
            $setup->addAttributeOption( $option );
            echo( "created new attribute option: $value\n" );
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getId())
                ->setPositionOrder('desc', true)
                ->load();
            
            foreach( $optionCollection as $option )
            {
                if( $option->value == $value )
                {
                    echo( "NOW found ".$option->option_id." for $value \n" );
                    return $option->option_id;
                }
        }
            
        }

        return "";
        
    }

    function findOptionName( $attname, $option_id )
    {
        $value = trim( $value );
        $attribute = $this->getAttribute( $attname );
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('desc', true)
            ->load();

        foreach( $optionCollection as $option )
        {
            if( $option->option_id == $option_id )
            {
//                echo( "found ".$option->option_id." for $value \n" );
                return $option->value;
            }
        }

        foreach( $optionCollection as $option )
        {
//            echo("was looking for $value, ".$option->value . "\n");
        }
        echo("NO MATCH FOR ".$option->value." within $attname\n" );
        return "";
        
    }

    function updateProduct( $simple_product, $configurable_product)
    {
        echo("sku is: " . $configurable_product->getSku(). "\n" );

        $configurable_product->setData( "color", $this->findOption( "color", $simple_product->getData( "color" ) ) );
        $configurable_product->setData( "colorway", $this->findOption( "colorway", $simple_product->getData( "colorway" ) ) );
        $configurable_product->setData( "style_number", $simple_product->getData( "style_number" ) );
        $configurable_product->setData( "label", $this->findOption( "label", $simple_product->getData( "label" ) ) );
	$configurable_product->setData( "group", $this->findOption( "group", $simple_product->getData( "group" ) ) );
        $configurable_product->setData( "pattern", $simple_product->getData( "pattern" ) );
        $configurable_product->setData( "collection1", $this->findOption( "collection1", $simple_product->getData( "collection1" ) ) );
        $configurable_product->setData( "color_abbreviation", $simple_product->getData( "color_abbreviation" ) );
        $configurable_product->setData( "composition", $simple_product->getData( "composition" ) );
	$configurable_product->setData( "gender", $this->findOption( "gender", $simple_product->getData( "gender" ) ) );
        $configurable_product->setData( "length", $simple_product->getData( "length" ) );
        $configurable_product->setData( "material_description", $simple_product->getData( "material_description" ) );
        $configurable_product->setData( "origin", $simple_product->getData( "origin" ) );
        $configurable_product->setData( "size_and_fit", $simple_product->getData( "size_and_fit" ) );
	//        $configurable_product->setData( "colorway", $this->findOption( "colorway", $simple_product->getData( "colorway" ) ) );
        $configurable_product->setData( "delivery_and_returns", $simple_product->getData( "delivery_and_returns" ) );

	
        $configurable_product->setName( $simple_product->getName() );
        $configurable_product->setDescription( $simple_product->getDescription() );
	if( $simple_product->getShortDescription() )
	    $configurable_product->setShortDescription( $simple_product->getShortDescription() );

        $baseprice = $simple_product->getPrice();
        $baseprice = (float)preg_replace( "/[^0-9.]/", '', $baseprice );
        if( !$baseprice )
            $baseprice = 0.00;
        
        $configurable_product->setPrice( $baseprice );
        $configurable_product->setData( "tax_class_id", 1 ); // for default


	$categories = $simple_product->getCategoryIds();
	$configurable_product->setCategoryIds( $categories );
	$categories[] = 3;
	//	$configurable_product->setCategoryIds( $categories );
        echo( "before save: " . $configurable_product->getTypeId() . "\n" ); 
        $configurable_product->save();


        if( $configurable_product->getId() > 0 )
        {
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->loadByProduct( $configurable_product->getId() );
            $stockItem->setProductId( $configurable_product->getId() );
            $stockItem->setStockId( 1 );
            $stockItem->save();
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->loadByProduct( $configurable_product->getId() );
            $stockItem->setData("use_config_backorders", 0 );
            $stockItem->setData('is_in_stock', 1);
            $stockItem->setData('manage_stock', 1);
            $stockItem->setData('use_config_manage_stock', 0);
            $stockItem->setQty( 100 );
            $stockItem->save();
        }
        else
        {
	    echo( "id was empty!\n" );
        }
        $configurable_product->save();
    }
    
    function updateConfigurables()
    {
        $newproducts = array();

        try
        {
            $attribute_map = array();
	    // this is for the cross selling
            $allproducts = array();
            $cnt = 0;
            foreach( $this->simplemapping as $configurable_productID => $simple_products_for_config )
            {
                $configurable_product = Mage::getModel('catalog/product');
                $configurable_product->load( $configurable_productID );
                $allproducts[$cnt] = $configurable_product;
                $cnt++;
            }
            
            foreach( $this->simplemapping as $configurable_productID => $simple_products_for_config )
            {
                $configurable_product = Mage::getModel('catalog/product');
                $configurable_product->load( $configurable_productID );
                
                $simple_products_array = array();

                foreach( $simple_products_for_config as $simple_product )
                {
                    $configurable_attribute_array = array();
                    $new_values = $this->sizeoptionarray[$simple_product->getId()];
		    //		    $color_new_values = $this->coloroptionarray[$simple_product->getId()];
                    $simple_productID= $simple_product->getID();

		    if( $new_values )
			{
			    $attribute_code = "size";
			    $configurable_attribute_array[ $attribute_map[ $attribute_code ]['order'] ]
				= array( 'label' => $new_values[ 'label' ],
					 'attribute_id' => $this->_getAttributeSetId( ) ,
					 'value_index' => $new_values[ 'option_id' ] );
			}
		    // no configurable colors for etiquette
// 		    if( $color_new_values )
// 			{
// 			    $attribute_code = "colorway";
// 			    $configurable_attribute_array[ $attribute_map[ $attribute_code ]['order'] ]
// 				= array( 'label' => $color_new_values[ 'label' ],
// 					 'attribute_id' => $this->_getAttributeSetId( ),
// 					 'value_index' => $color_new_values[ 'option_id' ] );
// 			}

                    $simple_products_array[ $simple_productID ] = $configurable_attribute_array;

                            // save in the list of items we'll add to websites
                    $newproducts[] = $simple_product->getId();
                }
                    // save in the list of items we'll add to the configurable product
                

            // if attributes start showing up multiple times, let's use this code
                if( 1 == 1 ) {
                    echo( "config product iD: " . $configurable_product->getId() ) ; 
                    $cspa = $configurable_product->getTypeInstance()->getConfigurableAttributesAsArray( $configurable_product );
                    $attr_codes = array();
                    if( isset($cspa) && !empty($cspa) )
                    { //found attributes
                        foreach($cspa as $cs_attr)
                        {
                            $attr_codes[] = $cs_attr['attribute_id'];
//                            fwrite( $handle, "attr code: " . $cs_attr['attribute_id'] . "\n" );
                        }
                    }
                }
                
//                print_r( $attr_codes );
                
                $attributeData = array();
                $values = array();
                
                    // start old loop
                if( !in_array( $this->sizeattributeid, $attr_codes ) )
                    $configurable_attribute_ids = array( 0=> $this->sizeattributeid );//, 1=> $this->colorattributeid
                else
                    $configurable_attribute_ids= array();
                
                $thisAttributeDataValues = array();
		//                $thisColorAttributeDataValues = array();
                foreach( $simple_products_for_config as $simple_product )
                {
                    $thissizeoption = $this->sizeoptionarray[$simple_product->getId()];
		    if( $thissizeoption )
			{
			    $thisAttributeDataValues[] = 
				array( 'value_index', $thissizeoption[ 'option_id' ],
				       'label', $thissizeoption[ 'label' ] );
			}
		    
//                     $thiscoloroption = $this->coloroptionarray[$simple_product->getId()];
// 		    if( $thiscoloroption )
// 			{
// 			    $thisColorAttributeDataValues[] = 
// 				array( 'value_index', $thiscoloroption[ 'option_id' ],
// 				       'label', $thiscoloroption[ 'label' ] );
// 			}
		    
                }
                

                $thisAttributeData = array( 'label' => "size",
                                            'attribute_id' => $this->sizeattributeid,
                                            'attribute_code' => "size",
                                            'values' => $thisAttributeDataValues );
                
                $attributeData[] = $thisAttributeData;

		// no colors for etiquette
//                 $thisAttributeData = array( 'label' => "color",
//                                             'attribute_id' => $this->colorattributeid,
//                                             'attribute_code' => "colorway",
//                                             'values' => $thisAttributeDataValues );
                
//                  $attributeData[] = $thisAttributeData;


// print_r( $attributeData );
		// echo( "\n" );
		// print_r( $configurable_attribute_ids );
		// echo( "\n" );
// end old loop
                
//        print_r( $simple_products_array );
                
//		echo( "before configure of configurable \n" );
                if( !empty( $configurable_attribute_ids ) )
                {
                        // this one is done?
                    $configurable_product->getTypeInstance()->setUsedProductAttributeIds( $configurable_attribute_ids );
                    
                        // this one is done?
                    $configurable_product->setConfigurableAttributesData( $attributeData );
                    
                        // this one is done?
                    $configurable_product->setCanSaveConfigurableAttributes(true);
                    
                        // this one is done?
                    $configurable_product->setConfigurableProductsData( $simple_products_array );
                }

		$toaddarr = array();
		$askus = array();
		$cnt = 1;
		echo( "count: " . count( $allproducts ) . "\n" );
		$max = count($allproducts)> 8?8:1;
		while( count( $toaddarr ) < $max )
		    {
			$r = rand( 0, count( $allproducts ) );
			$p = $allproducts[$r];
			if( !$p )
			    continue;
			if( $p->getStatus() ==   Mage_Catalog_Model_Product_Status::STATUS_DISABLED )
			    continue;
                        if( $p->getVisibility() != Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH )
                            continue;
			if( $p->getSku() == $configurable_product->getSku() )
			    continue;
			//			$askus[$p->getData( "style_number" )] = 1;
			$toaddarr[$p->getId()] = array( "position"=>$cnt++ );
		    }
//		echo( "cross sells:" . print_r( $toaddarr, true ) . "\n" );
		$configurable_product->setUpSellLinkData($toaddarr);

                
//		echo( "after configure of configurable \n" );
                try {
                        //		fwrite( $handle, "before save\n" );
                    $configurable_product->save();
                    echo( "saved\n" );
                } catch (Mage_Core_Exception $e) {
                    $this->_fault('data_invalid', $e->getMessage());
                }
                
                    // save in the list of items we'll add to websites
                $newproducts[] = $configurable_productID;
		


            }
            
            
            if( count( $newproducts ) ) 
            {
                $websiteIds = array();
//                $websiteIds[] = Mage::app()->getStore()->getWebsiteId();
                $websiteIds[] = 1;
                $websiteIds[] = 2;
                Mage::getModel('catalog/product_website')->addProducts($websiteIds, $newproducts );
                echo( "setting products to : ".print_r( $websiteIds, true ) . "\n"  );
                echo( "pids to : ".print_r( $newproducts, true ) . "\n"  );
            }
    
	    fwrite( $h, "done with config assignment \n" );
		
	} catch (Exception $e) {
	    echo( $e );
	    $hand = fopen( "/tmp/rc", "a+" );
	    fwrite( $hand, "error during product import: " . $e->getMessage() . "\n" ) ;
	    fclose( $hand );
	}
    }

}


  // 10,11,12: gift,men,women

if( !getenv( "MAGENTO" ) )
{
    echo( "the MAGENTO environment variable must be defined\n" );
    exit( 1 );
}
define('MAGENTO', getenv( "MAGENTO" ) );
if( !is_dir( MAGENTO ) )
{
    echo( "ERROR: " . MAGENTO." is not a directory or doesn't exist.\n" );
    exit( 1 );
}

require_once( MAGENTO . '/app/Mage.php' );

Mage::app("us");
$myid = Mage_Core_Model_App::ADMIN_STORE_ID;
Mage::app()->setCurrentStore( $myid );
// echo( $myid );
// exit;
$pi = new NewProductImport();

$products = Mage::getModel('catalog/resource_eav_mysql4_product_collection')
    ->addAttributeToFilter('type_id', array('eq' => 'simple')
			   );
foreach( $products as $p )
{
    //    if( strpos( $p->getSku(), "MWCB11-01" )!==false )
    //	{
	    echo( "starting: " . $p->getSku() . "\n" );
	    $pi->createMissingProducts( $p );
	    //	}
}

$pi->updateConfigurables();



