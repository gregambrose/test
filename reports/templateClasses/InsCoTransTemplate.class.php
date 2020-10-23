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

	class inscotranstemplate {
		var $transactions = null;
		var $page = null;
		var $sortType = null;
		var $doNormalItem = null;
		var $doClientTotal = null;
		var $doMonthTotal = null;
		var $doGrandTotal = null;
		var $subTotal = null;
		var $grandTotal = null;

		function inscotranstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportOrder' );
			$this->addField( 'insCo' );
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
			$this->insCos = null;
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

		function showinsurancecompanies($text) {
			$q = 'SELECT * FROM insuranceCompanies ORDER BY icName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$insCo = $this->get( 'insCo' );

			while ($row = udbgetrow( $result )) {
				$icCode = $row['icCode'];
				$icName = $row['icName'];

				if ($icCode == $insCo) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'icCode', $icCode );
				$this->set( 'icName', $icName );
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
			$icCode = null;
			$icCurrentCode = null;
			$icName = null;
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
				$itCode = &$this->transactions[$elem];

				$it = new InsCoTransaction( null );
				$found = $it->tryGettingRecord( $itCode );

				if ($found == false) {
					continue;
				}

				$icCode = $it->get( 'itInsCo' );
				$icName = '';
				$this->doCompanyTotal = false;

				if ($icCurrentCode != $icCode) {
					if ($icCurrentCode != null) {
						$this->doCompanyTotal = true;
						$this->set( 'nameForTotal', $this->get( 'icName' ) );
					}

					$icCurrentCode = $icCode;
					$ins = new Insco( $icCode );
					$icName = $ins->get( 'icName' );
					$this->set( 'icName', $icName );
				}

				$this->set( 'mainGross', $it->get( 'itGross' ) );
				$this->set( 'mainCommission', $it->get( 'itCommission' ) );
				$this->set( 'mainRate', $it->get( 'itCommissionRate' ) );
				$this->set( 'mainIPT', $it->get( 'itGrossIPT' ) );
				$this->set( 'addlGross', $it->get( 'itAddlGross' ) );
				$this->set( 'addlCommission', $it->get( 'itAddlCommission' ) );
				$this->set( 'addlRate', $it->get( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPT', $it->get( 'itAddlIPT' ) );
				$this->set( 'fees', $it->get( 'itEngineeringFee' ) );
				$this->set( 'feesVAT', $it->get( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRate', $it->get( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommission', $it->get( 'itEngineeringFeeComm' ) );
				$this->set( 'mainGrossFormatted', $it->getAsMoneyWithCommas( 'itGross' ) );
				$this->set( 'mainCommissionFormatted', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'mainRateFormatted', $it->getAsMoneyWithCommas( 'itCommissionRate' ) );
				$this->set( 'mainIPTFormatted', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'addlGrossFormatted', $it->getAsMoneyWithCommas( 'itAddlGross' ) );
				$this->set( 'addlCommissionFormatted', $it->getAsMoneyWithCommas( 'itAddlCommission' ) );
				$this->set( 'addlRateFormatted', $it->getAsMoneyWithCommas( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPTFormatted', $it->getAsMoneyWithCommas( 'itAddlIPT' ) );
				$this->set( 'feesFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feesVATFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRateFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommissionFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$date = $it->getForHTML( 'itPostingDate' );
				$paidDate = $it->getForHTML( 'itPaidDate' );
				$direct = $it->get( 'itDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$grossEtc = $it->get( 'itGross' ) + $it->get( 'itGrossIPT' ) + $it->get( 'itAddlGross' ) + $it->get( 'itAddlIPT' ) + $it->get( 'itEngineeringFee' ) + $it->get( 'itEngineeringFeeVAT' );
				$ptCode = $it->get( 'itPolicyTran' );

				if (0 < $ptCode) {
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
else {
					$itTransType = $it->get( 'itTransType' );

					if ($itTransType == 'C') {
						$transType = 'IC Payment';
					} 
else {
						$transType = 'IC Receipt';
					}

					$client = '';
					$effectiveDate = '';
					$polNo = '';
				}

				$this->set( 'itCode', $it->getKeyValue(  ) );
				$this->set( 'transDate', $date );
				$this->set( 'effectiveDate', $effectiveDate );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'invNo', $it->getForHTML( 'itInvoiceNo' ) );
				$this->set( 'polNo', $polNo );
				$this->set( 'client', $client );
				$this->set( 'transType', $transType );
				$this->set( 'direct', $direct );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'grossEtc', uformatmoneywithcommas( $grossEtc ) );
				$this->set( 'net', $it->getAsMoneyWithCommas( 'itNet' ) );
				$this->set( 'comm', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'ipt', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'fees', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feescomm', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$this->set( 'vat', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'orig', $it->getAsMoneyWithCommas( 'itOriginal' ) );
				$this->set( 'balance', $it->getAsMoneyWithCommas( 'itBalance' ) );
				$this->set( 'paid', $it->getAsMoneyWithCommas( 'itPaid' ) );
				$out .= $this->parse( $text );
				$this->origTotal += $it->get( 'itOriginal' );
				$this->balanceTotal += $it->get( 'itBalance' );
				$this->origGrand += $it->get( 'itOriginal' );
				$this->balanceGrand += $it->get( 'itBalance' );
				++$elem;
			}


			if ($icCurrentCode != null) {
				$this->doTransaction = false;
				$this->doCompanyTotal = true;
				$this->doGrandTotal = true;
				$this->set( 'nameForTotal', $this->get( 'icName' ) );
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
			$icCode = $this->get( 'insCo' );

			if (0 < $icCode) {
				$ins = new InsCo( $icCode );
				$insName = $ins->get( 'icName' );
			} 
else {
				$insName = '';
			}

			$this->set( 'insName', $insName );
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
			$reportOrder = $this->get( 'reportOrder' );
			$orderByMessage = '';

			if ($reportOrder == 'T') {
				$orderByMessage = 'Transaction Date Order';
			}


			if ($reportOrder == 'E') {
				$orderByMessage = 'Effective Date Order';
			}


			if ($reportOrder == 'C') {
				$orderByMessage = 'Client Name Order';
			}

			$this->set( 'orderByMessage', $orderByMessage );
		}

		function _makemonthfromdate($date) {
			$date = uformatsqldate2( $date );
			$out = trim( substr( $date, 2 ) );
			return $out;
		}
	}

?>