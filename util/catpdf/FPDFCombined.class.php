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

	class fpdfcombined {
		var $encrypted = null;
		var $Uvalue = null;
		var $Ovalue = null;
		var $Pvalue = null;
		var $enc_obj_id = null;
		var $last_rc4_key = null;
		var $last_rc4_key_c = null;
		var $lineHeight = null;
		var $currentLine = null;
		var $tags = null;

		function fpdfcombined($orientation = 'P', $unit = 'mm', $format = 'A4') {
			fpdf::fpdf( $orientation, $unit, $format );
			$this->encrypted = false;
			$this->last_rc4_key = '';
			$this->padding = '(¿N^NuŠAd' . '..';
			$this->lineHeight = 6;
			$this->currentLine = 0;
			$this->tags = array(  );
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

		function addxml($xmlText) {
			$xmlParser = xml_parser_create( 'ISO-8859-1' );
			xml_set_object( $xmlParser, $this );
			xml_set_character_data_handler( $xmlParser, '_tagContent' );
			xml_set_element_handler( $xmlParser, '_openTag', '_closeTag' );

			if (!xml_parse( $xmlParser, $xmlText )) {
				exit( sprintf( 'error XML : %s at line %d', xml_error_string( xml_get_error_code( $xmlParser ) ), xml_get_current_line_number( $xmlParser ) ) );
			}

			xml_parser_free( $xmlParser );
		}

		function addhtml($htmlText) {
			$this->Write( 10, $htmlText, null );
		}

		function header() {
			if (isset( ->objectToDoHeader )) {
				$this->objectToDoHeader->doHeader(  );
			}

		}

		function footer() {
			if (isset( ->objectToDoFooter )) {
				$this->objectToDoFooter->doFooter(  );
			}

		}

		function setobjecttodoheader($obj) {
			$this->objectToDoHeader = &$obj;

		}

		function setobjecttodofooter($obj) {
			$this->objectToDoFooter = &$obj;

		}

		function _putstream($s) {
			if ($this->encrypted) {
				$s = $this->_RC4( $this->_objectkey( $this->n ), $s );
			}

			fpdf::_putstream( $s );
		}

		function _textstring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4( $this->_objectkey( $this->n ), $s );
			}

			return fpdf::_textstring( $s );
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
			fpdf::_putresources(  );

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
			fpdf::_puttrailer(  );

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

		function _opentag($parser, $name, $attributes) {
			$tag = new XMLTag( $name );
			$tag->setAttributes( $attributes );
			array_push( $this->tags, $tag );
		}

		function _tagcontent($parser, $data) {
			if (trim( $data ) == '') {
				return null;
			}

			$tag = array_pop( $this->tags );

			if ($tag == null) {
				trigger_error( 'stack wrong', E_USER_ERROR );
			}

			$tag->setData( $data );
			array_push( $this->tags, $tag );
			$y = $this->getY(  );
			$posn = $y + $this->lineHeight;
			$this->setY( $posn );
			$this->setX( 0 );
			$this->write( $this->lineHeight, $data );
		}

		function _closetag($parser, $name) {
			$tag = array_pop( $this->tags );

			if ($tag == null) {
				trigger_error( 'stack wrong', E_USER_ERROR );
			}

			$tagName = $tag->getName(  );

			if ($tagName != $name) {
				trigger_error( '' . 'end tag ' . $name . ' and ' . $tagName . ' dont match', E_USER_ERROR );
			}

			$this->_processTag( $tag );
		}

		function _processtag($tag) {
			$name = $tag->getName(  );
		}
	}

?>