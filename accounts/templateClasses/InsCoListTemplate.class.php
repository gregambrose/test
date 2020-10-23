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

	class inscolisttemplate {
		var $page = null;
		var $sortType = null;
		var $balanceType = null;
		var $doNormalItem = null;
		var $doClientTotal = null;
		var $doMonthTotal = null;
		var $doGrandTotal = null;
		var $subTotal = null;
		var $grandTotal = null;

		function inscolisttemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'insCoCode' );
			$this->addField( 'balanceType' );
			$this->addField( 'insCosFound' );
			$this->setHeader( SITE_NAME );
			$this->insCos = array(  );
			$this->sortType = '';
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
			$icCode = $this->get( 'insCoCode' );

			if (0 < $icCode) {
				$in = new Insco( $icCode );
				$name = $cl->get( 'icName' );
			} 
else {
				$name = '';
			}

			$this->set( 'insCo', $name );
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
		}

		function showbalancechecked($type) {
			$balanceType = $this->get( 'balanceType' );

			if ($type == $balanceType) {
				return 'selected';
			}

			return '';
		}

		function whenreporttoview($text) {
			if ($this->insCos == null) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function listinscos($text) {
			$numOfInsCos = count( $this->insCos );
			$this->grand = array(  );
			$slot = 0;

			while ($slot < 5) {
				$this->grand[$slot] = 0;
				++$slot;
			}

			reset( $this->insCos );
			$out = '';
			foreach ($this->insCos as $icCode) {
				$ins = new Insco( null );
				$found = $ins->tryGettingRecord( $icCode );

				if ($found == false) {
					continue;
				}

				$icCode = $ins->getKeyValue(  );
				$this->set( 'icCode', $icCode );
				$icName = $ins->get( 'icName' );
				$credit = $ins->getAgedCredit(  );
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
				$this->set( 'icCode', $ins->get( 'icCode' ) );
				$this->set( 'icName', $icName );
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