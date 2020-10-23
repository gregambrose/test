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

	function _goback($template, $input) {
		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$url = $template->popReturnTo(  );
		flocationheader( $url );
	}

	function _drilldown($template, $input) {
		global $session;
		global $tempTable;

		$tmCode = $input['goTo'];

		if ($tmCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$tempTable = $template->getTempTable(  );
		$ret = '../clients/clientHistory.php';
		$session->set( 'returnTo', $ret );
		$q = '' . 'SELECT * FROM ' . $tempTable . ' WHERE tmCode = ' . $tmCode;
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ) . $q, E_USER_ERROR );
		}

		$row = udbgetrow( $result );

		if ($row == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$tmType = $row['tmType'];
		$tmItemCode = $row['tmItemCode'];
		$tmTransType = $row['tmTransType'];

		if ($tmType == 'A') {
			$st = new SystemTransaction( $tmItemCode );
			$tnTran = $st->get( 'tnTran' );
			$sysType = $st->get( 'tnType' );

			if ($sysType == 'PT') {
				flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $tnTran );
				exit(  );
			}


			if ($sysType == 'CB') {
				flocationheader( '' . '../batches/cashBatchEdit.php?batch=' . $tnTran );
				exit(  );
			}


			if ($sysType == 'CJ') {
				$q = '' . 'SELECT jnCode FROM journals WHERE jnSysTran =' . $tmItemCode;
				$result = udbquery( $q );

				if ($result != false) {
					$row = udbgetrow( $result );

					if (isset( $row['jnCode'] )) {
						$jnCode = $row['jnCode'];
						flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $jnCode );
						exit(  );
					}
				}
			}
		} 
else {
			if ($tmType == 'D') {
				flocationheader( '' . '../clients/clientDocms.php?clientDocument=' . $tmItemCode );
				exit(  );
			} 
else {
				if ($tmType == 'N') {
					flocationheader( '' . '../clients/clientNotes.php?clientNote=' . $tmItemCode );
					exit(  );
				}
			}
		}

		return false;
	}

	function _topolicytran($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ptCode = $input['tranNo'];

		if ($ptCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '' . '../clients/clientHistory.php?client=' . $clCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		flocationheader( $url );
	}

	function _toclienttran($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$ctCode = $input['clientTran'];

		if ($ctCode <= 0) {
			return false;
		}

		$clCode = $template->get( 'clCode' );
		$ret = '' . '../clients/clientHistory.php?client=' . $clCode;
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../clients/cashReceiptsEdit.php?view=' . $ctCode );
		flocationheader( $url );
	}

	function handlecashpaid($template, $input) {
		$ctCode = 0;
		reset( $input );

		if ($elem = each( $input )) {
			$key = $elem['key'];
			$value = $elem['value'];

			if (strcmp( 'update-', substr( $key, 0, 7 ) ) != 0) {
				continue;
			}

			$ctCode = substr( $key, 7 );
			break;
		}


		if ($ctCode == 0) {
			return false;
		}

		$amt = $input['' . 'toPay-' . $ctCode];
		$amt = uconvertmoneytointeger( $amt );
		$date = $input['' . 'payDate-' . $ctCode];
		$date = umakesqldate2( $date );

		if ($amt == 0) {
			$template->setMessage( 'no amount has been entered' );
			return false;
		}


		if ($date == null) {
			$template->setMessage( 'no date has been entered' );
			return false;
		}

		$ct = new ClientTransaction( $ctCode );
		$ctOriginal = $ct->get( 'ctOriginal' );
		$ctPaid = $ct->get( 'ctPaid' );
		$ctBalance = $ct->get( 'ctBalance' );
		$ctPaid += $amt;
		$ct->set( 'ctPaid', $ctPaid );
		$ct->recalcTotals(  );
		$ct->set( 'ctPaidDate', $date );
		$ct->update(  );
		$template->setMessage( 'paid updated' );
		return false;
	}

	require( '../include/startup.php' );
	$clientHistoryTemplate = &$session->get( 'clientHistoryTemplate' );

	if ($clientHistoryTemplate == null) {
		$clientHistoryTemplate = new ClientHistoryTemplate( 'clientHistory.html' );
		$clientHistoryTemplate->setProcess( '_goBack', 'back' );
		$clientHistoryTemplate->setProcess( '_toPolicyTran', 'tranNo' );
		$clientHistoryTemplate->setProcess( '_toClientTran', 'clientTran' );
		$clientHistoryTemplate->setProcess( '_drillDown', 'goTo' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$clientHistoryTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['client'] )) {
		$clCode = $_GET['client'];
		$clientHistoryTemplate->setCanAmend( false );
		$clientHistoryTemplate->setClient( $clCode );
	}


	if (isset( $_GET['clientAccount'] )) {
		$clCode = $_GET['clientAccount'];
		$clientHistoryTemplate->setCanAmend( true );
		$clientHistoryTemplate->setClient( $clCode );
	}


	if (isset( $_GET['refresh'] )) {
		$clCode = $clientHistoryTemplate->get( 'clCode' );
		$clientHistoryTemplate->setClient( $clCode );
	}

	$session->set( 'clientHistoryTemplate', $clientHistoryTemplate );
	$clientHistoryTemplate->process(  );
	$session->set( 'clientHistoryTemplate', $clientHistoryTemplate );
?>