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

	function _topolicytran($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ptCode = $input['tranNo'];

		if ($ptCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '' . '../clients/clientAccountEnquiry.php?client=' . $clCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		flocationheader( $url );
	}

	function _toclienttran($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ctCode = $input['clientTran'];

		if ($ctCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '' . '../clients/clientAccountEnquiry.php?client=' . $clCode;
		$session->set( 'returnTo', $ret );
		$ct = new ClientTransaction( $ctCode );
		$ctTransType = $ct->get( 'ctTransType' );

		if ($ctTransType == 'J') {
			$ctJournal = $ct->get( 'ctJournal' );

			if ($ctJournal <= 0) {
				return false;
			}

			flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $ctJournal );
			return null;
		}

		flocationheader( '' . '../clients/cashReceiptsEdit.php?view=' . $ctCode );
	}

	function _tocashreceipts($template, $input) {
		global $session;

		$ctCode = $input['allocateCash'];

		if ($ctCode <= 0) {
			return false;
		}


		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ret = '../clients/clientAccountEnquiry.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/cashReceiptsEdit.php?allocateCash=' . $ctCode );
		return false;
	}

	function _topayclient($template, $input) {
		global $session;

		$clCode = $input['clCode'];

		if ($clCode <= 0) {
			return false;
		}

		$client = new Client( null );
		$found = $client->tryGettingRecord( $clCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this client has been deleted' );
			return false;
		}

		$ret = '../clients/clientAccountEnquiry.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/cashPaymentEdit.php?payClient=' . $clCode );
	}

	function _showallocations($template, $input) {
		$item = $input['allocationItem'];

		if ($item <= 0) {
			return false;
		}

		$template->setViewAllocation( $item );
		return false;
	}

	function handlecashpaid($template, $input) {
		$ctCode = 0;
		reset( $input );

		if ($elem = each( $input )) {
			$key = $elem['key'];
			$value = $elem['value'];

			if (strcmp( 'update-', substr( $key, 0, 7 ) ) != 0) {
				continue;
			}

			$ctCode = substr( $key, 7 );
			break;
		}


		if ($ctCode == 0) {
			return false;
		}

		$amt = $input['' . 'toPay-' . $ctCode];
		$amt = uconvertmoneytointeger( $amt );
		$date = $input['' . 'payDate-' . $ctCode];
		$date = umakesqldate2( $date );

		if ($amt == 0) {
			$template->setMessage( 'no amount has been entered' );
			return false;
		}


		if ($date == null) {
			$template->setMessage( 'no date has been entered' );
			return false;
		}

		$ct = new ClientTransaction( $ctCode );
		$ctOriginal = $ct->get( 'ctOriginal' );
		$ctPaid = $ct->get( 'ctPaid' );
		$ctBalance = $ct->get( 'ctBalance' );
		$ctPaid += $amt;
		$ct->set( 'ctPaid', $ctPaid );
		$ct->recalcTotals(  );
		$ct->set( 'ctPaidDate', $date );
		$ct->update(  );
		$template->setMessage( 'paid updated' );
		return false;
	}

	function _doupdate($template, $input) {
		return false;
	}

	function _createstatement($template, $input) {
		global $session;

		$clCode = $template->get( 'clCode' );
		$client = new Client( $clCode );
		$doCode = $client->createStatement(  );
		$ret = 'clientAccountEnquiry.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'clientDocms.php?client=' . $clCode . '&clientDocument=' . $doCode );
		return false;
	}

	require( '../include/startup.php' );
	$clientAccountEnquiryTemplate = &$session->get( 'clientAccountEnquiryTemplate' );

	if ($clientAccountEnquiryTemplate == null) {
		$clientAccountEnquiryTemplate = new ClientAccountEnquiryTemplate( 'clientAccountEnquiry.html' );
		$clientAccountEnquiryTemplate->setProcess( '_doUpdate', 'update' );
		$clientAccountEnquiryTemplate->setProcess( '_doCancel', 'cancel' );
		$clientAccountEnquiryTemplate->setProcess( '_goBack', 'back' );
		$clientAccountEnquiryTemplate->setProcess( '_toPolicyTran', 'tranNo' );
		$clientAccountEnquiryTemplate->setProcess( '_toClientTran', 'clientTran' );
		$clientAccountEnquiryTemplate->setProcess( '_showAllocations', 'allocationItem' );
		$clientAccountEnquiryTemplate->setProcess( '_toCashReceipts', 'allocateCash' );
		$clientAccountEnquiryTemplate->setProcess( '_toPayClient', 'payClient' );
		$clientAccountEnquiryTemplate->setProcess( '_createStatement', 'createStatement' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$clientAccountEnquiryTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['client'] )) {
		$clCode = $_GET['client'];
		$clientAccountEnquiryTemplate->setCanAmend( false );
		$clientAccountEnquiryTemplate->setClient( $clCode );
	}


	if (isset( $_GET['clientAccount'] )) {
		$clCode = $_GET['clientAccount'];
		$clientAccountEnquiryTemplate->setCanAmend( true );
		$clientAccountEnquiryTemplate->setClient( $clCode );
	}


	if (isset( $_GET['refresh'] )) {
		$clCode = $clientAccountEnquiryTemplate->get( 'clCode' );
		$clientAccountEnquiryTemplate->setClient( $clCode );
	}

	$session->set( 'clientAccountEnquiryTemplate', $clientAccountEnquiryTemplate );
	$clientAccountEnquiryTemplate->process(  );
	$session->set( 'clientAccountEnquiryTemplate', $clientAccountEnquiryTemplate );
?>