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

	$host = 'localhost';
	$user = 'dbuser';
	$password = 'lx1CJNdQnUCEyXjx1q32';
	$db = 'dmc';
	$ok = mysql_pconnect( $host, $user, $password );

	if ($ok == false) {
		echo 'cant connect';
	}

	$ok = mysql_select_db( $db );

	if ($ok == false) {
		echo 'cant select db';
	}


	if ($ok == false) {
		echo 'failure!';
	} 
else {
		echo 'must be connected';
	}

	echo '	
';
?>