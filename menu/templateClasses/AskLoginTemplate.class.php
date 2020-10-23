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

	class asklogintemplate {
		function asklogintemplate($html) {
			ftemplate::ftemplate( $html );
		}

		function dobeforeleaving($input) {
			return 'sorry, you are not logged in yet';
		}
	}

?>