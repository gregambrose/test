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

	class notetype {
		var $table = null;
		var $keyField = null;

		function notetype($code) {
			$this->keyField = 'ntCode';
			$this->table = 'noteTypes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['ntSequence'] = 'INT';
			$this->fieldTypes['ntClient'] = 'BOOL';
			$this->fieldTypes['ntPolicy'] = 'BOOL';
			$this->fieldTypes['ntIntroducer'] = 'BOOL';
		}
	}

?>