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

	function fcreatesystemtran() {
		$times = 0;
		$code = 0;

		while (true) {
			$q = 'SELECT ssSequence FROM systemSequeneces WHERE ssCode = 1';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );
			$code = $row['ssSequence'];
			$next = $code + 1;
			$q = '' . 'UPDATE systemSequeneces SET ssSequence = ' . $next . ' WHERE ssCode = 1';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$q = '' . 'INSERT INTO systemTransactions (tnCode) VALUES (' . $code . ')';
			$result = udbquery( $q );

			if ($result == true) {
				break;
			}


			if (20 <= ++$times) {
				trigger_error( 'cant insert system trans', E_USER_ERROR );
				continue;
			}
		}

		return $code;
	}

	function flogvisit($page, $user) {
		if (READ_ONLY == true) {
			return null;
		}

		$name = $user->getFullName(  );
		$date = ugettimenow(  );
		$url = $_SERVER['REMOTE_ADDR'];
		$q = '' . 'INSERT INTO accesslog (loPage, loName,  loURL, loWhen)
				VALUES("' . $page . '","' . $name . '","' . $url . '","' . $date . '")';
		$result = udbquery( $q );

		if ($result == null) {
			trigger_error( 'cant add to visit log' . udblasterror(  ), E_USER_ERROR );
		}

	}

	function fmakesecurityid() {
		global $securityID;

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$remoteAddress = $_SERVER['REMOTE_ADDR'];
		$date = date( 'Ymd' );
		$securityID = md5( $userAgent . $remoteAddress . $date );
	}

	function flocationheader($url) {
		global $sessionName;

		if (( isset( $sessionName ) || 0 < strlen( $sessionName ) )) {
			if (strpos( $url, '?sn=' ) == false) {
				if (strpos( $url, '?' ) == false) {
					$c = '?';
				} 
else {
					$c = '&';
				}

				$url .= $c . 'sn=' . $sessionName;
			}
		}

		ulocationheader( $url );
	}

	function fnewsessionname() {
		$prefix = 'SN';
		$maxToTry = 100;
		$i = 1;

		while ($i < $maxToTry) {
			$sessionName = $prefix . $i;

			if (!array_key_exists( $sessionName, $_COOKIE )) {
				break;
			}

			++$i;
		}


		if (!isset( $sessionName )) {
			trigger_error( '' . 'more that ' . $maxToTry . ' sessions started', E_USER_ERROR );
		}

		return $sessionName;
	}

	function fhandlelinks($template, $input) {
		global $levelsRequired;
		global $user;
		global $isUserSysManager;

		if (method_exists( $template, 'doBeforeLeaving' )) {
			$messg = $template->doBeforeLeaving( $input );

			if ($messg != null) {
				$template->setMessage( $messg );
				return false;
			}
		}

		$script = $input['link'];
		$posn = strrpos( $script, '/' );

		if ($posn === false) {
			$prog = $script;
		} 
else {
			$prog = substr( $script, $posn + 1 );
		}


		if (( isset( $levelsRequired[$prog] ) && $isUserSysManager != true )) {
			$actual = 0;

			if (isset( $user ) == true) {
				$actual = $user->getLevel(  );
			}

			$required = $levelsRequired[$prog];

			if ($actual < $required) {
				$template->setMessage( 'Sorry...you are not allowed to access this option' );
				return false;
			}
		}

		$posn = strpos( $script, '?' );

		if ($posn === false) {
			$file = $script;
		} 
else {
			$file = substr( $script, 0, $posn );
		}


		if (file_exists( $file ) == false) {
			$template->setMessage( 'sorry...that option is not available' );
			return false;
		}

		$pos = strpos( $script, 'returnTo' );

		if ($pos !== false) {
			$returnTo = $template->popReturn(  );

			if ($returnTo == null) {
				trigger_error( 'cant get return to link ', E_USER_ERROR );
			}


			if (strpos( $script, '?' ) == false) {
				$c = '?';
			} 
else {
				$c = '&';
			}

			$script = $script . $c . '=' . $returnTo;
		}

		flocationheader( $script );
		exit(  );
	}

	function fmoveup($table, $keyField, $sequenceField, $select, $keyToMove) {
		$q = '' . 'SELECT ' . $keyField . ', ' . $sequenceField . ' FROM ' . $table;

		if ($select != null) {
			$q .= '' . ' WHERE ' . $select;
		}

		$q .= '' . ' ORDER BY ' . $sequenceField;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$found = array(  );
		$num = 0;

		while ($row = udbgetrow( $result )) {
			$record = array(  );
			$record[0] = $row[$keyField];
			$record[1] = $row[$sequenceField];
			$found[$num++] = $record;
		}

		$total = count( $found );
		$x = 0 - 1;
		$i = 0;

		while ($i < $total) {
			$record = $found[$i];
			$key = $record[0];

			if ($key != $keyToMove) {
				continue;
			}

			$x = $i;
			break;
			++$i;
		}


		if ($x < 1) {
			return null;
		}

		$record = $found[$x];
		$thisKey = $record[0];
		$thisSequence = $record[1];
		$record = $found[$x - 1];
		$prevKey = $record[0];
		$prevSequence = $record[1];
		$q = '' . 'UPDATE ' . $table . ' SET ' . $sequenceField . '=' . $prevSequence . ' WHERE ' . $keyField . '=' . $thisKey;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'UPDATE ' . $table . ' SET ' . $sequenceField . '=' . $thisSequence . ' WHERE ' . $keyField . '=' . $prevKey;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

	}

	function fmovedown($table, $keyField, $sequenceField, $select, $keyToMove) {
		$q = '' . 'SELECT ' . $keyField . ', ' . $sequenceField . ' FROM ' . $table;

		if ($select != null) {
			$q .= '' . ' WHERE ' . $select;
		}

		$q .= '' . ' ORDER BY ' . $sequenceField;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$found = array(  );
		$num = 0;

		while ($row = udbgetrow( $result )) {
			$record = array(  );
			$record[0] = $row[$keyField];
			$record[1] = $row[$sequenceField];
			$found[$num++] = $record;
		}

		$total = count( $found );
		$x = 0 - 1;
		$i = 0;

		while ($i < $total) {
			$record = $found[$i];
			$key = $record[0];

			if ($key != $keyToMove) {
				continue;
			}

			$x = $i;
			break;
			++$i;
		}


		if ($x == 0 - 1) {
			return null;
		}


		if ($x == $total - 1) {
			return null;
		}

		$record = $found[$x];
		$thisKey = $record[0];
		$thisSequence = $record[1];
		$record = $found[$x + 1];
		$nextKey = $record[0];
		$nextSequence = $record[1];
		$q = '' . 'UPDATE ' . $table . ' SET ' . $sequenceField . '=' . $nextSequence . ' WHERE ' . $keyField . '=' . $thisKey;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$q = '' . 'UPDATE ' . $table . ' SET ' . $sequenceField . '=' . $thisSequence . ' WHERE ' . $keyField . '=' . $nextKey;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

	}

	function fhandleback($template, $input) {
		global $levelsRequired;
		global $user;

		if (method_exists( $template, 'doBeforeLeaving' )) {
			$messg = $template->doBeforeLeaving( $input );

			if ($messg != null) {
				$template->setMessage( $messg );
				return false;
			}
		}

		$script = $input['back'];
		$posn = strpos( $script, '/' );

		if ($posn === false) {
			$prog = $script;
		} 
else {
			$prog = substr( $script, $posn + 1 );
		}


		if (isset( $levelsRequired[$prog] )) {
			$actual = 0;

			if (isset( $user ) == true) {
				$actual = $user->getLevel(  );
			}

			$required = $levelsRequired[$prog];

			if ($actual < $required) {
				$template->setMessage( 'Sorry...you are not allowed to access this option' );
				return false;
			}
		}

		flocationheader( $script );
		exit(  );
	}

	function faddtourl($url, $toAdd) {
		if (strpos( $url, '?' ) == false) {
			$c = '?';
		} 
else {
			$c = '&';
		}

		$url .= $c . $toAdd;
		return $url;
	}

	function fsetsequence($type, $master) {
		if (strlen( $type ) == 0) {
			trigger_error( 'not a suitable type', E_USER_ERROR );
		}


		if (( !is_numeric( $master ) || $master <= 0 )) {
			trigger_error( 'no key value', E_USER_ERROR );
		}

		$q = '' . 'SELECT * FROM sequences WHERE sqType=\'' . $type . '\' AND sqMaster=' . $master;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( 'wrong select' . $q, E_USER_ERROR );
		}

		$num = udbnumberofrows( $result );

		if ($num == 0) {
			$seq = new Sequence( null );
			$seq->set( 'sqType', $type );
			$seq->set( 'sqMaster', $master );
			$seq->insert( null );
		} 
else {
			$row = udbgetrow( $result );
			$seq = new Sequence( $row );
		}

		$last = $seq->get( 'sqLastUsed' );
		++$last;
		$seq->set( 'sqLastUsed', $last );
		$seq->update(  );
		return $last;
	}

	function fformatdatefordocument($sqlDate) {
		$format = 'd.m.y';
		$date = umakeunixdatefromsqldate( $sqlDate );
		$value = date( $format, $date );
		return $value;
	}

	function fcalcage($date, $today) {
		if (( ( $date == null || $date == '' ) || $date == '0000-00-00' )) {
			return 0;
		}

		$todayDay = substr( $today, 8, 2 );
		$todayMth = substr( $today, 5, 2 );
		$todayYr = substr( $today, 0, 4 );
		$dateDay = substr( $date, 8, 2 );
		$dateMth = substr( $date, 5, 2 );
		$dateYr = substr( $date, 0, 4 );
		$todayTot = $todayMth + 12 * $todayYr;
		$dateTot = $dateMth + 12 * $dateYr;
		$mths = $todayTot - $dateTot;

		if ($mths < 0) {
			return 0;
		}

		return $mths;
	}

	function fisdateinthisaccountingperiod($date) {
		global $periodFrom;
		global $periodTo;

		if (( $date < $periodFrom || $periodTo < $date )) {
			return false;
		}

		return true;
	}

	function fsettoendofyear() {
		global $accountingYear;
		global $accountingYearDesc;
		global $accountingPeriod;
		global $periodFrom;
		global $periodTo;

		$year = new AccountingYear( YEAR_CODE_FOR_YEAR_END );
		$period = new AccountingPeriod( PERIOD_CODE_FOR_YEAR_END );
		$accountingYear = $year->ayYear;
		$accountingYearDesc = $year->ayName;
		$accountingPeriod = $period->apPeriod;
		$periodFrom = $period->apFromDate;
		$periodTo = $period->apToDate;
		$currPeriodDesc = $period->getPeriodDescription(  );
		return $currPeriodDesc;
	}

	function fgetaccountingperioddesc($period, $year) {
		$q = '' . 'SELECT ayName FROM accountingYears WHERE ayYear = ' . $year;
		$result = udbquery( $q );

		if ($result == null) {
			return '';
		}

		$row = udbgetrow( $result );

		if ($row == null) {
			return '';
		}

		$desc = $row['ayName'];

		if (( 0 < $period && $period < 10 )) {
			$period = '0' . $period;
		}

		$desc = $period . '/' . $desc;
		return $desc;
	}

	function fgetaccountingperiodandyear($date) {
		$q = '' . 'SELECT * FROM accountingPeriods, accountingYears
		WHERE ayCode = apYear
		AND \'' . $date . '\' >= apFromDate AND \'' . $date . '\' <= apToDate';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}


		if (udbnumberofrows( $result ) != 1) {
			return null;
		}

		$row = udbgetrow( $result );
		$yr = $row['ayYear'];
		$per = $row['apPeriod'];
		$out = array(  );
		$out['period'] = $per;
		$out['year'] = $yr;
		return $out;
	}

	function flinktopolicytrans() {
	}

	function fgetjournaltypedescription($jnlType) {
		$desc = null;

		if ($jnlType == 'CC') {
			$desc = 'Client Cash Journal';
		}


		if ($jnlType == 'CI') {
			$desc = 'Insurance Company Cash Journal';
		}


		if ($jnlType == 'CN') {
			$desc = 'Introducer Cash Journal';
		}


		if ($jnlType == 'NC') {
			$desc = 'Client Non-Cash Journal';
		}


		if ($jnlType == 'NI') {
			$desc = 'Insurance Company Non-Cash Journal';
		}


		if ($jnlType == 'NN') {
			$desc = 'Introducer Non-Cash Journal';
		}

		return $desc;
	}

	function flog($file, $messg) {
		$fp = fopen( $file, 'a' );

		if ($fp == null) {
			trigger_error( '' . 'cant open log file ' . $file, E_USER_ERROR );
		}

		$m = date( 'Y-m-d H:i:s ' ) . $messg . '
';
		fwrite( $fp, $m );
		fclose( $fp );
	}

?>