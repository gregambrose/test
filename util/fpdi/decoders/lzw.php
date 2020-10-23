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

	class lzwdecode {
		var $sTable = array(  );
		var $data = null;
		var $tIdx = null;
		var $bitsToGet = 9;
		var $bytePointer = null;
		var $bitPointer = null;
		var $nextData = 0;
		var $nextBits = 0;
		var $andTable = array( 0 => 511, 1 => 1023, 2 => 2047, 3 => 4095 );

		function lzwdecode($fpdi) {
			$this->fpdi = &$fpdi;

		}

		function decode($data) {
			if (( $data[0] == 0 && $data[1] == 1 )) {
				$this->fpdi->error( 'LZW flavour not supported.' );
			}

			$this->initsTable(  );
			$this->data = &$data;

			$this->bytePointer = 0;
			$this->bitPointer = 0;
			$this->nextData = 0;
			$this->nextBits = 0;
			$oldCode = 0;
			$string = '';
			$uncompData = '';

			while ($code = $this->getNextCode(  ) != 257) {
				if ($code == 256) {
					$this->initsTable(  );
					$code = $this->getNextCode(  );

					if ($code == 257) {
						break;
					}

					$uncompData .= $this->sTable[$code];
					$oldCode = $code;
					continue;
				}


				if ($code < $this->tIdx) {
					$string = $this->sTable[$code];
					$uncompData .= $string;
					$this->addStringToTable( $this->sTable[$oldCode], $string[0] );
					$oldCode = $code;
					continue;
				}

				$string = $this->sTable[$oldCode];
				$string = $string . $string[0];
				$uncompData .= $string;
				$this->addStringToTable( $string );
				$oldCode = $code;
			}

			return $uncompData;
		}

		function initstable() {
			$this->sTable = array(  );
			$i = 0;

			while ($i < 256) {
				$this->sTable[$i] = chr( $i );
				++$i;
			}

			$this->tIdx = 258;
			$this->bitsToGet = 9;
		}

		function addstringtotable($oldString, $newString = '') {
			$string = $oldString . $newString;
			$this->sTable[$this->tIdx++] = $string;

			if ($this->tIdx == 511) {
				$this->bitsToGet = 10;
				return null;
			}


			if ($this->tIdx == 1023) {
				$this->bitsToGet = 11;
				return null;
			}


			if ($this->tIdx == 2047) {
				$this->bitsToGet = 12;
			}

		}

		function getnextcode() {
			if ($this->bytePointer == strlen( $this->data )) {
				return 257;
			}

			$this->nextData = $this->nextData << 8 | ord( $this->data[$this->bytePointer++] ) & 255;
			$this->nextBits += 8;

			if ($this->nextBits < $this->bitsToGet) {
				$this->nextData = $this->nextData << 8 | ord( $this->data[$this->bytePointer++] ) & 255;
				$this->nextBits += 8;
			}

			$code = $this->nextData >> $this->nextBits - $this->bitsToGet & $this->andTable[$this->bitsToGet - 9];
			$this->nextBits -= $this->bitsToGet;
			return $code;
		}
	}

?>