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

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		flocationheader( $url );
	}

	function _doupdate($template, $input) {
		global $isUserSysManager;
		$user = &$template->getUser(  );

		if ($user->recordExists(  ) == false) {
			trigger_error( 'cant get user', E_USER_ERROR );
		}

		$usCode = $user->getKeyValue(  );
		$usLogin = trim( $input['usLogin'] );
		$usDisabled = $input['usDisabled'];
		$usDepartment = $input['usDepartment'];

		if (( strlen( $usLogin ) == 0 && $usDisabled != 1 )) {
			$template->setMessage( 'you must specify a login name' );
			return false;
		}


		if (0 < strlen( $usLogin )) {
			$q = '' . 'SELECT usCode FROM users where usLogin = \'' . $usLogin . '\' AND usCode != ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (0 < udbnumberofrows( $result )) {
				$template->setMessage( 'this login name has been used by someone else' );
				return false;
			}
		}

		$q = '' . 'SELECT COUNT(usCode) as numUsers FROM users
		  WHERE usCode != ' . $usCode . '
		  AND (usDisabled != 1 OR usDisabled IS NULL)
		  AND (usSysManager != 1 OR usSysManager IS NULL)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$numUsers = $row['numUsers'];

		if ($usDisabled != 1) {
			++$numUsers;
		}

		$system = new System( 1 );
		$max = $system->getMaxUsers(  );

		if (( $max < $numUsers && $usDisabled != 1 )) {
			$text = '' . 'you have exceeded the maximum number of active users (' . $max . ')';
			$template->setMessage( $text );
			return false;
		}

		$user->setAll( $input );
		$messg = $user->validate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$ok = $user->update(  );

		if ($ok == false) {
			$usCode = $template->get( 'usCode' );
			$template->setUser( $usCode );
			$user->refresh(  );
			$template->setAll( $user->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this user. You will need to re-enter any changes you made' );
			return false;
		}

		$template->set( 'message', 'user updated' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _docancel($template, $input) {
		$usCode = $template->get( 'usCode' );
		$template->setUser( $usCode );
		$template->set( 'message', 'amendments cancelled' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _dodelete($template, $input) {
		$usCode = $input['usCode'];
		$canDelete = true;
		$q = '' . 'SELECT plCode FROM policies  WHERE plLastUpdateBy = ' . $usCode . ' ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			$canDelete = false;
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT clCode FROM clients  WHERE clHandler = ' . $usCode . ' ||  clLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT icCode FROM insuranceCompanies  WHERE icLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT inCode FROM introducers  WHERE inLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT noCode FROM notes
			WHERE noOriginator = ' . $usCode . ' ||
			      noEnteredBy = ' . $usCode . ' ||
			      noNextActionBy= ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT doCode FROM documents
			WHERE doOriginator = ' . $usCode . ' ||
			      doEnteredBy = ' . $usCode . ' ||
			      doClientSentBy = ' . $usCode . ' ||
			      doInscoSentBy = ' . $usCode . ' ||
			      doIntroducerSentBy = ' . $usCode . ' ||
			      doNextActionBy = ' . $usCode . ' ||
			      doIntroducerSentBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT ptCode FROM policyTransactions
			WHERE ptHandler = ' . $usCode . ' ||
			      ptCreatedBy = ' . $usCode . ' ||
			      ptLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT ctCode FROM clientTransactions
			WHERE ctHandler = ' . $usCode . ' ||
			      ctCreatedBy = ' . $usCode . ' ||
			      ctLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT caCode FROM clientTransAllocations
			WHERE
			      caLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT itCode FROM inscoTransactions
			WHERE
			      itCreatedBy = ' . $usCode . ' ||
			      itLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT iaCode FROM inscoTransAllocations
			WHERE
			      iaLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT rtCode FROM introducerTransactions
			WHERE
			      rtCreatedBy = ' . $usCode . ' ||
			      rtLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT raCode FROM introducerTransAllocations
			WHERE
			      raLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT baCode FROM bankAccountTrans
			WHERE
			      baCreatedBy = ' . $usCode . ' ||
			      baLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT aaCode FROM accountingAudit
			WHERE
			      aaCreatedBy = ' . $usCode . ' ||
			      aaLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == true) {
			$q = '' . 'SELECT biCode FROM cashBatchItems
			WHERE
			      biAllocatedBy = ' . $usCode . ' ||
			      biLastUpdateBy = ' . $usCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$num = udbnumberofrows( $result );

			if (0 < $num) {
				$canDelete = false;
			}
		}


		if ($canDelete == false) {
			$template->setMessage( 'You cant delete this user as it is has one or more entries' );
			return false;
		}

		$user = new User( $usCode );

		if ($user->recordExists(  ) == false) {
			trigger_error( '' . 'cant get user ' . $usCode, E_USER_ERROR );
		}

		$ok = $user->delete(  );

		if ($ok == false) {
			$usCode = $template->get( 'usCode' );
			$template->setUser( $usCode );
			$user->refresh(  );
			$template->setAll( $user->getAllForHTML(  ) );
			$template->setMessage( 'Sorry...Someone else has amended this user. You will need to re-enter any changes you made' );
			return false;
		}

		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->setMessage( 'user deleted' );
		flocationheader( 'users.php' );
		exit(  );
	}

	require( '../include/startup.php' );
	$userEditTemplate = &$session->get( 'userEditTemplate' );

	if ($userEditTemplate == null) {
		$userEditTemplate = new UserEditTemplate( 'userEdit.html' );
		$userEditTemplate->setProcess( '_doUpdate', 'update' );
		$userEditTemplate->setProcess( '_doDelete', 'deleteUser' );
		$userEditTemplate->setProcess( '_doCancel', 'cancel' );
		$userEditTemplate->setProcess( '_doDelete', 'delete' );
		$userEditTemplate->setProcess( '_goBack', 'back' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$userEditTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['amendUser'] )) {
		$usCode = $_GET['amendUser'];
		$userEditTemplate->setUser( $usCode );
	}


	if (isset( $_GET['refresh'] )) {
		$usCode = $userEditTemplate->get( 'usCode' );
		$userEditTemplate->setUser( $usCode );
	}

	$session->set( 'userEditTemplate', $userEditTemplate );
	$userEditTemplate->process(  );
	$session->set( 'userEditTemplate', $userEditTemplate );
?>