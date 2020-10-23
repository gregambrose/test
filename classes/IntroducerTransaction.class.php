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

	class introducertransaction {
		var $table = null;
		var $keyField = null;

		function introducertransaction($code) {
			$this->keyField = 'rtCode';
			$this->table = 'introducerTransactions';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['rtTransType'] = 'INT';
			$this->fieldTypes['rtPostingDate'] = 'DATE';
			$this->fieldTypes['rtEffectiveDate'] = 'DATE';
			$this->fieldTypes['rtPolicyTran'] = 'INT';
			$this->fieldTypes['rtPaymentType'] = 'INT';
			$this->fieldTypes['rtIntroducer'] = 'INT';
			$this->fieldTypes['rtDirect'] = 'INT';
			$this->fieldTypes['rtCalcOn'] = 'MONEY';
			$this->fieldTypes['rtRate'] = 'MONEY';
			$this->fieldTypes['rtOriginal'] = 'MONEY';
			$this->fieldTypes['rtPaid'] = 'MONEY';
			$this->fieldTypes['rtWrittenOff'] = 'MONEY';
			$this->fieldTypes['rtBalance'] = 'MONEY';
			$this->fieldTypes['rtAccountingYear'] = 'INT';
			$this->fieldTypes['rtAccountingPeriod'] = 'INT';
			$this->fieldTypes['rtPaidDate'] = 'DATE';
			$this->fieldTypes['rtPaidYear'] = 'INT';
			$this->fieldTypes['rtPaidPeriod'] = 'INT';
			$q = 'SELECT cpName as paymentTypeName   FROM introducerTransactions
				LEFT JOIN cashPaymentMethods on rtPaymentType = cpCode
				where rtCode = CODE';
			$this->setExtraSql( $q );
			$this->_setUpdatedByField( 'rtLastUpdateBy' );
			$this->_setUpdatedWhenField( 'rtLastUpdateOn' );
			$this->handleConcurrency( true );
		}

		function setcreatedbyandwhen() {
			global $user;

			$usCode = 0;

			if (is_object( $user )) {
				$usCode = $user->getKeyValue(  );
			}

			$this->set( 'rtCreatedBy', $usCode );
			$this->set( 'rtCreatedOn', ugettimenow(  ) );
		}

		function recalcbalance() {
			$rtTransType = $this->get( 'rtTransType' );

			if (( $rtTransType == 'C' || $rtTransType == 'R' )) {
				trigger_error( 'cant recalc this type of it trans', E_USER_ERROR );
			}

			$rtBalance = $this->get( 'rtOriginal' ) - ( $this->get( 'rtPaid' ) + $this->get( 'rtWrittenOff' ) );
			$this->set( 'rtBalance', $rtBalance );
		}
	}

?>