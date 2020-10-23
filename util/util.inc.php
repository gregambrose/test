<?php

	function urequireclasses($directory) {
		if (is_dir( $directory ) == false) {
			return 0;
		}


		if ($dir = opendir( $directory )) {

		$files = array(  );

		while ( false !== ($file = readdir( $dir ))) {
			if (strstr( $file, '.class.php' ) === false) {
				continue;
			}


			if (strstr( $file, '.' ) === 0) {
				continue;
			}

			$files[] = $file;
		  }
                }

		closedir( $dir );
		sort( $files );
		reset( $files );
		foreach ($files as $key => $file) {
			$fullFile = $directory . $file;
			require_once( $fullFile );
		}

		return 1;
	}

	function ulocationheader($url) {
		session_write_close(  );
		header( 'Cache-Control: private' );
		header( '' . 'Location: ' . $url );
		exit(  );
	}

	function urunmvc($baseName, $html) {
		global $session;

		$baseUpper = strtoupper( substr( $baseName, 0, 1 ) ) . substr( $baseName, 1 );
		$baseLower = strtolower( substr( $baseName, 0, 1 ) ) . substr( $baseName, 1 );
		$controllerLower = $baseLower . 'Controller';
		$controllerUpper = $baseUpper . 'Controller';
		$controllerClassName = $controllerUpper . '.class.php';
		require_once( CONTROLLER_PATH . $controllerClassName );
		$$controllerLower = &$session->get( $controllerLower );

		if ($$controllerLower == null) {
			$$controllerLower = new $controllerUpper(  );
		}

		$viewLower = $baseLower . 'View';
		$viewUpper = $baseUpper . 'View';
		$viewClassName = $viewUpper . '.class.php';
		require_once( VIEW_PATH . $viewClassName );
		$$viewLower = &$session->get( $viewLower );

		if ($$viewLower == null) {
			$x = new $viewUpper( $html );
			$$viewLower = new $viewUpper( $html );
		}

		$modelLower = $baseLower . 'Model';
		$modelUpper = $baseUpper . 'Model';
		$modelClassName = $modelUpper . '.class.php';
		require_once( MODEL_PATH . $modelClassName );
		$$modelLower = &$session->get( $modelLower );

		if ($$modelLower == null) {
			$$modelLower = new $modelUpper(  );
		}

		$$modelLower->setView( $$viewLower );
		$$viewLower->setModel( $$modelLower );
		$$controllerLower->setModel( $$modelLower );
		$$controllerLower->process(  );
		$session->set( $controllerLower, $$controllerLower );
		$session->set( $viewLower, $$viewLower );
		$session->set( $modelLower, $$modelLower );
		session_write_close(  );
	}

	function uformatmoney($amt) {
		if (strpos( $amt, '.' ) === false) {
			$amt /= 100;
			$amt = round( $amt, 2 );
		}

		$neg = false;

		if ($amt < 0) {
			$neg = true;
			$amt = 0 - $amt;
		}

		$out = sprintf( '%.2f', $amt );

		if (substr( $out, 0, 1 ) == '0') {
			$out = substr( $out, 1 );
		}


		if ($neg == true) {
			$out = '-' . $out;
		}

		return $out;
	}

	function uformatmoneywithcommas($amt) {
		if (strpos( $amt, '.' ) === false) {
			$amt /= 100;
			$amt = round( $amt, 2 );
		}

		$out = number_format( $amt, 2, '.', ',' );
		return $out;
	}

	function uconvertmoneytointeger($amt) {
		$value = (bool)$amt;

		if (substr( $value, 0, 1 ) == '-') {
			$neg = true;
			$value = substr( $value, 1 );
		} 
else {
			$neg = false;
		}


		if (strpos( (bool)$value, '.' ) !== false) {
			$x = $value * 100 + 0.5;
			$y = (bool)$x;
			$p = strpos( $x, '.' );
			$value = substr( $x, 0, $p );
		}

		$value = intval( $value );

		if ($neg == true) {
			$value = 0 - $value;
		}

		return $value;
	}

	function uroundmoney($amt) {
		if (0 <= $amt) {
			$amt = (int)$amt + 0.5;
		} 
else {
			$amt = (int)$amt - 0.5;
		}

		return $amt;
	}

	function ucolourtorgb($colour) {
		if ($colour[0] == '#') {
			$colour = substr( $colour, 1 );
		}


		if (strlen( $colour ) == 6) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} 
else {
			if (strlen( $colour ) == 3) {
				list( $r, $g, $b ) = array( $colour[0], $colour[1], $colour[2] );
			} 
else {
				return false;
			}
		}

		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return array( $r, $g, $b );
	}

	function ucalcusingrate($amt, $rate) {
		if ($amt == 0) {
			return 0;
		}

		$value = $amt * $rate / 10000;

		if (0 < $value) {
			$value = (int)$value + 0.5;
		}


		if ($value < 0) {
			$value = (int)$value - 0.5;
		}

		return $value;
	}

	function ucalcinclusiveusingrate($amt, $rate) {
		if ($amt == 0) {
			return 0;
		}

		$value = $amt / ( 1 + $rate / 10000 );

		if (0 < $value) {
			$value = (int)$value + 0.5;
		}


		if ($value < 0) {
			$value = (int)$value - 0.5;
		}

		return $value;
	}

	function udbopen($type, $host, $db, $user, $password) {
		global $_udbType;
		global $_udbCon;
		global $_udbDB;

		$_udbType = $type;

		
		$_udbCon=@mysqli_connect( $host, $user, $password );
		if (!$_udbCon) { trigger_error( 'couldn\'t connect to mysql server', E_USER_ERROR ); }
		$_udbDB = mysqli_select_db( $_udbCon , $db, ); 
        if (!$_udbDB) trigger_error( '' . 'connected to mysql, but couldnt access database ' . $db, E_USER_ERROR );
	}

	function udbquery($q) {
		global $_udbType;
		global $_udbCon;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbQuery type incorrect' );
		}

		$result = @mysqli_query( $_udbCon, $q );
		return $result;
	}

	function udbgetrow($result) {
		global $_udbType;
		global $_udbCon;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbLastError type incorrect' );
		}

		return mysqli_fetch_assoc( $result );
	}

	function udbquerysingle($query) {
		$result = udbquery( $query );

		if ($result == false) {
			trigger_error( udblasterror(  ) . $query, E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		udbfreeresult( $result );
		return $row;
	}

	function udbquerymultiple($query) {
		$result = udbquery( $query );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$out = array(  );

		while ($row = udbgetrow( $result )) {
			$out[] = $row;
		}

		udbfreeresult( $result );
		return $out;
	}

	function udblasterror() {
		global $_udbType;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbLastError type incorrect', E_USER_ERROR );
		}

		return @mysqli_error(  );
	}

	function udbfreeresult($result) {
		global $_udbType;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbLastError type incorrect', E_USER_ERROR );
		}


		if (@mysqli_free_result( $result ) == false) {
			trigger_error( 'trying to free results', E_USER_ERROR );
		}

	}

	function udbgetinsertid() {
		global $_udbType;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbGetInsertID type incorrect', E_USER_ERROR );
		}

		$num = mysqli_insert_id(  );

		if ($num < 1) {
			trigger_error( 'Cant get insert id', E_USER_ERROR );
		}

		return mysqli_insert_id(  );
	}

	function udbnumberofrows($result) {
		global $_udbType;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbNumberOfRows type incorrect', E_USER_ERROR );
		}

		return mysqli_num_rows( $result );
	}

	function udbgetlistoffields($table) {
		global $_udbType;
		global $_udbCon;

		if ($_udbType != 'mysqli') {
			trigger_error( 'udbGetListOfFields type incorrect', E_USER_ERROR );
		}

		$q = '' . 'SHOW COLUMNS FROM ' . $table;
		$result = @mysqli_query( $_udbCon, $q );

		if ($result == null) {
			trigger_error( '' . 'util: cant get columns for table ' . $table . ' ' . mysqli_error(  ), E_USER_ERROR );
		}

		$flds = array(  );

		while ($row = mysqli_fetch_assoc( $_udbCon, $result )) {
			$fld = $row['Field'];
			$flds[$fld] = $fld;
		}

		return $flds;
	}

	function udbmakefieldsafe($fld) {
		if (( $fld == null || $fld == '' )) {
			return $fld;
		}


		if (get_magic_quotes_gpc(  )) {
			$fld = stripslashes( $fld );
		}


		if (!is_numeric( $fld )) {
			$fld = str_replace( ';', '\;', $fld );
			$fld = str_replace( '*', '\*', $fld );
			$fld = mysqli_real_escape_string( $fld );
		}

		return $fld;
	}

	function udbcreatetable($table, $query) {
		global $_udbCon;
		$q = '' . 'DROP TABLE ' . $table;
		@mysqli_query( $_udbCon,$q );
		$q = '' . 'CREATE TABLE ' . $table . ' (';
		$q .= $query;
		$result = @mysqli_query( $_udbCon, $q );

		if ($result == 1) {
			print '' . '<P>' . $table . ' Table Created<br>';
			return null;
		}

		print '' . '<P>' . $table . ' Table Not Created - ' . $q . '<br>' . mysqli_error(  );
		print mysqli_error(  );
	}

	function udbcreatetablenoreplace($table, $query) {
		$q = '' . 'CREATE TABLE ' . $table . ' (';
		$q .= $query;
		$result = @mysqli_query( $q );

		if ($result == 1) {
			print '' . '<P>' . $table . ' Table Created<br>';
			return null;
		}

		print '' . '<P>' . $table . ' Table Not Created - ' . $q . '<br>' . mysqli_error(  );
		print mysqli_error(  );
	}

	function udbcreateindex($table, $index, $col) {
		$q = 'CREATE INDEX ' . $index . ' ON ' . $table . '(' . $col . ')';
		$result = @mysqli_query( $q );

		if ($result == 1) {
			print '' . '<P>' . $table . ' Index Created<br>';
			return null;
		}

		print '' . '<P>' . $table . ' Index Not Created - ' . $q . '<br>';
		print mysqli_error(  );
	}

	function udbcantabledotransactions($table) {
		$result = udbquery( '' . 'SHOW TABLE STATUS LIKE \'' . $table . '\'' );

		if ($result == false) {
			trigger_error( '' . 'error with ' . $table . ' ' . udblasterror(  ), E_USER_ERROR );
		}

		$row = udbgetrow( $result );
		$type = $row['Engine'];

		if ($type == 'InnoDB') {
			return true;
		}

		return false;
	}

	function udbsettablefortransactions($table) {
		$ok = udbquery( '' . 'ALTER TABLE ' . $table . ' TYPE=INNODB;' );

		if ($ok == false) {
			trigger_error( '' . 'cant set table ' . $table . ' ' . udblasterror(  ), E_USER_ERROR );
		}

	}

	function udbstarttransaction() {
		global $transactionStarted;

		$ok = udbquery( 'SET AUTOCOMMIT=0;' );

		if ($ok == false) {
			trigger_error( 'cant turn auto commit off ' . udblasterror(  ), E_USER_ERROR );
		}

		$ok = udbquery( 'START TRANSACTION;' );

		if ($ok == false) {
			trigger_error( 'cant start transaction ' . udblasterror(  ), E_USER_ERROR );
		}

		$transactionStarted = true;
	}

	function udbcommittransaction() {
		global $transactionStarted;

		$ok = udbquery( 'COMMIT;' );

		if ($ok == false) {
			trigger_error( 'cant commit ' . udblasterror(  ), E_USER_ERROR );
		}

		$ok = udbquery( 'SET AUTOCOMMIT=1;' );

		if ($ok == false) {
			trigger_error( 'cant turn auto commit off ' . udblasterror(  ), E_USER_ERROR );
		}

		$transactionStarted = false;
	}

	function udbrollbacktransaction() {
		global $transactionStarted;

		$ok = udbquery( 'ROLLBACK;' );

		if ($ok == false) {
			trigger_error( 'cant roll back ' . udblasterror(  ), E_USER_ERROR );
		}

		$ok = udbquery( 'SET AUTOCOMMIT=1;' );

		if ($ok == false) {
			trigger_error( 'cant turn auto commit off ' . udblasterror(  ), E_USER_ERROR );
		}

		$transactionStarted = false;
	}

	function getsqlvaluestring($theValue, $theType) {
		$theValue = addslashes( $theValue );
		switch ($theType) {
			case 'text': {
				$theValue = ($theValue != '' ? '\'' . $theValue . '\'' : 'NULL');
				break;
			}

			case 'long': {
			}

			case 'int': {
				$theValue = ($theValue != '' ? intval( $theValue ) : 'NULL');
				break;
			}

			case 'double': {
				$theValue = ($theValue != '' ? '\'' . doubleval( $theValue ) . '\'' : 'NULL');
				break;
			}

			case 'date': {
				$theValue = ($theValue != '' ? '\'' . $theValue . '\'' : 'NULL');
			}
		}

		return $theValue;
	}

	function ugetphpversion() {
		$v = PHP_VERSION;
		$dot = strpos( $v, '.' );
		$versions = 4;

		if (0 < $dot) {
			$versions = substr( $v, 0, $dot );
		}

		return $versions;
	}

	function uhandlelinks($template, $input) {
		global $levelsRequired;
		global $user;

		if (method_exists( $template, 'doBeforeLeaving' )) {
			$messg = $template->doBeforeLeaving( $input );

			if ($messg != null) {
				$template->setMessage( $messg );
				return false;
			}
		}

		$script = $input['link'];

		if (isset( $levelsRequired[$script] )) {
			$actual = 0;

			if (( isset( $user ) == true && $user != null )) {
				$actual = $user->getLevel(  );
			}

			$required = $levelsRequired[$script];

			if ($actual < $required) {
				$template->setMessage( 'Sorry...you are not allowed to access this option' );
				return false;
			}
		}

		ulocationheader( $script );
		exit(  );
	}

	function ushowlist($template, $text, $tableName, $codeName, $descName, $sequenceName = null, $currentValue, $condition = null) {
		$q = '' . 'SELECT ' . $codeName . ', ' . $descName . ' FROM ' . $tableName . ' ';

		if ($condition != null) {
			$q .= '' . ' ' . $condition . ' ';
		}


		if ($sequenceName != null) {
			$q .= '' . 'ORDER BY ' . $sequenceName;
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$out = '';

		while ($row = udbgetrow( $result )) {
			$code = $row[$codeName];
			$desc = $row[$descName];

			if ($code == $currentValue) {
				$selected = 'selected';
			} 
else {
				$selected = '';
			}

			$template->set( 'code', $code );
			$template->set( 'desc', $desc );
			$template->set( 'selected', $selected );
			$out .= $template->parse( $text );
		}

		return $out;
	}

	function ucheckemailaddress($email) {
		$email = trim( $email );

		if (strlen( $email ) == 0) {
			return 0;
		}


		if (strlen( $email ) < 6) {
			return 0 - 1;
		}

		$posn = strpos( $email, '@' );

		if ($posn == 0) {
			return 0 - 1;
		}

		$posn = strrpos( $email, '.' );

		if ($posn == 0) {
			return 0 - 1;
		}

		$len = strlen( $email );

		if ($len - $posn < 3) {
			return 0 - 1;
		}


		if (function_exists( 'checkdnsrr' )) {
			$posn = strpos( $email, '@' );
			$domain = substr( $email, $posn + 1 );
			$ok = checkdnsrr( $domain );

			if ($ok == false) {
				return 0 - 2;
			}
		}

		return 1;
	}

	function uhtmlspecialchars($input) {
		$x = str_replace( '"', '&quot;', $input );
		return $x;
	}

	function uforhtml($input) {
		$x = stripslashes( $input );
		$x = htmlspecialchars( $x );
		return $x;
	}

	function uhtmlcrtobr($input) {
		$x = str_replace( '
', '<br>', $input );
		return $x;
	}

	function uhtmlbrtocr($input) {
		$x = str_replace( '<br>', '
', $input );
		$y = str_replace( '<BR>', '
', $x );
		return $x;
	}

	function useturlparameters($p1, $p2, $p3) {
		if (!isset( $_SERVER['PATH_INFO'] )) {
			return null;
		}

		$parameters = explode( '/', $_SERVER['PATH_INFO'] );

		if (( isset( $parameters[1] ) && isset( $p1 ) )) {
			$_GET[$p1] = $parameters[1];
		}


		if (( isset( $parameters[2] ) && isset( $p2 ) )) {
			$_GET[$p2] = $parameters[2];
		}


		if (( isset( $parameters[3] ) && isset( $p3 ) )) {
			$_GET[$p3] = $parameters[3];
		}

	}

	function ugetcurrenturlwithprotocol() {
		if (isset( $_SERVER['SERVER_PROTOCOL'] )) {
			$protocol = $_SERVER['SERVER_PROTOCOL'];
		} 
else {
			$protocol = '';
		}

		$prot = 'http://';

		if (substr( $protocol, 0, 5 ) == 'HTTPS') {
			$prot = 'https//';
		}

		$url = $prot . $_SERVER['HTTP_HOST'] . dirname( $_SERVER['PHP_SELF'] ) . '/';
		return $url;
	}

	function ugetcurrenturl() {
		$url = $_SERVER['HTTP_HOST'] . dirname( $_SERVER['PHP_SELF'] ) . '/';
		return $url;
	}

	function ugetthisurl() {
		$x = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
		$p = strrpos( $x, '/' );
		$q = substr( $x, 0, $p );
		$q .= '/';
		return $q;
	}

	function uaddslashes($in) {
		$out = $in;

		if (get_magic_quotes_gpc(  ) == false) {
			$out = addslashes( $in );
		}

		$out = str_replace( '
', '\n', $out );
		$out = str_replace( '
', '', $out );
		return $out;
	}

	function uremoveslashes($in) {
		$out = $in;

		if (get_magic_quotes_gpc(  ) == false) {
			$out = stripslashes( $in );
		}

		$out = str_replace( '\n', '
', $out );
		$out = str_replace( '\"', '"', $out );
		return $out;
	}

	function umakeinputsafe($in) {
		$out = trim( $in );
		return $out;
	}

	function ugettimenow() {
		if (defined( 'TIME_DIFFERENCE' )) {
			$diff = TIME_DIFFERENCE;
		} 
else {
			$diff = 0;
		}

		$new = mktime( date( 'H' ), date( 'i' ) + $diff, date( 's' ), date( 'm' ), date( 'd' ), date( 'Y' ) );
		$out = umakesqltimestampfromunixdate( $new );
		return $out;
	}

	function ugetmicrotime() {
		if (ugetphpversion(  ) <= 4) {
			list( $usec, $sec ) = explode( ' ', microtime(  ) );
			$time = (double)$usec + (double)$sec;
		} 
else {
			$time = microtime( true );
		}

		return $time;
	}

	function ugettoday() {
		$out = date( 'd/m/Y' );
		return $out;
	}

	function ugettodayassqldate() {
		$out = date( 'Y-m-d' );
		return $out;
	}

	function udaysinmonth($month, $year) {
		return ($month == 2 ? ($year % 4 ? 28 : 29) : ($month % 7 % 2 ? 31 : 30));
	}

	function uformatsqltimestamp($date) {
		if (strlen( $date ) < 12) {
			return '';
		}

		$year = substr( $date, 0, 4 );
		$month = substr( $date, 5, 2 );
		$day = substr( $date, 8, 2 );
		$hour = substr( $date, 11, 2 );
		$minute = substr( $date, 14, 2 );
		$return = '' . $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute;
		return $return;
	}

	function uformattimestamp($date) {
		if (strlen( $date ) < 12) {
			return '';
		}

		$year = substr( $date, 0, 4 );
		$month = substr( $date, 5, 2 );
		$day = substr( $date, 8, 2 );
		$hour = substr( $date, 11, 2 );
		$minute = substr( $date, 14, 2 );
		$return = '' . $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute;
		return $return;
	}

	function uformatourtimestamp($date) {
		if (strlen( $date ) < 12) {
			return '';
		}

		$year = substr( $date, 0, 4 );
		$month = substr( $date, 4, 2 );
		$day = substr( $date, 6, 2 );
		$hour = substr( $date, 8, 2 );
		$minute = substr( $date, 10, 2 );
		$return = '' . $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute;
		return $return;
	}

	function uformatourtimestamp2($date) {
		if (strlen( $date ) < 12) {
			return '';
		}

		$year = substr( $date, 0, 4 );
		$month = substr( $date, 4, 2 );
		$month = ugetmonthname( $month );
		$day = substr( $date, 6, 2 );
		$hour = substr( $date, 8, 2 );
		$minute = substr( $date, 10, 2 );
		$return = '' . $day . ' ' . $month . ' ' . $year . ' ' . $hour . ':' . $minute;
		return $return;
	}

	function uformatsqldate($date) {
		if (( $date != '' && $date != '0000-00-00' )) {
			$year = substr( $date, 0, 4 );
			$month = substr( $date, 5, 2 );
			$day = substr( $date, 8, 2 );
			$return = '' . $day . '/' . $month . '/' . $year;
		} 
else {
			$return = '';
		}

		return $return;
	}

	function uformatsqldate2($date) {
		$value = $date;

		if (!defined( 'DATE_FORMAT' )) {
			trigger_error( 'need DATE_FORMAT' );
		}

		$format = DATE_FORMAT;
		$day = substr( $value, 8, 2 );
		$month = substr( $value, 5, 2 );
		$year = substr( $value, 0, 4 );

		if (( is_numeric( $day ) == false || $day < 1 )) {
			return '';
		}


		if (( is_numeric( $month ) == false || $month < 1 )) {
			return '';
		}


		if (( is_numeric( $year ) == false || $year < 1 )) {
			return '';
		}

		$monthAlpha = ugetmonthname( $month );
		$value = str_replace( 'dd', $day, $format );
		$value = str_replace( 'mm', $month, $value );
		$value = str_replace( 'MMM', $monthAlpha, $value );
		$value = str_replace( 'yyyy', $year, $value );
		return $value;
	}

	function uformatsqldate3($sqlDate) {
		if (( $sqlDate == null || $sqlDate == '0000-00-00' )) {
			return '';
		}

		$format = 'jS F Y';
		$date = umakeunixdatefromsqldate( $sqlDate );
		$value = date( $format, $date );
		return $value;
	}

	function ugetmonthname($month) {
		$months = array( 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
		$name = '';
		reset( $months );
		foreach ($months as $key => $value) {
			if ($value == $month) {
				$name = $key;
				break;
			}
		}

		return $name;
	}

	function umakesqldate($value) {
		$elems = explode( '/', $value );

		if (count( $elems ) == 3) {
			$day = sprintf( $elems[0], '%2d' );
			$month = $elems[1];
			$year = $elems[2];

			if (( 0 < $year && $year < 20 )) {
				$year += 2000;
			}


			if (( 20 < $year && $year < 100 )) {
				$year += 1900;
			}

			$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );
		} 
else {
			$elems = explode( ' ', $value );

			if (count( $elems ) == 3) {
				$day = sprintf( $elems[0], '%2d' );
				$month = $elems[1];
				$year = $elems[2];
				settype( $day, 'integer' );
				settype( $year, 'integer' );

				if (( 0 <= $year && $year < 30 )) {
					$year += 2000;
				}


				if (( 30 <= $year && $year < 100 )) {
					$year += 1900;
				}

				$month = ugetmonthnumber( $month );

				if (0 < $month) {
					$ok = @checkdate( $month, $day, $year );

					if ($ok == true) {
						$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );
					}
				}
			}
		}


		if ($value == '0000-00-00') {
			$value = '';
		}

		return $value;
	}

	function umakesqldate2($value) {
		$elems = explode( ' ', $value );

		if (count( $elems ) != 3) {
			return null;
		}

		$day = sprintf( '%2d', $elems[0] );
		$month = $elems[1];
		$year = $elems[2];

		if (!is_numeric( $day )) {
			return null;
		}


		if (!is_numeric( $year )) {
			return null;
		}


		if (( 0 <= $year && $year < 30 )) {
			$year += 2000;
		}


		if (( 30 <= $year && $year < 100 )) {
			$year += 1900;
		}

		$month = _getmonthnumber( $month );

		if ($month == 0) {
			return null;
		}

		$ok = @checkdate( $month, $day, $year );

		if ($ok == false) {
			return null;
		}

		$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );

		if ($value == '0000-00-00') {
			$value = '';
		}

		return $value;
	}

	function umakesqldatefromourtimestamp($date) {
		if (strlen( $date ) != 14) {
			return null;
		}

		$out = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-' . substr( $date, 6, 2 );
		return $out;
	}

	function umakeourtimestamp($date, $atEnd) {
		$sqlDate = umakesqldate2( $date );

		if ($sqlDate == null) {
			return null;
		}

		$yr = substr( $sqlDate, 0, 4 );
		$mth = substr( $sqlDate, 5, 2 );
		$day = substr( $sqlDate, 8, 2 );
		$out = $yr . $mth . $day;

		if ($atEnd == true) {
			$out .= '235959';
		}

		return $out;
	}

	function umakesqltimestampfromsqldate($date, $startOfDay) {
		if (( $date == null || strlen( $date ) == 0 )) {
			exit( 'uMakeSQLTimestampFromSQLDate: bad date' );
		}

		$year = substr( $date, 0, 4 );
		$month = substr( $date, 5, 2 );
		$day = substr( $date, 8, 2 );

		if ($startOfDay == true) {
			$hour = '00';
			$minute = '00';
			$second = '00';
		} 
else {
			$hour = '23';
			$minute = '59';
			$second = '59';
		}

		$text = sprintf( '%04d%02d%02d%02d%02d%02d', $year, $month, $day, $hour, $minute, $second );
		return $text;
	}

	function uvalidatedate($date) {
		$elems = explode( '/', $date );

		if (count( $elems ) == 3) {
			$day = sprintf( '%2d', $elems[0] );
			$month = $elems[1];
			$year = $elems[2];

			if (( $day < 1 || 31 < $day )) {
				return false;
			}


			if (( $month < 1 || 12 < $month )) {
				return false;
			}


			if (( 0 <= $year && $year <= 30 )) {
				$year += 2000;
			}


			if (( 30 < $year && $year <= 99 )) {
				$year += 1900;
			}

			$ok = @checkdate( $month, $day, $year );
			return $ok;
		}

		$elems = explode( ' ', $date );

		if (count( $elems ) != 3) {
			return false;
		}

		$day = sprintf( $elems[0], '%2d' );
		$month = $elems[1];
		$year = $elems[2];

		if (!is_numeric( $day )) {
			return false;
		}


		if (!is_numeric( $year )) {
			return false;
		}

		settype( $day, 'integer' );
		settype( $year, 'integer' );

		if (( 0 <= $year && $year < 30 )) {
			$year += 2000;
		}


		if (( 30 <= $year && $year < 100 )) {
			$year += 1900;
		}

		$month = ugetmonthnumber( $month );

		if (0 < $month) {
			$ok = @checkdate( $month, $day, $year );

			if ($ok == false) {
				return false;
			}
		} 
else {
			return false;
		}

		$d = $day;
		$m = $month;
		$y = $year;

		if (( ( $y % 4 == 0 && $m == 2 ) && 29 < $d )) {
			return false;
		}


		if (( ( 0 < $y % 4 && $m == 2 ) && 28 < $d )) {
			return false;
		}


		if (( ( ( ( $m == 4 || $m == 6 ) || $m == 9 ) || $m == 11 ) && $d == 31 )) {
			return false;
		}

		return true;
	}

	function uaddmonthstonow($months) {
		if ($months < 0) {
			exit( 'uAddMonthsToNow: months negative' );
		}

		$now = date( 'YmdHis' );
		$day = substr( $now, 6, 2 );
		$month = substr( $now, 4, 2 );
		$year = substr( $now, 0, 4 );
		$month += $months;

		while (true) {
			if ($month <= 12) {
				break;
			}

			$month -= 12;
			++$year;
		}

		$days = cal_days_in_month( CAL_GREGORIAN, $month, $year );

		if ($days < $day) {
			$day = $days;
		}

		$date = sprintf( '%04d', $year ) . sprintf( '%02d', $month ) . sprintf( '%02d', $day ) . substr( $now, 8 );
		return $date;
	}

	function uaddmonthssqldate($date, $months) {
		$day = substr( $date, 8, 2 );
		$month = substr( $date, 5, 2 );
		$year = substr( $date, 0, 4 );

		if (0 < $months) {
			$month += $months;

			while (true) {
				if ($month <= 12) {
					break;
				}

				$month -= 12;
				++$year;
			}
		} 
else {
			if ($months < 0) {
				$month += $months;

				while (true) {
					if (0 < $month) {
						break;
					}

					$month += 12;
					--$year;
				}
			}
		}

		$days = cal_days_in_month( CAL_GREGORIAN, $month, $year );

		if ($days < $day) {
			$day = $days;
		}

		$date = sprintf( '%04d', $year ) . '-' . sprintf( '%02d', $month ) . '-' . sprintf( '%02d', $day );
		return $date;
	}

	function ugetdateusingdaysinthepast($days) {
		$now = date( 'YmdHis' );
		$day = substr( $now, 6, 2 );
		$month = substr( $now, 4, 2 );
		$year = substr( $now, 0, 4 );
		$unixNow = umakeunixdatefromdaymonthyear( $day, $month, $year );

		if ($unixNow == null) {
			exit( 'util.inc: cant get unix date' );
		}

		$unixThen = $unixNow - 86400 * $days;
		$newDate = umakesqldatefromunixdate( $unixThen );
		return $newDate;
	}

	function ucomparedatetotoday($date) {
		$now = date( 'Y-m-d' );

		if ($date < $now) {
			return 0 - 1;
		}


		if ($now < $date) {
			return 1;
		}

		return 0;
	}

	function umakeunixdatefromdaymonthyear($day, $month, $year) {
		$ok = checkdate( $month, $day, $year );

		if ($ok == false) {
			return null;
		}

		$time = mktime( 0, 0, 0, $month, $day, $year, 0 - 1 );
		return $time;
	}

	function umakeunixdatefromsqldate($sqlDate) {
		if ($sqlDate == null) {
			return null;
		}


		if (strlen( $sqlDate ) != 10) {
			return null;
		}

		$year = (int)substr( $sqlDate, 0, 4 );
		$month = (int)substr( $sqlDate, 5, 2 );
		$day = (int)substr( $sqlDate, 8, 2 );
		$time = mktime( 0, 0, 0, $month, $day, $year, 0 - 1 );
		return $time;
	}

	function umakeunixdatefromsqltimestamp($sqlTimestamp) {
		if ($sqlTimestamp == null) {
			return null;
		}


		if (strlen( $sqlTimestamp ) != 14) {
			return null;
		}

		$year = (int)substr( $sqlTimestamp, 0, 4 );
		$month = (int)substr( $sqlTimestamp, 4, 2 );
		$day = (int)substr( $sqlTimestamp, 6, 2 );
		$hour = (int)substr( $sqlTimestamp, 8, 2 );
		$min = (int)substr( $sqlTimestamp, 10, 2 );
		$sec = (int)substr( $sqlTimestamp, 12, 2 );
		$time = mktime( $hour, $min, $sec, $month, $day, $year, 0 - 1 );
		return $time;
	}

	function umakesqldatefromunixdate($unixDate) {
		if ($unixDate == null) {
			return null;
		}

		$year = (int)date( 'Y', $unixDate );
		$month = (int)date( 'm', $unixDate );
		$day = (int)date( 'j', $unixDate );
		$date = sprintf( '%04d-%02d-%02d', $year, $month, $day );
		return $date;
	}

	function umakesqltimestampfromunixdate($unixDate) {
		if ($unixDate == null) {
			return null;
		}

		$year = (int)date( 'Y', $unixDate );
		$month = (int)date( 'm', $unixDate );
		$day = (int)date( 'j', $unixDate );
		$hr = (int)date( 'H', $unixDate );
		$min = (int)date( 'i', $unixDate );
		$sec = (int)date( 's', $unixDate );
		$date = sprintf( '%04d%02d%02d%02d%02d%02d', $year, $month, $day, $hr, $min, $sec );
		return $date;
	}

	function umakesqldatefromsqltimestamp($date) {
		$year = substr( $date, 0, 4 );
		$month = substr( $date, 4, 2 );
		$day = substr( $date, 6, 2 );
		$new = $year . '-' . $month . '-' . $day;
		return $new;
	}

	function _getmonthnumber($month) {
		$months = array( 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
		$mthNum = 0;
		foreach ($months as $mth => $num) {
			if (strcasecmp( $month, $mth ) == 0) {
				$mthNum = $num;
				break;
			}
		}

		return $mthNum;
	}

	function ugetmonthnumber($month) {
		$months = array( 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
		$mthNum = 0;
		foreach ($months as $mth => $num) {
			if (strcasecmp( $month, $mth ) == 0) {
				$mthNum = $num;
				break;
			}
		}

		return $mthNum;
	}

	function ucalculateage($dob) {
		$elems = explode( '/', $dob );

		if (count( $elems ) != 3) {
			return 0 - 1;
		}

		$dobDay = (int)$elems[0];
		$dobMonth = (int)$elems[1];
		$dobYear = (int)$elems[2];
		$nowDay = (int)date( 'j' );
		$nowMonth = (int)date( 'n' );
		$nowYear = (int)date( 'Y' );
		$age = $nowYear - $dobYear;

		if ($nowMonth < $dobMonth) {
			--$age;
		}


		if ($dobMonth == $nowMonth) {
			if ($nowDay < $dobDay) {
				--$age;
			}
		}

		return $age;
	}

	function uiswindows() {
		return false;
	}

	function utempnam($dir, $prefix) {
		$name = $dir . $prefix . date( "YmdHis" );
		return $name;
	}

	function gettexttodisplayfromfile($file) {
		$fp = @fopen( $file, 'r' );

		if ($fp == false) {
			return false;
		}

		$text = fread( $fp, filesize( $file ) );
		fclose( $fp );
		return $text;
	}

	function uxmlentities($text) {
		$text = htmlentities( $text );
		$text = uhtmlentities2unicodeentities( $text );
		return $text;
	}

	function uhtmlentities2unicodeentities($input) {
		$htmlEntities = array_values( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) );
		$entitiesDecoded = array_keys( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) );
		$num = count( $entitiesDecoded );
		$u = 0;

		while ($u < $num) {
			$utf8Entities[$u] = '&#' . ord( $entitiesDecoded[$u] ) . ';';
			++$u;
		}

		return str_replace( $htmlEntities, $utf8Entities, $input );
	}

	function urandomtext($spaces, $size) {
		$letters = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', ' ', ' ', ' ', ' ' );
		$num = 26;

		if ($spaces == true) {
			$num = 30;
		}

		$text = '';
		$i = 0;

		while ($i < $size) {
			$x = rand( 0, $num - 1 );
			$char = $letters[$x];
			$text .= $char;
			++$i;
		}

		return $text;
	}

	function ugetsessionpath($useWebRoot) {
		if ($useWebRoot == true) {
			$dir = $_SERVER['DOCUMENT_ROOT'] . '/sessions/';
		} 
else {
			$dir = $_SERVER['DOCUMENT_ROOT'];
			$last = strrpos( $dir, '/' );
			$dir = substr( $dir, 0, $last ) . '/sessions';
		}

		return $dir;
	}

	function uaddtoarray($a, $i) {
		$a[] = $i;
		end( $a );
		$elem = key( $a );
		return $elem;
	}

	function ufileputcontents($file, $contents) {
		if (function_exists( 'file_put_contents' )) {
			file_put_contents( $file, $contents );
			return null;
		}

		$fh = fopen( $file, 'w' );
		fwrite( $fh, $contents );
		fclose( $fh );
	}

	function ustartourerrorhandling($weDoErrors) {
		define( 'UERROR', E_USER_ERROR );
		define( 'UWARNING', E_USER_WARNING );

		if ($weDoErrors != true) {
			return null;
		}


		if (file_exists( ERROR_LOG_FILE ) == false) {
			touch( ERROR_LOG_FILE );
		}


		if (DISPLAY_ERRORS == true) {
			error_reporting( E_ALL );
			ini_set( 'display_startup_errors', 1 );
			ini_set( 'display_errors', 1 );
		} 
else {
			error_reporting( 0 );
		}

		$old_error_handler = set_error_handler( '_uErrorHandler' );
	}

	function _uerrorhandler($errno, $errmsg, $filename, $linenum, $vars) {
		global $transactionStarted;
		global $errorWithMailing;

		if (!isset( $errorWithMailing )) {
			$errorWithMailing = false;
		}


		if (defined( 'E_STRICT' )) {
			if ($errno == E_STRICT) {
				return null;
			}
		}

		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$errortype = array( E_ERROR => 'Error', E_WARNING => 'Warning', E_PARSE => 'Parsing Error', E_NOTICE => 'Notice', E_CORE_ERROR => 'Core Error', E_CORE_WARNING => 'Core Warning', E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice' );

		if (defined( 'E_STRICT' )) {
			$errortype[E_STRICT] = 'Strict Notice';
		}


		if (isset( $errortype[$errno] )) {
			$errorTypeDesc = $errortype[$errno];
		} 
else {
			$errorTypeDesc = '' . 'unknown type ' . $errno;
		}

		$user_errors = array( E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE );
		$err = '';
		$err .= '"' . $date . '",';
		$err .= '"' . $time . '",';
		$err .= '"' . $errno . '",';
		$err .= '"' . EMAIL_SUBJECT . '",';
		$err .= '"' . $errorTypeDesc . '<",';
		$err .= '"' . $errmsg . '",';
		$err .= '"' . $filename . '",';
		$err .= '"' . $linenum . '"';
		$err .= print_r( $vars, true );
		$err .= '
';

		if (function_exists( 'debug_backtrace' )) {
			$tmp = print_r( debug_backtrace(  ), true );
			$tmp = str_replace( '
', '<br>
', $tmp );
			$err .= $tmp;
		}

		error_log( $err, 3, ERROR_LOG_FILE );

		if (( ( EMAIL_ON_ERROR == true && $errno != E_USER_NOTICE ) && $errorWithMailing == false )) {
			umail( EMAIL_ERRORS_TO, 'admin', EMAIL_SUBJECT, $err, null, '', 'PHP Script', '', false );
		}


		if (( DISPLAY_ERRORS == true && $errno != E_USER_NOTICE )) {
			print '' . '<br>' . $errmsg . ' : file =  ' . $filename . ', line = ' . $linenum . '<br>
';

			if (function_exists( 'debug_backtrace' )) {
				$tmp = print_r( debug_backtrace(  ), true );
				$tmp = str_replace( '
', '<br>
', $tmp );
				print $tmp;
			}
		}


		if (( $transactionStarted == true && $errno == E_USER_ERROR )) {
			$transactionStarted = false;
			udbrollbacktransaction(  );
		}


		if ($errno == E_USER_ERROR) {
			exit( '' . $errmsg . ' : file =  ' . $filename . ', line = ' . $linenum );
		}

	}

	function umail($to, $toName = null, $subject, $messg, $attachment = null, $from = null, $fromName = null, $replyTo = null, $asHTML = true) {
		global $errorWithMailing;

		$errorWithMailing = true;

		if (!defined( 'UTIL_PATH' )) {
			trigger_error( 'no util path', E_USER_ERROR );
		}

		require_once( UTIL_PATH . 'class.phpmailer.php' );
		require_once( UTIL_PATH . 'class.smtp.php' );

		if (defined( 'EMAIL_TYPE' )) {
			$emailType = EMAIL_TYPE;
		} 
else {
			$emailType = 'mail';
		}


		if (( ( $emailType == 'smtp' && !defined( 'EMAIL_SMTP' ) ) && !defined( 'SMTP' ) )) {
			trigger_error( 'No SMTP host', E_USER_ERROR );
		}

		$smtp = '';

		if (defined( 'SMTP' )) {
			$smtp = SMTP;
		}


		if (defined( 'EMAIL_SMTP' )) {
			$smtp = EMAIL_SMTP;
		}


		if (( $from == null && defined( 'EMAIL_FROM' ) )) {
			$from = EMAIL_FROM;
		}


		if (( $replyTo == null && defined( 'EMAIL_REPLY_TO' ) )) {
			$replyTo = EMAIL_REPLY_TO;
		}

		$mail = new PHPMailer(  );
		$mail->SetLanguage( 'en', UTIL_PATH );
		$mail->From = $from;

		if ($fromName != null) {
			$mail->FromName = $fromName;
		}

		$mail->Host = '' . $smtp . ';' . $smtp;
		$mail->Mailer = $emailType;
		$mail->Subject = $subject;

		if ($replyTo != null) {
			$mail->AddReplyTo( $replyTo );
		}

		$mail->Body = $messg;

		if ($asHTML == true) {
			$mail->IsHTML( true );
		}

		$mail->AltBody = '';
		$mail->AddAddress( '' . $to, $toName );

		if ($attachment != null) {
			$mail->AddAttachment( $attachment );
		}


		if (!$mail->Send(  )) {
			trigger_error( '' . 'cant send to ' . $to . ', error ' . $mail->ErrorInfo, E_USER_ERROR );
		}

		$mail->ClearAddresses(  );
		$mail->ClearAttachments(  );
		$errorWithMailing = false;
	}

?>
