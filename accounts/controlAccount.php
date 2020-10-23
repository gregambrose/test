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

	/**
*	show details for a specific cell
* 	@param 	cell name
*/
	function _showCell($template, $cell) {
		if ($cell == '') {
			trigger_error(  . 'no cell ' . $cell, 256 );
		}

		$c = $template->getField( $cell );

		if ($c == null) {
			trigger_error(  . $cell . ' cell not made yet', 256 );
		}

		$template->setDetailObject( $c );
		return false;
	}

	/**
 * This makes all the figurs from the summary page
 *
 */
	function _makeTotals() {
		global $controlFromDate;
		global $controlToDate;
		global $fields;

		$START = $controlFromDate;
		$END = $controlToDate;
		$fields = array(  );
		$clPrem = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)', false, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDirect = 1
			AND   ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPrem->addSelect( false, (  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT,
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDirect != 1
			AND   ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPrem->addSelect( true, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPrem->addSelect( true, (  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT,
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDirect != 1
			AND   ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$fields['clPrem'] = &$clPrem;

		$clBrFees = new ControlAccountTotal( 'Broker Fees including VAT', false, (  . 'SELECT
				SUM(ptBrokerFee) 		as ptBrokerFee,
				SUM(ptBrokerFeeVAT) 	as ptBrokerFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clBrFees->addSelect( true, (  . 'SELECT
				SUM(ptBrokerFee) 		as ptBrokerFee,
				SUM(ptBrokerFeeVAT) 	as ptBrokerFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$fields['clBrFees'] = &$clBrFees;

		$clRecPay = new ControlAccountTotal( 'Receipts from and cash paid to clients', false, (  . 'SELECT
				SUM(ctOriginal) 		as ctOriginal
			FROM clientTransactions
			WHERE ctTransType = \'C\'
			AND   ctPostingDate >= \'' . $START . '\' AND ctPostingDate <= \'' . $END . '\'' ) );
		$fields['clRecPay'] = &$clRecPay;

		$clDisc = new ControlAccountTotal( 'Client discounts given', true, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clDisc->addSelect( false, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$fields['clDisc'] = &$clDisc;

		$clWrOff = new ControlAccountTotal( 'Client write offs on cash receipts', true, (  . 'SELECT
				SUM(caAmount) 		as caAmount
			FROM clientTransAllocations
			WHERE caType = \'W\'
			AND   caPostingDate >= \'' . $START . '\' AND caPostingDate <= \'' . $END . '\'' ) );
		$fields['clWrOff'] = &$clWrOff;

		$clTotal = new ControlAccountTotal( 'Client total', false, null );
		$clPrem->_calcTotals(  );
		$clBrFees->_calcTotals(  );
		$clRecPay->_calcTotals(  );
		$clDisc->_calcTotals(  );
		$clWrOff->_calcTotals(  );
		$tot = $clPrem->get(  ) + $clBrFees->get(  ) + $clRecPay->get(  ) + $clDisc->get(  ) + $clWrOff->get(  );
		$clTotal->setAmount( $tot );
		$fields['clTotal'] = &$clTotal;

		$icPrem = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)', true, (  . 'SELECT
					
				SUM(itNet) 				as itNet,
				SUM(itGrossIPT) 		as itGrossIPT,

				SUM(itAddlNet) 			as itAddlNet,
				SUM(itAddlIPT) 			as itAddlIPT,

				SUM(itEngineeringFeeNet) 	as itEngineeringFeeNet,
				SUM(itEngineeringFeeVAT)as itEngineeringFeeVAT

			FROM inscoTransactions
			WHERE itDirect != 1
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$fields['icPrem'] = &$icPrem;

		$icDrComm = new ControlAccountTotal( 'Commission on direct policies', false, (  . 'SELECT
				SUM(itCommission) 		as itCommission,
				SUM(itAddlCommission) 	as itAddlCommission,
				SUM(itEngineeringFeeComm) as itEngineeringFeeComm

			FROM inscoTransactions
			WHERE  itDirect = 1
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$fields['icDrComm'] = &$icDrComm;

		$icRecPay = new ControlAccountTotal( 'payments to and receipts from insurance companies', true, (  . 'SELECT
				SUM(itOriginal) 		as itOriginal

			FROM inscoTransactions
			WHERE (itTransType = \'C\' OR itTransType = \'R\')
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$fields['icRecPay'] = &$icRecPay;

		$icWrOff = new ControlAccountTotal( 'insurance company adjustments', false, (  . 'SELECT
				SUM(itWrittenOff) 		as itWrittenOff
			FROM inscoTransactions
			WHERE itTransType = \'I\'
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$fields['icWrOff'] = &$icWrOff;

		$icTotal = new ControlAccountTotal( 'Insurance company total', false, null );
		$tot = $icPrem->get(  ) + $icDrComm->get(  ) + $icRecPay->get(  ) + $icWrOff->get(  );
		$icTotal->setAmount( $tot );
		$fields['icTotal'] = &$icTotal;

		$cpPrem = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)', true, (  . 'SELECT
				SUM(ptAddOnCommission) 		as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPrem->addSelect( true, (  . 'SELECT
				SUM(ptCommission) 			as ptCommission,
				SUM(ptAddlCommission) 		as ptAddlCommission,

				SUM(ptAddOnCommission) 		as ptAddOnCommission,
				SUM(ptEngineeringFeeComm) 	as ptEngineeringFeeComm

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPrem->addSelect( false, (  . 'SELECT
				SUM(ptAddOnCommission) 		as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPrem->addSelect( false, (  . 'SELECT
				SUM(ptCommission) 			as ptCommission,
				SUM(ptAddlCommission) 		as ptAddlCommission,

				SUM(ptAddOnCommission) 		as ptAddOnCommission,
				SUM(ptEngineeringFeeComm) 	as ptEngineeringFeeComm

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$fields['cpPrem'] = &$cpPrem;

		$cpDrComm = new ControlAccountTotal( 'Commission on direct policies', false, null );
		$amt = 0 - $icDrComm->get(  );
		$cpDrComm->setAmount( $amt );
		$fields['cpDrComm'] = &$cpDrComm;

		$cpCommPaid = new ControlAccountTotal( 'commission on items fully paid to insurance companies', false, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   itBalance = 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$fields['cpCommPaid'] = &$cpCommPaid;

		$cpTotal = new ControlAccountTotal( 'Commission posted total', false, null );
		$tot = $cpPrem->get(  ) + $cpDrComm->get(  ) + $cpCommPaid->get(  );
		$cpTotal->setAmount( $tot );
		$fields['cpTotal'] = &$cpTotal;

		$fpBrFees = new ControlAccountTotal( 'Broker Fees', false, null );
		$amt = 0 - $clBrFees->get(  );
		$fpBrFees->setAmount( $amt );
		$fields['fpBrFees'] = &$fpBrFees;

		$fpBrFeesPaid = new ControlAccountTotal( 'broker fees on trans where the client has fully paid', false, (  . 'SELECT
				SUM(ptBrokerFee) 	as ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ptClientTran=ctCode
			AND	  ptDebit = 1
			AND   ctBalance = 0
			AND	  ctPaidDate >= \'' . $START . '\' AND ctPaidDate <= \'' . $END . '\'' ) );
		$fpBrFeesPaid->addSelect( true, (  . 'SELECT
				SUM(ptBrokerFee) 	as ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ptClientTran=ctCode
			AND	  ptDebit != 1
			AND   ctBalance = 0
			AND	  ctPaidDate >= \'' . $START . '\' AND ctPaidDate <= \'' . $END . '\'' ) );
		$fields['fpBrFeesPaid'] = &$fpBrFeesPaid;

		$fpTotal = new ControlAccountTotal( 'Fees Posted total', false, null );
		$tot = $fpBrFees->get(  ) + $fpBrFeesPaid->get(  );
		$fpTotal->setAmount( $tot );
		$fields['fpTotal'] = &$fpTotal;

		$inRecPay = new ControlAccountTotal( 'Introducers payments and receipts', true, (  . 'SELECT
				SUM(rtOriginal) 			as rtOriginal
			FROM introducerTransactions
			WHERE ( rtTransType = \'C\' OR rtTransType = \'R\')
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$fields['inRecPay'] = &$inRecPay;

		$inCommPost = new ControlAccountTotal( 'Introducer Commission posted)', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPost->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$fields['inCommPost'] = &$inCommPost;

		$inWrOff = new ControlAccountTotal( 'Introducers written off', false, (  . 'SELECT
				SUM(rtWrittenOff) 			as rtWrittenOff
			FROM introducerTransactions
			WHERE ( rtTransType = \'C\' OR rtTransType = \'R\')
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$fields['inWrOff'] = &$inWrOff;

		$inTotal = new ControlAccountTotal( 'Introducers total', false, null );
		$tot = $inRecPay->get(  );
		$tot += $inCommPost->get(  );
		$tot += $inWrOff->get(  );
		$inTotal->setAmount( $tot );
		$fields['inTotal'] = &$inTotal;

		$prDisc = new ControlAccountTotal( 'client discount posted', false, null );
		$tot = 0 - $clDisc->get(  );
		$prDisc->setAmount( $tot );
		$fields['prDisc'] = &$prDisc;

		$prCommPost = new ControlAccountTotal( 'introducer commission posted', false, null );
		$tot = 0 - $inCommPost->get(  );
		$prCommPost->setAmount( $tot );
		$fields['prCommPost'] = &$prCommPost;

		$prWrOff = new ControlAccountTotal( 'ins co written off', false, null );
		$tot = 0 - $icWrOff->get(  );
		$prWrOff->setAmount( $tot );
		$fields['prWrOff'] = &$prWrOff;

		$prCommPaid = new ControlAccountTotal( 'introducer commission paid', false, null );
		$tot = 0 - $cpCommPaid->get(  );
		$prCommPaid->setAmount( $tot );
		$fields['prCommPaid'] = &$prCommPaid;

		$prCommTransferred = new ControlAccountTotal( 'Commission transferred)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 1
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['prCommTransferred'] = &$prCommTransferred;

		$prTotal = new ControlAccountTotal( 'Commission paid/reconciled total', false, null );
		$tot = $prDisc->get(  ) + $prCommPost->get(  ) + $prCommPaid->get(  ) + $prWrOff->get(  ) + $prCommTransferred->get(  );
		$prTotal->setAmount( $tot );
		$fields['prTotal'] = &$prTotal;

		$pfBrFeesPaid = new ControlAccountTotal( 'broker fees', false, null );
		$tot = 0 - $fpBrFeesPaid->get(  );
		$pfBrFeesPaid->setAmount( $tot );
		$fields['pfBrFeesPaid'] = &$pfBrFeesPaid;

		$prFeesTransferred = new ControlAccountTotal( 'Broker fees transferred)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 2
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['prFeesTransferred'] = &$prFeesTransferred;

		$pfTotal = new ControlAccountTotal( 'Broker fee total', false, null );
		$tot = $pfBrFeesPaid->get(  );
		$tot += $prFeesTransferred->get(  );
		$pfTotal->setAmount( $tot );
		$fields['pfTotal'] = &$pfTotal;

		$oiMiscReceipts = new ControlAccountTotal( 'Misc. Receipts)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE (baType = 8)
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['oiMiscReceipts'] = &$oiMiscReceipts;

		$oiBankInterest = new ControlAccountTotal( 'Bank Interest', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 3
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['oiBankInterest'] = &$oiBankInterest;

		$oiOtherIncomeTransferred = new ControlAccountTotal( 'Other Net Income Transferred)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE (baType = 9 OR baType = 18 OR baType = 20)
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['oiOtherIncomeTransferred'] = &$oiOtherIncomeTransferred;

		$oiTotal = new ControlAccountTotal( 'Other income total', false, null );
		$tot = 0;
		$tot += $oiBankInterest->get(  );
		$tot += $oiMiscReceipts->get(  );
		$tot += $oiOtherIncomeTransferred->get(  );
		$oiTotal->setAmount( $tot );
		$fields['oiTotal'] = &$oiTotal;

		$ocJournals = new ControlAccountTotal( 'Journals)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE (baType = 11 OR baType = 12)
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['ocJournals'] = &$ocJournals;

		$ocWrOff = new ControlAccountTotal( 'client write off', false, null );
		$tot = 0 - $clWrOff->get(  );
		$tot -= $inWrOff->get(  );
		$ocWrOff->setAmount( $tot );
		$fields['ocWrOff'] = &$ocWrOff;

		$ocBankCharges = new ControlAccountTotal( 'Bank Charges', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 4
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['ocBankCharges'] = &$ocBankCharges;

		$ocOtherChargesTransferred = new ControlAccountTotal( 'Other Net Charges Transferred)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE (baType = 10 OR
			baType = 7 OR
			baType = 6 OR
			baType = 5 OR
			baType = 17 OR
			baType = 19
			)
			
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$fields['ocOtherChargesTransferred'] = &$ocOtherChargesTransferred;

		$ocTotal = new ControlAccountTotal( 'Other income total', false, null );
		$tot = $ocJournals->get(  );
		$tot += $ocWrOff->get(  );
		$tot += $ocBankCharges->get(  );
		$tot += $ocOtherChargesTransferred->get(  );
		$ocTotal->setAmount( $tot );
		$fields['ocTotal'] = &$ocTotal;

		$ibaReceiptsPayments = new ControlAccountTotal( 'IBA receipts and payments', false, null );
		$clRec = $clRecPay->get(  );
		$icRec = $icRecPay->get(  );
		$inRec = $inRecPay->get(  );
		$tot = $clRec + $icRec + $inRec;
		$ibaReceiptsPayments->setAmount( 0 - $tot );
		$fields['ibaReceiptsPayments'] = &$ibaReceiptsPayments;

		$ibaJournals = new ControlAccountTotal( 'client write off', false, null );
		$ocJnl = $ocJournals->get(  );
		$tot = 0 - $ocJnl;
		$ibaJournals->setAmount( $tot );
		$fields['ibaJournals'] = &$ibaJournals;

		$ibaBankInterest = new ControlAccountTotal( 'Bank Interest', true, null );
		$tot = 0 - $oiBankInterest->get(  );
		$ibaBankInterest->setAmount( $tot );
		$fields['ibaBankInterest'] = &$ibaBankInterest;

		$ibaMiscReceipts = new ControlAccountTotal( 'Misc Income', true, null );
		$oiMiscReceipts = $fields['oiMiscReceipts'];
		$tot = 0 - $oiMiscReceipts->get(  );
		$ibaMiscReceipts->setAmount( $tot );
		$fields['ibaMiscReceipts'] = &$ibaMiscReceipts;

		$ibaBankCharges = new ControlAccountTotal( 'Bank Charges', true, null );
		$tot = 0 - $ocBankCharges->get(  );
		$ibaBankCharges->setAmount( $tot );
		$fields['ibaBankCharges'] = &$ibaBankCharges;

		$ibaCommTransferred = new ControlAccountTotal( 'Earned commission transferred', true, null );
		$tot = 0 - $prCommTransferred->get(  );
		$ibaCommTransferred->setAmount( $tot );
		$fields['ibaCommTransferred'] = &$ibaCommTransferred;

		$ibaFeesTransferred = new ControlAccountTotal( 'Earned broker feestransferred', true, null );
		$tot = 0 - $prFeesTransferred->get(  );
		$ibaFeesTransferred->setAmount( $tot );
		$fields['ibaFeesTransferred'] = &$ibaFeesTransferred;

		$ibaOtherIncomeTransferred = new ControlAccountTotal( 'Other earned income transferred', true, null );
		$tot = 0 - $oiOtherIncomeTransferred->get(  );
		$ibaOtherIncomeTransferred->setAmount( $tot );
		$fields['ibaOtherIncomeTransferred'] = &$ibaOtherIncomeTransferred;

		$ibaOtherChargesTransferred = new ControlAccountTotal( 'Other charges transferred', true, null );
		$tot = 0 - $ocOtherChargesTransferred->get(  );
		$ibaOtherChargesTransferred->setAmount( $tot );
		$fields['ibaOtherChargesTransferred'] = &$ibaOtherChargesTransferred;

		$ibaOtherIncome = new ControlAccountTotal( 'Other income', true, null );
		$oiMiscReceipts = $fields['oiMiscReceipts'];
		$tot = 0 - $oiMiscReceipts->get(  );
		$ibaOtherIncome->setAmount( $tot );
		$fields['ibaOtherIncome'] = &$ibaOtherIncome;

		$ibaTotal = new ControlAccountTotal( 'Other income total', false, null );
		$tot = $ibaReceiptsPayments->get(  );
		$tot += $ibaJournals->get(  );
		$tot += $ibaBankInterest->get(  );
		$tot += $ibaMiscReceipts->get(  );
		$tot += $ibaBankCharges->get(  );
		$tot += $ibaCommTransferred->get(  );
		$tot += $ibaFeesTransferred->get(  );
		$tot += $ibaOtherIncomeTransferred->get(  );
		$tot += $ibaOtherChargesTransferred->get(  );
		$ibaTotal->setAmount( $tot );
		$fields['ibaTotal'] = &$ibaTotal;

		$controlTotal = new ControlAccountTotal( 'Control total cross check - should be zero', false, null );
		$tot = $clTotal->get(  );
		$tot += $icTotal->get(  );
		$tot += $cpTotal->get(  );
		$tot += $fpTotal->get(  );
		$tot += $inTotal->get(  );
		$tot += $prTotal->get(  );
		$tot += $pfTotal->get(  );
		$tot += $oiTotal->get(  );
		$tot += $ocTotal->get(  );
		$tot += $ibaTotal->get(  );
		$controlTotal->setAmount( $tot );
		$fields['controlTotal'] = &$controlTotal;

		return $fields;
	}

	/**
 * We update the array with new items for client detail
 *
 * @param pointer to array we add items to
 */
	function _makeClientDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$clPremDR = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit', false, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPremDR->addSelect( false, (  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT,
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPremDR->addDetailSelect( false, (  . 'SELECT
				ptCode			    as Policy_Tran,
				ptPolicy		    as Policy,
				ptAddOnGrossIncIPT  as AddOn_Gross_Inc_IPT_direct
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPremDR->addDetailSelect( false, (  . 'SELECT
				ptCode			    as Policy_Tran,
				ptPolicy		    as Policy,
				ptGrossIncIPT 		as Gross_Inc_IPT,
				ptAddlGrossIncIPT   as Addl_Gross_Inc_IPT,
				ptAddOnGrossIncIPT  as AddOn_Gross_Inc_IPT,
				ptEngineeringFee	as Eng_Fees,
				ptEngineeringFeeVAT as Eng_Fees_VAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDR'] = &$clPremDR;

		$clPremDRMain = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit main premium', false, (  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPremDRMain->addDetailSelect( false, (  . 'SELECT
			ptCode			    as Policy_Tran,
			ptPolicy		    as Policy,
			ptGrossIncIPT 		as Gross_Inc_IPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDRMain'] = &$clPremDRMain;

		$clPremDRAP = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit additional premium', false, (  . 'SELECT
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDRAP'] = &$clPremDRAP;

		$clPremDRAddOn = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit add on premium', false, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) 	as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDRAddOn'] = &$clPremDRAddOn;

		$clPremDRInsFees = new ControlAccountTotal( 'Ins Co fees', false, (  . 'SELECT
				SUM(ptEngineeringFee) 	    as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT) 	as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDRInsFees'] = &$clPremDRInsFees;

		$clPremDREngFees = new ControlAccountTotal( 'Engineering fees', false, (  . 'SELECT
				SUM(ptEngineeringFee) 	    as ptEngineeringFee
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDREngFees'] = &$clPremDREngFees;

		$clPremDREngFeesVAT = new ControlAccountTotal( 'Engineering fees VAT', false, (  . 'SELECT
				SUM(ptEngineeringFeeVAT) 	as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND   ptDebit = 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremDREngFeesVAT'] = &$clPremDREngFeesVAT;

		$total = $clPremDRMain->get(  ) + $clPremDRAP->get(  ) + $clPremDRAddOn->get(  ) + $clPremDRInsFees->get(  ) + $clPremDREngFees->get(  ) + $clPremDREngFeesVAT->get(  );
		$prev = $clPremDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Premium DR',  . 'clPremDR amount was ' . $prev . ', now ' . $total );
		}

		$clPremCR = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- credit', true, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$clPremCR->addSelect( true,  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT,
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptAddOnGrossIncIPT) as ptAddOnGrossIncIPT,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'
		' );
		$totals['clPremCR'] = &$clPremCR;

		$clPremCRMain = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit main premium', true, (  . 'SELECT
				SUM(ptGrossIncIPT) 		as ptGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCRMain'] = &$clPremCRMain;

		$clPremCRAP = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- credit additional premium', true, (  . 'SELECT
				SUM(ptAddlGrossIncIPT) 	as ptAddlGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCRAP'] = &$clPremCRAP;

		$clPremCRAddOn = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes)- debit add on premium', true, (  . 'SELECT
				SUM(ptAddOnGrossIncIPT) 	as ptAddOnGrossIncIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCRAddOn'] = &$clPremCRAddOn;

		$clPremCRInsFees = new ControlAccountTotal( 'Ins Co fees', true, (  . 'SELECT
				SUM(ptEngineeringFee) 	    as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT) 	as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCRInsFees'] = &$clPremCRInsFees;

		$clPremCREngFees = new ControlAccountTotal( 'Engineering fees', true, (  . 'SELECT
				SUM(ptEngineeringFee) 	    as ptEngineeringFee
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCREngFees'] = &$clPremCREngFees;

		$clPremCREngFeesVAT = new ControlAccountTotal( 'Engineering fees VAT', true, (  . 'SELECT
				SUM(ptEngineeringFeeVAT) 	as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND   ptDebit != 1
			AND   ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clPremCREngFeesVAT'] = &$clPremCREngFeesVAT;

		$total = $clPremCRMain->get(  ) + $clPremCRAP->get(  ) + $clPremCRAddOn->get(  ) + $clPremCRInsFees->get(  ) + $clPremCREngFees->get(  ) + $clPremCREngFeesVAT->get(  );
		$prev = $clPremCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Premium CR', (  . 'clPremCR amount was ' . $prev . ', now ' . $total . ')' ) );
		}

		$total = $clPremCR->get(  ) + $clPremDR->get(  );
		$clPrem = &$totals['clPrem'];

		$prev = $clPrem->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Premium Total', (  . 'clPrem amount was ' . $prev . ', now ' . $total . ')' ) );
		}

		$clBrFeesDR = new ControlAccountTotal( 'Broker Fees including VAT, debit', false, (  . 'SELECT
				SUM(ptBrokerFee) 		as ptBrokerFee,
				SUM(ptBrokerFeeVAT) 	as ptBrokerFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit = 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clBrFeesDR'] = &$clBrFeesDR;

		$clBrFeesCR = new ControlAccountTotal( 'Broker Fees including VAT, credit', true, (  . 'SELECT
				SUM(ptBrokerFee) 		as ptBrokerFee,
				SUM(ptBrokerFeeVAT) 	as ptBrokerFeeVAT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND   ptDebit != 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clBrFeesCR'] = &$clBrFeesCR;

		$total = $clBrFeesDR->get(  ) + $clBrFeesCR->get(  );
		$clBrFees = &$totals['clBrFees'];

		$prev = $clBrFees->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Broker Fees',  . 'clBrFees was ' . $prev . ', now ' . $total );
		}

		$clRecPayDR = new ControlAccountTotal( 'Receipts from and cash paid to clients, debits only', false, (  . 'SELECT
				SUM(ctOriginal) 		as ctOriginal
			FROM clientTransactions
			WHERE ctTransType = \'C\'
			AND ctOriginal >= 0
			AND   ctPostingDate >= \'' . $START . '\' AND ctPostingDate <= \'' . $END . '\'' ) );
		$totals['clRecPayDR'] = &$clRecPayDR;

		$clRecPayCR = new ControlAccountTotal( 'Receipts from and cash paid to clients, credits only', false, (  . 'SELECT
				SUM(ctOriginal) 		as ctOriginal
			FROM clientTransactions
			WHERE ctTransType = \'C\'
			AND ctOriginal < 0
			AND   ctPostingDate >= \'' . $START . '\' AND ctPostingDate <= \'' . $END . '\'' ) );
		$totals['clRecPayCR'] = &$clRecPayCR;

		$total = $clRecPayDR->get(  ) + $clRecPayCR->get(  );
		$clRecPay = &$totals['clRecPay'];

		$prev = $clRecPay->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Receipts', (  . 'clRecPay was ' . $prev . ', now ' . $total . ')' ) );
		}

		$clDiscDR = new ControlAccountTotal( 'Client discounts given', true, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clDiscDR'] = &$clDiscDR;

		$clDiscCR = new ControlAccountTotal( 'Client discounts taken', false, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['clDiscCR'] = &$clDiscCR;

		$total = $clDiscDR->get(  ) + $clDiscCR->get(  );
		$clDisc = &$totals['clDisc'];

		$prev = $clDisc->get(  );

		if ($total != $prev) {
			_showWhenError( 'Client Discounts',  . 'clDisc was ' . $prev . ', now ' . $total );
		}

		$clWrOffDR = new ControlAccountTotal( 'Client write offs on cash receipts, debits', true, (  . 'SELECT
				SUM(caAmount) 		as caAmount
			FROM clientTransAllocations
			WHERE caType = \'W\'
			AND  caAmount >= 0
			AND   caPostingDate >= \'' . $START . '\' AND caPostingDate <= \'' . $END . '\'' ) );
		$totals['clWrOffDR'] = &$clWrOffDR;

		$clWrOffCR = new ControlAccountTotal( 'Client write offs on cash receipts, credits', true, (  . 'SELECT
				SUM(caAmount) 		as caAmount
			FROM clientTransAllocations
			WHERE caType = \'W\'
			AND  caAmount < 0
			AND   caPostingDate >= \'' . $START . '\' AND caPostingDate <= \'' . $END . '\'' ) );
		$totals['clWrOffCR'] = &$clWrOffCR;

		$total = $clWrOffDR->get(  ) + $clWrOffCR->get(  );
		$clWrOff = &$totals['clWrOff'];

		$prev = $clWrOff->get(  );

		if ($total != $clWrOff->get(  )) {
			_showWhenError( 'Clients Written Off',  . 'clWrOff was ' . $prev . ', now ' . $total );
		}

		$total = 0 + $clPrem->get(  ) + $clBrFees->get(  ) + $clRecPay->get(  ) + $clDisc->get(  ) + $clWrOff->get(  );
		$clTotal = &$totals['clTotal'];

		$prev = $clTotal->get(  );

		if ($prev != $total) {
			_showWhenError( 'Client Total',  . 'clTotal was ' . $prev . ', now ' . $total );
		}

		$clTotal->setAmount( $total );
	}

	/**
 * This makes all the opeing figures if we are doing an accounting period
 *
 */
	function _makeOpeningTotals() {
		global $controlFromDate;
		global $controlToDate;
		global $totals;
		global $selectedPeriodCode;

		$clOpening = new ControlAccountTotal( 'Clients brought forward total', false, null );
		$clOpening->setAlwaysShowBlank( true );
		$totals['clOpening'] = $clOpening;
		$icOpening = new ControlAccountTotal( 'Insurers brought forward total', false, null );
		$icOpening->setAlwaysShowBlank( true );
		$totals['icOpening'] = $icOpening;
		$cpOpening = new ControlAccountTotal( 'Commission posted brought forward total', false, null );
		$cpOpening->setAlwaysShowBlank( true );
		$totals['cpOpening'] = $cpOpening;
		$fpOpening = new ControlAccountTotal( 'Fees Posted brought forward total', false, null );
		$fpOpening->setAlwaysShowBlank( true );
		$totals['fpOpening'] = $fpOpening;
		$inOpening = new ControlAccountTotal( 'Introducers brought forward total', false, null );
		$inOpening->setAlwaysShowBlank( true );
		$totals['inOpening'] = $inOpening;
		$prOpening = new ControlAccountTotal( 'Commission paid/reconciled brought forward total', false, null );
		$prOpening->setAlwaysShowBlank( true );
		$totals['prOpening'] = $prOpening;
		$pfOpening = new ControlAccountTotal( 'Fees paid brought forward total', false, null );
		$pfOpening->setAlwaysShowBlank( true );
		$totals['pfOpening'] = $pfOpening;
		$oiOpening = new ControlAccountTotal( 'Other income brought forward total', false, null );
		$oiOpening->setAlwaysShowBlank( true );
		$totals['oiOpening'] = $oiOpening;
		$ocOpening = new ControlAccountTotal( 'Other charges brought forward total', false, null );
		$ocOpening->setAlwaysShowBlank( true );
		$totals['ocOpening'] = $ocOpening;
		$ibaOpening = new ControlAccountTotal( 'IBA brought forward total', false, null );
		$ibaOpening->setAlwaysShowBlank( true );
		$totals['ibaOpening'] = $ibaOpening;
		$cfOpening = new ControlAccountTotal( 'Commission posted brought forward total', false, null );
		$cfOpening->setAlwaysShowBlank( true );
		$totals['cfOpening'] = $cfOpening;

		if ($selectedPeriodCode <= 0) {
			return null;
		}

		$ap = new AccountingPeriod( $selectedPeriodCode );
		$prevCode = $ap->getPreviousPeriod(  );

		if ($prevCode < 1) {
			return null;
		}

		$q =  . 'SELECT afCode FROM accountingFigures
			WHERE afPeriodCode = ' . $prevCode;
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}


		if (udbNumberOfRows( $result ) == 0) {
			return null;
		}

		$row = udbGetRow( $result );
		$afCode = $row['afCode'];
		$af = new AccountingFigures( $afCode );
		$clOpening->setAlwaysShowBlank( false );
		$clOpening->setAmount( $af->get( 'afClients' ) );
		$totals['clOpening'] = $clOpening;
		$icOpening->setAlwaysShowBlank( false );
		$icOpening->setAmount( $af->get( 'afInsurers' ) );
		$totals['icOpening'] = $icOpening;
		$cpOpening->setAlwaysShowBlank( false );
		$cpOpening->setAmount( $af->get( 'afCommPosted' ) );
		$totals['cpOpening'] = $cpOpening;
		$fpOpening->setAlwaysShowBlank( false );
		$fpOpening->setAmount( $af->get( 'afFeesPosted' ) );
		$totals['fpOpening'] = $fpOpening;
		$inOpening->setAlwaysShowBlank( false );
		$inOpening->setAmount( $af->get( 'afIntroducers' ) );
		$totals['inOpening'] = $inOpening;
		$prOpening->setAlwaysShowBlank( false );
		$prOpening->setAmount( $af->get( 'afCommPaid' ) );
		$totals['prOpening'] = $prOpening;
		$pfOpening->setAlwaysShowBlank( false );
		$pfOpening->setAmount( $af->get( 'afFeesPaid' ) );
		$totals['pfOpening'] = $pfOpening;
		$oiOpening->setAlwaysShowBlank( false );
		$oiOpening->setAmount( $af->get( 'afOtherIncome' ) );
		$totals['oiOpening'] = $oiOpening;
		$ocOpening->setAlwaysShowBlank( false );
		$ocOpening->setAmount( $af->get( 'afOtherCharges' ) );
		$totals['ocOpening'] = $ocOpening;
		$ibaOpening->setAlwaysShowBlank( false );
		$ibaOpening->setAmount( $af->get( 'afBank' ) );
		$totals['ibaOpening'] = $ibaOpening;
		$cfOpening->setAlwaysShowBlank( false );
		$cfOpening->setAmount( $af->get( 'afCommFees' ) );
		$totals['cfOpening'] = $cfOpening;
	}

	/**
 * This makes all the opeing figures if we are doing an accounting period
 *
 */
	function _makeClosingTotals() {
		global $controlFromDate;
		global $controlToDate;
		global $totals;
		global $selectedPeriodCode;

		$clClosing = new ControlAccountTotal( 'Clients carried forward total', false, null );
		$clClosing->setAlwaysShowBlank( true );
		$totals['clClosing'] = $clClosing;
		$icClosing = new ControlAccountTotal( 'Insurers carried forward total', false, null );
		$icClosing->setAlwaysShowBlank( true );
		$totals['icClosing'] = $icClosing;
		$cpClosing = new ControlAccountTotal( 'Commission carried brought forward total', false, null );
		$cpClosing->setAlwaysShowBlank( true );
		$totals['cpClosing'] = $cpClosing;
		$fpClosing = new ControlAccountTotal( 'Fees Posted carried forward total', false, null );
		$fpClosing->setAlwaysShowBlank( true );
		$totals['fpClosing'] = $fpClosing;
		$inClosing = new ControlAccountTotal( 'Introducers carried forward total', false, null );
		$inClosing->setAlwaysShowBlank( true );
		$totals['inClosing'] = $inClosing;
		$prClosing = new ControlAccountTotal( 'Commission paid/reconciled carried forward total', false, null );
		$prClosing->setAlwaysShowBlank( true );
		$totals['prClosing'] = $prClosing;
		$pfClosing = new ControlAccountTotal( 'Fees paid carried forward total', false, null );
		$pfClosing->setAlwaysShowBlank( true );
		$totals['pfClosing'] = $pfClosing;
		$oiClosing = new ControlAccountTotal( 'Other income carried forward total', false, null );
		$oiClosing->setAlwaysShowBlank( true );
		$totals['oiClosing'] = $oiClosing;
		$ocClosing = new ControlAccountTotal( 'Other charges carried forward total', false, null );
		$ocClosing->setAlwaysShowBlank( true );
		$totals['ocClosing'] = $ocClosing;
		$ibaClosing = new ControlAccountTotal( 'IBA carried forward total', false, null );
		$ibaClosing->setAlwaysShowBlank( true );
		$totals['ibaClosing'] = $ibaClosing;
		$cfClosing = new ControlAccountTotal( 'Commission posted brought forward total', false, null );
		$cfClosing->setAlwaysShowBlank( true );
		$totals['cfClosing'] = $cfClosing;
		$availableForTransfer = new ControlAccountTotal( 'Total available for transfer', false, null );
		$totals['availableForTransfer'] = &$availableForTransfer;

		if ($selectedPeriodCode <= 0) {
			return null;
		}

		$ap = new AccountingPeriod( $selectedPeriodCode );
		$prevCode = $ap->getPreviousPeriod(  );

		if ($prevCode < 1) {
			return null;
		}

		$q =  . 'SELECT afCode FROM accountingFigures
			WHERE afPeriodCode = ' . $prevCode;
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}


		if (udbNumberOfRows( $result ) == 0) {
			return null;
		}

		$clOpening = &$totals['clOpening'];
		$clTotal = &$totals['clTotal'];

		$clClosing->setAmount( $clOpening->get(  ) + $clTotal->get(  ) );
		$clClosing->setAlwaysShowBlank( false );
		$totals['clClosing'] = $clClosing;
		$icOpening = &$totals['icOpening'];
		$icTotal = &$totals['icTotal'];

		$icClosing->setAmount( $icOpening->get(  ) + $icTotal->get(  ) );
		$icClosing->setAlwaysShowBlank( false );
		$totals['icClosing'] = $icClosing;
		$cpOpening = &$totals['cpOpening'];
		$cpTotal = &$totals['cpTotal'];

		$cpClosing->setAmount( $cpOpening->get(  ) + $cpTotal->get(  ) );
		$cpClosing->setAlwaysShowBlank( false );
		$totals['cpClosing'] = $cpClosing;
		$fpOpening = &$totals['fpOpening'];
		$fpTotal = &$totals['fpTotal'];

		$fpClosing->setAmount( $fpOpening->get(  ) + $fpTotal->get(  ) );
		$fpClosing->setAlwaysShowBlank( false );
		$totals['fpClosing'] = $fpClosing;
		$inOpening = &$totals['inOpening'];
		$inTotal = &$totals['inTotal'];

		$inClosing->setAmount( $inOpening->get(  ) + $inTotal->get(  ) );
		$inClosing->setAlwaysShowBlank( false );
		$totals['inClosing'] = $inClosing;
		$prOpening = &$totals['prOpening'];
		$prTotal = &$totals['prTotal'];

		$prClosing->setAmount( $prOpening->get(  ) + $prTotal->get(  ) );
		$prClosing->setAlwaysShowBlank( false );
		$totals['prClosing'] = $prClosing;
		$pfOpening = &$totals['pfOpening'];
		$pfTotal = &$totals['pfTotal'];

		$pfClosing->setAmount( $pfOpening->get(  ) + $pfTotal->get(  ) );
		$pfClosing->setAlwaysShowBlank( false );
		$totals['pfClosing'] = $pfClosing;
		$oiOpening = &$totals['oiOpening'];
		$oiTotal = &$totals['oiTotal'];

		$oiClosing->setAmount( $oiOpening->get(  ) + $oiTotal->get(  ) );
		$oiClosing->setAlwaysShowBlank( false );
		$totals['oiClosing'] = $oiClosing;
		$ocOpening = &$totals['ocOpening'];
		$ocTotal = &$totals['ocTotal'];

		$ocClosing->setAmount( $ocOpening->get(  ) + $ocTotal->get(  ) );
		$ocClosing->setAlwaysShowBlank( false );
		$totals['ocClosing'] = $ocClosing;
		$ibaOpening = &$totals['ibaOpening'];
		$ibaTotal = &$totals['ibaTotal'];

		$ibaClosing->setAmount( $ibaOpening->get(  ) + $ibaTotal->get(  ) );
		$ibaClosing->setAlwaysShowBlank( false );
		$totals['ibaClosing'] = $ibaClosing;
		$cfOpening = &$totals['cfOpening'];
		$cfTotal = &$totals['cfTotal'];

		$cfClosing->setAmount( $cfOpening->get(  ) + $cfTotal->get(  ) );
		$cfClosing->setAlwaysShowBlank( false );
		$totals['cfClosing'] = $cfClosing;
	}

	/**
 * We update the array with new items for client detail
 *
 * @param pointer to array we add items to
 */
	function _makeInsurerDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$icPremDR = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes) - debit only', true, (  . 'SELECT
				SUM(ptAddOnNet) 		as ptAddOnNet,
				SUM(ptAddOnIPT) 		as ptAddOnIPT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremDR->addSelect( true, (  . 'SELECT
				SUM(ptNet) 				as ptNet,
				SUM(ptGrossIPT) 		as ptGrossIPT,

				SUM(ptAddlNet) 			as ptAddlNet,
				SUM(ptAddlIPT) 			as ptAddlIPT,

				SUM(ptAddOnNet) 		as ptAddOnNet,
				SUM(ptAddOnIPT) 		as ptAddOnIPT,

				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremDR->addSelect( false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 	as ptEngineeringFeeComm

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDR'] = &$icPremDR;

		$icPremDRMain = new ControlAccountTotal( 'Main net - debit only', true, (  . 'SELECT
				SUM(ptNet) 				as ptNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRMain'] = &$icPremDRMain;

		$icPremDRMainIPT = new ControlAccountTotal( 'AP IPT - debit only', true, (  . 'SELECT
				SUM(ptGrossIPT) 				as ptGrossIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRMainIPT'] = &$icPremDRMainIPT;

		$icPremDRAP = new ControlAccountTotal( 'AP net - debit only', true, (  . 'SELECT
				SUM(ptAddlNet) 				as ptAddlNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRAP'] = &$icPremDRAP;

		$icPremDRAPIPT = new ControlAccountTotal( 'AP IPT - debit only', true, (  . 'SELECT
				SUM(ptAddlIPT) 				as ptAddlIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRAPIPT'] = &$icPremDRAPIPT;

		$icPremDRAddOn = new ControlAccountTotal( 'AddOn net - debit only', true, (  . 'SELECT
				SUM(ptAddOnNet) 				as ptAddOnNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRAddOn'] = &$icPremDRAddOn;

		$icPremDRAddOnIPT = new ControlAccountTotal( 'AddOn IPT - debit only', true, (  . 'SELECT
				SUM(ptAddOnIPT) 				as ptAddlIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRAddOnIPT'] = &$icPremDRAddOnIPT;

		$icPremDRICFees = new ControlAccountTotal( 'Ins Co Fees - debit only', true, (  . 'SELECT
				SUM(ptEngineeringFee) 				as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT) 			as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremDRICFees->addSelect( false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 			as ptEngineeringFeeComm
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDRICFees'] = &$icPremDRICFees;

		$icPremDREngFees = new ControlAccountTotal( 'Engineering Fees - debit only', true, (  . 'SELECT
				SUM(ptEngineeringFee) 				as ptEngineeringFee
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremDREngFees->addSelect( false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 				as ptEngineeringFeeComm
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDREngFees'] = &$icPremDREngFees;

		$icPremDREngFeesVAT = new ControlAccountTotal( 'Engineering Fees VAT - debit only', true, (  . 'SELECT
				SUM(ptEngineeringFeeVAT) 				as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremDREngFeesVAT'] = &$icPremDREngFeesVAT;

		$total = $icPremDRMain->get(  ) + $icPremDRMainIPT->get(  ) + $icPremDRAP->get(  ) + $icPremDRAPIPT->get(  ) + $icPremDRAddOn->get(  ) + $icPremDRAddOnIPT->get(  ) + $icPremDRICFees->get(  ) + $icPremDREngFees->get(  ) + $icPremDREngFeesVAT->get(  );
		$prev = $icPremDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Premium DR',  . 'icPremDR amount was ' . $prev . ', now ' . $total );
		}

		$icPremCR = new ControlAccountTotal( 'Premiums (incl. IC Fees & Taxes) - credit only', false, (  . 'SELECT
				SUM(ptAddOnNet) 		as ptAddOnNet,
				SUM(ptAddOnIPT) 		as ptAddOnIPT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCR->addDetailSelect( false, (  . 'SELECT
				ptCode,
				ptAddOnNet,
				ptAddOnIPT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCR->addSelect( false, (  . 'SELECT
				SUM(ptNet) 				as ptNet,
				SUM(ptGrossIPT) 		as ptGrossIPT,

				SUM(ptAddlNet) 			as ptAddlNet,
				SUM(ptAddlIPT) 			as ptAddlIPT,

				SUM(ptAddOnNet) 		as ptAddOnNet,
				SUM(ptAddOnIPT) 		as ptAddOnIPT,

				SUM(ptEngineeringFee) 	as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT)as ptEngineeringFeeVAT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCR->addDetailSelect( false, (  . 'SELECT
				ptCode,
				ptNet,
				ptGrossIPT,

				ptAddlNet,
				ptAddlIPT,

				ptAddOnNet,
				ptAddOnIPT,

				ptEngineeringFee,
				ptEngineeringFeeVAT

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCR->addSelect( true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 	as ptEngineeringFeeComm

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCR->addDetailSelect( true, (  . 'SELECT
				ptEngineeringFeeComm

			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCR'] = &$icPremCR;

		$icPremCRMain = new ControlAccountTotal( 'Main net - credits only', false, (  . 'SELECT
				SUM(ptNet) 				as ptNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRMain'] = &$icPremCRMain;

		$icPremCRMainIPT = new ControlAccountTotal( 'AP IPT - credits only', false, (  . 'SELECT
				SUM(ptGrossIPT) 				as ptGrossIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRMainIPT'] = &$icPremCRMainIPT;

		$icPremCRAP = new ControlAccountTotal( 'AP net - credit only', false, (  . 'SELECT
				SUM(ptAddlNet) 				as ptAddlNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRAP'] = &$icPremCRAP;

		$icPremCRAPIPT = new ControlAccountTotal( 'AP IPT - credit only', false, (  . 'SELECT
				SUM(ptAddlIPT) 				as ptAddlIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRAPIPT'] = &$icPremCRAPIPT;

		$icPremCRAddOn = new ControlAccountTotal( 'AddOn net - credit only', false, (  . 'SELECT
				SUM(ptAddOnNet) 				as ptAddOnNet
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRAddOn'] = &$icPremCRAddOn;

		$icPremCRAddOnIPT = new ControlAccountTotal( 'AddOn IPT - credits only', false, (  . 'SELECT
				SUM(ptAddOnIPT) 				as ptAddlIPT
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRAddOnIPT'] = &$icPremCRAddOnIPT;

		$icPremCRICFees = new ControlAccountTotal( 'Ins Co Fees - credits only', false, (  . 'SELECT
				SUM(ptEngineeringFee) 				as ptEngineeringFee,
				SUM(ptEngineeringFeeVAT) 			as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCRICFees->addSelect( true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 			as ptEngineeringFeeComm
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable != 1
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCRICFees'] = &$icPremCRICFees;

		$icPremCREngFees = new ControlAccountTotal( 'Engineering Fees - credits only', false, (  . 'SELECT
				SUM(ptEngineeringFee) 				as ptEngineeringFee
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$icPremCREngFees->addSelect( true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 				as ptEngineeringFeeComm
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCREngFees'] = &$icPremCREngFees;

		$icPremCREngFeesVAT = new ControlAccountTotal( 'Engineering Fees VAT - credits only', false, (  . 'SELECT
				SUM(ptEngineeringFeeVAT) 				as ptEngineeringFeeVAT
			FROM policyTransactions, classOfBus
			WHERE ptPostStatus = \'P\'
			AND	   cbCode = ptClassOfBus
			AND	   cbFeesVatable = 1
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icPremCREngFeesVAT'] = &$icPremCREngFeesVAT;

		$total = $icPremCRMain->get(  ) + $icPremCRMainIPT->get(  ) + $icPremCRAP->get(  ) + $icPremCRAPIPT->get(  ) + $icPremCRAddOn->get(  ) + $icPremCRAddOnIPT->get(  ) + $icPremCRICFees->get(  ) + $icPremCREngFees->get(  ) + $icPremCREngFeesVAT->get(  );
		$prev = $icPremCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Premium CR',  . 'icPremCR amount was ' . $prev . ', now ' . $total );
		}

		$total = $icPremDR->get(  ) + $icPremCR->get(  );
		$icPrem = &$totals['icPrem'];

		$prev = $icPrem->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Premium',  . 'icPrem amount was ' . $prev . ', now ' . $total );
		}

		$icDrCommDR = new ControlAccountTotal( 'Commission on direct policies debits', false, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission,
				SUM(ptAddlCommission) 	as ptAddlCommission,
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommDR'] = &$icDrCommDR;

		$icDrCommDRMain = new ControlAccountTotal( 'Commission on direct policies , main premium, debits', false, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommDRMain'] = &$icDrCommDRMain;

		$icDrCommDRAP = new ControlAccountTotal( 'Commission on direct policies , AP, debits', false, (  . 'SELECT
				SUM(ptAddlCommission) 	as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommDRAP'] = &$icDrCommDRAP;

		$icDrCommDREngFee = new ControlAccountTotal( 'Commission on direct policies , Eng Fees, debits', false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommDREngFee'] = &$icDrCommDREngFee;

		$total = $icDrCommDRMain->get(  ) + $icDrCommDRAP->get(  ) + $icDrCommDREngFee->get(  );
		$prev = $icDrCommDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Direct Commission DR',  . 'icDrCommDR amount was ' . $prev . ', now ' . $total );
		}

		$icDrCommCR = new ControlAccountTotal( 'Commission on direct policies credits', true, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission,
				SUM(ptAddlCommission) 	as ptAddlCommission,
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommCR'] = &$icDrCommCR;

		$icDrCommCRMain = new ControlAccountTotal( 'Commission on direct policies , main premium, creditss', true, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommCRMain'] = &$icDrCommCRMain;

		$icDrCommCRAP = new ControlAccountTotal( 'Commission on direct policies , AP, credits', true, (  . 'SELECT
				SUM(ptAddlCommission) 	as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommCRAP'] = &$icDrCommCRAP;

		$icDrCommCREngFee = new ControlAccountTotal( 'Commission on direct policies , Eng Fees, credits', true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['icDrCommCREngFee'] = &$icDrCommCREngFee;

		$total = $icDrCommCRMain->get(  ) + $icDrCommCRAP->get(  ) + $icDrCommCREngFee->get(  );
		$prev = $icDrCommCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Direct Commission CR',  . 'icDrCommCR amount was ' . $prev . ', now ' . $total );
		}

		$total = $icDrCommDR->get(  ) + $icDrCommCR->get(  );
		$icDrComm = $totals['icDrComm'];
		$prev = $icDrComm->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Direct Commission',  . 'icDrComm amount was ' . $prev . ', now ' . $total );
		}

		$icRecPayDR = new ControlAccountTotal( 'payments to insurance companies - debits', true, (  . 'SELECT
				SUM(itOriginal) 		as itOriginal
			FROM inscoTransactions
			WHERE (itTransType = \'C\' OR itTransType = \'R\')
			AND  itOriginal < 0
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$totals['icRecPayDR'] = &$icRecPayDR;

		$icRecPayDRMain = new ControlAccountTotal( 'payments to insurance companies - debits - main', true, (  . 'SELECT
				SUM(itPaid) 			as itPaid
			FROM inscoTransactions
			WHERE (itTransType = \'C\' OR itTransType = \'R\')
			AND  itOriginal < 0
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$totals['icRecPayDRMain'] = &$icRecPayDRMain;

		$icRecPayDRUnalloc = new ControlAccountTotal( 'payments to insurance companies - debits - unallocated', true, null );
		$unalloc = $icRecPayDR->get(  ) - $icRecPayDRMain->get(  );
		$icRecPayDRUnalloc->setAmount( $unalloc );
		$totals['icRecPayDRUnalloc'] = &$icRecPayDRUnalloc;

		$total = $icRecPayDRMain->get(  ) + $icRecPayDRUnalloc->get(  );
		$prev = $icRecPayDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Payments DR',  . 'icRecPayDR amount was ' . $prev . ', now ' . $total );
		}

		$icRecPayCR = new ControlAccountTotal( 'payments to insurance companies - credits', true, (  . 'SELECT
				SUM(itOriginal) 		as itOriginal
			FROM inscoTransactions
			WHERE (itTransType = \'C\' OR itTransType = \'R\')
			AND  itOriginal > 0
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$totals['icRecPayCR'] = &$icRecPayCR;

		$icRecPayCRMain = new ControlAccountTotal( 'payments to insurance companies - credits - main', true, (  . 'SELECT
				SUM(itPaid) 			as itPaid
			FROM inscoTransactions
			WHERE (itTransType = \'C\' OR itTransType = \'R\')
			AND  itOriginal > 0
			AND	  itPostingDate >= \'' . $START . '\' AND itPostingDate <= \'' . $END . '\'' ) );
		$totals['icRecPayCRMain'] = &$icRecPayCRMain;

		$icRecPayCRUnalloc = new ControlAccountTotal( 'payments to insurance companies - credits - unallocated', true, null );
		$unalloc = $icRecPayCR->get(  ) - $icRecPayCRMain->get(  );
		$icRecPayCRUnalloc->setAmount( $unalloc );
		$totals['icRecPayCRUnalloc'] = &$icRecPayCRUnalloc;

		$total = $icRecPayCRMain->get(  ) + $icRecPayCRUnalloc->get(  );
		$prev = $icRecPayCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Payments CR',  . 'icRecPayCR amount was ' . $prev . ', now ' . $total );
		}

		$total = $icRecPayDR->get(  ) + $icRecPayCR->get(  );
		$icRecPay = $totals['icRecPay'];
		$prev = $icRecPay->get(  );

		if ($total != $prev) {
			_showWhenError( 'IC Payments',  . 'icRecPay amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for comm posted
 *
 * @param pointer to array we add items to
 */
	function _makeCommissionPostedDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$cpPremDR = new ControlAccountTotal( 'Commission debit', true, (  . 'SELECT
				SUM(ptCommission) 				as ptCommission,
				SUM(ptAddlCommission) 			as ptAddlCommission,
				SUM(ptAddOnCommission) 			as ptAddOnCommission,
				SUM(ptEngineeringFeeComm) 		as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPremDR->addSelect( true, (  . 'SELECT
				SUM(ptAddOnCommission) 			as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremDR'] = &$cpPremDR;

		$cpPremDRMain = new ControlAccountTotal( 'Main comm - debit only', true, (  . 'SELECT
				SUM(ptCommission) 				as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremDRMain'] = &$cpPremDRMain;

		$cpPremDRAP = new ControlAccountTotal( 'AP comm - debit only', true, (  . 'SELECT
				SUM(ptAddlCommission) 				as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremDRAP'] = &$cpPremDRAP;

		$cpPremDRAddOn = new ControlAccountTotal( 'AddOn comm - debit only', true, (  . 'SELECT
				SUM(ptAddOnCommission) 				as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPremDRAddOn->addSelect( true, (  . 'SELECT
				SUM(ptAddOnCommission) 			as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremDRAddOn'] = &$cpPremDRAddOn;

		$cpPremDRICFees = new ControlAccountTotal( 'Ins Co Eng Fees Comm - debit only', true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 				as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremDRICFees'] = &$cpPremDRICFees;

		$total = $cpPremDRMain->get(  ) + $cpPremDRAP->get(  ) + $cpPremDRAddOn->get(  ) + $cpPremDRICFees->get(  );
		$prev = $cpPremDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Posted Premium DR',  . 'cpPremDR amount was ' . $prev . ', now ' . $total );
		}

		$cpPremCR = new ControlAccountTotal( 'Commission credit', false, (  . 'SELECT
				SUM(ptCommission) 			as ptCommission,
				SUM(ptAddlCommission) 			as ptAddlCommission,
				SUM(ptAddOnCommission) 			as ptAddOnCommission,
				SUM(ptEngineeringFeeComm) 			as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPremCR->addSelect( false, (  . 'SELECT
				SUM(ptAddOnCommission) 			as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremCR'] = &$cpPremCR;

		$cpPremCRMain = new ControlAccountTotal( 'Main comm - credit only', false, (  . 'SELECT
				SUM(ptCommission) 				as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremCRMain'] = &$cpPremCRMain;

		$cpPremCRAP = new ControlAccountTotal( 'AP comm - credit only', false, (  . 'SELECT
				SUM(ptAddlCommission) 				as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremCRAP'] = &$cpPremCRAP;

		$cpPremCRAddOn = new ControlAccountTotal( 'AddOn comm - credit only', false, (  . 'SELECT
				SUM(ptAddOnCommission) 				as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$cpPremCRAddOn->addSelect( false, (  . 'SELECT
				SUM(ptAddOnCommission) 			as ptAddOnCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremCRAddOn'] = &$cpPremCRAddOn;

		$cpPremCRICFees = new ControlAccountTotal( 'Ins Co Eng Fees Comm - credit only', false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) 				as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND	  ptDirect != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpPremCRICFees'] = &$cpPremCRICFees;

		$total = $cpPremCRMain->get(  ) + $cpPremCRAP->get(  ) + $cpPremCRAddOn->get(  ) + $cpPremCRICFees->get(  );
		$prev = $cpPremCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Posted Premium CR',  . 'cpPremCR amount was ' . $prev . ', now ' . $total );
		}

		$total = $cpPremDR->get(  ) + $cpPremCR->get(  );
		$cpPrem = &$totals['cpPrem'];

		$prev = $cpPrem->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Posted Premium',  . 'cpPrem amount was ' . $prev . ', now ' . $total );
		}

		$cpDrCommDR = new ControlAccountTotal( 'Commission on direct policies debits', true, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission,
				SUM(ptAddlCommission) 	as ptAddlCommission,
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommDR'] = &$cpDrCommDR;

		$cpDrCommDRMain = new ControlAccountTotal( 'Commission on direct policies , main premium, debits', true, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommDRMain'] = &$cpDrCommDRMain;

		$cpDrCommDRAP = new ControlAccountTotal( 'Commission on direct policies , AP, debits', true, (  . 'SELECT
				SUM(ptAddlCommission) 	as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommDRAP'] = &$cpDrCommDRAP;

		$cpDrCommDRAddOn = new ControlAccountTotal( 'must always be zero', true, null );
		$totals['cpDrCommDRAddOn'] = &$cpDrCommDRAddOn;

		$cpDrCommDRICFee = new ControlAccountTotal( 'Commission on direct policies , Eng Fees, debits', true, (  . 'SELECT
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit = 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommDRICFee'] = &$cpDrCommDRICFee;

		$total = $cpDrCommDRMain->get(  ) + $cpDrCommDRAP->get(  ) + $cpDrCommDRICFee->get(  );
		$prev = $cpDrCommDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Direct DR',  . 'cpDrCommDR amount was ' . $prev . ', now ' . $total );
		}

		$cpDrCommCR = new ControlAccountTotal( 'Commission on direct policies credits', false, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission,
				SUM(ptAddlCommission) 	as ptAddlCommission,
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommCR'] = &$cpDrCommCR;

		$cpDrCommCRMain = new ControlAccountTotal( 'Commission on direct policies , main premium, creditss', false, (  . 'SELECT
				SUM(ptCommission) 		as ptCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommCRMain'] = &$cpDrCommCRMain;

		$cpDrCommCRAP = new ControlAccountTotal( 'Commission on direct policies , AP, credits', false, (  . 'SELECT
				SUM(ptAddlCommission) 	as ptAddlCommission
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommCRAP'] = &$cpDrCommCRAP;

		$cpDrCommCRAddOn = new ControlAccountTotal( 'must always be zero', true, null );
		$totals['cpDrCommCRAddOn'] = &$cpDrCommCRAddOn;

		$cpDrCommCREngFee = new ControlAccountTotal( 'Commission on direct policies , Eng Fees, credits', false, (  . 'SELECT
				SUM(ptEngineeringFeeComm) as ptEngineeringFeeComm
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDirect = 1
			AND	  ptDebit != 1
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cpDrCommCREngFee'] = &$cpDrCommCREngFee;

		$total = $cpDrCommCRMain->get(  ) + $cpDrCommCRAP->get(  ) + $cpDrCommCREngFee->get(  );
		$prev = $cpDrCommCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Direct CR',  . 'cpDrCommCR amount was ' . $prev . ', now ' . $total );
		}

		$total = $cpDrCommDR->get(  ) + $cpDrCommCR->get(  );
		$cpDrComm = &$totals['cpDrComm'];

		$prev = $cpDrComm->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Direct',  . 'cpDrComm amount was ' . $prev . ', now ' . $total );
		}

		$cpDetailPaid = new ControlAccountTotal( 'Comm  Paid', false, null );
		$x = &$totals['cpCommPaid'];

		$amt = $x->get(  );
		$cpDetailPaid->setAmount( $amt );
		$totals['cpDetailPaid'] = &$cpDetailPaid;

	}

	/**
 * We update the array with new items for fees posted detail
 *
 * @param pointer to array we add items to
 */
	function _makeFeesPostedDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$fpBrFeesDR = new ControlAccountTotal( 'Broker Fees', false, null );
		$clBrFeesDR = &$totals['clBrFeesDR'];

		$amt = 0 - $clBrFeesDR->get(  );
		$fpBrFeesDR->setAmount( $amt );
		$totals['fpBrFeesDR'] = &$fpBrFeesDR;

		$fpBrFeesCR = new ControlAccountTotal( 'Broker Fees', false, null );
		$clBrFeesCR = &$totals['clBrFeesCR'];

		$amt = 0 - $clBrFeesCR->get(  );
		$fpBrFeesCR->setAmount( $amt );
		$totals['fpBrFeesCR'] = &$fpBrFeesCR;

		$total = $fpBrFeesDR->get(  ) + $fpBrFeesCR->get(  );
		$fpBrFees = &$totals['fpBrFees'];

		$prev = $fpBrFees->get(  );

		if ($total != $prev) {
			_showWhenError( 'Broker Fees Paid',  . 'fpBrFees amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for fees posted detail
 *
 * @param pointer to array we add items to
 */
	function _makeIntroducersDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$inRecPayDR = new ControlAccountTotal( 'Introducers payments and receipts, debits', true, (  . 'SELECT
				SUM(rtOriginal) 			as rtOriginal
			FROM introducerTransactions
			WHERE (rtTransType = \'C\' OR rtTransType = \'R\')
			AND rtOriginal < 0
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$totals['inRecPayDR'] = &$inRecPayDR;

		$inRecPayCR = new ControlAccountTotal( 'Introducers payments and receipts, credits', true, (  . 'SELECT
				SUM(rtOriginal) 			as rtOriginal
			FROM introducerTransactions
			WHERE (rtTransType = \'C\' OR rtTransType = \'R\')
			AND rtOriginal >= 0
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$totals['inRecPayCR'] = &$inRecPayCR;

		$total = $inRecPayDR->get(  ) + $inRecPayCR->get(  );
		$inRecPay = &$totals['inRecPay'];

		$prev = $inRecPay->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Payments',  . 'inRecPay amount was ' . $prev . ', now ' . $total );
		}

		$inWrittenOff = new ControlAccountTotal( 'Introducers written off', false, (  . 'SELECT
				SUM(rtWrittenOff) 			as rtWrittenOff
			FROM introducerTransactions
			WHERE (rtTransType = \'C\' OR rtTransType = \'R\')
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$totals['inWrittenOff'] = &$inWrittenOff;

		$inWrittenOffDR = new ControlAccountTotal( 'Introducers written off, debits', false, (  . 'SELECT
				SUM(rtWrittenOff) 			as rtWrittenOff
			FROM introducerTransactions
			WHERE (rtTransType = \'C\' OR rtTransType = \'R\')
			AND    rtWrittenOff >= 0
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$totals['inWrittenOffDR'] = &$inWrittenOffDR;

		$inWrittenOffCR = new ControlAccountTotal( 'Introducers written off, credits', false, (  . 'SELECT
				SUM(rtWrittenOff) 			as rtWrittenOff
			FROM introducerTransactions
			WHERE (rtTransType = \'C\' OR rtTransType = \'R\')
			AND    rtWrittenOff < 0
			AND	  rtPostingDate >= \'' . $START . '\' AND rtPostingDate <= \'' . $END . '\'' ) );
		$totals['inWrittenOffCR'] = &$inWrittenOffCR;

		$total = $inWrittenOffDR->get(  ) + $inWrittenOffCR->get(  );
		$prev = $inWrittenOff->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Written Off Detail',  . 'inWrittenOff amount was ' . $prev . ', now ' . $total );
		}

		$inWrOff = $totals['inWrOff'];
		$total = $inWrOff->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Written Off',  . 'inWrOff amount was ' . $prev . ', now ' . $total );
		}

		$inCommPostPaid = new ControlAccountTotal( 'Introducer Commission posted, where client has paid', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit = 1
			AND ptCode = ctPolicyTran
			AND ctBalance = 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostPaid->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance = 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostPaid'] = &$inCommPostPaid;

		$inCommPostPaidDR = new ControlAccountTotal( 'Introducer Commission posted, where client has paid. debits', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit = 1
			AND ptCode = ctPolicyTran
			AND ctBalance = 0
			AND ctOriginal >= 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostPaidDR->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance = 0
			AND ctOriginal >= 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostPaidDR'] = &$inCommPostPaidDR;

		$inCommPostPaidCR = new ControlAccountTotal( 'Introducer Commission posted, where client has paid. credits', false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptCode = ctPolicyTran
			AND ptDebit = 1
			AND ctBalance = 0
			AND ctOriginal < 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostPaidCR->addSelect( true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance = 0
			AND ctOriginal < 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostPaidCR'] = &$inCommPostPaidCR;

		$total = $inCommPostPaidDR->get(  ) - $inCommPostPaidCR->get(  );
		$prev = $inCommPostPaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Commission Paid',  . 'inCommPostPaid amount was ' . $prev . ', now ' . $total );
		}

		$inCommPostUnpaid = new ControlAccountTotal( 'Introducer Commission posted, where client has not paid', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit = 1
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostUnpaid->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostUnpaid'] = &$inCommPostUnpaid;

		$inCommPostUnpaidDR = new ControlAccountTotal( 'Introducer Commission posted, where client has not paid, debits', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit = 1
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND ctOriginal < 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostUnpaidDR->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND ctOriginal < 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostUnpaidDR'] = &$inCommPostUnpaidDR;

		$inCommPostUnpaidCR = new ControlAccountTotal( 'Introducer Commission posted, where client has not paid, credits', true, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND ctOriginal >= 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$inCommPostUnpaidCR->addSelect( false, (  . 'SELECT
				SUM(ptIntroducerComm) 			as ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE ptPostStatus = \'P\'
			AND ptDebit != 1
			AND ptCode = ctPolicyTran
			AND ctBalance != 0
			AND ctOriginal >= 0
			AND	  ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['inCommPostUnpaidCR'] = &$inCommPostUnpaidCR;

		$total = $inCommPostUnpaidDR->get(  ) + $inCommPostUnpaidCR->get(  );
		$prev = $inCommPostUnpaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Commission Unpaid',  . 'inCommPostUnpaid amount was ' . $prev . ', now ' . $total );
		}

		$total = $inCommPostPaid->get(  ) + $inCommPostUnpaid->get(  );
		$inCommPost = &$totals['inCommPost'];

		$prev = $inCommPost->get(  );

		if ($total != $prev) {
			_showWhenError( 'Introducer Commission Posted',  . 'inCommPost amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for commission paid/reconciled detail
 *
 * @param pointer to array we add items to
 */
	function _makeCommPaidRecDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$prDiscDR = new ControlAccountTotal( 'Client discounts given, debit', false, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit = 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['prDiscDR'] = &$prDiscDR;

		$prDiscCR = new ControlAccountTotal( 'Client discounts given, credit', true, (  . 'SELECT
				SUM(ptClientDiscount) 		as ptClientDiscount
			FROM policyTransactions
			WHERE ptPostStatus = \'P\'
			AND	  ptDebit != 1
			AND   ptPostingDate >= \'' . $START . '\' AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['prDiscCR'] = &$prDiscCR;

		$total = $prDiscDR->get(  ) + $prDiscCR->get(  );
		$prDisc = &$totals['prDisc'];

		$prev = $prDisc->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Discount',  . 'prDisc amount was ' . $prev . ', now ' . $total );
		}

		$prCommPaidPaid = new ControlAccountTotal( 'Commission Paid - where client has paid', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as itAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance = 0
			AND   itBalance = 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidPaid'] = &$prCommPaidPaid;

		$prCommPaidPaidDR = new ControlAccountTotal( 'Commission Paid - where client has paid, debits', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance = 0
			AND   itBalance = 0
			AND   itCommission > 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidPaidDR'] = &$prCommPaidPaidDR;

		$prCommPaidPaidCR = new ControlAccountTotal( 'Commission Paid - where client has paid, credits', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance = 0
			AND   itBalance = 0
			AND   itCommission <= 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidPaidCR'] = &$prCommPaidPaidCR;

		$total = $prCommPaidPaidDR->get(  ) + $prCommPaidPaidCR->get(  );
		$prev = $prCommPaidPaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Discount Paid',  . 'prCommPaidPaid amount was ' . $prev . ', now ' . $total );
		}

		$prCommPaidUnpaid = new ControlAccountTotal( 'Commission Paid - where client has not paid', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance != 0
			AND   itBalance = 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidUnpaid'] = &$prCommPaidUnpaid;

		$prCommPaidUnpaidDR = new ControlAccountTotal( 'Commission Paid - where client has not paid, debits', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance != 0
			AND   itBalance = 0
			AND   itOriginal < 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidUnpaidDR'] = &$prCommPaidUnpaidDR;

		$prCommPaidUnpaidCR = new ControlAccountTotal( 'Commission Paid - where client has not paid, credits', true, (  . 'SELECT
				SUM(itCommission) 			as itCommission,
				SUM(itAddlCommission) 		as iitAddlCommission,
				SUM(itEngineeringFeeComm) 	as itEngineeringFeeComm
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itTransType = \'I\'
			AND	  itPolicyTran = ptCode
			AND	  ctPolicyTran = ptCode
			AND   ctBalance != 0
			AND   itBalance = 0
			AND   itOriginal >= 0
			AND	  itPaidDate >= \'' . $START . '\' AND itPaidDate <= \'' . $END . '\'' ) );
		$totals['prCommPaidUnpaidCR'] = &$prCommPaidUnpaidCR;

		$total = $prCommPaidUnpaidDR->get(  ) + $prCommPaidUnpaidCR->get(  );
		$prev = $prCommPaidUnpaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Unpaid',  . 'prCommPaidUnPaid amount was ' . $prev . ', now ' . $total );
		}

		$prWrOff = &$totals['prWrOff'];

		$total = $prCommPaidPaid->get(  ) + $prCommPaidUnpaid->get(  );
		$prCommPaid = &$totals['prCommPaid'];

		$prev = $prCommPaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Paid Total',  . 'prCommPaid amount was ' . $prev . ', now ' . $total );
		}

		$prCommTransDR = new ControlAccountTotal( 'Commission transferred, debits)', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 1
			AND baAmount > 0
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$totals['prCommTransDR'] = &$prCommTransDR;

		$prCommTransCR = new ControlAccountTotal( 'Commission transferred, credits', false, (  . 'SELECT
				SUM(baAmount) 			as baAmount
			FROM bankAccountTrans
			WHERE baType = 1
			AND baAmount < 0
			AND	  baPostingDate >= \'' . $START . '\' AND baPostingDate <= \'' . $END . '\'' ) );
		$totals['prCommTransCR'] = &$prCommTransCR;

		$total = $prCommTransDR->get(  ) + $prCommTransCR->get(  );
		$prCommTransferred = &$totals['prCommTransferred'];

		$prev = $prCommTransferred->get(  );

		if ($total != $prev) {
			_showWhenError( 'Commission Transferred',  . 'prCommTrans amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for fees paid detail
 *
 * @param pointer to array we add items to
 */
	function _makeFeesPaidDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$fdBrFeesPaid = new ControlAccountTotal( 'Broker Fees Paid', false, null );
		$pfBrFeesPaid = &$totals['pfBrFeesPaid'];

		$amt = $pfBrFeesPaid->get(  );
		$fdBrFeesPaid->setAmount( $amt );
		$totals['fdBrFeesPaid'] = &$fdBrFeesPaid;

		$fdBrFeesPaidDR = new ControlAccountTotal( 'broker fees on trans where the client has fully paid', true, (  . 'SELECT
				SUM(ptBrokerFee) 	as ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ptClientTran=ctCode
			AND   ctBalance = 0
			AND   ctOriginal >= 0
			AND	  ctPaidDate >= \'' . $START . '\' AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['fdBrFeesPaidDR'] = &$fdBrFeesPaidDR;

		$fdBrFeesPaidCR = new ControlAccountTotal( 'broker fees on trans where the client has been fully paid', false, (  . 'SELECT
				SUM(ptBrokerFee) 	as ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ptClientTran=ctCode
			AND   ctBalance = 0
			AND   ctOriginal <= 0
			AND	  ctPaidDate >= \'' . $START . '\' AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['fdBrFeesPaidCR'] = &$fdBrFeesPaidCR;

		$total = $fdBrFeesPaidDR->get(  ) + $fdBrFeesPaidCR->get(  );
		$prev = $fdBrFeesPaid->get(  );

		if ($total != $prev) {
			_showWhenError( 'Broker Fees Paid',  . 'fdBrFeesPaid amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for IBA detail
 *
 * @param pointer to array we add items to
 */
	function _makeIBADetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$ibaReceiptsPaymentsDR = new ControlAccountTotal( 'IBA Receipts/Payments, debits', false, null );
		$clRecPayDR = &$totals['clRecPayDR'];
		$icRecPayDR = &$totals['icRecPayDR'];
		$inRecPayDR = &$totals['inRecPayDR'];

		$amt = 0 - ( $clRecPayDR->get(  ) + $icRecPayDR->get(  ) + $inRecPayDR->get(  ) );
		$ibaReceiptsPaymentsDR->setAmount( $amt );
		$totals['ibaReceiptsPaymentsDR'] = &$ibaReceiptsPaymentsDR;

		$ibaReceiptsPaymentsDRcls = new ControlAccountTotal( 'IBA Receipts/Payments, clients,  debits', true, null );
		$clRecPayDR = &$totals['clRecPayDR'];

		$amt = $clRecPayDR->get(  );
		$ibaReceiptsPaymentsDRcls->setAmount( 0 - $amt );
		$totals['ibaReceiptsPaymentsDRcls'] = &$ibaReceiptsPaymentsDRcls;

		$ibaReceiptsPaymentsDRins = new ControlAccountTotal( 'IBA Receipts/Payments, insurers,  debits', false, null );
		$icRecPayDR = &$totals['icRecPayDR'];

		$amt = 0 - $icRecPayDR->get(  );
		$ibaReceiptsPaymentsDRins->setAmount( $amt );
		$totals['ibaReceiptsPaymentsDRins'] = &$ibaReceiptsPaymentsDRins;

		$ibaReceiptsPaymentsDRint = new ControlAccountTotal( 'IBA Receipts/Payments, insurers,  debits', false, null );
		$inRecPayDR = &$totals['inRecPayDR'];

		$amt = 0 - $inRecPayDR->get(  );
		$ibaReceiptsPaymentsDRint->setAmount( $amt );
		$totals['ibaReceiptsPaymentsDRint'] = &$ibaReceiptsPaymentsDRint;

		$total = $ibaReceiptsPaymentsDRcls->get(  ) + $ibaReceiptsPaymentsDRint->get(  ) + $ibaReceiptsPaymentsDRins->get(  );
		$prev = $ibaReceiptsPaymentsDR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IBA Receipts DR',  . 'ibaReceiptsPaymentsDR amount was ' . $prev . ', now ' . $total );
		}

		$ibaReceiptsPaymentsCR = new ControlAccountTotal( 'IBA Receipts/Payments, credits', false, null );
		$clRecPayCR = &$totals['clRecPayCR'];
		$icRecPayCR = &$totals['icRecPayCR'];
		$inRecPayCR = &$totals['inRecPayCR'];

		$amt = 0 - ( $clRecPayCR->get(  ) + $icRecPayCR->get(  ) + $inRecPayCR->get(  ) );
		$ibaReceiptsPaymentsCR->setAmount( $amt );
		$totals['ibaReceiptsPaymentsCR'] = &$ibaReceiptsPaymentsCR;

		$ibaReceiptsPaymentsCRcls = new ControlAccountTotal( 'IBA Receipts/Payments, clients,  credits', false, null );
		$clRecPayCR = &$totals['clRecPayCR'];

		$amt = 0 - $clRecPayCR->get(  );
		$ibaReceiptsPaymentsCRcls->setAmount( $amt );
		$totals['ibaReceiptsPaymentsCRcls'] = &$ibaReceiptsPaymentsCRcls;

		$ibaReceiptsPaymentsCRins = new ControlAccountTotal( 'IBA Receipts/Payments, insurers,  credits', false, null );
		$icRecPayCR = &$totals['icRecPayCR'];

		$amt = 0 - $icRecPayCR->get(  );
		$ibaReceiptsPaymentsCRins->setAmount( $amt );
		$totals['ibaReceiptsPaymentsCRins'] = &$ibaReceiptsPaymentsCRins;

		$ibaReceiptsPaymentsCRint = new ControlAccountTotal( 'IBA Receipts/Payments, insurers,  credits', false, null );
		$inRecPayCR = &$totals['inRecPayCR'];

		$amt = 0 - $inRecPayCR->get(  );
		$ibaReceiptsPaymentsCRint->setAmount( $amt );
		$totals['ibaReceiptsPaymentsCRint'] = &$ibaReceiptsPaymentsCRint;

		$total = $ibaReceiptsPaymentsCRcls->get(  ) + $ibaReceiptsPaymentsCRint->get(  ) + $ibaReceiptsPaymentsCRins->get(  );
		$prev = $ibaReceiptsPaymentsCR->get(  );

		if ($total != $prev) {
			_showWhenError( 'IBA Receipts CR',  . 'ibaReceiptsPaymentsCR amount was ' . $prev . ', now ' . $total );
		}

		$ibaMiscReceipts = &$totals['ibaMiscReceipts'];

		$total = $ibaReceiptsPaymentsDR->get(  ) + $ibaReceiptsPaymentsCR->get(  );
		0 - $ibaMiscReceipts->get(  );
		$ibaReceiptsPayments = &$totals['ibaReceiptsPayments'];

		$prev = $ibaReceiptsPayments->get(  );

		if ($total != $prev) {
			_showWhenError( 'IBA Receipts',  . 'ibaReceiptsPayments amount was ' . $prev . ', now ' . $total );
		}

	}

	/**
 * We update the array with new items for Comm Fees detail
 *
 * @param pointer to array we add items to
 */
	function _makeCommFeesDetail($totals) {
		global $controlFromDate;
		global $controlToDate;

		$START = $controlFromDate;
		$END = $controlToDate;
		$cfCommUnrecPaid = new ControlAccountTotal( 'Commission posted this period, but not reconciled by the end of the period, but paid by client this period', false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptMainInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND	ctPaidDate >= \'' . $START . '\' 
			AND	ctPaidDate <= \'' . $END . '\' 
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( false,  . 'SELECT sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND	ctPaidDate >= \'' . $START . '\' 
			AND	ctPaidDate <= \'' . $END . '\' 
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE ptMainInsCoTran = itCode
			AND ptPostStatus = \'P\'
			AND	ptDirect = 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom >= \'' . $START . '\' AND ptEffectiveFrom <= \'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( false,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctPolicyTran = ptCode
			AND	ptDirect = 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND	ctPaidDate >= \'' . $START . '\' 
			AND	ctPaidDate <= \'' . $END . '\' 
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) + sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptMainInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND	ctPaidDate >= \'' . $START . '\' 
			AND	ctPaidDate <= \'' . $END . '\' 
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( true,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom >= \'' . $START . '\' AND ptEffectiveFrom <= \'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE ptMainInsCoTran = itCode
			AND ptPostStatus = \'P\'
			AND	ptDirect = 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom < \'' . $START . '\' OR ptEffectiveFrom > \'' . $END . '\')' );
		$cfCommUnrecPaid->addSelect( true,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctPolicyTran = ptCode			
			AND	ptDirect = 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND	ctPaidDate >= \'' . $START . '\' 
			AND	ctPaidDate <= \'' . $END . '\' 
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$totals['cfCommUnrecPaid'] = &$cfCommUnrecPaid;

		$cfCommUnrecUnpaid = new ControlAccountTotal( 'Commission posted this period, but not reconciled by the end of the period, but unpaid by client this period', false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptMainInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( false,  . 'SELECT sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE ptMainInsCoTran = itCode
			AND ptPostStatus = \'P\'
			AND	ptDirect = 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom > \'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( false,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctPolicyTran = ptCode
			AND	ptDirect = 1
			AND	ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) + sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptMainInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( true,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctCode = ptClientTran
			AND ptPostStatus = \'P\'
			AND	ptDirect != 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom > \'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE ptMainInsCoTran = itCode
			AND ptPostStatus = \'P\'
			AND	ptDirect = 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ( ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom > \'' . $END . '\')' );
		$cfCommUnrecUnpaid->addSelect( true,  . 'SELECT  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE ptAddOnInsCoTran = itCode
			AND ctPolicyTran = ptCode			
			AND	ptDirect = 1
			AND	ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')
			AND (itPaidDate = \'0000-00-00\' OR itPaidDate >\'' . $END . '\')' );
		$totals['cfCommUnrecUnpaid'] = &$cfCommUnrecUnpaid;

		$cfCommUnrec = new ControlAccountTotal( 'Commission posted this period, but not reconciled by the end of the period, sum of client paid and not paid this period', false, null );
		$amt = $cfCommUnrecPaid->get(  ) + $cfCommUnrecUnpaid->get(  );
		$cfCommUnrec->setAmount( $amt );
		$totals['cfCommUnrec'] = &$cfCommUnrec;

		$cfCommRecUnpaid = new ControlAccountTotal( 'Commission reconciled this period, but not paid by client by the end of the period', false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm)  as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itCode = ptMainInsCoTran
			AND ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')' );
		$cfCommRecUnpaid->addSelect( false,  . 'SELECT   sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE itCode = ptAddOnInsCoTran
			AND ptDirect = 1
			AND ptDebit = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom >\'' . $END . '\')' );
		$cfCommRecUnpaid->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm)  as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itCode = ptMainInsCoTran
			AND ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')' );
		$cfCommRecUnpaid->addSelect( true,  . 'SELECT   sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE itCode = ptAddOnInsCoTran
			AND ptDirect = 1
			AND ptDebit != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom >\'' . $END . '\')' );
		$cfCommRecUnpaid->addDetailSelect( false,  . 'SELECT  ptSysTran, ptPostingDate, ptCommission, ptAddlCommission, ptEngineeringFeeComm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itCode = ptMainInsCoTran
			AND ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')' );
		$cfCommRecUnpaid->addDetailSelect( false,  . 'SELECT  ptSysTran, ptPostingDate, ptAddOnCommission
			FROM policyTransactions, inscoTransactions
			WHERE itCode = ptAddOnInsCoTran
			AND ptDirect = 1
			AND ptDebit = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom >\'' . $END . '\')' );
		$cfCommRecUnpaid->addDetailSelect( true,  . 'SELECT  ptSysTran, ptPostingDate, ptCommission, ptAddlCommission, ptEngineeringFeeComm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itCode = ptMainInsCoTran
			AND ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate >\'' . $END . '\')' );
		$cfCommRecUnpaid->addDetailSelect( true,  . 'SELECT ptSysTran, ptPostingDate, ptAddOnCommission
			FROM policyTransactions, inscoTransactions
			WHERE itCode = ptAddOnInsCoTran
			AND ptDirect = 1
			AND ptDebit != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND (ptEffectiveFrom = \'0000-00-00\' OR ptEffectiveFrom >\'' . $END . '\')' );
		$totals['cfCommRecUnpaid'] = &$cfCommRecUnpaid;

		$cfCommRecPaidThis = new ControlAccountTotal( 'Commission reconciled this period, and paid by client (anytime)', false,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ptDirect != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND ctPaidDate != \'0000-00-00\'' );
		$cfCommRecPaidThis->addSelect( true,  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptDirect != 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND ctPaidDate != \'0000-00-00\'' );
		$cfCommRecPaidThis->addSelect( false, (  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions
			WHERE itPolicyTran = ptCode
			AND ptDebit = 1
			AND ptDirect = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$cfCommRecPaidThis->addSelect( true, (  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptDirect = 1
			AND itPaidDate >= \'' . $START . '\' 
			AND itPaidDate <= \'' . $END . '\'
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$totals['cfCommRecPaidThis'] = &$cfCommRecPaidThis;

		$cfCommRecPaidPrev = new ControlAccountTotal( 'Commission reconciled previously, and paid by client this period', true, (  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit = 1
			AND (itPaidDate != \'0000-00-00\' AND itPaidDate < \'' . $START . '\' )
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfCommRecPaidPrev->addSelect( false, (  . 'SELECT  sum(ptCommission) + sum(ptAddlCommission) + sum(ptEngineeringFeeComm) +  sum(ptAddOnCommission) as comm 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit != 1
			AND (itPaidDate != \'0000-00-00\' AND itPaidDate < \'' . $START . '\' )
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['cfCommRecPaidPrev'] = &$cfCommRecPaidPrev;

		$cfCommRecPaidPrev->addDetailSelect( true, (  . 'SELECT  ptSysTran, ptPostingDate, ptCommission, ptAddlCommission, ptEngineeringFeeComm , ptAddOnCommission
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit = 1
			AND (itPaidDate != \'0000-00-00\' AND itPaidDate < \'' . $START . '\' )
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfCommRecPaidPrev->addDetailSelect( false, (  . 'SELECT  ptSysTran, ptPostingDate, ptCommission, ptAddlCommission, ptEngineeringFeeComm , ptAddOnCommission
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND ctPolicyTran = ptCode
			AND ptDebit != 1
			AND (itPaidDate != \'0000-00-00\' AND itPaidDate < \'' . $START . '\' )
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfICComAdj = new ControlAccountTotal( 'Ins Co Adjustments this period where client has paid', false,  . 'SELECT  sum(itWrittenOff) as adj
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itPaidDate >= \'' . $START . '\' 
			AND   itPaidDate <= \'' . $END . '\'
			AND itCode = ptMainInsCoTran
			AND ptDirect != 1
			AND ptClientTran = ctCode
			AND ( ctPaidDate != \'0000-00-00\' AND ctPaidDate <= \'' . $END . '\')' );
		$cfICComAdj->addSelect( false,  . 'SELECT  sum(itWrittenOff) as adj
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itPaidDate >= \'' . $START . '\' 
			AND   itPaidDate <= \'' . $END . '\'
			AND itCode = ptMainInsCoTran
			AND ptDirect = 1
			AND ptClientTran = ctCode
			AND ( ptEffectiveFrom != \'0000-00-00\' AND ptEffectiveFrom <= \'' . $END . '\')' );
		$totals['cfICComAdj'] = &$cfICComAdj;

		$cfICComAdj->addDetailSelect( false,  . 'SELECT  itSysTran, itPostingDate,  itWrittenOff
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itPaidDate >= \'' . $START . '\' 
			AND   itPaidDate <= \'' . $END . '\'
			AND itCode = ptMainInsCoTran
			AND ptDirect != 1
			AND ptClientTran = ctCode
			AND ( ctPaidDate != \'0000-00-00\' AND ctPaidDate <= \'' . $END . '\')' );
		$cfICComAdj->addDetailSelect( false,  . 'SELECT  itSysTran, itPostingDate,  itWrittenOff
			FROM inscoTransactions, policyTransactions, clientTransactions
			WHERE itPaidDate >= \'' . $START . '\' 
			AND   itPaidDate <= \'' . $END . '\'
			AND itCode = ptMainInsCoTran
			AND ptDirect = 1
			AND ptClientTran = ctCode
			AND ( ptEffectiveFrom != \'0000-00-00\' AND ptEffectiveFrom <= \'' . $END . '\')' );
		$cfClDisc = new ControlAccountTotal( 'Client discount where paid by client this period', true,  . 'SELECT  sum(ptClientDiscount) as disc
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfClDisc->addSelect( false,  . 'SELECT  sum(ptClientDiscount) as disc
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$totals['cfClDisc'] = &$cfClDisc;

		$cfClDisc->addDetailSelect( true,  . 'SELECT  ptSysTran, ptPostingDate,  ptClientDiscount
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfClDisc->addDetailSelect( false,  . 'SELECT  ptSysTran, ptPostingDate,  ptClientDiscount
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfIntrodPaid = new ControlAccountTotal( 'Introducer commission where paid by client this period', true,  . 'SELECT  sum(ptIntroducerComm) as comm
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit = 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfIntrodPaid->addSelect( true, (  . 'SELECT  sum(ptIntroducerComm) as comm
			FROM policyTransactions
			WHERE  ptDirect = 1		
			AND ptDebit = 1
			AND ptEffectiveFrom >= \'' . $START . '\' 
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$cfIntrodPaid->addSelect( false,  . 'SELECT  sum(ptIntroducerComm) as comm
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit != 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfIntrodPaid->addSelect( false, (  . 'SELECT  sum(ptIntroducerComm) as comm
			FROM policyTransactions
			WHERE  ptDirect = 1		
			AND ptDebit != 1
			AND ptEffectiveFrom >= \'' . $START . '\' 
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$totals['cfIntrodPaid'] = &$cfIntrodPaid;

		$cfIntrodPaid->addDetailSelect( true,  . 'SELECT  ptSysTran, ptPostingDate,  ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit = 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfIntrodPaid->addDetailSelect( true, (  . 'SELECT  ptSysTran, ptPostingDate,  ptIntroducerComm
			FROM policyTransactions
			WHERE  ptDirect = 1		
			AND ptDebit = 1
			AND ptEffectiveFrom >= \'' . $START . '\' 
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$cfIntrodPaid->addDetailSelect( false,  . 'SELECT  ptSysTran, ptPostingDate,  ptIntroducerComm
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDirect != 1
			AND ptDebit != 1
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'
			AND ctBalance = 0' );
		$cfIntrodPaid->addDetailSelect( false, (  . 'SELECT  ptSysTran, ptPostingDate,  ptIntroducerComm
			FROM policyTransactions
			WHERE  ptDirect = 1		
			AND ptDebit != 1
			AND ptEffectiveFrom >= \'' . $START . '\' 
			AND ptEffectiveFrom <= \'' . $END . '\'' ) );
		$cfCommRecPaid = new ControlAccountTotal( 'Commission reconciled this period, and paid by client before the end of this period', true, null );
		$amt = $cfCommRecPaidThis->get(  ) + $cfCommRecPaidPrev->get(  ) + $cfICComAdj->get(  ) + $cfClDisc->get(  ) + $cfIntrodPaid->get(  );
		$cfCommRecPaid->setAmount( $amt );
		$totals['cfCommRecPaid'] = &$cfCommRecPaid;

		$cfFeesPosted = new ControlAccountTotal( 'Broker fees posted where unpaid by client by the end of this period', true, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit != 1
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate > \'' . $END . '\')
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'' ) );
		$cfFeesPosted->addSelect( false, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE  ctPolicyTran = ptCode
			AND ptDebit = 1
			AND (ctPaidDate = \'0000-00-00\' OR ctPaidDate > \'' . $END . '\')
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'' ) );
		$totals['cfFeesPosted'] = &$cfFeesPosted;

		$cfFeesPaidThis = new ControlAccountTotal( 'Fees posted this period, and paid by client this period', false, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaidThis->addSelect( true, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['cfFeesPaidThis'] = &$cfFeesPaidThis;

		$cfFeesPaidThis->addDetailSelect( false, (  . 'SELECT  ptSysTran, ptPostingDate,  ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaidThis->addDetailSelect( true, (  . 'SELECT  ptSysTran, ptPostingDate,  ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptPostingDate >= \'' . $START . '\' 
			AND ptPostingDate <= \'' . $END . '\'
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaidPrev = new ControlAccountTotal( 'Fees posted previously, and paid by client this period', false, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ptPostingDate < \'' . $START . '\' 
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaidPrev->addSelect( true, (  . 'SELECT  sum(ptBrokerFee) as fees
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptPostingDate < \'' . $START . '\' 
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['cfFeesPaidPrev'] = &$cfFeesPaidPrev;

		$cfFeesPaidPrev->addDetailSelect( false, (  . 'SELECT  ptSysTran, ptPostingDate,  ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit = 1
			AND ptPostingDate < \'' . $START . '\' 
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaidPrev->addDetailSelect( true, (  . 'SELECT  ptSysTran, ptPostingDate,  ptBrokerFee
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND ptDebit != 1
			AND ptPostingDate < \'' . $START . '\' 
			AND ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfFeesPaid = new ControlAccountTotal( 'Broker fees posted where paid by client this period', false, null );
		$amt = $cfFeesPaidThis->get(  ) + $cfFeesPaidPrev->get(  );
		$cfFeesPaid->setAmount( $amt );
		$totals['cfFeesPaid'] = &$cfFeesPaid;

		$cfOtherClAdj = new ControlAccountTotal( 'Client write off/write back this period', true, (  . 'SELECT  sum(ctWrittenOff) as adj
			FROM  clientTransactions
			WHERE  ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$totals['cfOtherClAdj'] = &$cfOtherClAdj;

		$cfOtherClAdj->addDetailSelect( true, (  . 'SELECT  ctSysTran, ctPostingDate,  ctWrittenOff
			FROM  clientTransactions
			WHERE  ctPaidDate >= \'' . $START . '\' 
			AND ctPaidDate <= \'' . $END . '\'' ) );
		$cfOtherIntrod = new ControlAccountTotal( 'Introducer write off/write back this period', false,  . 'SELECT  sum(rtWrittenOff) as adj
			FROM  introducerTransactions
			WHERE  rtPaidDate >= \'' . $START . '\' 
			AND rtPaidDate <= \'' . $END . '\'
			AND rtTransType = \'I\'' );
		$totals['cfOtherIntrod'] = &$cfOtherIntrod;

		$cfOtherIntrod->addDetailSelect( false,  . 'SELECT  rtSysTran, rtPostingDate,  rtWrittenOff
			FROM  introducerTransactions
			WHERE  rtPaidDate >= \'' . $START . '\' 
			AND rtPaidDate <= \'' . $END . '\'
			AND rtTransType = \'I\'' );
		$cfOtherPaid = new ControlAccountTotal( 'Sum of introducer and client adj write off/write back this period', false, null );
		$amt = $cfOtherIntrod->get(  ) + $cfOtherClAdj->get(  );
		$cfOtherPaid->setAmount( $amt );
		$totals['cfOtherPaid'] = &$cfOtherPaid;

		$cfTransferComm = new ControlAccountTotal( 'Transferred commission this period', true,  . 'SELECT  sum(baAmount) as tot
			FROM  bankAccountTrans
			WHERE  baPostingDate >= \'' . $START . '\' 
			AND baPostingDate <= \'' . $END . '\'
			AND (baType = 1) ' );
		$totals['cfTransferComm'] = &$cfTransferComm;

		$cfTransferFees = new ControlAccountTotal( 'Transferred fees this period', false,  . 'SELECT  sum(baAmount) as tot
			FROM  bankAccountTrans
			WHERE  baPostingDate >= \'' . $START . '\' 
			AND baPostingDate <= \'' . $END . '\'
			AND (baType = 2) ' );
		$totals['cfTransferFees'] = &$cfTransferFees;

		$cfTransferOther = new ControlAccountTotal( 'Other income transferred this period', false, null );
		$totals['cfTransferOther'] = &$cfTransferOther;

		$cfTransfer = new ControlAccountTotal( 'Transferred total this period', false, null );
		$total = $cfTransferComm->get(  ) + $cfTransferFees->get(  ) + $cfTransferOther->get(  );
		$cfTransfer->setAmount( $total );
		$totals['cfTransfer'] = &$cfTransfer;

		$cfTotalCommUnpaid = new ControlAccountTotal( 'Total Comm. Reconciled where Client Not Paid', false, 'SELECT
				SUM(ptCommission) +
				SUM(ptAddlCommission) +
				SUM(ptEngineeringFeeComm)+ 
				SUM(ptAddOnCommission) as total 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND 	ptDebit = 1
			AND   ctPolicyTran = ptCode
			AND	  ctBalance != 0
			AND	  itBalance = 0' );
		$cfTotalCommUnpaid->addSelect( true, 'SELECT
				SUM(ptCommission) +
				SUM(ptAddlCommission) +
				SUM(ptEngineeringFeeComm)+ 
				SUM(ptAddOnCommission) as total 
			FROM policyTransactions, inscoTransactions, clientTransactions
			WHERE itPolicyTran = ptCode
			AND 	ptDebit != 1
			AND   ctPolicyTran = ptCode
			AND	  ctBalance != 0
			AND	  itBalance = 0' );
		$totals['cfTotalCommUnpaid'] = &$cfTotalCommUnpaid;

		$cfTotalFeesUnpaid = new ControlAccountTotal( 'Total Broker fees where Client Not Paid', false, 'SELECT
				SUM(ptBrokerFee) as total 
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND 	ptDebit = 1
			AND	  ctBalance != 0' );
		$cfTotalFeesUnpaid->addSelect( true, 'SELECT
				SUM(ptBrokerFee) as total 
			FROM policyTransactions, clientTransactions
			WHERE ctPolicyTran = ptCode
			AND 	ptDebit != 1
			AND	  ctBalance != 0' );
		$totals['cfTotalFeesUnpaid'] = &$cfTotalFeesUnpaid;

		$cfTotalAwaiting = new ControlAccountTotal( 'Total Awaiting Client Paid', false, null );
		$amt = $cfTotalCommUnpaid->get(  ) + $cfTotalFeesUnpaid->get(  );
		$cfTotalAwaiting->setAmount( $amt );
		$totals['cfTotalAwaiting'] = &$cfTotalAwaiting;

		$cfTotal = new ControlAccountTotal( 'Commission & fees period total', false, null );
		$amt = $cfCommRecPaid->get(  );
		$amt += $cfFeesPaid->get(  );
		$amt += $cfOtherPaid->get(  );
		$amt += $cfTransfer->get(  );
		$cfTotal->setAmount( $amt );
		$totals['cfTotal'] = &$cfTotal;

	}

	function _doReport($template, $input, $doIntegrity = true) {
		global $controlFromDate;
		global $controlToDate;
		global $totals;
		global $selectedPeriodCode;

		$selectedPeriodCode = 0;
		$controlFromDate = udbMakeFieldSafe( $template->getSQLDate( 'dateFrom' ) );
		$controlToDate = udbMakeFieldSafe( $template->getSQLDate( 'dateTo' ) );
		$selectedPeriod = (int)$template->get( 'selectedPeriod' );
		$selectedYear = (int)$template->get( 'selectedYear' );

		if (( ( $selectedPeriod == 0 && 0 < $selectedYear ) || ( 0 < $selectedPeriod && $selectedYear == 0 ) )) {
			$template->setMessage( 'You need to specify both a year and a period' );
			return false;
		}


		if (( 0 < $selectedPeriod && 0 < $selectedYear )) {
			$ay = new AccountingYear( $selectedYear );
			$yearDesc = $ay->get( 'ayName' );
			$q =  . 'SELECT apCode FROM accountingPeriods
			  WHERE apYear = ' . $selectedYear . '
			  AND apPeriod = ' . $selectedPeriod;
			$result = udbQuery( $q );

			if ($result == false) {
				trigger_error( udbLastError(  ), 256 );
			}

			$rows = udbNumberOfRows( $result );

			if ($rows != 1) {
				$template->setMessage( 'This period has not been set up in the system tables' );
				return false;
			}

			$row = udbGetRow( $result );
			$apCode = $row['apCode'];
			$ap = new AccountingPeriod( $apCode );
			$from = $ap->get( 'apFromDate' );
			$to = $ap->get( 'apToDate' );

			if (( $from == '' || $to == '' )) {
				$template->setMessage( 'This period has not been set up properly with dates' );
				return false;
			}


			if (( 0 < strlen( trim( $controlFromDate ) ) || 0 < strlen( trim( $controlToDate ) ) )) {
				$template->setMessage( 'You can\'t specify a period and a range of dates' );
				return false;
			}

			$controlFromDate = $from;
			$controlToDate = $to;
			$periodDesc =  . 'For Period ' . $selectedPeriod . ' Year ' . $yearDesc;
			$selectedPeriodCode = $apCode;
		} 
else {
			if (( strlen( trim( $controlFromDate ) ) == 0 || strlen( trim( $controlToDate ) ) == 0 )) {
				$template->setMessage( 'You need to specify a from and to date, or an accounting period' );
				return false;
			}


			if ($controlToDate < $controlFromDate) {
				$template->setMessage( 'Dates are in the wrong order' );
				return false;
			}

			$f = $template->get( 'dateFrom' );
			$t = $template->get( 'dateTo' );
			$periodDesc =  . 'For Dates ' . $f . ' to ' . $t;
		}

		$template->set( 'periodDesc', $periodDesc );
		$do = true;

		if (( defined( 'DONT_DO_INTEGRITY' ) && DONT_DO_INTEGRITY == true )) {
			$do = false;
		}


		if ($do == true) {
			$url = SITE_ROOT_INTERNAL_URL . 'admin/accountingIntegrity.php?ignoreUnalloc';
			$x = file_get_contents( $url );

			if ($x != 'OK') {
				$template->setMessage( 'A system imbalance exists....the system manager has been informed' );
				return false;
			}
		}

		$totals = _makeTotals(  );
		_makeOpeningTotals(  );
		_makeClientDetail( &$totals );
		_makeInsurerDetail( &$totals );
		_makeCommissionPostedDetail( &$totals );
		_makeFeesPostedDetail( &$totals );
		_makeIntroducersDetail( &$totals );
		_makeCommPaidRecDetail( &$totals );
		_makeFeesPaidDetail( &$totals );
		_makeIBADetail( &$totals );
		_makeCommFeesDetail( &$totals );
		_makeClosingTotals(  );
		$template->setFields( $totals );
		return false;
	}

	function _toDetail($template, $input) {
		$type = $input['detailType'];

		if ($type == '') {
			return false;
		}


		if ($type == 'C') {
			$template->setHTML( 'controlAccountClients.html' );
		}


		if ($type == 'I') {
			$template->setHTML( 'controlAccountInsurers.html' );
		}


		if ($type == 'CP') {
			$template->setHTML( 'controlAccountCommPosted.html' );
		}


		if ($type == 'FP') {
			$template->setHTML( 'controlAccountFeesPosted.html' );
		}


		if ($type == 'IN') {
			$template->setHTML( 'controlAccountIntroducers.html' );
		}


		if ($type == 'CR') {
			$template->setHTML( 'controlAccountCommPaidRec.html' );
		}


		if ($type == 'FD') {
			$template->setHTML( 'controlAccountFeesPaid.html' );
		}


		if ($type == 'IB') {
			$template->setHTML( 'controlAccountIBA.html' );
		}


		if ($type == 'COMMFEES') {
			$template->setHTML( 'controlAccountCommFees.html' );
		}

		return false;
	}

	function _backButton($template, $input) {
		$template->setHTML( 'controlAccountSummary.html' );
		return false;
	}

	function _backToCommFees($template, $input) {
		$template->setHTML( 'controlAccountCommFees.html' );
		return false;
	}

	function _goToCell($template, $input) {
		if (!isset( $input['cellToView'] )) {
			return false;
		}

		$cell = $input['cellToView'];

		if ($cell == '') {
			return false;
		}

		$template->setHTML( 'controlAccountDetail.html' );
		_showCell( &$template, $cell );
		return false;
	}

	function _goToTran($template, $input) {
		global $session;

		if (!isset( $input['tranToView'] )) {
			return false;
		}

		$x = $input['tranToView'];

		if ($x <= 0) {
			return false;
		}

		$ret = '../accounts/controlAccount.php';
		$session->set( 'returnTo', $ret );
		$st = new SystemTransaction( $x );
		$type = $st->get( 'tnType' );
		$tran = $st->get( 'tnTran' );

		if ($type == 'PT') {
			fLocationHeader(  . '../policies/policyTransEdit.php?transToView=' . $tran );
		}


		if ($type == 'IR') {
			fLocationHeader(  . '../inscos/inscoRecon.php?view=' . $tran );
		}


		if ($type == 'CT') {
			fLocationHeader(  . '../clients/cashReceiptsEdit.php?view=' . $tran );
		}


		if ($type == 'NR') {
			fLocationHeader(  . '../introducers/introducerRecon.php?view=' . $tran );
		}

		return false;
	}

	/**
 * This shows a message in the messg fld, and generates a worning which may send an email
 *
 * @param string $type - brief desc for messg fld
 * @param string $messg - full details to email
 */
	function _showWhenError($type, $messg) {
		global $controlAccountTemplate;

		$type = 'There has been an error in calulations - ' . $type;
		$controlAccountTemplate->setMessage( $type );
	}

	/**
 * this is called by other scripts to get all totals - called from period end
 * returns in comma separated string
 *
 */
	function _getTotals($template, $input) {
		global $controlFromDate;
		global $controlToDate;
		global $accountingPeriodCode;
		global $fields;

		if ($accountingPeriodCode <= 0) {
			trigger_error( 'no accounting period', 256 );
		}

		$ap = new AccountingPeriod( $accountingPeriodCode );
		$controlFromDate = '1970-01-01';
		$controlToDate = $ap->get( 'apToDate' );

		if (isset( $_GET['toDate'] )) {
			$controlToDate = $_GET['toDate'];
		}

		_makeTotals(  );
		_makeCommPaidRecDetail( &$fields );
		_makeCommFeesDetail( &$fields );
		$x = $fields['clTotal'];
		$clTotal = $x->get(  );
		$x = $fields['icTotal'];
		$icTotal = $x->get(  );
		$x = $fields['inTotal'];
		$inTotal = $x->get(  );
		$x = $fields['ibaTotal'];
		$ibaTotal = $x->get(  );
		$x = $fields['controlTotal'];
		$controlTotal = $x->get(  );
		$x = $fields['cpTotal'];
		$cpTotal = $x->get(  );
		$x = $fields['fpTotal'];
		$fpTotal = $x->get(  );
		$x = $fields['prTotal'];
		$prTotal = $x->get(  );
		$x = $fields['pfTotal'];
		$pfTotal = $x->get(  );
		$x = $fields['oiTotal'];
		$oiTotal = $x->get(  );
		$x = $fields['ocTotal'];
		$ocTotal = $x->get(  );
		$x = $fields['cfTotal'];
		$cfTotal = $x->get(  );
		$out = ( ( ( ( ( ( ( ( ( ( ( (  . $clTotal . ',' ) . $icTotal . ',' ) . $inTotal . ',' ) . $ibaTotal . ',' ) . $controlTotal . ',' ) . $cpTotal . ',' ) . $fpTotal . ',' ) . $prTotal . ',' ) . $pfTotal . ',' ) . $oiTotal . ',' ) . $ocTotal . ',' ) . $cfTotal . '
' );
		echo $out;
		exit(  );
	}

	/**
	*	
	*	@return		code of docm created
	* 
	*  print OK if successful else not ok
	*/
	function _doPeriodEnd($template, $input) {
		global $user;
		global $accountingYear;
		global $accountingYearCode;
		global $accountingYearDesc;
		global $accountingPeriod;
		global $accountingPeriodCode;
		global $periodFrom;
		global $periodTo;

		if (isset( $input['user'] )) {
			$usCode = $input['user'];
			$user = new User( $usCode );
		}


		if (is_a( $user, 'User' )) {
			$usCode = $user->getKeyValue(  );
		} 
else {
			if (DEBUG_MODE == true) {
				$usCode = null;
			} 
else {
				trigger_error( 'no user', 256 );
			}
		}

		$document = new Document( null );
		$document->insert( null );
		$docmNo = $document->getKeyValue(  );
		$document->set( 'doWhenOriginated', uGetTimeNow(  ) );
		$document->set( 'doOriginator', $usCode );
		$document->set( 'doUploadType', 1 );
		$document->set( 'doLocked', 1 );
		$document->set( 'doWhenEntered', uGetTimeNow(  ) );
		$document->set( 'doEnteredBy', $usCode );
		$periodEnd = $periodEnd = uFormatSQLDate3( $periodTo );
		$periodMessg =  . $accountingPeriod . ' year ' . $accountingYearDesc . ': P/E ' . $periodEnd;
		$subject =  . 'Control Account for period ' . $periodMessg;
		$document->set( 'doSubject', $subject );
		$doDocmType = MANAGEMENT_DOCM_TYPE;
		$document->set( 'doDocmType', $doDocmType );
		$document->set( 'doUpdateorCreate', uGetTimeNow(  ) );
		$pdfText = _makePDF( $docmNo, $template );
		$name = sprintf( '%07d', $docmNo ) . '.pdf';
		$type = 'application/pdf';
		$document->addDocumentUsingText( $name, $type, $pdfText );
		$document->update(  );
		$doCode = $document->getKeyValue(  );
		echo 'OK';
		return $doCode;
	}

	/**
	*	@param 		type - invoice or receipt
	*	@param 		was docm posted?
	*	@param 		docm number
	*	@return 	actual text of the pdf
	*/
	function _makePDF(&$docmNo, $template) {
		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		$pdf = new UPDF( 'l', false );
		$caAsXMLForPDF = _makeXMLTextForPDF( $docmNo, $template );
		$xml = new UPDFXML( $caAsXMLForPDF, $pdf );
		$pdf->close(  );
		$text = $pdf->returnAsString(  );
		return $text;
	}

	/**
	*	@param 		type - invoice or receipt
	*	@param 		was docm posted?
	*	@param 		docm number
	*	@return 	actual text of the pdf
	*/
	function _makeXMLTextForPDF(&$docmNo, $mainTemplate) {
		global $accountingYear;
		global $accountingYearDesc;
		global $accountingYearCode;
		global $accountingPeriod;
		global $periodTo;

		require_once( UTIL_PATH . 'UXML.class.php' );
		require_once( UTIL_PATH . 'UXMLTag.class.php' );
		require_once( UTIL_PATH . 'UPDF.class.php' );
		require_once( UTIL_PATH . 'UPDFXML.class.php' );
		require_once( '../accounts/templateClasses/ControlAccountPDFTemplate.class.php' );
		$input = array(  );
		$mainTemplate->set( 'selectedPeriod', $accountingPeriod );
		$mainTemplate->set( 'selectedYear', $accountingYearCode );
		_doReport( &$mainTemplate, $input, false );
		$xmlText = file_get_contents( PDFS_PATH . 'controlAccount.xml' );
		$template = new ControlAccountPDFTemplate( null );
		$template->setFields( $mainTemplate->getFields(  ) );
		$template->setPeriod( $accountingPeriod );
		$template->setYear( $accountingYearDesc );
		$template->setPeriodDescription( uFormatSQLDate3( $periodTo ) );
		$template->setParseForXML(  );
		$template->set( 'docmNo', sprintf( '%07d', $docmNo ) );
		$template->set( 'date', uFormatSQLDate3( uGetTodayAsSQLDate(  ) ) );
		$template->setHTMLFromText( $xmlText );
		$template->parseAll(  );
		$newXMLText = $template->getOutput(  );
		return $newXMLText;
	}

	global $fields;
	global $controlAccountTemplate;

	require( 'classes/ControlAccountTotal.class.php' );
	require( '../include/startup.php' );
	$controlAccountTemplate = &$session->get( 'controlAccountTemplate' );

	if ($controlAccountTemplate == null) {
		$controlAccountTemplate = new ControlAccountTemplate( 'controlAccountSummary.html' );
		$controlAccountTemplate->setProcess( '_backButton', 'back' );
		$controlAccountTemplate->setProcess( '_backToCommFees', 'backToCommFees' );
		$controlAccountTemplate->setProcess( '_doReport', 'doReport' );
		$controlAccountTemplate->setProcess( '_toDetail', 'detailType' );
		$controlAccountTemplate->setProcess( '_getTotals', 'getTotals' );
		$controlAccountTemplate->setProcess( '_doPeriodEnd', 'periodEnd' );
		$controlAccountTemplate->setProcess( '_goToCell', 'cellToView' );
		$controlAccountTemplate->setProcess( '_goToTran', 'tranToView' );
	}

	$session->set( 'controlAccountTemplate', $controlAccountTemplate );
	$controlAccountTemplate->process(  );
	$session->set( 'controlAccountTemplate', $controlAccountTemplate );
?>