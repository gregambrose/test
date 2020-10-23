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

	class policiestemplate {
		var $policies = null;
		var $page = null;
		var $sortType = null;

		function policiestemplate($html) {
			ftemplate::ftemplate( $html );
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
			$this->addField( 'policyHolder' );
			$this->addField( 'plStatusDesc' );
			$this->set( 'plStatus', 1 );
			$this->setProcess( '_setPage', 'doPage' );
			$this->setHeader( SITE_NAME );
			$this->policies = array(  );
			$this->page = 0;
			$this->sortType = '';
		}

		function policytypeselected($type) {
			$plPolicyType = $this->get( 'plPolicyType' );

			if ($plPolicyType == $type) {
				return 'selected';
			}

			return '';
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

				$name = $row['cbName'];

				if (33 < strlen( $name )) {
					$name = substr( $name, 0, 33 ) . '...';
				}

				$this->set( 'cbCode', $row['cbCode'] );
				$this->set( 'cbName', $name );
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

				if (32 < strlen( $icName )) {
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
			$page = $this->page;
			$start = $page * POLICIES_PER_PAGE;
			$end = $start + POLICIES_PER_PAGE - 1;
			$numOfPolicies = count( $this->policies );
			$out = '';
			$rowNo = 0;
			$elem = $start;

			while ($elem <= $end) {
				if ($numOfPolicies <= $elem) {
					break;
				}

				$plCode = &$this->policies[$elem];

				$policy = new Policy( null );
				$found = $policy->tryGettingRecord( $plCode );

				if ($found == false) {
					continue;
				}

				$plPolicyNumber = $policy->get( 'plPolicyNumber' );
				$plPolicyHolder = $policy->get( 'plPolicyHolder' );
				$type = $policy->get( 'plPolicyType' );
				$cbCode = $policy->get( 'plClassOfBus' );
				$icCode = $policy->get( 'plInsCo' );
				$clCode = $policy->get( 'plClient' );
				$this->set( 'plCode', $plCode );
				$plPolicyNumber = $plPolicyNumber;

				if (strlen( $plPolicyNumber ) == 0) {
					$plPolicyNumber = 'none';
				}

				$this->set( 'plPolicyNumber', $plPolicyNumber );
				$this->set( 'plPolicyHolder', $plPolicyHolder );
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
				$plStatusDesc = '';
				$plStatus = $policy->get( 'plStatus' );

				if (0 < $plStatus) {
					$ps = new PolicyStatus( $plStatus );
					$plStatusDesc = $ps->get( 'stName' );
				}

				$this->set( 'plStatusDesc', $plStatusDesc );
				$plInceptionDate = $policy->getForHTML( 'plInceptionDate' );
				$this->set( 'plInceptionDate', $plInceptionDate );
				$plRenewalDate = $policy->getForHTML( 'plRenewalDate' );
				$this->set( 'plRenewalDate', $plRenewalDate );
				$plGrossIncIPT = $policy->getForHTML( 'plGrossIncIPT' );
				$this->set( 'plGrossIncIPT', $plGrossIncIPT );

				if ($rowNo % 2 == 0) {
					$x = ROW_COLOUR_A;
				} 
else {
					$x = ROW_COLOUR_B;
				}

				$this->set( 'rowColour', $x );
				++$rowNo;
				$out .= $this->parse( $text );
				++$elem;
			}

			return $out;
		}

		function showpagenumberlinks($text) {
			$pages = 1 + ( count( $this->policies ) - 1 ) / POLICIES_PER_PAGE;
			$pages = floor( $pages );
			$out = '';
			$elem = 0;

			while ($elem < $pages) {
				if (( $elem < $this->page - 5 && $elem != 0 )) {
					continue;
				}


				if (( $this->page + 5 < $elem && $elem != $pages - 1 )) {
					continue;
				}

				$fontColour = '#3366CC';

				if ($elem == $this->page) {
					$fontColour = 'red';
				}

				$this->set( 'fontColour', $fontColour );
				$this->set( 'actualPage', $elem );
				$displayPage = $elem + 1;

				if ($elem == 0) {
					$displayPage = 'first';
				}


				if ($elem == $pages - 1) {
					$displayPage = 'last';
				}

				$this->set( 'displayPage', $displayPage );
				$out .= $this->parse( $text );
				++$elem;
			}

			return $out;
		}

		function whenpolicyholder($text) {
			$ph = $this->get( 'policyHolder' );

			if (strlen( trim( $ph ) ) == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function _setpage($template, $input) {
			global $sessionName;

			$page = $input['doPage'];
			$this->page = $page;
			$sessionName = $_REQUEST['sn'];
			return false;
		}

		function setsorttype($type) {
			$this->sortType = $type;
		}

		function getsorttype() {
			return $this->sortType;
		}
	}

?>