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

	class viewdocumentstemplate {
		function viewdocumentstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'docmNo' );
			$this->setHeader( SITE_NAME );
		}

		function showoriginators($text) {
			$q = 'SELECT * FROM users ORDER BY usLastName, usFirstName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$originator = $this->get( 'originator' );

			while ($row = udbgetrow( $result )) {
				$user = new User( $row );
				$usCode = $row['usCode'];

				if ($usCode == $originator) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$name = $user->getFullName(  );
				$this->set( 'usCode', $usCode );
				$this->set( 'name', $name );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showlsstupdatedby($text) {
			$q = 'SELECT * FROM users ORDER BY usLastName, usFirstName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$lastUpdatedBy = $this->get( 'lastUpdatedBy' );

			while ($row = udbgetrow( $result )) {
				$user = new User( $row );
				$usCode = $row['usCode'];

				if ($usCode == $lastUpdatedBy) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$name = $user->getFullName(  );
				$this->set( 'usCode', $usCode );
				$this->set( 'name', $name );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function shownextactionby($text) {
			$q = 'SELECT * FROM users ORDER BY usLastName, usFirstName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$nextActionBy = $this->get( 'nextActionBy' );

			while ($row = udbgetrow( $result )) {
				$user = new User( $row );
				$usCode = $row['usCode'];

				if ($usCode == $nextActionBy) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$name = $user->getFullName(  );
				$this->set( 'usCode', $usCode );
				$this->set( 'name', $name );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whenadocument($text) {
			if ($this->type != 'D') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenanote($text) {
			if ($this->type != 'N') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listitems($text) {
			$items = array(  );
			$itemType = $this->get( 'itemType' );
			$originator = $this->get( 'originator' );
			$lastUpdatedBy = $this->get( 'lastUpdatedBy' );
			$nextActionBy = $this->get( 'nextActionBy' );

			if (( $itemType == 'N' || $itemType == 'A' )) {
				$q = 'SELECT noCode, noWhenOriginated
				FROM notes
				WHERE noLocked != 1 ';

				if (0 < $originator) {
					$q .= '' . ' AND noOriginator=' . $originator . ' ';
				}


				if (0 < $lastUpdatedBy) {
					$q .= '' . ' AND noEnteredBy=' . $lastUpdatedBy . ' ';
				}


				if (0 < $nextActionBy) {
					$q .= '' . ' AND noNextActionBy=' . $nextActionBy . ' ';
				}

				$q .= ' ORDER BY noCode';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$noCode = $row['noCode'];
					$noWhenOriginated = $row['noWhenOriginated'] . 'N';
					$items[$noCode] = $noWhenOriginated;
				}
			}


			if (( $itemType == 'D' || $itemType == 'A' )) {
				$q = 'SELECT doCode, doWhenOriginated
				FROM documents
			   WHERE doDeleted != 1 AND doLocked != 1 ';

				if (0 < $originator) {
					$q .= '' . ' AND doOriginator=' . $originator . ' ';
				}


				if (0 < $lastUpdatedBy) {
					$q .= '' . ' AND doEnteredBy=' . $lastUpdatedBy . ' ';
				}


				if (0 < $nextActionBy) {
					$q .= '' . ' AND doNextActionBy=' . $nextActionBy . ' ';
				}

				$q .= ' ORDER BY doCode';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$doCode = $row['doCode'];
					$doWhenOriginated = $row['doWhenOriginated'] . 'D';
					$items[$doCode] = $doWhenOriginated;
				}
			}


			if (count( $items ) == 0) {
				$this->setMessage( 'none found' );
				return '';
			}

			$this->setMessage( '' );
			asort( $items );
			$out = '';
			reset( $items );
			foreach ($items as $key => $value) {
				$dateAndType = $value;
				$code = $key;
				$date = substr( $dateAndType, 0, 14 );
				$type = substr( $dateAndType, 14, 1 );
				$this->type = $type;

				if ($type == 'N') {
					$note = new Note( $code );
					$this->set( 'type', 'note' );
					$this->set( 'noCode', $code );
					$originatedBy = '';
					$usCode = $note->get( 'noOriginator' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$originatedBy = $user->getFullName(  );
					}

					$this->set( 'originatedBy', $originatedBy );
					$date = $note->get( 'noWhenOriginated' );
					$date = uformatourtimestamp2( $date );
					$this->set( 'originatedWhen', $date );
					$enteredBy = '';
					$usCode = $note->get( 'noEnteredBy' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$enteredBy = $user->getFullName(  );
					}

					$this->set( 'enteredBy', $enteredBy );
					$date = $note->get( 'noWhenEntered' );
					$date = uformatourtimestamp2( $date );
					$this->set( 'enteredWhen', $date );
					$nextBy = '';
					$usCode = $note->get( 'noNextActionBy' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$nextBy = $user->getFullName(  );
					}

					$this->set( 'nextBy', $nextBy );
					$desc = $note->get( 'noSubject' );
					$this->set( 'subject', $desc );
				}


				if ($type == 'D') {
					$docm = new Document( $code );
					$this->set( 'type', 'document' );
					$this->set( 'doCode', $code );
					$originatedBy = '';
					$usCode = $docm->get( 'doOriginator' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$originatedBy = $user->getFullName(  );
					}

					$this->set( 'originatedBy', $originatedBy );
					$date = $docm->get( 'doWhenOriginated' );
					$date = uformatourtimestamp2( $date );
					$this->set( 'originatedWhen', $date );
					$enteredBy = '';
					$usCode = $docm->get( 'doEnteredBy' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$enteredBy = $user->getFullName(  );
					}

					$this->set( 'enteredBy', $enteredBy );
					$date = $docm->get( 'doWhenEntered' );
					$date = uformatourtimestamp2( $date );
					$this->set( 'enteredWhen', $date );
					$nextBy = '';
					$usCode = $docm->get( 'doNextActionBy' );

					if (0 < $usCode) {
						$user = new User( $usCode );
						$nextBy = $user->getFullName(  );
					}

					$this->set( 'nextBy', $nextBy );
					$desc = $docm->get( 'doSubject' );
					$this->set( 'subject', $desc );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function typeselected($type) {
			$itemType = $this->get( 'itemType' );

			if ($itemType == $type) {
				return 'selected';
			}

			return '';
		}
	}

?>