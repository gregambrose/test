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

	class paymentmethod {
		var $table = null;
		var $keyField = null;

		function paymentmethod($code) {
			$this->keyField = 'pmCode';
			$this->table = 'policyPaymentMethods';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['pmSequence'] = 'INT';
			$this->fieldTypes['pmDirect'] = 'BOOL';
		}
	}

?>