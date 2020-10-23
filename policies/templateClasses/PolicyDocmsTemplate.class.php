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

	class policydocmstemplate {
		function policydocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function setpolicy($plCode) {
			$this->type = 'PL';
			$this->policy = new Policy( $plCode );
			$policy = &$this->policy;

			$this->clearDetailFields(  );
			$this->setAll( $policy->getAllForHTML(  ) );
			$clCode = $policy->get( 'plClient' );

			if ($clCode <= 0) {
				trigger_error( 'cant get client', E_USER_ERROR );
			}

			$client = new Client( $clCode );
			$this->client = &$client;

			$this->set( 'policyNumber', $policy->get( 'plPolicyNumber' ) );
			$this->set( 'clName', $client->get( 'clName' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->_setIntroducerFromClient(  );
		}

		function getpolicy() {
			return $this->policy;
		}

		function showdocuments($text) {
			$policy = $this->getPolicy(  );
			$plCode = $policy->getKeyValue(  );
			$doType = $this->get( 'existingType' );
			$selectSent = $this->get( 'selectSent' );
			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doPolicy=' . $plCode;

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

		function showinsurancecompaniesthispolicy($text) {
			$existingCo = $this->get( 'detailInscoCode' );
			$policy = &$this->policy;

			$plInsCo = $policy->get( 'plInsCo' );
			$plAltInsCo = $policy->get( 'plAltInsCo' );
			$q = 'SELECT icCode, icName  FROM insuranceCompanies  ';
			$done = false;

			if (0 < $plInsCo) {
				$q .= '' . 'WHERE icCode=' . $plInsCo;
				$done = true;
			}


			if (0 < $plAltInsCo) {
				if ($done == true) {
					$q .= ' OR ';
				} 
else {
					$q .= ' WHERE ';
				}

				$q .= '' . 'icCode=' . $plAltInsCo;
			}

			$q .= '	ORDER BY icName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ) . $q, E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'icCode', $row['icCode'] );
				$this->set( 'icName', $row['icName'] );

				if ($existingCo == $row['icCode']) {
					$showSelected = 'selected';
				} 
else {
					$showSelected = '';
				}

				$this->set( 'showSelected', $showSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>