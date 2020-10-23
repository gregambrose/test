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

	define( 'FPDF_FONTPATH', './' );
	require( '../fpdf.php' );
	$pdf = new FPDF(  );
	$pdf->AddFont( 'Calligrapher', '', 'calligra.php' );
	$pdf->AddPage(  );
	$pdf->SetFont( 'Calligrapher', '', 35 );
	$pdf->Cell( 0, 10, 'Enjoy new fonts with FPDF!' );
	$pdf->Output(  );
?>