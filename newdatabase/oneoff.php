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

	function _doquery($text, $q) {
		$result = mysql_query( $q );

		if ($result === true) {
			print '' . 'OK ' . $text . '  <br>';

			if (substr( $q, 0, 6 ) == 'INSERT') {
				echo 'insert value was ' . mysql_insert_id(  );
				return null;
			}
		} 
else {
			$err = mysql_error(  );
			print '' . 'FAILED  ' . $text . ' : error was ' . $err . ' <br>';
		}

	}

	require_once( '../include/startup.php' );
	$q = 'update cashBatches set btBatchDate=\'2006-03-02\' where btCode= 8000059';
	_doquery( 'btBatchDate	8000059', $q );
	$q = 'update cashBatches set btBatchDate=\'2006-03-02\' where btCode= 8000060';
	_doquery( 'btBatchDate	8000060', $q );
	echo 'DONE';
?>