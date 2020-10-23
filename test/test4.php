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
	$pdf->setProtection( array( 'print' ), 'greg', 'admin' );
	$pdf->addImage( IMAGES_PATH . 'docmhead.jpg', 14, 12, 185, 33 );
	$pdf->addImage( IMAGES_PATH . 'docmfooter.jpg', 25, 250, 155, 35 );
	$pdf->drawLine( 1, 48, 600, 48 );
	$text = '	
Jones & Co.
Solicitors
Second Floor
Empire House
5 Cambridge Road
Harrow
UB5 7SV

SJD/2008561/5009533
lst October 2005
';
	$pdf->addCell( 17, 53, 172, 4, $text, 0 );
	$pdf->newLine( 4 );
	$text = 'DEBIT NOTE';
	$pdf->setFontSize( 15 );
	$pdf->addCell( 84, 110, 172, 4, $text, 0 );
	$pdf->setFontSize( 11 );
	$text = 'We have arranged this insurance according to your instructions and would ask that you now let us have
payment of the premium shown (cheques made payable to ourselves). If payment is by instalments no
action need be taken.
';
	$pdf->setFontSize( 11 );
	$pdf->addCell( 17, 120, 176, 4, $text, 0 );
	$pdf->setFontSize( 11 );
	$left = 17;
	$pdf->startBox( $left, 140, 173, 0.40000000000000002220446, 0.5, 0.5 );
	$pdf->setFontSize( 11 );
	$pdf->setTab( 0, $left );
	$pdf->setTab( 1, $left + 43 );
	$pdf->setTab( 2, $left + 83 );
	$pdf->setTab( 3, $left + 130 );
	$pdf->setTab( 4, $left + 175 );
	$pdf->startColumnInBox( 0 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'Company' );
	$pdf->newLine( 4 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'Royal & Sun Alliance at Hiscox.' );
	$pdf->endColumnInBox( 0 );
	$pdf->startColumnInBox( 1 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'Policy No.' );
	$pdf->newLine( 4 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'P17324566/3342445255' );
	$pdf->newLine( 4 );
	$pdf->endColumnInBox( 1 );
	$pdf->startColumnInBox( 2 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'Effective Date' );
	$pdf->newLine( 4 );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'From:  01.11.05' );
	$pdf->newLine( 4 );
	$pdf->write( 4, 'To:       01.10.06' );
	$pdf->newLine( 4 );
	$pdf->endColumnInBox( 2 );
	$pdf->startColumnInBox( 3 );
	$pdf->newLine( 4 );
	$pdf->write( 4, '          ' );
	$pdf->write( 4, 'Premium' );
	$pdf->newLine( 4 );
	$pdf->newLine( 4 );
	$pdf->write( 4, '          ' );
	$pdf->write( 4, '£5198.29' );
	$pdf->newLine( 4 );
	$pdf->write( 4, '          ' );
	$pdf->setFontStyle( 'UI' );
	$pdf->write( 4, 'BY INSTALMENT' );
	$pdf->setFontStyle( 'B' );
	$pdf->write( 4, 'Fees: £8000.00' );
	$pdf->newLine( 4 );
	$pdf->newLine( 4 );
	$pdf->endColumnInBox( 3 );
	$pdf->endBox(  );
	$pdf->newLine( 7 );
	$pdf->setTab( 0, $left );
	$pdf->setTab( 1, $left + 55 );
	$pdf->setTab( 2, $left + 160 );
	$pdf->setFontSize( 12 );
	$text = 'Policy Type:';
	$pdf->writeAtTab( 0, 3, $text );
	$text = 'PROFESSIONAL INDEMNITY INSURANCE';
	$pdf->writeAtTab( 1, 3, $text );
	$pdf->newLine( 10 );
	$text = 'Transaction Detail:';
	$pdf->writeAtTab( 0, 3, $text );
	$text = 'First Premium for full partner Professional Indemnity:';
	$pdf->writeAtTab( 1, 3, $text );
	$pdf->newLine( 4 );
	$text = 'Insurance from 15.11.05 to 01.10.06';
	$pdf->writeAtTab( 1, 3, $text );
	$pdf->newLine( 4 );
	$text = '*Due to Dolden Martin & Co. by cheque';
	$pdf->writeAtTab( 1, 3, $text );
	$pdf->newLine( 10 );
	$text = 'Description of Cover:';
	$pdf->writeAtTab( 0, 3, $text );
	$pdf->setLeftMargin( $left + 65 );
	$pdf->setRightMargin( 25 );
	$text = 'Professional Indemnity Insurance against claims for loss or damages by third parties arising out work of partners.';
	$pdf->writeAtTab( 1, 4, $text );
	$pdf->newLine( 5 );
	$pdf->setLeftMargin( 0 );
	$pdf->setRightMargin( 0 );
	$text = '(The premium shown includes Government Insurance Premium Tax)';
	$pdf->setFontSize( 13 );
	$pdf->addCell( 19, 225, 176, 4, $text, 0 );
	$pdf->setFontSize( 11 );
	$left = 17;
	$pdf->output(  );
	$pdf->close(  );
	exit(  );
?>