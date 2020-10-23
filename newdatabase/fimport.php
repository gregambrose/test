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

	function doimport($from, $to, $handle) {
		$done = 0;
		$docs = 0;

		while (true) {
			$data = fgetcsv( $handle, 9000, ',' );

			if ($data === false) {
				echo 'end of file';
				break;
			}

			$x = count( $data );

			if (( $data == '' || ( $data[0] == '' && $data[1] == '' ) )) {
				continue;
			}


			if (( 0 < $from && $docs < $from )) {
				++$docs;
				continue;
			}


			if (( 0 < $to && $to < $docs )) {
				++$docs;
				break;
			}

			++$docs;
			insertdata( $data );
			++$done;
			echo '' . 'Done ' . $done . '<br>
';
		}

	}

	function insertdata($data) {
		$data = _fixdata( $data );
		$plSourceDocm = $data[0];
		$clientType = $data[1];
		$clientName = $data[2];
		$clientAdd = array(  );
		$clientAdd[] = $data[3];
		$clientAdd[] = $data[4];
		$clientAdd[] = $data[5];
		$clientAdd[] = $data[6];
		$clientAdd[] = $data[7];
		$clientAdd[] = $data[8];
		$clientAdd[] = $data[9];
		$clientAdd[] = $data[10];
		$clientAdd[] = $data[11];
		$clientAdd[] = $data[12];
		$clientPC = $data[13];
		$policyInsCo = $data[14];
		$policyNumber = $data[15];
		$jbSequence = $data[16];
		$policyRenewalDate = $data[17];
		$policyPremium = $data[18];
		$policyType = $data[19];
		$policyCoverDescription = $data[20];
		$policyPaymentMethod = $data[21];
		$policyAddlPremium = $data[22];
		$policyFee = $data[23];
		$newBusiness = $data[24];
		$plStatus = $data[25];
		$plStatusDate = $data[26];
		$clCode = _doclient( $clientType, $clientName, $clientAdd, $clientPC, $policyRenewalDate );
		$icCode = _doinsco( $policyInsCo );
		$plCode = _dopolicy( $clCode, $icCode, $policyInsCo, $policyNumber, $policyRenewalDate, $policyPremium, $policyCoverDescription, $plSourceDocm, $policyPaymentMethod, $policyAddlPremium, $policyFee, $clientType, $policyType, $newBusiness, $plStatus, $plStatusDate, $jbSequence );

		if ($plSourceDocm == '') {
			return null;
		}

		_dodocuments( $clCode, $plCode, $plSourceDocm );
	}

	function _dopolicy($clCode, $icCode, $policyInsCo, $policyNumber, $policyRenewalDate, $policyPremium, $policCoverDescription, $plSourceDocm, $policyPaymentMethod, $policyAddlPremium, $policyFee, $clType, $policyType, $newBusiness, $plStatus, $plStatusDate, $jbSequence) {
		global $brokerVATRate;
		global $companyVATRate;
		global $iptNormalRate;
		global $iptTravelRate;

		$plPolicyType = '';

		if ($clType == 'C') {
			$plPolicyType = 'C';
		}


		if ($clType == 'R') {
			$plPolicyType = 'R';
		}

		$t = trim( $policyType );

		if (( $t == 'C' || $t == 'R' )) {
			$plPolicyType = $t;
		}

		$policyRenewalDate = _fixrenewaldate( $policyRenewalDate );
		$y = umakesqldate2( $policyRenewalDate );
		$x = uaddmonthssqldate( $y, 0 - 12 );
		$prevRenewalDate = uformatsqldate2( $x );
		$policyPremium = _fixmoney( $policyPremium );
		$brokerFeeAmt = 0;
		$amountToAllocate = $policyAddlPremium;
		$policyAddOnPremium = 0;
		$policyAddlPremium = 0;
		$plAddlCoverDesc = '';
		$engFee = 0;
		$vatAmt = 0;
		$addl = strcasecmp( 'AP', substr( $amountToAllocate, 0, 2 ) );
		$brokerFee = strcasecmp( 'APB', substr( $amountToAllocate, 0, 3 ) );
		$vat = strcasecmp( 'VT', substr( $amountToAllocate, 0, 2 ) );

		if ($brokerFee == 0) {
			$addl = 1;
		}


		if ($addl == 0) {
			$policyAddlPremium = _fixmoney( $amountToAllocate );
			$plAddlCoverDesc = 'Terrorism';
		} 
else {
			if ($vat == 0) {
				$vatAmt = _fixmoney( $amountToAllocate );
				$engFee = $policyPremium;
				$policyPremium = 0;
			} 
else {
				if ($brokerFee == 0) {
					$brokerFeeAmt = _fixmoney( $amountToAllocate );
				} 
else {
					$policyAddOnPremium = _fixmoney( $amountToAllocate );
				}
			}
		}

		$policyFee = _fixmoney( $policyFee );
		$pl = new Policy( null );
		$pl->set( 'plClient', $clCode );
		$pl->set( 'plPolicyNumber', $policyNumber );
		$pl->set( 'plCoverDescription', $policCoverDescription );
		$pl->set( 'plInsCo', $icCode );
		$pl->set( 'plSourceDocm', $plSourceDocm );
		$pl->set( 'plPolicyType', $plPolicyType );
		$pl->set( 'plRenewalDate', $policyRenewalDate );
		$pl->set( 'plBrStatus', $policyRenewalDate );
		$pl->set( 'plGrossIncIPT', $policyPremium );
		$pl->set( 'plPremium', $policyPremium );
		$pl->set( 'plAddlGrossIncIPT', $policyAddlPremium );
		$pl->set( 'plAddlCoverDesc', $plAddlCoverDesc );
		$pl->set( 'plAddOnGrossIncIPT', $policyAddOnPremium );
		$pl->set( 'plEngineeringFee', $engFee );
		$pl->set( 'plEngineeringFeeVAT', $vatAmt );
		$pl->set( 'plJBSequence', $jbSequence );
		$pl->set( 'plBrokerFee', $policyFee );

		if ($brokerFeeAmt != 0) {
			$pl->set( 'plBrokerFee', $brokerFeeAmt );
		}

		$pl->set( 'plNewBusiness', 0 - 1 );
		$pl->set( 'plStatus', 1 );
		$pl->set( 'plStatusDate', $prevRenewalDate );
		$pl->set( 'plInceptionDate', $prevRenewalDate );
		$pl->set( 'plSourceOfBus', 9 );
		$pl->set( 'plSaleMethod', 1 );
		$pl->set( 'plDurable', 4 );
		$pl->set( 'plDurableDate', '2005-01-14' );
		$pl->set( 'plIntrodComm', 0 - 1 );
		$pl->set( 'plClientDisc', 0 - 1 );
		$pl->set( 'plFrequency', 12 );
		$pl->set( 'plEnquiryDate', $prevRenewalDate );

		if ($policyPaymentMethod == 1) {
			$plDirect = 0 - 1;
			$pl->set( 'plDirect', $plDirect );
		} 
else {
			if ($policyPaymentMethod == 2) {
				$pl->set( 'plDirect', 1 );
			} 
else {
				$policyPaymentMethod = 1;
				$pl->set( 'plDirect', 0 - 1 );
			}
		}

		$pl->set( 'plPaymentMethod', $policyPaymentMethod );
		$pl->set( 'plGross', $policyPremium );
		$client = new Client( $clCode );

		if ($client->paysIPT(  ) == true) {
			$iptRate = $iptNormalRate;
		} 
else {
			$iptRate = 0;
		}

		$pl->set( 'plIPTRate', $iptRate );
		$pl->set( 'plAddOnIPTRate', $iptRate );
		$pl->set( 'plEngineeringFeeVATRate', $companyVATRate );
		$pl->set( 'plBrokerFeeVATRate', $brokerVATRate );
		$pl->recalculateAccountingFields(  );

		if (( stristr( $policCoverDescription, 'MOTOR' ) !== false || stristr( $policCoverDescription, ' CAR ' ) !== false )) {
			if (0 < $policyAddlPremium) {
				$pl->set( 'plAltInsCo', 1000074 );
			}
		}

		$pl->insert( null );
		$plCode = $pl->getKeyValue(  );
		return $plCode;
	}

	function _dodocuments($clCode, $plCode, $plSourceDocm) {
		$doc = new Document( null );
		$doc->set( 'doClient', $clCode );
		$doc->set( 'doPolicy', $plCode );
		$doc->set( 'doDocmType', 1 );
		$doc->set( 'doWhenEntered', ugettimenow(  ) );
		$doc->set( 'doWhenOriginated', ugettimenow(  ) );
		$doc->set( 'doOriginator', 1 );
		$doc->set( 'doEnteredBy', 1 );
		$doc->set( 'doSubject', 'Imported Renewal Notice 2004/2005' );
		$doc->set( 'doLocked', 1 );
		$doc->set( 'doDocmType', 41 );
		$doc->set( 'doUploadType', 1 );
		$doc->setClientSequence(  );
		$doc->setPolicySequence(  );
		$newDoc = str_replace( '.sxw', '.doc', $plSourceDocm );
		$fromFile = 'wordDocms/' . $newDoc;

		if (!file_exists( $fromFile )) {
			echo '' . 'cant find docm ' . $plSourceDocm . '<br>
';
			return null;
		}

		$contents = file_get_contents( $fromFile );
		$size = strlen( $contents );
		$type = 'application/msword';
		$doc->addDocument( $newDoc, $size, $type, $fromFile );
		$doc->insert( null );
		$cdCode = $doc->getKeyValue(  );
		return $cdCode;
	}

	function _doinsco($insCo) {
		$q = '' . 'SELECT icCode FROM insuranceCompanies WHERE icName=\'' . $insCo . '\'';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (1 <= $num) {
			$row = udbgetrow( $result );
			$icCode = $row['icCode'];
			return $icCode;
		}

		return null;
	}

	function _doclient($clientType, $clientName, $clientAdd, $clientPC, $policyRenewalDate) {
		$clientName = udbmakefieldsafe( $clientName );
		$q = '' . 'SELECT clCode FROM clients WHERE clName =\'' . $clientName . '\'';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (1 <= $num) {
			$row = udbgetrow( $result );
			$clCode = $row['clCode'];
			$client = new Client( $clCode );
			_setearliestdisclosure( $client, $policyRenewalDate );
			$client->update(  );
			return $clCode;
		}

		$client = new Client( null );

		if ($clientType == 'C') {
			$clType = 1;
			$client->set( 'clType', $clType );
		}


		if ($clientType == 'R') {
			$clType = 2;
			$client->set( 'clType', $clType );
		}

		$client->set( 'clName', $clientName );
		$clAddress = '';
		reset( $clientAdd );

		while ($row = each( $clientAdd )) {
			$row = trim( $row['value'] );

			if (strlen( $row ) == 0) {
				continue;
			}

			$clAddress .= $row . '
';
		}

		$client->set( 'clAddress', $clAddress );
		$client->set( 'clPostcode', $clientPC );
		$client->set( 'clInvAddress', 1 );
		$client->set( 'clStatus', 1 );
		$client->set( 'clStatusDate', '2005-01-14' );
		$client->set( 'clClientSince', '2005-01-14' );
		$client->set( 'clSourceOfBus', 9 );
		$client->set( 'clDurable', 4 );
		$client->set( 'clDurableDate', '2005-01-14' );
		$client->set( 'clNewBusiness', 0 - 1 );
		$client->set( 'clDiscount', 0 - 1 );
		_setearliestdisclosure( $client, $policyRenewalDate );
		$client->insert( null );
		$clCode = $client->getKeyValue(  );
		return $clCode;
	}

	function _fixrenewaldate($policyRenewalDate) {
		$policyRenewalDate = str_replace( '.', '/', $policyRenewalDate );
		$formatted = '';

		if (strpos( $policyRenewalDate, '/' ) !== false) {
			if (uvalidatedate( $policyRenewalDate )) {
				$elems = explode( '/', $policyRenewalDate );

				if (count( $elems ) == 3) {
					$day = sprintf( '%2d', $elems[0] );
					$month = $elems[1];
					$year = $elems[2];

					if (strlen( $year ) == 1) {
						$year = '200' . $year;
					}


					if (strlen( $year ) == 2) {
						$year = '20' . $year;
					}

					$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );
					$format = DATE_FORMAT;
					$day = substr( $value, 8, 2 );
					$month = substr( $value, 5, 2 );
					$year = substr( $value, 0, 4 );
					$monthAlpha = _getmonthname( $month );
					$value = str_replace( 'dd', $day, $format );
					$value = str_replace( 'mm', $month, $value );
					$value = str_replace( 'MMM', $monthAlpha, $value );
					$value = str_replace( 'yyyy', $year, $value );
					$formatted = $value;
				}
			}
		}

		return $formatted;
	}

	function _fixmoney($prem) {
		$size = strlen( $prem );
		$out = '';
		$i = 0;

		while ($i < $size) {
			$char = substr( $prem, $i, 1 );

			if (( $char != '.' && !is_numeric( $char ) )) {
				continue;
			}

			$out .= $char;
			++$i;
		}

		$num = preg_match( '' . '/^-?\d*\.\d*$/', $out );

		if ($num == 1) {
			$out = round( $out * 100, 0 );
		}

		return $out;
	}

	function makeheading($data) {
		$headings = array(  );
		$num = sizeof( $data );
		$x = $num - 1;

		while (0 <= $x) {
			if (trim( $data[$x] ) != '') {
				break;
			}

			--$x;
		}

		$num = $x + 1;
		$elem = 1;

		while ($elem < $num) {
			$col = trim( $data[$elem] );
			$headings[] = $col;
			++$elem;
		}

		return $headings;
	}

	function _fixdata($data) {
		$new = array(  );
		$i = 0;

		while ($i < 100) {
			if (isset( $data[$i] )) {
				$new[$i] = $data[$i];
			} 
else {
				$new[$i] = '';
			}

			$fld = $new[$i];

			if (substr( $fld, 0, 1 ) == '"') {
				$fld = substr( $fld, 1 );
			}

			$len = strlen( $fld );

			if (( 0 < $len && substr( $fld, $len - 1, 1 ) == '"' )) {
				$fld = substr( $fld, 0, $len - 1 );
			}

			$new[$i] = $fld;
			++$i;
		}

		return $new;
	}

	function fixstring($in) {
		$num = preg_match( '' . '/^-?\d*\.\d*$/', $in );

		if ($num == 1) {
			$x = round( $in * 100, 0 );
		}

		$x = addslashes( $in );
		$out = trim( $x );
		return $out;
	}

	function _getmonthname($month) {
		$months = array( 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
		$name = '';
		reset( $months );
		foreach ($months as $key => $value) {
			if ($value == $month) {
				$name = $key;
				break;
			}
		}

		return $name;
	}

	function _setearliestdisclosure($client, $policyRenewalDate) {
		if (( $policyRenewalDate == null || $policyRenewalDate == '' )) {
			return null;
		}

		$clBrStatus = $client->get( 'clBrStatus' );
		$x = umakesqldate( $policyRenewalDate );

		if (( ( $x < $clBrStatus || $clBrStatus == '0000-00-00' ) || $clBrStatus == null )) {
			$client->set( 'clBrStatus', $policyRenewalDate );
		}

	}

	ini_set( 'memory_limit', '32M' );
	require( '../include/startup.php' );
	echo 'Starting';

	if (isset( $_GET['demo'] )) {
		$file = 'DEMO.csv';
	} 
else {
		$file = 'structured.csv';
	}

	$handle = fopen( '' . 'imports/' . $file, 'r' );
	$from = 0;

	if (isset( $_GET['from'] )) {
		$from = $_GET['from'];
	} 
else {
		exit( 'you need to specify from and to' );
	}

	$to = 0;

	if (isset( $_GET['to'] )) {
		$to = $_GET['to'];
	} 
else {
		exit( 'you need to specify from and to' );
	}

	doimport( $from, $to, $handle );
	print 'Done';
?>