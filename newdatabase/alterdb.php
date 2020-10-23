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

	$q = "ALTER TABLE accountingAudit CHANGE COLUMN  aaType aaType	CHAR(2)";
	_doQuery("change aaType	", $q);

	$q = "ALTER TABLE clientTransactions ADD COLUMN  ctJournal	INT";
	_doQuery("add ctJournal	", $q);
	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itJournal	INT";
	_doQuery("add itJournal	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtJournal	INT";
	_doQuery("add rtJournal	", $q);
	
	$q = "ALTER TABLE insuranceCompanies ADD COLUMN  icIPTAmendable	INT";
	_doQuery("add icIPTAmendable	", $q);


	

	//******************  Journals ****************************************
	

	$q = "";
	$q .=	"jnCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .= 	"jnType				CHAR(2),";			
	
	$q .=	"jnMaster			INT,";			// code to cl, ins co or intord

	$q .=	"jnNarrative		VARCHAR(200),";

	
	$q .=	"jnPostingDate		CHAR(14),";
	$q .= 	"jnAccountingYear	INT,";				// current acc year
	$q .= 	"jnAccountingPeriod	INT,";				// current acc period, within year

	$q .=	"jnTran				INT,";				// key of trans created
	$q .=	"jnAmount			BIGINT,";

	
	$q .=	"jnCreatedBy		INT,";		// who and when original trans created
	$q .=	"jnCreatedOn		CHAR(14),";

	$q .=	"jnLastUpdateBy		INT,";
	$q .=	"jnLastUpdateOn		CHAR(14),";

	$q .=	"lastAccessTime		CHAR(14))";	   // for concurrency see URecord


	udbCreateTable("journals",$q);

	// so can do commit and rollback
	udbSetTableForTransactions("journals");

	// we want to start order numbers at a high value
	$q = "ALTER TABLE journals AUTO_INCREMENT = 9000000";

	_doQuery("alter journals	", $q);
	
	
	// ---   above done 1-nov-06 in live -----
	
	
	
	// add new other trans types
	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (17, 'Client Write Offs Adjs', 0, 17, 1)";
		_doQuery("adding bankTransTypes	17", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (18, 'Client Write Back Adjs.', 1, 18, 1)";
		_doQuery("adding bankTransTypes	17", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (19, 'Introd. Comm. W/Off Adjs.', 0, 19, 1)";
		_doQuery("adding bankTransTypes	17", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (20, 'Introd. Comm. W/Back Adjs.', 1, 20, 1)";
		_doQuery("adding bankTransTypes	17", $q);

	
	
	
	// debits and credits round the wrong way
	$q = "UPDATE  bankTransTypes SET ByDebit = 0 where byCode = 5";
	_doQuery("change bank trans type sign	", $q);
	
	$q = "UPDATE  bankTransTypes SET ByDebit = 1 where byCode = 6";
	_doQuery("change bank trans type sign	", $q);

	// trans type wording
	$q = "UPDATE  bankTransTypes SET byName = 'Other Net Earned Charges' where byCode = 10";
	_doQuery("change bank trans type	", $q);

	

	
	// this has a new format - lots of fields dropped
	$q = "";
	$q .= "afCode			INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";
	$q .= "afYear			INT,";	// 2005 is 2005/2006 etc
	$q .= "afPeriod			INT,";	//  1, 2 etc
	$q .= "afPeriodCode		INT UNIQUE,";	//  key to period table - for period done

	$q .= "afFromDate		DATE,";	// in case tables changed
	$q .= "afToDate			DATE,";

		// carried fwd figures at end of period
	$q .= "afClients			BIGINT,";
	$q .= "afInsurers			BIGINT,";
	$q .= "afCommPosted			BIGINT,";
	$q .= "afFeesPosted			BIGINT,";
	$q .= "afIntroducers		BIGINT,";
	$q .= "afCommPaid			BIGINT,";
	$q .= "afFeesPaid			BIGINT,";
	$q .= "afOtherIncome		BIGINT,";
	$q .= "afOtherCharges		BIGINT,";
	$q .= "afBank				BIGINT,";

	// bank statement period cr fwd
	$q .= "afBankAccount		BIGINT,";
	
	// IBA Other trans total
	$q .= "afIBAOther		BIGINT,";
	
	//  b/f and c/f on the comm & fess page of the C/A
	$q .= "afCommFees		BIGINT)";
	udbCreateTable("accountingFigures",$q);

	

	
	// -- insert document type -------------------------
	
	udbCreateIndex("documents","doDocmTypeIndex","doDocmType");


	$q = "INSERT INTO  documentTypes (dtCode, dtName, dtClient, dtPolicy, dtInsco, dtIntroducer, dtSequence)
			   VALUES (55, 'Client Statement', 1,  0, 0, 0, 55)";
	_doQuery("add docm type for client statement	", $q);

	$q = "INSERT INTO  documentTypes (dtCode, dtName, dtClient, dtPolicy, dtInsco, dtIntroducer, dtSequence)
			   VALUES (56, 'Introducer Statement', 0,  0, 0, 1, 56)";
	_doQuery("add docm type for introducer statement	", $q);

	// -- insert document type -------------------------
	$q = "INSERT INTO  documentTypes (dtCode, dtName, dtClient, dtPolicy, dtInsco, dtIntroducer, dtSequence)
			   VALUES (57, 'Management Documents', 1,  0, 0, 0, 57)";
	_doQuery("add docm type for mang documents	", $q);

	$q = "ALTER TABLE insuranceCompanies ADD COLUMN  icStatus	INT";
	_doQuery("add icStatus	", $q);
	
	$q = "ALTER TABLE introducers ADD COLUMN  inStatementType		INT";
	_doQuery("add inStatementType	", $q);
	
	$q = "ALTER TABLE introducers ADD COLUMN  inStatus		INT";
	_doQuery("add inStatus	", $q);
	
	$q = "ALTER TABLE introducers ADD COLUMN  inDurable		INT";
	_doQuery("add inDurable	", $q);
	
	$q = "ALTER TABLE clients ADD COLUMN  clStatementType		INT";
	_doQuery("add clStatementType	", $q);
	
	
	$q = "UPDATE insuranceCompanies SET  icStatus	= 1";
	_doQuery("update icStatus	", $q);
	
	$q = "UPDATE introducers SET  inStatus	= 1";
	_doQuery("update inStatus	", $q);
	
	// make sure introducers and clients get statements if a bal
	$q = "UPDATE introducers SET  inStatementType	= 1";
	_doQuery("update inStatementType	", $q);
	
	$q = "UPDATE clients SET  clStatementType	= 1";
	_doQuery("update clStatementType	", $q);

	


	// take item from cash batch and correct
	$q = "delete from cashBatchItems   WHERE biCode = 182";
	_doQuery("delet cash batch item 182	", $q);

	$q = "UPDATE cashBatches SET btTotal = 0, btUnallocated = 0 where btCode = 8000076";
	_doQuery("change batch 8000076	", $q);


	// early trans was wrong
	$q = "UPDATE  inscoTransactions SET itWrittenOff = -223 where itCode = 580";
	_doQuery("update ic alloc 1	", $q);


	// early trans was wrong
	$q = "UPDATE  inscoTransAllocations
	SET iaAmount = -81250
	where iaCode = 1";
	_doQuery("update ic alloc 1	", $q);

	// early trans was wrong
	$q = "UPDATE  inscoTransactions
	SET itOriginal = -81250, itPaid = -81250
	where itcode = 579";
	_doQuery("update ic tran 579	", $q);


	// add back an ins co which shouldn't have been deleted
	$q = "INSERT INTO   insuranceCompanies   (icCode)
		VALUES (1000009)";
		_doQuery("adding ins co 1000009	", $q);

	// add back an ins co which shouldn't have been deleted
	$q = "INSERT INTO   insuranceCompanies   (icCode)
		VALUES (1000046)";
		_doQuery("adding ins co 1000046	", $q);

	// add back an ins co which shouldn't have been deleted
	$q = "INSERT INTO   insuranceCompanies   (icCode)
		VALUES (1000058)";
		_doQuery("adding ins co 1000058	", $q);

	// ********* introducer new fld ************************************
	$q = "ALTER TABLE introducers ADD COLUMN  inLastUpdateBy	INT";
	_doQuery("add inLastUpdateBy	", $q);

	// ********* introducer new fld ************************************
	$q = "ALTER TABLE introducers ADD COLUMN  inLastUpdateOn	CHAR(14)";
	_doQuery("add inLastUpdateOn	", $q);

	// ********* user  new fld ************************************
	$q = "ALTER TABLE users ADD COLUMN  usDisabled		BOOL";
	_doQuery("add usDisabled	", $q);

	// ********* policy trans new fld ************************************
	$q = "ALTER TABLE policyTransactions ADD COLUMN  ptReversesTran		INT";
	_doQuery("add ptReversesTran	", $q);


	// ********* policy trans types ************************************
	$q = "ALTER TABLE policyTransactionTypes ADD COLUMN  pyReverseAs		INT";
	_doQuery("add pyReverseAs	", $q);

	// --  bank trans types -----
	$q = "INSERT INTO  policyTransactionTypes (pyCode, pyName, pyFromPolicy, pyDebit, pyReverseAs, pySequence)
		VALUES (10, 'Reversal', 0, 1, 0, 10)";
		_doQuery("adding policyTransactionTypes 10	", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=1" ;
	_doQuery("set pol type reversal 1 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=2" ;
	_doQuery("set pol type revrsal 2 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=3" ;
	_doQuery("set pol type revrsal 3 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=4" ;
	_doQuery("set pol type revrsal 4 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=5" ;
	_doQuery("set pol type revrsal 5 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 3
			WHERE pyCode=6" ;
	_doQuery("set pol type revrsal 6 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 2
			WHERE pyCode=7" ;
	_doQuery("set pol type revrsal 7 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 2
			WHERE pyCode=8" ;
	_doQuery("set pol type revrsal 8 ", $q);

	$q = "UPDATE policyTransactionTypes
			SET pyReverseAs = 2
			WHERE pyCode=9" ;
	_doQuery("set pol type revrsal 9 ", $q);


	// ********* end of policy trans types ************************************

 	// before 22-may-06
	$q = "ALTER TABLE cashBatches ADD COLUMN  btAccountingPeriod	INT";
	_doQuery("add btAccountingPeriod	", $q);

	$q = "ALTER TABLE insuranceCompanies ADD COLUMN  icAddonCOB		INT";
	_doQuery("add icAddonCOB	", $q);

	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtIntrodRef		VARCHAR(100)";
	_doQuery("add rtIntrodRef	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtChequeNo			VARCHAR(100)";
	_doQuery("add rtChequeNo	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtPaymentType		INT";
	_doQuery("add rtPaymentType	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtWrittenOff		BIGINT";
	_doQuery("add rtWrittenOff	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtPaidDate			DATE";
	_doQuery("add rtPaidDate	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtPaidYear			INT";
	_doQuery("add rtPaidYear	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtPaidPeriod		INT";
	_doQuery("add rtPaidPeriod	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtCashBatch		INT";
	_doQuery("add rtCashBatch	", $q);
	$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtCashBatchItem	INT";
	_doQuery("add rtCashBatchItem	", $q);

	// --- introducers trans allocations table -----------------------------
	$q = "";
	$q .=	"raCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .=	"raType				CHAR(1),";		// will be either C cash or W written off
	$q .=	"raCashTran			INT,";		// the cash trans
	$q .=	"raOtherTran		INT,";		// transaction cash allocation to

	$q .=	"raAmount			BIGINT,";		// the amount
	$q .=	"raPaymentMethod	INT,";		// see payment type table cashPaymentMethods

	$q .=	"raPostingDate		DATE,";

	$q .= 	"raAccountingYear	INT,";				// current acc year
	$q .= 	"raAccountingPeriod	INT,";				// current acc period, within year

	$q .=	"raLastUpdateBy		INT,";
	$q .=	"raLastUpdateOn		CHAR(14))";

	udbCreateTableNoReplace("introducerTransAllocations",$q);

	// so can do commit and rollback
	udbSetTableForTransactions("introducerTransAllocations");

	udbCreateIndex("introducerTransAllocations","raCashTranIndex", "raCashTran");
	udbCreateIndex("introducerTransAllocations","raOtherTranIndex","raOtherTran");

	// - bank account --------------------------------------
	$q = "";
	$q .=	"baCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .= 	"baType				INT,";			// see bankTransTypes
	$q .=	"baTran				INT,";			// trans code of trans based on type
	$q .=   "baDebit			BOOL,";			// from type - amts neg if credit

	$q .=	"baAmount			BIGINT,";		// debits os, credist neg, so table can be added

	$q .=	"baPostingRef		VARCHAR(50),";
	$q .= 	"baDescription		TEXT,";
	$q .= 	"baPaymentType		INT,";		// see cashPaymentMethods


	$q .=	"baPostingDate		DATE,";

	$q .= 	"baAccountingYear	INT,";				// current acc year
	$q .= 	"baAccountingPeriod	INT,";				// current acc period, within year

	$q .=	"baCreatedBy		INT,";		// who and when original trans created
	$q .=	"baCreatedOn		CHAR(14),";

	$q .=	"baLastUpdateBy		INT,";
	$q .=	"baLastUpdateOn		CHAR(14),";

	$q .=	"lastAccessTime		CHAR(14))";	   // for concurrency see URecord


	udbCreateTable("bankAccountTrans",$q);

	// so can do commit and rollback
	udbSetTableForTransactions("bankAccountTrans");

	// we want to start order numbers at a high value
	$q = "ALTER TABLE bankAccountTrans AUTO_INCREMENT = 1000000";

	$result = mysql_query($q);
	if($result == null) die("cat set auto increment on bank acc");

	// ------ bank trans types -----------------------------------
	$q = "";
	$q .=	"byCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .= 	"byName				VARCHAR(50),";
	$q .= 	"byDebit			BOOL,";			// is debit, else credit
	$q .= 	"byAllowUserSelect	BOOL DEFAULT 0,";
	$q .=	"bySequence			INT)";

	udbCreateTable("bankTransTypes",$q);

	// --  bank trans types -----
	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (1, 'Earned Commission', 1, 1, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (2, 'Earned Broker Fees', 1, 2, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (3, 'Bank Interest', 0, 3, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (4, 'Bank Charges', 1, 4, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (5, 'Adjustment/Write Of', 1, 5, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (6, 'Adjustment/Write Back', 0, 6, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (7, 'Returned/Bad Cheque', 1, 7, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (8, 'Miscellaneous Receipts', 0, 8, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (9, 'Other Earned Net Income', 1, 9, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (10, 'Other Net Earned Charges', 0, 10, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (11, 'Journal Dr', 1, 11, 1)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (12, 'Journal Cr', 0, 12, 1)";
		_doQuery("adding bankTransTypes	", $q);



	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (13, 'Cash Batch', 0, 13, 0)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (14, 'Cash Paid To Client', 1, 14, 0)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (15, 'Payment to Ins. Co.', 1, 15, 0)";
		_doQuery("adding bankTransTypes	", $q);

	$q = "INSERT INTO  bankTransTypes (byCode, byName, byDebit, bySequence, byAllowUserSelect)
		VALUES (16, 'Payment to Introducer', 1, 16, 0)";
		_doQuery("adding bankTransTypes	", $q);


	// --  payment types -----
	$q = "INSERT INTO  cashPaymentMethods (cpCode, cpName, cpSequence)
		VALUES (5, 'Direct Debit', 5)";
		_doQuery("adding cashPaymentMethods 5	", $q);

	$q = "INSERT INTO  cashPaymentMethods (cpCode, cpName, cpSequence)
		VALUES (6, 'Transfer', 6)";
		_doQuery("adding cashPaymentMethods 6	", $q);

	$q = "INSERT INTO  cashPaymentMethods (cpCode, cpName, cpSequence)
		VALUES (7, 'Various', 7)";
		_doQuery("adding cashPaymentMethods 7	", $q);

		// above done in test, not live !!!!

// ---------------------  old - dont run stuff below here --------------------------------
	/*****

	installed below 4-apr-06

	// change period names
		$q = "UPDATE accountingYears SET ayName='2006'
		  WHERE ayCode=1";
	_doQuery("acc year1 name", $q);
	$q = "UPDATE accountingYears SET ayName='2007'
		  WHERE ayCode=2";
	_doQuery("acc year2 name", $q);



	// period etc

	// ---- policy trans -----------------
	$q = "UPDATE policyTransactions SET ptAccountingYear=2005
		  WHERE ptAccountingYear IS NULL  OR ptAccountingYear < 2005";
	_doQuery("pol trans acc year	", $q);

	$q = "UPDATE policyTransactions SET ptAccountingPeriod=10
		  WHERE ptAccountingPeriod  IS NULL  OR ptAccountingPeriod < 1
		  AND ptPostingDate >= '2006-01-01' AND ptPostingDate <= '2006-01-31'";
	_doQuery("pol trans acc period	10", $q);

	$q = "UPDATE policyTransactions SET ptAccountingPeriod=11
		  WHERE ptAccountingPeriod  IS NULL  OR ptAccountingPeriod < 1
		  AND ptPostingDate >= '2006-02-01' AND ptPostingDate <= '2006-02-28'";
	_doQuery("pol trans acc period	11", $q);

	$q = "UPDATE policyTransactions SET ptAccountingPeriod=12
		  WHERE ptAccountingPeriod  IS NULL  OR ptAccountingPeriod < 1
		  AND ptPostingDate >= '2006-03-01' AND ptPostingDate <= '2006-03-28'";
	_doQuery("pol trans acc period	12", $q);


	// ---- client trans -----------------
	$q = "UPDATE clientTransactions SET ctAccountingYear=2005
		  WHERE ctAccountingYear IS NULL  OR ctAccountingYear < 2005";
	_doQuery("client trans acc year	", $q);

	$q = "UPDATE clientTransactions SET ctAccountingPeriod=10
		  WHERE ctAccountingPeriod  IS NULL  OR ctAccountingPeriod < 1
		  AND ctPostingDate >= '2006-01-01' AND ctPostingDate <= '2006-01-31'";
	_doQuery("client trans acc period 10	", $q);

	$q = "UPDATE clientTransactions SET ctAccountingPeriod=11
		  WHERE ctAccountingPeriod  IS NULL  OR ctAccountingPeriod < 1
		  AND ctPostingDate >= '2006-02-01' AND ctPostingDate <= '2006-02-28'";
	_doQuery("client trans acc period 11	", $q);

	$q = "UPDATE clientTransactions SET ctAccountingPeriod=12
		  WHERE ctAccountingPeriod  IS NULL  OR ctAccountingPeriod < 1
		  AND ctPostingDate >= '2006-03-01' AND ctPostingDate <= '2006-03-28'";
	_doQuery("client trans acc period 12	", $q);

	// loop through allocations that need fixing
	$q = "SELECT caCode FROM clientTransAllocations
		  WHERE caAccountingPeriod  IS NULL  OR caAccountingPeriod < 1";
	$result = udbQuery($q);
	if($result == false) trigger_error(udbLastError(),E_USER_ERROR);

	while($row = udbGetRow($result))
	{
		$caCode = $row['caCode'];

		$ca = new ClientTransAllocation($caCode);

		$ca->tempCorrectPeriods();

		$ca->update();
	}

	// ---- ins co trans -----------------
	$q = "UPDATE inscoTransactions SET itAccountingYear=2005
		  WHERE itAccountingYear IS NULL  OR itAccountingYear < 2005";
	_doQuery("insco trans acc year	", $q);

	$q = "UPDATE inscoTransactions SET itAccountingPeriod=10
		  WHERE itAccountingPeriod  IS NULL  OR itAccountingPeriod < 1
		  AND itPostingDate >= '2006-01-01' AND itPostingDate <= '2006-01-31'";
	_doQuery("insco trans acc period 10	", $q);

	$q = "UPDATE inscoTransactions SET itAccountingPeriod=11
		  WHERE itAccountingPeriod  IS NULL  OR itAccountingPeriod < 1
		  AND itPostingDate >= '2006-02-01' AND itPostingDate <= '2006-02-28'";
	_doQuery("insco trans acc period 11	", $q);

	$q = "UPDATE inscoTransactions SET itAccountingPeriod=12
		  WHERE itAccountingPeriod  IS NULL  OR itAccountingPeriod < 1
		  AND itPostingDate >= '2006-03-01' AND itPostingDate <= '2006-03-28'";
	_doQuery("insco trans acc period 12	", $q);

	// loop through allocations that need fixing
	$q = "SELECT iaCode FROM inscoTransAllocations
		  WHERE iaAccountingPeriod  IS NULL  OR iaAccountingPeriod < 1";
	$result = udbQuery($q);
	if($result == false) trigger_error(udbLastError(),E_USER_ERROR);

	while($row = udbGetRow($result))
	{
		$iaCode = $row['iaCode'];

		$ia = new InsCoTransAllocation($iaCode);

		$ia->tempCorrectPeriods();

		$ia->update();
	}

	// ---- introd trans -----------------
	$q = "UPDATE introducerTransactions SET rtAccountingYear=2005
		  WHERE rtAccountingYear IS NULL  OR rtAccountingYear < 2005";
	_doQuery("introducer trans acc year	", $q);

	$q = "UPDATE introducerTransactions SET rtAccountingPeriod=10
		  WHERE rtAccountingPeriod  IS NULL  OR rtAccountingPeriod < 1
		  AND rtPostingDate >= '2006-01-01' AND rtPostingDate <= '2006-01-31'";
	_doQuery("introducer trans acc period 10	", $q);

	$q = "UPDATE introducerTransactions SET rtAccountingPeriod=11
		  WHERE rtAccountingPeriod  IS NULL  OR rtAccountingPeriod < 1
		  AND rtPostingDate >= '2006-02-01' AND rtPostingDate <= '2006-02-28'";
	_doQuery("introducer trans acc period 11	", $q);

	$q = "UPDATE introducerTransactions SET rtAccountingPeriod=12
		  WHERE rtAccountingPeriod  IS NULL  OR rtAccountingPeriod < 1
		  AND rtPostingDate >= '2006-03-01' AND rtPostingDate <= '2006-03-28'";
	_doQuery("introducer trans acc period 12	", $q);

	// below install dmc 29-mar-06

	$q = "ALTER TABLE insuranceCompanies ADD COLUMN  icDelegated		INT";
	_doQuery("add icDelegated	", $q);


	//*****************  accounting audit file ****************************************

	$q = "";
	$q .=	"aaCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .= 	"aaType				CHAR(1),";		// P = policy C = client  I=ins co R=introducer
	$q .=	"aaTran				INT,";			// trans code in trans table (cl, in or introd)

	$q .=	"aaPostingDate		DATE,";

	$q .= 	"aaAccountingYear	INT,";				// current acc year
	$q .= 	"aaAccountingPeriod	INT,";				// current acc period, within year

	$q .=	"aaCreatedBy		INT,";		// who and when original trans created
	$q .=	"aaCreatedOn		CHAR(14),";

	$q .=	"aaLastUpdateBy		INT,";
	$q .=	"aaLastUpdateOn		CHAR(14),";

	$q .=	"lastAccessTime		CHAR(14))";	   // for concurrency see URecord


	udbCreateTableNoReplace("accountingAudit",$q);

	// so can do commit and rollback
	udbSetTableForTransactions("accountingAudit");

	// we want to start order numbers at a high value
	$q = "ALTER TABLE accountingAudit AUTO_INCREMENT = 9000000";

	$result = mysql_query($q);
	if($result == null) echo ("cat set auto increment on audit");





	// --- start of stuff 15-mar-06
	// -- insert type for reconciliation -------------------------
		$q = "INSERT INTO  documentTypes (dtCode, dtName, dtPolicy, dtInsco, dtIntroducer, dtSequence)
			   VALUES (54, 'Remittance Advice', 0, 1, 0, 54)";
		_doQuery("add docm type for rem advice	", $q);


		// ---------- update a long ref
	$q = "UPDATE cashBatches SET
		btPayInSlip = 'FLAHIVE CONTRAS'
		WHERE btCode = 8000072";

	_doQuery("truncate cash batch", $q);

	all below run 14-mar-06 at dmc
// start of stuff 23-feb-06	 -  for control acc

	$q = "ALTER TABLE clientTransAllocations ADD COLUMN  caPostingDate		DATE";
	_doQuery("add caPostingDate	", $q);

	$q = "ALTER TABLE clientTransAllocations ADD COLUMN  caAccountingYear	INT";
	_doQuery("add caAccountingYear	", $q);

	$q = "ALTER TABLE clientTransAllocations ADD COLUMN  caAccountingPeriod	INT";
	_doQuery("add caAccountingPeriod	", $q);


	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itPaidDate			DATE";
	_doQuery("add itPaidDate	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itPaidYear			INT";
	_doQuery("add itPaidYear	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itPaidPeriod		INT";
	_doQuery("add itPaidPeriod	", $q);

	//******************  accounting period  figures **************************************
	$q = "";
	$q .= "afCode			INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";
	$q .= "afYear			INT,";	// 2005 is 2005/2006 etc
	$q .= "afPeriod			INT,";	//  1, 2 etc
	$q .= "afPeriodCode		INT,";	//  key to period table - for period done

	$q .= "afFromDate		DATE,";	// in case tables changed
	$q .= "afToDate			DATE,";

	// brought fwd figures
	$q .= "afBFClients			BIGINT,";
	$q .= "afBFInsures			BIGINT,";
	$q .= "afBFCommPosted		BIGINT,";
	$q .= "afBFFeesPosted		BIGINT,";
	$q .= "afBFIntroducers		BIGINT,";
	$q .= "afBFCommPaid			BIGINT,";
	$q .= "afBFFeesPaid			BIGINT,";
	$q .= "afBFOtherIncome		BIGINT,";
	$q .= "afBFOtherCharges		BIGINT,";
	$q .= "afBFBank				BIGINT,";

	// net movment figures
	$q .= "afNMClients			BIGINT,";
	$q .= "afNMInsures			BIGINT,";
	$q .= "afNMCommPosted		BIGINT,";
	$q .= "afNMFeesPosted		BIGINT,";
	$q .= "afNMIntroducers		BIGINT,";
	$q .= "afNMCommPaid			BIGINT,";
	$q .= "afNMFeesPaid			BIGINT,";
	$q .= "afNMOtherIncome		BIGINT,";
	$q .= "afNMOtherCharges		BIGINT,";
	$q .= "afNMBank				BIGINT,";

		// carried fwd figures
	$q .= "afCFClients			BIGINT,";
	$q .= "afCFInsures			BIGINT,";
	$q .= "afCFCommPosted		BIGINT,";
	$q .= "afCFFeesPosted		BIGINT,";
	$q .= "afCFIntroducers		BIGINT,";
	$q .= "afCFCommPaid			BIGINT,";
	$q .= "afCFFeesPaid			BIGINT,";
	$q .= "afCFOtherIncome		BIGINT,";
	$q .= "afCFOtherCharges		BIGINT,";
	$q .= "afCFBank				BIGINT)";

	udbCreateTable("accountingFigures",$q);


// endof stuff 23-feb-06	 - for control acc



// start of stuff 16-feb-06	 - stage 2

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itWrittenOff		BIGINT";
	_doQuery("add itWrittenOff	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itChequeNo			VARCHAR(100)";
	_doQuery("add itChequeNo	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itPaymentType		INT";
	_doQuery("add itPaymentType	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itCashBatch		INT";
	_doQuery("add itCashBatch	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN  itCashBatchItem		INT";
	_doQuery("add itCashBatchItem	", $q);


	$q = "";
	$q .=	"iaCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";	// internal id
	$q .=	"iaType				CHAR(1),";		// will be either C cash or W written off
	$q .=	"iaCashTran			INT,";		// the cash trans
	$q .=	"iaOtherTran		INT,";		// transaction cash allocation to

	$q .=	"iaAmount			BIGINT,";		// the amount
	$q .=	"iaPaymentMethod	INT,";		// see payment type table cashPaymentMethods

	$q .=	"iaPostingDate		DATE,";

	$q .= 	"iaAccountingYear	INT,";				// current acc year
	$q .= 	"iaAccountingPeriod	INT,";				// current acc period, within year

	$q .=	"iaLastUpdateBy		INT,";
	$q .=	"iaLastUpdateOn		CHAR(14))";

	udbCreateTableNoReplace("inscoTransAllocations",$q);

	// so can do commit and rollback
	udbSetTableForTransactions("inscoTransAllocations");

	udbCreateIndex("inscoTransAllocations","iaCashTranIndex", "iaCashTran");
	udbCreateIndex("inscoTransAllocations","iaOtherTranIndex","iaOtherTran");


// end of stuff 16-feb-06	 - stage 2



// start of stuff 16-feb-06

	$q = "ALTER TABLE policies ADD COLUMN  plPolicyTotal			BIGINT";
	_doQuery("add plPolicyTotal	", $q);


	// update all clients - so name sort calced
	$q = "SELECT plCode FROM policies";

	$result = mysql_query($q);
	if($result === null) trigger_error("cant update policies", E_USER_ERROR);

	while($row = mysql_fetch_array($result))
	{
		$plCode = $row['plCode'];

		$pl  = new Policy($plCode);

		$pl->recalculateAccountingFields();

		$pl->update();
	}
	echo "DONE - policies updated<br>\n";

// end of stuff 16-feb-06


	// start of stuff 7-feb-06

	// ---- system table -------------
	$q = "ALTER TABLE system ADD COLUMN  syYearCode	INT";
	_doQuery("add syYearCode	", $q);

	$q = "ALTER TABLE system ADD COLUMN  syPeriodCode	INT";
	_doQuery("add syPeriodCode	", $q);

		// --  set the current accounting year and period ---------------------------------
	$q = "UPDATE system SET
		syAccountingYear = 2005,
		syAccountingPeriod=11,
		syPeriodFrom='2006-02-01',
		syPeriodTo=  '2006-02-28',
		syYearCode=  1,
		syPeriodCode=  11
			";
	_doQuery("set period in system", $q);


	// -------- new col for class of business ---------------------
	$q = "ALTER TABLE classOfBus ADD COLUMN
			cbAllowIPTAmend			BOOL";
	_doQuery("add cbAllowIPTAmendT	", $q);

	// ---------------------- set ipt for class of business - marine only -----
	$q = "UPDATE classOfBus
			SET cbAllowIPTAmend	= 1
			WHERE cbCode=20" ;
	_doQuery("set marine cob ", $q);

	// -- insert cash payment type  ---------------------------------
		$q = "INSERT INTO  cashPaymentMethods (cpCode, cpName, cpSequence)
			   VALUES (4, 'Journal', 4)";
		_doQuery("add add journal payment method	", $q);

	// ---------------------- set ipt for class of business - marine only -----
	$q = "ALTER TABLE policyTransactions ADD COLUMN
			ptAddlIPT				BIGINT";
	_doQuery("add ptAddlIPTT	", $q);

	// end of stuff 7-feb-06


/
	// start of stuff 6-feb-06 - first stage

	// ---------------------- set ipt for class of business - marine only -----
	$q = "UPDATE policyTransactions
			SET ptCreatedBy	= 4, ptLastUpdateBy = 4
			WHERE ptCode=274" ;
	_doQuery("set initials on trans 278 ", $q);

	$q = "UPDATE policyTransactions
			SET ptCreatedBy	= 4, ptLastUpdateBy = 4
			WHERE ptCode=268" ;
	_doQuery("set initials on trans 268 ", $q);

	// end of stuff 6-feb-06 - first stage


	// start of stuff 5-feb-06 - done
	$x = true;
	if($x == true)
	{

		//
		$q = "";
		$q .= "ayCode			INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";
		$q .= "ayYear			INT,";		// which acccounting year - see ayCode
		$q .= "ayName			VARCHAR(100),";
		$q .= "ayFromDate		DATE,";
		$q .= "ayToDate			DATE)";

		udbCreateTable("accountingYears",$q);

		//
		$q = "";
		$q .= "apCode			INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,";
		$q .= "apYear			INT,";		// which acccounting year - see ayCode
		$q .= "apPeriod			INT,";		// within year 1 through 12 or 13
		$q .= "apName			VARCHAR(100),";
		$q .= "apFromDate		DATE,";
		$q .= "apToDate			DATE)";

		udbCreateTable("accountingPeriods",$q);


		// ---- system table -------------
		$q = "ALTER TABLE system ADD COLUMN  syAccountingYear	INT";
		_doQuery("add syAccountingYear	", $q);

		$q = "ALTER TABLE system ADD COLUMN  syAccountingPeriod	INT";
		_doQuery("add syAccountingPeriod	", $q);

		$q = "ALTER TABLE system ADD COLUMN  syPeriodFrom	DATE";
		_doQuery("add syAccountingYear	", $q);

		$q = "ALTER TABLE system ADD COLUMN  syPeriodTo		DATE";
		_doQuery("add syAccountingYear	", $q);

		// ---- various transaction tables -------------
		$q = "ALTER TABLE policyTransactions ADD COLUMN  ptAccountingYear	INT";
		_doQuery("add ptAccountingYear		", $q);

		$q = "ALTER TABLE policyTransactions ADD COLUMN  ptAccountingPeriod	INT";
		_doQuery("add ptAccountingPeriod	", $q);


		$q = "ALTER TABLE clientTransactions ADD COLUMN  ctAccountingYear	INT";
		_doQuery("add ctAccountingYear", $q);

		$q = "ALTER TABLE clientTransactions ADD COLUMN  ctAccountingPeriod	INT";
		_doQuery("add ctAccountingPeriod	", $q);


		$q = "ALTER TABLE inscoTransactions ADD COLUMN  itAccountingYear	INT";
		_doQuery("add itAccountingYear", $q);

		$q = "ALTER TABLE inscoTransactions ADD COLUMN  itAccountingPeriod	INT";
		_doQuery("add itAccountingPeriod	", $q);


		$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtAccountingYear	INT";
		_doQuery("add rtAccountingYear", $q);

		$q = "ALTER TABLE introducerTransactions ADD COLUMN  rtAccountingPeriod	INT";
		_doQuery("add rtAccountingPeriod", $q);


		$q = "ALTER TABLE cashBatches ADD COLUMN  btAccountingYear	INT";
		_doQuery("add btAccountingYear	", $q);

		$q = "ALTER TABLE introducerTransactions ADD COLUMN  btAccountingPeriod	INT";
		_doQuery("add btAccountingPeriod	", $q);


		// -- create first accounting year ---------------------------------
		$q = "INSERT INTO  accountingYears (ayCode, ayYear, ayName, ayFromDate, ayToDate)
			   VALUES (1, 2005, 	'2005/2006', '2005-03-29', '2006-03-28')";
		_doQuery("add add accounting year	", $q);

		// -- then accounting periods ---------------------------------
		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (1, 1, 1,	'1', '2005-03-29', '2005-04-30')";
		_doQuery("add  yr 1, acc period 1", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (2, 1,	2, '2', '2005-05-01', '2005-05-31')";
		_doQuery("add  yr 1, acc period 2", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (3, 1, 3,	'3', '2005-06-01', '2005-06-30')";
		_doQuery("add  yr 1, acc period 3", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (4, 1, 4,	'4', '2005-07-01', '2005-07-31')";
		_doQuery("add  yr 1, acc period 4", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (5, 1, 5,	'5', '2005-08-01', '2005-08-31')";
		_doQuery("add  yr 1, acc period 5", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (6, 1, 6,	'6', '2005-09-01', '2005-09-30')";
		_doQuery("add  yr 1, acc period 6", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (7, 1,	7, '7', '2005-10-01', '2005-10-31')";
		_doQuery("add  yr 1, acc period 7", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (8, 1, 8,	'8', '2005-11-01', '2005-11-30')";
		_doQuery("add  yr 1, acc period 8", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (9, 1, 9,	'9', '2005-12-01', '2005-12-31')";
		_doQuery("add  yr 1, acc period 9", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (10, 1,10,	'10', '2006-01-01', '2006-01-31')";
		_doQuery("add  yr 1, acc period 10", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (11, 1,	11, '11', '2006-02-01', '2006-02-28')";
		_doQuery("add  yr 1, acc period 11", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (12, 1,12,	'12', '2006-03-01', '2006-03-28')";
		_doQuery("add  yr 1, acc period 12", $q);



		// -- create second accounting year ---------------------------------
		$q = "INSERT INTO  accountingYears (ayCode, ayYear, ayName, ayFromDate, ayToDate)
			   VALUES (2,	2006, '2006/2007', '2006-03-29', '2007-03-28')";
		_doQuery("add add accounting year	", $q);

		// -- then accounting periods ---------------------------------
		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (13, 2, 1	,'1', '2006-03-29', '2006-04-30')";
		_doQuery("add  yr 2, acc period 1", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod,  apName, apFromDate, apToDate)
			   VALUES (14, 2, 2,	'2', '2006-05-01', '2006-05-31')";
		_doQuery("add  yr 2, acc period 2", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (15, 2, 3,	'3', '2006-06-01', '2006-06-30')";
		_doQuery("add  yr 2, acc period 3", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (16, 2, 4,	'4', '2006-07-01', '2006-07-31')";
		_doQuery("add  yr 2, acc period 4", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (17, 2, 5,	'5', '2006-08-01', '2006-08-31')";
		_doQuery("add  yr 2, acc period 5", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (18, 2, 6,	'6', '2006-09-01', '2006-09-30')";
		_doQuery("add  yr 2, acc period 6", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (19, 2 , 7,	'7', '2006-10-01', '2006-10-31')";
		_doQuery("add  yr 2, acc period 7", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (20, 2, 8,	'8', '2006-11-01', '2006-11-30')";
		_doQuery("add  yr 2, acc period 8", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (21, 2, 9,	'9', '2006-12-01', '2006-12-31')";
		_doQuery("add  yr 1, acc period 9", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (22, 2, 10,	'10', '2007-01-01', '2007-01-31')";
		_doQuery("add  yr 2, acc period 10", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (23, 2, 11,	'11', '2007-02-01', '2007-02-28')";
		_doQuery("add  yr 2, acc period 11", $q);

		$q = "INSERT INTO accountingPeriods (apCode, apYear, apPeriod, apName, apFromDate, apToDate)
			   VALUES (24, 2, 12,	'12', '2007-03-01', '2007-03-28')";
		_doQuery("add  yr 2, acc period 12", $q);


		// -- finally set the current accounting year and period ---------------------------------
		$q = "UPDATE system SET
			syAccountingYear = 2005,
			syAccountingPeriod=11,
			syPeriodFrom='2006-02-01',
			syPeriodTo=  '2006-02-28'
			";
		_doQuery("set period in system", $q);
	}

	// ---------------------------  stuff to run this time here -----------------------------

	// -------- new col for class of business ---------------------
	$q = "ALTER TABLE classOfBus ADD COLUMN
			cbAllowIPTAmend			BOOL";
	_doQuery("add cbAllowIPTAmendT	", $q);

	// ---------------------- set ipt for class of business - marine only -----
	$q = "UPDATE classOfBus
			SET cbAllowIPTAmend	= 1
			WHERE cbCode=20" ;
	_doQuery("set marine cob ", $q);

	// --------------- change ins co name ------------------
	$q = "UPDATE insuranceCompanies
			SET icName='C E Heath Insurance (for Others)'
			WHERE
			icCode=1000014";
	_doQuery("change ins co name	", $q);

	// end of stuff run 5-feb-06


	// start of stuff 2-feb-06

	// update all clients - so name sort calced
	$q = "SELECT clCode FROM clients";

	$result = mysql_query($q);
	if($result === null) trigger_error("cant update clients", E_USER_ERROR);

	while($row = mysql_fetch_array($result))
	{
		$clCode = $row['clCode'];

		$cl  = new Client($clCode);

		$cl->update();
	}
	echo "DONE - clients updated<br>\n";


	// --------------- change ins co name ------------------
	$q = "UPDATE insuranceCompanies
			SET icName='C E Heath Insurance (for Others)'
			WHERE
			icCode=1000014";
	_doQuery("change ins co name	", $q);
	// end of stuff 2-feb-06



	// --- start of stuff 31-jan-06 run
	// --------------- some trans has incorrect bal - how? ------------------
	$q = "UPDATE clientTransactions
			SET ctBalance = (ctOriginal - ctPaid -ctWrittenOff)
			WHERE
			ctBalance != (ctOriginal - ctPaid - ctWrittenOff)";
	_doQuery("fix 3p problem	", $q);

	// --------------- change ins co name ------------------
	$q = "UPDATE insuranceCompanies
			SET icName='C E Heath Insurance (for Others)'
			WHERE
			icCode=1000014";
	_doQuery("change ins co name	", $q);


	// end of stuff 31-jan-06


	// --------------- change ins co types? ------------------

	$q = "UPDATE insCoTypes
			SET iyName = 'Direct'
			WHERE
			iyCode = 1";
	_doQuery("ins co type 1	", $q);

	$q = "UPDATE insCoTypes
			SET iyName = 'Wholesale'
			WHERE
			iyCode = 2";
	_doQuery("ins co type 2	", $q);



	// --------------  client name sort stuff ---------------------
	$q = "ALTER TABLE clients ADD COLUMN
			clNameSort			VARCHAR(200)";
	_doQuery("add clNameSort	", $q);

	// update all clients - so name sort calced
	$q = "SELECT clCode FROM clients";

	$result = mysql_query($q);
	if($result === null) trigger_error("cant update clients", E_USER_ERROR);

	while($row = mysql_fetch_array($result))
	{
		$clCode = $row['clCode'];

		$cl  = new Client($clCode);

		$cl->update();
	}
	echo "DONE - clients updated<br>\n";

	// --------------------  account period stuff ----------------------------------






	// --------------------------------------
	$q = "ALTER TABLE classOfBus ADD COLUMN
			cbZeroIPT			BOOL DEFAULT 0";
	_doQuery("add cbZeroIPT	", $q);

	$q = "ALTER TABLE policies ADD COLUMN
			plPolicyHolder		VARCHAR(200)";
	_doQuery("add plPolicyHolder	", $q);


	$q = "ALTER TABLE clientTransactions ADD COLUMN
			ctCreatedBy		INT";
	_doQuery("add ctCreatedBy	", $q);

	$q = "ALTER TABLE clientTransactions ADD COLUMN
			ctCreatedOn		CHAR(14)";
	_doQuery("add ctCreatedOn	", $q);


	$q = "ALTER TABLE policyTransactions ADD COLUMN
			ptCreatedBy		INT";
	_doQuery("add ptCreatedBy	", $q);

	$q = "ALTER TABLE policyTransactions ADD COLUMN
			ptCreatedOn		CHAR(14)";
	_doQuery("add ptCreatedOn	", $q);


	$q = "ALTER TABLE inscoTransactions ADD COLUMN
			itCreatedBy		INT";
	_doQuery("add itCreatedBy	", $q);

	$q = "ALTER TABLE inscoTransactions ADD COLUMN
			itCreatedOn		CHAR(14)";
	_doQuery("add itCreatedOn	", $q);


	$q = "ALTER TABLE introducerTransactions ADD COLUMN
			rtCreatedBy		INT";
	_doQuery("add ptCreatedBy	", $q);

	$q = "ALTER TABLE introducerTransactions ADD COLUMN
			rtCreatedOn		CHAR(14)";
	_doQuery("add ptCreatedOn	", $q);



	// -----------------   set create by and when for client trans - all from last update
	$q = "UPDATE clientTransactions
			SET ctCreatedOn	= ctLastUpdateOn, ctCreatedBy	= ctLastUpdateBy" ;
	_doQuery("set cl tran creation date and time ", $q);


	// now we update the same using pol trans
	$q = "UPDATE clientTransactions, policyTransactions
			SET ctCreatedOn	= ptLastUpdateOn, ctCreatedBy = ptLastUpdateBy
		  WHERE  ptClientTran=ctCode";
	_doQuery("set cl tran creation date and time ", $q);


	$q = "UPDATE policyTransactions
			SET ptCreatedOn	= ptLastUpdateOn, ptCreatedBy	= ptLastUpdateBy" ;
	_doQuery("set pol trancreation date and time ", $q);

	$q = "UPDATE inscoTransactions
			SET itCreatedOn	= itLastUpdateOn, itCreatedBy	= itLastUpdateBy" ;
	_doQuery("set insco trancreation date and time ", $q);

	$q = "UPDATE introducerTransactions
			SET rtCreatedOn	= rtLastUpdateOn, rtCreatedBy	= rtLastUpdateBy" ;
	_doQuery("set introd trancreation date and time ", $q);


	// ---------------------- set ipt for class of business - marine only
	$q = "UPDATE classOfBus
			SET cbZeroIPT	= 1
			WHERE cbCode=20" ;
	_doQuery("set marine cob ", $q);

	// ---------------------- change payment method name
	$q = "UPDATE policyPaymentMethods
			SET pmName='IC Cash'
			WHERE pmCode=7" ;
	_doQuery("set payment type ", $q);


	$q = "ALTER TABLE documents ADD COLUMN
			doDeleted			BOOL DEFAULT 0";
	_doQuery("add doDeleted	", $q);

	udbCreateIndex("clients","clTypeIndex","clType");
	udbCreateIndex("clients","clCompanyNameIndex","clCompanyName");
	udbCreateIndex("clients","clFirstNameIndex","clFirstName");
	udbCreateIndex("clients","clLastNameIndex","clLastName");

	****/
	function _doQuery($text, $q) {
		$result = mysql_query( $q );

		if ($result === true) {
			print  . 'OK ' . $text . '  <br>';

			if (substr( $q, 0, 6 ) == 'INSERT') {
				echo 'insert value was ' . mysql_insert_id(  );
			}
		} 
else {
			$err = mysql_error(  );
			print  . 'FAILED  ' . $text . ' : error was ' . $err . ' <br>';
		}

	}

	require_once( '../include/startup.php' );
	$q = 'UPDATE inscoTransactions
	SET itBalance 	= 70959	, itOriginal	= 70959
	WHERE itSysTran = 7009550';
	_doQuery( 'correct single trans', $q );
	exit(  );
?>