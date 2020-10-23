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

	class audittemplate {
		function audittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportType' );
			$this->addField( 'newBusiness' );
			$this->addField( 'reportSummary' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'processFromDate' );
			$this->addField( 'processToDate' );
			$this->addField( 'effectiveFromDate' );
			$this->addField( 'effectiveToDate' );
			$this->addField( 'found' );
			$this->addField( 'selectedType' );
			$this->addField( 'user' );
			$this->addField( 'sortBy' );
			$this->addField( 'sortByMessg' );
			$this->addField( 'periodDesc' );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedPeriodCode' );
			$this->addField( 'selectedYear' );
			$this->setHeader( SITE_NAME );
			$this->set( 'newBusiness', 'B' );
			$this->set( 'reportSummary', 'S' );
			$this->set( 'sortBt', 'A' );
		}

		function showtype($type) {
			$ty = $this->get( 'selectedType' );

			if ($ty == $type) {
				return 'selected';
			}

			return '';
		}

		function sortselected($type) {
			$ty = $this->get( 'sortBy' );

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

			if ($found == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listdetail($text) {
			global $userCode;

			$q = '' . 'SELECT *  FROM tmpA' . $userCode . ' ORDER BY tmCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$tmType = $row['tmType'];
				$tmAudit = $row['tmAudit'];
				$tmSysTran = $row['tmSysTran'];
				$tmTran = $row['tmTran'];
				$tmPostingDate = $row['tmPostingDate'];
				$tmEffectiveDate = $row['tmEffectiveDate'];
				$tmCreatedBy = $row['tmCreatedBy'];
				$tmCreatedOn = $row['tmCreatedOn'];
				$tmName = '';
				$tmPolicyNo = '';
				$tmCOB = '';
				$amt = 0;

				if (0 < $tmCreatedBy) {
					$user = new User( $tmCreatedBy );
					$initials = $user->getInitials(  );
				} 
else {
					$initials = '?';
				}

				$tranTypeDesc = fgetjournaltypedescription( $tmType );

				if ($tranTypeDesc == null) {
					$tranTypeDesc = $tmType;
				}


				if ($tmType == 'B') {
					$tranTypeDesc = 'Cash Batch';
					$cb = new CashBatch( $tmTran );
					$amt = $cb->get( 'btTotal' );
				}


				if ($tmType == 'C') {
					$tranTypeDesc = 'Client Account Trans.';
					$ct = new ClientTransaction( $tmTran );
					$clCode = $ct->get( 'ctClient' );
					$cl = new Client( $clCode );
					$tmName = $cl->getDisplayName(  );
					$amt = $ct->get( 'ctOriginal' );
				}


				if ($tmType == 'P') {
					$tranTypeDesc = 'Client Premium Trans.';
					$pt = new PolicyTransaction( $tmTran );
					$plCode = $pt->get( 'ptPolicy' );
					$pl = new Policy( null );
					$found = $pl->tryGettingRecord( $plCode );

					if ($found == false) {
						trigger_error( '' . 'cat get policy ' . $plCode . ' for audit rec ' . $tmAudit, E_USER_NOTICE );
						continue;
					}

					$pl = new Policy( $plCode );
					$tmPolicyNo = $pl->get( 'plPolicyNumber' );
					$cbCode = $pl->get( 'plClassOfBus' );
					$cob = new Cob( $cbCode );
					$tmCOB = $cob->get( 'cbName' );
					$clCode = $pl->get( 'plClient' );
					$cl = new Client( $clCode );
					$tmName = $cl->getDisplayName(  );
					$amt = $pt->get( 'ptGrossIncIPT' );
					$debit = $pt->get( 'ptDebit' );

					if ($debit != 1) {
						$amt = 0 - $amt;
					}
				}


				if ($tmType == 'I') {
					$tranTypeDesc = 'Ins. Co. Prem. Trans.';
					$it = new InsCoTransaction( $tmTran );
					$icCode = $it->get( 'itInsCo' );
					$itTransType = $it->get( 'itTransType' );
					$tranTypeDesc = 'Ins. Co.Trans.';

					if ($itTransType == 'P') {
						$tranTypeDesc = 'Ins. Co. Prem. Trans.';
					}


					if ($itTransType == 'C') {
						$tranTypeDesc = 'Ins. Co. Cash Trans.';
					}


					if ($itTransType == 'J') {
						$tranTypeDesc = 'Ins. Co. Jnlm. Trans.';
					}

					$ins = new Insco( $icCode );
					$tmName = $ins->get( 'icName' );
					$amt = $it->get( 'itOriginal' );
				}


				if ($tmType == 'R') {
					$tranTypeDesc = 'Introducer';
					$rt = new IntroducerTransaction( $tmTran );
					$inCode = $rt->get( 'rtIntroducer' );
					$introd = new Introducer( $inCode );
					$tmName = $introd->get( 'inName' );
					$amt = $rt->get( 'rtOriginal' );
				}


				if ($tmType == 'IO') {
					$tranTypeDesc = 'IBA Other Trans';
					$bt = new BankAccountTran( $tmTran );
					$amt = $bt->get( 'baAmount' );
				}

				$this->set( 'tmAudit', $tmAudit );
				$this->set( 'tmType', $tranTypeDesc );
				$this->set( 'tmSysTran', $tmSysTran );
				$this->set( 'tmTran', $tmTran );
				$this->set( 'tmPostingDate', uformatsqldate2( $tmPostingDate ) );
				$this->set( 'tmEffectiveDate', uformatsqldate2( $tmEffectiveDate ) );
				$this->set( 'tmCreatedOn', uformatourtimestamp2( $tmCreatedOn ) );
				$this->set( 'tmCreatedBy', $initials );
				$this->set( 'tmName', $tmName );
				$this->set( 'tmPolicyNo', $tmPolicyNo );
				$this->set( 'tmCOB', $tmCOB );
				$this->set( 'amount', uformatmoney( $amt ) );
				$out .= $this->parse( $text );
			}

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