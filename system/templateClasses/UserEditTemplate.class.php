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

	class useredittemplate {
		function useredittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'usCode' );
			$this->addField( 'usFirstName' );
			$this->addField( 'usLastName' );
			$this->addField( 'usDepartment' );
			$this->addField( 'usGroup' );
			$this->addField( 'usSysManager' );
			$this->addField( 'usHandler' );
			$this->addField( 'usInitials' );
			$this->addField( 'usLogin' );
			$this->setFieldType( 'usSysManager', 'checked' );
			$this->setFieldType( 'usHandler', 'checked' );
			$this->setFieldType( 'usDisabled', 'checked' );
			$this->setUpdateFieldsWhenNoEdit( false );
		}

		function setuser($usCode) {
			$user = new User( $usCode );
			$this->user = &$user;

			$this->setAll( $user->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandedituser($usCode) {
			$this->setUser( $usCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getuser() {
			return $this->user;
		}

		function showdepartments($text) {
			$q = 'SELECT * FROM departments ORDER BY dpName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$usDepartment = $this->get( 'usDepartment' );

			while ($row = udbgetrow( $result )) {
				$dpCode = $row['dpCode'];
				$dpName = $row['dpName'];

				if ($dpCode == $usDepartment) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'dpCode', $dpCode );
				$this->set( 'dpName', $dpName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

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

		function whennotsystemmanager($text) {
			global $isUserSysManager;

			if ($isUserSysManager == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
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

		function whennotinternalmanager($text) {
			global $isUserSysManager;
			global $isUserInternalManager;

			if ($isUserSysManager == true) {
				return '';
			}


			if ($isUserInternalManager == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenusersowndetails($text) {
			global $userLevel;
			global $userCode;
			global $isUserSysManager;
			global $isUserInternalManager;

			$canDo = false;

			if ($isUserSysManager == true) {
				$canDo = true;
			}


			if ($isUserInternalManager == true) {
				$canDo = true;
			}

			$usCode = $this->get( 'usCode' );

			if ($usCode == $userCode) {
				$canDo = true;
			}


			if ($canDo == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showgroups($text) {
			$q = 'SELECT * FROM userGroups ORDER BY ugSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$usGroup = $this->get( 'usGroup' );

			while ($row = udbgetrow( $result )) {
				$ugCode = $row['ugCode'];
				$ugName = $row['ugName'];

				if ($ugCode == $usGroup) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'ugCode', $ugCode );
				$this->set( 'ugName', $ugName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>