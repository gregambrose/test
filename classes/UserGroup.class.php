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

	class usergroup {
		var $table = null;
		var $keyField = null;

		function usergroup($code) {
			$this->keyField = 'ugCode';
			$this->table = 'userGroups';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['ugLevel'] = 'INT';
			$this->fieldTypes['ugSequence'] = 'INT';
		}
	}

?>