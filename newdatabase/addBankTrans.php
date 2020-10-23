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

	require_once( '../include/startup.php' );
	$q = 'SELECT btCode FROM cashBatches WHERE btAccountingYear IS NULL OR btAccountingYear = 0';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$btCode = $row['btCode'];
		$cb = new CashBatch( $btCode );
		$btBatchDate = $cb->get( 'btBatchDate' );
		$perYr = fgetaccountingperiodandyear( $btBatchDate );

		if ($perYr == null) {
			continue;
		}

		$per = $perYr['period'];
		$yr = $perYr['year'];
		$cb->set( 'btAccountingYear', $yr );
		$cb->set( 'btAccountingPeriod', $per );
		$cb->update(  );
	}

	$q = 'SELECT itCode FROM inscoTransactions WHERE itTransType = \'C\' OR  itTransType = \'R\'
		AND itCashBatch = 0';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$itCode = $row['itCode'];
		$q = '' . 'SELECT baCode FROM bankAccountTrans WHERE baType = 15 AND baTran = ' . $itCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );
		$it = new InsCoTransaction( $itCode );
		$itCashBatch = $it->get( 'itCashBatch' );

		if (0 < $itCashBatch) {
			continue;
		}

		$bt = new BankAccountTran( null );
		$bt->set( 'baType', 15 );
		$bt->set( 'baTran', $it->getKeyValue(  ) );
		$bt->set( 'baDebit', 1 );
		$bt->set( 'baAmount', 0 - $it->get( 'itPaid' ) );
		$bt->set( 'baPostingRef', $it->get( 'itChequeNo' ) );
		$bt->set( 'baPaymentType', $it->get( 'itPaymentType' ) );
		$bt->set( 'baPostingDate', $it->get( 'itPostingDate' ) );
		$bt->set( 'baAccountingYear', $it->get( 'itAccountingYear' ) );
		$bt->set( 'baAccountingPeriod', $it->get( 'itAccountingPeriod' ) );
		$bt->set( 'baCreatedBy', $it->get( 'itCreatedBy' ) );
		$bt->set( 'baCreatedOn', $it->get( 'itCreatedOn' ) );
		$bt->insert( null );
		echo '' . 'bank for ic tran ' . $itCode . ' added <br>';
	}

	$q = 'SELECT btCode FROM cashBatches WHERE btLocked = 1';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$btCode = $row['btCode'];
		$q = '' . 'SELECT baCode FROM bankAccountTrans WHERE baType = 13 AND baTran = ' . $btCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );
		$cb = new CashBatch( $btCode );
		$ba = new BankAccountTran( null );
		$amt = 0 - $cb->get( 'btTotal' );
		$ba->set( 'baType', 13 );
		$ba->set( 'baDebit', 0 );
		$ba->set( 'baTran', $cb->getKeyValue(  ) );
		$ba->set( 'baAmount', $amt );
		$ba->set( 'baPostingRef', $cb->get( 'btPayInSlip' ) );
		$ba->set( 'baPostingDate', $cb->get( 'btBatchDate' ) );
		$ba->set( 'baAccountingYear', $cb->get( 'btAccountingYear' ) );
		$ba->set( 'baAccountingPeriod', $cb->get( 'btAccountingPeriod' ) );
		$ba->set( 'baCreatedBy', $cb->get( 'btWhoPosted' ) );
		$ba->set( 'baCreatedOn', $cb->get( 'btWhenPosted' ) );
		$ba->insert( null );
		echo '' . 'bank for cash batch  ' . $btCode . ' added <br>';
	}

	$q = 'SELECT ctCode FROM clientTransactions WHERE ctTransType = \'C\' AND (ctCashBatch IS NULL OR ctCashBatch = 0)';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$ctCode = $row['ctCode'];
		$q = '' . 'SELECT baCode FROM bankAccountTrans WHERE baType = 14 AND baTran = ' . $ctCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );
		$ct = new ClientTransaction( $ctCode );
		$ctCashBatch = $ct->get( 'ctCashBatch' );

		if (0 < $ctCashBatch) {
			continue;
		}

		$ba = new BankAccountTran( null );
		$amt = 0 - $ct->get( 'ctOriginal' );
		$ba->set( 'baType', 14 );
		$ba->set( 'baTran', $ctCode );
		$ba->set( 'baDebit', 0 );
		$ba->set( 'baAmount', 0 - $amt );
		$ba->set( 'baPostingRef', $ct->get( 'ctChequeNo' ) );
		$ba->set( 'baPaymentType', $ct->get( 'ctPaymentMethod' ) );
		$ba->set( 'baPostingDate', $ct->get( 'ctPostingDate' ) );
		$ba->set( 'baAccountingYear', $ct->get( 'ctAccountingYear' ) );
		$ba->set( 'baAccountingPeriod', $ct->get( 'ctAccountingPeriod' ) );
		$ba->set( 'baCreatedBy', $ct->get( 'ctWhoPosted' ) );
		$ba->set( 'baCreatedOn', $ct->get( 'ctWhenPosted' ) );
		$ba->insert( null );
		echo '' . 'bank for cl cash paid  ' . $ctCode . ' added <br>';
	}

	$q = 'UPDATE policyTransactions SET ptStatusDate = ptPostingDate WHERE ptStatusDate IS NULL OR ptStatusDate = \'\'';
	$result2 = mysql_query( $q );

	if ($result2 == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	echo 'status date corrected';
	echo 'ALL DONE';
?>