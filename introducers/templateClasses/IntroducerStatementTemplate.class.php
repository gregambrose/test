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

	class introducerstatementtemplate {
		function introducerstatementtemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function setintroducer($introducer) {
			$this->introducer = &$introducer;

			$inCode = $introducer->get( 'inCode' );
			$this->clearDetailFields(  );
			$this->setAll( $introducer->getAllForHTML(  ) );
			$this->set( 'name', $introducer->get( 'inName' ) );
			$this->set( 'address', $introducer->getInvoiceNameAndAddress(  ) );
			$this->set( 'processDate', 'today' );
			$this->set( 'code', sprintf( '%07d', $inCode ) );
			$this->set( 'doCode', 222 );
			$this->set( 'totalDue', uformatmoneywithcommas( $this->introducer->getTotalDue(  ) ) );
			$this->setAllowEditing( false );
			$this->setAllowExiting( true );
		}

		function listtransactions($text) {
			$inCode = $this->introducer->getKeyValue(  );
			$q = '' . 'SELECT * FROM introducerTransactions 
			  WHERE rtIntroducer = ' . $inCode . ' 
			  AND rtBalance != 0
			  ORDER BY rtPostingDate';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$rt = new IntroducerTransaction( $row );
				$this->set( 'rtPostingDate', $rt->getForHTML( 'rtPostingDate' ) );
				$this->set( 'rtCode', $rt->getForHTML( 'rtCode' ) );
				$type = '';
				$rtTransType = $rt->get( 'rtTransType' );
				$rtOriginal = $rt->getForHTML( 'rtOriginal' );

				if ($rtTransType == 'C') {
					$type = 'CASH';
					$rtPaymentMethod = $rt->get( 'rtPaymentMethod' );
					$cp = new CashPaymentMethod( $rtPaymentMethod );
					$transType = $cp->getForHTML( 'cpName' );
					$btCode = $rt->get( 'rtCashBatch' );

					if (0 < $btCode) {
						$bt = new CashBatch( $btCode );
						$polNumDesc = $bt->get( 'btPayInSlip' );
					} 
else {
						$polNumDesc = '';
					}

					$this->set( 'ref', 'CB ' . $rt->getForHTML( 'rtCashBatch' ) );
				} 
else {
					if ($rtTransType == 'I') {
						if ($rtOriginal < 0) {
							$type = 'CR NOTE';
						} 
else {
							$type = 'DR NOTE';
						}

						$ptCode = $rt->get( 'rtPolicyTran' );
						$pt = new PolicyTransaction( $ptCode );
						$polNumDesc = $pt->get( 'ptPolicyNumber' );
						$ptTransType = $pt->get( 'ptTransType' );
						$py = new PolicyTransactionType( $ptTransType );
						$transType = $py->getForHTML( 'pyName' );
						$this->set( 'ref', sprintf( '%07d', $pt->get( 'ptInvoiceNo' ) ) );
					} 
else {
						if ($rtTransType == 'R') {
							$type = 'RECEIPT';
							$polNumDesc = '';
							$transType = $type;
						} 
else {
							trigger_error( '' . 'wrong type ' . $rtTransType, E_USER_ERROR );
						}
					}
				}

				$this->set( 'type', $type );
				$this->set( 'polNumDesc', $polNumDesc );
				$this->set( 'transType', $transType );
				$this->set( 'rtOriginal', $rt->getForHTML( 'rtOriginal' ) );
				$this->set( 'rtBalance', $rt->getForHTML( 'rtBalance' ) );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>