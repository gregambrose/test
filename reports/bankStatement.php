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

	function _doreport($template, $input) {
		global $userCode;
		global $selectedAccountingPeriod;

		if (( isset( $input['periodEnd'] ) && $input['periodEnd'] == true )) {
			$selectedPeriod = (int)$template->get( 'selectedPeriod' );
			$selectedYear = (int)$template->get( 'selectedYear' );
			$template->setByPeriod( true );
			$controlFromDate = '';
			$controlToDate = '';
			$usCode = 0;
			$selectedType = 0;
		} 
else {
			$controlFromDate = trim( $template->get( 'fromDate' ) );
			$controlToDate = trim( $template->get( 'toDate' ) );
			$fromDate = trim( $template->get( 'fromDate' ) );
			$fromDate = umakesqldate( $fromDate );
			$toDate = trim( $template->get( 'toDate' ) );
			$toDate = umakesqldate( $toDate );
			$template->setMessage( '' );
			$selectedPeriod = (int)$template->get( 'selectedPeriod' );
			$selectedYear = (int)$template->get( 'selectedYear' );
			$selectedType = $template->get( 'selectedType' );
			$usCode = $template->get( 'user' );
			$periodDesc = '';
			$selectedPeriodCode = 0;
			$usePeriod = false;
			$selectedAccountingPeriod = 0;

			if (( ( $selectedPeriod == 0 && 0 < $selectedYear ) || ( 0 < $selectedPeriod && $selectedYear == 0 ) )) {
				$template->setMessage( 'You need to specify both a year and a period' );
				return false;
			}

			$template->setByPeriod( false );
		}


		if (( 0 < $selectedPeriod && 0 < $selectedYear )) {
			$template->setByPeriod( true );
			$ay = new AccountingYear( $selectedYear );
			$yearDesc = $ay->get( 'ayName' );
			$accYear = $ay->get( 'ayYear' );
			$q = '' . 'SELECT apCode FROM accountingPeriods
				  WHERE apYear = ' . $selectedYear . '
				  AND apPeriod = ' . $selectedPeriod;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$rows = udbnumberofrows( $result );

			if ($rows != 1) {
				$template->setMessage( 'This period has not been set up in the system tables' );
				return false;
			}

			$row = udbgetrow( $result );
			$apCode = $row['apCode'];
			$ap = new AccountingPeriod( $apCode );
			$from = $ap->get( 'apFromDate' );
			$to = $ap->get( 'apToDate' );
			$accPeriod = $ap->get( 'apPeriod' );

			if (( $from == '' || $to == '' )) {
				$template->setMessage( 'This period has not been set up properly with dates' );
				return false;
			}


			if (( 0 < strlen( trim( $controlFromDate ) ) || 0 < strlen( trim( $controlToDate ) ) )) {
				$template->setMessage( 'You can\'t specify a period and a range of dates' );
				return false;
			}

			$controlFromDate = $from;
			$controlToDate = $to;
			$periodDesc = '' . 'For Period ' . $selectedPeriod . ' Year ' . $yearDesc;
			$usePeriod = true;
			$selectedPeriodCode = $apCode;
			$selectedAccountingPeriod = $apCode;
		} 
else {
			if (( $fromDate == '' || $toDate == '' )) {
				$template->setMessage( 'you must enter from and to dates, or an accounting period' );
				return false;
			}


			if ($toDate < $fromDate) {
				$template->setMessage( 'dates are in the wrong order' );
				return false;
			}
		}

		$template->set( 'periodDesc', $periodDesc );
		$q = '' . 'DROP TABLE IF EXISTS tmpB' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'CREATE  TABLE tmpB' . $userCode . ' (
				tmCode			INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmBankCode		INT,
				tmType			INT,
				tmPaymentType	INT,
				tmTran			INT,
				tmRef			VARCHAR(50),
				tmDesc			VARCHAR(20),
				tmPostingDate	DATE,
				tmAmount		BIGINT
			)';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'INSERT INTO tmpB' . $userCode . ' (tmBankCode, tmType, tmPaymentType, tmTran, tmRef, tmDesc, tmPostingDate, tmAmount) ';
		$q .= 'SELECT baCode, baType, baPaymentType, baTran, baPostingRef, baDescription, baPostingDate, baAmount
				FROM bankAccountTrans  ';

		if ($usePeriod == true) {
			$q .= '' . 'WHERE baAccountingYear = ' . $accYear . ' AND baAccountingPeriod = ' . $accPeriod;
		} 
else {
			$q .= '' . 'WHERE baPostingDate >= \'' . $fromDate . '\' AND baPostingDate <= \'' . $toDate . '\'';
		}


		if (0 < $usCode) {
			$q .= '' . ' AND baCreatedBy = ' . $usCode;
		}


		if (0 < $selectedType) {
			$q .= '' . ' AND baType = ' . $selectedType . ' ';
		}

		$q .= ' ORDER BY baPostingDate, baCode DESC';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'SELECT COUNT(tmCode) AS total FROM tmpB' . $userCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$found = $row['total'];
		$template->set( 'transFound', $found );

		if ($found == 0) {
			$template->setMessage( 'no transactions found' );
		}

		$template->set( 'bfBalance', '' );
		$template->set( 'cfBalance', '' );

		if (0 < $selectedPeriodCode) {
			$ap = new AccountingPeriod( $selectedPeriodCode );
			$prevCode = $ap->getPreviousPeriod(  );

			if ($prevCode < 1) {
				return null;
			}

			$q = '' . 'SELECT afBankAccount FROM accountingFigures
				  WHERE afPeriodCode = ' . $prevCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (0 < udbnumberofrows( $result )) {
				$row = udbgetrow( $result );
				$bfBalance = $row['afBankAccount'];
				$template->set( 'bfBalance', uformatmoney( $bfBalance ) );
				$q = '' . 'SELECT SUM(baAmount) as total FROM bankAccountTrans
				  WHERE baPostingDate < \'' . $from . '\' ';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$rows = udbnumberofrows( $result );

				if ($rows != 1) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$row = udbgetrow( $result );
				$calcedBfBalance = 0 - $row['total'];

				if ($calcedBfBalance != $bfBalance) {
					$template->setMessage( 'There is a discrepancy in the bank statement opening balance. Please notify support staff.' );
					return false;
				}

				$q = '' . 'SELECT SUM(baAmount) as total FROM bankAccountTrans
				  WHERE baAccountingYear = ' . $accYear . ' 
				  AND baAccountingPeriod = ' . $accPeriod;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$rows = udbnumberofrows( $result );

				if ($rows != 1) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$row = udbgetrow( $result );
				$total = $row['total'];
				$cfBalance = $bfBalance - $total;

				if ($cfBalance < 0) {
					$cfBalance = 0 - $cfBalance;
					$cfSign = 'OD';
				} 
else {
					$cfSign = '';
				}

				$template->set( 'cfBalance', uformatmoney( $cfBalance ) );
				$template->set( 'cfSign', $cfSign );
			}
		}

		return false;
	}

	function _viewreport($template, $input) {
		$reportType = $template->get( 'reportType' );
		$reportSummary = $template->get( 'reportSummary' );
		$selectedType = $template->get( 'selectedType' );
		$desc = 'All Transaction Types';

		if (0 < $selectedType) {
			$bat = new BankTransType( $selectedType );
			$desc = $bat->get( 'byName' );
		}

		$template->set( 'typesSelected', $desc );
		$user = $template->get( 'user' );
		$userName = 'All Users';

		if (0 < $user) {
			$us = new User( $user );
			$userName = $us->getFullName(  );
		}

		$template->set( 'userName', $userName );
		$template->setHTML( 'bankStatementOutput.html' );
		$template->setHeaderFields(  );
	}

	function _returntooptions($template, $input) {
		$template->setHTML( 'bankStatement.html' );
		return false;
	}

	function _viewtran($template, $input) {
		global $session;

		$baCode = $input['tranToView'];

		if ($baCode <= 0) {
			return false;
		}

		$ba = new BankAccountTran( null );
		$found = $ba->tryGettingRecord( $baCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this transaction has been deleted' );
			return false;
		}

		$ret = '../reports/bankStatement.php';
		$session->set( 'returnTo', $ret );
		$baType = $ba->get( 'baType' );
		$baTran = $ba->get( 'baTran' );

		if (( 1 <= $baType && $baType <= 12 )) {
			flocationheader( '' . '../accounts/ibaOther.php?toView=' . $baCode );
			exit(  );
		}


		if ($baType == 13) {
			flocationheader( '' . '../batches/cashBatchEdit.php?batch=' . $baTran );
			exit(  );
		}


		if ($baType == 14) {
			flocationheader( '' . '../clients/cashReceiptsEdit.php?view=' . $baTran );
			exit(  );
		}


		if ($baType == 15) {
			flocationheader( '' . '../inscos/inscoRecon.php?view=' . $baTran );
			exit(  );
		}


		if ($baType == 16) {
			flocationheader( '' . '../introducers/introducerRecon.php?view=' . $baTran );
			exit(  );
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
		$periodEnd = uformatsqldate3( $periodTo );
		$periodMessg = '' . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$subject = '' . 'Bank Statement for period ' . $periodMessg;
		$document->set( 'doSubject', $subject );
		$doDocmType = MANAGEMENT_DOCM_TYPE;
		$document->set( 'doDocmType', $doDocmType );
		$document->set( 'doUpdateorCreate', ugettimenow(  ) );
		$pdfText = _makepdf( $docmNo, $template );
		$name = sprintf( '%07d', $docmNo ) . '.pdf';
		$type = 'application/pdf';
		$document->addDocumentUsingText( $name, $type, $pdfText );
		$document->update(  );
		$doCode = $document->getKeyValue(  );
		echo 'OK';
		return $doCode;
	}

	function _makepdf(&$docmNo, $template) {
		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		$pdf = new UPDF( 'p', false );
		$caAsXMLForPDF = _makexmltextforpdf( $docmNo, $template );
		$xml = new UPDFXML( $caAsXMLForPDF, $pdf );
		$pdf->close(  );
		$text = $pdf->returnAsString(  );
		return $text;
	}

	function _makexmltextforpdf(&$docmNo, $mainTemplate) {
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
		require_once( '../reports/templateClasses/BankStatementPDFTemplate.class.php' );
		$input = array(  );
		$mainTemplate->set( 'selectedPeriod', $accountingPeriod );
		$mainTemplate->set( 'selectedYear', $accountingYearCode );
		$input['periodEnd'] = true;
		_doreport( &$mainTemplate, $input );
		$xmlText = file_get_contents( PDFS_PATH . 'bankStatement.xml' );
		$template = new BankStatementPDFTemplate( null );
		$template->setYear( $accountingYearDesc );
		$template->setPeriod( $accountingPeriod );
		$template->setPeriodDescription( uformatsqldate3( $periodTo ) );
		$template->setBalances( $mainTemplate );
		$template->setParseForXML(  );
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uformatsqldate3( ugettodayassqldate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	require( '../include/startup.php' );
	$bankStatementTemplate = &$session->get( 'bankStatementTemplate' );

	if ($bankStatementTemplate == null) {
		$bankStatementTemplate = new BankStatementTemplate( 'bankStatement.html' );
		$bankStatementTemplate->setProcess( '_doReport', 'doReport' );
		$bankStatementTemplate->setProcess( '_viewReport', 'viewList' );
		$bankStatementTemplate->setProcess( '_returnToOptions', 'returnToOptions' );
		$bankStatementTemplate->setProcess( '_viewTran', 'tranToView' );
		$bankStatementTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
		$bankStatementTemplate->setReturnTo( '../reports/bankStatement.php' );
	}

	$session->set( 'bankStatementTemplate', $bankStatementTemplate );
	$bankStatementTemplate->process(  );
	$session->set( 'bankStatementTemplate', $bankStatementTemplate );
?>