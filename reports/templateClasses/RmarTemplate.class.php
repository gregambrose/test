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

	class rmartemplate {
		function rmartemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'reportType' );
			$this->addField( 'newBusiness' );
			$this->addField( 'reportSummary' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'found' );
			$this->setHeader( SITE_NAME );
			$this->set( 'newBusiness', 'B' );
			$this->set( 'reportSummary', 'S' );
		}

		function selectreporttype($type) {
			$reportType = $this->get( 'reportType' );

			if ($reportType == $type) {
				return 'selected';
			}

			return '';
		}

		function selectsummary($type) {
			$reportSummary = $this->get( 'reportSummary' );

			if ($reportSummary == $type) {
				return 'selected';
			}

			return '';
		}

		function selectnew($type) {
			$nb = $this->get( 'newBusiness' );

			if ($nb == $type) {
				return 'selected';
			}

			return '';
		}

		function whenreporttoview($text) {
			$found = $this->get( 'policiesFound' );

			if ($found == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listhdetail($text) {
			global $userCode;

			$q = '' . 'SELECT *  FROM tmpRMAR' . $userCode . ', policies
			  WHERE tmPolicy = plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$policy = new Policy( $row );
				$sbCode = $policy->get( 'plSourceOfBus' );

				if (0 < $sbCode) {
					$sb = new Source( $sbCode );
					$name = $sb->getForHTML( 'sbName' );
				} 
else {
					$name = 'not set';
				}

				$this->set( 'source', $name );
				$this->set( 'plCode', $policy->getForHTML( 'plCode' ) );
				$this->set( 'plPolicyNumber', $policy->getForHTML( 'plPolicyNumber' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listhsummary($text) {
			global $userCode;

			$q = 'SELECT sbCode, sbName FROM sourceOfBus ORDER BY sbName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$sob = array(  );

			while ($row = udbgetrow( $result )) {
				$sobDetails = array(  );
				$sobDetails['name'] = $row['sbName'];
				$sobDetails['number'] = 0;
				$sbCode = $row['sbCode'];
				$sob[$sbCode] = $sobDetails;
			}

			$sobDetails = array(  );
			$sobDetails['name'] = 'not allocated';
			$sobDetails['number'] = 0;
			$sob[0] = $sobDetails;
			$q = '' . 'SELECT plCode, plSourceOfBus  FROM tmpRMAR' . $userCode . ', policies
			  WHERE tmPolicy = plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$sbCode = $row['plSourceOfBus'];
				$sobDetails = $sob[$sbCode];
				++$sobDetails['number'];
				$sob[$sbCode] = $sobDetails;
			}

			$out = '';
			foreach ($sob as $sobDetails) {
				$number = $sobDetails['number'];
				$name = $sobDetails['name'];
				$this->set( 'name', $name );
				$this->set( 'number', $number );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listidetail($text) {
			global $userCode;

			$q = '' . 'SELECT *  FROM tmpRMAR' . $userCode . ', policies
			  WHERE tmPolicy = plCode
			  ORDER BY tmPolicy, tmType DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$policy = new Policy( $row );
				$cbCode = $policy->get( 'tmClassOfBus' );

				if (0 < $cbCode) {
					$cb = new COB( $cbCode );
					$name = $cb->getForHTML( 'cbName' );
				} 
else {
					$name = 'not set';
				}

				$this->set( 'class', $name );
				$this->set( 'plCode', $policy->getForHTML( 'plCode' ) );
				$this->set( 'plPolicyNumber', $policy->getForHTML( 'plPolicyNumber' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listisummary($text) {
			global $userCode;

			$fromDate = trim( $this->get( 'fromDate' ) );
			$fromDate = umakesqldate2( $fromDate );
			$toDate = trim( $this->get( 'toDate' ) );
			$toDate = umakesqldate2( $toDate );
			$q = 'SELECT cbCode, cbName FROM classOfBus
			  WHERE cbRMAR = 1
			  ORDER BY cbName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$cob = array(  );

			while ($row = udbgetrow( $result )) {
				$cobDetails = array(  );
				$cobDetails['cbCode'] = $row['cbCode'];
				$cobDetails['name'] = $row['cbName'];
				$cobDetails['A'] = '';
				$cobDetails['B'] = '';
				$cobDetails['BTotal'] = 0;
				$cobDetails['C'] = '';
				$cobDetails['D'] = '';
				$cobDetails['DTotal'] = 0;
				$cobDetails['E'] = '';
				$cobDetails['F'] = '';
				$cobDetails['FTotal'] = 0;
				$cobDetails['G'] = '';
				$cbCode = $row['cbCode'];
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus  FROM tmpRMAR' . $userCode . ', policies
			  WHERE tmPolicy = plCode AND tmType = \'M\'
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];
				$cobDetails = $cob[$cbCode];
				$cobDetails['A'] = 'Y';
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddonCOB  FROM tmpRMAR' . $userCode . ', policies, insuranceCompanies
			  WHERE tmPolicy = plCode
			  AND icCode = plAltInsCo  AND tmType = \'A\'
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddonCOB'];

				if ($cbCode < 1) {
					continue;
				}


				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$cobDetails = $cob[$cbCode];
				$cobDetails['A'] = 'Y';
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus,
				SUM(ptGrossIncIPT) 	    	 as totalGrossIncIPT,
				SUM(ptAddlGrossIncIPT)  	 as totalAddlGrossIncIPT,
				SUM(ptEngineeringFee)    	 as totalEngineeringFee,
				SUM(ptEngineeringFeeVAT)     as totalEngineeringFeeVAT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit = 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
			  	AND tmType = \'M\'
			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$cGrand = 0;

			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = $row['totalGrossIncIPT'];
				$tot[1] = $row['totalAddlGrossIncIPT'];
				$tot[2] = 0;
				$tot[3] = $row['totalEngineeringFee'];
				$tot[4] = $row['totalEngineeringFeeVAT'];
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus,
				SUM(ptGrossIncIPT) 	    	 as totalGrossIncIPT,
				SUM(ptAddlGrossIncIPT)  	 as totalAddlGrossIncIPT,
				SUM(ptEngineeringFee)    	 as totalEngineeringFee,
				SUM(ptEngineeringFeeVAT)     as totalEngineeringFeeVAT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit != 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
				 AND tmType = \'M\'
	 		  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0 - $row['totalGrossIncIPT'];
				$tot[1] = 0 - $row['totalAddlGrossIncIPT'];
				$tot[2] = 0;
				$tot[3] = 0 - $row['totalEngineeringFee'];
				$tot[4] = 0 - $row['totalEngineeringFeeVAT'];
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddOnCOB,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			 FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions, insuranceCompanies
			  WHERE tmPolicy = plCode
			    AND icCode = plAltInsCo
			  	AND icAddonCOB > 0
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit = 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
 				AND tmType = \'A\'	
     		  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddOnCOB'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddOnCOB,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			 FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions, insuranceCompanies
			  WHERE tmPolicy = plCode
			    AND icCode = plAltInsCo
			  	AND icAddonCOB > 0
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit != 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
				AND tmType = \'A\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddOnCOB'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = 0 - $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			 FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions, insuranceCompanies
			  WHERE tmPolicy = plCode
			    AND icCode = plAltInsCo
			  	AND ( icAddonCOB IS NULL OR icAddonCOB = 0)
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit = 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
				AND tmType = \'A\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			 FROM tmpRMAR' . $userCode . ', policies, policyTransactions, clientTransactions, insuranceCompanies
			  WHERE tmPolicy = plCode
			    AND icCode = plAltInsCo
			  	AND ( icAddonCOB IS NULL OR icAddonCOB = 0)
			  	AND plCode = ptPolicy
			  	AND ptCode = ctPolicyTran
			  	AND ptDebit != 1
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
				AND tmType = \'A\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = 0 - $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$cGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['BTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}


			if ($cGrand != 0) {
				foreach ($cob as $cobDetails) {
					$cbCode = $cobDetails['cbCode'];
					$BTotal = $cobDetails['BTotal'];

					if (0.40000000000000002220446 <= $BTotal / $cGrand) {
						$c = 'Y';
					} 
else {
						$c = '';
					}

					$cobDetails['B'] = $c;
					$cob[$cbCode] = $cobDetails;
				}
			}

			$q = '' . 'SELECT plCode, tmClassOfBus  FROM tmpRMAR' . $userCode . ', policies, insuranceCompanies
			  WHERE tmPolicy = plCode
			  AND plInsCo = icCode
			  AND icType = 3
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$cobDetails = $cob[$cbCode];
				$cobDetails['C'] = 'Y';
				$cobDetails['E'] = 'Y';
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddonCOB
			  FROM tmpRMAR' . $userCode . ', policies, insuranceCompanies
			  WHERE tmPolicy = plCode
			  AND plAltInsCo = icCode
			  AND (icAddonCOB IS NOT NULL AND icAddonCOB != 0)
			  AND icType = 3
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddonCOB'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$cobDetails = $cob[$cbCode];
				$cobDetails['C'] = 'Y';
				$cobDetails['E'] = 'Y';
				$cob[$cbCode] = $cobDetails;
			}

			$dGrand = 0;
			$q = '' . 'SELECT plCode, tmClassOfBus ,
				SUM(ptGrossIncIPT) 	    	 as totalGrossIncIPT,
				SUM(ptAddlGrossIncIPT)  		 as totalAddlGrossIncIPT,
				SUM(ptEngineeringFee)    as totalEngineeringFee,
				SUM(ptEngineeringFeeVAT) as totalEngineeringFeeVAT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions,  insuranceCompanies, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plInsCo = icCode
			  	AND ptCode = ctPolicyTran
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
			    AND icType = 3
			  	AND plCode = ptPolicy
				AND tmType = \'M\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = $row['totalGrossIncIPT'];
				$tot[1] = $row['totalAddlGrossIncIPT'];
				$tot[2] = 0;
				$tot[3] = $row['totalEngineeringFee'];
				$tot[4] = $row['totalEngineeringFeeVAT'];
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$dGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['DTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddonCOB ,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions,  insuranceCompanies, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plAltInsCo = icCode
			  	AND (icAddonCOB IS NOT NULL AND icAddonCOB != 0)
			  	AND ptCode = ctPolicyTran
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
			    AND icType = 3
			  	AND plCode = ptPolicy
				AND tmType = \'A\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddonCOB'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$dGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['DTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, tmClassOfBus ,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions,  insuranceCompanies, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plAltInsCo = icCode
			  	AND (icAddonCOB  IS NULL OR icAddonCOB = 0)
			  	AND ptCode = ctPolicyTran
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
			    AND icType = 3
			  	AND plCode = ptPolicy
			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = 0;
				$tot[1] = 0;
				$tot[2] = $row['totalAddOnGrossIncIPT'];
				$tot[3] = 0;
				$tot[4] = 0;
				$total = 0;
				$i = 0;

				while ($i < 5) {
					$total += $tot[$i];
					++$i;
				}

				$dGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['DTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			foreach ($cob as $cobDetails) {
				$cbCode = $cobDetails['cbCode'];
				$DTotal = $cobDetails['DTotal'];
				$c = '';

				if (( $dGrand != 0 && 0.40000000000000002220446 <= $DTotal / $dGrand )) {
					$c = 'Y';
				}

				$cobDetails['D'] = $c;
				$cob[$cbCode] = $cobDetails;
			}

			$eGrand = 0;
			$q = '' . 'SELECT plCode, tmClassOfBus ,
				SUM(ptGrossIncIPT) 	   		 as totalGrossIncIPT,
				SUM(ptAddlGrossIncIPT) 		 as totalAddlGrossIncIPT,
				SUM(ptEngineeringFee)    as totalEngineeringFee,
				SUM(ptEngineeringFeeVAT) as totalEngineeringFeeVAT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions,  insuranceCompanies, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plInsCo = icCode
			  	AND ptCode = ctPolicyTran
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
			    AND icDelegated = 1
			  	AND plCode = ptPolicy
				AND tmType = \'M\'	
 			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$su86 = udbnumberofrows( $result );

			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['tmClassOfBus'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = $row['totalGrossIncIPT'];
				$tot[1] = $row['totalAddlGrossIncIPT'];
				$tot[2] = $row['totalEngineeringFee'];
				$tot[3] = $row['totalEngineeringFeeVAT'];
				$total = 0;
				$i = 0;

				while ($i < 4) {
					$total += $tot[$i];
					++$i;
				}

				$eGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['FTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}

			$q = '' . 'SELECT plCode, icAddonCOB  ,
				SUM(ptAddOnGrossIncIPT) 		 as totalAddOnGrossIncIPT
			FROM tmpRMAR' . $userCode . ', policies, policyTransactions,  insuranceCompanies, clientTransactions
			  WHERE tmPolicy = plCode
			  	AND plAltInsCo = icCode
			  	AND (icAddonCOB IS NOT NULL AND icAddonCOB != 0)
			    AND icDelegated = 1
			  	AND ptCode = ctPolicyTran
			  	AND ( (ctPaidDate >= \'' . $fromDate . '\' AND ctPaidDate <= \'' . $toDate . '\')
			  	  OR (ctDirectPaidDate >= \'' . $fromDate . '\' AND ctDirectPaidDate <= \'' . $toDate . '\') )
			  	AND ctBalance = 0
				AND tmType = \'A\'	
 			  	AND plCode = ptPolicy
			  GROUP BY plCode
			  ORDER BY tmPolicy';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$su86 += udbnumberofrows( $result );

			while ($row = udbgetrow( $result )) {
				$plCode = $row['plCode'];
				$cbCode = $row['icAddonCOB'];

				if (!isset( $cob[$cbCode] )) {
					continue;
				}

				$tot[0] = $row['totalAddOnGrossIncIPT'];
				$total = 0;
				$i = 0;

				while ($i < 1) {
					$total += $tot[$i];
					++$i;
				}

				$eGrand += $total;
				$cobDetails = $cob[$cbCode];
				$cobDetails['FTotal'] += $total;
				$cob[$cbCode] = $cobDetails;
			}


			if ($eGrand != 0) {
				foreach ($cob as $cobDetails) {
					$cbCode = $cobDetails['cbCode'];
					$FTotal = $cobDetails['FTotal'];

					if (0.40000000000000002220446 <= $FTotal / $eGrand) {
						$c = 'Y';
					} 
else {
						$c = '';
					}

					$cobDetails['G'] = $c;
					$cob[$cbCode] = $cobDetails;
				}
			}

			$out = '';
			foreach ($cob as $cobDetails) {
				$name = $cobDetails['name'];
				$this->set( 'name', $name );
				$this->set( 'AData', $cobDetails['A'] );
				$this->set( 'BData', $cobDetails['B'] );
				$this->set( 'BTotal', uformatmoneywithcommas( $cobDetails['BTotal'] ) );
				$this->set( 'CData', $cobDetails['C'] );
				$this->set( 'DData', $cobDetails['D'] );
				$this->set( 'DTotal', uformatmoneywithcommas( $cobDetails['DTotal'] ) );
				$this->set( 'EData', $cobDetails['E'] );
				$this->set( 'FData', $cobDetails['F'] );
				$this->set( 'FTotal', uformatmoneywithcommas( $cobDetails['FTotal'] ) );
				$this->set( 'GData', $cobDetails['G'] );
				$out .= $this->parse( $text );
			}

			$this->set( 'BGrandTotal', uformatmoneywithcommas( $cGrand ) );
			$this->set( 'FGrandTotal', uformatmoneywithcommas( $eGrand ) );
			$this->set( 'su86', $su86 );
			return $out;
		}

		function listtransactions($text) {
			if ($this->transactions == null) {
				return '';
			}

			$this->subTotal = 0;
			$this->grandTotal = 0;
			$order = $this->get( 'reportOrder' );
			$currentMonth = null;
			$currentClient = null;
			$clientName = null;
			$monthName = null;
			$icCode = null;
			$icCurrentCode = null;
			$icName = null;
			$this->doTransaction = true;
			$this->doCompanyTotal = false;
			$this->doGrandTotal = false;
			$this->origTotal = 0;
			$this->balanceTotal = 0;
			$this->origGrand = 0;
			$this->balanceGrand = 0;
			$numOfTrans = count( $this->transactions );
			$out = '';
			$elem = 0;

			while ($elem <= $numOfTrans) {
				$itCode = &$this->transactions[$elem];

				$it = new Rmaraction( null );
				$found = $it->tryGettingRecord( $itCode );

				if ($found == false) {
					continue;
				}

				$icCode = $it->get( 'itInsCo' );
				$icName = '';
				$this->doCompanyTotal = false;

				if ($icCurrentCode != $icCode) {
					if ($icCurrentCode != null) {
						$this->doCompanyTotal = true;
						$this->set( 'nameForTotal', $this->get( 'icName' ) );
					}

					$icCurrentCode = $icCode;
					$ins = new Insco( $icCode );
					$icName = $ins->get( 'icName' );
					$this->set( 'icName', $icName );
				}

				$this->set( 'mainGross', $it->get( 'itGross' ) );
				$this->set( 'mainCommission', $it->get( 'itCommission' ) );
				$this->set( 'mainRate', $it->get( 'itCommissionRate' ) );
				$this->set( 'mainIPT', $it->get( 'itGrossIPT' ) );
				$this->set( 'addlGross', $it->get( 'itAddlGross' ) );
				$this->set( 'addlCommission', $it->get( 'itAddlCommission' ) );
				$this->set( 'addlRate', $it->get( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPT', $it->get( 'itAddlIPT' ) );
				$this->set( 'fees', $it->get( 'itEngineeringFee' ) );
				$this->set( 'feesVAT', $it->get( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRate', $it->get( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommission', $it->get( 'itEngineeringFeeComm' ) );
				$this->set( 'mainGrossFormatted', $it->getAsMoneyWithCommas( 'itGross' ) );
				$this->set( 'mainCommissionFormatted', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'mainRateFormatted', $it->getAsMoneyWithCommas( 'itCommissionRate' ) );
				$this->set( 'mainIPTFormatted', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'addlGrossFormatted', $it->getAsMoneyWithCommas( 'itAddlGross' ) );
				$this->set( 'addlCommissionFormatted', $it->getAsMoneyWithCommas( 'itAddlCommission' ) );
				$this->set( 'addlRateFormatted', $it->getAsMoneyWithCommas( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPTFormatted', $it->getAsMoneyWithCommas( 'itAddlIPT' ) );
				$this->set( 'feesFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feesVATFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRateFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommissionFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$date = $it->getForHTML( 'itPostingDate' );
				$paidDate = $it->getForHTML( 'itPaidDate' );
				$direct = $it->get( 'itDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$grossEtc = $it->get( 'itGross' ) + $it->get( 'itGrossIPT' ) + $it->get( 'itAddlGross' ) + $it->get( 'itAddlIPT' ) + $it->get( 'itEngineeringFee' ) + $it->get( 'itEngineeringFeeVAT' );
				$ptCode = $it->get( 'itPolicyTran' );
				$pt = new PolicyTransaction( $ptCode );
				$polNo = $pt->get( 'ptPolicyNumber' );
				$effectiveDate = $pt->getForHTML( 'ptEffectiveFrom' );
				$this->set( 'ptCode', $ptCode );
				$covDesc = $pt->get( 'ptAddlCoverDesc' );
				$this->set( 'addlCoverDesc', $covDesc );
				$feeDesc = $pt->get( 'ptEngineeringFeeDesc' );
				$this->set( 'icFeeDesc', $feeDesc );
				$client = '';
				$clCode = $pt->get( 'ptClient' );

				if (0 < $clCode) {
					$cl = new Client( $clCode );
					$client = $cl->getDisplayName(  );
				}

				$plCode = $pt->get( 'ptPolicy' );

				if (0 < $plCode) {
					$pl = new Policy( $plCode );
					$ph = trim( $pl->get( 'plPolicyHolder' ) );

					if (0 < strlen( $ph )) {
						$client .= '' . '<br>' . $ph;
					}
				}

				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$transType = $tranType->get( 'pyName' );
				$this->set( 'itCode', $it->getKeyValue(  ) );
				$this->set( 'transDate', $date );
				$this->set( 'effectiveDate', $effectiveDate );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'invNo', $it->getForHTML( 'itInvoiceNo' ) );
				$this->set( 'polNo', $polNo );
				$this->set( 'client', $client );
				$this->set( 'transType', $transType );
				$this->set( 'direct', $direct );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'grossEtc', uformatmoneywithcommas( $grossEtc ) );
				$this->set( 'net', $it->getAsMoneyWithCommas( 'itNet' ) );
				$this->set( 'comm', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'ipt', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'fees', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feescomm', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$this->set( 'vat', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'orig', $it->getAsMoneyWithCommas( 'itOriginal' ) );
				$this->set( 'balance', $it->getAsMoneyWithCommas( 'itBalance' ) );
				$this->set( 'paid', $it->getAsMoneyWithCommas( 'itPaid' ) );
				$out .= $this->parse( $text );
				$this->origTotal += $it->get( 'itOriginal' );
				$this->balanceTotal += $it->get( 'itBalance' );
				$this->origGrand += $it->get( 'itOriginal' );
				$this->balanceGrand += $it->get( 'itBalance' );
				++$elem;
			}


			if ($icCurrentCode != null) {
				$this->doTransaction = false;
				$this->doCompanyTotal = true;
				$this->doGrandTotal = true;
				$this->set( 'nameForTotal', $this->get( 'icName' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whenmainpremium($text) {
			$do = false;

			if ($this->get( 'mainNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaddlpremium($text) {
			$do = false;

			if ($this->get( 'addlNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenfees($text) {
			$do = false;

			if ($this->get( 'fees' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesVAT' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesCommission' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function companytotal($text) {
			if ($this->doCompanyTotal == false) {
				return '';
			}

			$this->set( 'origTotal', uformatmoneywithcommas( $this->origTotal ) );
			$this->set( 'balanceTotal', uformatmoneywithcommas( $this->balanceTotal ) );
			$this->origTotal = 0;
			$this->balanceTotal = 0;
			$out = $this->parse( $text );
			return $out;
		}

		function grandtotal($text) {
			if ($this->doGrandTotal == false) {
				return '';
			}

			$icCode = $this->get( 'insCo' );

			if (0 < $icCode) {
				return '';
			}

			$this->set( 'origTotal', uformatmoneywithcommas( $this->origGrand ) );
			$this->set( 'balanceTotal', uformatmoneywithcommas( $this->balanceGrand ) );
			$out = $this->parse( $text );
			return $out;
		}

		function whentranstodo($text) {
			if ($this->doTransaction == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenmanycompanies($text) {
			$icCode = $this->get( 'insCo' );

			if (0 < $icCode) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function setheaderfields() {
			$nb = $this->get( 'newBusiness' );

			if ($nb == 'N') {
				$name = 'New Business Only';
			} 
else {
				if ($nb == 'E') {
					$name = 'Existing Business Only';
				} 
else {
					if ($nb == 'B') {
						$name = 'New & Existing Business';
					} 
else {
						$name = '';
					}
				}
			}

			$this->set( 'newBus', $name );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
		}

		function _makemonthfromdate($date) {
			$date = uformatsqldate2( $date );
			$out = trim( substr( $date, 2 ) );
			return $out;
		}
	}

?>