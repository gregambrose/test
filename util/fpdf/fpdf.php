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

	class fpdf {
		var $page = null;
		var $n = null;
		var $offsets = null;
		var $buffer = null;
		var $pages = null;
		var $state = null;
		var $compress = null;
		var $DefOrientation = null;
		var $CurOrientation = null;
		var $OrientationChanges = null;
		var $k = null;
		var $fwPt = null;
		var $fhPt = null;
		var $fw = null;
		var $fh = null;
		var $wPt = null;
		var $hPt = null;
		var $w = null;
		var $h = null;
		var $lMargin = null;
		var $tMargin = null;
		var $rMargin = null;
		var $bMargin = null;
		var $cMargin = null;
		var $x = null;
		var $y = null;
		var $lasth = null;
		var $LineWidth = null;
		var $CoreFonts = null;
		var $fonts = null;
		var $FontFiles = null;
		var $diffs = null;
		var $images = null;
		var $PageLinks = null;
		var $links = null;
		var $FontFamily = null;
		var $FontStyle = null;
		var $underline = null;
		var $CurrentFont = null;
		var $FontSizePt = null;
		var $FontSize = null;
		var $DrawColor = null;
		var $FillColor = null;
		var $TextColor = null;
		var $ColorFlag = null;
		var $ws = null;
		var $AutoPageBreak = null;
		var $PageBreakTrigger = null;
		var $InFooter = null;
		var $ZoomMode = null;
		var $LayoutMode = null;
		var $title = null;
		var $subject = null;
		var $author = null;
		var $keywords = null;
		var $creator = null;
		var $AliasNbPages = null;
		var $PDFVersion = null;

		function fpdf($orientation = 'P', $unit = 'mm', $format = 'A4') {
			$this->_dochecks(  );
			$this->page = 0;
			$this->n = 2;
			$this->buffer = '';
			$this->pages = array(  );
			$this->OrientationChanges = array(  );
			$this->state = 0;
			$this->fonts = array(  );
			$this->FontFiles = array(  );
			$this->diffs = array(  );
			$this->images = array(  );
			$this->links = array(  );
			$this->InFooter = false;
			$this->lasth = 0;
			$this->FontFamily = '';
			$this->FontStyle = '';
			$this->FontSizePt = 12;
			$this->underline = false;
			$this->DrawColor = '0 G';
			$this->FillColor = '0 g';
			$this->TextColor = '0 g';
			$this->ColorFlag = false;
			$this->ws = 0;
			$this->CoreFonts = array( 'courier' => 'Courier', 'courierB' => 'Courier-Bold', 'courierI' => 'Courier-Oblique', 'courierBI' => 'Courier-BoldOblique', 'helvetica' => 'Helvetica', 'helveticaB' => 'Helvetica-Bold', 'helveticaI' => 'Helvetica-Oblique', 'helveticaBI' => 'Helvetica-BoldOblique', 'times' => 'Times-Roman', 'timesB' => 'Times-Bold', 'timesI' => 'Times-Italic', 'timesBI' => 'Times-BoldItalic', 'symbol' => 'Symbol', 'zapfdingbats' => 'ZapfDingbats' );

			if ($unit == 'pt') {
				$this->k = 1;
			} 
else {
				if ($unit == 'mm') {
					$this->k = 72 / 25.3999999999999985789145;
				} 
else {
					if ($unit == 'cm') {
						$this->k = 72 / 2.54000000000000003552714;
					} 
else {
						if ($unit == 'in') {
							$this->k = 72;
						} 
else {
							$this->Error( 'Incorrect unit: ' . $unit );
						}
					}
				}
			}


			if (is_string( $format )) {
				$format = strtolower( $format );

				if ($format == 'a3') {
					$format = array( 841.889999999999986357579, 1190.54999999999995452526 );
				} 
else {
					if ($format == 'a4') {
						$format = array( 595.279999999999972715159, 841.889999999999986357579 );
					} 
else {
						if ($format == 'a5') {
							$format = array( 420.939999999999997726263, 595.279999999999972715159 );
						} 
else {
							if ($format == 'letter') {
								$format = array( 612, 792 );
							} 
else {
								if ($format == 'legal') {
									$format = array( 612, 1008 );
								} 
else {
									$this->Error( 'Unknown page format: ' . $format );
								}
							}
						}
					}
				}

				$this->fwPt = $format[0];
				$this->fhPt = $format[1];
			} 
else {
				$this->fwPt = $format[0] * $this->k;
				$this->fhPt = $format[1] * $this->k;
			}

			$this->fw = $this->fwPt / $this->k;
			$this->fh = $this->fhPt / $this->k;
			$orientation = strtolower( $orientation );

			if (( $orientation == 'p' || $orientation == 'portrait' )) {
				$this->DefOrientation = 'P';
				$this->wPt = $this->fwPt;
				$this->hPt = $this->fhPt;
			} 
else {
				if (( $orientation == 'l' || $orientation == 'landscape' )) {
					$this->DefOrientation = 'L';
					$this->wPt = $this->fhPt;
					$this->hPt = $this->fwPt;
				} 
else {
					$this->Error( 'Incorrect orientation: ' . $orientation );
				}
			}

			$this->CurOrientation = $this->DefOrientation;
			$this->w = $this->wPt / $this->k;
			$this->h = $this->hPt / $this->k;
			$margin = 28.3500000000000014210855 / $this->k;
			$this->SetMargins( $margin, $margin );
			$this->cMargin = $margin / 10;
			$this->LineWidth = 0.566999999999999948485652 / $this->k;
			$this->SetAutoPageBreak( true, 2 * $margin );
			$this->SetDisplayMode( 'fullwidth' );
			$this->SetCompression( true );
			$this->PDFVersion = '1.3';
		}

		function setmargins($left, $top, $right = -1) {
			$this->lMargin = $left;
			$this->tMargin = $top;

			if ($right == 0 - 1) {
				$right = $left;
			}

			$this->rMargin = $right;
		}

		function setleftmargin($margin) {
			$this->lMargin = $margin;

			if (( 0 < $this->page && $this->x < $margin )) {
				$this->x = $margin;
			}

		}

		function getleftmargin() {
			return $this->lMargin;
		}

		function settopmargin($margin) {
			$this->tMargin = $margin;
		}

		function setrightmargin($margin) {
			$this->rMargin = $margin;
		}

		function getrightmargin() {
			return $this->rMargin;
		}

		function setautopagebreak($auto, $margin = 0) {
			$this->AutoPageBreak = $auto;
			$this->bMargin = $margin;
			$this->PageBreakTrigger = $this->h - $margin;
		}

		function setdisplaymode($zoom, $layout = 'continuous') {
			if (( ( ( ( $zoom == 'fullpage' || $zoom == 'fullwidth' ) || $zoom == 'real' ) || $zoom == 'default' ) || !is_string( $zoom ) )) {
				$this->ZoomMode = $zoom;
			} 
else {
				$this->Error( 'Incorrect zoom display mode: ' . $zoom );
			}


			if (( ( ( $layout == 'single' || $layout == 'continuous' ) || $layout == 'two' ) || $layout == 'default' )) {
				$this->LayoutMode = $layout;
				return null;
			}

			$this->Error( 'Incorrect layout display mode: ' . $layout );
		}

		function setcompression($compress) {
			if (function_exists( 'gzcompress' )) {
				$this->compress = $compress;
				return null;
			}

			$this->compress = false;
		}

		function settitle($title) {
			$this->title = $title;
		}

		function setsubject($subject) {
			$this->subject = $subject;
		}

		function setauthor($author) {
			$this->author = $author;
		}

		function setkeywords($keywords) {
			$this->keywords = $keywords;
		}

		function setcreator($creator) {
			$this->creator = $creator;
		}

		function aliasnbpages($alias = '{nb}') {
			$this->AliasNbPages = $alias;
		}

		function error($msg) {
			exit( '<B>FPDF error: </B>' . $msg );
		}

		function open() {
			$this->state = 1;
		}

		function close() {
			if ($this->state == 3) {
				return null;
			}


			if ($this->page == 0) {
				$this->AddPage(  );
			}

			$this->InFooter = true;
			$this->Footer(  );
			$this->InFooter = false;
			$this->_endpage(  );
			$this->_enddoc(  );
		}

		function addpage($orientation = '') {
			if ($this->state == 0) {
				$this->Open(  );
			}

			$family = $this->FontFamily;
			$style = $this->FontStyle . ($this->underline ? 'U' : '');
			$size = $this->FontSizePt;
			$lw = $this->LineWidth;
			$dc = $this->DrawColor;
			$fc = $this->FillColor;
			$tc = $this->TextColor;
			$cf = $this->ColorFlag;

			if (0 < $this->page) {
				$this->InFooter = true;
				$this->Footer(  );
				$this->InFooter = false;
				$this->_endpage(  );
			}

			$this->_beginpage( $orientation );
			$this->_out( '2 J' );
			$this->LineWidth = $lw;
			$this->_out( sprintf( '%.2f w', $lw * $this->k ) );

			if ($family) {
				$this->SetFont( $family, $style, $size );
			}

			$this->DrawColor = $dc;

			if ($dc != '0 G') {
				$this->_out( $dc );
			}

			$this->FillColor = $fc;

			if ($fc != '0 g') {
				$this->_out( $fc );
			}

			$this->TextColor = $tc;
			$this->ColorFlag = $cf;
			$this->Header(  );

			if ($this->LineWidth != $lw) {
				$this->LineWidth = $lw;
				$this->_out( sprintf( '%.2f w', $lw * $this->k ) );
			}


			if ($family) {
				$this->SetFont( $family, $style, $size );
			}


			if ($this->DrawColor != $dc) {
				$this->DrawColor = $dc;
				$this->_out( $dc );
			}


			if ($this->FillColor != $fc) {
				$this->FillColor = $fc;
				$this->_out( $fc );
			}

			$this->TextColor = $tc;
			$this->ColorFlag = $cf;
		}

		function header() {
		}

		function footer() {
		}

		function pageno() {
			return $this->page;
		}

		function setdrawcolor($r, $g = -1, $b = -1) {
			if (( ( ( $r == 0 && $g == 0 ) && $b == 0 ) || $g == 0 - 1 )) {
				$this->DrawColor = sprintf( '%.3f G', $r / 255 );
			} 
else {
				$this->DrawColor = sprintf( '%.3f %.3f %.3f RG', $r / 255, $g / 255, $b / 255 );
			}


			if (0 < $this->page) {
				$this->_out( $this->DrawColor );
			}

		}

		function setfillcolor($r, $g = -1, $b = -1) {
			if (( ( ( $r == 0 && $g == 0 ) && $b == 0 ) || $g == 0 - 1 )) {
				$this->FillColor = sprintf( '%.3f g', $r / 255 );
			} 
else {
				$this->FillColor = sprintf( '%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255 );
			}

			$this->ColorFlag = $this->FillColor != $this->TextColor;

			if (0 < $this->page) {
				$this->_out( $this->FillColor );
			}

		}

		function settextcolor($r, $g = -1, $b = -1) {
			if (( ( ( $r == 0 && $g == 0 ) && $b == 0 ) || $g == 0 - 1 )) {
				$this->TextColor = sprintf( '%.3f g', $r / 255 );
			} 
else {
				$this->TextColor = sprintf( '%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255 );
			}

			$this->ColorFlag = $this->FillColor != $this->TextColor;
		}

		function getstringwidth($s) {
			$s = (bool)$s;
			$cw = &$this->CurrentFont['cw'];

			$w = 0;
			$l = strlen( $s );
			$i = 0;

			while ($i < $l) {
				$w += $cw[$s[$i]];
				++$i;
			}

			return $w * $this->FontSize / 1000;
		}

		function setlinewidth($width) {
			$this->LineWidth = $width;

			if (0 < $this->page) {
				$this->_out( sprintf( '%.2f w', $width * $this->k ) );
			}

		}

		function line($x1, $y1, $x2, $y2) {
			$this->_out( sprintf( '%.2f %.2f m %.2f %.2f l S', $x1 * $this->k, ( $this->h - $y1 ) * $this->k, $x2 * $this->k, ( $this->h - $y2 ) * $this->k ) );
		}

		function rect($x, $y, $w, $h, $style = '') {
			if ($style == 'F') {
				$op = 'f';
			} 
else {
				if (( $style == 'FD' || $style == 'DF' )) {
					$op = 'B';
				} 
else {
					$op = 'S';
				}
			}

			$this->_out( sprintf( '%.2f %.2f %.2f %.2f re %s', $x * $this->k, ( $this->h - $y ) * $this->k, $w * $this->k, 0 - $h * $this->k, $op ) );
		}

		function addfont($family, $style = '', $file = '') {
			$family = strtolower( $family );

			if ($file == '') {
				$file = str_replace( ' ', '', $family ) . strtolower( $style ) . '.php';
			}


			if ($family == 'arial') {
				$family = 'helvetica';
			}

			$style = strtoupper( $style );

			if ($style == 'IB') {
				$style = 'BI';
			}

			$fontkey = $family . $style;

			if (isset( $this->fonts[$fontkey] )) {
				$this->Error( 'Font already added: ' . $family . ' ' . $style );
			}

			include( $this->_getfontpath(  ) . $file );

			if (!isset( $name )) {
				$this->Error( 'Could not include font definition file' );
			}

			$i = count( $this->fonts ) + 1;
			$this->fonts[$fontkey] = array( 'i' => $i, 'type' => $type, 'name' => $name, 'desc' => $desc, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'enc' => $enc, 'file' => $file );

			if ($diff) {
				$d = 0;
				$nb = count( $this->diffs );
				$i = 1;

				while ($i <= $nb) {
					if ($this->diffs[$i] == $diff) {
						$d = $i;
						break;
					}

					++$i;
				}


				if ($d == 0) {
					$d = $nb + 1;
					$this->diffs[$d] = $diff;
				}

				$this->fonts[$fontkey]['diff'] = $d;
			}


			if ($file) {
				if ($type == 'TrueType') {
					$this->FontFiles[$file] = array( 'length1' => $originalsize );
					return null;
				}

				$this->FontFiles[$file] = array( 'length1' => $size1, 'length2' => $size2 );
			}

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

			if (strpos( $style, 'U' ) !== false) {
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


			if (( ( $this->FontFamily == $family && $this->FontStyle == $style ) && $this->FontSizePt == $size )) {
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

						include( $this->_getfontpath(  ) . $file . '.php' );

						if (!isset( $fpdf_charwidths[$fontkey] )) {
							$this->Error( 'Could not include font metric file' );
						}
					}

					$i = count( $this->fonts ) + 1;
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
				$this->_out( sprintf( 'BT /F%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt ) );
			}

		}

		function setfontsize($size) {
			if ($this->FontSizePt == $size) {
				return null;
			}

			$this->FontSizePt = $size;
			$this->FontSize = $size / $this->k;

			if (0 < $this->page) {
				$this->_out( sprintf( 'BT /F%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt ) );
			}

		}

		function addlink() {
			$n = count( $this->links ) + 1;
			$this->links[$n] = array( 0, 0 );
			return $n;
		}

		function setlink($link, $y = 0, $page = -1) {
			if ($y == 0 - 1) {
				$y = $this->y;
			}


			if ($page == 0 - 1) {
				$page = $this->page;
			}

			$this->links[$link] = array( $page, $y );
		}

		function link($x, $y, $w, $h, $link) {
			$this->PageLinks[$this->page][] = array( $x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h * $this->k, $link );
		}

		function text($x, $y, $txt) {
			$s = sprintf( 'BT %.2f %.2f Td (%s) Tj ET', $x * $this->k, ( $this->h - $y ) * $this->k, $this->_escape( $txt ) );

			if (( $this->underline && $txt != '' )) {
				$s .= ' ' . $this->_dounderline( $x, $y, $txt );
			}


			if ($this->ColorFlag) {
				$s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
			}

			$this->_out( $s );
		}

		function acceptpagebreak() {
			return $this->AutoPageBreak;
		}

		function cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '') {
			$k = $this->k;

			if (( ( $this->PageBreakTrigger < $this->y + $h && !$this->InFooter ) && $this->AcceptPageBreak(  ) )) {
				$x = $this->x;
				$ws = $this->ws;

				if (0 < $ws) {
					$this->ws = 0;
					$this->_out( '0 Tw' );
				}

				$this->AddPage( $this->CurOrientation );
				$this->x = $x;

				if (0 < $ws) {
					$this->ws = $ws;
					$this->_out( sprintf( '%.3f Tw', $ws * $k ) );
				}
			}


			if ($w == 0) {
				$w = $this->w - $this->rMargin - $this->x;
			}

			$s = '';

			if (( $fill == 1 || $border == 1 )) {
				if ($fill == 1) {
					$op = ($border == 1 ? 'B' : 'f');
				} 
else {
					$op = 'S';
				}

				$s = sprintf( '%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, 0 - $h * $k, $op );
			}


			if (is_string( $border )) {
				$x = $this->x;
				$y = $this->y;

				if (strpos( $border, 'L' ) !== false) {
					$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
				}


				if (strpos( $border, 'T' ) !== false) {
					$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
				}


				if (strpos( $border, 'R' ) !== false) {
					$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
				}


				if (strpos( $border, 'B' ) !== false) {
					$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
				}
			}


			if ($txt !== '') {
				if ($align == 'R') {
					$dx = $w - $this->cMargin - $this->GetStringWidth( $txt );
				} 
else {
					if ($align == 'C') {
						$dx = ( $w - $this->GetStringWidth( $txt ) ) / 2;
					} 
else {
						$dx = $this->cMargin;
					}
				}


				if ($this->ColorFlag) {
					$s .= 'q ' . $this->TextColor . ' ';
				}

				$txt2 = str_replace( ')', '\)', str_replace( '(', '\(', str_replace( '\', '\\', $txt ) ) );
				$s .= sprintf( 'BT %.2f %.2f Td (%s) Tj ET', ( $this->x + $dx ) * $k, ( $this->h - ( $this->y + 0.5 * $h + 0.29999999999999998889777 * $this->FontSize ) ) * $k, $txt2 );

				if ($this->underline) {
					$s .= ' ' . $this->_dounderline( $this->x + $dx, $this->y + 0.5 * $h + 0.29999999999999998889777 * $this->FontSize, $txt );
				}


				if ($this->ColorFlag) {
					$s .= ' Q';
				}


				if ($link) {
					$this->Link( $this->x + $dx, $this->y + 0.5 * $h - 0.5 * $this->FontSize, $this->GetStringWidth( $txt ), $this->FontSize, $link );
				}
			}


			if ($s) {
				$this->_out( $s );
			}

			$this->lasth = $h;

			if (0 < $ln) {
				$this->y += $h;

				if ($ln == 1) {
					$this->x = $this->lMargin;
					return null;
				}
			} 
else {
				$this->x += $w;
			}

		}

		function multicell($w, $h, $txt, $border = 0, $align = 'J', $fill = 0) {
			$cw = &$this->CurrentFont['cw'];

			if ($w == 0) {
				$w = $this->w - $this->rMargin - $this->x;
			}

			$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
			$s = str_replace( '', '', $txt );
			$nb = strlen( $s );

			if (( 0 < $nb && $s[$nb - 1] == '
' )) {
				--$nb;
			}

			$b = 0;

			if ($border) {
				if ($border == 1) {
					$border = 'LTRB';
					$b = 'LRT';
					$b2 = 'LR';
				} 
else {
					$b2 = '';

					if (strpos( $border, 'L' ) !== false) {
						$b2 .= 'L';
					}


					if (strpos( $border, 'R' ) !== false) {
						$b2 .= 'R';
					}

					$b = (strpos( $border, 'T' ) !== false ? $b2 . 'T' : $b2);
				}
			}

			$sep = 0 - 1;
			$i = 0;
			$j = 0;
			$l = 0;
			$ns = 0;
			$nl = 1;

			while ($i < $nb) {
				$c = $s[$i];

				if ($c == '
') {
					if (0 < $this->ws) {
						$this->ws = 0;
						$this->_out( '0 Tw' );
					}

					$this->Cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
					++$i;
					$sep = 0 - 1;
					$j = $i;
					$l = 0;
					$ns = 0;
					++$nl;

					if (( $border && $nl == 2 )) {
						$b = $b2;
					}

					continue;
				}


				if ($c == ' ') {
					$sep = $i;
					$ls = $l;
					++$ns;
				}

				$l += $cw[$c];

				if ($wmax < $l) {
					if ($sep == 0 - 1) {
						if ($i == $j) {
							++$i;
						}


						if (0 < $this->ws) {
							$this->ws = 0;
							$this->_out( '0 Tw' );
						}

						$this->Cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
					} 
else {
						if ($align == 'J') {
							$this->ws = (1 < $ns ? ( $wmax - $ls ) / 1000 * $this->FontSize / ( $ns - 1 ) : 0);
							$this->_out( sprintf( '%.3f Tw', $this->ws * $this->k ) );
						}

						$this->Cell( $w, $h, substr( $s, $j, $sep - $j ), $b, 2, $align, $fill );
						$i = $sep + 1;
					}

					$sep = 0 - 1;
					$j = $i;
					$l = 0;
					$ns = 0;
					++$nl;

					if (( $border && $nl == 2 )) {
						$b = $b2;
						continue;
					}

					continue;
				}

				++$i;
			}


			if (0 < $this->ws) {
				$this->ws = 0;
				$this->_out( '0 Tw' );
			}


			if (( $border && strpos( $border, 'B' ) !== false )) {
				$b .= 'B';
			}

			$this->Cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
			$this->x = $this->lMargin;
		}

		function write($h, $txt, $link = '') {
			$cw = &$this->CurrentFont['cw'];

			$w = $this->w - $this->rMargin - $this->x;
			$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
			$s = str_replace( '', '', $txt );
			$nb = strlen( $s );
			$sep = 0 - 1;
			$i = 0;
			$j = 0;
			$l = 0;
			$nl = 1;

			while ($i < $nb) {
				$c = $s[$i];

				if ($c == '
') {
					$this->Cell( $w, $h, substr( $s, $j, $i - $j ), 0, 2, '', 0, $link );
					++$i;
					$sep = 0 - 1;
					$j = $i;
					$l = 0;

					if ($nl == 1) {
						$this->x = $this->lMargin;
						$w = $this->w - $this->rMargin - $this->x;
						$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
					}

					++$nl;
					continue;
				}


				if ($c == ' ') {
					$sep = $i;
				}

				$l += $cw[$c];

				if ($wmax < $l) {
					if ($sep == 0 - 1) {
						if ($this->lMargin < $this->x) {
							$this->x = $this->lMargin;
							$this->y += $h;
							$w = $this->w - $this->rMargin - $this->x;
							$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
							++$i;
							++$nl;
							continue;
						}


						if ($i == $j) {
							++$i;
						}

						$this->Cell( $w, $h, substr( $s, $j, $i - $j ), 0, 2, '', 0, $link );
					} 
else {
						$this->Cell( $w, $h, substr( $s, $j, $sep - $j ), 0, 2, '', 0, $link );
						$i = $sep + 1;
					}

					$sep = 0 - 1;
					$j = $i;
					$l = 0;

					if ($nl == 1) {
						$this->x = $this->lMargin;
						$w = $this->w - $this->rMargin - $this->x;
						$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
					}

					++$nl;
					continue;
				}

				++$i;
			}


			if ($i != $j) {
				$this->Cell( $l / 1000 * $this->FontSize, $h, substr( $s, $j ), 0, 0, '', 0, $link );
			}

		}

		function image($file, $x, $y, $w = 0, $h = 0, $type = '', $link = '') {
			if (!isset( $this->images[$file] )) {
				if ($type == '') {
					$pos = strrpos( $file, '.' );

					if (!$pos) {
						$this->Error( 'Image file has no extension and no type was specified: ' . $file );
					}

					$type = substr( $file, $pos + 1 );
				}

				$type = strtolower( $type );
				$mqr = get_magic_quotes_runtime(  );
				set_magic_quotes_runtime( 0 );

				if (( $type == 'jpg' || $type == 'jpeg' )) {
					$info = $this->_parsejpg( $file );
				} 
else {
					if ($type == 'png') {
						$info = $this->_parsepng( $file );
					} 
else {
						$mtd = '_parse' . $type;

						if (!method_exists( $this, $mtd )) {
							$this->Error( 'Unsupported image type: ' . $type );
						}

						$info = $this->$mtd( $file );
					}
				}

				set_magic_quotes_runtime( $mqr );
				$info['i'] = count( $this->images ) + 1;
				$this->images[$file] = $info;
			} 
else {
				$info = $this->images[$file];
			}


			if (( $w == 0 && $h == 0 )) {
				$w = $info['w'] / $this->k;
				$h = $info['h'] / $this->k;
			}


			if ($w == 0) {
				$w = $h * $info['w'] / $info['h'];
			}


			if ($h == 0) {
				$h = $w * $info['h'] / $info['w'];
			}

			$this->_out( sprintf( 'q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ( $this->h - ( $y + $h ) ) * $this->k, $info['i'] ) );

			if ($link) {
				$this->Link( $x, $y, $w, $h, $link );
			}

		}

		function ln($h = '') {
			$this->x = $this->lMargin;

			if (is_string( $h )) {
				$this->y += $this->lasth;
				return null;
			}

			$this->y += $h;
		}

		function getx() {
			return $this->x;
		}

		function setx($x) {
			if (0 <= $x) {
				$this->x = $x;
				return null;
			}

			$this->x = $this->w + $x;
		}

		function gety() {
			return $this->y;
		}

		function sety($y) {
			$this->x = $this->lMargin;

			if (0 <= $y) {
				$this->y = $y;
				return null;
			}

			$this->y = $this->h + $y;
		}

		function setxy($x, $y) {
			$this->SetY( $y );
			$this->SetX( $x );
		}

		function output($name = '', $dest = '') {
			if ($this->state < 3) {
				$this->Close(  );
			}


			if (is_bool( $dest )) {
				$dest = ($dest ? 'D' : 'F');
			}

			$dest = strtoupper( $dest );

			if ($dest == '') {
				if ($name == '') {
					$name = 'doc.pdf';
					$dest = 'I';
				} 
else {
					$dest = 'F';
				}
			}

			switch ($dest) {
				case 'I': {
					if (php_sapi_name(  ) != 'cli') {
						header( 'Content-Type: application/pdf' );
						header( 'Content-Length: ' . strlen( $this->buffer ) );
						header( 'Content-disposition: inline; filename="' . $name . '"' );
					}

					echo $this->buffer;
					break;
				}

				case 'D': {
					if (ob_get_contents(  )) {
						$this->Error( 'Some data has already been output, can\'t send PDF file' );
					}


					if (( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) )) {
						header( 'Content-Type: application/force-download' );
					} 
else {
						header( 'Content-Type: application/octet-stream' );
					}


					if (headers_sent(  )) {
						$this->Error( 'Some data has already been output to browser, can\'t send PDF file' );
					}

					header( 'Content-Length: ' . strlen( $this->buffer ) );
					header( 'Content-disposition: attachment; filename="' . $name . '"' );
					echo $this->buffer;
					break;
				}

				case 'F': {
					$f = fopen( $name, 'wb' );

					if (!$f) {
						$this->Error( 'Unable to create output file: ' . $name );
					}

					fwrite( $f, $this->buffer, strlen( $this->buffer ) );
					fclose( $f );
					break;
				}

				case 'S': {
					$this->buffer;
				}
			}

			return ;
		}

		function _dochecks() {
			if (1.10000000000000008881784 == 1) {
				$this->Error( 'Don\'t alter the locale before including class file' );
			}


			if (sprintf( '%.1f', 1 ) != '1.0') {
				setlocale( LC_NUMERIC, 'C' );
			}

		}

		function _getfontpath() {
			if (( !defined( 'FPDF_FONTPATH' ) && is_dir( dirname( __FILE__ ) . '/font' ) )) {
				define( 'FPDF_FONTPATH', dirname( __FILE__ ) . '/font/' );
			}

			return (defined( 'FPDF_FONTPATH' ) ? FPDF_FONTPATH : '');
		}

		function _putpages() {
			$nb = $this->page;

			if (!empty( $this->AliasNbPages )) {
				$n = 1;

				while ($n <= $nb) {
					$this->pages[$n] = str_replace( $this->AliasNbPages, $nb, $this->pages[$n] );
					++$n;
				}
			}


			if ($this->DefOrientation == 'P') {
				$wPt = $this->fwPt;
				$hPt = $this->fhPt;
			} 
else {
				$wPt = $this->fhPt;
				$hPt = $this->fwPt;
			}

			$filter = ($this->compress ? '/Filter /FlateDecode ' : '');
			$n = 1;

			while ($n <= $nb) {
				$this->_newobj(  );
				$this->_out( '<</Type /Page' );
				$this->_out( '/Parent 1 0 R' );

				if (isset( $this->OrientationChanges[$n] )) {
					$this->_out( sprintf( '/MediaBox [0 0 %.2f %.2f]', $hPt, $wPt ) );
				}

				$this->_out( '/Resources 2 0 R' );

				if (isset( $this->PageLinks[$n] )) {
					$annots = '/Annots [';
					foreach ($this->PageLinks[$n] as $pl) {
						$rect = sprintf( '%.2f %.2f %.2f %.2f', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3] );
						$annots .= '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';

						if (is_string( $pl[4] )) {
							$annots .= '/A <</S /URI /URI ' . $this->_textstring( $pl[4] ) . '>>>>';
							continue;
						}

						$l = $this->links[$pl[4]];
						$h = (isset( $this->OrientationChanges[$l[0]] ) ? $wPt : $hPt);
						$annots .= sprintf( '/Dest [%d 0 R /XYZ 0 %.2f null]>>', 1 + 2 * $l[0], $h - $l[1] * $this->k );
					}

					$this->_out( $annots . ']' );
				}

				$this->_out( '/Contents ' . ( $this->n + 1 ) . ' 0 R>>' );
				$this->_out( 'endobj' );
				$p = ($this->compress ? gzcompress( $this->pages[$n] ) : $this->pages[$n]);
				$this->_newobj(  );
				$this->_out( '<<' . $filter . '/Length ' . strlen( $p ) . '>>' );
				$this->_putstream( $p );
				$this->_out( 'endobj' );
				++$n;
			}

			$this->offsets[1] = strlen( $this->buffer );
			$this->_out( '1 0 obj' );
			$this->_out( '<</Type /Pages' );
			$kids = '/Kids [';
			$i = 0;

			while ($i < $nb) {
				$kids .= 3 + 2 * $i . ' 0 R ';
				++$i;
			}

			$this->_out( $kids . ']' );
			$this->_out( '/Count ' . $nb );
			$this->_out( sprintf( '/MediaBox [0 0 %.2f %.2f]', $wPt, $hPt ) );
			$this->_out( '>>' );
			$this->_out( 'endobj' );
		}

		function _putfonts() {
			$nf = $this->n;
			foreach ($this->diffs as $diff) {
				$this->_newobj(  );
				$this->_out( '<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $diff . ']>>' );
				$this->_out( 'endobj' );
			}

			$mqr = get_magic_quotes_runtime(  );
			set_magic_quotes_runtime( 0 );
			foreach ($this->FontFiles as $file => $info) {
				$this->_newobj(  );
				$this->FontFiles[$file]['n'] = $this->n;
				$font = '';
				$f = fopen( $this->_getfontpath(  ) . $file, 'rb', 1 );

				if (!$f) {
					$this->Error( 'Font file not found' );
				}


				while (!feof( $f )) {
					$font .= fread( $f, 8192 );
				}

				fclose( $f );
				$compressed = substr( $file, 0 - 2 ) == '.z';

				if (( !$compressed && isset( $info['length2'] ) )) {
					$header = ord( $font[0] ) == 128;

					if ($header) {
						$font = substr( $font, 6 );
					}


					if (( $header && ord( $font[$info['length1']] ) == 128 )) {
						$font = substr( $font, 0, $info['length1'] ) . substr( $font, $info['length1'] + 6 );
					}
				}

				$this->_out( '<</Length ' . strlen( $font ) );

				if ($compressed) {
					$this->_out( '/Filter /FlateDecode' );
				}

				$this->_out( '/Length1 ' . $info['length1'] );

				if (isset( $info['length2'] )) {
					$this->_out( '/Length2 ' . $info['length2'] . ' /Length3 0' );
				}

				$this->_out( '>>' );
				$this->_putstream( $font );
				$this->_out( 'endobj' );
			}

			set_magic_quotes_runtime( $mqr );
			foreach ($this->fonts as $k => $font) {
				$this->fonts[$k]['n'] = $this->n + 1;
				$type = $font['type'];
				$name = $font['name'];

				if ($type == 'core') {
					$this->_newobj(  );
					$this->_out( '<</Type /Font' );
					$this->_out( '/BaseFont /' . $name );
					$this->_out( '/Subtype /Type1' );

					if (( $name != 'Symbol' && $name != 'ZapfDingbats' )) {
						$this->_out( '/Encoding /WinAnsiEncoding' );
					}

					$this->_out( '>>' );
					$this->_out( 'endobj' );
					continue;
				}


				if (( $type == 'Type1' || $type == 'TrueType' )) {
					$this->_newobj(  );
					$this->_out( '<</Type /Font' );
					$this->_out( '/BaseFont /' . $name );
					$this->_out( '/Subtype /' . $type );
					$this->_out( '/FirstChar 32 /LastChar 255' );
					$this->_out( '/Widths ' . ( $this->n + 1 ) . ' 0 R' );
					$this->_out( '/FontDescriptor ' . ( $this->n + 2 ) . ' 0 R' );

					if ($font['enc']) {
						if (isset( $font['diff'] )) {
							$this->_out( '/Encoding ' . ( $nf + $font['diff'] ) . ' 0 R' );
						} 
else {
							$this->_out( '/Encoding /WinAnsiEncoding' );
						}
					}

					$this->_out( '>>' );
					$this->_out( 'endobj' );
					$this->_newobj(  );
					$cw = &$font['cw'];

					$s = '[';
					$i = 32;

					while ($i <= 255) {
						$s .= $cw[chr( $i )] . ' ';
						++$i;
					}

					$this->_out( $s . ']' );
					$this->_out( 'endobj' );
					$this->_newobj(  );
					$s = '<</Type /FontDescriptor /FontName /' . $name;
					foreach ($font['desc'] as $k => $v) {
						$s .= ' /' . $k . ' ' . $v;
					}

					$file = $font['file'];

					if ($file) {
						$s .= ' /FontFile' . ($type == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$file]['n'] . ' 0 R';
					}

					$this->_out( $s . '>>' );
					$this->_out( 'endobj' );
					continue;
				}

				$mtd = '_put' . strtolower( $type );

				if (!method_exists( $this, $mtd )) {
					$this->Error( 'Unsupported font type: ' . $type );
				}

				$this->$mtd( $font );
			}

		}

		function _putimages() {
			$filter = ($this->compress ? '/Filter /FlateDecode ' : '');
			reset( $this->images );

			while (list( $file, $info ) = each( $this->images )) {
				$this->_newobj(  );
				$this->images[$file]['n'] = $this->n;
				$this->_out( '<</Type /XObject' );
				$this->_out( '/Subtype /Image' );
				$this->_out( '/Width ' . $info['w'] );
				$this->_out( '/Height ' . $info['h'] );

				if ($info['cs'] == 'Indexed') {
					$this->_out( '/ColorSpace [/Indexed /DeviceRGB ' . ( strlen( $info['pal'] ) / 3 - 1 ) . ' ' . ( $this->n + 1 ) . ' 0 R]' );
				} 
else {
					$this->_out( '/ColorSpace /' . $info['cs'] );

					if ($info['cs'] == 'DeviceCMYK') {
						$this->_out( '/Decode [1 0 1 0 1 0 1 0]' );
					}
				}

				$this->_out( '/BitsPerComponent ' . $info['bpc'] );

				if (isset( $info['f'] )) {
					$this->_out( '/Filter /' . $info['f'] );
				}


				if (isset( $info['parms'] )) {
					$this->_out( $info['parms'] );
				}


				if (( isset( $info['trns'] ) && is_array( $info['trns'] ) )) {
					$trns = '';
					$i = 0;

					while ($i < count( $info['trns'] )) {
						$trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
						++$i;
					}

					$this->_out( '/Mask [' . $trns . ']' );
				}

				$this->_out( '/Length ' . strlen( $info['data'] ) . '>>' );
				$this->_putstream( $info['data'] );
				unset( $this->images[$file][data] );
				$this->_out( 'endobj' );

				if ($info['cs'] == 'Indexed') {
					$this->_newobj(  );
					$pal = ($this->compress ? gzcompress( $info['pal'] ) : $info['pal']);
					$this->_out( '<<' . $filter . '/Length ' . strlen( $pal ) . '>>' );
					$this->_putstream( $pal );
					$this->_out( 'endobj' );
					continue;
				}
			}

		}

		function _putxobjectdict() {
			foreach ($this->images as $image) {
				$this->_out( '/I' . $image['i'] . ' ' . $image['n'] . ' 0 R' );
			}

		}

		function _putresourcedict() {
			$this->_out( '/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]' );
			$this->_out( '/Font <<' );
			foreach ($this->fonts as $font) {
				$this->_out( '/F' . $font['i'] . ' ' . $font['n'] . ' 0 R' );
			}

			$this->_out( '>>' );
			$this->_out( '/XObject <<' );
			$this->_putxobjectdict(  );
			$this->_out( '>>' );
		}

		function _putresources() {
			$this->_putfonts(  );
			$this->_putimages(  );
			$this->offsets[2] = strlen( $this->buffer );
			$this->_out( '2 0 obj' );
			$this->_out( '<<' );
			$this->_putresourcedict(  );
			$this->_out( '>>' );
			$this->_out( 'endobj' );
		}

		function _putinfo() {
			$this->_out( '/Producer ' . $this->_textstring( 'FPDF ' . FPDF_VERSION ) );

			if (!empty( $this->title )) {
				$this->_out( '/Title ' . $this->_textstring( $this->title ) );
			}


			if (!empty( $this->subject )) {
				$this->_out( '/Subject ' . $this->_textstring( $this->subject ) );
			}


			if (!empty( $this->author )) {
				$this->_out( '/Author ' . $this->_textstring( $this->author ) );
			}


			if (!empty( $this->keywords )) {
				$this->_out( '/Keywords ' . $this->_textstring( $this->keywords ) );
			}


			if (!empty( $this->creator )) {
				$this->_out( '/Creator ' . $this->_textstring( $this->creator ) );
			}

			$this->_out( '/CreationDate ' . $this->_textstring( 'D:' . date( 'YmdHis' ) ) );
		}

		function _putcatalog() {
			$this->_out( '/Type /Catalog' );
			$this->_out( '/Pages 1 0 R' );

			if ($this->ZoomMode == 'fullpage') {
				$this->_out( '/OpenAction [3 0 R /Fit]' );
			} 
else {
				if ($this->ZoomMode == 'fullwidth') {
					$this->_out( '/OpenAction [3 0 R /FitH null]' );
				} 
else {
					if ($this->ZoomMode == 'real') {
						$this->_out( '/OpenAction [3 0 R /XYZ null null 1]' );
					} 
else {
						if (!is_string( $this->ZoomMode )) {
							$this->_out( '/OpenAction [3 0 R /XYZ null null ' . $this->ZoomMode / 100 . ']' );
						}
					}
				}
			}


			if ($this->LayoutMode == 'single') {
				$this->_out( '/PageLayout /SinglePage' );
				return null;
			}


			if ($this->LayoutMode == 'continuous') {
				$this->_out( '/PageLayout /OneColumn' );
				return null;
			}


			if ($this->LayoutMode == 'two') {
				$this->_out( '/PageLayout /TwoColumnLeft' );
			}

		}

		function _putheader() {
			$this->_out( '%PDF-' . $this->PDFVersion );
		}

		function _puttrailer() {
			$this->_out( '/Size ' . ( $this->n + 1 ) );
			$this->_out( '/Root ' . $this->n . ' 0 R' );
			$this->_out( '/Info ' . ( $this->n - 1 ) . ' 0 R' );
		}

		function _enddoc() {
			$this->_putheader(  );
			$this->_putpages(  );
			$this->_putresources(  );
			$this->_newobj(  );
			$this->_out( '<<' );
			$this->_putinfo(  );
			$this->_out( '>>' );
			$this->_out( 'endobj' );
			$this->_newobj(  );
			$this->_out( '<<' );
			$this->_putcatalog(  );
			$this->_out( '>>' );
			$this->_out( 'endobj' );
			$o = strlen( $this->buffer );
			$this->_out( 'xref' );
			$this->_out( '0 ' . ( $this->n + 1 ) );
			$this->_out( '0000000000 65535 f ' );
			$i = 1;

			while ($i <= $this->n) {
				$this->_out( sprintf( '%010d 00000 n ', $this->offsets[$i] ) );
				++$i;
			}

			$this->_out( 'trailer' );
			$this->_out( '<<' );
			$this->_puttrailer(  );
			$this->_out( '>>' );
			$this->_out( 'startxref' );
			$this->_out( $o );
			$this->_out( '%%EOF' );
			$this->state = 3;
		}

		function _beginpage($orientation) {
			++$this->page;
			$this->pages[$this->page] = '';
			$this->state = 2;
			$this->x = $this->lMargin;
			$this->y = $this->tMargin;
			$this->FontFamily = '';

			if (!$orientation) {
				$orientation = $this->DefOrientation;
			} 
else {
				$orientation = strtoupper( $orientation[0] );

				if ($orientation != $this->DefOrientation) {
					$this->OrientationChanges[$this->page] = true;
				}
			}


			if ($orientation != $this->CurOrientation) {
				if ($orientation == 'P') {
					$this->wPt = $this->fwPt;
					$this->hPt = $this->fhPt;
					$this->w = $this->fw;
					$this->h = $this->fh;
				} 
else {
					$this->wPt = $this->fhPt;
					$this->hPt = $this->fwPt;
					$this->w = $this->fh;
					$this->h = $this->fw;
				}

				$this->PageBreakTrigger = $this->h - $this->bMargin;
				$this->CurOrientation = $orientation;
			}

		}

		function _endpage() {
			$this->state = 1;
		}

		function _newobj() {
			++$this->n;
			$this->offsets[$this->n] = strlen( $this->buffer );
			$this->_out( $this->n . ' 0 obj' );
		}

		function _dounderline($x, $y, $txt) {
			$up = $this->CurrentFont['up'];
			$ut = $this->CurrentFont['ut'];
			$w = $this->GetStringWidth( $txt ) + $this->ws * substr_count( $txt, ' ' );
			return sprintf( '%.2f %.2f %.2f %.2f re f', $x * $this->k, ( $this->h - ( $y - $up / 1000 * $this->FontSize ) ) * $this->k, $w * $this->k, 0 - $ut / 1000 * $this->FontSizePt );
		}

		function _parsejpg($file) {
			$a = getimagesize( $file );

			if (!$a) {
				$this->Error( 'Missing or incorrect image file: ' . $file );
			}


			if ($a[2] != 2) {
				$this->Error( 'Not a JPEG file: ' . $file );
			}


			if (( !isset( $a['channels'] ) || $a['channels'] == 3 )) {
				$colspace = 'DeviceRGB';
			} 
else {
				if ($a['channels'] == 4) {
					$colspace = 'DeviceCMYK';
				} 
else {
					$colspace = 'DeviceGray';
				}
			}

			$bpc = (isset( $a['bits'] ) ? $a['bits'] : 8);
			$f = fopen( $file, 'rb' );
			$data = '';

			while (!feof( $f )) {
				$data .= fread( $f, 4096 );
			}

			fclose( $f );
			return array( 'w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data );
		}

		function _parsepng($file) {
			$f = fopen( $file, 'rb' );

			if (!$f) {
				$this->Error( 'Can\'t open image file: ' . $file );
			}


			if (fread( $f, 8 ) != chr( 137 ) . 'PNG' . chr( 13 ) . chr( 10 ) . chr( 26 ) . chr( 10 )) {
				$this->Error( 'Not a PNG file: ' . $file );
			}

			fread( $f, 4 );

			if (fread( $f, 4 ) != 'IHDR') {
				$this->Error( 'Incorrect PNG file: ' . $file );
			}

			$w = $this->_freadint( $f );
			$h = $this->_freadint( $f );
			$bpc = ord( fread( $f, 1 ) );

			if (8 < $bpc) {
				$this->Error( '16-bit depth not supported: ' . $file );
			}

			$ct = ord( fread( $f, 1 ) );

			if ($ct == 0) {
				$colspace = 'DeviceGray';
			} 
else {
				if ($ct == 2) {
					$colspace = 'DeviceRGB';
				} 
else {
					if ($ct == 3) {
						$colspace = 'Indexed';
					} 
else {
						$this->Error( 'Alpha channel not supported: ' . $file );
					}
				}
			}


			if (ord( fread( $f, 1 ) ) != 0) {
				$this->Error( 'Unknown compression method: ' . $file );
			}


			if (ord( fread( $f, 1 ) ) != 0) {
				$this->Error( 'Unknown filter method: ' . $file );
			}


			if (ord( fread( $f, 1 ) ) != 0) {
				$this->Error( 'Interlacing not supported: ' . $file );
			}

			fread( $f, 4 );
			$parms = '/DecodeParms <</Predictor 15 /Colors ' . ($ct == 2 ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . '>>';
			$pal = '';
			$trns = '';
			$data = '';

			do {
				$n = $this->_freadint( $f );
				$type = fread( $f, 4 );

				if ($type == 'PLTE') {
					$pal = fread( $f, $n );
					fread( $f, 4 );
					continue;
				} 
else {
					if ($type == 'tRNS') {
						$t = fread( $f, $n );

						if ($ct == 0) {
							$trns = array( ord( substr( $t, 1, 1 ) ) );
						} 
else {
							if ($ct == 2) {
								$trns = array( ord( substr( $t, 1, 1 ) ), ord( substr( $t, 3, 1 ) ), ord( substr( $t, 5, 1 ) ) );
							} 
else {
								$pos = strpos( $t, chr( 0 ) );

								if ($pos !== false) {
									$trns = array( $pos );
								}
							}
						}

						fread( $f, 4 );
						continue;
					} 
else {
						if ($type == 'IDAT') {
							$data .= fread( $f, $n );
							fread( $f, 4 );
							continue;
						} 
else {
							if ($type == 'IEND') {
								break;
							}

							fread( $f, $n + 4 );
						}
					}
				}
			}while (!( $n));


			if (( $colspace == 'Indexed' && empty( $pal ) )) {
				$this->Error( 'Missing palette in ' . $file );
			}

			fclose( $f );
			return array( 'w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'parms' => $parms, 'pal' => $pal, 'trns' => $trns, 'data' => $data );
		}

		function _freadint($f) {
			$a = unpack( 'Ni', fread( $f, 4 ) );
			return $a['i'];
		}

		function _textstring($s) {
			return '(' . $this->_escape( $s ) . ')';
		}

		function _escape($s) {
			return str_replace( ')', '\)', str_replace( '(', '\(', str_replace( '\', '\\', $s ) ) );
		}

		function _putstream($s) {
			$this->_out( 'stream' );
			$this->_out( $s );
			$this->_out( 'endstream' );
		}

		function _out($s) {
			if ($this->state == 2) {
				$this->pages[$this->page] .= $s . '
';
				return null;
			}

			$this->buffer .= $s . '
';
		}
	}


	if (!class_exists( 'FPDF' )) {
		define( 'FPDF_VERSION', '1.53' );

		if (( isset( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] == 'contype' )) {
			header( 'Content-Type: application/pdf' );
			exit(  );
		}
	}

?>