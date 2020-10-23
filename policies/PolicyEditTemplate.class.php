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

	class policyedittemplate {
		function policyedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'plCode' );
			$this->addField( 'plAltInsCo' );
			$this->addField( 'plClassOfBus' );
			$this->addField( 'plClient' );
			$this->addField( 'plClientDisc' );
			$this->addField( 'plDirect' );
			$this->addField( 'plHandler' );
			$this->addField( 'plEnquiryDate' );
			$this->addField( 'plFrequency' );
			$this->addField( 'plInceptionDate' );
			$this->addField( 'plIntrodComm' );
			$this->addField( 'plInsCo' );
			$this->addField( 'plNewBusiness' );
			$this->addField( 'plPaymentMethod' );
			$this->addField( 'plPolDocsDue' );
			$this->addField( 'plPolicyType' );
			$this->addField( 'plSaleMethod' );
			$this->addField( 'plStatus' );
			$this->addField( 'plStatusDate' );
			$this->addField( 'plSourceOfBus' );
			$this->addField( 'plTORDate' );
			$this->addField( 'returnTo' );
			$this->addField( 'fullName' );
			$this->addField( 'fullNameAbbreviated' );
			$this->setHeader( SITE_NAME );
		}

		function setpolicy($plCode) {
			$this->policy = new Policy( $plCode );
			$policy = &$this->policy;

			$this->setAll( $policy->getAllForHTML(  ) );
			$plClient = $this->get( 'plClient' );

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
				$this->set( 'fullNameAbbreviated', substr( $fullName, 0, 25 ) );
				$this->set( 'fullName', $fullName );
			}

			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandeditpolicy($plCode) {
			$this->setPolicy( $plCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
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

		function showsourceofbusiness($text) {
			$q = 'SELECT * FROM sourceOfBus ORDER BY sbSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plSourceOfBus = $this->get( 'plSourceOfBus' );

			while ($row = udbgetrow( $result )) {
				$sbCode = $row['sbCode'];
				$sbName = $row['sbName'];

				if ($sbCode == $plSourceOfBus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'sbCode', $sbCode );
				$this->set( 'sbName', $sbName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showpolicysalemethod($text) {
			$q = 'SELECT * FROM policySaleMethods ORDER BY psSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plSaleMethod = $this->get( 'plSaleMethod' );

			while ($row = udbgetrow( $result )) {
				$psCode = $row['psCode'];
				$psName = $row['psName'];

				if ($psCode == $plSaleMethod) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'psCode', $psCode );
				$this->set( 'psName', $psName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showpaymentmethod($text) {
			$q = 'SELECT * FROM policyPaymentMethods ORDER BY pmSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plPaymentMethod = $this->get( 'plPaymentMethod' );

			while ($row = udbgetrow( $result )) {
				$pmCode = $row['pmCode'];
				$pmName = $row['pmName'];

				if ($pmCode == $plPaymentMethod) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'pmCode', $pmCode );
				$this->set( 'pmName', $pmName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
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
				$cbName = $row['cbName'];

				if ($cbCode == $plClassOfBus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'cbCode', $cbCode );
				$this->set( 'cbName', $cbName );
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

		function showaltinsurancecompanies($text) {
			$q = 'SELECT * FROM insuranceCompanies ORDER BY icName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plInsCo = $this->get( 'plAltInsCo' );

			while ($row = udbgetrow( $result )) {
				$icCode = $row['icCode'];
				$icName = $row['icName'];

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

		function showhandlers($text) {
			$q = 'SELECT * FROM users, departments WHERE usHandler=1 AND usDepartment=dpCode ORDER BY usFirstName, usLastName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$plHandler = $this->get( 'plHandler' );

			while ($row = udbgetrow( $result )) {
				$usCode = $row['usCode'];
				$usFirstName = $row['usFirstName'];
				$usLastName = $row['usLastName'];
				$dpName = $row['dpName'];

				if ($usCode == $plHandler) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$name = '' . $usFirstName . ' ' . $usLastName . ' - ' . $dpName;
				$this->set( 'usCode', $usCode );
				$this->set( 'handlerAndDept', $name );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpolicynotes($text) {
			$policy = &$this->policy;

			$plCode = $policy->get( 'plCode' );

			if ($plCode < 1) {
				return '';
			}

			$q = '' . 'SELECT * FROM notes WHERE noPolicy = ' . $plCode . ' ORDER BY noPolicySequence DESC, noWhenOriginated DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$noCode = $row['noCode'];
				$noSubject = $row['noSubject'];
				$noWhenEntered = $row['noWhenEntered'];
				$noWhenOriginated = $row['noWhenOriginated'];
				$noOriginator = $row['noOriginator'];
				$usCode = $row['noEnteredBy'];

				if (( $noWhenEntered == null || strlen( $noWhenEntered ) == 0 )) {
					$noWhenEntered = $noWhenOriginated;
					$usCode = $noOriginator;
				}

				$noWhenEntered = uformatourtimestamp( $noWhenEntered );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'noCode', $noCode );
				$this->set( 'noSubject', trim( $noSubject ) );
				$this->set( 'usInitials', $usInitials );
				$this->set( 'noWhenEntered', $noWhenEntered );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpolicydocs($text) {
			$plCode = $this->get( 'plCode' );

			if ($plCode < 1) {
				return '';
			}

			$q = '' . 'SELECT * FROM documents WHERE doDeleted != 1 AND doPolicy = ' . $plCode . '  ORDER BY doWhenEntered DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$doc = new Document( $row );
				$doCode = $row['doCode'];
				$doSubject = $row['doSubject'];
				$doWhenEntered = $row['doWhenEntered'];
				$doWhenOriginated = $row['doWhenOriginated'];
				$doOriginator = $row['doOriginator'];
				$usCode = $row['doEnteredBy'];

				if (( $doWhenEntered == null || strlen( $doWhenEntered ) == 0 )) {
					$doWhenEntered = $doWhenOriginated;
					$usCode = $doOriginator;
				}

				$doWhenEntered = uformatourtimestamp( $doWhenEntered );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'doCode', $doCode );
				$this->set( 'doSubject', trim( $doSubject ) );
				$this->set( 'usInitials', $usInitials );
				$this->set( 'doWhenEntered', $doWhenEntered );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpolicytrans($text) {
			$plCode = $this->get( 'plCode' );

			if ($plCode < 1) {
				return '';
			}

			$out = '';
			return $out;
		}

		function policytypeselected($type) {
			$plPolicyType = $this->get( 'plPolicyType' );

			if ($plPolicyType == $type) {
				return 'selected';
			}

			return '';
		}

		function shownewbusiness($type) {
			$plNewBusiness = $this->get( 'plNewBusiness' );

			if ($type == $plNewBusiness) {
				return 'selected';
			}

			return '';
		}

		function showselecteddirect($type) {
			$plDirect = $this->get( 'plDirect' );

			if ($type == $plDirect) {
				return 'selected';
			}

			return '';
		}

		function showintrodcomm($type) {
			$plIntrodComm = $this->get( 'plIntrodComm' );

			if ($type == $plIntrodComm) {
				return 'selected';
			}

			return '';
		}

		function showclientdisc($type) {
			$plClientDisc = $this->get( 'plClientDisc' );

			if ($type == $plClientDisc) {
				return 'selected';
			}

			return '';
		}

		function showpoldocsdue($type) {
			$plPolDocsDue = $this->get( 'plPolDocsDue' );

			if ($type == $plPolDocsDue) {
				return 'selected';
			}

			return '';
		}

		function showdepartmentname() {
			$usCode = $this->get( 'plHandler' );

			if ($usCode < 1) {
				return '';
			}

			$user = new User( $usCode );
			$dpCode = $user->get( 'usDepartment' );

			if ($dpCode < 1) {
				return '';
			}

			$dept = new Department( $dpCode );
			$name = $dept->get( 'dpName' );
			return $name;
		}

		function frequencyselected($type) {
			$plFrequency = $this->get( 'plFrequency' );

			if ($type == $plFrequency) {
				return 'selected';
			}

			return '';
		}
	}

?>