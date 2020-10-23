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

	class client {
		var $table = null;
		var $keyField = null;

		function client($code) {
			$this->keyField = 'clCode';
			$this->table = 'clients';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['clNewBusiness'] = 'BOOL';
			$this->fieldTypes['clClientSince'] = 'DATE';
			$this->fieldTypes['clDOB'] = 'DATE';
			$this->fieldTypes['clHandler'] = 'INT';
			$this->fieldTypes['clHomeOwner'] = 'INT';
			$this->fieldTypes['clIntroducer'] = 'INT';
			$this->fieldTypes['clInvAddress'] = 'INT';
			$this->fieldTypes['clMaritalStatus'] = 'INT';
			$this->fieldTypes['clNewBusiness'] = 'INT';
			$this->fieldTypes['clNonSmoker'] = 'INT';
			$this->fieldTypes['clSourceOfBus'] = 'INT';
			$this->fieldTypes['clTitle'] = 'INT';
			$this->fieldTypes['clInvAddTitle'] = 'INT';
			$this->fieldTypes['clType'] = 'INT';
			$this->fieldTypes['clStatus'] = 'INT';
			$this->fieldTypes['clStatusDate'] = 'DATE';
			$this->fieldTypes['clStatementType'] = 'INT';
			$this->fieldTypes['clDiscount'] = 'INT';
			$this->fieldTypes['clDurable'] = 'INT';
			$this->fieldTypes['clDurableDate'] = 'DATE';
			$this->fieldTypes['clBrStatus'] = 'DATE';
			$this->handleConcurrency( true );
			$this->_setUpdatedByField( 'clLastUpdateBy' );
			$this->_setUpdatedWhenField( 'clLastUpdateOn' );
			$q = 'SELECT inName, sbName, csName, cmName, tiName, msName FROM clients
				LEFT JOIN introducers on clIntroducer = inCode
				LEFT JOIN sourceOfBus on clSourceOfBus = sbCode
				LEFT JOIN clientStatus on clStatus = csCode
				LEFT JOIN communMethod on clDurable = cmCode
				LEFT JOIN titles on clTitle = tiCode
				LEFT JOIN maritalStatus on clMaritalStatus = msCode
				where clCode = CODE';
			$this->setExtraSql( $q );
			$q = 'SELECT usFirstName as handlerFirst,  usLastName as handlerLast  FROM clients
				LEFT JOIN users on clHandler = usCode
				where clCode = CODE';
			$this->setExtraSql( $q );
			$q = 'SELECT tiName as invAddTitle  FROM clients
				LEFT JOIN titles on clInvAddTitle = tiCode
				where clCode = CODE';
			$this->setExtraSql( $q );
		}

		function getfullname() {
			$name = $this->get( 'clFirstName' ) . ' ' . $this->get( 'clLastName' );
			return $name;
		}

		function getdisplayname() {
			if ($this->get( 'clType' ) == 1) {
				$name = $this->get( 'clName' );
			} 
else {
				if (0 < strlen( trim( $this->get( 'clLastName' ) ) )) {
					$name = $this->get( 'clLastName' ) . ', ' . $this->get( 'clFirstName' );
					$tiCode = $this->get( 'clTitle' );

					if (0 < $tiCode) {
						$title = new Title( $tiCode );
						$tiName = $title->get( 'tiName' );
						$name .= '' . ', ' . $tiName;
					}
				} 
else {
					$name = $this->get( 'clName' );
				}
			}

			return $name;
		}

		function getinvoicenameandaddress() {
			if ($this->get( 'clInvAddress' ) == 1) {
				$name = '';
				$name = $this->get( 'clName' );
				$add = trim( $this->get( 'clAddress' ) );
				$len = strlen( $add );

				if (substr( $add, $len - 1, 1 ) == '
') {
					$add = substr( $add, 0, $len - 1 );
				}

				$pc = $this->get( 'clPostcode' );

				if (0 < strlen( trim( $pc ) )) {
					$add .= '
' . $pc;
				}

				$c = $this->get( 'clCountry' );

				if (0 < strlen( trim( $c ) )) {
					$add .= '
' . $c;
				}

				$nameAndAddress = $name . '
' . $add;
			} 
else {
				$name = '';
				$t = $this->get( 'clInvAddTitle' );

				if (0 < $t) {
					$ct = new Title( $t );
					$title = $ct->get( 'tiName' );

					if (0 < strlen( trim( $title ) )) {
						$name .= $title . ' ';
					}
				}

				$f = $this->get( 'clInvAddFirstName' );

				if (0 < strlen( trim( $f ) )) {
					$name .= $f . ' ';
				}

				$l = $this->get( 'clInvAddLastName' );

				if (0 < strlen( trim( $l ) )) {
					$name .= $l . ' ';
				}

				$add = $this->get( 'clInvAddAddress' );
				$pc = $this->get( 'clInvAddPostcode' );

				if (0 < strlen( trim( $pc ) )) {
					$add .= '
' . $pc;
				}

				$c = $this->get( 'clInvAddCountry' );

				if (0 < strlen( trim( $c ) )) {
					$add .= '
' . $c;
				}

				$nameAndAddress = $name . '
' . $add;
			}

			return $nameAndAddress;
		}

		function getfullorcompanyname() {
			$name = $this->get( 'clName' );
			return $name;
		}

		function getageddebt($includeTrans = 'P', $ageEffective = false) {
			global $periodTo;

			$clCode = $this->getKeyValue(  );

			if ($ageEffective == true) {
				$date = 'ctEffectiveDate';
			} 
else {
				$date = 'ctPostingDate';
			}


			if ($includeTrans == 'A') {
				$q = '' . 'SELECT ' . $date . ' as date,ctBalance FROM clientTransactions 
				WHERE ctClient=' . $clCode . ' AND ctBalance!=0';
			} 
else {
				if ($includeTrans == 'P') {
					$q = '' . 'SELECT ' . $date . ' as date,ctBalance FROM clientTransactions 
				WHERE ctClient=' . $clCode . ' AND ctBalance!=0';
					$q .= '' . ' AND ctPostingDate <= \'' . $periodTo . '\'';
				} 
else {
					if ($includeTrans == 'E') {
						$q = '' . 'SELECT ' . $date . ' as date,ctBalance FROM clientTransactions 
				WHERE ctClient=' . $clCode . ' AND ctBalance!=0';
						$q .= '' . ' AND ctEffectiveDate <= \'' . $periodTo . '\' AND ctPostingDate <= \'' . $periodTo . '\'';
					} 
else {
						trigger_error( '' . 'wrong cl trans option ' . $includeTrans, E_USER_ERROR );
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
				$amt = $row['ctBalance'];
				$slot = fcalcage( $date, $periodTo );

				if (!isset( $aged[$slot] )) {
					$aged[$slot] = 0;
				}


				if (3 < $slot) {
					$slot = 3;
				}

				$aged[$slot] += $amt;
				$aged[4] += $amt;
			}

			return $aged;
		}

		function gettotaldue() {
			$clCode = $this->getKeyValue(  );
			$q = '' . 'SELECT SUM(ctBalance) as balance FROM clientTransactions WHERE ctClient = ' . $clCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );
			$balance = $row['balance'];
			return $balance;
		}

		function paysipt() {
			$paysIPT = true;
			$pc = $this->get( 'clPostcode' );

			if (substr( $pc, 0, 2 ) == 'IM') {
				$paysIPT = false;
			}


			if (substr( $pc, 0, 2 ) == 'JE') {
				$paysIPT = false;
			}


			if (substr( $pc, 0, 2 ) == 'GY') {
				$paysIPT = false;
			}

			return $paysIPT;
		}

		function setnewdefaults() {
			$this->set( 'clNewBusiness', 1 );
			$this->set( 'clStatus', 2 );
			$this->set( 'clStatusDate', ugettoday(  ) );
			$this->set( 'clClientSince', ugettoday(  ) );
			$this->set( 'clDiscount', 0 - 1 );
			$this->set( 'clInvAddress', 1 );
		}

		function validate() {
			$x = $this->get( 'clInvAddress' );

			if (( $x != 0 - 1 && $x != 1 )) {
				return 'invoice address has to be set as yes or no';
			}

			$x = $this->get( 'clHandler' );

			if ($x < 1) {
				return 'you need to select a handler';
			}

			$x = $this->get( 'clNewBusiness' );

			if ($x == 0) {
				return 'new business has to be set as yes or no';
			}

			$x = $this->get( 'clSourceOfBus' );

			if ($x < 1) {
				return 'you need to select a source of business';
			}

			$x = $this->get( 'clStatus' );

			if ($x < 1) {
				return 'you need to select a client status';
			}

			$x = $this->get( 'clStatusDate' );

			if (( ( $x == '' || $x == null ) || $x == '0000-00-00' )) {
				return 'you need to select a client status date';
			}

			$x = $this->get( 'clClientSince' );

			if (( ( $x == '' || $x == null ) || $x == '0000-00-00' )) {
				return 'you need to select a client since date';
			}

			$x = $this->get( 'clType' );
			$y = $this->get( 'clBusinessTrade' );

			if (( $x == 1 && trim( $y ) == '' )) {
				return 'you need specify a business trade';
			}

			$x = $this->get( 'clStatus' );

			if (( $x == 3 || $x == 4 )) {
				$ok = $this->_canWeLapseClient(  );

				if ($ok == false) {
					return 'you cannot set this client as lapsed';
				}
			}

			$x = $this->get( 'clDurable' );

			if ($x < 1) {
				return 'you need to specify a communication method';
			}

			$x = trim( $this->get( 'clDurableDate' ) );

			if (( ( $x == null || $x == '0000-00-00' ) || $x == '' )) {
				return 'you need to specify a durable medium agree date';
			}

			$x = trim( $this->get( 'clBrStatus' ) );

			if (( ( $x == null || $x == '0000-00-00' ) || $x == '' )) {
				return 'you need to specify a broker status disclosure date';
			}

			$x = $this->get( 'clDiscount' );

			if ($x == 0) {
				return 'you need to decide if the client is to receive discounts';
			}


			if ($x == 0 - 1) {
				$clCode = $this->getKeyValue(  );
				$q = '' . 'SELECT plCode FROM policies WHERE plClient=' . $clCode . ' AND plClientDisc=1';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$found = udbnumberofrows( $result );

				if (0 < $found) {
					return 'discount can\'t be set to NO where at least one policy is set to YES';
				}
			}

			return null;
		}

		function iscreatingpoliciesallowed() {
			$clStatus = $this->get( 'clStatus' );
			$canCreatePolicies = false;

			if (0 < $clStatus) {
				$status = new ClientStatus( $clStatus );
				$locked = $status->get( 'csLocked' );

				if ($locked != 1) {
					$canCreatePolicies = true;
				}
			}


			if ($canCreatePolicies == false) {
				return false;
			}

			return true;
		}

		function _canwelapseclient() {
			$clCode = $this->getKeyValue(  );
			$q = '' . 'SELECT SUM(ctBalance) FROM clientTransactions WHERE ctClient=' . $clCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			if (0 < udbnumberofrows( $result )) {
				$row = udbgetrow( $result );
				$x = $row['SUM(ctBalance)'];

				if ($x != 0) {
					return false;
				}
			}

			return true;
		}

		function update() {
			$clType = $this->get( 'clType' );

			if ($clType == 1) {
				$this->set( 'clNameSort', $this->get( 'clName' ) );
			}


			if ($clType == 2) {
				$clName = $this->get( 'clName' );
				$clFirstName = $this->get( 'clFirstName' );
				$clLastName = $this->get( 'clLastName' );

				if (0 < strlen( trim( $clLastName ) )) {
					$name = $clLastName . ' ' . $clFirstName;
				} 
else {
					$name = $clName;
				}

				$clTitle = $this->get( 'clTitle' );

				if (0 < $clTitle) {
					$title = new Title( $clTitle );
					$desc = $title->get( 'tiName' );
					$name .= '' . ', ' . $desc;
				}

				$this->set( 'clNameSort', $name );
			}

			$ok = urecord::update(  );
			return $ok;
		}

		function createstatement($subject = '', $date = null) {
			$doCode = $this->_produceDocument( $subject, $date );
			return $doCode;
		}

		function getxmlforstatement() {
			if (!isset( $this->_statementAsXMLForPDF )) {
				trigger_error( 'no statement XML to return', E_USER_ERROR );
			}

			return $this->_statementAsXMLForPDF;
		}

		function _producedocument($name = '', $date = null) {
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
			$clCode = $this->get( 'clCode' );
			$document->set( 'doClient', $clCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$subject = 'Client Statement';

			if ($name == '') {
				$document->set( 'doSubject', $subject );
			} 
else {
				$document->set( 'doSubject', $name );
			}

			$doDocmType = CLIENT_STATEMENT_DOCM_TYPE;
			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( $docmNo, $date );
			$name = sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );
			$document->setClientSequence(  );
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
			require_once( '../clients/templateClasses/ClientStatementTemplate.class.php' );

			if ($date == null) {
				$date = ugettodayassqldate(  );
			}

			$formattedDate = uformatsqldate3( $date );
			$xmlText = file_get_contents( PDFS_PATH . 'clientStatement.xml' );
			$template = new ClientStatementTemplate( null );
			$template->setReportDate( $date );
			$template->setParseForXML(  );
			$template->setClient( $this );
			$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
			$out = $this->getInvoiceNameAndAddress(  );
			$template->set( 'address', $out );
			$template->set( 'accNo', $this->get( 'clCode' ) );
			$template->set( 'date', $formattedDate );
			$template->setHTMLFromText( $xmlText );
			$template->parseAll(  );
			$newXMLText = $template->getOutput(  );
			return $newXMLText;
		}
	}

?>
