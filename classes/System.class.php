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

	class system {
		var $table = null;
		var $keyField = null;

		function system($code) {
			$this->keyField = 'syCode';
			$this->table = 'system';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['syBrokerVATRate'] = 'MONEY';
			$this->fieldTypes['syCompanyVATRate'] = 'MONEY';
			$this->fieldTypes['syNormalIPTRate'] = 'MONEY';
			$this->fieldTypes['syTravelIPTRate'] = 'MONEY';
			$this->fieldTypes['syAccountingYear'] = 'INT';
			$this->fieldTypes['syAccountingPeriod'] = 'INT';
			$this->fieldTypes['syPeriodCode'] = 'INT';
			$this->fieldTypes['syPeriodFrom'] = 'DATE';
			$this->fieldTypes['syPeriodTo'] = 'DATE';
			$this->fieldTypes['syMaxUsers'] = 'INT';
		}

		function getbrokervatrate() {
			return $this->get( 'syBrokerVATRate' );
		}

		function getcompanyvatrate() {
			return $this->get( 'syCompanyVATRate' );
		}

		function getnormaliptrate() {
			return $this->get( 'syNormalIPTRate' );
		}

		function gettraveliptrate() {
			return $this->get( 'syTravelIPTRate' );
		}

		function getaccountingyear() {
			return $this->get( 'syAccountingYear' );
		}

		function getaccountingyearcode() {
			return $this->get( 'syYearCode' );
		}

		function getmaxusers() {
			return $this->get( 'syMaxUsers' );
		}

		function getaccountingyeardesc() {
			$desc = '';
			$yr = $this->get( 'syAccountingYear' );
			$q = '' . 'SELECT ayName from accountingYears WHERE ayYear = ' . $yr;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );

			if ($row == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$desc = $row['ayName'];
			return $desc;
		}

		function getperioddescription() {
			$yrDesc = $this->getAccountingYearDesc(  );
			$per = $this->get( 'syAccountingPeriod' );

			if (( 0 < $per && $per < 10 )) {
				$per = '0' . $per;
			}

			$desc = $per . '/' . $yrDesc;
			return $desc;
		}

		function getreversedperioddescription() {
			$yrDesc = $this->getAccountingYearDesc(  );
			$per = $this->get( 'syAccountingPeriod' );

			if (( 0 < $per && $per < 10 )) {
				$per = '0' . $per;
			}

			$desc = $yrDesc . '/' . $per;
			return $desc;
		}

		function getaccountingperiod() {
			return $this->get( 'syAccountingPeriod' );
		}

		function getperiodcode() {
			return $this->get( 'syPeriodCode' );
		}

		function getperiodfrom() {
			return $this->get( 'syPeriodFrom' );
		}

		function getperiodto() {
			return $this->get( 'syPeriodTo' );
		}

		function incrementperiod($doIt) {
			if (( $doIt != true && $doIt != false )) {
				trigger_error( 'must be true or false', E_USER_ERROR );
			}

			$syYearCode = $this->get( 'syYearCode' );
			$syPeriodCode = $this->get( 'syPeriodCode' );
			$syAccountingYear = $this->get( 'syAccountingYear' );
			$syAccountingPeriod = $this->get( 'syAccountingPeriod' );

			if (( ( ( $syYearCode < 1 || $syPeriodCode < 1 ) || $syAccountingYear < 1 ) || $syAccountingPeriod < 1 )) {
				trigger_error( '' . 'cant get period details a=' . $syYearCode . ' b=' . $syPeriodCode . ' c=' . $syAccountingYear . ' d=' . $syAccountingPeriod, E_USER_NOTICE );
				return false;
			}


			if ($syAccountingPeriod < ACCOUNTING_PERIODS_PER_YEAR) {
				++$syAccountingPeriod;
			} 
else {
				++$syAccountingYear;
				$syAccountingPeriod = 1;
			}

			$q = '' . 'SELECT ayCode FROM accountingYears WHERE ayYear = ' . $syAccountingYear;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (udbnumberofrows( $result ) == 0) {
				trigger_error( '' . 'cant get year ' . $syAccountingYear, E_USER_NOTICE );
				return false;
			}

			$row = udbgetrow( $result );
			$ayCode = $row['ayCode'];
			$q = '' . 'SELECT apCode FROM accountingPeriods
				WHERE apYear = ' . $ayCode . '
				AND   apPeriod = ' . $syAccountingPeriod;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (udbnumberofrows( $result ) == 0) {
				trigger_error( '' . 'cant get year ' . $syAccountingPeriod, E_USER_NOTICE );
				return false;
			}

			$row = udbgetrow( $result );
			$apCode = $row['apCode'];

			if ($doIt == false) {
				return true;
			}

			$year = new AccountingYear( $ayCode );
			$period = new AccountingPeriod( $apCode );
			$this->set( 'syAccountingYear', $year->get( 'ayYear' ) );
			$this->set( 'syAccountingPeriod', $period->get( 'apPeriod' ) );
			$this->set( 'syYearCode', $ayCode );
			$this->set( 'syPeriodCode', $apCode );
			$this->set( 'syPeriodFrom', $period->get( 'apFromDate' ) );
			$this->set( 'syPeriodTo', $period->get( 'apToDate' ) );
			return true;
		}
	}

?>