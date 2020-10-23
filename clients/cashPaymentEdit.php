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
		$template->set( 'message', 'payment cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _dopost($template, $input) {
		$messg = $template->doPosting( $input );

		if ($messg != null) {
			$template->set( 'message', $messg );
			return false;
		}

		$template->set( 'message', 'payment posted' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->setAllowAllocation( true );
		return false;
	}

	function _tocashreceipts($template, $input) {
		global $session;

		$ctCode = $input['ctCode'];

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

	function _setclient($template, $input) {
		$clCode = $input['payClient'];

		if ($clCode < 1) {
			return false;
		}

		$template->setClient( $clCode );
		return false;
	}

	require( '../include/startup.php' );
	$cashPaymentEditTemplate = &$session->get( 'cashPaymentEditTemplate' );

	if ($cashPaymentEditTemplate == null) {
		$cashPaymentEditTemplate = new CashPaymentEditTemplate( 'cashPaymentEdit.html' );
		$cashPaymentEditTemplate->setProcess( '_setClient', 'payClient' );
		$cashPaymentEditTemplate->setProcess( '_doPost', 'post' );
		$cashPaymentEditTemplate->setProcess( '_doCancel', 'cancel' );
		$cashPaymentEditTemplate->setProcess( '_goBack', 'back' );
		$cashPaymentEditTemplate->setProcess( '_toCashReceipts', 'allocateCashPaid' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$cashPaymentEditTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'cashPaymentEditTemplate', $cashPaymentEditTemplate );
	$cashPaymentEditTemplate->recalcTotals(  );
	$cashPaymentEditTemplate->process(  );
	$session->set( 'cashPaymentEditTemplate', $cashPaymentEditTemplate );
?>