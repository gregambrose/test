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

	class bankaccounttran {
		var $table = null;
		var $keyField = null;

		function bankaccounttran($code) {
			$this->keyField = 'baCode';
			$this->table = 'bankAccountTrans';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['baDebit'] = 'BOOL';
			$this->fieldTypes['baType'] = 'INT';
			$this->fieldTypes['baAmount'] = 'MONEY';
			$this->fieldTypes['baDebit'] = 'BOOL';
			$this->fieldTypes['baDebit'] = 'BOOL';
			$this->fieldTypes['baPostingDate'] = 'DATE';
			$this->fieldTypes['baTran'] = 'INT';
			$this->fieldTypes['baPaymentType'] = 'INT';
			$this->fieldTypes['baTran'] = 'INT';
			$this->fieldTypes['baAccountingYear'] = 'INT';
			$this->fieldTypes['baAccountingPeriod'] = 'INT';
			$this->fieldTypes['baCreatedBy'] = 'INT';
			$this->fieldTypes['baLastUpdateBy'] = 'INT';
			$q = 'SELECT byName as typeName, cpName as paymentMethodName  FROM bankAccountTrans
			LEFT JOIN bankTransTypes on baType = byCode
			LEFT JOIN cashPaymentMethods on baPaymentType = cpCode
			where baCode = CODE';
			$this->setExtraSql( $q );
			$this->handleConcurrency( true );
			$this->_setUpdatedByField( 'baLastUpdateBy' );
			$this->_setUpdatedWhenField( 'baLastUpdateOn' );
		}

		function setcreatedbyandwhen() {
			global $user;

			$usCode = 0;

			if (is_object( $user )) {
				$usCode = $user->getKeyValue(  );
			}

			$this->set( 'baCreatedBy', $usCode );
			$this->set( 'baCreatedOn', ugettimenow(  ) );
		}
	}

?>