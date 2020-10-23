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

	class banktranstype {
		var $table = null;
		var $keyField = null;

		function banktranstype($code) {
			$this->keyField = 'byCode';
			$this->table = 'bankTransTypes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['byDebit'] = 'BOOL';
		}
	}

?>