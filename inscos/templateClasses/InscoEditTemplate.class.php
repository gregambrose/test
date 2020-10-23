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

	class inscoedittemplate {
		function inscoedittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'icCode' );
			$this->addField( 'icAccTitle' );
			$this->addField( 'icInvAddress' );
			$this->addField( 'icDelegated' );
			$this->addField( 'icIPTAmendable' );
			$this->addField( 'icTerms' );
			$this->addField( 'icType' );
			$this->addField( 'icApplyToAll' );
			$this->addField( 'icAddonCOB' );
			$this->addField( 'iyName' );
		}

		function setinsco($icCode) {
			$this->insco = new Insco( $icCode );
			$insco = &$this->insco;

			$this->set( 'icCode', $icCode );
			$insco->fetchExtraColumns(  );
			$this->setAll( $insco->getAllForHTML(  ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandeditinsco($icCode) {
			$this->setInsco( $icCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getinsco() {
			return $this->insco;
		}

		function whencashbatchexists($text) {
			global $session;

			$out = '';
			$cashBatchEditTemplate = &$session->get( 'cashBatchEditTemplate' );

			if ($cashBatchEditTemplate == null) {
				return '';
			}


			if ($cashBatchEditTemplate->getAllowEditing(  ) == false) {
				return '';
			}


			if ($cashBatchEditTemplate->getItemPayeeType(  ) != 'I') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenjournalexists($text) {
			global $session;

			$out = '';
			$journalEditTemplate = &$session->get( 'journalEditTemplate' );

			if ($journalEditTemplate == null) {
				return '';
			}


			if (!is_a( $journalEditTemplate, 'JournalEditTemplate' )) {
				trigger_error( 'not a journal template', E_USER_ERROR );
			}


			if ($journalEditTemplate->getAllowEditing(  ) == false) {
				return '';
			}


			if (substr( $journalEditTemplate->getJournalType(  ), 1, 1 ) != 'I') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$insco = &$this->insco;

			if (isset( $insco )) {
				$usCode = $insco->get( 'icLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $insco->get( 'icLastUpdateOn' );
				$when = uformatourtimestamp( $when );
			}


			if ($do == false) {
				return '';
			}

			$this->set( 'lastUpdateBy', $initials );
			$this->set( 'lastUpdatedOn', $when );
			$out = $this->parse( $text );
			return $out;
		}

		function showsourceofbusiness($text) {
			$q = 'SELECT * FROM sourceOfBus ORDER BY sbSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clSourceOfBus = $this->get( 'clSourceOfBus' );

			while ($row = udbgetrow( $result )) {
				$sbCode = $row['sbCode'];
				$sbName = $row['sbName'];

				if ($sbCode == $clSourceOfBus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'sbCode', $sbCode );
				$this->set( 'sbName', $sbName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtitles($text) {
			$q = 'SELECT * FROM titles ORDER BY tiSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$icAccTitle = $this->get( 'icAccTitle' );

			while ($row = udbgetrow( $result )) {
				$tiCode = $row['tiCode'];
				$tiName = $row['tiName'];

				if ($tiCode == $icAccTitle) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'tiCode', $tiCode );
				$this->set( 'tiName', $tiName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtypes($text) {
			$q = 'SELECT * FROM insCoTypes ORDER BY iySequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$icType = $this->get( 'icType' );

			while ($row = udbgetrow( $result )) {
				$iyCode = $row['iyCode'];
				$iyName = $row['iyName'];

				if ($iyCode == $icType) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'iyCode', $iyCode );
				$this->set( 'iyName', $iyName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showclassofbusiness($text) {
			$q = 'SELECT * FROM classOfBus ORDER BY cbName';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$icAddonCOB = $this->get( 'icAddonCOB' );

			while ($row = udbgetrow( $result )) {
				$cbCode = $row['cbCode'];
				$cbName = $row['cbName'];

				if ($cbCode == $icAddonCOB) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'cbCode', $cbCode );
				$this->set( 'cbName', $cbName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showtermsselected($type) {
			$inTerms = $this->get( 'inTerms' );

			if ($type == $inTerms) {
				return 'selected';
			}

			return '';
		}

		function selectinvaddress($type) {
			$icInvAddress = $this->get( 'icInvAddress' );

			if ($type == $icInvAddress) {
				return 'selected';
			}

			return '';
		}

		function selectdelegated($type) {
			$icDelegated = $this->get( 'icDelegated' );

			if ($type == $icDelegated) {
				return 'selected';
			}

			return '';
		}

		function selectiptamendable($type) {
			$icIPTAmendable = $this->get( 'icIPTAmendable' );

			if ($type == $icIPTAmendable) {
				return 'selected';
			}

			return '';
		}

		function invaddressvalue() {
			$p = $this->get( 'icInvAddress' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function delegatedvalue() {
			$p = $this->get( 'icDelegated' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}

		function amendablevalue() {
			$p = $this->get( 'icIPTAmendable' );
			$out = '';

			if ($p == 0) {
				$out = 'Y or N';
			}


			if ($p == 1) {
				$out = 'YES';
			}


			if ($p == 0 - 1) {
				$out = 'NO';
			}

			return $out;
		}
	}

?>