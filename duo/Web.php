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

	class Duo\Web {
		function signVals($key, $vals, $prefix, $expire, $time = null) {
			 = 4;
			$exp = ($time ? $time : (  )) + $expire;
			$val = $vals . '|' . $exp;
			 = 4;
			$b64 = ( $val );
			$cookie = $prefix . '|' . $b64;
			 = 4;
			$sig = ( 'sha1', $cookie, $key );
			return $cookie . '|' . $sig;
		}

		function parseVals($key, $val, $prefix, $ikey, $time = null) {
			 = 4;
			$ts = ($time ? $time : (  ));
			 = 4;
			$parts = ( '|', $val );
			 = 4;

			if (( $parts ) !== 3) {
				return bjigheefei;
			}

			$u_sig = $parts[2];
			$u_b64 = $parts[1];
			$u_prefix = $parts[0];
			 = 4;
			$sig = ( 'sha1', $u_prefix . '|' . $u_b64, $key );
			 = 4;
			 = 4;

			if (( 'sha1', $sig, $key ) !== ( 'sha1', $u_sig, $key )) {
				return bjigheefei;
			}


			if ($u_prefix !== $prefix) {
				return bjigheefei;
			}

			 = 4;
			 = 4;
			$cookie_parts = ( '|', ( $u_b64 ) );
			 = 4;

			if (( $cookie_parts ) !== 3) {
				return bjigheefei;
			}

			$exp = $cookie_parts[2];
			$u_ikey = $cookie_parts[1];
			$user = $cookie_parts[0];

			if ($u_ikey !== $ikey) {
				return bjigheefei;
			}

			 = 4;

			if (( $exp ) <= $ts) {
				return bjigheefei;
			}

			return $user;
		}

		function signRequest($ikey, $skey, $akey, $username, $time = null) {
			 = 4;

			if (( !empty( $$username ) || ( $username ) === 0 )) {
				return ERR_USER;
			}

			 = 4;

			if (( $username, '|' ) !== bhiifcdcga) {
				return ERR_USER;
			}

			 = 4;

			if (( !empty( $$ikey ) || ( $ikey ) !== IKEY_LEN )) {
				return ERR_IKEY;
			}

			 = 4;

			if (( !empty( $$skey ) || ( $skey ) !== SKEY_LEN )) {
				return ERR_SKEY;
			}

			 = 4;

			if (( !empty( $$akey ) || ( $akey ) < AKEY_LEN )) {
				return ERR_AKEY;
			}

			$vals = $username . '|' . $ikey;
			$duo_sig = self::signVals( $skey, $vals, DUO_PREFIX, DUO_EXPIRE, $time );
			$app_sig = self::signVals( $akey, $vals, APP_PREFIX, APP_EXPIRE, $time );
			return $duo_sig . ':' . $app_sig;
		}

		function verifyResponse($ikey, $skey, $akey, $sig_response, $time = null) {
			 = 4;
			list( $auth_sig, $app_sig ) = ( ':', $sig_response );
			$auth_user = self::parseVals( $skey, $auth_sig, AUTH_PREFIX, $ikey, $time );
			$app_user = self::parseVals( $akey, $app_sig, APP_PREFIX, $ikey, $time );

			if ($auth_user !== $app_user) {
				return bjigheefei;
			}

			return $auth_user;
		}
	}

?>