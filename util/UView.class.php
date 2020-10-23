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

	class uview {
		var $htmlFile = null;
		var $htmlAfterDisplay = null;
		var $fields = null;
		var $fieldTypes = null;
		var $model = null;
		var $needXMLEntities = false;
		var $allowEdit = null;
		var $fieldsEnabled = array(  );
		var $allowExit = null;
		var $updateFieldsWhenNoEdit = null;

		function uview($htmlFile = null) {
			$this->fields = array(  );
			$this->fieldTypes = array(  );

			if ($htmlFile != null) {
				$this->setHTML( $htmlFile );
			}

			$this->setMessage( '   ' );
			$this->htmlAfterDisplay = null;
			$this->setAllowEditing( true );
			$this->setAllowExiting( true );
			$this->updateFieldsWhenNoEdit = true;
		}

		function setmodel($model) {
			$this->model = &$model;

			$this->_getAllFromModel(  );
		}

		function setfieldtype($fieldName, $type) {
			$this->fieldTypes[$fieldName] = $type;
		}

		function getfieldvalue($fieldName) {
			if (array_key_exists( $fieldName, $this->fields ) == false) {
				trigger_error( '' . 'field \'' . $fieldName . '\' not defined', E_USER_ERROR );
			}

			$fld = $this->fields[$fieldName];
			$val = $fld->getValue(  );
			return $val;
		}

		function process() {
			$this->_getAllFromModel(  );
			$this->doEachCall(  );
			$this->parseAll(  );
			$this->display(  );
			$this->doAfterDisplay(  );
			$this->setMessage( '' );
		}

		function parseall() {
			if ($this->original == null) {
				trigger_error( 'cant do this until html set', E_USER_ERROR );
			}


			if (( ( defined( 'DEBUG_MODE' ) && DEBUG_MODE == true ) && $this->htmlFile != null )) {
				$this->setHTML( $this->htmlFile );
			}

			$text = $this->original;
			$this->text = $this->parse( $text );
		}

		function parse($text) {
			if ($this->original == null) {
				trigger_error( 'cant do this until html set', E_USER_ERROR );
			}

			$a = substr( $text, 0 - 1, 1 );
			$c = ord( $a );
			$b = strlen( $text );

			if ($c == 10) {
				$text = substr( $text, 0, $b - 1 );
			}

			$lines = explode( '
', $text );
			$output = '';
			$sectionText = '';
			$section = null;
			foreach ($lines as $line) {
				$found = 0;
				$posn = strstr( $line, 'BEGIN' );

				if ($posn !== false) {
					$found = preg_match( '/(<!-- BEGIN)(.*)( -->)/', $line, $parts );
				}


				if ($found == 1) {
					$name = trim( $parts[2] );

					if ($section != null) {
						$sectionText .= $line . '
';
						continue;
					}

					$section = $name;
					$output .= $line . '
';
					$sectionText = '';
					continue;
				}

				$found = 0;
				$posn = strstr( $line, 'END' );

				if ($posn !== false) {
					$found = preg_match( '/(<!-- END)(.*)( -->)/', $line, $parts );
				}


				if ($found == 1) {
					$name = trim( $parts[2] );

					if ($section != $name) {
						$sectionText .= $line . '
';
						continue;
					}

					$arg = null;
					$left = strpos( $section, '(' );
					$right = strpos( $section, ')' );

					if (( ( $left !== false && $right !== false ) && 1 < $right - $left )) {
						$len = $right - $left - 1;
						$arg = substr( $section, $left + 1, $len );
						$arg = str_replace( '\"', '', $arg );
						$arg = str_replace( '\'', '', $arg );
						$section = substr( $section, 0, $left );
					}


					if (method_exists( $this, $section ) == true) {
						if ($arg == null) {
							$newSectionText = $this->$section( $sectionText );
						} 
else {
							$newSectionText = $this->$section( $sectionText, $arg );
						}

						$output .= $newSectionText;
						$output .= $line . '
';
					} 
else {
						trigger_error( '' . 'cant get function ' . $name . ' BEGIN in ' . $this->htmlFile, E_USER_ERROR );
					}

					$section = null;
					continue;
				}

				$found = 0;
				$posn = strstr( $line, 'INCLUDE' );

				if ($posn !== false) {
					$found = preg_match( '/(<!-- INCLUDE)(.*)( -->)/', $line, $parts );
				}


				if ($found == 1) {
					$htmlFile = trim( $parts[2] );
					$htmlFile = $this->_parseLine( $htmlFile );
					$includeFile = TEMPLATE_PATH . $htmlFile;

					if (!file_exists( $includeFile )) {
						trigger_error( '' . 'UView.class: cant find include file ' . $htmlFile, E_USER_ERROR );
					}

					$text = file_get_contents( $includeFile );
					$output .= $this->parse( $text );
				}


				if ($section != null) {
					$sectionText .= $line . '
';
					continue;
				}

				$newLine = $this->_parseLine( $line );
				$output .= $newLine . '
';
			}

			return $output;
		}

		function display() {
			global $startTime;

			if ($this->original == null) {
				trigger_error( 'cant do this until html set', E_USER_ERROR );
			}

			$this->beenDisplayed = true;
			echo $this->text;

			if ($this->htmlAfterDisplay != null) {
				$this->setHTML( $this->htmlAfterDisplay );
				$this->htmlAfterDisplay = null;
			}

		}

		function sethtmlfromtext($html) {
			$this->text = $html;
			$this->_preProcessHTML(  );
		}

		function sethtml($htmlFile) {
			$this->htmlFile = $htmlFile;
			$actualFile = TEMPLATE_PATH . $htmlFile;

			if (!file_exists( $actualFile )) {
				trigger_error( '' . 'UView.class: cant find template ' . $htmlFile, E_USER_ERROR );
			}

			$this->text = file_get_contents( $actualFile );
			$this->_preProcessHTML(  );
		}

		function sethtmltobeusedafterdisplay($htmlFile) {
			$this->htmlAfterDisplay = $htmlFile;
		}

		function setmessage($messg) {
			if (( array_key_exists( 'messg', $this->fields ) == true && $this->fields['messg'] != null )) {
				$fld = &$this->fields['messg'];

				$fld->setValue( $messg );
				return null;
			}

			$this->fields['messg'] = new UField( 'text', $messg );
		}

		function setfield($fld, $value) {
			$field = new UField( 'text', $value );
			$this->fields[$fld] = $field;
		}

		function doafterdisplay() {
		}

		function wheneditallowed($text) {
			$out = '';

			if ($this->allowEdit == true) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function wheneditnotallowed($text) {
			$out = '';

			if ($this->allowEdit == false) {
				$out = $this->parse( $text );
			}

			return $out;
		}

		function setallowediting($ok) {
			if (!is_bool( $ok )) {
				trigger_error( 'setAllowEDiting arg not bool', E_USER_ERROR );
			}

			$this->allowEdit = $ok;

			if ($ok == false) {
				$this->setField( 'allowEdit', 'disabled' );
				$this->setField( 'allowEditText', 'readonly' );
				return null;
			}

			$this->setField( 'allowEdit', '' );
			$this->setField( 'allowEditText', '' );
		}

		function setfieldallowediting($fldName, $ok) {
			$this->fieldsEnabled[$fldName] = $ok;
		}

		function setallfieldsallowediting($ok) {
			if (( $ok != true && $ok != false )) {
				trigger_error( '' . 'must be true or false not ' . $ok, E_USER_ERROR );
			}

			$this->fieldsEnabled = $this->fields;
			reset( $this->fieldsEnabled );

			while ($elem = each( $this->fieldsEnabled )) {
				$key = $elem['key'];
				$this->fieldsEnabled[$key] = $ok;
			}

		}

		function setupdatefieldswhennoedit($allow) {
			$this->updateFieldsWhenNoEdit = $allow;
		}

		function allowedit($fldName) {
			if ($this->allowEdit == false) {
				return 'disabled';
			}


			if (isset( $this->fieldsEnabled[$fldName] )) {
				$ok = $this->fieldsEnabled[$fldName];

				if ($ok == 0) {
					return 'disabled';
				}
			}

			return '';
		}

		function allowedittext($fldName) {
			if ($this->allowEdit == false) {
				return 'readonly';
			}


			if (isset( $this->fieldsEnabled[$fldName] )) {
				$ok = $this->fieldsEnabled[$fldName];

				if ($ok == 0) {
					return 'readonly';
				}
			}

			return '';
		}

		function setallowexiting($ok) {
			if (!is_bool( $ok )) {
				trigger_error( 'setAllowExiting arg not bool', E_USER_ERROR );
			}

			$this->allowExit = $ok;
		}

		function getallowexiting() {
			return $this->allowExit;
		}

		function getallowediting() {
			return $this->allowEdit;
		}

		function doeachcall() {
		}

		function _parseline($line) {
			$foundArray = $this->_findTags( $line );

			if (count( $foundArray ) == 0) {
				return $line;
			}

			foreach ($foundArray as $key => $value) {
				if (!array_key_exists( $value, $this->fields )) {
					if (substr( $value, 0 - 1 ) != ')') {
						trigger_error( '' . 'when parsing html, we havent got variable ' . $value . ' in line ' . $line, E_USER_ERROR );
					}
				}


				if (substr( $value, 0 - 2 ) == '()') {
					$functionName = substr( $value, 0, 0 - 2 );

					if (method_exists( $this, $functionName ) == false) {
						trigger_error( '' . 'UView: method ' . $functionName . ' doesnt exist in line ' . $line, E_USER_ERROR );
					}

					$text = $this->$functionName(  );
				}

				$functionDone = false;

				if (substr( $value, 0 - 1 ) == ')') {
					$start = strpos( $value, '(' );
					$end = strpos( $value, ')' );

					if (( $start !== false && $end !== false )) {
						++$start;
						--$end;
						$len = $end - $start + 1;

						if (0 < $len) {
							$arg = substr( $value, $start, $len );
						} 
else {
							$arg = '';
						}

						$arg = str_replace( '\'', '', $arg );
						$arg = str_replace( '"', '', $arg );
					} 
else {
						$start = 0;
						$arg = '';
					}

					$functionName = substr( $value, 0, $start - 1 );

					if (method_exists( $this, $functionName ) == false) {
						trigger_error( '' . 'UView: method \'' . $functionName . '\' doesnt exist  in line ' . $line, E_USER_ERROR );
					}

					$text = $this->$functionName( $arg );
					$functionDone = true;
				}


				if ($functionDone == false) {
					$posn = strpos( $value, ' ' );

					if ($posn !== false) {
						continue;
					}

					$text = $this->_getHTML( $value );
				}

				$line = str_replace( '{' . $value . '}', $text, $line );
			}

			return $line;
		}

		function _findtags($text) {
			$MAX_TAG_SIZE = 40;
			$found = array(  );
			$len = strlen( $text );
			$tag = null;
			$bracketsFound = array(  );
			$offset = 0;

			while (true) {
				$posn = strpos( $text, '{', $offset );

				if ($posn === false) {
					break;
				}

				$bracketsFound[] = $posn;
				$offset = $posn + 1;
			}

			$brackets = count( $bracketsFound );

			if ($brackets == 0) {
				return $found;
			}

			$elem = 0;

			while ($elem < $brackets) {
				$start = $bracketsFound[$elem] + 1;
				$end = $start + $MAX_TAG_SIZE;
				$tag = '';
				$endFound = false;
				$i = $start;

				while ($i < $end) {
					$char = substr( $text, $i, 1 );

					if ($char == '}') {
						$endFound = true;
						break;
					}

					$tag .= $char;
					++$i;
				}

				$len = strlen( $tag );

				if (( ( 0 < $len && $len < $MAX_TAG_SIZE ) && $endFound == true )) {
					$found[] = $tag;
				}

				++$elem;
			}

			return $found;
		}

		function _havewegotfield($value) {
			$ok = array_key_exists( $value, $this->fields );
			return $ok;
		}

		function _preprocesshtml() {
			$this->text = str_replace( '', '', $this->text );
			$this->original = $this->text;
		}

		function _gethtml($fieldName) {
			$fld = $this->_get( $fieldName );

			if ($fld == null) {
				return '';
			}

			$val = $fld->getValue(  );
			$type = $fld->getType(  );
			$htmlValue = stripslashes( $val );

			if ($this->needXMLEntities == true) {
				$htmlValue = uxmlentities( $htmlValue );
			}


			if (!isset( $type )) {
				return $htmlValue;
			}


			if (( ( $type == 'checkbox' || $type == 'checked' ) || $type == 'bool' )) {
				$a = 0;

				if ('on' == (bool)0) {
					$a += 2;
				}


				if (( ( is_numeric( $val ) && $val == 1 ) || ( !is_numeric( $val ) && ( $val == 'on' || $val == 'checked' ) ) )) {
					$htmlValue = 'checked';
				} 
else {
					$htmlValue = '';
				}
			}


			if (( $type == 'MONEY' || $type == 'money' )) {
				$htmlValue = uformatmoney( $val );
			}

			return $htmlValue;
		}

		function _get($fieldName) {
			if (array_key_exists( $fieldName, $this->fields ) == false) {
				trigger_error( '' . 'field ' . $fieldName . ' not defined', E_USER_ERROR );
			}

			return $this->fields[$fieldName];
		}

		function _getallfrommodel() {
			$messg = $this->_get( 'messg' );
			$this->fields = $this->model->getAllFields(  );
			$this->fields['messg'] = $messg;

			if (isset( $this->fields['_messg'] )) {
				$fld = $this->fields['_messg'];

				if (0 < strlen( trim( $fld->getValue(  ) ) )) {
					$x = $this->fields['_messg']->getValue(  );
					$this->fields['messg']->setValue( $x );
					$fld->setValue( '' );
					$this->fields['_messg'] = $fld;
				}
			}

		}
	}

?>