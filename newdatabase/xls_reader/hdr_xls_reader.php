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

	function unix2excel($time = false) {
		$UNIX_Start = 25569.125;
		$unix = ($time ? $time : time(  ));
		$days = floor( $unix / 86400 );
		$seconds = $unix - $days * 86400;
		$excel = $days + $UNIX_Start + round( 999999 / 86400 * $seconds ) * 9.99999999999999954748112e-7;
		return $excel;
	}

	function get_long_from_string($string) {
		return ord( $string[0] ) + 256 * ( ord( $string[1] ) + 256 * ( ord( $string[2] ) + 256 * ord( $string[3] ) ) );
	}

	function getunicodestring($str, $ofs, $length = false) {
		if ($length) {
			if ($length == 0 - 1) {
				$length = ord( $str[$ofs] );
				$alter = unicode2ascii( substr( $str, $ofs + 3, $length ) );
				return $alter;
			}

			$bstring = '';
			$index = $ofs + 1;
			$i = 0;

			while ($i < $length) {
				$bstring = $bstring . $str[$index];
				$index += 2;
				++$i;
			}

			return substr( $bstring, 0, $length );
		}

		$size = 0;
		$i_ofs = 0;
		$size = ord( $str[$ofs] );
		$i_ofs = 1;
		$alter = substr( $str, $ofs + $i_ofs + 1, $size );
		$alter = unicode2ascii( $alter );
		return $alter;
	}

	function getbytestring($str, $ofs) {
		$size = 0;
		$i_ofs = 0;
		$size = ord( $str[$ofs] );
		$i_ofs = 1;
		return substr( $str, $ofs + $i_ofs + 1, $size );
	}

	function unicode2ascii($str) {
		$stack = '';
		$i = 0;

		while ($i < strlen( $str )) {
			$c_char = $str[$i];
			$asci = ord( $c_char );

			if ($asci == 0) {
				continue;
			}

			$ok = true;

			if ($asci <= 32) {
				$ok = false;
			}


			if (126 < $asci) {
				$ok = false;
			}


			if ($asci == 8) {
				$ok = true;
			}


			if ($asci == 10) {
				$ok = true;
			}


			if ($asci == 13) {
				$ok = true;
			}


			if ($ok == false) {
				$c_char = ' ';
			}

			$stack .= $c_char;
			++$i;
		}

		return $stack;
	}

	function unicode2html($str) {
		$stack = '';
		$i = 0;

		while ($i < strlen( $str ) / 2) {
			$charcode = ord( $str[$i * 2] ) + 256 * ord( $str[$i * 2 + 1] );

			if ($charcode == 0) {
				$cur = substr( $str, $i * 2, 2 );
				$stack .= $cur;
			} 
else {
				$stack .= '&#' . $charcode . ';';
			}

			++$i;
		}

		return $stack;
	}

	define( 'EXCEL_BLOCK_SIZE', 512 );
	define( 'THIS_BLOCK_SIZE', 128 );
	define( 'RCRD_BOF', 2057 );
	define( 'RCRD_BOUNDSHEET', 133 );
	define( 'RCRD_CONTINUE', 60 );
	define( 'RCRD_DATEMODE', 34 );
	define( 'RCRD_EOF', 10 );
	define( 'RCRD_EXTERNCOUNT', 22 );
	define( 'RCRD_EXTERNSHEET', 23 );
	define( 'RCRD_SST', 252 );
	define( 'RCRD_STYLE', 659 );
	define( 'RCRD_WINDOW1', 61 );
	define( 'RCRD_WINDOW2', 574 );
	define( 'RCRD_BLANK', 513 );
	define( 'RCRD_BOTTOMMARGIN', 41 );
	define( 'RCRD_COLINFO', 125 );
	define( 'RCRD_DEFCOLWIDTH', 85 );
	define( 'RCRD_DIMENSIONS', 512 );
	define( 'RCRD_FOOTER', 21 );
	define( 'RCRD_FORMAT', 1054 );
	define( 'RCRD_FORMULA', 6 );
	define( 'RCRD_HCENTER', 131 );
	define( 'RCRD_HEADER', 20 );
	define( 'RCRD_HLINK', 440 );
	define( 'RCRD_LABEL', 516 );
	define( 'RCRD_LABELSST', 253 );
	define( 'RCRD_LEFTMARGIN', 38 );
	define( 'RCRD_MERGEDCELLS', 229 );
	define( 'RCRD_MULRK', 189 );
	define( 'RCRD_NAME', 24 );
	define( 'RCRD_NUMBER', 515 );
	define( 'RCRD_PALETTE', 146 );
	define( 'RCRD_PASSWORD', 19 );
	define( 'RCRD_PRINTGRIDLINES', 43 );
	define( 'RCRD_PRINTHEADERS', 42 );
	define( 'RCRD_PROTECT', 18 );
	define( 'RCRD_RIGHTMARGIN', 39 );
	define( 'RCRD_RK', 638 );
	define( 'RCRD_ROW', 520 );
	define( 'RCRD_SCL', 160 );
	define( 'RCRD_SELECTION', 29 );
	define( 'RCRD_SETUP', 161 );
	define( 'RCRD_STRING', 519 );
	define( 'RCRD_TOPMARGIN', 40 );
	define( 'RCRD_VCENTER', 132 );
	define( 'RCRD_WSBOOL', 129 );
	define( 'RCRD_FONT', 49 );
	define( 'RCRD_XF', 224 );
	define( 'XF_SCRIPT_NONE', 0 );
	define( 'XF_SCRIPT_SUP', 1 );
	define( 'XF_SCRIPT_SUB', 2 );
	define( 'XF_UL_NONE', 0 );
	define( 'XF_UL_SINGLE', 1 );
	define( 'XF_UL_DOUBLE', 2 );
	define( 'XF_UL_SINGLE_ACC', 3 );
	define( 'XF_UL_DOUBLE_ACC', 4 );
	define( 'XF_STYLE_ITALIC', 2 );
	define( 'XF_STYLE_STRIKEOUT', 8 );
	define( 'XF_WGHT_REGULAR', 400 );
	define( 'XF_WGHT_BOLD', 700 );
?>