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
		$insCoCode = trim( $template->get( 'insCoCode' ) );
		$includeTrans = $template->get( 'includeTrans' );

		if (( 0 < strlen( $insCoCode ) && is_numeric( $insCoCode ) == false )) {
			$template->setMessage( 'ins co code needs to be numeric' );
			return false;
		}

		$q = 'SELECT itInsCo, sum(itBalance), icCode,  icName
				FROM insuranceCompanies, inscoTransactions
				WHERE icCode=itInsCo ';

		if (0 < strlen( $searchText )) {
			$q .= 'AND ';
			$q .= '' . '(icName 		 LIKE \'%' . $searchText . '%\' OR
				   icAddress     LIKE \'%' . $searchText . '%\' OR
				   icContact     LIKE \'%' . $searchText . '%\' OR
				   icPostcode    LIKE \'%' . $searchText . '%\')
			   ';
		}


		if ($includeTrans == 'P') {
			$q .= '' . ' AND itPostingDate <= \'' . $periodTo . '\' ';
		}


		if ($includeTrans == 'E') {
			$q .= '' . ' AND itEffectiveDate <= \'' . $periodTo . '\' AND itPostingDate <= \'' . $periodTo . '\' ';
		}


		if (strlen( $alphas ) == 1) {
			$q .= 'AND ';
			$q .= '' . 'icName    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $insCoCode )) {
			$q .= 'AND ';
			$q .= '' . 'icCode=' . $insCoCode;
		}

		$q .= ' group by itInsCo
				HAVING sum(itBalance)  != 0
				order by icName';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$insCos = array(  );

		while ($row = udbgetrow( $result )) {
			$insCos[] = $row['itInsCo'];
		}

		$template->insCos = $insCos;
		$found = count( $insCos );
		$template->set( 'insCosFound', $found );

		if ($found == 0) {
			$template->insCos = null;
			$template->setMessage( 'no insurance companies found' );
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
		$document->set( 'doLocked', 0 );
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

		$subject = '' . 'Ins Co Aged Credit ' . $includeMessage . ' for period ' . $periodMessg;
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
		$input = array(  );
		$mainTemplate->set( 'includeTrans', $includeType );
		_dosearch( &$mainTemplate, $input );
		$xmlText = file_get_contents( PDFS_PATH . 'inscoAgedCredit.xml' );
		$template = new InsCoAgedCreditPDFTemplate( null );
		$template->setYear( $accountingYearDesc );
		$template->setPeriod( $accountingPeriod );
		$template->setPeriodDescription( uformatsqldate3( $periodTo ) );
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
		$template->insCos = $mainTemplate->insCos;
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uformatsqldate3( ugettodayassqldate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	require( '../include/startup.php' );
	$insCoAgedCreditTemplate = &$session->get( 'insCoAgedCreditTemplate' );

	if ($insCoAgedCreditTemplate == null) {
		$insCoAgedCreditTemplate = new InsCoAgedCreditTemplate( 'insCoAgedCredit.html' );
		$insCoAgedCreditTemplate->setProcess( '_doSearch', 'doSearch' );
		$insCoAgedCreditTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
		$insCoAgedCreditTemplate->setReturnTo( '../reports/insCoAgedCredit.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$insCoAgedCreditTemplate->setHTML( 'insCoAgedCreditOutput.html' );
		$insCoAgedCreditTemplate->setHeaderFields(  );
	} 
else {
		$insCoAgedCreditTemplate->setHTML( 'insCoAgedCredit.html' );
	}

	$session->set( 'insCoAgedCreditTemplate', $insCoAgedCreditTemplate );
	$insCoAgedCreditTemplate->process(  );
	$session->set( 'insCoAgedCreditTemplate', $insCoAgedCreditTemplate );
?>