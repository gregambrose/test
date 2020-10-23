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

	function _totransaction($template, $input) {
		global $session;

		$rtCode = $input['tranNo'];

		if ($rtCode <= 0) {
			return false;
		}

		$rt = new IntroducerTransaction( $rtCode );
		$rtTransType = $rt->get( 'rtTransType' );
		$inCode = $template->get( 'inCode' );
		$ret = '' . '../introducers/introducerAccountEnquiry.php?introducer=' . $inCode;
		$session->set( 'returnTo', $ret );

		if ($rtTransType == 'I') {
			$ptCode = $rt->get( 'rtPolicyTran' );

			if ($ptCode <= 0) {
				return false;
			}

			flocationheader( '' . '../policies/policyTransEdit.php?transToView=' . $ptCode );
		}


		if (( $rtTransType == 'C' || $rtTransType == 'R' )) {
			flocationheader( '' . '../introducers/introducerRecon.php?view=' . $rtCode );
		}


		if ($rtTransType == 'J') {
			$rtJournal = $rt->get( 'rtJournal' );
			flocationheader( '' . '../accounts/journalEdit.php?viewJournal=' . $rtJournal );
		}

		exit(  );
	}

	function _toreconcile($template, $input) {
		global $session;

		$inCode = $template->get( 'inCode' );
		$ret = 'introducerAccountEnquiry.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . '../introducers/introducerRecon.php?introd=' . $inCode );
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
	$introducerAccountEnquiryTemplate = &$session->get( 'introducerAccountEnquiryTemplate' );

	if ($introducerAccountEnquiryTemplate == null) {
		$introducerAccountEnquiryTemplate = new IntroducerAccountEnquiryTemplate( 'introducerAccountEnquiry.html' );
		$introducerAccountEnquiryTemplate->setProcess( '_doCancel', 'cancel' );
		$introducerAccountEnquiryTemplate->setProcess( '_goBack', 'back' );
		$introducerAccountEnquiryTemplate->setProcess( '_toReconcile', 'reconcile' );
		$introducerAccountEnquiryTemplate->setProcess( '_showAllocations', 'allocationItem' );
		$introducerAccountEnquiryTemplate->setProcess( '_toTransaction', 'tranNo' );
	}


	if (( isset( $returnTo ) && $returnTo != null )) {
		$introducerAccountEnquiryTemplate->setReturnTo( $returnTo );
	}


	if (isset( $_GET['introducer'] )) {
		$inCode = $_GET['introducer'];
		$introducerAccountEnquiryTemplate->setIntroducer( $inCode );
	}


	if (isset( $_GET['refresh'] )) {
		$icCode = $introducerAccountEnquiryTemplate->get( 'inCode' );
		$introducerAccountEnquiryTemplate->setIntroducer( $inCode );
	}

	$session->set( 'introducerAccountEnquiryTemplate', $introducerAccountEnquiryTemplate );
	$introducerAccountEnquiryTemplate->process(  );
	$session->set( 'introducerAccountEnquiryTemplate', $introducerAccountEnquiryTemplate );
?>