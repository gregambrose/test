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

	class usession {
		function usession() {
		}

		function get($type) {
			if (isset( $_SESSION[$type] )) {
				return $_SESSION[$type];
			}

			$temp = null;
			return $temp;
		}

		function set(&$type, $value) {
			$_SESSION[$type] = &$value;

		}

		function clear($type) {
			unset( $_SESSION[$type] );
		}

		function removeallbutuser() {
			foreach ($_SESSION as $key => $value) {
				$remove = true;

				if ($key == 'user') {
					$remove = false;
				}


				if ($key == 'session') {
					$remove = false;
				}


				if ($remove == false) {
					continue;
				}

				unset( $_SESSION[$key] );
			}

		}

		function removeall() {
			$_SESSION = array(  );
		}

		function setinput($type, $value) {
			$input = $this->get( '_input' );

			if (!is_array( $input )) {
				$input = array(  );
			}

			$input[$type] = $value;
			$this->set( '_input', $input );
		}

		function getinput() {
			$input = $this->get( '_input' );

			if (!is_array( $input )) {
				$input = array(  );
			}

			$this->clear( '_input' );
			return $input;
		}

		function setmessage($text) {
			$input = $this->get( '_input' );

			if (!is_array( $input )) {
				$input = array(  );
			}

			$input['_messg'] = $text;
			$this->set( '_input', $input );
		}
	}

?>