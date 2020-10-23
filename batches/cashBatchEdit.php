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

	function _newbatch($template, $input) {
		$batch = new CashBatch( null );
		$batch->set( 'btLocked', false );
		$batch->insert( null );
		$btCode = $batch->getKeyValue(  );
		$template->clearAll(  );
		$template->setBatch( $btCode );
		$template->setAllowEditing( true );
		$template->setAllowExiting( false );
		return false;
	}

	function _amendbatch($template, $input) {
		$btCode = $input['batch'];
		$template->setBatch( $btCode );
		return false;
	}

	function _printbatch($template, $input) {
		$template->printPayinSlip(  );
		return true;
	}

	function _newitem($template, $input) {
		$type = $input['newItemType'];

		if (( ( $type != 'C' && $type != 'I' ) && $type != 'N' )) {
			$template->setMessage( 'you need to select a payee type' );
			return false;
		}

		$template->batch->setAll( $input );
		$template->newItem( $type );
		return false;
	}

	function _accessitem($template, $input) {
		$item = $input['item'];

		if ($item < 0) {
			return '';
		}

		$template->setItem( $item );
		$template->setMessage( 'item selected' );
		return false;
	}

	function _saveitem($template, $input) {
		if (isset( $template->item ) != false) {
			$template->updateItem( $input );
		}

		$batch = &$template->getBatch(  );

		if ($batch->recordExists(  ) == false) {
			trigger_error( 'cant get batch', E_USER_ERROR );
		}

		$batch->setAll( $input );
		$batch->recalcAllocated(  );
		$template->setAll( $batch->getAllForHTML(  ) );
		return false;
	}

	function _cancelitem($template, $input) {
		$template->cancelItem(  );
		$template->setMessage( 'item amendment cancelled' );
		return false;
	}

	function _deleteitem($template, $input) {
		$template->deleteItem(  );
		$template->setMessage( 'item deleted' );
		return false;
	}

	function _allocateitem($template, $input) {
		global $session;

		$key = $input['allocateItem'];

		if ($key < 0) {
			return '';
		}

		$batch = &$template->getBatch(  );

		if ($batch->get( 'btLocked' ) != 1) {
			trigger_error( 'unposted batch', E_USER_ERROR );
		}

		$item = &$template->items[$key];

		$item->refresh(  );
		$ret = '../batches/cashBatchEdit.php';
		$session->set( 'returnTo', $ret );
		$biCode = $item->get( 'biCode' );
		$biPayeeType = $item->get( 'biPayeeType' );

		if ($biPayeeType == 'C') {
			flocationheader( '' . '../clients/cashReceiptsEdit.php?allocate=' . $biCode );
		}


		if ($biPayeeType == 'I') {
			$itCode = $item->get( 'biTrans' );
			flocationheader( '' . '../inscos/inscoRecon.php?reconcile=' . $itCode );
		}


		if ($biPayeeType == 'N') {
			$rtCode = $item->get( 'biTrans' );
			flocationheader( '' . '../introducers/introducerRecon.php?reconcile=' . $rtCode );
		}

		return false;
	}

	function _chooseclient($template, $input) {
		global $session;
		$batch = &$template->getBatch(  );

		if ($batch->recordExists(  ) == false) {
			trigger_error( 'cant get batch', E_USER_ERROR );
		}

		$batch->setAll( $input );
		$batch->recalcAllocated(  );
		$btCode = $batch->getKeyValue(  );
		$template->_calcTotals(  );
		$ok = $batch->update(  );
		$template->updateItem( $input );
		$ret = '../batches/cashBatchEdit.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '../clients/clients.php' );
		return false;
	}

	function _setclient($template, $input) {
		$clCode = $input['setClient'];

		if ($clCode <= 0) {
			return false;
		}

		$template->set( 'biClient', $clCode );

		if (isset( $template->item )) {
			$template->item->set( 'biClient', $clCode );
		}

		return false;
	}

	function _chooseinsco($template, $input) {
		global $session;

		$template->updateItem( $input );
		$ret = '../batches/cashBatchEdit.php';
		$session->set( 'returnTo', $ret );
		$btCode = $template->getBatchCode(  );
		$session->set( 'cashBatchEditing', $btCode );
		flocationheader( '../inscos/inscos.php' );
		return false;
	}

	function _setinsco($template, $input) {
		$icCode = $input['setInsco'];

		if ($icCode <= 0) {
			return false;
		}

		$template->set( 'biInsco', $icCode );

		if (isset( $template->item )) {
			$template->item->set( 'biInsco', $icCode );
		}

		return false;
	}

	function _chooseintroducer($template, $input) {
		global $session;

		$template->updateItem( $input );
		$ret = '../batches/cashBatchEdit.php';
		$session->set( 'returnTo', $ret );
		$btCode = $template->getBatchCode(  );
		$session->set( 'cashBatchEditing', $btCode );
		flocationheader( '../introducers/introducers.php' );
		return false;
	}

	function _setintroducer($template, $input) {
		$inCode = $input['setIntroducer'];

		if ($inCode <= 0) {
			return false;
		}

		$template->set( 'biIntroducer', $inCode );

		if (isset( $template->item )) {
			$template->item->set( 'biIntroducer', $inCode );
		}

		return false;
	}

	function _docancel($template, $input) {
		$btCode = $template->get( 'btCode' );
		$template->setBatch( $btCode );
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

	function _savebatchanditem($template, $input) {
		$messg = $template->checkBatchDate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$messg = $template->checkPaymentTypesAndAmounts(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}


		if (isset( $template->item ) != false) {
			$template->updateItem( $input );
		}

		$ok = _doupdate( $template, $input );

		if ($ok == false) {
			return false;
		}

		return false;
	}

	function _postbatch($template, $input) {
		$messg = $template->checkBatchDate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$messg = $template->checkPaymentTypesAndAmounts(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$batch = &$template->getBatch(  );

		if ($batch->get( 'btLocked' ) == 1) {
			return false;
		}


		if (isset( $template->item ) != false) {
			$template->updateItem( $input );
		}

		$ok = _doupdate( $template, $input );

		if ($ok == false) {
			return false;
		}

		$btRemaining = $batch->get( 'btRemaining' );

		if ($btRemaining != 0) {
			$amt = $batch->getForHTML( 'btRemaining' );
			$template->setMessage( '' . 'Batch can\'t be posted as an amount of ' . $amt . ' is still to be entered' );
			return false;
		}


		if ($messg = $template->haveAllItemsGotPayeesEtc(  ) != null) {
			$template->setMessage( $messg );
			return false;
		}

		$batch->postAndLockBatch(  );
		$template->clearItem(  );
		$template->setMessage( 'Batch posted' );
		return false;
	}

	function _doupdate($template, $input) {
		$batch = &$template->getBatch(  );

		if ($batch->recordExists(  ) == false) {
			trigger_error( 'cant get batch', E_USER_ERROR );
		}

		$batch->setAll( $input );
		$batch->recalcAllocated(  );
		$btCode = $batch->getKeyValue(  );
		$template->_calcTotals(  );
		$ok = $batch->update(  );

		if ($ok == false) {
			$plCode = $template->get( 'plCode' );
			$template->setPolicy( $plCode );
			$template->setMessage( 'Sorry...Someone else has amended this batch. You will need to re-enter any changes you made' );
			return false;
		}

		$template->saveItems(  );
		$batch->recalcAllocated(  );
		$batch->update(  );
		$template->setAll( $batch->getAllForHTML(  ) );
		$template->set( 'message', 'batch saved' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	require( '../include/startup.php' );
	$cashBatchEditTemplate = &$session->get( 'cashBatchEditTemplate' );

	if ($cashBatchEditTemplate == null) {
		$cashBatchEditTemplate = new CashBatchEditTemplate( 'cashBatchEdit.html' );
		$cashBatchEditTemplate->setProcess( '_goBack', 'back' );
		$cashBatchEditTemplate->setProcess( '_newBatch', 'newBatch' );
		$cashBatchEditTemplate->setProcess( '_amendBatch', 'batch' );
		$cashBatchEditTemplate->setProcess( '_doCancel', 'cancel' );
		$cashBatchEditTemplate->setProcess( '_saveBatchAndItem', 'save' );
		$cashBatchEditTemplate->setProcess( '_postBatch', 'post' );
		$cashBatchEditTemplate->setProcess( '_printBatch', 'print' );
		$cashBatchEditTemplate->setProcess( '_accessItem', 'item' );
		$cashBatchEditTemplate->setProcess( '_newItem', 'newItem' );
		$cashBatchEditTemplate->setProcess( '_saveItem', 'saveItem' );
		$cashBatchEditTemplate->setProcess( '_cancelItem', 'cancelItem' );
		$cashBatchEditTemplate->setProcess( '_deleteItem', 'deleteItem' );
		$cashBatchEditTemplate->setProcess( '_allocateItem', 'allocateItem' );
		$cashBatchEditTemplate->setProcess( '_chooseClient', 'chooseClient' );
		$cashBatchEditTemplate->setProcess( '_setClient', 'setClient' );
		$cashBatchEditTemplate->setProcess( '_chooseInsco', 'chooseInsco' );
		$cashBatchEditTemplate->setProcess( '_setInsco', 'setInsco' );
		$cashBatchEditTemplate->setProcess( '_chooseIntroducer', 'chooseIntroducer' );
		$cashBatchEditTemplate->setProcess( '_setIntroducer', 'setIntroducer' );
		$cashBatchEditTemplate->setReturnTo( '../batches/cashBatchEdit.php' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$cashBatchEditTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'cashBatchEditTemplate', $cashBatchEditTemplate );
	$cashBatchEditTemplate->process(  );
	$session->set( 'cashBatchEditTemplate', $cashBatchEditTemplate );
?>