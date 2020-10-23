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

	function _processfile($file) {
		$zipFile = INPUT_OO_DIR . $file;

		if (!file_exists( $zipFile )) {
			echo '' . $file . ' doesnt exist<br>
';
			return null;
		}

		$zip = zip_open( $zipFile );

		if (!$zip) {
			echo '' . ' ' . $file . ' cant open zip file<br>
';
			return null;
		}

		$wasDone = false;

		while ($zip_entry = zip_read( $zip )) {
			if (zip_entry_name( $zip_entry ) != 'content.xml') {
				continue;
			}


			if (!zip_entry_open( $zip, $zip_entry, 'r' )) {
				echo '' . 'cant get xml content for ' . $file . '
<br>';
				continue;
			}

			$buf = zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
			zip_entry_close( $zip_entry );
			_processxmlcontents( $file, $buf );
			$wasDone = true;
		}


		if ($wasDone == false) {
			echo '' . ' ' . $file . ' no contents.xml file<br>
';
		}

		zip_close( $zip );
	}

	function _processxmlcontents($file, $buffer) {
		global $docmDetails;

		$docmDetails = array(  );
		$par = xml_parser_create(  );
		xml_set_element_handler( $par, 'startElement', 'endElement' );
		xml_set_character_data_handler( $par, 'characterData' );
		$ok = xml_parse( $par, $buffer, true );

		if ($ok == false) {
			exit( xml_error_string( xml_get_error_code( $par ) ) );
		}

		xml_parser_free( $par );
		_outputrawcsv( $file, $docmDetails );

		if (_isrenewalnotice( $docmDetails ) == false) {
			echo '' . ' ' . $file . ' not renewal notice<br>
';
			return null;
		}

	}

	function _outputstructuredcsv($file, $docmDetails) {
		global $fdStructured;

		$docmDetails = _mergelines( $docmDetails );
		reset( $docmDetails );
		$elements = count( $docmDetails );
		$current = 0;
		$fields = array(  );
		$renewalDate = null;
		$i = 0;

		while ($i < 50) {
			$fields[$i] = '';
			++$i;
		}

		$fld = 0;
		foreach ($docmDetails as $item) {
			if (strcasecmp( trim( $item ), 'by instalments' ) == 0) {
				$fields[48] = 'BY INSTALMENTS';
				continue;
			}

			$tmp = addslashes( $item );
			$fields[$fld++] = $tmp;
			$found = preg_match( '/^\d{1,2}\/\d{1,2}\/\d{2,4}/', $tmp );

			if ($found == true) {
				$renewalDate = $tmp;
				continue;
			}
		}


		if ($renewalDate != null) {
			$fields[49] = $renewalDate;
		}

		$pc = _findpostcode( $fields );

		if ($pc < 0) {
			echo '' . $file . ' no post code<br>
';
			return null;
		}

		$text = $fields[$pc];
		$text = ( ( '' . '"' ) . $file . '"' );
		$fld1 = $fields[0];
		$fld2 = $fields[1];
		$fld = $fld1 . ' ' . $fld2;
		$coFound = false;

		if (strpos( $fld, 'Ltd' ) !== false) {
			$coFound = true;
		}


		if (strpos( $fld, 'PLC' ) !== false) {
			$coFound = true;
		}


		if ($coFound == true) {
			$text .= ',"C"';
		} 
else {
			$text .= ',"R"';
		}

		$i = 0;
		$pcFoundYet = false;
		$rnFoundYet = false;
		foreach ($fields as $item) {
			if (trim( $item ) == 'Premium') {
				$rnFoundYet = true;
			}


			if (( $pcFoundYet == true && $rnFoundYet == false )) {
				continue;
			}


			if ($i == $pc) {
				$x = $i;

				while ($x < 10) {
					$text .= ',""';
					++$x;
				}

				$parts = _breakpostcode( $item );
				$text .= ( '' . ',"' . $parts['0'] . '"' );
				$item = $parts[1];
				$pcFoundYet = true;
			}

			++$i;

			if (trim( $item ) == 'Premium') {
				$rnFoundYet = true;
				continue;
			}

			$text .= ( '' . ',"' . $item . '"' );
		}

		$text .= '
';
		fwrite( $fdStructured, $text );
	}

	function _findpostcode($fields) {
		reset( $fields );
		$found = false;
		$fld = 0 - 1;
		foreach ($fields as $item) {
			++$fld;

			if ($fld < 2) {
				continue;
			}


			if (12 < $fld) {
				break;
			}

			$item = trim( $item );
			$len = strlen( $item );

			if (3 <= $len) {
				$ok = false;

				if (strpos( 'HA2', $item ) !== false) {
					$ok = true;
				}


				if (strpos( 'HA4', $item ) !== false) {
					$ok = true;
				}


				if (strpos( 'HA5', $item ) !== false) {
					$ok = true;
				}


				if ($ok == true) {
					$ok = true;
					$nums = 1;
					break;
				}
			}

			$parts = _breakpostcode( $item );
			$item = $parts[1];
			$x = explode( ' ', $item );
			$num = count( $x );
			$y = array(  );
			$e = $num - 2;

			if (isset( $x[$e] )) {
				$y[] = $x[$e];
			}

			$e = $num - 1;

			if (isset( $x[$e] )) {
				$y[] = $x[$e];
			}

			$item = implode( ' ', $y );

			if (strlen( $item ) < 2) {
				continue;
			}


			if (10 <= strlen( $item )) {
				continue;
			}

			$len = strlen( $item );
			$ok = true;
			$nums = 0;
			$i = 0;

			while ($i < $len) {
				$char = substr( $item, $i, 1 );

				if ($char == ' ') {
					continue;
				}


				if (( $i == 0 && ctype_upper( $char ) == false )) {
					$ok = false;
					break;
				}


				if (ctype_upper( $char ) == true) {
					continue;
				}


				if (is_numeric( $char ) == true) {
					++$nums;
					continue;
				}

				$ok = false;
				break;
				++$i;
			}


			if (( $ok == false || $nums == 0 )) {
				continue;
			}

			$found = true;
			break;
		}


		if ($found == true) {
			return $fld;
		}

		return 0 - 1;
	}

	function _breakpostcode($item) {
		$parts = array(  );
		$x = explode( ' ', $item );
		$num = count( $x );
		$y = array(  );
		$e = $num - 2;

		if (isset( $x[$e] )) {
			$y[] = $x[$e];
		}

		$e = $num - 1;

		if (isset( $x[$e] )) {
			$y[] = $x[$e];
		}

		$parts[1] = implode( ' ', $y );
		$z = array(  );
		$i = 0;

		while ($i < $num - 2) {
			$z[] = $x[$i];
			++$i;
		}

		$z = implode( ' ', $z );
		$parts[0] = $z;
		return $parts;
	}

	function _outputrawcsv($file, $docmDetails) {
		global $fdRaw;

		$text = ( ( '' . '"' ) . $file . '"' );
		foreach ($docmDetails as $item) {
			$tmp = addslashes( $item );
			$text .= ( '' . ',"' . $tmp . '"' );
		}

		$text .= '
';
		fwrite( $fdRaw, $text );
	}

	function _isrenewalnotice($docmDetails) {
		$found = false;
		foreach ($docmDetails as $item) {
			if (strstr( 'RENEWAL NOTICE', $item )) {
				$found = true;
				break;
			}


			if (strstr( 'RENEWAL NOTE', $item )) {
				$found = true;
				break;
			}


			if (strstr( 'DEBIT NOTE', $item )) {
				$found = true;
				break;
			}
		}

		return $found;
	}

	function _mergelines($docmDetails) {
		$newDetails = $docmDetails;
		$items = count( $docmDetails );
		$elem = 0;

		while ($elem < $items) {
			$item = $docmDetails[$elem];

			if (_isnormalfield( $item ) == true) {
				$item .= '|';
				$newDetails[$elem] = $item;
				continue;
			}


			if (0 < $elem) {
				$tmp = $elem - 1;
				$item2 = $newDetails[$tmp];

				if (substr( $item2, 0 - 1 ) == '|') {
					$item2 = str_replace( '|', ' ', $item2 );
				}

				$newDetails[$tmp] = $item2;
			}

			++$elem;
		}

		$text = implode( '', $newDetails );
		$newDetails = explode( '|', $text );
		return $newDetails;
	}

	function _isnormalfield($fld) {
		$normal = true;
		$fld = trim( $fld );

		if ($fld == 'th') {
			$normal = false;
		}


		if ($fld == 'h') {
			$normal = false;
		}


		if ($fld == 'rd') {
			$normal = false;
		}


		if ($fld == 'nd') {
			$normal = false;
		}


		if ($fld == 'st') {
			$normal = false;
		}


		if ($fld == '&') {
			$normal = false;
		}


		if ($fld == '\'') {
			$normal = false;
		}

		return $normal;
	}

	function startelement($parser, $name, $attrs) {
		global $elementName;

		$elementName = $name;
	}

	function endelement($parser, $name) {
	}

	function characterdata($parser, $value) {
		global $docmDetails;
		global $elementName;

		$docmDetails[] = $value;
	}

	global $fdRaw;
	global $fdStructured;

	define( 'INPUT_OO_DIR', $_SERVER['DOCUMENT_ROOT'] . 'clients/jonathan/accounts/docmImport/OODocms/' );
	define( 'RAW_OUTPUT_CSV', 'output/RAW.csv' );
	$fdRaw = fopen( RAW_OUTPUT_CSV, 'w' );

	if ($fdRaw == null) {
		exit( 'cant create raw output' );
	}

	$dir = opendir( INPUT_OO_DIR );

	if ($dir == null) {
		exit( 'cant open input dir ' . INPUT_OO_DIR );
	}

	$totalRead = 0;

	while ($file = readdir( $dir )) {
		if (strlen( $file ) < 4) {
			continue;
		}

		++$totalRead;
		_processfile( $file );
	}

	closedir( $dir );
	fclose( $fdRaw );
	echo '' . 'Total Read = ' . $totalRead . '<br>
';
	echo 'Done';
?>