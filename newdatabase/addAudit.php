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
	$q = 'SELECT ptCode FROM policyTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$ptCode = $row['ptCode'];
		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'P\' AND aaTran = ' . $ptCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );

		if (1 <= $num) {
			continue;
		}

		$pt = new PolicyTransaction( $ptCode );
		$aa = new AccountingAudit( null );
		$aa->set( 'aaType', 'P' );
		$aa->set( 'aaTran', $pt->getKeyValue(  ) );
		$aa->set( 'aaPostingDate', $pt->get( 'ptPostingDate' ) );
		$aa->set( 'aaPostingDate', $pt->get( 'ptPostingDate' ) );
		$aa->set( 'aaAccountingYear', $pt->get( 'ptAccountingYear' ) );
		$aa->set( 'aaAccountingPeriod', $pt->get( 'ptAccountingPeriod' ) );
		$aa->set( 'aaCreatedBy', $pt->get( 'ptCreatedBy' ) );
		$aa->set( 'aaCreatedOn', $pt->get( 'ptCreatedOn' ) );
		$aa->insert( null );
		echo '' . 'audit for policy tran ' . $ptCode . ' added <br>';
	}

	$q = 'SELECT ctCode FROM clientTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$ctCode = $row['ctCode'];
		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'C\' AND aaTran = ' . $ctCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );

		if (1 <= $num) {
			continue;
		}

		$ct = new ClientTransaction( $ctCode );
		$aa = new AccountingAudit( null );
		$aa->set( 'aaType', 'C' );
		$aa->set( 'aaTran', $ct->getKeyValue(  ) );
		$aa->set( 'aaPostingDate', $ct->get( 'ctPostingDate' ) );
		$aa->set( 'aaPostingDate', $ct->get( 'ctPostingDate' ) );
		$aa->set( 'aaAccountingYear', $ct->get( 'ctAccountingYear' ) );
		$aa->set( 'aaAccountingPeriod', $ct->get( 'ctAccountingPeriod' ) );
		$aa->set( 'aaCreatedBy', $ct->get( 'ctCreatedBy' ) );
		$aa->set( 'aaCreatedOn', $ct->get( 'ctCreatedOn' ) );
		$aa->insert( null );
		echo '' . 'audit for client tran ' . $ctCode . ' added <br>';
	}

	$q = 'SELECT itCode FROM inscoTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$itCode = $row['itCode'];
		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'I\' AND aaTran = ' . $itCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );

		if (1 <= $num) {
			continue;
		}

		$it = new InsCoTransaction( $itCode );
		$aa = new AccountingAudit( null );
		$aa->set( 'aaType', 'I' );
		$aa->set( 'aaTran', $it->getKeyValue(  ) );
		$aa->set( 'aaPostingDate', $it->get( 'itPostingDate' ) );
		$aa->set( 'aaAccountingYear', $it->get( 'itAccountingYear' ) );
		$aa->set( 'aaAccountingPeriod', $it->get( 'itAccountingPeriod' ) );
		$aa->set( 'aaCreatedBy', $it->get( 'itCreatedBy' ) );
		$aa->set( 'aaCreatedOn', $it->get( 'itCreatedOn' ) );
		$aa->insert( null );
		echo '' . 'audit for insco tran ' . $itCode . ' added <br>';
	}

	$q = 'SELECT rtCode FROM introducerTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = mysql_fetch_array( $result )) {
		$rtCode = $row['rtCode'];
		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'R\' AND aaTran = ' . $rtCode;
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result2 );

		if (1 <= $num) {
			continue;
		}

		$rt = new IntroducerTransaction( $rtCode );
		$aa = new AccountingAudit( null );
		$aa->set( 'aaType', 'R' );
		$aa->set( 'aaTran', $rt->getKeyValue(  ) );
		$aa->set( 'aaPostingDate', $rt->get( 'rtPostingDate' ) );
		$aa->set( 'aaAccountingYear', $rt->get( 'rtAccountingYear' ) );
		$aa->set( 'aaAccountingPeriod', $rt->get( 'rtAccountingPeriod' ) );
		$aa->set( 'aaCreatedBy', $rt->get( 'rtCreatedBy' ) );
		$aa->set( 'aaCreatedOn', $rt->get( 'rtCreatedOn' ) );
		$aa->insert( null );
		echo '' . 'audit for introd tran ' . $rtCode . ' added <br>';
	}

	echo 'ALL DONE';
?>