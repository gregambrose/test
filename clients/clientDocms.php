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

	function _clientmaindetails($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$client = $template->getClient(  );
		$clCode = $client->getKeyValue(  );
		$ret = './clientDocms.php';
		$session->set( 'returnTo', $ret );
		$url = '' . './clientEdit.php?amendClient=' . $clCode;
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
		$client = $template->getClient(  );
		$clCode = $client->get( 'clCode' );

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
			$doCode = $document->getKeyValue(  );
			$document->correctDocumentName(  );
			$template->set( 'detailUploadFile', $document->get( 'doFileName' ) );
			$ok = $document->update(  );

			if ($ok == false) {
				$document->refresh(  );
				$document->fetchExtraColumns(  );
				$template->setAll( $document->getAllForHTML(  ) );
				$template->setMessage( 'Sorry...Someone else has amended this document. You will need to re-enter any changes you made' );
				$doCode = $document->getKeyValue(  );
				$template->selectDocumentToDisplay( $doCode );
				return false;
			}

			$template->selectDocumentToDisplay( $doCode );
			$template->setMessage( 'document updated' );
		} 
else {
			$document->insert( null );
			$document->set( 'doClient', $clCode );
			$document->setClientSequence(  );

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
			$template->setSequence( $document->get( 'doClientSequence' ) );
			$template->setMessage( 'new document created' );
			$template->setAll( $document->getAllForHTML(  ) );
		}

		$document->refresh(  );
		$document->fetchExtraColumns(  );
		$template->setAll( $document->getAllForHTML(  ) );
		$template->setDocument( $document );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return true;
	}

	require( '../include/startup.php' );
	$clientDocmsTemplate = &$session->get( 'clientDocmsTemplate' );

	if ($clientDocmsTemplate == null) {
		$clientDocmsTemplate = new ClientDocmsTemplate( 'clientDocms.html' );
		$clientDocmsTemplate->setProcess( '_doUpdate', 'updateDocm' );
		$clientDocmsTemplate->setProcess( '_goBack', 'back' );
		$clientDocmsTemplate->setProcess( '_clientMainDetails', 'clientMain' );
	}


	if (isset( $_GET['client'] )) {
		$clCode = $_GET['client'];
		$clientDocmsTemplate->setClient( $clCode );
	}


	if (isset( $_GET['clientDocument'] )) {
		$doCode = $_GET['clientDocument'];
		$clientDocmsTemplate->selectDocumentToDisplay( $doCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$clientDocmsTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'clientDocmsTemplate', $clientDocmsTemplate );
	$clientDocmsTemplate->process(  );
	$session->set( 'clientDocmsTemplate', $clientDocmsTemplate );
?>