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

	class introducerstemplate {
		var $introducers = null;
		var $page = null;
		var $sortType = null;
		var $reportDate = null;

		function introducerstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'introducerCode' );
			$this->setProcess( '_setPage', 'doPage' );
			$this->setReturnTo( '../introducers/introducers.php' );
			$this->introducers = array(  );
			$this->sortType = '';
		}

		function setreportdate($date) {
			return $this->reportDate = $date;
		}

		function listintroducers($text) {
			$page = $this->page;
			$start = $page * INTRODUCERS_PER_PAGE;
			$end = $start + INTRODUCERS_PER_PAGE - 1;
			$numOfIntroducers = count( $this->introducers );
			$rowNo = 0;
			$out = '';
			$elem = $start;

			while ($elem <= $end) {
				if ($numOfIntroducers <= $elem) {
					break;
				}

				$inCode = &$this->introducers[$elem];

				$introducer = new Introducer( null );
				$found = $introducer->tryGettingRecord( $inCode );

				if ($found == false) {
					continue;
				}

				$icName = $introducer->get( 'inName' );
				$fullAddress = $introducer->get( 'inAddress' ) . ' ' . $introducer->get( 'inPostcode' );
				$fullAddress = str_replace( '
', ',', $fullAddress );
				$fullAddress = str_replace( ',,', ',', $fullAddress );
				$this->set( 'fullAddress', $fullAddress );
				$this->set( 'inCode', $introducer->get( 'inCode' ) );
				$this->set( 'inName', $introducer->get( 'inName' ) );
				$this->set( 'inDepartment', $introducer->get( 'inDepartment' ) );
				$this->set( 'inContact', $introducer->get( 'inContact' ) );
				$this->set( 'inContactPhone', $introducer->get( 'inContactPhone' ) );
				$this->set( 'inEmail', $introducer->get( 'inEmail' ) );

				if ($rowNo % 2 == 0) {
					$x = ROW_COLOUR_A;
				} 
else {
					$x = ROW_COLOUR_B;
				}

				$this->set( 'rowColour', $x );
				++$rowNo;
				$out .= $this->parse( $text );
				++$elem;
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

		function showpagenumberlinks($text) {
			$pages = 1 + ( count( $this->introducers ) - 1 ) / INTRODUCERS_PER_PAGE;
			$pages = floor( $pages );
			$out = '';
			$elem = 0;

			while ($elem < $pages) {
				if (( $elem < $this->page - 5 && $elem != 0 )) {
					continue;
				}


				if (( $this->page + 5 < $elem && $elem != $pages - 1 )) {
					continue;
				}

				$fontColour = '#3366CC';

				if ($elem == $this->page) {
					$fontColour = 'red';
				}

				$this->set( 'fontColour', $fontColour );
				$this->set( 'actualPage', $elem );
				$displayPage = $elem + 1;

				if ($elem == 0) {
					$displayPage = 'first';
				}


				if ($elem == $pages - 1) {
					$displayPage = 'last';
				}

				$this->set( 'displayPage', $displayPage );
				$out .= $this->parse( $text );
				++$elem;
			}

			return $out;
		}

		function _setpage($template, $input) {
			global $sessionName;

			$page = $input['doPage'];
			$this->page = $page;
			$sessionName = $_REQUEST['sn'];
			return false;
		}

		function setsorttype($type) {
			$this->sortType = $type;
		}

		function getsorttype() {
			return $this->sortType;
		}
	}

?>