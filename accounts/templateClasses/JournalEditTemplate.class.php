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

	class journaledittemplate {
		var $allocated = null;
		var $posted = null;

		function journaledittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'jnlKey' );
			$this->addField( 'jnlType' );
			$this->addField( 'jnlCode' );
			$this->addField( 'jnlRef' );
			$this->addField( 'jnlName' );
			$this->addField( 'jnlDate' );
			$this->addField( 'jnlTotal' );
			$this->addField( 'jnlIncludeAll' );
			$this->addField( 'jnlSysTran' );
			$this->setFieldType( 'jnlDate', 'DATE' );
			$this->setFieldType( 'jnlTotal', 'MONEY' );
			$this->setFieldType( 'jnlIncludeAll', 'checked' );
			$this->addField( 'journalType' );
			$this->allocated = array(  );
			$this->posted = false;
		}

		function clearinput() {
			$this->set( 'jnlKey', '' );
			$this->set( 'jnlType', '' );
			$this->set( 'jnlRef', '' );
			$this->set( 'jnlCode', 0 );
			$this->set( 'jnlDate', '' );
			$this->set( 'jnlName', '' );
			$this->set( 'jnlTotal', 0 );
			$this->set( 'jnlSysTran', 0 );
			$this->allocated = array(  );
			$this->setJournalPosted( false );
		}

		function clearallocated() {
			$this->allocated = array(  );
		}

		function checkbatchdate() {
			$date = trim( $this->get( 'jnlDate' ) );

			if ($date == '') {
				return 'Journal must have a date';
			}

			$date = umakesqldate2( $date );

			if (fisdateinthisaccountingperiod( $date ) == false) {
				return 'posting date not in the current accounting period';
			}

			return null;
		}

		function checkamounts() {
			$jnlType = $this->get( 'jnlType' );
			$total = uconvertmoneytointeger( $this->get( 'jnlTotal' ) );

			if ($total != 0) {
				return 'total must be zero before journal can be posted';
			}


			if (strlen( trim( $this->get( 'jnlRef' ) ) ) == 0) {
				return 'you must enter a narrative';
			}

			$items = 0;
			foreach ($this->allocated as $key => $value) {
				$alloc = uconvertmoneytointeger( $value );

				if ($alloc == 0) {
					continue;
				}

				$bal = 0;

				if (substr( $jnlType, 1, 1 ) == 'C') {
					$ct = new ClientTransaction( $key );
					$bal = $ct->get( 'ctBalance' );
				}


				if (substr( $jnlType, 1, 1 ) == 'I') {
					$it = new InsCoTransaction( $key );
					$bal = $it->get( 'itBalance' );
				}


				if (substr( $jnlType, 1, 1 ) == 'N') {
					$rt = new IntroducerTransaction( $key );
					$bal = $rt->get( 'rtBalance' );
				}


				if ($bal != $alloc) {
					return 'at least one item hasn\'t been completely cleared';
				}

				++$items;
			}


			if ($items == 0) {
				return 'no amounts have been allocated';
			}

			return null;
		}

		function setjournalposted($posted) {
			$this->posted = $posted;
		}

		function doposting() {
			$jnlCode = $this->get( 'jnlCode' );

			if ($jnlCode < 1) {
				trigger_error( 'no code', E_USER_ERROR );
			}

			$jnlType = $this->get( 'jnlType' );

			if ($jnlType == 'CC') {
				$this->_postClientTran( $jnlCode, 'C' );
			}


			if ($jnlType == 'CI') {
				$this->_postInsCoTran( $jnlCode, 'C' );
			}


			if ($jnlType == 'CN') {
				$this->_postIntroducerTran( $jnlCode, 'C' );
			}


			if ($jnlType == 'NC') {
				$this->_postClientTran( $jnlCode, 'N' );
			}


			if ($jnlType == 'NI') {
				$this->_postInsCoTran( $jnlCode, 'N' );
			}


			if ($jnlType == 'NN') {
				$this->_postIntroducerTran( $jnlCode, 'N' );
			}

			$this->setJournalPosted( true );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function _postclienttran($clCode, $type) {
			global $accountingYear;
			global $accountingPeriod;
			global $user;

			$processDate = $this->get( 'jnlDate' );
			$paymentMethod = JOURNAL_PAYMENT_METHOD;
			$ref = $this->get( 'jnlRef' );
			$amt = 0;

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
			$ct->set( 'ctTransType', 'J' );
			$ct->set( 'ctSysTran', $tnCode );
			$ct->set( 'ctClient', $clCode );
			$ct->set( 'ctCashBatch', 0 );
			$ct->set( 'ctCashBatchItem', 0 );
			$ct->set( 'ctTransDesc', $ref );
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
			$jnType = $type . 'C';
			$jn = new Journal( null );
			$jn->set( 'jnType', $jnType );
			$jn->set( 'jnSysTran', $tnCode );
			$jn->set( 'jnTran', $ctCode );
			$jn->set( 'jnMaster', $clCode );
			$jn->set( 'jnNarrative', $ref );
			$jn->set( 'jnPostingDate', $ct->get( 'ctPostingDate' ) );
			$jn->set( 'jnAccountingYear', $accountingYear );
			$jn->set( 'jnAccountingPeriod', $accountingPeriod );
			$jn->set( 'jnCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$jn->set( 'jnCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$jn->insert( null );
			$jnCode = $jn->getKeyValue(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', $jnType );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaAmount', $amt );
			$aa->set( 'aaTran', $jnCode );
			$aa->set( 'aaPostingDate', $ct->get( 'ctPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $ct->get( 'ctEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$aa->insert( null );
			$total = $amt;
			$ct->set( 'ctJournal', $jnCode );
			$ct->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $ct->getKeyValue(  ) );
			$st->set( 'tnType', 'CJ' );
			$st->set( 'tnCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$st->set( 'tnCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$st->update(  );
			foreach ($this->allocated as $key => $value) {
				$code = $key;
				$alloc = uconvertmoneytointeger( $value );

				if ($alloc == 0) {
					continue;
				}

				$ctOld = new ClientTransaction( $code );
				$ctPaid = (int)$ctOld->get( 'ctPaid' );
				$ctPaid += $alloc;
				$ctOld->set( 'ctPaid', $ctPaid );
				$ctOld->recalcTotals(  );

				if (( $ctOld->get( 'ctBalance' ) == 0 && $type == 'N' )) {
					$ctOld->set( 'ctPaidDate', $processDate );
				}

				$ctOld->update(  );
				$ca = new ClientTransAllocation( null );
				$ca->set( 'caType', 'J' );
				$ca->set( 'caCashTran', $ctCode );
				$ca->set( 'caOtherTran', $code );
				$ca->set( 'caAmount', $alloc );
				$ca->set( 'caPaymentMethod', $paymentMethod );
				$ca->set( 'caPostingDate', $processDate );
				$ca->set( 'caAccountingYear', $accountingYear );
				$ca->set( 'caAccountingPeriod', $accountingPeriod );
				$ca->insert( null );
				$biCode = $ctOld->get( 'ctCashBatchItem' );

				if (0 < $biCode) {
					$item = new CashBatchItem( $biCode );
					$item->set( 'biDateAllocated', ugettimenow(  ) );

					if (isset( $user )) {
						$usCode = $user->getKeyValue(  );
						$item->set( 'biAllocatedBy', $usCode );
					}

					$item->set( 'biAllocated', 1 );
					$item->update(  );
				}

				$btCode = $ctOld->get( 'ctCashBatch' );

				if (0 < $btCode) {
					$batch = new CashBatch( $btCode );
					$batch->recalcAllocated(  );
					$batch->update(  );
					continue;
				}
			}

			$ct->update(  );
			udbcommittransaction(  );
			$this->set( 'jnlKey', $jnCode );
		}

		function _postinscotran($icCode, $type) {
			global $accountingYear;
			global $accountingPeriod;
			global $user;

			$processDate = $this->get( 'jnlDate' );
			$paymentMethod = JOURNAL_PAYMENT_METHOD;
			$ref = $this->get( 'jnlRef' );
			$amt = 0;

			if ($icCode < 1) {
				trigger_error( 'no ins co', E_USER_ERROR );
			}

			$ok = udbcantabledotransactions( 'inscoTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$it = new InsCoTransaction( null );
			$it->set( 'itTransType', 'J' );
			$it->set( 'itSysTran', $tnCode );
			$it->set( 'itInsCo', $icCode );
			$it->set( 'itCashBatch', 0 );
			$it->set( 'itCashBatchItem', 0 );
			$it->set( 'itTransDesc', $ref );
			$it->set( 'itPaymentMethod', $paymentMethod );
			$it->set( 'itPostingDate', $processDate );
			$it->set( 'itEffectiveDate', $processDate );
			$it->set( 'itOriginal', $amt );
			$it->set( 'itPaid', 0 );
			$it->set( 'itBalance', $amt );
			$it->set( 'itAccountingYear', $accountingYear );
			$it->set( 'itAccountingPeriod', $accountingPeriod );
			$it->setCreatedByAndWhen(  );
			$it->insert( null );
			$itCode = $it->getKeyValue(  );
			$jnType = $type . 'I';
			$jn = new Journal( null );
			$jn->set( 'jnType', $jnType );
			$jn->set( 'jnSysTran', $tnCode );
			$jn->set( 'jnTran', $itCode );
			$jn->set( 'jnMaster', $icCode );
			$jn->set( 'jnNarrative', $ref );
			$jn->set( 'jnPostingDate', $it->get( 'itPostingDate' ) );
			$jn->set( 'jnAccountingYear', $accountingYear );
			$jn->set( 'jnAccountingPeriod', $accountingPeriod );
			$jn->set( 'jnCreatedBy', $it->get( 'itCreatedBy' ) );
			$jn->set( 'jnCreatedOn', $it->get( 'itCreatedOn' ) );
			$jn->insert( null );
			$jnCode = $jn->getKeyValue(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', $jnType );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaAmount', $amt );
			$aa->set( 'aaTran', $jnCode );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaPostingDate', $it->get( 'itPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $it->get( 'itEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $it->get( 'itCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $it->get( 'itCreatedOn' ) );
			$aa->insert( null );
			$total = $amt;
			$it->set( 'itJournal', $jnCode );
			$it->set( 'itSysTran', $tnCode );
			$it->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $it->getKeyValue(  ) );
			$st->set( 'tnType', 'IJ' );
			$st->set( 'tnCreatedBy', $it->get( 'itCreatedBy' ) );
			$st->set( 'tnCreatedOn', $it->get( 'itCreatedOn' ) );
			$st->update(  );
			foreach ($this->allocated as $key => $value) {
				$code = $key;
				$alloc = uconvertmoneytointeger( $value );

				if ($alloc == 0) {
					continue;
				}

				$itOld = new InsCoTransaction( $code );
				$itPaid = (int)$itOld->get( 'itPaid' );
				$itPaid += $alloc;
				$itOld->set( 'itPaid', $itPaid );
				$itBalance = (int)$itOld->get( 'itBalance' );
				$itBalance -= $alloc;
				$itOld->set( 'itBalance', $itBalance );

				if (( $itBalance == 0 && $type == 'N' )) {
					$itOld->set( 'itPaidDate', $processDate );
				}

				$itOld->update(  );
				$ia = new InsCoTransAllocation( null );
				$ia->set( 'iaType', 'J' );
				$ia->set( 'iaCashTran', $itCode );
				$ia->set( 'iaOtherTran', $code );
				$ia->set( 'iaAmount', $alloc );
				$ia->set( 'iaPaymentMethod', $paymentMethod );
				$ia->set( 'iaPostingDate', $processDate );
				$ia->set( 'iaAccountingYear', $accountingYear );
				$ia->set( 'iaAccountingPeriod', $accountingPeriod );
				$ia->insert( null );
				$biCode = $itOld->get( 'itCashBatchItem' );

				if (0 < $biCode) {
					$item = new CashBatchItem( $biCode );
					$item->set( 'biDateAllocated', ugettimenow(  ) );

					if (isset( $user )) {
						$usCode = $user->getKeyValue(  );
						$item->set( 'biAllocatedBy', $usCode );
					}

					$item->set( 'biAllocated', 1 );
					$item->update(  );
				}

				$btCode = $itOld->get( 'itCashBatch' );

				if (0 < $btCode) {
					$batch = new CashBatch( $btCode );
					$batch->recalcAllocated(  );
					$batch->update(  );
					continue;
				}
			}

			$it->update(  );
			udbcommittransaction(  );
			$this->set( 'jnlKey', $jnCode );
		}

		function _postintroducertran($inCode, $type) {
			global $accountingYear;
			global $accountingPeriod;
			global $user;

			$processDate = $this->get( 'jnlDate' );
			$paymentMethod = JOURNAL_PAYMENT_METHOD;
			$ref = $this->get( 'jnlRef' );
			$amt = 0;

			if ($inCode < 1) {
				trigger_error( 'no introducer', E_USER_ERROR );
			}

			$ok = udbcantabledotransactions( 'inscoTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$rt = new IntroducerTransaction( null );
			$rt->set( 'rtSysTran', $tnCode );
			$rt->set( 'rtTransType', 'J' );
			$rt->set( 'rtIntroducer', $inCode );
			$rt->set( 'rtCashBatch', 0 );
			$rt->set( 'rtCashBatchrtem', 0 );
			$rt->set( 'rtTransDesc', $ref );
			$rt->set( 'rtPaymentMethod', $paymentMethod );
			$rt->set( 'rtPostingDate', $processDate );
			$rt->set( 'rtEffectiveDate', $processDate );
			$rt->set( 'rtOriginal', $amt );
			$rt->set( 'rtPaid', 0 );
			$rt->set( 'rtBalance', $amt );
			$rt->set( 'rtAccountingYear', $accountingYear );
			$rt->set( 'rtAccountingPeriod', $accountingPeriod );
			$rt->setCreatedByAndWhen(  );
			$rt->insert( null );
			$rtCode = $rt->getKeyValue(  );
			$jnType = $type . 'N';
			$jn = new Journal( null );
			$jn->set( 'jnType', $jnType );
			$jn->set( 'jnSysTran', $tnCode );
			$jn->set( 'jnTran', $rtCode );
			$jn->set( 'jnMaster', $inCode );
			$jn->set( 'jnNarrative', $ref );
			$jn->set( 'jnPostingDate', $rt->get( 'rtPostingDate' ) );
			$jn->set( 'jnAccountingYear', $accountingYear );
			$jn->set( 'jnAccountingPeriod', $accountingPeriod );
			$jn->set( 'jnCreatedBy', $rt->get( 'rtCreatedBy' ) );
			$jn->set( 'jnCreatedOn', $rt->get( 'rtCreatedOn' ) );
			$jn->insert( null );
			$jnCode = $jn->getKeyValue(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', $jnType );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaTran', $jnCode );
			$aa->set( 'aaPostingDate', $rt->get( 'rtPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $rt->get( 'rtEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $rt->get( 'rtCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $rt->get( 'rtCreatedOn' ) );
			$aa->insert( null );
			$total = $amt;
			$rt->set( 'rtJournal', $jnCode );
			$rt->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $rt->getKeyValue(  ) );
			$st->set( 'tnType', 'RJ' );
			$st->set( 'tnCreatedBy', $rt->get( 'rtCreatedBy' ) );
			$st->set( 'tnCreatedOn', $rt->get( 'rtCreatedOn' ) );
			$st->update(  );
			foreach ($this->allocated as $key => $value) {
				$code = $key;
				$alloc = uconvertmoneytointeger( $value );

				if ($alloc == 0) {
					continue;
				}

				$rtOld = new IntroducerTransaction( $code );
				$rtPaid = (int)$rtOld->get( 'rtPaid' );
				$rtPaid += $alloc;
				$rtOld->set( 'rtPaid', $rtPaid );
				$rtBalance = (int)$rtOld->get( 'rtBalance' );
				$rtBalance -= $alloc;
				$rtOld->set( 'rtBalance', $rtBalance );

				if (( $rtBalance == 0 && $type == 'N' )) {
					$rtOld->set( 'rtPaidDate', $processDate );
				}

				$rtOld->update(  );
				$ra = new IntroducerTransAllocation( null );
				$ra->set( 'raType', 'J' );
				$ra->set( 'raCashTran', $rtCode );
				$ra->set( 'raOtherTran', $code );
				$ra->set( 'raAmount', $alloc );
				$ra->set( 'raPaymentMethod', $paymentMethod );
				$ra->set( 'raPostingDate', $processDate );
				$ra->set( 'raAccountingYear', $accountingYear );
				$ra->set( 'raAccountingPeriod', $accountingPeriod );
				$ra->insert( null );
				$biCode = $rtOld->get( 'rtCashBatchItem' );

				if (0 < $biCode) {
					$item = new CashBatchItem( $biCode );
					$item->set( 'biDateAllocated', ugettimenow(  ) );

					if (isset( $user )) {
						$usCode = $user->getKeyValue(  );
						$item->set( 'biAllocatedBy', $usCode );
					}

					$item->set( 'biAllocated', 1 );
					$item->update(  );
				}

				$btCode = $rtOld->get( 'rtCashBatch' );

				if (0 < $btCode) {
					$batch = new CashBatch( $btCode );
					$batch->recalcAllocated(  );
					$batch->update(  );
					continue;
				}
			}

			$rt->update(  );
			udbcommittransaction(  );
			$this->set( 'jnlKey', $jnCode );
		}

		function getjournaltype() {
			$type = $this->get( 'jnlType' );
			return $type;
		}

		function newitem($type) {
			$this->clearInput(  );
			$this->set( 'jnlType', $type );
			$name = fgetjournaltypedescription( $type );
			$this->set( 'journalType', $name );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			$this->setMessage( 'new journal started' );
			$this->setJournalPosted( false );
		}

		function whenclient($text) {
			$type = $this->get( 'jnlType' );

			if (substr( $type, 1, 1 ) != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function wheninsco($text) {
			$type = $this->get( 'jnlType' );

			if (substr( $type, 1, 1 ) != 'I') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenintroducer($text) {
			$type = $this->get( 'jnlType' );

			if (substr( $type, 1, 1 ) != 'N') {
				return '';
			}

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
			$jnlCode = $this->get( 'jnlCode' );

			if ($jnlCode < 1) {
				return '';
			}

			$jnlType = $this->get( 'jnlType' );
			$jnlIncludeAll = $this->get( 'jnlIncludeAll' );

			if ($this->posted == true) {
				foreach ($this->allocated as $key => $value) {
					if ($value == 0) {
						continue;
					}

					$code = $key;

					if (substr( $jnlType, 1, 1 ) == 'C') {
						$out .= $this->_showClientRow( $code, $text );
					}


					if (substr( $jnlType, 1, 1 ) == 'I') {
						$out .= $this->_showInscoRow( $code, $text );
					}


					if (substr( $jnlType, 1, 1 ) == 'N') {
						$out .= $this->_showIntroducerRow( $code, $text );
						continue;
					}
				}

				return $out;
			}

			$cashType = substr( $jnlType, 0, 1 );

			if (substr( $jnlType, 1, 1 ) == 'C') {
				$q = '' . 'SELECT ctCode FROM clientTransactions 
				  WHERE ctClient = ' . $jnlCode;

				if ($cashType == 'C') {
					$q .= ' AND ctTransType =\'C\' ';
				}


				if ($cashType == 'N') {
					$q .= ' AND ctTransType =\'I\' ';
				}


				if (( ( $jnlIncludeAll != 1 && $jnlIncludeAll != 'on' ) && $jnlIncludeAll != 'checked' )) {
					$q .= 'AND ctBalance != 0 ';
				}

				$q .= 'ORDER BY ctPostingDate DESC,  ctCode DESC ';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$ctCode = $row['ctCode'];
					$out .= $this->_showClientRow( $ctCode, $text );
				}

				return $out;
			}


			if (substr( $jnlType, 1, 1 ) == 'I') {
				$q = '' . 'SELECT itCode FROM inscoTransactions 
				  WHERE itInsCo = ' . $jnlCode . ' ';

				if ($cashType == 'C') {
					$q .= ' AND (itTransType =\'C\' OR itTransType =\'R\') ';
				}


				if ($cashType == 'N') {
					$q .= ' AND itTransType =\'I\' ';
				}


				if (( ( $jnlIncludeAll != 1 && $jnlIncludeAll != 'on' ) && $jnlIncludeAll != 'checked' )) {
					$q .= 'AND itBalance != 0 ';
				}

				$q .= 'ORDER BY itPostingDate DESC,  itCode DESC ';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$itCode = $row['itCode'];
					$out .= $this->_showInscoRow( $itCode, $text );
				}

				return $out;
			}


			if (substr( $jnlType, 1, 1 ) == 'N') {
				$q = '' . 'SELECT rtCode FROM introducerTransactions 
				  WHERE rtIntroducer = ' . $jnlCode . ' ';

				if ($cashType == 'C') {
					$q .= ' AND (rtTransType =\'C\' OR rtTransType =\'R\') ';
				}


				if ($cashType == 'N') {
					$q .= ' AND rtTransType =\'I\' ';
				}


				if (( ( $jnlIncludeAll != 1 && $jnlIncludeAll != 'on' ) && $jnlIncludeAll != 'checked' )) {
					$q .= 'AND rtBalance != 0 ';
				}

				$q .= 'ORDER BY rtPostingDate DESC,  rtCode DESC ';
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$rtCode = $row['rtCode'];
					$out .= $this->_showIntroducerRow( $rtCode, $text );
				}

				return $out;
			}

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
			foreach ($input as $key => $value) {
				if (substr( $key, 0, 6 ) != 'alloc_') {
					continue;
				}

				$code = substr( $key, 6 );
				$this->allocated[$code] = $value;
			}

			$total = 0;
			foreach ($this->allocated as $key => $value) {
				$total += uconvertmoneytointeger( $value );
			}

			$this->set( 'jnlTotal', $total );
			$desc = '';
			$jnlType = $this->get( 'jnlType' );

			if (isset( $input['jnlType'] ) == true) {
				$jnlType = $input['jnlType'];
			}

			$desc = fgetjournaltypedescription( $jnlType );
			$this->set( 'journalType', $desc );
			return false;
		}

		function _showclientrow($ctCode, $text) {
			$ct = new ClientTransaction( $ctCode );
			$ctPaymentMethod = $ct->get( 'ctPaymentMethod' );

			if (0 < $ctPaymentMethod) {
				$cpt = new CashPaymentMethod( $ctPaymentMethod );
				$payMethod = $cpt->get( 'cpName' );
			} 
else {
				$payMethod = '';
			}

			$transDate = $ct->getForHTML( 'ctPostingDate' );
			$transRef = $ct->getForHTML( 'ctCode' );
			$ctSysTran = $ct->getForHTML( 'ctSysTran' );
			$orig = uformatmoney( 0 - $ct->get( 'ctOriginal' ) );
			$paid = uformatmoney( 0 - $ct->get( 'ctPaid' ) );
			$balance = uformatmoney( 0 - $ct->get( 'ctBalance' ) );
			$toClear = $ct->get( 'ctBalance' );
			$this->set( 'code', $ctCode );
			$this->set( 'transNo', sprintf( '%07d', $ctSysTran ) );
			$this->set( 'transRef', $transRef );
			$this->set( 'transDate', $transDate );
			$this->set( 'payMethod', $payMethod );
			$this->set( 'orig', $orig );
			$this->set( 'paid', $paid );
			$this->set( 'balance', $balance );
			$this->set( 'toClear', $toClear );
			$ctTransType = $ct->get( 'ctTransType' );
			$typeName = '';

			if ($ctTransType == 'I') {
				$ptCode = $ct->get( 'ctPolicyTran' );
				$pt = new PolicyTransaction( $ptCode );
				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$typeName = $tranType->get( 'pyName' );
			} 
else {
				$orig = $ct->get( 'ctOriginal' );

				if ($orig <= 0) {
					$typeName = 'Cash Received';
				} 
else {
					$typeName = 'Cash Payment';
				}


				if ($ctTransType == 'J') {
					$typeName = 'Journal';
				}
			}

			$this->set( 'transType', $typeName );

			if (isset( $this->allocated[$ctCode] )) {
				$allocated = uformatmoney( $this->allocated[$ctCode] );
			} 
else {
				$allocated = '0.00';
			}

			$this->set( 'allocated', $allocated );
			$out = $this->parse( $text );
			return $out;
		}

		function _showinscorow($itCode, $text) {
			$it = new InsCoTransaction( $itCode );
			$transDesc = '';
			$itTransType = $it->get( 'itTransType' );
			$ptCode = $it->get( 'itPolicyTran' );

			if (0 < $ptCode) {
				$pt = new PolicyTransaction( $ptCode );
				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$transDesc = $tranType->get( 'pyName' );
			} 
else {
				$bal = $it->get( 'itBalance' );

				if (0 < $it->get( 'itOriginal' )) {
					$transDesc = 'IC Rec Receipt';
				} 
else {
					$transDesc = 'IC Rec Payment';
				}


				if ($it->get( 'itTransType' ) == 'J') {
					$transDesc = 'Journal';
				}


				if ($it->get( 'itOriginal' ) != 0) {
					if ($bal != 0) {
						$transDesc .= ' Unalloc';
					}
				}
			}

			$this->set( 'transType', $transDesc );
			$itSysTran = $it->getForHTML( 'itSysTran' );
			$transDate = $it->getForHTML( 'itPostingDate' );
			$transRef = $it->getForHTML( 'itInsCoRef' );
			$orig = uformatmoney( $it->get( 'itOriginal' ) );
			$paid = uformatmoney( $it->get( 'itPaid' ) );
			$balance = uformatmoney( $it->get( 'itBalance' ) );
			$toClear = $it->get( 'itBalance' );
			$this->set( 'code', $itCode );
			$this->set( 'transNo', sprintf( '%07d', $itSysTran ) );
			$this->set( 'transDate', $transDate );
			$this->set( 'transRef', $transRef );
			$this->set( 'orig', $orig );
			$this->set( 'paid', $paid );
			$this->set( 'balance', $balance );
			$this->set( 'toClear', $toClear );

			if (isset( $this->allocated[$itCode] )) {
				$allocated = uformatmoney( $this->allocated[$itCode] );
			} 
else {
				$allocated = '0.00';
			}

			$this->set( 'allocated', $allocated );
			$out = $this->parse( $text );
			return $out;
		}

		function _showintroducerrow($rtCode, $text) {
			$rt = new IntroducerTransaction( $rtCode );
			$rtPaymentType = $rt->get( 'rtPaymentType' );

			if (0 < $rtPaymentType) {
				$cpt = new CashPaymentMethod( $rtPaymentType );
				$payMethod = $cpt->get( 'cpName' );
			} 
else {
				$payMethod = '';
			}

			$transDate = $rt->getForHTML( 'rtPostingDate' );
			$transRef = $rt->getForHTML( 'rtIntrodRef' );
			$orig = uformatmoney( $rt->get( 'rtOriginal' ) );
			$paid = uformatmoney( $rt->get( 'rtPaid' ) );
			$balance = uformatmoney( $rt->get( 'rtBalance' ) );
			$toClear = $rt->get( 'rtBalance' );
			$rtTransType = $rt->get( 'rtTransType' );
			$transType = '';

			if ($rtTransType == 'I') {
				$ptCode = $rt->get( 'rtPolicyTran' );

				if (0 < $ptCode) {
					$pt = new PolicyTransaction( $ptCode );
					$type = $pt->get( 'ptTransType' );
					$tranType = new PolicyTransactionType( $type );
					$transType = $tranType->get( 'pyName' );
				}
			} 
else {
				if (0 < $rt->get( 'rtOriginal' )) {
					$transType = 'Introd. Recon. Receipt';
				} 
else {
					$transType = 'Introd. Recon. Payment';
				}


				if ($rtTransType == 'J') {
					$transType = 'Journal';
				}
			}

			$this->set( 'transType', $transType );
			$rtSysTran = $rt->getForHTML( 'rtSysTran' );
			$this->set( 'code', $rtCode );
			$this->set( 'transNo', sprintf( '%07d', $rtSysTran ) );
			$this->set( 'transDate', $transDate );
			$this->set( 'transRef', $transRef );
			$this->set( 'payMethod', $payMethod );
			$this->set( 'orig', $orig );
			$this->set( 'paid', $paid );
			$this->set( 'balance', $balance );
			$this->set( 'toClear', $toClear );

			if (isset( $this->allocated[$rtCode] )) {
				$allocated = uformatmoney( $this->allocated[$rtCode] );
			} 
else {
				$allocated = '0.00';
			}

			$this->set( 'allocated', $allocated );
			$out = $this->parse( $text );
			return $out;
		}

		function setjournal($jnCode) {
			if ($jnCode <= 0) {
				trigger_error( 'cant create journal ' . $jnCode, E_USER_ERROR );
			}

			$this->set( 'jnlCode', $jnCode );
			$this->journal = new Journal( $jnCode );
			$jnType = $this->journal->get( 'jnType' );
			$jnTran = $this->journal->get( 'jnTran' );
			$this->set( 'jnlKey', $this->journal->get( 'jnCode' ) );
			$this->set( 'jnlRef', $this->journal->get( 'jnNarrative' ) );
			$this->set( 'jnlDate', $this->journal->getForHTML( 'jnPostingDate' ) );
			$this->set( 'jnlTotal', $this->journal->getForHTML( 'jnAmount' ) );
			$this->set( 'jnlSysTran', $this->journal->getForHTML( 'jnSysTran' ) );
			$this->setAll( $this->journal->getAllForHTML(  ) );
			$this->set( 'jnlType', $jnType );
			$code = $this->journal->get( 'jnMaster' );
			$name = '';

			if (substr( $jnType, 1, 1 ) == 'C') {
				$cl = new Client( $code );
				$name = $cl->getDisplayName(  );
			}


			if (substr( $jnType, 1, 1 ) == 'I') {
				$ic = new Insco( $code );
				$name = $ic->get( 'icName' );
			}


			if (substr( $jnType, 1, 1 ) == 'N') {
				$in = new Introducer( $code );
				$name = $in->get( 'inName' );
			}

			$this->set( 'jnlName', $name );
			$desc = fgetjournaltypedescription( $jnType );
			$this->set( 'journalType', $desc );
			$this->readInItems( $jnType, $jnTran );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
			$this->setJournalPosted( true );
		}

		function readinitems($jnType, $jnTran) {
			$this->allocated = array(  );
			$jnCode = $this->get( 'jnlCode' );

			if ($jnCode <= 0) {
				return '';
			}


			if (substr( $jnType, 1, 1 ) == 'C') {
				$q = '' . 'SELECT caOtherTran, caAmount FROM clientTransAllocations WHERE caCashTran = ' . $jnTran;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$key = $row['caOtherTran'];
					$amt = $row['caAmount'];
					$this->allocated[$key] = $amt;
				}
			}


			if (substr( $jnType, 1, 1 ) == 'I') {
				$q = '' . 'SELECT iaOtherTran, iaAmount FROM inscoTransAllocations WHERE iaCashTran = ' . $jnTran;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$key = $row['iaOtherTran'];
					$amt = $row['iaAmount'];
					$this->allocated[$key] = $amt;
				}
			}


			if (substr( $jnType, 1, 1 ) == 'N') {
				$q = '' . 'SELECT raOtherTran, raAmount FROM introducerTransAllocations WHERE raCashTran = ' . $jnTran;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}


				while ($row = udbgetrow( $result )) {
					$key = $row['raOtherTran'];
					$amt = $row['raAmount'];
					$this->allocated[$key] = $amt;
				}
			}

		}
	}

?>