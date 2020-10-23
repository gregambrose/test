<?php
/**
* @ iDezender 8.0
* @ Developed by Qarizma
*
* @    Visit our website:
* @    www.iRadikal.com
* @    For cheap decoding service :)
* @    And for the ionCube Decoder!
*/          

	require_once( '../include/startup.php' );
	require_once( UTIL_PATH . 'UXML.class.php' );
	require_once( UTIL_PATH . 'UXMLTag.class.php' );
	require_once( UTIL_PATH . 'UPDF.class.php' );
	require_once( UTIL_PATH . 'UPDFXML.class.php' );
	$pdf = new UPDF(  );
	$xmlText = file_get_contents( 'test3.xml' );
	$template = new UTemplate( null );
	$template->setForHTML( 'addressEtc', '
Jones & Co.
Solicitors
Second Floor
Empire House
5 Cambridge Road
Harrow
UB5 7SV

SJD/2008561/5009533

lst October 2005
' );
	$template->setForHTML( 'docmName', 'DEBIT NOTE' );
	$template->setHTMLFromText( $xmlText );
	$template->parseAll(  );
	$newXMLText = $template->getOutput(  );
	$xml = new UPDFXML( $newXMLText, $pdf );
	$pdf->close(  );
	$pdf->output(  );
	exit(  );
?>