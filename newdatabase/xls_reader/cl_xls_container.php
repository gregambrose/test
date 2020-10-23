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

	class xls_container {
		var $_data = false;
		var $_isfile = false;
		var $_s_offset = 0;
		var $_data_size = 0;

		function xls_container($Data, $IsFile = false) {
			if ($IsFile) {
				$this->_isfile = true;
				$this->_open_file( $Data );
			} 
else {
				$this->_data = $Data;
				$this->_data_size = strlen( $this->_data );
			}

			register_shutdown_function( array( $this, '_kill' ) );
		}

		function _kill() {
			if ($this->_isfile === true) {
				@fclose( $this->_data );
				$this->_isfile = false;
				return null;
			}

			$this->_data = false;
			$this->_data_size = 0;
		}

		function _open_file($file) {
			$this->_data = @fopen( $file, 'rb' );

			if (!$this->_data) {
				trigger_error( 'Unable to open file [' . $file . ']', E_USER_ERROR );
			}

			$this->_data_size = @filesize( $file );

			if (!$this->_data_size) {
				trigger_error( 'Unable get file size [' . $file . ']', E_USER_ERROR );
			}

		}

		function _fgets($offset, $length) {
			if ($this->_data === false) {
				trigger_error( '_fgets failed - no data available', E_USER_ERROR );
			}


			if ($this->_data_size < $this->_s_offset + $offset + $length) {
				trigger_error( '_fgets failed - illegal offset/length', E_USER_ERROR );
			}


			if ($this->_isfile) {
				if (@fseek( $this->_data, $this->_s_offset + $offset, SEEK_SET ) == 0 - 1) {
					trigger_error( '_fgets failed - unable to seek by given offset', E_USER_ERROR );
				}

				return @fread( $this->_data, $length );
			}

			return substr( $this->_data, $this->_s_offset + $offset, $length );
		}

		function get_data_size() {
			if (!$this->_data === false) {
				return $this->_data_size;
			}

			trigger_error( 'Container does not have any data', E_USER_ERROR );
		}

		function get_blocks_number() {
			if (!$this->_data === false) {
				return (int)( $this->_data_size - 1 ) / EXCEL_BLOCK_SIZE - 1;
			}

			trigger_error( 'Container does not have any data', E_USER_ERROR );
		}

		function read_blocks_chain($chain, $block_size = EXCEL_BLOCK_SIZE) {
			$chain_data = '';
			$num = count( $chain );
			$i = 0;

			while ($i < $num) {
				$chain_data .= $this->_fgets( $chain[$i] * $block_size, $block_size );
				++$i;
			}

			return $chain_data;
		}

		function get_byte($offset) {
			return $this->_fgets( $offset, 1 );
		}

		function get_ord($offset) {
			return ord( $this->get_byte( $offset ) );
		}

		function get_long($offset) {
			return get_long_from_string( $this->_fgets( $offset, 4 ) );
		}
	}

?>