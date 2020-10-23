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

	function _download() {
		$bup = new mysql_backup( DBHOST, DBDATABASE, DBUSER, DBPASSWORD, null, false );

		if (!$bup->backup(  ) == true) {
			trigger_error( 'Download failed!!!!!', E_USER_ERROR );
		}

		exit(  );
	}

	function _dobackup() {
		$output = BACKUP_PATH . 'B' . date( 'YmdHis' );
		$bup = new mysql_backup( DBHOST, DBDATABASE, DBUSER, DBPASSWORD, $output, false );

		if (!$bup->backup(  ) == true) {
			trigger_error( 'Backup failed!!!!!', E_USER_ERROR );
		}

		echo 'Backup Done';
	}

	function _dorestore() {
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

		if (!$bup->largeRestore( $first, $last ) == false) {
			trigger_error( 'Restore failed!!!!!', E_USER_ERROR );
		}

		echo 'Restore Done';
	}

	$startWithoutTables = true;
	require( '../include/startup.php' );
	require_once( UTIL_PATH . 'mysql_backup.class.php' );

	if (isset( $_GET['restore'] )) {
		_dorestore(  );
	}


	if (isset( $_GET['backup'] )) {
		_dobackup(  );
	}


	if (isset( $_GET['download'] )) {
		_download(  );
	}

?>