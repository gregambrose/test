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

	function _strspn($str1, $str2, $start = null, $length = null) {
		$numargs = func_num_args(  );

		if (PHP_VER_LOWER43 == 1) {
			if (isset( $length )) {
				$str1 = substr( $str1, $start, $length );
			} 
else {
				$str1 = substr( $str1, $start );
			}
		}


		if (( $numargs == 2 || PHP_VER_LOWER43 == 1 )) {
			return strspn( $str1, $str2 );
		}


		if ($numargs == 3) {
			return strspn( $str1, $str2, $start );
		}

		return strspn( $str1, $str2, $start, $length );
	}

	function _strcspn($str1, $str2, $start = null, $length = null) {
		$numargs = func_num_args(  );

		if (PHP_VER_LOWER43 == 1) {
			if (isset( $length )) {
				$str1 = substr( $str1, $start, $length );
			} 
else {
				$str1 = substr( $str1, $start );
			}
		}


		if (( $numargs == 2 || PHP_VER_LOWER43 == 1 )) {
			return strcspn( $str1, $str2 );
		}


		if ($numargs == 3) {
			return strcspn( $str1, $str2, $start );
		}

		return strcspn( $str1, $str2, $start, $length );
	}

	function _fgets($h, $force = false) {
		$startpos = ftell( $h );
		$s = fgets( $h, 1024 );

		if (( ( PHP_VER_LOWER43 == 1 || $force ) && preg_match( '/^([^
]*[
]{1,2})(.)/', trim( $s ), $ns ) )) {
			$s = $ns[1];
			fseek( $h, $startpos + strlen( $s ) );
		}

		return $s;
	}


	if (!defined( 'PHP_VER_LOWER43' )) {
		define( 'PHP_VER_LOWER43', version_compare( PHP_VERSION, '4.3', '<' ) );
	}

?>