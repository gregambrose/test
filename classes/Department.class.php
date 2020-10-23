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

	class department {
		var $table = null;
		var $keyField = null;

		function department($code) {
			$this->keyField = 'dpCode';
			$this->table = 'departments';
			urecord::urecord( $code, $this->table, $this->keyField );
		}
	}

?>