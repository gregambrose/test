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

	class clientinvoiceaddresstemplate {
		function clientinvoiceaddresstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'clInvAddTitle' );
			$this->addField( 'clInvAddFirstName' );
			$this->addField( 'clInvAddLastName' );
			$this->addField( 'clInvAddSalutation' );
			$this->addField( 'clInvAddPosition' );
			$this->addField( 'clInvAddAddress' );
			$this->addField( 'clInvAddPostcode' );
			$this->addField( 'clInvAddCountry' );
			$this->addField( 'clInvAddWorkPhone' );
			$this->addField( 'clInvAddMobile' );
			$this->addField( 'clInvAddFax' );
		}

		function setclient($clCode) {
			$this->client = new Client( $clCode );
			$client = &$this->client;

			$client->fetchExtraColumns(  );
			$this->setAll( $client->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getclient() {
			return $this->client;
		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$client = &$this->client;

			if (isset( $client )) {
				$usCode = $client->get( 'clLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $client->get( 'clLastUpdateOn' );
				$when = uformatourtimestamp( $when );
			}


			if ($do == false) {
				return '';
			}

			$this->set( 'lastUpdateBy', $initials );
			$this->set( 'lastUpdatedOn', $when );
			$out = $this->parse( $text );
			return $out;
		}

		function listtitles($text) {
			$q = 'SELECT * FROM titles ORDER BY tiSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clTitle = $this->get( 'clInvAddTitle' );

			while ($row = udbgetrow( $result )) {
				$tiCode = $row['tiCode'];
				$tiName = $row['tiName'];

				if ($tiCode == $clTitle) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'tiCode', $tiCode );
				$this->set( 'tiName', $tiName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>