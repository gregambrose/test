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

	class urecord {
		var $tableName = null;
		var $keyField = null;
		var $keyValue = null;
		var $recordExists = null;
		var $fieldNames = null;
		var $fieldTypes = null;
		var $ignoreForUpdate = null;
		var $months = null;
		var $handleConcurrency = null;
		var $_fldForUpdatedBy = null;
		var $_fldForUpdatedWhen = null;
		var $sqlForExtras = null;

		function urecord($code, $table, $keyField) {
			$this->tableName = $table;
			$this->keyField = $keyField;

			if (( !isset( $this->fieldTypes ) || !is_array( $this->fieldTypes ) )) {
				$this->fieldTypes = array(  );
			}

			$this->fieldNames = array(  );
			$this->ignoreForUpdate = array(  );
			$this->handleConcurrency = false;
			$this->recordExists = true;

			if ($code == null) {
				$this->recordExists = false;
				$this->_getFieldsFromDatabase(  );
			} 
else {
				if (is_array( $code )) {
					$this->_populateFromArray( $code );
				} 
else {
					$this->_populateFromDatabase( $code, $table, $keyField );
				}
			}


			if ($this->handleConcurrency == true) {
				if (!isset( $this->lastAccessTime )) {
					trigger_error( '' . 'no lastAccessTime Field in ' . $this->tableName, E_USER_ERROR );
				}
			}

			$this->months = array( 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12 );
		}

		function handleconcurrency($doIt) {
			if (( $doIt != true && $doIt != false )) {
				trigger_error( 'needs to be true or false', E_USER_ERROR );
			}

			$this->handleConcurrency = $doIt;

			if ($doIt == true) {
				if (!isset( $this->fieldNames['lastAccessTime'] )) {
					$this->fieldNames['lastAccessTime'] = null;
				}


				if (!isset( $this->lastAccessTime )) {
					$this->lastAccessTime = null;
				}
			}

		}

		function getkeyvalue() {
			return $this->keyValue;
		}

		function fieldexists($field) {
			$a = isset( $this->fieldNames[$field] );

			if (isset( $this->fieldNames[$field] ) == true) {
				return true;
			}

			return false;
		}

		function get($fld) {
			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BLOB' )) {
				$table = $this->tableName;
				$keyField = $this->keyField;
				$code = $this->keyValue;

				if (defined( 'BLOB_DIR' )) {
					$fileName = BLOB_DIR . '/' . $table . '-' . $fld;

					if (file_exists( $fileName )) {
						$size = filesize( $fileName );

						if (define( 'MAX_BLOB_SIZE' )) {
							if (MAX_BLOB_SIZE < $size) {
								trigger_error( '' . 'BLOB too big for file ' . $fileName . ', key ' . $code, E_USER_ERROR );
							}
						}

						$value = file_get_contents( $fileName );
						return $value;
					}
				} 
else {
					$q = '' . 'SELECT ' . $fld . ' FROM ' . $table . ' WHERE ' . $keyField . '=' . $code;
					$result = udbquery( $q );

					if ($result == false) {
						trigger_error( '' . 'URecord: cant do query for ' . $fld . ' on ' . $this->tableName, E_USER_ERROR );
					}

					$row = udbgetrow( $result );

					if ($row == null) {
						trigger_error( '' . 'URecord: cant get result for ' . $fld . ' on table ' . $table . ', query=' . $q, E_USER_ERROR );
					}

					$value = base64_decode( $row[$fld] );
					udbfreeresult( $result );
					return $value;
				}
			}


			if (isset( $this->$fld )) {
				if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
					$this->$fld = round( $this->$fld, 0 );
					$value = $this->$fld;
					settype( $value, 'integer' );
				} 
else {
					$value = $this->$fld;
				}
			} 
else {
				$value = null;
			}

			return $value;
		}

		function getblobfromdb($fld) {
			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BLOB' )) {
				$table = $this->tableName;
				$keyField = $this->keyField;
				$code = $this->keyValue;
				$q = '' . 'SELECT ' . $fld . ' FROM ' . $table . ' WHERE ' . $keyField . '=' . $code;
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( '' . 'URecord: cant do query for ' . $fld . ' on ' . $this->tableName, E_USER_ERROR );
				}

				$row = udbgetrow( $result );

				if ($row == null) {
					trigger_error( '' . 'URecord: cant get result for ' . $fld . ' on table ' . $table . ', query=' . $q, E_USER_ERROR );
				}

				$x = $row[$fld];
				$value = base64_decode( $x );
				udbfreeresult( $result );
				return $value;
			}

		}

		function set($fld, $value) {
			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BOOL' )) {
				if (is_string( $value )) {
					if ($value == 'on') {
						$value = 1;
					}


					if ($value == 'off') {
						$value = 0;
					}
				}
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'DATE' )) {
				$elems = explode( '/', $value );

				if (count( $elems ) == 3) {
					$day = sprintf( $elems[0], '%2d' );
					$month = $elems[1];
					$year = $elems[2];

					if (( 0 < $year && $year < 20 )) {
						$year += 2000;
					}


					if (( 20 < $year && $year < 100 )) {
						$year += 1900;
					}

					$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );
				} 
else {
					$elems = explode( ' ', $value );

					if (count( $elems ) == 3) {
						$day = sprintf( $elems[0], '%2d' );
						$month = $elems[1];
						$year = $elems[2];
						settype( $day, 'integer' );
						settype( $year, 'integer' );

						if (( 0 <= $year && $year < 30 )) {
							$year += 2000;
						}


						if (( 30 <= $year && $year < 100 )) {
							$year += 1900;
						}

						$month = $this->_getMonthNumber( $month );

						if (0 < $month) {
							$ok = @checkdate( $month, $day, $year );

							if ($ok == true) {
								$value = sprintf( '%04d-%02d-%02d', $year, $month, $day );
							}
						}
					}
				}


				if ($value == '0000-00-00') {
					$value = '';
				}


				if ($value == NULL) {
					$value = '';
				}
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'TIME' )) {
				if ($value == '00:00:00') {
					$value = '';
				}
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'DOUBLE' )) {
				$value = round( $value, 2 );
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
				$value = uconvertmoneytointeger( $value );
				$this->$fld = (int)$value;
				settype( $this->$fld, 'integer' );
				return null;
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BLOB' )) {
				$value = base64_encode( $value );
			}

			$this->$fld = $value;
		}

		function getforhtml($fld) {
			if (isset( $this->$fld )) {
				if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BLOB' )) {
					$value = base64_decode( $this->$fld );
				} 
else {
					$value = $this->$fld;
				}
			} 
else {
				$value = '';
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'DATE' )) {
				if (( $value == '0000-00-00' || $value == '' )) {
					$value = '';
				} 
else {
					if (defined( 'DATE_FORMAT' )) {
						$format = DATE_FORMAT;
						$day = substr( $value, 8, 2 );
						$month = substr( $value, 5, 2 );
						$year = substr( $value, 0, 4 );
						$monthAlpha = $this->_getMonthName( $month );
						$value = str_replace( 'dd', $day, $format );
						$value = str_replace( 'mm', $month, $value );
						$value = str_replace( 'MMM', $monthAlpha, $value );
						$value = str_replace( 'yyyy', $year, $value );
					} 
else {
						$value = uformatsqldate( $value );
					}
				}
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'TIME' )) {
				if ($value == '00:00:00') {
					$value = '';
				}
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'DOUBLE' )) {
				$x = (double)$value;
				$value = sprintf( '%.2f', $x );
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
				$value = uformatmoney( $value );
			}


			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'BOOL' )) {
				if ($value == 1) {
					$out = 'checked';
				} 
else {
					$out = '';
				}

				$value = $out;
			}

			$value = $this->_formatForHTML( $value );
			return $value;
		}

		function getasmoneywithcommas($fld) {
			if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
				$value = $this->get( $fld );
				$value = uformatmoneywithcommas( $value );
			} 
else {
				trigger_error( 'not money so cant format ' . $fld, E_USER_ERROR );
			}

			return $value;
		}

		function getcheckboxforhtml($fld) {
			if (isset( $this->$fld )) {
				$value = $this->$fld;
			} 
else {
				$value = '';
			}

			$text = '';

			if ($value == 1) {
				$text = ' checked';
			}

			return $text;
		}

		function getselected($field, $value) {
			$x = $this->get( $field );

			if ($x == $value) {
				return ' selected';
			}

			return '';
		}

		function getradiochecked($field, $value) {
			$x = $this->get( $field );

			if ($x == $value) {
				return ' checked';
			}

			return '';
		}

		function getwholemumberswithcommas($fld) {
			if (isset( $this->$fld )) {
				$value = $this->$fld;
			} 
else {
				return '';
			}

			$text = number_format( $value, 0, '.', ',' );
			return $text;
		}

		function recordexists() {
			return $this->recordExists;
		}

		function trygettingrecord($code) {
			$table = $this->tableName;
			$keyField = $this->keyField;
			$q = '' . 'SELECT * FROM ' . $table . ' WHERE ' . $keyField . '=' . $code;
			$result = udbquery( $q );

			if ($result == false) {
				return false;
			}

			$num = udbnumberofrows( $result );

			if ($num == 0) {
				return false;
			}

			$this->_populateFromDatabase( $code, $table, $keyField );
			$this->recordExists = true;
			return true;
		}

		function setall($input) {
			if (!is_array( $input )) {
				trigger_error( '_setAll: arg not array', E_USER_ERROR );
			}

			reset( $input );

			while ($elem = each( $input )) {
				$key = $elem['key'];
				$value = $elem['value'];

				if (isset( $this->fieldNames[$key] ) == false) {
					continue;
				}


				if ($key == $this->keyField) {
					continue;
				}


				if (( isset( $this->fieldTypes[$key] ) && $this->fieldTypes[$key] == 'BOOL' )) {
					if (is_string( $value )) {
						if ($value == 'checked') {
							$value = 1;
						}


						if ($value == 'on') {
							$value = 1;
						}


						if ($value == 'off') {
							$value = 0;
						}
					}
				}

				$this->set( $key, $value );
			}

		}

		function clearall() {
			foreach ($this->fieldNames as $key => $value) {
				if ($key == $this->keyField) {
					continue;
				}

				$this->$key = '';
			}

		}

		function getallforhtml() {
			$values = array(  );
			reset( $this->fieldNames );

			while ($elem = each( $this->fieldNames )) {
				$key = $elem['key'];
				$value = $elem['value'];
				$tmp = $this->getForHTML( $key );
				$values[$key] = $tmp;
			}

			return $values;
		}

		function getall() {
			$values = array(  );
			reset( $this->fieldNames );

			while ($elem = each( $this->fieldNames )) {
				$key = $elem['key'];
				$value = $elem['value'];
				$tmp = $this->$key;
				$values[$key] = $tmp;
			}

			return $values;
		}

		function update() {
			if ($this->recordExists == false) {
				trigger_error( 'URecord: record not created so cant update', E_USER_ERROR );
			}


			if ($this->keyValue == null) {
				trigger_error( 'URecord: no key for update', E_USER_ERROR );
			}


			if ($this->handleConcurrency == true) {
				$ok = $this->_concurrencyOK(  );

				if ($ok == false) {
					return false;
				}

				$now = ugettimenow(  );
				$this->lastAccessTime = $now;
			}

			$this->_setUpdated(  );
			$q = '' . 'UPDATE  ' . $this->tableName . ' SET ';
			reset( $this->fieldNames );
			$done = 0;

			while ($fld = each( $this->fieldNames )) {
				$fldKey = $fld['key'];
				$fldValue = $fld['value'];

				if (( isset( $this->ignoreForUpdate[$fldKey] ) && $this->ignoreForUpdate[$fldKey] == true )) {
					continue;
				}


				if (!isset( $this->$fldKey )) {
					continue;
				}


				if (( isset( $this->fieldTypes[$fldKey] ) && $this->fieldTypes[$fldKey] == 'BLOB' )) {
					if ($fldValue == null) {
						continue;
					}
				}


				if ($this->$fldKey === null) {
					continue;
				}


				if (0 < $done) {
					$q .= ',';
				}

				$q .= $fldKey;
				++$done;
				$value = $this->$fldKey;

				if (( isset( $this->fieldTypes[$fldKey] ) && $this->fieldTypes[$fldKey] == 'BOOL' )) {
					if (is_string( $value )) {
						if ($value == 'on') {
							$value = 1;
						}


						if ($value == 'off') {
							$value = 0;
						}
					}
				}

				$val = udbmakefieldsafe( $value );
				$q .= '' . '=\'' . $val . '\'';
				++$done;
			}

			$q .= '' . ' WHERE ' . $this->keyField . '=' . $this->keyValue;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( 'URecord: cant do update' . udblasterror(  ) . '<br>' . $q, E_USER_ERROR );
			}

			$this->_populateFromDatabase( $this->keyValue, $this->tableName, $this->keyField );
			return true;
		}

		function delete() {
			if ($this->recordExists == false) {
				trigger_error( 'URecord: record not created so cant delete', E_USER_ERROR );
			}


			if ($this->keyValue == null) {
				trigger_error( 'URecord: no key for delete', E_USER_ERROR );
			}


			if ($this->handleConcurrency == true) {
				$ok = $this->_concurrencyOK(  );

				if ($ok == false) {
					return false;
				}
			}

			$q = '' . 'DELETE FROM  ' . $this->tableName;
			$q .= '' . ' WHERE ' . $this->keyField . '=' . $this->keyValue;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( 'URecord: cant do delete' . udblasterror(  ) . '<br>' . $q, E_USER_ERROR );
			}

			return true;
		}

		function insert($row = null) {
			if ($this->recordExists == true) {
				trigger_error( 'URecord: record exists so cant insert', E_USER_ERROR );
			}


			if ($row != null) {
				$this->_populateFromArray( $row );
			}


			if ($this->handleConcurrency == true) {
				$now = ugettimenow(  );
				$this->lastAccessTime = $now;

				if (!isset( $this->fieldNames['lastAccessTime'] )) {
					$this->fieldNames['lastAccessTime'] = 'lastAccessTime';
				}
			}

			$this->_setUpdated(  );
			$q = '' . 'INSERT INTO  ' . $this->tableName . ' (';

			if ($this->fieldNames != null) {
				reset( $this->fieldNames );
				$done = 0;

				while ($fld = each( $this->fieldNames )) {
					$fldKey = $fld['key'];
					$fldValue = $fld['value'];

					if (( isset( $this->ignoreForUpdate[$fldKey] ) && $this->ignoreForUpdate[$fldKey] == true )) {
						continue;
					}


					if (isset( $this->$fldKey ) == false) {
						continue;
					}


					if ($this->$fldKey === null) {
						continue;
					}


					if (0 < $done) {
						$q .= ',';
					}

					$q .= $fldKey;
					++$done;
				}
			}

			$q .= ') VALUES (';

			if ($this->fieldNames != null) {
				reset( $this->fieldNames );
				$done = 0;
				$num = count( $this->fieldNames );

				while ($fld = each( $this->fieldNames )) {
					$fldKey = $fld['key'];
					$fldValue = $fld['value'];

					if (( isset( $this->ignoreForUpdate[$fldKey] ) && $this->ignoreForUpdate[$fldKey] == true )) {
						continue;
					}


					if (isset( $this->$fldKey ) == false) {
						continue;
					}


					if ($this->$fldKey === null) {
						continue;
					}


					if (0 < $done) {
						$q .= ',';
					}

					++$done;
					$value = udbmakefieldsafe( $this->$fldKey );

					if (( isset( $this->fieldTypes[$fldKey] ) && $this->fieldTypes[$fldKey] == 'BOOL' )) {
						if (is_string( $value )) {
							if ($value == 'on') {
								$value = 1;
							}


							if ($value == 'off') {
								$value = 0;
							}
						}
					}

					$q .= '' . '\'' . $value . '\'';
				}
			}

			$q .= ')';
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( 'URecord: cant do insert' . udblasterror(  ) . '<br>' . $q, E_USER_ERROR );
			}

			$id = udbgetinsertid(  );
			$this->keyValue = $id;
			$keyName = $this->keyField;
			$this->$keyName = $id;
			$this->recordExists = true;
		}

		function refresh() {
			if ($this->recordExists == false) {
				trigger_error( 'URecord: record not created so cant update', E_USER_ERROR );
			}

			$table = $this->tableName;
			$keyField = $this->keyField;
			$code = $this->getKeyValue(  );
			$this->_populateFromDatabase( $code, $table, $keyField );
		}

		function makefieldsfromdatabase() {
			$fldlist = ugetlistoffields( $this->tableName );
			$this->fieldNames = $fldlist;
		}

		function setextrasql($sql) {
			if (!is_array( $this->sqlForExtras )) {
				$this->sqlForExtras = array(  );
			}

			$this->sqlForExtras[] = $sql;
		}

		function fetchextracolumns() {
			if (!is_array( $this->sqlForExtras )) {
				return null;
			}

			$key = $this->getKeyValue(  );
			foreach ($this->sqlForExtras as $k => $value) {
				$q = $value;
				$q = str_replace( 'CODE', $key, $q );
				$result = udbquery( $q );

				if ($result == false) {
					trigger_error( '' . 'URecord: cant do query for extra on ' . $this->tableName, E_USER_ERROR );
				}

				$row = udbgetrow( $result );

				if ($row == null) {
					trigger_error( '' . 'URecord: cant get result for extra on table ' . $table . ', query=' . $q, E_USER_ERROR );
				}

				foreach ($row as $fld => $value) {
					$this->fieldNames[$fld] = $fld;
					$this->ignoreForUpdate[$fld] = true;
					$value = $row[$fld];

					if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
						$this->$fld = intval( stripcslashes( $value ) );
						settype( $this->$fld, 'integer' );
					} 
else {
						$this->$fld = stripcslashes( $value );
					}


					if ($fld == $this->keyField) {
						$this->keyValue = stripslashes( $value );
						continue;
					}
				}

				udbfreeresult( $result );
			}

		}

		function _formatforhtml($input) {
			$x = htmlspecialchars( stripslashes( $input ), ENT_QUOTES );
			return $x;
		}

		function _populatefromdatabase($code, $table, $keyField) {
			$this->tableName = $table;
			$this->keyField = $keyField;
			$q = '' . 'SELECT * FROM ' . $table . ' WHERE ' . $keyField . '=' . $code;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( '' . 'URecord: cant do query for ' . $this->tableName, E_USER_ERROR );
			}

			$row = udbgetrow( $result );

			if ($row == null) {
				trigger_error( '' . 'URecord: cant get result for table ' . $table . ', query=' . $q, E_USER_ERROR );
			}

			$this->fieldNames = array(  );
			foreach ($row as $fld => $value) {
				$this->fieldNames[$fld] = $fld;
				$value = $row[$fld];

				if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
					$this->$fld = intval( stripcslashes( $value ) );
					settype( $this->$fld, 'integer' );
				} 
else {
					$this->$fld = stripcslashes( $value );
				}


				if ($fld == $this->keyField) {
					$this->keyValue = stripslashes( $value );
					continue;
				}
			}

			udbfreeresult( $result );
		}

		function _populatefromarray($row) {
			reset( $row );
			$this->fieldNames = array(  );
			foreach ($row as $fld => $value) {
				$this->fieldNames[$fld] = $fld;
				$value = $row[$fld];

				if (( isset( $this->fieldTypes[$fld] ) && $this->fieldTypes[$fld] == 'MONEY' )) {
					$this->$fld = intval( stripcslashes( $value ) );
					settype( $this->$fld, 'integer' );
				} 
else {
					$this->$fld = stripcslashes( $value );
				}


				if ($fld == $this->keyField) {
					$this->keyValue = $value;
					continue;
				}
			}

		}

		function _getfieldsfromdatabase() {
			$q = '' . 'SHOW COLUMNS FROM ' . $this->tableName;
			$result = udbquery( $q );

			if ($result == null) {
				return null;
			}


			while ($row = udbgetrow( $result )) {
				$fld = $row['Field'];

				if ($fld == $this->keyField) {
					continue;
				}

				$this->fieldNames[$fld] = $fld;
				$type = $row['Type'];
				$ourType = '';

				if (substr( $type, 0, 3 ) == 'int') {
					$ourType = 'INT';
				}


				if (substr( $type, 0, 8 ) == 'longblob') {
					$ourType = 'BLOB';
				}


				if (substr( $type, 0, 7 ) == 'tinyint') {
					$ourType = 'BOOL';
				}


				if (substr( $type, 0, 6 ) == 'bigint') {
					$ourType = 'MONEY';
				}


				if ($ourType != '') {
					$this->fieldTypes[$fld] = $ourType;
				}

				$this->$fld = '';
			}

			udbfreeresult( $result );
		}

		function _getmonthname($month) {
			$name = '';
			reset( $this->months );
			foreach ($this->months as $key => $value) {
				if ($value == $month) {
					$name = $key;
					break;
				}
			}

			return $name;
		}

		function _concurrencyok() {
			if (!isset( $this->lastAccessTime )) {
				trigger_error( '' . 'no lastAccessTime Field in ' . $this->tableName, E_USER_ERROR );
			}

			$q = '' . 'SELECT lastAccessTime FROM ' . $this->tableName . ' WHERE ' . $this->keyField . ' = ' . $this->keyValue;
			$result = udbquery( $q );

			if ($result == false) {
				trigger_error( '' . 'cant reread ' . $this->tableName . ' ' . udblasterror(  ), E_USER_ERROR );
			}

			$row = udbgetrow( $result );

			if ($row == null) {
				trigger_error( 'cant get current' . udblasterror(  ), E_USER_ERROR );
			}

			$last = $row['lastAccessTime'];

			if ($last != $this->lastAccessTime) {
				$this->refresh(  );
				udbfreeresult( $result );
				return false;
			}

			udbfreeresult( $result );
			return true;
		}

		function _setupdatedbyfield($fld) {
			$this->_fldForUpdatedBy = $fld;
		}

		function _setupdatedwhenfield($fld) {
			$this->_fldForUpdatedWhen = $fld;
		}

		function _setupdated() {
			global $user;

			if (( isset( $this->_fldForUpdatedBy ) && is_a( $user, 'User' ) )) {
				$fld = $this->_fldForUpdatedBy;
				$code = $user->getKeyValue(  );
				$this->set( $fld, $code );
			}


			if (isset( $this->_fldForUpdatedWhen )) {
				$fld = $this->_fldForUpdatedWhen;
				$this->set( $fld, ugettimenow(  ) );
			}

		}

		function _getmonthnumber($month) {
			$mthNum = 0;
			foreach ($this->months as $mth => $num) {
				if (strcasecmp( $month, $mth ) == 0) {
					$mthNum = $num;
					break;
				}
			}

			return $mthNum;
		}
	}

?>
