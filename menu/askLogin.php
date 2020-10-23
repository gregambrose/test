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

	function _doLogin($template, $input) {
		global $session;

		$username = $input['username'];
		$password = $input['password'];

		if (0 < strlen( $password )) {
			$q = (  'SELECT * FROM users WHERE usLogin=\'' . $username . '\' AND usPassword=\'' . $password . '\'' );
			$result = udbQuery( $q );

			if ($result == false) {
				trigger_error( udbLastError(  ), 256 );
			}

			$num = udbNumberOfRows( $result );

			if (1 <= $num) {
				$row = udbGetRow( $result );
				$user = new User( $row );
				$session->set( 'user', $user );
				fLogVisit( 'login', $user );
				fLocationHeader( 'index.php' );
				exit(  );
			}
		}

		$template->set( 'message', 'invalid user name and password' );
		return false;
	}

	require( '../include/startup.php' );
	$askLoginTemplate = $session->get( 'askLoginTemplate' );

	if ($askLoginTemplate == null) {
		$askLoginTemplate = new AskLoginTemplate( 'askLogin.html' );
		$askLoginTemplate->setProcess( '_doLogin', 'submit' );
	}

	$session->set( 'askLoginTemplate', $askLoginTemplate );
	$askLoginTemplate->process(  );
	$session->set( 'askLoginTemplate', $askLoginTemplate );
?>
