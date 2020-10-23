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

	class insconotestemplate {
		function insconotestemplate($html) {
			notestemplate::notestemplate( $html );
		}

		function setinsco($icCode) {
			$this->type = 'IC';
			$this->insco = new Insco( $icCode );
			$insco = &$this->insco;

			$this->clearDetailFields(  );
			$this->setAll( $insco->getAllForHTML(  ) );
			$this->set( 'contactName', $insco->get( 'icDeptContact1' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getinsco() {
			return $this->insco;
		}

		function shownotes($text) {
			$insco = $this->getInsco(  );
			$icCode = $insco->getKeyValue(  );
			$noType = $this->get( 'existingType' );

			if (0 < $noType) {
				$q = '' . 'SELECT * FROM notes WHERE noInsco=' . $icCode . ' AND noType=' . $noType . ' ORDER BY noUpdateorCreate DESC, noWhenEntered DESC';
			} 
else {
				$q = '' . 'SELECT * FROM notes WHERE noInsco=' . $icCode . ' ORDER BY  noUpdateorCreate DESC,noWhenEntered DESC';
			}

			$out = $this->_displayNotesUsingSelect( $q, $text );
			return $out;
		}
	}

?>