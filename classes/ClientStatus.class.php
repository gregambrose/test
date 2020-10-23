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

	class clientstatus {
		var $table = null;
		var $keyField = null;

		function clientstatus($code) {
			$this->keyField = 'csCode';
			$this->table = 'clientStatus';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['csLocked'] = 'INT';
			$this->fieldTypes['csSequence'] = 'BOOL';
		}
	}

?>