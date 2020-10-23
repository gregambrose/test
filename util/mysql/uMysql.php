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

	require_once( 'config/config.inc.php' );
	define( 'CLASSES_PATH', 'classes/' );
	define( 'TEMPLATE_CLASSES_PATH', 'templateClasses/' );
	define( 'LOG_PATH', 'log/' );
	define( 'TEMPLATE_PATH', 'templates/' );

	if (USE_LOCAL_UTIL == true) {
		define( 'UTIL_PATH', 'util/' );
	} 
else {
		define( 'UTIL_PATH', STD_UTIL_PATH );
	}

	require_once( UTIL_PATH . 'util.inc.php' );
	require_once( UTIL_PATH . 'URecord.class.php' );
	require_once( UTIL_PATH . 'UField.class.php' );
	require_once( UTIL_PATH . 'UTemplate.class.php' );
	require_once( UTIL_PATH . 'USession.class.php' );
	require_once( TEMPLATE_CLASSES_PATH . 'UMysqlTemplate.class.php' );
	ustartourerrorhandling( WE_HANDLE_ERRORS );

	if (isset( $_SESSION['session'] )) {
		$session = &$_SESSION['session'];
	}


	if (( !isset( $session ) || !is_a( $session, 'USession' ) )) {
		$session = new USession(  );
	}

	$_SESSION['session'] = &$session;

	if (isset( $_GET['config'] )) {
		$dir = $_GET['config'];
		$path = '' . 'config/' . $dir . '/dbConfig.inc.php';
	} 
else {
		if (isset( $_SESSION['config'] )) {
			$dir = $_SESSION['config'];
			$path = '' . 'config/' . $dir . '/dbConfig.inc.php';
		} 
else {
			$dir = '';
			$path = 'config/dbConfig.inc.php';
		}
	}

	$_SESSION['config'] = $dir;
	require_once( $path );
	udbopen( DBTYPE, DBHOST, DBDATABASE, DBUSER, DBPASSWORD );
	udbrollbacktransaction(  );
	$uMysqlTemplate = &$session->get( 'uMysqlTemplate' );

	if ($uMysqlTemplate == null) {
		$uMysqlTemplate = new UMysqlTemplate( 'mysql.html' );
		$uMysqlTemplate->setProcess( 'handleRequest', 'request' );
	}

	$session->set( 'uMysqlTemplate', $uMysqlTemplate );
	$uMysqlTemplate->process(  );
	$session->set( 'uMysqlTemplate', $uMysqlTemplate );
?>