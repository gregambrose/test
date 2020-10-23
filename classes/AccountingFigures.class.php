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

	class accountingfigures {
		var $table = null;
		var $keyField = null;

		function accountingfigures($code) {
			$this->keyField = 'afCode';
			$this->table = 'accountingFigures';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['afYear'] = 'INT';
			$this->fieldTypes['afPeriod'] = 'INT';
			$this->fieldTypes['afPeriodCode'] = 'INT';
			$this->fieldTypes['afFromDate'] = 'DATE';
			$this->fieldTypes['afToDate'] = 'DATE';
			$this->fieldTypes['afBFClients'] = 'MONEY';
			$this->fieldTypes['afBFInsures'] = 'MONEY';
			$this->fieldTypes['afBFCommPosted'] = 'MONEY';
			$this->fieldTypes['afBFFeesPosted'] = 'MONEY';
			$this->fieldTypes['afBFIntroducers'] = 'MONEY';
			$this->fieldTypes['afBFCommPaid'] = 'MONEY';
			$this->fieldTypes['afBFFeesPaid'] = 'MONEY';
			$this->fieldTypes['afBFOtherIncome'] = 'MONEY';
			$this->fieldTypes['afBFOtherCharges'] = 'MONEY';
			$this->fieldTypes['afBFBank'] = 'MONEY';
			$this->fieldTypes['afNMClients'] = 'MONEY';
			$this->fieldTypes['afNMInsures'] = 'MONEY';
			$this->fieldTypes['afNMCommPosted'] = 'MONEY';
			$this->fieldTypes['afNMFeesPosted'] = 'MONEY';
			$this->fieldTypes['afNMIntroducers'] = 'MONEY';
			$this->fieldTypes['afNMCommPaid'] = 'MONEY';
			$this->fieldTypes['afNMFeesPaid'] = 'MONEY';
			$this->fieldTypes['afNMOtherIncome'] = 'MONEY';
			$this->fieldTypes['afNMOtherCharges'] = 'MONEY';
			$this->fieldTypes['afNMBank'] = 'MONEY';
			$this->fieldTypes['afCFClients'] = 'MONEY';
			$this->fieldTypes['afCFInsures'] = 'MONEY';
			$this->fieldTypes['afCFCommPosted'] = 'MONEY';
			$this->fieldTypes['afCFFeesPosted'] = 'MONEY';
			$this->fieldTypes['afCFIntroducers'] = 'MONEY';
			$this->fieldTypes['afCFCommPaid'] = 'MONEY';
			$this->fieldTypes['afCFFeesPaid'] = 'MONEY';
			$this->fieldTypes['afCFOtherIncome'] = 'MONEY';
			$this->fieldTypes['afCFOtherCharges'] = 'MONEY';
			$this->fieldTypes['afCFBank'] = 'MONEY';
		}

		function setfromcontrolaccount($flds) {
		}
	}

?>