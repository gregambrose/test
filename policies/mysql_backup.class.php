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

	class mysql_backup {
		var $host = null;
		var $db = null;
		var $user = null;
		var $pass = null;
		var $output = null;
		var $structure_only = null;
		var $fptr = null;

		function mysql_backup($host, $db, $user, $pass, $output, $structure_only) {
			set_time_limit( 120 );
			$this->host = $host;
			$this->db = $db;
			$this->user = $user;
			$this->pass = $pass;
			$this->output = $output;
			$this->structure_only = $structure_only;
		}

		function _mysqlbackup($host, $dbname, $uid, $pwd, $output, $structure_only) {
			if (strval( $this->output ) != '') {
				$this->fptr = fopen( $this->output, 'w' );
			} 
else {
				$this->fptr = false;
			}

			$con = mysql_connect( $this->host, $this->user, $this->pass );
			$db = mysql_select_db( $dbname, $con );
			$res = mysql_list_tables( $dbname );
			$nt = mysql_num_rows( $res );
			$a = 0;

			while ($a < $nt) {
				$row = mysql_fetch_row( $res );
				$tablename = $row[0];
				$sql = '' . 'create table ' . $tablename . '
(
';
				$res2 = mysql_query( '' . 'select * from ' . $tablename, $con );
				$nf = mysql_num_fields( $res2 );
				$nr = mysql_num_rows( $res2 );
				$fl = '';
				$b = 0;

				while ($b < $nf) {
					$fn = mysql_field_name( $res2, $b );
					$ft = mysql_fieldtype( $res2, $b );
					$fs = mysql_field_len( $res2, $b );
					$ff = mysql_field_flags( $res2, $b );
					$sql .= '' . '    ' . $fn . ' ';
					$is_numeric = false;
					switch (strtolower( $ft )) {
						case 'int': {
							$sql .= 'int';
							$is_numeric = true;
							break;
						}

						case 'blob': {
							$sql .= 'text';
							$is_numeric = false;
							break;
						}

						case 'real': {
							$sql .= 'real';
							break;
						}

						case 'string': {
							$sql .= '' . 'char(' . $fs . ')';
							$is_numeric = false;
							break;
						}

						case 'unknown': {
							switch (intval( $fs )) {
								case 4: {
									$sql .= 'tinyint';
									$is_numeric = true;
									break;
								}

								default: {
									$sql .= 'int';
									$is_numeric = true;
									break;
								}
							}

							break;
						}

						case 'timestamp': {
							$sql .= 'timestamp';
							break;
						}

						case 'date': {
							$sql .= 'date';
							$is_numeric = false;
							break;
						}

						case 'datetime': {
							$sql .= 'datetime';
							$is_numeric = false;
							break;
						}

						case 'time': {
							$sql .= 'time';
							$is_numeric = false;
							break;
						}

						default: {
							$sql .= $ft;
							$is_numeric = true;
							break;
						}
					}


					if (strpos( $ff, 'unsigned' ) != false) {
						if ($ft != 'timestamp') {
							$sql .= ' unsigned';
						}
					}


					if (strpos( $ff, 'zerofill' ) != false) {
						if ($ft != 'timestamp') {
							$sql .= ' zerofill';
						}
					}


					if (strpos( $ff, 'auto_increment' ) != false) {
						$sql .= ' auto_increment';
					}


					if (strpos( $ff, 'not_null' ) != false) {
						$sql .= ' not null';
					}


					if (strpos( $ff, 'primary_key' ) != false) {
						$sql .= ' primary key';
					}


					if ($b < $nf - 1) {
						$sql .= ',
';
						$fl .= $fn . ', ';
					} 
else {
						$sql .= '
);

';
						$fl .= $fn;
					}

					$fna[$b] = $fn;
					$ina[$b] = $is_numeric;
					++$b;
				}

				$this->_Out( $sql );

				if ($this->structure_only != true) {
					$c = 0;

					while ($c < $nr) {
						$sql = '' . 'insert into ' . $tablename . ' (' . $fl . ') values (';
						$row = mysql_fetch_row( $res2 );
						$d = 0;

						while ($d < $nf) {
							$data = strval( $row[$d] );

							if ($ina[$d] == true) {
								$sql .= intval( $data );
							} 
else {
								$line = '"' . mysql_real_escape_string( $data ) . '"';
								$line = str_replace( ';', '\S', $line );
								$sql .= $line;
							}


							if ($d < $nf - 1) {
								$sql .= ', ';
							}

							++$d;
						}

						$sql .= ');
';
						$this->_Out( $sql );
						++$c;
					}

					$this->_Out( '

' );
				}

				mysql_free_result( $res2 );
				++$a;
			}


			if ($this->fptr != false) {
				fclose( $this->fptr );
			}

			return 0;
		}

		function _open() {
			$SQL = '';
			$filename = $this->output;

			if (!( $fp = fopen( $filename, 'r' ))) {
				exit( '' . 'Couldn\'t open ' . $filename );
				(bool)true;
			}


			while (!feof( $fp )) {
				$line = fgets( $fp, 1024 );
				$SQL .= '' . $line;
			}

			return $SQL;
		}

		function restore() {
			$tables = '';
			$SQL = explode( ';', $this->_Open( $this->output ) );

			if (!( $link = mysql_connect( $this->host, $this->user, $this->pass ))) {
				exit( mysql_error(  ) );
				(bool)true;
			}


			if (!( mysql_select_db( $this->db, $link ))) {
				exit( mysql_error(  ) );
				(bool)true;
			}

			$result = mysql_list_tables( $this->db );
			$not = mysql_num_rows( $result );
			$i = 0;

			while ($i < $not - 1) {
				$row = mysql_fetch_row( $result );
				$tables .= $row[0] . ',';
				++$i;
			}

			$row = mysql_fetch_row( $result );
			$tables .= $row[0];

			if (( $tables != '' || $tables != NULL )) {
				if (!( mysql_query( 'DROP TABLE ' . $tables ))) {
					exit( mysql_error(  ) );
					(bool)true;
				}
			}

			$i = 0;

			while ($i < count( $SQL ) - 1) {
				if (!( mysql_unbuffered_query( $SQL[$i] ))) {
					exit( $SQL[$i] . ' mysql' . mysql_error(  ) . ' err=' . mysql_errno(  ) );
					(bool)true;
				}

				++$i;
			}

			return 1;
		}

		function _out($s) {
			if ($this->fptr == false) {
				echo '' . $s;
				return null;
			}

			fputs( $this->fptr, $s );
		}

		function backup() {
			$this->_Mysqlbackup( $this->host, $this->db, $this->user, $this->pass, $this->output, $this->structure_only );
			return 1;
		}
	}

?>