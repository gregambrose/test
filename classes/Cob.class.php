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

	class cob {
		var $table = null;
		var $keyField = null;

		function cob($code) {
			$this->keyField = 'cbCode';
			$this->table = 'classOfBus';
			urecord::urecord( $code, $this->table, $this->keyField );
			$this->fieldTypes['cbSequence'] = 'INT';
			$this->fieldTypes['cbIsTravel'] = 'BOOL';
			$this->fieldTypes['cbFeesVatable'] = 'BOOL';
			$this->fieldTypes['cbRMAR'] = 'BOOL';
			$this->fieldTypes['cbZeroIPT'] = 'BOOL';
			$this->fieldTypes['cbAllowIPTAmend'] = 'BOOL';
		}

		function isthistravel() {
			$cbIsTravel = $this->get( 'cbIsTravel' );

			if ($cbIsTravel == 1) {
				return true;
			}

			return false;
		}

		function isiptzerorated() {
			$cbzero = $this->get( 'cbZeroIPT' );

			if ($cbzero == 1) {
				return true;
			}

			return false;
		}
	}

?>