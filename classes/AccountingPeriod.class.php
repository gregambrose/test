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

	class accountingperiod {
		var $table = null;
		var $keyField = null;

		function accountingperiod($code) {
			$this->keyField = 'apCode';
			$this->table = 'accountingPeriods';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['apYear'] = 'INT';
			$this->fieldTypes['apPeriod'] = 'INT';
			$this->fieldTypes['apFromDate'] = 'DATE';
			$this->fieldTypes['apToDate'] = 'DATE';
		}

		function getperioddescription() {
			$ayCode = $this->get( 'apYear' );
			$ay = new AccountingYear( $ayCode );
			$yrDesc = $ay->get( 'ayName' );
			$day = $this->get( 'apPeriod' );

			if (( 0 < $day && $day < 10 )) {
				$day = '0' . $day;
			}

			$desc = $day . '/' . $yrDesc;
			return $desc;
		}

		function getpreviousperiod() {
			$year = $this->get( 'apYear' );
			$period = $this->get( 'apPeriod' );
			$ay = new AccountingYear( $year );
			$actualYear = $ay->get( 'ayYear' );

			if (--$period < 1) {
				$period = ACCOUNTING_PERIODS_PER_YEAR;
				--$actualYear;
			}

			$q = '' . 'SELECT apCode FROM accountingPeriods, accountingYears 
				WHERE 
				apYear = ayCode
				AND ayYear = ' . $actualYear . ' 
				AND   apPeriod = ' . $period;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ) . $q, E_USER_ERROR );
			}


			if (udbnumberofrows( $result ) == 0) {
				return 0;
			}

			$row = udbgetrow( $result );
			$apCode = $row['apCode'];
			return $apCode;
		}
	}

?>