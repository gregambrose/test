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

	class backuptemplate {
		function backuptemplate($html) {
			ftemplate::ftemplate( $html );
		}

		function whenrestoreallowed($text) {
			if (( defined( 'LIVE_PROCESSING' ) && LIVE_PROCESSING == true )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>