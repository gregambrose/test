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

	class uadmin {
		var $table = null;
		var $docmRoot = null;
		var $keyName = null;
		var $descField = null;
		var $sequenceField = null;
		var $relations = null;
		var $message = null;
		var $heading = null;
		var $className = null;
		var $selectCondition = null;
		var $orderBy = null;

		function uadmin($docmRoot, $table, $keyName, $descField, $sequenceField) {
			$this->docmRoot = $docmRoot;
			$this->table = $table;
			$this->keyName = $keyName;
			$this->descField = $descField;
			$this->sequenceField = $sequenceField;
			$this->conditions = array(  );
			$this->relations = array(  );
			$this->message = '';
			$this->heading = '';
		}

		function setclassname($name) {
			$this->className = $name;
		}

		function setorderby($text) {
			$this->orderBy = $text;
		}

		function setselectcondition($condition) {
			$this->selectCondition = $condition;
		}

		function setheading($heading) {
			$this->heading = $heading;
		}

		function setmessage($messg) {
			$this->message = $messg;
		}

		function dolistofentries() {
			require( '' . $this->docmRoot . '.html' );
		}

		function listentries($html) {
			$q = '' . 'SELECT * FROM ' . $this->table;

			if ($this->selectCondition != null) {
				$q .= ' ' . $this->selectCondition;
			}


			if ($this->orderBy != null) {
				$q .= ' ' . $this->orderBy;
			} 
else {
				if ($this->sequenceField != null) {
					$q .= '' . ' ORDER BY ' . $this->sequenceField;
				}
			}

			$result = mysql_query( $q );

			if ($result == null) {
				trigger_error( '' . 'UAdmin.class: cant list ' . $this->table . ' ' . mysql_error(  ) . $q, E_USER_ERROR );
			}


			while ($row = mysql_fetch_assoc( $result )) {
				if ($this->className == null) {
					$item = new URecord( $row, $this->table, $this->keyName );
				} 
else {
					$item = new $this->className( $row, $this->table, $this->keyName );
				}


				if ($html != null) {
					require( $html );
					continue;
				}

				require( $this->docmRoot . 'item.html' );
			}

			mysql_free_result( $result );
		}

		function getitem($keyCode) {
			if ($this->className == null) {
				$item = new URecord( $keyCode, $this->table, $this->keyName );
			} 
else {
				$item = new $this->className( $keyCode, $this->table, $this->keyName );
			}

			return $item;
		}

		function amenditem($keyCode) {
			if ($this->className == null) {
				$item = new URecord( $keyCode, $this->table, $this->keyName );
			} 
else {
				$item = new $this->className( $keyCode, $this->table, $this->keyName );
			}

			$message = $this->message;
			$html = $this->docmRoot . 'amend.html';
			require( $html );
		}

		function update($keyCode, $input) {
			$item = $this->getItem( $keyCode );
			$item->setAll( $input );
			$item->update(  );
			$message = $this->message;
			$html = $this->docmRoot . 'amend.html';
			require( $html );
		}

		function delete($keyCode) {
			$item = $this->getItem( $keyCode );

			if ($this->_okToDelete( $keyCode ) == false) {
				$this->message = 'sorry this record can\'t be deleted';
				$message = $this->message;
				$html = $this->docmRoot . 'amend.html';
				require( $html );
				return null;
			}

			$item->delete(  );
			$message = $this->message;
			require( '' . $this->docmRoot . '.html' );
		}

		function insert($displayPageWhenDone) {
			if ($this->className == null) {
				$item = new URecord( null, $this->table, $this->keyName );
			} 
else {
				$item = new $this->className( null, $this->table, $this->keyName );
			}

			$item->insert( null );
			$this->_setAllSequenceNumber(  );
			$message = $this->message;
			$html = $this->docmRoot . 'amend.html';

			if ($displayPageWhenDone == true) {
				require( $html );
			}

			$code = $item->getKeyValue(  );
			return $code;
		}

		function addrelation($rel) {
			$this->relations[] = $rel;
		}

		function moveup($keyCode) {
			$codes = $this->_setAllSequenceNumber(  );
			$prevKey = 0 - 1;
			$nextKey = 0 - 1;
			$found = false;
			reset( $codes );

			while ($elem = each( $codes )) {
				$value = $elem['value'];

				if ($found == true) {
					$nextKey = $value;
					break;
				}


				if ($value == $keyCode) {
					$found = true;
					continue;
				}

				$prevKey = $value;
			}


			if ($found == false) {
				trigger_error( 'UAdmin.class: cant find entry in array', E_USER_ERROR );
			}


			if ($prevKey < 0) {
				return null;
			}

			$prev = new URecord( $prevKey, $this->table, $this->keyName );
			$prevSequence = $prev->get( $this->sequenceField );
			$curr = new URecord( $keyCode, $this->table, $this->keyName );
			$currSequence = $curr->get( $this->sequenceField );
			$prev->set( $this->sequenceField, $currSequence );
			$prev->update(  );
			$curr->set( $this->sequenceField, $prevSequence );
			$curr->update(  );
		}

		function movedown($keyCode) {
			$codes = $this->_setAllSequenceNumber(  );
			$prevKey = 0 - 1;
			$nextKey = 0 - 1;
			$found = false;
			reset( $codes );

			while ($elem = each( $codes )) {
				$value = $elem['value'];

				if ($found == true) {
					$nextKey = $value;
					break;
				}


				if ($value == $keyCode) {
					$found = true;
					continue;
				}

				$prevKey = $value;
			}


			if ($found == false) {
				trigger_error( 'UAdmin.class: cant find entry in array', E_USER_ERROR );
			}


			if ($nextKey < 0) {
				return null;
			}

			$next = new URecord( $nextKey, $this->table, $this->keyName );
			$nextSequence = $next->get( $this->sequenceField );
			$curr = new URecord( $keyCode, $this->table, $this->keyName );
			$currSequence = $curr->get( $this->sequenceField );
			$next->set( $this->sequenceField, $currSequence );
			$next->update(  );
			$curr->set( $this->sequenceField, $nextSequence );
			$curr->update(  );
		}

		function _setallsequencenumber() {
			if ($this->sequenceField == null) {
				return null;
			}

			$codes = array(  );
			$q = 'SELECT ' . $this->keyName . ' FROM ' . $this->table;

			if ($this->selectCondition != null) {
				$q .= ' ' . $this->selectCondition;
			}

			$q .= ' ORDER BY ' . $this->sequenceField;
			$result = mysql_query( $q );

			if ($result == null) {
				trigger_error( 'UAdmin.class: cant read all for sequence', E_USER_ERROR );
			}

			$sequence = 1;

			while ($row = mysql_fetch_assoc( $result )) {
				$key = $row[$this->keyName];
				$codes[] = $key;
				$item = new URecord( $key, $this->table, $this->keyName );
				$item->set( $this->sequenceField, $sequence );
				$item->update(  );
				++$sequence;
			}

			mysql_free_result( $result );
			return $codes;
		}

		function _oktodelete($keyCode) {
			$canDelete = true;
			reset( $this->relations );

			while ($elem = each( $this->relations )) {
				$key = $elem['key'];
				$value = $elem['value'];
				$q = str_replace( 'KEY', $keyCode, $value );
				$result = mysql_query( $q );

				if ($result == null) {
					trigger_error( '' . 'UAdmin.class: can\'t run user defined sql ' . $q, E_USER_ERROR );
				}

				$num = mysql_affected_rows(  );

				if (0 < $num) {
					mysql_free_result( $result );
					$canDelete = false;
					break;
				}

				mysql_free_result( $result );
			}

			return $canDelete;
		}
	}

?>