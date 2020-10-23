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

	echo 'Hi 2';
	$host = 'localhost';
	$user = 'greg';
	$password = 'chezery2';
	echo 'trying db open';
	$a = mysql_connect( $host, $user, $password );
	echo 'db open ok ';
	require( '../include/startup.php' );
	echo ' after startup';
?>