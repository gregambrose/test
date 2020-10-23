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

	class bankstatementtemplate {
		var $isByPeriod = false;

		function bankstatementtemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportType' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'found' );
			$this->addField( 'selectedType' );
			$this->addField( 'user' );
			$this->addField( 'periodDesc' );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedPeriodCode' );
			$this->addField( 'selectedYear' );
			$this->addField( 'bfBalance' );
			$this->addField( 'cfBalance' );
			$this->addField( 'movement' );
			$this->addField( 'total' );
			$this->addField( 'debit' );
			$this->addField( 'credit' );
			$this->addField( 'sign' );
			$this->addField( 'bfSign' );
			$this->addField( 'cfSign' );
			$this->addField( 'movSign' );
			$this->addField( 'totalSign' );
			$this->addField( 'transFound' );
			$this->setFieldType( 'debit', 'MONEY' );
			$this->setFieldType( 'credit', 'MONEY' );
			$this->setFieldType( 'bfBalance', 'MONEY' );
			$this->setFieldType( 'cfBalance', 'MONEY' );
			$this->setFieldType( 'total', 'MONEY' );
			$this->setFieldType( 'movement', 'MONEY' );
			$this->set( 'transFound', 0 - 1 );
			$this->setHeader( SITE_NAME );
			$this->setMoneyShouldHaveCommas( true );
		}

		function setbyperiod($bool) {
			$this->isByPeriod = $bool;
		}

		function showtype($type) {
			$ty = $this->get( 'selectedType' );

			if ($ty == $type) {
				return 'selected';
			}

			return '';
		}

		function listperiods($text) {
			$selectedPeriod = $this->get( 'selectedPeriod' );
			$out = '';
			$p = 1;

			while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
				$this->set( 'period', $p );

				if ($selectedPeriod == $p) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
				++$p;
			}

			return $out;
		}

		function listyears($text) {
			$selectedYear = $this->get( 'selectedYear' );
			$q = 'SELECT ayCode FROM accountingYears ORDER BY ayYear';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ayCode = $row['ayCode'];
				$ay = new AccountingYear( $ayCode );
				$this->set( 'code', $ayCode );
				$desc = $ay->getForHTML( 'ayYear' );

				if (strlen( trim( $desc ) ) == 0) {
					$desc = 'blank';
				}

				$this->set( 'year', $ay->getForHTML( 'ayName' ) );

				if ($selectedYear == $ayCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtransactiontypes($text) {
			$selectedType = $this->get( 'selectedType' );
			$q = 'SELECT byCode, byName FROM bankTransTypes ORDER BY bySequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$byCode = $row['byCode'];
				$byName = $row['byName'];
				$this->set( 'code', $byCode );
				$this->set( 'desc', $byName );

				if ($selectedType == $byCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listusers($text) {
			$userCode = $this->get( 'user' );
			$q = 'SELECT usCode FROM users ORDER BY usLastName, usFirstName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$usCode = $row['usCode'];
				$user = new User( $usCode );
				$this->set( 'usCode', $usCode );
				$name = $user->getFullName(  );

				if (strlen( trim( $name ) ) == 0) {
					$name = 'blank';
				}

				$this->set( 'usName', $name );

				if ($userCode == $usCode) {
					$this->set( 'userSelected', 'selected' );
				} 
else {
					$this->set( 'userSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whenreporttoview($text) {
			$found = $this->get( 'transFound' );

			if ($found < 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaperiod($text) {
			$ok = $this->isByPeriod;

			if ($ok != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennotaperiod($text) {
			$ok = $this->isByPeriod;

			if ($ok == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
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
			$movement = 0;
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
				$movement += $tmAmount;

				if (0 < $balance) {
					$balanceToView = $balance;
					$this->set( 'sign', '' );
				} 
else {
					$balanceToView = 0 - $balance;
					$this->set( 'sign', 'OD' );
				}

				$this->set( 'balance', uformatmoneywithcommas( $balanceToView ) );
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
					$debit = $tmAmount;
				}


				if ($tmAmount < 0) {
					$credit = 0 - $tmAmount;
				}

				$this->set( 'debit', $debit );
				$this->set( 'credit', $credit );

				if ($needBalance == false) {
					$this->set( 'sign', '' );
					$this->set( 'balance', '' );
				}

				$out .= $this->parse( $text );
				$currentRow = $nextRow;
			}

			$total = $movement;

			if (0 < $total) {
				$totalSign = 'OD';
			} 
else {
				$total = 0 - $total;
				$totalSign = '';
			}

			$this->set( 'totalSign', $totalSign );
			$this->set( 'total', $total );
			$this->set( 'movement', 0 - $movement );
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