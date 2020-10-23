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

	class journalstemplate {
		function journalstemplate($html) {
			ftemplate::ftemplate( $html );
		}

		function showjournals($text) {
			$q = 'SELECT * FROM journals ';
			$q .= ' ORDER by jnCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$jn = new Journal( $row );
				$this->set( 'code', sprintf( '%07d', $jn->get( 'jnCode' ) ) );
				$this->set( 'jnAmount', $jn->getForHTML( 'jnAmount' ) );
				$this->set( 'jnNarrative', $jn->getForHTML( 'jnNarrative' ) );
				$this->set( 'jnPostingDate', uformatsqldate2( $jn->get( 'jnPostingDate' ) ) );
				$type = $jn->get( 'jnType' );
				$typeName = '';

				if ($type == 'CC') {
					$typeName = 'Client Cash Journal';
				}


				if ($type == 'CI') {
					$typeName = 'Ins Co Cash Journal';
				}


				if ($type == 'CN') {
					$typeName = 'Introducer Cash Journal';
				}


				if ($type == 'NC') {
					$typeName = 'Client Non-Cash Journal';
				}


				if ($type == 'NI') {
					$typeName = 'Ins Co Non-Cash Journal';
				}


				if ($type == 'NN') {
					$typeName = 'Introducer Non-Cash Journal';
				}

				$this->set( 'type', $typeName );
				$usCode = $jn->getForHTML( 'jnLastUpdateBy' );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'whoPosted', $usInitials );
				$colour = '#EBFFEA';
				$this->set( 'colour', $colour );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>