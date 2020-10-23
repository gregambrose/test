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

	class clientedittemplate {
		function clientedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'clHandler' );
			$this->addField( 'clHandler' );
			$this->addField( 'clHomeOwner' );
			$this->addField( 'clIntroducer' );
			$this->addField( 'clInvAddress' );
			$this->addField( 'clMaritalStatus' );
			$this->addField( 'clNewBusiness' );
			$this->addField( 'clNonSmoker' );
			$this->addField( 'clSex' );
			$this->addField( 'clSourceOfBus' );
			$this->addField( 'clStatus' );
			$this->addField( 'clStatusEdit' );
			$this->addField( 'clTitle' );
			$this->addField( 'clType' );
			$this->addField( 'clStatementType' );
			$this->addField( 'clDiscount' );
			$this->addField( 'clDurable' );
			$this->addField( 'clBrStatus' );
			$this->addField( 'clLastUpdateBy' );
			$this->addField( 'clLastUpdateOn' );
			$this->addField( 'policyStatus' );
			$this->addField( 'handlerFirst' );
			$this->addField( 'handlerLast' );
			$this->addField( 'inName' );
			$this->addField( 'sbName' );
			$this->addField( 'csName' );
			$this->addField( 'cmName' );
			$this->addField( 'tiName' );
			$this->set( 'policyStatus', 1 );
		}

		function setclient($clCode) {
			$client = new Client( $clCode );
			$this->client = &$client;

			$clType = $client->get( 'clType' );

			if ($clType == COMMERCIAL_TYPE) {
				$this->setHTML( 'clientCommercialEdit.html' );
			} 
else {
				if ($clType == RETAIL_TYPE) {
					$this->setHTML( 'clientRetailEdit.html' );
				} 
else {
					trigger_error( '' . 'incorrect type for ' . $clCode, E_USER_ERROR );
				}
			}

			$this->client->fetchExtraColumns(  );
			$this->setAll( $client->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandeditclient($clCode) {
			$this->setClient( $clCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getclient() {
			return $this->client;
		}

		function whencashbatchexists($text) {
			global $session;

			$out = '';
			$cashBatchEditTemplate = &$session->get( 'cashBatchEditTemplate' );

			if ($cashBatchEditTemplate == null) {
				return '';
			}


			if ($cashBatchEditTemplate->getAllowEditing(  ) == false) {
				return '';
			}


			if ($cashBatchEditTemplate->getItemPayeeType(  ) != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenjournalexists($text) {
			global $session;

			$out = '';
			$journalEditTemplate = &$session->get( 'journalEditTemplate' );

			if ($journalEditTemplate == null) {
				return '';
			}


			if (!is_a( $journalEditTemplate, 'JournalEditTemplate' )) {
				trigger_error( 'not a journal template', E_USER_ERROR );
			}


			if ($journalEditTemplate->getAllowEditing(  ) == false) {
				return '';
			}


			if (substr( $journalEditTemplate->getJournalType(  ), 1, 1 ) != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$client = &$this->client;

			if (isset( $client )) {
				$usCode = $client->get( 'clLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $client->get( 'clLastUpdateOn' );
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

		function listclientspolicies($text) {
			$client = $this->getClient(  );
			$clCode = $client->getKeyValue(  );
			$status = $this->get( 'policyStatus' );
			$q = '' . 'SELECT * FROM policies WHERE plClient=' . $clCode . ' ';

			if (0 < $status) {
				$q .= '' . ' AND plStatus = ' . $status . ' ';
			}

			$q .= ' ORDER BY plPolicyNumber';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$policy = new Policy( $row );
				$this->set( 'plCode', $row['plCode'] );
				$plPolicyNumber = $row['plPolicyNumber'];

				if (strlen( $plPolicyNumber ) == 0) {
					$plPolicyNumber = 'none';
				}

				$this->set( 'plPolicyNumber', $plPolicyNumber );
				$policyType = '';
				$type = $row['plPolicyType'];

				if ($type == 'C') {
					$policyType = 'Commercial';
				}


				if ($type == 'R') {
					$policyType = 'Retail';
				}

				$this->set( 'plPolicyType', $policyType );
				$cbCode = $row['plClassOfBus'];
				$classOfBus = '';

				if (0 < $cbCode) {
					$cob = new Cob( $cbCode );
					$classOfBus = $cob->get( 'cbName' );
				}

				$this->set( 'classOfBus', $classOfBus );
				$icCode = $row['plInsCo'];
				$insCoName = '';

				if (0 < $icCode) {
					$ins = new Insco( $icCode );
					$insCoName = $ins->get( 'icName' );
				}

				$this->set( 'insCoName', $insCoName );
				$plStatus = $policy->get( 'plStatus' );
				$ststus = '';

				if (0 < $plStatus) {
					$ps = new PolicyStatus( $plStatus );
					$status = $ps->get( 'stName' );
				}

				$this->set( 'status', $status );
				$plInceptionDate = $policy->getForHTML( 'plInceptionDate' );
				$this->set( 'plInceptionDate', $plInceptionDate );
				$plRenewalDate = $policy->getForHTML( 'plRenewalDate' );
				$this->set( 'plRenewalDate', $plRenewalDate );
				$plClientTotal = $policy->getForHTML( 'plClientTotal' );
				$this->set( 'plClientTotal', $plClientTotal );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showsourceofbusiness($text) {
			$q = 'SELECT * FROM sourceOfBus ORDER BY sbSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clSourceOfBus = $this->get( 'clSourceOfBus' );

			while ($row = udbgetrow( $result )) {
				$sbCode = $row['sbCode'];
				$sbName = $row['sbName'];

				if ($sbCode == $clSourceOfBus) {
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

		function showcommunmethod($text) {
			$q = 'SELECT * FROM communMethod ORDER BY cmSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clDurable = $this->get( 'clDurable' );

			while ($row = udbgetrow( $result )) {
				$cmCode = $row['cmCode'];
				$cmName = $row['cmName'];

				if ($cmCode == $clDurable) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'cmCode', $cmCode );
				$this->set( 'cmName', $cmName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showdiscount($type) {
			$clDiscount = $this->get( 'clDiscount' );

			if ($type == $clDiscount) {
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
			$plStatus = $this->get( 'policyStatus' );

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
			$plStatus = $this->get( 'policyStatus' );

			if ($plStatus <= 0) {
				return 'selected';
			}

			return '';
		}

		function listtitles($text) {
			$q = 'SELECT * FROM titles ORDER BY tiSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clTitle = $this->get( 'clTitle' );

			while ($row = udbgetrow( $result )) {
				$tiCode = $row['tiCode'];
				$tiName = $row['tiName'];

				if ($tiCode == $clTitle) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'tiCode', $tiCode );
				$this->set( 'tiName', $tiName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showmaritalstatus($text) {
			$q = 'SELECT * FROM maritalStatus ORDER BY msSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clMaritalStatus = $this->get( 'clMaritalStatus' );

			while ($row = udbgetrow( $result )) {
				$msCode = $row['msCode'];
				$msName = $row['msName'];

				if ($msCode == $clMaritalStatus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'msCode', $msCode );
				$this->set( 'msName', $msName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showclientstatus($text) {
			$q = 'SELECT * FROM clientStatus ORDER BY csSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clStatus = $this->get( 'clStatus' );

			while ($row = udbgetrow( $result )) {
				$csCode = $row['csCode'];
				$csName = $row['csName'];

				if ($csCode == $clStatus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'csCode', $csCode );
				$this->set( 'csName', $csName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showintroducers($text) {
			$q = 'SELECT * FROM introducers ORDER BY inName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clIntroducer = $this->get( 'clIntroducer' );

			while ($row = udbgetrow( $result )) {
				$inCode = $row['inCode'];
				$inName = $row['inName'];

				if ($inCode == $clIntroducer) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'inCode', $inCode );
				$this->set( 'inName', $inName );
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
			$clHandler = $this->get( 'clHandler' );

			while ($row = udbgetrow( $result )) {
				$usCode = $row['usCode'];
				$usFirstName = $row['usFirstName'];
				$usLastName = $row['usLastName'];
				$dpName = $row['dpName'];

				if ($usCode == $clHandler) {
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

		function shownewbusiness($type) {
			$clNewBusiness = $this->get( 'clNewBusiness' );

			if ($type == $clNewBusiness) {
				return 'selected';
			}

			return '';
		}

		function showsex($type) {
			$clSex = $this->get( 'clSex' );

			if ($type == $clSex) {
				return 'selected';
			}

			return '';
		}

		function selectnonsmoker($type) {
			$clNonSmoker = $this->get( 'clNonSmoker' );

			if ($type == $clNonSmoker) {
				return 'selected';
			}

			return '';
		}

		function selecthomeowner($type) {
			$clHomeOwner = $this->get( 'clHomeOwner' );

			if ($type == $clHomeOwner) {
				return 'selected';
			}

			return '';
		}

		function selectinvaddress($type) {
			$clInvAddress = $this->get( 'clInvAddress' );

			if ($type == $clInvAddress) {
				return 'selected';
			}

			return '';
		}

		function selectstatementtype($type) {
			$clStatementType = $this->get( 'clStatementType' );

			if ($type == $clStatementType) {
				return 'selected';
			}

			return '';
		}

		function invaddressvalue() {
			$p = $this->get( 'clInvAddress' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function newbusinessvalue() {
			$p = $this->get( 'clNewBusiness' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function discountvalue() {
			$p = $this->get( 'clDiscount' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function statementtypevalue() {
			$p = $this->get( 'clStatementType' );
			$out = '';

			if ($p == 0) {
				$out = 'never';
			}


			if ($p == 1) {
				$out = 'if non-zero balance';
			}


			if ($p == 2) {
				$out = 'if transactions in the period';
			}


			if ($p == 3) {
				$out = 'always';
			}

			return $out;
		}

		function sexvalue() {
			$p = $this->get( 'clSex' );
			$out = '';

			if ($p == 'M') {
				$out = 'Male';
			}


			if ($p == 'F') {
				$out = 'Female';
			}

			return $out;
		}

		function ownervalue() {
			$p = $this->get( 'clHomeOwner' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function smokervalue() {
			$p = $this->get( 'clNonSmoker' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}
	}

?>