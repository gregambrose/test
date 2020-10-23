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

	class clientstemplate {
		var $clients = null;
		var $page = null;
		var $sortType = null;

		function clientstemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'clientType' );
			$this->addField( 'alphas' );
			$this->addField( 'searchText' );
			$this->addField( 'clientCode' );
			$this->addField( 'clStatus' );
			$this->set( 'clStatus', 1 );
			$this->setProcess( '_setPage', 'doPage' );
			$this->setReturnTo( '../clients/clients.php' );
			$this->clients = array(  );
			$this->sortType = '';
		}

		function showclientstatus($text) {
			$q = 'SELECT * FROM clientStatus ORDER BY csSequence';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';
			$clStatus = $this->get( 'clStatus' );

			while ($row = udbgetrow( $result )) {
				$csCode = $row['csCode'];
				$csName = $row['csName'];

				if ($csCode == $clStatus) {
					$showIfSelected = 'selected';
				} 
else {
					$showIfSelected = '';
				}

				$this->set( 'csCode', $csCode );
				$this->set( 'csName', $csName );
				$this->set( 'showIfSelected', $showIfSelected );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function showwhennoclientstatus() {
			$clStatus = $this->get( 'clStatus' );

			if ($clStatus <= 0) {
				return 'selected';
			}

			return '';
		}

		function listclients($text) {
			$page = $this->page;
			$start = $page * CLIENTS_PER_PAGE;
			$end = $start + CLIENTS_PER_PAGE - 1;
			$numOfClients = count( $this->clients );
			$rowNo = 0;
			$out = '';
			$elem = $start;

			while ($elem <= $end) {
				if ($numOfClients <= $elem) {
					break;
				}

				$clCode = &$this->clients[$elem];

				$client = new Client( null );
				$found = $client->tryGettingRecord( $clCode );

				if ($found == false) {
					continue;
				}

				$clType = $client->get( 'clType' );
				$cyName = '';

				if (0 < $clType) {
					$type = new ClientType( $clType );
					$cyName = $type->get( 'cyName' );
				}

				$this->set( 'cyName', $cyName );
				$businessType = '';
				$occ = $client->get( 'clBusinessTrade' );
				$businessType = $occ;
				$this->set( 'businessType', $businessType );
				$clCode = $client->getKeyValue(  );
				$this->set( 'clCode', $clCode );
				$clName = $client->getDisplayName(  );
				$occOrBusType = $client->get( 'clBusinessTrade' );
				$this->set( 'occOrBusType', $occOrBusType );
				$fullAddress = $client->get( 'clAddress' ) . ',' . $client->get( 'clPostcode' );
				$fullAddress = str_replace( '
', ',', $fullAddress );
				$fullAddress = str_replace( ',,', ',', $fullAddress );
				$this->set( 'fullAddress', $fullAddress );
				$this->set( 'contactName', $client->getFullName(  ) );
				$this->set( 'clEmail', $client->get( 'clEmail' ) );
				$this->set( 'clName', $clName );
				$this->set( 'clSalutation', $client->get( 'clSalutation' ) );
				$this->set( 'clWorkPhone', $client->get( 'clWorkPhone' ) );
				$this->set( 'clMobile', $client->get( 'clMobile' ) );

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

		function showpagenumberlinks($text) {
			$pages = 1 + ( count( $this->clients ) - 1 ) / CLIENTS_PER_PAGE;
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