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

	function _correctaudit($auditCode, $tran, $type, $sysTran) {
		if (( $type == '' || $tran == 0 )) {
			return null;
		}

		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'' . $type . '\' AND aaTran = ' . $tran;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$rows = udbnumberofrows( $result );

		if ($rows == 0) {
			return null;
		}


		if (1 < $rows) {
			echo '' . 'too many audit records for ' . $type . ', ' . $tran . '<br>';
		}

		$row = udbgetrow( $result );
		$aaCode = $row['aaCode'];
		$aud = new AccountingAudit( $aaCode );
		$x = $aud->get( 'aaSysTran' );

		if (0 < $x) {
			return null;
		}

		$aud->set( 'aaSysTran', $sysTran );
		unset( $aud[_fldForUpdatedBy] );
		unset( $aud[_fldForUpdatedWhen] );
		$aud->update(  );
	}

	function _isthisrecordinauditalready($aaType, $aaTran) {
		$q = '' . 'SELECT aaCode FROM accountingAudit WHERE aaType = \'' . $aaType . '\' AND aaTran = ' . $aaTran;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if (0 < $num) {
			return true;
		}

		return false;
	}

	function _isthisadeletedpolicytransaction($ptCode) {
		if ($ptCode <= 0) {
			return true;
		}

		$pt = new PolicyTransaction( $ptCode );
		$ptPostStatus = $pt->get( 'ptPostStatus' );

		if ($ptPostStatus != 'P') {
			return true;
		}

		return false;
	}

	require_once( '../include/startup.php' );
	$q = 'DROP TABLE  tempSysTrans';
	$result = mysql_query( $q );
	$q = 'CREATE TABLE tempSysTrans SELECT * FROM systemTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'DROP TABLE  systemTransactions';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = '';
	$q .= 'tnCode			INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,';
	$q .= 'tnType			VARCHAR(10),';
	$q .= 'tnCreatedBy		INT,';
	$q .= 'tnCreatedOn		CHAR(14),';
	$q .= 'tnTran			INT)';
	udbcreatetable( 'systemTransactions', $q );
	$q = 'UPDATE  accountingAudit, introducerTransactions  
	SET aaPostingDate = rtPostingDate, aaCreatedOn = rtCreatedOn, aaCreatedBy = aaCreatedBy
	WHERE rtCode = aaTran 
	AND aaType = \'R\' 
	AND	aaPostingDate = \'0000-00-00\'';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'DROP TABLE  tempAudit';
	$result = mysql_query( $q );
	$q = 'CREATE TABLE tempAudit SELECT * FROM accountingAudit';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'DROP TABLE  accountingAudit';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = '';
	$q .= 'aaCode		 		INT NOT NULL  PRIMARY KEY AUTO_INCREMENT,';
	$q .= 'aaSysTran			INT,';
	$q .= 'aaType				CHAR(2),';
	$q .= 'aaTran				INT,';
	$q .= 'aaPostingDate		DATE,';
	$q .= 'aaEffectiveDate	DATE,';
	$q .= 'aaAccountingYear	INT,';
	$q .= 'aaAccountingPeriod INT,';
	$q .= 'aaCreatedBy		INT,';
	$q .= 'aaCreatedOn		CHAR(14),';
	$q .= 'aaLastUpdateBy		INT,';
	$q .= 'aaLastUpdateOn		CHAR(14),';
	$q .= 'lastAccessTime		CHAR(14))';
	udbcreatetable( 'accountingAudit', $q );
	udbsettablefortransactions( 'accountingAudit' );
	$q = 'ALTER TABLE accountingAudit AUTO_INCREMENT = 9000000';
	$result = mysql_query( $q );

	if ($result == null) {
		exit( 'cat set auto increment on audit' );
	}

	$q = 'SELECT * FROM tempAudit ORDER BY aaCreatedOn,  aaCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$done = 0;

	while ($row = mysql_fetch_array( $result )) {
		unset( $row[aaCode] );
		$aaType = $row['aaType'];
		$aaTran = $row['aaTran'];
		$x = _isthisrecordinauditalready( $aaType, $aaTran );

		if ($x == true) {
			continue;
		}


		if ($aaType == 'P') {
			$x = _isthisadeletedpolicytransaction( $aaTran );

			if ($x == true) {
				continue;
			}
		}

		$row2 = array(  );
		foreach ($row as $key => $value) {
			if (substr( $key, 0, 2 ) != 'aa') {
				continue;
			}

			$row2[$key] = $value;
		}

		$audit = new AccountingAudit( null );

		if ($done == 0) {
			$row2['aaCode'] = 9000000;
		}

		++$done;
		unset( $audit[_fldForUpdatedBy] );
		unset( $audit[_fldForUpdatedWhen] );
		$audit->insert( $row2 );
	}

	$q = 'SELECT aaCode, aaTran, aaSysTran, aaType FROM accountingAudit 
		WHERE aaSysTran IS NULL OR aaSysTran = 0
		ORDER BY  aaCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$done = 0;

	while ($row = mysql_fetch_array( $result )) {
		$aaTran = $row['aaTran'];
		$aaType = $row['aaType'];
		$aaCode = $row['aaCode'];
		$aaSysTran = $row['aaSysTran'];
		++$done;

		if ($aaType == 'P') {
			$ptCode = $aaTran;
			$pt = new PolicyTransaction( $ptCode );
			$tnCode = $pt->get( 'ptSysTran' );

			if ($tnCode <= 0) {
				$tnCode = fcreatesystemtran(  );
				$pt->set( 'ptSysTran', $tnCode );
				unset( $pt[_fldForUpdatedBy] );
				unset( $pt[_fldForUpdatedWhen] );
				$pt->update(  );
			}

			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $ptCode );
			$st->set( 'tnType', 'PT' );
			$st->set( 'tnCreatedBy', $pt->get( 'ptCreatedBy' ) );
			$st->set( 'tnCreatedOn', $pt->get( 'ptCreatedOn' ) );
			$st->update(  );
			_correctaudit( $aaCode, $ptCode, 'P', $tnCode );
			$ptClientTran = $pt->get( 'ptClientTran' );

			if (0 < $ptClientTran) {
				$ct = new ClientTransaction( $ptClientTran );
				$ct->set( 'ctSysTran', $tnCode );
				unset( $ct[_fldForUpdatedBy] );
				unset( $ct[_fldForUpdatedWhen] );
				$ct->update(  );
				_correctaudit( $aaCode, $ptClientTran, 'C', $tnCode );
			}

			$ptMainInsCoTran = $pt->get( 'ptMainInsCoTran' );

			if (0 < $ptMainInsCoTran) {
				$it = new InsCoTransaction( $ptMainInsCoTran );
				$it->set( 'itSysTran', $tnCode );
				unset( $it[_fldForUpdatedBy] );
				unset( $it[_fldForUpdatedWhen] );
				$it->update(  );
				_correctaudit( $aaCode, $ptMainInsCoTran, 'I', $tnCode );
			}

			$ptAddOnInsCoTran = $pt->get( 'ptAddOnInsCoTran' );

			if (0 < $ptAddOnInsCoTran) {
				$it = new InsCoTransaction( $ptAddOnInsCoTran );
				$it->set( 'itSysTran', $tnCode );
				unset( $it[_fldForUpdatedBy] );
				unset( $it[_fldForUpdatedWhen] );
				$it->update(  );
				_correctaudit( $aaCode, $ptAddOnInsCoTran, 'I', $tnCode );
			}

			$ptIntroducerTran = $pt->get( 'ptIntroducerTran' );

			if (0 < $ptIntroducerTran) {
				$rt = new IntroducerTransaction( $ptIntroducerTran );
				$rt->set( 'rtSysTran', $tnCode );
				unset( $rt[_fldForUpdatedBy] );
				unset( $rt[_fldForUpdatedWhen] );
				$rt->update(  );
				_correctaudit( $aaCode, $ptIntroducerTran, 'R', $tnCode );
			}
		}


		if ($aaType == 'C') {
			$ct = new ClientTransaction( $aaTran );
			$ctSysTran = $ct->get( 'ctSysTran' );

			if (0 < $ctSysTran) {
				_correctaudit( $aaCode, $aaTran, 'C', $ctSysTran );
				continue;
			}

			$tnCode = fcreatesystemtran(  );
			$ct->set( 'ctSysTran', $tnCode );
			unset( $ct[_fldForUpdatedBy] );
			unset( $ct[_fldForUpdatedWhen] );
			$ct->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $aaTran );
			$st->set( 'tnType', 'CT' );
			$st->set( 'tnCreatedBy', $ct->get( 'ctCreatedBy' ) );
			$st->set( 'tnCreatedOn', $ct->get( 'ctCreatedOn' ) );
			$st->update(  );
			_correctaudit( $aaCode, $aaTran, 'C', $tnCode );
			$ctPolicyTran = $ct->get( 'ctPolicyTran' );

			if (0 < $ctPolicyTran) {
				$pt = new PolicyTransaction( $ctPolicyTran );
				$pt->set( 'ptSysTran', $tnCode );
				unset( $pt[_fldForUpdatedBy] );
				unset( $pt[_fldForUpdatedWhen] );
				$pt->update(  );
				$ptMainInsCoTran = $pt->get( 'ptMainInsCoTran' );

				if (0 < $ptMainInsCoTran) {
					$it = new InsCoTransaction( $ptMainInsCoTran );
					$it->set( 'itSysTran', $tnCode );
					unset( $it[_fldForUpdatedBy] );
					unset( $it[_fldForUpdatedWhen] );
					$it->update(  );
				}

				$ptAddOnInsCoTran = $pt->get( 'ptAddOnInsCoTran' );

				if (0 < $ptAddOnInsCoTran) {
					$it = new InsCoTransaction( $ptAddOnInsCoTran );
					$it->set( 'itSysTran', $tnCode );
					unset( $it[_fldForUpdatedBy] );
					unset( $it[_fldForUpdatedWhen] );
					$it->update(  );
				}

				$ptIntroducerTran = $pt->get( 'ptIntroducerTran' );

				if (0 < $ptIntroducerTran) {
					$rt = new IntroducerTransaction( $ptIntroducerTran );
					$rt->set( 'rtSysTran', $tnCode );
					unset( $rt[_fldForUpdatedBy] );
					unset( $rt[_fldForUpdatedWhen] );
					$rt->update(  );
				}
			}
		}


		if ($aaType == 'I') {
			$it = new InsCoTransaction( $aaTran );
			$tnCode = $it->get( 'itSysTran' );

			if ($tnCode <= 0) {
				$tnCode = fcreatesystemtran(  );
				$it->set( 'itSysTran', $tnCode );
				unset( $it[_fldForUpdatedBy] );
				unset( $it[_fldForUpdatedWhen] );
				$it->update(  );
			}

			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $aaTran );
			$st->set( 'tnType', 'IT' );
			$st->set( 'tnCreatedBy', $it->get( 'itCreatedBy' ) );
			$st->set( 'tnCreatedOn', $it->get( 'itCreatedOn' ) );
			$st->update(  );
			_correctaudit( $aaCode, $aaTran, 'I', $tnCode );
		}


		if ($aaType == 'R') {
			$rt = new IntroducerTransaction( $aaTran );
			$rtSysTran = $rt->get( 'rtSysTran' );

			if ($rtSysTran == 0) {
				$tnCode = fcreatesystemtran(  );
				$rt->set( 'rtSysTran', $tnCode );
				unset( $rt[_fldForUpdatedBy] );
				unset( $rt[_fldForUpdatedWhen] );
				$rt->update(  );
				$st = new SystemTransaction( $tnCode );
				$st->set( 'tnTran', $aaTran );
				$st->set( 'tnType', 'NR' );
				$st->set( 'tnCreatedBy', $rt->get( 'rtCreatedBy' ) );
				$st->set( 'tnCreatedOn', $rt->get( 'rtCreatedOn' ) );
				$st->update(  );
			} 
else {
				$tnCode = $rtSysTran;
			}

			_correctaudit( $aaCode, $aaTran, 'R', $tnCode );
		}


		if ($aaType == 'B') {
			$bt = new CashBatch( $aaTran );
			$btSysTran = $bt->get( 'btSysTran' );

			if (0 < $btSysTran) {
				_correctaudit( $aaCode, $aaTran, 'B', $btSysTran );
				continue;
			}

			$tnCode = fcreatesystemtran(  );
			$bt->set( 'btSysTran', $tnCode );
			unset( $bt[_fldForUpdatedBy] );
			unset( $bt[_fldForUpdatedWhen] );
			$bt->update(  );
			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $aaTran );
			$st->set( 'tnType', 'CB' );
			$st->set( 'tnCreatedBy', $bt->get( 'btWhoPosted' ) );
			$st->set( 'tnCreatedOn', $bt->get( 'btWhenPosted' ) );
			$st->update(  );
			_correctaudit( $aaCode, $aaTran, 'B', $tnCode );
			$q = '' . 'SELECT * FROM cashBatchItems WHERE biBatch = ' . $aaTran . ' ORDER BY biCode';
			$result2 = udbquery( $q );

			if ($result2 == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}


			while ($row = udbgetrow( $result2 )) {
				$item = new CashBatchItem( $row );
				$type = $item->get( 'biPayeeType' );
				$tran = $item->get( 'biTrans' );

				if ($type == 'C') {
					$ct = new ClientTransaction( $tran );
					$ct->set( 'ctSysTran', $tnCode );
					unset( $ct[_fldForUpdatedBy] );
					unset( $ct[_fldForUpdatedWhen] );
					$ct->update(  );
					_correctaudit( $aaCode, $aaTran, 'C', $tnCode );
				}


				if ($type == 'I') {
					$it = new InsCoTransaction( $tran );
					$it->set( 'itSysTran', $tnCode );
					unset( $it[_fldForUpdatedBy] );
					unset( $it[_fldForUpdatedWhen] );
					$it->update(  );
					_correctaudit( $aaCode, $aaTran, 'I', $tnCode );
				}


				if ($type == 'N') {
					$rt = new IntroducerTransaction( $tran );
					$rt->set( 'rtSysTran', $tnCode );
					unset( $rt[_fldForUpdatedBy] );
					unset( $rt[_fldForUpdatedWhen] );
					$rt->update(  );
					_correctaudit( $aaCode, $aaTran, 'R', $tnCode );
					continue;
				}
			}
		}


		if (( ( ( ( ( $aaType == 'CC' || $aaType == 'CI' ) || $aaType == 'CN' ) || $aaType == 'NC' ) || $aaType == 'NI' ) || $aaType == 'NN' )) {
			$jn = new Journal( $aaTran );
			$jnType = $jn->get( 'jnType' );
			$jnSysTran = $jn->get( 'jnSysTran' );

			if ($jnSysTran <= 0) {
				$tnCode = fcreatesystemtran(  );
				$jn->set( 'jnSysTran', $tnCode );
				unset( $jn[_fldForUpdatedBy] );
				unset( $jn[_fldForUpdatedWhen] );
				$jn->update(  );
			} 
else {
				$tnCode = $jnSysTran;
			}

			$jnlType = $jn->get( 'jnType' );
			$jnTran = $jn->get( 'jnTran' );
			_correctaudit( $aaCode, $aaTran, $aaType, $tnCode );

			if (( $jnlType == 'CC' || $jnlType == 'NC' )) {
				$ct = new ClientTransaction( $jnTran );
				$ct->set( 'ctSysTran', $tnCode );
				unset( $ct[_fldForUpdatedBy] );
				unset( $ct[_fldForUpdatedWhen] );
				$ct->update(  );
			}


			if (( $jnlType == 'CI' || $jnlType == 'NI' )) {
				$it = new InscoTransaction( $jnTran );
				$it->set( 'itSysTran', $tnCode );
				unset( $it[_fldForUpdatedBy] );
				unset( $it[_fldForUpdatedWhen] );
				$it->update(  );
			}


			if (( $jnlType == 'CN' || $jnlType == 'NN' )) {
				$rt = new IntroducerTransaction( $jnTran );
				$rt->set( 'rtSysTran', $tnCode );
				unset( $rt[_fldForUpdatedBy] );
				unset( $rt[_fldForUpdatedWhen] );
				$rt->update(  );
			}

			$st = new SystemTransaction( $tnCode );
			$st->set( 'tnTran', $jn->getKeyValue(  ) );
			$st->set( 'tnType', $jnlType );
			$st->set( 'tnCreatedBy', $jn->get( 'jnCreatedBy' ) );
			$st->set( 'tnCreatedOn', $jn->get( 'jnCreatedOn' ) );
			$st->update(  );
			continue;
		}
	}

	$q = 'UPDATE  accountingAudit, clientTransactions
		set aaSysTran = ctSysTran
		where aaTran = ctCode
		and aaSysTran < 1
		AND aaType = \'C\'
		';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE  accountingAudit, policyTransactions
	set aaSysTran = ptSysTran
	where aaTran = ptCode
	and aaSysTran < 1
	AND aaType = \'P\'

		';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE  accountingAudit, inscoTransactions
	set aaSysTran = itSysTran
	where aaTran = itCode
	and aaSysTran < 1
	AND aaType = \'I\'
	
		';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	$q = 'UPDATE  accountingAudit, journals
	set aaSysTran = jnSysTran
	where aaTran = jnCode
	and aaSysTran < 1
	AND aaType = \'CC\'

		';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}

	echo 'ALL DONE';
?>