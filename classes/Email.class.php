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

	class email {
		var $table = null;
		var $keyField = null;

		function email($code) {
			$this->keyField = 'elCode';
			$this->table = 'emaillist';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['elActive'] = 'BOOL';
			$this->fieldTypes['elWhenCreated'] = 'TIMESTAMP';
		}

		function getsecurity() {
			return $this->get( 'elSecurity' );
		}

		function getemailaddress() {
			return $this->get( 'elEmail' );
		}
	}

?>