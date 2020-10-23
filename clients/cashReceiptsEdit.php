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
		if (isset( $template->item )) {
			$item = &$template->item;

			$biCode = $item->getKeyValue(  );
			$template->setCashBatchItem( $biCode );
		}

		$template->set( 'message', 'amendments cancelled' );
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

		$template->set( 'message', 'cash posted' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _doredisplay($template, $input) {
		$template->doRedisplay( $input );
		return false;
	}

	function _viewtrans($template, $input) {
		$ctCode = $input['view'];
		$template->setClientTransaction( $ctCode );
		return false;
	}

	function _toaccountenquiry($template, $input) {
		global $session;

		$session->set( 'cashReceiptsEditTemplate', $template );
		$ret = '../clients/cashReceiptsEdit.php';
		$session->set( 'returnTo', $ret );
		$clCode = $template->client->get( 'clCode' );
		flocationheader( '' . '../clients/clientAccountEnquiry.php?client=' . $clCode );
	}

	function _tomaindetails($template, $input) {
		global $session;

		$session->set( 'cashReceiptsEditTemplate', $template );
		$ret = '../clients/cashReceiptsEdit.php';
		$session->set( 'returnTo', $ret );
		$clCode = $template->client->get( 'clCode' );
		flocationheader( '' . '../clients/clientEdit.php?amendClient=' . $clCode );
	}

	function _doreceipt($template, $input) {
		$ctCode = $input['receiptItem'];

		if ($ctCode <= 0) {
			return false;
		}

		$ct = new ClientTransaction( $ctCode );
		$ptCode = $ct->get( 'ctPolicyTran' );

		if ($ptCode <= 0) {
			return false;
		}

		$pt = new PolicyTransaction( $ptCode );

		if ($pt->get( 'ptInvoiceNo' ) <= 0) {
			return false;
		}

		$docm = $pt->createAndViewReceiptDocument(  );
		$pt->update(  );
		$docm->viewDocument(  );
		return false;
	}

	function _allocateitem($template, $input) {
		$biCode = $input['allocate'];
		$item = new CashBatchItem( $biCode );
		$biPayeeType = $item->get( 'biPayeeType' );

		if ($biPayeeType != 'C') {
			trigger_error( 'wrong payee type', E_USER_ERROR );
		}

		$template->setCashBatchItem( $biCode );
	}

	function _allocatetransaction($template, $input) {
		$ctCode = $input['allocateCash'];
		$template->setClientTransaction( $ctCode );
	}

	require( '../include/startup.php' );
	$cashReceiptsEditTemplate = &$session->get( 'cashReceiptsEditTemplate' );

	if ($cashReceiptsEditTemplate == null) {
		$cashReceiptsEditTemplate = new CashReceiptsEditTemplate( 'cashReceiptsEdit.html' );
		$cashReceiptsEditTemplate->setProcess( '_allocateItem', 'allocate' );
		$cashReceiptsEditTemplate->setProcess( '_allocateTransaction', 'allocateCash' );
		$cashReceiptsEditTemplate->setProcess( '_doPost', 'post' );
		$cashReceiptsEditTemplate->setProcess( '_doCancel', 'cancel' );
		$cashReceiptsEditTemplate->setProcess( '_goBack', 'back' );
		$cashReceiptsEditTemplate->setProcess( '_doRedisplay', 'redisplay' );
		$cashReceiptsEditTemplate->setProcess( '_viewTrans', 'view' );
		$cashReceiptsEditTemplate->setProcess( '_doReceipt', 'receiptItem' );
		$cashReceiptsEditTemplate->setProcess( '_toCashBatch', 'toCashBatch' );
		$cashReceiptsEditTemplate->setProcess( '_toAccountEnquiry', 'accEnquiry' );
		$cashReceiptsEditTemplate->setProcess( '_toMainDetails', 'mainDetails' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$cashReceiptsEditTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'cashReceiptsEditTemplate', $cashReceiptsEditTemplate );
	$cashReceiptsEditTemplate->recalcTotals(  );
	$cashReceiptsEditTemplate->process(  );
	$session->set( 'cashReceiptsEditTemplate', $cashReceiptsEditTemplate );
?>