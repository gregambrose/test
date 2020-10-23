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

	class cashpaymentedittemplate {
		var $cashTransaction = null;

		function cashpaymentedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clCode' );
			$this->addField( 'ctCode' );
			$this->addField( 'clientName' );
			$this->addField( 'processDate' );
			$this->addField( 'paymentMethod' );
			$this->addField( 'chequeNo' );
			$this->addField( 'paymentAmount' );
			$this->setOneOffFunctionToCall( '_recalcAgedDebtAndUpdateTrans' );
			$this->canGoToAllocation = false;
		}

		function _dobeforeanyprocessing($input) {
			$this->_inputAndRecalcItems( $input );
		}

		function setclient($clCode) {
			$this->client = new Client( $clCode );
			$this->set( 'clCode', $clCode );
			$name = $this->client->getDisplayName(  );
			$this->set( 'clientName', $name );
			$this->canGoToAllocation = false;
			$this->set( 'ctCode', '' );
			$this->set( 'processDate', '' );
			$this->set( 'paymentMethod', '' );
			$this->set( 'chequeNo', '' );
			$this->set( 'paymentAmount', '' );
		}

		function recalctotals() {
		}

		function doposting($input) {
			global $accountingYear;
			global $accountingPeriod;
			global $user;

			$amt = $this->get( 'paymentAmount' );

			if ($amt <= 0) {
				return 'you need to enter an amount to pay';
			}

			$messg = $this->checkPostingDate(  );

			if ($messg != null) {
				return $messg;
			}

			$pm = $this->get( 'paymentMethod' );

			if ($pm == 0) {
				return 'you need to specify the payment type';
			}


			if ($pm == 4) {
				return 'journal payment type cannot be used here';
			}

			$clCode = $this->get( 'clCode' );
			$processDate = $this->get( 'processDate' );
			$paymentMethod = $this->get( 'paymentMethod' );
			$chequeNo = $this->get( 'chequeNo' );
			$amt = uconvertmoneytointeger( $this->get( 'paymentAmount' ) );

			if ($clCode < 1) {
				trigger_error( 'no client', E_USER_ERROR );
			}

			$ok = udbcantabledotransactions( 'clientTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$ct = new ClientTransaction( null );
			$ct->set( 'ctSysTran', $tnCode );
			$ct->set( 'ctTransType', 'C' );
			$ct->set( 'ctClient', $clCode );
			$ct->set( 'ctCashBatch', 0 );
			$ct->set( 'ctCashBatchItem', 0 );
			$ct->set( 'ctChequeNo', $chequeNo );
			$ct->set( 'ctPaymentMethod', $paymentMethod );
			$ct->set( 'ctPostingDate', $processDate );
			$ct->set( 'ctEffectiveDate', $processDate );
			$ct->set( 'ctOriginal', $amt );
			$ct->set( 'ctPaid', 0 );
			$ct->set( 'ctBalance', $amt );
			$ct->set( 'ctAccountingYear', $accountingYear );
			$ct->set( 'ctAccountingPeriod', $accountingPeriod );
			$ct->setCreatedByAndWhen(  );
			$ct->insert( null );
			$ctCode = $ct->getKeyValue(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'C' );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaTran', $ct->getKeyValue(  ) );
			$aa->set( 'aaPostingDate', $ct->get( 'ctPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $ct->get( 'ctEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$aa->insert( null );
			$total = $amt;

			if ($total != 0) {
				$bat = new BankTransType( KEY_BANK_CASH_TO_CLIENT );
				$debit = $bat->get( 'byDebit' );

				if ($debit != 1) {
					$total = 0 - $total;
				}

				$ba = new BankAccountTran( null );
				$ba->set( 'baType', KEY_BANK_CASH_TO_CLIENT );
				$ba->set( 'baSysTran', $tnCode );
				$ba->set( 'baTran', $ct->getKeyValue(  ) );
				$ba->set( 'baDebit', $debit );
				$ba->set( 'baPostingRef', $chequeNo );
				$ba->set( 'baPaymentType', $paymentMethod );
				$ba->set( 'baPostingDate', $processDate );
				$ba->set( 'baAccountingYear', $accountingYear );
				$ba->set( 'baAccountingPeriod', $accountingPeriod );
				$ba->set( 'baCreatedBy', $ct->get( 'ctCreatedBy' ) );
				$ba->set( 'baCreatedOn', $ct->get( 'ctCreatedOn' ) );
				$ba->set( 'baAmount', $total );
				$ba->insert( null );
			}

			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $ct->getKeyValue(  ) );
			$st->set( 'tnType', 'PC' );
			$st->set( 'tnCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$st->set( 'tnCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$st->update(  );
			udbcommittransaction(  );
			$this->_recalcAgedDebt(  );
			$this->set( 'ctCode', $ctCode );
			return null;
		}

		function _inputandrecalcitems($input) {
			$this->setAll( $input );
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

		function setallowallocation($ok) {
			$this->canGoToAllocation = $ok;
		}

		function listpaymentmethods($text) {
			$code = $this->get( 'paymentMethod' );
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

		function whencangotoallocation($text) {
			if ($this->canGoToAllocation != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whencannotgotoallocation($text) {
			if ($this->canGoToAllocation == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function _settemplatefields() {
			$name = $this->client->get( 'clName' );
		}

		function checkpostingdate() {
			$date = trim( $this->get( 'processDate' ) );

			if ($date == '') {
				return 'you must select a processing date';
			}

			$date = umakesqldate2( $date );

			if (fisdateinthisaccountingperiod( $date ) == false) {
				return 'posting date not in the current accounting period';
			}

			return null;
		}
	}

?>