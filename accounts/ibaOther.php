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

	function _createnew($template, $input) {
		$template->clearInputFields(  );
		$template->setAllowEditing( true );
		$template->setAllowExiting( false );
		$template->set( 'message', 'enter transaction details' );
		return false;
	}

	function _post($template, $input) {
		$messg = $template->validate(  );

		if ($messg != null) {
			$template->setMessage( $messg );
			return false;
		}

		$template->post(  );
		$template->set( 'message', 'transaction posted' );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		return false;
	}

	function _cancel($template, $input) {
		$template->clearInputFields(  );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->set( 'message', 'entry cancelled' );
		return false;
	}

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$script = $template->popReturnTo(  );

		if ($script == '') {
			return false;
		}

		flocationheader( $script );
	}

	function _viewtrans($template, $input) {
		$baCode = $input['toView'];

		if ($baCode < 1) {
			return false;
		}

		$template->viewTrans( $baCode );
		$template->setAllowEditing( false );
		$template->setAllowExiting( true );
		$template->set( 'message', 'existing transaction' );
		return false;
	}

	function _tostatement($template, $input) {
		global $session;

		$ret = '../accounts/ibaOther.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '../reports/bankStatement.php' );
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
		$periodEnd = $periodEnd = uformatsqldate3( $periodTo );
		$periodMessg = '' . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$subject = '' . 'IBA Other Transactions for period ' . $periodMessg;
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
		require_once( '../accounts/templateClasses/IbaOtherPDFTemplate.class.php' );
		$input = array(  );
		$xmlText = file_get_contents( PDFS_PATH . 'ibaOther.xml' );
		$template = new IbaOtherPDFTemplate( null );
		$mainTemplate->makeOpeningAndClosing(  );
		$template->set( 'debitOpening', $mainTemplate->get( 'debitOpening' ) );
		$template->set( 'creditOpening', $mainTemplate->get( 'creditOpening' ) );
		$template->set( 'debitClosing', $mainTemplate->get( 'debitClosing' ) );
		$template->set( 'creditClosing', $mainTemplate->get( 'creditClosing' ) );
		$template->setYear( $accountingYearDesc );
		$template->setPeriod( $accountingPeriod );
		$template->setPeriodDescription( uformatsqldate3( $periodTo ) );
		$template->setParseForXML(  );
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uformatsqldate3( ugettodayassqldate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	require( '../include/startup.php' );
	$ibaOtherTemplate = &$session->get( 'ibaOtherTemplate' );

	if ($ibaOtherTemplate == null) {
		$ibaOtherTemplate = new IbaOtherTemplate( 'ibaOther.html' );
		$ibaOtherTemplate->setProcess( '_goBack', 'back' );
		$ibaOtherTemplate->setProcess( '_cancel', 'cancel' );
		$ibaOtherTemplate->setProcess( '_createNew', 'newTransaction' );
		$ibaOtherTemplate->setProcess( '_post', 'post' );
		$ibaOtherTemplate->setProcess( '_viewTrans', 'toView' );
		$ibaOtherTemplate->setProcess( '_toStatement', 'toStatement' );
		$ibaOtherTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$ibaOtherTemplate->setReturnTo( $returnTo );
	}

	$session->set( 'ibaOtherTemplate', $ibaOtherTemplate );
	$ibaOtherTemplate->makeOpeningAndClosing(  );
	$ibaOtherTemplate->process(  );
	$session->set( 'ibaOtherTemplate', $ibaOtherTemplate );
?>