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

	class introduceredittemplate {
		function introduceredittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'inCode' );
			$this->addField( 'inAccTitle' );
			$this->addField( 'inInvAddress' );
			$this->addField( 'inTerms' );
			$this->addField( 'inApplyToAll' );
			$this->addField( 'inStatementType' );
			$this->addField( 'inDurable' );
			$this->addField( 'inStatus' );
			$this->addField( 'tiName' );
			$this->addField( 'cmName' );
		}

		function setintroducer($inCode) {
			$this->introducer = new Introducer( $inCode );
			$introducer = &$this->introducer;

			$introducer->fetchExtraColumns(  );
			$this->setAll( $introducer->getAllForHTML(  ) );
			$this->set( 'inCode', $inCode );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setandeditintroducer($inCode) {
			$this->setIntroducer( $inCode );
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
		}

		function getintroducer() {
			return $this->introducer;
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


			if ($cashBatchEditTemplate->getItemPayeeType(  ) != 'N') {
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


			if (substr( $journalEditTemplate->getJournalType(  ), 1, 1 ) != 'N') {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function whenlastupdatetoshow($text) {
			$initials = '';
			$when = '';
			$do = false;
			$introducer = &$this->introducer;

			if (isset( $introducer )) {
				$usCode = $introducer->get( 'inLastUpdateBy' );

				if (0 < $usCode) {
					$amendUser = new User( $usCode );
					$initials = $amendUser->getInitials(  );
					$do = true;
				}

				$when = $introducer->get( 'inLastUpdateOn' );
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
			$inAccTitle = $this->get( 'inAccTitle' );

			while ($row = udbgetrow( $result )) {
				$tiCode = $row['tiCode'];
				$tiName = $row['tiName'];

				if ($tiCode == $inAccTitle) {
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

		function showcommunmethod($text) {
			$q = 'SELECT * FROM communMethod ORDER BY cmSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$inDurable = $this->get( 'inDurable' );

			while ($row = udbgetrow( $result )) {
				$cmCode = $row['cmCode'];
				$cmName = $row['cmName'];

				if ($cmCode == $inDurable) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'cmCode', $cmCode );
				$this->set( 'cmName', $cmName );
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

		function showselectedapplytoall($type) {
			$inApplyToAll = $this->get( 'inApplyToAll' );

			if ($type == $inApplyToAll) {
				return 'selected';
			}

			return '';
		}

		function selectstatementtype($type) {
			$inStatementType = $this->get( 'inStatementType' );

			if ($type == $inStatementType) {
				return 'selected';
			}

			return '';
		}

		function selectdurable($type) {
			$inDurable = $this->get( 'inDurable' );

			if ($type == $inDurable) {
				return 'selected';
			}

			return '';
		}

		function selectstatus($type) {
			$inStatus = $this->get( 'inStatus' );

			if ($type == $inStatus) {
				return 'selected';
			}

			return '';
		}

		function termsvalue() {
			$p = $this->get( 'inTerms' );
			$out = '';

			if ($p == 'F') {
				$out = 'Flat Rate';
			}


			if ($p == 'R') {
				$out = '% of Ins. Co. Commission';
			}

			return $out;
		}

		function applytoallvalue() {
			$p = $this->get( 'inApplyToAll' );
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

		function sendstatementvalue() {
			$p = $this->get( 'inStatementType' );
			$out = '';

			if ($p == 0) {
				$out = 'never';
			}


			if ($p == 1) {
				$out = 'if non-zero balance';
			}


			if ($p == 2) {
				$out = 'if transactions in the period';
			}


			if ($p == 3) {
				$out = 'always';
			}

			return $out;
		}

		function statusvalue() {
			$p = $this->get( 'inStatus' );
			$out = '';

			if ($p == 0) {
				$out = 'Inactive';
			}


			if ($p == 1) {
				$out = 'Active';
			}

			return $out;
		}
	}

?>