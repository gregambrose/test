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

	class ibaothertemplate {
		var $flds = null;

		function ibaothertemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'transType' );
			$this->addField( 'openingBalance' );
			$this->addField( 'movement' );
			$this->addField( 'closingBalance' );
			$this->addField( 'transRef' );
			$this->addField( 'transDesc' );
			$this->addField( 'transAmount' );
			$this->addField( 'postingDate' );
			$this->addField( 'paymentMethod' );
			$this->setFieldType( 'postingDate', 'DATE' );
			$this->setFieldType( 'transAmount', 'MONEY' );
			$this->setAllowEditing( false );
		}

		function validate() {
			$messg = null;
			$postingDate = trim( $this->get( 'postingDate' ) );
			$postingDate = umakesqldate( $postingDate );

			if (( ( $postingDate == null || $postingDate == '0000-00-00' ) || $postingDate == '' )) {
				return 'you need to enter a posting date';
			}


			if (fisdateinthisaccountingperiod( $postingDate ) == false) {
				return 'posting date not in the current accounting period';
			}

			$transType = $this->get( 'transType' );

			if ($transType < 1) {
				return 'you need to select a transaction type';
			}

			$paymentMethod = $this->get( 'paymentMethod' );

			if ($paymentMethod < 1) {
				return 'you need to select a payment type';
			}

			$transAmount = $this->get( 'transAmount' );

			if ($transAmount <= 0) {
				return 'you need to enter an amount';
			}

			$transDesc = trim( $this->get( 'transDesc' ) );

			if (strlen( $transDesc ) == 0) {
				return 'you need to enter a description';
			}

			return $messg;
		}

		function post() {
			global $accountingYear;
			global $accountingPeriod;

			$ok = udbcantabledotransactions( 'bankAccountTrans' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$transType = $this->get( 'transType' );
			$transRef = $this->get( 'transRef' );
			$transDesc = $this->get( 'transDesc' );
			$transAmount = $this->getMoneyAsPennies( 'transAmount' );
			$postingDate = $this->get( 'postingDate' );
			$paymentMethod = $this->get( 'paymentMethod' );
			$dr = 1;

			if (0 < $transType) {
				$btt = new BankTransType( $transType );
				$dr = $btt->get( 'byDebit' );
			}


			if ($dr != 1) {
				$transAmount = 0 - $transAmount;
			}

			$ba = new BankAccountTran( null );
			$ba->set( 'baType', $transType );
			$ba->set( 'baSysTran', $tnCode );
			$ba->set( 'baTran', 0 );
			$ba->set( 'baDebit', $dr );
			$ba->set( 'baAmount', $transAmount );
			$ba->set( 'baDescription', $transDesc );
			$ba->set( 'baPostingRef', $transRef );
			$ba->set( 'baPaymentType', $paymentMethod );
			$ba->set( 'baPostingDate', $postingDate );
			$ba->set( 'baAccountingYear', $accountingYear );
			$ba->set( 'baAccountingPeriod', $accountingPeriod );
			$ba->setCreatedByAndWhen(  );
			$ba->insert( null );
			$baCode = $ba->getKeyValue(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'IO' );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaTran', $baCode );
			$aa->set( 'aaPostingDate', $ba->get( 'baPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $ba->get( 'baPostingDate' ) );
			$aa->set( 'aaAccountingYear', $accountingYear );
			$aa->set( 'aaAccountingPeriod', $accountingPeriod );
			$aa->set( 'aaCreatedBy', $ba->get( 'baCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $ba->get( 'baCreatedOn' ) );
			$aa->insert( null );
			$this->set( 'transCode', $baCode );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $baCode );
			$st->set( 'tnType', 'BA' );
			$st->set( 'tnCreatedBy', $ba->get( 'baCreatedBy' ) );
			$st->set( 'tnCreatedOn', $ba->get( 'baCreatedOn' ) );
			$st->update(  );
			udbcommittransaction(  );
			$baCode = $ba->getKeyValue(  );
			$this->viewTrans( $baCode );
		}

		function clearinputfields() {
			$this->set( 'transType', 0 );
			$this->set( 'transCode', '' );
			$this->set( 'transRef', '' );
			$this->set( 'transDesc', '' );
			$this->set( 'transAmount', '.00' );
			$this->set( 'postingDate', '' );
			$this->set( 'paymentMethod', 0 );
		}

		function viewtrans($baCode) {
			if ($baCode < 1) {
				return false;
			}

			$ba = new BankAccountTran( $baCode );
			$ba->fetchExtraColumns(  );
			$this->set( 'transCode', $ba->get( 'baCode' ) );
			$this->set( 'transType', $ba->get( 'baType' ) );
			$this->set( 'transRef', $ba->get( 'baPostingRef' ) );
			$this->set( 'transAmount', $ba->get( 'baAmount' ) );
			$this->set( 'postingDate', $ba->getForHTML( 'baPostingDate' ) );
			$this->set( 'postingRef', $ba->get( 'baPostingRef' ) );
			$this->set( 'transDesc', $ba->get( 'baDescription' ) );
			$this->set( 'paymentMethod', $ba->get( 'baPaymentType' ) );
			$this->set( 'transType', $ba->get( 'baType' ) );
			$this->set( 'typeName', $ba->get( 'typeName' ) );
			$this->set( 'paymentMethodName', $ba->get( 'paymentMethodName' ) );
		}

		function makeopeningandclosing() {
			global $accountingPeriodCode;
			global $periodFrom;
			global $periodTo;

			$open = 0;
			$close = 0;
			$ap = new AccountingPeriod( $accountingPeriodCode );
			$prevCode = $ap->getPreviousPeriod(  );

			if (0 < $prevCode) {
				$q = '' . 'SELECT afIBAOther  FROM accountingFigures WHERE afPeriodCode = ' . $prevCode;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( udblasterror(  ), E_USER_ERROR );
				}

				$row = udbgetrow( $result );

				if ($row != null) {
					$open = $row['afIBAOther'];
				}
			}

			$q = '' . 'SELECT SUM(baAmount) as total FROM bankAccountTrans
			  WHERE baPostingDate >= \'' . $periodFrom . '\' AND baPostingDate <= \'' . $periodTo . '\'';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );

			if ($row == null) {
				trigger_error( 'no sum', E_USER_ERROR );
			}

			$total = $row['total'];
			$close = $open + $total;
			$openingBalance = uformatmoney( $open );
			$closingBalance = uformatmoney( $close );

			if (0 <= $open) {
				$openDebit = uformatmoney( $open );
				$openCredit = '';
			} 
else {
				$openCredit = uformatmoney( 0 - $open );
				$openDebit = '';
			}


			if (0 <= $close) {
				$closeDebit = uformatmoney( $close );
				$closeCredit = '';
			} 
else {
				$closeCredit = uformatmoney( 0 - $close );
				$closeDebit = '';
			}

			$this->set( 'debitOpening', $openDebit );
			$this->set( 'creditOpening', $openCredit );
			$this->set( 'debitClosing', $closeDebit );
			$this->set( 'creditClosing', $closeCredit );
			$this->set( 'openingBalance', $openingBalance );
			$this->set( 'closingBalance', $closingBalance );
		}

		function openingbaldate() {
			global $periodFrom;
			global $periodTo;

			$toDate = uformatsqldate2( $periodFrom );
			return $toDate;
		}

		function closingbaldate() {
			global $periodFrom;
			global $periodTo;

			$toDate = uformatsqldate2( $periodTo );
			return $toDate;
		}

		function currentdate() {
			$now = uformatsqldate2( ugettodayassqldate(  ) );
			return $now;
		}

		function showmovement() {
			global $periodFrom;
			global $periodTo;

			$q = '' . 'SELECT SUM(baAmount) as total FROM bankAccountTrans
			  WHERE  baPostingDate >= \'' . $periodFrom . '\' AND baPostingDate <= \'' . $periodTo . '\'';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );

			if ($row == null) {
				trigger_error( 'no sum', E_USER_ERROR );
			}

			$total = $row['total'];

			if (0 < $total) {
				$x = uformatmoney( $total );
				$this->set( 'debitMovement', $x );
				$this->set( 'creditMovement', '' );
			} 
else {
				$x = uformatmoney( 0 - $total );
				$this->set( 'debitMovement', '' );
				$this->set( 'creditMovement', $x );
			}

			$total = uformatmoneywithcommas( $total );
			return $total;
		}

		function listtransactions($text) {
			global $periodFrom;
			global $periodTo;

			$this->makeOpeningAndClosing(  );
			$out = '';
			$q = '' . 'SELECT baCode  FROM bankAccountTrans
			WHERE  baPostingDate >= \'' . $periodFrom . '\' AND baPostingDate <= \'' . $periodTo . '\'
			ORDER BY baPostingDate ';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$baCode = $row['baCode'];
				$ba = new BankAccountTran( $baCode );
				$amount = $ba->get( 'baAmount' );

				if (0 <= $amount) {
					$debitAmount = uformatmoney( $amount );
					$creditAmount = '';
				} 
else {
					$amount = 0 - $amount;
					$debitAmount = '';
					$creditAmount = uformatmoney( $amount );
				}

				$baType = $ba->get( 'baType' );
				$baTran = $ba->get( 'baTran' );
				$transType = 'unknown';

				if (0 < $baType) {
					$bat = new BankTransType( $baType );
					$transType = $bat->getForHTML( 'byName' );
				}

				$name = '';

				if ($baType == KEY_BANK_CASH_TO_CLIENT) {
					if (0 < $baTran) {
						$ct = new ClientTransaction( $baTran );
						$clCode = $ct->get( 'ctClient' );
						$client = new Client( $clCode );
						$name = $client->getDisplayName(  );
					}
				}


				if ($baType == KEY_BANK_CASH_TO_INSCO) {
					if (0 < $baTran) {
						$it = new InsCoTransaction( $baTran );
						$icCode = $it->get( 'itInsCo' );
						$ins = new Insco( $icCode );
						$name = $ins->get( 'icName' );
					}
				}


				if ($baType == KEY_BANK_CASH_TO_INTROD) {
					if (0 < $baTran) {
						$nt = new IntroducerTransaction( $baTran );
						$inCode = $nt->get( 'rtIntroducer' );
						$in = new Introducer( $inCode );
						$name = $in->get( 'inName' );
					}
				}

				$this->set( 'baCode', $baCode );
				$this->set( 'baDescription', $ba->getForHTML( 'baDescription' ) );
				$this->set( 'baSysTran', $ba->getForHTML( 'baSysTran' ) );
				$this->set( 'baPostingRef', $ba->getForHTML( 'baPostingRef' ) );
				$this->set( 'baPostingDate', $ba->getForHTML( 'baPostingDate' ) );
				$this->set( 'narrative', $name );
				$this->set( 'debitAmount', $debitAmount );
				$this->set( 'creditAmount', $creditAmount );
				$this->set( 'transType', $transType );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtransactiontypes($text) {
			$selectedType = $this->get( 'transType' );
			$out = '';
			$q = 'SELECT byCode, byName  FROM bankTransTypes
			WHERE byAllowUserSelect = TRUE
			ORDER BY bySequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$byCode = $row['byCode'];
				$byName = $row['byName'];
				$this->set( 'byCode', $byCode );
				$this->set( 'byName', $byName );

				if ($selectedType == $byCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listpaymentmethods($text) {
			$selectedType = $this->get( 'paymentMethod' );
			$out = '';
			$q = 'SELECT cpCode, cpName FROM cashPaymentMethods ORDER BY cpSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$cpCode = $row['cpCode'];
				$cpName = $row['cpName'];
				$this->set( 'cpCode', $cpCode );
				$this->set( 'cpName', $cpName );

				if ($selectedType == $cpCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>