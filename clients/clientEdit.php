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

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		flocationheader( $url );
	}

	function _gotopolicy($template, $input) {
		global $session;

		$plCode = $input['selectPolicy'];

		if ($plCode < 1) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '' . '../clients/clientEdit.php?amendClient=' . $clCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyEdit.php?amendPolicy=' . $plCode );
	}

	function _toinvoiceaddress($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clInvAddress = $template->get( 'clInvAddress' );

		if ($clInvAddress != 0 - 1) {
			$template->setMessage( 'current address set as invoice address' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		flocationheader( '' . 'clientInvoiceAddress.php?amendClient=' . $clCode );
	}

	function _newcommercialpolicy($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$client = &$template->getClient(  );

		$ok = $client->isCreatingPoliciesAllowed(  );

		if ($ok == false) {
			$template->setMessage( 'this client\'s status means you can\'t create new policies' );
			return false;
		}

		_newpolicy( $template, $input, 'C' );
	}

	function _newretailpolicy($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$client = &$template->getClient(  );

		$ok = $client->isCreatingPoliciesAllowed(  );

		if ($ok == false) {
			$template->setMessage( 'this client\'s status means you can\'t create new policies' );
			return false;
		}

		_newpolicy( $template, $input, 'R' );
	}

	function _newpolicy($template, $input, $type) {
		global $session;
		global $brokerVATRate;
		global $companyVATRate;
		global $iptNormalRate;
		global $iptTravelRate;

		$policy = new Policy( null );
		$client = $template->getClient(  );
		$policy->setNewDefaults( $client, $type );
		$policy->insert( null );
		$plCode = $policy->getKeyValue(  );
		$ret = '../clients/clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyEdit.php?editPolicy=' . $plCode );
	}

	function _doupdate($template, $input) {
		$client = &$template->getClient(  );

		if ($client->recordExists(  ) == false) {
			trigger_error( 'cant get client', E_USER_ERROR );
		}

		$client->setAll( $input );
		$messg = $client->validate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$ok = $client->update(  );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$client->refresh(  );
			$template->setAll( $client->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		_updatepolicyhandlers( $clCode );
		$client->fetchExtraColumns(  );
		$template->setAll( $client->getAllForHTML(  ) );
		$template->set( 'message', 'client updated' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$clCode = $template->get( 'clCode' );
		$template->setClient( $clCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _tocommercial($template, $input) {
		$client = &$template->getClient(  );

		if ($client->recordExists(  ) == false) {
			trigger_error( 'cant get client', E_USER_ERROR );
		}

		$client->setAll( $input );
		$client->set( 'clType', 1 );
		$ok = $client->update(  );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setHTML( 'clientCommercialEdit.html' );
		$template->setAll( $client->getAllForHTML(  ) );
		$template->set( 'message', 'client now commercial' );
		return false;
	}

	function _toretail($template, $input) {
		$client = &$template->getClient(  );

		if ($client->recordExists(  ) == false) {
			trigger_error( 'cant get client', E_USER_ERROR );
		}

		$client->setAll( $input );
		$client->set( 'clType', 2 );
		$ok = $client->update(  );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setHTML( 'clientRetailEdit.html' );
		$template->setAll( $client->getAllForHTML(  ) );
		$template->set( 'message', 'client now retail' );
		return false;
	}

	function _dodelete($template, $input) {
		$clCode = $input['clCode'];
		$client = &$template->getClient(  );

		$clStatus = $client->get( 'clStatus' );

		if (( $clStatus != 3 && $clStatus != 4 )) {
			$template->setMessage( 'You can only delete a client if their status is set as lapsed' );
			return false;
		}

		$q = '' . 'SELECT plCode FROM policies  WHERE plClient = \'' . $clCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->setMessage( 'You cant delete this client as it is has one or more policies' );
			return false;
		}

		$q = '' . 'SELECT noCode FROM notes  WHERE noClient = \'' . $clCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->setMessage( 'You cant delete this client as it is has one or more client notes' );
			return false;
		}

		$q = '' . 'SELECT ctCode FROM clientTransactions  WHERE ctClient = \'' . $clCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->setMessage( 'You cant delete this client as it is has one or more transaction' );
			return false;
		}


		if ($client->recordExists(  ) == false) {
			trigger_error( '' . 'cant get client ' . $clCode, E_USER_ERROR );
		}

		$ok = $client->delete(  );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$client->refresh(  );
			$template->setAll( $client->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->setMessage( 'client deleted' );
		flocationheader( 'clients.php' );
		exit(  );
	}

	function _gotoclientnotes($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = 'clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientNotes.php?client=' . $clCode );
	}

	function _gotoaccountenquiry($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = 'clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientAccountEnquiry.php?client=' . $clCode );
	}

	function _gotohistory($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = 'clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientHistory.php?client=' . $clCode );
	}

	function _gotoclientdocuments($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = 'clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientDocms.php?client=' . $clCode );
	}

	function _tocashbatch($template, $input) {
		$clCode = $template->get( 'clCode' );
		flocationheader( '' . '../batches/cashBatchEdit.php?setClient=' . $clCode );
	}

	function _tojournal($template, $input) {
		$clCode = $template->get( 'clCode' );
		flocationheader( '' . '../accounts/journalEdit.php?setClient=' . $clCode );
	}

	function _createstatement($template, $input) {
		global $session;

		$clCode = $template->get( 'clCode' );
		$client = new Client( $clCode );
		$doCode = $client->createStatement(  );
		$ret = 'clientEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientDocms.php?client=' . $clCode . '&clientDocument=' . $doCode );
		return false;
	}

	function _updatepolicyhandlers($clCode) {
		$client = new Client( $clCode );
		$clHandler = $client->get( 'clHandler' );

		if ($clHandler <= 0) {
			return null;
		}

		$q = '' . 'SELECT plCode FROM policies WHERE plClient=' . $clCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		while ($row = udbgetrow( $result )) {
			$plCode = $row['plCode'];
			$policy = new Policy( $plCode );
			$plHandler = $policy->get( 'plHandler' );

			if (0 < $plHandler) {
				continue;
			}

			$policy->set( 'plHandler', $clHandler );
			$policy->update(  );
		}

	}

	require( '../include/startup.php' );
	$clientEditTemplate = &$session->get( 'clientEditTemplate' );

	if ($clientEditTemplate == null) {
		$clientEditTemplate = new ClientEditTemplate( 'clientCommercialEdit.html' );
		$clientEditTemplate->setProcess( '_doUpdate', 'update' );
		$clientEditTemplate->setProcess( '_doDelete', 'deleteClient' );
		$clientEditTemplate->setProcess( '_doCancel', 'cancel' );
		$clientEditTemplate->setProcess( '_doDelete', 'delete' );
		$clientEditTemplate->setProcess( '_goBack', 'back' );
		$clientEditTemplate->setProcess( '_goToPolicy', 'selectPolicy' );
		$clientEditTemplate->setProcess( '_newCommercialPolicy', 'newCommercialPolicy' );
		$clientEditTemplate->setProcess( '_newRetailPolicy', 'newRetailPolicy' );
		$clientEditTemplate->setProcess( '_toRetail', 'toRetail' );
		$clientEditTemplate->setProcess( '_toCommercial', 'toCommercial' );
		$clientEditTemplate->setProcess( '_goToClientNotes', 'clientNotes' );
		$clientEditTemplate->setProcess( '_goToAccountEnquiry', 'accEnquiry' );
		$clientEditTemplate->setProcess( '_goToclientDocuments', 'clientDocuments' );
		$clientEditTemplate->setProcess( '_goToHistory', 'history' );
		$clientEditTemplate->setProcess( '_toInvoiceAddress', 'invAddress' );
		$clientEditTemplate->setProcess( '_createStatement', 'createStatement' );
		$clientEditTemplate->setProcess( '_toCashBatch', 'toCashBatch' );
		$clientEditTemplate->setProcess( '_toJournal', 'toJournal' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$clientEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['amendClient'] )) {
		$clCode = $_GET['amendClient'];
		$clientEditTemplate->setClient( $clCode );
	}


	if (isset( $_GET['editClient'] )) {
		$clCode = $_GET['editClient'];
		$clientEditTemplate->setAndEditClient( $clCode );
	}


	if (isset( $_GET['refresh'] )) {
		$clCode = $clientEditTemplate->get( 'clCode' );
		$clientEditTemplate->setClient( $clCode );
	}

	$session->set( 'clientEditTemplate', $clientEditTemplate );
	$clientEditTemplate->process(  );
	$session->set( 'clientEditTemplate', $clientEditTemplate );
?>