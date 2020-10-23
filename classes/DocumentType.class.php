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

	class documenttype {
		var $table = null;
		var $keyField = null;

		function documenttype($code) {
			$this->keyField = 'dtCode';
			$this->table = 'documentTypes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['dtClient'] = 'BOOL';
			$this->fieldTypes['dtPolicy'] = 'BOOL';
			$this->fieldTypes['dtInsco'] = 'BOOL';
			$this->fieldTypes['dtIntroducer'] = 'BOOL';
			$this->fieldTypes['dtSequence'] = 'INT';
		}
	}

?>