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

	$startWithoutTables = true;
	require( '../include/startup.php' );
	$mysqlTemplate = &$session->get( 'mysqlTemplate' );

	if ($mysqlTemplate == null) {
		$mysqlTemplate = new MysqlTemplate( 'mysql.html' );
		$mysqlTemplate->setProcess( 'handleRequest', 'request' );
	}

	$session->set( 'mysqlTemplate', $mysqlTemplate );
	$mysqlTemplate->process(  );
	$session->set( 'mysqlTemplate', $mysqlTemplate );
?>