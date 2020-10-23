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
			$this->Image( 'logo_pb.png', 10, 8, 33 );
			$this->SetFont( 'Arial', 'B', 15 );
			$this->Cell( 80 );
			$this->Cell( 30, 10, 'Title', 1, 0, 'C' );
			$this->Ln( 20 );
		}

		function footer() {
			$this->SetY( 0 - 15 );
			$this->SetFont( 'Arial', 'I', 8 );
			$this->Cell( 0, 10, 'Page ' . $this->PageNo(  ) . '/{nb}', 0, 0, 'C' );
		}
	}

	require( '../fpdf.php' );
	$pdf = new PDF(  );
	$pdf->AliasNbPages(  );
	$pdf->AddPage(  );
	$pdf->SetFont( 'Times', '', 12 );
	$i = 1;

	while ($i <= 40) {
		$pdf->Cell( 0, 10, 'Printing line number ' . $i, 0, 1 );
		++$i;
	}

	$pdf->Output(  );
?>