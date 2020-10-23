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
		$detailUploadType = $template->get( 'detailUploadType' );
		$detailSubject = $template->get( 'detailSubject' );
		$detailInscoCode = $template->get( 'detailInscoCode' );
		$introducerCodeFromClient = $template->get( 'introducerCodeFromClient' );
		$detailIntroducerCode = $template->get( 'detailIntroducerCode' );
		$detailIntroducerChosen = $template->get( 'detailIntroducerChosen' );
		$detailAttachedTo = $template->get( 'detailAttachedTo' );

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'cant get user for docms', E_USER_WARNING );
			}
		}

		$detailCode = $template->get( 'detailCode' );

		if ($detailCode <= 0) {
			$exists = false;
		} 
else {
			$exists = true;
		}

		$document = $template->getDocument(  );

		if ($document != null) {
			$document = $template->getDocument(  );
			$doLocked = $document->get( 'doLocked' );

			if ($doLocked == 1) {
				trigger_error( 'cant amend locked document', E_USER_ERROR );
			}
		} 
else {
			$document = new Document( null );
			$template->setDocument( $document );
		}


		if ($exists == true) {
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
		} 
else {
			$document->set( 'doWhenOriginated', ugettimenow(  ) );
			$document->set( 'doOriginator', $usCode );
		}

		$document->getUploadedDocument( 'detailUpload' );
		$template->set( 'detailUploadFile', $document->get( 'doFileName' ) );
		$template->set( 'detailUploadSize', $document->get( 'doFileSize' ) );
		$trans = $template->getTransaction(  );
		$ptCode = $trans->getKeyValue(  );
		$ptSysTran = $trans->get( 'ptSysTran' );
		$policy = $template->getPolicy(  );
		$plCode = $policy->getKeyValue(  );
		$clCode = $policy->get( 'plClient' );

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'cant get user for docms', E_USER_WARNING );
			}
		}

		$detailNextActionBy = $template->get( 'detailNextActionBy' );
		$document->set( 'doTrans', $ptCode );
		$document->set( 'doSysTran', $ptSysTran );
		$document->set( 'doPolicy', $plCode );
		$document->set( 'doClient', $clCode );
		$document->set( 'doDocmType', $detailType );
		$document->set( 'doSubject', $detailSubject );
		$document->set( 'doNextActionBy', $detailNextActionBy );
		$document->set( 'doUploadType', $detailUploadType );
		$document->set( 'doClientAttachedTo', $detailAttachedTo );

		if ($detailIntroducerChosen == 'on') {
			$inCode = $introducerCodeFromClient;
		} 
else {
			$inCode = 0;
		}

		$document->set( 'doIntroducer', $inCode );
		$template->set( 'detailIntroducerCode', $inCode );

		if (0 < $detailInscoCode) {
			$icCode = $detailInscoCode;
		} 
else {
			$icCode = 0;
		}

		$document->set( 'doInsco', $icCode );

		if ($detailUploadType == 1) {
			$template->setMessage( 'you cannot select this upload type' );
			return false;
		}


		if (( $detailUploadType != 2 && $detailUploadType != 3 )) {
			$template->setMessage( 'you need to select an upload type' );
			return false;
		}


		if ($detailType <= 0) {
			$template->setMessage( 'you need to select a document type' );
			return false;
		}


		if (strlen( trim( $detailSubject ) ) == 0) {
			$template->setMessage( 'you need to select a subject' );
			return false;
		}

		$detailUploadSize = $document->get( 'doFileSize' );

		if (( $detailUploadType == 2 && $detailUploadSize == 0 )) {
			$template->setMessage( 'you haven\'t uploaded a document' );
			return false;
		}


		if (( $detailUploadType == 3 && 0 < $detailUploadSize )) {
			$template->setMessage( 'you have selected record only but have uploaded a file' );
			return false;
		}

		$template->setWhenEntered( $document->get( 'doWhenEntered' ) );
		$template->setEnteredBy( $document->get( 'doEnteredBy' ) );
		$template->setWhenOriginated( $document->get( 'doWhenOriginated' ) );
		$template->setOriginator( $document->get( 'doOriginator' ) );
		$document->set( 'doUpdateorCreate', ugettimenow(  ) );

		if ($exists) {
			$document->correctDocumentName(  );
			$template->set( 'detailUploadFile', $document->get( 'doFileName' ) );
			$ok = $document->update(  );

			if ($ok == false) {
				$document->refresh(  );
				$template->setAll( $document->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this document. You will need to re-enter any changes you made' );
				$doCode = $document->getKeyValue(  );
				$template->selectDocumentToDisplay( $doCode );
				return false;
			}

			$template->setMessage( 'document updated' );
		} 
else {
			$document->insert( null );
			$document->set( 'doPolicy', $plCode );
			$document->setTransSequence(  );
			$document->setPolicySequence(  );

			if (0 < $clCode) {
				$document->setClientSequence(  );
			}


			if (0 < $icCode) {
				$document->setInscoSequence(  );
			}


			if (0 < $inCode) {
				$document->setIntroducerSequence(  );
			}

			$document->correctDocumentName(  );
			$template->set( 'detailUploadFile', $document->get( 'doFileName' ) );
			$document->update(  );
			$detailCode = $document->getKeyValue(  );
			$template->set( 'detailCode', $detailCode );
			$template->setSequence( $document->get( 'doPolicySequence' ) );
			$template->setMessage( 'new document created' );
			$template->setAll( $document->getAllForHTML(  ) );
		}

		$document->refresh(  );
		$template->setDocument( $document );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	require( '../include/startup.php' );
	$policyTransDocmsTemplate = &$session->get( 'policyTransDocmsTemplate' );

	if ($policyTransDocmsTemplate == null) {
		$policyTransDocmsTemplate = new PolicyTransDocmsTemplate( 'policyTransDocms.html' );
		$policyTransDocmsTemplate->setProcess( '_doUpdate', 'updateDocm' );
		$policyTransDocmsTemplate->setProcess( '_goBack', 'back' );
	}


	if (isset( $_GET['trans'] )) {
		$ptCode = $_GET['trans'];
		$policyTransDocmsTemplate->setTransaction( $ptCode );
	}


	if (isset( $_GET['policyDocument'] )) {
		$doCode = $_GET['policyDocument'];
		$policyTransDocmsTemplate->selectDocumentToDisplay( $doCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$policyTransDocmsTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'policyTransDocmsTemplate', $policyTransDocmsTemplate );
	$policyTransDocmsTemplate->process(  );
	$session->set( 'policyTransDocmsTemplate', $policyTransDocmsTemplate );
?>