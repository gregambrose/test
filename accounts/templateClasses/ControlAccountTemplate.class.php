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

	class controlaccounttemplate {
		var $flds = null;
		var $detailObject = null;
		var $detailFields = null;

		function controlaccounttemplate($html) {
			ftemplate::ftemplate( $html );
			$this->addField( 'dateFrom' );
			$this->addField( 'dateTo' );
			$this->addField( 'periodDesc' );
			$this->addField( 'selectedPeriod' );
			$this->addField( 'selectedPeriodCode' );
			$this->addField( 'selectedYear' );
			$this->setFieldType( 'dateFrom', 'DATE' );
			$this->setFieldType( 'dateTo', 'DATE' );
			$this->transDescs = array(  );
			$this->transDescs['ptSysTran'] = 'Policy Transaction';
			$this->transDescs['ctSysTran'] = 'Client Transaction';
			$this->transDescs['itSysTran'] = 'Ins.Co.Transaction';
			$this->transDescs['rtSysTran'] = 'Introd Transaction';
			$this->fieldDescs = array(  );
			$this->fieldDescs['ctWrittenOff'] = 'Written Off';
			$this->fieldDescs['ptAddlCommission'] = 'Addl Commission';
			$this->fieldDescs['ptAddOnCommission'] = 'Add On Commission';
			$this->fieldDescs['ptBrokerFee'] = 'Broker Fee';
			$this->fieldDescs['ptClientDiscount'] = 'Client Discount';
			$this->fieldDescs['ptCommission'] = 'Main Commission';
			$this->fieldDescs['ptEngineeringFeeComm'] = 'Eng. Fee. Commission';
			$this->fieldDescs['ptIntroducerComm'] = 'Introd. Commission';
			$this->fieldDescs['itWrittenOff'] = 'IC Comm. Adj.';
			$this->fieldDescs['rtWrittenOff'] = 'Introd. Comm. Adj.';
		}

		function setfields($flds) {
			$this->flds = $flds;
		}

		function getfields() {
			return $this->flds;
		}

		function getfield($fld) {
			if (!isset( $this->flds[$fld] )) {
				return null;
			}

			return $this->flds[$fld];
		}

		function whensometodo($text) {
			if (!isset( $this->flds )) {
				return '';
			}

			$out = $this->parse( $text );
			return $out;
		}

		function setdetailobject($c) {
			$this->detailObject = &$c;

			$toolTip = $c->getToolTip(  );
			$this->set( 'toolTip', $toolTip );
		}

		function showamount($name) {
			if (!isset( $this->flds[$name] )) {
				return 'undef';
			}

			$obj = $this->flds[$name];
			$amt = $obj->getForHTML(  );

			if ($amt == '') {
				return '';
			}

			$curr = '';

			if (defined( 'CURRENCY_SYMBOL_FOR_HTML' )) {
				$curr = CURRENCY_SYMBOL_FOR_HTML;
			} 
else {
				if (defined( 'CURRENCY_SYMBOL' )) {
					$curr = CURRENCY_SYMBOL;
				}
			}

			return $curr . $amt;
		}

		function showtooltip($name) {
			if (!isset( $this->flds[$name] )) {
				return 'undef';
			}

			$obj = $this->flds[$name];
			$text = $obj->getToolTip(  );
			return $text;
		}

		function showcolour($name) {
			if (!isset( $this->flds[$name] )) {
				return 'undef';
			}

			$obj = $this->flds[$name];
			$amt = (int)$obj->get(  );

			if (0 <= $amt) {
				$colour = 'black';
			} 
else {
				$colour = 'red';
			}

			return $colour;
		}

		function classformoney($name) {
			if (!isset( $this->flds[$name] )) {
				return 'undef';
			}

			$obj = $this->flds[$name];
			$amt = (int)$obj->get(  );

			if (0 <= $amt) {
				$colour = 'positive';
			} 
else {
				$colour = 'negative';
			}

			return $colour;
		}

		function listperiods($text) {
			$selectedPeriod = $this->get( 'selectedPeriod' );
			$out = '';
			$p = 1;

			while ($p <= ACCOUNTING_PERIODS_PER_YEAR) {
				$this->set( 'period', $p );

				if ($selectedPeriod == $p) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
				++$p;
			}

			return $out;
		}

		function listyears($text) {
			$selectedYear = $this->get( 'selectedYear' );
			$q = 'SELECT ayCode FROM accountingYears ORDER BY ayYear';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( udblasterror(  ), E_USER_ERROR );
			}

			$out = '';

			while ($row = udbgetrow( $result )) {
				$ayCode = $row['ayCode'];
				$ay = new AccountingYear( $ayCode );
				$this->set( 'code', $ayCode );
				$desc = $ay->getForHTML( 'ayYear' );

				if (strlen( trim( $desc ) ) == 0) {
					$desc = 'blank';
				}

				$this->set( 'year', $ay->getForHTML( 'ayName' ) );

				if ($selectedYear == $ayCode) {
					$this->set( 'showIfSelected', 'selected' );
				} 
else {
					$this->set( 'showIfSelected', '' );
				}

				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listtransactions($text) {
			if (!isset( $this->detailObject )) {
				return '';
			}

			$out = '';
			$details = &$this->detailObject;

			$d = $details->getDetailArray(  );

			if ($d == null) {
				return $out;
			}

			$ok = usort( $d, array( 'ControlAccountTemplate', '_detailSortComparison' ) );
			$this->detailTotal = 0;
			foreach ($d as $key => $value) {
				$this->detailFields = $value;
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listfields($text) {
			$out = '';
			$fields = &$this->detailFields;

			if ($fields == null) {
				return $out;
			}

			$done = 0;
			foreach ($fields as $key => $value) {
				$key = str_replace( '_', ' ', $key );
				$value = str_replace( '_', ' ', $value );

				if ($done < 2) {
					++$done;
					continue;
				}


				if ($done == 2) {
					$type = $key;
					$ref = $value;
					++$done;
					continue;
				}


				if ($done == 3) {
					$date = $value;
					++$done;
					continue;
				}


				if ($value == 0) {
					continue;
				}

				$this->detailTotal += $value;
				$value = uformatmoney( $value );
				$this->set( 'type', $this->_getTransDesc( $type ) );
				$this->set( 'ref', $ref );
				$this->set( 'date', uformatsqldate2( $date ) );
				$this->set( 'field', $this->_getFieldDesc( $key ) );
				$this->set( 'value', uformatmoneywithcommas( $value ) );
				$out .= $this->parse( $text );
				$type = '';
				$ref = '';
				$date = '';
			}

			$this->set( 'total', uformatmoneywithcommas( $this->detailTotal ) );
			return $out;
		}

		function _gettransdesc($key) {
			$type = $key;

			if (isset( $this->transDescs[$key] )) {
				$type = $this->transDescs[$key];
			}

			return $type;
		}

		function _getfielddesc($key) {
			$type = $key;

			if (isset( $this->fieldDescs[$key] )) {
				$type = $this->fieldDescs[$key];
			}

			return $type;
		}

		function _detailsortcomparison($v1, $v2) {
			$d1 = $v1['date'];
			$d2 = $v2['date'];

			if ($d1 < $d2) {
				return 0 - 1;
			}


			if ($d2 < $d1) {
				return 1;
			}

			$r1 = $v1['key'];
			$r2 = $v2['key'];

			if ($r1 < $r2) {
				return 0 - 1;
			}


			if ($r2 < $r1) {
				return 1;
			}

			return 0;
		}
	}

?>