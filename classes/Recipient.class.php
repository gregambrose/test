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

	class recipient {
		var $table = null;
		var $keyField = null;

		function recipient($code) {
			$this->keyField = 'reCode';
			$this->table = 'recipients';
			urecord::urecord( $code, $this->table, $this->keyField );
		}

		function getfullname() {
			$name = $this->get( 'reFirstName' ) . ' ' . $this->get( 'reSurname' );
			return $name;
		}
	}

?>