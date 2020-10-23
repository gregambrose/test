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

	class policystatus {
		var $table = null;
		var $keyField = null;

		function policystatus($code) {
			$this->keyField = 'stCode';
			$this->table = 'policyStatus';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['stSequence'] = 'INT';
			$this->handleConcurrency( false );
		}
	}

?>