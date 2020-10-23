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

	function _dorefresh($template, $input) {
		return false;
	}

	function _viewdocm($template, $input) {
		global $session;

		$doCode = $input['viewDocm'];

		if ($doCode < 1) {
			return false;
		}

		$ret = '../messaging/outstandingMessages.php';
		$session->set( 'returnTo', $ret );
		$doc = new Document( $doCode );
		$doClient = $doc->get( 'doClient' );
		$doPolicy = $doc->get( 'doPolicy' );
		$doInsco = $doc->get( 'doInsco' );
		$doIntroducer = $doc->get( 'doIntroducer' );

		if (0 < $doClient) {
			flocationheader( '' . '../clients/clientDocms.php?client=' . $doClient . '&clientDocument=' . $doCode );
			return null;
		}


		if (0 < $doPolicy) {
			flocationheader( '' . '../policies/policyDocms.php?policy=' . $doPolicy . '&policyDocument=' . $doCode );
			return null;
		}


		if (0 < $doInsco) {
			flocationheader( '' . '../inscos/inscoDocms.php?insco=' . $doInsco . '&inscoDocument=' . $doCode );
			return null;
		}


		if (0 < $doIntroducer) {
			flocationheader( '' . '../introducers/introducerDocms.php?introducer=' . $doIntroducer . '&introducerDocument=' . $doCode );
		}

	}

	function _viewnote($template, $input) {
		global $session;

		$noCode = $input['viewNote'];

		if ($noCode < 1) {
			return false;
		}

		$ret = '../messaging/outstandingMessages.php';
		$session->set( 'returnTo', $ret );
		$note = new Note( $noCode );
		$noClient = $note->get( 'noClient' );
		$noPolicy = $note->get( 'noPolicy' );
		$noInsco = $note->get( 'noInsco' );
		$noIntroducer = $note->get( 'noIntroducer' );

		if (0 < $noClient) {
			flocationheader( '' . '../clients/clientNotes.php?client=' . $noClient . '&clientNote=' . $noCode );
			return null;
		}


		if (0 < $noPolicy) {
			flocationheader( '' . '../policies/policyNotes.php?policy=' . $noPolicy . '&policyNote=' . $noCode );
			return null;
		}


		if (0 < $noInsco) {
			flocationheader( '' . '../inscos/inscoNotes.php?insco=' . $noInsco . '&inscoNote=' . $noCode );
			return null;
		}


		if (0 < $noIntroducer) {
			flocationheader( '' . '../introducers/introducerNotes.php?introducer=' . $noIntroducer . '&introducerNote=' . $noCode );
		}

	}

	require( '../include/startup.php' );
	$outstandingMessagesTemplate = &$session->get( 'outstandingMessagesTemplate' );

	if ($outstandingMessagesTemplate == null) {
		$outstandingMessagesTemplate = new OutstandingMessagesTemplate( 'outstandingMessages.html' );
		$outstandingMessagesTemplate->setProcess( '_doRefresh', 'refresh' );
		$outstandingMessagesTemplate->setProcess( '_viewDocm', 'viewDocm' );
		$outstandingMessagesTemplate->setProcess( '_viewNote', 'viewNote' );
		$outstandingMessagesTemplate->setReturnTo( '../messaging/outstandingMessages.php' );
	}

	$session->set( 'outstandingMessagesTemplate', $outstandingMessagesTemplate );
	$outstandingMessagesTemplate->process(  );
	$session->set( 'outstandingMessagesTemplate', $outstandingMessagesTemplate );
?>