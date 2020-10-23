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

	class policytransdocmstemplate {
		function policytransdocmstemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function settransaction($ptCode) {
			$this->type = 'PT';
			$transaction = new PolicyTransaction( $ptCode );
			$this->transaction = &$transaction;

			$this->clearDetailFields(  );
			$this->setAll( $transaction->getAllForHTML(  ) );
			$plCode = $transaction->get( 'ptPolicy' );
			$policy = new Policy( $plCode );
			$this->policy = &$policy;

			$clCode = $policy->get( 'plClient' );

			if ($clCode <= 0) {
				trigger_error( 'cant get client', E_USER_ERROR );
			}

			$client = new Client( $clCode );
			$this->client = &$client;

			$this->set( 'polTransNo', sprintf( '%07s', $ptCode ) );
			$this->set( 'policyNumber', $policy->get( 'plPolicyNumber' ) );
			$this->set( 'clName', $client->get( 'clName' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->_setIntroducerFromClient(  );
		}

		function gettransaction() {
			return $this->transaction;
		}

		function getpolicy() {
			return $this->policy;
		}

		function showdocuments($text) {
			$transaction = $this->getTransaction(  );
			$ptCode = $transaction->getKeyValue(  );
			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doTrans=' . $ptCode;
			$q .= ' ORDER BY  doUpdateorCreate DESC,doWhenEntered DESC';
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