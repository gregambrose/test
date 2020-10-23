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

	class clienttransallocation {
		var $table = null;
		var $keyField = null;

		function clienttransallocation($code) {
			$this->keyField = 'caCode';
			$this->table = 'clientTransAllocations';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['caCashTran'] = 'INT';
			$this->fieldTypes['caOtherTran'] = 'INT';
			$this->fieldTypes['caAmount'] = 'MONEY';
			$this->fieldTypes['caLastUpdateBy'] = 'INT';
			$this->fieldTypes['caPostingDate'] = 'DATE';
			$this->fieldTypes['caAccountingYear'] = 'INT';
			$this->fieldTypes['caAccountingPeriod'] = 'INT';
			$this->_setUpdatedByField( 'caLastUpdateBy' );
			$this->_setUpdatedWhenField( 'caLastUpdateOn' );
		}

		function tempcorrectperiods() {
			$caCode = $this->getKeyValue(  );

			if (0 < $this->get( 'caAccountingPeriod' )) {
				trigger_error( '' . 'cannt set ' . $caCode, E_USER_ERROR );
			}

			$date = $this->get( 'caPostingDate' );

			if (( $date == '' || $date == null )) {
				$last = $this->get( 'caLastUpdateOn' );
				$date = substr( $last, 0, 4 ) . '-' . substr( $last, 4, 2 ) . '-' . substr( $last, 6, 2 );
				$this->set( 'caPostingDate', $date );
			}

			$year = 2005;
			$period = 0;

			if (( '2006-01-01' <= $date && $date <= '2006-01-31' )) {
				$period = 10;
			}


			if (( '2006-02-01' <= $date && $date <= '2006-02-28' )) {
				$period = 11;
			}


			if (( '2006-03-01' <= $date && $date <= '2006-03-28' )) {
				$period = 12;
			}

			$this->set( 'caAccountingYear', $year );
			$this->set( 'caAccountingPeriod', $period );
		}
	}

?>