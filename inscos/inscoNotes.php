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
			$note->set( 'noWhenEntered', ugettimenow(  ) );
			$note->set( 'noEnteredBy', $usCode );
		} 
else {
			$note = new Note( null );
			$template->setNote( $note );
			$note->set( 'noWhenOriginated', ugettimenow(  ) );
			$note->set( 'noOriginator', $usCode );
		}

		$insco = $template->getInsco(  );
		$icCode = $insco->get( 'icCode' );

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

		if ($template->type == 'IC') {
			$note->set( 'noInsco', $icCode );
		}

		$note->set( 'noType', $detailType );
		$note->set( 'noSubject', $detailSubject );
		$note->set( 'noNote', $detailNote );
		$note->set( 'noNextActionBy', $detailNextActionBy );

		if ($template->type != 'IC') {
			if (0 < $detailInscoCode) {
				$code = $detailInscoCode;
			} 
else {
				$code = 0;
			}

			$note->set( 'noInsco', $code );
		}

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
			$note->set( 'noInsco', $icCode );
			$note->setInscoSequence(  );
			$note->update(  );
			$detailCode = $note->getKeyValue(  );
			$template->set( 'detailCode', $detailCode );
			$template->setSequence( $note->get( 'noInscoSequence' ) );
			$template->setMessage( 'new note created' );
			$template->setAll( $note->getAllForHTML(  ) );
		}

		$note->refresh(  );
		$note->fetchExtraColumns(  );
		$detailCode = $note->getKeyValue(  );
		$template->selectNoteToDisplay( $detailCode );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	require( '../include/startup.php' );
	$inscoNotesTemplate = &$session->get( 'inscoNotesTemplate' );

	if ($inscoNotesTemplate == null) {
		$inscoNotesTemplate = new InscoNotesTemplate( 'inscoNotes.html' );
		$inscoNotesTemplate->setProcess( '_doUpdate', 'updateNote' );
		$inscoNotesTemplate->setProcess( '_goBack', 'back' );
	}


	if (isset( $_GET['insco'] )) {
		$icCode = $_GET['insco'];
		$inscoNotesTemplate->setInsco( $icCode );
	}


	if (isset( $_GET['inscoNote'] )) {
		$noCode = $_GET['inscoNote'];
		$inscoNotesTemplate->selectNoteToDisplay( $noCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$inscoNotesTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'inscoNotesTemplate', $inscoNotesTemplate );
	$inscoNotesTemplate->process(  );
	$session->set( 'inscoNotesTemplate', $inscoNotesTemplate );
?>