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

	class cashbatchedittemplate {
		function cashbatchedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'btCode' );
			$this->addField( 'btPayInSlip' );
			$this->addField( 'btWhenPosted' );
			$this->addField( 'btWhoPosted' );
			$this->addField( 'btTotal' );
			$this->addField( 'btEntered' );
			$this->addField( 'btRemaining' );
			$this->addField( 'btAllocated' );
			$this->addField( 'btUnallocated' );
			$this->addField( 'biPaymentMethod' );
			$this->addField( 'biClient' );
			$this->addField( 'biInsco' );
			$this->addField( 'biIntroducer' );
			$this->addField( 'biItemName' );
			$this->addField( 'biCheque' );
			$this->addField( 'biAmount' );
			$this->addField( 'newItemType' );
		}

		function printpayinslip() {
			$this->setHTML( 'cashBatchPrint.html' );
			$this->set( 'companyName', COMPANY_NAME );
			$companyAddress = str_replace( '
', '<br>
', COMPANY_ADDRESS );
			$this->set( 'companyAddress', $companyAddress );
			$batch = &$this->batch;

			$this->setAll( $batch->getAllForHTML(  ) );
			$this->set( 'enteredOn', uformatourtimestamp2( $batch->get( 'btLastUpdateOn' ) ) );
			$usCode = $batch->get( 'btLastUpdateBy' );

			if (0 < $usCode) {
				$user = new User( $usCode );
				$name = $user->getFullName(  );
			} 
else {
				$name = '';
			}

			$this->set( 'enteredBy', $name );
			$this->set( 'total', $batch->getForHTML( 'btEntered' ) );
			$btCode = $batch->getKeyValue(  );
			$q = '' . 'SELECT COUNT(biCode) FROM cashBatchItems WHERE biBatch=' . $btCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );
			$items = $row['COUNT(biCode)'];
			$this->set( 'items', $items );
			$this->parseAll(  );
			$this->display(  );
			$this->setHTML( 'cashBatchEdit.html' );
			return false;
		}

		function checkbatchdate() {
			$date = trim( $this->get( 'btBatchDate' ) );

			if ($date == '') {
				return 'Batch must have a date';
			}

			$date = umakesqldate2( $date );

			if (fisdateinthisaccountingperiod( $date ) == false) {
				return 'posting date not in the current accounting period';
			}

			return null;
		}

		function checkpaymenttypesandamounts() {
			$total = trim( $this->get( 'btTotal' ) );
			$messg = null;
			foreach ($this->items as $key => $item) {
				if ($item->isThisForDeletion(  ) == true) {
					continue;
				}

				$pm = $item->get( 'biPaymentMethod' );

				if ($pm == 0) {
					return 'at least one item is missing a payment method';
				}


				if (( $total == 0 && $pm != 4 )) {
					return 'all payment methods in a zero batch must be journals';
				}


				if (( $total != 0 && $pm == 4 )) {
					return 'journal payment methods can only be used in zero batches';
				}

				$amt = $item->get( 'biAmount' );

				if (( $total != 0 && $amt < 0 )) {
					return 'you cant have negative items except when doing a journal (zero batch)';
					continue;
				}
			}

			return null;
		}

		function setbatch($btCode) {
			if ($btCode <= 0) {
				trigger_error( 'cant create batch ' . $btCode, E_USER_ERROR );
			}

			$this->batch = new CashBatch( $btCode );
			$this->setAll( $this->batch->getAllForHTML(  ) );
			$this->item = null;
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->readInItems(  );
		}

		function getbatch() {
			return $this->batch;
		}

		function getbatchcode() {
			return $this->batch->getKeyValue(  );
		}

		function getitempayeetype() {
			if ($this->item == null) {
				return null;
			}

			$type = $this->item->get( 'biPayeeType' );
			return $type;
		}

		function newitem($type) {
			$cbi = new CashBatchItem( null );
			$cbi->set( 'biPayeeType', $type );

			if (isset( $this->batch )) {
				$btCode = $this->batch->getKeyValue(  );
				$cbi->set( 'biBatch', $btCode );
			}

			$this->items[] = &$cbi;

			$this->_calcTotals(  );
			$this->editItem( $cbi );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			$this->setMessage( 'item added' );
		}

		function edititem($item) {
			$this->item = &$item;

			$this->setAll( $this->item->getAllForHTML(  ) );
		}

		function setitem($key) {
			$this->item = &$this->items[$key];

			$this->setAll( $this->item->getAllForHTML(  ) );
		}

		function clearitem() {
			unset( $this[item] );
		}

		function cancelitem() {
			if (!isset( $this->item )) {
				return null;
			}

			$key = $this->item->getSequence(  );
			$this->item = &$this->items[$key];

		}

		function deleteitem() {
			if (!isset( $this->item )) {
				return null;
			}

			$this->item->setForDeletion(  );
			unset( $this[item] );
			$this->_calcTotals(  );
		}

		function readinitems() {
			$this->items = array(  );
			$btCode = $this->batch->getKeyValue(  );

			if ($btCode <= 0) {
				return '';
			}

			$q = '' . 'SELECT * FROM cashBatchItems WHERE biBatch = ' . $btCode . ' ORDER BY biCode';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$item = new CashBatchItem( $row );
				$item->fetchExtraColumns(  );
				$this->_addItem( $item );
			}

		}

		function updateitem($input) {
			$this->item->SetAll( $this->getAll(  ) );
			$this->item->SetAll( $input );
			$this->_calcTotals(  );
		}

		function saveitems() {
			foreach ($this->items as $key => $item) {
				if ($item->isThisForDeletion(  ) == true) {
					if ($item->recordExists(  ) == true) {
						$item->delete(  );
					}

					unset( $this->items[$key] );
					continue;
				}


				if ($item->recordExists(  ) == true) {
					$item->update(  );
				} 
else {
					$item->insert( null );
				}

				$item->fetchExtraColumns(  );
				$this->items[$key] = $item;
			}

		}

		function haveallitemsgotpayeesetc() {
			$messg = null;
			foreach ($this->items as $key => $item) {
				if ($item->isThisForDeletion(  ) == true) {
					continue;
				}

				$pm = $item->get( 'biPaymentMethod' );

				if ($pm < 1) {
					$messg = 'Batch can\'t be posted as there are one or more items without a payment type';
					break;
				}

				$biPayeeType = $item->get( 'biPayeeType' );

				if ($biPayeeType == 'C') {
					$biClient = $item->get( 'biClient' );

					if ($biClient <= 0) {
						$messg = 'Batch can\'t be posted as there are one or more items without payees';
						break;
					}
				}


				if ($biPayeeType == 'I') {
					$biInsco = $item->get( 'biInsco' );

					if ($biInsco <= 0) {
						$messg = 'Batch can\'t be posted as there are one or more items without payees';
						break;
					}
				}


				if ($biPayeeType == 'N') {
					$biIntroducer = $item->get( 'biIntroducer' );

					if ($biIntroducer <= 0) {
						$messg = 'Batch can\'t be posted as there are one or more items without payees';
						break;
					}

					continue;
				}
			}

			return $messg;
		}

		function clientname() {
			$clCode = $this->get( 'biClient' );

			if ($clCode <= 0) {
				$name = '';
			} 
else {
				$client = new Client( $clCode );
				$name = $client->get( 'clName' );
			}

			return $name;
		}

		function insconame() {
			$icCode = $this->get( 'biInsco' );

			if ($icCode <= 0) {
				$name = '';
			} 
else {
				$insco = new Insco( $icCode );
				$name = $insco->get( 'icName' );
			}

			return $name;
		}

		function introducername() {
			$inCode = $this->get( 'biIntroducer' );

			if ($inCode <= 0) {
				$name = '';
			} 
else {
				$in = new Introducer( $inCode );
				$name = $in->get( 'inName' );
			}

			return $name;
		}

		function showbatchmessage() {
			if (!isset( $this->batch )) {
				return '';
			}


			if ($this->batch->isBatchLocked(  ) == true) {
				$messg = 'BATCH POSTED';
			} 
else {
				$messg = '';
			}

			return $messg;
		}

		function whenitemexists($text) {
			if (!isset( $this->item )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennonzerobalance($text) {
			if ($this->get( 'balanceInPennies' ) == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenclientcashheader($text) {
			if (!isset( $this->item )) {
				return '';
			}

			$type = $this->item->get( 'biPayeeType' );

			if ($type != 'C') {
				return '';
			}

			$this->set( 'biItemName', $this->item->getForHTML( 'biItemName' ) );
			$this->set( 'biCheque', $this->item->getForHTML( 'biCheque' ) );
			$this->set( 'biAmount', $this->item->getForHTML( 'biAmount' ) );
			$out = $this->parse( $text );
			return $out;
		}

		function wheninscocashheader($text) {
			if (!isset( $this->item )) {
				return '';
			}

			$type = $this->item->get( 'biPayeeType' );

			if ($type != 'I') {
				return '';
			}

			$this->set( 'biItemName', $this->item->getForHTML( 'biItemName' ) );
			$this->set( 'biCheque', $this->item->getForHTML( 'biCheque' ) );
			$this->set( 'biAmount', $this->item->getForHTML( 'biAmount' ) );
			$out = $this->parse( $text );
			return $out;
		}

		function whenintroducercashheader($text) {
			if (!isset( $this->item )) {
				return '';
			}

			$type = $this->item->get( 'biPayeeType' );

			if ($type != 'N') {
				return '';
			}

			$this->set( 'biItemName', $this->item->getForHTML( 'biItemName' ) );
			$this->set( 'biCheque', $this->item->getForHTML( 'biCheque' ) );
			$this->set( 'biAmount', $this->item->getForHTML( 'biAmount' ) );
			$out = $this->parse( $text );
			return $out;
		}

		function whenlocked($text) {
			if ($this->batch->isBatchLocked(  ) == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennotlocked($text) {
			if ($this->batch->isBatchLocked(  ) == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listitems($text) {
			$out = '';

			if (!isset( $this->items )) {
				return '';
			}


			if (!is_array( $this->items )) {
				return '';
			}

			foreach ($this->items as $key => $item) {
				if (!is_object( $item )) {
					continue;
				}


				if ($item->isThisForDeletion(  ) == true) {
					continue;
				}

				$locked = $this->batch->get( 'btLocked' );

				if (( $locked == 1 && 0 < $item->getKeyValue(  ) )) {
					$item->refresh(  );
				}

				$name = '';
				$type = '';
				$trans = '';
				$balance = $item->get( 'biAmount' );
				$biPayeeType = $item->get( 'biPayeeType' );

				if ($biPayeeType == 'C') {
					$clCode = $item->get( 'biClient' );

					if (0 < $clCode) {
						$client = new Client( $clCode );
						$name = $client->get( 'clName' );
					}

					$type = 'Client';
					$ctCode = $item->get( 'biTrans' );

					if (0 < $ctCode) {
						$ct = new ClientTransaction( $ctCode );
						$balance = 0 - $ct->get( 'ctBalance' );
						$trans = $ctCode;
					}
				}


				if ($biPayeeType == 'I') {
					$icCode = $item->get( 'biInsco' );

					if (0 < $icCode) {
						$insco = new Insco( $icCode );
						$name = $insco->get( 'icName' );
					}

					$type = 'Insurance Co.';
					$itCode = $item->get( 'biTrans' );

					if (0 < $itCode) {
						$it = new InscoTransaction( $itCode );
						$balance = $it->get( 'itBalance' );
						$trans = $itCode;
					}
				}


				if ($biPayeeType == 'N') {
					$inCode = $item->get( 'biIntroducer' );

					if (0 < $inCode) {
						$introd = new Introducer( $inCode );
						$name = $introd->get( 'inName' );
					}

					$type = 'Introducer';
					$rtCode = $item->get( 'biTrans' );

					if (0 < $rtCode) {
						$rt = new IntroducerTransaction( $rtCode );
						$balance = $rt->get( 'rtBalance' );
						$trans = $rtCode;
					}
				}

				$balanceInPennies = $balance;
				$balance = uformatmoney( $balance );
				$cpCode = $item->get( 'biPaymentMethod' );
				$paymentMethod = '';

				if (0 < $cpCode) {
					$cpt = new CashPaymentMethod( $cpCode );
					$paymentMethod = $cpt->get( 'cpName' );
				}

				$dateAllocated = $item->get( 'biDateAllocated' );
				$dateAllocated = uformatourtimestamp2( $dateAllocated );
				$allocatedBy = $item->get( 'biAllocatedBy' );
				$initials = '';

				if (0 < $allocatedBy) {
					$user = new User( $allocatedBy );
					$initials = $user->getInitials(  );
				}

				$this->set( 'item', $key );
				$this->set( 'type', $type );
				$this->set( 'name', $name );
				$this->set( 'paymentMethod', $paymentMethod );
				$this->set( 'sortCode', $item->getForHTML( 'biSortCode' ) );
				$this->set( 'cheque', $item->getForHTML( 'biCheque' ) );
				$this->set( 'trans', sprintf( '%07d', $trans ) );
				$this->set( 'amount', $item->getForHTML( 'biAmount' ) );
				$this->set( 'dateAllocated', $dateAllocated );
				$this->set( 'by', $initials );
				$this->set( 'balance', $balance );
				$this->set( 'balanceInPennies', $balanceInPennies );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function totalsatbottom($text) {
			$out = $this->parse( $text );
			return $out;
		}

		function listpaymentmethods($text) {
			$code = 0;

			if (isset( $this->item )) {
				$code = $this->item->get( 'biPaymentMethod' );
			}

			$q = 'SELECT * FROM cashPaymentMethods ORDER BY cpSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$cpCode = $row['cpCode'];
				$this->set( 'cpCode', $cpCode );
				$this->set( 'cpName', $row['cpName'] );

				if ($cpCode == $code) {
					$selected = 'selected';
				} 
else {
					$selected = '';
				}

				$this->set( 'showSelected', $selected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function wheneditrequested($template, $input) {
			$batch = &$this->batch;

			$x = $batch->isBatchLocked(  );

			if ($x == true) {
				$this->setMessage( 'This batch is locked so can\'t be amended' );
				return false;
			}

			utemplate::wheneditrequested( $template, $input );
			return false;
		}

		function _additem($item) {
			$elem = uaddtoarray( $this->items, $item );
			$item = &$this->items[$elem];

			$item->setSequence( $elem );
		}

		function _calctotals() {
			$total = 0;
			foreach ($this->items as $item) {
				if (!is_object( $item )) {
					continue;
				}


				if ($item->isThisForDeletion(  ) == true) {
					continue;
				}

				$amt = $item->get( 'biAmount' );
				$total += $amt;
			}

			$this->batch->set( 'btEntered', $total );
			$this->batch->recalculateTotals(  );
			$this->set( 'btEntered', $this->batch->getForHTML( 'btEntered' ) );
			$this->set( 'btRemaining', $this->batch->getForHTML( 'btRemaining' ) );
		}

		function _dobeforeanyprocessing($input) {
			if (!isset( $this->batch )) {
				return false;
			}

			$locked = $this->batch->get( 'btLocked' );

			if ($locked != 1) {
				$this->batch->setAll( $input );
				return null;
			}

			$this->batch->refresh(  );
			$this->setAll( $this->batch->getAllForHTML(  ) );
			return false;
		}
	}

?>
