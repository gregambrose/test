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

	class updf {
		var $pdf = null;
		var $boxX = null;
		var $boxY = null;
		var $boxW = null;
		var $boxHPad = null;
		var $boxVPad = null;
		var $boxLowest = 0;
		var $boxLineWidth = 0.40000000000000002220446;
		var $tabsSet = array(  );
		var $columns = null;
		var $widths = null;
		var $height = 3;
		var $text = null;
		var $columnJustify = null;
		var $columnColours = null;
		var $columnFillColours = null;
		var $_tableX = null;
		var $_tableY = null;
		var $userKeptY = null;

		function updf($orientation = 'p', $startPage = true) {
			if (!defined( 'FPDF_FONTPATH' )) {
				define( 'FPDF_FONTPATH', UTIL_PATH . 'fpdf/font/' );
			}

			require_once( UTIL_PATH . 'fpdf/fpdf.php' );
			require_once( UTIL_PATH . 'fpdi/fpdi_pdf_parser.php' );
			require_once( UTIL_PATH . 'fpdi/fpdf_tpl.php' );
			require_once( UTIL_PATH . 'catpdf/XMLTag.class.php' );
			require_once( UTIL_PATH . 'catpdf/FPDFCombined.class.php' );
			$this->pdf = new FPDFCombined( $orientation );

			if ($startPage == true) {
				$this->pdf->AddPage(  );
			}

			$this->pdf->SetFont( 'times', 'B', 14 );
			$this->pdf->SetAutoPageBreak( false, 0 );
			$this->columnColours = array(  );
			$this->columnFillColours = array(  );
		}

		function setobjecttodoheader($obj) {
			$this->pdf->setObjectToDoHeader( $obj );
		}

		function setobjecttodofooter($obj) {
			$this->pdf->setObjectToDoFooter( $obj );
		}

		function setprotection($prot, $user, $admin) {
			if (!is_array( $prot )) {
				trigger_error( 'not array', E_USER_ERROR );
			}

			$this->pdf->SetProtection( $prot, $user, $admin );
		}

		function close() {
			$this->pdf->Close(  );
		}

		function addpage() {
			$this->pdf->AddPage(  );
		}

		function addcell($x, $y, $w, $h, $text, $border = 0, $ln = 2, $align = 'L', $fill = 0) {
			if (0 <= $x) {
				$this->setX( $x );
			}


			if (0 <= $y) {
				$this->setY( $y );
			}


			if ($align == 'R') {
				$x = 0;
			}

			$this->pdf->MultiCell( $w, $h, $text, $border, $align, $fill );
		}

		function rightjustcell($x, $y, $w, $h, $text) {
			$pad = 5;

			if (0 <= $y) {
				$this->setY( $y );
			}


			if (0 <= $x) {
				$this->setX( $x );
			}

			$x = $this->getX(  );
			$y = $this->getY(  );
			$textWidth = $this->pdf->GetStringWidth( $text );
			$gap = $w - $textWidth;
			$gap -= $pad;
			$x = $this->getX(  );

			if (( 0 < $gap && $gap < $w )) {
				$x += $gap;
				$w -= $gap;
			}

			$newX = $x;
			$this->setX( $newX );
			$x = $this->getX(  );
			$y = $this->getY(  );
			$this->pdf->MultiCell( $w, $h, $text, 0, 2, 'L', 0 );
			$x = $this->getX(  );
			$y = $this->getY(  );
			$this->setX( $x );
		}

		function addimage($file, $x, $y, $w, $h) {
			if ($x < 0) {
				$x = $this->pdf->getX(  );
			}


			if ($y < 0) {
				$y = $this->pdf->getY(  );
			}

			$this->pdf->Image( $file, $x, $y, $w, $h );
		}

		function drawrectangle($x, $y, $w, $h) {
			$this->pdf->Rect( $x, $y, $w, $h, '' );
		}

		function drawline($x1, $y1, $x2, $y2) {
			$this->pdf->Line( $x1, $y1, $x2, $y2 );
		}

		function write($h, $text) {
			$this->pdf->write( $h, $text );
		}

		function writeattab($tab, $h, $text) {
			if (!isset( $this->tabsSet[$tab] )) {
				trigger_error( '' . 'cant get tab ' . $num, E_USER_ERROR );
			}

			$posn = $this->tabsSet[$tab];
			$oldLeft = $this->pdf->GetLeftMargin(  );
			$oldRight = $this->pdf->GetRightMargin(  );
			$this->pdf->SetLeftMargin( $posn );

			if (isset( $this->tabsSet[$tab + 1] )) {
				$x = $this->tabsSet[$tab + 1];
				$pageWidth = $this->getPageWidth(  );
				$this->pdf->SetRightMargin( $pageWidth - $x );
			}

			$this->setX( $posn );
			$this->pdf->write( $h, $text );
			$this->pdf->SetLeftMargin( $oldLeft );
			$this->pdf->SetRightMargin( $oldRight );
			$y = $this->getY(  );

			if ($this->boxLowest < $y) {
				$this->boxLowest = $y;
			}

		}

		function writeatposn($posn, $h, $text) {
			$this->setX( $posn );
			$this->pdf->write( $h, $text );
		}

		function newline($gap) {
			$this->pdf->Ln( $gap );
		}

		function setfontsize($size) {
			$this->pdf->SetFontSize( $size );
		}

		function settab($num, $value) {
			$this->tabsSet[$num] = $value;
		}

		function setfontstyle($style) {
			$this->pdf->SetFont( '', $style, '' );
		}

		function setfont($font, $style, $size) {
			$this->pdf->SetFont( $font, $style, $size );
		}

		function setleftmargin($posn) {
			$this->pdf->SetLeftMargin( $posn );
		}

		function setlinewidth($width) {
			$this->pdf->SetLineWidth( $width );
		}

		function setrightmargin($posn) {
			$this->pdf->SetRightMargin( $posn );
		}

		function setx($x) {
			return $this->pdf->setX( $x );
		}

		function sety($y) {
			$x = $this->pdf->getX(  );
			$this->pdf->setY( $y );
			$this->pdf->setX( $x );
		}

		function setautopagebreak($auto, $gap) {
			$this->pdf->SetAutoPageBreak( $auto, $gap );
		}

		function keepy() {
			$this->userKeptY = $this->pdf->getY(  );
		}

		function resety() {
			$this->setY( $this->userKeptY );
		}

		function setleftmargins($left) {
			$this->pdf->SetLeftMargins( $left );
		}

		function setrightmargins($right) {
			$this->pdf->SetRightMargins( $right );
		}

		function settextcolour($red, $green, $blue) {
			$this->pdf->SetTextColor( $red, $green, $blue );
		}

		function setfillcolour($red, $green, $blue) {
			$this->pdf->SetFillColor( $red, $green, $blue );
		}

		function getpagewidth() {
			return $this->pdf->w;
		}

		function getx() {
			return $this->pdf->getX(  );
		}

		function gety() {
			return $this->pdf->getY(  );
		}

		function gettab($num) {
			if (!isset( $this->tabsSet[$num] )) {
				trigger_error( '' . 'cant get tab ' . $num, E_USER_ERROR );
			}

			return $this->tabsSet[$num];
		}

		function getpagenumber() {
			$pn = $this->pdf->PageNo(  );
			return $pn;
		}

		function startnewpageifinsufficientspace($size) {
			$base = $this->getY(  ) + $size;
			$gap = 5;

			if ($this->pdf->h - $gap <= $base) {
				$this->addPage(  );
			}

		}

		function startbox($x, $y, $w, $l = 0, $vPad = 0, $hPad = 0) {
			if ($x < 0) {
				$x = $this->getX(  );
			}


			if ($y < 0) {
				$y = $this->getY(  );
			}

			$this->boxOldLeft = $this->pdf->GetLeftMargin(  );
			$this->boxOldRight = $this->pdf->GetRightMargin(  );
			$this->boxLowest = $x;
			$this->boxLineWidth = $l;
			$this->boxX = $x;
			$this->boxY = $y;
			$this->boxW = $w;
			$this->boxHPad = $hPad;
			$this->boxVPad = $vPad;
			$right = $x + $w;
			$this->pdf->SetLeftMargin( $x + $hPad );
			$pageWidth = $this->getPageWidth(  );
			$this->pdf->SetRightMargin( $pageWidth - ( $right - $hPad ) );
			$this->pdf->SetXY( $x + $hPad, $y + $vPad );
		}

		function gototopofbox() {
			$this->setY( $this->boxY );
		}

		function startcolumninbox($tab) {
			$posn = $this->tabsSet[$tab];
			$this->pdf->SetLeftMargin( $posn );

			if (isset( $this->tabsSet[$tab + 1] )) {
				$x = $this->tabsSet[$tab + 1];
				$pageWidth = $this->getPageWidth(  );
				$this->pdf->SetRightMargin( $pageWidth - $x );
			}

			$this->setY( $this->boxY );
			$this->setX( $posn );
		}

		function endcolumninbox() {
			$y = $this->getY(  );

			if ($this->boxLowest < $y) {
				$this->boxLowest = $y;
			}

		}

		function setboxminimumheight($h) {
			$this->boxLowest = $this->boxY + $h;
		}

		function endbox() {
			$this->pdf->SetLeftMargin( $this->boxOldLeft );
			$this->pdf->SetRightMargin( $this->boxOldRight );
			$y = $this->boxLowest;
			$h = $y - $this->boxY + $this->boxVPad;
			$l = $this->boxLineWidth;

			if (0 < $l) {
				$this->pdf->SetLineWidth( $l );
				$this->drawRectangle( $this->boxX, $this->boxY, $this->boxW, $h );
			}

			$x = $this->boxX;
			$y = $this->boxY + $h;
			$this->pdf->setXY( $x, $y );
		}

		function tablestart() {
			$this->columns = 0;
			$this->widths = array(  );
			$this->text = array(  );
			$this->columnJustify = array(  );
			$this->columnColours = array(  );
			$this->columnFillColours = array(  );
			$this->_tableX = $this->getX(  );
			$this->_tableY = $this->getY(  );
		}

		function tablestartrow() {
			$height = $this->height;
			$base = $this->getY(  ) + $height;
			$gap = 12;

			if ($this->pdf->h - $gap <= $base) {
				$this->addPage(  );
				$this->_tableY = $this->getY(  );
			}

		}

		function tableaddcolumn($width) {
			$col = $this->columns;
			++$this->columns;
			$this->widths[$col] = $width;
			$this->text[$col] = array(  );
		}

		function tableaddtext($col, $text) {
			$this->text[$col][] = $text;
		}

		function tablesetrowheight($h) {
			$this->height = $h;
		}

		function tablesetcolumncolour($col, $colour) {
			$this->columnColours[$col] = $colour;
		}

		function tablesetcolumnfillcolour($col, $colour) {
			$this->columnFillColours[$col] = $colour;
		}

		function tablesetcolumnjustify($col, $j) {
			$this->columnJustify[$col] = $j;
		}

		function tableoutputrow() {
			$cols = $this->columns;
			$height = $this->height;
			$x = $this->_tableX;
			$c = 0;

			while ($c < $cols) {
				$this->pdf->setY( $this->_tableY );
				$this->pdf->setX( $x );
				$this->pdf->SetLeftMargin( $x );
				$pageWidth = $this->getPageWidth(  );
				$gap = $pageWidth - ( $x + $this->widths[$c] );
				$this->pdf->SetRightMargin( $gap );
				$done = 0;
				foreach ($this->text[$c] as $item) {
					if (0 < $done++) {
						$this->pdf->write( 5, '
' );
					}


					if (( isset( $this->columnJustify[$c] ) && $this->columnJustify[$c] == 'R' )) {
						$len = $this->pdf->GetStringWidth( $item );
						$space = $this->widths[$c] - $len - 3;

						if ($space < 0) {
							$space = 0;
						}

						$this->pdf->setX( $x + $space );
					}


					if (( isset( $this->columnJustify[$c] ) && $this->columnJustify[$c] == 'C' )) {
						$len = $this->pdf->GetStringWidth( $item );
						$space = ( $this->widths[$c] - $len ) / 2 - 2;

						if ($space < 0) {
							$space = 0;
						}

						$this->pdf->setX( $x + $space );
					}


					if (isset( $this->columnColours[$c] )) {
						$colour = $this->columnColours[$c];
					} 
else {
						$colour = 'black';
					}


					if ($colour == 'red') {
						$red = 254;
						$green = 0;
						$blue = 4;
					} 
else {
						$red = 0;
						$green = 0;
						$blue = 0;
					}

					$this->setTextColour( $red, $green, $blue );

					if (isset( $this->columnFillColours[$c] )) {
						$colour = $this->columnFillColours[$c];
					} 
else {
						$colour = 'white';
					}


					if ($colour == 'grey') {
						$red = 120;
						$green = 22;
						$blue = 5;
					} 
else {
						$red = 0;
						$green = 40;
						$blue = 200;
					}

					$this->setFillColour( $red, $green, $blue );
					$this->pdf->write( 5, $item );
				}

				$y = $this->pdf->getY(  ) + 5;
				$h = $y - $this->_tableY;

				if ($height < $h) {
					$height = $h;
				}

				$x += $this->widths[$c];
				++$c;
			}

			$x = $this->_tableX;
			$c = 0;

			while ($c < $cols) {
				$this->pdf->setY( $this->_tableY );
				$this->pdf->setX( $x );
				$this->drawRectangle( $x, $this->_tableY, $this->widths[$c], $height );
				$x += $this->widths[$c];
				++$c;
			}

			$this->_tableY += $height;
			$this->pdf->setY( $this->_tableY );
			$c = 0;

			while ($c < $cols) {
				$this->text[$c] = array(  );
				++$c;
			}

			$this->columnColours = array(  );
		}

		function output() {
			$this->pdf->Output(  );
		}

		function returnasstring() {
			return $this->pdf->Output( '', 'S' );
		}
	}

?>