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

	class changeperiodtemplate {
		var $year = null;
		var $canPeriodBeChanged = null;

		function changeperiodtemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'currentYear' );
			$this->addField( 'currentPeriod' );
			$this->addField( 'newYear' );
			$this->addField( 'newPeriod' );
			$this->canPeriodBeChanged = true;
		}

		function setcanbechanged($bool) {
			$this->canPeriodBeChanged = $bool;
		}

		function setdates() {
			$system = new System( 1 );
			$accountingYear = $system->get( 'syYearCode' );
			$year = new AccountingYear( $accountingYear );
			$currentYear = $year->get( 'ayName' );
			$this->set( 'currentYear', $currentYear );
			$accountingPeriod = $system->get( 'syPeriodCode' );
			$period = new AccountingPeriod( $accountingPeriod );
			$currentPeriod = $period->get( 'apName' );
			$this->set( 'currentPeriod', $currentPeriod );
			$ok = $system->incrementPeriod( true );

			if ($ok == false) {
				$this->setMessage( 'system tables dont allow period to be changed' );
				return null;
			}

			$accountingYear = $system->get( 'syYearCode' );
			$year = new AccountingYear( $accountingYear );
			$newYear = $year->get( 'ayName' );
			$this->set( 'newYear', $newYear );
			$accountingPeriod = $system->get( 'syPeriodCode' );
			$period = new AccountingPeriod( $accountingPeriod );
			$newPeriod = $period->get( 'apName' );
			$this->set( 'newPeriod', $newPeriod );
		}

		function whenperiodcanbechanged($text) {
			global $isUserInternalManager;
			global $isUserSysManager;

			if (( $isUserInternalManager != true && $isUserSysManager != true )) {
				return '';
			}


			if ($this->canPeriodBeChanged != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenperiodcantbechanged($text) {
			global $isUserSysManager;
			global $isUserSysManager;

			if (( $isUserSysManager == true || $isUserSysManager == true )) {
				return '';
			}


			if ($this->canPeriodBeChanged == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennotsystemmanager($text) {
			global $isUserInternalManager;
			global $isUserSysManager;

			if (( $isUserInternalManager == true || $isUserSysManager == true )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>