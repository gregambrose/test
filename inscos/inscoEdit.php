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

		$icCode = $template->get( 'icCode' );
		$ret = 'inscoEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'inscoAccountEnquiry.php?insco=' . $icCode );
	}

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		flocationheader( $url );
	}

	function _toinvoiceaddress($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$clInvAddress = $template->get( 'clInvAddress' );

		if ($clInvAddress != 0) {
			$template->setMessage( 'current address set as invoice address' );
			return false;
		}

		flocationheader( '' . 'inscoInvoiceAddress.php?amendInsco=' . $icCode );
	}

	function _doupdate($template, $input) {
		$ok = _updateinsco( $template, $input );

		if ($ok == false) {
			$icCode = $template->get( 'icCode' );
			$template->setInsco( $icCode );
			$template->setMessage( 'Sorry...Someone else has amended this insurance company. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->set( 'message', 'insurance company updated' );
		return false;
	}

	function _docancel($template, $input) {
		$icCode = $template->get( 'icCode' );
		$template->setInsco( $icCode );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->set( 'message', 'amendments cancelled' );
		return false;
	}

	function _updateinsco($template, $input) {
		$insco = &$template->getInsco(  );

		if ($insco->recordExists(  ) == false) {
			trigger_error( 'cant get insco', E_USER_ERROR );
		}

		$insco->setAll( $input );
		$ok = $insco->update(  );

		if ($ok == false) {
			$icCode = $template->get( 'icCode' );
			$template->setInsco( $icCode );
			$insco->refresh(  );
			$template->setAll( $insco->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this insurance company. You will need to re-enter any changes you made' );
			return false;
		}

		$insco->fetchExtraColumns(  );
		$template->setAll( $insco->getAllForHTML(  ) );
		return $ok;
	}

	function _dodelete($template, $input) {
		$icCode = $input['icCode'];
		$insco = &$template->getInsco(  );

		$q = '' . 'SELECT plCode FROM policies  WHERE plInsco = \'' . $icCode . '\' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this insurance company as it is has one or more policies' );
			return false;
		}

		$q = '' . 'SELECT itCode FROM inscoTransactions WHERE  itInsCo = ' . $icCode . ' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$template->set( 'message', 'You cant delete this insurance company as it is has one or more transactions' );
			return false;
		}


		if ($insco->recordExists(  ) == false) {
			trigger_error( '' . 'cant get insco ' . $icCode, E_USER_ERROR );
		}

		$ok = $insco->delete(  );

		if ($ok == false) {
			$icCode = $template->get( 'icCode' );
			$template->setInsco( $icCode );
			$insco->refresh(  );
			$template->setAll( $insco->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this insurance company. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->setMessage( 'insurance company deleted' );
		flocationheader( 'inscos.php' );
		exit(  );
	}

	function _gotoinsconotes($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$icCode = $template->get( 'icCode' );
		$ret = 'inscoEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'inscoNotes.php?insco=' . $icCode );
	}

	function _gotoinscodocuments($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$icCode = $template->get( 'icCode' );
		$ret = 'inscoEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'inscoDocms.php?insco=' . $icCode );
	}

	function _tocashbatch($template, $input) {
		$icCode = $template->get( 'icCode' );
		flocationheader( '' . '../batches/cashBatchEdit.php?setInsco=' . $icCode );
	}

	function _tojournal($template, $input) {
		$icCode = $template->get( 'icCode' );
		flocationheader( '' . '../accounts/journalEdit.php?setInsco=' . $icCode );
	}

	function _toreconcile($template, $input) {
		global $session;

		$icCode = $template->get( 'icCode' );
		$ret = 'inscoEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoRecon.php?insco=' . $icCode );
	}

	require( '../include/startup.php' );
	$inscoEditTemplate = &$session->get( 'inscoEditTemplate' );

	if ($inscoEditTemplate == null) {
		$inscoEditTemplate = new InscoEditTemplate( 'inscoEdit.html' );
		$inscoEditTemplate->setProcess( '_doUpdate', 'update' );
		$inscoEditTemplate->setProcess( '_doDelete', 'delete' );
		$inscoEditTemplate->setProcess( '_doCancel', 'cancel' );
		$inscoEditTemplate->setProcess( '_goBack', 'back' );
		$inscoEditTemplate->setProcess( '_goToPolicy', 'selectPolicy' );
		$inscoEditTemplate->setProcess( '_newPolicy', 'newPolicy' );
		$inscoEditTemplate->setProcess( '_toRetail', 'toRetail' );
		$inscoEditTemplate->setProcess( '_goToAccountEnquiry', 'accEnquiry' );
		$inscoEditTemplate->setProcess( '_toCommercial', 'toCommercial' );
		$inscoEditTemplate->setProcess( '_goToInscoNotes', 'inscoNotes' );
		$inscoEditTemplate->setProcess( '_goToInscoDocuments', 'inscoDocms' );
		$inscoEditTemplate->setProcess( '_toInvoiceAddress', 'invAddress' );
		$inscoEditTemplate->setProcess( '_toReconcile', 'reconcile' );
		$inscoEditTemplate->setProcess( '_toCashBatch', 'toCashBatch' );
		$inscoEditTemplate->setProcess( '_toJournal', 'toJournal' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$inscoEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['amendInsco'] )) {
		$icCode = $_GET['amendInsco'];
		$inscoEditTemplate->setInsco( $icCode );
	}


	if (isset( $_GET['editInsco'] )) {
		$icCode = $_GET['editInsco'];
		$inscoEditTemplate->setAndEditInsco( $icCode );
	}


	if (isset( $_GET['refresh'] )) {
		$icCode = $inscoEditTemplate->get( 'icCode' );
		$inscoEditTemplate->setInsco( $icCode );
	}

	$session->set( 'inscoEditTemplate', $inscoEditTemplate );
	$inscoEditTemplate->process(  );
	$session->set( 'inscoEditTemplate', $inscoEditTemplate );
?>