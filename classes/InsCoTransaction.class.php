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

	class inscotransaction {
		var $table = null;
		var $keyField = null;

		function inscotransaction($code) {
			$this->keyField = 'itCode';
			$this->table = 'inscoTransactions';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['itPostingDate'] = 'DATE';
			$this->fieldTypes['itEffectiveDate'] = 'DATE';
			$this->fieldTypes['itPolicyTran'] = 'INT';
			$this->fieldTypes['itPaymentType'] = 'INT';
			$this->fieldTypes['itAccountingPeriod'] = 'INT';
			$this->fieldTypes['itAccountingYear'] = 'INT';
			$this->fieldTypes['itPaidDate'] = 'DATE';
			$this->fieldTypes['itPaidYear'] = 'INT';
			$this->fieldTypes['itPaidPeriod'] = 'INT';
			$this->fieldTypes['itInsCo'] = 'INT';
			$this->fieldTypes['itDirect'] = 'INT';
			$this->fieldTypes['itGross'] = 'MONEY';
			$this->fieldTypes['itCommission'] = 'MONEY';
			$this->fieldTypes['itCommissionRate'] = 'MONEY';
			$this->fieldTypes['itNet'] = 'MONEY';
			$this->fieldTypes['itGrossIPT'] = 'MONEY';
			$this->fieldTypes['itAddlGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['itAddlGross'] = 'MONEY';
			$this->fieldTypes['itAddlCommissionRate'] = 'MONEY';
			$this->fieldTypes['itAddlCommission'] = 'MONEY';
			$this->fieldTypes['itAddlNet'] = 'MONEY';
			$this->fieldTypes['itGrossIPT'] = 'MONEY';
			$this->fieldTypes['itAddlIPT'] = 'MONEY';
			$this->fieldTypes['itEngineeringFee'] = 'MONEY';
			$this->fieldTypes['itEngineeringFeeCommRate'] = 'MONEY';
			$this->fieldTypes['itEngineeringFeeComm'] = 'MONEY';
			$this->fieldTypes['itEngineeringFeeNet'] = 'MONEY';
			$this->fieldTypes['itEngineeringFeeVATRate'] = 'MONEY';
			$this->fieldTypes['itEngineeringFeeVAT'] = 'MONEY';
			$this->fieldTypes['itOriginal'] = 'MONEY';
			$this->fieldTypes['itWrittenOff'] = 'MONEY';
			$this->fieldTypes['itPaid'] = 'MONEY';
			$this->fieldTypes['itBalance'] = 'MONEY';
			$q = 'SELECT cpName as paymentTypeName   FROM inscoTransactions
				LEFT JOIN cashPaymentMethods on itPaymentType = cpCode
				where itCode = CODE';
			$this->setExtraSql( $q );
			$this->_setUpdatedByField( 'itLastUpdateBy' );
			$this->_setUpdatedWhenField( 'itLastUpdateOn' );
			$this->handleConcurrency( true );
		}

		function setcreatedbyandwhen() {
			global $user;

			$usCode = 0;

			if (is_object( $user )) {
				$usCode = $user->getKeyValue(  );
			}

			$this->set( 'itCreatedBy', $usCode );
			$this->set( 'itCreatedOn', ugettimenow(  ) );
		}

		function recalcbalance() {
			$itTransType = $this->get( 'itTransType' );

			if (( $itTransType == 'C' || $itTransType == 'R' )) {
				trigger_error( 'cant recalc this type of it trans', E_USER_ERROR );
			}

			$itBalance = $this->get( 'itOriginal' ) - ( $this->get( 'itPaid' ) + $this->get( 'itWrittenOff' ) );
			$x = $this->get( 'itOriginal' );
			$x = $this->get( 'itPaid' );
			$x = $this->get( 'itWrittenOff' );
			$this->set( 'itBalance', $itBalance );
		}

		function gettotalcommission() {
			$tc = $this->get( 'itCommission' ) + $this->get( 'itAddlCommission' ) + $this->get( 'itEngineeringFeeComm' );
			return $tc;
		}
	}

?>