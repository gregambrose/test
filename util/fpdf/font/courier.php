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

	$i = 0;

	while ($i <= 255) {
		$fpdf_charwidths['courier'][chr( $i )] = 600;
		++$i;
	}

	$fpdf_charwidths['courierB'] = $fpdf_charwidths['courier'];
	$fpdf_charwidths['courierI'] = $fpdf_charwidths['courier'];
	$fpdf_charwidths['courierBI'] = $fpdf_charwidths['courier'];
?>