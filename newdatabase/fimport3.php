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

	function doprocess($handle, $output) {
		$done = 0;
		$docs = 0;

		while (true) {
			$data = fgetcsv( $handle, 9000, ',' );

			if ($data === false) {
				echo 'end of file';
				break;
			}


			if (( $data == '' || $data[0] == '' )) {
				continue;
			}

			++$docs;
			processrow( $data, $output );
			++$done;
		}

		fclose( $output );
	}

	function processrow($data, $output) {
		global $owners;

		$data = _fixdata( $data );
		$docmName = $data[0];

		if (isset( $owners[$docmName] )) {
			$data[18] = 'C';
			$data[20] = '2';
		}

		_writerow( $data, $output );
	}

	function _writerow($data, $output) {
		$out = _makeline( $data );
		$len = strlen( $out );

		if ($len == 0) {
			return null;
		}

		fwrite( $output, $out, $len );
	}

	function _makeline($data) {
		$num = count( $data );
		$out = '';
		$i = 0;

		while ($i < $num) {
			if (0 < $i) {
				$out .= ',';
			}

			$text = $data[$i];
			$quotes = '"';

			if (strlen( $text ) == 0) {
				$quotes = '';
			}

			$out .= $quotes . $data[$i] . $quotes;
			++$i;
		}

		$out .= '
';
		return $out;
	}

	function _makeownersarray() {
		global $owners;

		$owners = array(  );
		$handle = fopen( 'imports/OWNERS.csv', 'r' );
		$done = 0;

		while (true) {
			$data = fgetcsv( $handle, 9000, ',' );

			if ($data === false) {
				echo 'end of file';
				break;
			}


			if ($done++ == 0) {
				continue;
			}

			$owners[$data[0]] = $data[1];
		}

	}

	function _fixdata($data) {
		$new = array(  );
		$i = 0;

		while ($i < 100) {
			if (isset( $data[$i] )) {
				$new[$i] = $data[$i];
			} 
else {
				$new[$i] = '';
			}

			$fld = $new[$i];

			if (substr( $fld, 0, 1 ) == '"') {
				$fld = substr( $fld, 1 );
			}

			$len = strlen( $fld );

			if (( 0 < $len && substr( $fld, $len - 1, 1 ) == '"' )) {
				$fld = substr( $fld, 0, $len - 1 );
			}

			$new[$i] = $fld;
			++$i;
		}

		return $new;
	}

	ini_set( 'memory_limit', '32M' );
	require( '../include/startup.php' );
	echo 'Starting';
	_makeownersarray(  );
	$handle = fopen( 'imports/structured.csv', 'r' );
	$output = fopen( 'imports/structured2.csv', 'w' );
	doprocess( $handle, $output );
	print 'Done';
?>