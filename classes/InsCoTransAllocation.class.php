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

	class inscotransallocation {
		var $table = null;
		var $keyField = null;

		function inscotransallocation($code) {
			$this->keyField = 'iaCode';
			$this->table = 'inscoTransAllocations';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['iaCashTran'] = 'INT';
			$this->fieldTypes['iaOtherTran'] = 'INT';
			$this->fieldTypes['iaAmount'] = 'MONEY';
			$this->fieldTypes['iaLastUpdateBy'] = 'INT';
			$this->fieldTypes['iaPostingDate'] = 'DATE';
			$this->fieldTypes['iaAccountingYear'] = 'INT';
			$this->fieldTypes['iaAccountingPeriod'] = 'INT';
			$this->_setUpdatedByField( 'iaLastUpdateBy' );
			$this->_setUpdatedWhenField( 'iaLastUpdateOn' );
		}

		function tempcorrectperiods() {
			$iaCode = $this->getKeyValue(  );

			if (0 < $this->get( 'iaAccountingYear' )) {
				trigger_error( '' . 'cannt set ' . $caCode, E_USER_ERROR );
			}


			if (0 < $this->get( 'iaAccountingPeriod' )) {
				trigger_error( '' . 'cannt set ' . $caCode, E_USER_ERROR );
			}

			$date = $this->get( 'iaPostingDate' );

			if (( $date == '' || $date == null )) {
				$last = $this->get( 'iaLastUpdateOn' );
				$date = substr( $last, 0, 4 ) . '-' . substr( $last, 4, 2 ) . '-' . substr( $last, 6, 2 );
				$this->set( 'iaPostingDate', $date );
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

			$this->set( 'iaAccountingYear', $year );
			$this->set( 'iaAccountingPeriod', $period );
		}
	}

?>