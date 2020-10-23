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

	class insco {
		var $table = null;
		var $keyField = null;

		function insco($code) {
			$this->keyField = 'icCode';
			$this->table = 'insuranceCompanies';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['icInvAddress'] = 'INT';
			$this->fieldTypes['icDelegated'] = 'INT';
			$this->fieldTypes['icAccTitle'] = 'INT';
			$this->fieldTypes['icType'] = 'INT';
			$this->fieldTypes['icAddonCOB'] = 'INT';
			$this->fieldTypes['icStatus'] = 'INT';
			$this->_setUpdatedByField( 'icLastUpdateBy' );
			$this->_setUpdatedWhenField( 'icLastUpdateOn' );
			$this->handleConcurrency( true );
			$q = 'SELECT iyName, cbName, tiName FROM insuranceCompanies
				LEFT JOIN insCoTypes on icType = iyCode
				LEFT JOIN classOfBus on icAddonCOB = cbCode
				LEFT JOIN titles on icAccTitle = tiCode

			where icCode = CODE';
			$this->setExtraSql( $q );
		}

		function getagedcredit($includeTrans = 'P') {
			global $periodTo;

			$icCode = $this->getKeyValue(  );

			if ($includeTrans == 'A') {
				$q = '' . 'SELECT itEffectiveDate as date, itPaidDate,  itPaid, itBalance FROM inscoTransactions WHERE itInsCo=' . $icCode;
			} 
else {
				if ($includeTrans == 'P') {
					$q = '' . 'SELECT itEffectiveDate as date, itPaidDate,  itPaid, itBalance FROM inscoTransactions WHERE itInsCo=' . $icCode;
					$q .= '' . ' AND itPostingDate <= \'' . $periodTo . '\'';
				} 
else {
					if ($includeTrans == 'E') {
						$q = '' . 'SELECT itEffectiveDate as date, itPaidDate,  itPaid, itBalance FROM inscoTransactions WHERE itInsCo=' . $icCode;
						$q .= '' . ' AND itEffectiveDate <= \'' . $periodTo . '\' AND itPostingDate <= \'' . $periodTo . '\'';
					} 
else {
						trigger_error( '' . 'wrong inc trans option ' . $includeTrans, E_USER_ERROR );
					}
				}
			}

			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$aged = array(  );
			$slot = 0;

			while ($slot < 5) {
				$aged[$slot] = 0;
				++$slot;
			}


			while ($row = udbgetrow( $result )) {
				$date = $row['date'];
				$amt = $row['itBalance'];
				$paid = $row['itPaid'];
				$paidDate = $row['itPaidDate'];
				$slot = fcalcage( $date, $periodTo );

				if (3 < $slot) {
					$slot = 3;
				}


				if (!isset( $aged[$slot] )) {
					$aged[$slot] = 0;
				}

				$aged[$slot] += $amt;
				$aged[4] += $amt;
			}

			return $aged;
		}

		function getinvoicenameandaddress() {
			if ($this->get( 'icInvAddress' ) == 1) {
				$name = '';
				$name = $this->get( 'icName' );
				$add = trim( $this->get( 'icAddress' ) );
				$len = strlen( $add );

				if (substr( $add, $len - 1, 1 ) == '
') {
					$add = substr( $add, 0, $len - 1 );
				}

				$pc = $this->get( 'icPostcode' );

				if (0 < strlen( trim( $pc ) )) {
					$add .= '
' . $pc;
				}

				$c = $this->get( 'icCountry' );

				if (0 < strlen( trim( $c ) )) {
					$add .= '
' . $c;
				}

				$nameAndAddress = $name . '
' . $add;
			} 
else {
				$name = $this->get( 'icName' ) . '
';
				$t = $this->get( 'icAccTitle' );

				if (0 < $t) {
					$ct = new Title( $t );
					$title = $ct->get( 'tiName' );

					if (0 < strlen( trim( $title ) )) {
						$name .= $title . ' ';
					}
				}

				$f = $this->get( 'icAccFirstName' );

				if (0 < strlen( trim( $f ) )) {
					$name .= $f . ' ';
				}

				$l = $this->get( 'icAccLastName' );

				if (0 < strlen( trim( $l ) )) {
					$name .= $l . ' ';
				}

				$add = $this->get( 'icAccAddress' );
				$pc = $this->get( 'icAccPostcode' );

				if (0 < strlen( trim( $pc ) )) {
					$add .= '
' . $pc;
				}

				$c = $this->get( 'icAccCountry' );

				if (0 < strlen( trim( $c ) )) {
					$add .= '
' . $c;
				}

				$nameAndAddress = $name . '
' . $add;
			}

			return $nameAndAddress;
		}
	}

?>