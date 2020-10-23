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

	class introducerrecontemplate {
		var $canAmend = null;
		var $toPay = null;
		var $adjustments = null;
		var $_tempTotalToPay = null;
		var $_tempTotalCommAdj = null;
		var $_tempMustReconcile = null;
		var $transToView = null;
		var $transToReconcile = null;
		var $sortBy = null;
		var $ascending = null;
		var $mode = null;

		function introducerrecontemplate($html) {
			documentstemplate::documentstemplate( $html );
			$this->addField( 'mode' );
			$this->addField( 'inCode' );
			$this->addField( 'directItems' );
			$this->addField( 'clearedItems' );
			$this->addField( 'clientPaid' );
			$this->addField( 'searchText' );
			$this->addField( 'processDate' );
			$this->addField( 'introdRef' );
			$this->addField( 'postingRef' );
			$this->addField( 'paymentType' );
			$this->addField( 'totalToPay' );
			$this->addField( 'totalCommAdj' );
			$this->setFieldType( 'directItems', 'checked' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'clientPaid', 'checked' );
			$this->setFieldType( 'totalToPay', 'MONEY' );
			$this->setFieldType( 'totalCommAdj', 'MONEY' );
			$this->canAmend = false;
			$this->transToReconcile = 0;
			$this->sortBy = '';
			$this->ascending = false;
			$this->mode = '';
		}

		function setintroducer($inCode) {
			$introd = new Introducer( $inCode );
			$this->introducer = &$introd;

			$this->clearAll(  );
			$this->setAll( $introd->getAllForHTML(  ) );
			$this->mode = 'P';
			$this->set( 'mode', $this->mode );
			$this->toPay = null;
			$this->adjustments = null;
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			return false;
		}

		function settranstoreconcile($rtCode) {
			$this->transToReconcile = $rtCode;
			unset( $this[transToView] );
			$rt = new IntroducerTransaction( $rtCode );
			$rt->fetchExtraColumns(  );
			$inCode = $rt->get( 'rtIntroducer' );
			$this->setIntroducer( $inCode );
			$this->mode = 'R';
			$this->set( 'mode', $this->mode );
			$input = array(  );
			$this->_doBeforeAnyProcessing( $input );
			$rtCashBatch = $rt->get( 'rtCashBatch' );
			$this->set( 'cashBatch', sprintf( '%07d', $rtCashBatch ) );
			$this->set( 'code', sprintf( '%07d', $rtCode ) );
			$this->set( 'processDate', $rt->getForHTML( 'rtPostingDate' ) );
			$this->set( 'postingRef', $rt->getForHTML( 'rtChequeNo' ) );
			$this->set( 'paymentType', $rt->getForHTML( 'rtPaymentType' ) );
			$this->set( 'introdRef', $rt->getForHTML( 'rtIntrodRef' ) );
			$this->set( 'paymentTypeName', $rt->getForHTML( 'paymentTypeName' ) );
			$this->_tempMustReconcile = uformatmoney( $rt->get( 'rtBalance' ) );
			$this->_tempTotalCommAdj = 0;
			$this->set( 'viewTotalMustPay', $this->_tempMustReconcile );
			$this->set( 'viewTotalToPay', $this->_tempTotalToPay );
			$this->set( 'viewTotalCommAdj', $this->_tempTotalCommAdj );
			return false;
		}

		function settranstoview($rtCode) {
			$this->transToView = $rtCode;
			unset( $this[transToReconcile] );
			$rt = new IntroducerTransaction( $rtCode );
			$rt->fetchExtraColumns(  );
			$inCode = $rt->get( 'rtIntroducer' );
			$this->setIntroducer( $inCode );
			$t = $rt->get( 'rtTransType' );

			if ($t == 'C') {
				$this->mode = 'VP';
			} 
else {
				$this->mode = 'VR';
			}

			$this->set( 'mode', $this->mode );
			$this->set( 'processDate', $rt->getForHTML( 'rtPostingDate' ) );
			$this->set( 'introdRef', $rt->getForHTML( 'rtIntrodRef' ) );
			$this->set( 'postingRef', $rt->getForHTML( 'rtChequeNo' ) );
			$this->set( 'paymentType', $rt->getForHTML( 'rtPaymentType' ) );
			$this->set( 'paymentTypeName', $rt->getForHTML( 'paymentTypeName' ) );

			if ($this->mode == 'VR') {
				$this->set( 'viewTotalToPay', uformatmoney( $rt->get( 'rtPaid' ) ) );
				$this->set( 'viewTotalCommAdj', uformatmoney( $rt->get( 'rtWrittenOff' ) ) );
			}


			if ($this->mode == 'VP') {
				$this->set( 'viewTotalToPay', uformatmoney( 0 - $rt->get( 'rtPaid' ) ) );
				$this->set( 'viewTotalCommAdj', uformatmoney( $rt->get( 'rtWrittenOff' ) ) );
			}

			$this->set( 'code', sprintf( '%07d', $rtCode ) );
			$this->set( 'doCode', $rt->get( 'rtDocm' ) );
			$this->_makeArrayForExistingTrans(  );
			$this->_tempMustReconcile = $rt->getForHTML( 'rtBalance' );
			$this->set( 'viewTotalMustPay', 0 - $this->_tempMustReconcile );
			return false;
		}

		function _dobeforeanyprocessing($input) {
			global $userCode;

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


			if (( isset( $input['view'] ) || isset( $this->transToView ) )) {
				if (isset( $this->transToView )) {
					$rtCode = $this->transToView;
					$this->setTransToView( $rtCode );
				}

				return false;
			}

			$this->setAll( $input );

			if (!isset( $this->introducer )) {
				return false;
			}

			$aged = $this->introducer->getAgedCredit(  );
			$this->set( 'currentAge', uformatmoneywithcommas( $aged[0] ) );
			$this->set( 'oneMonthAge', uformatmoneywithcommas( $aged[1] ) );
			$this->set( 'twoMonthAge', uformatmoneywithcommas( $aged[2] ) );
			$this->set( 'threeOrOverMonthAge', uformatmoneywithcommas( $aged[3] ) );
			$this->set( 'totalAged', uformatmoneywithcommas( $aged[4] ) );
			$anyInput = false;
			foreach ($input as $key => $value) {
				if (substr( $key, 0, 6 ) == 'toPay_') {
					$anyInput = true;
					break;
				}


				if (substr( $key, 0, 4 ) == 'adj_') {
					$anyInput = true;
					break;
				}
			}


			if (!isset( $this->toPay )) {
				$this->toPay = array(  );
			}


			if (!isset( $this->adjustments )) {
				$this->adjustments = array(  );
			}


			if (!isset( $combined )) {
				$combined = array(  );
			}


			if ($anyInput == true) {
				$this->toPay = array(  );
				$this->adjustments = array(  );
				$combined = array(  );
				foreach ($input as $key => $value) {
					$value = uconvertmoneytointeger( $value );

					if (substr( $key, 0, 6 ) == 'toPay_') {
						$rtCode = substr( $key, 6 );
						$this->toPay[$rtCode] = $value;

						if (isset( $combined[$rtCode] )) {
							$combined[$rtCode] += $value;
						} 
else {
							$combined[$rtCode] = $value;
						}
					}


					if (substr( $key, 0, 4 ) == 'adj_') {
						$rtCode = substr( $key, 4 );
						$this->adjustments[$rtCode] = $value;

						if (isset( $combined[$rtCode] )) {
							$combined[$rtCode] += $value;
							continue;
						}

						$combined[$rtCode] = $value;
						continue;
					}
				}
			}

			$totalToPay = 0;
			$totalCommAdj = 0;
			foreach ($combined as $key => $value) {
				$rtCode = $key;

				if (isset( $this->toPay[$rtCode] )) {
					$toPay = $this->toPay[$rtCode];
				} 
else {
					$toPay = 0;
				}


				if (isset( $this->adjustments[$rtCode] )) {
					$adj = $this->adjustments[$rtCode];
				} 
else {
					$adj = 0;
				}


				if (( $toPay == 0 && $adj == 0 )) {
					continue;
				}

				$rt = new IntroducerTransaction( $rtCode );
				$bal = $rt->get( 'rtBalance' );
				$totalToPay += $toPay;
				$totalCommAdj += $adj;
			}


			if (!isset( $this->introducer )) {
				return false;
			}

			$introd = &$this->introducer;

			$inCode = $introd->getKeyValue(  );
			$fromDate = $this->get( 'fromDate' );
			$fromDate = umakesqldate2( $fromDate );
			$toDate = $this->get( 'toDate' );
			$toDate = umakesqldate2( $toDate );
			$clearedItems = $this->get( 'clearedItems' );
			$directItems = $this->get( 'directItems' );
			$clientPaid = $this->get( 'clientPaid' );
			$searchText = umakeinputsafe( $this->get( 'searchText' ) );
			$q = '' . 'DROP TABLE IF EXISTS tmpR' . $userCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'CREATE TABLE tmpR' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmIntrodTran		INT,
				tmTransDate			DATE,
				tmEffectiveDate		DATE,
				tmClientName		VARCHAR(200),
				tmPolicyNo			VARCHAR(50)
			)';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if ($clientPaid == 0) {
				$q = '' . 'INSERT INTO tmpR' . $userCode . ' (tmIntrodTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
				$q .= '' . 'SELECT rtCode, rtPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
				FROM introducerTransactions, policyTransactions, clients, policies
				WHERE rtIntroducer=' . $inCode . '
			    AND plCode = ptPolicy
				AND rtPolicyTran = ptCode
				AND clCode = ptClient
				AND (rtTransType != \'C\' AND rtTransType != \'R\') ';

				if ($searchText != '') {
					$q .= '' . '
					AND (
						ptPolicyNumber LIKE \'%' . $searchText . '%\'
					OR  clNameSort LIKE \'%' . $searchText . '%\'
					OR  ptInsCoRef LIKE \'%' . $searchText . '%\'
					OR  plPolicyHolder LIKE \'%' . $searchText . '%\')
				 ';
				}
			} 
else {
				$q = '' . 'INSERT INTO tmpR' . $userCode . ' (tmIntrodTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
				$q .= '' . 'SELECT rtCode, rtPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
					FROM introducerTransactions, policyTransactions, clientTransactions, clients, policies
					WHERE (rtTransType != \'C\' AND rtTransType != \'R\')
					AND rtIntroducer=' . $inCode . '
			        AND plCode = ptPolicy
					AND rtPolicyTran = ptCode
					AND ptClientTran = ctCode
					AND clCode = ptClient
					AND ctBalance = 0 ';

				if ($searchText != '') {
					$q .= '' . '
					AND (
						ptPolicyNumber LIKE \'%' . $searchText . '%\'
					OR  clNameSort LIKE \'%' . $searchText . '%\'
					OR  ptInsCoRef LIKE \'%' . $searchText . '%\'
					OR  plPolicyHolder LIKE \'%' . $searchText . '%\')
				 ';
				}
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


			if ($clearedItems != 1) {
				$q .= 'AND rtBalance != 0 ';
			}


			if ($directItems == 1) {
				$q .= 'AND rtDirect = 1 ';
			}

			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$sortBy = $this->sortBy;
			$order = ' ORDER BY rtCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by rtPostingDate ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'effectDate') {
				$order = '' . ' ORDER by ptEffectiveFrom ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'clientName') {
				$order = '' . ' ORDER by clNameSort ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'policyNo') {
				$order = '' . ' ORDER by ptPolicyNumber ' . $asc . ', rtCode DESC';
			}

			$q .= $order;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'UPDATE tmpR' . $userCode . ', introducerTransactions, policies, policyTransactions
					SET tmClientName = plPolicyHolder
					WHERE tmIntrodTran = rtCode
					AND ptCode = rtPolicyTran
					AND plCode = ptPolicy
				 	AND plPolicyHolder IS NOT NULL
				 	AND plPolicyHolder != \'\' ';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'SELECT * FROM  tmpR' . $userCode;

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

			$this->readIn = array(  );

			while ($row = udbgetrow( $result )) {
				$rtCode = $row['tmIntrodTran'];
				$this->readIn[$rtCode] = 1;
			}


			if (!isset( $this->toPay )) {
				$this->toPay = array(  );
			}


			if (!isset( $this->adjustments )) {
				$this->adjustments = array(  );
			}

			$needAdding = array(  );
			foreach ($this->toPay as $key => $value) {
				if ($value == 0) {
					continue;
				}


				if (isset( $this->readIn[$key] )) {
					continue;
				}

				$needAdding[$key] = 0;
			}

			foreach ($this->adjustments as $key => $value) {
				if ($value == 0) {
					continue;
				}


				if (isset( $this->readIn[$key] )) {
					continue;
				}

				$needAdding[$key] = 0;
			}

			$this->readIn = $needAdding + $this->readIn;

			if ($anyInput == true) {
				if ($this->mode == 'P') {
					$this->_tempTotalToPay = uformatmoney( $totalToPay );
					$this->_tempTotalCommAdj = uformatmoney( $totalCommAdj );
				}


				if ($this->mode == 'R') {
					$this->_tempTotalToPay = uformatmoney( 0 - $totalToPay );
					$this->_tempTotalCommAdj = uformatmoney( $totalCommAdj );
				}
			}

			return false;
		}

		function _makearrayforexistingtrans() {
			$rtCode = $this->transToView;

			if ($rtCode <= 0) {
				trigger_error( 'no trans to view' );
			}

			$this->readInAmount = array(  );
			$this->readInWrittenOff = array(  );
			$q = '' . 'SELECT raOtherTran, raAmount, raType
		FROM  introducerTransAllocations ,introducerTransactions, policyTransactions, clients
		WHERE raCashTran = ' . $rtCode . '
		AND raOtherTran = rtCode
		AND rtPolicyTran = ptCode
		AND clCode = ptClient';
			$sortBy = $this->sortBy;
			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$order = ' ORDER BY rtCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by rtPostingDate ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'effectDate') {
				$order = '' . ' ORDER by ptEffectiveFrom  ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'clientName') {
				$order = '' . ' ORDER by clNameSort  ' . $asc . ', rtCode DESC';
			}


			if ($sortBy == 'policyNo') {
				$order = '' . ' ORDER by ptPolicyNumber  ' . $asc . ', rtCode DESC';
			}

			$q .= $order;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$this->readIn = array(  );

			while ($row = udbgetrow( $result )) {
				$rtCode = $row['raOtherTran'];
				$raType = $row['raType'];
				$raAmount = $row['raAmount'];
				$this->readIn[$rtCode] = 1;

				if ($raType == 'C') {
					if (isset( $this->readInAmount[$rtCode] )) {
						$this->readInAmount[$rtCode] += $raAmount;
					} 
else {
						$this->readInAmount[$rtCode] = $raAmount;
					}
				}


				if ($raType == 'W') {
					if (isset( $this->readInWrittenOff[$rtCode] )) {
						$this->readInWrittenOff[$rtCode] += $raAmount;
						continue;
					}

					$this->readInWrittenOff[$rtCode] = $raAmount;
					continue;
				}
			}

		}

		function setspecificfields() {
			$this->set( 'viewTotalMustPay', $this->_tempMustReconcile );
			$this->set( 'viewTotalToPay', $this->_tempTotalToPay );
			$this->set( 'viewTotalCommAdj', $this->_tempTotalCommAdj );
		}

		function clearalldisplayfields() {
			if (!isset( ->transToReconcile )) {
				$this->set( 'processDate', '' );
				$this->set( 'introdRef', '' );
				$this->set( 'postingRef', '' );
				$this->set( 'paymentType', '' );
				$this->set( 'totalToPay', '' );
				$this->set( 'viewTotalToPay', '' );
			}

			$this->set( 'totalCommAdj', '' );
			$this->set( 'totalCommDue', '' );
			$this->set( 'totalCommPaid', '' );
			$this->set( 'viewTotalCommAdj', '' );
			$this->toPay = null;
			$this->adjustments = null;
		}

		function wheneditrequested($template, $input) {
			$this->toPay = array(  );
			$this->adjustments = array(  );
			documentstemplate::wheneditrequested( $template, $input );
		}

		function validate() {
			$date = trim( $this->get( 'processDate' ) );

			if ($date == '') {
				return 'You need to enter a process date';
			}

			$date = umakesqldate2( $date );

			if (fisdateinthisaccountingperiod( $date ) == false) {
				return 'posting date not in the current accounting period';
			}

			$introdRef = $this->get( 'introdRef' );
			$postingRef = $this->get( 'postingRef' );
			$paymentType = $this->get( 'paymentType' );

			if (strlen( trim( $introdRef ) ) == 0) {
				return 'You need to enter an introducers ref';
			}


			if (strlen( trim( $postingRef ) ) == 0) {
				return 'You need to enter a cheque number or BACS ref';
			}


			if ($paymentType <= 0) {
				return 'You need to specify a payment type';
			}


			if (isset( $this->transToReconcile )) {
				$totalMustPay = $this->_tempMustReconcile;
				$totalToPay = $this->_tempTotalToPay;

				if ($totalToPay != $totalMustPay) {
					return 'You need to fully allocate the payment';
				}
			}

			return null;
		}

		function postentries() {
			global $session;
			global $accountingYear;
			global $accountingPeriod;

			$processDate = $this->get( 'processDate' );
			$introdRef = $this->get( 'introdRef' );
			$postingRef = $this->get( 'postingRef' );
			$paymentType = $this->get( 'paymentType' );
			$inCode = $this->introducer->getKeyValue(  );
			$ok = udbcantabledotransactions( 'introducerTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$trans = array(  );
			foreach ($this->toPay as $key => $value) {
				if ($this->toPay[$key] == 0) {
					continue;
				}


				if (isset( $trans[$key] )) {
					continue;
				}

				$trans[$key] = 1;
			}

			foreach ($this->adjustments as $key => $value) {
				if ($this->adjustments[$key] == 0) {
					continue;
				}


				if (isset( $trans[$key] )) {
					continue;
				}

				$trans[$key] = 1;
			}

			$totalToPay = 0;
			$totalAdj = 0;
			foreach ($trans as $key => $value) {
				$rtCode = $key;

				if (isset( $this->toPay[$key] )) {
					$toPay = (double)$this->toPay[$key];
				} 
else {
					$toPay = 0;
				}


				if (isset( $this->adjustments[$key] )) {
					$adj = (double)$this->adjustments[$key];
				} 
else {
					$adj = 0;
				}


				if (( $toPay == 0 && $adj == 0 )) {
					continue;
				}

				$rt = new IntroducerTransaction( $rtCode );
				$rtPaidYear = $rt->get( 'rtPaidYear' );
				$rtPaidPeriod = $rt->get( 'rtPaidPeriod' );

				if (( 0 < $rtPaidYear || 0 < $rtPaidPeriod )) {
					trigger_error( '' . 'item already paid ' . $rtCode, E_USER_ERROR );
				}

				$rtPaid = $rt->get( 'rtPaid' );
				$rtPaid += $toPay;
				$rt->set( 'rtPaid', $rtPaid );
				$rtWrittenOff = $rt->get( 'rtWrittenOff' );
				$rtWrittenOff += $toPay;
				$rt->set( 'rtWrittenOff', $adj );
				$bal = $rt->get( 'rtBalance' );
				$rt->recalcBalance(  );
				$bal = $rt->get( 'rtBalance' );

				if ($bal == 0) {
					$rt->set( 'rtPaidDate', $processDate );
					$rt->set( 'rtPaidYear', $accountingYear );
					$rt->set( 'rtPaidPeriod', $accountingPeriod );
				} 
else {
					$rt->set( 'rtPaidDate', '' );
					$rt->set( 'rtPaidYear', 0 );
					$rt->set( 'rtPaidPeriod', 0 );
				}

				$rt->update(  );
				$totalToPay += $toPay;
				$totalAdj += $adj;
			}


			if ($this->transToReconcile <= 0) {
				$tnCode = fcreatesystemtran(  );
				$introdTrans = new IntroducerTransaction( null );
				$introdTrans->set( 'rtIntroducer', $inCode );
				$introdTrans->set( 'rtTransType', 'C' );
				$introdTrans->set( 'rtSysTran', $tnCode );
				$introdTrans->set( 'rtTransDesc', 'Introducer Trans' );
				$introdTrans->set( 'rtPostingDate', $processDate );
				$introdTrans->set( 'rtEffectiveDate', $processDate );
				$introdTrans->set( 'rtPaidDate', $processDate );
				$introdTrans->set( 'rtChequeNo', $postingRef );
				$introdTrans->set( 'rtIntrodRef', $introdRef );
				$introdTrans->set( 'rtPaymentType', $paymentType );
				$introdTrans->set( 'rtDebit', 0 );
				$introdTrans->set( 'rtOriginal', 0 - $totalToPay );
				$introdTrans->set( 'rtWrittenOff', $totalAdj );
				$introdTrans->set( 'rtPaid', 0 - $totalToPay );
				$introdTrans->set( 'rtBalance', 0 );
				$introdTrans->set( 'rtAccountingYear', $accountingYear );
				$introdTrans->set( 'rtAccountingPeriod', $accountingPeriod );
				$introdTrans->setCreatedByAndWhen(  );
				$introdTrans->insert( null );
				$st = new SystemTransaction( $tnCode );
				$st->set( 'tnTran', $introdTrans->getKeyValue(  ) );
				$st->set( 'tnType', 'NR' );
				$st->set( 'tnCreatedBy', $introdTrans->get( 'rtCreatedBy' ) );
				$st->set( 'tnCreatedOn', $introdTrans->get( 'rtCreatedOn' ) );
				$st->update(  );
				$aa = new AccountingAudit( null );
				$aa->set( 'aaType', 'R' );
				$aa->set( 'aaTran', $introdTrans->getKeyValue(  ) );
				$aa->set( 'aaSysTran', $tnCode );
				$aa->set( 'aaPostingDate', $introdTrans->get( 'rtPostingDate' ) );
				$aa->set( 'aaEffectiveDate', $introdTrans->get( 'rtEffectiveDate' ) );
				$aa->set( 'aaAccountingYear', $accountingYear );
				$aa->set( 'aaAccountingPeriod', $accountingPeriod );
				$aa->set( 'aaCreatedBy', $introdTrans->get( 'rtCreatedBy' ) );
				$aa->set( 'aaCreatedOn', $introdTrans->get( 'rtCreatedOn' ) );
				$aa->insert( null );
			} 
else {
				$introdTrans = new IntroducerTransaction( $this->transToReconcile );
				$paid = $introdTrans->get( 'rtPaid' );
				$adj = $introdTrans->get( 'rtWrittenOff' );
				$bal = $introdTrans->get( 'rtBalance' );
				$introdTrans->set( 'rtWrittenOff', $adj + $totalAdj );
				$introdTrans->set( 'rtPaid', $paid - $totalToPay );
				$introdTrans->set( 'rtBalance', $bal + $totalToPay );
				$introdTrans->set( 'rtChequeNo', $postingRef );
				$introdTrans->set( 'rtInsCoRef', $introdRef );
			}

			$introdTrans->set( 'rtPaidDate', $processDate );
			$introdTrans->set( 'rtPaidYear', $accountingYear );
			$introdTrans->set( 'rtPaidPeriod', $accountingPeriod );
			$introdTrans->update(  );

			if ($introdTrans->get( 'rtBalance' ) == 0) {
				$document = $this->_produceDocument( $introdTrans, $trans );
				$doCode = $document->getKeyValue(  );
				$introdTrans->set( 'rtDocm', $doCode );
			}

			$introdTrans->update(  );
			$rtCodeMain = $introdTrans->getKeyValue(  );
			$btCode = $introdTrans->get( 'rtCashBatch' );

			if (0 < $btCode) {
				$batch = new CashBatch( $btCode );
				$batch->recalcAllocated(  );
				$batch->update(  );
			}

			$biCode = $introdTrans->get( 'rtCashBatchItem' );

			if (0 < $biCode) {
				$usCode = 0;
				$user = $session->get( 'user' );

				if (is_a( $user, 'User' )) {
					$usCode = $user->getKeyValue(  );
				}

				$bi = new CashBatchItem( $biCode );
				$bi->set( 'biAllocatedBy', $usCode );
				$bi->set( 'biDateAllocated', ugettimenow(  ) );
				$bi->update(  );
			}

			foreach ($trans as $key => $value) {
				$rtCode = $key;

				if (isset( $this->toPay[$key] )) {
					$toPay = (double)$this->toPay[$key];
				} 
else {
					$toPay = 0;
				}


				if (isset( $this->adjustments[$key] )) {
					$adj = (double)$this->adjustments[$key];
				} 
else {
					$adj = 0;
				}


				if (( $toPay == 0 && $adj == 0 )) {
					continue;
				}


				if ($toPay != 0) {
					$ra = new IntroducerTransAllocation( null );
					$ra->set( 'raPostingDate', $processDate );
					$ra->set( 'raAccountingYear', $accountingYear );
					$ra->set( 'raAccountingPeriod', $accountingPeriod );
					$ra->set( 'raType', 'C' );
					$ra->set( 'raCashTran', $rtCodeMain );
					$ra->set( 'raOtherTran', $rtCode );
					$ra->set( 'raAmount', $toPay );
					$ra->set( 'raPaymentMethod', $paymentType );
					$ra->insert( null );
				}


				if ($adj != 0) {
					$ra = new IntroducerTransAllocation( null );
					$ra->set( 'raPostingDate', $processDate );
					$ra->set( 'raAccountingYear', $accountingYear );
					$ra->set( 'raAccountingPeriod', $accountingPeriod );
					$ra->set( 'raType', 'W' );
					$ra->set( 'raCashTran', $rtCodeMain );
					$ra->set( 'raOtherTran', $rtCode );
					$ra->set( 'raAmount', $adj );
					$ra->set( 'raPaymentMethod', $paymentType );
					$ra->insert( null );
					continue;
				}
			}

			$total = $totalToPay;

			if (( $total != 0 && $this->transToReconcile <= 0 )) {
				$bat = new BankTransType( KEY_BANK_CASH_TO_INSCO );
				$debit = $bat->get( 'byDebit' );

				if ($debit != 1) {
					$total = 0 - $total;
				}

				$ba = new BankAccountTran( null );
				$ba->set( 'baType', KEY_BANK_CASH_TO_INTROD );
				$ba->set( 'baTran', $introdTrans->getKeyValue(  ) );
				$ba->set( 'baSysTran', $tnCode );
				$ba->set( 'baDebit', $debit );
				$ba->set( 'baPostingRef', $postingRef );
				$ba->set( 'baPaymentType', $paymentType );
				$ba->set( 'baPostingDate', $processDate );
				$ba->set( 'baAccountingYear', $accountingYear );
				$ba->set( 'baAccountingPeriod', $accountingPeriod );
				$ba->set( 'baCreatedBy', $introdTrans->get( 'rtCreatedBy' ) );
				$ba->set( 'baCreatedOn', $introdTrans->get( 'rtCreatedOn' ) );
				$ba->set( 'baAmount', $total );
				$ba->insert( null );
			}

			udbcommittransaction(  );
			$this->clearAll(  );
			$this->setTransToView( $rtCodeMain );
		}

		function _producedocument(&$rt, $trans) {
			global $user;

			if (is_a( $user, 'User' )) {
				$usCode = $user->getKeyValue(  );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					if ($posted == true) {
						trigger_error( 'no user', E_USER_NOTICE );
						$usCode = null;
					} 
else {
						trigger_error( 'no user', E_USER_NOTICE );
						$usCode = null;
					}
				}
			}

			$doCode = $rt->get( 'rtDocm' );

			if (0 < $doCode) {
				$document = new Document( $doCode );
				$docmNo = $doCode;
			} 
else {
				$document = new Document( null );
				$document->insert( null );
				$docmNo = $document->getKeyValue(  );
			}

			$document->set( 'doWhenOriginated', ugettimenow(  ) );
			$document->set( 'doOriginator', $usCode );
			$rtCode = $rt->getKeyValue(  );
			$inCode = $rt->get( 'rtIntroducer' );
			$rtSysTran = $rt->get( 'rtSysTran' );
			$document->set( 'doTrans', $rtCode );
			$document->set( 'doSysTran', $rtSysTran );
			$document->set( 'doIntroducer', $inCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$subject = 'Remittance Advice';
			$document->set( 'doSubject', $subject );
			$doDocmType = REMITTANCE_ADVICE_DOCM_TYPE;
			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( $rtCode, 'remittance', $docmNo, $trans );
			$name = sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );

			if (0 < $inCode) {
				$document->setIntroducerSequence(  );
			}

			$document->setTransSequence(  );
			$document->update(  );
			return $document;
		}

		function _makepdf($rtCode, $type, &$docmNo, $trans) {
			require_once( UTIL_PATH . 'UXML.class.php' );
			require_once( UTIL_PATH . 'UXMLTag.class.php' );
			require_once( UTIL_PATH . 'UPDF.class.php' );
			require_once( UTIL_PATH . 'UPDFXML.class.php' );
			require_once( '../introducers/templateClasses/IntrodRemittanceAdviceTemplate.class.php' );
			$inCode = $this->get( 'rtIntroducer' );
			$in = new Introducer( $inCode );
			$pdf = new UPDF( 'l', false );
			$xmlText = file_get_contents( PDFS_PATH . 'introdRemittance.xml' );
			$template = new IntrodRemittanceAdviceTemplate( null );
			$template->setArraysOfTransactions( $trans, $this->toPay, $this->adjustments );
			$template->setParseForXML(  );
			$template->setTransaction( $rtCode );
			$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
			$inCode = $this->get( 'ptIntoducer' );

			if (0 < $inCode) {
				$ins = new Introducer( $inCode );
				$name = $ins->get( 'inName' );
				$address = $ins->getInvoiceNameAndAddress(  );
			} 
else {
				$address = '';
				$name = '';
			}

			$template->set( 'introdName', $name );
			$template->setHTMLFromText( $xmlText );
			$template->parseAll(  );
			$newXMLText = $template->getOutput(  );
			$xml = new UPDFXML( $newXMLText, $pdf );
			$pdf->close(  );
			$text = $pdf->returnAsString(  );
			return $text;
		}

		function setsortby($sortBy) {
			$this->sortBy = $sortBy;
		}

		function listtransactions($text) {
			$out = '';
			foreach ($this->readIn as $key => $value) {
				$rtCode = $key;
				$inList = $value;
				$rt = new IntroducerTransaction( $rtCode );
				$this->currentTrans = &$rt;

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
				$paidDate = $rt->getForHTML( 'rtPaidDate' );
				$itemBalance = $rt->get( 'rtBalance' );
				$this->set( 'itemBalance', $itemBalance );
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

				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$transType = $tranType->get( 'pyName' );
				$this->set( 'rtCode', $rt->getKeyValue(  ) );
				$this->set( 'client', $clName );
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
				$this->set( 'orig', $rt->getAsMoneyWithCommas( 'rtOriginal' ) );
				$this->set( 'balance', $rt->getAsMoneyWithCommas( 'rtBalance' ) );
				$this->set( 'paid', $rt->getAsMoneyWithCommas( 'rtPaid' ) );

				if (( $rt->get( 'rtRate' ) == 0 && $rt->get( 'rtOriginal' ) != 0 )) {
					$this->set( 'rtRate', 'flat' );
				}


				if ($inList == 1) {
					$colour = 'white';
				} 
else {
					$colour = '#fffcde';
				}

				$this->set( 'rowColour', $colour );
				$this->set( 'toPay', '' );
				$this->set( 'adj', '' );

				if (is_array( $this->toPay )) {
					if (isset( $this->toPay[$rtCode] )) {
						$this->set( 'toPay', uformatmoney( $this->toPay[$rtCode] ) );
					}
				}


				if (is_array( $this->adjustments )) {
					if (isset( $this->adjustments[$rtCode] )) {
						$this->set( 'adj', uformatmoney( $this->adjustments[$rtCode] ) );
					}
				}


				if (isset( $this->transToView )) {
					if (isset( $this->readInAmount[$rtCode] )) {
						$toPay = uformatmoney( $this->readInAmount[$rtCode] );
					} 
else {
						$toPay = '';
					}


					if (isset( $this->readInWrittenOff[$rtCode] )) {
						$adj = uformatmoney( $this->readInWrittenOff[$rtCode] );
					} 
else {
						$adj = '';
					}

					$this->set( 'toPay', uformatmoney( $toPay ) );
					$this->set( 'adj', uformatmoney( $adj ) );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpaymentmethods($text) {
			$code = $this->get( 'paymentType' );
			$q = 'SELECT * FROM cashPaymentMethods ORDER BY cpSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$cpCode = $row['cpCode'];
				$this->set( 'cpCode', $cpCode );
				$this->set( 'cpName', $row['cpName'] );

				if ($cpCode == $code) {
					$selected = 'selected';
				} 
else {
					$selected = '';
				}

				$this->set( 'showSelected', $selected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whencanshowtransaction($text) {
			global $accountingYear;
			global $accountingPeriod;
			$rt = &$this->currentTrans;

			if (!isset( $this->transToView )) {
				$rtPaidYear = $rt->get( 'rtPaidYear' );
				$rtPaidPeriod = $rt->get( 'rtPaidPeriod' );

				if (( $rtPaidYear != 0 && $rtPaidYear != $accountingYear )) {
					return '';
				}


				if (( $rtPaidPeriod != 0 && $rtPaidPeriod != $accountingPeriod )) {
					return '';
				}
			}

			$out = $this->parse( $text );
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

		function whenspecifictrans($text) {
			if (!isset( $this->transToReconcile )) {
				return '';
			}


			if ($this->transToReconcile <= 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenreconciling($text) {
			if (isset( $this->transToView )) {
				if (0 < $this->transToView) {
					return false;
				}
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenviewingonly($text) {
			if (!isset( $this->transToView )) {
				return false;
			}


			if ($this->transToView <= 0) {
				return false;
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenpayment($text) {
			if ($this->mode != 'P') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenreceipt($text) {
			if ($this->mode != 'R') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenviewpayment($text) {
			if ($this->mode != 'VP') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenviewreceipt($text) {
			if ($this->mode != 'VR') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenheadercanbeamended($text) {
			$canAmend = true;

			if (isset( $this->transToReconcile )) {
				if (0 < $this->transToReconcile) {
					$canAmend = false;
				}
			}


			if (isset( $this->transToView )) {
				if (0 < $this->transToView) {
					$canAmend = false;
				}
			}


			if ($canAmend == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenheadercantbeamended($text) {
			$canAmend = true;

			if (isset( $this->transToReconcile )) {
				if (0 < $this->transToReconcile) {
					$canAmend = false;
				}
			}


			if (isset( $this->transToView )) {
				if (0 < $this->transToView) {
					$canAmend = false;
				}
			}


			if ($canAmend == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>