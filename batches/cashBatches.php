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

	function _createbatch($template, $input) {
		global $session;

		$ret = '../batches/cashBatches.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '../batches/cashBatchEdit.php?newBatch' );
		return false;
	}

	function _accessbatch($template, $input) {
		global $session;

		$btCode = $input['toView'];

		if ($btCode <= 0) {
			return false;
		}

		$ret = '../batches/cashBatches.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../batches/cashBatchEdit.php?batch=' . $btCode );
		return false;
	}

	function _viewtrans($template, $input) {
		global $session;

		$ptCode = $input['transToView'];

		if ($ptCode < 1) {
			return false;
		}

		$ret = '../reports/cashBatches.php';
		$session->set( 'returnTo', $ret );
		$trans = new PolicyTransaction( $ptCode );
		$plCode = $trans->get( 'ptPolicy' );
		flocationheader( '' . '../policies/policyTransEdit.php?policy=' . $plCode . '&transToView=' . $ptCode );
	}

	function _seeifbatchbeingedited($template, $input) {
		global $session;
		$cashBatchEditTemplate = &$session->get( 'cashBatchEditTemplate' );

		if ($cashBatchEditTemplate == null) {
			return false;
		}


		if ($cashBatchEditTemplate->getAllowEditing(  ) == true) {
			flocationheader( '../batches/cashBatchEdit.php' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$cashBatchesTemplate = &$session->get( 'cashBatchesTemplate' );

	if ($cashBatchesTemplate == null) {
		$cashBatchesTemplate = new CashBatchesTemplate( 'cashBatches.html' );
		$cashBatchesTemplate->setProcess( '_createBatch', 'createBatch' );
		$cashBatchesTemplate->setProcess( '_accessBatch', 'toView' );
		$cashBatchesTemplate->setOneOffFunctionToCall( '_seeIfBatchBeingEdited' );
		$cashBatchesTemplate->setReturnTo( '../batches/cashBatches.php' );
	}

	$session->set( 'cashBatchesTemplate', $cashBatchesTemplate );
	$cashBatchesTemplate->process(  );
	$session->set( 'cashBatchesTemplate', $cashBatchesTemplate );
?>