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

	class inscorecontemplate {
		var $canAmend = null;
		var $toPay = null;
		var $adjustments = null;
		var $_tempTotalToPay = null;
		var $_tempTotalCommAdj = null;
		var $_tempTotalCommDue = null;
		var $_tempTotalCommPaid = null;
		var $_tempMustReconcile = null;
		var $transToView = null;
		var $transToReconcile = null;
		var $sortBy = null;
		var $ascending = null;
		var $mode = null;

		function inscorecontemplate($html) {
			documentstemplate::documentstemplate( $html );
			$this->addField( 'mode' );
			$this->addField( 'icCode' );
			$this->addField( 'directItems' );
			$this->addField( 'clearedItems' );
			$this->addField( 'clientPaid' );
			$this->addField( 'searchText' );
			$this->addField( 'processDate' );
			$this->addField( 'insCoRef' );
			$this->addField( 'postingRef' );
			$this->addField( 'paymentType' );
			$this->addField( 'totalToPay' );
			$this->addField( 'totalCommAdj' );
			$this->addField( 'totalCommDue' );
			$this->addField( 'totalCommPaid' );
			$this->setFieldType( 'directItems', 'checked' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'clientPaid', 'checked' );
			$this->setFieldType( 'totalToPay', 'MONEY' );
			$this->setFieldType( 'totalCommAdj', 'MONEY' );
			$this->setFieldType( 'totalCommDue', 'MONEY' );
			$this->setFieldType( 'totalCommPaid', 'MONEY' );
			$this->canAmend = false;
			$this->transToReconcile = 0;
			$this->sortBy = '';
			$this->ascending = false;
			$this->mode = '';
		}

		function setinsco($icCode) {
			$insco = new Insco( $icCode );
			$this->insco = &$insco;

			$this->clearAll(  );
			$this->setAll( $insco->getAllForHTML(  ) );
			$this->mode = 'P';
			$this->set( 'mode', $this->mode );
			$this->toPay = null;
			$this->adjustments = null;
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			return false;
		}

		function settranstoreconcile($itCode) {
			$this->transToReconcile = $itCode;
			unset( $this[transToView] );
			$it = new InsCoTransaction( $itCode );
			$it->fetchExtraColumns(  );
			$icCode = $it->get( 'itInsCo' );
			$this->setInsco( $icCode );
			$this->mode = 'R';
			$this->set( 'mode', $this->mode );
			$input = array(  );
			$this->_doBeforeAnyProcessing( $input );
			$itCashBatch = $it->get( 'itCashBatch' );
			$this->set( 'cashBatch', sprintf( '%07d', $itCashBatch ) );
			$this->set( 'code', sprintf( '%07d', $itCode ) );
			$this->set( 'processDate', $it->getForHTML( 'itPostingDate' ) );
			$this->set( 'insCoRef', $it->getForHTML( 'itInsCoRef' ) );
			$this->set( 'postingRef', $it->getForHTML( 'itChequeNo' ) );
			$this->set( 'paymentType', $it->getForHTML( 'itPaymentType' ) );
			$this->set( 'paymentTypeName', $it->getForHTML( 'paymentTypeName' ) );
			$this->_tempMustReconcile = uformatmoney( $it->get( 'itBalance' ) );
			$this->_tempTotalCommAdj = 0;
			$this->_tempTotalCommDue = 0;
			$this->_tempTotalCommPaid = 0;
			$this->set( 'viewTotalMustPay', $this->_tempMustReconcile );
			$this->set( 'viewTotalToPay', $this->_tempTotalToPay );
			$this->set( 'viewTotalCommAdj', $this->_tempTotalCommAdj );
			$this->set( 'viewTotalCommDue', $this->_tempTotalCommDue );
			$this->set( 'viewTotalCommPaid', $this->_tempTotalCommPaid );
			return false;
		}

		function settranstoview($itCode) {
			$this->transToView = $itCode;
			unset( $this[transToReconcile] );
			$it = new InsCoTransaction( $itCode );
			$it->fetchExtraColumns(  );
			$icCode = $it->get( 'itInsCo' );
			$this->setInsco( $icCode );
			$t = $it->get( 'itTransType' );

			if ($t == 'C') {
				$this->mode = 'VP';
			} 
else {
				$this->mode = 'VR';
			}

			$this->set( 'mode', $this->mode );
			$this->set( 'processDate', $it->getForHTML( 'itPostingDate' ) );
			$this->set( 'insCoRef', $it->getForHTML( 'itInsCoRef' ) );
			$this->set( 'postingRef', $it->getForHTML( 'itChequeNo' ) );
			$this->set( 'paymentType', $it->getForHTML( 'itPaymentType' ) );
			$this->set( 'paymentTypeName', $it->getForHTML( 'paymentTypeName' ) );

			if ($this->mode == 'VR') {
				$this->set( 'viewTotalToPay', uformatmoney( $it->get( 'itPaid' ) ) );
				$this->set( 'viewTotalCommAdj', uformatmoney( $it->get( 'itWrittenOff' ) ) );
				$this->set( 'viewTotalCommDue', uformatmoney( $it->get( 'itCommission' ) ) );
				$paid = $it->get( 'itCommission' ) + $it->get( 'itWrittenOff' );
				$this->set( 'viewTotalCommPaid', uformatmoney( $paid ) );
			}


			if ($this->mode == 'VP') {
				$this->set( 'viewTotalToPay', uformatmoney( 0 - $it->get( 'itPaid' ) ) );
				$this->set( 'viewTotalCommAdj', uformatmoney( $it->get( 'itWrittenOff' ) ) );
				$this->set( 'viewTotalCommDue', uformatmoney( $it->get( 'itCommission' ) ) );
				$paid = $it->get( 'itCommission' ) + $it->get( 'itWrittenOff' );
				$this->set( 'viewTotalCommPaid', uformatmoney( $paid ) );
			}

			$this->set( 'code', sprintf( '%07d', $itCode ) );
			$this->set( 'doCode', $it->get( 'itDocm' ) );
			$this->_makeArrayForExistingTrans(  );
			$this->_tempMustReconcile = $it->getForHTML( 'itBalance' );
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
					$itCode = $this->transToView;
					$this->setTransToView( $itCode );
				}

				return false;
			}

			$this->setAll( $input );

			if (!isset( $this->insco )) {
				return false;
			}

			$aged = $this->insco->getAgedCredit(  );
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
						$itCode = substr( $key, 6 );
						$this->toPay[$itCode] = $value;

						if (isset( $combined[$itCode] )) {
							$combined[$itCode] += $value;
						} 
else {
							$combined[$itCode] = $value;
						}
					}


					if (substr( $key, 0, 4 ) == 'adj_') {
						$itCode = substr( $key, 4 );
						$this->adjustments[$itCode] = $value;

						if (isset( $combined[$itCode] )) {
							$combined[$itCode] += $value;
							continue;
						}

						$combined[$itCode] = $value;
						continue;
					}
				}
			}

			$totalToPay = 0;
			$totalCommAdj = 0;
			$totalCommDue = 0;
			foreach ($combined as $key => $value) {
				$itCode = $key;

				if (isset( $this->toPay[$itCode] )) {
					$toPay = $this->toPay[$itCode];
				} 
else {
					$toPay = 0;
				}


				if (isset( $this->adjustments[$itCode] )) {
					$adj = $this->adjustments[$itCode];
				} 
else {
					$adj = 0;
				}


				if (( $toPay == 0 && $adj == 0 )) {
					continue;
				}

				$it = new InsCoTransaction( $itCode );
				$comm = $it->getTotalCommission(  );
				$bal = $it->get( 'itBalance' );

				if (( $bal != 0 && $bal - ( $toPay + $adj ) == 0 )) {
					$totalCommDue += $comm;
				}

				$totalToPay += $toPay;
				$totalCommAdj += $adj;
			}


			if ($this->mode == 'P') {
				$totalCommPaid = $totalCommDue + $totalCommAdj;
			}


			if ($this->mode == 'R') {
				$totalCommPaid = $totalCommDue + $totalCommAdj;
			}


			if (!isset( $this->insco )) {
				return false;
			}

			$insco = &$this->insco;

			$icCode = $insco->getKeyValue(  );
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


			if ($clientPaid == 0) {
				$q = '' . 'INSERT INTO tmpR' . $userCode . ' (tmInsTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
				$q .= '' . 'SELECT itCode, itPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
				FROM inscoTransactions, policyTransactions, clients, policies
				WHERE itInsco=' . $icCode . '
			    AND plCode = ptPolicy
				AND itPolicyTran = ptCode
				AND clCode = ptClient
				AND (itTransType != \'C\' AND itTransType != \'R\') ';

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
				$q = '' . 'INSERT INTO tmpR' . $userCode . ' (tmInsTran, tmTransDate, tmEffectiveDate, tmClientName, tmPolicyNo) ';
				$q .= '' . 'SELECT itCode itCode, itPostingDate, ptEffectiveFrom, clNameSort, ptPolicyNumber
					FROM inscoTransactions, policyTransactions, clientTransactions, clients, policies
					WHERE (itTransType != \'C\' AND itTransType != \'R\')
					AND itInsco=' . $icCode . '
			        AND plCode = ptPolicy
					AND itPolicyTran = ptCode
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
				$q .= '' . 'AND itPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . 'AND itPostingDate <= \'' . $toDate . '\' ';
			}


			if ($clearedItems == 1) {
				$q .= 'AND itBalance = 0 ';
			}


			if ($clearedItems != 1) {
				$q .= 'AND itBalance != 0 ';
			}


			if ($directItems == 1) {
				$q .= 'AND itDirect = 1 ';
			}

			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$sortBy = $this->sortBy;
			$order = ' ORDER BY itCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by itPostingDate ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'effectDate') {
				$order = '' . ' ORDER by ptEffectiveFrom ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'clientName') {
				$order = '' . ' ORDER by clNameSort ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'policyNo') {
				$order = '' . ' ORDER by ptPolicyNumber ' . $asc . ', itCode DESC';
			}

			$q .= $order;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'UPDATE tmpR' . $userCode . ', inscoTransactions, policies, policyTransactions
					SET tmClientName = plPolicyHolder
					WHERE tmInsTran = itCode
					AND ptCode = itPolicyTran
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
				$itCode = $row['tmInsTran'];
				$this->readIn[$itCode] = 1;
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
					$this->_tempTotalCommDue = uformatmoney( $totalCommDue );
					$this->_tempTotalCommPaid = uformatmoney( $totalCommPaid );
				}


				if ($this->mode == 'R') {
					$this->_tempTotalToPay = uformatmoney( 0 - $totalToPay );
					$this->_tempTotalCommAdj = uformatmoney( $totalCommAdj );
					$this->_tempTotalCommDue = uformatmoney( $totalCommDue );
					$this->_tempTotalCommPaid = uformatmoney( $totalCommPaid );
				}
			}

			return false;
		}

		function _makearrayforexistingtrans() {
			$itCode = $this->transToView;

			if ($itCode <= 0) {
				trigger_error( 'no trans to view' );
			}

			$this->readInAmount = array(  );
			$this->readInWrittenOff = array(  );
			$q = '' . 'SELECT iaOtherTran, iaAmount, iaType
		FROM  inscoTransAllocations ,inscoTransactions, policyTransactions, clients
		WHERE iaCashTran = ' . $itCode . '
		AND iaOtherTran = itCode
		AND itPolicyTran = ptCode
		AND clCode = ptClient';
			$sortBy = $this->sortBy;
			$asc = '';

			if ($this->ascending == false) {
				$asc = 'DESC';
			}

			$order = ' ORDER BY itCode DESC';

			if ($sortBy == 'transDate') {
				$order = '' . ' ORDER by itPostingDate ' . $asc . ', itCode DESC';
			}


			if ($sortBy == 'effectDate') {
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

			$this->readIn = array(  );

			while ($row = udbgetrow( $result )) {
				$itCode = $row['iaOtherTran'];
				$iaType = $row['iaType'];
				$iaAmount = $row['iaAmount'];
				$this->readIn[$itCode] = 1;

				if ($iaType == 'C') {
					if (isset( $this->readInAmount[$itCode] )) {
						$this->readInAmount[$itCode] += $iaAmount;
					} 
else {
						$this->readInAmount[$itCode] = $iaAmount;
					}
				}


				if ($iaType == 'W') {
					if (isset( $this->readInWrittenOff[$itCode] )) {
						$this->readInWrittenOff[$itCode] += $iaAmount;
						continue;
					}

					$this->readInWrittenOff[$itCode] = $iaAmount;
					continue;
				}
			}

		}

		function setspecificfields() {
			$this->set( 'viewTotalMustPay', $this->_tempMustReconcile );
			$this->set( 'viewTotalToPay', $this->_tempTotalToPay );
			$this->set( 'viewTotalCommAdj', $this->_tempTotalCommAdj );
			$this->set( 'viewTotalCommDue', $this->_tempTotalCommDue );
			$this->set( 'viewTotalCommPaid', $this->_tempTotalCommPaid );
		}

		function clearalldisplayfields() {
			if (!isset( ->transToReconcile )) {
				$this->set( 'processDate', '' );
				$this->set( 'insCoRef', '' );
				$this->set( 'postingRef', '' );
				$this->set( 'paymentType', '' );
				$this->set( 'totalToPay', '' );
				$this->set( 'viewTotalToPay', '' );
			}

			$this->set( 'totalCommAdj', '' );
			$this->set( 'totalCommDue', '' );
			$this->set( 'totalCommPaid', '' );
			$this->set( 'viewTotalCommAdj', '' );
			$this->set( 'viewTotalCommDue', '' );
			$this->set( 'viewTotalCommPaid', '' );
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

			$insCoRef = $this->get( 'insCoRef' );
			$postingRef = $this->get( 'postingRef' );
			$paymentType = $this->get( 'paymentType' );

			if (strlen( trim( $insCoRef ) ) == 0) {
				return 'You need to enter an insurance company ref';
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
else {
				if ($this->_tempTotalToPay < 0) {
					return 'You can\'t post a cash receipt from an insurance company through this option';
				}
			}

			return null;
		}

		function postentries() {
			global $session;
			global $accountingYear;
			global $accountingPeriod;

			$processDate = $this->get( 'processDate' );
			$insCoRef = $this->get( 'insCoRef' );
			$postingRef = $this->get( 'postingRef' );
			$paymentType = $this->get( 'paymentType' );
			$icCode = $this->insco->getKeyValue(  );
			$ok = udbcantabledotransactions( 'policyTransactions' );

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
			$totalCommDue = 0;
			foreach ($trans as $key => $value) {
				$itCode = $key;

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

				$it = new InsCoTransaction( $itCode );
				$itPaidYear = $it->get( 'itPaidYear' );
				$itPaidPeriod = $it->get( 'itPaidPeriod' );

				if (( 0 < $itPaidYear || 0 < $itPaidPeriod )) {
					trigger_error( '' . 'item already paid ' . $itCode, E_USER_ERROR );
				}

				$itPaid = $it->get( 'itPaid' );
				$itPaid += $toPay;
				$it->set( 'itPaid', $itPaid );
				$itWrittenOff = $it->get( 'itWrittenOff' );
				$itWrittenOff += $toPay;
				$it->set( 'itWrittenOff', $adj );
				$bal = $it->get( 'itBalance' );
				$comm = $it->getTotalCommission(  );
				$direct = $it->get( 'itDirect' );
				$comm = 0 - $comm;

				if (( $bal != 0 && $bal - ( $toPay + $adj ) == 0 )) {
					$commDue = $comm;
				} 
else {
					$commDue = 0;
				}

				$it->recalcBalance(  );
				$bal = $it->get( 'itBalance' );

				if ($bal == 0) {
					$it->set( 'itPaidDate', $processDate );
					$it->set( 'itPaidYear', $accountingYear );
					$it->set( 'itPaidPeriod', $accountingPeriod );
				} 
else {
					$it->set( 'itPaidDate', '' );
					$it->set( 'itPaidYear', 0 );
					$it->set( 'itPaidPeriod', 0 );
				}

				$it->update(  );
				$totalToPay += $toPay;
				$totalAdj += $adj;
				$totalCommDue += $commDue;
			}


			if ($this->transToReconcile <= 0) {
				$creatingNewEntry = true;
			} 
else {
				$creatingNewEntry = false;
			}


			if ($creatingNewEntry == true) {
				$tnCode = fcreatesystemtran(  );
				$insTrans = new InsCoTransaction( null );
				$insTrans->set( 'itInsCo', $icCode );
				$insTrans->set( 'itSysTran', $tnCode );
				$insTrans->set( 'itTransType', 'C' );
				$insTrans->set( 'itTransDesc', 'Ins Co Trans' );
				$insTrans->set( 'itPostingDate', $processDate );
				$insTrans->set( 'itEffectiveDate', $processDate );
				$insTrans->set( 'itPaidDate', $processDate );
				$insTrans->set( 'itChequeNo', $postingRef );
				$insTrans->set( 'itInsCoRef', $insCoRef );
				$insTrans->set( 'itPaymentType', $paymentType );
				$insTrans->set( 'itDebit', 0 );
				$insTrans->set( 'itOriginal', 0 - $totalToPay );
				$insTrans->set( 'itWrittenOff', $totalAdj );
				$insTrans->set( 'itPaid', 0 - $totalToPay );
				$insTrans->set( 'itCommission', 0 - $totalCommDue );
				$insTrans->set( 'itBalance', 0 );
				$insTrans->set( 'itAccountingYear', $accountingYear );
				$insTrans->set( 'itAccountingPeriod', $accountingPeriod );
				$insTrans->setCreatedByAndWhen(  );
				$insTrans->insert( null );
				$st = new SystemTransaction( $tnCode );
				$st->set( 'tnTran', $insTrans->getKeyValue(  ) );
				$st->set( 'tnType', 'IR' );
				$st->set( 'tnCreatedBy', $insTrans->get( 'itCreatedBy' ) );
				$st->set( 'tnCreatedOn', $insTrans->get( 'itCreatedOn' ) );
				$st->update(  );
				$aa = new AccountingAudit( null );
				$aa->set( 'aaType', 'I' );
				$aa->set( 'aaTran', $insTrans->getKeyValue(  ) );
				$aa->set( 'aaSysTran', $tnCode );
				$aa->set( 'aaPostingDate', $insTrans->get( 'itPostingDate' ) );
				$aa->set( 'aaEffectiveDate', $insTrans->get( 'itEffectiveDate' ) );
				$aa->set( 'aaAccountingYear', $accountingYear );
				$aa->set( 'aaAccountingPeriod', $accountingPeriod );
				$aa->set( 'aaCreatedBy', $insTrans->get( 'itCreatedBy' ) );
				$aa->set( 'aaCreatedOn', $insTrans->get( 'itCreatedOn' ) );
				$aa->insert( null );
			} 
else {
				$insTrans = new InsCoTransaction( $this->transToReconcile );
				$paid = $insTrans->get( 'itPaid' );
				$adj = $insTrans->get( 'itWrittenOff' );
				$bal = $insTrans->get( 'itBalance' );
				$comm = $insTrans->get( 'itCommission' );
				$insTrans->set( 'itWrittenOff', $adj + $totalAdj );
				$insTrans->set( 'itPaid', $paid - $totalToPay );
				$insTrans->set( 'itBalance', $bal + $totalToPay );
				$insTrans->set( 'itCommission', $comm - $totalCommDue );
				$insTrans->set( 'itChequeNo', $postingRef );
				$insTrans->set( 'itInsCoRef', $insCoRef );
			}

			$insTrans->set( 'itPaidDate', $processDate );
			$insTrans->set( 'itPaidYear', $accountingYear );
			$insTrans->set( 'itPaidPeriod', $accountingPeriod );

			if ($insTrans->get( 'itBalance' ) == 0) {
				$document = $this->_produceDocument( $insTrans, $trans );
				$doCode = $document->getKeyValue(  );
				$insTrans->set( 'itDocm', $doCode );
			}

			$insTrans->update(  );
			$itCodeMain = $insTrans->getKeyValue(  );
			$btCode = $insTrans->get( 'itCashBatch' );

			if (0 < $btCode) {
				$batch = new CashBatch( $btCode );
				$batch->recalcAllocated(  );
				$batch->update(  );
			}

			$biCode = $insTrans->get( 'itCashBatchItem' );

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
				$itCode = $key;

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
					$ia = new InsCoTransAllocation( null );
					$ia->set( 'iaPostingDate', $processDate );
					$ia->set( 'iaAccountingYear', $accountingYear );
					$ia->set( 'iaAccountingPeriod', $accountingPeriod );
					$ia->set( 'iaType', 'C' );
					$ia->set( 'iaCashTran', $itCodeMain );
					$ia->set( 'iaOtherTran', $itCode );
					$ia->set( 'iaAmount', $toPay );
					$ia->set( 'iaPaymentMethod', 0 );
					$ia->insert( null );
				}


				if ($adj != 0) {
					$ia = new InsCoTransAllocation( null );
					$ia->set( 'iaPostingDate', $processDate );
					$ia->set( 'iaAccountingYear', $accountingYear );
					$ia->set( 'iaAccountingPeriod', $accountingPeriod );
					$ia->set( 'iaType', 'W' );
					$ia->set( 'iaCashTran', $itCodeMain );
					$ia->set( 'iaOtherTran', $itCode );
					$ia->set( 'iaAmount', $adj );
					$ia->set( 'iaPaymentMethod', 0 );
					$ia->insert( null );
					continue;
				}
			}


			if ($creatingNewEntry == true) {
				$total = $totalToPay;

				if ($total != 0) {
					$bat = new BankTransType( KEY_BANK_CASH_TO_INSCO );
					$debit = $bat->get( 'byDebit' );

					if ($debit != 1) {
						$total = 0 - $total;
					}

					$ba = new BankAccountTran( null );
					$ba->set( 'baType', KEY_BANK_CASH_TO_INSCO );
					$ba->set( 'baTran', $insTrans->getKeyValue(  ) );
					$ba->set( 'baSysTran', $tnCode );
					$ba->set( 'baDebit', $debit );
					$ba->set( 'baPostingRef', $postingRef );
					$ba->set( 'baPaymentType', $paymentType );
					$ba->set( 'baPostingDate', $processDate );
					$ba->set( 'baAccountingYear', $accountingYear );
					$ba->set( 'baAccountingPeriod', $accountingPeriod );
					$ba->set( 'baCreatedBy', $insTrans->get( 'itCreatedBy' ) );
					$ba->set( 'baCreatedOn', $insTrans->get( 'itCreatedOn' ) );
					$ba->set( 'baAmount', $total );
					$ba->insert( null );
				}
			}

			udbcommittransaction(  );
			$this->clearAll(  );
			$this->setTransToView( $itCodeMain );
		}

		function _producedocument(&$it, $trans) {
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

			$doCode = $it->get( 'itDocm' );

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
			$itCode = $it->getKeyValue(  );
			$icCode = $it->get( 'itInsCo' );
			$itSysTran = $it->get( 'itSysTran' );
			$document->set( 'doTrans', $itCode );
			$document->set( 'doSysTran', $itSysTran );
			$document->set( 'doInsco', $icCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$subject = 'Remittance Advice';
			$document->set( 'doSubject', $subject );
			$doDocmType = REMITTANCE_ADVICE_DOCM_TYPE;
			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( $it, 'remittance', $docmNo, $trans );
			$name = sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );

			if (0 < $icCode) {
				$document->setInscoSequence(  );
			}

			$document->setTransSequence(  );
			$document->update(  );
			return $document;
		}

		function _makepdf($it, $type, &$docmNo, $trans) {
			require_once( UTIL_PATH . 'UXML.class.php' );
			require_once( UTIL_PATH . 'UXMLTag.class.php' );
			require_once( UTIL_PATH . 'UPDF.class.php' );
			require_once( UTIL_PATH . 'UPDFXML.class.php' );
			require_once( '../inscos/templateClasses/RemittanceAdviceTemplate.class.php' );
			$icCode = $this->get( 'itInsCo' );
			$ic = new Insco( $icCode );
			$pdf = new UPDF( 'l', false );
			$xmlText = file_get_contents( PDFS_PATH . 'remittance.xml' );
			$template = new RemittanceAdviceTemplate( null );
			$template->setArraysOfTransactions( $trans, $this->toPay, $this->adjustments );
			$template->setParseForXML(  );
			$template->setTransaction( $it );
			$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
			$icCode = $this->get( 'ptInsCo' );

			if (0 < $icCode) {
				$ins = new Insco( $icCode );
				$name = $ins->get( 'icName' );
				$address = $ins->getInvoiceNameAndAddress(  );
			} 
else {
				$address = '';
				$name = '';
			}

			$template->set( 'insCoName', $name );
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

			if (!isset( $this->readIn )) {
				return '';
			}

			foreach ($this->readIn as $key => $value) {
				$itCode = $key;
				$inList = $value;
				$it = new InscoTransaction( $itCode );
				$this->currentTrans = &$it;

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
				$totalCommDue = $it->getTotalCommission(  );
				$this->set( 'totalComm', $totalCommDue );
				$itemBalance = $it->get( 'itBalance' );
				$this->set( 'itemBalance', $itemBalance );
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
				$pt = new PolicyTransaction( $ptCode );
				$polNo = $pt->get( 'ptPolicyNumber' );
				$effectiveDate = $pt->getForHTML( 'ptEffectiveFrom' );
				$this->set( 'ptCode', $ptCode );
				$covDesc = $pt->get( 'ptAddlCoverDesc' );
				$this->set( 'addlCoverDesc', $covDesc );
				$feeDesc = $pt->get( 'ptEngineeringFeeDesc' );
				$this->set( 'icFeeDesc', $feeDesc );
				$plCode = $pt->get( 'ptPolicy' );
				$policy = new Policy( $plCode );
				$policyHolder = $policy->get( 'plPolicyHolder' );
				$this->set( 'policyHolder', $policyHolder );
				$client = '';
				$clCode = $pt->get( 'ptClient' );

				if (0 < $clCode) {
					$cl = new Client( $clCode );
					$client = $cl->getDisplayName(  );
				}

				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$transType = $tranType->get( 'pyName' );
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
					if (isset( $this->toPay[$itCode] )) {
						$this->set( 'toPay', uformatmoney( $this->toPay[$itCode] ) );
					}
				}


				if (is_array( $this->adjustments )) {
					if (isset( $this->adjustments[$itCode] )) {
						$this->set( 'adj', uformatmoney( $this->adjustments[$itCode] ) );
					}
				}


				if (isset( $this->transToView )) {
					if (isset( $this->readInAmount[$itCode] )) {
						$toPay = uformatmoney( $this->readInAmount[$itCode] );
					} 
else {
						$toPay = '';
					}


					if (isset( $this->readInWrittenOff[$itCode] )) {
						$adj = uformatmoney( $this->readInWrittenOff[$itCode] );
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

			if (!isset( $this->transToView )) {
				$it = &$this->currentTrans;

				$itPaidYear = $it->get( 'itPaidYear' );
				$itPaidPeriod = $it->get( 'itPaidPeriod' );

				if (( $itPaidYear != 0 && $itPaidYear != $accountingYear )) {
					return '';
				}


				if (( $itPaidPeriod != 0 && $itPaidPeriod != $accountingPeriod )) {
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