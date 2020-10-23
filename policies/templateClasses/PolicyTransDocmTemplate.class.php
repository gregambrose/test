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

	class policytransdocmtemplate {
		var $doCashReceipts = false;

		function policytransdocmtemplate($html) {
			ftemplate::ftemplate( $html );
		}

		function settransaction($trans) {
			$this->trans = &$trans;

		}

		function setdocashreceipts($do) {
			$this->doCashReceipts = $do;
		}

		function whentwodates($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$type = $this->trans->get( 'ptTransType' );

			if ($type == 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenonedate($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$type = $this->trans->get( 'ptTransType' );

			if ($type != 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenadditional($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$amt = $this->trans->get( 'ptAddlGrossIncIPT' );
			$amt = uroundmoney( $amt );

			if ($amt == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenrenewalnotice($text) {
			$type = $this->trans->getDocumentType(  );

			if ($type != 'R') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenmainpremium($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$amt = $this->trans->get( 'ptGrossIncIPT' );
			$amt = uroundmoney( $amt );

			if ($amt == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenengfees($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$fees = $this->trans->get( 'ptEngineeringFee' );
			$fees = uroundmoney( $fees );

			if ($fees == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenengfeesvat($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$fees = $this->trans->get( 'ptEngineeringFeeVAT' );
			$fees = uroundmoney( $fees );

			if ($fees == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaddon($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$amt = $this->trans->get( 'ptAddOnGrossIncIPT' );
			$amt = uroundmoney( $amt );

			if ($amt == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenclientdiscount($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$direct = $this->trans->get( 'ptDirect' );

			if ($direct != 1) {
				return '';
			}

			$amt = $this->trans->get( 'ptClientDiscount' );

			if ($amt == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenbrokerfees($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$fees = $this->trans->get( 'ptBrokerFee' );

			if ($fees == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenvatonbrokerfees($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$fees = $this->trans->get( 'ptBrokerFeeVAT' );

			if ($fees == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenreturn($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$debit = $this->trans->get( 'ptDebit' );

			if ($debit == 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennotreturn($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$debit = $this->trans->get( 'ptDebit' );

			if ($debit != 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whendirect($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$direct = $this->trans->get( 'ptDirect' );

			if ($direct != 1) {
				return '';
			}

			$desc = 'PAID DIRECT';
			$pm = $this->trans->get( 'ptPaymentMethod' );

			if ($pm == 2) {
				$desc = 'BY INSTALMENTS';
			}

			$this->set( 'howPaid', $desc );
			$out = $this->parse( $text );
			return $out;
		}

		function whentotalneeded($text) {
			if (!isset( $this->trans )) {
				return '';
			}


			if ($this->trans->isTotalNeeded(  ) == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function wheninstalmenttotalneeded($text) {
			if (!isset( $this->trans )) {
				return '';
			}

			$direct = $this->trans->get( 'ptDirect' );

			if ($direct != 1) {
				return '';
			}

			$prem = $this->trans->get( 'ptGrossIncIPT' );
			$addl = $this->trans->get( 'ptAddlGrossIncIPT' );
			$fee = $this->trans->get( 'ptEngineeringFee' );
			$vat = $this->trans->get( 'ptEngineeringFeeVAT' );
			$items = 0;

			if ($prem != 0) {
				++$items;
			}


			if ($fee != 0) {
				++$items;
			}


			if ($vat != 0) {
				++$items;
			}


			if ($addl != 0) {
				++$items;
			}


			if ($items < 2) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenreceiptsmessageneeded($text) {
			if ($this->doCashReceipts != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennoreceiptsmessageneeded($text) {
			if ($this->doCashReceipts == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenpolicyholder($text) {
			$name = $this->get( 'policyHolderInCaps' );

			if (strlen( trim( $name ) ) == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenasterix0($text) {
			$out = $this->_handleAsterix( $text, 0, false );
			return $out;
		}

		function whenasterix1($text) {
			$out = $this->_handleAsterix( $text, 1, false );
			return $out;
		}

		function whenasterix2($text) {
			$out = $this->_handleAsterix( $text, 2, false );
			return $out;
		}

		function whenasterix3($text) {
			$out = $this->_handleAsterix( $text, 3, false );
			return $out;
		}

		function whenasterix4($text) {
			$out = $this->_handleAsterix( $text, 4, false );
			return $out;
		}

		function whenasterix5($text) {
			$out = $this->_handleAsterix( $text, 5, false );
			return $out;
		}

		function whenasterix6($text) {
			$out = $this->_handleAsterix( $text, 6, true );
			return $out;
		}

		function _handleasterix($text, $num, $total) {
			$ast = $this->trans->getAsterixArray(  );
			$astNumber = $ast[$num];

			if ($astNumber == 0 - 1) {
				return '';
			}


			if (( $this->trans->isTotalNeeded(  ) == false || $total == true )) {
				$messg = $this->_getPayToMessage(  );
			} 
else {
				$messg = '';
			}

			$this->set( 'payToMessage', $messg );
			$out = $this->parse( $text );
			$this->set( 'payToMessage', '' );
			return $out;
		}

		function asterix($num) {
			$ast = $this->trans->getAsterixArray(  );

			if (0 <= $num) {
				$astNumber = $ast[$num];
			} 
else {
				$astNumber = 0 - 1;
			}


			if ($astNumber == 0 - 1) {
				return '     ';
			}


			if ($astNumber == 0) {
				return '*   ';
			}

			return '*' . $astNumber . ' ';
		}

		function asterixnospace($num) {
			$ast = $this->trans->getAsterixArray(  );

			if (0 <= $num) {
				$astNumber = $ast[$num];
			} 
else {
				$astNumber = 0 - 1;
			}


			if ($astNumber == 0 - 1) {
				return '';
			}


			if ($astNumber == 0) {
				return '*';
			}

			return '*' . $astNumber . ' ';
		}

		function _getpaytomessage() {
			$debit = true;

			if ($this->trans->get( 'ptDebit' ) == true) {
				if (0 <= $this->trans->get( 'ptClientTotal' )) {
					$debit = true;
				} 
else {
					$debit = false;
				}
			}


			if ($this->trans->get( 'ptDebit' ) != true) {
				if (0 <= $this->trans->get( 'ptClientTotal' )) {
					$debit = false;
				} 
else {
					$debit = true;
				}
			}


			if ($debit == true) {
				$messg = DEBIT_PAY_TO_MESSAGE;
			} 
else {
				$messg = CREDIT_PAY_TO_MESSAGE;
			}

			return $messg;
		}
	}

?>