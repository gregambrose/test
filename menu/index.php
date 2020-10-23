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
* @ iDezender 8.0
* @ Developed by Qarizma
*
* @    Visit our website:
* @    www.iRadikal.com
* @    For cheap decoding service :)
* @    And for the ionCube Decoder!
*/
	function _dologoff($template, $input) {
		global $session;
		global $user;

		unset( $$user );
		$session->clear( 'user' );
		ulocationheader( 'askLogin.php' );
	}

	require( '../include/startup.php' );
	$requiredLevel = 2;
	$loggedIn = false;

	if (empty( $$user ) == true) {
		$level = $user->getLevel(  );

		if ($requiredLevel <= $level) {
			$loggedIn = true;
		}
	}


	if ($loggedIn == false) {
		flocationheader( 'askLogin.php' );
		exit(  );
	}

	$menuTemplate = &$session->get( 'menuTemplate' );

	if ($menuTemplate == null) {
		$menuTemplate = new MenuTemplate( 'menu.html' );
		$menuTemplate->setProcess( '_doLogoff', 'logOff' );
		$menuTemplate->set( 'companyName', COMPANY_NAME );
		$companyAddress = str_replace( '
', '<br>
', COMPANY_ADDRESS );
		$menuTemplate->set( 'companyAddress', $companyAddress );
	}

	$session->set( 'menuTemplate', $menuTemplate );
	$menuTemplate->process(  );
	$session->set( 'menuTemplate', $menuTemplate );
?>