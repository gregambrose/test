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

	function _selectpolicy($template, $input) {
		global $session;

		$plCode = $input['selectPolicy'];

		if ($plCode < 1) {
			return false;
		}

		$policy = new Policy( null );
		$found = $policy->tryGettingRecord( $plCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this policy has been deleted' );
			return false;
		}

		$ret = 'policies.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyEdit.php?amendPolicy=' . $plCode );
	}

	function _selectclient($template, $input) {
		global $session;

		$clCode = $input['selectClient'];

		if ($clCode < 1) {
			return false;
		}

		$client = new Client( null );
		$found = $client->tryGettingRecord( $clCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this client has been deleted' );
			return false;
		}

		$ret = '../policies/policies.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/clientEdit.php?amendClient=' . $clCode );
	}

	function _dosort($template, $input) {
		$type = $input['sort'];
		$template->setSortType( $type );
		_dosearch( $template, $input );
	}

	function _dosearch($template, $input) {
		$template->page = 0;
		$sortType = $template->getSortType(  );
		$plPolicyType = udbmakefieldsafe( $template->get( 'plPolicyType' ) );
		$policyNumber = udbmakefieldsafe( trim( $template->get( 'policyNumber' ) ) );
		$policyHolder = udbmakefieldsafe( trim( $template->get( 'policyHolder' ) ) );
		$policyCode = udbmakefieldsafe( trim( $template->get( 'policyCode' ) ) );

		if (( 0 < strlen( $policyCode ) && !is_numeric( $policyCode ) )) {
			$template->setMessage( 'policy code needs to be numeric' );
			return false;
		}


		if (!is_numeric( $policyCode )) {
			$policyCode = '';
		}

		$plInsCo = udbmakefieldsafe( $template->get( 'plInsCo' ) );
		$plStatus = udbmakefieldsafe( $template->get( 'plStatus' ) );
		$plClassOfBus = udbmakefieldsafe( $template->get( 'plClassOfBus' ) );
		$renewalFrom = udbmakefieldsafe( trim( $template->get( 'renewalFrom' ) ) );
		$renewalFrom = umakesqldate2( $renewalFrom );
		$renewalTo = udbmakefieldsafe( trim( $template->get( 'renewalTo' ) ) );
		$renewalTo = umakesqldate2( $renewalTo );
		$inceptionFrom = udbmakefieldsafe( trim( $template->get( 'inceptionFrom' ) ) );
		$inceptionFrom = umakesqldate2( $inceptionFrom );
		$inceptionTo = udbmakefieldsafe( trim( $template->get( 'inceptionTo' ) ) );
		$inceptionTo = umakesqldate2( $inceptionTo );
		$someDone = false;
		$q = 'SELECT plCode, plRenewalDate  FROM policies ';

		if ($sortType == 'client') {
			$q = 'SELECT plCode, plRenewalDate  FROM policies, clients WHERE clCode=plClient ';
			$someDone = true;
		}


		if ($sortType == 'ins') {
			$q = 'SELECT plCode, plRenewalDate  FROM policies, insuranceCompanies WHERE icCode=plInsCo ';
			$someDone = true;
		}


		if (( 1 < $policyCode && is_numeric( $policyCode ) )) {
			$q .= '' . 'WHERE plCode = ' . $policyCode;
		} 
else {
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


			if ($inceptionFrom != null) {
				if ($someDone == true) {
					$q .= 'AND ';
				} 
else {
					$q .= 'WHERE ';
				}

				$q .= '' . 'plInceptionDate >= \'' . $inceptionFrom . '\' ';
				$someDone = true;
			}


			if ($inceptionTo != null) {
				if ($someDone == true) {
					$q .= 'AND ';
				} 
else {
					$q .= 'WHERE ';
				}

				$q .= '' . 'plInceptionDate <= \'' . $inceptionTo . '\' ';
				$someDone = true;
			}


			if (0 < strlen( trim( $policyHolder ) )) {
				if ($someDone == true) {
					$q .= 'AND ';
				} 
else {
					$q .= 'WHERE ';
				}

				$q .= '' . 'plPolicyHolder  LIKE \'%' . $policyHolder . '%\' ';
				$someDone = true;
			}
		}


		if ($sortType == 'code') {
			$q .= ' ORDER BY  plCode';
		} 
else {
			if ($sortType == 'number') {
				$q .= ' ORDER BY  plPolicyNumber, plCode';
			} 
else {
				if ($sortType == 'type') {
					$q .= ' ORDER BY  plPolicyType, plRenewalDate, plPolicyNumber';
				} 
else {
					if ($sortType == 'cob') {
						$q .= ' ORDER BY  plClassOfBus, plRenewalDate, plPolicyNumber';
					} 
else {
						if ($sortType == 'client') {
							$q .= ' ORDER BY clName, plPolicyNumber, plClassOfBus, plRenewalDate ';
						} 
else {
							if ($sortType == 'ins') {
								$q .= ' ORDER BY  icName, plRenewalDate, plPolicyNumber';
							} 
else {
								if ($sortType == 'inc') {
									$q .= ' ORDER BY  plInceptionDate, plPolicyNumber';
								} 
else {
									if ($sortType == 'ren') {
										$q .= ' ORDER BY  plRenewalDate, plPolicyNumber';
									} 
else {
										$q .= ' ORDER BY  plPolicyNumber, plRenewalDate';
									}
								}
							}
						}
					}
				}
			}
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

		if (count( $policies ) == 0) {
			$template->setMessage( 'no policies found' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$policiesTemplate = &$session->get( 'policiesTemplate' );

	if ($policiesTemplate == null) {
		$policiesTemplate = new PoliciesTemplate( 'policies.html' );
		$policiesTemplate->setProcess( '_doSearch', 'doSearch' );
		$policiesTemplate->setProcess( '_selectPolicy', 'selectPolicy' );
		$policiesTemplate->setProcess( '_doSort', 'sort' );
		$policiesTemplate->setProcess( '_selectClient', 'selectClient' );
		$policiesTemplate->setReturnTo( '../policies/policies.php' );
	}

	$session->set( 'policiesTemplate', $policiesTemplate );
	$policiesTemplate->process(  );
	$session->set( 'policiesTemplate', $policiesTemplate );
?>