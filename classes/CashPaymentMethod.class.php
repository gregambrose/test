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

	class cashpaymentmethod {
		var $table = null;
		var $keyField = null;

		function cashpaymentmethod($code) {
			$this->keyField = 'cpCode';
			$this->table = 'cashPaymentMethods';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['cpSequence'] = 'INT';
		}
	}

?>