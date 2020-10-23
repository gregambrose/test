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

	function _selectinsco($template, $input) {
		global $session;

		$icCode = $input['selectInsco'];

		if ($icCode < 1) {
			return false;
		}

		$insco = new Insco( null );
		$found = $insco->tryGettingRecord( $icCode );

		if ($found == false) {
			$template->setMessage( 'Sorry...this insurance company has been deleted' );
			return false;
		}

		$ret = 'inscos.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'inscoEdit.php?amendInsco=' . $icCode );
	}

	function _newcompany($template, $input) {
		global $session;

		$q = 'INSERT  INTO insuranceCompanies () VALUES()';
		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$icCode = udbgetinsertid(  );
		$ret = 'inscos.php';
		$session->set( 'returnTo', $ret );
		flocationheader( '' . 'inscoEdit.php?editInsco=' . $icCode );
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
		$inscoCode = udbmakefieldsafe( trim( $template->get( 'inscoCode' ) ) );

		if (( 0 < strlen( $inscoCode ) && is_numeric( $inscoCode ) == false )) {
			$template->setMessage( 'insurance company code needs to be numeric' );
			return false;
		}

		$q = 'SELECT icCode FROM insuranceCompanies ';
		$someDone = false;

		if (0 < strlen( $searchText )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . '(icName 		  LIKE \'%' . $searchText . '%\' OR
				   icAddress   	  LIKE \'%' . $searchText . '%\' OR
				   icContact      LIKE \'%' . $searchText . '%\' OR
				   icPostcode     LIKE \'%' . $searchText . '%\')
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

			$q .= '' . 'icName        LIKE \'' . $alphas . '%\'';
			$someDone = true;
		}


		if (0 < strlen( $inscoCode )) {
			if ($someDone == true) {
				$q .= 'AND ';
			} 
else {
				$q .= 'WHERE ';
			}

			$q .= '' . 'icCode=' . $inscoCode;
			$someDone = true;
		}


		if ($sortType == 'name') {
			$q .= ' ORDER BY  icName, icCode';
		} 
else {
			if ($sortType == 'code') {
				$q .= ' ORDER BY  icCode';
			} 
else {
				$q .= ' ORDER BY  icName';
			}
		}

		$result = udbquery( $q );

		if ($result == false) {
			trigger_error( udblasterror(  ), E_USER_ERROR );
		}

		$inscos = array(  );

		while ($row = udbgetrow( $result )) {
			$inscos[] = $row['icCode'];
		}

		$template->inscos = $inscos;

		if (count( $inscos ) == 0) {
			$template->setMessage( 'no insurance companies found' );
		}

		return false;
	}

	require( '../include/startup.php' );
	$inscosTemplate = &$session->get( 'inscosTemplate' );

	if ($inscosTemplate == null) {
		$inscosTemplate = new InscosTemplate( 'inscos.html' );
		$inscosTemplate->setProcess( '_doSearch', 'doSearch' );
		$inscosTemplate->setProcess( '_doSort', 'sort' );
		$inscosTemplate->setProcess( '_newCompany', 'newCompany' );
		$inscosTemplate->setProcess( '_goToMenu', 'home' );
		$inscosTemplate->setProcess( '_selectInsco', 'selectInsco' );
	}

	$session->set( 'inscosTemplate', $inscosTemplate );
	$inscosTemplate->process(  );
	$session->set( 'inscosTemplate', $inscosTemplate );
?>