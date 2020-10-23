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

	class ascii85decode {
		function ascii85decode($fpdi) {
			$this->fpdi = &$fpdi;

		}

		function decode($in) {
			$out = '';
			$state = 0;
			$chn = null;
			$l = strlen( $in );
			$k = 0;

			while ($k < $l) {
				$ch = ord( $in[$k] ) & 255;

				if ($ch == ORD_tilde) {
					break;
				}


				if (preg_match( '' . '/^\s$/', chr( $ch ) )) {
					continue;
				}


				if (( $ch == ORD_z && $state == 0 )) {
					$out .= chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 );
					continue;
				}


				if (( $ch < ORD_exclmark || ORD_u < $ch )) {
					$this->fpdi->error( 'Illegal character in ASCII85Decode.' );
				}

				$chn[$state++] = $ch - ORD_exclmark;

				if ($state == 5) {
					$state = 0;
					$r = 0;
					$j = 0;

					while ($j < 5) {
						$r = $r * 85 + $chn[$j];
						++$j;
					}

					$out .= chr( $r >> 24 );
					$out .= chr( $r >> 16 );
					$out .= chr( $r >> 8 );
					$out .= chr( $r );
				}

				++$k;
			}

			$r = 0;

			if ($state == 1) {
				$this->fpdi->error( 'Illegal length in ASCII85Decode.' );
			}


			if ($state == 2) {
				$r = $chn[0] * 85 * 85 * 85 * 85 + ( $chn[1] + 1 ) * 85 * 85 * 85;
				$out .= chr( $r >> 24 );
			} 
else {
				if ($state == 3) {
					$r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85 + ( $chn[2] + 1 ) * 85 * 85;
					$out .= chr( $r >> 24 );
					$out .= chr( $r >> 16 );
				} 
else {
					if ($state == 4) {
						$r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85 + $chn[2] * 85 * 85 + ( $chn[3] + 1 ) * 85;
						$out .= chr( $r >> 24 );
						$out .= chr( $r >> 16 );
						$out .= chr( $r >> 8 );
					}
				}
			}

			return $out;
		}
	}


	if (!defined( 'ORD_z' )) {
		define( 'ORD_z', ord( 'z' ) );
	}


	if (!defined( 'ORD_!' )) {
		define( 'ORD_exclmark', ord( '!' ) );
	}


	if (!defined( 'ORD_u' )) {
		define( 'ORD_u', ord( 'u' ) );
	}


	if (!defined( 'ORD_tilde' )) {
		define( 'ORD_tilde', ord( '~' ) );
	}

?>