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

	class cashbatchestemplate {
		function cashbatchestemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'includeEmpty' );
			$this->setFieldType( 'includeEmpty', 'checked' );
		}

		function showcashbatches($text) {
			$includeEmpty = $this->get( 'includeEmpty' );
			$q = 'SELECT * FROM cashBatches ';

			if ($includeEmpty != 1) {
				$q .= 'WHERE (btEntered != 0 OR btTotal != 0) OR btLocked = 1';
			}

			$q .= ' ORDER by btLocked, btCode DESC';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$batch = new CashBatch( $row );
				$this->set( 'btCode', $batch->get( 'btCode' ) );
				$this->set( 'btPayInSlip', $batch->getForHTML( 'btPayInSlip' ) );
				$this->set( 'btTotal', $batch->getForHTML( 'btTotal' ) );
				$this->set( 'btAllocated', $batch->getForHTML( 'btAllocated' ) );
				$this->set( 'btUnallocated', $batch->getForHTML( 'btUnallocated' ) );
				$this->set( 'btBatchDate', uformatsqldate2( $batch->get( 'btBatchDate' ) ) );
				$this->set( 'whenSaved', uformatourtimestamp2( $batch->get( 'btLastUpdateOn' ) ) );
				$usCode = $batch->getForHTML( 'btLastUpdateBy' );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'whoSaved', $usInitials );
				$this->set( 'whenPosted', uformatourtimestamp2( $batch->getForHTML( 'btWhenPosted' ) ) );
				$usCode = $batch->getForHTML( 'btWhoPosted' );

				if (0 < $usCode) {
					$user = new User( $usCode );
					$usInitials = $user->get( 'usInitials' );
				} 
else {
					$usInitials = '';
				}

				$this->set( 'whoPosted', $usInitials );
				$posted = $batch->get( 'btLocked' );

				if ($posted == 1) {
					$colour = '#EBFFEA';
				} 
else {
					$colour = '#FAE2BB';
				}

				$this->set( 'colour', $colour );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>