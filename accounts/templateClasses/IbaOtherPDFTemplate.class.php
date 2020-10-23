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

	class ibaotherpdftemplate {
		function ibaotherpdftemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportType' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'found' );
			$this->addField( 'selectedType' );
			$this->addField( 'user' );
			$this->addField( 'bfBalance' );
			$this->addField( 'periodDesc' );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedPeriodCode' );
			$this->addField( 'selectedYear' );
			$this->setHeader( SITE_NAME );
		}

		function setyear($year) {
			$this->set( 'year', $year );
		}

		function setperiod($period) {
			$this->set( 'period', $period );
		}

		function setperioddescription($desc) {
			$this->set( 'periodDesc', $desc );
		}

		function setbalances($mainTemplate) {
			$this->set( 'bfBalance', $mainTemplate->get( 'bfBalance' ) );
			$this->set( 'cfBalance', $mainTemplate->get( 'cfBalance' ) );
		}

		function listdetail($text) {
			global $userCode;
			global $periodFrom;
			global $periodTo;

			$q = '' . 'SELECT baCode  FROM bankAccountTrans
			WHERE baPostingDate >= \'' . $periodFrom . '\' AND baPostingDate <= \'' . $periodTo . '\'
			ORDER BY baPostingDate DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$balance = 0;

			while ($row = udbgetrow( $result )) {
				$baCode = $row['baCode'];
				$ba = new BankAccountTran( $baCode );
				$amount = $ba->get( 'baAmount' );
				$balance += $amount;

				if (0 <= $amount) {
					$debitAmount = uformatmoney( $amount );
					$creditAmount = '';
				} 
else {
					$amount = 0 - $amount;
					$debitAmount = '';
					$creditAmount = uformatmoney( $amount );
				}

				$baType = $ba->get( 'baType' );
				$transType = 'unknown';

				if (0 < $baType) {
					$bat = new BankTransType( $baType );
					$transType = $bat->getForHTML( 'byName' );
				}

				$this->set( 'transRef', $baCode );
				$this->set( 'desc', $ba->getForHTML( 'baDescription' ) );
				$this->set( 'postRef', $ba->getForHTML( 'baPostingRef' ) );
				$this->set( 'date', $ba->getForHTML( 'baPostingDate' ) );
				$this->set( 'debit', $debitAmount );
				$this->set( 'credit', $creditAmount );
				$this->set( 'type', $transType );
				$out .= $this->parse( $text );
			}


			if (0 < $balance) {
				$balanceToView = uformatmoneywithcommas( $balance );
				$this->set( 'sign', 'OD' );
			} 
else {
				$negBalance = 0 - $balance;
				$balanceToView = uformatmoneywithcommas( $negBalance );
				$this->set( 'sign', '' );
			}

			$this->set( 'cFbalance', $balanceToView );
			return $out;
		}

		function grandtotal($text) {
			if ($this->doGrandTotal == false) {
				return '';
			}

			$icCode = $this->get( 'insCo' );

			if (0 < $icCode) {
				return '';
			}

			$this->set( 'origTotal', uformatmoneywithcommas( $this->origGrand ) );
			$this->set( 'balanceTotal', uformatmoneywithcommas( $this->balanceGrand ) );
			$out = $this->parse( $text );
			return $out;
		}

		function whentranstodo($text) {
			if ($this->doTransaction == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenmanycompanies($text) {
			$icCode = $this->get( 'insCo' );

			if (0 < $icCode) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function setheaderfields() {
			$nb = $this->get( 'newBusiness' );

			if ($nb == 'N') {
				$name = 'New Business Only';
			} 
else {
				if ($nb == 'E') {
					$name = 'Existing Business Only';
				} 
else {
					if ($nb == 'B') {
						$name = 'New & Existing Business';
					} 
else {
						$name = '';
					}
				}
			}

			$this->set( 'newBus', $name );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
		}

		function _makemonthfromdate($date) {
			$date = uformatsqldate2( $date );
			$out = trim( substr( $date, 2 ) );
			return $out;
		}
	}

?>