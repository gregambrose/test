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

	class pdf_context {
		var $file = null;
		var $buffer = null;
		var $offset = null;
		var $length = null;
		var $stack = null;

		function pdf_context($f) {
			$this->file = $f;
			$this->reset(  );
		}

		function reset($pos = null, $l = 100) {
			if (!is_null( $pos )) {
				fseek( $this->file, $pos );
			}

			$this->buffer = fread( $this->file, $l );
			$this->offset = 0;
			$this->length = strlen( $this->buffer );
			$this->stack = array(  );
		}

		function ensure_content() {
			if ($this->length - 1 <= $this->offset) {
				return $this->increase_length(  );
			}

			return true;
		}

		function increase_length($l = 100) {
			if (feof( $this->file )) {
				return false;
			}

			$this->buffer .= fread( $this->file, $l );
			$this->length = strlen( $this->buffer );
			return true;
		}
	}

?>