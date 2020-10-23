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

	class Archive_Tar {
		var $_tarname = '';
		var $_compress = false;
		var $_compress_type = 'none';
		var $_separator = ' ';
		var $_file = 0;
		var $_temp_tarname = '';

		function Archive_Tar($p_tarname, $p_compress = null) {
			if (substr( 'WINNT', 0, 3 ) == 'WIN') {
				define( 'OS_WINDOWS', true );
				define( 'OS_UNIX', false );
				define( 'PEAR_OS', 'Windows' );
			} 
else {
				define( 'OS_WINDOWS', false );
				define( 'OS_UNIX', true );
				define( 'PEAR_OS', 'Unix' );
			}

			$this->_compress = false;
			$this->_compress_type = 'none';

			if ($p_compress === null) {
				if (@file_exists( $p_tarname )) {
					if ($fp = @fopen( $p_tarname, 'rb' )) {
						$data = fread( $fp, 2 );
						fclose( $fp );

						if ($data == '‹') {
							$this->_compress = true;
							$this->_compress_type = 'gz';
						} 
else {
							if ($data == 'BZ') {
								$this->_compress = true;
								$this->_compress_type = 'bz2';
							}
						}
					}
				} 
else {
					if (substr( $p_tarname, -2 ) == 'gz') {
						$this->_compress = true;
						$this->_compress_type = 'gz';
					} 
else {
						if (( substr( $p_tarname, -3 ) == 'bz2' || substr( $p_tarname, -2 ) == 'bz' )) {
							$this->_compress = true;
							$this->_compress_type = 'bz2';
						}
					}
				}
			} 
else {
				if (( $p_compress === true || $p_compress == 'gz' )) {
					$this->_compress = true;
					$this->_compress_type = 'gz';
				} 
else {
					if ($p_compress == 'bz2') {
						$this->_compress = true;
						$this->_compress_type = 'bz2';
					}
				}
			}

			$this->_tarname = $p_tarname;

			if ($this->_compress) {
				if ($this->_compress_type == 'gz') {
					$extname = 'zlib';
				} 
else {
					if ($this->_compress_type == 'bz2') {
						$extname = 'bz2';
					}
				}


				if (!extension_loaded( $extname )) {
					exit(  . 'The extension \'' . $extname . '\' couldn\'t be found.
' . 'Please make sure your version of PHP was built ' . (  . 'with \'' . $extname . '\' support.
' ) );
					return false;
				}
			}

		}

		function _Archive_Tar() {
			$this->_close(  );

			if ($this->_temp_tarname != '') {
				@unlink( $this->_temp_tarname );
			}

		}

		function extract($p_path = '', $write_mode) {
			return $this->extractModify( $p_path, '', $write_mode );
		}

		function listContent() {
			$v_list_detail = array(  );

			if ($this->_openRead(  )) {
				if (!$this->_extractList( '', $v_list_detail, 'list', '', '', 'wb' )) {
					unset( $$v_list_detail );
					$v_list_detail = 0;
				}

				$this->_close(  );
			}

			return $v_list_detail;
		}

		function extractModify($p_path, $p_remove_path, $write_mode) {
			$v_result = true;
			$v_list_detail = array(  );

			if ($v_result = $this->_openRead(  )) {
				$v_result = $this->_extractList( $p_path, $v_list_detail, 'complete', 0, $p_remove_path, $write_mode );
				$this->_close(  );
			}

			return $v_result;
		}

		function raiseError($msg) {
			echo  . '<b>TAR ERROR</b> ' . $msg . ' <br>';
		}

		function _error($p_message) {
			$this->raiseError( $p_message );
		}

		function _warning($p_message) {
			$this->raiseError( $p_message );
		}

		function _openRead() {
			$v_filename = $this->_tarname;

			if ($this->_compress_type == 'gz') {
				$this->_file = @gzopen( $v_filename, 'rb' );
			} 
else {
				if ($this->_compress_type == 'bz2') {
					$this->_file = @bzopen( $v_filename, 'rb' );
				} 
else {
					if ($this->_compress_type == 'none') {
						$this->_file = @fopen( $v_filename, 'rb' );
					} 
else {
						$this->_error( 'Unknown or missing compression type (' . $this->_compress_type . ')' );
					}
				}
			}


			if ($this->_file == 0) {
				$this->_error( 'Unable to open in read mode \'' . $v_filename . '\'' );
				return false;
			}

			return true;
		}

		function _close() {
			if (is_resource( $this->_file )) {
				if ($this->_compress_type == 'gz') {
					@gzclose( $this->_file );
				} 
else {
					if ($this->_compress_type == 'bz2') {
						@bzclose( $this->_file );
					} 
else {
						if ($this->_compress_type == 'none') {
							@fclose( $this->_file );
						} 
else {
							$this->_error( 'Unknown or missing compression type (' . $this->_compress_type . ')' );
						}
					}
				}

				$this->_file = 0;
			}


			if ($this->_temp_tarname != '') {
				@unlink( $this->_temp_tarname );
				$this->_temp_tarname = '';
			}

			return true;
		}

		function _readBlock($p_len = null) {
			$v_block = null;

			if (is_resource( $this->_file )) {
				if ($p_len === null) {
					$p_len = 512;
				}


				if ($this->_compress_type == 'gz') {
					$v_block = @gzread( $this->_file, 512 );
				} 
else {
					if ($this->_compress_type == 'bz2') {
						$v_block = @bzread( $this->_file, 512 );
					} 
else {
						if ($this->_compress_type == 'none') {
							$v_block = @fread( $this->_file, 512 );
						} 
else {
							$this->_error( 'Unknown or missing compression type (' . $this->_compress_type . ')' );
						}
					}
				}
			}

			return $v_block;
		}

		function _jumpBlock($p_len = null) {
			if (is_resource( $this->_file )) {
				if ($p_len === null) {
					$p_len = 1;
				}


				if ($this->_compress_type == 'gz') {
					gzseek( $this->_file, @gztell( $this->_file ) + $p_len * 512 );
				} 
else {
					if ($this->_compress_type == 'bz2') {
						for  ($i = 0; $i < $p_len; $i++) {
							$this->_readBlock(  );
						}
					} 
else {
						if ($this->_compress_type == 'none') {
							fseek( $this->_file, @ftell( $this->_file ) + $p_len * 512 );
						} 
else {
							$this->_error( 'Unknown or missing compression type (' . $this->_compress_type . ')' );
						}
					}
				}
			}

			return true;
		}

		function _readHeader(&$v_binary_data, $v_header) {
			if (strlen( $v_binary_data ) == 0) {
				$v_header['filename'] = '';
				return true;
			}


			if (strlen( $v_binary_data ) != 512) {
				$v_header['filename'] = '';
				$this->_error( 'Invalid block size : ' . strlen( $v_binary_data ) );
				return false;
			}

			$v_checksum = 0;
			for  ($i = 0; $i < 148; $i++) {
				$v_checksum += ord( substr( $v_binary_data, $i, 1 ) );
			}

			for  ($i = 148; $i < 156; $i++) {
				$v_checksum += ord( ' ' );
			}

			for  ($i = 156; $i < 512; $i++) {
				$v_checksum += ord( substr( $v_binary_data, $i, 1 ) );
			}

			$v_data = unpack( 'a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor', $v_binary_data );
			$v_header['checksum'] = octdec( trim( $v_data['checksum'] ) );

			if ($v_header['checksum'] != $v_checksum) {
				$v_header['filename'] = '';

				if (( $v_checksum == 256 && $v_header['checksum'] == 0 )) {
					return true;
				}

				$this->_error( 'Invalid checksum for file "' . $v_data['filename'] . '" : ' . $v_checksum . ' calculated, ' . $v_header['checksum'] . ' expected' );
				return false;
			}

			$v_header['filename'] = trim( $v_data['filename'] );
			$v_header['mode'] = octdec( trim( $v_data['mode'] ) );
			$v_header['uid'] = octdec( trim( $v_data['uid'] ) );
			$v_header['gid'] = octdec( trim( $v_data['gid'] ) );
			$v_header['size'] = octdec( trim( $v_data['size'] ) );
			$v_header['mtime'] = octdec( trim( $v_data['mtime'] ) );
			$v_header['typeflag'] = $v_data['typeflag'];

			if ( == '5') {
				$v_header['size'] = 0;
			}

			return true;
		}

		function _readLongHeader($v_header) {
			$v_filename = '';
			$n = floor( $v_header['size'] / 512 );
			for  ($i = 0; $i < $n; $i++) {
				$v_content = $this->_readBlock(  );
				$v_filename .= $v_content;
			}


			if ($v_header['size'] % 512 != 0) {
				$v_content = $this->_readBlock(  );
				$v_filename .= $v_content;
			}

			$v_binary_data = $this->_readBlock(  );

			if (!$this->_readHeader( $v_binary_data, $v_header )) {
				return false;
			}

			$v_header['filename'] = $v_filename;
			return true;
		}

		function _extractList(&$p_path, $p_list_detail, $p_mode, $p_file_list, $p_remove_path, $write_mode) {
			$v_result = true;
			$v_nb = 0;
			$v_extract_all = true;
			$v_listing = false;
			$p_path = $this->_translateWinPath( $p_path, false );

			if (( $p_path == '' || ( ( substr( $p_path, 0, 1 ) != '/' && substr( $p_path, 0, 3 ) != '../' ) && !strpos( $p_path, ':' ) ) )) {
				$p_path = './' . $p_path;
			}

			$p_remove_path = $this->_translateWinPath( $p_remove_path );

			if (( $p_remove_path != '' && substr( $p_remove_path, -1 ) != '/' )) {
				$p_remove_path .= '/';
			}

			$p_remove_path_size = strlen( $p_remove_path );
			switch ($p_mode) {
				case 'complete': {
					$v_extract_all = true;
					$v_listing = false;
					break;
				}

				case 'partial': {
					$v_extract_all = false;
					$v_listing = false;
					break;
				}

				case 'list': {
					$v_extract_all = false;
					$v_listing = true;
					break;
					break;
				}
			}
		}

		function _dirCheck($p_dir) {
			if (( @is_dir( $p_dir ) || $p_dir == '' )) {
				return true;
			}

			$p_parent_dir = dirname( $p_dir );

			if (( ( $p_parent_dir != $p_dir && $p_parent_dir != '' ) && !$this->_dirCheck( $p_parent_dir ) )) {
				return false;
			}


			if (!@mkdir( $p_dir, 511 )) {
				$this->_error( (  . 'Unable to create directory \'' . $p_dir . '\'' ) );
				return false;
			}

			return true;
		}

		function _pathReduction($p_dir) {
			$v_result = '';

			if ($p_dir != '') {
				$v_list = explode( '/', $p_dir );
				for  ($i = sizeof( $v_list ) - 1; 0 <= $i; $i--) {
					if ($v_list[$i] == '.') {
					} 
else {
						if ($v_list[$i] == '..') {
							$i--;
						} 
else {
							if (( ( $v_list[$i] == '' && $i != sizeof( $v_list ) - 1 ) && $i != 0 )) {
							} 
else {
								$v_result = $v_list[$i] . ($i != sizeof( $v_list ) - 1 ? '/' . $v_result : '');
							}
						}
					}
				}
			}

			$v_result = strtr( $v_result, '\', '/' );
			return $v_result;
		}

		function _translateWinPath($p_path, $p_remove_disk_letter = true) {
			if (OS_WINDOWS) {
				if (( $p_remove_disk_letter && $v_position = strpos( $p_path, ':' ) != false )) {
					$p_path = substr( $p_path, $v_position + 1 );
				}


				if (( 0 < strpos( $p_path, '\' ) || substr( $p_path, 0, 1 ) == '\' )) {
					$p_path = strtr( $p_path, '\', '/' );
				}
			}

			return $p_path;
		}
	}

	define( 'ARCHIVE_TAR_ATT_SEPARATOR', 90001 );
?>