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

	class clientstatementtemplate {
		var $reportDate = null;

		function clientstatementtemplate($html) {
			documentstemplate::documentstemplate( $html );
			$this->reportDate = null;
		}

		function setclient($client) {
			$this->client = &$client;

			$clCode = $client->get( 'clCode' );
			$this->clearDetailFields(  );
			$this->setAll( $client->getAllForHTML(  ) );
			$this->set( 'name', $client->getDisplayName(  ) );
			$this->set( 'address', $client->getInvoiceNameAndAddress(  ) );
			$this->set( 'processDate', 'today' );
			$this->set( 'code', sprintf( '%07d', $clCode ) );
			$this->set( 'doCode', 222 );
			$this->set( 'totalDue', uformatmoneywithcommas( $this->client->getTotalDue(  ) ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function setreportdate($date) {
			return $this->reportDate = $date;
		}

		function listtransactions($text) {
			$clCode = $this->client->getKeyValue(  );
			$q = '' . 'SELECT * FROM clientTransactions 
			  WHERE ctClient = ' . $clCode . ' 
			  AND ctBalance != 0
			  ORDER BY ctPostingDate';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ct = new ClientTransaction( $row );
				$this->set( 'ctPostingDate', $ct->getForHTML( 'ctPostingDate' ) );
				$this->set( 'ctCode', $ct->getForHTML( 'ctCode' ) );
				$type = '';
				$ctTransType = $ct->get( 'ctTransType' );
				$ctOriginal = $ct->getForHTML( 'ctOriginal' );

				if ($ctTransType == 'C') {
					$type = 'CASH';
					$ctPaymentMethod = $ct->get( 'ctPaymentMethod' );
					$cp = new CashPaymentMethod( $ctPaymentMethod );
					$transType = $cp->getForHTML( 'cpName' );
					$btCode = $ct->get( 'ctCashBatch' );

					if (0 < $btCode) {
						$bt = new CashBatch( $btCode );
						$polNumDesc = $bt->get( 'btPayInSlip' );
					} 
else {
						$polNumDesc = '';
					}

					$this->set( 'ref', 'CB ' . $ct->getForHTML( 'ctCashBatch' ) );
				} 
else {
					if ($ctTransType == 'I') {
						if ($ctOriginal < 0) {
							$type = 'CR NOTE';
						} 
else {
							$type = 'DR NOTE';
						}

						$ptCode = $ct->get( 'ctPolicyTran' );
						$pt = new PolicyTransaction( $ptCode );
						$polNumDesc = $pt->get( 'ptPolicyNumber' );
						$ptTransType = $pt->get( 'ptTransType' );
						$py = new PolicyTransactionType( $ptTransType );
						$transType = $py->getForHTML( 'pyName' );
						$this->set( 'ref', sprintf( '%07d', $ct->get( 'ctInvoiceNo' ) ) );
					} 
else {
						trigger_error( '' . 'wrong type ' . $ctTransType, E_USER_ERROR );
					}
				}

				$this->set( 'type', $type );
				$this->set( 'polNumDesc', $polNumDesc );
				$this->set( 'transType', $transType );
				$this->set( 'ctOriginal', $ct->getForHTML( 'ctOriginal' ) );
				$this->set( 'ctBalance', $ct->getForHTML( 'ctBalance' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>