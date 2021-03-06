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

		$clCode = $template->get( 'clCode' );
		$ret = '../introducers/introducerRecon.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		flocationheader( $url );
	}

	require( '../include/startup.php' );
	$introducerReconTemplate = &$session->get( 'introducerReconTemplate' );

	if ($introducerReconTemplate == null) {
		$introducerReconTemplate = new IntroducerReconTemplate( 'introducerRecon.html' );
		$introducerReconTemplate->setProcess( '_goBack', 'back' );
		$introducerReconTemplate->setProcess( 'whenEditRequested', 'icrEdit' );
		$introducerReconTemplate->setProcess( '_toPolicyTran', 'transToView' );
		$introducerReconTemplate->setProcess( '_doPost', 'post' );
		$introducerReconTemplate->setProcess( '_doCancel', 'cancel' );
		$introducerReconTemplate->setProcess( '_doReset', 'reset' );
		$introducerReconTemplate->setProcess( '_setTrans', 'reconcile' );
		$introducerReconTemplate->setProcess( '_showTrans', 'view' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$introducerReconTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['introd'] )) {
		$inCode = $_GET['introd'];
		$introducerReconTemplate->setIntroducer( $inCode );
		unset( $introducerReconTemplate[transToView] );
		unset( $introducerReconTemplate[transToReconcile] );
	}

	$session->set( 'introducerReconTemplate', $introducerReconTemplate );
	$introducerReconTemplate->process(  );
	$session->set( 'introducerReconTemplate', $introducerReconTemplate );
?>