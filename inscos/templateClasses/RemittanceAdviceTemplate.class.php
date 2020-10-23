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

	class remittanceadvicetemplate {
		function remittanceadvicetemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function settransaction($it) {
			$this->type = 'IC';
			$this->transaction = &$it;

			$itCode = $it->get( 'itCode' );
			$this->clearDetailFields(  );
			$this->setAll( $it->getAllForHTML(  ) );
			$icCode = $it->get( 'itInsCo' );
			$insCo = new InsCo( $icCode );
			$this->insCo = &$insCo;

			$desc = '';
			$cpCode = $it->get( 'itPaymentType' );

			if (0 < $cpCode) {
				$cp = new CashPaymentMethod( $cpCode );
				$desc = $cp->getForHTML( 'cpName' );
			}

			$this->set( 'paymentTypeDesc', $desc );
			$this->set( 'icName', $insCo->get( 'icName' ) );
			$this->set( 'address', $insCo->getInvoiceNameAndAddress(  ) );
			$this->set( 'processDate', $it->getForHTML( 'itPostingDate' ) );
			$this->set( 'insCoRef', $it->getForHTML( 'itInsCoRef' ) );
			$this->set( 'postingRef', $it->getForHTML( 'itChequeNo' ) );
			$this->set( 'paymentType', $it->getForHTML( 'itPaymentType' ) );
			$this->set( 'totalToPay', uformatmoney( 0 - $it->get( 'itOriginal' ) ) );
			$this->set( 'code', sprintf( '%07d', $itCode ) );
			$this->set( 'doCode', $it->get( 'itDocm' ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setarraysoftransactions($trans, $toPay, $adjustments) {
			$this->trans = &$trans;
			$this->toPay = &$toPay;
			$this->adjustments = &$adjustments;

		}

		function getinsco() {
			return $this->insCo;
		}

		function listtransactions($text) {
			$grossTotal = 0;
			$iptVatTotal = 0;
			$premInsTotal = 0;
			$commTotal = 0;
			$origTotal = 0;
			$commAdjTotal = 0;
			$out = '';
			foreach ($this->trans as $key => $value) {
				$itCode = $key;

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
				$it = new InsCoTransaction( $itCode );
				$this->currentTrans = &$it;

				$this->set( 'mainGross', $it->get( 'itGross' ) );
				$this->set( 'mainCommission', $it->get( 'itCommission' ) );
				$this->set( 'mainRate', $it->get( 'itCommissionRate' ) );
				$this->set( 'mainIPT', $it->get( 'itGrossIPT' ) );
				$this->set( 'addlGross', $it->get( 'itAddlGross' ) );
				$this->set( 'addlCommission', $it->get( 'itAddlCommission' ) );
				$this->set( 'addlRate', $it->get( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPT', $it->get( 'itAddlIPT' ) );
				$this->set( 'fees', $it->get( 'itEngineeringFee' ) );
				$this->set( 'feesVAT', $it->get( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRate', $it->get( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommission', $it->get( 'itEngineeringFeeComm' ) );
				$this->set( 'mainGrossFormatted', $it->getAsMoneyWithCommas( 'itGross' ) );
				$this->set( 'mainCommissionFormatted', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'mainRateFormatted', $it->getAsMoneyWithCommas( 'itCommissionRate' ) );
				$this->set( 'mainIPTFormatted', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'addlGrossFormatted', $it->getAsMoneyWithCommas( 'itAddlGross' ) );
				$this->set( 'addlCommissionFormatted', $it->getAsMoneyWithCommas( 'itAddlCommission' ) );
				$this->set( 'addlRateFormatted', $it->getAsMoneyWithCommas( 'itAddlCommissionRate' ) );
				$this->set( 'addlIPTFormatted', $it->getAsMoneyWithCommas( 'itAddlIPT' ) );
				$this->set( 'feesFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feesVATFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'feesRateFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeCommRate' ) );
				$this->set( 'feesCommissionFormatted', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$this->set( 'transDate', $it->getForHTML( 'itPostingDate' ) );
				$totalCommDue = $it->getTotalCommission(  );
				$this->set( 'totalComm', $totalCommDue );
				$itemBalance = $it->get( 'itBalance' );
				$this->set( 'itemBalance', $itemBalance );
				$date = $it->getForHTML( 'itPostingDate' );
				$paidDate = $it->getForHTML( 'itPaidDate' );
				$direct = $it->get( 'itDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$grossEtc = $it->get( 'itGross' ) + $it->get( 'itGrossIPT' ) + $it->get( 'itAddlGross' ) + $it->get( 'itAddlIPT' ) + $it->get( 'itEngineeringFee' ) + $it->get( 'itEngineeringFeeVAT' );
				$grossTotal += $grossEtc;
				$iptVatTotal += $it->get( 'itGrossIPT' ) + $it->get( 'itAddlIPT' ) + $it->get( 'itEngineeringFeeVAT' );
				$premInsTotal += $it->get( 'itGross' ) + $it->get( 'itAddlGross' ) + $it->get( 'itEngineeringFee' );
				$commTotal += $it->get( 'itCommission' ) + $it->get( 'itAddlCommission' ) + $it->get( 'itEngineeringFeeComm' );
				$origTotal += $it->get( 'itOriginal' );
				$ptCode = $it->get( 'itPolicyTran' );
				$pt = new PolicyTransaction( $ptCode );
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
				$this->set( 'itCode', $it->getKeyValue(  ) );
				$this->set( 'transDate', $date );
				$this->set( 'effectiveDate', $effectiveDate );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'invNo', $it->getForHTML( 'itInvoiceNo' ) );
				$this->set( 'polNo', $polNo );
				$this->set( 'client', $client );
				$this->set( 'transType', $transType );
				$this->set( 'direct', $direct );
				$this->set( 'paidDate', $paidDate );
				$this->set( 'grossEtc', uformatmoneywithcommas( $grossEtc ) );
				$this->set( 'net', $it->getAsMoneyWithCommas( 'itNet' ) );
				$this->set( 'comm', $it->getAsMoneyWithCommas( 'itCommission' ) );
				$this->set( 'ipt', $it->getAsMoneyWithCommas( 'itGrossIPT' ) );
				$this->set( 'fees', $it->getAsMoneyWithCommas( 'itEngineeringFee' ) );
				$this->set( 'feescomm', $it->getAsMoneyWithCommas( 'itEngineeringFeeComm' ) );
				$this->set( 'vat', $it->getAsMoneyWithCommas( 'itEngineeringFeeVAT' ) );
				$this->set( 'orig', $it->getAsMoneyWithCommas( 'itOriginal' ) );
				$this->set( 'balance', $it->getAsMoneyWithCommas( 'itBalance' ) );
				$this->set( 'paid', $it->getAsMoneyWithCommas( 'itPaid' ) );
				$this->set( 'toPay', '' );
				$this->set( 'adj', '' );

				if (is_array( $this->toPay )) {
					if (isset( $this->toPay[$itCode] )) {
						$this->set( 'toPay', uformatmoney( $this->toPay[$itCode] ) );
					}
				}


				if (is_array( $this->adjustments )) {
					if (isset( $this->adjustments[$itCode] )) {
						$this->set( 'adj', uformatmoney( $this->adjustments[$itCode] ) );
						$commAdjTotal += $this->adjustments[$itCode];
					}
				}


				if (isset( $this->transToView )) {
					if (isset( $this->readInAmount[$itCode] )) {
						$toPay = uformatmoney( $this->readInAmount[$itCode] );
					} 
else {
						$toPay = '';
					}


					if (isset( $this->readInWrittenOff[$itCode] )) {
						$adj = uformatmoney( $this->readInWrittenOff[$itCode] );
					} 
else {
						$adj = '';
					}

					$this->set( 'toPay', $toPay );
					$this->set( 'adj', $adj );
					$commAdjTotal += $adj;
				}

				$out .= $this->parse( $text );
			}

			$this->set( 'grossTotal', uformatmoneywithcommas( $grossTotal ) );
			$this->set( 'iptVatTotal', uformatmoneywithcommas( $iptVatTotal ) );
			$this->set( 'premInsTotal', uformatmoneywithcommas( $premInsTotal ) );
			$this->set( 'commTotal', uformatmoneywithcommas( $commTotal ) );
			$this->set( 'origTotal', uformatmoneywithcommas( $origTotal ) );
			$this->set( 'commAdjTotal', uformatmoneywithcommas( $commAdjTotal ) );
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