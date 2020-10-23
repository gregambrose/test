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

	define( 'SIZE', 1000000 );
	$startWithoutTables = false;
	require( '../include/startup.php' );
	define( 'EMAIL_ON_ERROR', true );
	$q = 'DELETE FROM documents WHERE doCode = 999999';
	$result = udbquery( $q );
	$doc = new Document( null );
	$doc->insert( null );
	$s = 'abcdefghij';
	$out = '';
	$i = 0;

	while ($i < SIZE / 10) {
		$out .= $s;
		++$i;
	}

	$doc->addDocumentUsingText( 'temp', 'text', $out );
	$doc->update(  );
	echo 'done';
?>