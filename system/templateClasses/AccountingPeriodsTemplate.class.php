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

	class accountingperiodstemplate {
		var $year = null;
		var $periods = null;
		var $newRecord = null;

		function accountingperiodstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'ayYear' );
			$this->addField( 'ayName' );
			$this->addField( 'ayFromDate' );
			$this->addField( 'ayToDate' );
		}

		function setyear($ayCode) {
			$this->year = new AccountingYear( $ayCode );
			$this->setAll( $this->year->getAllForHTML(  ) );
			$this->getPeriods(  );
		}

		function setnewrecord($new) {
			$this->newRecord = $new;
		}

		function getperiods() {
			$this->periods = array(  );

			if (!isset( $this->year )) {
				return '';
			}

			$ayCode = $this->year->getKeyValue(  );
			$p = 1;

			while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
				$this->set( 'periodNo', $p );
				$q = '' . 'SELECT apCode FROM accountingPeriods
				  WHERE apYear = ' . $ayCode . ' AND apPeriod = ' . $p . '
				  ORDER BY apCode';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				if (udbnumberofrows( $result ) == 1) {
					$row = udbgetrow( $result );
					$apCode = $row['apCode'];
					$ap = new AccountingPeriod( $apCode );
				} 
else {
					$ap = new AccountingPeriod( null );
				}

				$this->periods[$p] = $ap;
				++$p;
			}

		}

		function updateperiods($input) {
			if (!isset( $this->year )) {
				return '';
			}


			if (!isset( $this->periods )) {
				return '';
			}

			$ayCode = $this->year->getKeyValue(  );
			reset( $this->periods );

			while ($elem = each( $this->periods )) {
				$ap = $elem['value'];
				$apPeriod = $ap->get( 'apPeriod' );
				$apName = $input['' . 'apName' . $apPeriod];
				$apFromDate = $input['' . 'apFromDate' . $apPeriod];
				$apToDate = $input['' . 'apToDate' . $apPeriod];
				$ap->set( 'apName', $apName );
				$ap->set( 'apFromDate', $apFromDate );
				$ap->set( 'apToDate', $apToDate );

				if ($ap->recordExists(  ) == true) {
					$ap->update(  );
				} 
else {
					$ap->insert(  );
				}

				$this->periods[$apPeriod] = $ap;
			}

		}

		function getyear() {
			return $this->year;
		}

		function validate($input) {
			$ayYear = $this->get( 'ayYear' );

			if ($ayYear <= 0) {
				return 'you need to specify a year';
			}

			$ayName = $this->get( 'ayName' );

			if (strlen( trim( $ayName ) ) == 0) {
				return 'you need to specify a description';
			}

			$ayFromDate = $this->get( 'ayFromDate' );

			if (uvalidatedate( $ayFromDate ) == false) {
				return 'you need to specify a valid from date';
			}

			$ayToDate = $this->get( 'ayToDate' );

			if (uvalidatedate( $ayToDate ) == false) {
				return 'you need to specify a valid to date';
			}

			$allDatesThere = true;
			$allNamesThere = true;
			$p = 1;

			while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
				$ap = &$this->periods[$p];

				$apPeriod = $ap->get( 'apPeriod' );
				$apName = $input['' . 'apName' . $apPeriod];
				$apFromDate = $input['' . 'apFromDate' . $apPeriod];
				$apToDate = $input['' . 'apToDate' . $apPeriod];

				if (( $apFromDate == '' || $apFromDate == '0000-00-00' )) {
					$allDatesThere = false;
					break;
				}


				if (( $apToDate == '' || $apToDate == '0000-00-00' )) {
					$allDatesThere = false;
					break;
				}


				if (strlen( trim( $apName ) ) == 0) {
					$allNamesThere = false;
					break;
				}

				++$p;
			}


			if ($allNamesThere == false) {
				return 'you need to specify period descriptions for all periods';
			}


			if ($allDatesThere == false) {
				return 'you need to specify valid dates for all periods';
			}

			return null;
		}

		function setspecificfields($input) {
			if (isset( $input['createYear'] )) {
				$this->newRecord = true;
			}


			if (isset( $input['accessYear'] )) {
				$this->newRecord = false;
			}


			if ($this->newRecord == true) {
				$ok = true;
			} 
else {
				$ok = false;
			}

			$this->setFieldAllowEditing( 'ayYear', $ok );
		}

		function listperiods($text) {
			if (!isset( $this->year )) {
				return '';
			}


			if (!isset( $this->periods )) {
				return '';
			}

			$ayCode = $this->year->getKeyValue(  );
			$out = '';
			reset( $this->periods );

			while ($elem = each( $this->periods )) {
				$ap = $elem['value'];
				$this->set( 'code', $ap->getForHTML( 'apPeriod' ) );
				$this->set( 'periodNo', $ap->getForHTML( 'apPeriod' ) );
				$this->set( 'apName', $ap->getForHTML( 'apName' ) );
				$this->set( 'apFromDate', $ap->getForHTML( 'apFromDate' ) );
				$this->set( 'apToDate', $ap->getForHTML( 'apToDate' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listyears($text) {
			if (isset( $this->year )) {
				$thisYear = $this->year->getKeyValue(  );
			} 
else {
				$thisYear = 0 - 1;
			}

			$out = '';
			$q = 'SELECT ayCode FROM accountingYears ORDER BY ayYear';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$ayCode = $row['ayCode'];
				$ay = new AccountingYear( $ayCode );
				$this->set( 'code', $ayCode );
				$desc = $ay->getForHTML( 'ayYear' );

				if (strlen( trim( $desc ) ) == 0) {
					$desc = 'blank';
				}

				$this->set( 'year', $ay->getForHTML( 'ayYear' ) );

				if ($ayCode == $thisYear) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whenyeartoview($text) {
			if (!isset( $this->year )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>