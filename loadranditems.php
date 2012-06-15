<?php
define('MAGENTO', "." );
if( !is_dir( MAGENTO ) )
{
    echo( "ERROR: " . MAGENTO." is not a directory or doesn't exist.\n" );
    exit( 1 );
}

require_once( MAGENTO . '/app/Mage.php' );

$coo = $_COOKIE["usingstore"];
if( !$coo )
    $coo = "us";
$myid = $coo=="us"?1:6;
Mage::app()->setCurrentStore( $myid );

$products = Mage::getModel('catalog/resource_eav_mysql4_product_collection')
    ->addAttributeToFilter('type_id', array('eq' => 'configurable') )
    ->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH ) ;
$products->setPage(1, 4);
$products->getSelect()->order('rand()');
    ?>
<link rel="stylesheet" type="text/css" href="/skin/frontend/default/etiquette/css/fonts/stylesheet.css" media="screen" />
<link rel="stylesheet" href="/culture/wp-content/themes/etiquette/style.css" type="text/css" media="screen" />
<style type="text/css">
    body {
        background: #fff;
    }
</style>

    <div id="sidebar" role="complementary" class="iframe"><div class="section iframe shop">

            <ul class="shop-list">
<?php
    $count = 0;
    foreach( $products as $p )
    {
	$config_product = Mage::getModel('catalog/product')->load( $p->getId() );
$url = $config_product->getProductUrlWithCategory();
$url = str_replace( "loadranditems.php/", "$coo/", $url );
$image = Mage::helper('catalog/image')->init($config_product, 'small_image')->resize(147, 147);
	$_categories = $config_product->getCategoryIds();
	    $touse = $_categories[count( $_categories )-1];
    if( $touse == 95 )
	    $touse = $_categories[count( $_categories )-2];

	$_category = Mage::getModel('catalog/category')->load($touse);
	//	echo( $config_product->getData( 'image' ) );
	?>

            	<li <?php if (($count % 2) > 0): ?>class="right"<?php endif; ?>>
                	<p class="shop-image"><a target=_top href="<?php echo $url?>"><img src="<?php echo $image?>" border="0" /></a></p>
                    <p class="title"><a target=_top href="<?php echo $url?>"><?php echo $_category->getName()?></a></p>
      <p class="description"><strong><?php echo $config_product->getShortDescription()?></strong></p>
                </li>
      <?php $count++;
         }      ?>
            </ul>
            <div class="clear"></div>
        </div>
</div>