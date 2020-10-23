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

	function _doreport($template, $input) {
		global $userCode;

		$controlFromDate = trim( $template->get( 'fromDate' ) );
		$controlToDate = trim( $template->get( 'toDate' ) );
		$fromDate = trim( $template->get( 'fromDate' ) );
		$fromDate = umakeourtimestamp( $fromDate, false );
		$toDate = trim( $template->get( 'toDate' ) );
		$toDate = umakeourtimestamp( $toDate, true );
		$processFromDate = trim( $template->get( 'processFromDate' ) );
		$processFromDate = umakeourtimestamp( $processFromDate, false );
		$processToDate = trim( $template->get( 'processToDate' ) );
		$processToDate = umakeourtimestamp( $processToDate, true );
		$effectiveFromDate = trim( $template->get( 'effectiveFromDate' ) );
		$effectiveFromDate = umakeourtimestamp( $effectiveFromDate, false );
		$effectiveToDate = trim( $template->get( 'effectiveToDate' ) );
		$effectiveToDate = umakeourtimestamp( $effectiveToDate, true );
		$template->setMessage( '' );
		$selectedPeriod = (int)$template->get( 'selectedPeriod' );
		$selectedYear = (int)$template->get( 'selectedYear' );
		$selectedType = $template->get( 'selectedType' );
		$usCode = $template->get( 'user' );
		$sortBy = $template->get( 'sortBy' );

		if ($sortBy == '') {
			$sortBy = 'P';
		}

		$periodDesc = '';
		$usePeriod = false;

		if (( ( $selectedPeriod == 0 && 0 < $selectedYear ) || ( 0 < $selectedPeriod && $selectedYear == 0 ) )) {
			$template->setMessage( 'You need to specify both a year and a period' );
			return false;
		}


		if (( 0 < $selectedPeriod && 0 < $selectedYear )) {
			$ay = new AccountingYear( $selectedYear );
			$yearDesc = $ay->get( 'ayName' );
			$accYear = $ay->get( 'ayYear' );
			$q = '' . 'SELECT apCode FROM accountingPeriods
				  WHERE apYear = ' . $selectedYear . '
				  AND apPeriod = ' . $selectedPeriod;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$rows = udbnumberofrows( $result );

			if ($rows != 1) {
				$template->setMessage( 'This period has not been set up in the system tables' );
				return false;
			}

			$row = udbgetrow( $result );
			$apCode = $row['apCode'];
			$ap = new AccountingPeriod( $apCode );
			$from = $ap->get( 'apFromDate' );
			$to = $ap->get( 'apToDate' );
			$accPeriod = $ap->get( 'apPeriod' );

			if (( $from == '' || $to == '' )) {
				$template->setMessage( 'This period has not been set up properly with dates' );
				return false;
			}


			if (( ( ( ( ( 0 < strlen( trim( $controlFromDate ) ) || 0 < strlen( trim( $controlToDate ) ) ) || 0 < strlen( trim( $processFromDate ) ) ) || 0 < strlen( trim( $processToDate ) ) ) || 0 < strlen( trim( $effectiveFromDate ) ) ) || 0 < strlen( trim( $effectiveToDate ) ) )) {
				$template->setMessage( 'You can\'t specify a period and a range of dates' );
				return false;
			}

			$controlFromDate = $from;
			$controlToDate = $to;
			$periodDesc = '' . 'For Period ' . $selectedPeriod . ' Year ' . $yearDesc;
			$usePeriod = true;
		} 
else {
			$dateSet = false;

			if (( $fromDate != '' && $toDate != '' )) {
				$dateSet = true;
			}


			if (( $processFromDate != '' && $processToDate != '' )) {
				$dateSet = true;
			}


			if (( $effectiveFromDate != '' && $effectiveToDate != '' )) {
				$dateSet = true;
			}


			if ($dateSet == false) {
				$template->setMessage( 'you must enter from and to dates, or an accounting period' );
				return false;
			}


			if ($toDate < $fromDate) {
				$template->setMessage( 'dates are in the wrong order' );
				return false;
			}


			if ($processToDate < $processFromDate) {
				$template->setMessage( 'process dates are in the wrong order' );
				return false;
			}


			if ($effectiveToDate < $effectiveFromDate) {
				$template->setMessage( 'effective dates are in the wrong order' );
				return false;
			}
		}

		$template->set( 'periodDesc', $periodDesc );
		$q = '' . 'DROP TABLE IF EXISTS tmpA' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpA' . $userCode . ' (
				tmCode			INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmAudit			INT,
				tmSysTran		INT,
				tmType			CHAR(2),
				tmTran			INT,
				tmPostingDate	DATE,
				tmEffectiveDate	DATE,
				tmCreatedBy		INT,
				tmCreatedOn		CHAR(14)
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpA' . $userCode . ' (tmType, tmAudit, tmTran, tmSysTran, tmPostingDate,  tmEffectiveDate, tmCreatedBy, tmCreatedOn) ';
		$q .= 'SELECT aaType, aaCode, aaTran, aaSysTran, aaPostingDate, aaEffectiveDate, aaCreatedBy, aaCreatedOn
				FROM accountingAudit  ';
		$whereDone = false;

		if ($usePeriod == true) {
			$q .= '' . 'WHERE aaAccountingYear = ' . $accYear . ' AND aaAccountingPeriod = ' . $accPeriod;
			$whereDone = true;
		} 
else {
			if ($fromDate != '') {
				$q .= '' . 'WHERE aaCreatedOn >= \'' . $fromDate . '\' AND aaCreatedOn <= \'' . $toDate . '\'';
				$whereDone = true;
			}


			if ($processFromDate != '') {
				if ($whereDone == false) {
					$q .= 'WHERE ';
				} 
else {
					$q .= 'AND ';
				}

				$q .= '' . ' aaPostingDate >= \'' . $processFromDate . '\' AND aaPostingDate <= \'' . $processToDate . '\'';
				$whereDone = true;
			}


			if ($effectiveFromDate != '') {
				if ($whereDone == false) {
					$q .= 'WHERE ';
				} 
else {
					$q .= 'AND ';
				}

				$q .= '' . ' aaEffectiveDate >= \'' . $effectiveFromDate . '\' AND aaEffectiveDate <= \'' . $effectiveToDate . '\'';
				$whereDone = true;
			}
		}


		if (0 < $usCode) {
			$q .= '' . ' AND aaCreatedBy = ' . $usCode;
		}


		if ($selectedType == 'P') {
			$q .= ' AND aaType = \'P\'';
		}


		if ($selectedType == 'I') {
			$q .= ' AND (aaType = \'I\' OR aaType = \'CI\' OR aaType = \'NI\') ';
		}


		if ($selectedType == 'C') {
			$q .= ' AND (aaType = \'C\' OR aaType = \'CC\' OR aaType = \'NC\') ';
		}


		if ($selectedType == 'R') {
			$q .= ' AND (aaType = \'R\' OR aaType = \'CN\' OR aaType = \'NN\') ';
		}


		if ($selectedType == 'J') {
			$q .= ' AND ( aaType = \'CI\' OR aaType = \'NI\' OR aaType = \'CC\' OR aaType = \'NC\'OR aaType = \'CN\' OR aaType = \'NN\' ) ';
		}

		$order = ' ORDER BY aaCreatedOn DESC,  aaCode DESC';

		if ($sortBy == 'A') {
			$order = ' ORDER BY aaCreatedOn DESC,  aaCode DESC';
		}


		if ($sortBy == 'P') {
			$order = ' ORDER BY aaPostingDate DESC,  aaCode DESC';
		}


		if ($sortBy == 'E') {
			$order = ' ORDER BY aaEffectiveDate DESC,  aaCode DESC';
		}

		$q .= $order;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT COUNT(tmCode) AS total FROM tmpA' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$found = $row['total'];
		$template->set( 'transFound', $found );

		if ($found == 0) {
			$template->setMessage( 'no transactions found' );
		}

		return false;
	}

	function _viewreport($template, $input) {
		$reportType = $template->get( 'reportType' );
		$reportSummary = $template->get( 'reportSummary' );
		$selectedType = $template->get( 'selectedType' );
		$sortBy = $template->get( 'sortBy' );
		$sortByMessg = '';

		if ($sortBy == 'A') {
			$sortByMessg = 'By Actual Dates';
		}


		if ($sortBy == 'P') {
			$sortByMessg = 'By Posting Dates';
		}


		if ($sortBy == 'E') {
			$sortByMessg = 'By Effective Dates';
		}

		$template->set( 'sortByMessg', $sortByMessg );
		$template->set( 'typesSelected', 'All Transaction Types' );
		$template->set( 'typesSelected', 'All Transaction Types' );

		if ($selectedType == 'P') {
			$template->set( 'typesSelected', 'Policy Transactions' );
		}


		if ($selectedType == 'I') {
			$template->set( 'typesSelected', 'Ins. Co. Transactions' );
		}


		if ($selectedType == 'C') {
			$template->set( 'typesSelected', 'Client Transactions' );
		}


		if ($selectedType == 'R') {
			$template->set( 'typesSelected', 'Introducer Transactions' );
		}

		$user = $template->get( 'user' );
		$userName = 'All Users';

		if (0 < $user) {
			$us = new User( $user );
			$userName = $us->getFullName(  );
		}

		$template->set( 'userName', $userName );
		$template->setHTML( 'auditOutput.html' );
		$template->setHeaderFields(  );
	}

	function _returntooptions($template, $input) {
		$template->setHTML( 'audit.html' );
		return false;
	}

	function _viewtran($template, $input) {
		global $session;

		$aaCode = $input['tranToView'];

		if ($aaCode <= 0) {
			return false;
		}

		$audit = new AccountingAudit( null );
		$found = $audit->tryGettingRecord( $aaCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this transaction has been deleted' );
			return false;
		}

		$ret = '../reports/audit.php';
		$session->set( 'returnTo', $ret );
		$aaType = $audit->get( 'aaType' );
		$aaTran = $audit->get( 'aaTran' );

		if ($aaType == 'B') {
			flocationheader( '' . '../batches/cashBatchEdit.php?batch=' . $aaTran );
			exit(  );
		}


		if ($aaType == 'P') {
			$pt = new PolicyTransaction( $aaTran );
			$plCode = $pt->get( 'ptPolicy' );
			flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $aaTran );
		}


		if ($aaType == 'C') {
			$ct = new ClientTransaction( $aaTran );
			$ctTransType = $ct->get( 'ctTransType' );

			if ($ctTransType == 'C') {
				flocationheader( '' . '../clients/cashReceiptsEdit.php?view=' . $aaTran );
				exit(  );
			}


			if ($ctTransType == 'I') {
				$plCode = $ct->get( 'ctPolicy' );
				$ptCode = $ct->get( 'ctPolicyTran' );
				flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
			}
		}


		if ($aaType == 'I') {
			$it = new InsCoTransaction( $aaTran );
			$itTransType = $it->get( 'itTransType' );

			if (( $itTransType == 'C' || $itTransType == 'R' )) {
				flocationheader( '' . '../inscos/inscoRecon.php?view=' . $aaTran );
			}


			if ($itTransType == 'I') {
				$plCode = $it->get( 'itPolicy' );
				$ptCode = $it->get( 'itPolicyTran' );
				flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
			}
		}


		if ($aaType == 'R') {
			$rt = new IntroducerTransaction( $aaTran );
			$rtTransType = $rt->get( 'rtTransType' );

			if (( $rtTransType == 'C' || $rtTransType == 'R' )) {
			}


			if ($rtTransType == 'I') {
				$plCode = $rt->get( 'rtPolicy' );
				$ptCode = $rt->get( 'rtPolicyTran' );
				flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
			}
		}


		if ($aaType == 'IO') {
			flocationheader( '' . '../accounts/ibaOther.php?toView=' . $aaTran );
		}


		if (( ( ( ( ( $aaType == 'CC' || $aaType == 'CI' ) || $aaType == 'CN' ) || $aaType == 'NC' ) || $aaType == 'NI' ) || $aaType == 'NN' )) {
			flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $aaTran );
		}

		return false;
	}

	require( '../include/startup.php' );
	$auditTemplate = &$session->get( 'auditTemplate' );

	if ($auditTemplate == null) {
		$auditTemplate = new AuditTemplate( 'audit.html' );
		$auditTemplate->setProcess( '_doReport', 'doReport' );
		$auditTemplate->setProcess( '_viewReport', 'viewList' );
		$auditTemplate->setProcess( '_returnToOptions', 'returnToOptions' );
		$auditTemplate->setProcess( '_viewTran', 'tranToView' );
		$auditTemplate->setReturnTo( '../reports/audit.php' );
	}

	$session->set( 'auditTemplate', $auditTemplate );
	$auditTemplate->process(  );
	$session->set( 'auditTemplate', $auditTemplate );
?>