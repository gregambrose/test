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
		$clientType = udbmakefieldsafe( $template->get( 'clientType' ) );
		$balanceType = udbmakefieldsafe( $template->get( 'balanceType' ) );
		$searchText = udbmakefieldsafe( trim( $template->get( 'searchText' ) ) );
		$clientCode = udbmakefieldsafe( trim( $template->get( 'clientCode' ) ) );

		if (( 0 < strlen( $clientCode ) && is_numeric( $clientCode ) == false )) {
			$template->setMessage( 'client code needs to be numeric' );
			return false;
		}

		$q = 'SELECT DISTINCT ctClient, sum(ctBalance), clCode
				FROM clients, clientTransactions
				WHERE clCode=ctClient ';
		$whereDone = true;

		if ($balanceType == 'Z') {
			$q = 'select DISTINCT clCode, clName, clFirstName, clLastName, clAddress, clPostcode  from clients 
					left  join clientTransactions  
					on ctClient=clCode
					GROUP BY clCode
					HAVING (sum(ctBalance) IS NULL OR  sum(ctBalance) = 0) ';
			$whereDone = true;
		}


		if (0 < $clientType) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . 'clType=' . $clientType . ' ';
		}


		if (0 < strlen( $searchText )) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . '(clName 		  LIKE \'%' . $searchText . '%\' OR
				   clFirstName    LIKE \'%' . $searchText . '%\' OR
				   clLastName     LIKE \'%' . $searchText . '%\' OR
				   clAddress      LIKE \'%' . $searchText . '%\' OR
				   clPostcode     LIKE \'%' . $searchText . '%\')
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

			$q .= '' . 'clNameSort    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $clientCode )) {
			if ($whereDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= ' WHERE ';
				$whereDone = true;
			}

			$q .= '' . 'clCode=' . $clientCode;
		}


		if ($balanceType == 'C') {
			$q .= ' group by ctClient
				HAVING sum(ctBalance)  < 0';
		}


		if ($balanceType == 'D') {
			$q .= ' group by ctClient
				HAVING sum(ctBalance)  > 0';
		}


		if ($balanceType == 'N') {
			$q .= ' group by ctClient
				HAVING sum(ctBalance)  != 0';
		}


		if ($balanceType == 'U') {
			$q .= ' AND ctTransType = \'C\' AND ctBalance  != 0
						GROUP BY ctClient';
		}

		$q .= ' ORDER BY clNameSort';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$clients = array(  );

		while ($row = udbgetrow( $result )) {
			$clients[] = $row['clCode'];
		}

		$template->clients = $clients;
		$found = count( $clients );
		$template->set( 'clientsFound', $found );

		if ($found == 0) {
			$template->clients = null;
			$template->setMessage( 'no clients found' );
		}

		return false;
	}

	function _toaccountenquiry($template, $input) {
		global $session;

		$clCode = $input['enquiry'];

		if ($clCode <= 0) {
			return false;
		}

		$client = new Client( null );
		$found = $client->tryGettingRecord( $clCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this client has been deleted' );
			return false;
		}

		$ret = '../accounts/clientList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/clientAccountEnquiry.php?clientAccount=' . $clCode );
	}

	function _tomaindetails($template, $input) {
		global $session;

		$clCode = $input['main'];

		if ($clCode <= 0) {
			return false;
		}

		$client = new Client( null );
		$found = $client->tryGettingRecord( $clCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this client has been deleted' );
			return false;
		}

		$ret = '../accounts/clientList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/clientEdit.php?amendClient=' . $clCode );
	}

	function _topayclient($template, $input) {
		global $session;

		$clCode = $input['payClient'];

		if ($clCode <= 0) {
			return false;
		}

		$client = new Client( null );
		$found = $client->tryGettingRecord( $clCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this client has been deleted' );
			return false;
		}

		$ret = '../accounts/clientList.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/cashPaymentEdit.php?payClient=' . $clCode );
	}

	require( '../include/startup.php' );
	$clientListTemplate = &$session->get( 'clientListTemplate' );

	if ($clientListTemplate == null) {
		$clientListTemplate = new ClientListTemplate( 'clientList.html' );
		$clientListTemplate->setProcess( '_doSearch', 'doSearch' );
		$clientListTemplate->setProcess( '_toAccountEnquiry', 'enquiry' );
		$clientListTemplate->setProcess( '_toMainDetails', 'main' );
		$clientListTemplate->setProcess( '_toPayClient', 'payClient' );
	}

	$session->set( 'clientListTemplate', $clientListTemplate );
	$clientListTemplate->process(  );
	$session->set( 'clientListTemplate', $clientListTemplate );
?>