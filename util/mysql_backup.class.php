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
			set_time_limit( 1200 );
			$this->host = $host;
			$this->db = $db;
			$this->user = $user;
			$this->pass = $pass;
			$this->output = $output;
			$this->structure_only = $structure_only;
		}

		function _mysqlbackup($host, $dbname, $uid, $pwd, $output, $structure_only) {
			if (( strval( $this->output ) != '' && $this->output != null )) {
				$this->fptr = fopen( $this->output, 'w' );
			} 
else {
				$this->fptr = false;
				header( 'Content-type: text' );
				header( 'Content-Disposition: attachment; filename=backup.txt' );
				header( 'Content-Description: Backup File' );
			}

			$this->_Out( '

' );
			fclose( $this->fptr );
			return 0;
		}

		function largerestore($first = 0, $last = 0) {
			if (!$link = mysql_connect( $this->host, $this->user, $this->pass )) {
				exit( mysql_error(  ) );
			}

			$ok = mysql_select_db( $this->db, $link );

			if ($ok == false) {
				trigger_error( mysql_error(  ), 256 );
			}

			$filename = $this->output;
			$fp = fopen( $filename, 'r' );

			if ($fp == false) {
				trigger_error(  . 'Couldn\'t open ' . $filename, 256 );
			}


			if ($first <= 1) {
				$this->_dropAllTables(  );
			}

			$text = '';
			$done = 0;

			while (!feof( $fp )) {
				$line = fgets( $fp, 4096 );
				$text .= $line;
				$posn = strpos( $text, ';' );

				if ($posn === false) {
					continue;
				}

				++$done;
				$doRestore = true;

				if (( 0 < $first && $done < $first )) {
					$doRestore = false;
				}


				if (( 0 < $last && $last < $done )) {
					break;
				}

				$left = $this->_restoreText( $text, $doRestore );
				$text = $left;
			}

			return true;
		}

		function _restoretext($input, $doRestore = true) {
			$text = $input;

			while (true) {
				$posn = strpos( $text, ';' );

				if ($posn === false) {
					break;
				}

				$len = $posn + 1;
				$sql = substr( $text, 0, $len );

				if ($doRestore == true) {
					$ok = mysql_unbuffered_query( $sql );

					if ($ok == false) {
						trigger_error( $sql . ' mysql' . mysql_error(  ) . ' err=' . mysql_errno(  ), 256 );
					}
				}

				$text = substr( $text, $posn + 1 );
			}

			return $text;
		}

		function _open() {
			$SQL = '';
			$filename = $this->output;

			if (!$fp = fopen( $filename, 'r' )) {
				exit(  . 'Couldn\'t open ' . $filename );
			}


			while (!feof( $fp )) {
				$line = fgets( $fp, 1024 );
				$SQL .=  . $line;
			}

			return $SQL;
		}

		function restore() {
			$tables = '';
			$SQL = explode( ';', $this->_Open( $this->output ) );

			if (!$link = mysql_connect( $this->host, $this->user, $this->pass )) {
				exit( mysql_error(  ) );
			}


			if (!mysql_select_db( $this->db, $link )) {
				exit( mysql_error(  ) );
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

			if (( $tables != '' || $tables != null )) {
				if (!mysql_query( 'DROP TABLE ' . $tables )) {
					exit( mysql_error(  ) );
				}
			}

			$i = 0;

			while ($i < count( $SQL ) - 1) {
				if (!mysql_unbuffered_query( $SQL[$i] )) {
					exit( $SQL[$i] . ' mysql' . mysql_error(  ) . ' err=' . mysql_errno(  ) );
				}

				++$i;
			}

			return 1;
		}

		function _out($s) {
			if ($this->fptr == false) {
				echo  . $s;
			} 
else {
				fputs( $this->fptr, $s );
			}

		}

		function backup() {
			$this->_Mysqlbackup( $this->host, $this->db, $this->user, $this->pass, $this->output, $this->structure_only );
			return 1;
		}

		function _isfieldtypenumeric($ft) {
			$num = false;

			if (substr( $ft, 0, 3 ) == 'int') {
				$num = true;
			}


			if (substr( $ft, 0, 7 ) == 'tinyint') {
				$num = true;
			}

			return $num;
		}

		function _dropalltables() {
			$tables = '';
			$result = mysql_list_tables( $this->db );
			$not = mysql_num_rows( $result );
			$i = 0;

			while ($i < $not) {
				if (0 < $i) {
					$tables .= ',';
				}

				$row = mysql_fetch_row( $result );
				$tables .= $row[0];
				++$i;
			}


			if ($tables != '') {
				mysql_query( 'DROP TABLE ' . $tables );
			}

		}
	}

	echo '
';
?>