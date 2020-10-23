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

	function _dosearch($template, $input) {
		global $userCode;

		$reportOrder = $template->getReportOrder(  );
		$introducer = $template->get( 'introducer' );
		$fromDate = trim( $template->get( 'fromDate' ) );
		$fromDate = umakesqldate2( $fromDate );
		$toDate = trim( $template->get( 'toDate' ) );
		$toDate = umakesqldate2( $toDate );
		$clearedItems = $template->get( 'clearedItems' );
		$directItems = $template->get( 'directItems' );
		$clientPaidItems = $template->get( 'clientPaid' );
		$q = '' . 'DROP TABLE IF EXISTS tmpINT' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpINT' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmIntrodTran		INT,
				tmTransDate			DATE,
				tmEffectiveDate		DATE,
				tmClientName		VARCHAR(200),
				tmIntroducersName	VARCHAR(200)
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$someDone = false;
		$q = '' . 'INSERT INTO tmpINT' . $userCode . ' (tmIntrodTran, tmTransDate, tmEffectiveDate, tmClientName, tmIntroducersName) ';
		$q .= 'SELECT rtCode, rtPostingDate, ptEffectiveFrom, clNameSort, inName  FROM introducerTransactions, introducers, policyTransactions, clients ';

		if ($clientPaidItems == 1) {
			$q .= ', clientTransactions ';
		}

		$q .= ' WHERE inCode = rtIntroducer AND rtPolicyTran=ptCode AND ptClient=clCode ';
		$q .= ' AND rtTransType = \'I\' ';

		if ($clientPaidItems == 1) {
			$q .= ' AND ptClientTran = ctCode AND ctBalance = 0 ';
		}


		if (0 < $introducer) {
			$q .= '' . 'AND rtIntroducer=' . $introducer . ' ';
		}


		if ($fromDate != '') {
			$q .= '' . 'AND rtPostingDate >= \'' . $fromDate . '\' ';
		}


		if ($toDate != '') {
			$q .= '' . 'AND rtPostingDate <= \'' . $toDate . '\' ';
		}


		if ($clearedItems == 1) {
			$q .= 'AND rtBalance = 0 ';
		} 
else {
			$q .= 'AND rtBalance != 0 ';
		}


		if ($directItems == 1) {
			$q .= 'AND rtDirect = 1 ';
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		if (( $clientPaidItems != 1 && $directItems != 1 )) {
			$q = '' . 'INSERT INTO tmpINT' . $userCode . ' (tmIntrodTran, tmTransDate,  tmIntroducersName) ';
			$q .= 'SELECT rtCode, rtPostingDate,  inName  FROM introducerTransactions, introducers ';
			$q .= 'WHERE inCode = rtIntroducer ';
			$q .= ' AND rtTransType != \'I\' ';

			if (0 < $introducer) {
				$q .= '' . 'AND rtIntroducer=' . $introducer . ' ';
			}


			if ($fromDate != '') {
				$q .= '' . 'AND rtPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != '') {
				$q .= '' . 'AND rtPostingDate <= \'' . $toDate . '\' ';
			}


			if ($clearedItems == 1) {
				$q .= 'AND rtBalance = 0 ';
			} 
else {
				$q .= 'AND rtBalance != 0 ';
			}
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'UPDATE tmpINT' . $userCode . ', introducerTransactions, policies, policyTransactions
				SET tmClientName = plPolicyHolder
				WHERE tmIntrodTran = rtCode AND ptCode = rtPolicyTran AND plCode = ptPolicy
				 AND plPolicyHolder IS NOT NULL AND plPolicyHolder != \'\'';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT * FROM tmpINT' . $userCode . ' ORDER BY';

		if ($introducer <= 0) {
			$q .= ' tmIntroducersName,  ';
		}


		if (( $reportOrder == null || strlen( $reportOrder ) != 1 )) {
			$reportOrder = 'T';
		}


		if ($reportOrder == 'T') {
			$q .= ' tmTransDate DESC, tmIntrodTran DESC ';
		}


		if ($reportOrder == 'E') {
			$q .= ' tmEffectiveDate DESC, tmIntrodTran DESC ';
		}


		if ($reportOrder == 'C') {
			$q .= ' tmClientName, tmTransDate DESC, tmIntrodTran DESC ';
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$transactions = array(  );

		while ($row = udbgetrow( $result )) {
			$transactions[] = $row['tmIntrodTran'];
		}

		$template->transactions = $transactions;
		$found = count( $transactions );
		$template->set( 'transFound', $found );

		if ($found == 0) {
			$template->transactions = null;
			$template->setMessage( 'no transactions found' );
		}

		$q = '' . 'DROP TABLE IF EXISTS tmpINT' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		return false;
	}

	require( '../include/startup.php' );
	$introducersTransTemplate = &$session->get( 'introducersTransTemplate' );

	if ($introducersTransTemplate == null) {
		$introducersTransTemplate = new IntroducersTransTemplate( 'introducersTrans.html' );
		$introducersTransTemplate->setProcess( '_doSearch', 'doSearch' );
		$introducersTransTemplate->setReturnTo( '../reports/introducersTrans.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$introducersTransTemplate->setHTML( 'introducersTransOutput.html' );
		$introducersTransTemplate->setHeaderFields(  );
	} 
else {
		$introducersTransTemplate->setHTML( 'introducersTrans.html' );
	}

	$session->set( 'introducersTransTemplate', $introducersTransTemplate );
	$introducersTransTemplate->process(  );
	$session->set( 'introducersTransTemplate', $introducersTransTemplate );
?>