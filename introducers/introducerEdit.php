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

	function _gotoaccountenquiry($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$inCode = $template->get( 'inCode' );
		$ret = 'introducerEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerAccountEnquiry.php?introducer=' . $inCode );
	}

	function _goback($template, $input) {
		if (_validate( $template ) == false) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		flocationheader( $url );
	}

	function _toinvoiceaddress($template, $input) {
		if (_validate( $template ) == false) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clInvAddress = $template->get( 'clInvAddress' );

		if ($clInvAddress != 0) {
			$template->setMessage( 'current address set as invoice address' );
			return false;
		}


		if (_validate( $template ) == false) {
			return false;
		}

		flocationheader( '' . 'introducerInvoiceAddress.php?amendIntroducer=' . $inCode );
	}

	function _doupdate($template, $input) {
		if (_validate( $template ) == false) {
			return false;
		}

		$ok = _updateintroducer( $template, $input );

		if ($ok == false) {
			$inCode = $template->get( 'inCode' );
			$template->setIntroducer( $inCode );
			$template->setMessage( 'Sorry...Someone else has amended this introducer. You will need to re-enter any changes you made' );
			return false;
		}

		$template->set( 'message', 'introducer updated' );
		return false;
	}

	function _docancel($template, $input) {
		$inCode = $template->get( 'inCode' );
		$template->setIntroducer( $inCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _updateintroducer($template, $input) {
		$introducer = &$template->getIntroducer(  );

		if ($introducer->recordExists(  ) == false) {
			trigger_error( 'cant get introducer', E_USER_ERROR );
		}

		$introducer->setAll( $input );
		$ok = $introducer->update(  );

		if ($ok == false) {
			$inCode = $template->get( 'inCode' );
			$template->setIntroducer( $inCode );
			$introducer->refresh(  );
			$template->setAll( $introducer->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this introducer. You will need to re-enter any changes you made' );
			return false;
		}

		$introducer->fetchExtraColumns(  );
		$template->setAll( $introducer->getAllForHTML(  ) );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return $ok;
	}

	function _dodelete($template, $input) {
		$inCode = $input['inCode'];
		$introducer = &$template->getIntroducer(  );

		$q = '' . 'SELECT clCode FROM clients  WHERE clIntroducer = \'' . $inCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this introducer as it is used on more clients' );
			return false;
		}


		if ($introducer->recordExists(  ) == false) {
			trigger_error( '' . 'cant get introducer ' . $inCode, E_USER_ERROR );
		}

		$ok = $introducer->delete(  );

		if ($ok == false) {
			$inCode = $template->get( 'inCode' );
			$template->setIntroducer( $inCode );
			$introducer->refresh(  );
			$template->setAll( $introducer->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this introducer. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->setMessage( 'introducer deleted' );
		flocationheader( 'introducers.php' );
		exit(  );
	}

	function _gotointroducernotes($template, $input) {
		global $session;

		if (_validate( $template ) == false) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$inCode = $template->get( 'inCode' );
		$ret = 'introducerEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerNotes.php?introducer=' . $inCode );
	}

	function _gotointroducerdocuments($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$inCode = $template->get( 'inCode' );
		$ret = 'introducerEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerDocms.php?introducer=' . $inCode );
	}

	function _validate($template) {
		$inTerms = $template->get( 'inTerms' );
		$inRate = $template->get( 'inRate' );
		$inFlatRate = $template->get( 'inFlatRate' );
		$inApplyToAll = $template->get( 'inApplyToAll' );

		if (( $inTerms != 'F' && $inTerms != 'R' )) {
			$template->setMessage( 'you must specify whether it if a rate or an agreed flat rate is to apply' );
			return false;
		}


		if (( $inTerms == 'F' && $inRate != 0 )) {
			$template->setMessage( 'you have specified flat rate but included a % rate' );
			return false;
		}


		if (( $inTerms == 'R' && $inFlatRate != 0 )) {
			$template->setMessage( 'you have specified % rate but included a flat rate' );
			return false;
		}


		if ($inApplyToAll == 0) {
			$template->setMessage( 'you must specify whether commission applies to all clients and policies' );
			return false;
		}

		return true;
	}

	function _tocashbatch($template, $input) {
		$inCode = $template->get( 'inCode' );
		flocationheader( '' . '../batches/cashBatchEdit.php?setIntroducer=' . $inCode );
	}

	function _tojournal($template, $input) {
		$inCode = $template->get( 'inCode' );
		flocationheader( '' . '../accounts/journalEdit.php?setIntroducer=' . $inCode );
	}

	function _toreconcile($template, $input) {
		global $session;

		$inCode = $template->get( 'inCode' );
		$ret = 'introducerEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../introducers/introducerRecon.php?introd=' . $inCode );
	}

	function _createstatement($template, $input) {
		global $session;

		$inCode = $template->get( 'inCode' );
		$introducer = new Introducer( $inCode );
		$doCode = $introducer->createStatement(  );
		$ret = 'introducerEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerDocms.php?introducer=' . $inCode . '&introducerDocument=' . $doCode );
		return false;
	}

	require( '../include/startup.php' );
	$introducerEditTemplate = &$session->get( 'introducerEditTemplate' );

	if ($introducerEditTemplate == null) {
		$introducerEditTemplate = new IntroducerEditTemplate( 'introducerEdit.html' );
		$introducerEditTemplate->setProcess( '_doUpdate', 'update' );
		$introducerEditTemplate->setProcess( '_doDelete', 'delete' );
		$introducerEditTemplate->setProcess( '_doCancel', 'cancel' );
		$introducerEditTemplate->setProcess( '_goBack', 'back' );
		$introducerEditTemplate->setProcess( '_goToPolicy', 'selectPolicy' );
		$introducerEditTemplate->setProcess( '_goToAccountEnquiry', 'accEnquiry' );
		$introducerEditTemplate->setProcess( '_newPolicy', 'newPolicy' );
		$introducerEditTemplate->setProcess( '_toRetail', 'toRetail' );
		$introducerEditTemplate->setProcess( '_toCommercial', 'toCommercial' );
		$introducerEditTemplate->setProcess( '_goToIntroducerNotes', 'introducerNotes' );
		$introducerEditTemplate->setProcess( '_goToIntroducerDocuments', 'introducerDocms' );
		$introducerEditTemplate->setProcess( '_toInvoiceAddress', 'invAddress' );
		$introducerEditTemplate->setProcess( '_toReconcile', 'reconcile' );
		$introducerEditTemplate->setProcess( '_createStatement', 'createStatement' );
		$introducerEditTemplate->setProcess( '_toCashBatch', 'toCashBatch' );
		$introducerEditTemplate->setProcess( '_toJournal', 'toJournal' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$introducerEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['amendIntroducer'] )) {
		$inCode = $_GET['amendIntroducer'];
		$introducerEditTemplate->setIntroducer( $inCode );
	}


	if (isset( $_GET['editIntroducer'] )) {
		$inCode = $_GET['editIntroducer'];
		$introducerEditTemplate->setAndEditIntroducer( $inCode );
	}


	if (isset( $_GET['refresh'] )) {
		$inCode = $introducerEditTemplate->get( 'inCode' );
		$introducerEditTemplate->setIntroducer( $inCode );
	}

	$session->set( 'introducerEditTemplate', $introducerEditTemplate );
	$introducerEditTemplate->process(  );
	$session->set( 'introducerEditTemplate', $introducerEditTemplate );
?>