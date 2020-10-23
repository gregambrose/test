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

	class introducertransallocation {
		var $table = null;
		var $keyField = null;

		function introducertransallocation($code) {
			$this->keyField = 'raCode';
			$this->table = 'introducerTransAllocations';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['raCashTran'] = 'INT';
			$this->fieldTypes['raOtherTran'] = 'INT';
			$this->fieldTypes['raAmount'] = 'MONEY';
			$this->fieldTypes['raLastUpdateBy'] = 'INT';
			$this->fieldTypes['raPostingDate'] = 'DATE';
			$this->fieldTypes['raAccountingYear'] = 'INT';
			$this->fieldTypes['raAccountingPeriod'] = 'INT';
			$this->_setUpdatedByField( 'raLastUpdateBy' );
			$this->_setUpdatedWhenField( 'raLastUpdateOn' );
		}

		function tempcorrectperiods() {
			$iaCode = $this->getKeyValue(  );

			if (0 < $this->get( 'raAccountingYear' )) {
				trigger_error( '' . 'cannt set ' . $caCode, E_USER_ERROR );
			}


			if (0 < $this->get( 'raAccountingPeriod' )) {
				trigger_error( '' . 'cannt set ' . $caCode, E_USER_ERROR );
			}

			$date = $this->get( 'raPostingDate' );

			if (( $date == '' || $date == null )) {
				$last = $this->get( 'raLastUpdateOn' );
				$date = substr( $last, 0, 4 ) . '-' . substr( $last, 4, 2 ) . '-' . substr( $last, 6, 2 );
				$this->set( 'raPostingDate', $date );
			}

			$year = 2005;
			$period = 0;

			if (( '2006-01-01' <= $date && $date <= '2006-01-31' )) {
				$period = 10;
			}


			if (( '2006-02-01' <= $date && $date <= '2006-02-28' )) {
				$period = 11;
			}


			if (( '2006-03-01' <= $date && $date <= '2006-01-28' )) {
				$period = 12;
			}

			$this->set( 'raAccountingYear', $year );
			$this->set( 'raAccountingPeriod', $period );
		}
	}

?>