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

	function _docancel($template, $input) {
		$template->set( 'message', 'entries cancelled' );
		$template->clearAllDisplayFields(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _doreset($template, $input) {
		$template->set( 'message', 'entries re-set' );
		$template->clearAll(  );
		return false;
	}

	function _dopost($template, $input) {
		$messg = $template->validate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$template->postEntries(  );
		$template->set( 'message', 'entries posted' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _settrans($template, $input) {
		$itCode = $input['reconcile'];

		if ($itCode <= 0) {
			return false;
		}

		$template->clearAll(  );
		$template->setTransToReconcile( $itCode );
		return false;
	}

	function _showtrans($template, $input) {
		$itCode = $input['view'];

		if ($itCode <= 0) {
			return false;
		}

		$template->setTransToView( $itCode );
		return false;
	}

	function _topolicytran($template, $input) {
		global $session;

		$ptCode = $input['transToView'];

		if ($ptCode <= 0) {
			return false;
		}

		$ret = '../inscos/inscoRecon.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
	}

	require( '../include/startup.php' );
	$inscoReconTemplate = &$session->get( 'inscoReconTemplate' );

	if ($inscoReconTemplate == null) {
		$inscoReconTemplate = new InscoReconTemplate( 'inscoRecon.html' );
		$inscoReconTemplate->setProcess( '_goBack', 'back' );
		$inscoReconTemplate->setProcess( 'whenEditRequested', 'icrEdit' );
		$inscoReconTemplate->setProcess( '_toPolicyTran', 'transToView' );
		$inscoReconTemplate->setProcess( '_doPost', 'post' );
		$inscoReconTemplate->setProcess( '_doCancel', 'cancel' );
		$inscoReconTemplate->setProcess( '_doReset', 'reset' );
		$inscoReconTemplate->setProcess( '_setTrans', 'reconcile' );
		$inscoReconTemplate->setProcess( '_showTrans', 'view' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$inscoReconTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['insco'] )) {
		$icCode = $_GET['insco'];
		$inscoReconTemplate->setInsco( $icCode );
		unset( $inscoReconTemplate[transToView] );
		unset( $inscoReconTemplate[transToReconcile] );
	}

	$session->set( 'inscoReconTemplate', $inscoReconTemplate );
	$inscoReconTemplate->process(  );
	$session->set( 'inscoReconTemplate', $inscoReconTemplate );
?>