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

	class uxml {
		var $tags = array(  );

		function uxml($xmlText) {
			$xmlParser = xml_parser_create( 'ISO-8859-1' );
			xml_set_object( $xmlParser, $this );
			xml_set_character_data_handler( $xmlParser, '_tagContent' );
			xml_set_element_handler( $xmlParser, '_openTag', '_closeTag' );

			if (!xml_parse( $xmlParser, $xmlText )) {
				print $xmlText;
				trigger_error( 'xml error ' . sprintf( 'error XML : %s at line %d', xml_error_string( xml_get_error_code( $xmlParser ) ), xml_get_current_line_number( $xmlParser ) ) . 'xml was 
' . $xmlText . '
', E_USER_ERROR );
			}

			xml_parser_free( $xmlParser );
		}

		function _opentag($parser, $name, $attributes) {
			$tag = new UXMLTag( $name );
			$tag->setAttributes( $attributes );
			array_push( $this->tags, $tag );

			if (method_exists( $this, '_startTag' )) {
				$this->_startTag( $tag );
			}

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


			if (method_exists( $this, '_processTag' )) {
				$this->_processTag( $tag );
			}

		}

		function _tagcontent($parser, $data) {
			$tag = array_pop( $this->tags );

			if ($tag == null) {
				trigger_error( 'stack wrong', E_USER_ERROR );
			}

			$tag->setData( $data );
			array_push( $this->tags, $tag );
		}
	}

?>