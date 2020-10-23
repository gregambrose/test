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

		if ($ok == 2) {
			$plCode = $template->get( 'plCode' );
			$template->setPolicy( $plCode );
			$template->setMessage( 'Sorry...Someone else has amended this policy. You will need to re-enter any changes you made' );
			return false;
		}


		if ($ok != 1) {
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$plCode = $template->get( 'plCode' );
		$template->setPolicy( $plCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
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
		$policy = &$template->getPolicy(  );

		if ($policy->recordExists(  ) == false) {
			trigger_error( 'cant get policy', E_USER_ERROR );
		}

		$plGross = $policy->get( 'plGross' );
		$plCommission = $policy->get( 'plCommission' );

		if ($plGross < $plCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$plAddlGross = $policy->get( 'plAddlGross' );
		$plAddlCommission = $policy->get( 'plAddlCommission' );

		if ($plAddlGross < $plAddlCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$plAddOnGross = $policy->get( 'plAddOnGross' );
		$plAddOnCommission = $policy->get( 'plAddOnCommission' );

		if ($plAddOnGross < $plAddOnCommission) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		$plEngineeringFee = $policy->get( 'plEngineeringFee' );
		$plEngineeringFeeComm = $policy->get( 'plEngineeringFeeComm' );

		if ($plEngineeringFee < $plEngineeringFeeComm) {
			$template->setMessage( 'Commission is set to more than gross premium' );
			return false;
		}

		return true;
	}

	function _viewtrans($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$plCode = $template->get( 'plCode' );
		$ret = '' . '../policies/policyAccountEdit.php?amendPolicy=' . $plCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'policyTransEdit.php?policy=' . $plCode );
	}

	require( '../include/startup.php' );
	$policyAccountEditTemplate = &$session->get( 'policyAccountEditTemplate' );

	if ($policyAccountEditTemplate == null) {
		$policyAccountEditTemplate = new PolicyAccountEditTemplate( null );
		$policyAccountEditTemplate->setProcess( '_goBack', 'back' );
		$policyAccountEditTemplate->setProcess( '_doCancel', 'cancel' );
		$policyAccountEditTemplate->setProcess( '_savePolicy', 'update' );
		$policyAccountEditTemplate->setProcess( '_viewTrans', 'trans' );
	}


	if (isset( $_GET['amendPolicy'] )) {
		$plCode = $_GET['amendPolicy'];
		$policyAccountEditTemplate->setPolicy( $plCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$policyAccountEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['refresh'] )) {
		$plCode = $policyAccountEditTemplate->get( 'plCode' );
		$policyAccountEditTemplate->setPolicy( $plCode );
	}

	$session->set( 'policyAccountEditTemplate', $policyAccountEditTemplate );
	$policyAccountEditTemplate->process(  );
	$session->set( 'policyAccountEditTemplate', $policyAccountEditTemplate );
?>