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

	class introducernotestemplate {
		function introducernotestemplate($html) {
			notestemplate::notestemplate( $html );
		}

		function setintroducer($inCode) {
			$this->type = 'IN';
			$this->introducer = new Introducer( $inCode );
			$introducer = &$this->introducer;

			$this->clearDetailFields(  );
			$this->setAll( $introducer->getAllForHTML(  ) );
			$this->set( 'contactName', $introducer->get( 'inContact' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getintroducer() {
			return $this->introducer;
		}

		function shownotes($text) {
			$introducer = $this->getIntroducer(  );
			$inCode = $introducer->getKeyValue(  );
			$noType = $this->get( 'existingType' );

			if (0 < $noType) {
				$q = '' . 'SELECT * FROM notes WHERE noIntroducer=' . $inCode . ' AND noType=' . $noType . ' ORDER BY noUpdateorCreate DESC, noWhenEntered DESC';
			} 
else {
				$q = '' . 'SELECT * FROM notes WHERE noIntroducer=' . $inCode . ' ORDER BY noUpdateorCreate DESC,noWhenEntered DESC';
			}

			$out = $this->_displayNotesUsingSelect( $q, $text );
			return $out;
		}
	}

?>