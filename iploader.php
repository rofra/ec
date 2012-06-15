<?php
//   $res = shell_exec( "/usr/bin/wget 'software77.net/geo-ip/?DL=2' -O /tmp/IpToCountry.csv.zip" );
//   echo( $res );
//   exit;
//    $res = shell_exec( "cd /tmp/; /usr/bin/unzip IpToCountry.csv.zip");
//    echo( "res:" . $res );
//    print_r( $out );
//    echo( "<br>" );
//    echo( $ret . "<br>" );
//    exit;
mysql_connect( "localhost", "etiquett_muser", "IUDkTC35aU" );
mysql_select_db( "etiquett_magento" );
//mysql_query( "create table iplocation ( ipfrom integer, ipto integer, ccode varchar( 4 ), reserved varchar( 255 ) );" );
mysql_query( "delete from iplocation" );

$handle = fopen( "/tmp/IpToCountry.csv", "r" );
if( $handle )
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
	    if( strpos($data[0], "#" ) !== false )
		continue;
	    if( count($data) == 7 )
		{
		    $any = 1;
		}
	    if( !$any )
		continue;
	    mysql_query( "insert into iplocation( ipfrom, ipto, ccode, reserved ) values ( '$data[0]','$data[1]','$data[4]','$data[6]' )" );
	}

?>
