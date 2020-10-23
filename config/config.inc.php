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
	define( 'DO_VALIDATION', true );
	define( 'READ_ONLY', false );
	define( 'SITE_COLOUR', 'white' );
	define( 'COPYRIGHT_MESSAGE', 'Copyright &copy;2006-2018' );
	define( 'ROW_COLOUR_HEADER', '#DDECFF' );
	define( 'ROW_COLOUR_A', '#F2F9F2' );
	define( 'ROW_COLOUR_B', '#F9F9FC' );
	define( 'ROW_COLOUR_MARKED', '#F0EFF0' );
	define( 'ROW_COLOUR_VISITED', '#F8EAE9' );

	if (isset( $_SERVER['HTTP_HOST'] )) {
		$host = $_SERVER['HTTP_HOST'];
	} 
else {
		$host = 'localhost';
	}

	define( 'SITE_ROOT_URL', 'https://' . $host . '/' );
	define( 'SITE_ROOT_SECURE_URL', 'https://' . $host . '/' );
	define( 'SITE_ROOT_INTERNAL_URL', 'http://127.0.0.1/' );
	define( 'SITE_MAIN_HELP_PAGE', SITE_ROOT_URL . 'help/help.html' );
	define( 'DOCUMENT_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/docms/' );
	define( 'DOCUMENT_ROOT_URL', SITE_ROOT_URL . 'docms/' );
	define( 'IMAGES_PATH', $_SERVER['DOCUMENT_ROOT'] . '/images/' );
	define( 'DBTYPE', 'mysqli' );
	define( 'DBHOST', 'localhost' );
	define( 'DBUSER', 'dbuser' );
	define( 'DBPASSWORD', 'lx1CJNdQnUCEyXjx1q32' );
	define( 'DBDATABASE', 'dmc' );
	define( 'USE_PCONNECT', false );
	define( 'TIME_DIFFERENCE', 0 );
	define( 'USE_OUR_OWN_SESSION_DIR', true );
	define( 'USE_WEB_ROOT_FOR_SESSION_PATH', false );
	define( 'ERROR_LOG_FILE', LOG_PATH . 'errorlog.php' );
	define( 'BACKUP_PATH', PATH_TO_ROOT . 'backup/' );
	define( 'PDFS_PATH', PATH_TO_ROOT . 'pdfs/' );
	define( 'USE_LOCAL_UTIL', false );
	define( 'STD_UTIL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/util/' );
	define( 'WE_HANDLE_ERRORS', true );
	define( 'EMAIL_ON_ERROR', true );
	define( 'EMAIL_ERRORS_TO', 'errors@catalina-it.com.au' );
	define( 'EMAIL_SUBJECT', 'DMC LIVE error' );
	define( 'DISPLAY_ERRORS', true );
	define( 'LOG_PROCESS_TIME', false );
	define( 'EMAIL_TYPE', 'sendmail' );
	define( 'SMTP', 'mail.catalina-it.com.au' );
	define( 'EMAIL_FROM', 'bill@catalina-it.com.au' );
	define( 'EMAIL_FROM_NAME', 'dmc test' );
	define( 'SEND_TO_EMAIL_LIST_SUBJECT', 'test email' );
	define( 'REPLY_TO', 'mary@catalina-it.com.au' );
	define( 'ADMIN_EMAIL', 'greg@catalina-it.com.au' );
	define( 'INTEGRITY_EMAIL', 'errors@catalina-it.com.au; jbullen@apollosystems.co.uk' );
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
	$levelsRequired = array( 'clientsXXX.php' => 9, 'backup.php' => 9, 'cobs.php' => 9, 'groupEdit.php' => 9, 'groups.php' => 9, 'accountingPeriods.php' => 9, 'userEdit.php' => 9, 'users.php' => 9 );
	define( 'CURRENCY_SYMBOL', '£' );
	define( 'CURRENCY_SYMBOL_FOR_HTML', '&pound;' );
	define( 'SITE_NAME', 'DMC ACCOUNTS LIVE' );
	define( 'SITE_WARNING', '' );
	define( 'COMPANY_NAME', 'Dolden Martin & Co.' );
	define( 'COMPANY_ADDRESS', 'Insurance Brokers & Consultants
22 Chapel Lane
Pinner
Middlesex
HA5 1AZ
' );
	define( 'DEBIT_PAY_TO_MESSAGE', 'DUE TO DOLDEN MARTIN & CO BY CHEQUE/CASH' );
	define( 'CREDIT_PAY_TO_MESSAGE', 'DUE FROM DOLDEN MARTIN & CO BY CHEQUE/CASH' );
	define( 'KEY_POLICY_DOCM_DEBIT', 28 );
	define( 'KEY_POLICY_DOCM_CREDIT', 27 );
	define( 'KEY_POLICY_DOCM_RENEWAL', 41 );
	define( 'KEY_POLICY_DOCM_RECEIPT', 53 );
	define( 'REMITTANCE_ADVICE_DOCM_TYPE', 54 );
	define( 'CLIENT_STATEMENT_DOCM_TYPE', 55 );
	define( 'INTRODUCER_STATEMENT_DOCM_TYPE', 56 );
	define( 'MANAGEMENT_DOCM_TYPE', 57 );
	define( 'ACCOUNTING_PERIODS_PER_YEAR', 12 );
	define( 'USER_FOR_YEAR_END', -1 );
	define( 'YEAR_CODE_FOR_YEAR_END', 2 );
	define( 'PERIOD_CODE_FOR_YEAR_END', 24 );
	define( 'KEY_BANK_CASH_BATCH', 13 );
	define( 'KEY_BANK_CASH_TO_CLIENT', 14 );
	define( 'KEY_BANK_CASH_TO_INSCO', 15 );
	define( 'KEY_BANK_CASH_TO_INTROD', 16 );
	define( 'KEY_BANK_CASHBATCH_PAYMENT_TYPE', 7 );
	define( 'JOURNAL_PAYMENT_METHOD', 4 );
	define( 'MAX_NORMAL_SYSTEM_USERS', 8 );
	define( 'MAX_DEVELOPER_SYSTEM_USERS', 2 );
	define( 'LIVE_PROCESSING', true );
?>
