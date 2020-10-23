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
		$insCo = $template->get( 'insCo' );
		$fromDate = trim( $template->get( 'fromDate' ) );
		$fromDate = umakesqldate2( $fromDate );
		$toDate = trim( $template->get( 'toDate' ) );
		$toDate = umakesqldate2( $toDate );
		$clearedItems = $template->get( 'clearedItems' );
		$directItems = $template->get( 'directItems' );
		$clientPaidItems = $template->get( 'clientPaid' );
		$q = '' . 'DROP TABLE IF EXISTS tmpIT' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpIT' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmInsTran			INT,
				tmTransDate			DATE,
				tmEffectiveDate		DATE,
				tmClientName		VARCHAR(200),
				tmInsCoName			VARCHAR(200)
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$someDone = false;
		$q = '' . 'INSERT INTO tmpIT' . $userCode . ' (tmInsTran, tmTransDate, tmEffectiveDate, tmClientName, tmInsCoName) ';
		$q .= 'SELECT itCode, itPostingDate, ptEffectiveFrom, clNameSort, icName  FROM inscoTransactions, insuranceCompanies, policyTransactions, clients ';

		if ($clientPaidItems == 1) {
			$q .= ', clientTransactions ';
		}

		$q .= ' WHERE icCode = itInsCo AND itPolicyTran=ptCode AND ptClient=clCode AND itPolicyTran = ptCode  ';

		if ($clientPaidItems == 1) {
			$q .= ' AND ptClientTran = ctCode AND ctBalance = 0 ';
		}


		if (0 < $insCo) {
			$q .= '' . 'AND itInsCo=' . $insCo . ' ';
		}


		if ($fromDate != '') {
			$q .= '' . 'AND itPostingDate >= \'' . $fromDate . '\' ';
		}


		if ($toDate != '') {
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

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		if ($clientPaidItems != 1) {
			$q = '' . 'INSERT INTO tmpIT' . $userCode . ' (tmInsTran, tmTransDate, tmInsCoName) ';
			$q .= 'SELECT itCode, itPostingDate, icName  FROM inscoTransactions, insuranceCompanies';
			$q .= ' WHERE icCode = itInsCo  ';
			$q .= ' AND (itTransType = \'R\' || itTransType = \'C\')  ';

			if (0 < $insCo) {
				$q .= '' . 'AND itInsCo=' . $insCo . ' ';
			}


			if ($fromDate != '') {
				$q .= '' . 'AND itPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != '') {
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

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}
		}

		$q = '' . 'UPDATE tmpIT' . $userCode . ', inscoTransactions, policies, policyTransactions
				SET tmClientName = plPolicyHolder
				WHERE tmInsTran = itCode AND ptCode = itPolicyTran AND plCode = ptPolicy
				 AND plPolicyHolder IS NOT NULL AND plPolicyHolder != \'\'';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT * FROM tmpIT' . $userCode . ' ORDER BY';

		if ($insCo <= 0) {
			$q .= ' tmInsCoName,  ';
		}


		if (( $reportOrder == null || strlen( $reportOrder ) != 1 )) {
			$reportOrder = 'T';
		}


		if ($reportOrder == 'T') {
			$q .= ' tmTransDate , tmInsTran  ';
		}


		if ($reportOrder == 'E') {
			$q .= ' tmEffectiveDate , tmInsTran  ';
		}


		if ($reportOrder == 'C') {
			$q .= ' tmClientName, tmTransDate, tmInsTran  ';
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$transactions = array(  );

		while ($row = udbgetrow( $result )) {
			$transactions[] = $row['tmInsTran'];
		}

		$template->transactions = $transactions;
		$found = count( $transactions );
		$template->set( 'transFound', $found );

		if ($found == 0) {
			$template->transactions = null;
			$template->setMessage( 'no transactions found' );
		}

		$q = '' . 'DROP TABLE IF EXISTS tmpIT' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		return false;
	}

	require( '../include/startup.php' );
	$insCoTransTemplate = &$session->get( 'insCoTransTemplate' );

	if ($insCoTransTemplate == null) {
		$insCoTransTemplate = new InsCoTransTemplate( 'insCoTrans.html' );
		$insCoTransTemplate->setProcess( '_doSearch', 'doSearch' );
		$insCoTransTemplate->setReturnTo( '../reports/insCoTrans.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$insCoTransTemplate->setHTML( 'insCoTransOutput.html' );
		$insCoTransTemplate->setHeaderFields(  );
	} 
else {
		$insCoTransTemplate->setHTML( 'insCoTrans.html' );
	}

	$session->set( 'insCoTransTemplate', $insCoTransTemplate );
	$insCoTransTemplate->process(  );
	$session->set( 'insCoTransTemplate', $insCoTransTemplate );
?>