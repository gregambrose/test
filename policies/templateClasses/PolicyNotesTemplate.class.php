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

	class policynotestemplate {
		function policynotestemplate($html) {
			notestemplate::notestemplate( $html );
			$this->addField( 'plCode' );
			$this->addField( 'detailPolicyCode' );
			$this->addField( 'detailPolicyNumber' );
			$this->addField( 'detailInscoCode' );
			$this->addField( 'detailAltInscoCode' );
			$this->addField( 'detailClientName' );
			$this->addField( 'detailIntroducerCode' );
			$this->addField( 'detailIntroducerChosen' );
		}

		function setpolicy($plCode) {
			$this->type = 'PL';
			$this->policy = new Policy( $plCode );
			$policy = &$this->policy;

			$this->clearDetailFields(  );
			$this->setAll( $policy->getAllForHTML(  ) );
			$this->set( 'detailPolicyNumber', $policy->get( 'plPolicyNumber' ) );
			$this->set( 'detailInscoCode', $policy->get( 'plInsco' ) );
			$this->set( 'detailAltInscoCode', $policy->get( 'plAltInsco' ) );
			$this->addField( 'detailInscoCode' );
			$this->addField( 'detailAltInscoCode' );
			$name = '';
			$inName = '';
			$inCode = 0;
			$clCode = $policy->get( 'plClient' );

			if (0 < $clCode) {
				$client = new Client( $clCode );
				$this->client = &$client;

				$name = $client->get( 'clName' );
				$inCode = $client->get( 'clIntroducer' );

				if (0 < $inCode) {
					$int = new Introducer( $inCode );
					$inName = $int->get( 'inName' );
				}
			}

			$this->set( 'detailClientName', $name );
			$this->set( 'introducerCodeFromClient', $inCode );
			$this->set( 'introducerNameFromClient', $inName );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getpolicy() {
			return $this->policy;
		}

		function shownotes($text) {
			$policy = $this->getPolicy(  );
			$plCode = $policy->getKeyValue(  );
			$noType = $this->get( 'existingType' );

			if (0 < $noType) {
				$q = '' . 'SELECT * FROM notes WHERE noPolicy=' . $plCode . ' AND noType=' . $noType . ' ORDER BY noUpdateorCreate DESC, noWhenEntered DESC';
			} 
else {
				$q = '' . 'SELECT * FROM notes WHERE noPolicy=' . $plCode . ' ORDER BY  noUpdateorCreate DESC,noWhenEntered DESC';
			}

			$out = $this->_displayNotesUsingSelect( $q, $text );
			return $out;
		}

		function showinsurancecompaniesthispolicy($text) {
			if (!isset( $this->policy )) {
				return '';
			}

			$existingCo = $this->get( 'detailInscoCode' );
			$policy = &$this->policy;

			$plInsCo = $policy->get( 'plInsCo' );
			$plAltInsCo = $policy->get( 'plAltInsCo' );

			if (( $plInsCo < 1 && $plAltInsCo < 1 )) {
				return '';
			}

			$q = 'SELECT icCode, icName  FROM insuranceCompanies WHERE ';
			$done = false;

			if (0 < $plInsCo) {
				$q .= '' . 'icCode=' . $plInsCo;
				$done = true;
			}


			if (0 < $plAltInsCo) {
				if ($done == true) {
					$q .= ' OR ';
				}

				$q .= '' . ' icCode=' . $plAltInsCo;
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
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