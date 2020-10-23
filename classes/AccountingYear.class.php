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

	class accountingyear {
		var $table = null;
		var $keyField = null;

		function accountingyear($code) {
			$this->keyField = 'ayCode';
			$this->table = 'accountingYears';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['ayYear'] = 'INT';
			$this->fieldTypes['ayFromDate'] = 'DATE';
			$this->fieldTypes['ayToDate'] = 'DATE';
		}
	}

?>