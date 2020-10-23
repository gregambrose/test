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

	class xls_reader {
		var $_data = false;
		var $workbook = false;
		var $_formats = array(  );

		function xls_reader() {
			$this->_init(  );
		}

		function _init() {
		}

		function read_file($file) {
			$this->_data = new xls_container( $file, true );
			return $this->_read_xls(  );
		}

		function read_string($string) {
			$this->_data = new xls_container( $string );
			return $this->_read_xls(  );
		}

		function _read_xls() {
			if ($this->_data->get_data_size(  ) <= EXCEL_BLOCK_SIZE) {
				trigger_error( 'File or stream is corrupted [size too small]', E_USER_ERROR );
			}

			$this->_blocks_count = $this->_data->get_blocks_number(  );
			$file_header = $this->_data->_fgets( 0, EXCEL_BLOCK_SIZE );
			$header_etalon = pack( 'H*', 'D0CF11E0A1B11AE1' );

			if ($header_etalon != substr( $file_header, 0, 8 )) {
				trigger_error( 'Invalid file header', E_USER_ERROR );
			}

			$this->_header = new xls_container( $file_header );
			$this->_data->_s_offset = EXCEL_BLOCK_SIZE;
			$res = $this->_read_workbook(  );
			$this->workbook->_check_palette(  );
			return $res;
		}

		function _read_workbook() {
			$root_node = $this->_header->get_long( 48 );
			$fat_blocks_count = $this->_header->get_long( 44 );
			$this->fat = array(  );
			$i = 0;

			while ($i < $fat_blocks_count) {
				$fat_block = $this->_header->get_long( 76 + 4 * $i );
				$fatbuf = $this->_data->_fgets( $fat_block * EXCEL_BLOCK_SIZE, EXCEL_BLOCK_SIZE );
				$fat = new xls_container( $fatbuf );

				if ($fat->get_data_size(  ) < EXCEL_BLOCK_SIZE) {
					trigger_error( 'File or stream is corrupted', E_USER_ERROR );
				}

				$c = 0;

				while ($c < THIS_BLOCK_SIZE) {
					$this->fat[] = $fat->get_long( $c * 4 );
					++$c;
				}

				$fat->_kill(  );
				unset( $fat_block );
				unset( $fatbuf );
				unset( $fat );
				++$i;
			}


			if (count( $this->fat ) < $fat_blocks_count) {
				trigger_error( 'File or stream is corrupted', E_USER_ERROR );
			}

			$blocks_chain = $this->get_blocks_chain( $root_node );
			$dir = new xls_container( $blocks_chain )(  );
			unset( $blocks_chain );
			$this->sfat = array(  );
			$small_block = $this->_header->get_long( 60 );

			if ($small_block != 4278190079) {
				$root_node_index = $this->_get_stream_index( $dir, 'Root Entry' );
				$sdc_start_block = $dir->get_long( $root_node_index * THIS_BLOCK_SIZE + 116 );
				$small_blocks_chain = $this->get_blocks_chain( $sdc_start_block );
				$this->_sblocks_count = count( $small_blocks_chain ) * 8;
				$schain = $this->get_blocks_chain( $small_block );
				$i = 0;

				while ($i < count( $schain )) {
					$sfatbuf = $this->_data->_fgets( $schain[$i] * EXCEL_BLOCK_SIZE, EXCEL_BLOCK_SIZE );
					$sfat = new xls_container( $sfatbuf );

					if ($sfat->get_data_size(  ) < EXCEL_BLOCK_SIZE) {
						trigger_error( 'File or stream is corrupted', E_USER_ERROR );
					}

					$c = 0;

					while ($c < THIS_BLOCK_SIZE) {
						$this->sfat[] = $sfat->get_long( $c * 4 );
						++$c;
					}

					$sfat->_kill(  );
					unset( $sfatbuf );
					unset( $sfat );
					++$i;
				}

				unset( $schain );
				$sfcbuf = $this->_data->read_blocks_chain( $small_blocks_chain );
				$sdp = new xls_container( $sfcbuf );
				unset( $sfcbuf );
				unset( $small_blocks_chain );
			}

			$workbook_index = $this->_get_stream_index( $dir, 'Workbook' );

			if ($workbook_index < 0) {
				$workbook_index = $this->_get_stream_index( $dir, 'Book' );

				if ($workbook_index < 0) {
					trigger_error( 'Stream \'WorkBook\' was not found', E_USER_ERROR );
				}
			}

			$workbook_start_block = $dir->get_long( $workbook_index * THIS_BLOCK_SIZE + 116 );
			$workbook_length = $dir->get_long( $workbook_index * THIS_BLOCK_SIZE + 120 );
			$wbk = '';

			if (0 < $workbook_length) {
				if (4096 <= $workbook_length) {
					$chain = $this->get_blocks_chain( $workbook_start_block );
					$wbk = $this->_data->read_blocks_chain( $chain );
				} 
else {
					$chain = $this->get_blocks_chain( $workbook_start_block, true );
					$wbk = $sdp->read_blocks_chain( $chain, 64 );
					unset( $sdp );
				}

				$wbk = substr( $wbk, 0, $workbook_length );

				if (strlen( $wbk ) != $workbook_length) {
					trigger_error( 'File or stream is corrupted', E_USER_ERROR );
				}

				unset( $chain );
			}

			unset( $this[fat] );
			unset( $this[sfat] );

			if (strlen( $wbk ) <= 0) {
				trigger_error( 'Stream \'WorkBook\' was not found', E_USER_ERROR );
			}


			if (( ( strlen( $wbk ) < 4 || strlen( $wbk ) < $this->_get_val( $wbk, 0, 2 ) ) || ord( $wbk[0] ) != 9 )) {
				trigger_error( 'File or stream is corrupted', E_USER_ERROR );
			}

			$cur_ver = ord( $wbk[1] );

			if (( ( ( $cur_ver != 0 && $cur_ver != 2 ) && $cur_ver != 4 ) && $cur_ver != 8 )) {
				trigger_error( 'Invalid file format version', E_USER_ERROR );
			}


			if ($cur_ver != 8) {
				$biff_ver = ( $ver + 4 ) / 2;
			} 
else {
				if (strlen( $wbk ) < 12) {
					trigger_error( 'File or stream is corrupted', E_USER_ERROR );
				}

				switch ($this->_get_val( $wbk, 0, 4 )) {
					case 1280: {
						if ($this->_get_val( $wbk, 0, 10 ) < 1994) {
							$biff_ver = 5;
						} 
else {
							switch ($this->_get_val( $wbk, 0, 8 )) {
								case 2412: {
								}

								case 3218: {
								}

								case 3321: {
									$biff_ver = 5;
									break;
								}

								default: {
									$biff_ver = 7;
									break;
								}
							}
						}

						break;
					}

					case 1536: {
						$biff_ver = 8;
						break;
					}

					default: {
						trigger_error( 'Unsupported file format version', E_USER_ERROR );
					}
				}
			}


			if ($biff_ver < 5) {
				trigger_error( ( '' . 'Unsupported file format version [' . $biff_ver . ']' ), E_USER_ERROR );
			}

			$this->workbook = new struct_book(  );
			$xf_count = 0;
			$pointer = 0;
			$opcode = 0;
			$cont_def = false;
			$wbklen = strlen( $wbk );

			while (( ord( $wbk[$pointer] ) != 10 && $pointer < $wbklen )) {
				$oc = $this->_get_val( $wbk, $pointer, 0 );

				if ($oc != 60) {
					$opcode = $oc;
				}

				switch ($opcode) {
					case RCRD_BOUNDSHEET: {
						$ofs = get_long_from_string( substr( $wbk, $pointer + 4, 4 ) );
						$this->workbook->add_sheet(  );
						$this->workbook->_cur_sheet->options = $this->_get_val( $wbk, $pointer, 8 );

						if ($biff_ver == 8) {
							$len = ord( $wbk[$pointer + 10] );

							if (0 < ( ord( $wbk[$pointer + 11] ) & 1 )) {
								$this->workbook->_cur_sheet->unicode = true;
								$len = $len * 2;
							} 
else {
								$this->workbook->_cur_sheet->unicode = false;
							}

							$this->workbook->_cur_sheet->name = substr( $wbk, $pointer + 12, $len );
						} 
else {
							$this->workbook->_cur_sheet->unicode = false;
							$len = ord( $wbk[$pointer + 10] );
							$this->workbook->_cur_sheet->name = substr( $wbk, $pointer + 11, $len );
						}

						$pws = $this->_read_worksheet( substr( $wbk, $ofs ) );
						$this->workbook->_cur_sheet->table = $pws;
						break;
					}

					case RCRD_FORMAT: {
						$fidx = $this->_get_val( $wbk, $pointer, 4 );
						$length = ord( $wbk[$pointer + 5] );
						$this->workbook->_num_formats[$fidx] = getunicodestring( $wbk, $pointer + 6, 0 - 1 );
						break;
					}

					case RCRD_DATEMODE: {
						$dateidx = $this->_get_val( $wbk, $pointer, 4 );
						$this->workbook->date_mode = $dateidx;
						break;
					}

					case RCRD_FONT: {
						$cur_font = array( 'size' => 0, 'script' => XF_SCRIPT_NONE, 'underline' => XF_UL_NONE, 'italic' => false, 'strikeout' => false, 'bold' => false, 'boldness' => XF_WGHT_REGULAR, 'color' => 0, 'name' => '' );
						$cur_font['size'] = $this->_get_val( $wbk, $pointer + 4, 0 ) / 20;
						$style = ord( $wbk[$pointer + 4 + 2] );

						if (( $style & XF_STYLE_ITALIC ) != 0) {
							$cur_font['italic'] = true;
						}


						if (( $style & XF_STYLE_STRIKEOUT ) != 0) {
							$cur_font['strikeout'] = true;
						}

						$cur_font['color'] = $this->_get_val( $wbk, $pointer + 4, 4 ) - 8;
						$cur_font['boldness'] = $this->_get_val( $wbk, $pointer + 4, 6 );
						$cur_font['bold'] = ($cur_font['boldness'] == XF_WGHT_REGULAR ? false : true);
						$cur_font['script'] = $this->_get_val( $wbk, $pointer + 4, 8 );
						$cur_font['underline'] = ord( $wbk[$pointer + 4 + 10] );

						if (ord( $wbk[$pointer + 4 + 17] ) != 0) {
							$length = ord( $wbk[$pointer + 4 + 14] );
							$cur_font['name'] = substr( $wbk, $pointer + 4 + 15, $length );
						} 
else {
							$length = ord( $wbk[$pointer + 4 + 14] );

							if (0 < $length) {
								if (ord( $wbk[$pointer + 4 + 15] ) == 0) {
									$cur_font['name'] = substr( $wbk, $pointer + 4 + 16, $length );
								} 
else {
									$cur_font['name'] = getunicodestring( $wbk, $pointer + 4 + 15, $length );
								}
							}
						}

						$this->workbook->_fonts[] = $cur_font;
						break;
					}

					case RCRD_XF: {
						$this->workbook->_xf['font'][$xf_count] = $this->_get_val( $wbk, $pointer, 4 );
						$this->workbook->_xf['format'][$xf_count] = $this->_get_val( $wbk, $pointer, 6 );
						$this->workbook->_xf['type'][$xf_count] = '1';
						$this->workbook->_xf['bitmask'][$xf_count] = '1';
						$align = $this->_get_val( $wbk, $pointer, 10 );
						$this->workbook->_xf['h_align'][$xf_count] = bindec( substr( decbin( $align ), 0 - 2 ) );
						$v_al = bindec( substr( decbin( $align ), 0 - 6, 2 ) );
						$v_al8 = bindec( substr( decbin( $align ), 0 - 5, 2 ) );

						if (8 <= $biff_ver) {
							switch ($v_al8) {
								case 1: {
									$this->workbook->_xf['v_align'][$xf_count] = 2;
									break;
								}

								case 2: {
									$this->workbook->_xf['v_align'][$xf_count] = 0;
									break;
								}

								case 3: {
									$this->workbook->_xf['v_align'][$xf_count] = 1;
								}
							}
						} 
else {
							$this->workbook->_xf['v_align'][$xf_count] = $v_al;
						}


						if (8 <= $biff_ver) {
							$bg_fg = $this->_get_val( $wbk, $pointer, 22 );
							$bg = bindec( substr( decbin( $bg_fg ), 0, 7 ) );
							$fg = bindec( substr( decbin( $bg_fg ), 0 - 7 ) );
						} 
else {
							$bg_fg = $this->_get_val( $wbk, $pointer, 12 );
							$bg = bindec( substr( decbin( $bg_fg ), 0, 7 ) );
							$fg = bindec( substr( decbin( $bg_fg ), 0 - 7 ) );
						}

						$this->workbook->_xf['fg_color'][$xf_count] = $fg - 8;
						$this->workbook->_xf['bg_color'][$xf_count] = $bg - 8;
						++$xf_count;
						break;
					}

					case RCRD_SST: {
						if ($biff_ver < 8) {
							break;
						}

						$sb_len = $this->_get_val( $wbk, $pointer, 2 );

						if ($oc != RCRD_CONTINUE) {
							if ($cont_def) {
								trigger_error( 'File or stream is corrupted', E_USER_ERROR );
							}

							$snum = get_long_from_string( substr( $wbk, $pointer + 8, 4 ) );
							$s_pointer = $pointer + 12;
							$cont_def = true;
						} 
else {
							if ($s_len < $rslen) {
								$s_pointer = $pointer + 4;
								$rslen -= $s_len;
								$s_len = $rslen;

								if (0 < ( ord( $wbk[$s_pointer] ) & 1 )) {
									if ($char_bytes == 1) {
										$sstr = '';
										$i = 0;

										while ($i < strlen( $str )) {
											$sstr .= $str[$i] . chr( 0 );
											++$i;
										}

										$str = $sstr;
										$char_bytes = 2;
									}

									$schar_bytes = 2;
								} 
else {
									$schar_bytes = 1;
								}


								if ($pointer + 4 + $sb_len < $s_pointer + $s_len * $schar_bytes) {
									$s_len = ( $pointer + $sb_len - $s_pointer + 3 ) / $schar_bytes;
								}

								$sstr = substr( $wbk, $s_pointer + 1, $s_len * $schar_bytes );

								if (( $char_bytes == 2 && $schar_bytes == 1 )) {
									$sstr2 = '';
									$i = 0;

									while ($i < strlen( $sstr )) {
										$sstr2 .= $sstr[$i] . chr( 0 );
										++$i;
									}

									$sstr = $sstr2;
								}

								$str .= $sstr;
								$s_pointer += $s_len * $schar_bytes + 1 + 4 * $rt + $fesz;

								if ($s_len < $rslen) {
									if (( ( strlen( $wbk ) <= $s_pointer || $s_pointer < $pointer + 4 + $sb_len ) || ord( $wbk[$s_pointer] ) != RCRD_CONTINUE )) {
										trigger_error( 'File or stream is corrupted', E_USER_ERROR );
									}

									break;
								}


								if ($char_bytes == 2) {
									$this->workbook->_cell_data['unicode'][] = true;
								} 
else {
									$this->workbook->_cell_data['unicode'][] = false;
								}

								$this->workbook->_cell_data['data'][] = $str;
								--$snum;
							} 
else {
								$s_pointer = $pointer + 4;

								if ($pointer < $s_pointer) {
									$s_pointer += 4 * $rt + $fesz;
								}
							}
						}


						while (( ( $s_pointer < $pointer + 4 + $sb_len && $s_pointer < strlen( $wbk ) ) && 0 < $snum )) {
							$rslen = $this->_get_val( $wbk, $s_pointer, 0 );
							$s_len = $rslen;

							if (0 < ( ord( $wbk[$s_pointer + 2] ) & 1 )) {
								$char_bytes = 2;
							} 
else {
								$char_bytes = 1;
							}

							$rt = 0;
							$fesz = 0;
							switch (ord( $wbk[$s_pointer + 2] ) & 12) {
								case 12: {
									$rt = $this->_get_val( $wbk, $s_pointer, 3 );
									$fesz = get_long_from_string( substr( $wbk, $s_pointer + 5, 4 ) );

									if ($pointer + 4 + $sb_len < $s_pointer + 9 + $s_len * $char_bytes) {
										$s_len = ( $pointer + $sb_len - $s_pointer - 5 ) / $char_bytes;
									}

									$str = substr( $wbk, $s_pointer + 9, $s_len * $char_bytes );
									$s_pointer += $s_len * $char_bytes + 9;
									break;
								}

								case 8: {
									$rt = $this->_get_val( $wbk, $s_pointer, 3 );

									if ($pointer + 4 + $sb_len < $s_pointer + 5 + $s_len * $char_bytes) {
										$s_len = ( $pointer + $sb_len - $s_pointer - 1 ) / $char_bytes;
									}

									$str = substr( $wbk, $s_pointer + 5, $s_len * $char_bytes );
									$s_pointer += $s_len * $char_bytes + 5;
									break;
								}

								case 4: {
									$fesz = get_long_from_string( substr( $wbk, $s_pointer + 3, 4 ) );

									if ($pointer + 4 + $sb_len < $s_pointer + 7 + $s_len * $char_bytes) {
										$s_len = ( $pointer + $sb_len - $s_pointer - 3 ) / $char_bytes;
									}

									$str = substr( $wbk, $s_pointer + 7, $s_len * $char_bytes );
									$s_pointer += $s_len * $char_bytes + 7;
									break;
								}

								case 0: {
									if ($pointer + 4 + $sb_len < $s_pointer + 3 + $s_len * $char_bytes) {
										$s_len = ( $pointer + $sb_len - $s_pointer + 1 ) / $char_bytes;
									}

									$str = substr( $wbk, $s_pointer + 3, $s_len * $char_bytes );
									$s_pointer += $s_len * $char_bytes + 3;
								}
							}


							if ($s_len < $rslen) {
								if (( ( strlen( $wbk ) <= $s_pointer || $s_pointer < $pointer + 4 + $sb_len ) || ord( $wbk[$s_pointer] ) != 60 )) {
									trigger_error( 'File or stream is corrupted', E_USER_ERROR );
									continue;
								}

								continue;
							}


							if ($char_bytes == 2) {
								$this->workbook->_cell_data['unicode'][] = true;
							} 
else {
								$this->workbook->_cell_data['unicode'][] = false;
							}

							$s_pointer += 4 * $rt + $fesz;
							$this->workbook->_cell_data['data'][] = $str;
							--$snum;
						}

						break;
					}

					case RCRD_PALETTE: {
						$pal_ln = $this->_get_val( $wbk, $pointer, 0 );
						list( $null, $rcrd_len, $colors_count ) = unpack( 'v2', substr( $wbk, $pointer + 2, 4 ) );
						$cl = 0;

						while ($cl < $colors_count) {
							list( $null, $r, $g, $b, $tmp ) = unpack( 'C4', substr( $wbk, $pointer + 6 + $cl * 4, 4 ) );
							$r_h = (strlen( dechex( $r ) ) < 2 ? '0' . dechex( $r ) : dechex( $r ));
							$g_h = (strlen( dechex( $g ) ) < 2 ? '0' . dechex( $g ) : dechex( $g ));
							$b_h = (strlen( dechex( $b ) ) < 2 ? '0' . dechex( $b ) : dechex( $b ));
							$col_HEX = $r_h . $g_h . $b_h;
							$this->workbook->_palette[] = strtoupper( $col_HEX );
							++$cl;
						}

						break;
					}

					default: {
						break;
						$pointer += 4 + $this->_get_val( $wbk, $pointer, 2 );
						break;
					}
				}
			}
		}

		function _read_worksheet($wst) {
			if (strlen( $wst ) <= 0) {
				trigger_error( 'WorkSheet was not found', E_USER_ERROR );
			}


			if (strlen( $wst ) < 4) {
				trigger_error( 'File or stream is corrupted', E_USER_ERROR );
			}


			if (( ( strlen( $wst ) < 4 || strlen( $wst ) < $this->_get_val( $wst, 0, 2 ) ) || ord( $wst[0] ) != 9 )) {
				trigger_error( 'File or stream is corrupted', E_USER_ERROR );
			}

			$cur_ver = ord( $wst[1] );

			if (( ( ( $cur_ver != 0 && $cur_ver != 2 ) && $cur_ver != 4 ) && $cur_ver != 8 )) {
				trigger_error( 'Invalid file format version', E_USER_ERROR );
			}


			if ($cur_ver != 8) {
				$biff_ver = ( $ver + 4 ) / 2;
			} 
else {
				if (strlen( $wst ) < 12) {
					trigger_error( 'File or stream is corrupted', E_USER_ERROR );
				}

				switch ($this->_get_val( $wst, 0, 4 )) {
					case 1280: {
						if ($this->_get_val( $wst, 0, 10 ) < 1994) {
							$biff_ver = 5;
						} 
else {
							switch ($this->_get_val( $wst, 0, 8 )) {
								case 2412: {
								}

								case 3218: {
								}

								case 3321: {
									$biff_ver = 5;
									break;
								}

								default: {
									$biff_ver = 7;
									break;
								}
							}
						}

						break;
					}

					case 1536: {
						$biff_ver = 8;
						break;
					}

					default: {
						trigger_error( 'Unsupported file format version', E_USER_ERROR );
					}
				}
			}


			if ($biff_ver < 5) {
				trigger_error( ( '' . 'Unsupported file format version [' . $biff_ver . ']' ), E_USER_ERROR );
			}

			$this->workbook->_cur_sheet->biff_ver = $biff_ver;
			$pointer = 0;
			$data = array(  );

			while (( ord( $wst[$pointer] ) != 10 && $pointer < strlen( $wst ) )) {
				switch ($this->_get_val( $wst, $pointer, 0 )) {
					case 229: {
						$number_of_merges = $this->_get_val( $wst, $pointer + 2, 2 );
						$cntr = 0;
						$mrg = array(  );
						$mr = 0;

						while ($mr < $number_of_merges) {
							$firstrow = $this->_get_val( $wst, $pointer + 4 + $cntr, 2 );
							$lastrow = $this->_get_val( $wst, $pointer + 6 + $cntr, 2 );
							$firstcol = $this->_get_val( $wst, $pointer + 8 + $cntr, 2 );
							$lastcol = $this->_get_val( $wst, $pointer + 10 + $cntr, 2 );
							$this->workbook->_cur_sheet->_cells_merge_info[$firstrow][$firstcol]['last_row'] = $lastrow;
							$this->workbook->_cur_sheet->_cells_merge_info[$firstrow][$firstcol]['last_col'] = $lastcol;
							$cntr += 8;
							++$mr;
						}

						unset( $mrg );
						break;
					}

					case RCRD_NUMBER: {
					}

					case RCRD_FORMULA: {
						if ($this->_get_val( $wst, $pointer, 2 ) < 14) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$row = $this->_get_val( $wst, $pointer, 4 );
						$col = $this->_get_val( $wst, $pointer, 6 );
						$num_lo = get_long_from_string( substr( $wst, $pointer + 10, 4 ) );
						$num_hi = get_long_from_string( substr( $wst, $pointer + 14, 4 ) );
						$xf_idx = $this->_get_val( $wst, $pointer, 8 );
						$data[$row][$col]['type'] = 2;
						$fonti = $this->workbook->_xf['font'][$xf_idx];
						$data[$row][$col]['xf'] = $xf_idx;
						$data[$row][$col]['font'] = $fonti;

						if (( $num_hi == 0 && $num_lo == 0 )) {
							$data[$row][$col]['data'] = 0;
						} 
else {
							$fexp = ( ( $num_hi & 2146435072 ) >> 20 ) - 1023;
							$val = 1 + ( ( $num_hi & 1048575 ) + $num_lo / 4294967296 ) / 1048576;

							if (0 < $fexp) {
								$i = 0;

								while ($i < $fexp) {
									$val *= 2;
									++$i;
								}
							} 
else {
								$i = 0;

								while ($i < abs( $fexp )) {
									$val /= 2;
									++$i;
								}
							}


							if ($num_hi & 2147483648) {
								$val = 0 - $val;
							}

							$data[$row][$col]['data'] = (double)$val;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_rows ) || $this->workbook->_cur_sheet->num_rows < $row )) {
							$this->workbook->_cur_sheet->num_rows = $row;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_cols ) || $this->workbook->_cur_sheet->num_cols < $col )) {
							$this->workbook->_cur_sheet->num_cols = $col;
						}

						break;
					}

					case RCRD_RK: {
						if ($this->_get_val( $wst, $pointer, 2 ) < 10) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$row = $this->_get_val( $wst, $pointer, 4 );
						$col = $this->_get_val( $wst, $pointer, 6 );
						$xf_idx = $this->_get_val( $wst, $pointer, 8 );
						$val = $this->_decode_rk( get_long_from_string( substr( $wst, $pointer + 10, 4 ) ) );
						$data[$row][$col]['type'] = $val['type'];
						$fonti = $this->workbook->_xf['font'][$xf_idx];
						$data[$row][$col]['xf'] = $xf_idx;
						$data[$row][$col]['font'] = $fonti;
						$data[$row][$col]['data'] = $val['val'];

						if (( !isset( $this->workbook->_cur_sheet->num_rows ) || $this->workbook->_cur_sheet->num_rows < $row )) {
							$this->workbook->_cur_sheet->num_rows = $row;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_cols ) || $this->workbook->_cur_sheet->num_cols < $col )) {
							$this->workbook->_cur_sheet->num_cols = $col;
						}

						break;
					}

					case RCRD_MULRK: {
						$sz = $this->_get_val( $wst, $pointer, 2 );

						if ($sz < 6) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$row = $this->_get_val( $wst, $pointer, 4 );
						$fc = $this->_get_val( $wst, $pointer, 6 );
						$lc = $this->_get_val( $wst, $pointer, $sz + 2 );
						$i = 0;

						while ($i <= $lc - $fc) {
							$val = $this->_decode_rk( get_long_from_string( substr( $wst, $pointer + 10 + $i * 6, 4 ) ) );
							$xf_idx = $this->_get_val( $wst, $pointer, $i * 6 + 8 );
							$data[$row][$fc + $i]['type'] = $val['type'];
							$fonti = $this->workbook->_xf['font'][$xf_idx];
							$data[$row][$fc + $i]['xf'] = $xf_idx;
							$data[$row][$fc + $i]['font'] = $fonti;
							$data[$row][$fc + $i]['data'] = $val['val'];
							++$i;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_rows ) || $this->workbook->_cur_sheet->num_rows < $row )) {
							$this->workbook->_cur_sheet->num_rows = $row;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_cols ) || $this->workbook->_cur_sheet->num_cols < $col )) {
							$this->workbook->_cur_sheet->num_cols = $col;
						}

						break;
					}

					case RCRD_LABEL: {
						if ($this->_get_val( $wst, $pointer, 2 ) < 8) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$row = $this->_get_val( $wst, $pointer, 4 );
						$col = $this->_get_val( $wst, $pointer, 6 );
						$xf = $this->_get_val( $wst, $pointer, 8 );
						$fonti = $this->workbook->_xf['font'][$xf];
						$font = $this->fonts[$fonti];
						$str_len = $this->_get_val( $wst, $pointer, 10 );

						if (strlen( $wst ) < $pointer + 12 + $str_len) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$this->workbook->_cell_data['unicode'][] = false;
						$this->workbook->_cell_data['data'][] = substr( $wst, $pointer + 12, $str_len );
						$data[$row][$col]['xf'] = $xf;
						$data[$row][$col]['type'] = 0;
						$sst_ind = count( $this->workbook->_cell_data['data'] ) - 1;
						$data[$row][$col]['data'] = $sst_ind;
						$data[$row][$col]['font'] = $fonti;

						if (( !isset( $this->workbook->_cur_sheet->num_rows ) || $this->workbook->_cur_sheet->num_rows < $row )) {
							$this->workbook->_cur_sheet->num_rows = $row;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_cols ) || $this->workbook->_cur_sheet->num_cols < $col )) {
							$this->workbook->_cur_sheet->num_cols = $col;
						}

						break;
					}

					case RCRD_LABELSST: {
						if ($biff_ver < 8) {
							break;
						}


						if ($this->_get_val( $wst, $pointer, 2 ) < 10) {
							trigger_error( 'File or stream is corrupted', E_USER_ERROR );
						}

						$row = $this->_get_val( $wst, $pointer, 4 );
						$col = $this->_get_val( $wst, $pointer, 6 );
						$xf = $this->_get_val( $wst, $pointer, 8 );
						$fonti = $this->workbook->_xf['font'][$xf];
						$data[$row][$col]['xf'] = $xf;
						$font = &$this->workbook->_fonts[$fonti];

						$data[$row][$col]['type'] = 0;
						$sst_ind = get_long_from_string( substr( $wst, $pointer + 10, 4 ) );
						$data[$row][$col]['data'] = $sst_ind;
						$data[$row][$col]['font'] = $fonti;

						if (( !isset( $this->workbook->_cur_sheet->num_rows ) || $this->workbook->_cur_sheet->num_rows < $row )) {
							$this->workbook->_cur_sheet->num_rows = $row;
						}


						if (( !isset( $this->workbook->_cur_sheet->num_cols ) || $this->workbook->_cur_sheet->num_cols < $col )) {
							$this->workbook->_cur_sheet->num_cols = $col;
						}

						break;
					}

					case RCRD_BLANK: {
						$row = $this->_get_val( $wst, $pointer, 4 );
						$col = $this->_get_val( $wst, $pointer, 6 );
						$xf = $this->_get_val( $wst, $pointer, 8 );
						$fonti = $this->workbook->_xf['font'][$xf];
						$data[$row][$col]['xf'] = $xf;
						$font = &$this->workbook->_fonts[$fonti];

						$data[$row][$col]['type'] = 0 - 1;
						$data[$row][$col]['font'] = $fonti;
						break;
					}

					case RCRD_COLINFO: {
						$start_col = $this->_get_val( $wst, $pointer, 4 );
						$end_col = $this->_get_val( $wst, $pointer, 6 );
						$col_width = round( $this->_get_val( $wst, $pointer, 8 ) / 256 - 0.719999999999999973354647 );
						$i = $start_col;

						while ($i <= $end_col) {
							$this->workbook->_cur_sheet->_col_width[$i] = $col_width;
							++$i;
						}

						break;
					}

					default: {
						break;
					}
				}

				$pointer += 4 + $this->_get_val( $wst, $pointer, 2 );
			}

			return $data;
		}

		function _get_val($cont, $idx, $offset) {
			return ord( $cont[$idx + $offset] ) + 256 * ord( $cont[$idx + $offset + 1] );
		}

		function get_blocks_chain($offset, $small = false) {
			$chain = array(  );
			$next_block = $offset;

			if ($small === false) {
				while (( ( $next_block != 4294967294 && $next_block <= $this->_blocks_count ) && $next_block < count( $this->fat ) )) {
					$chain[] = $next_block;
					$next_block = $this->fat[$next_block];
				}
			} 
else {
				while (( ( $next_block != 4294967294 && $next_block <= $this->_sblocks_count ) && $next_block < count( $this->sfat ) )) {
					$chain[] = $next_block;
					$next_block = $this->sfat[$next_block];
				}
			}


			if ($next_block != 4294967294) {
				return false;
			}

			return $chain;
		}

		function _get_stream_index($dir, $stream_name, $stream_num = 0) {
			$dt = $dir->get_ord( $stream_num * THIS_BLOCK_SIZE + 66 );
			$prev = $dir->get_long( $stream_num * THIS_BLOCK_SIZE + 68 );
			$next = $dir->get_long( $stream_num * THIS_BLOCK_SIZE + 72 );
			$dir_ = $dir->get_long( $stream_num * THIS_BLOCK_SIZE + 76 );
			$curr_name = '';
			$s_area = ( $dir->get_ord( $stream_num * THIS_BLOCK_SIZE + 64 ) + 256 * $dir->get_ord( $stream_num * THIS_BLOCK_SIZE + 65 ) ) / 2 - 1;

			if (( $dt == 2 || $dt == 5 )) {
				$i = 0;

				while ($i < $s_area) {
					$curr_name .= $dir->get_byte( $stream_num * THIS_BLOCK_SIZE + $i * 2 );
					++$i;
				}
			}


			if (( ( $dt == 2 || $dt == 5 ) && strcmp( $curr_name, $stream_name ) == 0 )) {
				return $stream_num;
			}


			if (( $prev != 4294967295 && $prev != 2704 )) {
				$i = $this->_get_stream_index( $dir, $stream_name, $prev );

				if (0 <= $i) {
					return $i;
				}
			}


			if (( $next != 4294967295 && $next != 2704 )) {
				$i = $this->_get_stream_index( $dir, $stream_name, $next );

				if (0 <= $i) {
					return $i;
				}
			}


			if (( $dir_ != 4294967295 && $dir_ != 2704 )) {
				$i = $this->_get_stream_index( $dir, $stream_name, $dir_ );

				if (0 <= $i) {
					return $i;
				}
			}

			return 0 - 2;
		}

		function _decode_rk($rk) {
			$res = array(  );

			if ($rk & 2) {
				$val = ( $rk & 4294967292 ) >> 2;

				if ($rk & 1) {
					$val = $val / 100;
				}


				if ((double)$val == floor( (double)$val )) {
					$res['val'] = (int)$val;
					$res['type'] = 1;
				} 
else {
					$res['val'] = (double)$val;
					$res['type'] = 2;
				}
			} 
else {
				$res['type'] = 2;
				$frk = $rk;
				$fexp = ( ( $frk & 2146435072 ) >> 20 ) - 1023;
				$val = 1 + ( ( $frk & 1048575 ) >> 2 ) / 262144;

				if (0 < $fexp) {
					$i = 0;

					while ($i < $fexp) {
						$val *= 2;
						++$i;
					}
				} 
else {
					if ($fexp == 0 - 1023) {
						$val = 0;
					} 
else {
						$i = 0;

						while ($i < abs( $fexp )) {
							$val /= 2;
							++$i;
						}
					}
				}


				if ($rk & 1) {
					$val = $val / 100;
				}


				if ($rk & 2147483648) {
					$val = 0 - $val;
				}

				$res['val'] = (double)$val;
			}

			return $res;
		}
	}

	require_once( 'hdr_xls_reader.php' );
	require_once( 'cl_xls_container.php' );
	require_once( 'str_xls_book.php' );
?>