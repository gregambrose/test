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
		function header() {
			global $title;

			$this->SetFont( 'Arial', 'B', 15 );
			$w = $this->GetStringWidth( $title ) + 6;
			$this->SetX( ( 210 - $w ) / 2 );
			$this->SetDrawColor( 0, 80, 180 );
			$this->SetFillColor( 230, 230, 0 );
			$this->SetTextColor( 220, 50, 50 );
			$this->SetLineWidth( 1 );
			$this->Cell( $w, 9, $title, 1, 1, 'C', 1 );
			$this->Ln( 10 );
		}

		function footer() {
			$this->SetY( 0 - 15 );
			$this->SetFont( 'Arial', 'I', 8 );
			$this->SetTextColor( 128 );
			$this->Cell( 0, 10, 'Page ' . $this->PageNo(  ), 0, 0, 'C' );
		}

		function chaptertitle($num, $label) {
			$this->SetFont( 'Arial', '', 12 );
			$this->SetFillColor( 200, 220, 255 );
			$this->Cell( 0, 6, '' . 'Chapter ' . $num . ' : ' . $label, 0, 1, 'L', 1 );
			$this->Ln( 4 );
		}

		function chapterbody($file) {
			$f = fopen( $file, 'r' );
			$txt = fread( $f, filesize( $file ) );
			fclose( $f );
			$this->SetFont( 'Times', '', 12 );
			$this->MultiCell( 0, 5, $txt );
			$this->Ln(  );
			$this->SetFont( '', 'I' );
			$this->Cell( 0, 5, '(end of excerpt)' );
		}

		function printchapter($num, $title, $file) {
			$this->AddPage(  );
			$this->ChapterTitle( $num, $title );
			$this->ChapterBody( $file );
		}
	}

	require( '../fpdf.php' );
	$pdf = new PDF(  );
	$title = '20000 Leagues Under the Seas';
	$pdf->SetTitle( $title );
	$pdf->SetAuthor( 'Jules Verne' );
	$pdf->PrintChapter( 1, 'A RUNAWAY REEF', '20k_c1.txt' );
	$pdf->PrintChapter( 2, 'THE PROS AND CONS', '20k_c2.txt' );
	$pdf->Output(  );
?>