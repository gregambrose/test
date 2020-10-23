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

	class controlaccounttotal {
		var $toolTip = null;
		var $selectStatements = null;
		var $swapSigns = null;
		var $detailSelectStatements = null;
		var $detailSwapSigns = null;
		var $_answer = null;
		var $_calcsDone = null;
		var $_alwaysShowBlank = null;

		function controlaccounttotal($toolTip, $swapSigns, $selectStatement) {
			global $controlFromDate;
			global $controlToDate;

			$START = $controlFromDate;
			$END = $controlToDate;
			$this->_alwaysShowBlank = false;
			$this->toolTip = $toolTip;
			$this->selectStatements = array(  );
			$this->swapSigns = array(  );

			if ($selectStatement != null) {
				$this->selectStatements[0] = $selectStatement;
				$this->swapSigns[0] = $swapSigns;
			}

			$this->_answer = 0;
			$this->_calcsDone = false;
			$this->name = '';
		}

		function addselect($swapSigns, $selectStatement) {
			if ($selectStatement == null) {
				return null;
			}

			$num = count( $this->selectStatements );
			$this->selectStatements[$num] = $selectStatement;
			$this->swapSigns[$num] = $swapSigns;
			$this->_calcsDone = false;
		}

		function adddetailselect($swapSigns, $selectStatement) {
			if ($selectStatement == null) {
				return null;
			}

			$num = count( $this->detailSelectStatements );
			$this->detailSelectStatements[$num] = $selectStatement;
			$this->detailSwapSigns[$num] = $swapSigns;
		}

		function setamount($amt) {
			$this->_calcsDone = true;
			$this->_answer = $amt;
		}

		function setalwaysshowblank($show) {
			$this->_alwaysShowBlank = $show;
		}

		function get() {
			if ($this->_calcsDone == false) {
				$this->_calcTotals(  );
			}

			return $this->_answer;
		}

		function getforhtml() {
			if ($this->_calcsDone == false) {
				$this->_calcTotals(  );
			}


			if ($this->_alwaysShowBlank == true) {
				return '';
			}

			return uformatmoneywithcommas( $this->_answer );
		}

		function gettooltip() {
			return $this->toolTip;
		}

		function getdetailarray() {
			if (isset( ->details )) {
				return $this->details;
			}

			$value = array(  );

			if (count( $this->detailSelectStatements ) == 0) {
				return null;
			}

			$results = array(  );
			foreach ($this->detailSelectStatements as $mainKey => $mainValue) {
				$selectStatement = $mainValue;
				$swapSigns = $this->detailSwapSigns[$mainKey];
				$q = $selectStatement;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( '' . 'cant do ' . $q . ' <br>' . udblasterror(  ), E_USER_ERROR );
				}

				$rows = udbnumberofrows( $result );

				while ($row = udbgetrow( $result )) {
					$numDone = 0;
					$newRow = array(  );
					$newRow['key'] = '';
					$newRow['date'] = '';
					foreach ($row as $key => $value) {
						if ($numDone == 0) {
							$newRow['key'] = $value;
						}


						if ($numDone == 1) {
							$newRow['date'] = $value;
						}


						if (( $swapSigns == true && 1 < $numDone )) {
							$value = 0 - $value;
						}

						++$numDone;
						$newRow[$key] = $value;
					}

					$results[] = $newRow;
				}
			}

			$this->details = $results;
			return $results;
		}

		function _calctotals() {
			$this->_calcsDone = true;
			$this->_answer = 0;

			if (count( $this->selectStatements ) == 0) {
				return null;
			}

			foreach ($this->selectStatements as $mainKey => $mainValue) {
				$selectStatement = $mainValue;
				$swapSigns = $this->swapSigns[$mainKey];
				$q = $selectStatement;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( '' . 'cant do ' . $q . ' <br>' . udblasterror(  ), E_USER_ERROR );
				}

				$rows = udbnumberofrows( $result );

				if ($rows != 1) {
					trigger_error( '' . 'gets ' . $rows . ' rows for ' . $q, E_USER_ERROR );
				}

				$row = udbgetrow( $result );

				if ($row == null) {
					trigger_error( '' . 'no row returned for ' . $q, E_USER_ERROR );
				}

				$ans = 0;
				foreach ($row as $key => $value) {
					$ans += $value;
				}


				if ($swapSigns == true) {
					$this->_answer -= $ans;
					continue;
				}

				$this->_answer += $ans;
			}

		}
	}

?>