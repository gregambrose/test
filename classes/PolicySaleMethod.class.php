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

	class policysalemethod {
		var $table = null;
		var $keyField = null;

		function policysalemethod($code) {
			$this->keyField = 'psCode';
			$this->table = 'policySaleMethods';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['psSequence'] = 'INT';
		}
	}

?>