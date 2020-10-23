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

	class userstemplate {
		var $users = null;
		var $page = null;
		var $sortType = null;

		function userstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'searchText' );
			$this->addField( 'maxAllowed' );
			$system = new System( 1 );
			$max = $system->getMaxUsers(  );
			$this->set( 'maxAllowed', $max );
			$this->setReturnTo( '../users/users.php' );
			$this->users = array(  );
			$this->sortType = '';
		}

		function wheninternalmanager($text) {
			global $isUserSysManager;
			global $isUserInternalManager;

			if (( $isUserSysManager != true && $isUserInternalManager != true )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whensystemmanager($text) {
			global $isUserSysManager;

			if ($isUserSysManager != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listusers($text) {
			global $userLevel;

			$numOfUsers = count( $this->users );
			$out = '';
			$elem = 0;

			while ($elem < $numOfUsers) {
				$usCode = &$this->users[$elem];

				$user = new User( null );
				$found = $user->tryGettingRecord( $usCode );

				if ($found == false) {
					continue;
				}

				$usDisabled = $user->get( 'usDisabled' );
				$usDepartment = $user->get( 'usDepartment' );

				if (0 < $usDepartment) {
					$dep = new Department( $usDepartment );
					$depName = $dep->get( 'dpName' );
				} 
else {
					$depName = '';
				}


				if ($usDisabled == 1) {
					$status = 'Inactive';
					$colour = '#FAE2BB';
				} 
else {
					$status = 'Active';
					$colour = '#EBFFEA';
				}

				$first = trim( $user->get( 'usFirstName' ) );

				if ($first == '') {
					$first = 'Blank';
				}

				$this->set( 'usCode', $usCode );
				$this->set( 'usFirstName', $first );
				$this->set( 'usLastName', $user->get( 'usLastName' ) );
				$this->set( 'depName', $depName );
				$this->set( 'colour', $colour );
				$this->set( 'status', $status );
				$out .= $this->parse( $text );
				++$elem;
			}

			return $out;
		}

		function setsorttype($type) {
			$this->sortType = $type;
		}

		function getsorttype() {
			return $this->sortType;
		}
	}

?>