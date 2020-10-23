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

	class cashreceiptsedittemplate {
		var $cashTransaction = null;
		var $amountsAllocated = null;
		var $amountsWrittenOff = null;

		function cashreceiptsedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'clLastUpdateBy' );
			$this->addField( 'clLastUpdateOn' );
			$this->addField( 'allocateDate' );
			$this->setFieldType( 'allocateDate', 'DATE' );
			$this->addField( 'includeCleared' );
			$this->setOneOffFunctionToCall( '_recalcAgedDebtAndUpdateTrans' );
			$this->set( 'allocateDate', '' );
		}

		function _dobeforeanyprocessing($input) {
			$this->_inputAndRecalcItems( $input );
		}

		function setcashbatchitem($biCode) {
			$item = new CashBatchItem( $biCode );
			$btCode = $item->get( 'biBatch' );
			$amt = $item->get( 'biAmount' );
			$biPaymentMethod = $item->get( 'biPaymentMethod' );
			$chequeNo = $item->get( 'biCheque' );
			$batch = new CashBatch( $btCode );
			$clCode = $item->get( 'biClient' );
			$client = new Client( $clCode );
			$ctCode = $item->get( 'biTrans' );

			if ($ctCode <= 0) {
				trigger_error( 'no cash transaction', E_USER_ERROR );
			}

			$cr = new ClientTransaction( $ctCode );
			$this->item = &$item;
			$this->batch = &$batch;
			$this->client = &$client;
			$this->cashTransaction = &$cr;

			$this->_setTemplateFields(  );
			$this->_inputAndRecalcItems( null );

			if ($batch == null) {
				$allocationDate = '';
			} 
else {
				$allocationDate = uformatsqldate2( $batch->get( 'btBatchDate' ) );
			}

			$this->set( 'allocateDate', $allocationDate );
			return false;
		}

		function setclienttransaction($ctCode) {
			$this->setAllowEditing( false );
			$cr = new ClientTransaction( $ctCode );
			$clCode = $cr->get( 'ctClient' );
			$client = new Client( $clCode );
			$btCode = $cr->get( 'ctCashBatch' );

			if (0 < $btCode) {
				$batch = new CashBatch( $btCode );
			} 
else {
				$batch = null;
			}

			$biCode = $cr->get( 'ctCashBatchItem' );

			if (0 < $biCode) {
				$item = new CashBatchItem( $biCode );
			} 
else {
				$item = null;
			}

			$this->item = $item;
			$this->batch = $batch;
			$this->client = &$client;
			$this->cashTransaction = &$cr;

			$this->_setTemplateFields(  );

			if ($batch == null) {
				$allocationDate = '';
			} 
else {
				$allocationDate = uformatsqldate2( $batch->get( 'btBatchDate' ) );
			}

			$this->set( 'allocateDate', $allocationDate );
			$this->_inputAndRecalcItems( null );
			return false;
		}

		function recalctotals() {
		}

		function doposting($input) {
			global $accountingYear;
			global $accountingPeriod;
			global $periodFrom;
			global $periodTo;
			global $user;

			if (!isset( $this->cashTransaction )) {
				trigger_error( 'no trans to post', E_USER_ERROR );
			}

			$this->_inputAndRecalcItems( $input );
			$this->_setTemplateFields(  );
			$ok = uvalidatedate( $this->get( 'allocateDate' ) );

			if ($ok == false) {
				return 'you need to specify a valid allocation date';
			}

			$allocateDate = $this->getSQLDate( 'allocateDate' );

			if (( $allocateDate < $periodFrom || $periodTo < $allocateDate )) {
				return 'the allocation date must be in the current accounting period';
			}


			if (isset( $this->batch )) {
				$btBatchDate = $this->batch->get( 'btBatchDate' );
			} 
else {
				$btBatchDate = $this->cashTransaction->get( 'ctPostingDate' );
			}

			udbstarttransaction(  );
			$cashTransaction = &$this->cashTransaction;

			$paymentMethod = $cashTransaction->get( 'ctPaymentMethod' );
			$ctCode = $cashTransaction->getKeyValue(  );

			if ($ctCode <= 0) {
				udbrollbacktransaction(  );
				trigger_error( 'no cash trans', E_USER_ERROR );
			}


			if (isset( $this->item )) {
				$item = &$this->item;

				$item->set( 'biDateAllocated', ugettimenow(  ) );

				if (isset( $user )) {
					$usCode = $user->getKeyValue(  );
					$item->set( 'biAllocatedBy', $usCode );
				}

				$item->set( 'biAllocated', 1 );
				$item->update(  );
			}

			$totalAllocation = 0;
			$totalWrittenOff = 0;
			foreach ($this->amountsAllocated as $key => $value) {
				$code = $key;
				$alloc = uconvertmoneytointeger( $value );
				$wrOff = 0;

				if (isset( $this->amountsWrittenOff[$code] )) {
					$wrOff = uconvertmoneytointeger( $this->amountsWrittenOff[$code] );
				}

				$ct = new ClientTransaction( $code );
				$ctPaidDate = $allocateDate;

				if (( $alloc != 0 || $wrOff != 0 )) {
					$ct->set( 'ctPaidDate', $ctPaidDate );
				}

				$ctPaid = (int)$ct->get( 'ctPaid' );
				$ctWrittenOff = (int)$ct->get( 'ctWrittenOff' );
				$ctPaid += $alloc;
				$ct->set( 'ctPaid', $ctPaid );
				$ctWrittenOff += $wrOff;
				$ct->set( 'ctWrittenOff', $ctWrittenOff );
				$ct->recalcTotals(  );
				$ct->update(  );
				$totalAllocation += $alloc;
				$totalWrittenOff += $wrOff;
				$plCode = $ct->get( 'ctPolicy' );

				if (0 < $plCode) {
					$policy = new Policy( $plCode );
					$policy->set( 'plPaymentDate', $allocateDate );
					$policy->update(  );
				}

				$ptCode = $ct->get( 'ctPolicyTran' );

				if (0 < $ptCode) {
					$pt = new PolicyTransaction( $ptCode );
					$pt->set( 'ptPaymentDate', $allocateDate );
					$ctOriginal = (int)$ct->get( 'ctOriginal' );
					$ctBalance = (int)$ct->get( 'ctBalance' );

					if ($ctOriginal == $ctBalance) {
						$status = 1;
					}


					if ($ctOriginal != $ctBalance) {
						$status = 2;
					}


					if ($ctBalance == 0) {
						$status = 3;
					}

					$pt->set( 'ptStatus', $status );
					$pt->set( 'ptStatusDate', $allocateDate );
					$pt->update(  );
				}


				if ($alloc != 0) {
					$ca = new ClientTransAllocation( null );
					$ca->set( 'caType', 'C' );
					$ca->set( 'caCashTran', $ctCode );
					$ca->set( 'caOtherTran', $code );
					$ca->set( 'caAmount', $alloc );
					$ca->set( 'caPaymentMethod', $paymentMethod );
					$ca->set( 'caPostingDate', $allocateDate );
					$ca->set( 'caAccountingYear', $accountingYear );
					$ca->set( 'caAccountingPeriod', $accountingPeriod );
					$ca->insert( null );
				}


				if ($wrOff != 0) {
					$ca = new ClientTransAllocation( null );
					$ca->set( 'caType', 'W' );
					$ca->set( 'caCashTran', $ctCode );
					$ca->set( 'caOtherTran', $code );
					$ca->set( 'caAmount', $wrOff );
					$ca->set( 'caPaymentMethod', 0 );
					$ca->set( 'caPostingDate', $allocateDate );
					$ca->set( 'caAccountingYear', $accountingYear );
					$ca->set( 'caAccountingPeriod', $accountingPeriod );
					$ca->insert( null );
					continue;
				}
			}

			$ctPaid = $cashTransaction->get( 'ctPaid' );
			$ctPaid -= $totalAllocation;
			$cashTransaction->set( 'ctPaid', $ctPaid );
			$cashTransaction->recalcTotals(  );
			$cashTransaction->update(  );

			if (isset( $this->batch )) {
				$batch = &$this->batch;

				$batch->recalcAllocated(  );
				$batch->update(  );
			}

			udbcommittransaction(  );
			$this->amountsAllocated = array(  );
			$this->amountsWrittenOff = array(  );
			$this->_setTemplateFields(  );
			return null;
		}

		function doredisplay($input) {
		}

		function _inputandrecalcitems($input) {
			$this->setAll( $input );
			$this->amountsAllocated = array(  );
			$this->amountsWrittenOff = array(  );
			$totallAllocated = 0;
			$totallWrittenOff = 0;

			if (is_array( $input )) {
				foreach ($input as $key => $value) {
					$value = uconvertmoneytointeger( $value );

					if ($value == 0) {
						continue;
					}


					if (substr( $key, 0, 14 ) == 'itemAllocated-') {
						$code = substr( $key, 14 );
						$this->amountsAllocated[$code] = $value;
						$totallAllocated += $value;
					}


					if (substr( $key, 0, 15 ) == 'itemWrittenOff-') {
						$code = substr( $key, 15 );
						$this->amountsWrittenOff[$code] = $value;

						if (!isset( $this->amountsAllocated[$code] )) {
							$this->amountsAllocated[$code] = 0;
						}

						$totallWrittenOff += $value;
						continue;
					}
				}
			}


			if (!isset( $this->client )) {
				return false;
			}

			$includeCleared = $this->get( 'includeCleared' );
			$clCode = $this->client->get( 'clCode' );
			$items = array(  );
			$q = 'SELECT ctCode, ctBalance FROM clientTransactions ';
			$q .= '' . 'WHERE ctClient = ' . $clCode . ' ';
			$q .= 'AND ctTransType != \'C\' ';
			$q .= 'ORDER BY ctCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$ctCode = $row['ctCode'];
				$ctBalance = (int)$row['ctBalance'];

				if (( $includeCleared == false && $ctBalance == 0 )) {
					$found = false;

					if (isset( $this->amountsAllocated[$ctCode] )) {
						$found = true;
					}


					if (isset( $this->amountsWrittenOff[$ctCode] )) {
						$found = true;
					}


					if ($found == false) {
						continue;
					}
				}

				$items[] = $ctCode;
			}

			$this->items = &$items;

		}

		function _recalcageddebtandupdatetrans($this, $input) {
			$this->_recalcAgedDebt(  );
			$this->_setTemplateFields(  );
		}

		function _recalcageddebt() {
			if (!isset( ->client )) {
				return false;
			}

			$aged = $this->client->getAgedDebt(  );
			$this->set( 'currentAge', uformatmoneywithcommas( $aged[0] ) );
			$this->set( 'oneMonthAge', uformatmoneywithcommas( $aged[1] ) );
			$this->set( 'twoMonthAge', uformatmoneywithcommas( $aged[2] ) );
			$this->set( 'threeOrOverMonthAge', uformatmoneywithcommas( $aged[3] ) );
			$this->set( 'totalAged', uformatmoneywithcommas( $aged[4] ) );
			return false;
		}

		function listitems($text) {
			if (!isset( $this->items )) {
				return '';
			}

			$out = '';
			foreach ($this->items as $item) {
				$ctCode = $item;
				$ct = new ClientTransaction( $ctCode );
				$this->ct = &$ct;

				$code = $ct->get( 'ctPolicyTransType' );

				if (0 < $code) {
					$pt = new PolicyTransactionType( $code );
					$transType = $pt->get( 'pyName' );
				} 
else {
					$transType = '';
				}

				$amountAllocated = 0;
				$amountWrittenOff = 0;

				if (isset( $this->amountsAllocated[$ctCode] )) {
					$amountAllocated = $this->amountsAllocated[$ctCode];
				}


				if (isset( $this->amountsWrittenOff[$ctCode] )) {
					$amountWrittenOff = $this->amountsWrittenOff[$ctCode];
				}

				$cobName = '';
				$polNum = '';
				$plCode = $ct->get( 'ctPolicy' );

				if (0 < $plCode) {
					$pl = new Policy( $plCode );
					$polNum = $pl->get( 'plPolicyNumber' );
					$cbCode = $pl->get( 'plClassOfBus' );

					if (0 < $cbCode) {
						$cob = new Cob( $cbCode );
						$cobName = $cob->get( 'cbName' );
					}
				}

				$this->set( 'polNum', $polNum );
				$this->set( 'cobName', $cobName );
				$this->set( 'item', $ctCode );
				$this->set( 'transType', $transType );
				$this->set( 'transNo', sprintf( '%07d', $ctCode ) );
				$this->set( 'postingDate', uformatsqldate2( $ct->get( 'ctPostingDate' ) ) );
				$this->set( 'ctOriginal', $ct->getForHTML( 'ctOriginal' ) );
				$this->set( 'ctBalance', $ct->getForHTML( 'ctBalance' ) );
				$this->set( 'ctPaid', $ct->getForHTML( 'ctPaid' ) );
				$this->set( 'ctWrittenOff', $ct->getForHTML( 'ctWrittenOff' ) );
				$this->set( 'amountAllocated', uformatmoney( $amountAllocated ) );
				$this->set( 'amountWrittenOff', uformatmoney( $amountWrittenOff ) );
				$this->ct = &$ct;

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showwhenbothdirectandindirect($text) {
			$ct = &$this->ct;

			$ctDirect = $ct->get( 'ctDirect' );

			if ($ctDirect != 1) {
				return '';
			}

			$ctTransType = $ct->get( 'ctTransType' );

			if ($ctTransType != 'I') {
				return '';
			}

			$ptCode = $ct->get( 'ctPolicyTran' );

			if ($ptCode <= 0) {
				return '';
			}

			$pt = new PolicyTransaction( $ptCode );
			$ptClientTotal = $pt->get( 'ptClientTotal' );

			if ($ptClientTotal == 0) {
				return '';
			}

			$typeName = $pt->getTypeDescription(  );
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
			$this->set( 'ptClientTotal', uformatmoney( $ptClientTotal ) );
			$out = $this->parse( $text );
			return $out;
		}

		function showwhenjustindirect($text) {
			$ct = &$this->ct;

			$ctDirect = $ct->get( 'ctDirect' );

			if ($ctDirect == 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whencandoreceipt($text) {
			if ($this->getAllowEditing(  ) == true) {
				return '';
			}


			if (!isset( $this->ct )) {
				return '';
			}

			$ct = &$this->ct;

			$type = $ct->get( 'ctTransType' );

			if ($type != 'I') {
				return '';
			}

			$bal = $ct->get( 'ctBalance' );

			if ($bal != 0) {
				return '';
			}

			$amt = $ct->get( 'ctOriginal' );

			if ($amt == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function includeclearedchecked() {
			$ok = $this->get( 'includeCleared' );

			if (( $ok == 1 || $ok == 'on' )) {
				return 'checked';
			}

			return '';
		}

		function _settemplatefields() {
			$name = $this->client->get( 'clName' );

			if (isset( $this->batch )) {
				$batch = &$this->batch;

				$batchDate = uformatourtimestamp2( $batch->get( 'btWhenPosted' ) );
				$batchRef = $batch->getForHTML( 'btCode' );
			} 
else {
				$batchDate = '';
				$batchRef = '';
			}


			if (isset( $this->item )) {
				$item = &$this->item;

				$chequeNo = $item->getForHTML( 'biCheque' );
				$paymentMethod = $item->getPaymentMethodDesc(  );
			} 
else {
				$chequeNo = '';
				$paymentMethod = '';
			}

			$cashTransaction = &$this->cashTransaction;

			$totalOriginal = 0 - $cashTransaction->get( 'ctOriginal' );
			$clearedElsewhere = 0 - $cashTransaction->get( 'ctPaid' );
			$totalAllocated = $this->_addUpAllocated(  );
			$totalUnallocated = $totalOriginal - $clearedElsewhere - $totalAllocated;
			$totalWrittenOff = 0 - $this->_addUpWrittenOff(  );
			$this->set( 'clientName', $name );
			$this->set( 'batchDate', $batchDate );
			$this->set( 'batchRef', $batchRef );
			$this->set( 'chequeNo', $chequeNo );
			$this->set( 'paymentMethod', $paymentMethod );
			$this->set( 'totalOriginal', uformatmoney( $totalOriginal ) );
			$this->set( 'clearedElsewhere', uformatmoney( $clearedElsewhere ) );
			$this->set( 'totalAllocated', uformatmoney( $totalAllocated ) );
			$this->set( 'totalUnallocated', uformatmoney( $totalUnallocated ) );
			$this->set( 'totalWrittenOff', uformatmoney( $totalWrittenOff ) );
		}

		function _addupallocated() {
			$total = 0;

			if (!is_array( $this->amountsAllocated )) {
				return 0;
			}

			foreach ($this->amountsAllocated as $key => $value) {
				$code = $key;
				$alloc = $value;
				$total += $alloc;
			}

			return $total;
		}

		function _addupwrittenoff() {
			$total = 0;

			if (!is_array( $this->amountsWrittenOff )) {
				return 0;
			}

			foreach ($this->amountsWrittenOff as $key => $value) {
				$code = $key;
				$wrOff = $value;
				$total += $wrOff;
			}

			return $total;
		}

		function _checknothingoverallocated() {
			if (!isset( ->items )) {
				return '';
			}

			foreach ($this->items as $item) {
				$ctCode = $item;
				$ct = new ClientTransaction( $ctCode );
				$amountAllocated = 0;
				$amountWrittenOff = 0;

				if (isset( $this->amountsAllocated[$ctCode] )) {
					$amountAllocated = (int)$this->amountsAllocated[$ctCode];
				}


				if (isset( $this->amountsWrittenOff[$ctCode] )) {
					$amountWrittenOff = (int)$this->amountsWrittenOff[$ctCode];
				}

				$ctOriginal = (int)$ct->get( 'ctOriginal' );
				$ctBalance = (int)$ct->get( 'ctBalance' );

				if (0 <= $ctOriginal) {
					if ($ctBalance - (int)$amountAllocated + $amountWrittenOff < 0) {
						return 'an item has been over allocated';
					}


					if ($ctOriginal < $ctBalance - (int)$amountAllocated + $amountWrittenOff) {
						return 'an item has been over allocated';
					}

					continue;
				}


				if (0 < $ctBalance - ( $amountAllocated + $amountWrittenOff )) {
					return 'an item has been over allocated';
				}


				if ($ctBalance - ( $amountAllocated + $amountWrittenOff ) < $ctOriginal) {
					return 'an item has been over allocated';
				}
			}

			return null;
		}
	}

?>