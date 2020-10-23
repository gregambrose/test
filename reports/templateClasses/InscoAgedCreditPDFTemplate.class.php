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

	class inscoagedcreditpdftemplate {
		var $reportUpTo = null;

		function inscoagedcreditpdftemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedYear' );
			$this->addField( 'includeType' );
			$this->addField( 'includeTypeMessg' );
			$this->setHeader( SITE_NAME );
			$this->insCos = array(  );
			$this->sortType = '';
			$this->reportUpTo = null;
		}

		function setreportdate($date) {
			return $this->reportUpTo = $date;
		}

		function setyear($year) {
			$this->set( 'year', $year );
		}

		function setperiod($period) {
			$this->set( 'period', $period );
		}

		function setperioddescription($desc) {
			$this->set( 'periodDesc', $desc );
		}

		function setincludefuturemessage($desc) {
			$this->set( 'includeTypeMessg', $desc );
		}

		function setincludetype($type) {
			$this->set( 'includeType', $type );
		}

		function setheaderfields() {
			$now = uformatourtimestamp( ugettimenow(  ) );
			$this->set( 'now', $now );
		}

		function listinscos($text) {
			global $periodTo;

			$numOfInscos = count( $this->insCos );
			$this->grand = array(  );
			$slot = 0;

			while ($slot < 5) {
				$this->grand[$slot] = 0;
				++$slot;
			}


			if ($this->insCos == null) {
				return '';
			}

			$includeType = $this->get( 'includeType' );
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
				$debt = $ins->getAgedCredit( $includeType );
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
	}

?>