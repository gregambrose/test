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

	function _doquery($text, $q) {
		$result = mysql_query( $q );

		if ($result === true) {
			print '' . 'OK ' . $text . '  <br>';

			if (substr( $q, 0, 6 ) == 'INSERT') {
				echo 'insert value was ' . mysql_insert_id(  );
				return null;
			}
		} 
else {
			$err = mysql_error(  );
			print '' . 'FAILED  ' . $text . ' : error was ' . $err . ' <br>';
		}

	}

	require_once( '../include/startup.php' );
	$q = 'DELETE FROM accessLog';
	_doquery( 'accessLog', $q );
	$q = 'DELETE FROM accountingAudit';
	_doquery( 'accountingAudit', $q );
	$q = 'DELETE FROM bankAccountTrans';
	_doquery( 'bankAccountTrans', $q );
	$q = 'DELETE FROM cashBatches';
	_doquery( 'cashBatches', $q );
	$q = 'DELETE FROM cashBatchItems';
	_doquery( 'cashBatchItems', $q );
	$q = 'DELETE FROM clientTransactions';
	_doquery( 'clientTransactions', $q );
	$q = 'DELETE FROM clientTransAllocations';
	_doquery( 'clientTransAllocations', $q );
	$q = 'DELETE FROM documents';
	_doquery( 'documents', $q );
	$q = 'DELETE FROM inscoTransactions';
	_doquery( 'inscoTransactions', $q );
	$q = 'DELETE FROM inscoTransAllocations';
	_doquery( 'inscoTransAllocations', $q );
	$q = 'DELETE FROM introducerTransactions';
	_doquery( 'introducerTransactions', $q );
	$q = 'DELETE FROM introducerTransAllocations';
	_doquery( 'introducerTransAllocations', $q );
	$q = 'DELETE FROM notes';
	_doquery( 'notes', $q );
	$q = 'DELETE FROM sequences';
	_doquery( 'sequences', $q );
	$q = 'DELETE FROM policyTransactions';
	_doquery( 'policyTransactions', $q );
	$q = 'ALTER TABLE accountingAudit CHANGE COLUMN  aaType aaType	CHAR(2)';
	_doquery( 'change aaType	', $q );
	$q = 'ALTER TABLE clientTransactions ADD COLUMN  ctJournal	INT';
	_doquery( 'add ctJournal	', $q );
	$q = 'ALTER TABLE inscoTransactions ADD COLUMN  itJournal	INT';
	_doquery( 'add itJournal	', $q );
	$q = 'ALTER TABLE introducerTransactions ADD COLUMN  rtJournal	INT';
	_doquery( 'add rtJournal	', $q );
	$q = 'ALTER TABLE insuranceCompanies ADD COLUMN  icIPTAmendable	INT';
	_doquery( 'add icIPTAmendable	', $q );
	$q = '';
	$q .= 'jnCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,';
	$q .= 'jnType				CHAR(2),';
	$q .= 'jnMaster			INT,';
	$q .= 'jnNarrative		VARCHAR(200),';
	$q .= 'jnPostingDate		CHAR(14),';
	$q .= 'jnAccountingYear	INT,';
	$q .= 'jnAccountingPeriod	INT,';
	$q .= 'jnTran				INT,';
	$q .= 'jnAmount			BIGINT,';
	$q .= 'jnCreatedBy		INT,';
	$q .= 'jnCreatedOn		CHAR(14),';
	$q .= 'jnLastUpdateBy		INT,';
	$q .= 'jnLastUpdateOn		CHAR(14),';
	$q .= 'lastAccessTime		CHAR(14))';
	udbcreatetable( 'journals', $q );
	udbsettablefortransactions( 'journals' );
	$q = 'ALTER TABLE journals AUTO_INCREMENT = 9000000';
	_doquery( 'alter journals	', $q );
	$seq = new Sequence( null );
	$seq->set( 'sqType', 'PTI' );
	$seq->set( 'sqMaster', 1 );
	$seq->set( 'sqLastUsed', 2999999 );
	$seq->insert( null );
	$q = 'UPDATE policies SET plPaymentDate = NULL';
	_doquery( 'clear payment dates	', $q );
	$q = 'SELECT clCode from clients WHERE clType = 2 AND clName IS NULL';
	$result = udbquery( $q );

	if ($result == false) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = udbgetrow( $result )) {
		$clCode = $row['clCode'];
		$cl = new Client( $clCode );
		$tiName = null;
		$tiCode = $cl->get( 'clTitle' );

		if (0 < $tiCode) {
			$title = new Title( $tiCode );
			$tiName = $title->get( 'tiName' );
		}

		$name = $cl->get( 'clFirstName' ) . ' ' . $cl->get( 'clLastName' );

		if ($tiName != null) {
			$name = $tiName . ' ' . $name;
		}

		$cl->set( 'clName', $name );
		$cl->update(  );
	}

	echo 'DONE';
?>