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

	class policytransaction {
		var $table = null;
		var $keyField = null;

		function policytransaction($code) {
			$this->keyField = 'ptCode';
			$this->table = 'policyTransactions';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['ptTransType'] = 'INT';
			$this->fieldTypes['ptStatus'] = 'INT';
			$this->fieldTypes['ptStatusDate'] = 'DATE';
			$this->fieldTypes['ptPostingDate'] = 'DATE';
			$this->fieldTypes['ptEffectiveFrom'] = 'DATE';
			$this->fieldTypes['ptEffectiveTo'] = 'DATE';
			$this->fieldTypes['ptAccountingYear'] = 'INT';
			$this->fieldTypes['ptAccountingPeriod'] = 'INT';
			$this->fieldTypes['ptAltInsCo'] = 'INT';
			$this->fieldTypes['ptClassOfBus'] = 'INT';
			$this->fieldTypes['ptClient'] = 'INT';
			$this->fieldTypes['ptClientDisc'] = 'INT';
			$this->fieldTypes['ptDirect'] = 'INT';
			$this->fieldTypes['ptEnquiryDate'] = 'DATE';
			$this->fieldTypes['ptFrequency'] = 'INT';
			$this->fieldTypes['ptHandler'] = 'INT';
			$this->fieldTypes['ptInceptionDate'] = 'DATE';
			$this->fieldTypes['ptInsCo'] = 'INT';
			$this->fieldTypes['ptIntrodComm'] = 'INT';
			$this->fieldTypes['ptIntroducer'] = 'INT';
			$this->fieldTypes['ptNewBusiness'] = 'INT';
			$this->fieldTypes['ptPaymentDate'] = 'DATE';
			$this->fieldTypes['ptPaymentMethod'] = 'INT';
			$this->fieldTypes['ptPolDocsDue'] = 'INT';
			$this->fieldTypes['ptRenewalDate'] = 'DATE';
			$this->fieldTypes['ptSaleMethod'] = 'INT';
			$this->fieldTypes['ptSourceOfBus'] = 'INT';
			$this->fieldTypes['ptStatus'] = 'INT';
			$this->fieldTypes['ptStatusDate'] = 'DATE';
			$this->fieldTypes['ptTORDate'] = 'DATE';
			$this->fieldTypes['ptGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['ptGross'] = 'MONEY';
			$this->fieldTypes['ptCommissionRate'] = 'MONEY';
			$this->fieldTypes['ptCommission'] = 'MONEY';
			$this->fieldTypes['ptNet'] = 'MONEY';
			$this->fieldTypes['ptIPTRate'] = 'MONEY';
			$this->fieldTypes['ptGrossIPT'] = 'MONEY';
			$this->fieldTypes['ptAddlGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['ptAddlGross'] = 'MONEY';
			$this->fieldTypes['ptAddlCommissionRate'] = 'MONEY';
			$this->fieldTypes['ptAddlCommission'] = 'MONEY';
			$this->fieldTypes['ptAddlNet'] = 'MONEY';
			$this->fieldTypes['ptAddlIPT'] = 'MONEY';
			$this->fieldTypes['ptAddOnGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['ptAddOnGross'] = 'MONEY';
			$this->fieldTypes['ptAddOnCommissionRate'] = 'MONEY';
			$this->fieldTypes['ptAddOnCommission'] = 'MONEY';
			$this->fieldTypes['ptAddOnNet'] = 'MONEY';
			$this->fieldTypes['ptAddOnIPTRate'] = 'MONEY';
			$this->fieldTypes['ptAddOnIPT'] = 'MONEY';
			$this->fieldTypes['ptClientDiscountRate'] = 'MONEY';
			$this->fieldTypes['ptClientDiscount'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFee'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFeeCommRate'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFeeNet'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFeeVATRate'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFeeVAT'] = 'MONEY';
			$this->fieldTypes['ptEngineeringFeeComm'] = 'MONEY';
			$this->fieldTypes['ptBrokerFee'] = 'MONEY';
			$this->fieldTypes['ptBrokerFeeVATRate'] = 'MONEY';
			$this->fieldTypes['ptBrokerFeeVAT'] = 'MONEY';
			$this->fieldTypes['ptTotalGross'] = 'MONEY';
			$this->fieldTypes['ptTotalGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['ptTotalCommission'] = 'MONEY';
			$this->fieldTypes['ptTotalNet'] = 'MONEY';
			$this->fieldTypes['ptTotalIPT'] = 'MONEY';
			$this->fieldTypes['ptClientSubTotal'] = 'MONEY';
			$this->fieldTypes['ptClientTotal'] = 'MONEY';
			$this->fieldTypes['ptBrokerSubTotal'] = 'MONEY';
			$this->fieldTypes['ptBrokerTotalPlusFees'] = 'MONEY';
			$this->fieldTypes['ptInsCoTotal'] = 'MONEY';
			$this->fieldTypes['ptAddOnTotal'] = 'MONEY';
			$this->fieldTypes['ptIntroducerCommRate'] = 'MONEY';
			$this->fieldTypes['ptIntroducerComm'] = 'MONEY';
			$this->fieldTypes['ptIntroducerCommRate'] = 'MONEY';
			$this->fieldTypes['ptBrokerTotal'] = 'MONEY';
			$this->fieldTypes['ptInsCoTotal'] = 'MONEY';
			$this->fieldTypes['ptAddOnTotal'] = 'MONEY';
			$this->fieldTypes['ptOthers'] = 'MONEY';
			$this->fieldTypes['ptTotalInstalmentComm'] = 'MONEY';
			$this->fieldTypes['ptDirectBrokerTotal'] = 'MONEY';
			$this->fieldTypes['ptDirectClientTotal'] = 'MONEY';
			$this->fieldTypes['ptDirectClientGrand'] = 'MONEY';
			$this->_setUpdatedByField( 'ptLastUpdateBy' );
			$this->_setUpdatedWhenField( 'ptLastUpdateOn' );
			$this->handleConcurrency( true );
		}

		function populatefrompolicy($policy, $transType) {
			$this->set( 'ptTransType', $transType );
			$type = new PolicyTransactionType( $transType );
			$takeFromPolicy = $type->doWeTakeAllPolicyDetails(  );
			$this->set( 'ptPolicy', $policy->get( 'plCode' ) );
			$this->set( 'ptAltInsCo', $policy->get( 'plAltInsCo' ) );
			$this->set( 'ptClassOfBus', $policy->get( 'plClassOfBus' ) );
			$this->set( 'ptClient', $policy->get( 'plClient' ) );
			$this->set( 'ptClientDisc', $policy->get( 'plClientDisc' ) );
			$this->set( 'ptCoverDescription', $policy->get( 'plCoverDescription' ) );
			$this->set( 'ptDirect', $policy->get( 'plDirect' ) );
			$this->set( 'ptHandler', $policy->get( 'plHandler' ) );
			$this->set( 'ptFrequency', $policy->get( 'plFrequency' ) );
			$this->set( 'ptInceptionDate', $policy->get( 'plInceptionDate' ) );
			$this->set( 'ptInsCo', $policy->get( 'plInsCo' ) );
			$this->set( 'ptIntrodComm', $policy->get( 'plIntrodComm' ) );
			$this->set( 'ptNewBusiness', $policy->get( 'plNewBusiness' ) );
			$this->set( 'ptRenewalDate', $policy->get( 'plRenewalDate' ) );
			$this->set( 'ptTORDate', $policy->get( 'plTORDate' ) );
			$this->set( 'ptPolicyNumber', $policy->get( 'plPolicyNumber' ) );
			$this->set( 'ptPaymentMethod', $policy->get( 'plPaymentMethod' ) );
			$clCode = $policy->get( 'plClient' );

			if (0 < $clCode) {
				$client = new Client( $clCode );
				$inCode = $client->get( 'clIntroducer' );
				$this->set( 'ptIntroducer', $inCode );
			}

			$this->set( 'ptCommissionRate', $policy->get( 'plCommissionRate' ) );
			$this->set( 'ptIPTRate', $policy->get( 'plIPTRate' ) );
			$this->set( 'ptAddlCommissionRate', $policy->get( 'plAddlCommissionRate' ) );
			$this->set( 'ptAddOnCommissionRate', $policy->get( 'plAddOnCommissionRate' ) );
			$this->set( 'ptAddOnIPTRate', $policy->get( 'plAddOnIPTRate' ) );
			$this->set( 'ptClientDiscountRate', $policy->get( 'plClientDiscountRate' ) );
			$this->set( 'ptEngineeringFeeCommRate', $policy->get( 'plEngineeringFeeCommRate' ) );
			$this->set( 'ptEngineeringFeeDesc', $policy->get( 'plEngineeringFeeDesc' ) );
			$this->set( 'ptEngineeringFeeVATRate', $policy->get( 'plEngineeringFeeVATRate' ) );
			$this->set( 'ptBrokerFeeVATRate', $policy->get( 'plBrokerFeeVATRate' ) );
			$this->set( 'ptAddlCoverDesc', $policy->get( 'plAddlCoverDesc' ) );
			$this->set( 'ptAddOnCoverDescription', $policy->get( 'plAddOnCoverDescription' ) );
			$this->set( 'ptIntroducerCommRate', $policy->get( 'plIntroducerCommRate' ) );

			if ($takeFromPolicy == true) {
				$this->set( 'ptGrossIncIPT', $policy->get( 'plGrossIncIPT' ) );
				$this->set( 'ptCommission', $policy->get( 'plCommission' ) );
				$this->set( 'ptAddlGrossIncIPT', $policy->get( 'plAddlGrossIncIPT' ) );
				$this->set( 'ptAddlCommission', $policy->get( 'plAddlCommission' ) );
				$this->set( 'ptAddOnGrossIncIPT', $policy->get( 'plAddOnGrossIncIPT' ) );
				$this->set( 'ptAddOnCommission', $policy->get( 'plAddOnCommission' ) );
				$this->set( 'ptIntroducerComm', $policy->get( 'plIntroducerComm' ) );
				$this->set( 'ptClientDiscount', $policy->get( 'plClientDiscount' ) );
				$this->set( 'ptEngineeringFee', $policy->get( 'plEngineeringFee' ) );
				$this->set( 'ptEngineeringFeeDesc', $policy->get( 'plEngineeringFeeDesc' ) );
				$this->set( 'ptEngineeringFeeComm', $policy->get( 'plEngineeringFeeComm' ) );
				$this->set( 'ptBrokerFee', $policy->get( 'plBrokerFee' ) );
			}

			$today = date( 'Y-m-d' );
			$today = uformatsqldate2( $today );
			$this->set( 'ptPostingDate', $today );
			$plFrequency = $policy->get( 'plFrequency' );
			$plInceptionDate = $policy->get( 'plInceptionDate' );
			$plRenewalDate = $policy->get( 'plRenewalDate' );
			$plPrevRenewalDate = $policy->get( 'plPrevRenewalDate' );
			$plTORDate = $policy->get( 'plTORDate' );

			if ($transType == 1) {
				$effectiveFrom = $plRenewalDate;

				if (( ( $effectiveFrom != null && $effectiveFrom != '' ) && $effectiveFrom != '0000-00-00' )) {
					$effectiveTo = uaddmonthssqldate( $effectiveFrom, $plFrequency );
					$effectiveTo = uformatsqldate2( $effectiveTo );
					$this->set( 'ptEffectiveFrom', $effectiveFrom );
					$this->set( 'ptEffectiveTo', $effectiveTo );
				}

				$this->set( 'ptCoverDesc', $policy->get( 'plPrevCoverDesc' ) );
			}


			if (( $transType == 4 || $transType == 6 )) {
				$effectiveTo = uaddmonthssqldate( $plInceptionDate, $plFrequency );
				$effectiveTo = uformatsqldate2( $effectiveTo );
				$this->set( 'ptEffectiveFrom', $plInceptionDate );
				$this->set( 'ptEffectiveTo', $effectiveTo );
				$this->set( 'ptCoverDesc', $policy->get( 'plPrevCoverDesc' ) );
			}


			if (( $transType == 9 || $transType == 7 )) {
				if (( $plTORDate == null || $plTORDate == '0000-00-00' )) {
					$effectiveTo = $plTORDate;
				} 
else {
					$effectiveTo = $plRenewalDate;
				}

				$this->set( 'ptEffectiveTo', $effectiveTo );
			}


			if (( $transType == 2 || $transType == 3 )) {
				$this->set( 'ptEffectiveTo', $plRenewalDate );
			}


			if ($transType == 5) {
				$effectiveTo = $plTORDate;
				$this->set( 'ptEffectiveTo', $effectiveTo );
				$this->set( 'ptEffectiveFrom', $plInceptionDate );
			}


			if ($transType == 7) {
				if ($plFrequency == 0) {
					$effectiveTo = $plTORDate;
				} 
else {
					$effectiveTo = $plRenewalDate;
				}

				$this->set( 'ptEffectiveFrom', $plPrevRenewalDate );
				$this->set( 'ptEffectiveTo', $effectiveTo );
			}


			if ($transType == 8) {
				if ($plFrequency == 0) {
					$effectiveTo = $plTORDate;
				} 
else {
					$effectiveTo = $plRenewalDate;
				}

				$this->set( 'ptEffectiveFrom', $plInceptionDate );
				$this->set( 'ptEffectiveTo', $effectiveTo );
			}


			if ($transType == 9) {
				if ($plFrequency == 0) {
					$effectiveTo = $plTORDate;
				} 
else {
					$effectiveTo = $plRenewalDate;
				}

				$this->set( 'ptEffectiveTo', $effectiveTo );
			}

		}

		function populatefromreversaltransaction($pt) {
			$oldPTCode = $pt->getKeyValue(  );
			$oldTransDesc = $pt->get( 'ptTransDesc' );
			$newTransDesc = 'Reversal Of ' . $oldTransDesc;
			$oldType = $pt->get( 'ptTransType' );

			if ($oldType < 1) {
				trigger_error( '' . 'type on reversal is ' . $oldType, E_USER_ERROR );
			}

			$ptt = new PolicyTransactionType( $oldType );
			$newType = $ptt->get( 'pyReverseAs' );

			if ($newType < 1) {
				trigger_error( '' . 'type ' . $oldType . ' cant be reversed', E_USER_ERROR );
			}

			$debit = $ptt->get( 'pyDebit' );
			$type = new PolicyTransactionType( $newType );
			$this->setAll( $pt->getAll(  ) );
			$this->set( 'ptCode', null );
			$this->set( 'ptTransType', $newType );
			$this->set( 'ptDebit', $debit );
			$this->set( 'ptTransDesc', $newTransDesc );
			$this->set( 'ptInvoiceNo', 0 );
			$this->set( 'ptStatus', 0 );
			$this->set( 'ptBrokerRef', '' );
			$this->set( 'ptPaymentDate', '' );
			$this->set( 'ptDocm', 0 );
			$this->set( 'ptReceiptDocm', 0 );
			$this->set( 'ptClientTran', 0 );
			$this->set( 'ptMainInsCoTran', 0 );
			$this->set( 'ptAddOnInsCoTran', 0 );
			$this->set( 'ptIntroducerTran', 0 );
			$this->set( 'ptDocm', 0 );
			$this->set( 'ptCreatedBy', 0 );
			$this->set( 'ptCreatedOn', '' );
			$this->set( 'ptReversesTran', $oldPTCode );
			$today = date( 'Y-m-d' );
			$today = uformatsqldate2( $today );
			$this->set( 'ptPostingDate', $today );
		}

		function savetransaction() {
			global $postingAccountingYear;
			global $postingAccountingPeriod;

			$ok = udbcantabledotransactions( 'policyTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );

			if ($this->recordExists(  ) == false) {
				$this->insert( null );
			}

			$debit = 1;
			$pyCode = $this->get( 'ptTransType' );

			if (0 < $pyCode) {
				$type = new PolicyTransactionType( $pyCode );
				$debit = $type->get( 'pyDebit' );
			}

			$this->set( 'ptDebit', $debit );
			$ptCode = $this->getKeyValue(  );
			$this->set( 'ptInvoiceNo', 0 );
			$this->set( 'ptBrokerRef', 'NOT POSTED YET - DO NOT SEND' );
			$document = $this->_produceDocument( false );
			$doCode = $document->getKeyValue(  );
			$this->set( 'ptDocm', $doCode );
			$clAmt = $this->get( 'ptClientTotal' );
			$direct = $this->get( 'ptDirect' );
			$this->set( 'ptPostStatus', 'S' );
			$this->set( 'ptAccountingYear', $postingAccountingYear );
			$this->set( 'ptAccountingPeriod', $postingAccountingPeriod );
			$this->setCreatedByAndWhen(  );
			$this->update(  );
			udbcommittransaction(  );
		}

		function posttransaction() {
			global $accountingYear;
			global $accountingPeriod;
			global $postingAccountingYear;
			global $postingAccountingPeriod;

			$ok = udbcantabledotransactions( 'policyTransactions' );

			if ($ok == false) {
				trigger_error( 'cant do commit and rollback', E_USER_ERROR );
			}

			udbstarttransaction(  );
			$tnCode = fcreatesystemtran(  );
			$this->set( 'ptSysTran', $tnCode );

			if ($this->recordExists(  ) == false) {
				$this->insert( null );
			}

			$debit = 1;
			$pyCode = $this->get( 'ptTransType' );

			if (0 < $pyCode) {
				$type = new PolicyTransactionType( $pyCode );
				$debit = $type->get( 'pyDebit' );
			}

			$this->set( 'ptDebit', $debit );
			$ptCode = $this->getKeyValue(  );
			$ptInvoiceNo = fsetsequence( 'PTI', 1 );
			$this->set( 'ptInvoiceNo', $ptInvoiceNo );
			$brRef = $this->_makeBrokerRef(  );
			$this->set( 'ptBrokerRef', $brRef );
			$document = $this->_produceDocument( true );
			$doCode = $document->getKeyValue(  );
			$this->set( 'ptDocm', $doCode );
			$clAmt = $this->get( 'ptClientTotal' );
			$direct = $this->get( 'ptDirect' );
			$status = 1;

			if (( $direct == 1 && $clAmt == 0 )) {
				$status = 3;
			}

			$this->set( 'ptStatus', $status );
			$this->set( 'ptStatusDate', $this->get( 'ptPostingDate' ) );
			$this->set( 'ptPostStatus', 'P' );
			$this->set( 'ptAccountingYear', $postingAccountingYear );
			$this->set( 'ptAccountingPeriod', $postingAccountingPeriod );
			$this->setCreatedByAndWhen(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $ptCode );
			$st->set( 'tnType', 'PT' );
			$st->set( 'tnCreatedBy', $this->get( 'ptCreatedBy' ) );
			$st->set( 'tnCreatedOn', $this->get( 'ptCreatedOn' ) );
			$st->update(  );
			$this->_createOtherTransactions(  );
			$this->update(  );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'P' );
			$aa->set( 'aaTran', $this->getKeyValue(  ) );
			$aa->set( 'aaSysTran', $tnCode );
			$aa->set( 'aaPostingDate', $this->get( 'ptPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $this->get( 'ptEffectiveFrom' ) );
			$aa->set( 'aaAccountingYear', $postingAccountingYear );
			$aa->set( 'aaAccountingPeriod', $postingAccountingPeriod );
			$aa->set( 'aaCreatedBy', $this->get( 'ptCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $this->get( 'ptCreatedOn' ) );
			$aa->insert( null );

			if ($this->ptReversesTran == 0) {
				$this->_updatePolicyAndClientWithAmountsAndDates(  );
			}

			udbcommittransaction(  );
			$postingAccountingYear = $accountingYear;
			$postingAccountingPeriod = $accountingPeriod;
		}

		function createandviewreceiptdocument() {
			$docm = $this->_produceReceipt(  );
			$doCode = $docm->getKeyValue(  );
			$this->set( 'ptReceiptDocm', $doCode );
			return $docm;
		}

		function deletetransaction() {
			$this->set( 'ptPostStatus', 'D' );
			$this->update(  );
			$doCode = $this->get( 'ptDocm' );

			if (0 < $doCode) {
				$doc = new Document( $doCode );
				$doc->set( 'doDeleted', 1 );
				$doc->update(  );
			}

			$doCode = $this->get( 'ptReceiptDocm' );

			if (0 < $doCode) {
				$doc = new Document( $doCode );
				$doc->set( 'doDeleted', 1 );
				$doc->update(  );
			}

		}

		function recalculateaccountingfields() {
			$ptDirect = $this->get( 'ptDirect' );

			if ($ptDirect == 1) {
				$this->_recalculateAccountingFieldsDirect(  );
				return null;
			}

			$this->_recalculateAccountingFieldsNormal(  );
		}

		function gettypedescription() {
			$ptTransType = $this->get( 'ptTransType' );
			$typeName = '';

			if (0 < $ptTransType) {
				$type = new PolicyTransactionType( $ptTransType );
				$typeName = $type->get( 'pyName' );
			}

			return $typeName;
		}

		function getasterixarray() {
			return $this->asterixes;
		}

		function setcreatedbyandwhen() {
			global $user;

			$usCode = 0;

			if (is_object( $user )) {
				$usCode = $user->getKeyValue(  );
			}

			$this->set( 'ptCreatedBy', $usCode );
			$this->set( 'ptCreatedOn', ugettimenow(  ) );
		}

		function _recalculateaccountingfieldsdirect() {
			$ptGrossIncIPT = $this->get( 'ptGrossIncIPT' );
			$ptGrossIPT = $this->get( 'ptGrossIPT' );
			$ptIPTRate = $this->get( 'ptIPTRate' );
			$ptAddOnIPTRate = $this->get( 'ptAddOnIPTRate' );
			$ptEngineeringFeeVATRate = $this->get( 'ptEngineeringFeeVATRate' );
			$ptEngineeringFeeCommRate = $this->get( 'ptEngineeringFeeCommRate' );
			$ptEngineeringFeeComm = $this->get( 'ptEngineeringFeeComm' );
			$ptEngineeringFee = $this->get( 'ptEngineeringFee' );
			$ptBrokerFeeVATRate = $this->get( 'ptBrokerFeeVATRate' );
			$ptCommissionRate = $this->get( 'ptCommissionRate' );
			$ptCommission = $this->get( 'ptCommission' );
			$ptTotalInstalmentComm = $this->get( 'ptTotalInstalmentComm' );
			$ptAddlIPT = $this->get( 'ptAddlIPT' );
			$ptAddlGrossIncIPT = $this->get( 'ptAddlGrossIncIPT' );
			$ptAddlCommissionRate = $this->get( 'ptAddlCommissionRate' );
			$ptAddlCommission = $this->get( 'ptAddlCommission' );
			$ptAddOnCommissionRate = $this->get( 'ptAddOnCommissionRate' );
			$ptAddOnCommission = $this->get( 'ptAddOnCommission' );
			$ptClientDiscountRate = $this->get( 'ptClientDiscountRate' );
			$ptIntroducerCommRate = $this->get( 'ptIntroducerCommRate' );
			$ptBrokerTotalPlusFees = $this->get( 'ptBrokerTotalPlusFees' );
			$plCode = $this->get( 'ptPolicy' );
			$policy = new Policy( $plCode );
			$ok = $policy->isIPTAmendable(  );
			$iptAmendable = $ok;

			if (( $iptAmendable == true && $ptIPTRate == 0 )) {
				$ptGross = $ptGrossIncIPT - $ptGrossIPT;
				$this->ptGross = $ptGross;
			} 
else {
				$ptGross = ucalcinclusiveusingrate( $ptGrossIncIPT, $ptIPTRate );
				$this->ptGross = $ptGross;
				$ptGrossIPT = $ptGrossIncIPT - $ptGross;
			}

			$this->ptGrossIPT = $ptGrossIPT;
			$this->ptGross = $ptGross;

			if (0 < $ptCommissionRate) {
				$ptCommission = ucalcusingrate( $ptGross, $ptCommissionRate );
				$this->ptCommission = $ptCommission;
			}

			$ptNet = $ptGross - $ptCommission;
			$this->ptNet = $ptNet;

			if (( $iptAmendable == true && $ptIPTRate == 0 )) {
				$ptAddlGross = $ptAddlGrossIncIPT - $ptAddlIPT;
				$this->ptAddlGross = $ptAddlGross;
			} 
else {
				$ptAddlGross = ucalcinclusiveusingrate( $ptAddlGrossIncIPT, $ptIPTRate );
				$this->ptAddlGross = $ptAddlGross;
				$ptAddlIPT = $ptAddlGrossIncIPT - $ptAddlGross;
				$this->ptAddlIPT = $ptAddlIPT;
			}


			if (0 < $ptAddlCommissionRate) {
				$ptAddlCommission = ucalcusingrate( $ptAddlGross, $ptAddlCommissionRate );
				$this->ptAddlCommission = $ptAddlCommission;
			}

			$ptAddlNet = $ptAddlGross - $ptAddlCommission;
			$this->ptAddlNet = $ptAddlNet;
			$ptTotalGross = $ptGross + $ptAddlGross;
			$this->ptTotalGross = $ptTotalGross;
			$ptTotalGrossIncIPT = $ptGrossIncIPT + $ptAddlGrossIncIPT;
			$this->ptTotalGrossIncIPT = $ptTotalGrossIncIPT;
			$ptTotalIPT = $ptGrossIPT + $ptAddlIPT;
			$this->ptTotalIPT = $ptTotalIPT;
			$ptTotalCommission = $ptCommission + $ptAddlCommission;
			$this->ptTotalCommission = $ptTotalCommission;
			$ptTotalNet = $ptNet + $ptAddlNet;
			$this->ptTotalNet = $ptTotalNet;
			$ptAddOnGrossIncIPT = $this->get( 'ptAddOnGrossIncIPT' );
			$ptAddOnGross = ucalcinclusiveusingrate( $ptAddOnGrossIncIPT, $ptAddOnIPTRate );
			$this->ptAddOnGross = $ptAddOnGross;
			$ptAddOnIPT = $ptAddOnGrossIncIPT - $ptAddOnGross;
			$this->ptAddOnIPT = $ptAddOnIPT;

			if (0 < $ptAddOnCommissionRate) {
				$ptAddOnCommissionRate = $this->get( 'ptAddOnCommissionRate' );
				$ptAddOnCommission = ucalcusingrate( $ptAddOnGross, $ptAddOnCommissionRate );
				$this->ptAddOnCommission = $ptAddOnCommission;
			}

			$ptAddOnNet = $ptAddOnGross - $ptAddOnCommission;
			$this->ptAddOnNet = $ptAddOnNet;
			$ptAddnPremium = $ptAddOnGross + $ptAddOnIPT;
			$this->ptAddnPremium = $ptAddnPremium;
			$ptEngineeringFee = $this->get( 'ptEngineeringFee' );
			$ptEngineeringFeeCommRate = $this->get( 'ptEngineeringFeeCommRate' );

			if (0 < $ptEngineeringFeeCommRate) {
				$ptEngineeringFeeComm = ucalcusingrate( $ptEngineeringFee, $ptEngineeringFeeCommRate );
				$this->ptEngineeringFeeComm = $ptEngineeringFeeComm;
			}

			$ptEngineeringFeeNet = $ptEngineeringFee - $ptEngineeringFeeComm;
			$this->ptEngineeringFeeNet = $ptEngineeringFeeNet;
			$ptEngineeringFeeVATRate = $this->get( 'ptEngineeringFeeVATRate' );
			$this->ptEngineeringFeeVATRate = $ptEngineeringFeeVATRate;
			$ptEngineeringFeeVAT = ucalcusingrate( $ptEngineeringFee, $ptEngineeringFeeVATRate );
			$this->ptEngineeringFeeVAT = $ptEngineeringFeeVAT;
			$ptClientDiscountRate = $this->get( 'ptClientDiscountRate' );
			$ptClientDiscount = $this->get( 'ptClientDiscount' );

			if (0 < $ptClientDiscountRate) {
				$ptClientDiscount = ucalcusingrate( $ptCommission + $ptAddlCommission + $ptAddOnCommission + $ptEngineeringFeeComm, $ptClientDiscountRate );
				$this->ptClientDiscount = $ptClientDiscount;
			}

			$ptIntroducerCommRate = $this->get( 'ptIntroducerCommRate' );
			$ptIntroducerComm = $this->get( 'ptIntroducerComm' );

			if (0 < $ptIntroducerCommRate) {
				$ptIntroducerComm = ucalcusingrate( $ptGross, $ptIntroducerCommRate );
				$this->ptIntroducerComm = $ptIntroducerComm;
			}

			$ptBrokerFee = $this->get( 'ptBrokerFee' );
			$ptBrokerFeeVATRate = $this->get( 'ptBrokerFeeVATRate' );
			$ptBrokerFeeVAT = ucalcusingrate( $ptBrokerFee, $ptBrokerFeeVATRate );
			$this->ptBrokerFeeVAT = $ptBrokerFeeVAT;
			$ptClientSubTotal = $ptAddOnGross + $ptAddOnIPT + $ptBrokerFee + $ptBrokerFeeVAT - $ptClientDiscount;
			$this->ptClientSubTotal = $ptClientSubTotal;
			$ptTotalPremium = $ptClientSubTotal;
			$this->ptTotalPremium = $ptTotalPremium;
			$this->ptClientTotal = $ptClientSubTotal;
			$ptBrokerSubTotal = $ptAddOnCommission + $ptBrokerFee + $ptBrokerFeeVAT - $ptClientDiscount;
			$this->ptBrokerSubTotal = $ptBrokerSubTotal;
			$ptBrokerTotal = $ptBrokerSubTotal - $ptIntroducerComm - $ptBrokerFeeVAT;
			$this->ptBrokerTotal = $ptBrokerTotal;
			$ptBrokerTotalPlusFees = $ptBrokerTotal;
			$this->ptBrokerTotalPlusFees = $ptBrokerTotalPlusFees;
			$ptInsCoTotal = 0;
			$this->ptInsCoTotal = $ptInsCoTotal;
			$ptTotalInstalmentComm = $ptTotalCommission + $ptEngineeringFeeComm;
			$this->ptTotalInstalmentComm = $ptTotalInstalmentComm;
			$ptAddOnTotal = $ptAddOnNet + $ptAddOnIPT;
			$this->ptAddOnTotal = $ptAddOnTotal;
			$ptOthers = 0 - $ptClientDiscount + $ptBrokerFee + $ptBrokerFeeVAT;
			$this->ptOthers = $ptOthers;
			$ptDirectBrokerTotal = $ptTotalInstalmentComm + $ptBrokerTotal;
			$this->ptDirectBrokerTotal = $ptDirectBrokerTotal;
			$ptDirectClientTotal = $ptTotalGross + $ptTotalIPT + $ptEngineeringFee + $ptEngineeringFeeVAT;
			$this->ptDirectClientTotal = $ptDirectClientTotal;
			$ptDirectClientGrand = $ptClientSubTotal + $ptDirectClientTotal;
			$this->ptDirectClientGrand = $ptDirectClientGrand;
		}

		function _recalculateaccountingfieldsnormal() {
			$ptGrossIncIPT = $this->get( 'ptGrossIncIPT' );
			$ptGrossIPT = $this->get( 'ptGrossIPT' );
			$ptIPTRate = $this->get( 'ptIPTRate' );
			$ptAddOnIPTRate = $this->get( 'ptAddOnIPTRate' );
			$ptEngineeringFeeVATRate = $this->get( 'ptEngineeringFeeVATRate' );
			$ptEngineeringFeeCommRate = $this->get( 'ptEngineeringFeeCommRate' );
			$ptEngineeringFeeComm = $this->get( 'ptEngineeringFeeComm' );
			$ptBrokerFeeVATRate = $this->get( 'ptBrokerFeeVATRate' );
			$ptCommissionRate = $this->get( 'ptCommissionRate' );
			$ptCommission = $this->get( 'ptCommission' );
			$ptAddlGrossIncIPT = $this->get( 'ptAddlGrossIncIPT' );
			$ptAddlIPT = $this->get( 'ptAddlIPT' );
			$ptAddlCommissionRate = $this->get( 'ptAddlCommissionRate' );
			$ptAddlCommission = $this->get( 'ptAddlCommission' );
			$ptAddOnCommissionRate = $this->get( 'ptAddOnCommissionRate' );
			$ptAddOnCommission = $this->get( 'ptAddOnCommission' );
			$ptClientDiscountRate = $this->get( 'ptClientDiscountRate' );
			$ptIntroducerCommRate = $this->get( 'ptIntroducerCommRate' );
			$ok = false;
			$x = $this->get( 'ptClassOfBus' );

			if (0 < $x) {
				$cob = new Cob( $x );
				$cbAllowIPTAmend = $cob->get( 'cbAllowIPTAmend' );

				if ($cbAllowIPTAmend == 1) {
					$ok = true;
				}
			}

			$iptAmendable = $ok;

			if (( $iptAmendable == true && $ptIPTRate == 0 )) {
				$ptGross = $ptGrossIncIPT - $ptGrossIPT;
				$this->ptGross = $ptGross;
			} 
else {
				$ptGross = ucalcinclusiveusingrate( $ptGrossIncIPT, $ptIPTRate );
				$this->ptGross = $ptGross;
				$ptGrossIPT = $ptGrossIncIPT - $ptGross;
			}

			$this->ptGrossIPT = $ptGrossIPT;

			if (0 < $ptCommissionRate) {
				$ptCommission = ucalcusingrate( $ptGross, $ptCommissionRate );
				$this->ptCommission = $ptCommission;
			}

			$ptNet = $ptGross - $ptCommission;
			$this->ptNet = $ptNet;

			if (( $iptAmendable == true && $ptIPTRate == 0 )) {
				$ptAddlGross = $ptAddlGrossIncIPT - $ptAddlIPT;
				$this->ptAddlGross = $ptAddlGross;
			} 
else {
				$ptAddlGross = ucalcinclusiveusingrate( $ptAddlGrossIncIPT, $ptIPTRate );
				$this->ptAddlGross = $ptAddlGross;
				$ptAddlIPT = $ptAddlGrossIncIPT - $ptAddlGross;
				$this->ptAddlIPT = $ptAddlIPT;
			}


			if (0 < $ptAddlCommissionRate) {
				$ptAddlCommission = ucalcusingrate( $ptAddlGross, $ptAddlCommissionRate );
				$this->ptAddlCommission = $ptAddlCommission;
			}

			$ptAddlNet = $ptAddlGross - $ptAddlCommission;
			$this->ptAddlNet = $ptAddlNet;
			$ptTotalGross = $ptGross + $ptAddlGross;
			$this->ptTotalGross = $ptTotalGross;
			$ptTotalGrossIncIPT = $ptGrossIncIPT + $ptAddlGrossIncIPT;
			$this->ptTotalGrossIncIPT = $ptTotalGrossIncIPT;
			$ptTotalIPT = $ptGrossIPT + $ptAddlIPT;
			$this->ptTotalIPT = $ptTotalIPT;
			$ptTotalCommission = $ptCommission + $ptAddlCommission;
			$this->ptTotalCommission = $ptTotalCommission;
			$ptTotalNet = $ptNet + $ptAddlNet;
			$this->ptTotalNet = $ptTotalNet;
			$ptAddOnGrossIncIPT = $this->get( 'ptAddOnGrossIncIPT' );
			$ptAddOnGross = ucalcinclusiveusingrate( $ptAddOnGrossIncIPT, $ptAddOnIPTRate );
			$this->ptAddOnGross = $ptAddOnGross;
			$ptAddOnIPT = $ptAddOnGrossIncIPT - $ptAddOnGross;
			$this->ptAddOnIPT = $ptAddOnIPT;

			if (0 < $ptAddOnCommissionRate) {
				$ptAddOnCommissionRate = $this->get( 'ptAddOnCommissionRate' );
				$ptAddOnCommission = ucalcusingrate( $ptAddOnGross, $ptAddOnCommissionRate );
				$this->ptAddOnCommission = $ptAddOnCommission;
			}

			$ptAddOnNet = $ptAddOnGross - $ptAddOnCommission;
			$this->ptAddOnNet = $ptAddOnNet;
			$ptAddnPremium = $ptAddOnGross + $ptAddOnIPT;
			$this->ptAddnPremium = $ptAddnPremium;
			$ptEngineeringFee = $this->get( 'ptEngineeringFee' );
			$ptEngineeringFeeCommRate = $this->get( 'ptEngineeringFeeCommRate' );

			if (0 < $ptEngineeringFeeCommRate) {
				$ptEngineeringFeeComm = ucalcusingrate( $ptEngineeringFee, $ptEngineeringFeeCommRate );
				$this->ptEngineeringFeeComm = $ptEngineeringFeeComm;
			}

			$ptEngineeringFeeNet = $ptEngineeringFee - $ptEngineeringFeeComm;
			$this->ptEngineeringFeeNet = $ptEngineeringFeeNet;
			$ptEngineeringFeeVATRate = $this->get( 'ptEngineeringFeeVATRate' );
			$this->ptEngineeringFeeVATRate = $ptEngineeringFeeVATRate;
			$ptEngineeringFeeVAT = ucalcusingrate( $ptEngineeringFee, $ptEngineeringFeeVATRate );
			$this->ptEngineeringFeeVAT = $ptEngineeringFeeVAT;
			$ptClientDiscountRate = $this->get( 'ptClientDiscountRate' );
			$ptClientDiscount = $this->get( 'ptClientDiscount' );

			if (0 < $ptClientDiscountRate) {
				$ptClientDiscount = ucalcusingrate( $ptCommission + $ptAddOnCommission + $ptAddlCommission + $ptEngineeringFeeComm, $ptClientDiscountRate );
				$this->ptClientDiscount = $ptClientDiscount;
			}

			$ptIntroducerCommRate = $this->get( 'ptIntroducerCommRate' );
			$ptIntroducerComm = $this->get( 'ptIntroducerComm' );

			if (0 < $ptIntroducerCommRate) {
				$ptIntroducerComm = ucalcusingrate( $ptGross, $ptIntroducerCommRate );
				$this->ptIntroducerComm = $ptIntroducerComm;
			}

			$ptBrokerFee = $this->get( 'ptBrokerFee' );
			$ptBrokerFeeVATRate = $this->get( 'ptBrokerFeeVATRate' );
			$ptBrokerFeeVAT = ucalcusingrate( $ptBrokerFee, $ptBrokerFeeVATRate );
			$this->ptBrokerFeeVAT = $ptBrokerFeeVAT;
			$ptClientTotal = $ptGross + $ptGrossIPT + $ptAddlGross + $ptAddlIPT + $ptAddOnGross + $ptAddOnIPT + $ptEngineeringFee + $ptEngineeringFeeVAT + $ptBrokerFee + $ptBrokerFeeVAT - $ptClientDiscount;
			$this->ptClientTotal = $ptClientTotal;
			$this->ptClientSubTotal = $ptClientTotal;
			$ptTotalPremium = $ptClientTotal;
			$this->ptTotalPremium = $ptTotalPremium;
			$ptBrokerSubTotal = $ptCommission + $ptAddOnCommission + $ptAddlCommission + $ptEngineeringFeeComm + $ptBrokerFee + $ptBrokerFeeVAT - $ptClientDiscount;
			$this->ptBrokerSubTotal = $ptBrokerSubTotal;
			$ptBrokerTotal = $ptBrokerSubTotal - $ptIntroducerComm - $ptBrokerFeeVAT;
			$this->ptBrokerTotal = $ptBrokerTotal;
			$ptInsCoTotal = $ptNet + $ptGrossIPT + $ptAddlNet + $ptAddlIPT + $ptEngineeringFeeNet + $ptEngineeringFeeVAT;
			$this->ptInsCoTotal = $ptInsCoTotal;
			$ptAddOnTotal = $ptAddOnNet + $ptAddOnIPT;
			$this->ptAddOnTotal = $ptAddOnTotal;
			$ptOthers = $ptEngineeringFee + $ptEngineeringFeeVAT - $ptClientDiscount + $ptBrokerFee + $ptBrokerFeeVAT;
			$this->ptOthers = $ptOthers;
		}

		function _createothertransactions() {
			global $postingAccountingYear;
			global $postingAccountingPeriod;

			$ptCode = $this->get( 'ptCode' );
			$ptSysTran = $this->get( 'ptSysTran' );
			$ptClientTotal = $this->get( 'ptClientTotal' );
			$ptClientSubTotal = $this->get( 'ptClientSubTotal' );
			$ptInvoiceNo = $this->get( 'ptInvoiceNo' );
			$ptBrokerRef = $this->get( 'ptBrokerRef' );
			$ptInsCoRef = $this->get( 'ptInsCoRef' );
			$ptTransDesc = $this->get( 'ptTransDesc' );
			$ptInvoiceNo = $this->get( 'ptInvoiceNo' );
			$ptClient = $this->get( 'ptClient' );
			$ptPolicy = $this->get( 'ptPolicy' );
			$ptTransType = $this->get( 'ptTransType' );
			$ptDebit = $this->get( 'ptDebit' );
			$ptClient = $this->get( 'ptClient' );
			$ptEffectiveFrom = $this->get( 'ptEffectiveFrom' );
			$ptPostingDate = $this->get( 'ptPostingDate' );
			$ptDocm = $this->get( 'ptDocm' );
			$ptCoverDescription = $this->get( 'ptCoverDescription' );
			$ptAddlCoverDesc = $this->get( 'ptAddlCoverDesc' );
			$ptAddOnCoverDescription = $this->get( 'ptAddOnCoverDescription' );

			if ($ptDebit == 1) {
				$mult = 1;
			} 
else {
				$mult = 0 - 1;
			}

			$ptDirect = $this->get( 'ptDirect' );
			$ptInsCo = $this->get( 'ptInsCo' );
			$ptAltInsCo = $this->get( 'ptAltInsCo' );
			$ptGross = $this->get( 'ptGross' );
			$ptCommissionRate = $this->get( 'ptCommissionRate' );
			$ptCommission = $this->get( 'ptCommission' );
			$ptNet = $this->get( 'ptNet' );
			$ptGrossIPT = $this->get( 'ptGrossIPT' );
			$ptAddlGross = $this->get( 'ptAddlGross' );
			$ptAddlCommissionRate = $this->get( 'ptAddlCommissionRate' );
			$ptAddlCommission = $this->get( 'ptAddlCommission' );
			$ptAddlNet = $this->get( 'ptAddlNet' );
			$ptAddlIPT = $this->get( 'ptAddlIPT' );
			$ptAddOnGross = $this->get( 'ptAddOnGross' );
			$ptAddOnCommissionRate = $this->get( 'ptAddOnCommissionRate' );
			$ptAddOnCommission = $this->get( 'ptAddOnCommission' );
			$ptAddOnNet = $this->get( 'ptAddOnNet' );
			$ptAddOnIPT = $this->get( 'ptAddOnIPT' );
			$ptClientDiscount = $this->get( 'ptClientDiscount' );
			$ptIntroducer = $this->get( 'ptIntroducer' );
			$ptIntroducerCommRate = $this->get( 'ptIntroducerCommRate' );
			$ptIntroducerComm = $this->get( 'ptIntroducerComm' );
			$ptEngineeringFeeDesc = $this->get( 'ptEngineeringFeeDesc' );
			$ptEngineeringFee = $this->get( 'ptEngineeringFee' );
			$ptEngineeringFeeCommRate = $this->get( 'ptEngineeringFeeCommRate' );
			$ptEngineeringFeeComm = $this->get( 'ptEngineeringFeeComm' );
			$ptEngineeringFeeNet = $this->get( 'ptEngineeringFeeNet' );
			$ptEngineeringFeeVATRate = $this->get( 'ptEngineeringFeeVATRate' );
			$ptEngineeringFeeVAT = $this->get( 'ptEngineeringFeeVAT' );
			$clTrans = new ClientTransaction( null );
			$clTrans->set( 'ctSysTran', $ptSysTran );
			$clTrans->set( 'ctClient', $ptClient );
			$clTrans->set( 'ctPolicy', $ptPolicy );
			$clTrans->set( 'ctTransType', 'I' );
			$clTrans->set( 'ctDirect', $ptDirect );
			$clTrans->set( 'ctPolicyTransType', $ptTransType );
			$clTrans->set( 'ctInvoiceNo', $ptInvoiceNo );
			$clTrans->set( 'ctBrokerRef', $ptBrokerRef );
			$clTrans->set( 'ctInsCoRef', $ptInsCoRef );
			$clTrans->set( 'ctPolicyTran', $ptCode );
			$clTrans->set( 'ctTransDesc', $ptTransDesc );
			$clTrans->set( 'ctPostingDate', $ptPostingDate );
			$clTrans->set( 'ctEffectiveDate', $ptEffectiveFrom );
			$clTrans->set( 'ctDocm', $ptDocm );
			$clTrans->set( 'ctAccountingYear', $postingAccountingYear );
			$clTrans->set( 'ctAccountingPeriod', $postingAccountingPeriod );
			$clTrans->setCreatedByAndWhen(  );

			if ($ptDirect == 1) {
				$clTrans->set( 'ctDirectPaidDate', $ptEffectiveFrom );
			}


			if ($ptDebit == 1) {
				$clTrans->set( 'ctOriginal', $ptClientTotal );
				$clTrans->set( 'ctBalance', $ptClientTotal );
			} 
else {
				$clTrans->set( 'ctOriginal', 0 - $ptClientTotal );
				$clTrans->set( 'ctBalance', 0 - $ptClientTotal );
			}

			$clTrans->set( 'ctWrittenOff', 0 );
			$clTrans->recalcTotals(  );
			$clTrans->insert( null );
			$ctCode = $clTrans->getKeyValue(  );
			$this->set( 'ptClientTran', $ctCode );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'C' );
			$aa->set( 'aaTran', $clTrans->getKeyValue(  ) );
			$aa->set( 'aaSysTran', $ptSysTran );
			$aa->set( 'aaPostingDate', $clTrans->get( 'ctPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $clTrans->get( 'ctEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $postingAccountingYear );
			$aa->set( 'aaAccountingPeriod', $postingAccountingPeriod );
			$aa->set( 'aaCreatedBy', $clTrans->get( 'ctCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $clTrans->get( 'ctCreatedOn' ) );
			$aa->insert( null );
			$insTrans = new InsCoTransaction( null );
			$insTrans->set( 'itSysTran', $ptSysTran );
			$insTrans->set( 'itInsCo', $ptInsCo );
			$insTrans->set( 'itTransType', 'I' );
			$insTrans->set( 'itPolicyTran', $ptCode );
			$insTrans->set( 'itTransDesc', $ptTransDesc );
			$insTrans->set( 'itPostingDate', $ptPostingDate );
			$insTrans->set( 'itEffectiveDate', $ptEffectiveFrom );
			$insTrans->set( 'itInsCoRef', $ptInsCoRef );
			$insTrans->set( 'itCoverDesc', $ptCoverDescription );
			$insTrans->set( 'itAddlCoverDesc', $ptAddlCoverDesc );
			$insTrans->set( 'itDebit', $ptDebit );
			$insTrans->set( 'itDirect', $ptDirect );
			$insTrans->set( 'itGross', $mult * $ptGross );
			$insTrans->set( 'itCommissionRate', $ptCommissionRate );
			$insTrans->set( 'itCommission', $mult * $ptCommission );
			$insTrans->set( 'itNet', $mult * $ptNet );
			$insTrans->set( 'itGrossIPT', $mult * $ptGrossIPT );
			$insTrans->set( 'itAddlGross', $mult * $ptAddlGross );
			$insTrans->set( 'itAddlCommissionRate', $ptAddlCommissionRate );
			$insTrans->set( 'itAddlCommission', $mult * $ptAddlCommission );
			$insTrans->set( 'itAddlNet', $mult * $ptAddlNet );
			$insTrans->set( 'itAddlIPT', $mult * $ptAddlIPT );
			$insTrans->set( 'itEngineeringFeeDesc', $ptEngineeringFeeDesc );
			$insTrans->set( 'itEngineeringFee', $mult * $ptEngineeringFee );
			$insTrans->set( 'itEngineeringFeeCommRate', $ptEngineeringFeeCommRate );
			$insTrans->set( 'itEngineeringFeeComm', $mult * $ptEngineeringFeeComm );
			$insTrans->set( 'itEngineeringFeeNet', $mult * $ptEngineeringFeeNet );
			$insTrans->set( 'itEngineeringFeeVATRate', $ptEngineeringFeeVATRate );
			$insTrans->set( 'itEngineeringFeeVAT', $mult * $ptEngineeringFeeVAT );
			$insTrans->set( 'itAccountingYear', $postingAccountingYear );
			$insTrans->set( 'itAccountingPeriod', $postingAccountingPeriod );

			if ($ptDebit == 1) {
				if ($ptDirect == 1) {
					$bal = 0 - $ptEngineeringFeeComm - $ptCommission - $ptAddlCommission;
				} 
else {
					$bal = $ptNet + $ptGrossIPT + $ptAddlNet + $ptAddlIPT + $ptEngineeringFeeNet + $ptEngineeringFeeVAT;
				}
			} 
else {
				if ($ptDirect == 1) {
					$bal = 0 - ( 0 - $ptEngineeringFeeComm - $ptCommission - $ptAddlCommission );
				} 
else {
					$bal = 0 - ( $ptNet + $ptGrossIPT + $ptAddlNet + $ptAddlIPT + $ptEngineeringFeeNet + $ptEngineeringFeeVAT );
				}
			}

			$insTrans->set( 'itOriginal', $bal );
			$insTrans->set( 'itBalance', $bal );
			$insTrans->set( 'itAccountingYear', $postingAccountingYear );
			$insTrans->set( 'itAccountingPeriod', $postingAccountingPeriod );
			$insTrans->setCreatedByAndWhen(  );
			$insTrans->insert( null );
			$itCode = $insTrans->getKeyValue(  );
			$this->set( 'ptMainInsCoTran', $itCode );
			$aa = new AccountingAudit( null );
			$aa->set( 'aaType', 'I' );
			$aa->set( 'aaSysTran', $ptSysTran );
			$aa->set( 'aaTran', $insTrans->getKeyValue(  ) );
			$aa->set( 'aaPostingDate', $insTrans->get( 'itPostingDate' ) );
			$aa->set( 'aaEffectiveDate', $insTrans->get( 'itEffectiveDate' ) );
			$aa->set( 'aaAccountingYear', $postingAccountingYear );
			$aa->set( 'aaAccountingPeriod', $postingAccountingPeriod );
			$aa->set( 'aaCreatedBy', $insTrans->get( 'itCreatedBy' ) );
			$aa->set( 'aaCreatedOn', $insTrans->get( 'itCreatedOn' ) );
			$aa->insert( null );

			if (( 0 < $ptAddOnGross || 0 < $ptAddOnCommission )) {
				$insTrans = new InsCoTransaction( null );
				$insTrans->set( 'itSysTran', $ptSysTran );
				$insTrans->set( 'itInsCo', $ptAltInsCo );
				$insTrans->set( 'itTransType', 'I' );
				$insTrans->set( 'itPolicyTran', $ptCode );
				$insTrans->set( 'itTransDesc', $ptTransDesc );
				$insTrans->set( 'itPostingDate', $ptPostingDate );
				$insTrans->set( 'itEffectiveDate', $ptEffectiveFrom );
				$insTrans->set( 'itInsCoRef', $ptInsCoRef );
				$insTrans->set( 'itCoverDesc', $ptAddOnCoverDescription );
				$insTrans->set( 'itDirect', 0 );
				$insTrans->set( 'itGross', $mult * $ptAddOnGross );
				$insTrans->set( 'itCommissionRate', $ptAddOnCommissionRate );
				$insTrans->set( 'itCommission', $mult * $ptAddOnCommission );
				$insTrans->set( 'itNet', $mult * $ptAddOnNet );
				$insTrans->set( 'itGrossIPT', $mult * $ptAddOnIPT );
				$bal = $ptAddOnNet + $ptAddOnIPT;

				if ($ptDebit != 1) {
					$bal = 0 - $bal;
				}

				$insTrans->set( 'itOriginal', $bal );
				$insTrans->set( 'itBalance', $bal );
				$insTrans->set( 'itAccountingYear', $postingAccountingYear );
				$insTrans->set( 'itAccountingPeriod', $postingAccountingPeriod );
				$insTrans->setCreatedByAndWhen(  );
				$insTrans->insert( null );
				$itCode = $insTrans->getKeyValue(  );
				$this->set( 'ptAddOnInsCoTran', $itCode );
				$aa = new AccountingAudit( null );
				$aa->set( 'aaType', 'I' );
				$aa->set( 'aaSysTran', $ptSysTran );
				$aa->set( 'aaTran', $insTrans->getKeyValue(  ) );
				$aa->set( 'aaPostingDate', $insTrans->get( 'itPostingDate' ) );
				$aa->set( 'aaEffectiveDate', $insTrans->get( 'itEffectiveDate' ) );
				$aa->set( 'aaAccountingYear', $postingAccountingYear );
				$aa->set( 'aaAccountingPeriod', $postingAccountingPeriod );
				$aa->set( 'aaCreatedBy', $insTrans->get( 'itCreatedBy' ) );
				$aa->set( 'aaCreatedOn', $insTrans->get( 'itCreatedOn' ) );
				$aa->insert( null );
			}


			if ($ptIntroducerComm != 0) {
				$rtCalcOn = $ptCommission + $ptAddlCommission + $ptAddOnCommission + $ptEngineeringFeeComm - $ptClientDiscount;
				$intTrans = new IntroducerTransaction( null );
				$intTrans->set( 'rtIntroducer', $ptIntroducer );
				$intTrans->set( 'rtTransType', 'I' );
				$intTrans->set( 'rtSysTran', $ptSysTran );
				$intTrans->set( 'rtPolicyTran', $ptCode );
				$intTrans->set( 'rtTransDesc', $ptTransDesc );
				$intTrans->set( 'rtPostingDate', $ptPostingDate );
				$intTrans->set( 'rtEffectiveDate', $ptEffectiveFrom );
				$intTrans->set( 'rtCalcOn', $rtCalcOn );
				$intTrans->set( 'rtRate', $ptIntroducerCommRate );
				$intTrans->set( 'rtDirect', $ptDirect );

				if ($ptDebit == 1) {
					$intTrans->set( 'rtOriginal', $ptIntroducerComm );
					$intTrans->set( 'rtBalance', $ptIntroducerComm );
				} 
else {
					$intTrans->set( 'rtOriginal', 0 - $ptIntroducerComm );
					$intTrans->set( 'rtBalance', 0 - $ptIntroducerComm );
				}

				$intTrans->set( 'rtAccountingYear', $postingAccountingYear );
				$intTrans->set( 'rtAccountingPeriod', $postingAccountingPeriod );
				$intTrans->setCreatedByAndWhen(  );
				$intTrans->insert( null );
				$rtCode = $intTrans->getKeyValue(  );
				$this->set( 'ptIntroducerTran', $rtCode );
				$aa = new AccountingAudit( null );
				$aa->set( 'aaType', 'R' );
				$aa->set( 'aaTran', $intTrans->getKeyValue(  ) );
				$aa->set( 'aaSysTran', $ptSysTran );
				$aa->set( 'aaPostingDate', $intTrans->get( 'rtPostingDate' ) );
				$aa->set( 'aaEffectiveDate', $intTrans->get( 'rtEffectiveDate' ) );
				$aa->set( 'aaAccountingYear', $postingAccountingYear );
				$aa->set( 'aaAccountingPeriod', $postingAccountingPeriod );
				$aa->set( 'aaCreatedBy', $intTrans->get( 'rtCreatedBy' ) );
				$aa->set( 'aaCreatedOn', $intTrans->get( 'rtCreatedOn' ) );
				$aa->insert( null );
			}

		}

		function _updatepolicyandclientwithamountsanddates() {
			$type = $this->get( 'ptTransType' );
			$updateRates = false;

			if ($type == 1) {
				$updateRates = true;
			}


			if ($type == 2) {
				$updateRates = true;
			}


			if ($type == 3) {
				$updateRates = true;
			}


			if ($type == 4) {
				$updateRates = true;
			}


			if ($type == 5) {
				$updateRates = true;
			}


			if ($type == 6) {
				$updateRates = true;
			}

			$updateAmounts = false;

			if ($type == 1) {
				$updateAmounts = true;
			}


			if ($type == 4) {
				$updateAmounts = true;
			}


			if ($type == 6) {
				$updateAmounts = true;
			}

			$plCode = $this->get( 'ptPolicy' );
			$policy = new Policy( $plCode );

			if ($updateRates == true) {
				$policy->set( 'plCommissionRate', $this->get( 'ptCommissionRate' ) );
				$policy->set( 'plAddlCommissionRate', $this->get( 'ptAddlCommissionRate' ) );
				$policy->set( 'plAddOnCommissionRate', $this->get( 'ptAddOnCommissionRate' ) );
				$policy->set( 'plClientDiscountRate', $this->get( 'ptClientDiscountRate' ) );
				$policy->set( 'plEngineeringFeeCommRate', $this->get( 'ptEngineeringFeeCommRate' ) );
				$policy->set( 'plIntroducerCommRate', $this->get( 'ptIntroducerCommRate' ) );
			}


			if ($updateAmounts == true) {
				$policy->set( 'plGrossIncIPT', $this->get( 'ptGrossIncIPT' ) );
				$policy->set( 'plCommission', $this->get( 'ptCommission' ) );
				$policy->set( 'plAddlGrossIncIPT', $this->get( 'ptAddlGrossIncIPT' ) );
				$policy->set( 'plAddlCommission', $this->get( 'ptAddlCommission' ) );
				$policy->set( 'plAddOnGrossIncIPT', $this->get( 'ptAddOnGrossIncIPT' ) );
				$policy->set( 'plAddOnCommission', $this->get( 'ptAddOnCommission' ) );
				$policy->set( 'plClientDiscount', $this->get( 'ptClientDiscount' ) );
				$policy->set( 'plEngineeringFee', $this->get( 'ptEngineeringFee' ) );
				$policy->set( 'plEngineeringFeeComm', $this->get( 'ptEngineeringFeeComm' ) );
				$policy->set( 'plIntroducerComm', $this->get( 'ptIntroducerComm' ) );
				$policy->set( 'plBrokerFee', $this->get( 'ptBrokerFee' ) );
				$policy->set( 'plBrokerFee', $this->get( 'ptBrokerFee' ) );
				$policy->set( 'plGrossIPT', $this->get( 'ptGrossIPT' ) );
				$policy->set( 'plAddlIPT', $this->get( 'ptAddlIPT' ) );
			}

			$policy->set( 'plEngineeringFeeDesc', $this->get( 'ptEngineeringFeeDesc' ) );
			$policy->recalculateAccountingFields(  );
			$ptPostingDate = $this->get( 'ptPostingDate' );
			$ptEffectiveFrom = $this->get( 'ptEffectiveFrom' );
			$ptEffectiveTo = $this->get( 'ptEffectiveTo' );

			if ($type == 1) {
				$prevRenewal = $policy->get( 'plRenewalDate' );
				$policy->set( 'plPrevRenewalDate', $prevRenewal );
				$policy->set( 'plRenewalDate', $ptEffectiveTo );
			}


			if ($type == 2) {
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
			}


			if ($type == 3) {
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
			}


			if (( $type == 4 || $type == 6 )) {
				$unixDate = umakeunixdatefromsqldate( $ptEffectiveTo );
				$newDate = umakesqldatefromunixdate( $unixDate );
				$policy->set( 'plRenewalDate', $newDate );
				$policy->set( 'plInceptionDate', $ptEffectiveFrom );
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
				$policy->set( 'plStatus', 1 );
			}


			if ($type == 5) {
				$policy->set( 'plTORDate', $ptEffectiveTo );
				$policy->set( 'plStatusDate', $policy->get( 'plInceptionDate' ) );
			}


			if ($type == 7) {
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
			}


			if ($type == 8) {
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
			}


			if ($type == 9) {
				$policy->set( 'plStatusDate', $ptEffectiveFrom );
			}

			$ptStatus = 0;

			if ($type == 1) {
				$ptStatus = 1;
			}


			if ($type == 4) {
				$ptStatus = 1;
			}


			if ($type == 5) {
				$ptStatus = 3;
			}


			if ($type == 6) {
				$ptStatus = 1;
			}


			if ($type == 7) {
				$ptStatus = 4;
			}


			if ($type == 8) {
				$ptStatus = 7;
			}


			if ($type == 9) {
				$ptStatus = 3;
			}


			if ($ptStatus != 0) {
				$policy->set( 'plStatus', $ptStatus );
			}

			$policy->update(  );
			$clCode = $this->get( 'ptClient' );
			$client = new Client( $clCode );

			if (( ( $type == 4 || $type == 6 ) || $type == 6 )) {
				$client->set( 'clStatus', 1 );
			}

			$client->update(  );
		}

		function producepreviewdocument() {
			return $this->_produceDocument( false );
		}

		function _producedocument($posted) {
			global $user;

			if (is_a( $user, 'User' )) {
				$usCode = $user->getKeyValue(  );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					if ($posted == true) {
						trigger_error( 'no user', E_USER_NOTICE );
						$usCode = null;
					} 
else {
						trigger_error( 'no user', E_USER_NOTICE );
						$usCode = null;
					}
				}
			}

			$doCode = $this->get( 'ptDocm' );

			if (0 < $doCode) {
				$document = new Document( $doCode );
				$docmNo = $doCode;
			} 
else {
				$document = new Document( null );
				$document->insert( null );
				$docmNo = $document->getKeyValue(  );
			}

			$document->set( 'doWhenOriginated', ugettimenow(  ) );
			$document->set( 'doOriginator', $usCode );
			$plCode = $this->get( 'ptPolicy' );
			$clCode = $this->get( 'ptClient' );
			$icCode = 0;
			$inCode = 0;
			$ptCode = $this->getKeyValue(  );
			$ptSysTran = $this->get( 'ptSysTran' );
			$document->set( 'doTrans', $ptCode );
			$document->set( 'doSysTran', $ptSysTran );
			$document->set( 'doPolicy', $plCode );
			$document->set( 'doClient', $clCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$subject = $this->get( 'ptTransDesc' );
			$invNo = $this->get( 'ptInvoiceNo' );
			$subject .= ' - Inv. ' . $invNo;
			$document->set( 'doSubject', $subject );

			if ($posted == false) {
				$document->set( 'doSubject', 'preview' );
				$this->set( 'ptBrokerRef', 'PREVIEW ONLY' );
			}

			$ptDebit = $this->get( 'ptDebit' );

			if ($ptDebit == 1) {
				$doDocmType = KEY_POLICY_DOCM_DEBIT;
			} 
else {
				$doDocmType = KEY_POLICY_DOCM_CREDIT;
			}

			$ptTransType = $this->get( 'ptTransType' );

			if ($ptTransType == 1) {
				$doDocmType = KEY_POLICY_DOCM_RENEWAL;
			}

			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( 'invoice', $posted, $docmNo );
			$name = sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );

			if ($posted == true) {
				$document->setPolicySequence(  );

				if (0 < $clCode) {
					$document->setClientSequence(  );
				}


				if (0 < $icCode) {
					$document->setInscoSequence(  );
				}


				if (0 < $inCode) {
					$document->setIntroducerSequence(  );
				}

				$document->setTransSequence(  );
			}

			$document->update(  );
			return $document;
		}

		function _producereceipt() {
			global $user;

			$posted = true;

			if (is_a( $user, 'User' )) {
				$usCode = $user->getKeyValue(  );
			} 
else {
				if (DEBUG_MODE == true) {
					$usCode = null;
				} 
else {
					trigger_error( 'cant get user ', E_USER_WARNING );
				}
			}

			$doCode = $this->get( 'ptReceiptDocm' );

			if (0 < $doCode) {
				$document = new Document( $doCode );
				$docmNo = $doCode;
			} 
else {
				$document = new Document( null );
				$document->insert( null );
				$docmNo = $document->getKeyValue(  );
			}

			$document->set( 'doWhenOriginated', ugettimenow(  ) );
			$document->set( 'doOriginator', $usCode );
			$plCode = $this->get( 'ptPolicy' );
			$clCode = $this->get( 'ptClient' );
			$icCode = 0;
			$inCode = 0;
			$ptCode = $this->getKeyValue(  );
			$ptSysTran = $this->get( 'ptSysTran' );
			$document->set( 'doTrans', $ptCode );
			$document->set( 'doSysTran', $ptSysTran );
			$document->set( 'doPolicy', $plCode );
			$document->set( 'doClient', $clCode );
			$document->set( 'doUploadType', 1 );
			$document->set( 'doLocked', 1 );
			$document->set( 'doWhenEntered', ugettimenow(  ) );
			$document->set( 'doEnteredBy', $usCode );
			$subject = 'Receipt ';
			$subject .= $this->get( 'ptTransDesc' );
			$invNo = $this->get( 'ptInvoiceNo' );
			$subject .= ' - Inv. ' . $invNo;
			$document->set( 'doSubject', $subject );
			$ptDebit = $this->get( 'ptDebit' );
			$doDocmType = KEY_POLICY_DOCM_RECEIPT;
			$document->set( 'doDocmType', $doDocmType );
			$document->set( 'doUpdateorCreate', ugettimenow(  ) );
			$pdfText = $this->_makePDF( 'receipt', true, $docmNo );
			$name = 'R' . sprintf( '%07d', $docmNo ) . '.pdf';
			$type = 'application/pdf';
			$document->addDocumentUsingText( $name, $type, $pdfText );

			if ($posted == true) {
				$document->setPolicySequence(  );

				if (0 < $clCode) {
					$document->setClientSequence(  );
				}


				if (0 < $icCode) {
					$document->setInscoSequence(  );
				}


				if (0 < $inCode) {
					$document->setIntroducerSequence(  );
				}

				$document->setTransSequence(  );
			}

			$document->update(  );
			return $document;
		}

		function _makepdf($type, $posted, $docmNo) {
			if (( $type != 'invoice' && $type != 'receipt' )) {
				trigger_error( 'incorrect type', E_USER_ERROR );
			}

			require_once( UTIL_PATH . 'UXML.class.php' );
			require_once( UTIL_PATH . 'UXMLTag.class.php' );
			require_once( UTIL_PATH . 'UPDF.class.php' );
			require_once( UTIL_PATH . 'UPDFXML.class.php' );
			require_once( '../policies/templateClasses/PolicyTransDocmTemplate.class.php' );
			$plCode = $this->get( 'ptPolicy' );
			$policy = new Policy( $plCode );
			$ptReversesTran = $this->get( 'ptReversesTran' );
			$this->_makeArrayOfAsterixes(  );
			$pdf = new UPDF( 'p', true );
			$xmlText = file_get_contents( PDFS_PATH . 'invoice.xml' );
			$template = new PolicyTransDocmTemplate( null );

			if ($type == 'receipt') {
				$template->setDoCashReceipts( true );
				$date = uformatsqldate3( $this->get( 'ptPaymentDate' ) );
				$template->set( 'receivedDate', $date );
			} 
else {
				$template->setDoCashReceipts( false );
			}

			$template->setParseForXML(  );
			$template->setTransaction( $this );
			$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );

			if ($posted == false) {
				$template->set( 'previewText', 'PREVIEW - DO NOT SEND' );
			} 
else {
				$template->set( 'previewText', '' );
			}


			if (0 < $ptReversesTran) {
				$template->set( 'reversalText', 'DO NOT SEND - INTERNAL DOCUMENT (REVERSAL JOURNAL)' );
			} 
else {
				$template->set( 'reversalText', '' );
			}

			$out = $this->_makeAddressEtc(  );
			$template->set( 'address', $out );
			$title = $this->_getDocumentTitle(  );
			$template->set( 'docmTitle', $title );
			$text = $this->_getDocumentText(  );
			$template->set( 'docmText', $text );
			$icCode = $this->get( 'ptInsCo' );

			if (0 < $icCode) {
				$ins = new Insco( $icCode );
				$name = $ins->get( 'icName' );
			} 
else {
				$name = '';
			}

			$template->set( 'insCoName', $name );
			$pn = $this->get( 'ptPolicyNumber' );

			if (strlen( trim( $pn ) ) == 0) {
				trigger_error( 'no pol number', E_USER_WARNING );
			}

			$template->set( 'policyNo', $this->get( 'ptPolicyNumber' ) );
			$date = $this->get( 'ptEffectiveFrom' );
			$date = fformatdatefordocument( $date );
			$template->setForHTML( 'fromDate', $date );
			$date = $this->get( 'ptEffectiveTo' );
			$date = fformatdatefordocument( $date );
			$template->setForHTML( 'toDate', $date );
			$direct = $this->get( 'ptDirect' );
			$amt = $this->get( 'ptGrossIncIPT' );
			$disc = $this->get( 'ptClientDiscount' );

			if ($direct != 1) {
				$amt -= $disc;
			}

			$amt = uformatmoneywithcommas( $amt );
			$template->set( 'premium', CURRENCY_SYMBOL . $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptAddlGrossIncIPT' );
			$template->set( 'addlPremium', CURRENCY_SYMBOL . $amt );
			$desc = $this->getForHTML( 'ptAddlCoverDesc' );
			$desc = substr( $desc, 0, 12 );
			$template->set( 'addlDesc', $desc );
			$desc = $this->get( 'ptEngineeringFeeDesc' );
			$template->setForHTML( 'engFeeDesc', $desc );
			$amt = $this->getAsMoneyWithCommas( 'ptEngineeringFee' );
			$template->set( 'engFee', CURRENCY_SYMBOL . $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptEngineeringFeeVAT' );
			$template->set( 'engFeeVAT', CURRENCY_SYMBOL . $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptEngineeringFeeVATRate' );
			$template->setForHTML( 'engFeeVATRate', $amt );
			$amt = $this->get( 'ptAddOnGrossIncIPT' );
			$amt = uformatmoneywithcommas( $amt );
			$template->set( 'addOnPremium', CURRENCY_SYMBOL . $amt );
			$amt = $this->get( 'ptClientDiscount' );
			$amt = 0 - $amt;
			$amt = uformatmoneywithcommas( $amt );
			$template->set( 'clientDiscount', CURRENCY_SYMBOL . $amt );
			$desc = $this->get( 'ptAddOnCoverDescription' );
			$template->set( 'addOnDesc', $desc );
			$amt = $this->getAsMoneyWithCommas( 'ptBrokerFee' );
			$template->set( 'brokerFee', CURRENCY_SYMBOL . $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptBrokerFeeVAT' );
			$template->set( 'brokerFeeVAT', CURRENCY_SYMBOL . $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptBrokerFeeVATRate' );
			$template->setForHTML( 'brokerFeeVATRate', $amt );
			$amt = $this->getAsMoneyWithCommas( 'ptClientTotal' );
			$template->set( 'clientTotal', CURRENCY_SYMBOL . $amt );
			$x = trim( $template->get( 'clientTotal' ) );
			$len = strlen( $x ) - 2;
			$under = '';
			$i = 0;

			while ($i < $len) {
				$under .= '-';
				++$i;
			}

			$template->set( 'bottomTotalUnderline', $under );
			$amt = $this->get( 'ptGrossIncIPT' );
			$amt += $this->get( 'ptAddlGrossIncIPT' );
			$amt += $this->get( 'ptEngineeringFee' );
			$amt += $this->get( 'ptEngineeringFeeVAT' );
			$amt = uformatmoneywithcommas( $amt );
			$template->set( 'instalmentTotal', CURRENCY_SYMBOL . $amt );
			$x = trim( $template->get( 'instalmentTotal' ) );
			$len = strlen( $x ) - 2;
			$under = '';
			$i = 0;

			while ($i < $len) {
				$under .= '-';
				++$i;
			}

			$template->set( 'topTotalUnderline', $under );
			$name = $policy->get( 'plPolicyHolder' );
			$name = strtoupper( $name );
			$template->set( 'policyHolderInCaps', $name );
			$name = $this->get( 'ptCoverDescription' );
			$name = strtoupper( $name );
			$template->set( 'policyTypeInCaps', $name );
			$desc = $this->get( 'ptTransDesc' );
			$template->set( 'transactionDetail', $desc );
			$desc = $this->get( 'ptCoverDesc' );
			$template->set( 'coverDesc', $desc );
			$template->setHTMLFromText( $xmlText );
			$template->parseAll(  );
			$newXMLText = $template->getOutput(  );
			$xml = new UPDFXML( $newXMLText, $pdf );
			$pdf->close(  );
			$text = $pdf->returnAsString(  );
			return $text;
		}

		function getdocumenttype() {
			$type = $this->get( 'ptTransType' );
			$docmType = '';
			switch ($type) {
				case 1: {
					$docmType = 'R';
					break;
				}

				case 2: {
					$docmType = 'D';
					break;
				}

				case 3: {
					$docmType = 'C';
					break;
				}

				case 4: {
					$docmType = 'D';
					break;
				}

				case 5: {
					$docmType = 'D';
					break;
				}

				case 6: {
					$docmType = 'D';
					break;
				}

				case 7: {
					$docmType = 'C';
					break;
				}

				case 8: {
					$docmType = 'C';
					break;
				}

				case 9: {
					$docmType = 'C';
				}
			}

			return $docmType;
		}

		function _makeaddressetc() {
			$clCode = $this->get( 'ptClient' );
			$client = new Client( $clCode );
			$nameAndAddress = $client->getInvoiceNameAndAddress(  );
			$ref = $this->get( 'ptBrokerRef' );
			$date = $this->get( 'ptPostingDate' );
			$date = uformatsqldate3( $date );
			$out = '' . $nameAndAddress . '


' . $ref . '
' . $date . '
';
			return $out;
		}

		function _makebrokerref() {
			$initials = $this->_getHandlerInitials(  );

			if (strlen( trim( $initials ) ) == 0) {
				udbrollbacktransaction(  );
				trigger_error( 'cant find initials of handler', E_USER_ERROR );
				$this->handlerInitials = '';
			}

			$clCode = $this->get( 'ptClient' );
			$invNo = $this->get( 'ptInvoiceNo' );
			$ref = '' . $initials . '/' . $clCode . '/' . $invNo;
			return $ref;
		}

		function _getdocumenttitle() {
			$type = $this->get( 'ptTransType' );
			$name = '';
			switch ($type) {
				case 1: {
					$name = 'RENEWAL NOTICE';
					break;
				}

				case 2: {
					$name = 'DEBIT NOTE';
					break;
				}

				case 3: {
					$name = 'CREDIT NOTE';
					break;
				}

				case 4: {
					$name = 'DEBIT NOTE';
					break;
				}

				case 5: {
					$name = 'DEBIT NOTE';
					break;
				}

				case 6: {
					$name = 'DEBIT NOTE';
					break;
				}

				case 7: {
					$name = 'CREDIT NOTE';
					break;
				}

				case 8: {
					$name = 'CREDIT NOTE';
					break;
				}

				case 9: {
					$name = 'CREDIT NOTE';
				}
			}

			return $name;
		}

		function _getdocumenttext() {
			$type = $this->get( 'ptTransType' );
			$direct = $this->get( 'ptDirect' );

			if ($direct == 1) {
				$ptCode = $this->get( 'ptPaymentMethod' );

				if ($ptCode == 5) {
					$direct = 0;
				}


				if ($ptCode == 7) {
					$direct = 0;
				}
			}


			if ($direct == 1) {
				$willGoToBroker = $this->_willSomeGoToBroker(  );
			}

			$docType = '';
			switch ($type) {
				case 1: {
					$docType = 'R';
					break;
				}

				case 2: {
					$docType = 'D';
					break;
				}

				case 3: {
					$docType = 'C';
					break;
				}

				case 4: {
					$docType = 'D';
					break;
				}

				case 5: {
					$docType = 'D';
					break;
				}

				case 6: {
					$docType = 'D';
					break;
				}

				case 7: {
					$docType = 'C';
					break;
				}

				case 8: {
					$docType = 'C';
					break;
				}

				case 9: {
					$docType = 'C';
				}
			}

			$text = '';

			if ($docType == 'R') {
				if ($direct == 1) {
					if ($willGoToBroker == true) {
						$text = '
The undermentioned policy expires on the renewal date shown below. The main premium payment is by instalments. However, we would ask that you let us have payment of the additional  item(s)  shown.

If you wish to make any alterations or do not require cover please let us know. Otherwise cover will continue as premiums are paid by instalments.
';
					} 
else {
						$text = '
The undermentioned policy expires on the renewal date shown below.

If you wish to make any alterations or do not require cover please let us know. Otherwise cover will continue as premiums are paid by instalments.
';
					}
				} 
else {
					$text = 'The undermentioned policy expires on the renewal date shown below.  Will you kindly let us have your renewal instructions as early as possible together  with your remittance in respect of the premium due.  If you wish to make any alteration please let us know.	';
				}
			}


			if ($docType == 'D') {
				if ($direct == 1) {
					if ($willGoToBroker == true) {
						$text = 'We have arranged this insurance according to your instructions.  The main premium payment is by instalments. However, we would ask that you let us have payment of the additional  item(s)  shown. ';
					} 
else {
						$text = 'We have arranged this insurance according to your instructions. As payment is by instalments, no action need be taken.';
					}
				} 
else {
					$text = 'We have arranged this insurance according to your instructions and would ask that you now let us have payment of the premium shown (cheques made payable to ourselves). ';
				}
			}


			if ($docType == 'C') {
				$text = '';
			}

			return $text;
		}

		function _makearrayofasterixes() {
			$ast = array(  );
			$i = 0;

			while ($i < 7) {
				$ast[$i] = 0 - 1;
				++$i;
			}

			$sequ = 0;

			if ($this->get( 'ptEngineeringFee' )) {
				$ast[0] = ++$sequ;
			}


			if (0 < $this->get( 'ptEngineeringFeeVAT' )) {
				$ast[1] = ++$sequ;
			}


			if (0 < $this->get( 'ptAddOnGrossIncIPT' )) {
				$ast[2] = ++$sequ;
			}


			if ($this->get( 'ptClientDiscount' )) {
				if ($this->get( 'ptDirect' ) == 1) {
					$ast[3] = ++$sequ;
				}
			}


			if (0 < $this->get( 'ptBrokerFee' )) {
				$ast[4] = ++$sequ;
			}


			if (0 < $this->get( 'ptBrokerFeeVAT' )) {
				$ast[5] = ++$sequ;
			}


			if ($this->isTotalNeeded(  ) == true) {
				if ($this->get( 'ptDirect' ) == 1) {
					$ast[6] = ++$sequ;
				}
			}


			if ($sequ == 1) {
				$i = 0;

				while ($i < 7) {
					if ($ast[$i] == 1) {
						$ast[$i] = 0;
					}

					++$i;
				}
			}

			$this->asterixes = $ast;
		}

		function istotalneeded() {
			$prem = $this->get( 'ptGrossIncIPT' );
			$disc = $this->get( 'ptClientDiscount' );
			$clTotal = $this->get( 'ptClientTotal' );

			if ($prem - $disc == $clTotal) {
				return false;
			}


			if ($this->get( 'ptDirect' ) == 1) {
				$num = 0;

				if (0 < $this->get( 'ptAddOnGrossIncIPT' )) {
					++$num;
				}


				if (0 < $this->get( 'ptBrokerFee' )) {
					++$num;
				}


				if (0 < $this->get( 'ptBrokerFeeVAT' )) {
					++$num;
				}


				if ($num < 2) {
					return false;
				}
			}

			return true;
		}

		function _willsomegotobroker() {
			if ($this->get( 'ptDirect' ) != 1) {
				return true;
			}

			$num = 0;

			if (0 < $this->get( 'ptAddOnGrossIncIPT' )) {
				++$num;
			}


			if (0 < $this->get( 'ptBrokerFee' )) {
				++$num;
			}


			if (0 < $this->get( 'ptBrokerFeeVAT' )) {
				++$num;
			}


			if ($num == 0) {
				return false;
			}

			return true;
		}

		function _gethandlerinitials() {
			$usCode = $this->get( 'ptHandler' );

			if ($usCode <= 0) {
				trigger_error( 'no handler', E_USER_ERROR );
			}

			$handler = new User( $usCode );
			$initials = $handler->getInitials(  );

			if (strlen( trim( $initials ) ) == 0) {
				trigger_error( 'zero length initials', E_USER_ERROR );
			}

			return $initials;
		}
	}

?>