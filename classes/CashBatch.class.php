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

	class cashbatch {
		var $table = null;
		var $keyField = null;

		function cashbatch($code) {
			$this->keyField = 'btCode';
			$this->table = 'cashBatches';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['btWhoPosted'] = 'INT';
			$this->fieldTypes['btLocked'] = 'BOOL';
			$this->fieldTypes['btBatchDate'] = 'DATE';
			$this->fieldTypes['btTotal'] = 'MONEY';
			$this->fieldTypes['btEntered'] = 'MONEY';
			$this->fieldTypes['btRemaining'] = 'MONEY';
			$this->fieldTypes['btAllocated'] = 'MONEY';
			$this->fieldTypes['btUnallocated'] = 'MONEY';
			$this->fieldTypes['btLastUpdateBy'] = 'INT';
			$this->handleConcurrency( true );
			$this->_setUpdatedByField( 'btLastUpdateBy' );
			$this->_setUpdatedWhenField( 'btLastUpdateOn' );
		}

		function recalculatetotals() {
			$btTotal = $this->get( 'btTotal' );
			$btEntered = $this->get( 'btEntered' );
			$btRemaining = $btTotal - $btEntered;
			$this->set( 'btRemaining', $btRemaining );
		}

		function recalcallocated() {
			$btCode = $this->getKeyValue(  );
			$q = '' . 'SELECT biCode, biAmount, biTrans FROM cashBatchItems WHERE biBatch=' . $btCode;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$alloc = 0;
			$unalloc = 0;
			$total = 0;
			$grand = $this->get( 'btTotal' );

			while ($row = udbgetrow( $result )) {
				$biCode = $row['biCode'];
				$biAmount = $row['biAmount'];
				$biTrans = $row['biTrans'];

				if ($biTrans <= 0) {
					continue;
				}

				$bi = new CashBatchItem( $biCode );
				$type = $bi->get( 'biPayeeType' );

				if ($type == 'C') {
					$ctCode = $biTrans;
					$ct = new ClientTransaction( $ctCode );
					$original = $ct->get( 'ctOriginal' );
					$paid = $ct->get( 'ctPaid' );
					$balance = $ct->get( 'ctBalance' );
					$total -= $original;
					$alloc -= $paid;
					$unalloc -= $balance;
				}


				if ($type == 'I') {
					$itCode = $biTrans;
					$it = new InscoTransaction( $itCode );
					$original = $it->get( 'itOriginal' );
					$paid = $it->get( 'itPaid' );
					$balance = $it->get( 'itBalance' );
					$total += $original;
					$alloc += $paid;
					$unalloc += $balance;
				}


				if ($type == 'N') {
					$rtCode = $biTrans;
					$rt = new IntroducerTransaction( $rtCode );
					$original = $rt->get( 'rtOriginal' );
					$paid = $rt->get( 'rtPaid' );
					$balance = $rt->get( 'rtBalance' );
					$total += $original;
					$alloc += $paid;
					$unalloc += $balance;
					continue;
				}
			}

			$this->set( 'btAllocated', $alloc );
			$amt = $grand - $alloc;
			$this->set( 'btUnallocated', $amt );
		}

		function postandlockbatch() {
			global $user;
			global $accountingYear;
			global $accountingPeriod;

			if (is_a( $user, 'User' )) {
				$code = $user->getKeyValue(  );
				$this->set( 'btWhoPosted', $code );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					trigger_error( 'no user', E_USER_WARNING );
				}
			}

			$this->set( 'btWhenPosted', ugettimenow(  ) );
			$postingDate = $this->get( 'btBatchDate' );
			$btCode = $this->getKeyValue(  );

			if ($btCode <= 0) {
				return '';
			}

			$ok = udbcantabledotransactions( 'cashBatches' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'B' );
			$aa->set( 'aaTran', $this->getKeyValue(  ) );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaPostingDate', $this->get( 'btBatchDate' ) );
			$aa->set( 'aaEffectiveDate', $this->get( 'btBatchDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $this->get( 'btLastUpdateBy' ) );
			$aa->set( 'aaCreatedOn', $this->get( 'btLastUpdateOn' ) );
			$aa->insert( null );
			$q = '' . 'SELECT * FROM cashBatchItems WHERE biBatch = ' . $btCode . ' ORDER BY biCode';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result )) {
				$item = new CashBatchItem( $row );
				$biCode = $item->get( 'biCode' );
				$amt = $item->get( 'biAmount' );
				$biPaymentMethod = $item->get( 'biPaymentMethod' );
				$chequeNo = $item->get( 'biCheque' );
				$type = $item->get( 'biPayeeType' );

				if ($type == 'C') {
					$clCode = $item->get( 'biClient' );
					$client = new Client( $clCode );
					$ctCode = $item->get( 'biTrans' );

					if (0 < $ctCode) {
						trigger_error( '' . 'already posted ' . $biCode, E_USER_ERROR );
					}

					$ct = new ClientTransaction( null );
					$ct->set( 'ctTransType', 'C' );
					$ct->set( 'ctClient', $clCode );
					$ct->set( 'ctSysTran', $tnCode );
					$ct->set( 'ctCashBatch', $btCode );
					$ct->set( 'ctCashBatchItem', $biCode );
					$ct->set( 'ctChequeNo', $chequeNo );
					$ct->set( 'ctPaymentMethod', $biPaymentMethod );
					$ct->set( 'ctPostingDate', $postingDate );
					$ct->set( 'ctEffectiveDate', $postingDate );
					$ct->set( 'ctOriginal', 0 - $amt );
					$ct->set( 'ctPaid', 0 );
					$ct->set( 'ctBalance', 0 - $amt );
					$ct->set( 'ctAccountingYear', $accountingYear );
					$ct->set( 'ctAccountingPeriod', $accountingPeriod );
					$ct->setCreatedByAndWhen(  );
					$ct->insert( null );
					$ctCode = $ct->getKeyValue(  );

					if ($ctCode <= 0) {
						udbrollbacktransaction(  );
						trigger_error( 'trans zero', E_USER_ERROR );
					}

					$item->set( 'biTrans', $ctCode );
					$item->update(  );
					$aa = new AccountingAudit( null );
					$aa->set( 'aaType', 'C' );
					$aa->set( 'aaTran', $ct->getKeyValue(  ) );
					$aa->set( 'aaSysTran', $tnCode );
					$aa->set( 'aaPostingDate', $ct->get( 'ctPostingDate' ) );
					$aa->set( 'aaEffectiveDate', $ct->get( 'ctEffectiveDate' ) );
					$aa->set( 'aaAccountingYear', $accountingYear );
					$aa->set( 'aaAccountingPeriod', $accountingPeriod );
					$aa->set( 'aaCreatedBy', $ct->get( 'ctCreatedBy' ) );
					$aa->set( 'aaCreatedOn', $ct->get( 'ctCreatedOn' ) );
					$aa->insert( null );
				}


				if ($type == 'I') {
					$icCode = $item->get( 'biInsco' );
					$ins = new Insco( $icCode );
					$itCode = $item->get( 'biTrans' );

					if (0 < $itCode) {
						trigger_error( '' . 'already posted ' . $biCode, E_USER_ERROR );
					}

					$it = new InsCoTransaction( null );
					$it->set( 'itTransType', 'R' );
					$it->set( 'itSysTran', $tnCode );
					$it->set( 'itInsCo', $icCode );
					$it->set( 'itCashBatch', $btCode );
					$it->set( 'itCashBatchItem', $biCode );
					$it->set( 'itChequeNo', $chequeNo );
					$it->set( 'itPaymentType', $biPaymentMethod );
					$it->set( 'itPostingDate', $postingDate );
					$it->set( 'itEffectiveDate', $postingDate );
					$it->set( 'itOriginal', 0 + $amt );
					$it->set( 'itPaid', 0 );
					$it->set( 'itBalance', 0 + $amt );
					$it->set( 'itAccountingYear', $accountingYear );
					$it->set( 'itAccountingPeriod', $accountingPeriod );
					$it->setCreatedByAndWhen(  );
					$it->insert( null );
					$itCode = $it->getKeyValue(  );

					if ($itCode <= 0) {
						udbrollbacktransaction(  );
						trigger_error( 'trans zero', E_USER_ERROR );
					}

					$item->set( 'biTrans', $itCode );
					$item->update(  );
					$aa = new AccountingAudit( null );
					$aa->set( 'aaType', 'I' );
					$aa->set( 'aaTran', $it->getKeyValue(  ) );
					$aa->set( 'aaSysTran', $tnCode );
					$aa->set( 'aaPostingDate', $it->get( 'itPostingDate' ) );
					$aa->set( 'aaEffectiveDate', $it->get( 'itEffectiveDate' ) );
					$aa->set( 'aaAccountingYear', $accountingYear );
					$aa->set( 'aaAccountingPeriod', $accountingPeriod );
					$aa->set( 'aaCreatedBy', $it->get( 'itCreatedBy' ) );
					$aa->set( 'aaCreatedOn', $it->get( 'itCreatedOn' ) );
					$aa->insert( null );
				}


				if ($type == 'N') {
					$inCode = $item->get( 'biIntroducer' );
					$in = new Introducer( $inCode );
					$rtCode = $item->get( 'biTrans' );

					if (0 < $rtCode) {
						trigger_error( '' . 'already posted ' . $biCode, E_USER_ERROR );
					}

					$rt = new IntroducerTransaction( null );
					$rt->set( 'rtTransType', 'R' );
					$rt->set( 'rtSysTran', $tnCode );
					$rt->set( 'rtIntroducer', $inCode );
					$rt->set( 'rtCashBatch', $btCode );
					$rt->set( 'rtCashBatchItem', $biCode );
					$rt->set( 'rtChequeNo', $chequeNo );
					$rt->set( 'rtIntrodRef', $this->get( 'btPayInSlip' ) );
					$rt->set( 'rtPaymentType', $biPaymentMethod );
					$rt->set( 'rtPostingDate', $postingDate );
					$rt->set( 'rtEffectiveDate', $postingDate );
					$rt->set( 'rtPaidDate', $postingDate );
					$rt->set( 'rtOriginal', 0 + $amt );
					$rt->set( 'rtPaid', 0 );
					$rt->set( 'rtBalance', 0 + $amt );
					$rt->set( 'rtAccountingYear', $accountingYear );
					$rt->set( 'rtAccountingPeriod', $accountingPeriod );
					$rt->setCreatedByAndWhen(  );
					$rt->insert( null );
					$rtCode = $rt->getKeyValue(  );

					if ($rtCode <= 0) {
						udbrollbacktransaction(  );
						trigger_error( 'trans zero', E_USER_ERROR );
					}

					$item->set( 'biTrans', $rtCode );
					$item->update(  );
					$aa = new AccountingAudit( null );
					$aa->set( 'aaType', 'R' );
					$aa->set( 'aaTran', $rt->getKeyValue(  ) );
					$aa->set( 'aaSysTran', $tnCode );
					$aa->set( 'aaPostingDate', $rt->get( 'rtPostingDate' ) );
					$aa->set( 'aaEffectiveDate', $rt->get( 'rtEffectiveDate' ) );
					$aa->set( 'aaAccountingYear', $accountingYear );
					$aa->set( 'aaAccountingPeriod', $accountingPeriod );
					$aa->set( 'aaCreatedBy', $rt->get( 'rtCreatedBy' ) );
					$aa->set( 'aaCreatedOn', $rt->get( 'rtCreatedOn' ) );
					$aa->insert( null );
					continue;
				}
			}

			$this->set( 'btWhenPosted', ugettimenow(  ) );
			$this->set( 'btLocked', 1 );
			$this->set( 'btAccountingYear', $accountingYear );
			$this->set( 'btAccountingPeriod', $accountingPeriod );
			$this->set( 'btSysTran', $tnCode );
			$this->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $this->getKeyValue(  ) );
			$st->set( 'tnType', 'CB' );
			$st->set( 'tnCreatedBy', $this->get( 'btWhoPosted' ) );
			$st->set( 'tnCreatedOn', $this->get( 'btWhenPosted' ) );
			$st->update(  );
			$total = $this->get( 'btTotal' );

			if ($total != 0) {
				$bat = new BankTransType( KEY_BANK_CASH_BATCH );
				$debit = $bat->get( 'byDebit' );

				if ($debit != 1) {
					$total = 0 - $total;
				}

				$ba = new BankAccountTran( null );
				$ba->set( 'baType', KEY_BANK_CASH_BATCH );
				$ba->set( 'baSysTran', $tnCode );
				$ba->set( 'baTran', $this->getKeyValue(  ) );
				$ba->set( 'baPaymentType', KEY_BANK_CASHBATCH_PAYMENT_TYPE );
				$ba->set( 'baDebit', $debit );
				$ba->set( 'baPostingRef', $this->get( 'btPayInSlip' ) );
				$ba->set( 'baPostingDate', $this->get( 'btBatchDate' ) );
				$ba->set( 'baAccountingYear', $accountingYear );
				$ba->set( 'baAccountingPeriod', $accountingPeriod );
				$ba->set( 'baCreatedBy', $this->get( 'btLastUpdateBy' ) );
				$ba->set( 'baCreatedOn', $this->get( 'btLastUpdateOn' ) );
				$ba->set( 'baAmount', $total );
				$ba->insert( null );
			}

			$this->recalcAllocated(  );
			udbcommittransaction(  );
		}

		function isbatchlocked() {
			$x = $this->get( 'btLocked' );

			if ($x == 1) {
				return true;
			}

			return false;
		}
	}

?>