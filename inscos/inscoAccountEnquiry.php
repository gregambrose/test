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

	function _totran($template, $input) {
		global $session;

		if ($template->getAllowExiting(  ) == false) {
			$template->setMessage( 'you must update or cancel before you leave' );
			return false;
		}

		$itCode = $input['tranNo'];

		if ($itCode <= 0) {
			return false;
		}


		if ($itCode <= 0) {
			return false;
		}

		$icCode = $template->get( 'icCode' );
		$ret = '' . '../inscos/inscoAccountEnquiry.php?insco=' . $icCode;
		$session->set( 'returnTo', $ret );
		$it = new InsCoTransaction( $itCode );
		$itTransType = $it->get( 'itTransType' );

		if ($itTransType == 'I') {
			$ptCode = $it->get( 'itPolicyTran' );
			flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		}


		if (( $itTransType == 'C' || $itTransType == 'R' )) {
			flocationheader( '' . '../inscos/inscoRecon.php?view=' . $itCode );
		}


		if ($itTransType == 'J') {
			$itJournal = $it->get( 'itJournal' );
			flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $itJournal );
		}

		return false;
	}

	function _toreconcile($template, $input) {
		global $session;

		$icCode = $template->get( 'icCode' );
		$ret = '../inscos/inscoAccountEnquiry.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../inscos/inscoRecon.php?insco=' . $icCode );
	}

	function _doupdate($template, $input) {
		return false;
	}

	function _showallocations($template, $input) {
		$item = $input['allocationItem'];

		if ($item <= 0) {
			return false;
		}

		$template->setViewAllocation( $item );
		return false;
	}

	require( '../include/startup.php' );
	$inscoAccountEnquiryTemplate = &$session->get( 'inscoAccountEnquiryTemplate' );

	if ($inscoAccountEnquiryTemplate == null) {
		$inscoAccountEnquiryTemplate = new InscoAccountEnquiryTemplate( 'inscoAccountEnquiry.html' );
		$inscoAccountEnquiryTemplate->setProcess( '_doCancel', 'cancel' );
		$inscoAccountEnquiryTemplate->setProcess( '_goBack', 'back' );
		$inscoAccountEnquiryTemplate->setProcess( '_toTran', 'tranNo' );
		$inscoAccountEnquiryTemplate->setProcess( '_showAllocations', 'allocationItem' );
		$inscoAccountEnquiryTemplate->setProcess( '_toReconcile', 'reconcile' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$inscoAccountEnquiryTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['insco'] )) {
		$icCode = $_GET['insco'];
		$inscoAccountEnquiryTemplate->setCanAmend( false );
		$inscoAccountEnquiryTemplate->setInsco( $icCode );
	}


	if (isset( $_GET['inscoAccount'] )) {
		$icCode = $_GET['inscoAccount'];
		$inscoAccountEnquiryTemplate->setCanAmend( true );
		$inscoAccountEnquiryTemplate->setInsco( $icCode );
	}


	if (isset( $_GET['refresh'] )) {
		$icCode = $inscoAccountEnquiryTemplate->get( 'icCode' );
		$inscoAccountEnquiryTemplate->setInsco( $icCode );
	}

	$session->set( 'inscoAccountEnquiryTemplate', $inscoAccountEnquiryTemplate );
	$inscoAccountEnquiryTemplate->process(  );
	$session->set( 'inscoAccountEnquiryTemplate', $inscoAccountEnquiryTemplate );
?>