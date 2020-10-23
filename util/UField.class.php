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

	class ufield {
		var $value = null;

		function ufield($type, $value = null) {
			$this->type = $type;
			$this->value = $value;
		}

		function getvalue() {
			return $this->value;
		}

		function setvalue($value) {
			$newValue = $value;

			if ($this->type == 'bool') {
				if ($value == 'on') {
					$newValue = 1;
				}


				if ($value == 'off') {
					$newValue = 0;
				}
			}

			$this->value = $newValue;
		}

		function gettype() {
			return $this->type;
		}
	}

?>