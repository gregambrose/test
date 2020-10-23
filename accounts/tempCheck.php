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

	function _prepareresults() {
		$q = 'ALTER TABLE inscoTransactions ADD COLUMN  itTempTrans	INT';
		_doquery( 'add itTempTrans	', $q );
		$q = 'ALTER TABLE inscoTransactions ADD COLUMN  itTempValue	BIGINT';
		_doquery( 'add itTempTrans	', $q );
		$q = 'UPDATE inscoTransactions SET  itTempTrans	= 0,  itTempValue	= 0';
		$messg = _doquery( 'add itTempTrans	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue - (ptAddOnNet + ptAddOnIPT)
		WHERE ptAddOnInsCoTran = itCode
		AND (ptAddOnNet != 0 OR ptAddOnIPT != 0)
		AND  ptPostStatus = \'P\'
		AND	 ptDebit = 1
		AND	 ptDirect = 1';
		$messg = _doquery( 'direct	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue - (
				ptNet +
				ptGrossIPT +
				ptAddlNet +
				ptAddlIPT +
				ptEngineeringFeeNet +
				ptEngineeringFeeVAT)
		WHERE ptMainInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit = 1
		AND	  ptDirect != 1';
		$messg = _doquery( 'indirect	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue - (
				ptAddOnNet +
				ptAddOnIPT )
		WHERE ptAddOnInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit = 1
		AND	  ptDirect != 1';
		$messg = _doquery( 'indirect	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue + (ptAddOnNet + ptAddOnIPT)
		WHERE ptAddOnInsCoTran= itCode
		AND  ptPostStatus = \'P\'
		AND	 ptDebit != 1
		AND	 ptDirect = 1';
		$messg = _doquery( 'direct	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = + (itTempValue +
				ptNet +
				ptGrossIPT +
				ptAddlNet +
				ptAddlIPT +
				ptEngineeringFeeNet +
				ptEngineeringFeeVAT)
		WHERE ptMainInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit != 1
		AND	  ptDirect != 1';
		$messg = _doquery( 'indirect	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = + (itTempValue +
				ptAddOnNet +
				ptAddOnIPT)
		WHERE ptAddOnInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit != 1
		AND	  ptDirect != 1';
		$messg = _doquery( 'indirect	', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue + (
			ptCommission +
			ptAddlCommission +
			ptEngineeringFeeComm)
		WHERE ptMainInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit = 1
		AND	  ptDirect = 1';
		$messg = _doquery( 'direct	debit comm', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions, policyTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue -
			(ptCommission +
			ptAddlCommission +
			ptEngineeringFeeComm)
		WHERE ptMainInsCoTran = itCode
		AND  ptPostStatus = \'P\'
		AND	  ptDebit != 1
		AND	  ptDirect = 1';
		$messg = _doquery( 'direct	debit comm', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue - itOriginal
		WHERE (itTransType = \'C\' OR itTransType = \'R\')';
		$messg = _doquery( 'direct	debit comm', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

		$q = 'UPDATE inscoTransactions
		SET itTempTrans = iTtempTrans + 1, itTempValue = itTempValue - itWrittenOff
	WHERE itTransType = \'I\'
	AND itWrittenOff != 0';
		$messg = _doquery( 'direct	debit comm', $q );

		if ($messg != null) {
			trigger_error( $messg, E_USER_ERROR );
		}

	}

	function _doquery($text, $q) {
		$result = mysql_query( $q );

		if ($result === true) {
			return null;
		}

		$err = mysql_error(  );
		return '' . 'FAILED  ' . $text . ' : error was ' . $err . ' <br>';
	}

	require( '../include/startup.php' );
	_prepareresults(  );
	$tempCheckTemplate = &$session->get( 'tempCheckTemplate' );

	if ($tempCheckTemplate == null) {
		$tempCheckTemplate = new TempCheckTemplate( 'tempCheck.html' );
	}

	$session->set( 'tempCheckTemplate', $tempCheckTemplate );
	$tempCheckTemplate->process(  );
	$session->set( 'tempCheckTemplate', $tempCheckTemplate );
?>