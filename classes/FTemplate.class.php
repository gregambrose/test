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

	class ftemplate {
		var $returnTo = null;
		var $accPeriodDesc = null;

		function ftemplate($htmlFile) {
			utemplate::utemplate( $htmlFile );
			$this->setProcess( 'fHandleLinks', 'link' );
			$this->returnTo = array(  );
			$this->setHelpPage( SITE_MAIN_HELP_PAGE );
		}

		function runaftersettinghtml() {
			global $sessionName;

			if (isset( $sessionName )) {
				$this->set( 'sessionName', $sessionName );
				$x = substr( $sessionName, 2 );

				if (0 < strlen( $x )) {
					$x = 'Session ' . $x;
				}

				$this->set( 'htmlSessionName', $x );
			}

			$this->setHeader( SITE_NAME );
			$this->set( 'SCRIPT_NAME', SCRIPT_NAME );
			$this->set( 'SITE_COLOUR', SITE_COLOUR );
			$this->set( 'SITE_WARNING', SITE_WARNING );

			if (defined( 'ROW_COLOUR_HEADER' )) {
				$x = ROW_COLOUR_HEADER;
			} 
else {
				$x = 'white';
			}

			$this->set( 'ROW_HEADER', $x );

			if (defined( 'ROW_COLOUR_A' )) {
				$x = ROW_COLOUR_A;
			} 
else {
				$x = 'white';
			}

			$this->set( 'ROW_A', $x );

			if (defined( 'ROW_COLOUR_B' )) {
				$x = ROW_COLOUR_B;
			} 
else {
				$x = 'white';
			}

			$this->set( 'ROW_B', $x );

			if (defined( 'ROW_COLOUR_MARKED' )) {
				$x = ROW_COLOUR_MARKED;
			} 
else {
				$x = 'white';
			}

			$this->set( 'ROW_MARKED', $x );

			if (defined( 'ROW_COLOUR_VISITED' )) {
				$x = ROW_COLOUR_VISITED;
			} 
else {
				$x = 'white';
			}

			$this->set( 'ROW_VISITED', $x );
			$this->set( 'SITE_WARNING', SITE_WARNING );
			$this->addField( 'copyright' );

			if (defined( 'COPYRIGHT_MESSAGE' )) {
				$messg = COPYRIGHT_MESSAGE;
			} 
else {
				$messg = 'Copyright';
			}

			$this->set( 'copyright', $messg );
		}

		function dobeforeleaving($input) {
			if ($this->allowExit == true) {
				$text = '';
			} 
else {
				$text = 'not updated yet';
			}

			return $text;
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

		function getname() {
			global $session;

			$user = $session->get( 'user' );

			if ($user == null) {
				return '';
			}

			$name = $user->getFullName(  );
			return $name;
		}

		function setreturnto($returnTo) {
			$this->returnTo[] = $returnTo;
		}

		function popreturnto() {
			return array_pop( $this->returnTo );
		}

		function getperioddesc() {
			global $accountingYear;
			global $accountingYearDesc;
			global $accountingPeriod;
			global $periodFrom;
			global $periodTo;

			$per = $accountingPeriod;

			if (( 0 < $per && $per < 10 )) {
				$per = '0' . $per;
			}

			return '' . $per . '/' . $accountingYearDesc;
		}

		function setperioddesc($period, $year) {
			$q = '' . 'SELECT ayCode, ayName FROM accountingYears WHERE ayYear = ' . $year;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (udbnumberofrows( $result ) == 0) {
				return 'undefined';
			}

			$row = udbgetrow( $result );
			$yr = $row['ayName'];
			$this->accPeriodDesc = $period . '/' . $yr;
			return $desc;
		}
	}

?>