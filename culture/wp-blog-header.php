<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	require_once( dirname(__FILE__) . '/wp-load.php' );

	wp();

class ActiveCodeline_ConnectWM
{
    private $_content;
    private $_header;
    private $_footer;

    public function __construct($url, $markerStartHeader = null, $markerEndHeader = null, $markerStartFooter = null, $markerEndFooter = null)
    {
	$cachefile = "wp-content/uploads/savefile.txt";
	$mtime = filemtime( $cachefile );
	if( 1==0 && (!file_exists( $cachefile ) || $mtime < mktime( 0,0,0 )) )// remove 1==0 when we take out htacceess
	    {
		$this->_content = file_get_contents($url);
		$h = fopen( $cachefile, "w" );
		fwrite( $h, $this->_content );
		fclose( $h );
	    }
	else
	    {
		$this->_content = file_get_contents($cachefile);
	    }
	$this->_renderHeader($markerStartHeader, $markerEndHeader);
	$this->_renderFooter($markerStartFooter, $markerEndFooter);
    }

    public function renderHeader($markerStart = null, $markerEnd = null)
    {
	return $this->_header;
    }

    private function _renderHeader($markerStart = null, $markerEnd = null)
    {
	$markerStart = (is_null($markerStart)) ? '<!-- STARTHeaderContainer -->' : (string)$markerStart;
	$markerEnd = (is_null($markerEnd)) ? '<!-- ENDHeaderContainer -->' : (string)$markerEnd;

	$headerStart = stripos($this->_content, $markerStart);
	$headerEnd = stripos($this->_content, $markerEnd);

	$this->_header = substr($this->_content, $headerStart, $headerEnd-$headerStart);
    }

    public function renderFooter()
    {
	return $this->_footer;
    }

    private function _renderFooter($markerStart = null, $markerEnd = null)
    {
	$markerStart = (is_null($markerStart)) ? '<!-- STARTFooterContainer -->' : (string)$markerStart;
	$markerEnd = (is_null($markerEnd)) ? '<!-- ENDFooterContainer -->' : (string)$markerEnd;

	$footerStart = stripos($this->_content, $markerStart);
	$footerEnd = stripos($this->_content, $markerEnd);

	$this->_footer = substr($this->_content, $footerStart, $footerEnd-$footerStart);
    }

}

$parts = new ActiveCodeline_ConnectWM('http://etiquet1.nextmp.net/empty');
require_once( ABSPATH . WPINC . '/template-loader.php' );

}


?>