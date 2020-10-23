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

	class combinedpdf {
		var $encrypted = null;
		var $Uvalue = null;
		var $Ovalue = null;
		var $Pvalue = null;
		var $enc_obj_id = null;
		var $last_rc4_key = null;
		var $last_rc4_key_c = null;

		function combinedpdf($orientation = 'P', $unit = 'mm', $format = 'A4') {
			fpdi::fpdf( $orientation, $unit, $format );
			$this->encrypted = false;
			$this->last_rc4_key = '';
			$this->padding = '(¿N^NuŠAd' . '..';
		}

		function setprotection($permissions = array(  ), $user_pass = '', $owner_pass = null) {
			$options = array( 'print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
			$protection = 192;
			foreach ($permissions as $permission) {
				if (!isset( $options[$permission] )) {
					$this->Error( 'Incorrect permission: ' . $permission );
				}

				$protection += $options[$permission];
			}


			if ($owner_pass === null) {
				$owner_pass = uniqid( rand(  ) );
			}

			$this->encrypted = true;
			$this->_generateencryptionkey( $user_pass, $owner_pass, $protection );
		}

		function _putstream($s) {
			if ($this->encrypted) {
				$s = $this->_RC4( $this->_objectkey( $this->n ), $s );
			}

			fpdi::_putstream( $s );
		}

		function _textstring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4( $this->_objectkey( $this->n ), $s );
			}

			return fpdi::_textstring( $s );
		}

		function _objectkey($n) {
			return substr( $this->_md5_16( $this->encryption_key . pack( 'VXxx', $n ) ), 0, 10 );
		}

		function _escape($s) {
			$s = str_replace( '\', '\\', $s );
			$s = str_replace( ')', '\)', $s );
			$s = str_replace( '(', '\(', $s );
			$s = str_replace( '', '\r', $s );
			return $s;
		}

		function _putresources() {
			fpdi::_putresources(  );

			if ($this->encrypted) {
				$this->_newobj(  );
				$this->enc_obj_id = $this->n;
				$this->_out( '<<' );
				$this->_putencryption(  );
				$this->_out( '>>' );
				$this->_out( 'endobj' );
			}

		}

		function _putencryption() {
			$this->_out( '/Filter /Standard' );
			$this->_out( '/V 1' );
			$this->_out( '/R 2' );
			$this->_out( '/O (' . $this->_escape( $this->Ovalue ) . ')' );
			$this->_out( '/U (' . $this->_escape( $this->Uvalue ) . ')' );
			$this->_out( '/P ' . $this->Pvalue );
		}

		function _puttrailer() {
			fpdi::_puttrailer(  );

			if ($this->encrypted) {
				$this->_out( '/Encrypt ' . $this->enc_obj_id . ' 0 R' );
				$this->_out( '/ID [()()]' );
			}

		}

		function _rc4($key, $text) {
			if ($this->last_rc4_key != $key) {
				$k = str_repeat( $key, 256 / strlen( $key ) + 1 );
				$rc4 = range( 0, 255 );
				$j = 0;
				$i = 0;

				while ($i < 256) {
					$t = $rc4[$i];
					$j = ( $j + $t + ord( $k[$i] ) ) % 256;
					$rc4[$i] = $rc4[$j];
					$rc4[$j] = $t;
					++$i;
				}

				$this->last_rc4_key = $key;
				$this->last_rc4_key_c = $rc4;
			} 
else {
				$rc4 = $this->last_rc4_key_c;
			}

			$len = strlen( $text );
			$a = 0;
			$b = 0;
			$out = '';
			$i = 0;

			while ($i < $len) {
				$a = ( $a + 1 ) % 256;
				$t = $rc4[$a];
				$b = ( $b + $t ) % 256;
				$rc4[$a] = $rc4[$b];
				$rc4[$b] = $t;
				$k = $rc4[( $rc4[$a] + $rc4[$b] ) % 256];
				$out .= chr( ord( $text[$i] ) ^ $k );
				++$i;
			}

			return $out;
		}

		function _md5_16($string) {
			return pack( 'H*', md5( $string ) );
		}

		function _ovalue($user_pass, $owner_pass) {
			$tmp = $this->_md5_16( $owner_pass );
			$owner_RC4_key = substr( $tmp, 0, 5 );
			return $this->_RC4( $owner_RC4_key, $user_pass );
		}

		function _uvalue() {
			return $this->_RC4( $this->encryption_key, $this->padding );
		}

		function _generateencryptionkey($user_pass, $owner_pass, $protection) {
			$user_pass = substr( $user_pass . $this->padding, 0, 32 );
			$owner_pass = substr( $owner_pass . $this->padding, 0, 32 );
			$this->Ovalue = $this->_Ovalue( $user_pass, $owner_pass );
			$tmp = $this->_md5_16( $user_pass . $this->Ovalue . chr( $protection ) . 'ÿÿÿ' );
			$this->encryption_key = substr( $tmp, 0, 5 );
			$this->Uvalue = $this->_Uvalue(  );
			$this->Pvalue = 0 - ( ( $protection ^ 255 ) + 1 );
		}
	}

	require_once( 'fpdf/fpdf.php' );
?>