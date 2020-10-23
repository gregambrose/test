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

	function _backup($template, $input) {
		_dobackup(  );
		$template->setMessage( 'backup complete' );
		return false;
	}

	function _periodend($template, $input) {
		_dobackup(  );
		echo 'OK
';
		exit(  );
	}

	function _download($template, $input) {
		$bup = new mysql_backup( DBHOST, DBDATABASE, DBUSER, DBPASSWORD, null, false );

		if (!$bup->backup(  ) == true) {
			trigger_error( 'Download failed!!!!!', E_USER_ERROR );
		}

		exit(  );
	}

	function _dologoff($template, $input) {
		flogoff(  );
	}

	function _dobackup() {
		$output = BACKUP_PATH . date( 'YmdHis', time(  ) );
		$restoreFrom = BACKUP_PATH . 'restorefrom.txt';
		$bup = new mysql_backup( DBHOST, DBDATABASE, DBUSER, DBPASSWORD, $output, false );

		if (!$bup->backup(  ) == true) {
			trigger_error( 'Backup failed!!!!!', E_USER_ERROR );
			return null;
		}


		if (( !defined( 'LIVE_PROCESSING' ) || LIVE_PROCESSING == false )) {
			copy( $output, $restoreFrom );
		}

	}

	function _dorestore($template, $input) {
		$input = BACKUP_PATH . '/restorefrom.txt';

		if (isset( $_GET['first'] )) {
			$first = $_GET['first'];
		} 
else {
			$first = 0;
		}


		if (isset( $_GET['last'] )) {
			$last = $_GET['last'];
		} 
else {
			$last = 0;
		}

		$bup = new mysql_backup( DBHOST, DBDATABASE, DBUSER, DBPASSWORD, $input, false );

		if ($bup->largeRestore( $first, $last ) == false) {
			trigger_error( 'Restore failed!!!!!', E_USER_ERROR );
		}

		fsetallsequencenumbers(  );
		echo 'Restore Done';
		$template->setMessage( 'Restore done' );
		return false;
	}

	function fsetallsequencenumbers() {
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
	}

	function freset($table, $sequence) {
		if (0 < $sequence) {
			$q = '' . 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . $sequence;
			$result = mysql_query( $q );

			if ($result == null) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}
		}

	}

	$requiredLevel = 9;
	$startWithoutTables = true;
	require( '../include/startup.php' );
	require_once( UTIL_PATH . 'mysql_backup.class.php' );
	$backupTemplate = &$session->get( 'backupTemplate' );

	if ($backupTemplate == null) {
		$backupTemplate = new BackupTemplate( 'backup.html' );
		$backupTemplate->setProcess( '_periodEnd', 'periodEnd' );
		$backupTemplate->setProcess( '_backup', 'backup' );
		$backupTemplate->setProcess( '_doRestore', 'restore' );
		$backupTemplate->setProcess( '_download', 'download' );
		$backupTemplate->setProcess( '_doLogOff', 'logOff' );
	}

	$session->set( 'backupTemplate', $backupTemplate );
	$backupTemplate->process(  );
	$session->set( 'backupTemplate', $backupTemplate );
?>