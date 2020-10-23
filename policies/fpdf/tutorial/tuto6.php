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

	class pdf {
		var $B = null;
		var $I = null;
		var $U = null;
		var $HREF = null;

		function pdf($orientation = 'P', $unit = 'mm', $format = 'A4') {
			$this->FPDF( $orientation, $unit, $format );
			$this->B = 0;
			$this->I = 0;
			$this->U = 0;
			$this->HREF = '';
		}

		function writehtml($html) {
			$html = str_replace( '
', ' ', $html );
			$a = preg_split( '/<(.*)>/U', $html, 0 - 1, PREG_SPLIT_DELIM_CAPTURE );
			foreach ($a as $i => $e) {
				if ($i % 2 == 0) {
					if ($this->HREF) {
						$this->PutLink( $this->HREF, $e );
						continue;
					}

					$this->Write( 5, $e );
					continue;
				}


				if ($e[0] == '/') {
					$this->CloseTag( strtoupper( substr( $e, 1 ) ) );
					continue;
				}

				$a2 = explode( ' ', $e );
				$tag = strtoupper( array_shift( $a2 ) );
				$attr = array(  );
				foreach ($a2 as $v) {
					if (ereg( '^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3 )) {
						$attr[strtoupper( $a3[1] )] = $a3[2];
						continue;
					}
				}

				$this->OpenTag( $tag, $attr );
			}

		}

		function opentag($tag, $attr) {
			if (( ( $tag == 'B' || $tag == 'I' ) || $tag == 'U' )) {
				$this->SetStyle( $tag, true );
			}


			if ($tag == 'A') {
				$this->HREF = $attr['HREF'];
			}


			if ($tag == 'BR') {
				$this->Ln( 5 );
			}

		}

		function closetag($tag) {
			if (( ( $tag == 'B' || $tag == 'I' ) || $tag == 'U' )) {
				$this->SetStyle( $tag, false );
			}


			if ($tag == 'A') {
				$this->HREF = '';
			}

		}

		function setstyle($tag, $enable) {
			$this->$tag += ($enable ? 1 : 0 - 1);
			$style = '';
			foreach (array( 'B', 'I', 'U' ) as $s) {
				if (0 < $this->$s) {
					$style .= $s;
					continue;
				}
			}

			$this->SetFont( '', $style );
		}

		function putlink($URL, $txt) {
			$this->SetTextColor( 0, 0, 255 );
			$this->SetStyle( 'U', true );
			$this->Write( 5, $txt, $URL );
			$this->SetStyle( 'U', false );
			$this->SetTextColor( 0 );
		}
	}

	require( '../fpdf.php' );
	$html = 'You can now easily print text mixing different
styles : <B>bold</B>, <I>italic</I>, <U>underlined</U>, or
<B><I><U>all at once</U></I></B>!<BR>You can also insert links
on text, such as <A HREF="http://www.fpdf.org">www.fpdf.org</A>,
or on an image: click on the logo.';
	$pdf = new PDF(  );
	$pdf->AddPage(  );
	$pdf->SetFont( 'Arial', '', 20 );
	$pdf->Write( 5, 'To find out what\'s new in this tutorial, click ' );
	$pdf->SetFont( '', 'U' );
	$link = $pdf->AddLink(  );
	$pdf->Write( 5, 'here', $link );
	$pdf->SetFont( '' );
	$pdf->AddPage(  );
	$pdf->SetLink( $link );
	$pdf->Image( 'logo.png', 10, 10, 30, 0, '', 'http://www.fpdf.org' );
	$pdf->SetLeftMargin( 45 );
	$pdf->SetFontSize( 14 );
	$pdf->WriteHTML( $html );
	$pdf->Output(  );
?>