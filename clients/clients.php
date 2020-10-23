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

		$ret = 'clients.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientEdit.php?amendClient=' . $clCode );
	}

	function _newcommercialclient($template, $input) {
		global $session;

		$ctType = COMMERCIAL_TYPE;
		$q = '' . 'INSERT  INTO clients (clType) VALUES(' . $ctType . ')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$clCode = udbgetinsertid(  );
		$client = new Client( $clCode );
		$client->setNewDefaults(  );
		$client->update(  );
		$ret = 'clients.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientEdit.php?editClient=' . $clCode );
		exit(  );
	}

	function _newretailclient($template, $input) {
		global $session;

		$ctType = RETAIL_TYPE;
		$q = '' . 'INSERT  INTO clients (clType) VALUES(' . $ctType . ')';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$clCode = udbgetinsertid(  );
		$client = new Client( $clCode );
		$client->setNewDefaults(  );
		$client->update(  );
		$ret = 'clients.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientEdit.php?editClient=' . $clCode );
		exit(  );
	}

	function _dosort($template, $input) {
		$type = $input['sort'];
		$template->setSortType( $type );
		_dosearch( $template, $input );
	}

	function _dosearch($template, $input) {
		global $session;

		$template->page = 0;
		$sortType = $template->getSortType(  );
		$alphas = udbmakefieldsafe( $template->get( 'alphas' ) );
		$clientType = udbmakefieldsafe( $template->get( 'clientType' ) );
		$searchText = udbmakefieldsafe( trim( $template->get( 'searchText' ) ) );
		$clientCode = udbmakefieldsafe( trim( $template->get( 'clientCode' ) ) );
		$clStatus = $template->get( 'clStatus' );

		if (( 0 < strlen( $clientCode ) && is_numeric( $clientCode ) == false )) {
			$template->setMessage( 'client code needs to be numeric' );
			return false;
		}

		$q = 'SELECT clCode FROM clients ';
		$someDone = false;

		if (0 < $clientType) {
			$q .= 'WHERE ';
			$q .= '' . 'clType=' . $clientType . ' ';
			$someDone = true;
		}


		if (0 < strlen( $searchText )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . '(clName 		  LIKE \'%' . $searchText . '%\' OR
				   clFirstName    LIKE \'%' . $searchText . '%\' OR
				   clLastName     LIKE \'%' . $searchText . '%\' OR
				   clAddress      LIKE \'%' . $searchText . '%\' OR
				   clPostcode     LIKE \'%' . $searchText . '%\')
			   ';
			$someDone = true;
		}


		if (strlen( $alphas ) == 1) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'clNameSort        LIKE \'' . $alphas . '%\' ';
			$someDone = true;
		}


		if (0 < strlen( $clientCode )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'clCode=' . $clientCode . ' ';
			$someDone = true;
		}


		if (0 < $clStatus) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'clStatus=' . $clStatus;
			$someDone = true;
		}


		if ($sortType == 'occ') {
			$q .= ' ORDER BY  clBusinessTrade,  clName, clLastName';
		} 
else {
			if ($sortType == 'name') {
				$q .= ' ORDER BY  clNameSort, clName, clLastName';
			} 
else {
				if ($sortType == 'type') {
					$q .= ' ORDER BY  clType, clNameSort,clName, clLastName';
				} 
else {
					if ($sortType == 'code') {
						$q .= ' ORDER BY  clCode';
					} 
else {
						$q .= ' ORDER BY  clNameSort, clName, clLastName';
					}
				}
			}
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ) . $q, E_USER_ERROR );
		}

		$clients = array(  );

		while ($row = udbgetrow( $result )) {
			$clients[] = $row['clCode'];
		}

		$template->clients = $clients;

		if (count( $clients ) == 0) {
			$template->setMessage( 'no clients found' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$clientsTemplate = &$session->get( 'clientsTemplate' );

	if ($clientsTemplate == null) {
		$clientsTemplate = new ClientsTemplate( 'clients.html' );
		$clientsTemplate->setProcess( '_doSearch', 'doSearch' );
		$clientsTemplate->setProcess( '_doSort', 'sort' );
		$clientsTemplate->setProcess( '_newCommercialClient', 'newCommercialClient' );
		$clientsTemplate->setProcess( '_newRetailClient', 'newRetailClient' );
		$clientsTemplate->setProcess( '_goToMenu', 'home' );
		$clientsTemplate->setProcess( '_selectClient', 'selectClient' );
	}

	$session->set( 'clientsTemplate', $clientsTemplate );
	$clientsTemplate->process(  );
	$session->set( 'clientsTemplate', $clientsTemplate );
?>