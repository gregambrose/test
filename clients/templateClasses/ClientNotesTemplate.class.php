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

	class clientnotestemplate {
		function clientnotestemplate($html) {
			notestemplate::notestemplate( $html );
			$this->type = 'CL';
		}

		function setclient($clCode) {
			$this->client = new Client( $clCode );
			$client = &$this->client;

			$this->clearDetailFields(  );
			$this->setAll( $client->getAllForHTML(  ) );
			$this->set( 'contactName', $client->getFullName(  ) );
			$this->set( 'detailClientName', $client->get( 'clName' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->_setIntroducerFromClient(  );
		}

		function getclient() {
			return $this->client;
		}

		function shownotes($text) {
			$client = $this->getClient(  );
			$clCode = $client->getKeyValue(  );
			$noType = $this->get( 'existingType' );

			if (0 < $noType) {
				$q = '' . 'SELECT * FROM notes WHERE noClient=' . $clCode . ' AND noType=' . $noType . ' ORDER BY noUpdateorCreate DESC, noWhenEntered DESC';
			} 
else {
				$q = '' . 'SELECT * FROM notes WHERE noClient=' . $clCode . ' ORDER BY  noUpdateorCreate DESC,noWhenEntered DESC';
			}

			$out = $this->_displayNotesUsingSelect( $q, $text );
			return $out;
		}
	}

?>