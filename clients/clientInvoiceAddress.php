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
		flocationheader( $url );
	}

	function _doupdate($template, $input) {
		$ok = _updateclient( $template, $input );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$template->set( 'message', 'client updated' );
		$clCode = $template->get( 'clCode' );
		$template->setClient( $clCode );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$clCode = $template->get( 'clCode' );
		$template->setClient( $clCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _updateclient($template, $input) {
		$client = &$template->getClient(  );

		if ($client->recordExists(  ) == false) {
			trigger_error( 'cant get client', E_USER_ERROR );
		}

		$client->setAll( $input );
		$ok = $client->update(  );

		if ($ok == false) {
			$clCode = $template->get( 'clCode' );
			$template->setClient( $clCode );
			$template->setMessage( 'Sorry...Someone else has amended this client. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAll( $client->getAllForHTML(  ) );
		return $ok;
	}

	require( '../include/startup.php' );
	$clientInvoiceAddressTemplate = &$session->get( 'clientInvoiceAddressTemplate' );

	if ($clientInvoiceAddressTemplate == null) {
		$clientInvoiceAddressTemplate = new ClientInvoiceAddressTemplate( 'clientInvoiceAddress.html' );
		$clientInvoiceAddressTemplate->setProcess( '_doUpdate', 'update' );
		$clientInvoiceAddressTemplate->setProcess( '_doCancel', 'cancel' );
		$clientInvoiceAddressTemplate->setProcess( '_goBack', 'back' );
	}


	if (isset( $_GET['amendClient'] )) {
		$clCode = $_GET['amendClient'];
		$clientInvoiceAddressTemplate->set( 'clCode', $clCode );
		$client = new Client( $clCode );
		$clientInvoiceAddressTemplate->setClient( $clCode );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$clientInvoiceAddressEditTemplate->setReturnTo( $returnTo );
	}

	$clCode = $clientInvoiceAddressTemplate->get( 'clCode' );
	$clientInvoiceAddressTemplate->setReturnTo( '' . 'clientEdit.php?amendClient=' . $clCode );
	$session->set( 'clientInvoiceAddressTemplate', $clientInvoiceAddressTemplate );
	$clientInvoiceAddressTemplate->process(  );
	$session->set( 'clientInvoiceAddressTemplate', $clientInvoiceAddressTemplate );
?>