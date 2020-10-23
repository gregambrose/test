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

	class journal {
		var $table = null;
		var $keyField = null;

		function journal($code) {
			$this->keyField = 'jnCode';
			$this->table = 'journals';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['jnPostingDate'] = 'DATE';
			$this->fieldTypes['jnAccountingYear'] = 'INT';
			$this->fieldTypes['jnAccountingPeriod'] = 'INT';
			$this->fieldTypes['jnCreatedBy'] = 'INT';
			$this->fieldTypes['jnLastUpdatedBy'] = 'INT';
			$this->_setUpdatedByField( 'jnLastUpdateBy' );
			$this->_setUpdatedWhenField( 'jnLastUpdateOn' );
			$this->handleConcurrency( true );
		}
	}

?>