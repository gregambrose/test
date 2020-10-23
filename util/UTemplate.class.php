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

	class utemplate {
		var $htmlFile = null;
		var $text = null;
		var $original = null;
		var $processesToCall = null;
		var $oneOffProcessToCall = null;
		var $fields = null;
		var $addedFields = null;
		var $fieldTypes = null;
		var $beenDisplayed = null;
		var $allowEdit = null;
		var $updateFieldsWhenNoEdit = null;
		var $fieldsEnabled = array(  );
		var $allowExit = null;
		var $needXMLEntities = false;
		var $moneyShouldHaveCommas = false;

		function utemplate($htmlFile) {
			$this->fields = array(  );
			$this->processesToCall = array(  );
			$this->addedFields = array(  );
			$this->beenDisplayed = false;
			$this->setProcess( 'uHandleLinks', 'link' );
			$this->setProcess( 'whenEditRequested', 'edit' );
			$this->addField( 'allowEdit' );
			$this->addField( 'allowEditText' );
			$this->addField( 'lastUpdateBy' );
			$this->addField( 'lastUpdatedOn' );
			$this->addField( 'helpPage' );
			$this->setAllowEditing( true );
			$this->setAllowExiting( true );
			$this->updateFieldsWhenNoEdit = true;

			if ($htmlFile != null) {
				$this->setHTML( $htmlFile );
			}

		}

		function process() {
			$this->fields['message'] = '';

			if (isset( $this->preMessage )) {
				$this->set( 'message', $this->preMessage );
				unset( $this[preMessage] );
			}

			$input = array_merge( $_GET, $_POST );

			if (method_exists( $this, '_doBeforeAnyProcessing' ) == true) {
				$wasDone = $this->_doBeforeAnyProcessing( $input );

				if ($wasDone == true) {
					return null;
				}
			}


			if (( $this->updateFieldsWhenNoEdit == true || $this->allowEdit == true )) {
				$this->setAll( $input );
			}

			$done = 0;
			foreach ($this->processesToCall as $inType => $function) {
				if (isset( $input[$inType] ) == true) {
					if (method_exists( $this, $function ) == true) {
						$wasDone = $this->$function( $this, $input );
					} 
else {
						$wasDone = $function( $this, $input );
					}


					if ($wasDone == true) {
						++$done;
						continue;
					}

					continue;
				}
			}


			if (0 < $done) {
				return null;
			}


			if (isset( $this->oneOffProcessToCall ) == true) {
				$function = $this->oneOffProcessToCall;

				if (method_exists( $this, $function ) == true) {
					$wasDone = $this->$function( $this, $input );
				} 
else {
					$wasDone = $function( $this, $input );
				}


				if ($wasDone == true) {
					return null;
				}
			}

			$this->parseAll(  );
			$this->display(  );
		}

		function setprocess($function, $input) {
			$this->processesToCall[$input] = $function;
		}

		function setoneofffunctiontocall($function) {
			$this->oneOffProcessToCall = $function;
		}

		function wheneditrequested($template, $input) {
			$this->setAllowEditing( true );
			$this->setAllowExiting( false );
			$this->setMessage( '' );
			return false;
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
					$includeFile = TEMPLATE_PATH . $htmlFile;

					if (!file_exists( $includeFile )) {
						trigger_error( '' . 'UTemplate.class: cant find include file ' . $htmlFile, E_USER_ERROR );
					}

					$text = file_get_contents( $actualFile );
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

			if (method_exists( $this, '_doAfterDisplayingPage' )) {
				$this->_doAfterDisplayingPage(  );
			}


			if (( defined( 'LOG_PROCESS_TIME' ) && LOG_PROCESS_TIME == true )) {
				$endTime = ugetmicrotime(  );
				$time = $endTime - $startTime;
				$script = $_SERVER['PHP_SELF'];
				trigger_error( '' . 'process time was ' . $time . ' for ' . $script, E_USER_NOTICE );
			}

		}

		function getoutput() {
			return $this->text;
		}

		function setall($input) {
			foreach ($this->fields as $key => $value) {
				if (isset( $input[$key] )) {
					$this->fields[$key] = $input[$key];
					continue;
				}
			}

			$this->setSpecificFields( $input );
		}

		function setallincludingnew($input) {
			foreach ($input as $key => $value) {
				$this->fields[$key] = $input[$key];
			}

			$this->setSpecificFields( $input );
		}

		function setspecificfields() {
		}

		function sethtmlfromtext($html) {
			$this->text = $html;
			$this->_preProcessHTML(  );
		}

		function sethtml($htmlFile) {
			$this->htmlFile = $htmlFile;
			$actualFile = TEMPLATE_PATH . $htmlFile;

			if (!file_exists( $actualFile )) {
				trigger_error( '' . 'UTemplate.class: cant find template ' . $htmlFile, E_USER_ERROR );
			}

			$this->text = file_get_contents( $actualFile );
			$this->_preProcessHTML(  );
		}

		function set($field, $value) {
			$this->fields[$field] = $value;
		}

		function setforhtml($field, $value) {
			$value = htmlentities( $value );
			$this->set( $field, $value );
		}

		function setparseforxml() {
			$this->needXMLEntities = true;
		}

		function setmessagebeforeprocess($value) {
			$this->preMessage = $value;
		}

		function setmessage($value) {
			$this->set( 'message', $value );
		}

		function setheader($value) {
			$this->set( 'header', $value );
			$this->set( 'systemName', $value );
		}

		function sethelppage($page) {
			$this->set( 'helpPage', $page );
		}

		function setfieldtype($field, $type) {
			$this->fieldTypes[$field] = $type;
		}

		function setallowediting($ok) {
			if (!is_bool( $ok )) {
				trigger_error( 'setAllowEDiting arg not bool', E_USER_ERROR );
			}

			$this->allowEdit = $ok;

			if ($ok == false) {
				$this->set( 'allowEdit', 'disabled' );
				$this->set( 'allowEditText', 'readonly' );
				return null;
			}

			$this->set( 'allowEdit', '' );
			$this->set( 'allowEditText', '' );
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

		function setmoneyshouldhavecommas($x) {
			$this->moneyShouldHaveCommas = $x;
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

		function get($field) {
			if (!isset( $this->fields[$field] )) {
				return null;
			}

			return $this->fields[$field];
		}

		function getmoneyaspennies($field) {
			if (!isset( $this->fields[$field] )) {
				return null;
			}

			$amt = $this->fields[$field];
			$pennies = uconvertmoneytointeger( $amt );
			return $pennies;
		}

		function getsqldate($field) {
			if ($this->fieldTypes[$field] != 'DATE') {
				trigger_error( '' . 'field type note date - ' . $field, E_USER_ERROR );
			}


			if (!isset( $this->fields[$field] )) {
				return null;
			}

			$date = $this->fields[$field];
			$sql = umakesqldate2( $date );
			return $sql;
		}

		function gethtml($field) {
			$val = $this->get( $field );
			$htmlValue = stripslashes( $val );

			if ($this->needXMLEntities == true) {
				$htmlValue = uxmlentities( $htmlValue );
			}


			if (!isset( $this->fieldTypes[$field] )) {
				return $htmlValue;
			}

			$type = $this->fieldTypes[$field];

			if ($type == 'checked') {
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


			if ($type == 'MONEY') {
				if ($this->moneyShouldHaveCommas == false) {
					$htmlValue = uformatmoney( $val );
				} 
else {
					$htmlValue = uformatmoneywithcommas( $val );
				}
			}

			return $htmlValue;
		}

		function getfields() {
			return $this->fields;
		}

		function getall() {
			return $this->fields;
		}

		function clearall() {
			reset( $this->fields );
			foreach ($this->fields as $key => $value) {
				if ($key == 'sn') {
					continue;
				}


				if ($key == 'sessionName') {
					continue;
				}


				if ($key == 'systemName') {
					continue;
				}


				if ($key == 'htmlSessionName') {
					continue;
				}


				if ($key == 'SCRIPT_NAME') {
					continue;
				}


				if ($key == 'SITE_COLOUR') {
					continue;
				}


				if ($key == 'SITE_WARNING') {
					continue;
				}


				if ($key == 'copyright') {
					continue;
				}

				$this->fields[$key] = null;
			}

		}

		function addfield($field) {
			$this->set( $field, null );
			$this->addedFields[$field] = null;
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

		function _parseline($line) {
			$foundArray = $this->_findTags( $line );

			if (count( $foundArray ) == 0) {
				return $line;
			}

			foreach ($foundArray as $key => $value) {
				if (!array_key_exists( $value, $this->fields )) {
					continue;
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
						trigger_error( '' . 'UTemplate: method \'' . $functionName . '\' doesnt exist', E_USER_ERROR );
					}

					$text = $this->$functionName( $arg );
					$functionDone = true;
				}


				if ($functionDone == false) {
					$posn = strpos( $value, ' ' );

					if ($posn !== false) {
						continue;
					}

					$text = $this->getHTML( $value );
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
				$i = $start;

				while ($i < $end) {
					$char = substr( $text, $i, 1 );

					if ($char == '}') {
						break;
					}

					$tag .= $char;
					++$i;
				}

				$len = strlen( $tag );

				if (( 0 < $len && $len < $MAX_TAG_SIZE )) {
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

			if (defined( 'SITE_ROOT_URL' )) {
				$base = SITE_ROOT_URL;
				$this->text = str_replace( '{HTMLBASE}', '' . '--> <BASE href=\'' . $base . '\'> <!-- ', $this->text );
				$this->text = str_replace( '{SITE_ROOT_URL}', SITE_ROOT_URL, $this->text );
			}


			if (defined( 'SITE_URL' )) {
				$this->text = str_replace( '{SITE_URL}', SITE_URL, $this->text );
			}


			if (defined( 'THIS_SCRIPT' )) {
				$this->text = str_replace( '{THIS_SCRIPT}', THIS_SCRIPT, $this->text );
			}

			$this->original = $this->text;
			$tags = $this->_findTags( $this->text );
			$oldFields = $this->fields;
			$this->fields = array(  );
			foreach ($tags as $key => $value) {
				$this->fields[$value] = null;
			}

			$this->fields = array_merge( $this->fields, $this->addedFields );
			foreach ($oldFields as $key => $value) {
				if (array_key_exists( $key, $this->fields )) {
					$this->fields[$key] = $value;
					continue;
				}
			}


			if (!isset( $this->fields['message'] )) {
				$this->fields['message'] = '';
			}

			$this->runAfterSettingHTML(  );
		}

		function runaftersettinghtml() {
		}
	}

?>