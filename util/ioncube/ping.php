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

	$cmd = $_GET['cmd'];

	if ($cmd == 'ping') {
		echo 'pong';
	} 
else {
		if ($cmd == 'run') {
			echo 'execution begun';
		} 
else {
			echo  . 'Unknown command : ' . $cmd;
		}
	}

?>