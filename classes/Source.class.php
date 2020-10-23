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

	class source {
		var $table = null;
		var $keyField = null;

		function source($code) {
			$this->keyField = 'sbCode';
			$this->table = 'sourceOfBus';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['sbSequence'] = 'INT';
		}
	}

?>