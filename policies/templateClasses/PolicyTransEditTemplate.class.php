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

	class policytransedittemplate {
		var $DIRECT_HTML = 'policyTransEditDirect.html';
		var $INDIRECT_HTML = 'policyTransEdit.html';
		var $iptAmendable = null;

		function policytransedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'ptCode' );
			$this->addField( 'ptCodeFormatted' );
			$this->addField( 'ptSysTranFormatted' );
			$this->addField( 'ptClient' );
			$this->addField( 'ptInsCo' );
			$this->addField( 'ptAltInsCo' );
			$this->addField( 'ptHandler' );
			$this->addField( 'ptGross' );
			$this->addField( 'ptCommissionRate' );
			$this->addField( 'ptCommission' );
			$this->addField( 'ptNet' );
			$this->addField( 'ptIPTRate' );
			$this->addField( 'ptGrossIPT' );
			$this->addField( 'ptAddlGross' );
			$this->addField( 'ptAddlCommissionRate' );
			$this->addField( 'ptAddlCommission' );
			$this->addField( 'ptAddlNet' );
			$this->addField( 'ptAddlIPT' );
			$this->addField( 'ptAddlIPTRate' );
			$this->addField( 'ptAddOnGross' );
			$this->addField( 'ptAddOnCommissionRate' );
			$this->addField( 'ptAddOnCommission' );
			$this->addField( 'ptAddOnNet' );
			$this->addField( 'ptAddOnIPT' );
			$this->addField( 'ptAddOnIPTRate' );
			$this->addField( 'ptClientDiscountRate' );
			$this->addField( 'ptClientDiscount' );
			$this->addField( 'ptEngineeringFee' );
			$this->addField( 'ptEngineeringFeeCommRate' );
			$this->addField( 'ptEngineeringFeeNet' );
			$this->addField( 'ptEngineeringFeeVATRate' );
			$this->addField( 'ptEngineeringFeeVAT' );
			$this->addField( 'ptBrokerFee' );
			$this->addField( 'ptBrokerFeeVATRate' );
			$this->addField( 'ptBrokerFeeVAT' );
			$this->addField( 'ptClientSubTotal' );
			$this->addField( 'ptClientTotal' );
			$this->addField( 'ptBrokerSubTotal' );
			$this->addField( 'ptInsCoTotal' );
			$this->addField( 'ptAddOnTotal' );
			$this->addField( 'ptIntroducerCommRate' );
			$this->addField( 'ptIntroducerComm' );
			$this->addField( 'ptIntroducerCommRate' );
			$this->addField( 'ptTotalGrossIncIPT' );
			$this->addField( 'ptTotalGross' );
			$this->addField( 'ptTotalCommission' );
			$this->addField( 'ptTotalNet' );
			$this->addField( 'ptDirectClientTotal' );
			$this->addField( 'ptDirectClientGrand' );
			$this->addField( 'ptDirectBrokerTotal' );
			$this->addField( 'ptBrokerTotalPlusFees' );
			$this->addField( 'iptAmendable' );
			$this->addField( 'returnTo' );
			$this->addField( 'processPeriodType' );
			$this->setHeader( SITE_NAME );
			$this->addField( 'fullName' );
		}

		function settransaction($ptCode) {
			$trans = new PolicyTransaction( $ptCode );
			$plCode = $trans->get( 'ptPolicy' );
			$this->setPolicy( $plCode );
			$this->policyTransaction = &$trans;

			$this->retrieveTransactionDetails(  );
		}

		function setpolicy($plCode) {
			$tran = new PolicyTransaction( null );
			$this->setAll( $tran->getAllForHTML(  ) );
			$this->set( 'ptCode', '' );
			$this->set( 'ptSysTranFormatted', '' );
			$this->set( 'statusDesc', '' );
			$this->set( 'accountPeriodDesc', '' );
			$this->set( 'accountPeriodDesc', '' );
			$this->policyTransaction = null;
			$this->policy = new Policy( $plCode );
			$policy = &$this->policy;

			$plDirect = $policy->get( 'plDirect' );

			if ($plDirect == 1) {
				$htmlFile = $this->DIRECT_HTML;
			} 
else {
				$htmlFile = $this->INDIRECT_HTML;
			}

			$this->setHTML( $htmlFile );
			$this->setAll( $policy->getAllForHTML(  ) );
			$this->set( 'plCode', $plCode );
			$plClient = $policy->get( 'plClient' );

			if (0 < $plClient) {
				$this->client = new Client( $plClient );
				$client = &$this->client;

				$this->setAll( $client->getAllForHTML(  ) );
				$clAddress = $client->get( 'clAddress' );
				$clAddressWithBRs = str_replace( '
', '<br>
', $clAddress );
				$this->set( 'clAddressWithBRs', $clAddressWithBRs );
				$fullName = $client->getFullOrCompanyName(  );

				if (32 < strlen( $fullName )) {
					$fullName = substr( $fullName, 0, 29 );
					$fullName .= '...';
				}

				$this->set( 'clientName', $fullName );
				$this->set( 'ptClient', $plClient );
			}

			$this->set( 'policyNumber', $policy->getForHTML( 'plPolicyNumber' ) );
			$iptAmendable = $policy->isIPTAmendable(  );
			$this->set( 'iptAmendable', $iptAmendable );
			$this->iptAmendable = $iptAmendable;
			$cobName = '';
			$cbAllowIPTAmend = false;
			$cbCode = $policy->get( 'plClassOfBus' );

			if (0 < $cbCode) {
				$cob = new Cob( $cbCode );
				$cobName = $cob->getForHTML( 'cbName' );
			}

			$this->set( 'classOfBusiness', $cobName );
			$text = $policy->get( 'plAddOnCoverDescription' );
			$this->set( 'addOnCoverDesc', $text );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function refreshpolicy() {
			$plCode = $this->policy->get( 'plCode' );
			$this->setPolicy( $plCode );
		}

		function getpolicy() {
			return $this->policy;
		}

		function getrefreshedpolicy() {
			$this->policy->refresh(  );
			return $this->policy;
		}

		function starttransaction($transType) {
			$this->policyTransaction = new PolicyTransaction( null );
			$this->policy->decideIPTAndVATRates(  );
			$this->policyTransaction->populateFromPolicy( $this->policy, $transType );
			$this->policyTransaction->recalculateAccountingFields(  );
			$this->setAll( $this->policyTransaction->getAllForHTML(  ) );
			$this->_setIndividualFieldsEditable(  );
		}

		function settransactiondetails() {
			$this->policyTransaction->setAll( $this->fields );
			$this->policyTransaction->recalculateAccountingFields(  );
		}

		function retrievetransactiondetails() {
			$this->setAll( $this->policyTransaction->getAllForHTML(  ) );
			$ptCode = $this->policyTransaction->getKeyValue(  );
			$ptCodeFormatted = sprintf( '%07d', $ptCode );
			$this->set( 'ptCodeFormatted', $ptCodeFormatted );
			$ptSysTranFormatted = sprintf( '%07d', $this->policyTransaction->get( 'ptSysTran' ) );
			$this->set( 'ptSysTranFormatted', $ptSysTranFormatted );
			$tsCode = $this->policyTransaction->get( 'ptStatus' );

			if (0 < $tsCode) {
				$status = new PolicyTransStatus( $tsCode );
				$name = $status->get( 'tsName' );
			} 
else {
				$name = '';
			}

			$this->set( 'statusDesc', $name );
			$ptAccountingYear = $this->policyTransaction->get( 'ptAccountingYear' );
			$ptAccountingPeriod = $this->policyTransaction->get( 'ptAccountingPeriod' );
			$desc = fgetaccountingperioddesc( $ptAccountingPeriod, $ptAccountingYear );
			$this->set( 'accountPeriodDesc', $desc );
		}

		function gettrans() {
			if (!isset( ->policyTransaction )) {
				return null;
			}


			if ($this->policyTransaction == null) {
				return null;
			}

			return $this->policyTransaction;
		}

		function starttransactionfromtransactiontoreverse($ptCode) {
			if ($ptCode < 1) {
				trigger_error( '' . 'no pol tran ' . $ptCode, E_USER_ERROR );
			}

			$pt = new PolicyTransaction( $ptCode );
			$this->policyTransaction = new PolicyTransaction( null );
			$this->policyTransaction->populateFromReversalTransaction( $pt );
			$this->policyTransaction->recalculateAccountingFields(  );
			$this->setAll( $this->policyTransaction->getAllForHTML(  ) );
			$this->_setIndividualFieldsEditable(  );
		}

		function canceltransaction() {
			$plCode = $this->policy->getKeyValue(  );
			$this->setPolicy( $plCode );
			$this->set( 'processPeriodType', 'C' );
		}

		function posttransaction() {
			$this->policyTransaction->postTransaction(  );
			$ptCode = $this->policyTransaction->getKeyValue(  );
			$this->set( 'ptCode', $ptCode );
			$this->set( 'ptCodeFormatted', sprintf( '%07d', $ptCode ) );
			$ptAccountingPeriod = $this->policyTransaction->get( 'ptAccountingPeriod' );
			$ptAccountingYear = $this->policyTransaction->get( 'ptAccountingYear' );
			$desc = fgetaccountingperioddesc( $ptAccountingPeriod, $ptAccountingYear );
			$this->set( 'accountPeriodDesc', $desc );
			$this->set( 'processPeriodType', 'C' );
		}

		function savetransaction() {
			$this->policyTransaction->saveTransaction(  );
			$ptCode = $this->policyTransaction->getKeyValue(  );
			$this->set( 'ptCode', $ptCode );
			$this->set( 'ptCodeFormatted', sprintf( '%07d', $ptCode ) );
			$this->set( 'processPeriodType', 'C' );
		}

		function deletetransaction() {
			$this->policyTransaction->deleteTransaction(  );
			$ptCode = $this->policyTransaction->getKeyValue(  );
			$this->set( 'ptCode', $ptCode );
			$this->set( 'ptCodeFormatted', sprintf( '%07d', $ptCode ) );
		}

		function wheneditrequested($template, $input) {
			$this->setFieldsUneditable(  );
			utemplate::wheneditrequested( $template, $input );
		}

		function setfieldsuneditable() {
			$this->setAllFieldsAllowEditing( true );
			$this->setFieldAllowEditing( 'ptEffectiveFrom', true );
			$this->setFieldAllowEditing( 'ptEffectiveTo', true );
			$trans = $this->getTrans(  );
			$type = $trans->get( 'ptTransType' );
			$frequ = $trans->get( 'ptFrequency' );

			if (( $type == 4 || $type == 6 )) {
				$this->setFieldAllowEditing( 'ptEffectiveFrom', false );
			}


			if ($type == 1) {
				$this->setFieldAllowEditing( 'ptEffectiveFrom', false );
				$this->setFieldAllowEditing( 'ptEffectiveTo', false );
			}


			if ($type == 2) {
				$this->setFieldAllowEditing( 'ptEffectiveTo', true );
			}


			if ($type == 3) {
				$this->setFieldAllowEditing( 'ptEffectiveTo', true );
			}


			if ($type == 5) {
				$this->setFieldAllowEditing( 'ptEffectiveTo', false );
			}


			if ($type == 7) {
				$this->setFieldAllowEditing( 'ptEffectiveFrom', false );
				$this->setFieldAllowEditing( 'ptEffectiveTo', false );
			}


			if ($type == 8) {
				$this->setFieldAllowEditing( 'ptEffectiveFrom', false );
				$this->setFieldAllowEditing( 'ptEffectiveTo', false );
			}


			if ($type == 9) {
				$this->setFieldAllowEditing( 'ptEffectiveTo', false );
			}

			$ptReversesTran = $trans->get( 'ptReversesTran' );

			if (0 < $ptReversesTran) {
				$this->setAllFieldsAllowEditing( false );
			}

		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$policy = &$this->policy;

			if (isset( $policy )) {
				$usCode = $policy->get( 'ptLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $policy->get( 'ptLastUpdateOn' );
				$when = uformatourtimestamp( $when );
			}


			if ($do == false) {
				return '';
			}

			$this->set( 'lastUpdateBy', $initials );
			$this->set( 'lastUpdatedOn', $when );
			$out = $this->parse( $text );
			return $out;
		}

		function showwhenclientdiscountallowed($text) {
			if (( ( isset( $this->policyTransaction ) && $this->policyTransaction->get( 'ptPostStatus' ) == 'P' ) && $this->policyTransaction->get( 'ptClientDiscount' ) != 0 )) {
				$out = $this->parse( $text );
				return $out;
			}

			$policy = &$this->policy;

			$ok = false;

			if (isset( $policy )) {
				$x = $policy->get( 'plClientDisc' );

				if ($x == 1) {
					$ok = true;
				}
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whendocmtoview($text) {
			if (!isset( $this->policyTransaction )) {
				return '';
			}


			if ($this->policyTransaction->recordExists(  ) == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $text;
		}

		function showwhenintroducerdiscountallowed($text) {
			if (( ( isset( $this->policyTransaction ) && $this->policyTransaction->get( 'ptPostStatus' ) == 'P' ) && $this->policyTransaction->get( 'ptIntroducerComm' ) != 0 )) {
				$out = $this->parse( $text );
				return $out;
			}

			$policy = &$this->policy;

			$ok = false;

			if (isset( $policy )) {
				$x = $policy->get( 'plIntrodComm' );

				if ($x == 1) {
					$ok = true;
				}
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenunpostedtrans($text) {
			if (!isset( $this->policyTransaction )) {
				return '';
			}


			if ($this->policyTransaction->recordExists(  ) == false) {
				return '';
			}

			$ps = $this->policyTransaction->get( 'ptPostStatus' );

			if (( $ps == 'P' || $ps == 'D' )) {
				return '';
			}

			$out = $this->parse( $text );
			return $text;
		}

		function listtranstypes($text) {
			$q = 'SELECT * FROM policyTransactionTypes ORDER BY pySequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$ptTransType = $this->get( 'ptTransType' );

			while ($row = udbgetrow( $result )) {
				$pyCode = $row['pyCode'];
				$pyName = $row['pyName'];

				if ($pyCode == $ptTransType) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'pyCode', $pyCode );
				$this->set( 'pyName', $pyName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showtrans($text) {
			$policy = &$this->policy;

			$plCode = $policy->getKeyValue(  );
			$q = '' . 'SELECT ptCode FROM policyTransactions WHERE ptPolicy=' . $plCode . ' AND ptPostStatus != \'D\' ORDER BY ptCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ptCode = $row['ptCode'];
				$tran = new PolicyTransaction( $ptCode );
				$typeName = $tran->getTypeDescription(  );
				$this->set( 'typeName', $typeName );
				$addOnDesc = trim( $tran->get( 'ptAddOnCoverDescription' ) );

				if (strlen( $addOnDesc ) == 0) {
					$addOnDesc = $typeName;
				}

				$ptAddOnGrossIncIPT = $tran->get( 'ptAddOnGrossIncIPT' );
				$ptClientDiscount = $tran->get( 'ptClientDiscount' );
				$ptBrokerFee = $tran->get( 'ptBrokerFee' );
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
				$this->set( 'codeFormatted', sprintf( '%07d', $tran->get( 'ptSysTran' ) ) );
				$this->set( 'code', sprintf( '%06d', $tran->get( 'ptSysTran' ) ) );
				$this->set( 'ptCode', $tran->get( 'ptCode' ) );
				$invNo = $tran->get( 'ptInvoiceNo' );

				if ($invNo <= 0) {
					$invNo = 'UNPOSTED';
				}

				$this->set( 'invNo', $invNo );
				$usCode = $tran->get( 'ptCreatedBy' );
				$ptCreatedOn = $tran->get( 'ptCreatedOn' );
				$ptCreatedOn = uformatourtimestamp( $ptCreatedOn );
				$this->set( 'postingDate', $ptCreatedOn );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'initials', $usInitials );
				$desc = $tran->get( 'ptTransDesc' );

				if (trim( $desc ) == '') {
					$desc = 'blank';
				}

				$this->set( 'desc', $desc );
				$direct = $tran->get( 'ptDirect' );

				if ($direct == 1) {
					$direct = 'Y';
				} 
else {
					$direct = 'N';
				}

				$this->set( 'direct', $direct );
				$this->set( 'directElement', 'Y' );
				$this->set( 'clientTotal', $tran->get( 'ptClientTotal' ) );
				$ds = '';
				$doCode = $tran->get( 'ptDocm' );

				if (0 < $doCode) {
					$doc = new Document( $doCode );
					$doClientSentWhen = $doc->get( 'doClientSentWhen' );

					if (( $doClientSentWhen != null && $doClientSentWhen != '0000-00-00' )) {
						$ds = uformatsqldate2( $doClientSentWhen );
					}
				}

				$this->set( 'docmSent', $ds );
				$ctCode = $tran->get( 'ptClientTran' );

				if (1 <= $ctCode) {
					$clTran = new ClientTransaction( $ctCode );
					$clTot = $clTran->getForHTML( 'ctOriginal' );
					$clBal = $clTran->getForHTML( 'ctBalance' );
					$clWrittenOff = $clTran->get( 'ctWrittenOff' );
					$clPaid = $clTran->get( 'ctPaid' );
					$clPaid += $clWrittenOff;
					$clPaid = uformatmoney( $clPaid );
					$clPaidDate = $clTran->getForHTML( 'ctPaidDate' );
					$clDirectPaidDate = $clTran->getForHTML( 'ctDirectPaidDate' );
				} 
else {
					$clTot = $tran->getForHTML( 'ptClientTotal' );
					$debit = $tran->getForHTML( 'ptDebit' );

					if ($debit == false) {
						$clTot = $tran->get( 'ptClientTotal' );
						$clTot = 0 - $clTot;
						$clTot = uformatmoney( $clTot );
					}

					$clBal = '';
					$clPaid = '';
					$clPaidDate = '';
					$clDirectPaidDate = '';
				}

				$this->set( 'clientTotal', $clTot );
				$this->set( 'clientPaid', $clPaid );
				$this->set( 'clientBalance', $clBal );
				$this->set( 'clientPaidDate', $clPaidDate );
				$this->set( 'clDirectPaidDate', $clDirectPaidDate );

				if ($tran->get( 'ptPostStatus' ) == 'P') {
					$colour = '#EBFFEA';
				} 
else {
					$colour = '#FAE2BB';
				}

				$this->set( 'colour', $colour );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showwhenjustindirect($text) {
			$direct = $this->get( 'direct' );

			if ($direct == 'Y') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenjustdirect($text) {
			$direct = $this->get( 'direct' );

			if ($direct != 'Y') {
				return '';
			}

			$ptClientTotal = $this->get( 'clientTotal' );

			if ($ptClientTotal != 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenfeeshavecommission($text) {
			$plClassOfBus = $this->policy->get( 'plClassOfBus' );

			if ($plClassOfBus < 1) {
				return '';
			}

			$cob = new Cob( $plClassOfBus );
			$cbFeesVatable = $cob->get( 'cbFeesVatable' );

			if ($cbFeesVatable != 1) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenbothdirectandindirect($text) {
			$direct = $this->get( 'direct' );

			if ($direct != 'Y') {
				return '';
			}

			$ptClientTotal = $this->get( 'clientTotal' );

			if ($ptClientTotal == 0) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhenaddonallowed($text) {
			$policy = &$this->policy;

			$ok = false;

			if (isset( $policy )) {
				$x = $policy->get( 'plAltInsCo' );
				$y = trim( $policy->get( 'plAddOnCoverDescription' ) );

				if (( 0 < $x && 0 < strlen( $y ) )) {
					$ok = true;
				}
			}


			if ($ok == false) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwhennoiptamend($text) {
			if ($this->iptAmendable == true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showwheniptamend($text) {
			if ($this->iptAmendable != true) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whencurrentperiod($text) {
			$ppt = $this->get( 'processPeriodType' );

			if ($ppt == '') {
				$ppt = 'C';
			}


			if ($ppt != 'C') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whennextperiod($text) {
			$ppt = $this->get( 'processPeriodType' );

			if ($ppt == '') {
				$ppt = 'C';
			}


			if ($ppt != 'N') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function showtranstypedescription() {
			if (!isset( ->policyTransaction )) {
				return '';
			}

			$pyCode = $this->policyTransaction->get( 'ptTransType' );

			if ($pyCode < 1) {
				return '';
			}

			$type = new PolicyTransactionType( $pyCode );
			$desc = $type->get( 'pyName' );
			return $desc;
		}

		function showperiodselected($type) {
			$ppt = $this->get( 'processPeriodType' );

			if ($ppt == '') {
				$ppt = 'C';
			}


			if ($type == $ppt) {
				return 'selected';
			}

			return '';
		}

		function setspecificfields() {
			global $session;
			global $accountingYear;
			global $accountingPeriod;
			global $periodFrom;
			global $periodTo;
			global $postingAccountingYear;
			global $postingAccountingPeriod;
			global $postingPeriodFrom;
			global $postingPeriodTo;

			$tmpSys = new System( 1 );
			$currentPeriodDesc = $tmpSys->getPeriodDescription(  );

			if (( defined( 'USER_FOR_YEAR_END' ) && isset( $user ) )) {
				$usCode = $user->getKeyValue(  );

				if ($usCode == USER_FOR_YEAR_END) {
					$currentPeriodDesc = fsettoendofyear(  );
				}
			}

			$tmpSys->incrementPeriod( true );
			$nextPeriodDesc = $tmpSys->getPeriodDescription(  );
			$ptt = $this->get( 'processPeriodType' );

			if ($ptt == '') {
				$ptt = 'C';
			}

			$ourSys = new System( 1 );

			if ($ptt == 'C') {
				$postingAccountingYear = $ourSys->getAccountingYear(  );
				$postingAccountingPeriod = $ourSys->getAccountingPeriod(  );
				$postingPeriodFrom = $ourSys->getPeriodFrom(  );
				$postingPeriodTo = $ourSys->getPeriodTo(  );
				$selectedPeriodDesc = $currentPeriodDesc;
				$user = $session->get( 'user' );

				if (( defined( 'USER_FOR_YEAR_END' ) && isset( $user ) )) {
					$usCode = $user->getKeyValue(  );

					if ($usCode == USER_FOR_YEAR_END) {
						$currentPeriodDesc = fsettoendofyear(  );
						$selectedPeriodDesc = $currentPeriodDesc;
					}
				}
			}


			if ($ptt == 'N') {
				$ourSys->incrementPeriod( true );
				$postingAccountingYear = $ourSys->getAccountingYear(  );
				$postingAccountingPeriod = $ourSys->getAccountingPeriod(  );
				$postingPeriodFrom = $ourSys->getPeriodFrom(  );
				$postingPeriodTo = $ourSys->getPeriodTo(  );
				$selectedPeriodDesc = $nextPeriodDesc;
			}

			$this->set( 'currentPeriodDesc', $currentPeriodDesc );
			$this->set( 'nextPeriodDesc', $nextPeriodDesc );
			$this->set( 'selectedPeriodDesc', $selectedPeriodDesc );
		}

		function _setindividualfieldseditable() {
			$policy = &$this->policy;

			if (0 < $policy->get( 'plAltInsCo' )) {
				$this->setFieldAllowEditing( 'ptAddOnGrossIncIPT', true );
				$this->setFieldAllowEditing( 'ptAddOnCommissionRate', true );
				$this->setFieldAllowEditing( 'ptAddOnCommission', true );
				return null;
			}

			$this->setFieldAllowEditing( 'ptAddOnGrossIncIPT', false );
			$this->setFieldAllowEditing( 'ptAddOnCommissionRate', false );
			$this->setFieldAllowEditing( 'ptAddOnCommission', false );
		}
	}

?>