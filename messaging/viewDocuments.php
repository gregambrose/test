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

	function _viewdocm($template, $input) {
		global $session;

		$doCode = $input['docmNo'];

		if ($doCode < 1) {
			$template->setMessage( 'invalid document number' );
			return false;
		}

		$ret = '../messaging/viewDocuments.php';
		$session->set( 'returnTo', $ret );
		$doc = new Document( null );
		$ok = $doc->tryGettingRecord( $doCode );

		if ($ok == false) {
			$template->setMessage( 'invalid document number' );
			return false;
		}

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

	require( '../include/startup.php' );
	$viewDocumentsTemplate = &$session->get( 'viewDocumentsTemplate' );

	if ($viewDocumentsTemplate == null) {
		$viewDocumentsTemplate = new ViewDocumentsTemplate( 'viewDocuments.html' );
		$viewDocumentsTemplate->setProcess( '_viewDocm', 'viewDocument' );
	}

	$session->set( 'viewDocumentsTemplate', $viewDocumentsTemplate );
	$viewDocumentsTemplate->process(  );
	$session->set( 'viewDocumentsTemplate', $viewDocumentsTemplate );
?>