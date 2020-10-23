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

	class bankstatementpdftemplate {
		function bankstatementpdftemplate($html) {
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

			$q = '' . 'SELECT *  FROM tmpB' . $userCode . ' ORDER BY tmPostingDate, tmCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$balance = $this->getMoneyAsPennies( 'bfBalance' );
			$currentRow = udbgetrow( $result );

			while (true) {
				if ($currentRow == false) {
					break;
				}

				$nextRow = udbgetrow( $result );
				$tmType = $currentRow['tmType'];
				$tmPaymentType = $currentRow['tmPaymentType'];
				$tmBankCode = $currentRow['tmBankCode'];
				$tmTran = $currentRow['tmTran'];
				$tmPostingDate = $currentRow['tmPostingDate'];
				$tmAmount = $currentRow['tmAmount'];
				$tmRef = $currentRow['tmRef'];
				$tmDesc = $currentRow['tmDesc'];
				$needBalance = true;

				if ($nextRow != false) {
					$nextPostingDate = $nextRow['tmPostingDate'];

					if ($nextPostingDate == $tmPostingDate) {
						$needBalance = false;
					}
				}

				$tranTypeDesc = 'unknown';

				if (0 < $tmType) {
					$bat = new BankTransType( $tmType );
					$tranTypeDesc = $bat->get( 'byName' );
				}

				$tranPaymentTypeDesc = 'unknown';

				if (0 < $tmPaymentType) {
					$cp = new CashPaymentMethod( $tmPaymentType );
					$tranPaymentTypeDesc = $cp->get( 'cpName' );
				}

				$balance -= $tmAmount;

				if (0 < $balance) {
					$balanceToView = uformatmoneywithcommas( $balance );
					$this->set( 'sign', '' );
				} 
else {
					$negBalance = 0 - $balance;
					$balanceToView = uformatmoneywithcommas( $negBalance );
					$this->set( 'sign', 'OD' );
				}

				$name = $tranTypeDesc;

				if ($tmType == KEY_BANK_CASH_TO_CLIENT) {
					if (0 < $tmTran) {
						$ct = new ClientTransaction( $tmTran );
						$clCode = $ct->get( 'ctClient' );
						$client = new Client( $clCode );
						$name = $client->getDisplayName(  );
					}
				}


				if ($tmType == KEY_BANK_CASH_TO_INSCO) {
					if (0 < $tmTran) {
						$it = new InsCoTransaction( $tmTran );
						$icCode = $it->get( 'itInsCo' );
						$ins = new Insco( $icCode );
						$name = $ins->get( 'icName' );
					}
				}


				if ($tmType == KEY_BANK_CASH_TO_INTROD) {
					if (0 < $tmTran) {
						$nt = new IntroducerTransaction( $tmTran );
						$inCode = $nt->get( 'rtIntroducer' );
						$in = new Introducer( $inCode );
						$name = $in->get( 'inName' );
					}
				}

				$this->set( 'baCode', $tmBankCode );
				$this->set( 'tmType', $tranTypeDesc );
				$this->set( 'tmDesc', $name );
				$this->set( 'tmRef', $tmRef );
				$this->set( 'tmPaymentType', $tranPaymentTypeDesc );
				$this->set( 'tmTran', $tranTypeDesc );
				$this->set( 'tmPostingDate', uformatsqldate2( $tmPostingDate ) );
				$debit = '';
				$credit = '';

				if (0 <= $tmAmount) {
					$debit = uformatmoneywithcommas( $tmAmount );
				}


				if ($tmAmount < 0) {
					$credit = uformatmoneywithcommas( 0 - $tmAmount );
				}

				$this->set( 'debit', $debit );
				$this->set( 'credit', $credit );

				if ($needBalance == false) {
					$balanceToView = '';
					$this->set( 'sign', '' );
				}

				$this->set( 'balance', $balanceToView );
				$out .= $this->parse( $text );
				$currentRow = $nextRow;
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

			$this->set( 'cfBalance', $balanceToView );
			return $out;
		}

		function whenmainpremium($text) {
			$do = false;

			if ($this->get( 'mainNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaddlpremium($text) {
			$do = false;

			if ($this->get( 'addlNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenfees($text) {
			$do = false;

			if ($this->get( 'fees' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesVAT' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesCommission' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function companytotal($text) {
			if ($this->doCompanyTotal == false) {
				return '';
			}

			$this->set( 'origTotal', uformatmoneywithcommas( $this->origTotal ) );
			$this->set( 'balanceTotal', uformatmoneywithcommas( $this->balanceTotal ) );
			$this->origTotal = 0;
			$this->balanceTotal = 0;
			$out = $this->parse( $text );
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