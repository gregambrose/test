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

	function fremove($table, $sequence) {
		$q = '' . 'DELETE FROM ' . $table;
		$result = mysql_query( $q );

		if ($result == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		if (0 < $sequence) {
			$q = '' . 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . $sequence;
			$result = mysql_query( $q );

			if ($result == null) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}
		}

	}

	require_once( '../include/startup.php' );
	fremove( 'documents', 6000000 );
	fremove( 'systemTransactions', 7000000 );
	fremove( 'bankAccountTrans', 1000000 );
	fremove( 'cashBatches', 8000000 );
	fremove( 'clientTransactions', 2000000 );
	fremove( 'inscoTransactions', 3000000 );
	fremove( 'introducerTransactions', 4000000 );
	fremove( 'policyTransactions', 5000000 );
	fremove( 'journals', 9000000 );
	fremove( 'accountingAudit', 9000000 );
	fremove( 'clientTransAllocations', 0 );
	fremove( 'inscoTransAllocations', 0 );
	fremove( 'introducerTransAllocations', 0 );
	fremove( 'systemSequeneces', 0 );
	fremove( 'cashBatchItems', 0 );
	fremove( 'accountingFigures', 0 );
	fremove( 'cashBatchItems', 0 );
	fremove( 'cashBatchItems', 0 );
	fremove( 'sequences', 0 );
	$q = 'INSERT INTO  systemSequeneces (ssCode, ssSequence) VALUES (1,  7000000)';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE system
		 SET 
		 syAccountingYear		= 2007,		
		 syAccountingPeriod	    = 1,
		 syPeriodFrom			= \'2007-04-01\',
		 syPeriodTo				= \'2007-04-30\',
		 syYearCode				= 4,
		 syPeriodCode			= 37
		 
		 WHERE syCode = 1';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'INSERT INTO  sequences (sqType, sqMaster, sqLastUsed) VALUES (\'PTI\', 1, 2999999)';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE sequences SET sqLastUsed=2999999 WHERE sqType=\'PTI\' AND sqMaster=1';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	echo 'All Done.....';
?>