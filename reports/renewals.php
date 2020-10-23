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
		$sortType = $template->getSortType(  );
		$plPolicyType = $template->get( 'plPolicyType' );
		$policyNumber = trim( $template->get( 'policyNumber' ) );
		$policyCode = trim( $template->get( 'policyCode' ) );

		if (( 0 < strlen( $policyCode ) && !is_numeric( $policyCode ) )) {
			$template->setMessage( 'policy code needs to be numeric' );
			return false;
		}


		if (!is_numeric( $policyCode )) {
			$policyCode = '';
		}

		$plInsCo = $template->get( 'plInsCo' );
		$plStatus = $template->get( 'plStatus' );
		$plClassOfBus = $template->get( 'plClassOfBus' );
		$renewalFrom = trim( $template->get( 'renewalFrom' ) );
		$renewalFrom = umakesqldate2( $renewalFrom );
		$renewalTo = trim( $template->get( 'renewalTo' ) );
		$renewalTo = umakesqldate2( $renewalTo );

		if (( ( $renewalTo != '' && $renewalTo != '' ) && $renewalTo < $renewalFrom )) {
			$template->setMessage( 'dates are round the wrong way' );
			return false;
		}

		$someDone = false;
		$q = 'SELECT plCode, plRenewalDate  FROM policies, clients WHERE clCode=plClient ';
		$someDone = true;

		if (strlen( $plPolicyType ) == 1) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plPolicyType=\'' . $plPolicyType . '\' ';
			$someDone = true;
		}


		if (0 < strlen( $policyNumber )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plPolicyNumber LIKE \'%' . $policyNumber . '%\' ';
			$someDone = true;
		}


		if (0 < $plInsCo) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plInsCo = ' . $plInsCo . ' ';
			$someDone = true;
		}


		if (0 < $plClassOfBus) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plClassOfBus = ' . $plClassOfBus . ' ';
			$someDone = true;
		}


		if (0 < $plStatus) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plStatus = ' . $plStatus . ' ';
			$someDone = true;
		}


		if ($renewalFrom != null) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plRenewalDate >= \'' . $renewalFrom . '\' ';
			$someDone = true;
		}


		if ($renewalTo != null) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'plRenewalDate <= \'' . $renewalTo . '\' ';
			$someDone = true;
		}


		if ($sortType == 'C') {
			$q .= ' ORDER BY clName,  plRenewalDate ';
		} 
else {
			$q .= ' ORDER BY  plRenewalDate, clName  ';
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$policies = array(  );

		while ($row = udbgetrow( $result )) {
			$policies[] = $row['plCode'];
		}

		$template->policies = $policies;
		$found = count( $policies );
		$template->set( 'policiesFound', $found );

		if ($found == 0) {
			$template->policies = null;
			$template->setMessage( 'no policies found' );
		}

		$plStatusShown = '';

		if (0 < $plStatus) {
			$ps = new PolicyStatus( $plStatus );
			$plStatusShown = $ps->get( 'stName' );
		}

		$template->set( 'plStatusShown', $plStatusShown );
		return false;
	}

	require( '../include/startup.php' );
	$renewalsTemplate = &$session->get( 'renewalsTemplate' );

	if ($renewalsTemplate == null) {
		$renewalsTemplate = new RenewalsTemplate( 'renewals.html' );
		$renewalsTemplate->setProcess( '_doSearch', 'doSearch' );
		$renewalsTemplate->setReturnTo( '../reports/renewals.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$renewalsTemplate->setHTML( 'renewalsOutput.html' );
		$renewalsTemplate->setHeaderFields(  );
	} 
else {
		$renewalsTemplate->setHTML( 'renewals.html' );
	}

	$session->set( 'renewalsTemplate', $renewalsTemplate );
	$renewalsTemplate->process(  );
	$session->set( 'renewalsTemplate', $renewalsTemplate );
?>