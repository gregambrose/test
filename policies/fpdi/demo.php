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

	error_reporting( E_ALL );
	define( 'FPDF_FONTPATH', 'font/' );
	require( 'fpdi.php' );
	$pdf = new fpdi(  );
	$pagecount = $pdf->setSourceFile( 'pdfdoc.pdf' );
	$tplidx = $pdf->ImportPage( 1 );
	$pdf->addPage(  );
	$pdf->useTemplate( $tplidx, 10, 10, 90 );
	$pdf->Output( 'newpdf.pdf', 'F' );
	$pdf->closeParsers(  );
?>