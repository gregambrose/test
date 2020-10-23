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

	function _dosearch($template, $input) {
		$q = 'SELECT itCode FROM inscoTransactions
				WHERE itTransType = \'C\'
				AND	 itBalance != 0
				ORDER BY itInsCo';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$transactions = array(  );

		while ($row = udbgetrow( $result )) {
			$transactions[] = $row['itCode'];
		}

		$template->transactions = $transactions;
		$found = count( $transactions );
		$template->set( 'transFound', $found );

		if ($found == 0) {
			$template->transactions = null;
			$template->setMessage( 'no transactions found' );
		}

		return false;
	}

	function _doreconcile($template, $input) {
		global $session;

		$itCode = $input['doReconcile'];

		if ($itCode < 1) {
			return false;
		}

		$ret = '../reports/insCoUnallocated.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoRecon.php?reconcile=' . $itCode );
	}

	require( '../include/startup.php' );
	$insCoUnallocatedTemplate = &$session->get( 'insCoUnallocatedTemplate' );

	if ($insCoUnallocatedTemplate == null) {
		$insCoUnallocatedTemplate = new InsCoUnallocatedTemplate( 'insCoUnallocated.html' );
		$insCoUnallocatedTemplate->setProcess( '_doSearch', 'doSearch' );
		$insCoUnallocatedTemplate->setProcess( '_doReconcile', 'doReconcile' );
		$insCoUnallocatedTemplate->setReturnTo( '../reports/insCoUnallocated.php' );
		$input = array(  );
		_dosearch( $insCoUnallocatedTemplate, $input );
	}

	$session->set( 'insCoUnallocatedTemplate', $insCoUnallocatedTemplate );
	$insCoUnallocatedTemplate->process(  );
	$session->set( 'insCoUnallocatedTemplate', $insCoUnallocatedTemplate );
?>