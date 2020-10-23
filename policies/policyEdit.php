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

	function _savepolicy($template, $input) {
		$ok = _doupdate( $template, $input );

		if ($ok == false) {
			return false;
		}

		return false;
	}

	function _selectclient($template, $input) {
		$clCode = $input['selectClient'];

		if (( $clCode < 1 || $clCode == null )) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		_viewclientdetails( $template, $input );
	}

	function _viewclientdetails($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'plClient' );

		if ($clCode < 1) {
			$template->setMessage( 'No client to view' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/clientEdit.php?amendClient=' . $clCode );
	}

	function _accounts($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plDirect = $template->get( 'plDirect' );

		if (( $plDirect != 0 - 1 && $plDirect != 1 )) {
			$template->setMessage( 'you need to set the policy as direct or broker' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyAccountEdit.php?amendPolicy=' . $plCode );
	}

	function _viewpolicynotes($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyNotes.php?policy=' . $plCode . '&returnTo=policyEdit.php' );
	}

	function _viewtrans($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyTransEdit.php?policy=' . $plCode );
	}

	function _viewspecifictrans($template, $input) {
		global $session;

		$ptCode = $input['policyTransToView'];

		if ($ptCode < 1) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
	}

	function _viewspecificclientnote($template, $input) {
		global $session;

		$cnCode = $input['clientNoteToView'];

		if (( $cnCode < 1 || $cnCode == null )) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ok = _doupdate( $template, $input );

		if ($ok == false) {
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		$clCode = $template->get( 'clCode' );
		flocationheader( '' . 'clientNotes.php?client=' . $clCode . '&returnTo=policyEdit.php&clientNote=' . $cnCode );
	}

	function _viewspecificpolicynote($template, $input) {
		global $session;

		$noCode = $input['policyNoteToView'];

		if (( $noCode < 1 || $noCode == null )) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$note = new Note( $noCode );
		$plCode = $note->get( 'noPolicy' );
		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyNotes.php?policy=' . $plCode . '&policyNote=' . $noCode );
	}

	function _gotopolicydocuments($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = 'policyEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyDocms.php?policy=' . $plCode );
	}

	function _viewdocument($template, $input) {
		if (( !isset( $input['docmFileToView'] ) || $input['docmFileToView'] <= 0 )) {
			return false;
		}

		$doCode = $input['docmFileToView'];
		$docm = new Document( $doCode );
		$ok = $docm->viewDocument(  );

		if ($ok == false) {
			$template->setMessage( 'sorry....cant show you that document' );
		}

		return false;
	}

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$script = $template->popReturnTo(  );

		if ($script == '') {
			trigger_error( 'cant go back', E_USER_ERROR );
		}

		flocationheader( $script );
	}

	function _docancel($template, $input) {
		$plCode = $template->get( 'plCode' );
		$template->setPolicy( $plCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _doupdate($template, $input) {
		$policy = &$template->getPolicy(  );

		if ($policy->recordExists(  ) == false) {
			trigger_error( 'cant get policy', E_USER_ERROR );
		}

		$policy->setAll( $input );

		if ($policy->get( 'plClientDisc' ) != 1) {
			$policy->set( 'plClientDiscountRate', 0 );
			$policy->set( 'plClientDiscount', 0 );
		}


		if ($policy->get( 'plIntrodComm' ) != 1) {
			$policy->set( 'plIntroducerCommRate', 0 );
			$policy->set( 'plIntroducerComm', 0 );
		}

		$policy->decideIPTAndVATRates(  );
		$policy->recalculateAccountingFields(  );
		$plCode = $policy->getKeyValue(  );
		$template->setAll( $policy->getAllForHTML(  ) );

		if (_validate( $template ) == false) {
			return false;
		}

		$ok = $policy->update(  );

		if ($ok == false) {
			$plCode = $template->get( 'plCode' );
			$template->setPolicy( $plCode );
			$template->setMessage( 'Sorry...Someone else has amended this policy. You will need to re-enter any changes you made' );
			return false;
		}

		$policy->fetchExtraColumns(  );
		$template->setAll( $policy->getAllForHTML(  ) );
		$template->set( 'message', 'policy updated' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	function _dodelete($template, $input) {
		global $session;

		$plCode = $input['plCode'];
		$policy = new Policy( $plCode );
		$q = '' . 'SELECT noCode FROM notes  WHERE noPolicy = \'' . $plCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this policy as it is has notes' );
			return false;
		}

		$q = '' . 'SELECT doCode FROM documents  WHERE doDeleted != 1 AND doPolicy = \'' . $plCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this policy as it is has documents' );
			return false;
		}

		$q = '' . 'SELECT ptCode FROM policyTransactions   WHERE ptPolicy = ' . $plCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this policy as it is has transactions' );
			return false;
		}


		if ($policy->recordExists(  ) == false) {
			trigger_error( '' . 'cant get policy ' . $plCode, E_USER_ERROR );
		}

		$ok = $policy->delete(  );

		if ($ok == false) {
			$plCode = $template->get( 'plCode' );
			$template->setPolicy( $plCode );
			$template->setMessage( 'Sorry...Someone else has amended this policy. You will need to re-enter any changes you made' );
			return false;
		}

		$script = $template->popReturnTo(  );

		if ($script == '') {
			trigger_error( 'cant go back', E_USER_ERROR );
		}

		$session->set( 'returnTo', $script );
		flocationheader( $script );
		exit(  );
	}

	function _validate($template) {
		$client = &$template->getClient(  );

		$client->refresh(  );
		$plInceptionDate = umakesqldate2( $template->get( 'plInceptionDate' ) );
		$plFrequency = $template->get( 'plFrequency' );
		$plRenewalDate = umakesqldate2( $template->get( 'plRenewalDate' ) );
		$plTORDate = umakesqldate2( $template->get( 'plTORDate' ) );
		$plEnquiryDate = umakesqldate2( $template->get( 'plEnquiryDate' ) );
		$plPaymentMethod = $template->get( 'plPaymentMethod' );
		$plPolicyNumber = $template->get( 'plPolicyNumber' );
		$plSourceOfBus = $template->get( 'plSourceOfBus' );
		$plHandler = $template->get( 'plHandler' );
		$plDirect = $template->get( 'plDirect' );
		$plInsCo = $template->get( 'plInsCo' );
		$plInsCo = $template->get( 'plInsCo' );
		$plClassOfBus = $template->get( 'plClassOfBus' );
		$plSaleMethod = $template->get( 'plSaleMethod' );
		$plCoverDescription = $template->get( 'plCoverDescription' );
		$plDirect = $template->get( 'plDirect' );
		$plIntrodComm = $template->get( 'plIntrodComm' );
		$plClientDisc = $template->get( 'plClientDisc' );
		$plAltInsCo = $template->get( 'plAltInsCo' );
		$plAddOnCoverDescription = $template->get( 'plAddOnCoverDescription' );
		$plStatus = $template->get( 'plStatus' );
		$clStatus = $client->get( 'clStatus' );
		$plPolicyType = $template->get( 'plPolicyType' );

		if (( $plPolicyType != 'C' && $plPolicyType != 'R' )) {
			$template->setMessage( 'you must specify a policy type' );
			return false;
		}


		if ($plInsCo < 1) {
			$template->setMessage( 'you must specify an insurance company' );
			return false;
		}


		if (strlen( trim( $plPolicyNumber ) ) == 0) {
			$template->setMessage( 'you must enter a policy number, else \'to be advised\'' );
			return false;
		}


		if ($plFrequency == 0) {
			$template->setMessage( 'you must specify a frequency' );
			return false;
		}


		if ($plClassOfBus < 1) {
			$template->setMessage( 'you must specify a class of business' );
			return false;
		}


		if ($plSaleMethod < 1) {
			$template->setMessage( 'you must specify a sale method' );
			return false;
		}


		if ($plSourceOfBus < 1) {
			$template->setMessage( 'you must specify a source of business' );
			return false;
		}


		if ($plHandler < 1) {
			$template->setMessage( 'you must specify a handler' );
			return false;
		}


		if ($plDirect == 0) {
			$template->setMessage( 'you must specify whether this is a direct policy or not' );
			return false;
		}


		if (( $plInceptionDate == null && $plStatus == 1 )) {
			$template->setMessage( 'you must specify an inception date for a live policy' );
			return false;
		}


		if (( $plStatus == 2 && $plEnquiryDate == '' )) {
			$template->setMessage( 'you must specify a sales enquiry date for a prospect policy' );
			return false;
		}


		if (( $plStatus == 1 && $clStatus != 1 )) {
			$template->setMessage( 'you can\'t set this policy to live as client not live' );
			return false;
		}


		if (trim( $plCoverDescription ) == '') {
			$template->setMessage( 'you must enter a cover description' );
			return false;
		}


		if ($plIntrodComm == 0) {
			$template->setMessage( 'you must decide if the introducer gets commission or not' );
			return false;
		}


		if (( $plIntrodComm == 1 && $client->get( 'clIntroducer' ) < 1 )) {
			$template->setMessage( 'client has no introducer so you cant allocate them commission' );
			return false;
		}


		if ($plClientDisc == 0) {
			$template->setMessage( 'you must decide if the client can get a discount or not' );
			return false;
		}


		if (( $plClientDisc == 1 && $client->get( 'clDiscount' ) != 1 )) {
			$template->setMessage( 'you can\'t apply a discount as the client has been set not to allow discounts' );
			return false;
		}


		if ($plPaymentMethod < 1) {
			$template->setMessage( 'you must specify a payment method' );
			return false;
		}


		if (( 0 < $plAltInsCo && trim( $plAddOnCoverDescription ) == '' )) {
			$template->setMessage( 'you need to enter an add on cover description' );
			return false;
		}


		if (( $plRenewalDate != null && $plTORDate != null )) {
			$template->setMessage( 'you can\'t have a renewal date, and a T.O.R. date' );
			return false;
		}


		if (( 0 < $plFrequency && $plTORDate != null )) {
			$template->setMessage( 'you can\'t have a T.O.R. date when it is not a single policy' );
			return false;
		}


		if (( $plFrequency < 0 && $plTORDate == null )) {
			$template->setMessage( 'you must enter a T.O.R. date on a single policy' );
			return false;
		}


		if (( $plRenewalDate != null && $plInceptionDate != null )) {
			if ($plRenewalDate <= $plInceptionDate) {
				$template->setMessage( 'you can\'t have a renewal date on or before the inception date' );
				return false;
			}
		}


		if ($plFrequency == 0 - 1) {
			if ($plRenewalDate != null) {
				$template->setMessage( 'a single policy can\'t have a renewal date' );
				return false;
			}
		}


		if (( $plEnquiryDate != null && $plInceptionDate != null )) {
			if ($plInceptionDate < $plEnquiryDate) {
				$template->setMessage( 'sales enquiry date can\'t be after inception date' );
				return false;
			}
		}


		if (( 0 < $plPaymentMethod && $plDirect != 0 )) {
			$method = new PaymentMethod( $plPaymentMethod );
			$pmDirect = $method->get( 'pmDirect' );

			if ($plDirect == 1) {
				$direct = 1;
			} 
else {
				$direct = 0;
			}


			if ($pmDirect != $direct) {
				if ($pmDirect == 0) {
					$template->setMessage( 'wrong payment method for direct policy' );
				}


				if ($pmDirect == 1) {
					$template->setMessage( 'wrong payment method for broker paid policy' );
				}

				return false;
			}
		}

		return true;
	}

	require( '../include/startup.php' );
	$policyEditTemplate = &$session->get( 'policyEditTemplate' );

	if ($policyEditTemplate == null) {
		$policyEditTemplate = new PolicyEditTemplate( 'policyEdit.html' );
		$policyEditTemplate->setProcess( '_goBack', 'back' );
		$policyEditTemplate->setProcess( '_savePolicy', 'update' );
		$policyEditTemplate->setProcess( '_doDelete', 'delete' );
		$policyEditTemplate->setProcess( '_viewpolicyNotes', 'policyNotes' );
		$policyEditTemplate->setProcess( '_viewTrans', 'viewTrans' );
		$policyEditTemplate->setProcess( '_doCancel', 'cancel' );
		$policyEditTemplate->setProcess( '_viewClientDetails', 'clientDetails' );
		$policyEditTemplate->setProcess( '_selectClient', 'selectClient' );
		$policyEditTemplate->setProcess( '_viewSpecificClientNote', 'clientNoteToView' );
		$policyEditTemplate->setProcess( '_viewSpecificPolicyNote', 'policyNoteToView' );
		$policyEditTemplate->setProcess( '_viewSpecificTrans', 'policyTransToView' );
		$policyEditTemplate->setProcess( '_viewDocument', 'docmFileToView' );
		$policyEditTemplate->setProcess( '_goToPolicyDocuments', 'policyDocuments' );
		$policyEditTemplate->setProcess( '_accounts', 'accountDetail' );
		$policyEditTemplate->setProcess( '_viewDocument', 'viewDoc' );
	}


	if (isset( $_GET['amendPolicy'] )) {
		$plCode = $_GET['amendPolicy'];
		$policyEditTemplate->setPolicy( $plCode );
	}


	if (isset( $_GET['editPolicy'] )) {
		$plCode = $_GET['editPolicy'];
		$policyEditTemplate->setAndEditPolicy( $plCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$policyEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['refresh'] )) {
		$plCode = $policyEditTemplate->get( 'plCode' );
		$policyEditTemplate->setPolicy( $plCode );
	}

	$session->set( 'policyEditTemplate', $policyEditTemplate );
	$policyEditTemplate->process(  );
	$session->set( 'policyEditTemplate', $policyEditTemplate );
?>