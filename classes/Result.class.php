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

	class result {
		var $table = null;
		var $keyField = null;

		function result($code) {
			$this->keyField = 'srCode';
			$this->table = 'results';
			urecord::urecord( $code, $this->table, $this->keyField );
		}
	}

?>