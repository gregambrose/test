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

	class phpmailer {
		var $Priority = 3;
		var $CharSet = 'iso-8859-1';
		var $ContentType = 'text/plain';
		var $Encoding = '8bit';
		var $ErrorInfo = '';
		var $From = 'root@localhost';
		var $FromName = 'Root User';
		var $Sender = '';
		var $Subject = '';
		var $Body = '';
		var $AltBody = '';
		var $WordWrap = 0;
		var $Mailer = 'mail';
		var $Sendmail = '/usr/sbin/sendmail';
		var $PluginDir = '';
		var $Version = '1.71';
		var $ConfirmReadingTo = '';
		var $Hostname = '';
		var $Host = 'localhost';
		var $Port = 25;
		var $Helo = '';
		var $SMTPAuth = false;
		var $Username = '';
		var $Password = '';
		var $Timeout = 10;
		var $SMTPDebug = false;
		var $SMTPKeepAlive = false;
		var $smtp = NULL;
		var $to = array(  );
		var $cc = array(  );
		var $bcc = array(  );
		var $ReplyTo = array(  );
		var $attachment = array(  );
		var $CustomHeader = array(  );
		var $message_type = '';
		var $boundary = array(  );
		var $language = array(  );
		var $error_count = 0;
		var $LE = '
';

		function ishtml($bool) {
			if ($bool == true) {
				$this->ContentType = 'text/html';
				return null;
			}

			$this->ContentType = 'text/plain';
		}

		function issmtp() {
			$this->Mailer = 'smtp';
		}

		function ismail() {
			$this->Mailer = 'mail';
		}

		function issendmail() {
			$this->Mailer = 'sendmail';
		}

		function isqmail() {
			$this->Sendmail = '/var/qmail/bin/sendmail';
			$this->Mailer = 'sendmail';
		}

		function addaddress($address, $name = '') {
			$cur = count( $this->to );
			$this->to[$cur][0] = trim( $address );
			$this->to[$cur][1] = $name;
		}

		function addcc($address, $name = '') {
			$cur = count( $this->cc );
			$this->cc[$cur][0] = trim( $address );
			$this->cc[$cur][1] = $name;
		}

		function addbcc($address, $name = '') {
			$cur = count( $this->bcc );
			$this->bcc[$cur][0] = trim( $address );
			$this->bcc[$cur][1] = $name;
		}

		function addreplyto($address, $name = '') {
			$cur = count( $this->ReplyTo );
			$this->ReplyTo[$cur][0] = trim( $address );
			$this->ReplyTo[$cur][1] = $name;
		}

		function send() {
			$header = '';
			$body = '';

			if (count( $this->to ) + count( $this->cc ) + count( $this->bcc ) < 1) {
				$this->SetError( $this->Lang( 'provide_address' ) );
				return false;
			}


			if (!empty( $this->AltBody )) {
				$this->ContentType = 'multipart/alternative';
			}

			$this->SetMessageType(  );
			$header .= $this->CreateHeader(  );
			$body = $this->CreateBody(  );

			if ($body == '') {
				return false;
			}


			if ($this->Mailer == 'sendmail') {
				if (!$this->SendmailSend( $header, $body )) {
					return false;
				}
			} 
else {
				if ($this->Mailer == 'mail') {
					if (!$this->MailSend( $header, $body )) {
						return false;
					}
				} 
else {
					if ($this->Mailer == 'smtp') {
						if (!$this->SmtpSend( $header, $body )) {
							return false;
						}
					} 
else {
						$this->SetError( $this->Mailer . $this->Lang( 'mailer_not_supported' ) );
						return false;
					}
				}
			}

			return true;
		}

		function sendmailsend($header, $body) {
			if ($this->Sender != '') {
				$sendmail = sprintf( '%s -oi -f %s -t', $this->Sendmail, $this->Sender );
			} 
else {
				$sendmail = sprintf( '%s -oi -t', $this->Sendmail );
			}


			if (!$mail = @popen( $sendmail, 'w' )) {
				$this->SetError( $this->Lang( 'execute' ) . $this->Sendmail );
				return false;
			}

			fputs( $mail, $header );
			fputs( $mail, $body );
			$result = pclose( $mail ) >> 8 & 255;

			if ($result != 0) {
				$this->SetError( $this->Lang( 'execute' ) . $this->Sendmail );
				return false;
			}

			return true;
		}

		function mailsend($header, $body) {
			$to = '';
			$i = 0;

			while ($i < count( $this->to )) {
				if ($i != 0) {
					$to .= ', ';
				}

				$to .= $this->to[$i][0];
				++$i;
			}


			if (( $this->Sender != '' && strlen( ini_get( 'safe_mode' ) ) < 1 )) {
				$old_from = ini_get( 'sendmail_from' );
				ini_set( 'sendmail_from', $this->Sender );
				$params = sprintf( '-oi -f %s', $this->Sender );
				$rt = @mail( $to, @$this->EncodeHeader( $this->Subject ), $body, $header, $params );
			} 
else {
				$rt = @mail( $to, @$this->EncodeHeader( $this->Subject ), $body, $header );
			}


			if (isset( $old_from )) {
				ini_set( 'sendmail_from', $old_from );
			}


			if (!$rt) {
				$this->SetError( $this->Lang( 'instantiate' ) );
				return false;
			}

			return true;
		}

		function smtpsend($header, $body) {
			include_once( $this->PluginDir . 'class.smtp.php' );
			$error = '';
			$bad_rcpt = array(  );

			if (!$this->SmtpConnect(  )) {
				return false;
			}

			$smtp_from = ($this->Sender == '' ? $this->From : $this->Sender);

			if (!$this->smtp->Mail( $smtp_from )) {
				$error = $this->Lang( 'from_failed' ) . $smtp_from;
				$this->SetError( $error );
				$this->smtp->Reset(  );
				return false;
			}

			$i = 0;

			while ($i < count( $this->to )) {
				if (!$this->smtp->Recipient( $this->to[$i][0] )) {
					$bad_rcpt[] = $this->to[$i][0];
				}

				++$i;
			}

			$i = 0;

			while ($i < count( $this->cc )) {
				if (!$this->smtp->Recipient( $this->cc[$i][0] )) {
					$bad_rcpt[] = $this->cc[$i][0];
				}

				++$i;
			}

			$i = 0;

			while ($i < count( $this->bcc )) {
				if (!$this->smtp->Recipient( $this->bcc[$i][0] )) {
					$bad_rcpt[] = $this->bcc[$i][0];
				}

				++$i;
			}


			if (0 < count( $bad_rcpt )) {
				$i = 0;

				while ($i < count( $bad_rcpt )) {
					if ($i != 0) {
						$error .= ', ';
					}

					$error .= $bad_rcpt[$i];
					++$i;
				}

				$error = $this->Lang( 'recipients_failed' ) . $error;
				$this->SetError( $error );
				$this->smtp->Reset(  );
				return false;
			}


			if (!$this->smtp->Data( $header . $body )) {
				$this->SetError( $this->Lang( 'data_not_accepted' ) );
				$this->smtp->Reset(  );
				return false;
			}


			if ($this->SMTPKeepAlive == true) {
				$this->smtp->Reset(  );
			} 
else {
				$this->SmtpClose(  );
			}

			return true;
		}

		function smtpconnect() {
			if ($this->smtp == NULL) {
				$this->smtp = new SMTP(  );
			}

			$this->smtp->do_debug = $this->SMTPDebug;
			$hosts = explode( ';', $this->Host );
			$index = 0;
			$connection = $this->smtp->Connected(  );

			while (( $index < count( $hosts ) && $connection == false )) {
				if (strstr( $hosts[$index], ':' )) {
					list( $host, $port ) = explode( ':', $hosts[$index] );
				} 
else {
					$host = $hosts[$index];
					$port = $this->Port;
				}


				if ($this->smtp->Connect( $host, $port, $this->Timeout )) {
					if ($this->Helo != '') {
						$this->smtp->Hello( $this->Helo );
					} 
else {
						$this->smtp->Hello( $this->ServerHostname(  ) );
					}


					if ($this->SMTPAuth) {
						if (!$this->smtp->Authenticate( $this->Username, $this->Password )) {
							$this->SetError( $this->Lang( 'authenticate' ) );
							$this->smtp->Reset(  );
							$connection = false;
						}
					}

					$connection = true;
				}

				++$index;
			}


			if (!$connection) {
				$this->SetError( $this->Lang( 'connect_host' ) );
			}

			return $connection;
		}

		function smtpclose() {
			if ($this->smtp != NULL) {
				if ($this->smtp->Connected(  )) {
					$this->smtp->Quit(  );
					$this->smtp->Close(  );
				}
			}

		}

		function setlanguage($lang_type, $lang_path = '') {
			if (file_exists( $lang_path . 'phpmailer.lang-' . $lang_type . '.php' )) {
				include( $lang_path . 'phpmailer.lang-' . $lang_type . '.php' );
			} 
else {
				if (file_exists( $lang_path . 'phpmailer.lang-en.php' )) {
					include( $lang_path . 'phpmailer.lang-en.php' );
				} 
else {
					if (( defined( 'UTIL_PATH' ) && file_exists( UTIL_PATH . 'phpmailer.lang-en.php' ) )) {
						include( $lang_path . 'phpmailer.lang-en.php' );
					} 
else {
						$this->SetError( 'Could not load language file' );
						return false;
					}
				}
			}

			$this->language = $PHPMAILER_LANG;
			return true;
		}

		function addrappend($type, $addr) {
			$addr_str = $type . ': ';
			$addr_str .= $this->AddrFormat( $addr[0] );

			if (1 < count( $addr )) {
				$i = 1;

				while ($i < count( $addr )) {
					$addr_str .= ', ' . $this->AddrFormat( $addr[$i] );
					++$i;
				}
			}

			$addr_str .= $this->LE;
			return $addr_str;
		}

		function addrformat($addr) {
			if (empty( $addr[1] )) {
				$formatted = $addr[0];
			} 
else {
				$formatted = $this->EncodeHeader( $addr[1], 'phrase' ) . ' <' . $addr[0] . '>';
			}

			return $formatted;
		}

		function wraptext($message, $length, $qp_mode = false) {
			$soft_break = ($qp_mode ? sprintf( ' =%s', $this->LE ) : $this->LE);
			$message = $this->FixEOL( $message );

			if (substr( $message, 0 - 1 ) == $this->LE) {
				$message = substr( $message, 0, 0 - 1 );
			}

			$line = explode( $this->LE, $message );
			$message = '';
			$i = 0;

			while ($i < count( $line )) {
				$line_part = explode( ' ', $line[$i] );
				$buf = '';
				$e = 0;

				while ($e < count( $line_part )) {
					$word = $line_part[$e];

					if (( $qp_mode && $length < strlen( $word ) )) {
						$space_left = $length - strlen( $buf ) - 1;

						if ($e != 0) {
							if (20 < $space_left) {
								$len = $space_left;

								if (substr( $word, $len - 1, 1 ) == '=') {
									--$len;
								} 
else {
									if (substr( $word, $len - 2, 1 ) == '=') {
										$len -= 2;
									}
								}

								$part = substr( $word, 0, $len );
								$word = substr( $word, $len );
								$buf .= ' ' . $part;
								$message .= $buf . sprintf( '=%s', $this->LE );
							} 
else {
								$message .= $buf . $soft_break;
							}

							$buf = '';
						}


						while (0 < strlen( $word )) {
							$len = $length;

							if (substr( $word, $len - 1, 1 ) == '=') {
								--$len;
							} 
else {
								if (substr( $word, $len - 2, 1 ) == '=') {
									$len -= 2;
								}
							}

							$part = substr( $word, 0, $len );
							$word = substr( $word, $len );

							if (0 < strlen( $word )) {
								$message .= $part . sprintf( '=%s', $this->LE );
								continue;
							}

							$buf = $part;
						}
					} 
else {
						$buf_o = $buf;
						$buf .= ($e == 0 ? $word : ' ' . $word);

						if (( $length < strlen( $buf ) && $buf_o != '' )) {
							$message .= $buf_o . $soft_break;
							$buf = $word;
						}
					}

					++$e;
				}

				$message .= $buf . $this->LE;
				++$i;
			}

			return $message;
		}

		function setwordwrap() {
			if ($this->WordWrap < 1) {
				return null;
			}

			switch ($this->message_type) {
				case 'alt': {
				}

				case 'alt_attachment': {
					$this->AltBody = $this->WrapText( $this->AltBody, $this->WordWrap );
					break;
				}

				default: {
					$this->Body = $this->WrapText( $this->Body, $this->WordWrap );
					break;
				}
			}

		}

		function createheader() {
			$result = '';
			$uniq_id = md5( uniqid( time(  ) ) );
			$this->boundary[1] = 'b1_' . $uniq_id;
			$this->boundary[2] = 'b2_' . $uniq_id;
			$result .= $this->Received(  );
			$result .= $this->HeaderLine( 'Date', $this->RFCDate(  ) );

			if ($this->Sender == '') {
				$result .= $this->HeaderLine( 'Return-Path', trim( $this->From ) );
			} 
else {
				$result .= $this->HeaderLine( 'Return-Path', trim( $this->Sender ) );
			}


			if ($this->Mailer != 'mail') {
				if (0 < count( $this->to )) {
					$result .= $this->AddrAppend( 'To', $this->to );
				} 
else {
					if (count( $this->cc ) == 0) {
						$result .= $this->HeaderLine( 'To', 'undisclosed-recipients:;' );
					}
				}


				if (0 < count( $this->cc )) {
					$result .= $this->AddrAppend( 'Cc', $this->cc );
				}
			}

			$from = array(  );
			$from[0][0] = trim( $this->From );
			$from[0][1] = $this->FromName;
			$result .= $this->AddrAppend( 'From', $from );

			if (( ( $this->Mailer == 'sendmail' || $this->Mailer == 'mail' ) && 0 < count( $this->bcc ) )) {
				$result .= $this->AddrAppend( 'Bcc', $this->bcc );
			}


			if (0 < count( $this->ReplyTo )) {
				$result .= $this->AddrAppend( 'Reply-to', $this->ReplyTo );
			}


			if ($this->Mailer != 'mail') {
				$result .= $this->HeaderLine( 'Subject', $this->EncodeHeader( trim( $this->Subject ) ) );
			}

			$result .= sprintf( 'Message-ID: <%s@%s>%s', $uniq_id, $this->ServerHostname(  ), $this->LE );
			$result .= $this->HeaderLine( 'X-Priority', $this->Priority );
			$result .= $this->HeaderLine( 'X-Mailer', 'PHPMailer [version ' . $this->Version . ']' );

			if ($this->ConfirmReadingTo != '') {
				$result .= $this->HeaderLine( 'Disposition-Notification-To', '<' . trim( $this->ConfirmReadingTo ) . '>' );
			}

			$index = 0;

			while ($index < count( $this->CustomHeader )) {
				$result .= $this->HeaderLine( trim( $this->CustomHeader[$index][0] ), $this->EncodeHeader( trim( $this->CustomHeader[$index][1] ) ) );
				++$index;
			}

			$result .= $this->HeaderLine( 'MIME-Version', '1.0' );
			switch ($this->message_type) {
				case 'plain': {
					$result .= $this->HeaderLine( 'Content-Transfer-Encoding', $this->Encoding );
					$result .= sprintf( 'Content-Type: %s; charset="%s"', $this->ContentType, $this->CharSet );
					break;
				}

				case 'attachments': {
				}

				case 'alt_attachments': {
					if ($this->InlineImageExists(  )) {
						$result .= sprintf( 'Content-Type: %s;%s	type="text/html";%s	boundary="%s"%s', 'multipart/related', $this->LE, $this->LE, $this->boundary[1], $this->LE );
					} 
else {
						$result .= $this->HeaderLine( 'Content-Type', 'multipart/mixed;' );
						$result .= $this->TextLine( '	boundary="' . $this->boundary[1] . '"' );
					}

					break;
				}

				case 'alt': {
					$result .= $this->HeaderLine( 'Content-Type', 'multipart/alternative;' );
					$result .= $this->TextLine( '	boundary="' . $this->boundary[1] . '"' );
				}
			}


			if ($this->Mailer != 'mail') {
				$result .= $this->LE . $this->LE;
			}

			return $result;
		}

		function createbody() {
			$result = '';
			$this->SetWordWrap(  );
			switch ($this->message_type) {
				case 'alt': {
					$result .= $this->GetBoundary( $this->boundary[1], '', 'text/plain', '' );
					$result .= $this->EncodeString( $this->AltBody, $this->Encoding );
					$result .= $this->LE . $this->LE;
					$result .= $this->GetBoundary( $this->boundary[1], '', 'text/html', '' );
					$result .= $this->EncodeString( $this->Body, $this->Encoding );
					$result .= $this->LE . $this->LE;
					$result .= $this->EndBoundary( $this->boundary[1] );
					break;
				}

				case 'plain': {
					$result .= $this->EncodeString( $this->Body, $this->Encoding );
					break;
				}

				case 'attachments': {
					$result .= $this->GetBoundary( $this->boundary[1], '', '', '' );
					$result .= $this->EncodeString( $this->Body, $this->Encoding );
					$result .= $this->LE;
					$result .= $this->AttachAll(  );
					break;
				}

				case 'alt_attachments': {
					$result .= sprintf( '--%s%s', $this->boundary[1], $this->LE );
					$result .= sprintf( 'Content-Type: %s;%s' . '	boundary="%s"%s', 'multipart/alternative', $this->LE, $this->boundary[2], $this->LE . $this->LE );
					$result .= $this->GetBoundary( $this->boundary[2], '', 'text/plain', '' ) . $this->LE;
					$result .= $this->EncodeString( $this->AltBody, $this->Encoding );
					$result .= $this->LE . $this->LE;
					$result .= $this->GetBoundary( $this->boundary[2], '', 'text/html', '' ) . $this->LE;
					$result .= $this->EncodeString( $this->Body, $this->Encoding );
					$result .= $this->LE . $this->LE;
					$result .= $this->EndBoundary( $this->boundary[2] );
					$result .= $this->AttachAll(  );
				}
			}


			if ($this->IsError(  )) {
				$result = '';
			}

			return $result;
		}

		function getboundary($boundary, $charSet, $contentType, $encoding) {
			$result = '';

			if ($charSet == '') {
				$charSet = $this->CharSet;
			}


			if ($contentType == '') {
				$contentType = $this->ContentType;
			}


			if ($encoding == '') {
				$encoding = $this->Encoding;
			}

			$result .= $this->TextLine( '--' . $boundary );
			$result .= sprintf( 'Content-Type: %s; charset = "%s"', $contentType, $charSet );
			$result .= $this->LE;
			$result .= $this->HeaderLine( 'Content-Transfer-Encoding', $encoding );
			$result .= $this->LE;
			return $result;
		}

		function endboundary($boundary) {
			return $this->LE . '--' . $boundary . '--' . $this->LE;
		}

		function setmessagetype() {
			if (( count( $this->attachment ) < 1 && strlen( $this->AltBody ) < 1 )) {
				$this->message_type = 'plain';
				return null;
			}


			if (0 < count( $this->attachment )) {
				$this->message_type = 'attachments';
			}


			if (( 0 < strlen( $this->AltBody ) && count( $this->attachment ) < 1 )) {
				$this->message_type = 'alt';
			}


			if (( 0 < strlen( $this->AltBody ) && 0 < count( $this->attachment ) )) {
				$this->message_type = 'alt_attachments';
			}

		}

		function headerline($name, $value) {
			return $name . ': ' . $value . $this->LE;
		}

		function textline($value) {
			return $value . $this->LE;
		}

		function addattachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
			if (!@is_file( $path )) {
				$this->SetError( $this->Lang( 'file_access' ) . $path );
				return false;
			}

			$filename = basename( $path );

			if ($name == '') {
				$name = $filename;
			}

			$cur = count( $this->attachment );
			$this->attachment[$cur][0] = $path;
			$this->attachment[$cur][1] = $filename;
			$this->attachment[$cur][2] = $name;
			$this->attachment[$cur][3] = $encoding;
			$this->attachment[$cur][4] = $type;
			$this->attachment[$cur][5] = false;
			$this->attachment[$cur][6] = 'attachment';
			$this->attachment[$cur][7] = 0;
			return true;
		}

		function attachall() {
			$mime = array(  );
			$i = 0;

			while ($i < count( $this->attachment )) {
				$bString = $this->attachment[$i][5];

				if ($bString) {
					$string = $this->attachment[$i][0];
				} 
else {
					$path = $this->attachment[$i][0];
				}

				$filename = $this->attachment[$i][1];
				$name = $this->attachment[$i][2];
				$encoding = $this->attachment[$i][3];
				$type = $this->attachment[$i][4];
				$disposition = $this->attachment[$i][6];
				$cid = $this->attachment[$i][7];
				$mime[] = sprintf( '--%s%s', $this->boundary[1], $this->LE );
				$mime[] = sprintf( 'Content-Type: %s; name="%s"%s', $type, $name, $this->LE );
				$mime[] = sprintf( 'Content-Transfer-Encoding: %s%s', $encoding, $this->LE );

				if ($disposition == 'inline') {
					$mime[] = sprintf( 'Content-ID: <%s>%s', $cid, $this->LE );
				}

				$mime[] = sprintf( 'Content-Disposition: %s; filename="%s"%s', $disposition, $name, $this->LE . $this->LE );

				if ($bString) {
					$mime[] = $this->EncodeString( $string, $encoding );

					if ($this->IsError(  )) {
						return '';
					}

					$mime[] = $this->LE . $this->LE;
				} 
else {
					$mime[] = $this->EncodeFile( $path, $encoding );

					if ($this->IsError(  )) {
						return '';
					}

					$mime[] = $this->LE . $this->LE;
				}

				++$i;
			}

			$mime[] = sprintf( '--%s--%s', $this->boundary[1], $this->LE );
			return join( '', $mime );
		}

		function encodefile($path, $encoding = 'base64') {
			if (!$fd = @fopen( $path, 'rb' )) {
				$this->SetError( $this->Lang( 'file_open' ) . $path );
				return '';
			}

			$file_buffer = fread( $fd, filesize( $path ) );
			$file_buffer = $this->EncodeString( $file_buffer, $encoding );
			fclose( $fd );
			return $file_buffer;
		}

		function encodestring($str, $encoding = 'base64') {
			$encoded = '';
			switch (strtolower( $encoding )) {
				case 'base64': {
					$encoded = chunk_split( base64_encode( $str ), 76, $this->LE );
					break;
				}

				case '7bit': {
				}

				case '8bit': {
					$encoded = $this->FixEOL( $str );

					if (substr( $encoded, 0 - strlen( $this->LE ) ) != $this->LE) {
						$encoded .= $this->LE;
					}

					break;
				}

				case 'binary': {
					$encoded = $str;
					break;
				}

				case 'quoted-printable': {
					$encoded = $this->EncodeQP( $str );
					break;
				}

				default: {
					$this->SetError( $this->Lang( 'encoding' ) . $encoding );
					break;
				}
			}

			return $encoded;
		}

		function encodeheader($str, $position = 'text') {
			$x = 0;
			switch (strtolower( $position )) {
				case 'phrase': {
					if (!preg_match( '/[\200-\377]/', $str )) {
						$encoded = addcslashes( $str, '' );

						if (( $str == $encoded && !preg_match( '/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str ) )) {
							return $encoded;
						}

						return ( ( '' . '"' ) . $encoded . '"' );
					}

					$x = preg_match_all( '/[^\040\041\043-\133\135-\176]/', $str, $matches );
					break;
				}

				case 'comment': {
					$x = preg_match_all( '/[()"]/', $str, $matches );
				}

				case 'text': {
				}

				default: {
				}
			}

			$x += preg_match_all( '/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches );
			/*break; not needed -- error J.Doran 210520*/

			if ($x == 0) {
				return $str;
			}

			$maxlen = 75 - 7 - strlen( $this->CharSet );

			if (strlen( $str ) / 3 < $x) {
				$encoding = 'B';
				$encoded = base64_encode( $str );
				$maxlen -= $maxlen % 4;
				$encoded = trim( chunk_split( $encoded, $maxlen, '
' ) );
			} 
else {
				$encoding = 'Q';
				$encoded = $this->EncodeQ( $str, $position );
				$encoded = $this->WrapText( $encoded, $maxlen, true );
				$encoded = str_replace( '=' . $this->LE, '
', trim( $encoded ) );
			}

			$encoded = preg_replace( '/^(.*)$/m', ' =?' . $this->CharSet . ( '' . '?' . $encoding . '?\1?=' ), $encoded );
			$encoded = trim( str_replace( '
', $this->LE, $encoded ) );
			return $encoded;
		}

		function encodeqp($str) {
			$encoded = $this->FixEOL( $str );

			if (substr( $encoded, 0 - strlen( $this->LE ) ) != $this->LE) {
				$encoded .= $this->LE;
			}

			$encoded = preg_replace( '/([\000-\010\013\014\016-\037\075\177-\377])/e', '\'=\'.sprintf(\'%02X\', ord(\'\1\'))', $encoded );
			$encoded = preg_replace( '/([	 ])' . $this->LE . '/e', '\'=\'.sprintf(\'%02X\', ord(\'\1\')).\'' . $this->LE . '\'', $encoded );
			$encoded = $this->WrapText( $encoded, 74, true );
			return $encoded;
		}

		function encodeq($str, $position = 'text') {
			$encoded = preg_replace( '[
]', '', $str );
			switch (strtolower( $position )) {
				case 'phrase': {
					$encoded = preg_replace( '/([^A-Za-z0-9!*+\/ -])/e', '\'=\'.sprintf(\'%02X\', ord(\'\1\'))', $encoded );
					break;
				}

				case 'comment': {
					$encoded = preg_replace( '/([\(\)"])/e', '\'=\'.sprintf(\'%02X\', ord(\'\1\'))', $encoded );
				}

				case 'text': {
				}

				default: {
				}
			}

			$encoded = preg_replace( '/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', '\'=\'.sprintf(\'%02X\', ord(\'\1\'))', $encoded );
			/*break; Not Needed J. Doran 210520*/
			$encoded = str_replace( ' ', '_', $encoded );
			return $encoded;
		}

		function addstringattachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream') {
			$cur = count( $this->attachment );
			$this->attachment[$cur][0] = $string;
			$this->attachment[$cur][1] = $filename;
			$this->attachment[$cur][2] = $filename;
			$this->attachment[$cur][3] = $encoding;
			$this->attachment[$cur][4] = $type;
			$this->attachment[$cur][5] = true;
			$this->attachment[$cur][6] = 'attachment';
			$this->attachment[$cur][7] = 0;
		}

		function addembeddedimage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
			if (!@is_file( $path )) {
				$this->SetError( $this->Lang( 'file_access' ) . $path );
				return false;
			}

			$filename = basename( $path );

			if ($name == '') {
				$name = $filename;
			}

			$cur = count( $this->attachment );
			$this->attachment[$cur][0] = $path;
			$this->attachment[$cur][1] = $filename;
			$this->attachment[$cur][2] = $name;
			$this->attachment[$cur][3] = $encoding;
			$this->attachment[$cur][4] = $type;
			$this->attachment[$cur][5] = false;
			$this->attachment[$cur][6] = 'inline';
			$this->attachment[$cur][7] = $cid;
			return true;
		}

		function inlineimageexists() {
			$result = false;
			$i = 0;

			while ($i < count( $this->attachment )) {
				if ($this->attachment[$i][6] == 'inline') {
					$result = true;
					break;
				}

				++$i;
			}

			return $result;
		}

		function clearaddresses() {
			$this->to = array(  );
		}

		function clearccs() {
			$this->cc = array(  );
		}

		function clearbccs() {
			$this->bcc = array(  );
		}

		function clearreplytos() {
			$this->ReplyTo = array(  );
		}

		function clearallrecipients() {
			$this->to = array(  );
			$this->cc = array(  );
			$this->bcc = array(  );
		}

		function clearattachments() {
			$this->attachment = array(  );
		}

		function clearcustomheaders() {
			$this->CustomHeader = array(  );
		}

		function seterror($msg) {
			++$this->error_count;
			$this->ErrorInfo = $msg;
		}

		function rfcdate() {
			$tz = date( 'Z' );
			$tzs = ($tz < 0 ? '-' : '+');
			$tz = abs( $tz );
			$tz = $tz / 3600 * 100 + $tz % 3600 / 60;
			$result = sprintf( '%s %s%04d', date( 'D, j M Y H:i:s' ), $tzs, $tz );
			return $result;
		}

		function received() {
			if ($this->ServerVar( 'SERVER_NAME' ) != '') {
				$protocol = ($this->ServerVar( 'HTTPS' ) == 'on' ? 'HTTPS' : 'HTTP');
				$remote = $this->ServerVar( 'REMOTE_HOST' );

				if ($remote == '') {
					$remote = 'phpmailer';
				}

				$remote .= ' ([' . $this->ServerVar( 'REMOTE_ADDR' ) . '])';
			} 
else {
				$protocol = 'local';
				$remote = $this->ServerVar( 'USER' );

				if ($remote == '') {
					$remote = 'phpmailer';
				}
			}

			$result = sprintf( 'Received: from %s %s	by %s ' . 'with %s (PHPMailer);%s	%s%s', $remote, $this->LE, $this->ServerHostname(  ), $protocol, $this->LE, $this->RFCDate(  ), $this->LE );
			return $result;
		}

		function servervar($varName) {
			global $HTTP_SERVER_VARS;
			global $HTTP_ENV_VARS;

			if (!isset( $_SERVER )) {
				$_SERVER = $HTTP_SERVER_VARS;

				if (!isset( $_SERVER['REMOTE_ADDR'] )) {
					$_SERVER = $HTTP_ENV_VARS;
				}
			}


			if (isset( $_SERVER[$varName] )) {
				return $_SERVER[$varName];
			}

			return '';
		}

		function serverhostname() {
			if ($this->Hostname != '') {
				$result = $this->Hostname;
			} 
else {
				if ($this->ServerVar( 'SERVER_NAME' ) != '') {
					$result = $this->ServerVar( 'SERVER_NAME' );
				} 
else {
					$result = 'localhost.localdomain';
				}
			}

			return $result;
		}

		function lang($key) {
			if (count( $this->language ) < 1) {
				$this->SetLanguage( 'en' );
			}


			if (isset( $this->language[$key] )) {
				return $this->language[$key];
			}

			return 'Language string failed to load: ' . $key;
		}

		function iserror() {
			return 0 < $this->error_count;
		}

		function fixeol($str) {
			$str = str_replace( '
', '
', $str );
			$str = str_replace( '', '
', $str );
			$str = str_replace( '
', $this->LE, $str );
			return $str;
		}

		function addcustomheader($custom_header) {
			$this->CustomHeader[] = explode( ':', $custom_header, 2 );
		}
	}

?>