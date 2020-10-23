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

	function readmap($enc) {
		$file = dirname( __FILE__ ) . '/' . strtolower( $enc ) . '.map';
		$a = file( $file );

		if (empty( $a )) {
			exit( '<B>Error:</B> encoding not found: ' . $enc );
		}

		$cc2gn = array(  );
		foreach ($a as $l) {
			if ($l[0] == '!') {
				$e = preg_split( '/[ \t]+/', rtrim( $l ) );
				$cc = hexdec( substr( $e[0], 1 ) );
				$gn = $e[2];
				$cc2gn[$cc] = $gn;
				continue;
			}
		}

		$i = 0;

		while ($i <= 255) {
			if (!isset( $cc2gn[$i] )) {
				$cc2gn[$i] = '.notdef';
			}

			++$i;
		}

		return $cc2gn;
	}

	function readafm(&$file, $map) {
		$a = file( $file );

		if (empty( $a )) {
			exit( 'File not found' );
		}

		$widths = array(  );
		$fm = array(  );
		$fix = array( 'Edot' => 'Edotaccent', 'edot' => 'edotaccent', 'Idot' => 'Idotaccent', 'Zdot' => 'Zdotaccent', 'zdot' => 'zdotaccent', 'Odblacute' => 'Ohungarumlaut', 'odblacute' => 'ohungarumlaut', 'Udblacute' => 'Uhungarumlaut', 'udblacute' => 'uhungarumlaut', 'Gcedilla' => 'Gcommaaccent', 'gcedilla' => 'gcommaaccent', 'Kcedilla' => 'Kcommaaccent', 'kcedilla' => 'kcommaaccent', 'Lcedilla' => 'Lcommaaccent', 'lcedilla' => 'lcommaaccent', 'Ncedilla' => 'Ncommaaccent', 'ncedilla' => 'ncommaaccent', 'Rcedilla' => 'Rcommaaccent', 'rcedilla' => 'rcommaaccent', 'Scedilla' => 'Scommaaccent', 'scedilla' => 'scommaaccent', 'Tcedilla' => 'Tcommaaccent', 'tcedilla' => 'tcommaaccent', 'Dslash' => 'Dcroat', 'dslash' => 'dcroat', 'Dmacron' => 'Dcroat', 'dmacron' => 'dcroat', 'combininggraveaccent' => 'gravecomb', 'combininghookabove' => 'hookabovecomb', 'combiningtildeaccent' => 'tildecomb', 'combiningacuteaccent' => 'acutecomb', 'combiningdotbelow' => 'dotbelowcomb', 'dongsign' => 'dong' );
		foreach ($a as $l) {
			$e = explode( ' ', rtrim( $l ) );

			if (count( $e ) < 2) {
				continue;
			}

			$code = $e[0];
			$param = $e[1];

			if ($code == 'C') {
				$cc = (int)$e[1];
				$w = $e[4];
				$gn = $e[7];

				if (substr( $gn, 0 - 4 ) == '20AC') {
					$gn = 'Euro';
				}


				if (isset( $fix[$gn] )) {
					foreach ($map as $c => $n) {
						if ($n == $fix[$gn]) {
							$map[$c] = $gn;
							continue;
						}
					}
				}


				if (empty( $map )) {
					$widths[$cc] = $w;
				} 
else {
					$widths[$gn] = $w;

					if ($gn == 'X') {
						$fm['CapXHeight'] = $e[13];
					}
				}


				if ($gn == '.notdef') {
					$fm['MissingWidth'] = $w;
					continue;
				}

				continue;
			}


			if ($code == 'FontName') {
				$fm['FontName'] = $param;
				continue;
			}


			if ($code == 'Weight') {
				$fm['Weight'] = $param;
				continue;
			}


			if ($code == 'ItalicAngle') {
				$fm['ItalicAngle'] = (double)$param;
				continue;
			}


			if ($code == 'Ascender') {
				$fm['Ascender'] = (int)$param;
				continue;
			}


			if ($code == 'Descender') {
				$fm['Descender'] = (int)$param;
				continue;
			}


			if ($code == 'UnderlineThickness') {
				$fm['UnderlineThickness'] = (int)$param;
				continue;
			}


			if ($code == 'UnderlinePosition') {
				$fm['UnderlinePosition'] = (int)$param;
				continue;
			}


			if ($code == 'IsFixedPitch') {
				$fm['IsFixedPitch'] = $param == 'true';
				continue;
			}


			if ($code == 'FontBBox') {
				$fm['FontBBox'] = array( $e[1], $e[2], $e[3], $e[4] );
				continue;
			}


			if ($code == 'CapHeight') {
				$fm['CapHeight'] = (int)$param;
				continue;
			}


			if ($code == 'StdVW') {
				$fm['StdVW'] = (int)$param;
				continue;
			}
		}


		if (!isset( $fm['FontName'] )) {
			exit( 'FontName not found' );
		}


		if (!empty( $map )) {
			if (!isset( $widths['.notdef'] )) {
				$widths['.notdef'] = 600;
			}


			if (( !isset( $widths['Delta'] ) && isset( $widths['increment'] ) )) {
				$widths['Delta'] = $widths['increment'];
			}

			$i = 0;

			while ($i <= 255) {
				if (!isset( $widths[$map[$i]] )) {
					echo '<B>Warning:</B> character ' . $map[$i] . ' is missing<BR>';
					$widths[$i] = $widths['.notdef'];
				} 
else {
					$widths[$i] = $widths[$map[$i]];
				}

				++$i;
			}
		}

		$fm['Widths'] = $widths;
		return $fm;
	}

	function makefontdescriptor($fm, $symbolic) {
		$asc = (isset( $fm['Ascender'] ) ? $fm['Ascender'] : 1000);
		$fd = 'array(\'Ascent\'=>' . $asc;
		$desc = (isset( $fm['Descender'] ) ? $fm['Descender'] : 0 - 200);
		$fd .= ',\'Descent\'=>' . $desc;

		if (isset( $fm['CapHeight'] )) {
			$ch = $fm['CapHeight'];
		} 
else {
			if (isset( $fm['CapXHeight'] )) {
				$ch = $fm['CapXHeight'];
			} 
else {
				$ch = $asc;
			}
		}

		$fd .= ',\'CapHeight\'=>' . $ch;
		$flags = 0;

		if (( isset( $fm['IsFixedPitch'] ) && $fm['IsFixedPitch'] )) {
			$flags += 1 << 0;
		}


		if ($symbolic) {
			$flags += 1 << 2;
		}


		if (!$symbolic) {
			$flags += 1 << 5;
		}


		if (( isset( $fm['ItalicAngle'] ) && $fm['ItalicAngle'] != 0 )) {
			$flags += 1 << 6;
		}

		$fd .= ',\'Flags\'=>' . $flags;

		if (isset( $fm['FontBBox'] )) {
			$fbb = $fm['FontBBox'];
		} 
else {
			$fbb = array( 0, $des - 100, 1000, $asc + 100 );
		}

		$fd .= ',\'FontBBox\'=>\'[' . $fbb[0] . ' ' . $fbb[1] . ' ' . $fbb[2] . ' ' . $fbb[3] . ']\'';
		$ia = (isset( $fm['ItalicAngle'] ) ? $fm['ItalicAngle'] : 0);
		$fd .= ',\'ItalicAngle\'=>' . $ia;

		if (isset( $fm['StdVW'] )) {
			$stemv = $fm['StdVW'];
		} 
else {
			if (( isset( $fm['Weight'] ) && eregi( '(bold|black)', $fm['Weight'] ) )) {
				$stemv = 120;
			} 
else {
				$stemv = 70;
			}
		}

		$fd .= ',\'StemV\'=>' . $stemv;

		if (isset( $fm['MissingWidth'] )) {
			$fd .= ',\'MissingWidth\'=>' . $fm['MissingWidth'];
		}

		$fd .= ')';
		return $fd;
	}

	function makewidtharray($fm) {
		$s = 'array(
	';
		$cw = $fm['Widths'];
		$i = 0;

		while ($i <= 255) {
			if (chr( $i ) == '\'') {
				$s .= '\'\'\'';
			} 
else {
				if (chr( $i ) == '\') {
					$s .= '\'\\\'';
				} 
else {
					if (( 32 <= $i && $i <= 126 )) {
						$s .= '\'' . chr( $i ) . '\'';
					} 
else {
						$s .= '' . 'chr(' . $i . ')';
					}
				}
			}

			$s .= '=>' . $fm['Widths'][$i];

			if ($i < 255) {
				$s .= ',';
			}


			if (( $i + 1 ) % 22 == 0) {
				$s .= '
	';
			}

			++$i;
		}

		$s .= ')';
		return $s;
	}

	function makefontencoding($map) {
		$ref = readmap( 'cp1252' );
		$s = '';
		$last = 0;
		$i = 32;

		while ($i <= 255) {
			if ($map[$i] != $ref[$i]) {
				if ($i != $last + 1) {
					$s .= $i . ' ';
				}

				$last = $i;
				$s .= '/' . $map[$i] . ' ';
			}

			++$i;
		}

		return rtrim( $s );
	}

	function savetofile($file, $s, $mode = 't') {
		$f = fopen( $file, 'w' . $mode );

		if (!$f) {
			exit( 'Can\'t write to file ' . $file );
		}

		fwrite( $f, $s, strlen( $s ) );
		fclose( $f );
	}

	function readshort($f) {
		$a = unpack( 'n1n', fread( $f, 2 ) );
		return $a['n'];
	}

	function readlong($f) {
		$a = unpack( 'N1N', fread( $f, 4 ) );
		return $a['N'];
	}

	function checkttf($file) {
		$f = fopen( $file, 'rb' );

		if (!$f) {
			exit( '<B>Error:</B> Can\'t open ' . $file );
		}

		fseek( $f, 4, SEEK_CUR );
		$nb = readshort( $f );
		fseek( $f, 6, SEEK_CUR );
		$found = false;
		$i = 0;

		while ($i < $nb) {
			if (fread( $f, 4 ) == 'OS/2') {
				$found = true;
				break;
			}

			fseek( $f, 12, SEEK_CUR );
			++$i;
		}


		if (!$found) {
			fclose( $f );
			return null;
		}

		fseek( $f, 4, SEEK_CUR );
		$offset = readlong( $f );
		fseek( $f, $offset, SEEK_SET );
		fseek( $f, 8, SEEK_CUR );
		$fsType = readshort( $f );
		$rl = ( $fsType & 2 ) != 0;
		$pp = ( $fsType & 4 ) != 0;
		$e = ( $fsType & 8 ) != 0;
		fclose( $f );

		if (( ( $rl && !$pp ) && !$e )) {
			echo '<B>Warning:</B> font license does not allow embedding';
		}

	}

	function makefont($fontfile, $afmfile, $enc = 'cp1252', $patch = array(  ), $type = 'TrueType') {
		set_magic_quotes_runtime( 0 );
		ini_set( 'auto_detect_line_endings', '1' );

		if ($enc) {
			$map = readmap( $enc );
			foreach ($patch as $cc => $gn) {
				$map[$cc] = $gn;
			}
		} 
else {
			$map = array(  );
		}


		if (!file_exists( $afmfile )) {
			exit( '<B>Error:</B> AFM file not found: ' . $afmfile );
		}

		$fm = readafm( $afmfile, &$map );

		if ($enc) {
			$diff = makefontencoding( $map );
		} 
else {
			$diff = '';
		}

		$fd = makefontdescriptor( $fm, empty( $map ) );

		if ($fontfile) {
			$ext = strtolower( substr( $fontfile, 0 - 3 ) );

			if ($ext == 'ttf') {
				$type = 'TrueType';
			} 
else {
				if ($ext == 'pfb') {
					$type = 'Type1';
				} 
else {
					exit( '<B>Error:</B> unrecognized font file extension: ' . $ext );
				}
			}
		} 
else {
			if (( $type != 'TrueType' && $type != 'Type1' )) {
				exit( '<B>Error:</B> incorrect font type: ' . $type );
			}
		}

		$s = '<?php' . '
';
		$s .= '$type=\'' . $type . '\';
';
		$s .= '$name=\'' . $fm['FontName'] . '\';
';
		$s .= '$desc=' . $fd . ';
';

		if (!isset( $fm['UnderlinePosition'] )) {
			$fm['UnderlinePosition'] = 0 - 100;
		}


		if (!isset( $fm['UnderlineThickness'] )) {
			$fm['UnderlineThickness'] = 50;
		}

		$s .= '$up=' . $fm['UnderlinePosition'] . ';
';
		$s .= '$ut=' . $fm['UnderlineThickness'] . ';
';
		$w = makewidtharray( $fm );
		$s .= '$cw=' . $w . ';
';
		$s .= '$enc=\'' . $enc . '\';
';
		$s .= '$diff=\'' . $diff . '\';
';
		$basename = substr( basename( $afmfile ), 0, 0 - 4 );

		if ($fontfile) {
			if (!file_exists( $fontfile )) {
				exit( '<B>Error:</B> font file not found: ' . $fontfile );
			}


			if ($type == 'TrueType') {
				checkttf( $fontfile );
			}

			$f = fopen( $fontfile, 'rb' );

			if (!$f) {
				exit( '<B>Error:</B> Can\'t open ' . $fontfile );
			}

			$file = fread( $f, filesize( $fontfile ) );
			fclose( $f );

			if ($type == 'Type1') {
				$header = ord( $file[0] ) == 128;

				if ($header) {
					$file = substr( $file, 6 );
				}

				$pos = strpos( $file, 'eexec' );

				if (!$pos) {
					exit( '<B>Error:</B> font file does not seem to be valid Type1' );
				}

				$size1 = $pos + 6;

				if (( $header && ord( $file[$size1] ) == 128 )) {
					$file = substr( $file, 0, $size1 ) . substr( $file, $size1 + 6 );
				}

				$pos = strpos( $file, '00000000' );

				if (!$pos) {
					exit( '<B>Error:</B> font file does not seem to be valid Type1' );
				}

				$size2 = $pos - $size1;
				$file = substr( $file, 0, $size1 + $size2 );
			}


			if (function_exists( 'gzcompress' )) {
				$cmp = $basename . '.z';
				savetofile( $cmp, gzcompress( $file ), 'b' );
				$s .= '$file=\'' . $cmp . '\';
';
				echo 'Font file compressed (' . $cmp . ')<BR>';
			} 
else {
				$s .= '$file=\'' . basename( $fontfile ) . '\';
';
				echo '<B>Notice:</B> font file could not be compressed (zlib extension not available)<BR>';
			}


			if ($type == 'Type1') {
				$s .= '$size1=' . $size1 . ';
';
				$s .= '$size2=' . $size2 . ';
';
			} 
else {
				$s .= '$originalsize=' . filesize( $fontfile ) . ';
';
			}
		} 
else {
			$s .= '$file=' . '\'\';
';
		}

		$s .= '?>
';
		savetofile( $basename . '.php', $s );
		echo 'Font definition file generated (' . $basename . '.php' . ')<BR>';
	}

?>