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

	class clienthistorytemplate {
		var $itemToShowAllocation = null;
		var $canAmend = null;
		var $balance = null;

		function clienthistorytemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'contactName' );
			$this->addField( 'tmCode' );
			$this->setProcess( '_displayList', 'display' );
		}

		function setclient($clCode) {
			$client = new Client( $clCode );
			$this->client = &$client;

			$this->setAll( $client->getAllForHTML(  ) );
			$this->set( 'contactName', $this->client->getFullName(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setcanamend($ok) {
			$this->canAmend = $ok;
		}

		function setandeditclient($clCode) {
			$this->setClient( $clCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getclient() {
			return $this->client;
		}

		function _displaylist($template, $input) {
			return false;
		}

		function _dobeforeanyprocessing($input) {
			$this->itemToShowAllocation = 0;

			if (!isset( $this->client )) {
				return false;
			}

			handlecashpaid( $this, $input );
			$aged = $this->client->getAgedDebt(  );
			$this->set( 'currentAge', uformatmoneywithcommas( $aged[0] ) );
			$this->set( 'oneMonthAge', uformatmoneywithcommas( $aged[1] ) );
			$this->set( 'twoMonthAge', uformatmoneywithcommas( $aged[2] ) );
			$this->set( 'threeOrOverMonthAge', uformatmoneywithcommas( $aged[3] ) );
			$this->set( 'totalAged', uformatmoneywithcommas( $aged[4] ) );
			$this->balance = $aged[4];
			return false;
		}

		function listitems($text) {
			global $userCode;

			$client = $this->getClient(  );
			$clCode = $client->getKeyValue(  );
			$fromDate = $this->get( 'fromDate' );
			$fromDate = umakeourtimestamp( $fromDate, false );
			$toDate = $this->get( 'toDate' );
			$toDate = umakeourtimestamp( $toDate, true );
			$tempTable = 'tmpCH' . $userCode;
			$this->setTempTable( $tempTable );
			$q = '' . 'DROP TABLE IF EXISTS ' . $tempTable;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'CREATE  TABLE ' . $tempTable . ' (
				tmCode				INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				tmItemCode			INT,
				tmDateTime			CHAR(14),
				tmType				CHAR(1),
				tmTransType			CHAR(1),
				tmAmount			BIGINT,
				tmPolicy			INT,
				tmPolicyNum			VARCHAR(50),
				tmRef				VARCHAR(50),
				tmDesc				VARCHAR(300),
				tmSentDate			DATE
			)';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'INSERT INTO ' . $tempTable . ' (tmItemCode, tmDateTime, tmType, tmTransType, tmAmount, tmPolicy, tmPolicyNum,  tmRef, tmDesc) ';
			$q .= '' . 'SELECT  ctSysTran, ctCreatedOn, \'A\', ctTransType ,  ctOriginal, ctPolicy, plPolicyNumber, ctBrokerRef, ctTransDesc
				FROM clientTransactions 
				LEFT JOIN policies ON ctPolicy = plCode
				WHERE ctClient=' . $clCode . ' ';

			if ($fromDate != null) {
				$q .= '' . 'AND ctCreatedOn >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . ' AND ctCreatedOn <= \'' . $toDate . '\' ';
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'INSERT INTO ' . $tempTable . ' (tmItemCode, tmDateTime, tmType, tmPolicy, tmPolicyNum, tmDesc, tmSentDate) ';
			$q .= '' . 'SELECT  doCode, doWhenOriginated, \'D\', doPolicy, plPolicyNumber, doSubject, doClientSentWhen
				FROM documents
				LEFT JOIN policies ON doPolicy = plCode
				WHERE doClient=' . $clCode . ' ';

			if ($fromDate != null) {
				$q .= '' . 'AND doWhenOriginated >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . ' AND doWhenOriginated <= \'' . $toDate . '\' ';
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'INSERT INTO ' . $tempTable . ' (tmItemCode, tmDateTime, tmType, tmPolicy, tmPolicyNum, tmDesc) ';
			$q .= '' . 'SELECT  noCode, noWhenOriginated, \'N\', noPolicy, plPolicyNumber, noSubject
				FROM notes
				LEFT JOIN policies ON noPolicy = plCode
				WHERE noClient=' . $clCode . ' ';

			if ($fromDate != null) {
				$q .= '' . 'AND noWhenOriginated >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . ' AND noWhenOriginated <= \'' . $toDate . '\' ';
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'SELECT * FROM ' . $tempTable . ' ORDER BY tmDateTime DESC, tmType';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$tmType = $row['tmType'];
				$tmItemCode = $row['tmItemCode'];
				$tmCode = $row['tmCode'];
				$tmPolicyNum = $row['tmPolicyNum'];
				$type = '';

				if ($tmType == 'A') {
					$type = 'Accounting';
					$ctTransType = $row['tmTransType'];

					if ($ctTransType == 'I') {
						$type = 'Invoice/Deb.Note';
					}


					if ($ctTransType == 'C') {
						$type = 'Cash';
					}


					if ($ctTransType == 'J') {
						$type = 'Journal';
					}
				}


				if ($tmType == 'D') {
					$type = 'Document';
				}


				if ($tmType == 'N') {
					$type = 'Note';
				}

				$value = '';

				if ($row['tmAmount'] != 0) {
					$value = CURRENCY_SYMBOL_FOR_HTML . uformatmoney( $row['tmAmount'] );
				}

				$this->set( 'tmCode', $tmCode );
				$this->set( 'date', uformatourtimestamp2( $row['tmDateTime'] ) );
				$this->set( 'ref', sprintf( '%07d', $tmItemCode ) );
				$this->set( 'desc', $row['tmDesc'] );
				$this->set( 'polNo', $tmPolicyNum );
				$this->set( 'type', $type );
				$this->set( 'value', $value );
				$this->set( 'sent', uformatsqldate2( $row['tmSentDate'] ) );

				if ($tmType != 'A') {
					$this->set( 'value', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showwhenjustindirect($text) {
			$direct = $this->get( 'directYesNo' );

			if ($direct == 'Y') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenjustdirect($text) {
			$direct = $this->get( 'directYesNo' );

			if ($direct != 'Y') {
				return '';
			}

			$orig = $this->get( 'orig' );

			if ($orig != 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenbothdirectandindirect($text) {
			$direct = $this->get( 'directYesNo' );

			if ($direct != 'Y') {
				return '';
			}

			$orig = $this->get( 'orig' );

			if ($orig == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhencanamend($text) {
			if ($this->canAmend != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhencashitem($text) {
			if ($this->ctTransType != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhennotcashitem($text) {
			if ($this->ctTransType == 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showifcreditbalanceandallowed($text) {
			if ($this->canAmend != true) {
				return '';
			}


			if (0 <= $this->balance) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showifanyallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'ctCode' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showallocationsrequested($text) {
			if ($this->itemToShowAllocation < 1) {
				return '';
			}


			if ($this->itemToShowAllocation != $this->get( 'ctCode' )) {
				return '';
			}

			$out = '';
			$ct = &$this->ct;

			$ctTransType = $ct->get( 'ctTransType' );
			$ctCode = $ct->getKeyValue(  );
			$out = '';

			if ($ctTransType == 'C') {
				$q = '' . 'SELECT * FROM clientTransAllocations 
					WHERE caCashTran=' . $ctCode . ' OR caOtherTran=' . $ctCode . '
					ORDER BY caCode DESC';
				$result = udbquery( $q );

				if ($result == null) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$ca = new ClientTransAllocation( $row );
					$date = uformatourtimestamp2( $ca->get( 'caLastUpdateOn' ) );
					$x = $ca->get( 'caType' );

					if ($x == 'J') {
						$trans = sprintf( '%07d', $ca->get( 'caCashTran' ) );
					} 
else {
						$trans = sprintf( '%07d', $ca->get( 'caOtherTran' ) );
					}

					$amount = $ca->getForHTML( 'caAmount' );
					$initials = '';
					$x = $ca->get( 'caLastUpdateBy' );

					if (0 < $x) {
						$user = new User( $x );
						$initials = $user->getInitials(  );
					}

					$amount = $ca->getForHTML( 'caAmount' );
					$x = $ca->get( 'caType' );
					$type = '';

					if ($x == 'C') {
						$type = 'cash';
					}


					if ($x == 'W') {
						$type = 'wr.off';
					}


					if ($x == 'J') {
						$type = 'jnl';
					}

					$invNo = '';
					$ctCode = $ca->get( 'caOtherTran' );

					if (0 < $ctCode) {
						$ct = new ClientTransaction( $ctCode );
						$invNo = $ct->get( 'ctInvoiceNo' );
					}

					$this->set( 'date', $date );
					$this->set( 'initials', $initials );
					$this->set( 'batch', $invNo );
					$this->set( 'trans', $trans );
					$this->set( 'type', $type );
					$this->set( 'amount', $amount );
					$out .= $this->parse( $text );
				}
			}


			if ($ctTransType == 'J') {
				$q = '' . 'SELECT * FROM clientTransAllocations WHERE caCashTran=' . $ctCode . '
					ORDER BY caCode DESC';
				$result = udbquery( $q );

				if ($result == null) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$ca = new ClientTransAllocation( $row );
					$date = uformatourtimestamp2( $ca->get( 'caLastUpdateOn' ) );
					$trans = sprintf( '%07d', $ca->get( 'caOtherTran' ) );
					$amount = $ca->getForHTML( 'caAmount' );
					$initials = '';
					$x = $ca->get( 'caLastUpdateBy' );

					if (0 < $x) {
						$user = new User( $x );
						$initials = $user->getInitials(  );
					}

					$amount = $ca->getForHTML( 'caAmount' );
					$type = 'jnl';
					$invNo = '';
					$ctCode = $ca->get( 'caOtherTran' );

					if (0 < $ctCode) {
						$ct = new ClientTransaction( $ctCode );
						$invNo = $ct->get( 'ctInvoiceNo' );
					}

					$this->set( 'date', $date );
					$this->set( 'initials', $initials );
					$this->set( 'batch', $invNo );
					$this->set( 'trans', $trans );
					$this->set( 'type', $type );
					$this->set( 'amount', $amount );
					$out .= $this->parse( $text );
				}
			}


			if ($ctTransType == 'I') {
				$q = '' . 'SELECT * FROM clientTransAllocations WHERE caOtherTran=' . $ctCode . '
					ORDER BY caCode DESC';
				$result = udbquery( $q );

				if ($result == null) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$ca = new ClientTransAllocation( $row );
					$date = uformatourtimestamp2( $ca->get( 'caLastUpdateOn' ) );
					$trans = sprintf( '%07d', $ca->get( 'caCashTran' ) );
					$amount = $ca->getForHTML( 'caAmount' );
					$x = $ca->get( 'caType' );
					$type = '';

					if ($x == 'C') {
						$type = 'cash';
					}


					if ($x == 'W') {
						$type = 'wr.off';
					}


					if ($ctTransType == 'J') {
						$type = 'jnl';
					}

					$initials = '';
					$x = $ca->get( 'caLastUpdateBy' );

					if (0 < $x) {
						$user = new User( $x );
						$initials = $user->getInitials(  );
					}

					$batch = '';
					$ctCode = $ca->get( 'caCashTran' );

					if (0 < $ctCode) {
						$ct = new ClientTransaction( $ctCode );
						$batch = $ct->get( 'ctCashBatch' );
					}

					$this->set( 'date', $date );
					$this->set( 'initials', $initials );
					$this->set( 'batch', $batch );
					$this->set( 'trans', $trans );
					$this->set( 'type', $type );
					$this->set( 'amount', $amount );
					$out .= $this->parse( $text );
				}
			}

			return $out;
		}

		function showwhencashtransallocated($text) {
			if (!isset( $this->ct )) {
				return '';
			}

			$type = $this->ct->get( 'ctTransType' );

			if ($type != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenpremiumtransallocated($text) {
			if (!isset( $this->ct )) {
				return '';
			}

			$type = $this->ct->get( 'ctTransType' );

			if ($type != 'I') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function settemptable($tempTable) {
			$this->tempTable = $tempTable;
		}

		function gettemptable() {
			return $this->tempTable;
		}
	}

?>