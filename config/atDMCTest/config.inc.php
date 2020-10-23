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

	global $levelsRequired;

	define( 'DEBUG_MODE', false );
	define( 'SITE_NAME', 'Dolden Martin & Co  Test' );
	define( 'SITE_ROOT_URL', 'http://www.catalina-it.com.au/dmctest/' );
	define( 'SITE_ROOT_SECURE_URL', 'http://www.catalina-it.com.au/dmctest' );
	define( 'SITE_MAIN_HELP_PAGE', SITE_ROOT_URL . 'help/help.html' );
	define( 'DOCUMENT_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/dmctest/docms/' );
	define( 'DOCUMENT_ROOT_URL', SITE_ROOT_URL . 'docms/' );
	define( 'DBTYPE', 'mysql' );
	define( 'DBHOST', 'localhost' );
	define( 'DBUSER', 'greg' );
	define( 'DBPASSWORD', 'chezery2' );
	define( 'DBDATABASE', 'catalinadmctest' );
	define( 'USE_PCONNECT', true );
	define( 'TIME_DIFFERENCE', 0 - 540 );
	define( 'USE_OUR_OWN_SESSION_DIR', false );
	define( 'USE_WEB_ROOT_FOR_SESSION_PATH', true );
	define( 'ERROR_LOG_FILE', LOG_PATH . 'errorlog.php' );
	define( 'BACKUP_PATH', PATH_TO_ROOT . 'backup/' );
	define( 'USE_LOCAL_UTIL', true );
	define( 'STD_UTIL_PATH', 'C:/Documents and Settings/greg/My Documents/www/clients/util/' );
	define( 'WE_HANDLE_ERRORS', true );
	define( 'EMAIL_ON_ERROR', true );
	define( 'EMAIL_ERRORS_TO', 'errors@catalina-it.com.au' );
	define( 'EMAIL_SUBJECT', 'DMC test at Catalina error' );
	define( 'DISPLAY_ERRORS', false );
	define( 'LOG_PROCESS_TIME', true );
	define( 'EMAIL_TYPE', 'sendmail' );
	define( 'SMTP', 'mail.catalina-it.com.au' );
	define( 'EMAIL_FROM', 'bill@catalina-it.com.au' );
	define( 'EMAIL_FROM_NAME', 'catalina it' );
	define( 'SEND_TO_EMAIL_LIST_SUBJECT', 'test email' );
	define( 'REPLY_TO', 'mary@catalina-it.com.au' );
	define( 'ADMIN_EMAIL', 'greg@catalina-it.com.au' );
	define( 'SYSTEM_KEY', 1 );
	define( 'OUR_IP_ADDRESS', '192.167.881.2' );
	define( 'DATE_FORMAT', 'dd MMM yyyy' );
	define( 'COMMERCIAL_TYPE', 1 );
	define( 'RETAIL_TYPE', 2 );
	define( 'CLIENTS_PER_PAGE', 12 );
	define( 'POLICIES_PER_PAGE', 11 );
	define( 'INSCOS_PER_PAGE', 15 );
	define( 'INTRODUCERS_PER_PAGE', 15 );
	$levelsRequired = array(  );
	$levelsRequired = array( 'clients.php' => 9, 'clientEdit.php' => 9, 'policies.php' => 9, 'policyEdit.php' => 9, 'groups.php' => 9, 'userEdit.php' => 9, 'users.php' => 9 );
?>