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

	function _selectintroducer($template, $input) {
		global $session;

		$inCode = $input['selectIntroducer'];

		if ($inCode < 1) {
			return false;
		}

		$introducer = new Introducer( null );
		$found = $introducer->tryGettingRecord( $inCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this introducer has been deleted' );
			return false;
		}

		$ret = 'introducers.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerEdit.php?amendIntroducer=' . $inCode );
	}

	function _newintroducer($template, $input) {
		global $session;

		$q = 'INSERT  INTO introducers () VALUES()';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$inCode = udbgetinsertid(  );
		$ret = 'introducers.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'introducerEdit.php?editIntroducer=' . $inCode );
		exit(  );
	}

	function _dosort($template, $input) {
		$type = $input['sort'];
		$template->setSortType( $type );
		_dosearch( $template, $input );
	}

	function _dosearch($template, $input) {
		global $session;

		$template->page = 0;
		$sortType = $template->getSortType(  );
		$alphas = udbmakefieldsafe( $template->get( 'alphas' ) );
		$searchText = udbmakefieldsafe( trim( $template->get( 'searchText' ) ) );
		$introducerCode = udbmakefieldsafe( trim( $template->get( 'introducerCode' ) ) );

		if (( 0 < strlen( $introducerCode ) && is_numeric( $introducerCode ) == false )) {
			$template->setMessage( 'introducer code needs to be numeric' );
			return false;
		}

		$q = 'SELECT inCode FROM introducers ';
		$someDone = false;

		if (0 < strlen( $searchText )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . '(inName 		  	LIKE \'%' . $searchText . '%\' OR
				   inAddress    	LIKE \'%' . $searchText . '%\' OR
				   inContact     	LIKE \'%' . $searchText . '%\' OR
				   inPostcode     	LIKE \'%' . $searchText . '%\')
			   ';
			$someDone = true;
		}


		if (strlen( $alphas ) == 1) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'inName        LIKE \'' . $alphas . '%\'';
			$someDone = true;
		}


		if (0 < strlen( $introducerCode )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'inCode=' . $introducerCode;
			$someDone = true;
		}


		if ($sortType == 'name') {
			$q .= ' ORDER BY  inName, inCode';
		} 
else {
			if ($sortType == 'code') {
				$q .= ' ORDER BY  inCode';
			} 
else {
				$q .= ' ORDER BY  inName, inCode';
			}
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$introducers = array(  );

		while ($row = udbgetrow( $result )) {
			$introducers[] = $row['inCode'];
		}

		$template->introducers = $introducers;

		if (count( $introducers ) == 0) {
			$template->setMessage( 'no introducers found' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$introducersTemplate = &$session->get( 'introducersTemplate' );

	if ($introducersTemplate == null) {
		$introducersTemplate = new IntroducersTemplate( 'introducers.html' );
		$introducersTemplate->setProcess( '_doSearch', 'doSearch' );
		$introducersTemplate->setProcess( '_doSort', 'sort' );
		$introducersTemplate->setProcess( '_newIntroducer', 'newIntroducer' );
		$introducersTemplate->setProcess( '_selectIntroducer', 'selectIntroducer' );
		$introducersTemplate->setReturnTo( '../introducers/introducers.php' );
	}

	$session->set( 'introducersTemplate', $introducersTemplate );
	$introducersTemplate->process(  );
	$session->set( 'introducersTemplate', $introducersTemplate );
?>