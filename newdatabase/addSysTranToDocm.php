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

	require_once( '../include/startup.php' );
	$q = 'SELECT doCode from documents WHERE doTrans > 0 AND (doSysTran IS NULL OR doSysTran <= 0)';
	$result = udbquery( $q );

	if ($result == false) {
		trigger_error( udblasterror(  ), E_USER_ERROR );
	}


	while ($row = udbgetrow( $result )) {
		$doCode = $row['doCode'];
		$doc = new Document( $doCode );
		$doDocmType = $doc->get( 'doDocmType' );

		if (( ( ( $doDocmType == KEY_POLICY_DOCM_DEBIT || $doDocmType == KEY_POLICY_DOCM_CREDIT ) || $doDocmType == KEY_POLICY_DOCM_RENEWAL ) || $doDocmType == KEY_POLICY_DOCM_RECEIPT )) {
			$doTrans = $doc->get( 'doTrans' );
			$pt = new PolicyTransaction( null );
			$ok = $pt->tryGettingRecord( $doTrans );

			if ($ok == false) {
				trigger_error( '' . 'cant get ' . $doTrans . ' for doc ' . $doCode, E_USER_ERROR );
			}

			$ptSysTran = $pt->get( 'ptSysTran' );
			$doc->set( 'doSysTran', $ptSysTran );
			$doc->update(  );
		}


		if ($doDocmType == REMITTANCE_ADVICE_DOCM_TYPE) {
			$doTrans = $doc->get( 'doTrans' );
			$doIntroducer = $doc->get( 'doIntroducer' );
			$doInsco = $doc->get( 'doInsco' );
			$doTrans = $doc->get( 'doTrans' );

			if (0 < $doIntroducer) {
				$rt = new IntroducerTransaction( $doTrans );
				$rtSysTran = $rt->get( 'rtSysTran' );
				$doc->set( 'doSysTran', $rtSysTran );
			}


			if (0 < $doInsco) {
				$it = new InsCoTransaction( $doTrans );
				$itSysTran = $it->get( 'itSysTran' );
				$doc->set( 'doSysTran', $itSysTran );
			}

			$doc->update(  );
			continue;
		}
	}

	echo 'Done';
?>