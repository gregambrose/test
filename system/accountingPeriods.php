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

	function _createyear($template, $input) {
		$year = new AccountingYear( null );
		$year->insert( null );
		$ayCode = $year->getKeyValue(  );
		$p = 1;

		while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
			$ap = new AccountingPeriod( null );
			$ap->set( 'apPeriod', $p );
			$ap->set( 'apName', $p );
			$ap->set( 'apYear', $ayCode );
			$ap->insert( null );
			++$p;
		}

		$template->setYear( $ayCode );
		$template->setAllowEditing( true );
		$template->setAllowExiting( false );
		$template->setNewRecord( true );
		return false;
	}

	function _accessyear($template, $input) {
		$ayCode = $input['years'];

		if ($ayCode <= 0) {
			return false;
		}

		$template->setYear( $ayCode );
		$template->setNewRecord( false );
		return false;
	}

	function _doupdate($template, $input) {
		$year = &$template->getYear(  );

		if ($year->recordExists(  ) == false) {
			trigger_error( 'cant get year', E_USER_ERROR );
		}

		$year->setAll( $input );
		$messg = $template->validate( $input );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$template->updatePeriods( $input );
		$ok = $year->update(  );

		if ($ok == false) {
			trigger_error( 'cant update year', E_USER_ERROR );
		}

		$template->set( 'message', 'year updated' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		unset( $template[year] );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	require( '../include/startup.php' );
	$accountingPeriodsTemplate = &$session->get( 'accountingPeriodsTemplate' );

	if ($accountingPeriodsTemplate == null) {
		$accountingPeriodsTemplate = new AccountingPeriodsTemplate( 'accountingPeriods.html' );
		$accountingPeriodsTemplate->setProcess( '_createYear', 'createYear' );
		$accountingPeriodsTemplate->setProcess( '_accessYear', 'accessYear' );
		$accountingPeriodsTemplate->setProcess( '_doUpdate', 'update' );
		$accountingPeriodsTemplate->setProcess( '_doCancel', 'cancel' );
	}

	$session->set( 'accountingPeriodsTemplate', $accountingPeriodsTemplate );
	$accountingPeriodsTemplate->process(  );
	$session->set( 'accountingPeriodsTemplate', $accountingPeriodsTemplate );
?>