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

	class introducerstranstemplate {
		var $transactions = null;
		var $page = null;
		var $sortType = null;
		var $doNormalItem = null;
		var $doClientTotal = null;
		var $doMonthTotal = null;
		var $doGrandTotal = null;
		var $subTotal = null;
		var $grandTotal = null;

		function introducerstranstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportOrder' );
			$this->addField( 'introducer' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'clearedItems' );
			$this->addField( 'directItems' );
			$this->addField( 'clientPaid' );
			$this->addField( 'transFound' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'directItems', 'checked' );
			$this->setFieldType( 'clientPaid', 'checked' );
			$this->setHeader( SITE_NAME );
			$this->Introducers = null;
			$this->set( 'reportOrder', 'T' );
		}

		function orderselected($type) {
			$reportOrder = $this->get( 'reportOrder' );

			if ($reportOrder == $type) {
				return 'selected';
			}

			return '';
		}

		function getreportorder() {
			return $this->get( 'reportOrder' );
		}

		function whenreporttoview($text) {
			if ($this->transactions == null) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showintroducers($text) {
			$q = 'SELECT * FROM introducers ORDER BY inName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$introducer = $this->get( 'introducer' );

			while ($row = udbgetrow( $result )) {
				$inCode = $row['inCode'];
				$inName = $row['inName'];

				if ($inCode == $introducer) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'inCode', $inCode );
				$this->set( 'inName', $inName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtransactions($text) {
			if ($this->transactions == null) {
				return '';
			}

			$this->subTotal = 0;
			$this->grandTotal = 0;
			$order = $this->get( 'reportOrder' );
			$currentMonth = null;
			$currentClient = null;
			$clientName = null;
			$monthName = null;
			$inCode = null;
			$inCurrentCode = null;
			$inName = null;
			$this->doTransaction = true;
			$this->doCompanyTotal = false;
			$this->doGrandTotal = false;
			$this->origTotal = 0;
			$this->balanceTotal = 0;
			$this->origGrand = 0;
			$this->balanceGrand = 0;
			$numOfTrans = count( $this->transactions );
			$out = '';
			$elem = 0;

			while ($elem <= $numOfTrans) {
				$rtCode = &$this->transactions[$elem];

				$rt = new IntroducerTransaction( null );
				$found = $rt->tryGettingRecord( $rtCode );

				if ($found == false) {
					continue;
				}

				$inCode = $rt->get( 'rtIntroducer' );
				$inName = '';
				$this->doCompanyTotal = false;

				if ($inCurrentCode != $inCode) {
					if ($inCurrentCode != null) {
						$this->doCompanyTotal = true;
						$this->set( 'nameForTotal', $this->get( 'inName' ) );
					}

					$inCurrentCode = $inCode;
					$int = new Introducer( $inCode );
					$inName = $int->get( 'inName' );
					$this->set( 'inName', $inName );
				}

				$date = $rt->getForHTML( 'rtPostingDate' );
				$paidDate = $rt->getForHTML( 'rtPaidDate' );
				$direct = $rt->get( 'rtDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$rtTransType = $rt->get( 'rtTransType' );

				if ($rtTransType != 'I') {
					$this->set( 'ptCode', '' );
					$this->set( 'addlCoverDesc', '' );
					$this->set( 'icFeeDesc', '' );
					$polNo = '';
					$effectiveDate = '';
					$client = '';
					$type = $rt->get( 'rtTransType' );
					$transType = $type;

					if ($type == 'R') {
						$transType = 'Recon.';
					}


					if ($type == 'C') {
						$transType = 'Payments';
					}
				} 
else {
					$ptCode = $rt->get( 'rtPolicyTran' );
					$pt = new PolicyTransaction( $ptCode );
					$polNo = $pt->get( 'ptPolicyNumber' );
					$effectiveDate = $pt->getForHTML( 'ptEffectiveFrom' );
					$this->set( 'ptCode', $ptCode );
					$covDesc = $pt->get( 'ptAddlCoverDesc' );
					$this->set( 'addlCoverDesc', $covDesc );
					$feeDesc = $pt->get( 'ptEngineeringFeeDesc' );
					$this->set( 'icFeeDesc', $feeDesc );
					$client = '';
					$clCode = $pt->get( 'ptClient' );

					if (0 < $clCode) {
						$cl = new Client( $clCode );
						$client = $cl->getDisplayName(  );
					}

					$plCode = $pt->get( 'ptPolicy' );

					if (0 < $plCode) {
						$pl = new Policy( $plCode );
						$ph = trim( $pl->get( 'plPolicyHolder' ) );

						if (0 < strlen( $ph )) {
							$client .= '' . '<br>' . $ph;
						}
					}

					$type = $pt->get( 'ptTransType' );
					$tranType = new PolicyTransactionType( $type );
					$transType = $tranType->get( 'pyName' );
				}

				$this->set( 'rtCode', $rt->getKeyValue(  ) );
				$this->set( 'transDate', $date );
				$this->set( 'effectiveDate', $effectiveDate );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'polNo', $polNo );
				$this->set( 'client', $client );
				$this->set( 'transType', $transType );
				$this->set( 'direct', $direct );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'orig', $rt->getAsMoneyWithCommas( 'rtOriginal' ) );
				$this->set( 'balance', $rt->getAsMoneyWithCommas( 'rtBalance' ) );
				$this->set( 'paid', $rt->getAsMoneyWithCommas( 'rtPaid' ) );
				$out .= $this->parse( $text );
				$this->origTotal += $rt->get( 'rtOriginal' );
				$this->balanceTotal += $rt->get( 'rtBalance' );
				$this->origGrand += $rt->get( 'rtOriginal' );
				$this->balanceGrand += $rt->get( 'rtBalance' );
				++$elem;
			}


			if ($inCurrentCode != null) {
				$this->doTransaction = false;
				$this->doCompanyTotal = true;
				$this->doGrandTotal = true;
				$this->set( 'nameForTotal', $this->get( 'inName' ) );
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

			$inCode = $this->get( 'introducer' );

			if (0 < $inCode) {
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
			$inCode = $this->get( 'introducer' );

			if (0 < $inCode) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function setheaderfields() {
			$inCode = $this->get( 'introducer' );

			if (0 < $inCode) {
				$int = new Introducer( $inCode );
				$intName = $int->get( 'inName' );
			} 
else {
				$intName = '';
			}

			$this->set( 'intName', $intName );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
			$clearedItems = $this->get( 'clearedItems' );

			if ($clearedItems == 1) {
				$clearedMessage = 'only cleared items';
			} 
else {
				$clearedMessage = '';
			}

			$this->set( 'clearedMessage', $clearedMessage );
			$directItems = $this->get( 'directItems' );

			if ($directItems == 1) {
				$directMessage = 'only direct items';
			} 
else {
				$directMessage = '';
			}

			$this->set( 'directMessage', $directMessage );
			$clientPaidItems = $this->get( 'clientPaid' );

			if ($clientPaidItems == 1) {
				$clientPaidMessage = 'only client paid  items';
			} 
else {
				$clientPaidMessage = '';
			}

			$this->set( 'clientPaidMessage', $clientPaidMessage );
		}

		function _makemonthfromdate($date) {
			$date = uformatsqldate2( $date );
			$out = trim( substr( $date, 2 ) );
			return $out;
		}
	}

?>