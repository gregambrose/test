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

	class umodel {
		var $fields = null;
		var $view = null;

		function umodel() {
			$this->fields = array(  );
			$this->addField( '_messg', 'text' );
		}

		function setview($view) {
			$this->view = &$view;

		}

		function setinput($input) {
			$this->rawInput = $input;
			foreach ($this->fields as $key => $value) {
				if (!isset( $this->fields[$key] )) {
					continue;
				}


				if (!isset( $input[$key] )) {
					continue;
				}

				$fld = &$this->fields[$key];

				$fld->setValue( $input[$key] );
			}

		}

		function getrawinput() {
			return $this->rawInput;
		}

		function getfromdatabase($record) {
			foreach ($this->fields as $key => $value) {
				if ($record->fieldExists( $key ) == false) {
					continue;
				}

				$fld = &$this->fields[$key];

				$fld->setValue( $record->getForHTML( $key ) );
			}

		}

		function settodatabase($record) {
			foreach ($this->fields as $key => $value) {
				if ($record->fieldExists( $key ) == false) {
					continue;
				}

				$fld = &$this->fields[$key];

				$record->set( $key, $fld->getValue( $key ) );
			}

		}

		function getallfields() {
			return $this->fields;
		}

		function getfield($name) {
			return $this->fields[$name];
		}

		function getfieldvalue($name) {
			if (!array_key_exists( $name, $this->fields )) {
				trigger_error( '' . 'havent got variable ' . $name, E_USER_ERROR );
			}

			$fld = $this->fields[$name];
			return $fld->getValue(  );
		}

		function clearallfields() {
			foreach ($this->fields as $key => $value) {
				$this->fields[$key]->setValue( '' );
			}

		}

		function displaypage() {
			$this->view->process(  );
		}

		function defaultaction() {
			return false;
		}

		function setfield($name, $value) {
			if (!isset( $this->fields[$name] )) {
				trigger_error( '' . 'field ' . $name . ' not set', E_USER_ERROR );
			}

			$fld = &$this->fields[$name];

			$messg = $this->_isValid( $name, $value );

			if ($messg != null) {
				return $messg;
			}

			$fld->setValue( $value );
			return null;
		}

		function addfield($name, $type) {
			$fld = new UField( $type );
			$this->fields[$name] = &$fld;

		}

		function getpolicylistiterator() {
			$pi = new _PolicyIterator(  );
			return $pi;
		}

		function _isvalid($name, $value) {
			return null;
		}

		function _get($name) {
			if (!array_key_exists( $name, $this->fields )) {
				trigger_error( '' . 'cant get field ' . $name, E_USER_ERROR );
			}

			$fld = &$this->fields[$name];

			return $fld;
		}
	}

?>