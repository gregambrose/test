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

	class documentstemplate {
		var $type = null;

		function documentstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->setProcess( '_createNewDocument', 'createNew' );
			$this->setProcess( '_displaySelectedDocument', 'docmToView' );
			$this->setProcess( '_viewDocument', 'docmFileToView' );
			$this->setProcess( '_cancelDocument', 'cancelDocument' );
			$this->setProcess( '_lockDocument', 'lockIt' );
			$this->setProcess( '_deleteDocument', 'deleteDocument' );
			$this->setProcess( '_removeUpload', 'removeUpload' );
			$this->setProcess( '_editDocument', 'edit' );
			$this->setProcess( '_updateSent', 'sent' );
			$this->setProcess( '_updateAttached', 'attach' );
			$this->addField( 'detailCode' );
			$this->addField( 'detailType' );
			$this->addField( 'detailUploadType' );
			$this->addField( 'detailSequence' );
			$this->addField( 'detailSubject' );
			$this->addField( 'detailDocument' );
			$this->addField( 'detailEnteredBy' );
			$this->addField( 'detailWhenEntered' );
			$this->addField( 'detailOriginator' );
			$this->addField( 'detailWhenOriginated' );
			$this->addField( 'detailNextActionBy' );
			$this->addField( 'detailLocked' );
			$this->addField( 'detailUploadFile' );
			$this->addField( 'detailTransNo' );
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
			$this->addField( 'detailSentWhen' );
			$this->addField( 'detailSentHow' );
			$this->addField( 'detailSentBy' );
			$this->addField( 'detailAttachedTo' );
			$this->addField( 'documentToView' );
			$this->addField( 'existingType' );
			$this->addField( 'selectSent' );
			$this->addField( 'cancelPrompt' );
			$this->addField( 'contactName' );
			$this->addField( 'docmType' );
			$this->addField( 'insCoName' );
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

		function setsentby($usCode) {
			$this->set( 'detailSentBy', '' );

			if ($usCode < 1) {
				return null;
			}

			$user = new User( null );
			$found = $user->tryGettingRecord( $usCode );

			if ($found == true) {
				$this->set( 'detailSentBy', $user->getFullName(  ) );
			}

		}

		function setwhenoriginated($date) {
			$this->set( 'detailWhenOriginated', uformatourtimestamp( $date ) );
		}

		function getdocument() {
			if (!isset( $this->document )) {
				$temp = null;
				return $temp;
			}

			return $this->document;
		}

		function setdocument($document) {
			$this->document = &$document;

		}

		function setsequence($doClientSequence) {
			$this->set( 'detailSequence', sprintf( '%04s', $doClientSequence ) );
		}

		function _createnewdocument($template, $input) {
			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}

			$this->document = null;
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			$this->clearDetailFields(  );
			$this->setMessage( 'creating new document' );
			$this->set( 'cancelPrompt', 'Do you wish to cancel this document?' );
			return false;
		}

		function _editdocument($template, $input) {
			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}


			if (( isset( $this->document ) == false || is_a( $this->document, 'Document' ) == false )) {
				$template->setMessage( 'no document to edit' );
				return false;
			}

			$this->set( 'cancelPrompt', 'Do you wish to cancel the amendments?' );
			$document = &$this->document;

			$doCode = $document->getKeyValue(  );
			$this->selectDocumentToDisplay( $doCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			return false;
		}

		function _displayselecteddocument($template, $input) {
			if (( !isset( $input['docmToView'] ) || $input['docmToView'] <= 0 )) {
				return false;
			}


			if ($template->getAllowExiting(  ) == false) {
				$template->setMessage( 'you must update or cancel before you leave' );
				return false;
			}


			if (isset( $this->client )) {
				$this->_setIntroducerFromClient(  );
			}

			$doCode = $input['docmToView'];
			$this->selectDocumentToDisplay( $doCode );
			return false;
		}

		function _canceldocument($template, $input) {
			$document = &$this->document;

			if (is_a( $document, 'Document' )) {
				$doCode = $document->getKeyValue(  );
				$this->selectDocumentToDisplay( $doCode );
			} 
else {
				$this->clearDetailFields(  );
				$this->setAllowEditing( false );
				$this->setAllowExiting( true );
			}

			$this->setMessage( 'edit cancelled' );
			return false;
		}

		function _lockdocument($template, $input) {
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


			if (is_a( $user, 'User' )) {
				$usCode = $user->getKeyValue(  );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					trigger_error( 'cant get user ', E_USER_WARNING );
				}
			}

			$document = $template->getDocument(  );
			$doNextActionBy = $document->get( 'doNextActionBy' );

			if (0 < $doNextActionBy) {
				$template->setMessage( 'cannot lock - there is still an outstanding action required' );
				return false;
			}

			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$ok = $document->update(  );

			if ($ok == false) {
				$document->refresh(  );
				$document->fetchExtraColumns(  );
				$template->setAll( $document->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this document. You will need to re-enter any changes you made' );
				return false;
			}

			$template->setDocument( $document );
			$template->setWhenEntered( $document->get( 'doWhenEntered' ) );
			$template->setEnteredBy( $document->get( 'doEnteredBy' ) );
			$template->set( 'detailLocked', 1 );
			$template->setMessage( 'document now locked' );
			$template->setAll( $document->getAllForHTML(  ) );
			return false;
		}

		function _removeupload($template, $input) {
			$document = &$template->getDocument(  );

			if ($document == null) {
				trigger_error( 'cant get docm', E_USER_ERROR );
			}

			$doLocked = $document->get( 'doLocked' );

			if ($doLocked == 1) {
				trigger_error( 'cant remove file from locked docm', E_USER_ERROR );
			}

			$document->set( 'doBinaryDetail', '' );
			$document->set( 'doFileType', '' );
			$document->set( 'doFileName', '' );
			$document->set( 'doFileSize', 0 );
			$template->set( 'detailUploadFile', '' );
			$template->setMessage( 'uploaded file removed' );
			$template->setAll( $document->getAllForHTML(  ) );
			return false;
		}

		function _deletedocument($template, $input) {
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

			$document = $template->getDocument(  );
			$locked = $document->get( 'doLocked' );

			if ($locked == true) {
				trigger_error( 'cant delete as locked', E_USER_ERROR );
			}

			$ok = $document->delete(  );

			if ($ok == false) {
				$document->refresh(  );
				$template->setAll( $document->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this client document. You will need to re-enter any changes you made' );
				return false;
			}

			$template->clearDetailFields(  );
			$template->setMessage( 'document deleted' );
			return false;
		}

		function _viewdocument($template, $input) {
			if (( !isset( $input['docmFileToView'] ) || $input['docmFileToView'] <= 0 )) {
				return false;
			}

			$doCode = $input['docmFileToView'];
			$docm = new Document( $doCode );
			$ok = $docm->viewDocument(  );

			if ($ok == false) {
				$template->setMessage( 'sorry....cant show you that document' );
			}

			return false;
		}

		function _updatesent($template, $input) {
			global $user;

			$usCode = 0;

			if (is_a( $user, 'User' )) {
				$usCode = $user->getKeyValue(  );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					trigger_error( 'no user', E_USER_WARNING );
				}
			}

			$doSentWhen = $template->get( 'detailSentWhen' );
			$doSentHow = $template->get( 'detailSentHow' );

			if (strlen( trim( $doSentWhen ) ) == 0) {
				$dateEntered = false;
			} 
else {
				$dateEntered = true;
			}


			if ($doSentHow <= 1) {
				$howEntered = false;
			} 
else {
				$howEntered = true;
			}

			$ok = true;

			if (( $howEntered == true && $dateEntered == false )) {
				$ok = false;
			}


			if (( $howEntered == false && $dateEntered == true )) {
				$ok = false;
			}


			if ($ok == false) {
				if ($doSentHow == 1) {
					$template->setMessage( 'you cant specify a date for documents that are not to be sent' );
				} 
else {
					$template->setMessage( 'you need to specify how and when the document was sent' );
				}

				return false;
			}

			$detailCode = $template->get( 'detailCode' );

			if ($detailCode <= 0) {
				$template->setMessage( 'nothing to update' );
				return false;
			}

			$document = $template->getDocument(  );
			$ok = $this->_setSentOnDocumentsAttachedToThis( $document, $this->type, $doSentWhen, $doSentHow, $usCode );

			if ($ok == false) {
				$template->setMessage( 'not all attached documents locked, so can\'t send' );
				return false;
			}

			$doLocked = $document->get( 'doLocked' );

			if ($doLocked != 1) {
				trigger_error( 'cant update unlocked docm', E_USER_ERROR );
			}

			$document->setSent( $this->type, $doSentWhen, $doSentHow, $usCode );
			$document->update(  );
			$document->fetchExtraColumns(  );
			$template->setDocument( $document );
			$this->setSentBy( $usCode );
			return false;
		}

		function _setsentondocumentsattachedtothis($document, $type, $doSentWhen, $doSentHow, $usCode) {
			$doCode = $document->getKeyValue(  );

			if ($doCode < 1) {
				trigger_error( 'cant get docms=' . $doCode, E_USER_ERROR );
			}

			$q = 'SELECT doCode FROM documents WHERE doDeleted != 1 AND ';

			if (( ( $type == 'CL' || $type == 'PL' ) || $type == 'PT' )) {
				$q .= 'doClientAttachedTo=';
			} 
else {
				if ($type == 'IC') {
					$q .= 'doInscoAttachedTo=';
				} 
else {
					if ($type == 'IN') {
						$q .= 'doIntroducerAttachedTo=';
					} 
else {
						if ($type == 'MR') {
							return true;
						}

						trigger_error( 'wrong type =' . $type, E_USER_ERROR );
					}
				}
			}

			$q .= $doCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$docms = array(  );
			$ok = true;

			while ($row = udbgetrow( $result )) {
				$doCode = $row['doCode'];
				$docms[] = $doCode;
				$doc = new Document( $doCode );

				if ($doc->get( 'doLocked' ) != 1) {
					$ok = false;
					break;
				}
			}


			if ($ok == false) {
				return false;
			}

			reset( $docms );

			while ($fld = each( $docms )) {
				$fldKey = $fld['key'];
				$doCode = $fld['value'];
				$doc = new Document( $doCode );
				$doc->setSent( $type, $doSentWhen, $doSentHow, $usCode );
				$doc->update(  );
			}

			return true;
		}

		function _updateattached($template, $input) {
			$doAttachedTo = $template->get( 'detailAttachedTo' );
			$detailCode = $template->get( 'detailCode' );

			if ($detailCode <= 0) {
				$template->setMessage( 'nothing to update' );
				return false;
			}

			$document = $template->getDocument(  );

			if (( ( $this->type == 'CL' || $this->type == 'PL' ) || $this->type == 'PT' )) {
				$document->set( 'doClientAttachedTo', $doAttachedTo );
			}


			if ($this->type == 'IC') {
				$document->set( 'doInscoAttachedTo', $doAttachedTo );
			}


			if ($this->type == 'IN') {
				$document->set( 'doIntroducerAttachedTo', $doAttachedTo );
			}

			$document->update(  );
			$document->fetchExtraColumns(  );
			$template->setDocument( $document );
			return false;
		}

		function showdocmtypes($text) {
			$newType = $this->get( 'detailType' );
			$existingType = $this->get( 'existingType' );
			$q = 'SELECT * FROM documentTypes ';

			if ($this->type == 'CL') {
				$q .= 'WHERE dtClient = 1';
			}


			if (( $this->type == 'PL' || $this->type == 'PT' )) {
				$q .= 'WHERE dtPolicy = 1';
			}


			if ($this->type == 'IC') {
				$q .= 'WHERE dtInsco = 1';
			}


			if ($this->type == 'IN') {
				$q .= 'WHERE dtIntroducer = 1';
			}

			$type = 0;

			if (( isset( $this->document ) && is_a( $this->document, 'Document' ) )) {
				$type = $this->document->get( 'doDocmType' );
			}


			if (0 < $type) {
				$q .= '' . ' OR dtCode=' . $type;
			}

			$q .= ' ORDER BY dtSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'dtCode', $row['dtCode'] );
				$this->set( 'dtName', $row['dtName'] );

				if ($newType == $row['dtCode']) {
					$showNewSelected = 'selected';
				} 
else {
					$showNewSelected = '';
				}

				$this->set( 'showNewSelected', $showNewSelected );

				if ($existingType == $row['dtCode']) {
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

		function showintroducer($text) {
			$inCode = $this->get( 'introducerCodeFromClient' );

			if ($inCode < 1) {
				return '';
			}

			$doIntroducer = $this->get( 'detailIntroducerCode' );
			$introducerNameFromClient = $this->get( 'introducerNameFromClient' );
			$checked = $this->get( 'detailIntroducerChosen' );

			if (( $checked == 1 || $checked == 'on' )) {
				$checked = 'checked';
			} 
else {
				$checked = 0;
			}


			if ($inCode != $doIntroducer) {
				$checked = '';
			}

			$this->set( 'introducerNameFromClient', $introducerNameFromClient );
			$this->set( 'showIfChecked', $checked );
			$out = $this->parse( $text );
			return $out;
		}

		function showsenthow($text) {
			$sentCode = $this->get( 'detailSentHow' );
			$q = 'SELECT * FROM howSent ORDER BY hsSequence ';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'hsCode', $row['hsCode'] );
				$this->set( 'hsName', $row['hsName'] );

				if ($sentCode == $row['hsCode']) {
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

		function showtransnumbers($text) {
			return '';
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

		function showattachedto($text) {
			$doAttachedTo = $this->get( 'detailAttachedTo' );
			$detailCode = $this->get( 'detailCode' );
			$orderBy = '';
			$q = '';

			if ($this->type == 'CL') {
				$clCode = $this->get( 'clCode' );

				if ($clCode < 1) {
					return '';
				}

				$q = '' . 'SELECT doCode, doClientSequence FROM documents
				WHERE doDeleted != 1 AND
					doClient=' . $clCode . ' AND
					(doClientAttachedTo=0 AND
					doClientSentWhen=\'0000-00-00\')';
				$orderBy = ' ORDER BY doClientSequence DESC';
			} 
else {
				if ($this->type == 'PL') {
					$plCode = $this->get( 'plCode' );

					if ($plCode < 1) {
						return '';
					}

					$q = '' . 'SELECT doCode, doPolicySequence FROM documents
				WHERE doDeleted != 1 AND doPolicy=' . $plCode . ' AND
					(doClientAttachedTo=0 AND
					doClientSentWhen=\'0000-00-00\')';
					$orderBy = ' ORDER BY doPolicySequence DESC';
				} 
else {
					if ($this->type == 'PT') {
						$ptCode = $this->get( 'ptCode' );

						if ($ptCode < 1) {
							return '';
						}

						$q = '' . 'SELECT doCode, doTransSequence FROM documents
				WHERE doDeleted != 1 AND  doTrans=' . $ptCode . ' AND
					(doClientAttachedTo=0 AND
					doClientSentWhen=\'0000-00-00\')';
						$orderBy = ' ORDER BY doTransSequence DESC';
					} 
else {
						if ($this->type == 'IC') {
							$icCode = $this->get( 'icCode' );

							if ($icCode < 1) {
								return '';
							}

							$q = '' . 'SELECT doCode, doInscoSequence FROM documents
				WHERE doDeleted != 1 AND  doInsco=' . $icCode . ' AND
					(doInscoAttachedTo=0 AND
					doInscoSentWhen=\'0000-00-00\')';
							$orderBy = ' ORDER BY doInscoSequence DESC';
						} 
else {
							if ($this->type == 'IN') {
								$inCode = $this->get( 'inCode' );

								if ($inCode < 1) {
									return '';
								}

								$q = '' . 'SELECT doCode, doIntroducerSequence FROM documents
				WHERE doDeleted != 1 AND doIntroducer=' . $inCode . ' AND
					(doIntroducerAttachedTo=0 AND
					doIntroducerSentWhen=\'0000-00-00\')';
								$orderBy = ' ORDER BY doCode DESC';
							} 
else {
								trigger_error( 'type not set correctly: ' . $this->type, E_USER_ERROR );
							}
						}
					}
				}
			}


			if (0 < $detailCode) {
				$q .= '' . ' AND doCode!=' . $detailCode;
			}


			if (0 < $doAttachedTo) {
				$q .= '' . ' OR (doCode=' . $doAttachedTo . ')';
			}

			$q .= $orderBy;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ) . ' ' . $q, E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'doCode', $row['doCode'] );

				if (isset( $row['doClientSequence'] )) {
					$this->set( 'doClientSequence', sprintf( '%04s', $row['doClientSequence'] ) );
				}


				if (isset( $row['doPolicySequence'] )) {
					$this->set( 'doPolicySequence', sprintf( '%04s', $row['doPolicySequence'] ) );
				}


				if (isset( $row['doInscoSequence'] )) {
					$this->set( 'doInscoSequence', sprintf( '%04s', $row['doInscoSequence'] ) );
				}


				if (isset( $row['doIntroducerSequence'] )) {
					$this->set( 'doIntroducerSequence', sprintf( '%04s', $row['doIntroducerSequence'] ) );
				}


				if (isset( $row['doTransSequence'] )) {
					$this->set( 'doTransSequence', sprintf( '%04s', $row['doTransSequence'] ) );
				}


				if ($this->type == 'PL') {
					if ($this->get( 'doPolicySequence' ) == '0000') {
						return null;
					}
				}


				if ($row['doCode'] == $doAttachedTo) {
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

		function showclientname($text) {
			$document = &$this->getDocument(  );

			if (!is_a( $document, 'Document' )) {
				return '';
			}

			$clCode = $document->get( 'doClient' );

			if (( $clCode == null || $clCode < 1 )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showpolicynumber($text) {
			$document = &$this->getDocument(  );

			if (!is_a( $document, 'Document' )) {
				return '';
			}

			$plCode = $document->get( 'doPolicy' );

			if (( $plCode == null || $plCode < 1 )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showinsurancecompany($text) {
			$document = &$this->getDocument(  );

			if (!is_a( $document, 'Document' )) {
				return '';
			}

			$icCode = $document->get( 'doInsco' );

			if (( $icCode == null || $icCode < 1 )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function wheneditnotallowedandnotlocked($text) {
			$document = &$this->getDocument(  );

			$doLocked = 1;

			if (is_a( $document, 'Document' )) {
				$doLocked = $document->get( 'doLocked' );
			}

			$out = '';

			if (( $this->allowEdit == false && $doLocked != 1 )) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function whenremovinguploadallowed($text) {
			$document = &$this->getDocument(  );

			$doLocked = 0;
			$size = 0;

			if (is_a( $document, 'Document' )) {
				$doLocked = $document->get( 'doLocked' );
				$size = $document->get( 'doFileSize' );
			}

			$out = '';

			if (( ( $this->allowEdit == true && $doLocked != 1 ) && 0 < $size )) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function whendocumenttoview($text) {
			$document = &$this->getDocument(  );

			$size = 0;

			if (is_a( $document, 'Document' )) {
				$size = $document->get( 'doFileSize' );
			}

			$out = '';

			if (0 < $size) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function showiflocked($text) {
			if ($this->get( 'detailLocked' ) != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showifnotlocked($text) {
			if ($this->get( 'detailLocked' ) == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whendocumentinlisttoview($text) {
			$size = $this->get( 'size' );
			$out = '';

			if (0 < $size) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function whencansetattached($text) {
			$ok = false;

			if ($this->get( 'detailLocked' ) == true) {
				$ok = true;
			}


			if ($this->allowEdit == true) {
				$ok = true;
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whencannotsetattached($text) {
			$ok = false;

			if ($this->get( 'detailLocked' ) == true) {
				$ok = true;
			}


			if ($this->allowEdit == true) {
				$ok = true;
			}


			if ($ok == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showcolour() {
			if ($this->get( 'detailLocked' ) == true) {
				return '#E9E9E9';
			}

			return '#E7EAFE';
		}

		function showuploadtype($type) {
			$current = $this->get( 'detailUploadType' );

			if ($current == $type) {
				return 'selected';
			}

			return '';
		}

		function showselectsent($type) {
			$current = $this->get( 'selectSent' );

			if ($current == null) {
				$current = 0;
			}


			if ($current == $type) {
				return 'selected';
			}

			return '';
		}

		function showdisabledwhencansetattached() {
			$ok = false;

			if ($this->get( 'detailLocked' ) == true) {
				$ok = true;
			}


			if ($this->allowEdit == true) {
				$ok = true;
			}


			if ($ok == false) {
				return 'disabled';
			}

			return '';
		}

		function cleardetailfields() {
			$this->set( 'detailCode', null );
			$this->set( 'detailType', null );
			$this->set( 'detailUploadType', null );
			$this->set( 'detailSubject', null );
			$this->set( 'detailSequence', null );
			$this->set( 'detailDocument', null );
			$this->set( 'detailEnteredBy', null );
			$this->set( 'detailWhenEntered', null );
			$this->set( 'detailWhenOriginated', null );
			$this->set( 'detailOriginator', null );
			$this->set( 'detailNextActionBy', null );
			$this->set( 'detailPolicyCode', null );
			$this->set( 'detailPolicyNumber', null );
			$this->set( 'detailInscoCode', null );
			$this->set( 'detailIntroducerCode', null );
			$this->set( 'detailIntroducerChosen', 0 );
			$this->set( 'detailSentWhen', null );
			$this->set( 'detailSentHow', 0 );
			$this->set( 'detailSentBy', '' );
			$this->set( 'detailAttachedTo', 0 );
			$this->set( 'detailUploadFile', '' );
			$this->set( 'detailLocked', null );
			$this->set( 'detailTransNo', null );
		}

		function selectdocumenttodisplay($doCode) {
			$document = new Document( $doCode );
			$document->fetchExtraColumns(  );
			$this->set( 'detailCode', $document->get( 'doCode' ) );
			$this->set( 'detailType', $document->get( 'doDocmType' ) );
			$this->set( 'detailUploadType', $document->get( 'doUploadType' ) );
			$this->set( 'docmType', $document->get( 'dtName' ) );
			$this->set( 'insCoName', $document->get( 'icName' ) );
			$a = $document->get( 'doClientAttachedTo' );

			if (0 < $a) {
				$doc = new Document( $a );
				$y = $doc->get( 'doClientSequence' );
				$x = sprintf( '%04s', $y );
			} 
else {
				$x = 'Not Attached';
			}

			$this->set( 'clientAttachedTo', $x );
			$a = $document->get( 'doInscoAttachedTo' );

			if (0 < $a) {
				$doc = new Document( $a );
				$y = $doc->get( 'doInscoSequence' );
				$x = sprintf( '%04s', $y );
			} 
else {
				$x = 'Not Attached';
			}

			$this->set( 'inscoAttachedTo', $x );
			$a = $document->get( 'doIntroduserAttachedTo' );

			if (0 < $a) {
				$doc = new Document( $a );
				$y = $doc->get( 'doIntroducerSequence' );
				$x = sprintf( '%04s', $y );
			} 
else {
				$x = 'Not Attached';
			}

			$this->set( 'introdAttachedTo', $x );
			$nextBy = trim( $document->get( 'nextByFirst' ) . ' ' . $document->get( 'nextByLast' ) );

			if (strlen( $nextBy ) == 0) {
				$nextBy = 'No Action Needed';
			}

			$this->set( 'nextBy', $nextBy );
			$clientSentBy = trim( $document->get( 'clientSentFirst' ) . ' ' . $document->get( 'clientSentLast' ) );

			if (strlen( $clientSentBy ) == 0) {
				$clientSentBy = 'Not Sent';
			}

			$this->set( 'clientSentBy', $clientSentBy );
			$seq = '';

			if ($this->type == 'CL') {
				$seq = $document->get( 'doClientSequence' );
				$this->set( 'detailSentWhen', $document->getForHTML( 'doClientSentWhen' ) );
				$this->set( 'detailSentHow', $document->get( 'doClientSentHow' ) );
				$this->set( 'detailSentBy', $document->get( 'doClientSentBy' ) );
				$this->set( 'detailAttachedTo', $document->get( 'doClientAttachedTo' ) );
				$this->setSentBy( $document->get( 'doClientSentBy' ) );
			}


			if ($this->type == 'PL') {
				$seq = $document->get( 'doPolicySequence' );
				$this->set( 'detailSentWhen', $document->getForHTML( 'doClientSentWhen' ) );
				$this->set( 'detailSentHow', $document->get( 'doClientSentHow' ) );
				$this->set( 'detailSentBy', $document->get( 'doClientSentBy' ) );
				$this->set( 'detailAttachedTo', $document->get( 'doClientAttachedTo' ) );
				$this->setSentBy( $document->get( 'doClientSentBy' ) );
			}


			if ($this->type == 'PT') {
				$seq = $document->get( 'doTransSequence' );
				$this->set( 'detailSentWhen', $document->getForHTML( 'doClientSentWhen' ) );
				$this->set( 'detailSentHow', $document->get( 'doClientSentHow' ) );
				$this->set( 'detailSentBy', $document->get( 'doClientSentBy' ) );
				$this->set( 'detailAttachedTo', $document->get( 'doClientAttachedTo' ) );
				$this->setSentBy( $document->get( 'doClientSentBy' ) );
				$this->set( 'detailTransNo', sprintf( '%07s', $document->get( 'doTrans' ) ) );
			}


			if ($this->type == 'IC') {
				$seq = $document->get( 'doInscoSequence' );
				$this->set( 'detailSentWhen', $document->getForHTML( 'doInscoSentWhen' ) );
				$this->set( 'detailSentHow', $document->get( 'doInscoSentHow' ) );
				$this->set( 'detailSentBy', $document->get( 'doInscoSentBy' ) );
				$this->set( 'detailAttachedTo', $document->get( 'doInscoAttachedTo' ) );
				$this->setSentBy( $document->get( 'doInscoSentBy' ) );
			}


			if ($this->type == 'IN') {
				$seq = $document->get( 'doIntroducerSequence' );
				$this->set( 'detailSentWhen', $document->getForHTML( 'doIntroducerSentWhen' ) );
				$this->set( 'detailSentHow', $document->get( 'doIntroducerSentHow' ) );
				$this->set( 'detailSentBy', $document->get( 'doIntroducerSentBy' ) );
				$this->set( 'detailAttachedTo', $document->get( 'doIntroducerAttachedTo' ) );
				$this->setSentBy( $document->get( 'doIntroducerSentBy' ) );
			}

			$this->set( 'detailSequence', sprintf( '%04s', $seq ) );
			$this->set( 'detailSubject', $document->get( 'doSubject' ) );
			$this->set( 'detailDocument', $document->get( 'doDocument' ) );
			$this->setEnteredBy( $document->get( 'doEnteredBy' ) );
			$this->setWhenEntered( $document->get( 'doWhenEntered' ) );
			$this->setOriginator( $document->get( 'doOriginator' ) );
			$this->setWhenOriginated( $document->get( 'doWhenOriginated' ) );
			$this->set( 'detailNextActionBy', $document->get( 'doNextActionBy' ) );
			$this->set( 'detailLocked', $document->get( 'doLocked' ) );
			$polNum = '';
			$plCode = $document->get( 'doPolicy' );

			if (0 < $plCode) {
				$policy = new Policy( $plCode );
				$polNum = $policy->get( 'plPolicyNumber' );
			}

			$this->set( 'detailPolicyCode', $plCode );
			$this->set( 'detailPolicyNumber', $polNum );
			$icCode = $document->get( 'doInsco' );
			$this->set( 'detailInscoCode', $icCode );
			$name = '';

			if (0 < $icCode) {
				$insco = new Insco( $icCode );
				$name = $insco->get( 'icName' );
			}

			$this->set( 'detailInsuranceCoName', $name );
			$clCode = $document->get( 'doClient' );
			$this->set( 'detailClientCode', $clCode );
			$name = '';

			if (0 < $clCode) {
				$client = new Client( $clCode );
				$name = $client->get( 'clName' );
				$this->client = &$client;

				$this->_setIntroducerFromClient(  );
			}

			$this->set( 'detailClientName', $name );
			$this->set( 'detailIntroducerCode', $document->get( 'doIntroducer' ) );
			$code = $this->get( 'introducerCodeFromClient' );
			$introdChosen = 0;
			$inCode = $document->get( 'doIntroducer' );

			if (( 0 < $inCode && $inCode == $code )) {
				$introdChosen = 1;
			}

			$this->set( 'detailIntroducerChosen', $introdChosen );
			$name = $document->get( 'doFileName' );
			$size = $document->get( 'doFileSize' );

			if (0 < $size) {
				$this->set( 'detailUploadFile', $name );
			} 
else {
				$this->set( 'detailUploadFile', '' );
			}

			$this->set( 'detailExists', true );
			$this->setDocument( $document );
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

		function _displaydocumentsusingselect($q, $text) {
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$doCode = $row['doCode'];
				$this->set( 'doCode', $doCode );
				$this->set( 'ref', sprintf( '%07d', $doCode ) );
				$this->set( 'doSubject', $row['doSubject'] );
				$doDocmType = $row['doDocmType'];

				if (0 < $doDocmType) {
					$type = new DocumentType( $doDocmType );
					$dtName = $type->get( 'dtName' );
				} 
else {
					$dtName = '';
				}

				$this->set( 'dtName', $dtName );
				$doLocked = $row['doLocked'];

				if ($doLocked == true) {
					$this->set( 'itemBackgroundColour', '#E9E9E9' );
				} 
else {
					$this->set( 'itemBackgroundColour', '#E7EAFE' );
				}

				$attachedTo = '';
				$code = 0;

				if (( ( $this->type == 'CL' || $this->type == 'PL' ) || $this->type == 'PT' )) {
					$code = $row['doClientAttachedTo'];
				}


				if ($this->type == 'IC') {
					$code = $row['doInscoAttachedTo'];
				}


				if ($this->type == 'IN') {
					$code = $row['doIntroducerAttachedTo'];
				}


				if (0 < $code) {
					$docm = new Document( $code );
					$attachedTo = 0 - 1;

					if ($this->type == 'CL') {
						$attachedTo = $docm->get( 'doClientSequence' );
					}


					if ($this->type == 'PL') {
						$attachedTo = $docm->get( 'doPolicySequence' );
					}


					if ($this->type == 'PT') {
						$attachedTo = $docm->get( 'doTransSequence' );
					}


					if ($this->type == 'IC') {
						$attachedTo = $docm->get( 'doInscoSequence' );
					}


					if ($this->type == 'IN') {
						$attachedTo = $docm->get( 'doIntroducerSequence' );
					}


					if (0 < $attachedTo) {
						$attachedTo = sprintf( '%04s', $attachedTo );
					}


					if ($this->type == 'PL') {
						$thisPolicy = $this->policy->get( 'plCode' );
						$plCode = $docm->get( 'doPolicy' );

						if (( $docm->get( 'doPolicySequence' ) < 1 || $plCode != $thisPolicy )) {
							$attachedTo = 'client docm';
						}
					}


					if ($this->type == 'PT') {
						$thisTrans = $this->transaction->get( 'ptCode' );
						$ptCode = $docm->get( 'doTrans' );

						if (( $docm->get( 'doTransSequence' ) < 1 || $ptCode != $thisTrans )) {
							$attachedTo = 'policy docm';
						}
					}
				}

				$this->set( 'attachedTo', $attachedTo );

				if ($doCode == $this->get( 'detailCode' )) {
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
					$seq = 'doClientSequence';
				}


				if ($this->type == 'PL') {
					$seq = 'doPolicySequence';
				}


				if ($this->type == 'PT') {
					$seq = 'doTransSequence';
				}


				if ($this->type == 'IC') {
					$seq = 'doInscoSequence';
				}


				if ($this->type == 'IN') {
					$seq = 'doIntroducerSequence';
				}


				if ($seq != '') {
					$this->set( 'sequence', sprintf( '%04s', $row[$seq] ) );
				}

				$on = $row['doWhenEntered'];

				if (( $on == null || strlen( $on ) == 0 )) {
					$doWhenOriginated = uformatourtimestamp( $row['doWhenOriginated'] );
					$this->set( 'doWhenEntered', $doWhenOriginated );
					$doOriginator = uformatourtimestamp( $row['doOriginator'] );
					$this->set( 'doEnteredBy', $doOriginator );
					$usCode = $row['doOriginator'];
				} 
else {
					$doWhenEntered = uformatourtimestamp( $row['doWhenEntered'] );
					$this->set( 'doWhenEntered', $doWhenEntered );
					$doEnteredBy = uformatourtimestamp( $row['doEnteredBy'] );
					$this->set( 'doEnteredBy', $doEnteredBy );
					$usCode = $row['doEnteredBy'];
				}

				$user = new User( null );

				if (( 0 < $usCode && $user->tryGettingRecord( $usCode ) )) {
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'usInitials', $usInitials );
				$doSentWhen = '';
				$doSentHow = '';

				if (( ( $this->type == 'CL' || $this->type == 'PL' ) || $this->type == 'PT' )) {
					$doSentHow = $row['doClientSentHow'];
					$doSentWhen = uformatsqldate( $row['doClientSentWhen'] );
				}


				if ($this->type == 'IC') {
					$doSentHow = $row['doInscoSentHow'];
					$doSentWhen = uformatsqldate( $row['doInscoSentWhen'] );
				}


				if ($this->type == 'IN') {
					$doSentHow = $row['doIntroducerSentHow'];
					$doSentWhen = uformatsqldate( $row['doIntroducerSentWhen'] );
				}

				$this->set( 'sentWhen', $doSentWhen );

				if ($doSentHow == 1) {
					$this->set( 'sentWhen', 'not to be sent' );
				}

				$doNextActionBy = $row['doNextActionBy'];

				if (0 < $doNextActionBy) {
					$actionNeeded = 'YES';
				} 
else {
					$actionNeeded = 'NO';
				}

				$this->set( 'actionNeeded', $actionNeeded );
				$doSysTran = $row['doSysTran'];

				if ($doSysTran == 0) {
					$doSysTran = '';
				}

				$this->set( 'transNo', $doSysTran );
				$this->set( 'size', $row['doFileSize'] );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function uploadtypevalue() {
			$p = $this->get( 'detailUploadType' );
			$out = '';

			if ($p == 1) {
				$out = 'System Generated';
			}


			if ($p == 2) {
				$out = 'Uploaded Document';
			}


			if ($p == 3) {
				$out = 'Not Uploaded - Record Only';
			}

			return $out;
		}
	}

?>
