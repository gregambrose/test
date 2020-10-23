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

	class notestemplate {
		var $type = null;

		function notestemplate($html) {
			ftemplate::ftemplate( $html );
			$this->setProcess( '_createNewNote', 'createNew' );
			$this->setProcess( '_displaySelectedNote', 'noteToView' );
			$this->setProcess( '_cancelNote', 'cancelNote' );
			$this->setProcess( '_lockNote', 'lockIt' );
			$this->setProcess( '_deleteNote', 'deleteNote' );
			$this->setProcess( '_editNote', 'edit' );
			$this->addField( 'detailCode' );
			$this->addField( 'detailType' );
			$this->addField( 'detailSequence' );
			$this->addField( 'detailSubject' );
			$this->addField( 'detailNote' );
			$this->addField( 'detailEnteredBy' );
			$this->addField( 'detailWhenEntered' );
			$this->addField( 'detailOriginator' );
			$this->addField( 'detailWhenOriginated' );
			$this->addField( 'detailNextActionBy' );
			$this->addField( 'detailLocked' );
			$this->addField( 'clCode' );
			$this->addField( 'detailClientName' );
			$this->addField( 'introducerCodeFromClient' );
			$this->addField( 'introducerNameFromClient' );
			$this->addField( 'detailPolicyCode' );
			$this->addField( 'detailPolicyNumber' );
			$this->addField( 'detailPolicyType' );
			$this->addField( 'detailInscoCode' );
			$this->addField( 'detailInsuranceCoName' );
			$this->addField( 'detailIntroducerCode' );
			$this->addField( 'detailIntroducerChosen' );
			$this->addField( 'noteToView' );
			$this->addField( 'existingType' );
			$this->addField( 'cancelPrompt' );
			$this->addField( 'uploadTypeValue' );
			$this->addField( 'noteTypeValue' );
			$this->addField( 'nextBy' );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->setHeader( SITE_NAME );
		}

		function setdisabled($disabled) {
			$this->isDisabled = $disabled;
		}

		function setenteredby($usCode) {
			$this->set( 'detailEnteredBy', '' );

			if ($usCode < 1) {
				return null;
			}

			$user = new User( null );
			$found = $user->tryGettingRecord( $usCode );

			if ($found == true) {
				$this->set( 'detailEnteredBy', $user->getFullName(  ) );
			}

		}

		function setwhenentered($date) {
			$this->set( 'detailWhenEntered', uformatourtimestamp( $date ) );
		}

		function setoriginator($usCode) {
			$this->set( 'detailOriginator', '' );

			if ($usCode < 1) {
				return null;
			}

			$user = new User( null );
			$found = $user->tryGettingRecord( $usCode );

			if ($found == true) {
				$this->set( 'detailOriginator', $user->getFullName(  ) );
			}

		}

		function setwhenoriginated($date) {
			$this->set( 'detailWhenOriginated', uformatourtimestamp( $date ) );
		}

		function getnote() {
			return $this->note;
		}

		function setnote($note) {
			$this->note = &$note;

		}

		function setsequence($noClientSequence) {
			$this->set( 'detailSequence', sprintf( '%04s', $noClientSequence ) );
		}

		function _createnewnote($template, $input) {
			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}

			$this->note = null;
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			$this->clearDetailFields(  );
			$this->setMessage( 'creating new note' );
			$this->set( 'cancelPrompt', 'Do you wish to cancel this note?' );
			return false;
		}

		function _editnote($template, $input) {
			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}


			if (( isset( $this->note ) == false || is_a( $this->note, 'Note' ) == false )) {
				$template->setMessage( 'no note to edit' );
				return false;
			}

			$this->set( 'cancelPrompt', 'Do you wish to cancel the amendments?' );
			$note = &$this->note;

			$noCode = $note->getKeyValue(  );
			$this->selectNoteToDisplay( $noCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			return false;
		}

		function _displayselectednote($template, $input) {
			if (( !isset( $input['noteToView'] ) || $input['noteToView'] <= 0 )) {
				return false;
			}


			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}


			if (isset( $this->client )) {
				$this->_setIntroducerFromClient(  );
			}

			$noCode = $input['noteToView'];
			$this->selectNoteToDisplay( $noCode );
			return false;
		}

		function _cancelnote($template, $input) {
			$note = &$this->note;

			if (is_a( $note, 'Note' )) {
				$noCode = $note->getKeyValue(  );
				$this->selectNoteToDisplay( $noCode );
			} 
else {
				$this->clearDetailFields(  );
				$this->setAllowEditing( false );
				$this->setAllowExiting( true );
			}

			$this->setMessage( 'edit cancelled' );
			return false;
		}

		function _locknote($template, $input) {
			global $user;

			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}

			$detailCode = $template->get( 'detailCode' );

			if ($detailCode <= 0) {
				$template->setMessage( 'nothing to lock' );
				return false;
			}

			$usCode = 0;

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

			$note = $template->getNote(  );
			$noNextActionBy = $note->get( 'noNextActionBy' );

			if (0 < $noNextActionBy) {
				$template->setMessage( 'cannot lock - there is still an outstanding action required' );
				return false;
			}

			$note->set( 'noLocked', 1 );
			$note->set( 'noWhenEntered', ugettimenow(  ) );
			$note->set( 'noEnteredBy', $usCode );
			$ok = $note->update(  );

			if ($ok == false) {
				$note->refresh(  );
				$template->setAll( $note->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this note. You will need to re-enter any changes you made' );
				return false;
			}

			$template->setWhenEntered( $note->get( 'noWhenEntered' ) );
			$template->setEnteredBy( $note->get( 'noEnteredBy' ) );
			$template->set( 'detailLocked', 1 );
			$template->setMessage( 'note now locked' );
			$template->setAll( $note->getAllForHTML(  ) );
			return false;
		}

		function _deletenote($template, $input) {
			global $user;

			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}

			$detailCode = $template->get( 'detailCode' );

			if ($detailCode <= 0) {
				$template->setMessage( 'nothing to delete' );
				return false;
			}

			$note = $template->getNote(  );
			$locked = $note->get( 'noLocked' );

			if ($locked == true) {
				trigger_error( 'cant delete as locked', E_USER_ERROR );
			}

			$ok = $note->delete(  );

			if ($ok == false) {
				$note->refresh(  );
				$template->setAll( $note->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this client note. You will need to re-enter any changes you made' );
				return false;
			}

			$template->clearDetailFields(  );
			$template->setMessage( 'note deleted' );
			return false;
		}

		function shownotetypes($text) {
			$newType = $this->get( 'detailType' );
			$existingType = $this->get( 'existingType' );
			$q = 'SELECT * FROM noteTypes ';

			if ($this->type == 'CL') {
				$q .= 'WHERE ntClient = 1';
			}


			if ($this->type == 'PL') {
				$q .= 'WHERE ntPolicy = 1';
			}


			if ($this->type == 'IC') {
				$q .= 'WHERE ntInsco = 1';
			}


			if ($this->type == 'IN') {
				$q .= 'WHERE ntIntroducer = 1';
			}

			$type = 0;

			if (( isset( $this->note ) && is_a( $this->note, 'Note' ) )) {
				$type = $this->note->get( 'noType' );
			}


			if (0 < $type) {
				$q .= '' . ' OR ntCode=' . $type;
			}

			$q .= ' ORDER BY ntSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'ntCode', $row['ntCode'] );
				$this->set( 'ntName', $row['ntName'] );

				if ($newType == $row['ntCode']) {
					$showNewSelected = 'selected';
				} 
else {
					$showNewSelected = '';
				}

				$this->set( 'showNewSelected', $showNewSelected );

				if ($existingType == $row['ntCode']) {
					$showExistingSelected = 'selected';
				} 
else {
					$showExistingSelected = '';
				}

				$this->set( 'showExistingSelected', $showExistingSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showinsurancecompanies($text) {
			$existingCo = $this->get( 'detailInscoCode' );
			$q = 'SELECT icCode, icName  FROM insuranceCompanies ORDER BY icName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'icCode', $row['icCode'] );
				$this->set( 'icName', $row['icName'] );

				if ($existingCo == $row['icCode']) {
					$showSelected = 'selected';
				} 
else {
					$showSelected = '';
				}

				$this->set( 'showSelected', $showSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function shownextactionby($text) {
			$nextActionBy = $this->get( 'detailNextActionBy' );
			$q = 'SELECT * FROM users  ORDER BY usFirstName, usLastName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$user = new User( $row );
				$this->set( 'usCode', $row['usCode'] );
				$this->set( 'usFullName', $user->getFullName(  ) );

				if ($nextActionBy == $row['usCode']) {
					$showSelected = 'selected';
				} 
else {
					$showSelected = '';
				}

				$this->set( 'showSelected', $showSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showintroducer($text) {
			$inCode = $this->get( 'introducerCodeFromClient' );

			if ($inCode < 1) {
				return '';
			}

			$noIntroducer = $this->get( 'detailIntroducerCode' );
			$introducerNameFromClient = $this->get( 'introducerNameFromClient' );
			$checked = $this->get( 'detailIntroducerChosen' );

			if (( $checked == 1 || $checked == 'on' )) {
				$checked = 'checked';
			} 
else {
				$checked = 0;
			}


			if ($inCode != $noIntroducer) {
				$checked = '';
			}

			$this->set( 'introducerNameFromClient', $introducerNameFromClient );
			$this->set( 'showIfChecked', $checked );
			$out = $this->parse( $text );
			return $out;
		}

		function showclientname($text) {
			$note = &$this->getNote(  );

			if (!is_a( $note, 'Note' )) {
				return '';
			}

			$clCode = $note->get( 'noClient' );

			if (( $clCode == null || $clCode < 1 )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showpolicynumber($text) {
			$note = &$this->getNote(  );

			if (!is_a( $note, 'Note' )) {
				return '';
			}

			$plCode = $note->get( 'noPolicy' );

			if (( $plCode == null || $plCode < 1 )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function wheneditnotallowedandnotlocked($text) {
			$note = &$this->getNote(  );

			$noLocked = 0;

			if (is_a( $note, 'Note' )) {
				$noLocked = $note->get( 'noLocked' );
			}

			$out = '';

			if (( $this->allowEdit == false && $noLocked != 1 )) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function showiflocked($text) {
			if ($this->get( 'detailLocked' ) != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $text;
		}

		function showifnotlocked($text) {
			if ($this->get( 'detailLocked' ) == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $text;
		}

		function showcolour() {
			if ($this->get( 'detailLocked' ) == true) {
				return '#E9E9E9';
			}

			return '#E7EAFE';
		}

		function cleardetailfields() {
			$this->set( 'detailCode', null );
			$this->set( 'detailType', null );
			$this->set( 'detailSubject', null );
			$this->set( 'detailSequence', null );
			$this->set( 'detailNote', null );
			$this->set( 'detailEnteredBy', null );
			$this->set( 'detailWhenEntered', null );
			$this->set( 'detailWhenOriginated', null );
			$this->set( 'detailOriginator', null );
			$this->set( 'detailNextActionBy', null );
			$this->set( 'detailPolicyCode', null );
			$this->set( 'detailInscoCode', null );
			$this->set( 'detailIntroducerCode', null );
			$this->set( 'detailIntroducerChosen', 0 );
			$this->set( 'detailLocked', null );
		}

		function selectnotetodisplay($noCode) {
			$note = new Note( $noCode );
			$note->fetchExtraColumns(  );
			$this->set( 'detailCode', $note->get( 'noCode' ) );
			$this->set( 'detailType', $note->get( 'noType' ) );
			$seq = '';

			if ($this->type == 'CL') {
				$seq = $note->get( 'noClientSequence' );
			}


			if ($this->type == 'PL') {
				$seq = $note->get( 'noPolicySequence' );
			}


			if ($this->type == 'IC') {
				$seq = $note->get( 'noInscoSequence' );
			}


			if ($this->type == 'IN') {
				$seq = $note->get( 'noIntroducerSequence' );
			}

			$this->set( 'detailSequence', sprintf( '%04s', $seq ) );
			$this->set( 'detailSubject', $note->get( 'noSubject' ) );
			$this->set( 'detailNote', $note->get( 'noNote' ) );
			$this->setEnteredBy( $note->get( 'noEnteredBy' ) );
			$this->setWhenEntered( $note->get( 'noWhenEntered' ) );
			$this->setOriginator( $note->get( 'noOriginator' ) );
			$this->setWhenOriginated( $note->get( 'noWhenOriginated' ) );
			$this->set( 'detailNextActionBy', $note->get( 'noNextActionBy' ) );
			$this->set( 'detailLocked', $note->get( 'noLocked' ) );
			$polNum = '';
			$plCode = $note->get( 'noPolicy' );

			if (0 < $plCode) {
				$policy = new Policy( $plCode );
				$polNum = $policy->get( 'plPolicyNumber' );
			}

			$this->set( 'detailPolicyCode', $plCode );
			$this->set( 'detailPolicyNumber', $polNum );
			$icCode = $note->get( 'noInsco' );
			$this->set( 'detailInscoCode', $icCode );
			$name = '';

			if (0 < $icCode) {
				$insco = new Insco( $icCode );
				$name = $insco->get( 'icName' );
			}

			$this->set( 'detailInsuranceCoName', $name );
			$clCode = $note->get( 'noClient' );
			$this->set( 'detailClientCode', $clCode );
			$name = '';

			if (0 < $clCode) {
				$client = new Client( $clCode );
				$name = $client->get( 'clName' );
				$this->client = $client;
			}

			$this->set( 'detailClientName', $name );
			$this->set( 'detailIntroducerCode', $note->get( 'noIntroducer' ) );
			$this->set( 'noteTypeValue', $note->get( 'ntName' ) );
			$nextBy = trim( $note->get( 'nextByFirst' ) . ' ' . $note->get( 'nextByLast' ) );

			if (strlen( $nextBy ) == 0) {
				$nextBy = 'No Action Needed';
			}

			$this->set( 'nextBy', $nextBy );
			$code = $this->get( 'introducerCodeFromClient' );
			$introdChosen = 0;
			$inCode = $note->get( 'noIntroducer' );

			if (( 0 < $inCode && $inCode == $code )) {
				$introdChosen = 1;
			}

			$this->set( 'detailIntroducerChosen', $introdChosen );
			$this->set( 'detailExists', true );
			$this->setNote( $note );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function _setintroducerfromclient() {
			$inName = '';
			$inCode = $this->client->get( 'clIntroducer' );

			if (0 < $inCode) {
				$introducer = new Introducer( $inCode );
				$inName = $introducer->get( 'inName' );
			}

			$this->set( 'introducerCodeFromClient', $inCode );
			$this->set( 'introducerNameFromClient', $inName );
		}

		function _displaynotesusingselect($q, $text) {
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$noCode = $row['noCode'];
				$this->set( 'noCode', $noCode );
				$this->set( 'noSubject', $row['noSubject'] );
				$noType = $row['noType'];

				if (0 < $noType) {
					$type = new NoteType( $noType );
					$ntName = $type->get( 'ntName' );
				} 
else {
					$ntName = '';
				}

				$this->set( 'ntName', $ntName );
				$noLocked = $row['noLocked'];

				if ($noLocked == true) {
					$this->set( 'itemBackgroundColour', '#E9E9E9' );
				} 
else {
					$this->set( 'itemBackgroundColour', '#E7EAFE' );
				}


				if ($noCode == $this->get( 'detailCode' )) {
					$boldOn = '<div style="background-color:#CDD2FE">';
					$boldOff = '</div>';
				} 
else {
					$boldOn = '';
					$boldOff = '';
				}

				$this->set( 'boldOn', $boldOn );
				$this->set( 'boldOff', $boldOff );
				$seq = '';

				if ($this->type == 'CL') {
					$seq = 'noClientSequence';
				}


				if ($this->type == 'PL') {
					$seq = 'noPolicySequence';
				}


				if ($this->type == 'IC') {
					$seq = 'noInscoSequence';
				}


				if ($this->type == 'IN') {
					$seq = 'noIntroducerSequence';
				}

				$this->set( 'sequence', sprintf( '%04s', $row[$seq] ) );
				$on = $row['noWhenEntered'];

				if (( $on == null || strlen( $on ) == 0 )) {
					$noWhenOriginated = uformatourtimestamp( $row['noWhenOriginated'] );
					$this->set( 'noWhenEntered', $noWhenOriginated );
					$noOriginator = uformatourtimestamp( $row['noOriginator'] );
					$this->set( 'noEnteredBy', $noOriginator );
					$usCode = $row['noOriginator'];
				} 
else {
					$noWhenEntered = uformatourtimestamp( $row['noWhenEntered'] );
					$this->set( 'noWhenEntered', $noWhenEntered );
					$noEnteredBy = uformatourtimestamp( $row['noEnteredBy'] );
					$this->set( 'noEnteredBy', $noEnteredBy );
					$usCode = $row['noEnteredBy'];
				}

				$user = new User( null );
				$usInitials = '';

				if (( 0 < $usCode && $user->tryGettingRecord( $usCode ) )) {
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					if (DEBUG_MODE == true) {
						$usInitials = '';
					} 
else {
						trigger_error( 'cant get user for notes', E_USER_WARNING );
					}
				}

				$this->set( 'usInitials', $usInitials );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>