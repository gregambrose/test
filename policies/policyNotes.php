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
		$url = faddtourl( $url, 'refresh' );
		flocationheader( $url );
	}

	function _doupdate($template, $input) {
		$ok = _doactualupdate( $template, $input );
		return false;
	}

	function _doactualupdate($template, $input) {
		global $user;

		$detailType = $template->get( 'detailType' );

		if ($detailType <= 0) {
			$template->setMessage( 'you need to select a note type' );
			return false;
		}

		$detailSubject = $template->get( 'detailSubject' );

		if (strlen( trim( $detailSubject ) ) == 0) {
			$template->setMessage( 'you need to select a subject' );
			return false;
		}

		$detailNote = $template->get( 'detailNote' );

		if (strlen( trim( $detailNote ) ) == 0) {
			$template->setMessage( 'you haven\'t entered a note' );
			return false;
		}

		$detailInscoCode = $template->get( 'detailInscoCode' );
		$introducerCode = $template->get( 'introducerCodeFromClient' );
		$introducerName = $template->get( 'introducerNameFromClient' );
		$detailIntroducerChosen = $template->get( 'detailIntroducerChosen' );

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'cant get user for notes', E_USER_WARNING );
			}
		}

		$detailCode = $template->get( 'detailCode' );

		if ($detailCode <= 0) {
			$exists = false;
		} 
else {
			$exists = true;
		}


		if ($exists) {
			$note = $template->getNote(  );
			$noLocked = $note->get( 'noLocked' );

			if ($noLocked == 1) {
				trigger_error( 'cant amend locked note', E_USER_ERROR );
			}

			$note->set( 'noWhenEntered', ugettimenow(  ) );
			$note->set( 'noEnteredBy', $usCode );
		} 
else {
			$note = new Note( null );
			$template->setNote( $note );
			$note->set( 'noWhenOriginated', ugettimenow(  ) );
			$note->set( 'noOriginator', $usCode );
		}

		$policy = $template->getPolicy(  );
		$plCode = $policy->get( 'plCode' );
		$clCode = $policy->get( 'plClient' );

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'cant get user for notes', E_USER_WARNING );
			}
		}

		$detailNextActionBy = $template->get( 'detailNextActionBy' );
		$note->set( 'noPolicy', $plCode );
		$note->set( 'noType', $detailType );
		$note->set( 'noClient', $clCode );
		$note->set( 'noSubject', $detailSubject );
		$note->set( 'noNote', $detailNote );
		$note->set( 'noNextActionBy', $detailNextActionBy );

		if (0 < $detailInscoCode) {
			$icCode = $detailInscoCode;
		} 
else {
			$icCode = 0;
		}

		$note->set( 'noInsco', $icCode );

		if (( $detailIntroducerChosen == 1 || $detailIntroducerChosen == 'on' )) {
			$inCode = $introducerCode;
		} 
else {
			$inCode = 0;
		}

		$note->set( 'noIntroducer', $inCode );
		$template->set( 'detailIntroducerCode', $inCode );
		$template->setWhenEntered( $note->get( 'noWhenEntered' ) );
		$template->setEnteredBy( $note->get( 'noEnteredBy' ) );
		$template->setWhenOriginated( $note->get( 'noWhenOriginated' ) );
		$template->setOriginator( $note->get( 'noOriginator' ) );

		if ($exists) {
			$ok = $note->update(  );

			if ($ok == false) {
				$note->refresh(  );
				$template->setAll( $note->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this note. You will need to re-enter any changes you made' );
				$noCode = $note->getKeyValue(  );
				$template->selectNoteToDisplay( $noCode );
				return false;
			}

			$template->setMessage( 'note updated' );
		} 
else {
			$note->insert( null );
			$note->set( 'noPolicy', $plCode );
			$note->set( 'noClient', $clCode );
			$note->setPolicySequence(  );
			$note->setClientSequence(  );

			if (0 < $icCode) {
				$note->setInscoSequence(  );
			}


			if (0 < $inCode) {
				$note->setIntroducerSequence(  );
			}

			$note->update(  );
			$detailCode = $note->getKeyValue(  );
			$template->set( 'detailCode', $detailCode );
			$template->setSequence( $note->get( 'noPolicySequence' ) );
			$template->setMessage( 'new note created' );
			$template->setAll( $note->getAllForHTML(  ) );
		}

		$note->refresh(  );
		$note->fetchExtraColumns(  );
		$detailCode = $note->getKeyValue(  );
		$template->selectNoteToDisplay( $detailCode );
		$template->setNote( $note );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	require( '../include/startup.php' );
	$policyNotesTemplate = &$session->get( 'policyNotesTemplate' );

	if ($policyNotesTemplate == null) {
		$policyNotesTemplate = new PolicyNotesTemplate( 'policyNotes.html' );
		$policyNotesTemplate->setProcess( '_doUpdate', 'updateNote' );
		$policyNotesTemplate->setProcess( '_goBack', 'back' );
	}


	if (isset( $_GET['policy'] )) {
		$plCode = $_GET['policy'];
		$policyNotesTemplate->setPolicy( $plCode );
	}


	if (isset( $_GET['policyNote'] )) {
		$noCode = $_GET['policyNote'];
		$policyNotesTemplate->selectNoteToDisplay( $noCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$policyNotesTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'policyNotesTemplate', $policyNotesTemplate );
	$policyNotesTemplate->process(  );
	$session->set( 'policyNotesTemplate', $policyNotesTemplate );
?>