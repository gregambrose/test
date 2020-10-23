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

	class inscoaccountenquirytemplate {
		var $canAmend = null;
		var $itemToShowAllocation = null;
		var $sortBy = null;
		var $ascending = null;

		function inscoaccountenquirytemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'icCode' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'clearedItems' );
			$this->addField( 'directItems' );
			$this->addField( 'clientPaid' );
			$this->addField( 'searchText' );
			$this->addField( 'includeTrans' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'directItems', 'checked' );
			$this->setFieldType( 'clientPaid', 'checked' );
			$this->itemToShowAllocation = 0;
			$this->canAmend = false;
			$this->sortBy = '';
			$this->ascending = false;
			$this->set( 'includeTrans', 'P' );
			$this->setProcess( '_displayList', 'display' );
		}

		function setinsco($icCode) {
			$insco = new Insco( $icCode );
			$this->insco = &$insco;

			$this->setAll( $insco->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setcanamend($ok) {
			$this->canAmend = $ok;
		}

		function setandeditinsco($icCode) {
			$this->setInsco( $icCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getinsco() {
			return $this->insco;
		}

		function _displaylist($template, $input) {
			return false;
		}

		function _dobeforeanyprocessing($input) {
			if (isset( $input['includeTrans'] )) {
				$includeTrans = $input['includeTrans'];
			} 
else {
				$includeTrans = $this->get( 'includeTrans' );
			}

			$this->itemToShowAllocation = 0;

			if (!isset( $this->insco )) {
				return false;
			}

			$aged = $this->insco->getAgedCredit( $includeTrans );
			$this->set( 'currentAge', uformatmoneywithcommas( $aged[0] ) );
			$this->set( 'oneMonthAge', uformatmoneywithcommas( $aged[1] ) );
			$this->set( 'twoMonthAge', uformatmoneywithcommas( $aged[2] ) );
			$this->set( 'threeOrOverMonthAge', uformatmoneywithcommas( $aged[3] ) );
			$this->set( 'totalAged', uformatmoneywithcommas( $aged[4] ) );

			if (( isset( $input['sortBy'] ) && $input['sortBy'] != '' )) {
				if ($this->sortBy == $input['sortBy']) {
					if ($this->ascending == true) {
						$this->ascending = false;
					} 
else {
						$this->ascending = true;
					}
				}

				$this->sortBy = $input['sortBy'];
			}

			return false;
		}

		function setviewallocation($item) {
			$this->itemToShowAllocation = $item;
		}

		function showinclude($type) {
			$x = $this->get( 'includeTrans' );

			if ($x == $type) {
				return 'checked';
			}

			return '';
		}

		function listtransactions($text) {
			global $userCode;
			global $periodTo;

			$insco = $this->getInsco(  );
			$icCode = $insco->getKeyValue(  );
			$fromDate = $this->get( 'fromDate' );
			$fromDate = umakesqldate2( $fromDate );
			$toDate = $this->get( 'toDate' );
			$toDate = umakesqldate2( $toDate );
			$clearedItems = $this->get( 'clearedItems' );
			$directItems = $this->get( 'directItems' );
			$clientPaid = $this->get( 'clientPaid' );
			$searchText = umakeinputsafe( $this->get( 'searchText' ) );
			$sortBy = $this->sortBy;
			$includeTrans = $this->get( 'includeTrans' );
			$needFullDetail = false;

			if ($clientPaid == 1) {
				$needFullDetail = true;
			}


			if ($sortBy == 'policyNo') {
				$needFullDetail = true;
			}


			if ($sortBy == 'effectiveDate') {
				$needFullDetail = true;
			}


			if ($sortBy == 'clientName') {
				$needFullDetail = true;
			}

			$q = '' . 'DROP TABLE IF EXISTS tmp' . $userCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'CREATE  TABLE tmp' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmInsTran			INT,
				tmTransDate			DATE,
				tmEffectiveDate		DATE,
				tmClientName		VARCHAR(200),
				tmPolicyNo			VARCHAR(50)
			)';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if ($needFullDetail == false) {
				$q = '' . 'INSERT INTO tmp' . $userCode . ' (tmInsTran) ';
				$q .= '' . 'SELECT itCode FROM inscoTransactions, policyTransactions, clientTransactions, clients, policies
				 WHERE itInsco=' . $icCode . '
			    AND plCode = ptPolicy
				 AND itPolicyTran = ptCode
				AND ptClientTran = ctCode
				AND ptClient = clCode ';

				if ($clientPaid == 1) {
					$q .= ' AND  ctBalance = 0 ';
				}
			} 
else {
				$q = '' . 'INSERT INTO tmp' . $userCode . ' (tmInsTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
				$q .= '' . 'SELECT itCode, itPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
					FROM inscoTransactions, policyTransactions, clientTransactions, clients, policies
					WHERE itInsco=' . $icCode . '
			    AND plCode = ptPolicy
					AND itPolicyTran = ptCode
					AND ptClientTran = ctCode
					AND ptClient = clCode ';

				if ($clientPaid == 1) {
					$q .= ' AND  ctBalance = 0 ';
				}
			}


			if ($searchText != '') {
				$q .= '' . '
				AND (
						ptPolicyNumber LIKE \'%' . $searchText . '%\'
					OR  clNameSort LIKE \'%' . $searchText . '%\'
					OR  ptInsCoRef LIKE \'%' . $searchText . '%\'
					OR  plPolicyHolder LIKE \'%' . $searchText . '%\')
				 ';
			}


			if ($fromDate != null) {
				$q .= '' . 'AND itPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . 'AND itPostingDate <= \'' . $toDate . '\' ';
			}


			if ($clearedItems == 1) {
				$q .= 'AND itBalance = 0 ';
			} 
else {
				$q .= 'AND itBalance != 0 ';
			}


			if ($directItems == 1) {
				$q .= 'AND itDirect = 1 ';
			}


			if ($includeTrans == 'P') {
				$q .= '' . ' AND ctPostingDate <= \'' . $periodTo . '\'';
			}


			if ($includeTrans == 'E') {
				$q .= '' . ' AND ctEffectiveDate <= \'' . $periodTo . '\' AND ctPostingDate <= \'' . $periodTo . '\'';
			}

			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$order = ' ORDER BY itCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by itPostingDate ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'effectiveDate') {
				$order = '' . ' ORDER by ptEffectiveFrom  ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'clientName') {
				$order = '' . ' ORDER by clNameSort  ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'policyNo') {
				$order = '' . ' ORDER by ptPolicyNumber  ' . $asc . ', itCode DESC';
			}

			$q .= $order;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if ($sortBy == 'clientName') {
				$q = '' . 'UPDATE tmp' . $userCode . ', inscoTransactions, policies, policyTransactions
					SET tmClientName = plPolicyHolder
					WHERE tmInsTran = itCode
					AND ptCode = itPolicyTran
					AND plCode = ptPolicy
				 	AND plPolicyHolder IS NOT NULL
				 	AND plPolicyHolder != \'\'';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}
			}

			$q = '' . 'SELECT tmInsTran FROM tmp' . $userCode;

			if ($sortBy == 'clientName') {
				$q .= '' . ' ORDER BY tmClientName ' . $asc;
			} 
else {
				$q .= ' ORDER BY tmCode';
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$polTrans = array(  );
			$otherTrans = array(  );

			while ($row = udbgetrow( $result )) {
				$itCode = $row['tmInsTran'];
				$polTrans[] = $itCode;
			}

			$q = '' . 'SELECT * FROM inscoTransactions WHERE itInsco=' . $icCode . '
				AND itTransType != \'I\' ';

			if ($fromDate != null) {
				$q .= '' . 'AND itPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . 'AND itPostingDate <= \'' . $toDate . '\' ';
			}


			if ($clearedItems == 1) {
				$q .= 'AND itBalance = 0 ';
			} 
else {
				$q .= 'AND itBalance != 0 ';
			}

			$order = ' ORDER BY itCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by itPostingDate ' . $asc . ', itCode DESC';
			}

			$q .= $order;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$otherTrans = array(  );

			while ($row = udbgetrow( $result )) {
				$itCode = $row['itCode'];
				$otherTrans[] = $itCode;
			}

			$combined = $otherTrans;
			foreach ($polTrans as $item) {
				$combined[] = $item;
			}

			$out = '';
			foreach ($combined as $itCode) {
				$it = new InscoTransaction( $itCode );
				$this->it = &$it;

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
				$this->set( 'itCode', $itCode );
				$this->set( 'policyHolder', '' );
				$itTransType = $it->get( 'itTransType' );
				$date = $it->getForHTML( 'itPostingDate' );

				if ($it->get( 'itPaidDate' ) == '0000-00-00') {
					if (( ( $itTransType == 'C' || $itTransType == 'R' ) || $itTransType == 'J' )) {
						$paidDate = 'N/A';
					} 
else {
						$paidDate = '';
					}
				} 
else {
					$paidDate = $it->getForHTML( 'itPaidDate' );
				}

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

					$type = $pt->get( 'ptTransType' );
					$tranType = new PolicyTransactionType( $type );
					$transType = $tranType->get( 'pyName' );
					$plCode = $pt->get( 'ptPolicy' );
					$policy = new Policy( $plCode );
					$policyHolder = $policy->get( 'plPolicyHolder' );
					$this->set( 'policyHolder', $policyHolder );
				} 
else {
					$bal = $it->get( 'itBalance' );

					if (0 < $it->get( 'itOriginal' )) {
						$transType = 'IC Rec Receipt';
					} 
else {
						$transType = 'IC Rec Payment';
					}


					if ($it->get( 'itTransType' ) == 'J') {
						$transType = 'Journal';
					}


					if ($it->get( 'itOriginal' ) != 0) {
						if ($bal != 0) {
							$transType .= ' Unalloc';
						}
					}

					$client = '';
					$feeDesc = '';
					$effectiveDate = '';
					$covDesc = '';
					$polNo = $it->get( 'itChequeNo' );
					$direct = '';
					$grossEtc = '';
				}

				$this->set( 'itCode', $it->getKeyValue(  ) );
				$this->set( 'transCode', sprintf( '%07d', $it->getKeyValue(  ) ) );
				$this->set( 'sysTran', sprintf( '%07d', $it->get( 'itSysTran' ) ) );
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

				if ($it->get( 'itWrittenOff' ) != 0) {
					$w = $it->getAsMoneyWithCommas( 'itWrittenOff' );
				} 
else {
					$w = '';
				}

				$this->set( 'wrOff', $w );

				if (( $it->get( 'itTransType' ) == 'C' || $it->get( 'itTransType' ) == 'R' )) {
					$this->set( 'grossEtc', '' );
					$this->set( 'mainGrossFormatted', '' );
					$this->set( 'mainIPTFormatted', '' );
					$this->set( 'mainCommissionFormatted', '' );
					$this->set( 'mainRateFormatted', '' );
					$this->set( 'wrOff', '' );
					$this->set( 'comm', '' );
				}

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

		function whenreconciliationtrans($text) {
			$type = $this->it->get( 'itTransType' );

			if (( $type != 'C' && $type != 'R' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhencanamend($text) {
			if ($this->canAmend != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showifanyallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'itCode' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'itCode' )) {
				return '';
			}

			$out = '';
			$it = &$this->it;

			$itCode = $it->getKeyValue(  );
			$itTransType = $it->get( 'itTransType' );

			if (( $itTransType == 'C' || $itTransType == 'R' )) {
				$fld = 'iaCashTran';
			} 
else {
				if ($itTransType == 'I') {
					$fld = 'iaOtherTran';
				} 
else {
					if ($itTransType == 'J') {
						$fld = 'iaCashTran';
					} 
else {
						trigger_error( '' . 'incorrect type for ict for ' . $itCode, E_USER_ERROR );
					}
				}
			}

			$q = '' . 'SELECT * FROM inscoTransAllocations WHERE iaCashTran=' . $itCode . ' OR iaOtherTran=' . $itCode . '
					ORDER BY iaCode DESC';
			$result = udbquery( $q );

			if ($result == null) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ia = new InsCoTransAllocation( $row );
				$date = uformatourtimestamp2( $ia->get( 'iaLastUpdateOn' ) );
				$iaType = $ia->get( 'iaType' );

				if ($iaType == 'J') {
					$trans = sprintf( '%07d', $ia->get( 'iaCashTran' ) );
				} 
else {
					$trans = sprintf( '%07d', $ia->get( 'iaOtherTran' ) );
				}

				$amount = $ia->getForHTML( 'iaAmount' );
				$initials = '';
				$x = $ia->get( 'iaLastUpdateBy' );

				if (0 < $x) {
					$user = new User( $x );
					$initials = $user->getInitials(  );
				}

				$amount = $ia->getForHTML( 'iaAmount' );
				$type = '';

				if ($iaType == 'C') {
					$type = 'cash';
				}


				if ($iaType == 'W') {
					$type = 'wr.off';
				}


				if ($iaType == 'J') {
					$type = 'journal';
				}

				$invNo = '';
				$ref = '';

				if (( $iaType == 'J' && $itTransType != 'J' )) {
					$itCode = $ia->get( 'iaCashTran' );
				} 
else {
					$itCode = $ia->get( 'iaOtherTran' );
				}


				if (0 < $itCode) {
					$it = new InsCoTransaction( $itCode );
					$invNo = $it->get( 'itInsCoRef' );
					$ref = $it->get( 'itChequeNo' );
				}

				$this->set( 'date', $date );
				$this->set( 'initials', $initials );
				$this->set( 'batch', $invNo );
				$this->set( 'trans', $trans );
				$this->set( 'ref', $ref );
				$this->set( 'type', $type );
				$this->set( 'amount', $amount );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>