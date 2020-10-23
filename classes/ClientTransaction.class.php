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

	class clienttransaction {
		var $table = null;
		var $keyField = null;

		function clienttransaction($code) {
			$this->keyField = 'ctCode';
			$this->table = 'clientTransactions';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['ctClient'] = 'INT';
			$this->fieldTypes['ctInvoiceNo'] = 'INT';
			$this->fieldTypes['ctPolicyTransType'] = 'INT';
			$this->fieldTypes['ctPolicyTran'] = 'INT';
			$this->fieldTypes['ctHandler'] = 'INT';
			$this->fieldTypes['ctDocm'] = 'INT';
			$this->fieldTypes['ctLastUpdateBy'] = 'INT';
			$this->fieldTypes['ctAccountingYear'] = 'INT';
			$this->fieldTypes['ctAccountingPeriod'] = 'INT';
			$this->fieldTypes['ctOriginal'] = 'MONEY';
			$this->fieldTypes['ctPaid'] = 'MONEY';
			$this->fieldTypes['ctBalance'] = 'MONEY';
			$this->fieldTypes['ctWrittenOff'] = 'MONEY';
			$this->fieldTypes['ctPostingDate'] = 'DATE';
			$this->fieldTypes['ctEffectiveDate'] = 'DATE';
			$this->fieldTypes['ctPaidDate'] = 'DATE';
			$this->fieldTypes['ctDirectPaidDate'] = 'DATE';
			$this->_setUpdatedByField( 'ctLastUpdateBy' );
			$this->_setUpdatedWhenField( 'ctLastUpdateOn' );
			$this->handleConcurrency( true );
		}

		function recalctotals() {
			$ctOriginal = $this->get( 'ctOriginal' );
			$ctPaid = $this->get( 'ctPaid' );
			$ctWrittenOff = $this->get( 'ctWrittenOff' );
			$ctTransType = $this->get( 'ctTransType' );
			$ctBalance = $ctOriginal - $ctPaid - $ctWrittenOff;
			$this->set( 'ctBalance', $ctBalance );
		}

		function setcreatedbyandwhen() {
			global $user;

			$usCode = 0;

			if (is_object( $user )) {
				$usCode = $user->getKeyValue(  );
			}

			$this->set( 'ctCreatedBy', $usCode );
			$this->set( 'ctCreatedOn', ugettimenow(  ) );
		}
	}

?>