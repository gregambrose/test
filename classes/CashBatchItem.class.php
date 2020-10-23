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

	class cashbatchitem {
		var $table = null;
		var $keyField = null;
		var $itemNum = null;
		var $setForDeletion = false;

		function cashbatchitem($code) {
			$this->keyField = 'biCode';
			$this->table = 'cashBatchItems';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['biBatch'] = 'INT';
			$this->fieldTypes['biSequence'] = 'INT';
			$this->fieldTypes['biClient'] = 'INT';
			$this->fieldTypes['biInsco'] = 'INT';
			$this->fieldTypes['biIntroducer'] = 'INT';
			$this->fieldTypes['biPaymentMethod'] = 'INT';
			$this->fieldTypes['biAmount'] = 'MONEY';
			$this->fieldTypes['biDateAllocated'] = 'DATE';
			$this->fieldTypes['biLastUpdateBy'] = 'INT';
			$q = 'SELECT cpName as paymentTypeName  FROM cashBatchItems
				LEFT JOIN cashPaymentMethods on biPaymentMethod = cpCode
				where biCode = CODE';
			$this->setExtraSql( $q );
			$this->handleConcurrency( true );
			$this->_setUpdatedByField( 'biLastUpdateBy' );
			$this->_setUpdatedWhenField( 'biLastUpdateOn' );
			$this->itemNum = 0 - 1;
			$this->setForDeletion = false;
		}

		function setsequence($i) {
			$this->itemNum = $i;
		}

		function getsequence() {
			return $this->itemNum;
		}

		function setfordeletion() {
			$this->setForDeletion = true;
		}

		function isthisfordeletion() {
			return $this->setForDeletion;
		}

		function getpaymentmethoddesc() {
			$cpCode = $this->get( 'biPaymentMethod' );

			if (0 < $cpCode) {
				$pt = new CashPaymentMethod( $cpCode );
				$desc = $pt->get( 'cpName' );
			} 
else {
				$desc = '';
			}

			return $desc;
		}
	}

?>