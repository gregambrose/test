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

	class clientdocmstemplate {
		function clientdocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
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

		function showdocuments($text) {
			$client = $this->getClient(  );
			$clCode = $client->getKeyValue(  );
			$doType = $this->get( 'existingType' );
			$selectSent = $this->get( 'selectSent' );
			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doClient=' . $clCode;

			if (0 < $doType) {
				$q .= '' . ' AND doDocmType=' . $doType;
			}


			if ($selectSent == 1) {
				$q .= ' AND doClientSentWhen != \'0000-00-00\'';
			}


			if ($selectSent == 2) {
				$q .= ' AND doClientSentWhen = \'0000-00-00\'';
			}


			if ($selectSent == 1) {
				$q .= ' ORDER BY  doClientSentWhen DESC, doWhenEntered DESC';
			} 
else {
				$q .= ' ORDER BY  doUpdateorCreate DESC,doWhenEntered DESC';
			}

			$out = $this->_displayDocumentsUsingSelect( $q, $text );
			return $out;
		}
	}

?>