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
		global $session;

		$sortType = $template->getSortType(  );
		$alphas = udbmakefieldsafe( $template->get( 'alphas' ) );
		$balanceType = udbmakefieldsafe( $template->get( 'balanceType' ) );
		$searchText = udbmakefieldsafe( trim( $template->get( 'searchText' ) ) );
		$introdCode = udbmakefieldsafe( trim( $template->get( 'introdCode' ) ) );

		if (( 0 < strlen( $introdCode ) && is_numeric( $introdCode ) == false )) {
			$template->setMessage( 'introducer code needs to be numeric' );
			return false;
		}

		$q = 'SELECT DISTINCT rtIntroducer, sum(rtBalance), inCode,  inName
				FROM introducers, introducerTransactions
				WHERE inCode=rtIntroducer ';
		$whereDone = true;

		if ($balanceType == 'Z') {
			$q = 'select DISTINCT inCode, inName, inAddress, inContact, inPostcode  from introducers 
					left  join introducerTransactions
					on rtIntroducer=inCode
					GROUP BY inCode
					HAVING (sum(rtBalance) IS NULL OR  sum(rtBalance) = 0) ';
			$whereDone = true;
		}


		if (0 < strlen( $searchText )) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . '(inName 		 LIKE \'%' . $searchText . '%\' OR
				   inAddress     LIKE \'%' . $searchText . '%\' OR
				   inContact     LIKE \'%' . $searchText . '%\' OR
				   inPostcode    LIKE \'%' . $searchText . '%\')
			   ';
		}


		if (strlen( $alphas ) == 1) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . 'inName    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $introdCode )) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . 'inCode=' . $introdCode;
		}


		if ($balanceType == 'C') {
			$q .= ' GROUP BY rtIntroducer
				HAVING sum(rtBalance)  < 0';
		}


		if ($balanceType == 'D') {
			$q .= ' GROUP BY rtIntroducer
				HAVING sum(rtBalance)  > 0';
		}


		if ($balanceType == 'N') {
			$q .= ' GROUP BY rtIntroducer
				HAVING sum(rtBalance)  != 0';
		}


		if ($balanceType == 'U') {
			$q .= ' AND (rtTransType = \'C\'  OR rtTransType = \'R\')
						AND rtBalance  != 0
						 GROUP BY rtIntroducer';
		}

		$q .= ' ORDER BY inName';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$introducers = array(  );

		while ($row = udbgetrow( $result )) {
			$introducers[] = $row['inCode'];
		}

		$template->introd = $introducers;
		$found = count( $introducers );
		$template->set( 'introducersFound', $found );

		if ($found == 0) {
			$template->introducers = null;
			$template->setMessage( 'no introducers found' );
		}

		return false;
	}

	function _toaccountenquiry($template, $input) {
		global $session;

		$inCode = $input['enquiry'];

		if ($inCode <= 0) {
			return false;
		}

		$in = new Introducer( null );
		$found = $in->tryGettingRecord( $inCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this introducer has been deleted' );
			return false;
		}

		$ret = '../accounts/introducerList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../introducers/introducerAccountEnquiry.php?introducer=' . $inCode );
	}

	function _tomaindetails($template, $input) {
		global $session;

		$inCode = $input['main'];

		if ($inCode <= 0) {
			return false;
		}

		$in = new Introducer( null );
		$found = $in->tryGettingRecord( $inCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this introducer has been deleted' );
			return false;
		}

		$ret = '../accounts/introducerList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../introducers/introducerEdit.php?amendIntroducer=' . $inCode );
	}

	function _toreconcile($template, $input) {
		global $session;

		$inCode = $input['toReconcile'];

		if ($inCode <= 0) {
			return false;
		}

		$in = new Introducer( null );
		$found = $in->tryGettingRecord( $inCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this introducer has been deleted' );
			return false;
		}

		$ret = '../accounts/introducerList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../introducers/introducerRecon.php?introd=' . $inCode );
	}

	require( '../include/startup.php' );
	$introducerListTemplate = &$session->get( 'introducerListTemplate' );

	if ($introducerListTemplate == null) {
		$introducerListTemplate = new IntroducerListTemplate( 'introducerList.html' );
		$introducerListTemplate->setProcess( '_doSearch', 'doSearch' );
		$introducerListTemplate->setProcess( '_toAccountEnquiry', 'enquiry' );
		$introducerListTemplate->setProcess( '_toMainDetails', 'main' );
		$introducerListTemplate->setProcess( '_toReconcile', 'toReconcile' );
		$introducerListTemplate->setReturnTo( '../accounts/introducerList.php' );
	}

	$session->set( 'introducerListTemplate', $introducerListTemplate );
	$introducerListTemplate->process(  );
	$session->set( 'introducerListTemplate', $introducerListTemplate );
?>