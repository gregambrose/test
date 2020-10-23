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

	class accountingaudit {
		var $table = null;
		var $keyField = null;

		function accountingaudit($code) {
			$this->keyField = 'aaCode';
			$this->table = 'accountingAudit';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['aaPostingDate'] = 'DATE';
			$this->fieldTypes['aaEffectiveDate'] = 'DATE';
			$this->fieldTypes['aaTran'] = 'INT';
			$this->fieldTypes['aaAccountingYear'] = 'INT';
			$this->fieldTypes['aaAccountingPeriod'] = 'INT';
			$this->fieldTypes['aaCreatedBy'] = 'INT';
			$this->fieldTypes['aaLastUpdatedBy'] = 'INT';
			$this->_setUpdatedByField( 'aaLastUpdateBy' );
			$this->_setUpdatedWhenField( 'aaLastUpdateOn' );
			$this->handleConcurrency( true );
		}
	}

?>