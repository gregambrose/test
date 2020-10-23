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

	class updfxml {
		var $inHeader = null;
		var $inFooter = null;
		var $headerTags = null;
		var $footerTags = null;

		function updfxml(&$xmlText, $pdf) {
			$this->pdf = &$pdf;

			$this->pdf->setObjectToDoHeader( $this );
			$this->pdf->setObjectToDoFooter( $this );
			uxml::uxml( $xmlText );
		}

		function _starttag($tag) {
			$name = $tag->getName(  );

			if ($name == 'DEFINEHEADER') {
				$this->inHeader = true;
				$this->headerTags = array(  );
			}


			if ($name == 'DEFINEFOOTER') {
				$this->inFooter = true;
				$this->footerTags = array(  );
			}

		}

		function _processtag($tag) {
			$name = $tag->getName(  );
			$name = strtoupper( $name );

			if ($this->inHeader == true) {
				if ($name == 'DEFINEHEADER') {
					$this->inHeader = false;
					return null;
				}

				$this->headerTags[] = $tag;
				return null;
			}


			if ($this->inFooter == true) {
				if ($name == 'DEFINEFOOTER') {
					$this->inFooter = false;
					return null;
				}

				$this->footerTags[] = $tag;
				return null;
			}

			switch ($name) {
				case 'ADDPAGE': {
					$this->_addPage( $tag );
					break;
				}

				case 'DRAWLINE': {
					$this->_drawLine( $tag );
					break;
				}

				case 'ENDBOX': {
					$this->_endBox( $tag );
					break;
				}

				case 'ENDCOLUMNINBOX': {
					$this->_endColumnInBox( $tag );
					break;
				}

				case 'FONT': {
					$this->_setFont( $tag );
					break;
				}

				case 'FONTSIZE': {
					$this->_fontSize( $tag );
					break;
				}

				case 'FONTSTYLE': {
					$this->_fontStyle( $tag );
					break;
				}

				case 'IMAGE': {
					$this->_image( $tag );
					break;
				}

				case 'KEEPY': {
					$this->_keepY( $tag );
					break;
				}

				case 'RESETY': {
					$this->_resetY( $tag );
					break;
				}

				case 'NEWLINE': {
					$this->_newLine( $tag );
					break;
				}

				case 'PAGENUMBER': {
					$this->_pageNumber( $tag );
					break;
				}

				case 'PROTECTION': {
					$this->_protection( $tag );
					break;
				}

				case 'RIGHTJUSTCELL': {
					$this->_rightJustCell( $tag );
					break;
				}

				case 'SETLEFTMARGIN': {
					$this->_setLeftMargin( $tag );
					break;
				}

				case 'SETRIGHTTMARGIN': {
					$this->_setRightMargin( $tag );
					break;
				}

				case 'SETTAB': {
					$this->_setTab( $tag );
					break;
				}

				case 'SETTEXTCOLOUR': {
					$this->_setTextColour( $tag );
					break;
				}

				case 'STARTBOX': {
					$this->_startBox( $tag );
					break;
				}

				case 'SETBOXMINIMUMHEIGHT': {
					$this->_setBoxMinimumHeight( $tag );
					break;
				}

				case 'STARTCOLUMNINBOX': {
					$this->_startColumnInBox( $tag );
					break;
				}

				case 'TEXTBOX': {
					$this->_textBox( $tag );
					break;
				}

				case 'WRITE': {
					$this->_write( $tag );
					break;
				}

				case 'SETX': {
					$this->_setX( $tag );
					break;
				}

				case 'SETAUTOPAGEBREAK': {
					$this->_setAutoPageBreak( $tag );
					break;
				}

				case 'STARTNEWPAGEIFINSUFFICIENTSPACE': {
					$this->_startNewPageIfInsufficientSpace( $tag );
					break;
				}

				case 'TABLESTART': {
					$this->_tableStart( $tag );
					break;
				}

				case 'TABLESTARTROW': {
					$this->_tableStartRow( $tag );
					break;
				}

				case 'TABLEADDCOLUMN': {
					$this->_tableAddColumn( $tag );
					break;
				}

				case 'TABLEADDTEXT': {
					$this->_tableAddText( $tag );
					break;
				}

				case 'TABLESETROWHEIGHT': {
					$this->_tableSetRowHeight( $tag );
					break;
				}

				case 'TABLESETCOLUMNCOLOUR': {
					$this->_tableSetColumnColour( $tag );
					break;
				}

				case 'TABLESETCOLUMNFILLCOLOUR': {
					$this->_tableSetColumnFillColour( $tag );
					break;
				}

				case 'TABLESETCOLUMNJUSTIFY': {
					$this->_tableSetColumnJustify( $tag );
					break;
				}

				case 'TABLEOUTPUTROW': {
					$this->_tableOutputRow( $tag );
					break;
				}

				case 'STARTHEADER': {
					$a = $tag;
					break;
				}

				case 'STARTFOOTER': {
					$a = $tag;
				}
			}

		}

		function _addpage($tag) {
			$this->pdf->addPage(  );
		}

		function _drawline($tag) {
			$xStart = $tag->getAttribute( 'XSTART' );
			$yStart = $tag->getAttribute( 'YSTART' );
			$xEnd = $tag->getAttribute( 'XEND' );
			$yEnd = $tag->getAttribute( 'YEND' );
			$this->pdf->drawLine( $xStart, $yStart, $xEnd, $yEnd );
		}

		function _endbox($tag) {
			$this->pdf->endBox(  );
		}

		function _endcolumninbox($tag) {
			$this->pdf->endColumnInBox(  );
		}

		function _setfont($tag) {
			$f = $tag->getAttribute( 'FONT' );
			$s = $tag->getAttribute( 'STYLE' );
			$x = $tag->getAttribute( 'SIZE' );
			$this->pdf->setFont( $f, $s, $x );
		}

		function _fontsize($tag) {
			$size = $tag->getData(  );

			if (!is_numeric( $size )) {
				return null;
			}

			$this->pdf->setFontSize( $size );
		}

		function _fontstyle($tag) {
			$style = $tag->getData(  );
			$this->pdf->setFontStyle( $style );
		}

		function _image($tag) {
			$image = $tag->getAttribute( 'NAME' );
			$x = $tag->getAttribute( 'XPOSN' );
			$y = $tag->getAttribute( 'YPOSN' );
			$width = $tag->getAttribute( 'WIDTH' );
			$height = $tag->getAttribute( 'HEIGHT' );
			$this->pdf->addImage( IMAGES_PATH . $image, $x, $y, $width, $height );
		}

		function _keepy($tag) {
			$this->pdf->keepY(  );
		}

		function _resety($tag) {
			$this->pdf->resetY(  );
		}

		function _newline($tag) {
			$height = $tag->getAttribute( 'HEIGHT' );
			$this->pdf->newLine( $height );
		}

		function _protection($tag) {
			$type = $tag->getAttribute( 'TYPE' );
			$user = $tag->getAttribute( 'USER' );
			$admin = $tag->getAttribute( 'ADMIN' );

			if (( ( strlen( $type ) == 0 && strlen( $user ) == 0 ) && strlen( $mang ) == 0 )) {
				return null;
			}

			$this->pdf->setProtection( array( $type ), $user, $admin );
		}

		function _rightjustcell($tag) {
			$height = $tag->getAttribute( 'HEIGHT' );
			$width = $tag->getAttribute( 'WIDTH' );
			$text = $tag->getData(  );
			$this->pdf->rightJustCell( 0 - 1, 0 - 1, $width, $height, $text );
		}

		function _settab($tag) {
			$tab = $tag->getAttribute( 'TAB' );
			$posn = $tag->getAttribute( 'POSN' );
			$this->pdf->setTab( $tab, $posn );
		}

		function _settextcolour($tag) {
			$red = $tag->getAttribute( 'RED' );
			$green = $tag->getAttribute( 'GREEN' );
			$blue = $tag->getAttribute( 'BLUE' );
			$this->pdf->setTextColour( $red, $green, $blue );
		}

		function _setleftmargin($tag) {
			$left = $tag->getData(  );
			$this->pdf->setLeftMargin( $left );
		}

		function _setrightmargin($tag) {
			$right = $tag->getData(  );
			$this->pdf->setRightMargin( $right );
		}

		function _startbox($tag) {
			$x = $tag->getAttribute( 'XPOSN' );
			$y = $tag->getAttribute( 'YPOSN' );
			$width = $tag->getAttribute( 'WIDTH' );
			$lineWidth = $tag->getAttribute( 'LINEWIDTH' );
			$vPad = $tag->getAttribute( 'VPADDING' );
			$hPad = $tag->getAttribute( 'HPADDING' );
			$this->pdf->startBox( $x, $y, $width, $lineWidth, $vPad, $hPad );
		}

		function _setboxminimumheight($tag) {
			$h = $tag->getData(  );
			$this->pdf->setBoxMinimumHeight( $h );
		}

		function _startcolumninbox($tag) {
			$tab = $tag->getAttribute( 'TAB' );
			$this->pdf->startColumnInBox( $tab );
		}

		function _pagenumber($tag) {
			$pn = $this->pdf->getPageNumber(  );
			$tag2 = $tag->getCopy(  );
			$tag2->setData( $pn );
			$this->_textBox( $tag2 );
		}

		function _textbox($tag) {
			$x = $tag->getAttribute( 'XPOSN' );
			$y = $tag->getAttribute( 'YPOSN' );
			$width = $tag->getAttribute( 'WIDTH' );
			$lineHeight = $tag->getAttribute( 'LINEHEIGHT' );
			$align = $tag->getAttribute( 'ALIGN' );
			$border = $tag->getAttribute( 'BORDER' );

			if (( $border != 0 && $border != 1 )) {
				$border = 0;
			}

			$text = $tag->getData(  );
			$this->pdf->addCell( $x, $y, $width, $lineHeight, $text, $border, 2, $align, 0 );
		}

		function _write($tag) {
			$height = $tag->getAttribute( 'HEIGHT' );
			$text = $tag->getData(  );
			$text = trim( $text );
			$this->pdf->write( $height, $text );
		}

		function _setx($tag) {
			$text = $tag->getData(  );
			$this->pdf->setX( $text );
		}

		function _setautopagebreak($tag) {
			$auto = $tag->getData(  );
			$gap = $tag->getAttribute( 'GAP' );
			$this->pdf->setAutoPageBreak( $auto, $gap );
		}

		function _startnewpageifinsufficientspace($tag) {
			$gap = $tag->getData(  );
			$this->pdf->startNewPageIfInsufficientSpace( $gap );
		}

		function _tablestart($tag) {
			$this->pdf->tableStart(  );
		}

		function _tablestartrow($tag) {
			$this->pdf->tableStartRow(  );
		}

		function _tableaddcolumn($tag) {
			$width = $tag->getData(  );
			$this->pdf->tableAddColumn( $width );
		}

		function _tableaddtext($tag) {
			$col = $tag->getAttribute( 'COLUMN' );
			$text = $tag->getData(  );
			$text = trim( $text );
			$this->pdf->tableAddText( $col, $text );
		}

		function _tablesetcolumncolour($tag) {
			$col = $tag->getAttribute( 'COLUMN' );
			$colour = $tag->getAttribute( 'COLOUR' );
			$this->pdf->tablesetColumnColour( $col, $colour );
		}

		function _tablesetcolumnfillcolour($tag) {
			$col = $tag->getAttribute( 'COLUMN' );
			$colour = $tag->getAttribute( 'COLOUR' );
			$this->pdf->tablesetColumnFillColour( $col, $colour );
		}

		function _tablesetrowheight($tag) {
			$text = $tag->getData(  );
			$this->pdf->tableSetRowHeight( $text );
		}

		function _tablesetcolumnjustify($tag) {
			$col = $tag->getAttribute( 'COLUMN' );
			$text = $tag->getData(  );
			$this->pdf->tableSetColumnJustify( $col, $text );
		}

		function _tableoutputrow($tag) {
			$this->pdf->tableOutputRow(  );
		}

		function doheader() {
			if (!isset( ->headerTags )) {
				return null;
			}

			foreach ($this->headerTags as $tag) {
				$this->_processTag( $tag );
			}

		}

		function dofooter() {
			if (!isset( ->footerTags )) {
				return null;
			}

			foreach ($this->footerTags as $tag) {
				$this->_processTag( $tag );
			}

		}
	}

?>