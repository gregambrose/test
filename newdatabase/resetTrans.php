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

	function freset($table, $sequence) {
		if (0 < $sequence) {
			$q = '' . 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . $sequence;
			$result = mysql_query( $q );

			if ($result == null) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}
		}

	}

	require_once( '../include/startup.php' );
	freset( 'documents', 6000000 );
	freset( 'systemTransactions', 7000000 );
	freset( 'bankAccountTrans', 1000000 );
	freset( 'cashBatches', 8000000 );
	freset( 'clientTransactions', 2000000 );
	freset( 'inscoTransactions', 3000000 );
	freset( 'introducerTransactions', 4000000 );
	freset( 'policyTransactions', 5000000 );
	freset( 'journals', 9000000 );
	freset( 'accountingAudit', 9000000 );
	echo 'All Done.....';
?>