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

	function doimport($from, $to, $a) {
		$done = 0;
		$docs = 0;
		$dataRows = $a[0]['SHEET_DATA'];
		$rows = count( $dataRows );
		$row = 0;

		while ($row < $rows) {
			$data = $dataRows[$row];
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
			insertdata( $docs, $data );
			++$done;
			echo '' . 'Done ' . $done . '<br>
';
			++$row;
		}

	}

	function insertdata($row, $data) {
		global $user;

		$data = _fixdata( $data );
		$polNumOrig = _stripasterix( $data[0] );
		$jbSequence = $data[1];
		$clientName = $data[2];
		$isInvAddress = $data[3];
		$invName = $data[4];
		$invAddress = $data[5];
		$invPostcode = $data[6];
		$polType = $data[7];
		$initials = $data[8];
		$oldCOB = $data[9];
		$handler = $data[10];
		$cob = $data[11];
		$gross = fformatmoney( $data[12] );
		$commRate = fformatmoney( $data[13] );
		$commission = fformatmoney( $data[14] );
		$addlDesc = $data[15];
		$addlPrem = fformatmoney( $data[16] );
		$addlCommRate = fformatmoney( $data[17] );
		$addlCommission = fformatmoney( $data[18] );
		$ignore = $data[19];
		$ignore = $data[20];
		$addOnPrem = fformatmoney( $data[21] );
		$addOnCommRate = fformatmoney( $data[22] );
		$addOnComm = fformatmoney( $data[23] );
		$engFees = fformatmoney( $data[24] );
		$engFeesCommRate = fformatmoney( $data[25] );
		$engFeesComm = fformatmoney( $data[26] );
		$engFeesVAT = fformatmoney( $data[27] );
		$isClientDisc = $data[28];
		$clDiscRate = fformatmoney( $data[29] );
		$clDiscount = fformatmoney( $data[30] );
		$brokerFee = fformatmoney( $data[31] );
		$isIntrod = $data[32];
		$introdCode = $data[33];
		$introdCommRate = fformatmoney( $data[34] );
		$introdComm = fformatmoney( $data[35] );
		$transType = $data[36];
		$transDesc = $data[37];
		$coverDesc = $data[38];
		$postDate = _stripasterix( $data[39] );
		$fromDate = _stripasterix( $data[40] );
		$toDate = _stripasterix( $data[41] );
		$policyStatus = $data[42];
		$statusDate = _stripasterix( $data[43] );
		$clTotal = fformatmoney( $data[44] );
		$clBalance = fformatmoney( $data[45] );
		$amountPaid = fformatmoney( $data[46] );
		$datePaid = _stripasterix( $data[47] );
		$dateDirectPaid = $data[48];
		$coverDesc = addslashes( $coverDesc );

		if ($polNumOrig == 'TOTALS') {
			_dototals( $gross, $addlPrem, $addOnPrem, $engFees, $clDiscount, $brokerFee, $clTotal, $amountPaid );
			exit(  );
		}

		$user = null;

		if (0 < $handler) {
			$user = new User( $handler );
		}


		if (strlen( trim( $polNumOrig ) ) == 0) {
			print '' . 'no policy number for line ' . $row . '<br>';
			return null;
		}

		$pn = trim( $polNumOrig );

		if (strcasecmp( 'to be advised', substr( $pn, 0, 13 ) ) == 0) {
			if ($jbSequence < 1) {
				print '' . 'no seq number ' . $row . '<br>';
				return null;
			}

			$q = '' . 'SELECT plCode FROM policies WHERE plJBSequence=' . $jbSequence;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( '' . 'row ' . $row, E_USER_ERROR );
			}
		} 
else {
			$q = '' . 'SELECT plCode FROM policies WHERE plPolicyNumber=\'' . $polNumOrig . '\'';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( '' . 'row ' . $row . ' ' . udblasterror(  ), E_USER_ERROR );
			}
		}

		$num = udbnumberofrows( $result );

		if ($num == 0) {
			print '' . 'cant get policy number ' . $polNumOrig . ' , ' . $clientName . ' for line ' . $row . '<br>';
			udbfreeresult( $result );
			return null;
		}


		if (1 < $num) {
			print '' . 'duplicate policy number ' . $polNumOrig . ' , ' . $clientName . ' for line ' . $row . '<br>';
			udbfreeresult( $result );
			return null;
		}

		$row = udbgetrow( $result );
		$plCode = $row['plCode'];
		udbfreeresult( $result );
		$policy = new Policy( $plCode );

		if (( $polType == 'R' || $polType == 'C' )) {
			$policy->set( 'plPolicyType', $polType );
		}


		if (0 < $commRate) {
			$policy->set( 'plCommissionRate', $commRate );
		}


		if (0 < $handler) {
			$policy->set( 'plHandler', $handler );
		}


		if (0 < $addlCommRate) {
			$policy->set( 'plAddlCommissionRate', $addlCommRate );
		}


		if (0 < $addOnCommRate) {
			$policy->set( 'plAddOnCommissionRate', $addOnCommRate );
		}


		if (0 < $engFees) {
			$policy->set( 'plEngineeringFee', $engFees );
		}


		if (0 < $engFeesCommRate) {
			$policy->set( 'plEngineeringFeeCommRate', $engFeesCommRate );
		}


		if (0 < $engFeesVAT) {
			$policy->set( 'plEngineeringFeeVAT', $engFeesVAT );
		}


		if (0 < $clDiscRate) {
			$policy->set( 'plClientDiscountRate', $clDiscRate );
		}


		if (0 < $introdCommRate) {
			$policy->set( 'plIntroducerCommRate', $introdCommRate );
		}


		if (0 < $handler) {
			$policy->set( 'plHandler', $handler );
		}


		if (0 < $policyStatus) {
			$policy->set( 'plStatus', $policyStatus );
		}


		if (0 < strlen( trim( $statusDate ) )) {
			$statusDate = _todate2( $statusDate );
			$policy->set( 'plStatusDate', $statusDate );
		}


		if (0 < $cob) {
			$policy->set( 'plClassOfBus', $cob );
		}

		$policy->recalculateAccountingFields(  );
		$policy->update(  );
		$plCode = $policy->getKeyValue(  );
		$clCode = $policy->get( 'plClient' );
		$client = new Client( $clCode );
		$invName = trim( $invName );
		$invAddress = trim( $invAddress );
		$invPostcode = trim( $invPostcode );
		$isInvAddress = trim( $isInvAddress );

		if ($isInvAddress == 'Y') {
			$client->set( 'clInvAddress', 1 );
		}


		if ($isInvAddress == 'N') {
			$client->set( 'clInvAddress', 0 );
		}


		if (0 < strlen( $invName )) {
			$client->set( 'clInvAddFirstName', $invName );
		}


		if (0 < $handler) {
			$client->set( 'clHandler', $handler );
		}


		if (0 < strlen( $invAddress )) {
			$client->set( 'clInvAddAddress', $invAddress );
		}


		if (0 < strlen( $invPostcode )) {
			$client->set( 'clInvAddPostcode', $invPostcode );
		}


		if (0 < strlen( $introdCode )) {
			$client->set( 'clIntroducer', $introdCode );
		}

		$client->update(  );
		$fields = array(  );
		$fields['ptDebit'] = 1;
		$fields['ptPostingDate'] = _todate( $postDate );
		$fields['ptEffectiveFrom'] = _todate( $fromDate );
		$fields['ptEffectiveTo'] = _todate( $toDate );
		$fields['ptGrossIncIPT'] = $gross;
		$fields['ptCommission'] = $commission;
		$fields['ptCommissionRate'] = $commRate;
		$fields['ptEngineeringFee'] = $engFees;
		$fields['ptEngineeringFeeComm'] = $engFeesComm;
		$fields['ptEngineeringFeeCommRate'] = $engFeesCommRate;
		$fields['ptAddlGrossIncIPT'] = $addlPrem;
		$fields['ptAddlCommission'] = $addlCommission;
		$fields['ptAddlCommissionRate'] = $addlCommRate;
		$fields['ptAddOnGrossIncIPT'] = $addOnPrem;
		$fields['ptAddOnCommissionRate'] = $addOnCommRate;
		$fields['ptAddOnCommission'] = $addOnComm;
		$fields['ptClientDiscount'] = $clDiscount;
		$fields['ptBrokerFee'] = $brokerFee;
		$fields['ptIntroducer'] = $introdCode;
		$fields['ptIntroducerComm'] = $introdComm;
		$fields['ptIntroducerCommRate'] = $introdCommRate;
		$fields['ptTransDesc'] = $transDesc;
		$fields['ptCoverDesc'] = $coverDesc;
		$fields['ptAddlCoverDesc'] = $addlDesc;
		$fields['ptAddlCoverDesc'] = $addlDesc;
		$policyTransEditTemplate = new PolicyTransEditTemplate( null );
		$policyTransEditTemplate->setPolicy( $plCode );
		$policyTransEditTemplate->startTransaction( $transType );
		$polTran = &$policyTransEditTemplate->getTrans(  );

		$policyTransEditTemplate->setAll( $fields );
		$policyTransEditTemplate->setTransactionDetails(  );
		$policyTransEditTemplate->postTransaction(  );
		$trans = &$policyTransEditTemplate->getTrans(  );

		$ctCode = $trans->get( 'ptClientTran' );

		if ($ctCode < 1) {
			trigger_error( 'cant get cl tran ' . $polNumOrig, E_USER_ERROR );
		}


		if ($amountPaid != 0) {
			$x = 0;
		}

		$ct = new ClientTransaction( $ctCode );
		$ct->set( 'ctPaid', $amountPaid );
		$orig = $ct->get( 'ctOriginal' );
		$bal = $orig - $amountPaid;
		$ct->set( 'ctBalance', $bal );

		if (0 < strlen( trim( $datePaid ) )) {
			$datePaid = _todate2( $datePaid );
			$ct->set( 'ctPaidDate', $datePaid );
		}

		$ct->update(  );
	}

	function _dototals($gross, $addlPrem, $addOnPrem, $engFees, $clDiscount, $brokerFee, $clTotal, $amountPaid) {
		$gross = uformatmoney( $gross );
		$addlPrem = uformatmoney( $addlPrem );
		$addOnPrem = uformatmoney( $addOnPrem );
		$engFees = uformatmoney( $engFees );
		$clDiscount = uformatmoney( $clDiscount );
		$brokerFee = uformatmoney( $brokerFee );
		$clTotal = uformatmoney( $clTotal );
		$amountPaid = uformatmoney( $amountPaid );
		$q = 'SELECT
		SUM(ptGrossIncIPT),
		SUM(ptAddlGrossIncIPT),
		SUM(ptAddOnGrossIncIPT),
		SUM(ptEngineeringFee),
		SUM(ptClientDiscount),
		SUM(ptBrokerFee),
		SUM(ptClientTotal)
		FROM policyTransactions';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$dbgross = uformatmoney( $row['SUM(ptGrossIncIPT)'] );
		$dbaddlPrem = uformatmoney( $row['SUM(ptAddlGrossIncIPT)'] );
		$dbaddOnPrem = uformatmoney( $row['SUM(ptAddOnGrossIncIPT)'] );
		$dbengFees = uformatmoney( $row['SUM(ptEngineeringFee)'] );
		$dbclDiscount = uformatmoney( $row['SUM(ptClientDiscount)'] );
		$dbbrokerFee = uformatmoney( $row['SUM(ptBrokerFee)'] );
		$dbclTotal = uformatmoney( $row['SUM(ptClientTotal)'] );
		$diffgross = uformatmoney( $gross - $dbgross );
		$diffaddlPrem = uformatmoney( $addlPrem - $dbaddlPrem );
		$diffaddOnPrem = uformatmoney( $addOnPrem - $dbaddOnPrem );
		$diffengFees = uformatmoney( $engFees - $dbengFees );
		$diffclDiscount = uformatmoney( $clDiscount - $dbclDiscount );
		$diffbrokerFee = uformatmoney( $brokerFee - $dbbrokerFee );
		$diffclTotal = uformatmoney( $clTotal - $dbclTotal );
		$out = '' . '<table border=\'1\'>
	<tr>
	<td>Type</td>
	<td>Excel</td>
	<td>Database</td>
	<td>Difference</td>
	</tr>

	<tr>
	<td>Gross</td>
	<td>' . $gross . '</td>
	<td>' . $dbgross . '</td>
	<td>' . $diffgross . '</td>
	</tr>

	<tr>
	<td>Addl Gross</td>
	<td>' . $addlPrem . '</td>
	<td>' . $dbaddlPrem . '</td>
	<td>' . $diffaddlPrem . '</td>
	</tr>

	<tr>
	<td>Add On Gross</td>
	<td>' . $addOnPrem . '</td>
	<td>' . $dbaddOnPrem . '</td>
	<td>' . $diffaddOnPrem . '</td>
	</tr>

	<tr>
	<td>Cl Discount</td>
	<td>' . $clDiscount . '</td>
	<td>' . $dbclDiscount . '</td>
	<td>' . $diffclDiscount . '</td>
	</tr>

	<tr>
	<td>Broker Fee</td>
	<td>' . $brokerFee . '</td>
	<td>' . $dbbrokerFee . '</td>
	<td>' . $diffbrokerFee . '</td>
	</tr>

	<tr>
	<td>Cl Total</td>
	<td>' . $clTotal . '</td>
	<td>' . $dbclTotal . '</td>
	<td>' . $diffclTotal . '</td>
	</tr>
  </table>';
		print $out;
		$q = 'SELECT
		SUM(ctOriginal),
		SUM(ctPaid)
		FROM clientTransactions';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$dbctClTotal = uformatmoney( $row['SUM(ctOriginal)'] );
		$dbctPaid = uformatmoney( $row['SUM(ctPaid)'] );
		$diffctClTotal = uformatmoney( $clTotal - $dbctClTotal );
		$diffctPaid = uformatmoney( $amountPaid - $dbctPaid );
		$out = '' . '<table border=\'1\'>
	<tr>
	<td>Type</td>
	<td>Excel</td>
	<td>Database</td>
	<td>Difference</td>
	</tr>

	<tr>
	<td>Cl Tran Total</td>
	<td>' . $clTotal . '</td>
	<td>' . $dbctClTotal . '</td>
	<td>' . $diffctClTotal . '</td>
	</tr>

	<tr>
	<td>Cl Tran Paid</td>
	<td>' . $amountPaid . '</td>
	<td>' . $dbctPaid . '</td>
	<td>' . $diffctPaid . '</td>
	</tr>

  </table>';
		print $out;
	}

	function _todate($date) {
		$day = substr( $date, 0, 2 );
		$mth = substr( $date, 2, 2 );
		$yr = substr( $date, 4, 2 );
		$date = '' . '20' . $yr . '-' . $mth . '-' . $day;
		$date = uformatsqldate2( $date );
		return $date;
	}

	function _todate2($date) {
		$day = substr( $date, 0, 2 );
		$mth = substr( $date, 2, 2 );
		$yr = substr( $date, 4, 2 );
		$date = '' . $day . '/' . $mth . '/20' . $yr;
		return $date;
	}

	function _gethandler($handler) {
		$handler = trim( $handler );

		if (strlen( $handler ) == 0) {
			return 0;
		}

		$q = '' . 'SELECT usCode FROM users WHERE usInitials = \'' . $handler . '\'';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( 'no handler', E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		udbfreeresult( $result );

		if ($row == null) {
			return 0;
		}

		$usCode = $row['usCode'];
		return $usCode;
	}

	function _gettranstype($type) {
		$transType = 0;

		if (trim( $type ) == 'RNL') {
			$transType = 1;
		}

		return $transType;
	}

	function _fixrenewaldate($policyRenewalDate) {
		$policyRenewalDate = str_replace( '.', '/', $policyRenewalDate );
		$formatted = '';

		if (strpos( $policyRenewalDate, '/' ) !== false) {
			if (uvalidatedate( $policyRenewalDate )) {
				$elems = explode( '/', $policyRenewalDate );

				if (count( $elems ) == 3) {
					$day = sprintf( $elems[0], '%2d' );
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

	function fformatmoney($amt) {
		return sprintf( '%.2f', $amt );
	}

	function _stripasterix($text) {
		if (substr( $text, 0, 1 ) == '*') {
			$text = substr( $text, 1 );
		}

		return $text;
	}

	ini_set( 'memory_limit', '32M' );
	require( '../include/startup.php' );
	require_once( 'xls_reader/cl_xls_reader.php' );
	urequireclasses( '../policies/' . TEMPLATECLASS_PATH );
	echo 'Starting';

	if (isset( $_GET['demo'] )) {
		$file = 'DEMOTRANS.xls';
	} 
else {
		$file = 'TRANS.xls';
	}

	$xls = new xls_reader(  );
	$xls->read_file( '' . 'imports/' . $file );
	$a = $xls->workbook->get_workbook_array(  );
	$from = 0;

	if (isset( $_GET['from'] )) {
		$from = $_GET['from'];
	} 
else {
		$from = 1;
	}

	$to = 0;

	if (isset( $_GET['to'] )) {
		$to = $_GET['to'];
	} 
else {
		$to = 0 - 1;
	}

	doimport( $from, $to, $a );
	print 'Done';
?>