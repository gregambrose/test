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

	class menutemplate {
		function menutemplate($html) {
			ftemplate::ftemplate( $html );
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
	}

?>