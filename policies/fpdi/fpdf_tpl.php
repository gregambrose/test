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

	class fpdf_tpl {
		var $tpls = array(  );
		var $tpl = 0;
		var $intpl = false;
		var $tplprefix = '/TPL';
		var $fontprefix = '/F';
		var $res = array(  );

		function fpdf_tpl($orientation = 'P', $unit = 'mm', $format = 'A4') {
			fpdf::fpdf( $orientation, $unit, $format );
		}

		function begintemplate($x = null, $y = null, $w = null, $h = null) {
			if ($this->page <= 0) {
				$this->error( 'You have to add a page to fpdf first!' );
			}

			++$this->tpl;
			$this->tpls[$this->tpl]['o_x'] = $this->x;
			$this->tpls[$this->tpl]['o_y'] = $this->y;
			$this->tpls[$this->tpl]['o_AutoPageBreak'] = $this->AutoPageBreak;
			$this->tpls[$this->tpl]['o_bMargin'] = $this->bMargin;
			$this->tpls[$this->tpl]['o_tMargin'] = $this->tMargin;
			$this->tpls[$this->tpl]['o_lMargin'] = $this->lMargin;
			$this->tpls[$this->tpl]['o_rMargin'] = $this->rMargin;
			$this->tpls[$this->tpl]['o_h'] = $this->h;
			$this->tpls[$this->tpl]['o_w'] = $this->w;
			$this->SetAutoPageBreak( false );

			if ($x == null) {
				$x = 0;
			}


			if ($y == null) {
				$y = 0;
			}


			if ($w == null) {
				$w = $this->w;
			}


			if ($h == null) {
				$h = $this->h;
			}

			$this->h = $h;
			$this->w = $w;
			$this->tpls[$this->tpl]['buffer'] = '';
			$this->tpls[$this->tpl]['x'] = $x;
			$this->tpls[$this->tpl]['y'] = $y;
			$this->tpls[$this->tpl]['w'] = $w;
			$this->tpls[$this->tpl]['h'] = $h;
			$this->intpl = true;
			$this->SetXY( $x + $this->lMargin, $y + $this->tMargin );
			$this->SetRightMargin( $this->w - $w + $this->rMargin );
			return $this->tpl;
		}

		function endtemplate() {
			if ($this->intpl) {
				$this->intpl = false;
				$this->SetAutoPageBreak( $this->tpls[$this->tpl]['o_AutoPageBreak'], $this->tpls[$this->tpl]['o_bMargin'] );
				$this->SetXY( $this->tpls[$this->tpl]['o_x'], $this->tpls[$this->tpl]['o_y'] );
				$this->tMargin = $this->tpls[$this->tpl]['o_tMargin'];
				$this->lMargin = $this->tpls[$this->tpl]['o_lMargin'];
				$this->rMargin = $this->tpls[$this->tpl]['o_rMargin'];
				$this->h = $this->tpls[$this->tpl]['o_h'];
				$this->w = $this->tpls[$this->tpl]['o_w'];
				return $this->tpl;
			}

			return false;
		}

		function usetemplate($tplidx, $_x = null, $_y = null, $_w = 0, $_h = 0) {
			if ($this->page <= 0) {
				$this->error( 'You have to add a page to fpdf first!' );
			}


			if (!$this->tpls[$tplidx]) {
				$this->error( 'Template does not exist!' );
			}


			if ($this->intpl) {
				$this->res['tpl'][$this->tpl]['tpls'][$tplidx] = &$this->tpls[$tplidx];
			}

			extract( $this->tpls[$tplidx] );

			if ($_x == null) {
				$_x = $x;
			}


			if ($_y == null) {
				$_y = $y;
			}

			$wh = $this->getTemplateSize( $tplidx, $_w, $_h );
			$_w = $wh['w'];
			$_h = $wh['h'];
			$this->_out( sprintf( 'q %.4f 0 0 %.4f %.2f %.2f cm', $_w / $w, $_h / $h, $_x * $this->k, ( $this->h - ( $_y + $_h ) ) * $this->k ) );
			$this->_out( $this->tplprefix . $tplidx . ' Do Q' );
			return array( 'w' => $_w, 'h' => $_h );
		}

		function gettemplatesize($tplidx, $_w = 0, $_h = 0) {
			if (!$this->tpls[$tplidx]) {
				return false;
			}

			extract( $this->tpls[$tplidx] );

			if (( $_w == 0 && $_h == 0 )) {
				$_w = $w;
				$_h = $h;
			}


			if ($_w == 0) {
				$_w = $_h * $w / $h;
			}


			if ($_h == 0) {
				$_h = $_w * $h / $w;
			}

			return array( 'w' => $_w, 'h' => $_h );
		}

		function setfont($family, $style = '', $size = 0) {
			global $fpdf_charwidths;

			$family = strtolower( $family );

			if ($family == '') {
				$family = $this->FontFamily;
			}


			if ($family == 'arial') {
				$family = 'helvetica';
			} 
else {
				if (( $family == 'symbol' || $family == 'zapfdingbats' )) {
					$style = '';
				}
			}

			$style = strtoupper( $style );

			if (is_int( strpos( $style, 'U' ) )) {
				$this->underline = true;
				$style = str_replace( 'U', '', $style );
			} 
else {
				$this->underline = false;
			}


			if ($style == 'IB') {
				$style = 'BI';
			}


			if ($size == 0) {
				$size = $this->FontSizePt;
			}


			if (( ( ( $this->FontFamily == $family && $this->FontStyle == $style ) && $this->FontSizePt == $size ) && !$this->intpl )) {
				return null;
			}

			$fontkey = $family . $style;

			if (!isset( $this->fonts[$fontkey] )) {
				if (isset( $this->CoreFonts[$fontkey] )) {
					if (!isset( $fpdf_charwidths[$fontkey] )) {
						$file = $family;

						if (( $family == 'times' || $family == 'helvetica' )) {
							$file .= strtolower( $style );
						}

						$file .= '.php';

						if (defined( 'FPDF_FONTPATH' )) {
							$file = FPDF_FONTPATH . $file;
						}

						include( $file );

						if (!isset( $fpdf_charwidths[$fontkey] )) {
							$this->Error( 'Could not include font metric file' );
						}
					}

					$i = $this->findNextAvailFont(  );
					$this->fonts[$fontkey] = array( 'i' => $i, 'type' => 'core', 'name' => $this->CoreFonts[$fontkey], 'up' => 0 - 100, 'ut' => 50, 'cw' => $fpdf_charwidths[$fontkey] );
				} 
else {
					$this->Error( 'Undefined font: ' . $family . ' ' . $style );
				}
			}

			$this->FontFamily = $family;
			$this->FontStyle = $style;
			$this->FontSizePt = $size;
			$this->FontSize = $size / $this->k;
			$this->CurrentFont = &$this->fonts[$fontkey];

			if (0 < $this->page) {
				$this->_out( sprintf( 'BT ' . $this->fontprefix . '%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt ) );
			}


			if ($this->intpl) {
				$this->res['tpl'][$this->tpl]['fonts'][$fontkey] = &$this->fonts[$fontkey];

				return null;
			}

			$this->res['page'][$this->page]['fonts'][$fontkey] = &$this->fonts[$fontkey];

		}

		function findnextavailfont() {
			return count( $this->fonts ) + 1;
		}

		function image($file, $x, $y, $w = 0, $h = 0, $type = '', $link = '') {
			fpdf::image( $file, $x, $y, $w, $h, $type, $link );

			if ($this->intpl) {
				$this->res['tpl'][$this->tpl]['images'][$file] = &$this->images[$file];

				return null;
			}

			$this->res['page'][$this->page]['images'][$file] = &$this->images[$file];

		}

		function addpage($orientation = '') {
			if ($this->intpl) {
				$this->Error( 'Adding pages in templates isn\'t possible!' );
			}

			fpdf::addpage( $orientation );
		}

		function link($x, $y, $w, $h, $link) {
			if ($this->intpl) {
				$this->Error( 'Using links in templates aren\'t possible!' );
			}

			fpdf::link( $x, $y, $w, $h, $link );
		}

		function addlink() {
			if ($this->intpl) {
				$this->Error( 'Adding links in templates aren\'t possible!' );
			}

			return fpdf::addlink(  );
		}

		function setlink($link, $y = 0, $page = -1) {
			if ($this->intpl) {
				$this->Error( 'Setting links in templates aren\'t possible!' );
			}

			fpdf::setlink( $link, $y, $page );
		}

		function _puttemplates() {
			$filter = ($this->compress ? '/Filter /FlateDecode ' : '');
			reset( $this->tpls );
			foreach ($this->tpls as $tplidx => $tpl) {
				$p = ($this->compress ? gzcompress( $tpl['buffer'] ) : $tpl['buffer']);
				$this->_newobj(  );
				$this->tpls[$tplidx]['n'] = $this->n;
				$this->_out( '<<' . $filter . '/Type /XObject' );
				$this->_out( '/Subtype /Form' );
				$this->_out( '/FormType 1' );
				$this->_out( sprintf( '/BBox [%.2f %.2f %.2f %.2f]', $tpl['x'] * $this->k, ( $tpl['h'] - $tpl['y'] ) * $this->k, $tpl['w'] * $this->k, ( $tpl['h'] - $tpl['y'] - $tpl['h'] ) * $this->k ) );
				$this->_out( '/Resources ' );
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
				$this->_out( '/Length ' . strlen( $p ) . ' >>' );
				$this->_putstream( $p );
				$this->_out( 'endobj' );
			}

		}

		function _putresources() {
			$this->_putfonts(  );
			$this->_putimages(  );
			$this->_puttemplates(  );
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

		function _out($s) {
			if ($this->state == 2) {
				if (!$this->intpl) {
					$this->pages[$this->page] .= $s . '
';
					return null;
				}

				$this->tpls[$this->tpl]['buffer'] .= $s . '
';
				return null;
			}

			$this->buffer .= $s . '
';
		}
	}

?>