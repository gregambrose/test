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
		function loaddata($file) {
			$lines = file( $file );
			$data = array(  );
			foreach ($lines as $line) {
				$data[] = explode( ';', chop( $line ) );
			}

			return $data;
		}

		function basictable($header, $data) {
			foreach ($header as $col) {
				$this->Cell( 40, 7, $col, 1 );
			}

			$this->Ln(  );
			foreach ($data as $row) {
				foreach ($row as $col) {
					$this->Cell( 40, 6, $col, 1 );
				}

				$this->Ln(  );
			}

		}

		function improvedtable($header, $data) {
			$w = array( 40, 35, 40, 45 );
			$i = 0;

			while ($i < count( $header )) {
				$this->Cell( $w[$i], 7, $header[$i], 1, 0, 'C' );
				++$i;
			}

			$this->Ln(  );
			foreach ($data as $row) {
				$this->Cell( $w[0], 6, $row[0], 'LR' );
				$this->Cell( $w[1], 6, $row[1], 'LR' );
				$this->Cell( $w[2], 6, number_format( $row[2] ), 'LR', 0, 'R' );
				$this->Cell( $w[3], 6, number_format( $row[3] ), 'LR', 0, 'R' );
				$this->Ln(  );
			}

			$this->Cell( array_sum( $w ), 0, '', 'T' );
		}

		function fancytable($header, $data) {
			$this->SetFillColor( 255, 0, 0 );
			$this->SetTextColor( 255 );
			$this->SetDrawColor( 128, 0, 0 );
			$this->SetLineWidth( 0.29999999999999998889777 );
			$this->SetFont( '', 'B' );
			$w = array( 40, 35, 40, 45 );
			$i = 0;

			while ($i < count( $header )) {
				$this->Cell( $w[$i], 7, $header[$i], 1, 0, 'C', 1 );
				++$i;
			}

			$this->Ln(  );
			$this->SetFillColor( 224, 235, 255 );
			$this->SetTextColor( 0 );
			$this->SetFont( '' );
			$fill = 0;
			foreach ($data as $row) {
				$this->Cell( $w[0], 6, $row[0], 'LR', 0, 'L', $fill );
				$this->Cell( $w[1], 6, $row[1], 'LR', 0, 'L', $fill );
				$this->Cell( $w[2], 6, number_format( $row[2] ), 'LR', 0, 'R', $fill );
				$this->Cell( $w[3], 6, number_format( $row[3] ), 'LR', 0, 'R', $fill );
				$this->Ln(  );
				$fill = !$fill;
			}

			$this->Cell( array_sum( $w ), 0, '', 'T' );
		}
	}

	require( '../fpdf.php' );
	$pdf = new PDF(  );
	$header = array( 'Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)' );
	$data = $pdf->LoadData( 'countries.txt' );
	$pdf->SetFont( 'Arial', '', 14 );
	$pdf->AddPage(  );
	$pdf->BasicTable( $header, $data );
	$pdf->AddPage(  );
	$pdf->ImprovedTable( $header, $data );
	$pdf->AddPage(  );
	$pdf->FancyTable( $header, $data );
	$pdf->Output(  );
?>