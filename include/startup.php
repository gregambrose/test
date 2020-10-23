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

	/**
*	Normally a script will check levels before sending the user here. If the script is called
*	direct, it should die with an error if the level is wrong. This is done in this function
* 
* NOTE so that the peiod end can call the control account for totals, security is relaxed
* for this when getTotals requested
*/
	function _checkRequiredLevel() {
		global $levelsRequired;
		global $session;

		$fullScript = THIS_SCRIPT;
		$posn = strrpos( $fullScript, '/' );

		if ($posn === false) {
			$script = $fullScript;
		} 
else {
			$script = substr( $fullScript, $posn + 1 );
		}


		if (strpos( $_SERVER['QUERY_STRING'], 'getTotals' ) !== false) {
			return true;
		}


		if (strpos( $_SERVER['QUERY_STRING'], 'periodEnd' ) !== false) {
			return true;
		}


		if (strpos( $_SERVER['QUERY_STRING'], 'accountingIntegrity' ) !== false) {
			return true;
		}

		$user = $session->get( 'user' );

		if (( ( ( ( !is_a( $user, 'User' ) && SCRIPT_NAME != 'index.php' ) && SCRIPT_NAME != 'askLogin.php' ) && SCRIPT_NAME != 'accountingIntegrity.php' ) && DEBUG_MODE != true )) {
			fLocationHeader( '../menu/askLogin.php' );
			exit(  );
		}


		if (( !is_a( $user, 'User' ) && SCRIPT_NAME == 'index.php' )) {
			fLocationHeader( '../menu/askLogin.php' );
			exit(  );
		}


		if (( ( SCRIPT_NAME != 'askLogin.php' && SCRIPT_NAME != 'accountingIntegrity.php' ) && DEBUG_MODE != true )) {
			if (!is_a( $user, 'User' )) {
				trigger_error(  'Script ' . $script . ' run directly without authority - no user set', 256 );
			}


			if (isset( $levelsRequired[$script] )) {
				$required = $levelsRequired[$script];
				$actual = $user->getLevel(  );

				if ($actual < $required) {
					trigger_error(  'Script ' . $script . ' run directly without authority - user level of ' . $actual . ' insufficient', 256 );
				}
			}
		}

	}

	global $session;
	global $levelsRequired;
	global $userLevel;
	global $startTime;
	global $userCode;
	global $isUserSysManager;
	global $isUserInternalManager;
	global $brokerVATRate;
	global $companyVATRate;
	global $iptNormalRate;
	global $iptTravelRate;
	global $securityID;
	global $sessionName;
	global $accountingYear;
	global $accountingYearCode;
	global $accountingYearDesc;
	global $accountingPeriod;
	global $accountingPeriodCode;
	global $periodFrom;
	global $periodTo;

	define( 'THIS_SCRIPT', $_SERVER['PHP_SELF'] );
	$posn = strrpos( THIS_SCRIPT, '/' );

	if ($posn !== false) {
		$s = substr( THIS_SCRIPT, $posn + 1 );
	} 
else {
		$s = THIS_SCRIPT;
	}

	define( 'SCRIPT_NAME', $s );
	require( 'paths.php' );
	define( 'CLASSES_PATH', PATH_TO_ROOT . 'classes/' );
	define( 'INCLUDE_PATH', PATH_TO_ROOT . 'include/' );
	define( 'LOG_PATH', PATH_TO_ROOT . 'log/' );
	define( 'TEMPLATE_PATH', 'templates/' );
	define( 'TEMPLATECLASS_PATH', 'templateClasses/' );
	define( 'IMPORT_PATH', PATH_TO_ROOT . 'imports/' );
	require_once( PATH_TO_ROOT . 'config/config.inc.php' );

	if (USE_LOCAL_UTIL == true) {
		define( 'UTIL_PATH', PATH_TO_ROOT . 'util/' );
	} 
else {
		define( 'UTIL_PATH', STD_UTIL_PATH );
	}

	require_once( UTIL_PATH . 'util.inc.php' );
	$startTime = uGetMicroTime(  );
	uStartOurErrorHandling( WE_HANDLE_ERRORS );
	$logFile = LOG_PATH . 'errors.txt';
	$ok = ini_set( 'error_log', $logFile );
	require_once( UTIL_PATH . 'URecord.class.php' );
	require_once( UTIL_PATH . 'UTemplate.class.php' );
	require_once( UTIL_PATH . 'USession.class.php' );
	require_once( UTIL_PATH . 'UDateSelector.class.php' );
	require_once( INCLUDE_PATH . 'functions.php' );
	require_once( CLASSES_PATH . 'FTemplate.class.php' );
	uRequireClasses( CLASSES_PATH );
	uRequireClasses( TEMPLATECLASS_PATH );
	require_once( '../batches/templateClasses/CashBatchEditTemplate.class.php' );
	require_once( '../accounts/templateClasses/JournalEditTemplate.class.php' );
	define( 'SITE_URL', uGetCurrentURLWithProtocol(  ) );
	udbOpen( DBTYPE, DBHOST, DBDATABASE, DBUSER, DBPASSWORD );
	udbRollBackTransaction(  );

	if (USE_OUR_OWN_SESSION_DIR == true) {
		$old = ini_set( 'session.save_handler', 'files' );
		$old = ini_set( 'session.save_path', uGetSessionPath( USE_WEB_ROOT_FOR_SESSION_PATH ) );
	}


	if (isset( $_REQUEST['sn'] )) {
		$sessionName = $_REQUEST['sn'];
	} 
else {
		$sessionName = fNewSessionName(  );
		$request = $_SERVER['PHP_SELF'];
		$ok = false;

		if (substr( $request, -9 ) == 'mysql.php') {
			$ok = true;
		}


		if (substr( $request, -10 ) == 'backup.php') {
			$ok = true;
		}


		if (substr( $request, -9 ) == 'index.php') {
			$ok = true;
		}


		if (substr( $request, -12 ) == 'askLogin.php') {
			$ok = true;
		}


		if (substr( $request, -23 ) == 'accountingIntegrity.php') {
			$ok = true;
		}


		if (strpos( $_SERVER['QUERY_STRING'], 'getTotals' ) !== false) {
			$ok = true;
		}


		if (strpos( $_SERVER['QUERY_STRING'], 'periodEnd' ) !== false) {
			$ok = true;
		}


		if ($ok == false) {
			if (( SCRIPT_NAME != 'askLogin.php' && DEBUG_MODE != true )) {
				fLocationHeader( '../menu/askLogin.php' );
				exit(  );
			}


			if (DEBUG_MODE != true) {
				$uri = $_SERVER['REQUEST_URI'];

				if (isset( $_SERVER['HTTP_REFERER'] )) {
					$referer = $_SERVER['HTTP_REFERER'];
				} 
else {
					$referer = 'no set';
				}

				trigger_error( 'no session passed uri=' . $uri . ' referer=' . $referer, 512 );
			}
		}
	}

	session_name( $sessionName );
	session_start(  );
	$sid = session_id(  );

	if (isset( $_SESSION['session'] )) {
		$session = &$_SESSION['session'];
	}


	if (( !empty( $$session ) || !is_a( $session, 'USession' ) )) {
		$session = new USession(  );
	}

	$returnTo = $session->get( 'returnTo' );
	$session->clear( 'returnTo' );

	if (isset( $_GET['newSession'] )) {
		$userObj = $session->get( 'user' );
		$sessionName = fNewSessionName(  );
		session_name( $sessionName );
		session_regenerate_id(  );
		session_unset(  );
		$session = new USession(  );

		if (empty( $$userObj )) {
			$session->set( 'user', $userObj );
			$user = &$userObj;
		}
	}

	$user = $session->get( 'user' );
	$userCode = 0;
	$userLevel = 0;
	$isUserSysManager = false;
	$isUserInternalManager = false;

	if (is_a( $user, 'User' )) {
		$userCode = $user->getKeyValue(  );
		$user->refresh(  );
		$session->set( 'user', $user );
		$userLevel = $user->getLevel(  );
		$isUserSysManager = $user->isUserSysManager(  );
		$isUserInternalManager = $user->isUserInternalManager(  );
	}


	/*if (( !empty( $$startWithoutTables ) || $startWithoutTables == false )) {
		$system = new System( 1 );
		$session->set( 'system', $system );
		$brokerVATRate = $system->getBrokerVATRate(  );
		$companyVATRate = $system->getCompanyVATRate(  );
		$iptNormalRate = $system->getNormalIPTRate(  );
		$iptTravelRate = $system->getTravelIPTRate(  );
		$accountingYearDesc = $system->getAccountingYearDesc(  );
		$accountingYear = $system->getAccountingYear(  );
		$accountingYearCode = $system->getAccountingYearCode(  );
		$accountingPeriod = $system->getAccountingPeriod(  );
		$accountingPeriodCode = $system->getPeriodCode(  );
		$periodFrom = $system->getPeriodFrom(  );
		$periodTo = $system->getPeriodTo(  );
	}*/


	/*if (( defined( 'USER_FOR_YEAR_END' ) && empty( $$user ) )) {
		$usCode = $user->getKeyValue(  );

		if ($usCode == USER_FOR_YEAR_END) {
			fSetToEndOfYear(  );
		}
	}*/

	header( 'Cache-Control: private' );
	$remoteURL = $session->get( 'remoteURL' );

	/*if ($remoteURL == '') {
		$remoteURL = $_SERVER['REMOTE_ADDR'];
		$session->set( 'remoteURL', $remoteURL );
	}*/


	/*if (( !empty( $$startWithoutTables ) || $startWithoutTables == false )) {
		_checkRequiredLevel(  );
	}*/


	if (!defined( 'TIME_OUT' )) {
		define( 'TIME_OUT', 6000 );
	}

	ini_set( 'max_input_time', TIME_OUT );
	ini_set( 'max_execution_time', TIME_OUT );
	ini_set( 'default_socket_timeout', TIME_OUT );
?>
