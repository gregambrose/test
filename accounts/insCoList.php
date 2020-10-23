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
		$insCoCode = udbmakefieldsafe( trim( $template->get( 'insCoCode' ) ) );

		if (( 0 < strlen( $insCoCode ) && is_numeric( $insCoCode ) == false )) {
			$template->setMessage( 'client code needs to be numeric' );
			return false;
		}

		$q = 'SELECT DISTINCT itInsCo, sum(itBalance), icCode,  icName
				FROM insuranceCompanies, inscoTransactions
				WHERE icCode=itInsCo ';
		$whereDone = true;

		if ($balanceType == 'Z') {
			$q = 'select DISTINCT icCode, icName, icAddress, icContact, icPostcode  from insuranceCompanies
					left  join inscoTransactions  
					on itInsCo=icCode
					GROUP BY icCode
					HAVING (sum(itBalance) IS NULL OR  sum(itBalance) = 0) ';
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

			$q .= '' . '(icName 		 LIKE \'%' . $searchText . '%\' OR
				   icAddress     LIKE \'%' . $searchText . '%\' OR
				   icContact     LIKE \'%' . $searchText . '%\' OR
				   icPostcode    LIKE \'%' . $searchText . '%\')
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

			$q .= '' . 'icName    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $insCoCode )) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . 'icCode=' . $insCoCode;
		}


		if ($balanceType == 'C') {
			$q .= ' GROUP BY itInsCo
				HAVING sum(itBalance)  < 0';
		}


		if ($balanceType == 'D') {
			$q .= ' GROUP BY itInsCo
				HAVING sum(itBalance)  > 0';
		}


		if ($balanceType == 'N') {
			$q .= ' GROUP BY itInsCo
				HAVING sum(itBalance)  != 0';
		}


		if ($balanceType == 'U') {
			$q .= ' AND (itTransType = \'C\'  OR itTransType = \'R\')
						AND itBalance  != 0
						 GROUP BY itInsCo';
		}

		$q .= ' ORDER BY  icName';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$insCos = array(  );

		while ($row = udbgetrow( $result )) {
			$insCos[] = $row['icCode'];
		}

		$template->insCos = $insCos;
		$found = count( $insCos );
		$template->set( 'insCosFound', $found );

		if ($found == 0) {
			$template->insCos = null;
			$template->setMessage( 'no insurance companies found' );
		}

		return false;
	}

	function _toaccountenquiry($template, $input) {
		global $session;

		$icCode = $input['enquiry'];

		if ($icCode <= 0) {
			return false;
		}

		$ins = new InsCo( null );
		$found = $ins->tryGettingRecord( $icCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this company has been deleted' );
			return false;
		}

		$ret = '../accounts/insCoList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoAccountEnquiry.php?inscoAccount=' . $icCode );
	}

	function _tomaindetails($template, $input) {
		global $session;

		$icCode = $input['main'];

		if ($icCode <= 0) {
			return false;
		}

		$ins = new InsCo( null );
		$found = $ins->tryGettingRecord( $icCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this company has been deleted' );
			return false;
		}

		$ret = '../accounts/insCoList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoEdit.php?amendInsco=' . $icCode );
	}

	function _toreconcile($template, $input) {
		global $session;

		$icCode = $input['toReconcile'];

		if ($icCode <= 0) {
			return false;
		}

		$ins = new InsCo( null );
		$found = $ins->tryGettingRecord( $icCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this company has been deleted' );
			return false;
		}

		$ret = '../accounts/insCoList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoRecon.php?insco=' . $icCode );
	}

	require( '../include/startup.php' );
	$insCoListTemplate = &$session->get( 'insCoListTemplate' );

	if ($insCoListTemplate == null) {
		$insCoListTemplate = new InsCoListTemplate( 'insCoList.html' );
		$insCoListTemplate->setProcess( '_doSearch', 'doSearch' );
		$insCoListTemplate->setProcess( '_toAccountEnquiry', 'enquiry' );
		$insCoListTemplate->setProcess( '_toMainDetails', 'main' );
		$insCoListTemplate->setProcess( '_toReconcile', 'toReconcile' );
		$insCoListTemplate->setReturnTo( '../accounts/insCoList.php' );
	}

	$session->set( 'insCoListTemplate', $insCoListTemplate );
	$insCoListTemplate->process(  );
	$session->set( 'insCoListTemplate', $insCoListTemplate );
?>