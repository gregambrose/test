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

	class clientaccountenquirytemplate {
		var $itemToShowAllocation = null;
		var $canAmend = null;
		var $balance = null;

		function clientaccountenquirytemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'fromDate' );
			$this->addField( 'toDate' );
			$this->addField( 'clearedItems' );
			$this->addField( 'direct' );
			$this->addField( 'includeTrans' );
			$this->addField( 'ageEffective' );
			$this->setFieldType( 'clearedItems', 'checked' );
			$this->setFieldType( 'direct', 'checked' );
			$this->itemToShowAllocation = 0;
			$this->set( 'includeTrans', 'P' );
			$this->canAmend = false;
			$this->setProcess( '_displayList', 'display' );
		}

		function setclient($clCode) {
			$client = new Client( $clCode );
			$this->client = &$client;

			$this->setAll( $client->getAllForHTML(  ) );
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
			global $periodTo;

			$this->itemToShowAllocation = 0;

			if (!isset( $this->client )) {
				return false;
			}

			$this->setAll( $input );
			$ageEffective = $this->get( 'ageEffective' );
			handlecashpaid( $this, $input );
			$includeTrans = $this->get( 'includeTrans' );
			$aged = $this->client->getAgedDebt( $includeTrans, $ageEffective );
			$this->set( 'currentAge', uformatmoneywithcommas( $aged[0] ) );
			$this->set( 'oneMonthAge', uformatmoneywithcommas( $aged[1] ) );
			$this->set( 'twoMonthAge', uformatmoneywithcommas( $aged[2] ) );
			$this->set( 'threeOrOverMonthAge', uformatmoneywithcommas( $aged[3] ) );
			$this->set( 'totalAged', uformatmoneywithcommas( $aged[4] ) );
			$this->balance = $aged[4];
			return false;
		}

		function setviewallocation($item) {
			$this->itemToShowAllocation = $item;
		}

		function showinclude($type) {
			$x = $this->get( 'includeTrans' );

			if ($x == $type) {
				return 'checked';
			}

			return '';
		}

		function showageeffective() {
			$x = $this->get( 'ageEffective' );

			if ($x == 1) {
				return 'checked';
			}

			return '';
		}

		function listtransactions($text) {
			global $periodTo;

			$client = $this->getClient(  );
			$clCode = $client->getKeyValue(  );
			$fromDate = $this->get( 'fromDate' );
			$fromDate = umakesqldate2( $fromDate );
			$toDate = $this->get( 'toDate' );
			$toDate = umakesqldate2( $toDate );
			$clearedItems = $this->get( 'clearedItems' );
			$direct = $this->get( 'direct' );
			$includeTrans = $this->get( 'includeTrans' );
			$q = '' . 'SELECT * FROM clientTransactions WHERE ctClient=' . $clCode . ' ';

			if ($fromDate != null) {
				$q .= '' . 'AND ctPostingDate >= \'' . $fromDate . '\' ';
			}


			if ($toDate != null) {
				$q .= '' . 'AND ctPostingDate <= \'' . $toDate . '\' ';
			}


			if (( $clearedItems != 1 && $direct != 1 )) {
				$q .= 'AND ctBalance != 0 ';
			}


			if ($includeTrans == 'P') {
				$q .= '' . ' AND ctPostingDate <= \'' . $periodTo . '\'';
			}


			if ($includeTrans == 'E') {
				$q .= '' . ' AND ctEffectiveDate <= \'' . $periodTo . '\' AND ctPostingDate <= \'' . $periodTo . '\'';
			}


			if ($direct == 1) {
				$q .= 'AND ctDirect = 1 ';
			}

			$q .= ' ORDER BY ctCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ct = new ClientTransaction( $row );
				$this->ct = &$ct;

				$ctTransType = $ct->get( 'ctTransType' );
				$this->ctTransType = $ctTransType;

				if ($ctTransType == 'I') {
					$date = $ct->getForHTML( 'ctPostingDate' );
					$paidDate = $ct->getForHTML( 'ctPaidDate' );
					$effectiveDate = $ct->getForHTML( 'ctEffectiveDate' );
					$paidDirectDate = $ct->getForHTML( 'ctDirectPaidDate' );
					$ptCode = $ct->get( 'ctPolicyTran' );
					$pt = new PolicyTransaction( $ptCode );
					$polNo = $pt->get( 'ptPolicyNumber' );
					$direct = $pt->get( 'ptDirect' );

					if ($direct == 1) {
						$direct = 'Y';
					} 
else {
						$direct = 'N';
					}

					$cbCode = $pt->get( 'ptClassOfBus' );

					if (0 < $cbCode) {
						$cob = new Cob( $cbCode );
						$cobDesc = $cob->get( 'cbName' );
					} 
else {
						$cobDesc = '';
					}

					$type = $pt->get( 'ptTransType' );
					$tranType = new PolicyTransactionType( $type );
					$typeName = $tranType->get( 'pyName' );
					$addOnDesc = trim( $pt->get( 'ptAddOnCoverDescription' ) );

					if (strlen( $addOnDesc ) == 0) {
						$addOnDesc = $typeName;
					}

					$ptAddOnGrossIncIPT = $pt->get( 'ptAddOnGrossIncIPT' );
					$ptClientDiscount = $pt->get( 'ptClientDiscount' );
					$ptBrokerFee = $pt->get( 'ptBrokerFee' );
					$num = 0;

					if ($ptAddOnGrossIncIPT != 0) {
						++$num;
					}


					if ($ptClientDiscount != 0) {
						++$num;
					}


					if ($ptBrokerFee != 0) {
						++$num;
					}


					if (1 < $num) {
						$addOnDesc = 'Other Amounts Due To Broker';
					} 
else {
						if ($ptClientDiscount != 0) {
							$addOnDesc = 'Discount';
						}


						if ($ptBrokerFee != 0) {
							$addOnDesc = 'Broker Fee';
						}
					}

					$this->set( 'addOnDesc', $addOnDesc );
					$this->set( 'ctCode', $ct->getKeyValue(  ) );
					$this->set( 'tranNo', sprintf( '%07d', $ct->get( 'ctSysTran' ) ) );
					$this->set( 'transDate', $date );
					$this->set( 'effectDate', $effectiveDate );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'ptCode', $ptCode );
					$this->set( 'invNo', $ct->getForHTML( 'ctInvoiceNo' ) );
					$this->set( 'polNo', $polNo );
					$this->set( 'cobDesc', $cobDesc );
					$this->set( 'transType', $typeName );
					$this->set( 'directYesNo', $direct );
					$this->set( 'paidDate', $paidDate );
					$this->set( 'paidDirectDate', $paidDirectDate );
					$this->set( 'orig', $ct->getAsMoneyWithCommas( 'ctOriginal' ) );
					$this->set( 'balance', $ct->getAsMoneyWithCommas( 'ctBalance' ) );
					$this->set( 'paid', $ct->getAsMoneyWithCommas( 'ctPaid' ) );
					$this->set( 'wrOff', $ct->getAsMoneyWithCommas( 'ctWrittenOff' ) );
				}


				if ($ctTransType == 'C') {
					$date = $ct->getForHTML( 'ctPostingDate' );
					$effectiveDate = $ct->getForHTML( 'ctEffectiveDate' );
					$this->set( 'ctCode', $ct->getKeyValue(  ) );
					$this->set( 'tranNo', sprintf( '%07d', $ct->get( 'ctSysTran' ) ) );
					$this->set( 'transDate', $date );
					$this->set( 'effectDate', $effectiveDate );
					$this->set( 'paidDate', '' );
					$this->set( 'ptCode', '' );
					$this->set( 'invNo', $ct->get( 'ctCashBatch' ) );
					$this->set( 'polNo', $ct->get( 'ctChequeNo' ) );
					$this->set( 'cobDesc', '' );
					$orig = $ct->get( 'ctOriginal' );

					if ($orig <= 0) {
						$this->set( 'transType', 'Cash Received' );
					} 
else {
						$this->set( 'transType', 'Cash Payment' );
					}

					$this->set( 'directYesNo', '' );
					$this->set( 'paidDate', '' );
					$this->set( 'paidDirectDate', '' );
					$this->set( 'orig', $ct->getAsMoneyWithCommas( 'ctOriginal' ) );
					$this->set( 'balance', $ct->getAsMoneyWithCommas( 'ctBalance' ) );
					$this->set( 'paid', $ct->getAsMoneyWithCommas( 'ctPaid' ) );
					$this->set( 'wrOff', '' );
					$this->set( 'cashBatchItem', $ct->get( 'ctCashBatchItem' ) );
				}


				if ($ctTransType == 'J') {
					$date = $ct->getForHTML( 'ctPostingDate' );
					$effectiveDate = $ct->getForHTML( 'ctEffectiveDate' );
					$this->set( 'ctCode', $ct->getKeyValue(  ) );
					$this->set( 'tranNo', sprintf( '%07d', $ct->get( 'ctSysTran' ) ) );
					$this->set( 'transDate', $date );
					$this->set( 'effectDate', $effectiveDate );
					$this->set( 'paidDate', '' );
					$this->set( 'ptCode', '' );
					$this->set( 'invNo', $ct->get( 'ctJournal' ) );
					$this->set( 'cobDesc', '' );
					$this->set( 'transType', 'Journal' );
					$this->set( 'directYesNo', '' );
					$this->set( 'paidDate', '' );
					$this->set( 'paidDirectDate', '' );
					$this->set( 'orig', $ct->getAsMoneyWithCommas( 'ctOriginal' ) );
					$this->set( 'balance', $ct->getAsMoneyWithCommas( 'ctBalance' ) );
					$this->set( 'paid', $ct->getAsMoneyWithCommas( 'ctPaid' ) );
					$this->set( 'wrOff', '' );
					$this->set( 'cashBatchItem', $ct->get( 'ctCashBatchItem' ) );
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
			if (( $this->ctTransType != 'C' && $this->ctTransType != 'J' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhennotjournalitem($text) {
			if ($this->ctTransType == 'J') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhennotcashitem($text) {
			if (( $this->ctTransType == 'C' || $this->ctTransType == 'J' )) {
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
	}

?>