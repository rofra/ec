<?php
extract( $_GET );
extract( $_POST );

if( $email )
  {
include_once('cc_class.php');
$ccContactOBJ = new CC_Contact();
//$ccListOBJ = new CC_List();
//$allLists = $ccListOBJ->getLists();
// print_r( $allLists );
 // echo( "<br>c<br>" );
/**
 * If we have post fields means that the form have been submitted.
 * Therefore we start submiting inserted data to ConstantContact Server.
 */
 $postFields = array();
 $postFields["email_address"] = $email;
 $postFields["lists"] = array( "http://api.constantcontact.com/ws/customers/bunited/lists/1" );

$postFields["custom_fields"] = array();
$postFields["custom_fields"][1] = $bmonth;

$contactXML = $ccContactOBJ->createContactXML(null,$postFields);
// 	    $h = fopen("/tmp/rc", "a+" );
// fwrite( $h, $contactXML . "\n" );

if (!$ccContactOBJ->addSubscriber($contactXML)) {
//echo( "err:" . $ccContactOBJ->lastError  . "\n" );
//   fwrite( $h, "err:" . $ccContactOBJ->lastError  . "\n" );
}
else
{
//echo( "not error" );
}
  }
Header( "Location: /thanks" );
?>