<?php
global $parts;

$c = $_COOKIE["usingstore"];
//echo( $c );
if( 1 == 0 )
{
//require_once($_SERVER['DOCUMENT_ROOT'].'/app/Mage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/app/Mage.php');
/* Store or website code */
//echo( $c );
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : $c=="eu"?"eu":"us";

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
Mage::init();
$core     = Mage::getSingleton('core/session', array('name' => 'frontend'));
$session  = Mage::getSingleton('customer/session', array('name' => 'frontend'));
$loggedIn = $session->isLoggedIn() ? 'customer_logged_in' : 'customer_logged_out';
//echo( $loggedIn );
$layout = Mage::app($mageRunCode, $mageRunType)->getLayout();
$layout->getUpdate()
    ->addHandle('default')
    ->addHandle('cms_page')
    ->addHandle('STORE_eu')
    ->addHandle('THEME_frontend_default_etiquette')
    ->addHandle('cms_index_index')
    ->addHandle('page_one_column')
    ->addHandle($loggedIn)
    ->load();

$layout->generateXml()
       ->generateBlocks();

$headBlock = $layout->getBlock('head');
$headBlock->setData('title', the_title(null, null, false));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en" lang="en">
    <head>
    <?php echo $headBlock->toHtml();?>
</head>
<body class=" cms-page-view cms-empty">
<div class="wrapper">
              <noscript>
              <div class="noscript">
                  <div class="noscript-inner">
                      <p><strong>JavaScript seem to be disabled in your browser.</strong></p>
                      <p>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
                  </div>
              </div>
          </noscript>
          <div class="page">
      <?php echo str_replace('My Favorites','SAVED ITEMS', $layout->getBlock('header')->toHtml());?>



    <?php
}
else
{


function fetchcookie( $inKey ) {
    $decode = $_COOKIE[$inKey];

    for ( $index = 1 ; array_key_exists( $inKey.COOKIE_PORTIONS.$index , $_COOKIE ) ; $index += 1 ) {
	$decode .= $_COOKIE[$inKey.COOKIE_PORTIONS.$index];
    }

    $decode = base64_decode( $decode );
    if( $decode )
	$decode = gzuncompress( $decode );
    else
	return "";

    return unserialize( $decode );
}

$h =  $parts->renderHeader();
$title = wp_title('', false) ;
if( !$title )
    $title = "Blog";
$total = $_COOKIE["my_cart_total"];
$carttop = fetchcookie("cart_top");
$items = $_COOKIE["my_cart_items"];
if( $items > 0 )
    $h = str_replace( ">Shopping Bag<", ">Shopping Bag ($items)<" , $h);
//print_r( $_COOKIE );
if( $_COOKIE["ami_logged_in"] )
    {
//		echo( "LOGGEDIN" );
	$h = str_replace( '<li class="search-bar">',
			  '<li class=" last" ><a href="http://etiquette.swedenunlimited.com/customer/account/logout/" title="Log Out" >Log Out</a></li><li class="search-bar">', $h );

    }
$h = str_replace( "Empty for WP", $title, $h );
if( strpos( $_SERVER[REQUEST_URI], "/specialedition/" ) !== false )
    $h = str_replace( "<body class=\" cms-page-view cms-empty\">", "<body class=\" cms-page-view cms-special-edition\">", $h );

$hspl = split( "<!-- begin cart top -->", $h );
$hsplend = split( "<!-- end cart top -->", $h );
$c = $_COOKIE["usingstore"];
if( $c )
    {
    // change here too
        if( $c == "us" )
        {
            $hspl[0] = str_replace( ".com/eu/", ".com/us/", $hspl[0] );
            $hsplend[1] = str_replace( ".com/eu/", ".com/us/", $hsplend[1] );
        }
	}

$h = $hspl[0] . stripslashes( $carttop ) . $hsplend[1];

echo( $h );

}
?>
<div class="main-container col1-layout">

       <div class="main">

                    <div id="wp-page">

                    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.css" type="text/css" media="screen" />




