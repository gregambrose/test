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

	class note {
		var $table = null;
		var $keyField = null;

		function note($code) {
			$this->keyField = 'noCode';
			$this->table = 'notes';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['noClient'] = 'INT';
			$this->fieldTypes['noPolicy'] = 'INT';
			$this->fieldTypes['noInsco'] = 'INT';
			$this->fieldTypes['noIntroducer'] = 'INT';
			$this->fieldTypes['noClientSequence'] = 'INT';
			$this->fieldTypes['noPolicySequence'] = 'INT';
			$this->fieldTypes['noInscoSequence'] = 'INT';
			$this->fieldTypes['noIntroducerSequence'] = 'INT';
			$this->fieldTypes['noType'] = 'INT';
			$this->fieldTypes['noEnteredBy'] = 'INT';
			$this->fieldTypes['noOriginator'] = 'INT';
			$this->fieldTypes['noNextActionBy'] = 'INT';
			$this->fieldTypes['noLocked'] = 'BOOL';
			$this->handleConcurrency( true );
			$q = 'SELECT ntName, usFirstName as nextByFirst, usLastName as nextByLast  
			FROM notes
				LEFT JOIN noteTypes on noType = ntCode
				LEFT JOIN users on noNextActionBy = usCode
				where noCode = CODE';
			$this->setExtraSql( $q );
		}

		function update() {
			$this->set( 'noUpdateorCreate', ugettimenow(  ) );
			$ok = urecord::update(  );
			return $ok;
		}

		function setclientsequence() {
			$clCode = $this->get( 'noClient' );

			if ($clCode <= 0) {
				trigger_error( 'cant create sequence without client', E_USER_ERROR );
			}

			$noClientSequence = fsetsequence( 'CLN', $clCode );
			$this->set( 'noClientSequence', $noClientSequence );
		}

		function setpolicysequence() {
			$plCode = $this->get( 'noPolicy' );

			if ($plCode <= 0) {
				trigger_error( 'cant create sequence without policy', E_USER_ERROR );
			}

			$noPolicySequence = fsetsequence( 'PLN', $plCode );
			$this->set( 'noPolicySequence', $noPolicySequence );
		}

		function setinscosequence() {
			$icCode = $this->get( 'noInsco' );

			if ($icCode <= 0) {
				trigger_error( 'cant create sequence without ins co', E_USER_ERROR );
			}

			$noInscoSequence = fsetsequence( 'ICN', $icCode );
			$this->set( 'noInscoSequence', $noInscoSequence );
		}

		function setintroducersequence() {
			$inCode = $this->get( 'noIntroducer' );

			if ($inCode <= 0) {
				trigger_error( 'cant create sequence without introducer', E_USER_ERROR );
			}

			$noIntroducerSequence = fsetsequence( 'INN', $inCode );
			$this->set( 'noIntroducerSequence', $noIntroducerSequence );
		}
	}

?>