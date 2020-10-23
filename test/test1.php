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
	require_once( UTIL_PATH . 'UPDF.class.php' );
	$pdf = new UPDF(  );
	$pdf->SetProtection( array( 'print' ), 'greg', 'admin' );
	$xml = file_get_contents( 'test.xml' );
	$pdf->pdf->addXML( $xml );
	$pdf->Output(  );
?>