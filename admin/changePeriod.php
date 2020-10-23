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

	function _checkthenchangeperiod($template, $input) {
		global $session;
		global $accountingYear;
		global $accountingYearCode;
		global $accountingYearDesc;
		global $accountingPeriod;
		global $accountingPeriodCode;
		global $periodFrom;
		global $periodTo;
		global $periodMessg;
		global $caTotals;

		$messg = _checkoktorun(  );

		if ($messg != null) {
			$messg = 'The period update cannot be run - ' . $messg . '<br>';
			$template->setMessage( $messg );
			return false;
		}

		$url = SITE_ROOT_INTERNAL_URL . 'accounts/controlAccount.php?getTotals';
		$x = file_get_contents( $url );

		if ($x == false) {
			trigger_error( 'cant get results from control account totals' );
		}

		$caTotals = explode( ',', $x );
		$periodEnd = $periodEnd = uformatsqldate3( $periodTo );
		$periodMessg =  . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$ok = _dobackup(  );

		if ($ok == false) {
			$messg = 'backup could not be done, so period can\'t be changed';
			$template->setMessage( $messg );
			return false;
		}

		$ok = _dochangeperiod(  );

		if ($ok == false) {
			$messg = 'accounting period cannot be changed';
		} 
else {
			$system = new System( 1 );
			$period = $system->getReversedPeriodDescription(  );
			$fromDate = uformatsqldate3( $system->getPeriodFrom(  ) );
			$toDate = uformatsqldate3( $system->getPeriodTo(  ) );
			$period = $system->getAccountingPeriod(  );
			$year = $system->getAccountingYearDesc(  );
			$messg = (  . 'Period End Update successful. Period now ' . $period . ' Year ' . $year . '.' );
			$template->setCanBeChanged( false );
		}

		$template->setMessage( $messg );
		$system = new System( 1 );
		$session->set( 'system', $system );
		$accountingYearDesc = $system->getAccountingYearDesc(  );
		$accountingPeriod = $system->getAccountingPeriod(  );
		return false;
	}

	function _dochangeperiod() {
		global $periodMessg;

		$system = new System( 1 );
		$accountingYear = $system->getAccountingYear(  );
		$accountingPeriod = $system->getAccountingPeriod(  );
		$periodFrom = $system->getPeriodFrom(  );
		$periodTo = $system->getPeriodTo(  );
		$ok = $system->incrementPeriod( false );

		if ($ok == false) {
			trigger_error( 'cant increment period', 256 );
			exit(  );
		}

		udbstarttransaction(  );
		$ok = _produceclientageddebtpdf( 'P' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Cl Aged debt PDF - inc', 256 );
		}

		$ok = _produceclientageddebtpdf( 'E' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Cl Aged debt PDF - exc', 256 );
		}

		$ok = _produceinscoagedcreditpdf( 'P' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Ins Aged credit PDF - inc', 256 );
		}

		$ok = _produceinscoagedcreditpdf( 'E' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Ins Aged credit PDF - exc', 256 );
		}

		$ok = _produceintroduceragedcreditpdf( 'P' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Introducer Aged credit PDF - inc', 256 );
		}

		$ok = _produceintroduceragedcreditpdf( 'E' );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Introducer Aged credit PDF - exc', 256 );
		}

		_produceclientstatements( $periodFrom, $periodTo );
		_produceintroducerstatements( $periodFrom, $periodTo );
		$ok = _producebankstatement(  );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating Bank Statement PDF', 256 );
		}

		$ok = _produceotheriba(  );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating IBA Other PDF', 256 );
		}

		$ok = _producecontrolaccountpdf( $periodTo );

		if ($ok == false) {
			trigger_error( 'Period Update failed creating C/A PDF', 256 );
		}

		_createnewperiodrecord( $system );
		$ok = $system->incrementPeriod( true );

		if ($ok == false) {
			return false;
		}

		$system->update(  );
		udbcommittransaction(  );
		return true;
	}

	function _produceclientstatements($periodFrom, $periodTo) {
		global $periodMessg;

		$title =  . 'Client Statement for period ' . $periodMessg;
		$bigPDFXML = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>
<document>
';
		$clients = array(  );
		$q = 'SELECT  clCode FROM clients, clientTransactions ';
		$q .= 'WHERE ctClient = clCode 
				AND clStatementType = 1 
				GROUP BY ctClient
				HAVING SUM(ctBalance) != 0
	 			ORDER BY clCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$clCode = $row['clCode'];
			$clients[$clCode] = $clCode;
		}

		$q = 'SELECT  clCode FROM clients, clientTransactions ';
		$q .=  . 'WHERE ctClient = clCode 
				AND clStatementType = 2
				AND ctPostingDate >= \'' . $periodFrom . '\' AND ctPostingDate <= \'' . $periodTo . '\'
				GROUP BY ctClient
	 			ORDER BY clCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$clCode = $row['clCode'];
			$clients[$clCode] = $clCode;
		}

		$q = 'SELECT  clCode FROM clients ';
		$q .= 'WHERE  clStatementType = 3
	 			ORDER BY clCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$clCode = $row['clCode'];
			$clients[$clCode] = $clCode;
		}

		reset( &$clients );
		foreach ($clients as $clCode) {
			$client = new Client( $clCode );
			$client->createStatement( $title, $periodTo );
			$x = $client->getXMLForStatement(  );
			$y = _ignorecertainlines( $x );
			$bigPDFXML .= $y;
		}

		$bigPDFXML .= '
		</document>';
		_createmanagementdocm( $bigPDFXML, 'Client Statements for period ' . $periodMessg );
	}

	function _produceintroducerstatements($periodFrom, $periodTo) {
		global $periodMessg;

		$title =  . 'Introducer Statement for period ' . $periodMessg;
		$bigPDFXML = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>
<document>
';
		$introducers = array(  );
		$q = 'SELECT  inCode FROM introducers, introducerTransactions ';
		$q .= 'WHERE rtIntroducer = inCode 
				AND inStatementType = 1 
				GROUP BY rtIntroducer
				HAVING SUM(rtBalance) != 0
	 			ORDER BY inCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$inCode = $row['inCode'];
			$introducers[$inCode] = $inCode;
		}

		$q = 'SELECT  inCode FROM introducers, introducerTransactions ';
		$q .=  . 'WHERE rtintroducer = inCode 
				AND inStatementType = 2
				AND rtPostingDate >= \'' . $periodFrom . '\' AND rtPostingDate <= \'' . $periodTo . '\'
				GROUP BY rtIntroducer
	 			ORDER BY inCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$inCode = $row['inCode'];
			$introducers[$inCode] = $inCode;
		}

		$q = 'SELECT  inCode FROM introducers ';
		$q .= 'WHERE  inStatementType = 3
	 			ORDER BY inCode ';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}


		while ($row = udbgetrow( $result )) {
			$inCode = $row['inCode'];
			$introducers[$inCode] = $inCode;
		}

		reset( &$introducers );
		foreach ($introducers as $inCode) {
			$introducer = new Introducer( $inCode );
			$introducer->createStatement( $title, $periodTo );
			$x = $introducer->getXMLForStatement(  );
			$y = _ignorecertainlines( $x );
			$bigPDFXML .= $y;
		}

		$bigPDFXML .= '
		</document>';
		_createmanagementdocm( $bigPDFXML, 'Introducer Statements for period ' . $periodMessg );
	}

	function _createmanagementdocm($bigPDFXML, $title) {
		global $user;

		$system = new System( 1 );
		$accountingYear = $system->getAccountingYearDesc(  );
		$accountingPeriod = $system->getAccountingPeriod(  );
		$periodTo = $system->getPeriodTo(  );

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'no user', 256 );
			}
		}

		$document = new Document( null );
		$document->insert( null );
		$docmNo = $document->getKeyValue(  );
		$document->set( 'doWhenOriginated', ugettimenow(  ) );
		$document->set( 'doOriginator', $usCode );
		$document->set( 'doUploadType', 1 );
		$document->set( 'doLocked', 1 );
		$document->set( 'doWhenEntered', ugettimenow(  ) );
		$document->set( 'doEnteredBy', $usCode );
		$periodEnd = uformatsqldate3( $periodTo );
		$subject = $title;
		$document->set( 'doSubject', $subject );
		$doDocmType = MANAGEMENT_DOCM_TYPE;
		$document->set( 'doDocmType', $doDocmType );
		$document->set( 'doUpdateorCreate', ugettimenow(  ) );
		$pdfText = _makebigpdf( $bigPDFXML );
		$name = sprintf( '%07d', $docmNo ) . '.pdf';
		$type = 'application/pdf';
		$document->addDocumentUsingText( $name, $type, $pdfText );
		$document->update(  );
		$doCode = $document->getKeyValue(  );
		return $doCode;
	}

	function _makebigpdf($bigPDFXML) {
		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		$pdf = new UPDF( 'p', false );
		$xml = new UPDFXML( $bigPDFXML, $pdf );
		$pdf->close(  );
		$text = $pdf->returnAsString(  );
		$doc = new Document( null );
		$size = strlen( $text );
		header(  . 'Content-length: ' . $size );
		$name = 'docm.pdf';
		return $text;
	}

	function _ignorecertainlines($in) {
		$lines = explode( '
', $in );
		$out = '';
		foreach ($lines as $line) {
			$keep = false;

			if (( stristr( $line, '<?xml' ) == false && stristr( $line, '<?xml' ) == false )) {
				$keep = true;
			}


			if ($keep == false) {
				continue;
			}

			$out .= $line . '
';
		}

		return $out;
	}

	function _producecontrolaccountpdf() {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'accounts/controlAccount.php?periodEnd&user=' . $usCode );
		$x = file_get_contents( $url );

		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _produceclientageddebtpdf($includeType) {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'reports/clientAgedDebt.php?periodEnd&user=' . $usCode . '&includeType=' . $includeType );
		$x = file_get_contents( $url );

		if ($x == false) {
			trigger_error( 'cant get ' . $url, 256 );
		}


		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _produceinscoagedcreditpdf($includeType) {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'reports/insCoAgedCredit.php?periodEnd&user=' . $usCode . '&includeType=' . $includeType );
		$x = file_get_contents( $url );

		if ($x == false) {
			trigger_error( 'cant get ' . $url, 256 );
		}


		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _produceintroduceragedcreditpdf($includeType) {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'reports/introdAgedCredit.php?periodEnd&user=' . $usCode . '&includeType=' . $includeType );
		$x = file_get_contents( $url );

		if ($x == false) {
			trigger_error( 'cant get ' . $url, 256 );
		}


		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _producebankstatement() {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'reports/bankStatement.php?periodEnd&user=' . $usCode );
		$x = file_get_contents( $url );

		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _produceotheriba() {
		global $user;

		$usCode = '';

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		}

		$url = SITE_ROOT_INTERNAL_URL . (  . 'accounts/ibaOther.php?periodEnd&user=' . $usCode );
		$x = file_get_contents( $url );

		if ($x == 'OK') {
			return true;
		}

		return false;
	}

	function _createnewperiodrecord($system) {
		global $caTotals;

		$q = 'SELECT SUM(baAmount) as total FROM bankAccountTrans';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), 256 );
		}

		$row = udbgetrow( $result );
		$total = $row['total'];

		if ($total == null) {
			$total = 0;
		}

		$caTotals['iba'] = $total;
		$apCode = $system->getPeriodCode(  );
		$ap = new AccountingPeriod( $apCode );
		$clTotal = $caTotals[0];
		$icTotal = $caTotals[1];
		$inTotal = $caTotals[2];
		$ibaTotal = $caTotals[3];
		$controlTotal = $caTotals[4];
		$cpTotal = $caTotals[5];
		$fpTotal = $caTotals[6];
		$prTotal = $caTotals[7];
		$pfTotal = $caTotals[8];
		$oiTotal = $caTotals[9];
		$ocTotal = $caTotals[10];
		$cfTotal = $caTotals[11];
		$ibaOther = $caTotals['iba'];
		$af = new AccountingFigures( null );
		$af->set( 'afYear', $ap->get( 'apYear' ) );
		$af->set( 'afPeriod', $ap->get( 'apPeriod' ) );
		$af->set( 'afPeriodCode', $ap->get( 'apCode' ) );
		$af->set( 'afFromDate', $ap->get( 'apFromDate' ) );
		$af->set( 'afToDate', $ap->get( 'apToDate' ) );
		$af->set( 'afClients', $clTotal );
		$af->set( 'afInsurers', $icTotal );
		$af->set( 'afCommPosted', $cpTotal );
		$af->set( 'afFeesPosted', $fpTotal );
		$af->set( 'afIntroducers', $inTotal );
		$af->set( 'afCommPaid', $prTotal );
		$af->set( 'afFeesPaid', $pfTotal );
		$af->set( 'afOtherIncome', $oiTotal );
		$af->set( 'afOtherCharges', $ocTotal );
		$af->set( 'afBank', $ibaTotal );
		$af->set( 'afBankAccount', $ibaTotal );
		$af->set( 'afIBAOther', $ibaOther );
		$af->set( 'afCommFees', $cfTotal );
		$af->insert( null );
	}

	function _checkoktorun() {
		global $sessionName;
		global $periodTo;

		$url = SITE_ROOT_INTERNAL_URL . 'admin/accountingIntegrity.php';
		$x = file_get_contents( $url );

		if ($x == 'OK') {
			return null;
		}

		return 'A system imbalance exists....the system manager has been informed';
	}

	function _dobackup() {
		global $user;

		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			return false;
		}

		return true;
	}

	require( '../include/startup.php' );

	if (( defined( 'USER_FOR_YEAR_END' ) && empty( $$user ) )) {
		$usCode = $user->getKeyValue(  );

		if ($usCode == USER_FOR_YEAR_END) {
			trigger_error( 'this user cant change accounting period' );
			exit(  );
		}
	}

	$changePeriodTemplate = &$session->get( 'changePeriodTemplate' );

	if ($changePeriodTemplate == null) {
		$changePeriodTemplate = new ChangePeriodTemplate( 'changePeriod.html' );
		$changePeriodTemplate->setProcess( '_checkThenChangePeriod', 'changePeriod' );
	}

	$session->set( 'changePeriodTemplate', $changePeriodTemplate );
	$changePeriodTemplate->setDates(  );
	$changePeriodTemplate->process(  );
	$session->set( 'changePeriodTemplate', $changePeriodTemplate );
?>