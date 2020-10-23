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

	class renewalstemplate {
		var $policies = null;
		var $page = null;
		var $sortType = null;
		var $doNormalItem = null;
		var $doClientTotal = null;
		var $doMonthTotal = null;
		var $doGrandTotal = null;
		var $subTotal = null;
		var $grandTotal = null;

		function renewalstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportOrder' );
			$this->addField( 'plPolicyType' );
			$this->addField( 'plInsCo' );
			$this->addField( 'plStatus' );
			$this->addField( 'plClassOfBus' );
			$this->addField( 'inceptionFrom' );
			$this->addField( 'inceptionTo' );
			$this->addField( 'renewalFrom' );
			$this->addField( 'renewalTo' );
			$this->addField( 'policyCode' );
			$this->addField( 'policyNumber' );
			$this->addField( 'policiesFound' );
			$this->addField( 'plStatus' );
			$this->addField( 'plStatusShown' );
			$this->setHeader( SITE_NAME );
			$this->policies = null;
			$this->sortType = '';
			$this->set( 'reportOrder', 'C' );
			$this->set( 'plStatus', 1 );
		}

		function policytypeselected($type) {
			$plPolicyType = $this->get( 'plPolicyType' );

			if ($plPolicyType == $type) {
				return 'selected';
			}

			return '';
		}

		function orderselected($type) {
			$reportOrder = $this->get( 'reportOrder' );

			if ($reportOrder == $type) {
				return 'selected';
			}

			return '';
		}

		function getsorttype() {
			return $this->get( 'reportOrder' );
		}

		function showpolicystatus($text) {
			$q = 'SELECT * FROM policyStatus ORDER BY stSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plStatus = $this->get( 'plStatus' );

			while ($row = udbgetrow( $result )) {
				$stCode = $row['stCode'];
				$stName = $row['stName'];

				if ($stCode == $plStatus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'stCode', $stCode );
				$this->set( 'stName', $stName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showwhennopolicystatus() {
			$plStatus = $this->get( 'plStatus' );

			if ($plStatus <= 0) {
				return 'selected';
			}

			return '';
		}

		function whenreporttoview($text) {
			if ($this->policies == null) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showclassofbusiness($text) {
			$q = 'SELECT * FROM classOfBus ORDER BY cbName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plClassOfBus = $this->get( 'plClassOfBus' );

			while ($row = udbgetrow( $result )) {
				$cbCode = $row['cbCode'];

				if ($cbCode == $plClassOfBus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'cbCode', $row['cbCode'] );
				$this->set( 'cbName', $row['cbName'] );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showinsurancecompanies($text) {
			$q = 'SELECT * FROM insuranceCompanies ORDER BY icName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plInsCo = $this->get( 'plInsCo' );

			while ($row = udbgetrow( $result )) {
				$icCode = $row['icCode'];
				$icName = $row['icName'];

				if (33 < strlen( $icName )) {
					$icName = substr( $icName, 0, 32 ) . '...';
				}


				if ($icCode == $plInsCo) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'icCode', $icCode );
				$this->set( 'icName', $icName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpolicies($text) {
			if ($this->policies == null) {
				return '';
			}

			$this->subTotal = 0;
			$this->grandTotal = 0;
			$order = $this->get( 'reportOrder' );
			$currentMonth = null;
			$currentClient = null;
			$clientName = null;
			$monthName = null;
			$numOfPolicies = count( $this->policies );
			$out = '';
			$elem = 0;

			while ($elem <= $numOfPolicies) {
				$plCode = &$this->policies[$elem];

				$policy = new Policy( null );
				$found = $policy->tryGettingRecord( $plCode );

				if ($found == false) {
					continue;
				}

				$this->doNormalItem = true;
				$this->doClientTotal = false;
				$this->doMonthTotal = false;
				$this->doGrandTotal = false;
				$plPolicyNumber = $policy->get( 'plPolicyNumber' );
				$type = $policy->get( 'plPolicyType' );
				$cbCode = $policy->get( 'plClassOfBus' );
				$icCode = $policy->get( 'plInsCo' );
				$clCode = $policy->get( 'plClient' );

				if ($order == 'C') {
					if ($clCode != $currentClient) {
						if ($currentClient != null) {
							$cl = new Client( $currentClient );
							$clName = $cl->getFullOrCompanyName(  );
							$this->set( 'clientName', $clName );
							$this->set( 'subTotal', uformatmoneywithcommas( $this->subTotal ) );
							$this->doClientTotal = true;
							$this->grandTotal += $this->subTotal;
							$this->subTotal = 0;
						}
					}

					$currentClient = $clCode;
				}


				if ($order == 'D') {
					$plRenewalDate = $policy->get( 'plRenewalDate' );
					$month = $this->_makeMonthFromDate( $plRenewalDate );

					if ($month != $currentMonth) {
						if ($currentMonth != null) {
							$this->set( 'month', $currentMonth );
							$this->set( 'subTotal', uformatmoneywithcommas( $this->subTotal ) );
							$this->doMonthTotal = true;
							$this->grandTotal += $this->subTotal;
							$this->subTotal = 0;
						}
					}

					$currentMonth = $month;
				}

				$this->set( 'plCode', $plCode );
				$plPolicyNumber = $plPolicyNumber;

				if (strlen( $plPolicyNumber ) == 0) {
					$plPolicyNumber = 'none';
				}

				$this->set( 'plPolicyNumber', $plPolicyNumber );
				$policyType = '';

				if ($type == 'C') {
					$policyType = 'Commercial';
				}


				if ($type == 'R') {
					$policyType = 'Retail';
				}

				$this->set( 'plPolicyTypeDesc', $policyType );
				$classOfBus = '';

				if (0 < $cbCode) {
					$cob = new Cob( $cbCode );
					$classOfBus = $cob->get( 'cbName' );
				}

				$this->set( 'classOfBus', $classOfBus );
				$clName = '';

				if (0 < $clCode) {
					$cl = new Client( $clCode );
					$clName = $cl->getFullOrCompanyName(  );
				}

				$this->set( 'clName', $clName );
				$this->set( 'clCode', $clCode );
				$insCoName = '';

				if (0 < $icCode) {
					$ins = new Insco( $icCode );
					$insCoName = $ins->get( 'icName' );
				}

				$this->set( 'insCoName', $insCoName );
				$plInceptionDate = $policy->getForHTML( 'plInceptionDate' );
				$this->set( 'plInceptionDate', $plInceptionDate );
				$plRenewalDate = $policy->getForHTML( 'plRenewalDate' );
				$this->set( 'plRenewalDate', $plRenewalDate );
				$plGrossIncIPT = $policy->get( 'plGrossIncIPT' );
				$this->set( 'plGrossIncIPT', uformatmoneywithcommas( $plGrossIncIPT ) );
				$this->subTotal += $plGrossIncIPT;
				$this->doNormalItem = true;
				$out .= $this->parse( $text );
				++$elem;
			}


			if ($order == 'C') {
				if ($currentClient != null) {
					$this->doNormalItem = false;
					$this->doClientTotal = true;
					$this->doMonthTotal = false;
					$this->doGrandTotal = false;
					$cl = new Client( $currentClient );
					$clName = $cl->getFullOrCompanyName(  );
					$this->set( 'clientName', $clName );
					$this->set( 'subTotal', uformatmoneywithcommas( $this->subTotal ) );
					$this->doClientTotal = true;
					$this->grandTotal += $this->subTotal;
					$this->subTotal = 0;
					$out .= $this->parse( $text );
				}
			}


			if ($order == 'D') {
				if ($currentMonth != null) {
					$this->doNormalItem = false;
					$this->doClientTotal = false;
					$this->doMonthTotal = true;
					$this->doGrandTotal = false;
					$this->set( 'month', $currentMonth );
					$this->set( 'subTotal', uformatmoneywithcommas( $this->subTotal ) );
					$this->grandTotal += $this->subTotal;
					$this->subTotal = 0;
					$out .= $this->parse( $text );
				}
			}

			$this->doNormalItem = false;
			$this->doClientTotal = false;
			$this->doMonthTotal = false;
			$this->doGrandTotal = true;
			$this->set( 'grandTotal', uformatmoneywithcommas( $this->grandTotal ) );
			$out .= $this->parse( $text );
			return $out;
		}

		function normalitem($text) {
			if ($this->doNormalItem == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function clienttotal($text) {
			if ($this->doClientTotal == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function monthtotal($text) {
			if ($this->doMonthTotal == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function grandtotal($text) {
			if ($this->doGrandTotal == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function setheaderfields() {
			$type = $this->get( 'plPolicyType' );

			if ($type = 'C') {
				$typeDesc = 'Commercial Only';
			} 
else {
				if ($type = 'R') {
					$typeDesc = 'Retail Only';
				} 
else {
					$typeDesc = '';
				}
			}

			$this->set( 'typeDesc', $typeDesc );
			$icCode = $this->get( 'plInsCo' );

			if (0 < $icCode) {
				$ins = new InsCo( $icCode );
				$insName = $ins->get( 'icName' );
			} 
else {
				$insName = '';
			}

			$this->set( 'insName', $insName );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
			$cbCode = $this->get( 'plClassOfBus' );

			if (0 < $cbCode) {
				$cob = new Cob( $cbCode );
				$cbName = $cob->get( 'cbName' );
			} 
else {
				$cbName = '';
			}

			$this->set( 'cbName', $cbName );
		}

		function _makemonthfromdate($date) {
			$date = uformatsqldate2( $date );
			$out = trim( substr( $date, 2 ) );
			return $out;
		}
	}

?>