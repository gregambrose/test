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

	function _viewtrans($template, $input) {
		global $session;

		$ptCode = $input['transToView'];

		if ($ptCode < 1) {
			return false;
		}

		$ret = '../reports/unposted.php';
		$session->set( 'returnTo', $ret );
		$trans = new PolicyTransaction( $ptCode );
		$plCode = $trans->get( 'ptPolicy' );
		flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
	}

	require( '../include/startup.php' );
	$unpostedTemplate = &$session->get( 'unpostedTemplate' );

	if ($unpostedTemplate == null) {
		$unpostedTemplate = new UnpostedTemplate( 'unposted.html' );
		$unpostedTemplate->setProcess( '_doRefresh', 'refresh' );
		$unpostedTemplate->setProcess( '_viewTrans', 'transToView' );
		$unpostedTemplate->setReturnTo( '../reports/unposted.php' );
	}

	$session->set( 'unpostedTemplate', $unpostedTemplate );
	$unpostedTemplate->process(  );
	$session->set( 'unpostedTemplate', $unpostedTemplate );
	echo '			';
?>