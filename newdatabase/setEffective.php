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

	require_once( '../include/startup.php' );
	$q = 'UPDATE clientTransactions, policyTransactions
		SET ctEffectiveDate = ptEffectiveFrom 
		WHERE ctCode = ptClientTran';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE clientTransactions
		SET ctEffectiveDate = ctPostingDate
		WHERE ctEffectiveDate IS NULL';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE inscoTransactions, policyTransactions
		SET itEffectiveDate = ptEffectiveFrom 
		WHERE itCode = ptMainInsCoTran';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE inscoTransactions, policyTransactions
		SET itEffectiveDate = ptEffectiveFrom 
		WHERE itCode = ptAddOnInsCoTran';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE inscoTransactions
		SET itEffectiveDate = itPostingDate
		WHERE itEffectiveDate IS NULL';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE introducerTransactions, policyTransactions
		SET rtEffectiveDate = ptEffectiveFrom 
		WHERE rtCode = ptIntroducerTran';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE introducerTransactions
		SET rtEffectiveDate = rtPostingDate
		WHERE rtEffectiveDate IS NULL';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE accountingAudit
		SET aaEffectiveDate = aaPostingDate
		WHERE aaEffectiveDate IS NULL';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE accountingAudit
		SET aaEffectiveDate = aaPostingDate
		WHERE aaEffectiveDate = \'0000-00-00\'';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'SELECT aaCode FROM accountingAudit ORDER BY aaCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = udbgetrow( $result )) {
		$aaCode = $row['aaCode'];
		$aud = new AccountingAudit( $aaCode );
		$aaType = $aud->get( 'aaType' );

		if ($aaType == 'P') {
			$ptCode = $aud->get( 'aaTran' );
			$pt = new PolicyTransaction( $ptCode );
			$ptEffectiveFrom = $pt->get( 'ptEffectiveFrom' );
			$aud->set( 'aaEffectiveDate', $ptEffectiveFrom );
		}


		if ($aaType == 'I') {
			$itCode = $aud->get( 'aaTran' );
			$it = new InscoTransaction( $itCode );
			$itEffectiveDate = $it->get( 'itEffectiveDate' );

			if ($itEffectiveDate != '0000-00-00') {
				$aud->set( 'aaEffectiveDate', $itEffectiveDate );
			}
		}


		if ($aaType == 'R') {
			$rtCode = $aud->get( 'aaTran' );
			$rt = new IntroducerTransaction( $rtCode );
			$rtEffectiveDate = $rt->get( 'rtEffectiveDate' );

			if ($rtEffectiveDate != '0000-00-00') {
				$aud->set( 'aaEffectiveDate', $rtEffectiveDate );
			}
		}

		unset( $aud[_fldForUpdatedBy] );
		unset( $aud[_fldForUpdatedWhen] );
		$aud->update(  );
	}

	echo 'ALL DONE';
?>