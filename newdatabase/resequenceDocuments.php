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

	function setSequence($type, $master, $sequ) {
		if (strlen( $type ) == 0) {
			trigger_error( 'not a suitable type', 256 );
		}


		if (( !is_numeric( $master ) || $master <= 0 )) {
			trigger_error( 'no key value', 256 );
		}

		$q =  . 'SELECT * FROM sequences WHERE sqType=\'' . $type . '\' AND sqMaster=' . $master;
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( 'wrong select' . $q, 256 );
		}

		$num = udbNumberOfRows( $result );

		if ($num == 0) {
			$seq = new Sequence( null );
			$seq->set( 'sqType', $type );
			$seq->set( 'sqMaster', $master );
			$seq->insert( null );
		} 
else {
			$row = udbGetRow( $result );
			$seq = new Sequence( $row );
		}

		$seq->set( 'sqLastUsed', $sequ );
		$seq->update(  );
	}

	require_once( '../include/startup.php' );
	$q = 'SELECT clCode FROM clients ORDER BY clCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udbLastError(  ), 256 );
	}

	$done = 0;

	while ($row = mysql_fetch_array( $result )) {
		$clCode = $row['clCode'];
		$q =  . 'select doCode , doClientSequence  from documents where doClient = ' . $clCode . ' order by doCode';
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udbLastError(  ), 256 );
		}

		$sequ = 0;

		while ($row2 = mysql_fetch_array( $result2 )) {
			$doCode = $row2['doCode'];
			$doc = new Document( $doCode );
			$doc->set( 'doClientSequence', ++$sequ );
			$doc->update(  );
		}

		setSequence( 'CLD', $clCode, $sequ );
	}

	$q = 'SELECT inCode FROM introducers ORDER BY inCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udbLastError(  ), 256 );
	}

	$done = 0;

	while ($row = mysql_fetch_array( $result )) {
		$inCode = $row['inCode'];
		$q =  . 'select doCode , doIntroducerSequence  from documents where doIntroducer = ' . $inCode . ' order by doCode';
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udbLastError(  ), 256 );
		}

		$sequ = 0;

		while ($row2 = mysql_fetch_array( $result2 )) {
			$doCode = $row2['doCode'];
			$doc = new Document( $doCode );
			$doc->set( 'doIntroducerSequence', ++$sequ );
			$doc->update(  );
		}

		setSequence( 'IND', $inCode, $sequ );
	}

	$q = 'SELECT icCode FROM insuranceCompanies ORDER BY icCode';
	$result = mysql_query( $q );

	if ($result == null) {
		trigger_error( udbLastError(  ), 256 );
	}

	$done = 0;

	while ($row = mysql_fetch_array( $result )) {
		$icCode = $row['icCode'];
		$q =  . 'select doCode , doInscoSequence  from documents where doInsco = ' . $icCode . ' order by doCode';
		$result2 = mysql_query( $q );

		if ($result2 == null) {
			trigger_error( udbLastError(  ), 256 );
		}

		$sequ = 0;

		while ($row2 = mysql_fetch_array( $result2 )) {
			$doCode = $row2['doCode'];
			$doc = new Document( $doCode );
			$doc->set( 'doInscoSequence', ++$sequ );
			$doc->update(  );
		}

		setSequence( 'ICD', $inCode, $sequ );
	}

	echo 'All Done';
?>