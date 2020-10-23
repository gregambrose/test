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
	$q = 'ALTER TABLE clientTransactions ADD COLUMN  ctTemp	INT ';
	$result = udbquery( $q );
	$q = 'ALTER TABLE inscoTransactions ADD COLUMN  itTemp	INT ';
	$result = udbquery( $q );
	$q = 'ALTER TABLE introducerTransactions ADD COLUMN  rtTemp	INT ';
	$result = udbquery( $q );
	$q = 'UPDATE  clientTransactions SET ctTemp = 0 ';
	$result = udbquery( $q );

	if ($result == false) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE  inscoTransactions SET itTemp = 0 ';
	$result = udbquery( $q );

	if ($result == false) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE  introducerTransactions SET rtTemp = 0 ';
	$result = udbquery( $q );

	if ($result == false) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'SELECT btCode FROM cashBatches WHERE btLocked = 1';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$btCode = $row['btCode'];
		$cb = new CashBatch( $btCode );
		$btCode = $cb->getKeyValue(  );
		$q = '' . 'SELECT biCode FROM cashBatchItems WHERE biBatch = ' . $btCode;
		$result2 = udbquery( $q );

		if ($result2 == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		while ($row = mysql_fetch_array( $result2 )) {
			$biCode = $row['biCode'];
			$ci = new CashBatchItem( $biCode );
			$biPayeeType = $ci->get( 'biPayeeType' );
			$biTrans = $ci->get( 'biTrans' );
			$biAmount = $ci->get( 'biAmount' );

			if ($biPayeeType == 'C') {
				$ct = new ClientTransaction( $biTrans );
				$ctOriginal = $ct->get( 'ctOriginal' );

				if ($ctOriginal != 0 - $biAmount) {
					print ( '' . '<br>Client item mismatch ' . $btCode . ' ' . $biTrans . ' ' . $biAmount . ' ' . $ctOriginal . '
' );
				}

				$ctCashBatch = $ct->get( 'ctCashBatch' );
				$ctCashBatchItem = $ct->get( 'ctCashBatchItem' );

				if ($ctCashBatch != $btCode) {
					print '' . '<br>Client item not pointing back to batch ' . $btCode . ' ' . $ctCashBatch . ' ' . $biTrans . ' 
';
				}


				if ($ctCashBatchItem != $biCode) {
					print '' . '<br>Client item not pointing backbatch item  ' . $btCode . ' ' . $biCode . ' ' . $ctCashBatchItem . ' ' . $biTrans . ' 
';
				}

				$ct->set( 'ctTemp', 1 );
				$ct->update(  );
				continue;
			}


			if ($biPayeeType == 'I') {
				if ($biTrans == 0) {
					break;
				}

				$it = new InscoTransaction( $biTrans );
				$itOriginal = $it->get( 'itOriginal' );

				if ($itOriginal != $biAmount) {
					print ( '' . '<br>Insco item mismatch ' . $btCode . ' ' . $biTrans . ' ' . $biAmount . ' ' . $itOriginal . '
' );
				}

				$itCashBatch = $it->get( 'itCashBatch' );
				$itCashBatchItem = $it->get( 'itCashBatchItem' );

				if ($itCashBatch != $btCode) {
					print '' . '<br>Insco item not pointing back to batch ' . $btCode . ' ' . $itCashBatch . ' ' . $biTrans . ' 
';
				}


				if ($itCashBatchItem != $biCode) {
					print '' . '<br>Insco item not pointing backbatch item  ' . $btCode . ' ' . $biCode . ' ' . $itCashBatchItem . ' ' . $biTrans . ' 
';
				}

				$it->set( 'itTemp', 1 );
				$it->update(  );
				continue;
			}


			if ($biPayeeType == 'N') {
				$rt = new IntroducerTransaction( $biTrans );
				$rtOriginal = $rt->get( 'rtOriginal' );

				if ($rtOriginal != $biAmount) {
					print ( '' . '<br>Introd item mismatch ' . $btCode . ' ' . $biTrans . ' ' . $biAmount . ' ' . $rtOriginal . '
' );
				}

				$rtCashBatch = $rt->get( 'rtCashBatch' );
				$rtCashBatchItem = $rt->get( 'rtCashBatchItem' );

				if ($rtCashBatch != $btCode) {
					print '' . '<br>Introd item not pointing back to batch ' . $btCode . ' ' . $rtCashBatch . ' ' . $biTrans . ' 
';
				}


				if ($rtCashBatchItem != $biCode) {
					print '' . '<br>Introd item not pointing backbatch item  ' . $btCode . ' ' . $biCode . ' ' . $rtCashBatchItem . ' ' . $biTrans . ' 
';
				}

				$rt->set( 'rtTemp', 1 );
				$rt->update(  );
				continue;
			}

			print '' . '<br>Trans of type ' . $biPayeeType . ' , ' . $btCode . ' ' . $biCode . ' ';
		}
	}

	jmp;
	print '<br>All done';
?>