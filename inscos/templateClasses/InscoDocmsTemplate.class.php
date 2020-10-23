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

	class inscodocmstemplate {
		function inscodocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function setinsco($icCode) {
			$this->type = 'IC';
			$this->insco = new Insco( $icCode );
			$insco = &$this->insco;

			$this->clearDetailFields(  );
			$this->setAll( $insco->getAllForHTML(  ) );
			$this->set( 'icCode', $icCode );
			$this->set( 'contactName', $insco->get( 'icDeptContact1' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getinsco() {
			return $this->insco;
		}

		function showdocuments($text) {
			$insco = $this->getInsco(  );
			$icCode = $insco->getKeyValue(  );
			$doType = $this->get( 'existingType' );
			$selectSent = $this->get( 'selectSent' );
			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doInsco=' . $icCode;

			if (0 < $doType) {
				$q .= '' . ' AND doDocmType=' . $doType;
			}


			if ($selectSent == 1) {
				$q .= ' AND doInscoSentWhen != \'0000-00-00\'';
			}


			if ($selectSent == 2) {
				$q .= ' AND doInscoSentWhen = \'0000-00-00\'';
			}


			if ($selectSent == 1) {
				$q .= ' ORDER BY  doInscoSentWhen DESC, doWhenEntered DESC';
			} 
else {
				$q .= ' ORDER BY  doUpdateorCreate DESC,doWhenEntered DESC';
			}

			$out = $this->_displayDocumentsUsingSelect( $q, $text );
			return $out;
		}
	}

?>