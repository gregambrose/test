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

	class policyaccountedittemplate {
		var $DIRECT_HTML = 'policyAccountEditDirect.html';
		var $INDIRECT_HTML = 'policyAccountEdit.html';
		var $iptAmendable = null;

		function policyaccountedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'plCode' );
			$this->addField( 'plGross' );
			$this->addField( 'plCommissionRate' );
			$this->addField( 'plCommission' );
			$this->addField( 'plNet' );
			$this->addField( 'plIPTRate' );
			$this->addField( 'plGrossIPT' );
			$this->addField( 'plAddlGross' );
			$this->addField( 'plAddlCommissionRate' );
			$this->addField( 'plAddlCommission' );
			$this->addField( 'plAddlNet' );
			$this->addField( 'plAddlIPT' );
			$this->addField( 'plAddlIPTRate' );
			$this->addField( 'plAddOnCoverDescription' );
			$this->addField( 'plAddOnGross' );
			$this->addField( 'plAddOnCommissionRate' );
			$this->addField( 'plAddOnCommission' );
			$this->addField( 'plAddOnNet' );
			$this->addField( 'plAddOnIPT' );
			$this->addField( 'plAddOnIPTRate' );
			$this->addField( 'plClientDiscountRate' );
			$this->addField( 'plClientDiscount' );
			$this->addField( 'plEngineeringFee' );
			$this->addField( 'plEngineeringFeeCommRate' );
			$this->addField( 'plEngineeringFeeNet' );
			$this->addField( 'plEngineeringFeeVATRate' );
			$this->addField( 'plEngineeringFeeVAT' );
			$this->addField( 'plBrokerFee' );
			$this->addField( 'plBrokerFeeVATRate' );
			$this->addField( 'plBrokerFeeVAT' );
			$this->addField( 'plClientSubTotal' );
			$this->addField( 'plClientTotal' );
			$this->addField( 'plBrokerSubTotal' );
			$this->addField( 'plInsCoTotal' );
			$this->addField( 'plAddOnTotal' );
			$this->addField( 'plIntroducerCommRate' );
			$this->addField( 'plIntroducerComm' );
			$this->addField( 'plIntroducerCommRate' );
			$this->addField( 'plTotalGrossIncIPT' );
			$this->addField( 'plTotalGross' );
			$this->addField( 'plTotalCommission' );
			$this->addField( 'plTotalNet' );
			$this->addField( 'plDirectClientTotal' );
			$this->addField( 'plDirectClientGrand' );
			$this->addField( 'plDirectBrokerTotal' );
			$this->addField( 'iptAmendable' );
			$this->addField( 'returnTo' );
			$this->setHeader( SITE_NAME );
			$this->addField( 'fullName' );
		}

		function setpolicy($plCode) {
			$this->policy = new Policy( $plCode );
			$policy = &$this->policy;

			$plDirect = $policy->get( 'plDirect' );

			if ($plDirect == 1) {
				$htmlFile = $this->DIRECT_HTML;
			} 
else {
				$htmlFile = $this->INDIRECT_HTML;
			}

			$this->setHTML( $htmlFile );
			$ok = false;
			$x = $policy->get( 'plClassOfBus' );

			if (0 < $x) {
				$cob = new Cob( $x );
				$cbAllowIPTAmend = $cob->get( 'cbAllowIPTAmend' );

				if ($cbAllowIPTAmend == 1) {
					$ok = true;
				}
			}

			$this->iptAmendable = $ok;
			$this->set( 'iptAmendable', $ok );
			$plClient = $policy->get( 'plClient' );

			if (0 < $plClient) {
				$this->client = new Client( $plClient );
				$client = &$this->client;

				$this->setAll( $client->getAllForHTML(  ) );
				$clAddress = $client->get( 'clAddress' );
				$clAddressWithBRs = str_replace( '
', '<br>
', $clAddress );
				$this->set( 'clAddressWithBRs', $clAddressWithBRs );
				$fullName = $client->getFullOrCompanyName(  );
				$this->set( 'clientName', $fullName );
				$policy->decideIPTAndVATRates(  );
			}

			$policy->recalculateAccountingFields(  );
			$this->setAll( $policy->getAllForHTML(  ) );
			$this->set( 'policyNumber', $policy->getForHTML( 'plPolicyNumber' ) );
			$cobName = '';
			$cbCode = $policy->get( 'plClassOfBus' );

			if (0 < $cbCode) {
				$cob = new Cob( $cbCode );
				$cobName = $cob->getForHTML( 'cbName' );
			}

			$this->set( 'classOfBusiness', $cobName );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function getpolicy() {
			return $this->policy;
		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$policy = &$this->policy;

			if (isset( $policy )) {
				$usCode = $policy->get( 'plLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $policy->get( 'plLastUpdateOn' );
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

		function showwhenclientdiscountallowed($text) {
			$policy = &$this->policy;

			$ok = false;

			if (isset( $policy )) {
				$x = $policy->get( 'plClientDisc' );

				if ($x == 1) {
					$ok = true;
				}
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenintroducerdiscountallowed($text) {
			$policy = &$this->policy;

			$ok = false;

			if (isset( $policy )) {
				$x = $policy->get( 'plIntrodComm' );

				if ($x == 1) {
					$ok = true;
				}
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhennoiptamend($text) {
			if ($this->iptAmendable == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwheniptamend($text) {
			if ($this->iptAmendable != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>