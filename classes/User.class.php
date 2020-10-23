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

	class user {
		var $table = null;
		var $keyField = null;

		function user($code) {
			$this->keyField = 'usCode';
			$this->table = 'users';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['usDepartment'] = 'INT';
			$this->fieldTypes['usGroup'] = 'INT';
			$this->fieldTypes['usHandler'] = 'BOOL';
			$this->fieldTypes['usDisabled'] = 'BOOL';
			$this->fieldTypes['usSysManager'] = 'BOOL';
		}

		function getemailaddress() {
			return $this->usEmail;
		}

		function getlevel() {
			if (isset( $this->usGroup )) {
				$ugCode = $this->usGroup;

				if ($ugCode < 1) {
					return 0;
				}
			} 
else {
				return 0;
			}

			$group = new Group( $ugCode );
			$level = $group->get( 'ugLevel' );

			if ($level == null) {
				$level = 0;
			}

			return $level;
		}

		function getfullname() {
			$name = $this->get( 'usFirstName' ) . ' ' . $this->get( 'usLastName' );
			return $name;
		}

		function getinitials() {
			$init = $this->get( 'usInitials' );
			return $init;
		}

		function isusersysmanager() {
			$x = $this->get( 'usSysManager' );

			if ($x == 1) {
				return true;
			}

			return false;
		}

		function isuserinternalmanager() {
			$x = $this->get( 'usSysManager' );

			if ($x == 1) {
				return false;
			}

			$x = $this->get( 'usGroup' );

			if ($x <= 0) {
				return false;
			}

			$ug = new UserGroup( $x );
			$x = $ug->get( 'ugLevel' );

			if (9 <= $x) {
				return true;
			}

			return false;
		}

		function validate() {
			return null;
		}
	}

?>
