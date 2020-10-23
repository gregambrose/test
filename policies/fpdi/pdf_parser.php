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

	class pdf_parser {
		var $filename = null;
		var $f = null;
		var $c = null;
		var $xref = null;
		var $root = null;

		function pdf_parser($filename) {
			$this->filename = $filename;
			$this->f = @fopen( $this->filename, 'rb' );

			if (!$this->f) {
				$this->error( sprintf( 'Cannot open %s !', $filename ) );
			}

			$this->getPDFVersion(  );
			$this->c = new pdf_context( $this->f );
			$this->pdf_read_xref( $this->xref, $this->pdf_find_xref(  ) );
			$this->getEncryption(  );
			$this->pdf_read_root(  );
		}

		function closefile() {
			if (isset( ->f )) {
				fclose( $this->f );
			}

		}

		function error($msg) {
			exit( '<b>PDF-Parser Error:</b> ' . $msg );
		}

		function getencryption() {
			if (isset( $this->xref['trailer'][1]['/Encrypt'] )) {
				$this->error( 'File is encrypted!' );
			}

		}

		function pdf_find_root() {
			if ($this->xref['trailer'][1]['/Root'][0] != PDF_TYPE_OBJREF) {
				$this->Error( 'Wrong Type of Root-Element! Must be an indirect reference' );
			}

			return $this->xref['trailer'][1]['/Root'];
		}

		function pdf_read_root() {
			$this->root = $this->pdf_resolve_object( $this->c, $this->pdf_find_root(  ) );
		}

		function getpdfversion() {
			fseek( $this->f, 0 );
			preg_match( '/\d\.\d/', fread( $this->f, 16 ), $m );
			$this->pdfVersion = $m[0];
		}

		function pdf_find_xref() {
			fseek( $this->f, 0 - 50, SEEK_END );
			$data = fread( $this->f, 50 );

			if (!preg_match( '/startxref\s*(\d+)\s*%%EOF\s*$/', $data, $matches )) {
				$this->error( 'Unable to find pointer to xref table' );
			}

			return (int)$matches[1];
		}

		function pdf_read_xref($result, $offset, $start = null, $end = null) {
			if (( is_null( $start ) || is_null( $end ) )) {
				fseek( $this->f, $o_pos = $offset );
				$data = trim( fgets( $this->f ) );

				if ($data !== 'xref') {
					fseek( $this->f, $o_pos );
					$data = trim( _fgets( $this->f, true ) );

					if ($data !== 'xref') {
						$this->error( 'Unable to find xref table - Maybe a Problem with \'auto_detect_line_endings\'' );
					}
				}

				$o_pos = ftell( $this->f );
				$data = explode( ' ', trim( fgets( $this->f ) ) );

				if (count( $data ) != 2) {
					fseek( $this->f, $o_pos );
					$data = explode( ' ', trim( _fgets( $this->f, true ) ) );

					if (count( $data ) != 2) {
						$this->error( 'Unexpected header in xref table' );
					}
				}

				$start = $data[0];
				$end = $start + $data[1];
			}


			if (!isset( $result['xref_location'] )) {
				$result['xref_location'] = $offset;
			}


			if (( !isset( $result['max_object'] ) || $result['max_object'] < $end )) {
				$result['max_object'] = $end;
			}


			while ($start < $end) {
				$data = fread( $this->f, 20 );
				$offset = substr( $data, 0, 10 );
				$generation = substr( $data, 11, 5 );

				if (!isset( $result['xref'][$start][(int)$generation] )) {
					$result['xref'][$start][(int)$generation] = (int)$offset;
				}

				++$start;
			}

			$o_pos = ftell( $this->f );
			$data = fgets( $this->f );

			if (preg_match( '/trailer/', $data )) {
				if (preg_match( '/(.*trailer[ 
]+)/', $data, $m )) {
					fseek( $this->f, $o_pos + strlen( $m[1] ) );
				}

				$c = &new pdf_context( $this->f );

				$trailer = $this->pdf_read_value( $c );

				if (isset( $trailer[1]['/Prev'] )) {
					$this->pdf_read_xref( $result, $trailer[1]['/Prev'][1] );
					$result['trailer'][1] = array_merge( $result['trailer'][1], $trailer[1] );
					return null;
				}

				$result['trailer'] = $trailer;
				return null;
			}

			$data = explode( ' ', trim( $data ) );

			if (count( $data ) != 2) {
				fseek( $this->f, $o_pos );
				$data = explode( ' ', trim( _fgets( $this->f, true ) ) );

				if (count( $data ) != 2) {
					$this->error( 'Unexpected data in xref table' );
				}
			}

			$this->pdf_read_xref( $result, null, (int)$data[0], (int)$data[0] + (int)$data[1] );
		}

		function pdf_read_value($c, $token = null) {
			if (is_null( $token )) {
				$token = $this->pdf_read_token( $c );
			}


			if ($token === false) {
				return false;
			}

			switch ($token) {
				case '<': {
					$pos = $c->offset;

					if (1) {
						$match = strpos( $c->buffer, '>', $pos );

						if ($match === false) {
							if (!$c->increase_length(  )) {
								return false;
							}

							continue;
						}

						$result = substr( $c->buffer, $c->offset, $match - $c->offset );
						$c->offset = $match + 1;
						return array( PDF_TYPE_HEX, $result );
					}

					break;
				}

				case '<<': {
					$result = array(  );

					while ($key = $this->pdf_read_token( $c ) !== '>>') {
						if ($key === false) {
							return false;
						}


						if ($value = $this->pdf_read_value( $c ) === false) {
							return false;
						}

						$result[$key] = $value;
					}

					array( PDF_TYPE_DICTIONARY, $result );
				}
			}

			return ;
		}

		function pdf_resolve_object($c, $obj_spec, $encapsulate = true) {
			if (!is_array( $obj_spec )) {
				return false;
			}


			if ($obj_spec[0] == PDF_TYPE_OBJREF) {
				if (isset( $this->xref['xref'][$obj_spec[1]][$obj_spec[2]] )) {
					$old_pos = ftell( $c->file );
					$c->reset( $this->xref['xref'][$obj_spec[1]][$obj_spec[2]] );
					$header = $this->pdf_read_value( $c, null, true );

					if (( ( $header[0] != PDF_TYPE_OBJDEC || $header[1] != $obj_spec[1] ) || $header[2] != $obj_spec[2] )) {
						$this->error( '' . 'Unable to find object (' . $obj_spec[1] . ', ' . $obj_spec[2] . ') at expected location' );
					}

					$this->actual_obj = &$result;

					if ($encapsulate) {
						$result = array( PDF_TYPE_OBJECT, 'obj' => $obj_spec[1], 'gen' => $obj_spec[2] );
					} 
else {
						$result = array(  );
					}


					while (1) {
						$value = $this->pdf_read_value( $c );

						if (( $value === false || 4 < count( $result ) )) {
							break;
						}


						if (( $value[0] == PDF_TYPE_TOKEN && $value[1] === 'endobj' )) {
							break;
						}

						$result[] = $value;
					}

					$c->reset( $old_pos );

					if (( isset( $result[2][0] ) && $result[2][0] == PDF_TYPE_STREAM )) {
						$result[0] = PDF_TYPE_STREAM;
					}

					return $result;
				}
			} 
else {
				return $obj_spec;
			}

		}

		function pdf_read_token($c) {
			if (count( $c->stack )) {
				return array_pop( $c->stack );
			}


			do {
				if (!$c->ensure_content(  )) {
					return false;
				}

				$c->offset += _strspn( $c->buffer, ' 
', $c->offset );
			}while (!( $c->length - 1 <= $c->offset));

			$char = $c->buffer[$c->offset++];
			switch ($char) {
				case '[': {
				}

				case ']': {
				}

				case '(': {
				}

				case ')': {
					$char;
				}
			}

			return ;
		}
	}


	if (!defined( 'PDF_TYPE_NULL' )) {
		define( 'PDF_TYPE_NULL', 0 );
	}


	if (!defined( 'PDF_TYPE_NUMERIC' )) {
		define( 'PDF_TYPE_NUMERIC', 1 );
	}


	if (!defined( 'PDF_TYPE_TOKEN' )) {
		define( 'PDF_TYPE_TOKEN', 2 );
	}


	if (!defined( 'PDF_TYPE_HEX' )) {
		define( 'PDF_TYPE_HEX', 3 );
	}


	if (!defined( 'PDF_TYPE_STRING' )) {
		define( 'PDF_TYPE_STRING', 4 );
	}


	if (!defined( 'PDF_TYPE_DICTIONARY' )) {
		define( 'PDF_TYPE_DICTIONARY', 5 );
	}


	if (!defined( 'PDF_TYPE_ARRAY' )) {
		define( 'PDF_TYPE_ARRAY', 6 );
	}


	if (!defined( 'PDF_TYPE_OBJDEC' )) {
		define( 'PDF_TYPE_OBJDEC', 7 );
	}


	if (!defined( 'PDF_TYPE_OBJREF' )) {
		define( 'PDF_TYPE_OBJREF', 8 );
	}


	if (!defined( 'PDF_TYPE_OBJECT' )) {
		define( 'PDF_TYPE_OBJECT', 9 );
	}


	if (!defined( 'PDF_TYPE_STREAM' )) {
		define( 'PDF_TYPE_STREAM', 10 );
	}

	require_once( 'pdf_context.php' );
?>