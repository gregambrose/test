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

	class clienttype {
		var $table = null;
		var $keyField = null;

		function clienttype($code) {
			$this->keyField = 'cyCode';
			$this->table = 'clientTypes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['cySequence'] = 'INT';
		}
	}

?>