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

	class unpostedtemplate {
		function unpostedtemplate($html) {
			ftemplate::ftemplate( $html );
			$this->setHeader( SITE_NAME );
		}

		function listunposted($text) {
			$q = 'SELECT ptCode
			FROM policyTransactions WHERE  ptPostStatus = \'S\'
			ORDER BY ptCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ptCode = $row['ptCode'];
				$trans = new PolicyTransaction( $ptCode );
				$ptTransDesc = $trans->getForHTML( 'ptTransDesc' );
				$ptLastUpdateBy = $trans->get( 'ptLastUpdateBy' );
				$ptLastUpdateOn = $trans->get( 'ptLastUpdateOn' );
				$amount = uformatmoneywithcommas( $trans->get( 'ptClientTotal' ) );
				$usCode = $ptLastUpdateBy;
				$ptLastUpdateOn = uformatourtimestamp( $ptLastUpdateOn );
				$plCode = $trans->get( 'ptPolicy' );
				$policy = new Policy( $plCode );
				$plPolicyNumber = $policy->get( 'plPolicyNumber' );
				$clCode = $policy->get( 'plClient' );
				$client = new Client( $clCode );
				$clName = $client->get( 'clName' );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$typeName = $trans->getTypeDescription(  );
				$this->set( 'typeName', $typeName );
				$this->set( 'ptCode', $ptCode );
				$this->set( 'ptTransDesc', trim( $ptTransDesc ) );
				$this->set( 'usInitials', $usInitials );
				$this->set( 'ptLastUpdateOn', $ptLastUpdateOn );
				$this->set( 'plCode', $plCode );
				$this->set( 'plPolicyNumber', $plPolicyNumber );
				$this->set( 'clName', $clName );
				$this->set( 'amount', $amount );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>