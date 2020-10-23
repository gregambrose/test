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

	class controlaccountpdftemplate {
		function controlaccountpdftemplate($html) {
			documentstemplate::documentstemplate( $html );
		}

		function setfields($flds) {
			$this->flds = $flds;
		}

		function setyear($year) {
			$this->set( 'year', $year );
		}

		function setperiod($period) {
			$this->set( 'period', $period );
		}

		function setperioddescription($desc) {
			$this->set( 'periodDesc', $desc );
		}

		function showamount($name) {
			if (!isset( $this->flds[$name] )) {
				return '';
			}


			if ($name == 'cfOpening') {
				$a = 0;
			}

			$obj = $this->flds[$name];
			$amt = $obj->getForHTML(  );

			if ($amt == '') {
				return '';
			}

			return CURRENCY_SYMBOL . $amt;
		}

		function showcolour($details) {
			$values = explode( ',', $details );

			if (sizeof( $values ) != 2) {
				trigger_error( 'wrong args ', E_USER_ERROR );
			}

			$column = $values[0];
			$name = $values[1];

			if (!isset( $this->flds[$name] )) {
				return '';
			}

			$obj = $this->flds[$name];
			$amt = (int)$obj->get(  );

			if (0 <= $amt) {
				$colour = 'black';
			} 
else {
				$colour = 'red';
			}

			$out = '' . '<tableSetColumnColour column="' . $column . '" colour="' . $colour . '" />';
			return $out;
		}
	}

?>