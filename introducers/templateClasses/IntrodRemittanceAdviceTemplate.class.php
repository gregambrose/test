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

	class introdremittanceadvicetemplate {
		function introdremittanceadvicetemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function settransaction($rtCode) {
			$this->type = 'IC';
			$rt = new IntroducerTransaction( $rtCode );
			$this->transaction = &$rt;

			$this->clearDetailFields(  );
			$this->setAll( $rt->getAllForHTML(  ) );
			$inCode = $rt->get( 'rtIntroducer' );
			$introd = new Introducer( $inCode );
			$this->introd = &$introd;

			$desc = '';
			$cpCode = $rt->get( 'rtPaymentType' );

			if (0 < $cpCode) {
				$cp = new CashPaymentMethod( $cpCode );
				$desc = $cp->getForHTML( 'cpName' );
			}

			$this->set( 'paymentTypeDesc', $desc );
			$this->set( 'icName', $introd->get( 'icName' ) );
			$this->set( 'address', $introd->getInvoiceNameAndAddress(  ) );
			$this->set( 'processDate', $rt->getForHTML( 'rtPostingDate' ) );
			$this->set( 'introdRef', $rt->getForHTML( 'rtIntrodRef' ) );
			$this->set( 'postingRef', $rt->getForHTML( 'rtChequeNo' ) );
			$this->set( 'paymentType', $rt->getForHTML( 'rtPaymentType' ) );
			$this->set( 'totalToPay', uformatmoney( 0 - $rt->get( 'rtPaid' ) ) );
			$this->set( 'code', sprintf( '%07d', $rtCode ) );
			$this->set( 'doCode', $rt->get( 'rtDocm' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setarraysoftransactions($trans, $toPay, $adjustments) {
			$this->trans = &$trans;
			$this->toPay = &$toPay;
			$this->adjustments = &$adjustments;

		}

		function getintroducer() {
			return $this->introd;
		}

		function listtransactions($text) {
			$grossTotal = 0;
			$netTotal = 0;
			$commTotal = 0;
			$origTotal = 0;
			$adjTotal = 0;
			$out = '';
			foreach ($this->trans as $key => $value) {
				$rtCode = $key;

				if (isset( $this->toPay[$key] )) {
					$toPay = (double)$this->toPay[$key];
				} 
else {
					$toPay = 0;
				}


				if (isset( $this->adjustments[$key] )) {
					$adj = (double)$this->adjustments[$key];
				} 
else {
					$adj = 0;
				}


				if (( $toPay == 0 && $adj == 0 )) {
					continue;
				}

				(int)$toPay *= 100;
				(int)$adj *= 100;
				$rt = new IntroducerTransaction( $rtCode );
				$this->currentTrans = &$rt;

				$ptCode = $rt->get( 'rtPolicyTran' );

				if ($ptCode < 1) {
					trigger_error( '' . 'no policy code for ' . $rtCode, E_USER_ERROR );
				}

				$pt = new PolicyTransaction( $ptCode );
				$this->set( 'mainGross', $pt->get( 'ptGross' ) );
				$this->set( 'mainCommission', $pt->get( 'ptCommission' ) );
				$this->set( 'mainRate', $pt->get( 'ptCommissionRate' ) );
				$this->set( 'mainIPT', $pt->get( 'ptGrossIPT' ) );
				$this->set( 'addlGross', $pt->get( 'ptAddlGross' ) );
				$this->set( 'addlCommission', $pt->get( 'ptAddlCommission' ) );
				$this->set( 'addlRate', $pt->get( 'ptAddlCommissionRate' ) );
				$this->set( 'addlIPT', $pt->get( 'ptAddlIPT' ) );
				$this->set( 'addOnGross', $pt->get( 'ptAddOnGross' ) );
				$this->set( 'addOnCommission', $pt->get( 'ptAddOnCommission' ) );
				$this->set( 'addOnRate', $pt->get( 'ptAddOnCommissionRate' ) );
				$this->set( 'addOnIPT', $pt->get( 'ptAddOnIPT' ) );
				$this->set( 'fees', $pt->get( 'ptEngineeringFee' ) );
				$this->set( 'feesVAT', $pt->get( 'ptEngineeringFeeVAT' ) );
				$this->set( 'feesRate', $pt->get( 'ptEngineeringFeeCommRate' ) );
				$this->set( 'feesCommission', $pt->get( 'ptEngineeringFeeComm' ) );
				$this->set( 'mainGrossFormatted', $pt->getAsMoneyWithCommas( 'ptGross' ) );
				$this->set( 'mainCommissionFormatted', $pt->getAsMoneyWithCommas( 'ptCommission' ) );
				$this->set( 'mainRateFormatted', $pt->getAsMoneyWithCommas( 'ptCommissionRate' ) );
				$this->set( 'mainIPTFormatted', $pt->getAsMoneyWithCommas( 'ptGrossIPT' ) );
				$this->set( 'addlGrossFormatted', $pt->getAsMoneyWithCommas( 'ptAddlGross' ) );
				$this->set( 'addlCommissionFormatted', $pt->getAsMoneyWithCommas( 'ptAddlCommission' ) );
				$this->set( 'addlRateFormatted', $pt->getAsMoneyWithCommas( 'ptAddlCommissionRate' ) );
				$this->set( 'addlIPTFormatted', $pt->getAsMoneyWithCommas( 'ptAddlIPT' ) );
				$this->set( 'addOnGrossFormatted', $pt->getAsMoneyWithCommas( 'ptAddOnGross' ) );
				$this->set( 'addOnCommissionFormatted', $pt->getAsMoneyWithCommas( 'ptAddOnCommission' ) );
				$this->set( 'addOnRateFormatted', $pt->getAsMoneyWithCommas( 'ptAddOnCommissionRate' ) );
				$this->set( 'addOnIPTFormatted', $pt->getAsMoneyWithCommas( 'ptAddOnIPT' ) );
				$this->set( 'feesFormatted', $pt->getAsMoneyWithCommas( 'ptEngineeringFee' ) );
				$this->set( 'feesVATFormatted', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeVAT' ) );
				$this->set( 'feesRateFormatted', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeCommRate' ) );
				$this->set( 'feesCommissionFormatted', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeComm' ) );
				$this->set( 'transDate', $rt->getForHTML( 'rtPostingDate' ) );
				$itemBalance = $rt->get( 'rtBalance' );
				$this->set( 'itemBalance', $itemBalance );
				$date = $rt->getForHTML( 'rtPostingDate' );
				$paidDate = $rt->getForHTML( 'rtPaidDate' );
				$direct = $rt->get( 'rtDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$grossEtc = $pt->get( 'ptGross' ) + $pt->get( 'ptGrossIPT' ) + $pt->get( 'ptAddlGross' ) + $pt->get( 'ptAddlIPT' ) + $pt->get( 'ptEngineeringFee' ) + $pt->get( 'ptEngineeringFeeVAT' );
				$polNo = $pt->get( 'ptPolicyNumber' );
				$effectiveDate = $pt->getForHTML( 'ptEffectiveFrom' );
				$this->set( 'ptCode', $ptCode );
				$covDesc = $pt->get( 'ptAddlCoverDesc' );
				$this->set( 'addlCoverDesc', $covDesc );
				$feeDesc = $pt->get( 'ptEngineeringFeeDesc' );
				$this->set( 'icFeeDesc', $feeDesc );
				$plCode = $pt->get( 'ptPolicy' );
				$policy = new Policy( $plCode );
				$policyHolder = $policy->get( 'plPolicyHolder' );
				$this->set( 'policyHolder', $policyHolder );
				$client = '';
				$clCode = $pt->get( 'ptClient' );

				if (0 < $clCode) {
					$cl = new Client( $clCode );
					$client = $cl->getDisplayName(  );
				}

				$type = $pt->get( 'ptTransType' );
				$tranType = new PolicyTransactionType( $type );
				$transType = $tranType->get( 'pyName' );
				$this->set( 'rtCode', $rt->getKeyValue(  ) );
				$this->set( 'transDate', $date );
				$this->set( 'effectiveDate', $effectiveDate );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'invNo', $rt->getForHTML( 'rtInvoiceNo' ) );
				$this->set( 'polNo', $polNo );
				$this->set( 'client', $client );
				$this->set( 'transType', $transType );
				$this->set( 'direct', $direct );
				$this->set( 'paidDate', $paidDate );
				$grossIncIPT = $pt->get( 'ptGrossIncIPT' ) + $pt->get( 'ptAddlGrossIncIPT' ) + $pt->get( 'ptAddOnGrossIncIPT' ) + $pt->get( 'ptEngineeringFee' ) + $pt->get( 'ptEngineeringFeeVAT' );
				$grossIncIPTFormatted = uformatmoneywithcommas( $grossIncIPT );
				$this->set( 'grossIncIPT', $grossIncIPTFormatted );
				$this->set( 'net', $pt->getAsMoneyWithCommas( 'ptNet' ) );
				$this->set( 'comm', $pt->getAsMoneyWithCommas( 'ptCommission' ) );
				$this->set( 'ipt', $pt->getAsMoneyWithCommas( 'ptGrossIPT' ) );
				$this->set( 'fees', $pt->getAsMoneyWithCommas( 'ptEngineeringFee' ) );
				$this->set( 'feescomm', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeComm' ) );
				$this->set( 'vat', $pt->getAsMoneyWithCommas( 'ptEngineeringFeeVAT' ) );
				$this->set( 'rate', $rt->getAsMoneyWithCommas( 'rtRate' ) );
				$this->set( 'orig', $rt->getAsMoneyWithCommas( 'rtOriginal' ) );
				$this->set( 'balance', $rt->getAsMoneyWithCommas( 'rtBalance' ) );
				$this->set( 'paid', $rt->getAsMoneyWithCommas( 'rtPaid' ) );
				$grossTotal += $grossIncIPT;
				$netTotal += $pt->get( 'ptGross' ) + $pt->get( 'ptAddlGross' ) + $pt->get( 'ptAddOnGross' ) + $pt->get( 'ptEngineeringFee' );
				$commTotal += $pt->get( 'ptCommission' ) + $pt->get( 'ptAddlCommission' ) + $pt->get( 'ptAddOnCommission' ) + $pt->get( 'ptEngineeringFeeComm' );
				$origTotal += $rt->get( 'rtOriginal' );
				$this->set( 'toPay', '' );
				$this->set( 'adj', '' );

				if (is_array( $this->toPay )) {
					if (isset( $this->toPay[$rtCode] )) {
						$this->set( 'toPay', uformatmoney( $this->toPay[$rtCode] ) );
					}
				}


				if (is_array( $this->adjustments )) {
					if (isset( $this->adjustments[$rtCode] )) {
						$this->set( 'adj', uformatmoney( $this->adjustments[$rtCode] ) );
						$adjTotal += $this->adjustments[$rtCode];
					}
				}


				if (isset( $this->transToView )) {
					if (isset( $this->readInAmount[$rtCode] )) {
						$toPay = uformatmoney( $this->readInAmount[$rtCode] );
					} 
else {
						$toPay = '';
					}


					if (isset( $this->readInWrittenOff[$rtCode] )) {
						$adj = uformatmoney( $this->readInWrittenOff[$rtCode] );
					} 
else {
						$adj = '';
					}

					$this->set( 'toPay', $toPay );
					$this->set( 'adj', $adj );
					$adjTotal += $adj;
				}

				$out .= $this->parse( $text );
			}

			$this->set( 'grossTotal', uformatmoneywithcommas( $grossTotal ) );
			$this->set( 'netTotal', uformatmoneywithcommas( $netTotal ) );
			$this->set( 'commTotal', uformatmoneywithcommas( $commTotal ) );
			$this->set( 'grossTotal', uformatmoneywithcommas( $grossTotal ) );
			$this->set( 'origTotal', uformatmoneywithcommas( $origTotal ) );
			$this->set( 'adjTotal', uformatmoneywithcommas( $adjTotal ) );
			return $out;
		}

		function whenmainpremium($text) {
			$do = false;

			if ($this->get( 'mainNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'mainIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaddlpremium($text) {
			$do = false;

			if ($this->get( 'addlNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addlIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenaddonpremium($text) {
			$do = false;

			if ($this->get( 'addOnNet' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnCommission' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'addOnIPT' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenfees($text) {
			$do = false;

			if ($this->get( 'fees' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesVAT' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesRate' ) != 0) {
				$do = true;
			}


			if ($this->get( 'feesCommission' ) != 0) {
				$do = true;
			}


			if ($do == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}
	}

?>