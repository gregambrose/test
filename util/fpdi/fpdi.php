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

	class fpdi {
		var $current_filename = null;
		var $parsers = null;
		var $current_parser = null;
		var $PDFVersion = 1.30000000000000004440892;
		var $importVersion = 1.30000000000000004440892;
		var $obj_stack = null;
		var $don_obj_stack = null;
		var $current_obj_id = null;

		function fpdi($orientation = 'P', $unit = 'mm', $format = 'A4') {
			fpdf_tpl::fpdf_tpl( $orientation, $unit, $format );
		}

		function setsourcefile($filename) {
			$this->current_filename = $filename;
			$fn = &$this->current_filename;

			$this->parsers[$fn] = new fpdi_pdf_parser( $fn, $this );
			$this->current_parser = &$this->parsers[$fn];

			return $this->parsers[$fn]->getPageCount(  );
		}

		function importpage($pageno) {
			$fn = &$this->current_filename;

			$this->parsers[$fn]->setPageno( $pageno );
			++$this->tpl;
			$this->tpls[$this->tpl] = array(  );
			$this->tpls[$this->tpl]['parser'] = &$this->parsers[$fn];

			$this->tpls[$this->tpl]['resources'] = $this->parsers[$fn]->getPageResources(  );
			$this->tpls[$this->tpl]['buffer'] = $this->parsers[$fn]->getContent(  );
			$mediabox = $this->parsers[$fn]->getPageMediaBox( $pageno );
			$this->tpls[$this->tpl] = array_merge( $this->tpls[$this->tpl], $mediabox );
			return $this->tpl;
		}

		function _putoobjects() {
			if (( is_array( $this->parsers ) && 0 < count( $this->parsers ) )) {
				foreach ($this->parsers as $filename => $p) {
					$this->current_parser = &$this->parsers[$filename];

					if (is_array( $this->obj_stack[$filename] )) {
						while ($n = key( $this->obj_stack[$filename] )) {
							$nObj = $this->current_parser->pdf_resolve_object( $this->current_parser->c, $this->obj_stack[$filename][$n][1] );
							$this->_newobj( $this->obj_stack[$filename][$n][0] );

							if ($nObj[0] == PDF_TYPE_STREAM) {
								$this->pdf_write_value( $nObj );
							} 
else {
								$this->pdf_write_value( $nObj[1] );
							}

							$this->_out( 'endobj' );
							$this->obj_stack[$filename][$n] = null;
							unset( $this->obj_stack[$filename][$n] );
							reset( $this->obj_stack[$filename] );
						}

						continue;
					}
				}
			}

		}

		function _begindoc() {
			$this->state = 1;
		}

		function setversion() {
			if ($this->PDFVersion < $this->importVersion) {
				$this->PDFVersion = $this->importVersion;
			}


			if (!method_exists( $this, '_putheader' )) {
				$this->buffer = '%PDF-' . $this->PDFVersion . '
' . $this->buffer;
			}

		}

		function _enddoc() {
			$this->setVersion(  );
			fpdf_tpl::_enddoc(  );
		}

		function _putresources() {
			$this->_putfonts(  );
			$this->_putimages(  );
			$this->_puttemplates(  );
			$this->_putOobjects(  );
			$this->offsets[2] = strlen( $this->buffer );
			$this->_out( '2 0 obj' );
			$this->_out( '<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]' );
			$this->_out( '/Font <<' );
			foreach ($this->fonts as $font) {
				$this->_out( $this->fontprefix . $font['i'] . ' ' . $font['n'] . ' 0 R' );
			}

			$this->_out( '>>' );

			if (( count( $this->images ) || count( $this->tpls ) )) {
				$this->_out( '/XObject <<' );

				if (count( $this->images )) {
					foreach ($this->images as $image) {
						$this->_out( '/I' . $image['i'] . ' ' . $image['n'] . ' 0 R' );
					}
				}


				if (count( $this->tpls )) {
					foreach ($this->tpls as $tplidx => $tpl) {
						$this->_out( $this->tplprefix . $tplidx . ' ' . $tpl['n'] . ' 0 R' );
					}
				}

				$this->_out( '>>' );
			}

			$this->_out( '>>' );
			$this->_out( 'endobj' );
		}

		function _puttemplates() {
			$filter = ($this->compress ? '/Filter /FlateDecode ' : '');
			reset( $this->tpls );
			foreach ($this->tpls as $tplidx => $tpl) {
				$p = $tpl['buffer'];
				$this->_newobj(  );
				$this->tpls[$tplidx]['n'] = $this->n;
				$this->_out( '<<' . $filter . '/Type /XObject' );
				$this->_out( '/Subtype /Form' );
				$this->_out( '/FormType 1' );
				$this->_out( sprintf( '/BBox [%.2f %.2f %.2f %.2f]', $tpl['x'] * $this->k, ( $tpl['h'] - $tpl['y'] ) * $this->k, $tpl['w'] * $this->k, ( $tpl['h'] - $tpl['y'] - $tpl['h'] ) * $this->k ) );
				$this->_out( '/Resources ' );

				if ($tpl['resources']) {
					$this->current_parser = &$tpl['parser'];

					$this->pdf_write_value( $tpl['resources'] );
				} 
else {
					$this->_out( '<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]' );

					if (count( $this->res['tpl'][$tplidx]['fonts'] )) {
						$this->_out( '/Font <<' );
						foreach ($this->res['tpl'][$tplidx]['fonts'] as $font) {
							$this->_out( $this->fontprefix . $font['i'] . ' ' . $font['n'] . ' 0 R' );
						}

						$this->_out( '>>' );
					}


					if (( count( $this->res['tpl'][$tplidx]['images'] ) || count( $this->res['tpl'][$tplidx]['tpls'] ) )) {
						$this->_out( '/XObject <<' );

						if (count( $this->res['tpl'][$tplidx]['images'] )) {
							foreach ($this->res['tpl'][$tplidx]['images'] as $image) {
								$this->_out( '/I' . $image['i'] . ' ' . $image['n'] . ' 0 R' );
							}
						}


						if (count( $this->res['tpl'][$tplidx]['tpls'] )) {
							foreach ($this->res['tpl'][$tplidx]['tpls'] as $i => $tpl) {
								$this->_out( $this->tplprefix . $i . ' ' . $tpl['n'] . ' 0 R' );
							}
						}

						$this->_out( '>>' );
					}

					$this->_out( '>>' );
				}

				$this->_out( '/Length ' . strlen( $p ) . ' >>' );
				$this->_putstream( $p );
				$this->_out( 'endobj' );
			}

		}

		function _newobj($obj_id = false, $onlynewobj = false) {
			if (!$obj_id) {
				$obj_id = ++$this->n;
			}


			if (!$onlynewobj) {
				$this->offsets[$obj_id] = strlen( $this->buffer );
				$this->_out( $obj_id . ' 0 obj' );
				$this->current_obj_id = $obj_id;
			}

		}

		function pdf_write_value($value) {
			switch ($value[0]) {
				case PDF_TYPE_NUMERIC: {
				}

				case PDF_TYPE_TOKEN: {
					$this->_out( $value[1] . ' ' );
					break;
				}

				case PDF_TYPE_ARRAY: {
					$this->_out( '[', false );
					$i = 0;

					while ($i < count( $value[1] )) {
						$this->pdf_write_value( $value[1][$i] );
						++$i;
					}

					$this->_out( ']' );
					break;
				}

				case PDF_TYPE_DICTIONARY: {
					$this->_out( '<<', false );
					reset( $value[1] );

					while (list( $k, $v ) = each( $value[1] )) {
						$this->_out( $k . ' ', false );
						$this->pdf_write_value( $v );
					}

					$this->_out( '>>' );
					break;
				}

				case PDF_TYPE_OBJREF: {
					if (!isset( $this->don_obj_stack[$this->current_parser->filename][$value[1]] )) {
						$this->_newobj( false, true );
						$this->obj_stack[$this->current_parser->filename][$value[1]] = array( $this->n, $value );
						$this->don_obj_stack[$this->current_parser->filename][$value[1]] = array( $this->n, $value );
					}

					$objid = $this->don_obj_stack[$this->current_parser->filename][$value[1]][0];
					$this->_out( '' . $objid . ' 0 R' );
					break;
				}

				case PDF_TYPE_STRING: {
					$this->_out( '(' . $value[1] . ')' );
					break;
				}

				case PDF_TYPE_STREAM: {
					$this->pdf_write_value( $value[1] );
					$this->_out( 'stream' );
					$this->_out( $value[2][1] );
					$this->_out( 'endstream' );
					break;
				}

				case PDF_TYPE_HEX: {
					$this->_out( '<' . $value[1] . '>' );
					break;
				}

				case PDF_TYPE_NULL: {
					$this->_out( 'null' );
				}
			}

		}

		function _out($s, $ln = true) {
			if ($this->state == 2) {
				if (!$this->intpl) {
					$this->pages[$this->page] .= $s . ($ln == true ? '
' : '');
					return null;
				}

				$this->tpls[$this->tpl]['buffer'] .= $s . ($ln == true ? '
' : '');
				return null;
			}

			$this->buffer .= $s . ($ln == true ? '
' : '');
		}

		function closeparsers() {
			foreach ($this->parsers as $parser) {
				$parser->closeFile(  );
			}

		}
	}

	define( 'PDF_TYPE_NULL', 0 );
	define( 'PDF_TYPE_NUMERIC', 1 );
	define( 'PDF_TYPE_TOKEN', 2 );
	define( 'PDF_TYPE_HEX', 3 );
	define( 'PDF_TYPE_STRING', 4 );
	define( 'PDF_TYPE_DICTIONARY', 5 );
	define( 'PDF_TYPE_ARRAY', 6 );
	define( 'PDF_TYPE_OBJDEC', 7 );
	define( 'PDF_TYPE_OBJREF', 8 );
	define( 'PDF_TYPE_OBJECT', 9 );
	define( 'PDF_TYPE_STREAM', 10 );
	ini_set( 'auto_detect_line_endings', 1 );
	require_once( 'fpdi/fpdf_tpl.php' );
	require_once( 'fpdi/fpdi_pdf_parser.php' );
?>