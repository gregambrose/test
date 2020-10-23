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

	class introducer {
		var $table = null;
		var $keyField = null;

		function introducer($code) {
			$this->keyField = 'inCode';
			$this->table = 'introducers';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['inInvAddress'] = 'INT';
			$this->fieldTypes['inSinceDate'] = 'DATE';
			$this->fieldTypes['inRate'] = 'MONEY';
			$this->fieldTypes['inFlatRate'] = 'MONEY';
			$this->fieldTypes['inApplyToAll'] = 'INT';
			$this->fieldTypes['inStatus'] = 'INT';
			$this->fieldTypes['inStatementType'] = 'INT';
			$this->fieldTypes['inAccTitle'] = 'INT';
			$this->_setUpdatedByField( 'inLastUpdateBy' );
			$this->_setUpdatedWhenField( 'inLastUpdateOn' );
			$this->handleConcurrency( true );
			$q = 'SELECT cmName, tiName FROM introducers
				LEFT JOIN communMethod on inDurable = cmCode
				LEFT JOIN titles on inAccTitle = tiCode
	
			where inCode = CODE';
			$this->setExtraSql( $q );
		}

		function getagedcredit($includeTrans = 'P', $ageEffective = false) {
			global $periodTo;

			$inCode = $this->getKeyValue(  );

			if ($ageEffective == true) {
				$date = 'rtEffectiveDate';
			} 
else {
				$date = 'rtPostingDate';
			}


			if ($includeTrans == 'A') {
				$q = '' . 'SELECT ' . $date . ' as date,rtBalance FROM introducerTransactions WHERE rtIntroducer=' . $inCode . ' AND rtBalance!=0';
			} 
else {
				if ($includeTrans == 'P') {
					$q = '' . 'SELECT ' . $date . ' as date,rtBalance FROM introducerTransactions WHERE rtIntroducer=' . $inCode . ' AND rtBalance!=0';
					$q .= '' . ' AND rtPostingDate <= \'' . $periodTo . '\'';
				} 
else {
					if ($includeTrans == 'E') {
						$q = '' . 'SELECT ' . $date . ' as date,rtBalance FROM introducerTransactions WHERE rtIntroducer=' . $inCode . ' AND rtBalance!=0';
						$q .= '' . ' AND rtEffectiveDate <= \'' . $periodTo . '\' AND rtPostingDate <= \'' . $periodTo . '\'';
					} 
else {
						trigger_error( '' . 'wrong introd trans option ' . $includeTrans, E_USER_ERROR );
					}
				}
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$aged = array(  );
			$slot = 0;

			while ($slot < 5) {
				$aged[$slot] = 0;
				++$slot;
			}


			while ($row = udbgetrow( $result )) {
				$date = $row['date'];
				$amt = $row['rtBalance'];
				$slot = fcalcage( $date, $periodTo );

				if (3 < $slot) {
					$slot = 3;
				}


				if (!isset( $aged[$slot] )) {
					$aged[$slot] = 0;
				}

				$aged[$slot] += $amt;
				$aged[4] += $amt;
			}

			return $aged;
		}

		function getinvoicenameandaddress() {
			$name = $this->get( 'inName' );
			$add = trim( $this->get( 'inAddress' ) );
			$len = strlen( $add );

			if (substr( $add, $len - 1, 1 ) == '
') {
				$add = substr( $add, 0, $len - 1 );
			}

			$pc = $this->get( 'inPostcode' );

			if (0 < strlen( trim( $pc ) )) {
				$add .= '
' . $pc;
			}

			$c = $this->get( 'inCountry' );

			if (0 < strlen( trim( $c ) )) {
				$add .= '
' . $c;
			}

			$nameAndAddress = $name . '
' . $add;
			return $nameAndAddress;
		}

		function gettotaldue() {
			$inCode = $this->getKeyValue(  );
			$q = '' . 'SELECT SUM(rtBalance) as balance FROM introducerTransactions WHERE rtIntroducer = ' . $inCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );
			$balance = $row['balance'];
			return $balance;
		}

		function createstatement($title = '', $date = null) {
			$doCode = $this->_produceDocument( $title, $date );
			return $doCode;
		}

		function getxmlforstatement() {
			if (!isset( $this->_statementAsXMLForPDF )) {
				trigger_error( 'no statement XML to return', E_USER_ERROR );
			}

			return $this->_statementAsXMLForPDF;
		}

		function _producedocument($title = '', $date = null) {
			global $user;

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
			$inCode = $this->get( 'inCode' );
			$document->set( 'doIntroducer', $inCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );

			if ($title == '') {
				$subject = 'Introducer Statement';
			} 
else {
				$subject = $title;
			}

			$document->set( 'doSubject', $subject );
			$doDocmType = INTRODUCER_STATEMENT_DOCM_TYPE;
			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( $docmNo, $date );
			$name = sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );
			$document->setIntroducerSequence(  );
			$document->update(  );
			$doCode = $document->getKeyValue(  );
			return $doCode;
		}

		function _makepdf($docmNo, $date = null) {
			require_once( UTIL_PATH . 'UXML.class.php' );
			require_once( UTIL_PATH . 'UXMLTag.class.php' );
			require_once( UTIL_PATH . 'UPDF.class.php' );
			require_once( UTIL_PATH . 'UPDFXML.class.php' );
			$pdf = new UPDF( 'p', false );
			$this->_statementAsXMLForPDF = $this->_makeXMLTextForPDF( $docmNo, $date );
			$xml = new UPDFXML( $this->_statementAsXMLForPDF, $pdf );
			$pdf->close(  );
			$text = $pdf->returnAsString(  );
			return $text;
		}

		function _makexmltextforpdf($docmNo, $date = null) {
			require_once( UTIL_PATH . 'UXML.class.php' );
			require_once( UTIL_PATH . 'UXMLTag.class.php' );
			require_once( UTIL_PATH . 'UPDF.class.php' );
			require_once( UTIL_PATH . 'UPDFXML.class.php' );
			require_once( '../introducers/templateClasses/IntroducerStatementTemplate.class.php' );

			if ($date == null) {
				$date = ugettodayassqldate(  );
			}

			$formattedDate = uformatsqldate3( $date );
			$xmlText = file_get_contents( PDFS_PATH . 'introducerStatement.xml' );
			$template = new IntroducerStatementTemplate( null );
			$template->setParseForXML(  );
			$template->setIntroducer( $this );
			$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
			$out = $this->getInvoiceNameAndAddress(  );
			$template->set( 'address', $out );
			$template->set( 'accNo', $this->get( 'inCode' ) );
			$template->set( 'date', $formattedDate );
			$template->setHTMLFromText( $xmlText );
			$template->parseAll(  );
			$newXMLText = $template->getOutput(  );
			return $newXMLText;
		}
	}

?>
