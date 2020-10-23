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

	class introdagedcredittemplate {
		var $page = null;
		var $sortType = null;
		var $subTotal = null;
		var $grandTotal = null;

		function introdagedcredittemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'introdCode' );
			$this->addField( 'includeTrans' );
			$this->addField( 'includeTransMessg' );
			$this->addField( 'ageEffective' );
			$this->addField( 'introdsFound' );
			$this->setHeader( SITE_NAME );
			$this->introds = array(  );
			$this->sortType = '';
			$this->set( 'includeTrans', 'P' );
		}

		function setreportdate($date) {
			return $this->reportUpTo = $date;
		}

		function setsorttype($type) {
			$this->sortType = $type;
		}

		function getsorttype() {
			return $this->sortType;
		}

		function setheaderfields() {
			$type = $this->get( 'alphas' );
			$this->set( 'alpha', $type );
			$type = $this->get( 'searchText' );
			$this->set( 'freeText', $type );
			$inCode = $this->get( 'introdCode' );

			if (0 < $inCode) {
				$in = new Introducer( $inCode );
				$name = $in->get( 'inName' );
			} 
else {
				$name = '';
			}

			$this->set( 'introd', $name );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
			$i = $this->get( 'includeTrans' );
			$messg = 'All Transactions';

			if ($i == 'P') {
				$messg = 'Exclude Future Period Transactions';
			}


			if ($i == 'E') {
				$messg = 'Exclude Future Effective Date Trans.';
			}

			$this->set( 'includeTransMessg', $messg );
			$x = $this->get( 'ageEffective' );

			if ($x == 1) {
				$messg = 'Aged by Effective Date';
			} 
else {
				$messg = '';
			}

			$this->set( 'ageByMessg', $messg );
		}

		function showinclude($type) {
			$x = $this->get( 'includeTrans' );

			if ($x == $type) {
				return 'checked';
			}

			return '';
		}

		function futurechecked() {
			$x = $this->get( 'includeFuture' );

			if ($x == 1) {
				return 'checked';
			}

			return '';
		}

		function effectivechecked() {
			$x = $this->get( 'useEffective' );

			if ($x == 1) {
				return 'checked';
			}

			return '';
		}

		function showageeffective() {
			$x = $this->get( 'ageEffective' );

			if ($x == 1) {
				return 'checked';
			}

			return '';
		}

		function whenreporttoview($text) {
			if ($this->introds == null) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listintroducers($text) {
			global $periodTo;

			$includeTrans = $this->get( 'includeTrans' );
			$ageEffective = $this->get( 'ageEffective' );
			$numOfIntrods = count( $this->introds );
			$this->grand = array(  );
			$slot = 0;

			while ($slot < 5) {
				$this->grand[$slot] = 0;
				++$slot;
			}

			reset( $this->introds );
			$out = '';
			foreach ($this->introds as $icCode) {
				$in = new Introducer( null );
				$found = $in->tryGettingRecord( $icCode );

				if ($found == false) {
					continue;
				}

				$inCode = $in->getKeyValue(  );
				$this->set( 'inCode', $inCode );
				$inName = $in->get( 'inName' );
				$credit = $in->getAgedCredit( $includeTrans, $ageEffective );
				$current = $credit[0];
				$month1 = $credit[1];
				$month2 = $credit[2];
				$month3 = $credit[3];
				$total = $credit[4];
				$slot = 0;

				while ($slot < 5) {
					$this->grand[$slot] += $credit[$slot];
					++$slot;
				}

				$current = uformatmoney( $credit[0] );
				$month1 = uformatmoney( $credit[1] );
				$month2 = uformatmoney( $credit[2] );
				$month3 = uformatmoney( $credit[3] );
				$total = uformatmoney( $credit[4] );
				$this->set( 'current', uformatmoney( $current ) );
				$this->set( 'month1', uformatmoney( $month1 ) );
				$this->set( 'month2', uformatmoney( $month2 ) );
				$this->set( 'month3', uformatmoney( $month3 ) );
				$this->set( 'total', uformatmoney( $total ) );
				$this->set( 'inCode', $in->get( 'inCode' ) );
				$this->set( 'inName', $inName );
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