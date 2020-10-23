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

	function _selectuser($template, $input) {
		global $session;

		$usCode = $input['selectUser'];

		if ($usCode < 1) {
			return false;
		}

		$user = new User( null );
		$found = $user->tryGettingRecord( $usCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this user has been deleted' );
			return false;
		}

		$ret = 'users.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'userEdit.php?amendUser=' . $usCode );
	}

	function _newuser($template, $input) {
		global $session;
		global $isUserSysManager;

		$q = 'SELECT COUNT(usCode) AS total FROM users
			WHERE usDisabled != 1 AND usSysManager != 1';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$total = $row['total'];
		$system = new System( 1 );
		$max = $system->getMaxUsers(  );

		if ($max <= $total) {
			$template->setMessage( '' . 'You can\'t create a new user as you already have the maximum of ' . $max );
			return false;
		}

		$q = 'INSERT  INTO users () VALUES()';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$usCode = udbgetinsertid(  );
		$user = new User( $usCode );
		$user->update(  );
		$ret = 'users.php?refresh';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'userEdit.php?amendUser=' . $usCode );
		exit(  );
	}

	function _dosort($template, $input) {
		$type = $input['sort'];
		$template->setSortType( $type );
		_dosearch( $template, $input );
	}

	function _dosearch($template, $input) {
		global $session;
		global $isUserSysManager;

		$sortType = $template->getSortType(  );
		$searchText = udbmakefieldsafe( trim( $template->get( 'searchText' ) ) );
		$q = 'SELECT usCode FROM users ';
		$someDone = false;

		if (0 < strlen( $searchText )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . '(usFirstName  LIKE \'%' . $searchText . '%\' OR
				   usLastName    LIKE \'%' . $searchText . '%\' )
			   ';
			$someDone = true;
		}


		if ($isUserSysManager == false) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '(usSysManager != 1 OR usSysManager IS NULL)';
			$someDone = true;
		}


		if ($sortType == 'first') {
			$q .= ' ORDER BY  usFirstName, usLastName, usCode';
		} 
else {
			if ($sortType == 'last') {
				$q .= ' ORDER BY  usLastName, usFirstName, usCode';
			} 
else {
				$q .= ' ORDER BY  usFirstName, usLastName, usCode';
			}
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$users = array(  );

		while ($row = udbgetrow( $result )) {
			$users[] = $row['usCode'];
		}

		$template->users = $users;

		if (count( $users ) == 0) {
			$template->setMessage( 'no users found' );
		}

		return false;
	}

	function _updatemaxallowed($template, $input) {
		$max = $input['maxAllowed'];

		if ($max <= 0) {
			return false;
		}

		$system = new System( 1 );
		$system->set( 'syMaxUsers', $max );
		$system->update(  );
	}

	require( '../include/startup.php' );
	$usersTemplate = &$session->get( 'usersTemplate' );

	if ($usersTemplate == null) {
		$usersTemplate = new UsersTemplate( 'users.html' );
		$usersTemplate->setProcess( '_doSearch', 'doSearch' );
		$usersTemplate->setProcess( '_doSort', 'sort' );
		$usersTemplate->setProcess( '_newUser', 'newUser' );
		$usersTemplate->setProcess( '_goToMenu', 'home' );
		$usersTemplate->setProcess( '_selectUser', 'selectUser' );
		$usersTemplate->setProcess( '_doSearch', 'refresh' );
		$usersTemplate->setProcess( '_updateMaxAllowed', 'updateMax' );
		_dosearch( $usersTemplate, null );
	}

	$session->set( 'usersTemplate', $usersTemplate );
	$usersTemplate->process(  );
	$session->set( 'usersTemplate', $usersTemplate );
?>