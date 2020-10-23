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

	class fpdi_pdf_parser {
		var $pages = null;
		var $page_count = null;
		var $pageno = null;
		var $pdfVersion = null;
		var $fpdi = null;

		function fpdi_pdf_parser(&$filename, $fpdi) {
			$this->fpdi = &$fpdi;

			$this->filename = $filename;
			pdf_parser::pdf_parser( $filename );
			$this->getInfo(  );
			$pages = $this->pdf_resolve_object( $this->c, $this->root[1][1]['/Pages'] );
			$this->read_pages( $this->c, $pages, $this->pages );
			$this->page_count = count( $this->pages );
		}

		function error($msg) {
			$this->fpdi->error( $msg );
		}

		function getpagecount() {
			return $this->page_count;
		}

		function setpageno($pageno) {
			$pageno -= 1;

			if (( $pageno < 0 || $this->getPageCount(  ) <= $pageno )) {
				$this->fpdi->error( 'Pagenumber is wrong!' );
			}

			$this->pageno = $pageno;
		}

		function getpageresources() {
			return $this->_getPageResources( $this->pages[$this->pageno] );
		}

		function _getpageresources($obj) {
			$obj = $this->pdf_resolve_object( $this->c, $obj );

			if (isset( $obj[1][1]['/Resources'] )) {
				$res = $this->pdf_resolve_object( $this->c, $obj[1][1]['/Resources'] );

				if ($res[0] == PDF_TYPE_OBJECT) {
					return $res[1];
				}

				return $res;
			}


			if (!isset( $obj[1][1]['/Parent'] )) {
				return false;
			}

			$res = $this->_getPageResources( $obj[1][1]['/Parent'] );

			if ($res[0] == PDF_TYPE_OBJECT) {
				return $res[1];
			}

			return $res;
		}

		function getinfo() {
			$avail_infos = array( 'Title', 'Author', 'Subject', 'Keywords', 'Creator', 'Producer', 'CreationDate', 'ModDate', 'Trapped' );
			$_infos = $this->pdf_resolve_object( $this->c, $this->xref['trailer'][1]['/Info'] );
			$infos = array(  );
			foreach ($avail_infos as $info) {
				if (isset( $_infos[1][1]['/' . $info] )) {
					if ($_infos[1][1]['/' . $info][0] == PDF_TYPE_STRING) {
						$infos[$info] = $this->deescapeString( $_infos[1][1]['/' . $info][1] );
						continue;
					}


					if ($_infos[1][1]['/' . $info][0] == PDF_TYPE_HEX) {
						$infos[$info] = $this->hex2String( $_infos[1][1]['/' . $info][1] );
						continue;
					}

					continue;
				}
			}

			$this->infos = $infos;
		}

		function hex2string($hex) {
			$endian = false;

			if (preg_match( '/^FEFF/', $hex )) {
				$i = 4;
				$endian = 'big';
			} 
else {
				if (preg_match( '/^FFFE/', $hex )) {
					$i = 4;
					$endian = 'little';
				} 
else {
					$i = 0;
				}
			}

			$s = '';
			$l = strlen( $hex );

			while ($i < $l) {
				if (!$endian) {
					$s .= chr( hexdec( $hex[$i] . (isset( $hex[$i + 1] ) ? $hex[$i + 1] : '0') ) );
				} 
else {
					if ($endian == 'big') {
						$_c = $hex[$i] . $hex[$i + 1];
						$i += 2;
						$c = $hex[$i] . $hex[$i + 1];

						if ($_c != '00') {
							$s .= '?';
							continue;
						}

						$s .= chr( hexdec( $c ) );
						continue;
					}


					if ($endian == 'little') {
						$c = $hex[$i] . $hex[$i + 1];
						$i += 2;
						$_c = $hex[$i] . $hex[$i + 1];

						if ($_c != '00') {
							$s .= '?';
							continue;
						}

						$s .= chr( hexdec( $c ) );
						continue;
					}
				}

				$i += 2;
			}

			return $s;
		}

		function deescapestring($s) {
			$torepl = array( '/\\(\d{1,3})/e' => 'chr(octdec(\1))', '/\\\(/' => '(', '/\\\)/' => ')' );
			return preg_replace( array_keys( $torepl ), $torepl, $s );
		}

		function getcontent() {
			$buffer = '';
			$contents = $this->getPageContent( $this->pages[$this->pageno][1][1]['/Contents'] );
			foreach ($contents as $tmp_content) {
				$buffer .= $this->rebuildContentStream( $tmp_content );
			}

			return $buffer;
		}

		function getpagecontent($content_ref) {
			$contents = array(  );

			if ($content_ref[0] == PDF_TYPE_OBJREF) {
				$content = $this->pdf_resolve_object( $this->c, $content_ref );

				if ($content[1][0] == PDF_TYPE_ARRAY) {
					$contents = $this->getPageContent( $content[1] );
				} 
else {
					$contents[] = $content;
				}
			} 
else {
				if ($content_ref[0] == PDF_TYPE_ARRAY) {
					foreach ($content_ref[1] as $tmp_content_ref) {
						$contents = array_merge( $contents, $this->getPageContent( $tmp_content_ref ) );
					}
				}
			}

			return $contents;
		}

		function rebuildcontentstream($obj) {
			$filters = array(  );

			if (isset( $obj[1][1]['/Filter'] )) {
				$_filter = $obj[1][1]['/Filter'];

				if ($_filter[0] == PDF_TYPE_TOKEN) {
					$filters[] = $_filter;
				} 
else {
					if ($_filter[0] == PDF_TYPE_ARRAY) {
						$filters = $_filter[1];
					}
				}
			}

			$stream = $obj[2][1];
			foreach ($filters as $_filter) {
				switch ($_filter[1]) {
					case '/FlateDecode': {
						if (function_exists( 'gzuncompress' )) {
							$stream = @gzuncompress( $stream );
						} 
else {
							$this->fpdi->error( sprintf( 'To handle %s filter, please compile php with zlib support.', $_filter[1] ) );
						}


						if ($stream === false) {
							$this->fpdi->error( 'Error while decompressing string.' );
						}

						break;
					}

					case '/LZWDecode': {
						@include_once( 'decoders/lzw.php' );

						if (class_exists( 'LZWDecode' )) {
							$lzwdec = new LZWDecode( $this->fpdi );
							$stream = $lzwdec->decode( $stream );
						} 
else {
							$this->fpdi->error( sprintf( 'Unsupported Filter: %s', $_filter[1] ) );
						}

						break;
					}

					case '/ASCII85Decode': {
						@include_once( 'decoders/ascii85.php' );

						if (class_exists( 'ASCII85Decode' )) {
							$ascii85 = new ASCII85Decode( $this->fpdi );
							$stream = $ascii85->decode( trim( $stream ) );
						} 
else {
							$this->fpdi->error( sprintf( 'Unsupported Filter: %s', $_filter[1] ) );
						}

						break;
					}

					case null: {
						$stream = $stream;
						break;
					}

					default: {
						$this->fpdi->error( sprintf( 'Unsupported Filter: %s', $_filter[1] ) );
					}
				}
			}

			return $stream;
		}

		function getpagemediabox($pageno) {
			return $this->getPageBox( $this->pages[$pageno - 1], '/MediaBox' );
		}

		function getpagebox($page, $box_index) {
			$page = $this->pdf_resolve_object( $this->c, $page );
			$box = null;

			if (isset( $page[1][1][$box_index] )) {
				$box = &$page[1][1][$box_index];
			}


			if (( !is_null( $box ) && $box[0] == PDF_TYPE_OBJREF )) {
				$tmp_box = $this->pdf_resolve_object( $this->c, $box );
				$box = $tmp_box[1];
			}


			if (( !is_null( $box ) && $box[0] == PDF_TYPE_ARRAY )) {
				$b = &$box[1];

				return array( 'x' => $b[0][1] / $this->fpdi->k, 'y' => $b[1][1] / $this->fpdi->k, 'w' => $b[2][1] / $this->fpdi->k, 'h' => $b[3][1] / $this->fpdi->k );
			}


			if (!isset( $page[1][1]['/Parent'] )) {
				return false;
			}

			return $this->getPageBox( $this->pdf_resolve_object( $this->c, $page[1][1]['/Parent'] ), $box_index );
		}

		function getpageboxes($page) {
			$_boxes = array( '/MediaBox', '/CropBox', '/BleedBox', '/TrimBox', '/ArtBox' );
			$boxes = array(  );
			foreach ($_boxes as $box) {
				if ($_box = $this->getPageBox( $page, $box )) {
					$boxes[$box] = $_box;
					continue;
				}
			}

			return $boxes;
		}

		function read_pages(&$c, &$pages, $result) {
			$kids = $this->pdf_resolve_object( $c, $pages[1][1]['/Kids'] );

			if (!is_array( $kids )) {
				$this->fpdi->Error( 'Cannot find /Kids in current /Page-Dictionary' );
			}

			foreach ($kids[1] as $v) {
				$pg = $this->pdf_resolve_object( $c, $v );

				if ($pg[1][1]['/Type'][1] === '/Pages') {
					$this->read_pages( $c, $pg, $result );
					continue;
				}

				$result[] = $pg;
			}

		}

		function getpdfversion() {
			pdf_parser::getpdfversion(  );

			if (( isset( $this->fpdi->importVersion ) && $this->fpdi->importVersion < $this->pdfVersion )) {
				$this->fpdi->importVersion = $this->pdfVersion;
			}

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

	require_once( 'wrapper_functions.php' );
	require_once( 'pdf_parser.php' );
?>