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

	class sequence {
		var $table = null;
		var $keyField = null;

		function sequence($code) {
			$this->keyField = 'sqCode';
			$this->table = 'sequences';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['sqMaster'] = 'INT';
			$this->fieldTypes['sqLastUsed'] = 'INT';
			$this->handleConcurrency( false );
		}
	}

?>