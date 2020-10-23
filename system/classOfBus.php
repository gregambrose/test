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

	function _domoveup($template, $input) {
		$code = $input['upCode'];

		if ($code < 1) {
			return false;
		}

		fmoveup( 'classOfBus', 'cbCode', 'cbSequence', null, $code );
		return false;
	}

	function _domovedown($template, $input) {
		$code = $input['downCode'];

		if ($code < 1) {
			return false;
		}

		fmovedown( 'classOfBus', 'cbCode', 'cbSequence', null, $code );
		return false;
	}

	function _viewcob($template, $input) {
		$cbCode = $input['viewCode'];

		if ($cbCode < 1) {
			return false;
		}

		$template->setCOB( $cbCode );
		return false;
	}

	function _doupdate($template, $input) {
		$template->updateCOB( $input );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docreate($template, $input) {
		$template->setMessage( 'new class of business created' );
		$cob = new Cob( null );
		$cob->insert( null );
		$cbCode = $cob->getKeyValue(  );
		$template->setCOB( $cbCode );
		$template->whenEditRequested( $template, $input );
		return false;
	}

	function _dodelete($template, $input) {
		if (!isset( $template->cob )) {
			$template->setMessage( 'no class of business selected to delete' );
			return false;
		}

		$cbCode = $template->cob->getKeyValue(  );
		$q = '' . 'SELECT COUNT(plCode) AS cobs FROM policies WHERE plClassOfBus = ' . $cbCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$cobs = $row['cobs'];

		if (0 < $cobs) {
			$template->setMessage( 'class of business cant be deleted - it is used on policies' );
			return false;
		}

		$cob = new Cob( $cbCode );

		if ($cob->delete(  ) == false) {
			$template->setMessage( 'sorry ...class of business cant be deleted' );
			return false;
		}

		$template->setMessage( 'class of business deleted' );
		$template->clearCOB(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	require( '../include/startup.php' );
	$classOfBusTemplate = &$session->get( 'classOfBusTemplate' );

	if ($classOfBusTemplate == null) {
		$classOfBusTemplate = new ClassOfBusTemplate( 'classOfBus.html' );
		$classOfBusTemplate->setProcess( '_doUpdate', 'update' );
		$classOfBusTemplate->setProcess( '_doCreate', 'createNew' );
		$classOfBusTemplate->setProcess( '_doCancel', 'cancel' );
		$classOfBusTemplate->setProcess( '_doDelete', 'delete' );
		$classOfBusTemplate->setProcess( '_viewCOB', 'view' );
		$classOfBusTemplate->setProcess( '_doMoveUp', 'up' );
		$classOfBusTemplate->setProcess( '_doMoveDown', 'down' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$classOfBusTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'classOfBusTemplate', $classOfBusTemplate );
	$classOfBusTemplate->process(  );
	$session->set( 'classOfBusTemplate', $classOfBusTemplate );
?>