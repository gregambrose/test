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

	function doimport($handle) {
		while (true) {
			$data = fgetcsv( $handle, 7000, ',' );

			if ($data == null) {
				break;
			}


			if (( $data == '' || $data[0] == '' )) {
				continue;
			}


			if ($data[0] == 'HEADING') {
				$headings = makeheading( $data );
				continue;
			}

			insertdata( $headings, $data );
		}

	}

	function insertdata($headings, $data) {
		$table = $data[0];
		$q = '' . 'INSERT INTO ' . $table . ' (';
		$num = sizeof( $headings );
		$firstDone = false;
		$elem = 0;

		while ($elem < $num) {
			if (strlen( trim( $data[$elem + 1] ) ) == 0) {
				continue;
			}


			if ($firstDone == true) {
				$q .= ',';
			}

			$firstDone = true;
			$q .= $headings[$elem];
			++$elem;
		}

		$q .= ') VALUES (';
		$firstDone = false;
		$elem = 1;

		while ($elem <= $num) {
			if (strlen( trim( $data[$elem] ) ) == 0) {
				continue;
			}


			if ($firstDone == true) {
				$q .= ',';
			}

			$firstDone = true;
			$fld = $data[$elem];
			$fld = fixstring( $fld );
			$q .= '\'';
			$q .= $fld;
			$q .= '\'';
			++$elem;
		}

		$q .= ')';
		$result = @mysql_query( $q );

		if ($result == false) {
			print '' . 'Error With: ' . $q . ' ERROR WAS ' . mysql_error(  );
			return null;
		}

		print '' . 'add to table ' . $table . '<br>\n';
	}

	function makeheading($data) {
		$headings = array(  );
		$num = sizeof( $data );
		$x = $num - 1;

		while (0 <= $x) {
			if (trim( $data[$x] ) != '') {
				break;
			}

			--$x;
		}

		$num = $x + 1;
		$elem = 1;

		while ($elem < $num) {
			$col = trim( $data[$elem] );
			$headings[] = $col;
			++$elem;
		}

		return $headings;
	}

	function fixstring($in) {
		$num = preg_match( '' . '/^-?\d*\.\d*$/', $in );

		if ($num == 1) {
			$in = round( $in * 100, 0 );
		}

		$x = addslashes( $in );
		$out = trim( $x );
		return $out;
	}

	require( '../include/startup.php' );
	echo 'Starting';
	$handle = fopen( 'imports/IMPORTS.csv', 'r' );
	doimport( $handle );
	print 'Done';
?>