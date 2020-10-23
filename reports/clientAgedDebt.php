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
		$clientType = $template->get( 'clientType' );
		$searchText = trim( $template->get( 'searchText' ) );
		$clientCode = trim( $template->get( 'clientCode' ) );
		$includeTrans = $template->get( 'includeTrans' );

		if (( 0 < strlen( $clientCode ) && is_numeric( $clientCode ) == false )) {
			$template->setMessage( 'client code needs to be numeric' );
			return false;
		}

		$q = 'SELECT ctClient, sum(ctBalance), clCode, clName
				FROM clients, clientTransactions
				WHERE clCode=ctClient ';

		if ($includeTrans == 'P') {
			$q .= '' . ' AND ctPostingDate <= \'' . $periodTo . '\' ';
		}


		if ($includeTrans == 'E') {
			$q .= '' . ' AND ctEffectiveDate <= \'' . $periodTo . '\' AND ctPostingDate <= \'' . $periodTo . '\' ';
		}


		if (0 < $clientType) {
			$q .= 'AND ';
			$q .= '' . 'clType=' . $clientType . ' ';
		}


		if (0 < strlen( $searchText )) {
			$q .= 'AND ';
			$q .= '' . '(clName 		  LIKE \'%' . $searchText . '%\' OR
				   clFirstName    LIKE \'%' . $searchText . '%\' OR
				   clLastName     LIKE \'%' . $searchText . '%\' OR
				   clAddress      LIKE \'%' . $searchText . '%\' OR
				   clPostcode     LIKE \'%' . $searchText . '%\')
			   ';
		}


		if (strlen( $alphas ) == 1) {
			$q .= 'AND ';
			$q .= '' . 'clNameSort    LIKE \'' . $alphas . '%\' ';
		}


		if (0 < strlen( $clientCode )) {
			$q .= 'AND ';
			$q .= '' . 'clCode=' . $clientCode;
		}

		$q .= ' group by ctClient
				HAVING sum(ctBalance)  != 0
				order by clNameSort';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$clients = array(  );

		while ($row = udbgetrow( $result )) {
			$clients[] = $row['ctClient'];
		}

		$template->clients = $clients;
		$found = count( $clients );
		$template->set( 'clientsFound', $found );

		if ($found == 0) {
			$template->clients = null;
			$template->setMessage( 'no clients found' );
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
		$includeMessage = 'not known';

		if ($includeType == 'P') {
			$includeMessage = 'Incl. Future Effect. Date Trans.';
		}


		if ($includeType == 'E') {
			$includeMessage = 'Excl. Future Effect. Date Trans.';
		}

		$periodMessg = '' . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$subject = '' . 'Client Aged Debt ' . $includeMessage . ' for period ' . $periodMessg;
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
		require_once( '../reports/templateClasses/ClientAgedDebtPDFTemplate.class.php' );
		$input = array(  );
		$mainTemplate->set( 'includeTrans', $includeType );
		_dosearch( &$mainTemplate, $input );
		$xmlText = file_get_contents( PDFS_PATH . 'clientAgedDebt.xml' );
		$template = new ClientAgedDebtPDFTemplate( null );
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
		$template->clients = $mainTemplate->clients;
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uformatsqldate3( ugettodayassqldate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	require( '../include/startup.php' );
	$clientAgedDebtTemplate = &$session->get( 'clientAgedDebtTemplate' );

	if ($clientAgedDebtTemplate == null) {
		$clientAgedDebtTemplate = new ClientAgedDebtTemplate( 'clientAgedDebt.html' );
		$clientAgedDebtTemplate->setProcess( '_doSearch', 'doSearch' );
		$clientAgedDebtTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
		$clientAgedDebtTemplate->setReturnTo( '../reports/clientAgedDebt.php' );
	}


	if (isset( $_POST['viewList'] )) {
		$clientAgedDebtTemplate->setHTML( 'clientAgedDebtOutput.html' );
		$clientAgedDebtTemplate->setHeaderFields(  );
	} 
else {
		$clientAgedDebtTemplate->setHTML( 'clientAgedDebt.html' );
	}

	$session->set( 'clientAgedDebtTemplate', $clientAgedDebtTemplate );
	$clientAgedDebtTemplate->process(  );
	$session->set( 'clientAgedDebtTemplate', $clientAgedDebtTemplate );
?>