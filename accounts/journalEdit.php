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

	function _newjournal($template, $input) {
		$type = $input['newJournal'];
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
		$ret = '../batches/journalEdit.php';
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

		$template->clearAllocated(  );
		$ret = '../accounts/journalEdit.php';
		$session->set( 'returnTo', $ret );
		$session->set( 'journalEditTemplate', $template );
		flocationheader( '../clients/clients.php' );
		return false;
	}

	function _setclient($template, $input) {
		$clCode = $input['setClient'];

		if ($clCode <= 0) {
			return false;
		}

		$template->set( 'jnlCode', $clCode );
		$cl = new Client( $clCode );
		$name = $cl->getDisplayName(  );
		$template->set( 'jnlName', $name );
		return false;
	}

	function _chooseinsco($template, $input) {
		global $session;

		$template->clearAllocated(  );
		$ret = '../accounts/journalEdit.php';
		$session->set( 'returnTo', $ret );
		$session->set( 'journalEditTemplate', $template );
		flocationheader( '../inscos/inscos.php' );
		return false;
	}

	function _setinsco($template, $input) {
		$icCode = $input['setInsco'];

		if ($icCode <= 0) {
			return false;
		}

		$template->set( 'jnlCode', $icCode );
		$ic = new Insco( $icCode );
		$name = $ic->get( 'icName' );
		$template->set( 'jnlName', $name );
		return false;
	}

	function _chooseintroducer($template, $input) {
		global $session;

		$template->clearAllocated(  );
		$ret = '../accounts/journalEdit.php';
		$session->set( 'returnTo', $ret );
		$session->set( 'journalEditTemplate', $template );
		flocationheader( '../introducers/introducers.php' );
		return false;
	}

	function _setintroducer($template, $input) {
		$inCode = $input['setIntroducer'];

		if ($inCode <= 0) {
			return false;
		}

		$template->set( 'jnlCode', $inCode );
		$in = new Introducer( $inCode );
		$name = $in->get( 'inName' );
		$template->set( 'jnlName', $name );
		return false;
	}

	function _docancel($template, $input) {
		$template->set( 'message', 'transaction cancelled' );
		$template->clearInput(  );
		$template->setJournalPosted( false );
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

	function _postjournal($template, $input) {
		$messg = $template->checkBatchDate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$messg = $template->checkAmounts(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$template->doPosting(  );
		$template->setJournalPosted( true );
		$template->setMessage( 'journal posted' );
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

	function _viewjournal($template, $input) {
		$jnCode = $input['viewJournal'];
		$template->setJournal( $jnCode );
		return false;
	}

	require( '../include/startup.php' );
	$journalEditTemplate = &$session->get( 'journalEditTemplate' );

	if ($journalEditTemplate == null) {
		$journalEditTemplate = new JournalEditTemplate( 'journalEdit.html' );
		$journalEditTemplate->setProcess( '_newJournal', 'newJournal' );
		$journalEditTemplate->setProcess( '_goBack', 'back' );
		$journalEditTemplate->setProcess( '_doCancel', 'cancel' );
		$journalEditTemplate->setProcess( '_viewJournal', 'viewJournal' );
		$journalEditTemplate->setProcess( '_postJournal', 'post' );
		$journalEditTemplate->setProcess( '_chooseClient', 'chooseClient' );
		$journalEditTemplate->setProcess( '_setClient', 'setClient' );
		$journalEditTemplate->setProcess( '_chooseInsco', 'chooseInsco' );
		$journalEditTemplate->setProcess( '_setInsco', 'setInsco' );
		$journalEditTemplate->setProcess( '_chooseIntroducer', 'chooseIntroducer' );
		$journalEditTemplate->setProcess( '_setIntroducer', 'setIntroducer' );
		$journalEditTemplate->setAllowEditing( false );
		$journalEditTemplate->setAllowExiting( true );
		$journalEditTemplate->setReturnTo( '../batches/journalEdit.php' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$journalEditTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'journalEditTemplate', $journalEditTemplate );
	$journalEditTemplate->process(  );
	$session->set( 'journalEditTemplate', $journalEditTemplate );
?>