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
	 * @return 	message if no, null if ok
	 *
	 */
	function _checkOK($ignoreUnalloc) {
		global $sessionName;
		global $periodTo;
		global $caTotals;

		$url = SITE_ROOT_INTERNAL_URL . 'accounts/controlAccount.php?getTotals';
		$x = file_get_contents( $url );

		if ($x == false) {
			trigger_error( 'cant get results from control account totals' );
		}

		$caTotals = explode( ',', $x );

		if ($caTotals[4] != 0) {
			return 'control account control total is non-zero';
		}

		$q = (  . 'SELECT SUM(ctBalance) as total FROM clientTransactions 
			WHERE ctPostingDate <= \'' . $periodTo . '\'' );
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}

		$row = udbGetRow( $result );
		$clTotal = $row['total'];

		if ($clTotal == null) {
			$clTotal = 0;
		}


		if ($clTotal != $caTotals[0]) {
			return 'control account client total doesnt match the client total debt (' . uFormatMoneyWithCommas( $clTotal ) . ' and ' . uFormatMoneyWithCommas( $caTotals[0] . ')' );
		}

		$q = (  . 'SELECT SUM(itBalance) as total FROM inscoTransactions WHERE itPostingDate <= \'' . $periodTo . '\'' );
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}

		$row = udbGetRow( $result );
		$icTotal = $row['total'];

		if ($icTotal == null) {
			$icTotal = 0;
		}


		if ($icTotal != 0 - $caTotals[1]) {
			$x = 0 - $caTotals[1];
			return  . 'control account ins. co. total doesnt match the ins. co. total credit icTotal = ' . $icTotal . ', c/s ' . $x;
		}

		$q = (  . 'SELECT SUM(rtBalance) as total FROM introducerTransactions WHERE rtPostingDate <= \'' . $periodTo . '\'' );
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}

		$row = udbGetRow( $result );
		$inTotal = $row['total'];

		if ($inTotal == null) {
			$inTotal = 0;
		}


		if ($inTotal != 0 - $caTotals[2]) {
			return 'control account introducer total doesnt match the introducer total credit';
		}

		$q = 'SELECT SUM(baAmount) as total FROM bankAccountTrans';
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}

		$row = udbGetRow( $result );
		$ibaTotal = $row['total'];

		if ($ibaTotal == null) {
			$ibaTotal = 0;
		}


		if ($ibaTotal != 0 - $caTotals[3]) {
			return 'control account IBA total doesnt match the Bank Statement total';
		}


		if ($ignoreUnalloc == false) {
			$q = 'SELECT COUNT(itCode) as total FROM inscoTransactions
					  WHERE (itTransType = \'C\' || itTransType = \'R\')
					  AND	itBalance != 0';
			$result = udbQuery( $q );

			if ($result == false) {
				trigger_error( udbLastError(  ), 256 );
			}

			$row = udbGetRow( $result );
			$total = $row['total'];

			if ($total == null) {
				$total = 0;
			}


			if (0 < $total) {
				return 'one or more insurance company payments or receipts have not been fully allocated';
			}

			$q = 'SELECT COUNT(rtCode) as total FROM introducerTransactions
					  WHERE (rtTransType = \'C\' || rtTransType = \'R\')
					  AND	rtBalance != 0';
			$result = udbQuery( $q );

			if ($result == false) {
				trigger_error( udbLastError(  ), 256 );
			}

			$row = udbGetRow( $result );
			$total = $row['total'];

			if ($total == null) {
				$total = 0;
			}


			if (0 < $total) {
				return 'one or more introducer payments or receipts have not been fully allocated';
			}
		}

		$q = 'SELECT SUM(baAmount) as total FROM bankAccountTrans';
		$result = udbQuery( $q );

		if ($result == false) {
			trigger_error( udbLastError(  ), 256 );
		}

		$row = udbGetRow( $result );
		$total = $row['total'];

		if ($total == null) {
			$total = 0;
		}

		$caTotals['iba'] = $total;
	}

	require( '../include/startup.php' );

	if (isset( $_GET['ignoreUnalloc'] )) {
		$ignoreUnalloc = true;
	} 
else {
		$ignoreUnalloc = false;
	}

	$messg = _checkOK( $ignoreUnalloc );

	if ($messg == null) {
		print 'OK';
		exit(  );
	}


	if (defined( 'INTEGRITY_EMAIL' )) {
		$to = INTEGRITY_EMAIL;
	} 
else {
		$to = ADMIN_EMAIL;
	}

	$subject = 'Accounting Error ' . SITE_NAME;
	$text = $messg;
	$sendTo = explode( ';', $to );

	if (defined( 'FROM_EMAIL_ADDRESS' )) {
		$fromEmail = FROM_EMAIL_ADDRESS;
	} 
else {
		$fromEmail = null;
	}


	if (defined( 'FROM_EMAIL_NAME' )) {
		$fromEmailName = FROM_EMAIL_NAME;
	} 
else {
		$fromEmailName = null;
	}

	foreach ($sendTo as $value) {
		$key = ;
		uMail( $value, '', $subject, $text, null, $fromEmail, $fromEmailName );
	}

	print 'NO ' . $text . 'sent to ' . $to;
	exit(  );
?>