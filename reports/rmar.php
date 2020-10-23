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
		$fromDate = trim( $template->get( 'fromDate' ) );
		$fromDate = umakesqldate2( $fromDate );
		$toDate = trim( $template->get( 'toDate' ) );
		$toDate = umakesqldate2( $toDate );
		$reportSummary = $template->get( 'reportSummary' );
		$reportType = $template->get( 'reportType' );
		$newBusiness = $template->get( 'newBusiness' );
		$template->setMessage( '' );

		if (( $fromDate == '' || $toDate == '' )) {
			$template->setMessage( 'you must enter from and to dates' );
			return false;
		}


		if ($toDate < $fromDate) {
			$template->setMessage( 'dates are in the wrong order' );
			return false;
		}


		if ($reportType == '') {
			$template->setMessage( 'you need to select a report type' );
			return false;
		}


		if ($reportSummary == '') {
			$template->setMessage( 'you need to decide if you want a summary or detail report' );
			return false;
		}


		if ($newBusiness == '') {
			$template->setMessage( 'you need to decide if new business, existing or both' );
			return false;
		}


		if ($reportType == 'H') {
			_preparereporth( $template, $fromDate, $toDate, $reportSummary, $newBusiness );
		}


		if ($reportType == 'I') {
			_preparereporti( $template, $fromDate, $toDate, $reportSummary, $newBusiness );
		}

		return false;
	}

	function _preparereporth($template, $fromDate, $toDate, $reportSummary, $newBusiness) {
		global $userCode;

		$q = '' . 'DROP TABLE IF EXISTS tmpRMAR' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpRMAR' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmPolicy			INT,
				tmSourceOfBus		INT
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmSourceOfBus) ';
		$q .= 'SELECT DISTINCT plCode, plSourceOfBus FROM policies, policyTransactions, clientTransactions ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND ptCode=ctPolicyTran ';
		$q .= 'AND ptPolicy = plCode ';
		$q .= '' . 'AND (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\' AND ctBalance = 0) ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = ptPolicy)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmSourceOfBus) ';
		$q .= 'SELECT DISTINCT plCode, plSourceOfBus FROM policies, policyTransactions ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plCode=ptPolicy ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . 'AND (plStatus = 1 AND ptPostingDate >= \'' . $fromDate . '\' AND ptPostingDate <= \'' . $toDate . '\')';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = ptPolicy)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT COUNT(tmCode) AS total FROM tmpRMAR' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$found = $row['total'];
		$template->set( 'policiesFound', $found );

		if ($found == 0) {
			$template->setMessage( 'no policies found' );
		}

		return false;
	}

	function _preparereporti($template, $fromDate, $toDate, $reportSummary, $newBusiness) {
		global $userCode;

		$q = '' . 'DROP TABLE IF EXISTS tmpRMAR' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpRMAR' . $userCode . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmPolicy			INT,
				tmType				CHAR(1),
				tmClassOfBus		INT
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT plCode, plClassOfBus, \'M\'  FROM policies, classOfBus ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plClassOfBus = cbCode ';
		$q .= 'AND cbRMAR = 1 ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . 'AND (plStatus = 2 AND plEnquiryDate >= \'' . $fromDate . '\' AND plEnquiryDate <= \'' . $toDate . '\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT plCode, icAddonCOB, \'A\'  FROM policies, classOfBus, insuranceCompanies ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plAltInsCo = icCode ';
		$q .= 'AND icAddonCOB = cbCode ';
		$q .= 'AND cbRMAR = 1 ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . 'AND (plStatus = 2 AND plEnquiryDate >= \'' . $fromDate . '\' AND plEnquiryDate <= \'' . $toDate . '\')';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = plCode AND tmType = \'A\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT DISTINCT plCode, plClassOfBus, \'M\' FROM policies, classOfBus, policyTransactions, clientTransactions ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plCode=ptPolicy ';
		$q .= 'AND ptCode=ctPolicyTran ';
		$q .= 'AND ptDirect = 1 ';
		$q .= 'AND plClassOfBus = cbCode ';
		$q .= 'AND cbRMAR = 1 ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . 'AND ptDirect = 1 AND ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\' ';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = ptPolicy  AND tmType = \'M\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT DISTINCT plCode, plClassOfBus, \'M\' FROM policies, classOfBus, policyTransactions, clientTransactions ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plCode=ptPolicy ';
		$q .= 'AND ptCode=ctPolicyTran ';
		$q .= 'AND ptDirect != 1 ';
		$q .= 'AND plClassOfBus = cbCode ';
		$q .= 'AND cbRMAR = 1 ';
		$q .= 'AND (ptGrossIncIPT + ptAddlGrossIncIPT != 0) ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= 'AND ctBalance = 0 ';
		$q .= '' . 'AND (ptDirect != 1 AND ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\') ';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = ptPolicy  AND tmType = \'M\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT DISTINCT plCode, icAddonCOB, \'A\' FROM policies, classOfBus, policyTransactions, clientTransactions,  insuranceCompanies ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plCode=ptPolicy ';
		$q .= 'AND ptDirect != 1 ';
		$q .= 'AND ptCode=ctPolicyTran ';
		$q .= 'AND plAltInsCo = icCode ';
		$q .= 'AND icAddonCOB = cbCode ';
		$q .= 'AND cbRMAR = 1 ';
		$q .= 'AND (ptAddOnGrossIncIPT != 0) ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= '' . 'AND (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\' AND ctBalance = 0)';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = plCode AND tmType = \'A\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpRMAR' . $userCode . ' (tmPolicy, tmClassOfBus, tmType) ';
		$q .= 'SELECT DISTINCT plCode, icAddonCOB, \'A\' FROM policies, classOfBus, policyTransactions, clientTransactions,  insuranceCompanies ';
		$q .= 'WHERE plPolicyType = \'R\' ';
		$q .= 'AND plCode=ptPolicy ';
		$q .= 'AND ptDirect = 1 ';
		$q .= 'AND ptCode=ctPolicyTran ';
		$q .= 'AND plAltInsCo = icCode ';
		$q .= 'AND icAddonCOB = cbCode ';
		$q .= 'AND cbRMAR = 1 ';
		$q .= 'AND (ptAddOnGrossIncIPT != 0) ';

		if ($newBusiness == 'N') {
			$q .= 'AND plNewBusiness =1 ';
		}


		if ($newBusiness == 'E') {
			$q .= 'AND plNewBusiness != 1 ';
		}

		$q .= 'AND ctBalance = 0 ';
		$q .= '' . 'AND (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\')';
		$q .= '' . ' AND NOT EXISTS (SELECT tmPolicy FROM tmpRMAR' . $userCode . ' WHERE tmPolicy = plCode AND tmType = \'A\')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT COUNT(tmCode) AS total FROM tmpRMAR' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$found = $row['total'];
		$template->set( 'policiesFound', $found );

		if ($found == 0) {
			$template->setMessage( 'no policies found' );
		}

		return false;
	}

	function _viewreport($template, $input) {
		$reportType = $template->get( 'reportType' );
		$reportSummary = $template->get( 'reportSummary' );

		if ($reportType == 'H') {
			if ($reportSummary == 'S') {
				$template->setHTML( 'rmarOutputHSummary.html' );
			}


			if ($reportSummary == 'D') {
				$template->setHTML( 'rmarOutputHDetail.html' );
			}
		}


		if ($reportType == 'I') {
			if ($reportSummary == 'S') {
				$template->setHTML( 'rmarOutputISummary.html' );
			}


			if ($reportSummary == 'D') {
				$template->setHTML( 'rmarOutputIDetail.html' );
			}
		}

		$template->setHeaderFields(  );
	}

	function _returntooptions($template, $input) {
		$template->setHTML( 'rmar.html' );
		return false;
	}

	function _viewpolicy($template, $input) {
		global $session;

		$plCode = $input['policyToView'];

		if ($plCode <= 0) {
			return false;
		}

		$policy = new Policy( null );
		$found = $policy->tryGettingRecord( $plCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this policy has been deleted' );
			return false;
		}

		$ret = '../reports/rmar.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyEdit.php?amendPolicy=' . $plCode );
		return false;
	}

	require( '../include/startup.php' );
	$rmarTemplate = &$session->get( 'rmarTemplate' );

	if ($rmarTemplate == null) {
		$rmarTemplate = new RmarTemplate( 'rmar.html' );
		$rmarTemplate->setProcess( '_doReport', 'doReport' );
		$rmarTemplate->setProcess( '_viewReport', 'viewList' );
		$rmarTemplate->setProcess( '_returnToOptions', 'returnToOptions' );
		$rmarTemplate->setProcess( '_viewPolicy', 'policyToView' );
		$rmarTemplate->setReturnTo( '../reports/rmar.php' );
	}

	$session->set( 'rmarTemplate', $rmarTemplate );
	$rmarTemplate->process(  );
	$session->set( 'rmarTemplate', $rmarTemplate );
?>