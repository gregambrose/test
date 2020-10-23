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

	function _createjournal($template, $input) {
		global $session;

		$ret = '../accounts/journals.php';
		$session->set( 'returnTo', $ret );
		$type = $input['jnlType'];
		$ok = false;

		if ($type == 'CC') {
			$ok = true;
		}


		if ($type == 'CI') {
			$ok = true;
		}


		if ($type == 'CN') {
			$ok = true;
		}


		if ($type == 'NC') {
			$ok = true;
		}


		if ($type == 'NI') {
			$ok = true;
		}


		if ($type == 'NN') {
			$ok = true;
		}


		if ($ok == false) {
			$template->setMessage( 'you need to select a journal type' );
			return false;
		}

		flocationheader( '' . '../accounts/journalEdit.php?newJournal=' . $type );
		return false;
	}

	function _accessjournal($template, $input) {
		global $session;

		$jnCode = $input['toView'];

		if ($jnCode <= 0) {
			return false;
		}

		$ret = '../accounts/journals.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $jnCode );
		return false;
	}

	function _viewtrans($template, $input) {
		global $session;

		$jnCode = $input['transToView'];

		if ($jnCode < 1) {
			return false;
		}

		$ret = '../accounts/journals.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../accounts/journalEdit.php?transToView=' . $jnCode );
	}

	function _seeifjournalbeingedited($template, $input) {
		global $session;
		$journalEditTemplate = &$session->get( 'journalEditTemplate' );

		if ($journalEditTemplate == null) {
			return false;
		}


		if ($journalEditTemplate->getAllowEditing(  ) == true) {
			flocationheader( '../accounts/journalEdit.php' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$journalsTemplate = &$session->get( 'journalsTemplate' );

	if ($journalsTemplate == null) {
		$journalsTemplate = new JournalsTemplate( 'journals.html' );
		$journalsTemplate->setProcess( '_createJournal', 'newJournal' );
		$journalsTemplate->setProcess( '_accessJournal', 'toView' );
		$journalsTemplate->setOneOffFunctionToCall( '_seeIfJournalBeingEdited' );
		$journalsTemplate->setReturnTo( '../accounts/journals.php' );
	}

	$session->set( 'journalsTemplate', $journalsTemplate );
	$journalsTemplate->process(  );
	$session->set( 'journalsTemplate', $journalsTemplate );
?>