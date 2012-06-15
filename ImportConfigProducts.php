<?php

$hand = fopen( "/home/etiquett/cron/importconfigs", "w+" );
fwrite( $hand, "b" );
fclose( $hand );
if( 1 )
{
echo( "Done! Please wait a minute for your changes to take effect." );
}
else
{
echo ( "Failed!" );
}
?>
