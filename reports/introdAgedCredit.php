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

	function _dosearch($template, $input) {
		global $session;
		global $periodTo;

		$sortType = $template->getSortType(  );
		$alphas = $template->get( 'alphas' );
		$searchText = trim( $template->get( 'searchText' ) );
		$introdCode = trim( $template->get( 'introdCode' ) );
		$includeTrans = $template->get( 'includeTrans' );

		if (( 0 < strlen( $introdCode ) && is_numeric( $introdCode ) == false )) {
			$template->setMessage( 'introducer code needs to be numeric' );
			return false;
		}

		$q = 'SELECT rtIntroducer, sum(rtBalance), rtCode,  inName
				FROM introducers, introducerTransactions
				WHERE inCode=rtIntroducer ';

		if (0 < strlen( $searchText )) {
			$q .= 'AND ';
			$q .= '' . '(inName 		 LIKE \'%' . $searchText . '%\' OR
				   inAddress     LIKE \'%' . $searchText . '%\' OR
				   inContact     LIKE \'%' . $searchText . '%\' OR
				   inPostcode    LIKE \'%' . $searchText . '%\')
			   ';
		}


		if ($includeTrans == 'P') {
			$q .= '' . ' AND rtPostingDate <= \'' . $periodTo . '\' ';
		}


		if ($includeTrans == 'E') {
			$q .= '' . ' AND rtEffectiveDate <= \'' . $periodTo . '\' AND rtPostingDate <= \'' . $periodTo . '\' ';
		}


		if (strlen( $alphas ) == 1) {
			$q .= 'AND ';
			$q .= '' . 'inName    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $introdCode )) {
			$q .= 'AND ';
			$q .= '' . 'inCode=' . $introdCode;
		}

		$q .= ' group by rtIntroducer
				HAVING sum(rtBalance)  != 0
				order by inName';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$introds = array(  );

		while ($row = udbgetrow( $result )) {
			$introds[] = $row['rtIntroducer'];
		}

		$template->introds = $introds;
		$found = count( $introds );
		$template->set( 'introdsFound', $found );

		if ($found == 0) {
			$template->introds = null;
			$template->setMessage( 'no introducers found' );
		}

		return false;
	}

	function _doperiodend($template, $input) {
		global $user;
		global $accountingYear;
		global $accountingYearCode;
		global $accountingYearDesc;
		global $accountingPeriod;
		global $accountingPeriodCode;
		global $periodFrom;
		global $periodTo;

		$reportUpTo = $periodTo;

		if (!isset( $input['includeType'] )) {
			trigger_error( 'no includeType specified', E_USER_ERROR );
		}

		$includeType = $input['includeType'];

		if (( $includeType != 'P' && $includeType != 'E' )) {
			trigger_error( '' . 'wrong type ' . $includeType, E_USER_ERROR );
		}

		$template->setReportDate( $reportUpTo );

		if (isset( $input['user'] )) {
			$usCode = $input['user'];
			$user = new User( $usCode );
		}


		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'no user', E_USER_ERROR );
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
		$periodEnd = $periodEnd = uformatsqldate3( $periodTo );
		$periodMessg = '' . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$includeMessage = 'not known';

		if ($includeType == 'P') {
			$includeMessage = 'Incl. Future Effect. Date Trans.';
		}


		if ($includeType == 'E') {
			$includeMessage = 'Excl. Future Effect. Date Trans.';
		}

		$subject = '' . 'Introducer Aged Credit ' . $includeMessage . ' for period ' . $periodMessg;
		$document->set( 'doSubject', $subject );
		$doDocmType = MANAGEMENT_DOCM_TYPE;
		$document->set( 'doDocmType', $doDocmType );
		$document->set( 'doUpdateorCreate', ugettimenow(  ) );
		$pdfText = _makepdf( $docmNo, $template, $includeType );
		$name = sprintf( '%07d', $docmNo ) . '.pdf';
		$type = 'application/pdf';
		$document->addDocumentUsingText( $name, $type, $pdfText );
		$document->update(  );
		$doCode = $document->getKeyValue(  );
		echo 'OK';
		return $doCode;
	}

	function _makepdf(&$docmNo, $template, $includeType) {
		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		$pdf = new UPDF( 'l', false );
		$caAsXMLForPDF = _makexmltextforpdf( $docmNo, $template, $includeType );
		$xml = new UPDFXML( $caAsXMLForPDF, $pdf );
		$pdf->close(  );
		$text = $pdf->returnAsString(  );
		return $text;
	}

	function _makexmltextforpdf(&$docmNo, $mainTemplate, $includeType) {
		global $accountingYearCode;
		global $accountingYearDesc;
		global $accountingPeriodCode;
		global $accountingYear;
		global $accountingPeriod;
		global $periodTo;

		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		require_once( '../reports/templateClasses/IntroducerAgedCreditPDFTemplate.class.php' );
		$input = array(  );
		$mainTemplate->set( 'includeTrans', $includeType );
		_dosearch( &$mainTemplate, $input );
		$xmlText = file_get_contents( PDFS_PATH . 'introdAgedCredit.xml' );
		$template = new IntroducerAgedCreditPDFTemplate( null );
		$template->setYear( $accountingYearDesc );
		$template->setPeriod( $accountingPeriod );
		$template->setPeriodDescription( uformatsqldate3( $periodTo ) );
		$template->setReportDate( $periodTo );
		$m = '?';

		if ($includeType == 'P') {
			$m = 'includes';
		}


		if ($includeType == 'E') {
			$m = 'excludes';
		}

		$template->setIncludeFutureMessage( $m );
		$template->setIncludeType( $includeType );
		$template->setParseForXML(  );
		$template->introds = $mainTemplate->introds;
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uformatsqldate3( ugettodayassqldate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	require( '../include/startup.php' );
	$introdAgedCreditTemplate = &$session->get( 'introdAgedCreditTemplate' );

	if ($introdAgedCreditTemplate == null) {
		$introdAgedCreditTemplate = new IntrodAgedCreditTemplate( 'introdAgedCredit.html' );
		$introdAgedCreditTemplate->setProcess( '_doSearch', 'doSearch' );
		$introdAgedCreditTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
		$introdAgedCreditTemplate->setReturnTo( '../reports/introdAgedCredit.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$introdAgedCreditTemplate->setHTML( 'introdAgedCreditOutput.html' );
		$introdAgedCreditTemplate->setHeaderFields(  );
	} 
else {
		$introdAgedCreditTemplate->setHTML( 'introdAgedCredit.html' );
	}

	$session->set( 'introdAgedCreditTemplate', $introdAgedCreditTemplate );
	$introdAgedCreditTemplate->process(  );
	$session->set( 'introdAgedCreditTemplate', $introdAgedCreditTemplate );
?>