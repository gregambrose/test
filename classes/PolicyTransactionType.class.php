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

	class policytransactiontype {
		var $table = null;
		var $keyField = null;

		function policytransactiontype($code) {
			$this->keyField = 'pyCode';
			$this->table = 'policyTransactionTypes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['pyFromPolicy'] = 'BOOL';
			$this->fieldTypes['pySequence'] = 'INT';
		}

		function dowetakeallpolicydetails() {
			$ok = $this->get( 'pyFromPolicy' );
			return $ok;
		}
	}

?>