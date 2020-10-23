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

	function _createtransaction($template, $input) {
		$transType = $input['transTypes'];

		if ($transType < 1) {
			$template->setMessage( 'you must choose a transaction type' );
			return false;
		}


		if (isset( $template->policyTransaction )) {
			$pt = $template->getTrans(  );
		} 
else {
			$pt = null;
		}

		$template->refreshPolicy(  );
		$policy = $template->getRefreshedPolicy(  );
		$plRenewalDate = $policy->get( 'plRenewalDate' );
		$plFrequency = $policy->get( 'plFrequency' );
		$plStatus = $policy->get( 'plStatus' );
		$plClassOfBus = $policy->get( 'plClassOfBus' );
		$plCoverDescription = trim( $policy->get( 'plCoverDescription' ) );
		$plInsCo = $policy->get( 'plInsCo' );
		$plHandler = $policy->get( 'plHandler' );
		$plInceptionDate = $policy->get( 'plInceptionDate' );
		$plTORDate = $policy->get( 'plTORDate' );
		$plPaymentMethod = $policy->get( 'PaymentMethod' );

		if ($plInsCo < 1) {
			$template->setMessage( 'this policy doesnt have a main insurance company' );
			return false;
		}


		if ($plHandler < 1) {
			$template->setMessage( 'this policy needs a handler' );
			return false;
		}


		if (( ( ( ( $plStatus == 3 || $plStatus == 4 ) || $plStatus == 5 ) || $plStatus == 7 ) || $plStatus == 10 )) {
			$template->setMessage( 'the policy status indicates that no further transactions can be posted to this policy' );
			return false;
		}


		if (( $plInceptionDate == null || $plInceptionDate == '0000-00-00' )) {
			$template->setMessage( 'policy needs an inception date to process this transaction' );
			return false;
		}


		if ($plFrequency == 0) {
			$template->setMessage( 'policy needs a frequency or set as a single policy to process this transaction' );
			return false;
		}


		if (( $plRenewalDate == null || $plRenewalDate == '0000-00-00' )) {
			if ($transType == 1) {
				$template->setMessage( 'no renewal date set so can\'t renew' );
				return false;
			}
		} 
else {
			if (( $transType == 4 || $transType == 6 )) {
				$template->setMessage( 'you can\'t process this transaction as there is already a renewal date' );
				return false;
			}
		}


		if (( $transType == 4 && ( $plStatus != 1 && $plStatus != 2 ) )) {
			$template->setMessage( 'The policy status needs to be \'prospect\' or \'live\'to process a first premium' );
			return false;
		}


		if (( $transType == 6 && ( $plStatus != 1 && $plStatus != 2 ) )) {
			$template->setMessage( 'The policy status needs to be \'prospect\' or \'live\'to process a provisional premium' );
			return false;
		}


		if ($plClassOfBus < 1) {
			$template->setMessage( 'The policy needs to have a class of business' );
			return false;
		}


		if (strlen( $plCoverDescription ) == 0) {
			$template->setMessage( 'The policy needs to have a cover description' );
			return false;
		}


		if (( ( ( ( $transType == 2 || $transType == 3 ) || $transType == 7 ) || $transType == 8 ) || $transType == 9 )) {
			if (( 0 < $plFrequency && ( $plRenewalDate == null || $plRenewalDate == '0000-00-00' ) )) {
				$template->setMessage( 'this policy needs to have a renewal date' );
				return false;
			}


			if (( $plFrequency == 0 - 1 && ( $plTORDate == null || $plTORDate == '0000-00-00' ) )) {
				$template->setMessage( 'this policy needs to have a TOR date' );
				return false;
			}
		}


		if ($transType == 1) {
			if (( $plRenewalDate == null || $plRenewalDate == '0000-00-00' )) {
				$template->setMessage( 'this policy needs to have a renewal date' );
				return false;
			}


			if ($plFrequency <= 0) {
				$template->setMessage( 'this policy needs a frequency to process this transaction' );
				return false;
			}

			$nextMonth = uaddmonthstonow( 3 );
			$nextMonth = umakesqldatefromourtimestamp( $nextMonth );

			if ($nextMonth < $plRenewalDate) {
				$template->setMessage( 'you can\'t renew a policy until three months before its renewal date' );
				return false;
			}
		}


		if ($transType == 5) {
			if (( $plTORDate == null || $plTORDate == '0000-00-00' )) {
				$template->setMessage( 'policy needs either a TOR date to process this transaction' );
				return false;
			}


			if ($plFrequency != 0 - 1) {
				$template->setMessage( 'policy needs to be set as single to process this transaction' );
				return false;
			}


			if (( $plRenewalDate != null && $plRenewalDate != '0000-00-00' )) {
				$template->setMessage( 'policy has a renewal date, so this transaction cannot be processed' );
				return false;
			}
		}


		if ($transType == 10) {
			if ($pt == null) {
				$template->setMessage( 'you need to choose a transaction to reverse' );
				return false;
			}

			$ptCode = $pt->getKeyValue(  );
		}


		if ($transType == 10) {
			$template->startTransactionFromTransactionToReverse( $ptCode );
			$template->set( 'ptCode', '' );
			$template->set( 'ptCodeFormatted', '' );
			$template->setMessage( 'the selected transaction will be reversed' );
		} 
else {
			$template->set( 'ptCode', '' );
			$template->set( 'ptCodeFormatted', '' );
			$template->set( 'statusDesc', '' );
			$template->set( 'statusDate', '' );
			$template->set( 'ptCoverDesc', '' );
			$template->startTransaction( $transType );
			$template->setMessage( 'enter transaction details' );
		}

		$template->setFieldsUneditable(  );
		$template->setAllowEditing( true );
		$template->setAllowExiting( false );
		return false;
	}

	function _savetransaction($template, $input) {
		$template->setTransactionDetails(  );

		if (_validate( $template ) == false) {
			return false;
		}

		$template->saveTransaction(  );
		$template->setMessage( 'transaction saved' );
		$template->retrieveTransactionDetails(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
	}

	function _deletetransaction($template, $input) {
		$template->deleteTransaction(  );
		$template->setMessage( 'transaction deleted' );
		$template->retrieveTransactionDetails(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
	}

	function _posttransaction($template, $input) {
		$template->setTransactionDetails(  );

		if (_validate( $template ) == false) {
			return false;
		}

		$template->postTransaction(  );
		$template->setMessage( 'transaction posted' );
		$template->retrieveTransactionDetails(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$template->cancelTransaction(  );
		$template->setMessage( 'transaction entry cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _viewtransaction($template, $input) {
		$ptCode = $input['transToView'];

		if ($ptCode <= 0) {
			return false;
		}

		$template->policyTransaction = new PolicyTransaction( $ptCode );
		$pt = &$template->policyTransaction;

		$plCode = $pt->get( 'ptPolicy' );
		$template->setPolicy( $plCode );
		$template->policyTransaction = new PolicyTransaction( $ptCode );
		$template->retrieveTransactionDetails(  );
		$policy = $template->getPolicy(  );

		if (!is_a( $policy, 'Policy' )) {
			$plCode = $template->policyTransaction->get( 'ptPolicy' );
			$template->setPolicy( $plCode );
			$template->policyTransaction = new PolicyTransaction( $ptCode );
			$template->retrieveTransactionDetails(  );
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _viewdocument($template, $input) {
		$trans = $template->getTrans(  );
		$doCode = $trans->get( 'ptDocm' );

		if ($doCode < 1) {
			$template->setMessage( 'no document to view' );
			return false;
		}

		$docm = new Document( $doCode );
		$docm->viewDocument(  );
		return false;
	}

	function _transdocuments($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$trans = $template->getTrans(  );
		$ptCode = $trans->getKeyValue(  );

		if ($ptCode < 1) {
			$template->setMessage( 'no transaction to view' );
			return false;
		}

		$ret = '' . '../policies/policyTransEdit.php?trans=' . $ptCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyTransDocms.php?trans=' . $ptCode );
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

	function _doupdate($template, $input) {
		$policy = &$template->getPolicy(  );

		if ($policy->recordExists(  ) == false) {
			trigger_error( 'cant get policy', E_USER_ERROR );
		}

		$policy->setAll( $input );
		$policy->decideIPTAndVATRates(  );
		$policy->recalculateAccountingFields(  );
		$plCode = $policy->getKeyValue(  );
		$template->setAll( $policy->getAllForHTML(  ) );

		if (_validate( $template ) == false) {
			return 0;
		}

		$ok = $policy->update(  );

		if ($ok == false) {
			$plCode = $template->get( 'plCode' );
			$template->setPolicy( $plCode );
			$template->setMessage( 'Sorry...Someone else has amended this policy. You will need to re-enter any changes you made' );
			return 2;
		}

		$template->setAll( $policy->getAllForHTML(  ) );
		$template->set( 'message', 'policy accounting details updated' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return 1;
	}

	function _validate($template) {
		global $userCode;

		if (DO_VALIDATION == false) {
			return true;
		}

		$trans = &$template->getTrans(  );

		$trans->recalculateAccountingFields(  );
		$template->setAll( $trans->getAllForHTML(  ) );
		$ptGross = $trans->get( 'ptGross' );
		$ptCommission = $trans->get( 'ptCommission' );
		$ptInsCo = $trans->get( 'ptInsCo' );
		$ptClientSubTotal = $trans->get( 'ptClientSubTotal' );
		$ptBrokerSubTotal = $trans->get( 'ptBrokerSubTotal' );
		$ptInsCoTotal = $trans->get( 'ptInsCoTotal' );
		$ptAddOnTotal = $trans->get( 'ptAddOnTotal' );
		$ptInsCo = $trans->get( 'ptInsCo' );
		$ptAltInsCo = $trans->get( 'ptAltInsCo' );
		$ptIntroducer = $trans->get( 'ptIntroducer' );
		$ptIntroducerComm = $trans->get( 'ptIntroducerComm' );
		$ptAddlCoverDesc = trim( $trans->get( 'ptAddlCoverDesc' ) );
		$ptAddlGrossIncIPT = $trans->get( 'ptAddlGrossIncIPT' );
		$ptHandler = $trans->get( 'ptHandler' );
		$ptTransType = $trans->get( 'ptTransType' );
		$tot = $ptBrokerSubTotal + $ptInsCoTotal + $ptAddOnTotal;
		$tot = uroundmoney( $tot );

		if ($tot != $ptClientSubTotal) {
			$template->setMessage( 'System Error: incorrect totals.... sorry you cannot post this.' );
			return false;
		}


		if ($ptHandler <= 0) {
			trigger_error( 'no handler when posting', E_USER_WARNING );
			$template->setMessage( 'System Error: no handler specified.' );
			return false;
		}


		if (( $ptGross != 0 || $ptCommission != 0 )) {
			if ($ptInsCo < 1) {
				$template->setMessage( 'No main insurance company has been specified' );
				return false;
			}
		}


		if (( $ptAddOnTotal != 0 && $ptAltInsCo < 1 )) {
			$template->setMessage( 'No add on insurance company has been specified' );
			return false;
		}


		if (( $ptIntroducerComm != 0 && $ptIntroducer < 1 )) {
			$template->setMessage( 'No introducer has been specified' );
			return false;
		}


		if ($ptGross < $ptCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$ptAddlGross = $trans->get( 'ptAddlGross' );
		$ptAddlCommission = $trans->get( 'ptAddlCommission' );

		if ($ptAddlGross < $ptAddlCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$ptAddOnGross = $trans->get( 'ptAddOnGross' );
		$ptAddOnCommission = $trans->get( 'ptAddOnCommission' );

		if ($ptAddOnGross < $ptAddOnCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$ptEngineeringFeeDesc = $trans->get( 'ptEngineeringFeeDesc' );
		$ptEngineeringFee = $trans->get( 'ptEngineeringFee' );
		$ptEngineeringFeeComm = $trans->get( 'ptEngineeringFeeComm' );

		if ($ptEngineeringFee < $ptEngineeringFeeComm) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}


		if (( strlen( trim( $ptEngineeringFeeDesc ) ) == 0 && $ptEngineeringFee != 0 )) {
			$template->setMessage( 'Insurance company fee needs a description' );
			return false;
		}

		$maxCover = 600;
		$cover = trim( $trans->get( 'ptCoverDesc' ) );

		if ($maxCover < strlen( $cover )) {
			$template->setMessage( '' . 'Cover description needs to be restricted to ' . $maxCover . ' characters.' );
			return false;
		}


		if (strlen( $cover ) == 0) {
			$template->setMessage( 'Cover description needs to be entered.' );
			return false;
		}


		if (( strlen( $ptAddlCoverDesc ) == 0 && $ptAddlGrossIncIPT )) {
			$template->setMessage( 'additional premium needs cover description' );
			return false;
		}

		$maxDesc = 70;
		$desc = trim( $trans->get( 'ptTransDesc' ) );

		if ($maxDesc < strlen( $desc )) {
			$template->setMessage( '' . 'Transaction description needs to be restricted to ' . $maxDesc . ' characters.' );
			return false;
		}


		if (strlen( $desc ) == 0) {
			$template->setMessage( 'You need to enter a transaction description.' );
			return false;
		}

		$ptPostingDate = trim( $trans->get( 'ptPostingDate' ) );
		$ptEffectiveFrom = trim( $trans->get( 'ptEffectiveFrom' ) );
		$ptEffectiveTo = trim( $trans->get( 'ptEffectiveTo' ) );

		if (( ( $ptPostingDate == null || $ptPostingDate == '0000-00-00' ) || $ptPostingDate == '' )) {
			$template->setMessage( 'you need to enter a posting date' );
			return false;
		}

		$ppt = $template->get( 'processPeriodType' );

		if ($ppt == '') {
			$ppt = 'C';
		}


		if ($ppt != 'N') {
			if (fisdateinthisaccountingperiod( $ptPostingDate ) == false) {
				$template->setMessage( 'posting date not in the current accounting period' );
				return false;
			}
		} 
else {
			if (( defined( 'USER_FOR_YEAR_END' ) && $userCode == USER_FOR_YEAR_END )) {
				$template->setMessage( 'this user is only allowed to post to the current accounting period' );
				return false;
			}

			$ourSys = new System( 1 );
			$ourSys->incrementPeriod( true );
			$nextFrom = $ourSys->getPeriodFrom(  );
			$nextTo = $ourSys->getPeriodTo(  );

			if (( $ptPostingDate < $nextFrom || $nextTo < $ptPostingDate )) {
				$template->setMessage( 'posting date not in the next accounting period' );
				return false;
			}
		}


		if (( ( $ptEffectiveFrom == null || $ptEffectiveFrom == '0000-00-00' ) || $ptEffectiveFrom == '' )) {
			$template->setMessage( 'you need to enter an effective from date' );
			return false;
		}


		if (( ( $ptEffectiveTo == null || $ptEffectiveTo == '0000-00-00' ) || $ptEffectiveTo == '' )) {
			$template->setMessage( 'you need to enter an effective to date' );
			return false;
		}


		if ($ptEffectiveTo < $ptEffectiveFrom) {
			$template->setMessage( 'you can\'t have an effective to date prior to effective from' );
			return false;
		}


		if (( $ptTransType == 2 || $ptTransType == 3 )) {
			$policy = $template->getPolicy(  );
			$plRenewalDate = $policy->get( 'plRenewalDate' );

			if (( ( $plRenewalDate != null && $plRenewalDate != '0000-00-00' ) && $plRenewalDate < $ptEffectiveTo )) {
				$template->setMessage( 'the effective to date cant be beyond the renewal date' );
				return false;
			}
		}

		return true;
	}

	function _viewclientdetails($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clCode = $template->get( 'ptClient' );

		if ($clCode < 1) {
			$template->setMessage( 'No client to view' );
			return false;
		}

		$ptCode = $template->get( 'ptCode' );
		$ret = '' . '../policies/policyTransEdit.php?transToView=' . $ptCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/clientEdit.php?amendClient=' . $clCode );
	}

	require( '../include/startup.php' );
	$policyTransEditTemplate = &$session->get( 'policyTransEditTemplate' );

	if ($policyTransEditTemplate == null) {
		$policyTransEditTemplate = new PolicyTransEditTemplate( null );
		$policyTransEditTemplate->setProcess( '_goBack', 'back' );
		$policyTransEditTemplate->setProcess( '_doCancel', 'cancel' );
		$policyTransEditTemplate->setProcess( '_saveTransaction', 'save' );
		$policyTransEditTemplate->setProcess( '_postTransaction', 'post' );
		$policyTransEditTemplate->setProcess( '_deleteTransaction', 'delete' );
		$policyTransEditTemplate->setProcess( '_createTransaction', 'create' );
		$policyTransEditTemplate->setProcess( '_viewTransaction', 'transToView' );
		$policyTransEditTemplate->setProcess( '_viewDocument', 'viewDocm' );
		$policyTransEditTemplate->setProcess( '_transDocuments', 'transDocms' );
		$policyTransEditTemplate->setProcess( '_viewClientDetails', 'viewClient' );
		$policyTransEditTemplate->setProcess( '_previewDocument', 'previewDocm' );
		$policyTransEditTemplate->set( 'processPeriodType', 'C' );
	}


	if (isset( $_GET['trans'] )) {
		$ptCode = $_GET['trans'];
		$policyTransEditTemplate->setTransaction( $ptCode );
	}


	if (isset( $_GET['policy'] )) {
		$plCode = $_GET['policy'];
		$policyTransEditTemplate->setPolicy( $plCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$policyTransEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['refresh'] )) {
		$ptCode = $policyTransEditTemplate->get( 'ptCode' );
		$policyTransEditTemplate->setTransaction( $ptCode );
	}

	$session->set( 'policyTransEditTemplate', $policyTransEditTemplate );
	$policyTransEditTemplate->process(  );
	$session->set( 'policyTransEditTemplate', $policyTransEditTemplate );
?>