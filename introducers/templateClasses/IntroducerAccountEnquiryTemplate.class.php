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

	class introduceraccountenquirytemplate {
		var $itemToShowAllocation = null;

		function introduceraccountenquirytemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'inCode' );
			$this->addField( 'inName' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'clearedItems' );
			$this->addField( 'includeTrans' );
			$this->addField( 'ageEffective' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'directItems', 'checked' );
			$this->setFieldType( 'paidItems', 'checked' );
			$this->set( 'includeTrans', 'P' );
			$this->set( 'clearedItems', 0 );
			$this->set( 'directItems', 0 );
			$this->set( 'paidItems', 0 );
			$this->itemToShowAllocation = 0;
			$this->sortBy = '';
			$this->ascending = false;
			$this->setProcess( '_displayList', 'display' );
		}

		function setintroducer($inCode) {
			$int = new Introducer( $inCode );
			$this->int = &$int;

			$this->setAll( $int->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandeditintroducer($inCode) {
			$this->setIntroducer( $inCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getintroducer() {
			return $this->int;
		}

		function _displaylist($template, $input) {
			return false;
		}

		function showinclude($type) {
			$x = $this->get( 'includeTrans' );

			if ($x == $type) {
				return 'checked';
			}

			return '';
		}

		function showageeffective() {
			$x = $this->get( 'ageEffective' );

			if ($x == 1) {
				return 'checked';
			}

			return '';
		}

		function _dobeforeanyprocessing($input) {
			global $periodTo;

			$this->itemToShowAllocation = 0;

			if (!isset( $this->int )) {
				return false;
			}

			$this->setAll( $input );
			$ageEffective = $this->get( 'ageEffective' );
			$includeTrans = $this->get( 'includeTrans' );
			$aged = $this->int->getAgedCredit( $includeTrans, $ageEffective );
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

		function listtransactions($text) {
			global $userCode;
			global $periodTo;

			$int = $this->getIntroducer(  );
			$inCode = $int->getKeyValue(  );
			$fromDate = $this->get( 'fromDate' );
			$fromDate = umakesqldate2( $fromDate );
			$toDate = $this->get( 'toDate' );
			$toDate = umakesqldate2( $toDate );
			$clearedItems = $this->get( 'clearedItems' );
			$paidItems = $this->get( 'paidItems' );
			$directItems = $this->get( 'directItems' );
			$sortBy = $this->sortBy;
			$includeTrans = $this->get( 'includeTrans' );
			$q = '' . 'DROP TABLE IF EXISTS tmpIn' . $userCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'CREATE  TABLE tmpIn' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmIntrodTran			INT,
				tmTransDate			DATE,
				tmEffectiveDate		DATE,
				tmClientName		VARCHAR(200),
				tmPolicyNo			VARCHAR(50)
			)';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'INSERT INTO tmpIn' . $userCode . ' (tmIntrodTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
			$q .= '' . 'SELECT rtCode, rtPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
					FROM introducerTransactions, policyTransactions, inscoTransactions, clientTransactions, clients, policies
					WHERE rtIntroducer=' . $inCode . '
			    	AND plCode = ptPolicy
					AND itCode = ptMainInsCoTran
					AND rtPolicyTran = ptCode
					AND ptClientTran = ctCode
					AND ptClient = clCode ';

			if ($paidItems == 1) {
				$q .= ' AND  itBalance = 0 ';
			}


			if ($fromDate != null) {
				$q .= '' . 'AND rtPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . 'AND rtPostingDate <= \'' . $toDate . '\' ';
			}


			if ($clearedItems == 1) {
				$q .= 'AND rtBalance = 0 ';
			} 
else {
				$q .= 'AND rtBalance != 0 ';
			}


			if ($includeTrans == 'P') {
				$q .= '' . ' AND rtPostingDate <= \'' . $periodTo . '\'';
			}


			if ($includeTrans == 'E') {
				$q .= '' . ' AND rtEffectiveDate <= \'' . $periodTo . '\' AND rtPostingDate <= \'' . $periodTo . '\'';
			}


			if ($directItems == 1) {
				$q .= 'AND rtDirect = 1 ';
			}

			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$order = ' ORDER BY rtCode DESC';

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
				$q = '' . 'UPDATE tmpIn' . $userCode . ', introducerTransactions, policies, policyTransactions
					SET tmClientName = plPolicyHolder
					WHERE tmIntrodTran = rtCode
					AND ptCode = rtPolicyTran
					AND plCode = ptPolicy
				 	AND plPolicyHolder IS NOT NULL
				 	AND plPolicyHolder != \'\'';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}
			}

			$q = '' . 'SELECT tmIntrodTran FROM tmpIn' . $userCode;

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

			$introdTrans = array(  );
			$otherTrans = array(  );

			while ($row = udbgetrow( $result )) {
				$rtCode = $row['tmIntrodTran'];
				$introdTrans[] = $rtCode;
			}

			$otherTrans = array(  );

			if (( $directItems != 1 && $paidItems != 1 )) {
				$q = '' . 'SELECT * FROM introducerTransactions WHERE rtIntroducer=' . $inCode . '
					AND rtTransType != \'I\' ';

				if ($fromDate != null) {
					$q .= '' . 'AND rtPostingDate >= \'' . $fromDate . '\' ';
				}


				if ($toDate != null) {
					$q .= '' . 'AND rtPostingDate <= \'' . $toDate . '\' ';
				}


				if ($clearedItems == 1) {
					$q .= 'AND rtBalance = 0 ';
				} 
else {
					$q .= 'AND rtBalance != 0 ';
				}

				$order = ' ORDER BY rtCode DESC';

				if ($sortBy == 'transDate') {
					$order = '' . ' ORDER by rtPostingDate ' . $asc . ', rtCode DESC';
				}

				$q .= $order;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$otherTrans = array(  );

				while ($row = udbgetrow( $result )) {
					$rtCode = $row['rtCode'];
					$otherTrans[] = $rtCode;
				}
			}

			$combined = $otherTrans;
			foreach ($introdTrans as $item) {
				$combined[] = $item;
			}

			$out = '';
			foreach ($combined as $rtCode) {
				$rt = new IntroducerTransaction( $rtCode );
				$this->rt = &$rt;

				$rtTransType = $rt->get( 'rtTransType' );

				if ($rtTransType == 'I') {
					$ptCode = $rt->get( 'rtPolicyTran' );
					$pt = new PolicyTransaction( $ptCode );
					$ptDebit = $pt->get( 'ptDebit' );

					if ($ptDebit == 1) {
						$mult = 1;
					} 
else {
						$mult = 0 - 1;
					}

					$this->set( 'mainGross', $pt->get( 'ptGross' ) * $mult );
					$this->set( 'mainCommission', $pt->get( 'ptCommission' ) * $mult );
					$this->set( 'mainRate', $pt->get( 'ptCommissionRate' ) );
					$this->set( 'mainIPT', $pt->get( 'ptGrossIPT' ) * $mult );
					$this->set( 'addlGross', $pt->get( 'ptAddlGross' ) * $mult );
					$this->set( 'addlCommission', $pt->get( 'ptAddlCommission' ) * $mult );
					$this->set( 'addlRate', $pt->get( 'ptAddlCommissionRate' ) );
					$this->set( 'addlIPT', $pt->get( 'ptAddlIPT' ) * $mult );
					$this->set( 'addOnGross', $pt->get( 'ptAddOnGross' ) * $mult );
					$this->set( 'addOnCommission', $pt->get( 'ptAddOnCommission' ) * $mult );
					$this->set( 'addOnRate', $pt->get( 'ptAddOnCommissionRate' ) );
					$this->set( 'addOnIPT', $pt->get( 'ptAddOnIPT' ) * $mult );
					$this->set( 'fees', $pt->get( 'ptEngineeringFee' ) * $mult );
					$this->set( 'feesVAT', $pt->get( 'ptEngineeringFeeVAT' ) * $mult );
					$this->set( 'feesRate', $pt->get( 'ptEngineeringFeeCommRate' ) );
					$this->set( 'feesCommission', $pt->get( 'ptEngineeringFeeComm' ) * $mult );
					$this->set( 'mainGrossFormatted', uformatmoneywithcommas( $pt->get( 'ptGross' ) * $mult ) );
					$this->set( 'mainCommissionFormatted', uformatmoneywithcommas( $pt->get( 'ptCommission' ) * $mult ) );
					$this->set( 'mainRateFormatted', $pt->getAsMoneyWithCommas( 'ptCommissionRate' ) );

					if (( $pt->get( 'ptCommissionRate' ) == 0 && $pt->get( 'ptCommission' ) != 0 )) {
						$this->set( 'mainRateFormatted', 'flat' );
					}

					$this->set( 'mainIPTFormatted', uformatmoneywithcommas( $pt->get( 'ptGrossIPT' ) * $mult ) );
					$this->set( 'addlGrossFormatted', uformatmoneywithcommas( $pt->get( 'ptAddlGross' ) * $mult ) );
					$this->set( 'addlCommissionFormatted', uformatmoneywithcommas( $pt->get( 'ptAddlCommission' ) * $mult ) );
					$this->set( 'addlRateFormatted', $pt->getAsMoneyWithCommas( 'ptAddlCommissionRate' ) );

					if (( $pt->get( 'ptAddlCommissionRate' ) == 0 && $pt->get( 'ptAddlCommission' ) != 0 )) {
						$this->set( 'addlRateFormatted', 'flat' );
					}

					$this->set( 'addlIPTFormatted', uformatmoneywithcommas( $pt->get( 'ptAddlIPT' ) * $mult ) );
					$this->set( 'addOnGrossFormatted', uformatmoneywithcommas( $pt->get( 'ptAddOnGross' ) * $mult ) );
					$this->set( 'addOnCommissionFormatted', uformatmoneywithcommas( $pt->get( 'ptAddOnCommission' ) * $mult ) );
					$this->set( 'addOnRateFormatted', $pt->getAsMoneyWithCommas( 'ptAddOnCommissionRate' ) );

					if (( $pt->get( 'ptAddOnCommissionRate' ) == 0 && $pt->get( 'ptAddOnCommission' ) != 0 )) {
						$this->set( 'addOnRateFormatted', 'flat' );
					}

					$this->set( 'addOnIPTFormatted', uformatmoneywithcommas( $pt->get( 'ptAddOnIPT' ) * $mult ) );
					$this->set( 'feesFormatted', uformatmoneywithcommas( $pt->get( 'ptEngineeringFee' ) * $mult ) );
					$this->set( 'feesVATFormatted', uformatmoneywithcommas( $pt->get( 'ptEngineeringFeeVAT' ) * $mult ) );
					$this->set( 'feesRateFormatted', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeCommRate' ) );
					$this->set( 'icFeeDesc', $pt->get( 'ptEngineeringFeeDesc' ) );

					if (( $pt->get( 'ptEngineeringFeeCommRate' ) == 0 && $pt->get( 'ptEngineeringFee' ) != 0 )) {
						$this->set( 'feesRateFormatted', 'flat' );
					}

					$this->set( 'feesCommissionFormatted', uformatmoneywithcommas( $pt->get( 'ptEngineeringFeeComm' ) * $mult ) );
					$date = $rt->getForHTML( 'rtPostingDate' );

					if ($rt->get( 'rtPaidDate' ) == '0000-00-00') {
						$paidDate = '';
					} 
else {
						$paidDate = $rt->getForHTML( 'rtPaidDate' );
					}

					$ptCode = $rt->get( 'rtPolicyTran' );
					$pt = new PolicyTransaction( $ptCode );
					$polNo = $pt->get( 'ptPolicyNumber' );
					$direct = $pt->get( 'ptDirect' );

					if ($direct == 1) {
						$direct = 'Y';
					} 
else {
						$direct = 'N';
					}

					$effectiveDate = $pt->getForHTML( 'ptEffectiveFrom' );
					$grossToCl = $pt->get( 'ptClientTotal' ) - $pt->get( 'ptBrokerFee' ) - $pt->get( 'ptBrokerVAT' ) - $pt->get( 'ptClientDiscount' );
					$grossToCl = uformatmoneywithcommas( $grossToCl );
					$grossIncIPT = $pt->get( 'ptGrossIncIPT' ) + $pt->get( 'ptAddlGrossIncIPT' ) + $pt->get( 'ptAddOnGrossIncIPT' ) + $pt->get( 'ptEngineeringFee' ) + $pt->get( 'ptEngineeringFeeVAT' );
					$grossIncIPT = uformatmoneywithcommas( $grossIncIPT );
					$discount = $pt->getAsMoneyWithCommas( 'ptClientDiscount' );
					$netComm = $pt->get( 'ptCommission' ) + $pt->get( 'ptAddlCommission' ) + $pt->get( 'ptAddOnCommission' ) + $pt->get( 'ptEngineeringFeeComm' ) - $pt->get( 'ptClientDiscount' );
					$netComm = uformatmoneywithcommas( $netComm );
					$clCode = $pt->get( 'ptClient' );

					if (0 < $clCode) {
						$client = new Client( $clCode );
						$clName = $client->getDisplayName(  );
					} 
else {
						$clName = '';
					}

					$plCode = $pt->get( 'ptPolicy' );
					$policy = new Policy( $plCode );
					$policyHolder = $policy->get( 'plPolicyHolder' );
					$type = $pt->get( 'ptTransType' );
					$tranType = new PolicyTransactionType( $type );
					$transType = $tranType->get( 'pyName' );
					$this->set( 'rtCode', $rt->getKeyValue(  ) );
					$this->set( 'client', $clName );
					$this->set( 'policyHolder', $policyHolder );
					$this->set( 'transDate', $date );
					$this->set( 'effectiveDate', $effectiveDate );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'direct', $direct );
					$this->set( 'ptCode', $ptCode );
					$this->set( 'invNo', $rt->getForHTML( 'rtInvoiceNo' ) );
					$this->set( 'polNo', $polNo );
					$this->set( 'transType', $transType );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'grossIncIPT', $grossIncIPT );
					$this->set( 'discount', $discount );
					$this->set( 'netComm', $netComm );
					$this->set( 'grossToClient', $grossToCl );
					$this->set( 'rtCalcOn', $rt->getAsMoneyWithCommas( 'rtCalcOn' ) );
					$this->set( 'rtRate', $rt->getAsMoneyWithCommas( 'rtRate' ) );

					if (( $rt->get( 'rtRate' ) == 0 && $rt->get( 'rtOriginal' ) != 0 )) {
						$this->set( 'rtRate', 'flat' );
					}

					$this->set( 'orig', $rt->getAsMoneyWithCommas( 'rtOriginal' ) );
					$this->set( 'balance', $rt->getAsMoneyWithCommas( 'rtBalance' ) );
					$this->set( 'paid', $rt->getAsMoneyWithCommas( 'rtPaid' ) );
				}


				if (( ( $rtTransType == 'C' || $rtTransType == 'R' ) || $rtTransType == 'J' )) {
					$this->set( 'mainGross', '' );
					$this->set( 'mainCommission', '' );
					$this->set( 'mainRate', '' );
					$this->set( 'mainIPT', '' );
					$this->set( 'addlGross', '' );
					$this->set( 'addlCommission', '' );
					$this->set( 'addlRate', '' );
					$this->set( 'addlIPT', '' );
					$this->set( 'addOnGross', '' );
					$this->set( 'addOnCommission', '' );
					$this->set( 'addOnRate', '' );
					$this->set( 'addOnIPT', '' );
					$this->set( 'fees', '' );
					$this->set( 'feesVAT', '' );
					$this->set( 'feesRate', '' );
					$this->set( 'feesCommission', '' );
					$this->set( 'mainGrossFormatted', '' );
					$this->set( 'mainCommissionFormatted', '' );
					$this->set( 'mainRateFormatted', '' );
					$this->set( 'mainRateFormatted', '' );
					$this->set( 'mainIPTFormatted', '' );
					$this->set( 'addlGrossFormatted', '' );
					$this->set( 'addlCommissionFormatted', '' );
					$this->set( 'addlRateFormatted', '' );
					$this->set( 'addlRateFormatted', '' );
					$this->set( 'addlIPTFormatted', '' );
					$this->set( 'addOnGrossFormatted', '' );
					$this->set( 'addOnCommissionFormatted', '' );
					$this->set( 'addOnRateFormatted', '' );
					$this->set( 'addOnRateFormatted', '' );
					$this->set( 'addOnIPTFormatted', '' );
					$this->set( 'feesFormatted', '' );
					$this->set( 'feesVATFormatted', '' );
					$this->set( 'feesRateFormatted', '' );
					$this->set( 'icFeeDesc', '' );
					$this->set( 'feesRateFormatted', '' );
					$this->set( 'feesCommissionFormatted', '' );
					$date = $rt->getForHTML( 'rtPostingDate' );

					if ($rt->get( 'rtPaidDate' ) == '0000-00-00') {
						$paidDate = 'N/A';
					} 
else {
						$paidDate = $rt->getForHTML( 'rtPaidDate' );
					}

					$this->set( 'rtCode', $rt->getKeyValue(  ) );
					$this->set( 'client', '' );
					$this->set( 'policyHolder', '' );
					$this->set( 'transDate', $date );
					$this->set( 'effectiveDate', '' );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'direct', '' );
					$this->set( 'ptCode', '' );
					$this->set( 'invNo', $rt->getForHTML( 'rtInvoiceNo' ) );
					$this->set( 'polNo', $rt->getForHTML( 'rtChequeNo' ) );

					if (0 < $rt->get( 'rtOriginal' )) {
						$transType = 'Introd. Recon. Receipt';
					} 
else {
						$transType = 'Introd. Recon. Payment';
					}


					if ($rtTransType == 'J') {
						$transType = 'Journal';
					}

					$this->set( 'transType', $transType );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'grossIncIPT', '' );
					$this->set( 'discount', '' );
					$this->set( 'netComm', '' );
					$this->set( 'grossToClient', '' );
					$this->set( 'rtCalcOn', '' );
					$this->set( 'rtRate', '' );
					$this->set( 'rtRate', '' );
					$this->set( 'orig', $rt->getAsMoneyWithCommas( 'rtOriginal' ) );
					$this->set( 'balance', $rt->getAsMoneyWithCommas( 'rtBalance' ) );
					$this->set( 'paid', $rt->getAsMoneyWithCommas( 'rtPaid' ) );
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

		function whenaddonpremium($text) {
			$do = false;

			if ($this->get( 'addOnNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnIPT' ) != 0) {
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

		function showifanyallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'rtCode' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'rtCode' )) {
				return '';
			}

			$out = '';
			$rt = &$this->rt;

			$rtTransType = $rt->get( 'rtTransType' );
			$rtCode = $rt->getKeyValue(  );
			$q = '' . 'SELECT * FROM introducerTransAllocations WHERE raCashTran=' . $rtCode . ' OR raOtherTran=' . $rtCode . '
					ORDER BY raCode DESC';
			$result = udbquery( $q );

			if ($result == null) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ra = new IntroducerTransAllocation( $row );
				$date = uformatourtimestamp2( $ra->get( 'raLastUpdateOn' ) );

				if (( $rtTransType == 'C' || $rtTransType == 'R' )) {
					$trans = sprintf( '%07d', $ra->get( 'raOtherTran' ) );
				} 
else {
					if ($rtTransType == 'I') {
						$trans = sprintf( '%07d', $ra->get( 'raCashTran' ) );
					} 
else {
						if ($rtTransType == 'J') {
							$trans = sprintf( '%07d', $ra->get( 'raCashTran' ) );
						} 
else {
							trigger_error( '' . 'incorrect typef for ict ' . $rtCode, E_USER_ERROR );
						}
					}
				}

				$amount = $ra->getForHTML( 'raAmount' );
				$initials = '';
				$x = $ra->get( 'raLastUpdateBy' );

				if (0 < $x) {
					$user = new User( $x );
					$initials = $user->getInitials(  );
				}

				$amount = $ra->getForHTML( 'raAmount' );
				$raType = $ra->get( 'raType' );
				$type = '';

				if ($raType == 'C') {
					$type = 'cash';
				}


				if ($raType == 'W') {
					$type = 'wr.off';
				}


				if ($raType == 'J') {
					$type = 'journal';
				}

				$invNo = '';
				$ref = '';

				if (( $raType == 'J' && $rtTransType != 'J' )) {
					$rtCode = $ra->get( 'raCashTran' );
				} 
else {
					$rtCode = $ra->get( 'raOtherTran' );
				}


				if (0 < $rtCode) {
					$rt = new IntroducerTransaction( $rtCode );
					$invNo = $rt->get( 'rtIntrodRef' );
					$ref = $rt->get( 'rtChequeNo' );
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