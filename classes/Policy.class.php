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

	class policy {
		var $table = null;
		var $keyField = null;

		function policy($code) {
			$this->keyField = 'plCode';
			$this->table = 'policies';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['plAltInsCo'] = 'INT';
			$this->fieldTypes['plClassOfBus'] = 'INT';
			$this->fieldTypes['plClient'] = 'INT';
			$this->fieldTypes['plClientDisc'] = 'INT';
			$this->fieldTypes['plDirect'] = 'INT';
			$this->fieldTypes['plEnquiryDate'] = 'DATE';
			$this->fieldTypes['plFrequency'] = 'INT';
			$this->fieldTypes['plHandler'] = 'INT';
			$this->fieldTypes['plInceptionDate'] = 'DATE';
			$this->fieldTypes['plInsCo'] = 'INT';
			$this->fieldTypes['plIntrodComm'] = 'INT';
			$this->fieldTypes['plNewBusiness'] = 'INT';
			$this->fieldTypes['plPaymentDate'] = 'DATE';
			$this->fieldTypes['plPaymentMethod'] = 'INT';
			$this->fieldTypes['plPolDocsDue'] = 'INT';
			$this->fieldTypes['plRenewalDate'] = 'DATE';
			$this->fieldTypes['plPrevRenewalDate'] = 'DATE';
			$this->fieldTypes['plSaleMethod'] = 'INT';
			$this->fieldTypes['plSourceOfBus'] = 'INT';
			$this->fieldTypes['plStatus'] = 'INT';
			$this->fieldTypes['plStatusDate'] = 'DATE';
			$this->fieldTypes['plTORDate'] = 'DATE';
			$this->fieldTypes['plDurable'] = 'INT';
			$this->fieldTypes['plDurableDate'] = 'DATE';
			$this->fieldTypes['plBrStatus'] = 'DATE';
			$this->fieldTypes['plGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['plGross'] = 'MONEY';
			$this->fieldTypes['plCommissionRate'] = 'MONEY';
			$this->fieldTypes['plCommission'] = 'MONEY';
			$this->fieldTypes['plNet'] = 'MONEY';
			$this->fieldTypes['plIPTRate'] = 'MONEY';
			$this->fieldTypes['plGrossIPT'] = 'MONEY';
			$this->fieldTypes['plAddlGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['plAddlGross'] = 'MONEY';
			$this->fieldTypes['plAddlCommissionRate'] = 'MONEY';
			$this->fieldTypes['plAddlCommission'] = 'MONEY';
			$this->fieldTypes['plAddlNet'] = 'MONEY';
			$this->fieldTypes['plAddlIPT'] = 'MONEY';
			$this->fieldTypes['plAddOnGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['plAddOnGross'] = 'MONEY';
			$this->fieldTypes['plAddOnCommissionRate'] = 'MONEY';
			$this->fieldTypes['plAddOnCommission'] = 'MONEY';
			$this->fieldTypes['plAddOnNet'] = 'MONEY';
			$this->fieldTypes['plAddOnIPTRate'] = 'MONEY';
			$this->fieldTypes['plAddOnIPT'] = 'MONEY';
			$this->fieldTypes['plClientDiscountRate'] = 'MONEY';
			$this->fieldTypes['plClientDiscount'] = 'MONEY';
			$this->fieldTypes['plEngineeringFee'] = 'MONEY';
			$this->fieldTypes['plEngineeringFeeCommRate'] = 'MONEY';
			$this->fieldTypes['plEngineeringFeeNet'] = 'MONEY';
			$this->fieldTypes['plEngineeringFeeVATRate'] = 'MONEY';
			$this->fieldTypes['plEngineeringFeeVAT'] = 'MONEY';
			$this->fieldTypes['plEngineeringFeeComm'] = 'MONEY';
			$this->fieldTypes['plBrokerFee'] = 'MONEY';
			$this->fieldTypes['plBrokerFeeVATRate'] = 'MONEY';
			$this->fieldTypes['plBrokerFeeVAT'] = 'MONEY';
			$this->fieldTypes['plTotalGross'] = 'MONEY';
			$this->fieldTypes['plTotalGrossIncIPT'] = 'MONEY';
			$this->fieldTypes['plTotalCommission'] = 'MONEY';
			$this->fieldTypes['plTotalNet'] = 'MONEY';
			$this->fieldTypes['plTotalIPT'] = 'MONEY';
			$this->fieldTypes['plClientSubTotal'] = 'MONEY';
			$this->fieldTypes['plClientTotal'] = 'MONEY';
			$this->fieldTypes['plBrokerSubTotal'] = 'MONEY';
			$this->fieldTypes['plInsCoTotal'] = 'MONEY';
			$this->fieldTypes['plAddOnTotal'] = 'MONEY';
			$this->fieldTypes['plIntroducerCommRate'] = 'MONEY';
			$this->fieldTypes['plIntroducerComm'] = 'MONEY';
			$this->fieldTypes['plIntroducerCommRate'] = 'MONEY';
			$this->fieldTypes['plBrokerTotal'] = 'MONEY';
			$this->fieldTypes['plOthers'] = 'MONEY';
			$this->fieldTypes['plPolicyTotal'] = 'MONEY';
			$this->fieldTypes['plTotalInstalmentComm'] = 'MONEY';
			$this->fieldTypes['plDirectBrokerTotal'] = 'MONEY';
			$this->fieldTypes['plDirectClientTotal'] = 'MONEY';
			$this->fieldTypes['plDirectClientGrand'] = 'MONEY';
			$this->_setUpdatedByField( 'plLastUpdateBy' );
			$this->_setUpdatedWhenField( 'plLastUpdateOn' );
			$this->handleConcurrency( true );
			$q = 'SELECT clName, inName, icName, cbName, sbName, psName, cmName, stName, pmName FROM policies
				LEFT JOIN clients on plClient = clCode
				LEFT JOIN introducers on clIntroducer = inCode
				LEFT JOIN insuranceCompanies on plInsco = icCode
				LEFT JOIN classOfBus on plClassOfBus = cbCode
				LEFT JOIN sourceOfBus on plSourceOfBus = sbCode
				LEFT JOIN policySaleMethods on plSaleMethod = psCode
				LEFT JOIN communMethod on plDurable = cmCode
				LEFT JOIN policyStatus on plStatus = stCode
				LEFT JOIN policyPaymentMethods on plPaymentMethod = pmCode
				where plCode = CODE';
			$this->setExtraSql( $q );
			$q = 'SELECT icName as altIcName FROM policies
				LEFT JOIN insuranceCompanies on plAltInsCo = icCode
				where plCode = CODE';
			$this->setExtraSql( $q );
			$q = 'SELECT usFirstName as handlerFirst,  usLastName as handlerLast  FROM policies
				LEFT JOIN users on plHandler = usCode
				where plCode = CODE';
			$this->setExtraSql( $q );
		}

		function recalculateaccountingfields() {
			$plDirect = $this->get( 'plDirect' );

			if ($plDirect == 1) {
				$this->_recalculateAccountingFieldsDirect(  );
				return null;
			}

			$this->_recalculateAccountingFieldsNormal(  );
		}

		function _recalculateaccountingfieldsdirect() {
			$plGrossIncIPT = $this->get( 'plGrossIncIPT' );
			$plIPTRate = $this->get( 'plIPTRate' );
			$plGrossIPT = $this->get( 'plGrossIPT' );
			$plAddlGrossIncIPT = $this->get( 'plAddlGrossIncIPT' );
			$plAddOnIPTRate = $this->get( 'plAddOnIPTRate' );
			$plEngineeringFeeVATRate = $this->get( 'plEngineeringFeeVATRate' );
			$plEngineeringFeeCommRate = $this->get( 'plEngineeringFeeCommRate' );
			$plEngineeringFeeComm = $this->get( 'plEngineeringFeeComm' );
			$plBrokerFeeVATRate = $this->get( 'plBrokerFeeVATRate' );
			$plCommissionRate = $this->get( 'plCommissionRate' );
			$plCommission = $this->get( 'plCommission' );
			$plTotalInstalmentComm = $this->get( 'plTotalInstalmentComm' );
			$plAddlCommissionRate = $this->get( 'plAddlCommissionRate' );
			$plAddlCommission = $this->get( 'plAddlCommission' );
			$plAddOnCommissionRate = $this->get( 'plAddOnCommissionRate' );
			$plAddOnCommission = $this->get( 'plAddOnCommission' );
			$plAddlIPT = $this->get( 'plAddlIPT' );
			$plClientDiscountRate = $this->get( 'plClientDiscountRate' );
			$plIntroducerCommRate = $this->get( 'plIntroducerCommRate' );
			$ok = false;
			$x = $this->get( 'plClassOfBus' );

			if (0 < $x) {
				$cob = new Cob( $x );
				$cbAllowIPTAmend = $cob->get( 'cbAllowIPTAmend' );

				if ($cbAllowIPTAmend == 1) {
					$ok = true;
				}
			}

			$iptAmendable = $ok;

			if (( $iptAmendable == true && $plIPTRate == 0 )) {
				$plGross = $plGrossIncIPT - $plGrossIPT;
				$this->plGross = $plGross;
			} 
else {
				$plGross = ucalcinclusiveusingrate( $plGrossIncIPT, $plIPTRate );
				$this->plGross = $plGross;
				$plGrossIPT = $plGrossIncIPT - $plGross;
			}

			$this->plGrossIPT = $plGrossIPT;

			if (0 < $plCommissionRate) {
				$plCommission = ucalcusingrate( $plGross, $plCommissionRate );
				$this->plCommission = $plCommission;
			}

			$plNet = $plGross - $plCommission;
			$this->plNet = $plNet;

			if (( $iptAmendable == true && $plIPTRate == 0 )) {
				$plAddlGross = $plAddlGrossIncIPT - $plAddlIPT;
				$this->plAddlGross = $plAddlGross;
			} 
else {
				$plAddlGross = ucalcinclusiveusingrate( $plAddlGrossIncIPT, $plIPTRate );
				$this->plAddlGross = $plAddlGross;
				$plAddlIPT = $plAddlGrossIncIPT - $plAddlGross;
				$this->plAddlIPT = $plAddlIPT;
			}


			if (0 < $plAddlCommissionRate) {
				$plAddlCommission = ucalcusingrate( $plAddlGross, $plAddlCommissionRate );
				$this->plAddlCommission = $plAddlCommission;
			}

			$plAddlNet = $plAddlGross - $plAddlCommission;
			$this->plAddlNet = $plAddlNet;
			$plTotalGross = $plGross + $plAddlGross;
			$this->plTotalGross = $plTotalGross;
			$plTotalGrossIncIPT = $plGrossIncIPT + $plAddlGrossIncIPT;
			$this->plTotalGrossIncIPT = $plTotalGrossIncIPT;
			$plTotalIPT = $plGrossIPT + $plAddlIPT;
			$this->plTotalIPT = $plTotalIPT;
			$plTotalCommission = $plCommission + $plAddlCommission;
			$this->plTotalCommission = $plTotalCommission;
			$plTotalNet = $plNet + $plAddlNet;
			$this->plTotalNet = $plTotalNet;
			$plAddOnGrossIncIPT = $this->get( 'plAddOnGrossIncIPT' );
			$plAddOnGross = ucalcinclusiveusingrate( $plAddOnGrossIncIPT, $plAddOnIPTRate );
			$this->plAddOnGross = $plAddOnGross;
			$plAddOnIPT = $plAddOnGrossIncIPT - $plAddOnGross;
			$this->plAddOnIPT = $plAddOnIPT;

			if (0 < $plAddOnCommissionRate) {
				$plAddOnCommissionRate = $this->get( 'plAddOnCommissionRate' );
				$plAddOnCommission = ucalcusingrate( $plAddOnGross, $plAddOnCommissionRate );
				$this->plAddOnCommission = $plAddOnCommission;
			}

			$plAddOnNet = $plAddOnGross - $plAddOnCommission;
			$this->plAddOnNet = $plAddOnNet;
			$plAddnPremium = $plAddOnGross + $plAddOnIPT;
			$this->plAddnPremium = $plAddnPremium;
			$plEngineeringFee = $this->get( 'plEngineeringFee' );
			$plEngineeringFeeCommRate = $this->get( 'plEngineeringFeeCommRate' );

			if (0 < $plEngineeringFeeCommRate) {
				$plEngineeringFeeComm = ucalcusingrate( $plEngineeringFee, $plEngineeringFeeCommRate );
				$this->plEngineeringFeeComm = $plEngineeringFeeComm;
			}

			$plEngineeringFeeNet = $plEngineeringFee - $plEngineeringFeeComm;
			$this->plEngineeringFeeNet = $plEngineeringFeeNet;
			$plEngineeringFeeVATRate = $this->get( 'plEngineeringFeeVATRate' );
			$this->plEngineeringFeeVATRate = $plEngineeringFeeVATRate;
			$plEngineeringFeeVAT = ucalcusingrate( $plEngineeringFee, $plEngineeringFeeVATRate );
			$this->plEngineeringFeeVAT = $plEngineeringFeeVAT;
			$plClientDiscountRate = $this->get( 'plClientDiscountRate' );
			$plClientDiscount = $this->get( 'plClientDiscount' );

			if (0 < $plClientDiscountRate) {
				$plClientDiscount = ucalcusingrate( $plCommission + $plAddlCommission + $plAddOnCommission + $plEngineeringFeeComm, $plClientDiscountRate );
				$this->plClientDiscount = $plClientDiscount;
			}

			$plIntroducerCommRate = $this->get( 'plIntroducerCommRate' );
			$plIntroducerComm = $this->get( 'plIntroducerComm' );

			if (0 < $plIntroducerCommRate) {
				$plIntroducerComm = ucalcusingrate( $plGross, $plIntroducerCommRate );
				$this->plIntroducerComm = $plIntroducerComm;
			}

			$plBrokerFee = $this->get( 'plBrokerFee' );
			$plBrokerFeeVATRate = $this->get( 'plBrokerFeeVATRate' );
			$plBrokerFeeVAT = ucalcusingrate( $plBrokerFee, $plBrokerFeeVATRate );
			$this->plBrokerFeeVAT = $plBrokerFeeVAT;
			$plClientSubTotal = $plAddOnGross + $plAddOnIPT + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plClientSubTotal = $plClientSubTotal;
			$plTotalPremium = $plClientSubTotal;
			$this->plTotalPremium = $plTotalPremium;
			$plBrokerSubTotal = $plAddOnCommission + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plBrokerSubTotal = $plBrokerSubTotal;
			$plBrokerTotal = $plBrokerSubTotal - $plIntroducerComm - $plBrokerFeeVAT;
			$this->plBrokerTotal = $plBrokerTotal;
			$plInsCoTotal = 0;
			$this->plInsCoTotal = $plInsCoTotal;
			$plTotalInstalmentComm = $plTotalCommission + $plEngineeringFeeComm;
			$this->plTotalInstalmentComm = $plTotalInstalmentComm;
			$plAddOnTotal = $plAddOnNet + $plAddOnIPT;
			$this->plAddOnTotal = $plAddOnTotal;
			$plOthers = 0 + $plEngineeringFee + $plEngineeringFeeVAT - $plClientDiscount + $plBrokerFee + $plBrokerFeeVAT;
			$this->plOthers = $plOthers;
			$plPolicyTotal = $plGross + $plGrossIPT + $plAddlGross + $plAddlIPT + $plAddOnGross + $plAddOnIPT + $plEngineeringFee + $plEngineeringFeeVAT + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plPolicyTotal = $plPolicyTotal;
			$plDirectBrokerTotal = $plTotalCommission + $plBrokerTotal;
			$this->plDirectBrokerTotal = $plDirectBrokerTotal;
			$plDirectClientTotal = $plTotalGross + $plTotalIPT + $plEngineeringFee + $plEngineeringFeeVAT;
			$this->plDirectClientTotal = $plDirectClientTotal;
			$plDirectClientGrand = $plClientSubTotal + $plDirectClientTotal;
			$this->plDirectClientGrand = $plDirectClientGrand;
			$this->plClientTotal = $plDirectClientGrand;
		}

		function _recalculateaccountingfieldsnormal() {
			$plIPTRate = $this->get( 'plIPTRate' );
			$plGrossIPT = $this->get( 'plGrossIPT' );
			$plIPTRate = $this->get( 'plIPTRate' );
			$plAddOnIPTRate = $this->get( 'plAddOnIPTRate' );
			$plEngineeringFeeVATRate = $this->get( 'plEngineeringFeeVATRate' );
			$plEngineeringFeeCommRate = $this->get( 'plEngineeringFeeCommRate' );
			$plEngineeringFeeComm = $this->get( 'plEngineeringFeeComm' );
			$plBrokerFeeVATRate = $this->get( 'plBrokerFeeVATRate' );
			$plCommissionRate = $this->get( 'plCommissionRate' );
			$plCommission = $this->get( 'plCommission' );
			$plAddlCommissionRate = $this->get( 'plAddlCommissionRate' );
			$plAddlCommission = $this->get( 'plAddlCommission' );
			$plAddlIPT = $this->get( 'plAddlIPT' );
			$plAddOnCommissionRate = $this->get( 'plAddOnCommissionRate' );
			$plAddOnCommission = $this->get( 'plAddOnCommission' );
			$plClientDiscountRate = $this->get( 'plClientDiscountRate' );
			$plIntroducerCommRate = $this->get( 'plIntroducerCommRate' );
			$ok = false;
			$x = $this->get( 'plClassOfBus' );

			if (0 < $x) {
				$cob = new Cob( $x );
				$cbAllowIPTAmend = $cob->get( 'cbAllowIPTAmend' );

				if ($cbAllowIPTAmend == 1) {
					$ok = true;
				}
			}

			$iptAmendable = $ok;
			$plGrossIncIPT = $this->get( 'plGrossIncIPT' );

			if (( $iptAmendable == true && $plIPTRate == 0 )) {
				$plGross = $plGrossIncIPT - $plGrossIPT;
				$this->plGross = $plGross;
			} 
else {
				$plGross = ucalcinclusiveusingrate( $plGrossIncIPT, $plIPTRate );
				$this->plGross = $plGross;
			}

			$plGrossIPT = $plGrossIncIPT - $plGross;
			$this->plGrossIPT = $plGrossIPT;

			if (0 < $plCommissionRate) {
				$plCommission = ucalcusingrate( $plGross, $plCommissionRate );
				$this->plCommission = $plCommission;
			}

			$plNet = $plGross - $plCommission;
			$this->plNet = $plNet;
			$plAddlGrossIncIPT = $this->get( 'plAddlGrossIncIPT' );

			if (( $iptAmendable == true && $plIPTRate == 0 )) {
				$plAddlGross = $plAddlGrossIncIPT - $plAddlIPT;
				$this->plAddlGross = $plAddlGross;
			} 
else {
				$plAddlGross = ucalcinclusiveusingrate( $plAddlGrossIncIPT, $plIPTRate );
				$this->plAddlGross = $plAddlGross;
				$plAddlIPT = $plAddlGrossIncIPT - $plAddlGross;
				$this->plAddlIPT = $plAddlIPT;
			}


			if (0 < $plAddlCommissionRate) {
				$plAddlCommission = ucalcusingrate( $plAddlGross, $plAddlCommissionRate );
				$this->plAddlCommission = $plAddlCommission;
			}

			$plAddlNet = $plAddlGross - $plAddlCommission;
			$this->plAddlNet = $plAddlNet;
			$plTotalGross = $plGross + $plAddlGross;
			$this->plTotalGross = $plTotalGross;
			$plTotalGrossIncIPT = $plGrossIncIPT + $plAddlGrossIncIPT;
			$this->plTotalGrossIncIPT = $plTotalGrossIncIPT;
			$plTotalIPT = $plGrossIPT + $plAddlIPT;
			$this->plTotalIPT = $plTotalIPT;
			$plTotalCommission = $plCommission + $plAddlCommission;
			$this->plTotalCommission = $plTotalCommission;
			$plTotalNet = $plNet + $plAddlNet;
			$this->plTotalNet = $plTotalNet;
			$plAddOnGrossIncIPT = $this->get( 'plAddOnGrossIncIPT' );
			$plAddOnGross = ucalcinclusiveusingrate( $plAddOnGrossIncIPT, $plAddOnIPTRate );
			$this->plAddOnGross = $plAddOnGross;
			$plAddOnIPT = $plAddOnGrossIncIPT - $plAddOnGross;
			$this->plAddOnIPT = $plAddOnIPT;

			if (0 < $plAddOnCommissionRate) {
				$plAddOnCommissionRate = $this->get( 'plAddOnCommissionRate' );
				$plAddOnCommission = ucalcusingrate( $plAddOnGross, $plAddOnCommissionRate );
				$this->plAddOnCommission = $plAddOnCommission;
			}

			$plAddOnNet = $plAddOnGross - $plAddOnCommission;
			$this->plAddOnNet = $plAddOnNet;
			$plAddnPremium = $plAddOnGross + $plAddOnIPT;
			$this->plAddnPremium = $plAddnPremium;
			$plEngineeringFee = $this->get( 'plEngineeringFee' );
			$plEngineeringFeeCommRate = $this->get( 'plEngineeringFeeCommRate' );

			if (0 < $plEngineeringFeeCommRate) {
				$plEngineeringFeeComm = ucalcusingrate( $plEngineeringFee, $plEngineeringFeeCommRate );
				$this->plEngineeringFeeComm = $plEngineeringFeeComm;
			}

			$plEngineeringFeeNet = $plEngineeringFee - $plEngineeringFeeComm;
			$this->plEngineeringFeeNet = $plEngineeringFeeNet;
			$plEngineeringFeeVATRate = $this->get( 'plEngineeringFeeVATRate' );
			$this->plEngineeringFeeVATRate = $plEngineeringFeeVATRate;
			$plEngineeringFeeVAT = ucalcusingrate( $plEngineeringFee, $plEngineeringFeeVATRate );
			$this->plEngineeringFeeVAT = $plEngineeringFeeVAT;
			$plClientDiscountRate = $this->get( 'plClientDiscountRate' );
			$plClientDiscount = $this->get( 'plClientDiscount' );

			if (0 < $plClientDiscountRate) {
				$plClientDiscount = ucalcusingrate( $plCommission + $plAddOnCommission + $plEngineeringFeeComm, $plClientDiscountRate );
				$this->plClientDiscount = $plClientDiscount;
			}

			$plIntroducerCommRate = $this->get( 'plIntroducerCommRate' );
			$plIntroducerComm = $this->get( 'plIntroducerComm' );

			if (0 < $plIntroducerCommRate) {
				$plIntroducerComm = ucalcusingrate( $plGross, $plIntroducerCommRate );
				$this->plIntroducerComm = $plIntroducerComm;
			}

			$plBrokerFee = $this->get( 'plBrokerFee' );
			$plBrokerFeeVATRate = $this->get( 'plBrokerFeeVATRate' );
			$plBrokerFeeVAT = ucalcusingrate( $plBrokerFee, $plBrokerFeeVATRate );
			$this->plBrokerFeeVAT = $plBrokerFeeVAT;
			$plClientTotal = $plGross + $plGrossIPT + $plAddlGross + $plAddlIPT + $plAddOnGross + $plAddOnIPT + $plEngineeringFee + $plEngineeringFeeVAT + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plClientTotal = $plClientTotal;
			$plTotalPremium = $plClientTotal;
			$this->plTotalPremium = $plTotalPremium;
			$plBrokerSubTotal = $plCommission + $plAddOnCommission + $plAddlCommission + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plBrokerSubTotal = $plBrokerSubTotal;
			$plBrokerTotal = $plBrokerSubTotal - $plIntroducerComm - $plBrokerFeeVAT;
			$this->plBrokerTotal = $plBrokerTotal;
			$plInsCoTotal = $plNet + $plGrossIPT + $plAddlNet + $plAddlIPT + $plEngineeringFeeNet + $plEngineeringFeeVAT;
			$this->plInsCoTotal = $plInsCoTotal;
			$plAddOnTotal = $plAddOnNet + $plAddOnIPT;
			$this->plAddOnTotal = $plAddOnTotal;
			$plOthers = $plEngineeringFee + $plEngineeringFeeVAT - $plClientDiscount + $plBrokerFee + $plBrokerFeeVAT;
			$this->plOthers = $plOthers;
			$plPolicyTotal = $plGross + $plGrossIPT + $plAddlGross + $plAddlIPT + $plAddOnGross + $plAddOnIPT + $plEngineeringFee + $plEngineeringFeeVAT + $plBrokerFee + $plBrokerFeeVAT - $plClientDiscount;
			$this->plPolicyTotal = $plPolicyTotal;
		}

		function decideiptandvatrates() {
			global $iptNormalRate;
			global $iptTravelRate;
			global $companyVATRate;

			$cbCode = $this->get( 'plClassOfBus' );

			if (0 < $cbCode) {
				$cob = new Cob( $cbCode );
			} 
else {
				$cob = null;
			}

			$clCode = $this->get( 'plClient' );

			if ($clCode < 1) {
				return null;
			}

			$client = new Client( $clCode );
			$paysIPT = $client->paysIPT(  );

			if ($paysIPT == false) {
				$iptRate = 0;
			} 
else {
				if ($cob != null) {
					$isTravel = $cob->isThisTravel(  );

					if ($isTravel == true) {
						$iptRate = $iptTravelRate;
					} 
else {
						$iptRate = $iptNormalRate;
					}

					$isZeroRated = $cob->isIPTZeroRated(  );

					if ($isZeroRated == 1) {
						$iptRate = 0;
					}
				} 
else {
					$iptRate = $iptNormalRate;
				}
			}

			$clCode = $this->get( 'plClient' );

			if (0 < $clCode) {
				$cl = new Client( $clCode );
				$x = $cl->paysIPT(  );

				if ($x == 0) {
					$iptRate = 0;
				}
			}

			$vatRate = $companyVATRate;

			if ($cob != null) {
				$vatable = $cob->get( 'cbFeesVatable' );

				if ($vatable == 0) {
					$vatRate = 0;
				}
			}

			$this->set( 'plEngineeringFeeVATRate', $vatRate );
			$this->set( 'plIPTRate', $iptRate );
			$this->set( 'plAddOnIPTRate', $iptRate );
		}

		function setnewdefaults($client, $type) {
			global $session;
			global $brokerVATRate;
			global $companyVATRate;
			global $iptNormalRate;
			global $iptTravelRate;

			$plClient = $client->getKeyValue(  );
			$this->set( 'plClient', $plClient );
			$this->set( 'plDirect', 0 );
			$this->set( 'plFrequency', 0 );
			$this->set( 'plStatus', 2 );

			if ($client->paysIPT(  ) == true) {
				$iptRate = $iptNormalRate;
			} 
else {
				$iptRate = 0;
			}

			$this->set( 'plIPTRate', $iptRate );
			$this->set( 'plAddOnIPTRate', $iptRate );
			$this->set( 'plEngineeringFeeVATRate', $companyVATRate );
			$this->set( 'plBrokerFeeVATRate', $brokerVATRate );
			$this->set( 'plPolicyType', $type );
			$clSourceOfBus = $client->get( 'clSourceOfBus' );
			$this->set( 'plSourceOfBus', $clSourceOfBus );
			$clHandler = $client->get( 'clHandler' );
			$this->set( 'plHandler', $clHandler );
			$clNewBusiness = $client->get( 'clNewBusiness' );

			if ($clNewBusiness != 0 - 1) {
				$this->set( 'plNewBusiness', $clNewBusiness );
			} 
else {
				$this->set( 'plNewBusiness', 1 );
			}

			$clClientSince = $client->getForHTML( 'clClientSince' );

			if (( $clClientSince != null && $clClientSince != '' )) {
				$this->set( 'plEnquiryDate', $clClientSince );
			} 
else {
				$this->set( 'plEnquiryDate', ugettoday(  ) );
			}

			$this->set( 'plStatus', 2 );
			$this->set( 'plStatusDate', ugettoday(  ) );
			$this->set( 'plDurable', $client->get( 'clDurable' ) );
			$this->set( 'plDurableDate', $client->get( 'clDurableDate' ) );
			$this->set( 'plBrStatus', $client->get( 'clBrStatus' ) );
			$this->set( 'plClientDisc', $client->get( 'clDiscount' ) );

			if (0 < $client->get( 'clIntroducer' )) {
				$x = 1;
			} 
else {
				$x = 0 - 1;
			}

			$this->set( 'plIntrodComm', $x );
		}

		function isiptamendable() {
			$iptAmendable = false;
			$icCode = $this->get( 'plInsCo' );

			if (0 < $icCode) {
				$ins = new Insco( $icCode );
				$icIPTAmendable = $ins->get( 'icIPTAmendable' );

				if ($icIPTAmendable == 1) {
					$iptAmendable = true;
				}
			}

			$cbAllowIPTAmend = false;
			$cbCode = $this->get( 'plClassOfBus' );

			if (0 < $cbCode) {
				$cob = new Cob( $cbCode );
				$cbAllowIPTAmend = $cob->get( 'cbAllowIPTAmend' );
			}


			if ($iptAmendable == false) {
				$iptAmendable = $cbAllowIPTAmend;
			}

			return $iptAmendable;
		}
	}

?>