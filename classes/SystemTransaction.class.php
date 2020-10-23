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

	class systemtransaction {
		var $table = null;
		var $keyField = null;

		function systemtransaction($code) {
			$this->keyField = 'tnCode';
			$this->table = 'systemTransactions';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['tnCode'] = 'INT';
			$this->fieldTypes['tnTran'] = 'INT';
			$this->fieldTypes['tnCreatedBy'] = 'INT';
		}
	}

?>