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

	function _goBack($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		fLocationHeader( $url );
	}

	/**
*	Cancel gets back version from database
*/
	function _doCancel($template, $input) {
		$template->set( 'message', 'entries cancelled' );
		$template->clearAllDisplayFields(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	/**
*	reset - leave in edit mode
*/
	function _doReset($template, $input) {
		$template->set( 'message', 'entries re-set' );
		$template->clearAll(  );
		return false;
	}

	/**
*	Cancel gets back version from database
*/
	function _doPost($template, $input) {
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

	/**
 * When a specific ic payment (from) is to be reconciled, this function gets called
 * with the ic trans code - comes from cash batch edit
 *
 */
	function _setTrans($template, $input) {
		$itCode = $input['reconcile'];

		if ($itCode <= 0) {
			return false;
		}

		$template->clearAll(  );
		$template->setTransToReconcile( $itCode );
		return false;
	}

	/**
 * Just to view a prev recon without amending
 *
 */
	function _showTrans($template, $input) {
		$itCode = $input['view'];

		if ($itCode <= 0) {
			return false;
		}

		$template->setTransToView( $itCode );
		return false;
	}

	function _toPolicyTran($template, $input) {
		global $session;

		$ptCode = $input['transToView'];

		if ($ptCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '../inscos/inscoRecon.php';
		$session->set( 'returnTo', $ret );
		fLocationHeader(  . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		fLocationHeader( $url );
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


	if (( empty( $$returnTo ) && $returnTo != null )) {
		$inscoReconTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['insco'] )) {
		$icCode = $_GET['insco'];
		$inscoReconTemplate->setInsco( $icCode );
	}

	trigger_error( 'test', 1024 );
	$session->set( 'inscoReconTemplate', $inscoReconTemplate );
	$inscoReconTemplate->process(  );
	$session->set( 'inscoReconTemplate', $inscoReconTemplate );
?>