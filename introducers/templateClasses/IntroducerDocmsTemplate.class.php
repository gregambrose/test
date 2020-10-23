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

	class introducerdocmstemplate {
		function introducerdocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function setintroducer($inCode) {
			$this->type = 'IN';
			$this->introducer = new Introducer( $inCode );
			$introducer = &$this->introducer;

			$this->clearDetailFields(  );
			$this->setAll( $introducer->getAllForHTML(  ) );
			$this->set( 'inCode', $inCode );
			$this->set( 'contactName', $introducer->get( 'inContact' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getintroducer() {
			return $this->introducer;
		}

		function showdocuments($text) {
			$introducer = $this->getIntroducer(  );
			$inCode = $introducer->getKeyValue(  );
			$doType = $this->get( 'existingType' );
			$selectSent = $this->get( 'selectSent' );
			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doIntroducer=' . $inCode;

			if (0 < $doType) {
				$q .= '' . ' AND doDocmType=' . $doType;
			}


			if ($selectSent == 1) {
				$q .= ' AND doIntroducerSentWhen != \'0000-00-00\'';
			}


			if ($selectSent == 2) {
				$q .= ' AND doIntroducerSentWhen = \'0000-00-00\'';
			}


			if ($selectSent == 1) {
				$q .= ' ORDER BY  doIntroducerSentWhen DESC, doWhenEntered DESC';
			} 
else {
				$q .= ' ORDER BY  doUpdateorCreate DESC,doWhenEntered DESC';
			}

			$out = $this->_displayDocumentsUsingSelect( $q, $text );
			return $out;
		}
	}

?>