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

	class clientageddebttemplate {
		var $policies = null;
		var $page = null;
		var $sortType = null;
		var $doNormalItem = null;
		var $doClientTotal = null;
		var $doMonthTotal = null;
		var $doGrandTotal = null;
		var $subTotal = null;
		var $grandTotal = null;

		function clientageddebttemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clientType' );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'clientCode' );
			$this->addField( 'clientsFound' );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedPeriodCode' );
			$this->addField( 'selectedYear' );
			$this->setHeader( SITE_NAME );
			$this->clients = array(  );
			$this->sortType = '';
		}

		function setsorttype($type) {
			$this->sortType = $type;
		}

		function getsorttype() {
			return $this->sortType;
		}

		function setheaderfields() {
			$clType = $this->get( 'clientType' );

			if (0 < $clType) {
				$type = new ClientType( $clType );
				$cyName = $type->get( 'cyName' );
			} 
else {
				$cyName = 'All';
			}

			$this->set( 'clType', $cyName );
			$type = $this->get( 'alphas' );
			$this->set( 'alpha', $type );
			$type = $this->get( 'searchText' );
			$this->set( 'freeText', $type );
			$clCode = $this->get( 'clientCode' );

			if (0 < $clCode) {
				$cl = new Client( $clCode );
				$name = $cl->get( 'clName' );
			} 
else {
				$name = '';
			}

			$this->set( 'client', $name );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
		}

		function listperiods($text) {
			$selectedPeriod = $this->get( 'selectedPeriod' );
			$out = '';
			$p = 1;

			while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
				$this->set( 'period', $p );

				if ($selectedPeriod == $p) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
				++$p;
			}

			return $out;
		}

		function listyears($text) {
			$selectedYear = $this->get( 'selectedYear' );
			$q = 'SELECT ayCode FROM accountingYears ORDER BY ayYear';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ayCode = $row['ayCode'];
				$ay = new AccountingYear( $ayCode );
				$this->set( 'code', $ayCode );
				$desc = $ay->getForHTML( 'ayYear' );

				if (strlen( trim( $desc ) ) == 0) {
					$desc = 'blank';
				}

				$this->set( 'year', $ay->getForHTML( 'ayYear' ) );

				if ($selectedYear == $ayCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function whenreporttoview($text) {
			if ($this->clients == null) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listclients($text) {
			$numOfClients = count( $this->clients );
			$this->grand = array(  );
			$slot = 0;

			while ($slot < 5) {
				$this->grand[$slot] = 0;
				++$slot;
			}

			reset( $this->clients );
			$out = '';
			foreach ($this->clients as $clCode) {
				$client = new Client( null );
				$found = $client->tryGettingRecord( $clCode );

				if ($found == false) {
					continue;
				}

				$clCode = $client->getKeyValue(  );
				$this->set( 'clCode', $clCode );
				$clName = $client->getDisplayName(  );
				$debt = $client->getAgedDebt(  );
				$current = $debt[0];
				$month1 = $debt[1];
				$month2 = $debt[2];
				$month3 = $debt[3];
				$total = $debt[4];
				$slot = 0;

				while ($slot < 5) {
					$this->grand[$slot] += $debt[$slot];
					++$slot;
				}

				$current = uformatmoney( $debt[0] );
				$month1 = uformatmoney( $debt[1] );
				$month2 = uformatmoney( $debt[2] );
				$month3 = uformatmoney( $debt[3] );
				$total = uformatmoney( $debt[4] );
				$this->set( 'current', uformatmoney( $current ) );
				$this->set( 'month1', uformatmoney( $month1 ) );
				$this->set( 'month2', uformatmoney( $month2 ) );
				$this->set( 'month3', uformatmoney( $month3 ) );
				$this->set( 'total', uformatmoney( $total ) );
				$this->set( 'clCode', $client->get( 'clCode' ) );
				$this->set( 'clName', $clName );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function grandtotal($text) {
			$this->set( 'grandCurrent', uformatmoney( $this->grand[0] ) );
			$this->set( 'grandMonth1', uformatmoney( $this->grand[1] ) );
			$this->set( 'grandMonth2', uformatmoney( $this->grand[2] ) );
			$this->set( 'grandMonth3', uformatmoney( $this->grand[3] ) );
			$this->set( 'grandTotal', uformatmoney( $this->grand[4] ) );
			$out = $this->parse( $text );
			return $out;
		}

		function listclienttypes($text) {
			$q = 'SELECT * FROM clientTypes ORDER BY cySequence';
			$result = udbquery( $q );
			$type = $this->get( 'clientType' );
			$out = '';

			while ($row = udbgetrow( $result )) {
				$this->set( 'cyCode', $row['cyCode'] );
				$this->set( 'cyName', $row['cyName'] );

				if ($type == $row['cyCode']) {
					$whenSelected = 'selected';
				} 
else {
					$whenSelected = '';
				}

				$this->set( 'whenSelected', $whenSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listalphas($text) {
			$out = '';
			$current = $this->get( 'alphas' );
			$ordA = ord( 'A' );
			$a = 0;

			while ($a < 26) {
				$i = $a + $ordA;
				$char = chr( $i );
				$this->set( 'char', $char );

				if ($char == $current) {
					$whenSelected = 'selected';
				} 
else {
					$whenSelected = '';
				}

				$this->set( 'whenSelected', $whenSelected );
				$out .= $this->parse( $text );
				++$a;
			}

			return $out;
		}
	}

?>