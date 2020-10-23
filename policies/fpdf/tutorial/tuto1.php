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

	require( '../fpdf.php' );
	$pdf = new FPDF(  );
	$pdf->AddPage(  );
	$pdf->SetFont( 'Arial', 'B', 16 );
	$pdf->Cell( 40, 10, 'Hello World!' );
	$pdf->Output(  );
?>