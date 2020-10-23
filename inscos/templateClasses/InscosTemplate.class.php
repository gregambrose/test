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

	class inscostemplate {
		var $clients = null;
		var $page = null;
		var $sortType = null;

		function inscostemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'inscoCode' );
			$this->setProcess( '_setPage', 'doPage' );
			$this->setReturnTo( '../inscos/inscos.php' );
			$this->inscos = array(  );
			$this->sortType = '';
		}

		function listinscos($text) {
			$page = $this->page;
			$start = $page * INSCOS_PER_PAGE;
			$end = $start + INSCOS_PER_PAGE - 1;
			$numOfInscos = count( $this->inscos );
			$out = '';
			$elem = $start;

			while ($elem <= $end) {
				if ($numOfInscos <= $elem) {
					break;
				}

				$icCode = &$this->inscos[$elem];

				$insco = new Insco( null );
				$found = $insco->tryGettingRecord( $icCode );

				if ($found == false) {
					continue;
				}

				$this->set( 'icCode', $icCode );
				$icName = $insco->get( 'icName' );
				$fullAddress = $insco->get( 'icAddress' ) . ' ' . $insco->get( 'icPostcode' );
				$fullAddress = str_replace( '
', ',', $fullAddress );
				$fullAddress = str_replace( ',,', ',', $fullAddress );
				$this->set( 'fullAddress', $fullAddress );
				$this->set( 'icName', $insco->get( 'icName' ) );
				$this->set( 'icDeptName1', $insco->get( 'icDeptName1' ) );
				$this->set( 'icDeptContact1', $insco->get( 'icDeptContact1' ) );
				$this->set( 'icDeptPhone1', $insco->get( 'icDeptPhone1' ) );
				$this->set( 'icDeptEmail1', $insco->get( 'icDeptEmail1' ) );
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
			$pages = 1 + ( count( $this->inscos ) - 1 ) / INSCOS_PER_PAGE;
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